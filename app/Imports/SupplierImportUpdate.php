<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;

class SupplierImportUpdate implements ToCollection, WithHeadingRow, WithStartRow
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

            $supplier = Supplier::find($row['id']);

            if (!$supplier) {
                continue;
            }

            $updateData = [];

            //field ini mengikuti desain database
            $allowedFields = [
                'main_category_id', 'supplier_name', 'address', 'fax', 'telp', 'email',
                'contact', 'pkp', 'no_rekening', 'note'
            ];

            $supplierNameExist = false;

            foreach ($allowedFields as $field) {
                if (isset($row[$field])) {
                    $value = $row[$field];

                    if (is_string($value) && strtolower(trim($value)) === 'null') {
                        continue;
                    }

                    if (!empty($value)) {
                        $updateData[$field] = $value;
                    }

                    if ($field === 'supplier_name' && !empty($value)) {
                        $supplierNameExist = true;
                    }
                }
            }

            if (!$supplierNameExist) {
                continue;
            }

            if (!empty($updateData)) {
                $supplier->update($updateData);
            }
        }
    }
    
    public function startRow(): int
    {
        return 3;
    }
}
