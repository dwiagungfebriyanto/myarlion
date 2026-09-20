<?php

namespace App\Imports;

use App\Models\Job;
use App\Models\OutcomeCheque;
use App\Models\PoAsset;
use App\Models\PoStock;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CostImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public function collection(Collection $rows)
    {
        // skip row contoh
        $dataRows = $rows->slice(2);

        $requiredColumns = [
            'date',
            'bank_account_id',
            'code_type',
            'code',
            'outcome_type_id',
            'amount',
            'recipient_type',
            'recipient_id',
            'note',
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

                $data[$requiredColumn] = $row[$requiredColumn];
            }

            if ($requiredIsFilled) {
                $data['code']        = $this->setCodeByType($data['code_type'], $data['code']);
                $data['po_stock_id'] = $row['po_stock_id'] ?? null;

                OutcomeCheque::create($data);
            }
        }
    }

    private function setCodeByType($codeType, $code)
    {
        switch ($codeType) {
            case 'job':
                return Job::where('code', $code)->first()->id;
                break;

            case 'po_stock':
                return PoStock::where('unique_id', $code)->first()->id;
                break;

            case 'po_asset':
                return PoAsset::where('unique_id', $code)->first()->id;
                break;

            default:
                return $code;
                break;
        }
    }
}
