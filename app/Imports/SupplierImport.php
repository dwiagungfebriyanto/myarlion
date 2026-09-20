<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public function collection(Collection $rows)
    {
        // skip row contoh
        $dataRows = $rows->slice(2);

        $requiredColumns = [
            'main_category_id',
            'name',
        ];

        foreach ($dataRows as $row) {
            $requiredIsFilled = true;

            // cek apakah setiap required field ada value-nya
            foreach ($requiredColumns as $requiredColumn) {
                if (!isset($row[$requiredColumn])) {
                    $requiredIsFilled = false;
                    break; // hentikan looping jika ada yang kosong
                }
            }

            if ($requiredIsFilled) {
                $last_code                  = Supplier::where('main_category_id', $row['main_category_id'])->max('code');
                $new_code                   = intval($last_code) + 1;

                $supplier                   = new Supplier;
                $supplier->main_category_id = $row['main_category_id'];
                $supplier->code             = str_pad($new_code, 3, '0', STR_PAD_LEFT);
                $supplier->supplier_name    = $row['name'];
                $supplier->address          = $row['address'];
                $supplier->email            = $row['email'];
                $supplier->telp             = $row['phone'];
                $supplier->fax              = $row['fax'];
                $supplier->contact          = $row['contact_person'];
                $supplier->pkp              = $row['pkp'];
                $supplier->no_rekening      = $row['bank_account'];
                $supplier->note             = $row['note'];
                $supplier->save();
            }
        }
    }
}
