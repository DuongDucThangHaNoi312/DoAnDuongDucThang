<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ContractsImport implements ToModel, WithChunkReading, ToCollection
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
}
