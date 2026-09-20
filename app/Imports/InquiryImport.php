<?php

namespace App\Imports;

use App\Models\Inquiry;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InquiryImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public function collection(Collection $rows)
    {
        // skip row contoh
        $dataRows = $rows->slice(2);

        foreach ($dataRows as $row) {
            if (
                isset($row['date']) &&
                isset($row['name']) &&
                isset($row['customer_category']) &&
                isset($row['country_code']) &&
                isset($row['channel_id'])
            ) {
                Inquiry::create([
                    'date'              => $row['date'],
                    'name'              => $row['name'],
                    'customer_category' => $row['customer_category'],
                    'country_code'      => $row['country_code'],
                    'city'              => $row['city'],
                    'channel_id'        => $row['channel_id'],
                    'website_id'        => $row['website_id'],
                    'user_id'           => $row['user_id'] ?? auth()->user()->id,
                    'phone'             => $row['phone'],
                    'email'             => $row['email'],
                    'destination_id'    => $row['destination_id'],
                    'note'              => $row['note'],
                ]);
            }
        }
    }
}
