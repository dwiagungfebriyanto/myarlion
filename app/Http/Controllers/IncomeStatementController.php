<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\OutcomeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class IncomeStatementController extends Controller
{
    public function index(Request $request)
    {
        $inputPeriod           = $request->period ?: date('Y-m');
        $data['salesData']     = $this->getSalesData($inputPeriod);
        $data['expenseData']   = $this->getExpenseData($inputPeriod);
        $data['inputPeriod']   = $inputPeriod;
        $data['documentMonth'] = date_format(date_create($inputPeriod), 'F Y');
        $data['totalExpenses'] = 0;
        
        return view('pages.income_statement.index', $data);
    }

    private function getSalesData($inputPeriod): array
    {
        $jobs                 = Job::where('period_job', 'like', "$inputPeriod%")->orderBy('period_job')->get();
        $data['sales']        = $jobs;
        $data['totalSalesGP'] = $jobs->sum('gross_profit');

        return $data;
    }

    private function getExpenseData($inputPeriod): Collection
    {
        # Ambil data: Group → Types + anak-anaknya
        $groups = OutcomeGroup::query()
            ->where(function ($q) use ($inputPeriod) {
                $q->whereHas('outcomeTypes.outcomeCheques', fn($qq) =>
                    $qq->forPeriod($inputPeriod)->where('code', 'CF')
                )->orWhereHas('outcomeTypes.otherIncomes', fn($qq) =>
                    $qq->forPeriod($inputPeriod)
                        ->where('code', 'CF')
                );
            })
            ->with(['outcomeTypes' => function ($q) use ($inputPeriod) {
                $q->orderBy('name')->with([
                    'outcomeCheques' => fn($qq) => $qq->forPeriod($inputPeriod)->where('code', 'CF'),
                    'otherIncomes'   => fn($qq) => $qq->forPeriod($inputPeriod),
                ]);
            }])
            ->orderBy('name')
            ->get();

        
        # Bentuk data siap render (merge & sort item per type)
        $report = $groups->map(function ($group) {
            $types = $group->outcomeTypes->map(function ($type) {
                $items = $type->outcomeCheques
                    ->map(fn($x) => [
                        'date'        => $x->date,
                        'description' => 'CF - ' . ($x->note ?? ''),
                        'amount'      => (float) $x->amount,
                        'source'      => 'cost',
                        'edit_url'    => route('accounting.cost.edit', $x->id),
                    ])->toBase()
                    ->concat(
                        $type->otherIncomes->map(fn($x) => [
                            'date'        => $x->date,
                            'description' => $x->description,
                            'amount'      => (float) $x->amount,
                            'source'      => 'other_income',
                            'edit_url'    => route('other_income.edit', $x->id),
                        ])->toBase()
                    )
                    ->sortBy('date')
                    ->values();

                return [
                    'type_id'   => $type->id,
                    'type_name' => $type->name,
                    'items'     => $items,
                    'total'     => $items->where('source', 'cost')->sum('amount')
                                    - $items->where('source', 'other_income')->sum('amount'),
                ];
            })->filter(fn($t) => $t['items']->isNotEmpty());

            return [
                'group_id'    => $group->id,
                'group_name'  => $group->name,
                'types'       => $types,
                'grand_total' => $types->sum(fn($t) => $t['total']),
            ];
        });

        return $report;
    }

    # Print Excel
    public function getExcel(Request $request)
    {
        $periode = $request->periode;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $sheet = $activeWorksheet->setTitle('Income Statement');

        #Set Orientation, size and scaling
        $activeWorksheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $activeWorksheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $activeWorksheet->getPageSetup()->setFitToPage(true);
        $activeWorksheet->getPageSetup()->setFitToWidth(1); //1
        $activeWorksheet->getPageSetup()->setFitToHeight(1); //0

        $activeWorksheet->getColumnDimension('A')->setWidth(15);
        $activeWorksheet->getColumnDimension('B')->setWidth(6);
        $activeWorksheet->getColumnDimension('C')->setWidth(10);
        $activeWorksheet->getColumnDimension('F')->setWidth(10);
        $activeWorksheet->getColumnDimension('H')->setWidth(17.5);
        $activeWorksheet->getColumnDimension('I')->setWidth(5);
        $activeWorksheet->getColumnDimension('J')->setWidth(15);
        $activeWorksheet->getColumnDimension('K')->setWidth(6);
        $activeWorksheet->getColumnDimension('G')->setWidth(6);
        $activeWorksheet->getColumnDimension('L')->setWidth(6);
        $activeWorksheet->getColumnDimension('O')->setWidth(4);

        #Print image
        $objDrawing = new Drawing();
        $objDrawing->setName('logo');
        $objDrawing->setDescription('logo');
        $objDrawing->setPath('assets/images/logo-indococo.png');
        $objDrawing->setCoordinates('A1');

        $objDrawing->setOffsetX(20);
        $objDrawing->setOffsetY(15);
        $objDrawing->setWidth(200);
        //$objDrawing->setheight(100);
        $objDrawing->setWorksheet($activeWorksheet);

        #kop surat
        setItalic($activeWorksheet->getStyle('E1'));
        setBold($activeWorksheet->getStyle('E1'), 'Calibri', 11);
        $sheet->setCellValue('E1', 'PT D&W Internasional');

        setItalic($activeWorksheet->getStyle('E2'));
        $sheet->setCellValue('E2', 'Office :');

        setFont($activeWorksheet->getStyle('E3'));
        $activeWorksheet->getRowDimension('3')->setRowHeight(11);
        $sheet->setCellValue('E3', 'Jl Jend A Yani (By Pass) Kawasan Grage City');

        setFont($activeWorksheet->getStyle('E4'));
        $activeWorksheet->getRowDimension('4')->setRowHeight(11);
        $sheet->setCellValue('E4', 'Biz Center Oasis Block A VII / 9 Cirebon 45113, West Java - Indonesia');


        setFont($activeWorksheet->getStyle('E5'));
        $activeWorksheet->getRowDimension('5')->setRowHeight(11);
        $sheet->setCellValue('E5', 'Tel. +62 231 8802888, 8802788');

        setFont($activeWorksheet->getStyle('E6'));
        $activeWorksheet->getRowDimension('6')->setRowHeight(11);
        $sheet->setCellValue('E6', 'Fax. +62 231 8491546');

        setFont($activeWorksheet->getStyle('E7'));
        $activeWorksheet->getRowDimension('7')->setRowHeight(11);
        $sheet->setCellValue('E7', 'Email. info@dw-corporation.com');


        setItalic($activeWorksheet->getStyle('L2'));
        $sheet->setCellValue('L2', 'Factory :');

        setFont($activeWorksheet->getStyle('L3'));
        $activeWorksheet->getRowDimension('3')->setRowHeight(11);
        $sheet->setCellValue('L3', 'Lengkong Wetan, Majalengka');

        setFont($activeWorksheet->getStyle('L4'));
        $activeWorksheet->getRowDimension('4')->setRowHeight(11);
        $sheet->setCellValue('L4', 'West Java - INDONESIA');

        $activeWorksheet->getRowDimension('8')->setRowHeight(12); //jarak 1 baris

        $activeWorksheet->getStyle('A8:N8')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        setUnderline($activeWorksheet->getStyle('A10'));
        setBold($activeWorksheet->getStyle('A10'), 'Arial', 12);
        $sheet->setCellValue('A10', 'INCOME STATEMENT');
        $sheet->mergeCells('A10:N10');
        setHorizontalAlignment($activeWorksheet->getStyle('A10:N10'), 'center');


        $documentMonth = date("F Y", strtotime($periode));

        setBold($activeWorksheet->getStyle('A11'), 'Arial', 12);
        $sheet->setCellValue('A11', $documentMonth);
        $sheet->mergeCells('A11:N11');
        setHorizontalAlignment($activeWorksheet->getStyle('A11:N11'), 'center');

        $salesData = $this->getSalesData($periode);

        # SALES :
        setUnderline($activeWorksheet->getStyle('A14'));
        $sheet->setCellValue('A14', 'SALES');
        $activeWorksheet->getRowDimension('15')->setRowHeight(9);

        // GROSS PROFIT :
        setBold($activeWorksheet->getStyle('A16'));
        $sheet->setCellValue('A16', 'Gross Profit :');

        $columnIndex = 17;
        if (count($salesData['sales']) > 0) {
            foreach ($salesData['sales'] as $netProfit) {
                setFont($activeWorksheet->getStyle("A$columnIndex"), 'Arial', 9.5);
                $sheet->setCellValue("A$columnIndex", $netProfit->date);
                $sheet->setCellValue("B$columnIndex", $netProfit->jobDesc());
                $sheet->mergeCells("B$columnIndex:H$columnIndex");
                $activeWorksheet->getStyle("B$columnIndex:H$columnIndex")->getAlignment()->setWrapText(true);

                //AMOUNT :
                setFont($activeWorksheet->getStyle("I$columnIndex"), 'Arial', 9.5);
                setHorizontalAlignment($activeWorksheet->getStyle("I$columnIndex"), 'right');
                $sheet->setCellValue("I$columnIndex", currencyFormat($netProfit->net_profit));
                $sheet->mergeCells("I$columnIndex:J$columnIndex");

                $columnIndex++;
            }
        }

        // TOTAL GROSS PROFIT :
        setBorderTop($activeWorksheet->getStyle("I$columnIndex:J$columnIndex"));
        $sheet->setCellValue('K' . ($columnIndex - 1), '+');

        setBold($activeWorksheet->getStyle("I$columnIndex"));
        setHorizontalAlignment($activeWorksheet->getStyle("I$columnIndex"), 'right');
        $sheet->setCellValue("I$columnIndex", currencyFormat($salesData['totalSalesGP']));
        $sheet->mergeCells("I$columnIndex:J$columnIndex");

        // TOTAL INCOME :
        setBold($activeWorksheet->getStyle('J' . ($columnIndex + 3)));
        setHorizontalAlignment($activeWorksheet->getStyle('J' . ($columnIndex + 3)), 'right');
        $sheet->setCellValue('J' . ($columnIndex + 3), 'TOTAL INCOME :');
        $sheet->mergeCells('J' . ($columnIndex + 3) . ':K' . ($columnIndex + 3));

        setHorizontalAlignment($activeWorksheet->getStyle('L' . ($columnIndex + 3)), 'right');
        setBold($activeWorksheet->getStyle('L' . ($columnIndex + 3)));
        $sheet->setCellValue('L' . ($columnIndex + 3), currencyFormat($salesData['totalSalesGP']));
        $sheet->mergeCells('L' . ($columnIndex + 3) . ':N' . ($columnIndex + 3));

        # EXPENSES :
        $expenseData = $this->getExpenseData($periode);

        setUnderline($activeWorksheet->getStyle('A' . ($columnIndex + 5)));
        $sheet->setCellValue('A' . ($columnIndex + 5), 'EXPENSES');

        $activeWorksheet->getRowDimension($columnIndex + 6)->setRowHeight(15);

        $startRow      = $columnIndex + 7;
        $totalExpenses = 0;
        foreach ($expenseData as $outcomeGroup) {
            // Group Name
            setBold($activeWorksheet->getStyle("A$startRow"));
            $sheet->setCellValue("A$startRow", $outcomeGroup['group_name']);
            $sheet->mergeCells("A{$startRow}:D{$startRow}");
            $startRow++;

            foreach ($outcomeGroup['types'] as $outcomeType) {
                    // Outcome Type Name
                    setBold($activeWorksheet->getStyle("A$startRow"));
                    $sheet->setCellValue("A$startRow", $outcomeType['type_name']);
                    $sheet->mergeCells("A{$startRow}:E{$startRow}");
                    $startRow++;

                    foreach ($outcomeType['items'] as $item) {
                        // Expense Item Details
                        setFont($activeWorksheet->getStyle("A$startRow"), 'Arial', 9.5);
                        $sheet->setCellValue("A$startRow", $item['date']);

                        $sheet->setCellValue("B$startRow", $item['description'] ?? '-');
                        $sheet->mergeCells("B$startRow:H$startRow");
                        $activeWorksheet->getStyle("B$startRow:H$startRow")->getAlignment()->setWrapText(true);

                        setFont($activeWorksheet->getStyle("I$startRow"), 'Arial', 9.5);
                        setHorizontalAlignment($activeWorksheet->getStyle("I$startRow"), 'right');
                        $amountPrefix = $item['source'] === 'other_income' ? '-' : '';
                        $sheet->setCellValue("I$startRow", currencyFormat($item['amount'], $amountPrefix));
                        $sheet->mergeCells("I$startRow:J$startRow");

                        $startRow++;
                    }

                    // Total Row
                    setBold($activeWorksheet->getStyle("I$startRow:J$startRow"), 'Arial', 9.5);
                    setBorderTop($activeWorksheet->getStyle("I$startRow:J$startRow"), 'medium');
                    setHorizontalAlignment($activeWorksheet->getStyle("I$startRow"), 'right');
                    $sheet->setCellValue("I$startRow", currencyFormat($outcomeType['total']));
                    $sheet->mergeCells("I$startRow:J$startRow");

                    $startRow += 2; // Add an extra row before next group/type for readability
            } // end foreach
    
            $totalExpenses += $outcomeGroup['grand_total'];
        } // end foreach

        // TOTAL EXPENSES :
        $startRow++;

        setBold($activeWorksheet->getStyle("J$startRow"));
        setHorizontalAlignment($activeWorksheet->getStyle("J$startRow"), 'right');
        $sheet->setCellValue("J$startRow", 'TOTAL EXPENSES :');
        $sheet->mergeCells("J$startRow" . ':K' . $startRow);

        setBold($activeWorksheet->getStyle("L$startRow"));
        setHorizontalAlignment($activeWorksheet->getStyle("L$startRow"), 'right');
        $sheet->setCellValue("L$startRow", currencyFormat($totalExpenses));
        $sheet->mergeCells("L$startRow:N$startRow");


        //TOTAL NET / LOSS PROFIT :
        $netLost = $salesData['totalSalesGP'] - $totalExpenses;


        $startRow += 2;
        setBold($activeWorksheet->getStyle("J$startRow"));
        setHorizontalAlignment($activeWorksheet->getStyle("J$startRow"), 'right');
        $sheet->setCellValue("J$startRow", 'NET / LOSS PROFIT :');
        $sheet->mergeCells("J$startRow:K$startRow");

        setBold($activeWorksheet->getStyle("L$startRow"));
        setBorderTop($activeWorksheet->getStyle("L$startRow:N$startRow"), 'medium');
        $sheet->setCellValue("L$startRow", currencyFormat($netLost));
        $sheet->mergeCells("L$startRow:N$startRow");
        setHorizontalAlignment($activeWorksheet->getStyle("L$startRow"), 'right');


        // LINE :
        $startRow += 2;
        setBorderTop($activeWorksheet->getStyle("A$startRow:O$startRow"), 'thick');

        // =====SIGNATURE :
        $startRow += 2;
        setFont($activeWorksheet->getStyle("A$startRow"));
        $sheet->setCellValue("A$startRow", 'Yogyakarta, ' . $documentMonth);

        $startRow += 4;
        setUnderline($activeWorksheet->getStyle("A$startRow"));
        setBold($activeWorksheet->getStyle("A$startRow"));
        $sheet->setCellValue("A$startRow", 'Fajar Stevano');

        $startRow++;
        setItalic($activeWorksheet->getStyle("A$startRow"));
        $sheet->setCellValue("A$startRow", 'Marketing Director');

        $filename = "Indococo - Income Statement $documentMonth.xlsx";

        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return response()->download($filename);
    }
}
