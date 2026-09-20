<?php
namespace App\Exports;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class VendorExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
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
        $fromCode = (int) $this->fromCode;
        $toCode = (int) $this->toCode;
        $query = Vendor::whereRaw('CAST(code AS UNSIGNED) BETWEEN ? AND ?', [$fromCode, $toCode])
            ->select(
                'id',
                'code',
                'vendor_name',
                'pkp',
                'no_rekening',
                'address',
                'telp',
                'fax',
                'email',
                'contact',
                'note'
            );

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'code',
            'vendor_name',
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
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFont()->setSize(12);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1:K1')->getFill()->getStartColor()->setRGB('4CAF50');
        $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('A1:K' . (count($this->collection()) + 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle('A2:K' . (count($this->collection()) + 1))->getFont()->setSize(10);

        $sheet->setCellValue('A2', 'e.g. 1'); //id
        $sheet->setCellValue('B2', 'e.g. 1'); //code
        $sheet->setCellValue('C2', 'e.g. vendor abc'); 
        $sheet->setCellValue('D2', 'e.g. pkp/nonpkp');
        $sheet->setCellValue('E2', 'e.g. 1234567890');
        $sheet->setCellValue('F2', 'e.g. jl. contoh no.1'); //address ya ges
        $sheet->setCellValue('G2', 'e.g. 08123456789'); //telp
        $sheet->setCellValue('H2', 'e.g. 121-9876321');
        $sheet->setCellValue('I2', 'e.g. vendorabc@example.com');
        $sheet->setCellValue('J2', 'e.g. john doe');

        $sheet->getStyle('A2:K2')->getFont()->setSize(10);
        $sheet->getStyle('A2:K2')->getFont()->getColor()->setRGB('000000');
        $sheet->getStyle('A2:K2')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A2:K2')->getFill()->getStartColor()->setRGB('D3D3D3');
        $sheet->getStyle('A2:K2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $sheet->fromArray($this->collection()->toArray(), NULL, 'A3');
        $sheet->getStyle('A3:K' . $sheet->getHighestRow())->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A3:K' . $sheet->getHighestRow())->getProtection()->setLocked(false);

        $sheet->getStyle('A1:K1')->getProtection()->setLocked(true);
        $sheet->getStyle('A2:K2')->getProtection()->setLocked(true);
        $sheet->getStyle('A3:A' . $sheet->getHighestRow())->getProtection()->setLocked(true);
        $sheet->getStyle('B3:B' . $sheet->getHighestRow())->getProtection()->setLocked(true);

        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('pssindococo');

        $sheet->setCellValue('L1', "README");
        $sheet->setCellValue('L2', "1. Data ini adalah data export dari database");
        $sheet->setCellValue('L3', "2. ID dan code jangan dihapus atau diganti");
        $sheet->setCellValue('L4', "3. Selain ID dan code, dapat diupdate");
        $sheet->setCellValue('L5', "4. Jangan merubah nama header (vendor_name,code,id dll)");
        $sheet->setCellValue('L6', "5. vendor_name wajib di isi, apabila tidak terisi maka data pada bagian vendor_name yang kosong tidak akan tersimpan.");

        $sheet->getStyle('L1')->getFont()->setBold(true);
        $sheet->getStyle('L1')->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle('L1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('L1')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('L1')->getFont()->setSize(10);


    }

    public function shouldAutoSize(): bool
    {
        return true;
    }
}
