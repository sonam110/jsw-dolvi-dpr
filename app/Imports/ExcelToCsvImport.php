<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelToCsvImport implements ToArray, WithHeadingRow
{
    
    public function array(array $row)
    {
        // $row is an associative array with column names as keys
        return $row;
    }
}
