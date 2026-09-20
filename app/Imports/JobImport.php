<?php

namespace App\Imports;

use App\Http\Controllers\JobListController;
use App\Models\Customer;
use App\Models\Job;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JobImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    public function collection(Collection $rows)
    {
        // skip row contoh
        $dataRows = $rows->slice(3);

        foreach ($dataRows as $row) {
            $checkValue = $this->checkValue($row->toArray());
            $data = $checkValue['clearData'];

            if ($checkValue['requiredIsFilled'] && Job::where('code', $data['job_code'])->doesntExist()) {
                $job              = new Job;
                $job->id          = Job::max('id') + 1;
                $job->code        = $data['job_code'];
                $job->source      = $data['source'];
                $job->employee_id = $data['marketing_id'] ?? auth()->user()->id;
                $job->currency_id = $data['currency_id'];
                $job->amount      = $data['amount'] ?? 0;
                $job->est_profit  = $data['est_profit'] ?? 0;

                if ($data['source'] === 'inquiry') {
                    $jobController = new JobListController;
                    $jobController->storeInquiryJob($job, $data['inquiry_id']);
                } else {
                    // $data['customer_id'] menggunakan penamaan id karena di excelnya menggunakan itu.
                    // karena di datatable juga code ditampilkan sebagai id
                    if ($customer = Customer::where('code', $data['customer_id'])->first()) {
                        $job->customer_id = $customer->id;
                        $job->country_id  = $data['country_id'];
                        $job->channel_id  = $data['channel_id'];
                        $job->save();
                    }
                }
            }
        }
    }

    private function checkValue(array $data)
    {
        $requiredColumns = [
            'job_code',
            'marketing_id',
            'source',
            'currency_id',
            'amount',
            'est_profit',
        ];

        // tambah required column tergantung source
        if (isset($data['source']) && $data['source'] === 'inquiry') {
            $requiredColumns[] = 'inquiry_id';
        } else {
            $requiredColumns[] = 'customer_id';
            $requiredColumns[] = 'channel_id';
            $requiredColumns[] = 'country_id';
        }

        $requiredIsFilled = true;
        $clearData = []; // untuk menampung data yang akan diinput

        // cek apakah setiap required field ada value-nya
        foreach ($requiredColumns as $requiredColumn) {

            if (!isset($data[$requiredColumn])) {
                $requiredIsFilled = false;
                break; // hentikan looping jika ada yang kosong
            }

            $clearData[$requiredColumn] = $data[$requiredColumn];
        }

        return [
            'requiredIsFilled' => $requiredIsFilled,
            'clearData' => $clearData
        ];
    }
}
