<?php

namespace App\Imports;

use App\Models\Vendor;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VendorImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public function collection(Collection $rows)
    {
        // skip row contoh
        $dataRows = $rows->slice(2);

        $requiredColumns = [
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
                $vendorCode = Vendor::orderBy('code', 'asc')
                    ->get()
                    ->toArray();

                $nextCode = generateCode($vendorCode, 1);

                $vendor              = new Vendor;
                $vendor->code        = $nextCode;
                $vendor->vendor_name = $row['name'];
                $vendor->address     = $row['address'];
                $vendor->fax         = $row['fax'];
                $vendor->telp        = $row['phone'];
                $vendor->email       = $row['email'];
                $vendor->contact     = $row['contact_person'];
                $vendor->pkp         = $row['pkp'];
                $vendor->no_rekening = $row['bank_account'];
                $vendor->note        = $row['note'];
                $vendor->save();
            }
        }
    }
}
