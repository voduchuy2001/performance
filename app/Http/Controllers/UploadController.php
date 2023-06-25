<?php

namespace App\Http\Controllers;

use App\Jobs\UploadCsvJob;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public $mergedFileName;

    public function index()
    {
        return view('welcome');
    }

    public function upload(Request $request)
    {
        $files =  $request->file();

        $timestamp = now()->timestamp;

        $mergedFileName = $timestamp . '_merged.csv';

        foreach ($files as $file) {
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.' . $extension, '', $file->getClientOriginalName());
            $fileName = $timestamp . '_' . md5(time()) . '.' . $extension;

            $file->move(public_path('uploads'), $fileName);
        }

        $csvFiles = glob(public_path('uploads/*.csv'));
        if (count($csvFiles) > 1) {
            $mergedFilePath = public_path('uploads/' . $mergedFileName);
            $mergedFileHandle = fopen($mergedFilePath, 'w');
            foreach ($csvFiles as $file) {
                $csvFileHandle = fopen($file, 'r');
                while (!feof($csvFileHandle)) {
                    $line = fgets($csvFileHandle);
                    if ($line !== false) {
                        fwrite($mergedFileHandle, $line);
                    }
                }
                fclose($csvFileHandle);
                unlink($file); // delete individual CSV files after merging
            }
            fclose($mergedFileHandle);
        } else {
            $mergedFileName = $csvFiles[0]; // no need to merge if there's only one file
        }

        return response()->json([
            'success' => true,
            'merged_file_name' => $mergedFileName // return the merged file name
        ]);
    }

    public function import()
    {
        $path = glob(public_path('uploads/*.csv'));
        $delimiter = ',';
        $header = [
            'Name',
            'SKU',
            'Description',
            'Price',
            'Stock',
            'Status',
            'Type',
            'Vendor',
            'Created At'
        ];
        $chunkSize = 100000;

        $handle = fopen($path[0], 'r');
        $counter = 0;
        $isFirstChunk = true; // added flag variable
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            // collect rows for the chunk
            $rows[] = $row;
            $counter++;

            // check if chunk size is reached
            if ($counter === $chunkSize) {
                // create a new collection
                $collection = new Collection($rows);

                // add headers to the collection if it's not the first chunk
                if (!$isFirstChunk) {
                    $collection->prepend($header);
                }

                // create a new CSV file
                $fileName = 'chunk-' . time() . '.csv';
                Storage::disk('local')->put($fileName, '');

                // write the collection to the new CSV file
                $handleNew = fopen(storage_path('app/' . $fileName), 'w');
                $collection->each(function ($row) use ($handleNew, $delimiter) {
                    fputcsv($handleNew, $row, $delimiter);
                });

                // reset the counter and rows
                $counter = 0;
                $rows = [];

                // set the flag to false after the first chunk is processed
                $isFirstChunk = false;

                // close the new handle
                fclose($handleNew);
            }
        }

        // check if there is any remaining rows
        if (!empty($rows)) {
            // create a new collection
            $collection = new Collection($rows);

            // add header to the collection if it's not the first chunk
            if (!$isFirstChunk) {
                $collection->prepend($header);
            }

            // create a new CSV file
            $fileName = 'chunk-' . time() . '.csv';
            Storage::disk('local')->put($fileName, '');

            // write the collection to the new CSV file
            $handleNew = fopen(storage_path('app/' . $fileName), 'w');
            $collection->each(function ($row) use ($handleNew, $delimiter) {
                fputcsv($handleNew, $row, $delimiter);
            });

            // close the new handle
            fclose($handleNew);
        }

        // close the handle of the large CSV file
        fclose($handle);

        $filePaths = glob(storage_path('app/*.csv'));

        foreach ($filePaths as $filePath) {
            UploadCsvJob::dispatch($filePath);
        }

        unlink($path[0]);

        $files = Storage::disk('local')->allFiles();

        // loop through the files and delete them
        foreach ($files as $file) {
            Storage::disk('local')->delete($file);
        }

        return response()
            ->json([
                'message' => 'Process of adding data is complete.',
                200
            ]);
    }
}
