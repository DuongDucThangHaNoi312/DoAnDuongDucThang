<?php

namespace App\Imports;

use App\Models\Van;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PayoffImport implements ToModel, WithChunkReading, ToCollection, WithCalculatedFormulas
{
    public function model(array $row)
    {
    }

    public function collection(Collection $collection)
    {
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    
    public function headingRow(): int
    {
        return 6;
    }

    
}
