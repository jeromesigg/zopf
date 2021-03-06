<?php

namespace App\Imports;

use App\Order;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class OrdersImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Order([
            //
           'quantity' => $row[5],
           'comments' => $row[8],
        ]);
    }
}
