<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;
use League\Csv\Statement;

class UploadCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reader = Reader::createFromPath($this->filePath);

        $reader->includeEmptyRecords();

        $reader->isEmptyRecordsIncluded();

        $reader->setHeaderOffset(0);

        $records = Statement::create()->process($reader);

        $records = iterator_to_array($reader->getRecords());

        $chunkedRecords = array_chunk($records, 5000);

        foreach ($chunkedRecords as $chunk) {
            $data = [];

            foreach ($chunk as $record) {
                $data[] = [
                    'name' => $record['Name'] ? $record['Name'] : null,
                    'sku' => $record['SKU'] ? $record['SKU'] : null,
                    'description' => $record['Description'] ? $record['Description'] : null,
                    'price' => $record['Price'] ? $record['Price'] : null,
                    'stock' => $record['Stock'] ? $record['Stock'] : null,
                    'status' =>  $record['Status'] ? $record['Status'] : null,
                    'type' => $record['Type'] ? $record['Type'] : null,
                    'vendor' => $record['Vendor'] ? $record['Vendor'] : null,
                    'created_at' => $record['Created At'] ? $record['Created At'] : null,
                ];
            }

            Product::insert($data);
        }
    }
}
