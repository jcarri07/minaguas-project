<?php
include '../php/Conexion.php';
require '../vendor/autoload.php';
require_once 'batimetria.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$type = $_GET["type"];

if ($type == "excel") {

    $id = $_GET["id"];
    $Bocono = new Batimetria($id, $conn);
    $Batimetria = $Bocono->getBatimetria();


    $spreadsheet = new Spreadsheet();
    $spreadsheet->removeSheetByIndex(0);


    $datos = [
        'Juan' => ['Nombre', 'Edad', 'Ocupación'],
        'María' => ['Nombre', 'Edad', 'Ocupación'],
    ];

    foreach ($Batimetria as $año => $añoCotas) {
        $sheet = $spreadsheet->createSheet();

        $sheet->setTitle((string)$año);

        $sheet->setCellValue("A" . 1, "COTA");
        $sheet->setCellValue("B" . 1, "AREA");
        $sheet->setCellValue("C" . 1, "CAPACIDAD");
        $sheet->getColumnDimension("A")->setWidth(15);
        $sheet->getColumnDimension("B")->setWidth(15);
        $sheet->getColumnDimension("C")->setWidth(15);
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $color = '5E72E4';
        $sheet->getStyle("A1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
        $sheet->getStyle("B1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
        $sheet->getStyle("C1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);

        $sheet->getStyle("A1")->getFont()->getColor()->setARGB('FFFFFF');
        $sheet->getStyle("B1")->getFont()->getColor()->setARGB('FFFFFF');
        $sheet->getStyle("C1")->getFont()->getColor()->setARGB('FFFFFF');

        $i = 2;
        foreach ($añoCotas as $cotas => $datos) {
            // $columnaLetra = chr(ord('A') + $filaIndice);
            // $celda = $columnaLetra . $i;
            $Cota = $cotas;
            // $Volumen = explode("-", $datos)[0];
            // $Superficie =  explode("-", $datos)[1];
            $Volumen = explodeBat($datos, 0);
            $Superficie = explodeBat($datos, 1);

            $sheet->setCellValue("A" . $i, $Cota);
            $sheet->setCellValue("B" . $i, $Volumen);
            $sheet->setCellValue("C" . $i, $Superficie);
            $i++;
        }
    }

    $writer = new Xlsx($spreadsheet);
    $excelFileName = 'archivo_excel.xlsx';
    $writer->save($excelFileName);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $excelFileName . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}


if ($type == "plantilla") {

    $Bocono = new Batimetria("1", $conn);
    $Batimetria = $Bocono->getBatimetria();

    $spreadsheet = new Spreadsheet();
    $spreadsheet->removeSheetByIndex(0);

    $sheet = $spreadsheet->createSheet();

    $sheet->setTitle("Ingresar Año");

    $sheet->setCellValue("A" . 1, "COTA");
    $sheet->setCellValue("B" . 1, "AREA");
    $sheet->setCellValue("C" . 1, "CAPACIDAD");

    $sheet->getColumnDimension("A")->setWidth(15);
    $sheet->getColumnDimension("B")->setWidth(15);
    $sheet->getColumnDimension("C")->setWidth(15);
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("C1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $color = '5E72E4';
    $sheet->getStyle("A1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
    $sheet->getStyle("B1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
    $sheet->getStyle("C1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);

    $sheet->getStyle("A1")->getFont()->getColor()->setARGB('FFFFFF');
    $sheet->getStyle("B1")->getFont()->getColor()->setARGB('FFFFFF');
    $sheet->getStyle("C1")->getFont()->getColor()->setARGB('FFFFFF');

    $writer = new Xlsx($spreadsheet);
    $excelFileName = 'archivo_excel.xlsx';
    $writer->save($excelFileName);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $excelFileName . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}

function explodeBat($value, $i = null)
{

    $pattern = "/^(-?[\d,.]+)-(-?[\d,.]+)$/";

    if (preg_match($pattern, $value, $matches)) {
        $valores = [$matches[1], $matches[2]]; // Valores capturados

        if ($i !== null) {
            return $valores[$i];
        } else {
            return $valores;
        }
    } else {
        $valores = [1, 1]; // Valores predeterminados en caso de no coincidencia

        if ($i !== null) {
            return $valores[$i];
        } else {
            return $valores;
        }
    }
}

exit();
// php/download_excel_batimetria.php
