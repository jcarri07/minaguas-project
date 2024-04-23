<?php
include '../php/Conexion.php';
require '../vendor/autoload.php';
require_once 'batimetria.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$archivo = $_FILES["excel"]['name'];
$archivo_tmp = $_FILES["excel"]['tmp_name'];


$spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($archivo_tmp);

$datos_embalses = array();
$num = 0;

$consulta = "INSERT INTO embalses (nombre_embalse, nombre_presa, cuenca_principal, afluentes_principales, operador, autoridad_responsable, proyectista, constructor, inicio_construccion, duracion_de_construccion, inicio_de_operacion, monitoreo_del_embalse, vida_util, cota_min, cota_nor, cota_max, vol_min, vol_nor, vol_max, sup_min, sup_nor, sup_max, numero_de_presas, tipo_de_presa, altura, talud_aguas_arriba, talud_aguas_abajo, longitud_cresta, cota_cresta, ancho_cresta, volumen_terraplen, ancho_base, ubicacion_aliviadero, tipo_aliviadero, numero_compuertas_aliviadero, carga_vertedero, descarga_maxima, longitud_aliviadero, ubicacion_toma, tipo_toma, numero_compuertas_toma, mecanismos_de_emergencia, mecanismos_de_regulacion, gasto_maximo, descarga_de_fondo, posee_obra, tipo_de_obra, poblacion_beneficiada, area_de_riego_beneficiada, area_protegida, poblacion_protegida, f_cargo, f_cedula, f_nombres, f_apellidos, f_telefono, f_correo, estatus) 
VALUES ";

