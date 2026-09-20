<?php
namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomerExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
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
        $query = Customer::whereRaw('CAST(code AS UNSIGNED) BETWEEN ? AND ?', [$fromCode, $toCode])
            ->select(
                'id',
                'code',
                'name',
                'tax',
                'no_rekening',
                'address',
                'country_id',
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
            'name',
            'tax',
            'no_rekening',
            'address',
            'country_id',
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

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getFont()->setSize(12);
        $sheet->getStyle('A1:L1')->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('A1:L1')->getFill()->getStartColor()->setRGB('4CAF50');
        $sheet->getStyle('A1:L1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'e.g. 1');
        $sheet->setCellValue('B2', 'e.g. 1');
        $sheet->setCellValue('C2', 'e.g. customer abc');
        $sheet->setCellValue('D2', 'e.g. tax/nontax');
        $sheet->setCellValue('E2', 'e.g. 1234567890');
        $sheet->setCellValue('F2', 'e.g. jl. contoh no.1');
        $sheet->setCellValue('G2', 'e.g. 123');
        $sheet->setCellValue('H2', 'e.g. 08123456789');
        $sheet->setCellValue('I2', 'e.g. 121-9876321');
        $sheet->setCellValue('J2', 'e.g. customerabc@example.com');
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
        $sheet->getStyle('B3:B' . $sheet->getHighestRow())->getProtection()->setLocked(true);

        $sheet->getStyle('F3:F' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);
        $sheet->getProtection()->setSheet(true);
        $sheet->getProtection()->setPassword('pssindococo');

        $sheet->setCellValue('M1', "README");
        $sheet->setCellValue('M2', "1. Data ini adalah data export dari database");
        $sheet->setCellValue('M3', "2. ID dan code jangan dihapus atau diganti");
        $sheet->setCellValue('M4', "3. Selain ID dan code, dapat diupdate");
        $sheet->setCellValue('M5', "4. Jangan merubah nama header (vendor_name,code,id dll)");
        $sheet->setCellValue('M6', "5. name wajib di isi, apabila tidak terisi maka data pada bagian vendor_name yang kosong tidak akan tersimpan.");

        $sheet->getStyle('M1')->getFont()->setBold(true);
        $sheet->getStyle('M1')->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle('M1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('M1')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('M1')->getFont()->setSize(10);

        $secondSheet = $spreadsheet->createSheet();
        $secondSheet->setTitle('country_id');

        $secondSheet->setCellValue('A1', 'country_id');
        $secondSheet->setCellValue('B1', 'country_code');
        $secondSheet->setCellValue('C1', 'country_name');

        $countries = [
            [1, '--', 'Unknown'],
            [2, 'AD', 'Andorra'],
            [3, 'AE', 'United Arab Emirates'],
            [4, 'AF', 'Afghanistan'],
            [5, 'AG', 'Antigua and Barbuda'],
            [6, 'AI', 'Anguilla'],
            [7, 'AL', 'Albania'],
            [8, 'AM', 'Armenia'],
            [9, 'AN', 'Netherlands Antilles'],
            [10, 'AO', 'Angola'],
            [11, 'AQ', 'Antarctica'],
            [12, 'AR', 'Argentina'],
            [13, 'AS', 'American Samoa'],
            [14, 'AT', 'Austria'],
            [15, 'AU', 'Australia'],
            [16, 'AW', 'Aruba'],
            [17, 'AX', 'Åland Islands'],
            [18, 'AZ', 'Azerbaijan'],
            [19, 'BA', 'Bosnia and Herzegovina'],
            [20, 'BB', 'Barbados'],
            [21, 'BD', 'Bangladesh'],
            [22, 'BE', 'Belgium'],
            [23, 'BF', 'Burkina Faso'],
            [24, 'BG', 'Bulgaria'],
            [25, 'BH', 'Bahrain'],
            [26, 'BI', 'Burundi'],
            [27, 'BJ', 'Benin'],
            [28, 'BL', 'Saint Barthélemy'],
            [29, 'BM', 'Bermuda'],
            [30, 'BN', 'Brunei Darussalam'],
            [31, 'BO', 'Bolivia'],
            [32, 'BQ', 'Bonaire'],
            [33, 'BR', 'Brazil'],
            [34, 'BS', 'Bahamas'],
            [35, 'BT', 'Bhutan'],
            [36, 'BV', 'Bouvet Island'],
            [37, 'BW', 'Botswana'],
            [38, 'BY', 'Belarus'],
            [39, 'BZ', 'Belize'],
            [40, 'CA', 'Canada'],
            [41, 'CC', 'Cocos (Keeling) Islands'],
            [42, 'CD', 'Congo, Democratic Republic'],
            [43, 'CF', 'Central African Republic'],
            [44, 'CG', 'Congo'],
            [45, 'CH', 'Switzerland'],
            [46, 'CI', 'Côte d\'Ivoire'],
            [47, 'CK', 'Cook Islands'],
            [48, 'CL', 'Chile'],
            [49, 'CM', 'Cameroon'],
            [50, 'CN', 'China'],
            [51, 'CO', 'Colombia'],
            [52, 'CR', 'Costa Rica'],
            [53, 'CU', 'Cuba'],
            [54, 'CV', 'Cape Verde'],
            [55, 'CW', 'Curaçao'],
            [56, 'CX', 'Christmas Island'],
            [57, 'CY', 'Cyprus'],
            [58, 'CZ', 'Czech Republic'],
            [59, 'DE', 'Germany'],
            [60, 'DJ', 'Djibouti'],
            [61, 'DK', 'Denmark'],
            [62, 'DM', 'Dominica'],
            [63, 'DO', 'Dominican Republic'],
            [64, 'DZ', 'Algeria'],
            [65, 'EC', 'Ecuador'],
            [66, 'EE', 'Estonia'],
            [67, 'EG', 'Egypt'],
            [68, 'EH', 'Western Sahara'],
            [69, 'ER', 'Eritrea'],
            [70, 'ES', 'Spain'],
            [71, 'ET', 'Ethiopia'],
            [72, 'FI', 'Finland'],
            [73, 'FJ', 'Fiji'],
            [74, 'FK', 'Falkland Islands (Malvinas)'],
            [75, 'FM', 'Micronesia, Federated States of'],
            [76, 'FO', 'Faroe Islands'],
            [77, 'FR', 'France'],
            [78, 'GA', 'Gabon'],
            [79, 'GB', 'United Kingdom'],
            [80, 'GD', 'Grenada'],
            [81, 'GE', 'Georgia'],
            [82, 'GF', 'French Guiana'],
            [83, 'GG', 'Guernsey'],
            [84, 'GH', 'Ghana'],
            [85, 'GI', 'Gibraltar'],
            [86, 'GL', 'Greenland'],
            [87, 'GM', 'Gambia'],
            [88, 'GN', 'Guinea'],
            [89, 'GP', 'Guadeloupe'],
            [90, 'GQ', 'Equatorial Guinea'],
            [91, 'GR', 'Greece'],
            [92, 'GS', 'South Georgia and the South Sandwich Islands'],
            [93, 'GT', 'Guatemala'],
            [94, 'GU', 'Guam'],
            [95, 'GW', 'Guinea-Bissau'],
            [96, 'GY', 'Guyana'],
            [97, 'HI', 'Hawaii'],
            [98, 'HK', 'Hong Kong'],
            [99, 'HM', 'Heard Island and McDonald Islands'],
            [100, 'HN', 'Honduras'],
            [101, 'HR', 'Croatia'],
            [102, 'HT', 'Haiti'],
            [103, 'HU', 'Hungary'],
            [104, 'ID', 'Indonesia'],
            [105, 'IE', 'Ireland'],
            [106, 'IL', 'Israel'],
            [107, 'IM', 'Isle of Man'],
            [108, 'IN', 'India'],
            [109, 'IO', 'British Indian Ocean Territory'],
            [110, 'IQ', 'Iraq'],
            [111, 'IR', 'Iran, Islamic Republic of'],
            [112, 'IS', 'Iceland'],
            [113, 'IT', 'Italy'],
            [114, 'JE', 'Jersey'],
            [115, 'JM', 'Jamaica'],
            [116, 'JO', 'Jordan'],
            [117, 'JP', 'Japan'],
            [118, 'KE', 'Kenya'],
            [119, 'KG', 'Kyrgyzstan'],
            [120, 'KH', 'Cambodia'],
            [121, 'KI', 'Kiribati'],
            [122, 'KM', 'Comoros'],
            [123, 'KN', 'Saint Kitts and Nevis'],
            [124, 'KP', 'Korea, Democratic People\'s Republic of'],
            [125, 'KR', 'Korea, Republic of'],
            [126, 'KW', 'Kuwait'],
            [127, 'KY', 'Cayman Islands'],
            [128, 'KZ', 'Kazakhstan'],
            [129, 'LA', 'Lao People\'s Democratic Republic'],
            [130, 'LB', 'Lebanon'],
            [131, 'LC', 'Saint Lucia'],
            [132, 'LI', 'Liechtenstein'],
            [133, 'LK', 'Sri Lanka'],
            [134, 'LR', 'Liberia'],
            [135, 'LS', 'Lesotho'],
            [136, 'LT', 'Lithuania'],
            [137, 'LU', 'Luxembourg'],
            [138, 'LV', 'Latvia'],
            [139, 'LY', 'Libya'],
            [140, 'MA', 'Morocco'],
            [141, 'MC', 'Monaco'],
            [142, 'MD', 'Moldova, Republic of'],
            [143, 'ME', 'Montenegro'],
            [144, 'MF', 'Saint Martin (French part)'],
            [145, 'MG', 'Madagascar'],
            [146, 'MH', 'Marshall Islands'],
            [147, 'MK', 'Macedonia, the Former Yugoslav Republic of'],
            [148, 'ML', 'Mali'],
            [149, 'MM', 'Myanmar'],
            [150, 'MN', 'Mongolia'],
            [151, 'MO', 'Macao'],
            [152, 'MP', 'Northern Mariana Islands'],
            [153, 'MQ', 'Martinique'],
            [154, 'MR', 'Mauritania'],
            [155, 'MS', 'Montserrat'],
            [156, 'MT', 'Malta'],
            [157, 'MU', 'Mauritius'],
            [158, 'MV', 'Maldives'],
            [159, 'MW', 'Malawi'],
            [160, 'MX', 'Mexico'],
            [161, 'MY', 'Malaysia'],
            [162, 'MZ', 'Mozambique'],
            [163, 'NA', 'Namibia'],
            [164, 'NC', 'New Caledonia'],
            [165, 'NE', 'Niger'],
            [166, 'NF', 'Norfolk Island'],
            [167, 'NG', 'Nigeria'],
            [168, 'NI', 'Nicaragua'],
            [169, 'NL', 'Netherlands'],
            [170, 'NO', 'Norway'],
            [171, 'NP', 'Nepal'],
            [172, 'NR', 'Nauru'],
            [173, 'NU', 'Niue'],
            [174, 'NZ', 'New Zealand'],
            [175, 'OM', 'Oman'],
            [176, 'PA', 'Panama'],
            [177, 'PE', 'Peru'],
            [178, 'PF', 'French Polynesia'],
            [179, 'PG', 'Papua New Guinea'],
            [180, 'PH', 'Philippines'],
            [181, 'PK', 'Pakistan'],
            [182, 'PL', 'Poland'],
            [183, 'PM', 'Saint Pierre and Miquelon'],
            [184, 'PN', 'Pitcairn'],
            [185, 'PR', 'Puerto Rico'],
            [186, 'PS', 'Palestine, State of'],
            [187, 'PT', 'Portugal'],
            [188, 'PW', 'Palau'],
            [189, 'PY', 'Paraguay'],
            [190, 'QA', 'Qatar'],
            [191, 'RE', 'Réunion'],
            [192, 'RO', 'Romania'],
            [193, 'RS', 'Serbia'],
            [194, 'RU', 'Russian Federation'],
            [195, 'RW', 'Rwanda'],
            [196, 'SA', 'Saudi Arabia'],
            [197, 'SB', 'Solomon Islands'],
            [198, 'SC', 'Seychelles'],
            [199, 'SD', 'Sudan'],
            [200, 'SE', 'Sweden'],
            [201, 'SG', 'Singapore'],
            [202, 'SH', 'Saint Helena, Ascension and Tristan da Cunha'],
            [203, 'SI', 'Slovenia'],
            [204, 'SJ', 'Svalbard and Jan Mayen'],
            [205, 'SK', 'Slovakia'],
            [206, 'SL', 'Sierra Leone'],
            [207, 'SM', 'San Marino'],
            [208, 'SN', 'Senegal'],
            [209, 'SO', 'Somalia'],
            [210, 'SR', 'Suriname'],
            [211, 'SS', 'South Sudan'],
            [212, 'ST', 'Sao Tome and Principe'],
            [213, 'SV', 'El Salvador'],
            [214, 'SX', 'Sint Maarten (Dutch part)'],
            [215, 'SY', 'Syrian Arab Republic'],
            [216, 'SZ', 'Swaziland'],
            [217, 'TC', 'Turks and Caicos Islands'],
            [218, 'TD', 'Chad'],
            [219, 'TF', 'French Southern Territories'],
            [220, 'TG', 'Togo'],
            [221, 'TH', 'Thailand'],
            [222, 'TJ', 'Tajikistan'],
            [223, 'TK', 'Tokelau'],
            [224, 'TL', 'Timor-Leste'],
            [225, 'TM', 'Turkmenistan'],
            [226, 'TN', 'Tunisia'],
            [227, 'TO', 'Tonga'],
            [228, 'TR', 'Turkey'],
            [229, 'TT', 'Trinidad and Tobago'],
            [230, 'TV', 'Tuvalu'],
            [231, 'TW', 'Taiwan, Province of China'],
            [232, 'TZ', 'Tanzania, United Republic of'],
            [233, 'UA', 'Ukraine'],
            [234, 'UG', 'Uganda'],
            [235, 'UM', 'United States Minor Outlying Islands'],
            [236, 'US', 'United States'],
            [237, 'UY', 'Uruguay'],
            [238, 'UZ', 'Uzbekistan'],
            [239, 'VA', 'Holy See (Vatican City)'],
            [240, 'VC', 'Saint Vincent and the Grenadines'],
            [241, 'VE', 'Venezuela, Bolivarian Republic of'],
            [242, 'VG', 'Virgin Islands, British'],
            [243, 'VI', 'Virgin Islands, U.S.'],
            [244, 'VN', 'Vietnam'],
            [245, 'VU', 'Vanuatu'],
            [246, 'WF', 'Wallis and Futuna'],
            [247, 'WS', 'Samoa'],
            [248, 'YE', 'Yemen'],
            [249, 'YT', 'Mayotte'],
            [250, 'ZA', 'South Africa'],
            [251, 'ZM', 'Zambia'],
            [252, 'ZW', 'Zimbabwe'],
        ];


        $secondSheet->fromArray($countries, NULL, 'A2');

        $secondSheet->getColumnDimension('A')->setWidth(15);
        $secondSheet->getColumnDimension('B')->setWidth(15);
        $secondSheet->getColumnDimension('C')->setWidth(30);

        $secondSheet->getStyle('A1:C' . (count($countries) + 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    }

    public function shouldAutoSize(): bool
    {
        return true;
    }
}
