<?php

namespace App\Http\Controllers;

use App\Jobs\UploadCsvJob;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public $perPage = 100;

    public function index(Request $request)
    {
        $products = Product::orderByDesc('id')->paginate($this->perPage);

        $this->removeAllFile();

        return view('index', compact('products'));
    }

    public function upload(Request $request)
    {
        $chunksPath = storage_path('app/uploads');
        $chunkFolder = $request->input('resumableIdentifier');
        $chunkNumber = $request->input('resumableChunkNumber');
        $totalChunks = $request->input('resumableTotalChunks');

        $filename = $request->file('file')->getClientOriginalName();
        $extension = $request->file('file')->getClientOriginalExtension();
        $timestamp = now()->timestamp;

        if (!File::exists($chunksPath . '/' . $chunkFolder)) {
            File::makeDirectory($chunksPath . '/' . $chunkFolder);
        }

        $request->file('file')->move($chunksPath . '/' . $chunkFolder, $chunkNumber . '.' . $extension);

        if ($chunkNumber == $totalChunks) {
            $mergedFilePath = storage_path('app/uploads/' . $timestamp . '_' . $filename);
            $destination = fopen($mergedFilePath, 'a');

            for ($i = 1; $i <= $totalChunks; $i++) {
                $chunkFilePath = $chunksPath . '/' . $chunkFolder . '/' . $i . '.' . $extension;
                fwrite($destination, file_get_contents($chunkFilePath));

                unlink($chunkFilePath);
            }

            fclose($destination);
            rmdir($chunksPath . '/' . $chunkFolder);

            return response()->json([
                'message' => 'File upload successfully',
            ]);
        }

        return response()->json([
            'message' => 'Chunk uploaded successfully',
        ]);
    }

    public function import()
    {
        $path = glob(storage_path('app/uploads/*.csv'));

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

            $fileName = time() . '.csv';
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

        $this->removeAllFile();

        return redirect()->route('index')->with('messages', 'Process of adding data is complete.');
    }

    public function remove()
    {
        $this->removeAllFile();

        return response()->json([
            'message' => 'Deleted.',
            200
        ]);
    }

    public function removeAllFile()
    {
        $filepPublicPaths = glob(storage_path('app/uploads/*.csv'));

        foreach ($filepPublicPaths as $path) {
            unlink($path);
        }

        $filepStoragePaths = glob(storage_path('app/*.csv'));

        foreach ($filepStoragePaths as $path) {
            unlink($path);
        }
    }
}