foreach ($spreadsheet->getSheetNames() as $sheetName) {
    $sheet = $spreadsheet->getSheetByName($sheetName);
    $highestRow = $sheet->getHighestRow();

    $datos = array();

    for ($row = 4; $row <= $highestRow; $row++) {


        $nombre_embalse = mayus($sheet->getCell('C' . $row)->getValue()); //LISTO
        $nombre_presa = mayus($sheet->getCell('BK' . $row)->getValue()); //LISTO
        // $norte = $sheet->getCell('C' . $row)->getValue();
        // $este = $sheet->getCell('C' . $row)->getValue();
        // $huso = $sheet->getCell('C' . $row)->getValue();
        $cuenca = mayus($sheet->getCell('M' . $row)->getValue()); //LISTO
        $afluentes = mayus($sheet->getCell('L' . $row)->getValue()); //LISTO
        // $area = $sheet->getCell('C' . $row)->getValue();
        // $escurrimiento = $sheet->getCell('C' . $row)->getValue();
        // $ubicacion_embalse = $sheet->getCell('C' . $row)->getValue();
        // $organo = $sheet->getCell('C' . $row)->getValue();
        // $personal = $sheet->getCell('C' . $row)->getValue();
        $operador = mayus($sheet->getCell('K' . $row)->getValue()); //LISTO
        $autoridad = mayus($sheet->getCell('AS' . $row)->getValue()); //LISTO
        $proyectista = mayus($sheet->getCell('AN' . $row)->getValue()); //LISTO
        $constructor = mayus($sheet->getCell('AO' . $row)->getValue()); //LISTO
        $inicio_construccion = $sheet->getCell('AQ' . $row)->getValue(); //LISTO
        $duracion_construccion = $sheet->getCell('AR' . $row)->getValue(); //LISTO
        $inicio_operacion = $sheet->getCell('J' . $row)->getValue(); //LISTO
        $monitoreo = mayus($sheet->getCell('AT' . $row)->getValue()); //LISTO
        $vida_util = $sheet->getCell('BI' . $row)->getValue(); //LISTO
        $cota_min = $sheet->getCell('BB' . $row)->getValue(); //LISTO
        $volumen_min = $sheet->getCell('BC' . $row)->getValue(); //LISTO
        $superficie_min = $sheet->getCell('BD' . $row)->getValue(); //LISTO
        $cota_nor = $sheet->getCell('AY' . $row)->getValue(); //LISTO
        $volumen_nor = $sheet->getCell('AZ' . $row)->getValue(); //LISTO
        $superficie_nor = $sheet->getCell('BA' . $row)->getValue(); //LISTO
        $cota_max = $sheet->getCell('AV' . $row)->getValue(); //LISTO
        $volumen_max = $sheet->getCell('AW' . $row)->getValue(); //LISTO
        $superficie_max = $sheet->getCell('AX' . $row)->getValue(); //LISTO
        $numero_presas = $sheet->getCell('BL' . $row)->getValue(); //LISTO
        $tipo_presa = mayus($sheet->getCell('BM' . $row)->getValue()); //LISTO
        $altura = $sheet->getCell('BN' . $row)->getValue(); //LISTO
        $talud_arriba = $sheet->getCell('BR' . $row)->getValue(); //LISTO
        $talud_abajo = $sheet->getCell('BS' . $row)->getValue(); //LISTO
        $longitud_cresta = $sheet->getCell('BO' . $row)->getValue(); //LISTO
        $cota_cresta = $sheet->getCell('BQ' . $row)->getValue(); //LISTO
        $ancho_cresta = $sheet->getCell('BP' . $row)->getValue(); //LISTO
        $volumen_terraplen = $sheet->getCell('BT' . $row)->getValue(); //LISTO
        $ancho_base = $sheet->getCell('BU' . $row)->getValue(); //LISTO
        $ubicacion_aliviadero = mayus($sheet->getCell('BY' . $row)->getValue()); //LISTO
        $tipo_aliviadero = mayus($sheet->getCell('BX' . $row)->getValue()); //LISTO
        $numero_compuertas_aliviadero = $sheet->getCell('BZ' . $row)->getValue(); //LISTO
        $carga_aliviadero = $sheet->getCell('CB' . $row)->getValue(); //LISTO
        $descarga_aliviadero = $sheet->getCell('CC' . $row)->getValue(); //LISTO
        $longitud_aliviadero = $sheet->getCell('CA' . $row)->getValue(); //LISTO
        $ubicacion_toma = mayus($sheet->getCell('CH' . $row)->getValue()); //LISTO ?
        $tipo_toma = mayus($sheet->getCell('CG' . $row)->getValue()); //LISTO ?
        $numero_compuertas_toma = $sheet->getCell('CI' . $row)->getValue(); //LISTO ?
        $emergencia_toma = $sheet->getCell('CJ' . $row)->getValue(); //LISTO ?
        $regulacion_toma = $sheet->getCell('CK' . $row)->getValue(); //LISTO ?
        $gasto_toma = $sheet->getCell('CL' . $row)->getValue(); //LISTO ?
        $descarga_fondo = $sheet->getCell('CV' . $row)->getValue(); //LISTO
        $obra_conduccion = mayus($sheet->getCell('CY' . $row)->getValue()); //LISTO
        $tipo_conduccion = mayus($sheet->getCell('CZ' . $row)->getValue()); //LISTO
        // $accion_conduccion = mayus($sheet->getCell('C' . $row)->getValue()); 
        $poblacion = $sheet->getCell('AB' . $row)->getValue(); //LISTO
        $area_riego = $sheet->getCell('R' . $row)->getValue(); //LISTO
        $area_protegida = $sheet->getCell('T' . $row)->getValue(); //LISTO
        $poblacion_protegida = $sheet->getCell('X' . $row)->getValue(); //LISTO
        // $produccion_hidro = $sheet->getCell('C' . $row)->getValue();
        $f_cargo = mayus($sheet->getCell('DE' . $row)->getValue());  //LISTO
        $f_cedula = $sheet->getCell('DF' . $row)->getValue(); //LISTO
        $f_nombres = mayus($sheet->getCell('DG' . $row)->getValue()); //LISTO
        $f_apellidos = mayus($sheet->getCell('DH' . $row)->getValue()); //LISTO
        $f_telefono = mayus($sheet->getCell('DI' . $row)->getValue()); //LISTO
        $f_correo = mayus($sheet->getCell('DJ' . $row)->getValue()); //LISTO


        $add_embalse = "('$nombre_embalse', '$nombre_presa' , '$cuenca', '$afluentes' ,'$operador', '$autoridad', '$proyectista', '$constructor', '$inicio_construccion', '$duracion_construccion', '$inicio_operacion', '$monitoreo', '$vida_util', '$cota_min', '$cota_nor', '$cota_max', '$volumen_min', '$volumen_nor', '$volumen_max', '$superficie_min', '$superficie_nor', '$superficie_max', '$numero_presas', '$tipo_presa', '$altura', '$talud_arriba', '$talud_abajo', '$longitud_cresta', '$cota_cresta', '$ancho_cresta', '$volumen_terraplen', '$ancho_base', '$ubicacion_aliviadero', '$tipo_aliviadero', '$numero_compuertas_aliviadero', '$carga_aliviadero', '$descarga_aliviadero', '$longitud_aliviadero', '$ubicacion_toma', '$tipo_toma', '$numero_compuertas_toma', '$emergencia_toma', '$regulacion_toma', '$gasto_toma', '$descarga_fondo', '$obra_conduccion', '$tipo_conduccion', '$poblacion', '$area_riego', '$area_protegida', '$poblacion_protegida', '$f_cargo', '$f_cedula', '$f_nombres', '$f_apellidos', '$f_telefono', '$f_correo', 'activo')";
        if ($row < $highestRow) {
            $add_embalse .= ",";
        } else {
            $add_embalse .= ";";
        }
        $consulta = $consulta . $add_embalse;
    }
    // echo mayus($consulta);
}


$resultado = mysqli_query($conn, $consulta);
if ($resultado) {

    echo "Los datos se guardaron correctamente en la base de datos.";
    // header("Location: ../main.php?page=embalses");
    // echo "<script>window.location='../main.php?page=embalses';</script>";
} else {
    echo "Ocurrió un error al guardar los datos: " . mysqli_error($conn);
}


function mayus($cadena)
{
    // if (!is_string($cadena)) {
    //     return $cadena;
    // }
    // Capitalizamos la primera letra de la cadena
    // Obtenemos la primera letra y la convertimos a mayúscula
    $primera_letra = mb_strtoupper(mb_substr($cadena, 0, 1), 'UTF-8');
    // Obtenemos el resto de la cadena y lo convertimos a minúsculas
    $resto_cadena = mb_strtolower(mb_substr($cadena, 1), 'UTF-8');
    // Combinamos la primera letra capitalizada con el resto de la cadena
    $cadena_capitalizada = $primera_letra . $resto_cadena;
    // Devolvemos la cadena capitalizada
    return $cadena_capitalizada;
}

exit();
// php/download_excel_batimetria.php
