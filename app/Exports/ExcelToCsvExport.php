<?php

namespace App\Exports;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class ExcelToCsvExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // Convert your Excel data to a collection
        return collect($this->data);
    }

    public function headings(): array
    {
        // Add headings for your CSV file if needed
        return [];
    }
}
