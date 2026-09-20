<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

function setBold($param, $font = 'Arial', $size = 10)
{
    return $param->getFont()->setBold(TRUE)->setName($font)->setSize($size);
}

function setBorderTop($param, $border = 'medium')
{
    $thickness = [
        'thin'   => Border::BORDER_THIN,
        'medium' => Border::BORDER_MEDIUM,
        'thick'  => Border::BORDER_MEDIUM
    ];

    return $param->getBorders()->getTop()->setBorderStyle($thickness[$border]);
}

function setFont($param, $font = 'Arial', $size = 9)
{
    return $param->getFont()->setName($font)->setSize($size);
}

function setHorizontalAlignment($param, $alignment = 'right')
{
    $align = [
        'left'   => Alignment::HORIZONTAL_LEFT,
        'center' => Alignment::HORIZONTAL_CENTER,
        'right'  => Alignment::HORIZONTAL_RIGHT
    ];

    return $param->getAlignment()->setHorizontal($align[$alignment]);
}

function setItalic($param, $font = 'Arial', $size = 10)
{
    return $param->getFont()->setItalic(TRUE)->setName($font)->setSize($size);
}

function setUnderline($param, $font = 'Arial', $size = 11)
{
    return $param->getFont()->setUnderline(TRUE)->setName($font)->setSize($size);
}
