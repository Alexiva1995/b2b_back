<?php

namespace App\Imports;

use App\Models\AmazonProducts;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportProductAmazon implements ToModel, WithHeadingRow
{
    protected $lotId;
    public function __construct($lotId)
    {
        $this->lotId = $lotId;
    }

    /**
    * @param Collection $collection
    */
    public function model(array $rows)
    {
        return new AmazonProducts([
            'amazon_lot_id' => $this->lotId,
            'name' => $rows['name'],
            'url' => $rows['url'],
            'pvp' => $rows['pvp'],
            'price' => $rows['price'],  
        ]);
    }
}
