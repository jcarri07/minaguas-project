<?php
include '../php/Conexion.php';
require '../vendor/autoload.php';
require_once 'batimetria.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$id = $_GET["id"];
$type = $_GET["type"];

if($type == "excel"){

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

    $i = 2;
    foreach ($añoCotas as $cotas => $datos) {
        // $columnaLetra = chr(ord('A') + $filaIndice);
        // $celda = $columnaLetra . $i;
        $Cota = $cotas;
        $Volumen = explode("-", $datos)[0];
        $Superficie =  explode("-", $datos)[1];

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
exit();

}


if($type == "plantilla"){

    $Bocono = new Batimetria("1", $conn);
    $Batimetria = $Bocono->getBatimetria();
    
    $spreadsheet = new Spreadsheet();
    
    $datos = [
        'Juan' => ['Nombre', 'Edad', 'Ocupación'],
        'María' => ['Nombre', 'Edad', 'Ocupación'],
    ];
    
    // foreach ($Batimetria as $año => $añoCotas) {
        $spreadsheet->removeSheetByIndex(0);
        $sheet = $spreadsheet->createSheet();
    
        $sheet->setTitle("Ingresar Año");
    
        $sheet->setCellValue("A" . 1, "COTA");
        $sheet->setCellValue("B" . 1, "AREA");
        $sheet->setCellValue("C" . 1, "CAPACIDAD");
    
        // $i = 2;
        // foreach ($añoCotas as $cotas => $datos) {
        //     // $columnaLetra = chr(ord('A') + $filaIndice);
        //     // $celda = $columnaLetra . $i;
        //     $Cota = $cotas;
        //     $Volumen = explode("-", $datos)[0];
        //     $Superficie =  explode("-", $datos)[1];
    
        //     $sheet->setCellValue("A" . $i, $Cota);
        //     $sheet->setCellValue("B" . $i, $Volumen);
        //     $sheet->setCellValue("C" . $i, $Superficie);
        //     $i++;
        // }
    // }
    
    $writer = new Xlsx($spreadsheet);
    $excelFileName = 'archivo_excel.xlsx';
    $writer->save($excelFileName);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $excelFileName . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit();
    
    }
    

// php/download_excel_batimetria.php
