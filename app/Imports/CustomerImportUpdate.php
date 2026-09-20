<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;

class CustomerImportUpdate implements ToCollection, WithHeadingRow, WithStartRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (
                !isset($row['id']) ||
                empty($row['id']) ||
                (is_string($row['id']) && strtolower(trim($row['id'])) === 'null')
            ) {
                continue;
            }

            $customer = Customer::find($row['id']);

            if (!$customer) {
                continue;
            }

            $updateData = [];

            //field ini mengikuti desain database
            $allowedFields = [
               'name', 'tax', 'no_rekening', 'address', 'country_id',
               'telp', 'fax', 'email', 'contact', 'note'
            ];

            $nameExist = false;

            foreach ($allowedFields as $field) {
                if (isset($row[$field])) {
                    $value = $row[$field];

                    if (is_string($value) && strtolower(trim($value)) === 'null') {
                        continue;
                    }

                    if (!empty($value)) {
                        $updateData[$field] = $value;
                    }

                    $otherCustomerName = Customer::where('name', $value)
                        ->where('id', '!=', $customer->id)
                        ->first();

                    if ($field === 'name' && !empty($value) && !$otherCustomerName) {
                        $nameExist = true;
                    }
                }
            }

            if (!$nameExist) {
                continue;
            }

            if (!empty($updateData)) {
                $customer->update($updateData);
            }
        }
    }

    public function startRow(): int
    {
        return 3;
    }
}
