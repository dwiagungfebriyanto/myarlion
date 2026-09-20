<?php
namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SupplierExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $fromCode;
    protected $toCode;

    public function __construct($fromCode, $toCode)
    {
        $this->fromCode = $fromCode;
        $this->toCode = $toCode;
    }

    public function collection()
    {
        return Supplier::whereBetween('code', [$this->fromCode, $this->toCode])
            ->select(
                'id',
                'main_category_id',
                'code',
                'supplier_name',
                'pkp',
                'no_rekening',
                'address',
                'telp',
                'fax',
                'email',
                'contact',
                'note'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'main_category_id',
            'code',
            'supplier_name',
            'pkp',
            'no_rekening',
            'address',
            'telp',
            'fax',
            'email',
            'contact',
            'note'
        ];
    }

    public function styles($sheet)
{
    $spreadsheet = $sheet->getParent();

    $sheet->getStyle('A1:L' . $sheet->getHighestRow())->getProtection()->setLocked(false);

    $sheet->getStyle('A1:L1')->getFont()->setBold(true);
    $sheet->getStyle('A1:L1')->getFont()->setSize(12);
    $sheet->getStyle('A1:L1')->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A1:L1')->getFill()->getStartColor()->setRGB('4CAF50');
    $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('A2', 'e.g. 1');
    $sheet->setCellValue('B2', 'e.g. 1');
    $sheet->setCellValue('C2', 'e.g. 001');
    $sheet->setCellValue('D2', 'e.g. supplier abc');
    $sheet->setCellValue('E2', 'e.g. pkp/nonpkp');
    $sheet->setCellValue('F2', 'e.g. 1234567890');
    $sheet->setCellValue('G2', 'e.g. jl. contoh no.1');
    $sheet->setCellValue('H2', 'e.g. 08123456789');
    $sheet->setCellValue('I2', 'e.g. 121-9876321');
    $sheet->setCellValue('J2', 'e.g. supplierabc@example.com');
    $sheet->setCellValue('K2', 'e.g. john doe');
    $sheet->setCellValue('L2', 'e.g. catatan');

    $sheet->getStyle('A2:L2')->getFont()->setSize(10);
    $sheet->getStyle('A2:L2')->getFont()->getColor()->setRGB('000000');
    $sheet->getStyle('A2:L2')->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle('A2:L2')->getFill()->getStartColor()->setRGB('D3D3D3');
    $sheet->getStyle('A2:L2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    $sheet->fromArray($this->collection()->toArray(), NULL, 'A3');
    $sheet->getStyle('A3:L' . $sheet->getHighestRow())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle('A3:L' . $sheet->getHighestRow())->getProtection()->setLocked(false);

    $sheet->getStyle('A1:L1')->getProtection()->setLocked(true);
    $sheet->getStyle('A2:L2')->getProtection()->setLocked(true);
    $sheet->getStyle('A3:A' . $sheet->getHighestRow())->getProtection()->setLocked(true);
    $sheet->getStyle('C3:C' . $sheet->getHighestRow())->getProtection()->setLocked(true);

    $sheet->getProtection()->setSheet(true);
    $sheet->getProtection()->setPassword('pssindococo');

    $sheet->setCellValue('M1', "README");
    $sheet->setCellValue('M2', "1. Data ini adalah data export dari database");
    $sheet->setCellValue('M3', "2. ID dan code jangan dihapus atau diganti");
    $sheet->setCellValue('M4', "3. Selain ID dan code, cell dapat diupdate");
    $sheet->setCellValue('M5', "4. Jangan merubah nama header (supplier_name, code, id dll)");
    $sheet->setCellValue('M6', "5. supplier_name wajib diisi, apabila tidak terisi maka data pada bagian supplier_name yang kosong tidak akan tersimpan.");

    $sheet->getStyle('M1')->getFont()->setBold(true);
    $sheet->getStyle('M1')->getFont()->getColor()->setRGB('FF0000');
    $sheet->getStyle('M1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    $sheet->getStyle('M1')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
    $sheet->getStyle('M1')->getFont()->setSize(10);

   

    $secondSheet = $spreadsheet->createSheet();
    $secondSheet->setTitle('main_category_id');

    $secondSheet->setCellValue('A1', 'main Category ID');
    $secondSheet->setCellValue('B1', 'Main Category Name');

    $categories = [
        [1, 'Kelapa & Turunannya'],
        [2, 'Karet & Olahannya'],
        [3, 'Biomassa'],
        [4, 'Hasil Alam'],
        [5, 'Kebutuhan Pertanian & Hobi'],
        [2, 'Peternakan dan Pet Supplier'],
        [3, 'Mesin'],
        [4, '--No Data--'],
        [5, 'Others'],
    ];

    $secondSheet->fromArray($categories, NULL, 'A2');

    $secondSheet->getColumnDimension('A')->setWidth(15);
    $secondSheet->getColumnDimension('B')->setWidth(30);

    $secondSheet->getStyle('A1:B' . (count($categories) + 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
}


    public function shouldAutoSize(): bool
    {
        return true;
    }
}
