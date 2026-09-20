<?php

namespace App\Exports;

use App\Models\BankAccount;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Files\LocalTemporaryFile;

class JobInvoiceExport implements WithEvents
{
    private ?BankAccount $bankAccount = null;

    public function __construct(
        private int $jobId,
        private int $bankAccountId
    ) {
        $this->bankAccount = BankAccount::find($bankAccountId);
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                // Ensure temp dir exists
                $tempPath = config('excel.temporary_files.local_path');
                if ($tempPath && !is_dir($tempPath)) {
                    File::ensureDirectoryExists($tempPath, 0775);
                }

                // Copy template to a writable working file
                $templatePath = storage_path('app/templates/invoice/job_invoice.xlsx');
                if (!file_exists($templatePath)) {
                    throw new \RuntimeException('Invoice template not found at: ' . $templatePath);
                }

                $workFile = rtrim($tempPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
                    . 'job_invoice_' . uniqid('', true) . '.xlsx';
                File::copy($templatePath, $workFile);
                @chmod($workFile, 0664);

                // Reopen writer using working copy (Windows-safe)
                $event->writer->reopen(new LocalTemporaryFile($workFile), ExcelWriter::XLSX);
                $event->writer->getSheetByIndex(0);
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $spread = $sheet->getParent();
                // Use first sheet (template) directly
                $spread->setActiveSheetIndex(0);
                $sheet = $spread->getActiveSheet();

                $job = Job::with(['customer', 'marketing'])->find($this->jobId);
                if (!$job) {
                    return;
                }

                // Fetch items: product name (product_type), qty, unit, price
                $items = DB::table('jobs')
                    ->where('jobs.id', $job->id)
                    ->join('jobs_has_products', 'jobs.id', '=', 'jobs_has_products.job_id')
                    ->join('products', 'products.id', '=', 'jobs_has_products.product_id')
                    ->join('product_types', 'product_types.id', '=', 'products.product_type_id')
                    ->leftJoin('specifications', 'specifications.id', '=', 'products.specification_id')
                    ->join('units', 'units.id', '=', 'products.unit_id')
                    ->select(
                        'jobs_has_products.quantity as qty',
                        'jobs_has_products.price as price',
                        DB::raw('product_types.product_type_name as product_name'),
                        DB::raw('specifications.specification_name as specification_name'),
                        DB::raw('units.unit_name as unit_name')
                    )
                    ->get();

                // Header: Invoice Number and Date on row 15
                // Invoice Number format: Job Code/Customer Code/Year from Period Job
                $customerCode = $job->customer ? $job->customer->code : '000';
                $yearFromPeriod = $job->period_job ? Carbon::parse($job->period_job)->format('y') : date('y');
                $invoiceNumber = 'DW' . $job->code . '/' . $customerCode . '/' . $yearFromPeriod;
                $sheet->setCellValue('B14', $invoiceNumber);
                
                $formattedDateTop = $this->formatDateId(Carbon::now());
                $sheet->setCellValue('J15', 'Date : ' . $formattedDateTop);

                // Items table
                $baseRow = 23;
                $grandTemplateRow = 24;
                $itemStyleRow = $baseRow;
                $grandStyleRow = $grandTemplateRow;
                $itemCount = $items->count();
                $currentRow = $baseRow;
                foreach ($items as $index => $it) {
                    if ($currentRow > $baseRow) {
                        $this->prepareItemRow($sheet, $itemStyleRow, $currentRow);
                    }

                    $this->fillItemRow($sheet, $currentRow, $index + 1, $it);
                    $currentRow++;
                }

                $hasItems = $itemCount > 0;
                $lastItemRow = $hasItems ? $currentRow - 1 : $baseRow;
                $grandRow = $hasItems ? $lastItemRow + 1 : $grandTemplateRow;
                $grandTotalFormula = $hasItems
                    ? '=SUM(I' . $baseRow . ':I' . $lastItemRow . ')'
                    : '=0';

                $this->prepareGrandTotalRow($sheet, $grandStyleRow, $grandRow);
                $sheet->setCellValue('A' . $grandRow, 'Grand Total ');
                $sheet->setCellValue('I' . $grandRow, $grandTotalFormula);
                $sheet->getStyle('I' . $grandRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('A' . $grandRow . ':K' . $grandRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $grandRow)->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('I' . $grandRow)->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('A' . $grandRow . ':K' . $grandRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // ==== Bank Account ====
                $bankAccountRow = $grandRow + 3;
                $bankName = $this->bankAccount?->bank?->bank_name ?? '-';
                $accountNumber = $this->bankAccount?->account_number ?? '-';
                $accountName = $this->bankAccount?->account_name ?? '-';

                $sheet->setCellValue("A" . ($bankAccountRow + 1), "Bank Address :");
                $sheet->getStyle("A" . ($bankAccountRow + 1))->getFont()
                    ->setName('Calibri')
                    ->setSize(11);
                
                $sheet->setCellValue("A" . ($bankAccountRow + 2), "Bank {$bankName}");
                $sheet->getStyle("A" . ($bankAccountRow + 2))->getFont()
                    ->setBold(true)
                    ->setName('Calibri')
                    ->setSize(11);
                
                $sheet->setCellValue("A" . ($bankAccountRow + 4), "Bank Acc No : {$accountNumber}");
                $sheet->getStyle("A" . ($bankAccountRow + 4))->getFont()
                    ->setBold(true)
                    ->setName('Calibri')
                    ->setSize(10);
                
                $sheet->setCellValue("A" . ($bankAccountRow + 5), "Beneficiary : {$accountName}");
                $sheet->getStyle("A" . ($bankAccountRow + 5))->getFont()
                    ->setBold(true)
                    ->setName('Calibri')
                    ->setSize(10);

                // Bottom city/date
                $dateRow = $bankAccountRow + 8;
                $formattedDateBottom = $this->formatDateId(Carbon::now());
                $sheet->setCellValue("B{$dateRow}", "Yogyakarta, {$formattedDateBottom}");
                $sheet->getStyle("B{$dateRow}")->getFont()
                    ->setName('Arial')
                    ->setSize(10);
                
                // Marketing name in signature area with bold italic formatting
                $marketingName = $job->marketing ? $job->marketing->name : '';
                if ($marketingName) {
                    $marketingRow = $dateRow + 6;
                    $sheet->setCellValue("B{$marketingRow}", $marketingName);
                    $sheet->getStyle("B{$marketingRow}")->getFont()
                        ->setBold(true)
                        ->setName('Arial')
                        ->setSize(10);
                    $sheet->getStyle("B{$marketingRow}")->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    
                    $sheet->setCellValue("B" . ($marketingRow + 1), 'Sales & Marketing');
                    $sheet->getStyle("B" . ($marketingRow + 1))->getFont()
                        ->setBold(true)
                        ->setName('Arial')
                        ->setSize(10);
                    $sheet->getStyle("B" . ($marketingRow + 1))->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }

    private function formatDateId(Carbon $date): string
    {
        try {
            return $date->locale('id')->translatedFormat('j F Y');
        } catch (\Throwable $_) {
            $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            return (int)$date->format('j') . ' ' . ($months[(int)$date->format('n')] ?? $date->format('F')) . ' ' . $date->format('Y');
        }
    }

    private function buildItemLabel(?string $productName, ?string $specificationName): string
    {
        $product = trim((string) $productName);
        $specification = trim((string) $specificationName);

        if ($specification === '') {
            return $product;
        }

        return $product . ' - ' . $specification;
    }

    private function prepareItemRow($sheet, int $templateRow, int $targetRow): void
    {
        try {
            $sheet->duplicateStyle(
                $sheet->getStyle("A{$templateRow}:K{$templateRow}"),
                "A{$targetRow}:K{$targetRow}"
            );
        } catch (\Throwable $_) {
        }

        try {
            $sheet->getRowDimension($targetRow)
                ->setRowHeight($sheet->getRowDimension($templateRow)->getRowHeight());
        } catch (\Throwable $_) {
        }

        $this->clearRowMerges($sheet, $targetRow);
        $this->ensureMerged($sheet, [
            "B{$targetRow}:C{$targetRow}",
            "D{$targetRow}:E{$targetRow}",
            "G{$targetRow}:H{$targetRow}",
            "I{$targetRow}:K{$targetRow}",
        ]);
    }

    private function prepareGrandTotalRow($sheet, int $templateRow, int $targetRow): void
    {
        if ($targetRow !== $templateRow) {
            try {
                $sheet->duplicateStyle(
                    $sheet->getStyle("A{$templateRow}:K{$templateRow}"),
                    "A{$targetRow}:K{$targetRow}"
                );
            } catch (\Throwable $_) {
            }
        }

        try {
            $sheet->getRowDimension($targetRow)
                ->setRowHeight($sheet->getRowDimension($templateRow)->getRowHeight());
        } catch (\Throwable $_) {
        }

        $this->clearRowMerges($sheet, $targetRow);
        $this->ensureMerged($sheet, [
            "A{$targetRow}:H{$targetRow}",
            "I{$targetRow}:K{$targetRow}",
        ]);
    }

    private function fillItemRow($sheet, int $row, int $number, object $item): void
    {
        $sheet->setCellValue("A{$row}", $number);

        $itemLabel = $this->buildItemLabel($item->product_name, $item->specification_name);
        $sheet->setCellValue("B{$row}", $itemLabel);
        $sheet->getStyle("B{$row}")->getAlignment()
            ->setWrapText(true)
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $qty = (float) $item->qty;
        $price = (float) $item->price;

        $sheet->setCellValue("D{$row}", $qty);
        $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0.0##');
        $sheet->getStyle("D{$row}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("F{$row}", (string) $item->unit_name);

        $sheet->setCellValue("G{$row}", $price);
        $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue("I{$row}", "=D{$row}*G{$row}");
        $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("I{$row}")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle("A{$row}:K{$row}")->getFont()
            ->setName('Calibri')
            ->setSize(11);
    }

    private function ensureMerged($sheet, array $ranges): void
    {
        try {
            $mergedCells = $sheet->getMergeCells();
            foreach ($ranges as $range) {
                if (!in_array($range, $mergedCells, true)) {
                    $sheet->mergeCells($range);
                }
            }
        } catch (\Throwable $_) {
        }
    }

    private function clearRowMerges($sheet, int $row): void
    {
        try {
            foreach (array_keys($sheet->getMergeCells()) as $range) {
                if (!preg_match('/^[A-Z]+(\d+):[A-Z]+(\d+)$/', $range, $matches)) {
                    continue;
                }

                $startRow = (int) $matches[1];
                $endRow = (int) $matches[2];

                if ($row >= $startRow && $row <= $endRow) {
                    try {
                        $sheet->unmergeCells($range);
                    } catch (\Throwable $_) {
                    }
                }
            }
        } catch (\Throwable $_) {
        }
    }
}
