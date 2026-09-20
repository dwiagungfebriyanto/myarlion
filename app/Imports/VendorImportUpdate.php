<?php

namespace App\Imports;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;

class VendorImportUpdate implements ToCollection, WithHeadingRow
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

            $vendor = Vendor::find($row['id']);

            if (!$vendor) {
                continue;
            }

            $updateData = [];

            //field ini mengikuti desain database
            $allowedFields = [
                'vendor_name', 'pkp', 'no_rekening', 'address', 'telp',
                'fax', 'email', 'contact', 'note'
            ];

            $vendorNameExist = false;

            foreach ($allowedFields as $field) {
                if (isset($row[$field])) {
                    $value = $row[$field];

                    if (is_string($value) && strtolower(trim($value)) === 'null') {
                        continue;
                    }

                    if (!empty($value)) {
                        $updateData[$field] = $value;
                    }

                    if ($field === 'vendor_name' && !empty($value)) {
                        $vendorNameExist = true;
                    }
                }
            }

            if (!$vendorNameExist) {
                continue;
            }

            if (!empty($updateData)) {
                $vendor->update($updateData);
            }
        }
    }
    public function startRow(): int
    {
        return 3;
    }
}
