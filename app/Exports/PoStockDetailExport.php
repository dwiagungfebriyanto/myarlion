<?php

namespace App\Exports;

use App\Models\PoStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Files\LocalTemporaryFile;

class PoStockDetailExport implements WithEvents
{
    protected int $poStockId;

    public function __construct(int $poStockId)
    {
        $this->poStockId = $poStockId;
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                // Ensure Laravel-Excel temporary directory exists and is writable
                $tempPath = config('excel.temporary_files.local_path');
                if ($tempPath && !is_dir($tempPath)) {
                    File::ensureDirectoryExists($tempPath, 0775);
                }

                // Prepare a writable working copy of the template inside the temp dir
                $templatePath = storage_path('app/templates/po/po_stock_detail.xlsx');
                if (!file_exists($templatePath)) {
                    throw new \RuntimeException('Template not found at: ' . $templatePath);
                }
                $workFile = rtrim($tempPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'po_stock_detail_' . uniqid('', true) . '.xlsx';
                File::copy($templatePath, $workFile);
                @chmod($workFile, 0664);

                // Reopen writer using the working copy to avoid touching original template
                $event->writer->reopen(new LocalTemporaryFile($workFile), ExcelWriter::XLSX);
                // Ensure first sheet is active
                $event->writer->getSheetByIndex(0);
            },

            AfterSheet::class => function (AfterSheet $event) {
                // Inject data on the existing template sheet (not a new one)
                // Ensure we target the template's first sheet or a sheet named 'template'
                $active  = $event->sheet->getDelegate();
                $spread  = $active->getParent();
                $target  = $spread->getSheetByName('template');
                if (!$target) {
                    $target = $spread->getSheet(0); // fallback to first sheet
                }
                $spread->setActiveSheetIndex($target->getParent()->getIndex($target));
                $sheet = $target; // write to the template sheet
                $poStock = PoStock::with(['supplier', 'mainCategory', 'poStockDetail.product', 'poStockDetail.unit'])
                    ->find($this->poStockId);

                if (!$poStock) {
                    return;
                }

                // Header fields (match rows in template: Po Number=E16, To=E17, Attn=E18)
                // Po Number
                $sheet->setCellValue('E16', $poStock->unique_id ?? '');
                // To (Supplier)
                $sheet->setCellValue('E17', $poStock->supplier->supplier_name ?? '');
                // Attn
                $attn = $poStock->supplier->contact_name
                    ?? $poStock->supplier->contact_person
                    ?? $poStock->supplier->pic
                    ?? null;
                if (!empty($attn)) {
                    $sheet->setCellValue('E18', $attn);
                }

                // Detail rows mapping
                $baseRow = 23; // first detail row in template (start at line 23)
                $currentRow = $baseRow;

                // Helper to resolve product name and dimensions gracefully
                $resolve = function ($product, $keys) {
                    foreach ($keys as $k) {
                        if (isset($product->$k) && $product->$k !== '') {
                            return $product->$k;
                        }
                    }
                    return null;
                };

                foreach ($poStock->poStockDetail as $index => $detail) {
                    $product = $detail->product;

                    if ($index > 0) {
                        // Insert new row for each extra item right before the target row
                        $sheet->insertNewRowBefore($currentRow, 1);
                        // Try to clone styles from the template's first detail row (B23:M23)
                        try {
                            $sheet->duplicateStyle($sheet->getStyle('B' . $baseRow . ':M' . $baseRow), 'B' . $currentRow . ':M' . $currentRow);
                        } catch (\Throwable $e) {
                            // If style clone fails, continue writing values only
                        }
                    }

                    // Product name (fallback to SKU when name not available)
                    $productName = $resolve($product, ['product_name', 'name']) ?? ($product?->skuFormat() ?? '');
                    // Multi-line item description (name + optional description/spec), wrapped like template
                    $extraDesc = $detail->description ?? ($detail->product->description ?? '');
                    $itemDesc = trim(implode("\n", array_filter([$productName, $extraDesc], fn($v) => (string)$v !== '')));

                    // Dimensions: try common field names; leave blank if unavailable
                    $a = $resolve($product, ['length', 'length_cm', 'l', 'dim_l']);
                    $b = $resolve($product, ['width', 'width_cm', 'w', 'dim_w']);
                    $c = $resolve($product, ['height', 'height_cm', 'thickness', 't', 'dim_t']);

                    // Merge C and D for numbering + item column (template expects merged area)
                    try {
                        $sheet->mergeCells('C' . $currentRow . ':D' . $currentRow);
                    } catch (\Throwable $_) {
                        // ignore if merge fails
                    }
                    // Write cells according to mapping
                    // Row number (No) in column B
                    $sheet->setCellValue('B' . $currentRow, $index + 1);
                    // Item description in merged C:D — put number and description together visually
                    $sheet->setCellValue('C' . $currentRow, ($itemDesc !== '' ? $itemDesc : $productName));
                    $sheet->getStyle('C' . $currentRow)->getAlignment()->setWrapText(true);
                    // Ensure product text area does not keep bold or yellow fill from template
                    try {
                        $sheet->getStyle('C' . $currentRow . ':D' . $currentRow)->getFont()->setBold(false);
                        $sheet->getStyle('C' . $currentRow . ':D' . $currentRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE);
                    } catch (\Throwable $_) {
                        // ignore style tweak failures
                    }
                    if ($a !== null) $sheet->setCellValue('E' . $currentRow, $a);
                    // Clean borders around E (no right border; keep top/bottom only)
                    try {
                        $bE = $sheet->getStyle('E' . $currentRow)->getBorders();
                        $bE->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $bE->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $bE->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
                    } catch (\Throwable $_) {}
                    // Put visual multiplier 'x' between L x W x T in columns F and H
                    $sheet->setCellValue('F' . $currentRow, 'x');
                    // Borders for F: only top/bottom; remove left/right
                    try {
                        $styleF = $sheet->getStyle('F' . $currentRow)->getBorders();
                        $styleF->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $styleF->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $styleF->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
                        $styleF->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
                    } catch (\Throwable $_) {}
                    if ($b !== null) $sheet->setCellValue('G' . $currentRow, $b);
                    // Borders for G: only top/bottom; remove left/right
                    try {
                        $styleG = $sheet->getStyle('G' . $currentRow)->getBorders();
                        $styleG->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $styleG->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $styleG->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
                        $styleG->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
                    } catch (\Throwable $_) {}
                    $sheet->setCellValue('H' . $currentRow, 'x');
                    // Borders for H: only top/bottom; remove left/right
                    try {
                        $styleH = $sheet->getStyle('H' . $currentRow)->getBorders();
                        $styleH->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $styleH->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $styleH->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
                        $styleH->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
                    } catch (\Throwable $_) {}
                    if ($c !== null) $sheet->setCellValue('I' . $currentRow, $c);
                    // Clean borders around I (no left border; keep top/bottom only)
                    try {
                        $bI = $sheet->getStyle('I' . $currentRow)->getBorders();
                        $bI->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $bI->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $bI->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
                    } catch (\Throwable $_) {}
                    // Get unit name from relationship (Kg, Cm, Pcs, etc)
                    $dimUnit = $detail->unit->unit_name 
                        ?? ($detail->dimension_unit 
                        ?? ($detail->product->dimension_unit ?? 'Cm'));
                    $sheet->setCellValue('J' . $currentRow, $dimUnit);
                    $sheet->setCellValue('K' . $currentRow, $detail->price ?? 0);
                    // Format harga dengan pemisah ribuan (lokal Excel akan menyesuaikan titik/koma)
                    $sheet->getStyle('K' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->setCellValue('L' . $currentRow, $detail->qty ?? 0);
                    // Format dan perataan Qty: ribuan dan center
                    $sheet->getStyle('L' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('L' . $currentRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('L' . $currentRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    // Total Price per row = K * L (keep template's number format)
                    $sheet->setCellValue('M' . $currentRow, '=K' . $currentRow . '*L' . $currentRow);
                    $sheet->getStyle('M' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');

                    $currentRow++;
                }

                // Grand Total row position and formula: directly below the last item row
                $addedRows = max(0, count($poStock->poStockDetail) - 1);
                $lastItemRow = $currentRow - 1;
                $grandRow = $lastItemRow + 1;
                if ($lastItemRow >= $baseRow) {
                    $sheet->setCellValue('M' . $grandRow, '=SUM(M' . $baseRow . ':M' . $lastItemRow . ')');
                    $sheet->getStyle('M' . $grandRow)->getNumberFormat()->setFormatCode('#,##0');
                }

                // Place PO note: move down one more row and left one column; allow overflow to the right
                $noteRow = $lastItemRow + 5;
                $noteText = (string) ($poStock->note ?? '');
                if ($noteText !== '') {
                    // Merge across the row to allow long text and keep overflow style
                    try {
                        $sheet->mergeCells('B' . $noteRow . ':M' . $noteRow);
                    } catch (\Throwable $_) {}
                    // Put note text into column B and disable wrapping so text can overflow to the right
                    $sheet->setCellValue('B' . $noteRow, $noteText);
                    $style = $sheet->getStyle('B' . $noteRow);
                    $style->getAlignment()->setWrapText(false);
                    $style->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    try { $style->getAlignment()->setShrinkToFit(false); } catch (\Throwable $_) {}
                }

                // Tanggal kota tetap "Yogyakarta", tanggal mengikuti sistem
                // Geser 2 baris lebih bawah dari sebelumnya (total 4 baris setelah NOTE)
                $dateRow = $noteRow + 4;
                try {
                    $formattedDate = Carbon::now()->locale('id')->translatedFormat('j F Y');
                } catch (\Throwable $_) {
                    // Fallback manual jika locale tidak tersedia
                    $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    $d = (int) date('j');
                    $m = (int) date('n');
                    $y = (int) date('Y');
                    $formattedDate = $d . ' ' . ($months[$m] ?? date('F')) . ' ' . $y;
                }
                $cityDate = 'Yogyakarta, ' . $formattedDate;
                try { $sheet->mergeCells('B' . $dateRow . ':M' . $dateRow); } catch (\Throwable $_) {}
                $sheet->setCellValue('B' . $dateRow, $cityDate);
                $sheet->getStyle('B' . $dateRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                // Pastikan tinggi baris normal (tidak penyet)
                try { $sheet->getRowDimension($dateRow)->setRowHeight(-1); } catch (\Throwable $_) {}
            },
        ];
    }
}
