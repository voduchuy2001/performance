<?php

namespace App\Http\Controllers;

use App\Jobs\UploadCsvJob;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public $perPage = 100;

    public function index(Request $request)
    {
        $products = Product::orderByDesc('id')->paginate($this->perPage);

        return view('index', compact('products'));
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
                unlink($file);
            }
            fclose($mergedFileHandle);
        } else {
            $mergedFileName = $csvFiles[0];
        }

        return response()->json([
            'message' => 'Upload file successfully',
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
        $isFirstChunk = true;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = $row;
            $counter++;

            if ($counter === $chunkSize) {
                $collection = new Collection($rows);

                if (!$isFirstChunk) {
                    $collection->prepend($header);
                }

                $fileName = 'chunk-' . time() . '.csv';
                Storage::disk('local')->put($fileName, '');

                $handleNew = fopen(storage_path('app/' . $fileName), 'w');
                $collection->each(function ($row) use ($handleNew, $delimiter) {
                    fputcsv($handleNew, $row, $delimiter);
                });

                $counter = 0;
                $rows = [];

                $isFirstChunk = false;

                fclose($handleNew);
            }
        }

        if (!empty($rows)) {
            $collection = new Collection($rows);

            if (!$isFirstChunk) {
                $collection->prepend($header);
            }

            $fileName = 'chunk-' . time() . '.csv';
            Storage::disk('local')->put($fileName, '');

            $handleNew = fopen(storage_path('app/' . $fileName), 'w');
            $collection->each(function ($row) use ($handleNew, $delimiter) {
                fputcsv($handleNew, $row, $delimiter);
            });

            fclose($handleNew);
        }

        fclose($handle);

        $filePaths = glob(storage_path('app/*.csv'));

        foreach ($filePaths as $filePath) {
            UploadCsvJob::dispatch($filePath);
        }

        return response()
            ->json([
                'message' => 'Process of adding data is complete.',
                200
            ]);
    }
}
