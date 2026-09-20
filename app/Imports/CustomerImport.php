<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
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
            $data = []; // untuk menampung data yang akan diinput

            // cek apakah setiap required field ada value-nya
            foreach ($requiredColumns as $requiredColumn) {
                if (!isset($row[$requiredColumn])) {
                    $requiredIsFilled = false;
                    break; // hentikan looping jika ada yang kosong
                }
            }

            // jika semua required field diisi, simpan ke database
            if ($requiredIsFilled && Customer::where('name', $row['name'])->doesntExist()) {
                $customerCode = Customer::orderBy('code', 'asc')->get()->toArray();
                $nextCode     = generateCode($customerCode, 1);

                $customer              = new Customer();
                $customer->id          = Customer::max('id') + 1;
                $customer->code        = $nextCode;
                $customer->name        = $row['name'];
                $customer->tax         = $row['tax'];
                $customer->no_rekening = $row['bank_account'];
                $customer->address     = $row['address'];
                $customer->country_id  = $row['country_id'];
                $customer->telp        = $row['phone'];
                $customer->fax         = $row['fax'];
                $customer->email       = $row['email'];
                $customer->contact     = $row['contact_person'];
                $customer->note        = $row['note'];
                $customer->save();
            }
        }
    }
}
