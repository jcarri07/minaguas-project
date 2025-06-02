<?php
// Conectar a la base de datos minagua_db
// $host = "localhost";
// $username = "root";
// $password = "";
// $database = "minagua_db";

// $conexion = mysqli_connect($host, $username, $password, $database) or die(mysqli_error());

// require_once "../database/Conexion.php";
include '../php/Conexion.php';
// require_once '../vendor/PHPExcel/Classes/PHPExcel.php';

require '../vendor/autoload.php';

function stringFloat($num, $dec = 2)
{
    // return number_format(floatval(str_replace(',', '.', $num)), $dec, ',', '.');
    // return floatval(str_replace('.', ',', $num));
    // return $num;
    $numero_limpio = str_replace('.', '', $num);
    $numero_limpio = str_replace(',', '.', $numero_limpio);
    $numero = floatval($numero_limpio);
    return $numero;
}

if (isset($_POST["Guardar"])) {

    // Obtener los datos del formulario
    $nombre_embalse = $_POST["embalse_nombre"];
    $nombre_presa = $_POST["presa_nombre"];
    $estado = isset($_POST["estado"]) && !empty($_POST["estado"]) ? implode(",", $_POST["estado"]) : "";
    $municipio = isset($_POST["municipio"]) && !empty($_POST["municipio"]) ? implode(",", $_POST["municipio"]) : "";
    $parroquia = isset($_POST["parroquia"]) && !empty($_POST["parroquia"]) ? implode(",", $_POST["parroquia"]) : "";
    $norte = $_POST["norte"];
    $este = $_POST["este"];
    $huso = $_POST["huso"];
    $cuenca = $_POST["cuenca"];
    $afluentes = $_POST["afluentes"];
    $area_cuenca = $_POST["area"];
    $escurrimiento = $_POST["escurrimiento"];
    $ubicacion_embalse = $_POST["ubicacion_embalse"];
    $organo = $_POST["organo"];
    $personal = $_POST["personal"];
    $operador = $_POST["operador"];
    $region = $_POST["region"];
    $autoridad = $_POST["autoridad"];
    $proyectista = $_POST["proyectista"];
    $constructor = $_POST["constructor"];
    $inicio_construccion = $_POST["inicio_construccion"];
    $duracion_construccion = $_POST["duracion_construccion"];
    $inicio_operacion = $_POST["inicio_operacion"];
    $monitoreo = $_POST["monitoreo"];
    // $batimetria = $_POST["batimetria"];
    $vida_util = $_POST["vida_util"];
    $cota_min = stringFloat($_POST["cota_min"]);
    $cota_min_dis = stringFloat($_POST["cota_min_dis"]);
    $volumen_min = stringFloat($_POST["vol_min"]);
    $superficie_min = stringFloat($_POST["sup_min"]);
    $cota_nor = stringFloat($_POST["cota_nor"]);
    $volumen_nor = stringFloat($_POST["vol_nor"]);
    $superficie_nor = stringFloat($_POST["sup_nor"]);
    $cota_max = stringFloat($_POST["cota_max"]);
    $volumen_max = stringFloat($_POST["vol_max"]);
    $superficie_max = stringFloat($_POST["sup_max"]);
    $numero_presas = $_POST["numero_presas"];
    $tipo_presa = $_POST["tipo_presa"];
    $altura = $_POST["altura"];
    $talud_arriba = $_POST["talud_arriba"];
    $talud_abajo = $_POST["talud_abajo"];
    $longitud_cresta = $_POST["longitud_cresta"];
    $cota_cresta = $_POST["cota_cresta"];
    $ancho_cresta = $_POST["ancho_cresta"];
    $volumen_terraplen = $_POST["volumen_terraplen"];
    $ancho_base = $_POST["ancho_base"];
    $ubicacion_aliviadero = $_POST["ubicacion_aliviadero"];
    $tipo_aliviadero = $_POST["tipo_aliviadero"];
    $numero_compuertas_aliviadero = $_POST["numero_compuertas_aliviadero"];
    $carga_aliviadero = $_POST["carga_aliviadero"];
    $descarga_aliviadero = $_POST["descarga_aliviadero"];
    $longitud_aliviadero = $_POST["longitud_aliviadero"];
    $ubicacion_toma = $_POST["ubicacion_toma"];
    $tipo_toma = $_POST["tipo_toma"];
    $numero_compuertas_toma = $_POST["numero_compuertas_toma"];
    $emergencia_toma = $_POST["emergencia_toma"];
    $regulacion_toma = $_POST["regulacion_toma"];
    $gasto_toma = $_POST["gasto_toma"];
    $descarga_fondo = $_POST["descarga_fondo"];
    $obra_conduccion = $_POST["obra_conduccion"];
    $tipo_conduccion = $_POST["tipo_conduccion"];
    $accion_conduccion = $_POST["accion_conduccion"];
    $proposito = $_POST["proposito"];
    $uso = $_POST["uso"];
    // $sectores = $_POST["sectores"];
    $sectores_estado = isset($_POST["sectoresEstado"]) && !empty($_POST["sectoresEstado"]) ? implode(",", $_POST["sectoresEstado"]) : "";
    $sectores_municipio = isset($_POST["sectoresMunicipio"]) && !empty($_POST["sectoresMunicipio"]) ? implode(",", $_POST["sectoresMunicipio"]) : "";
    $sectores_parroquia = isset($_POST["sectoresParroquia"]) && !empty($_POST["sectoresParroquia"]) ? implode(",", $_POST["sectoresParroquia"]) : "";
    $poblacion = $_POST["poblacion"];
    $area_riego = $_POST["area_riego"];
    $area_protegida = $_POST["area_protegida"];
    $poblacion_protegida = $_POST["poblacion_prote"];
    $produccion_hidro = $_POST["produccion_hidro"];
    $f_cargo = $_POST["f_cargo"];
    $f_cedula = $_POST["f_cedula"];
    $f_nombres = $_POST["f_nombres"];
    $f_apellidos = $_POST["f_apellidos"];
    $f_telefono = $_POST["f_telefono"];
    $f_correo = $_POST["f_correo"];
    $responsable = $_POST["responsable"];
    //$imagen_uno = $_POST["imagen_uno"];
    //$imagen_dos = $_POST["imagen_dos"];
    $imagen_uno = $_FILES["imagen_uno"]['name'];
    $imagen_uno_tmp = $_FILES["imagen_uno"]['tmp_name'];
    $imagen_dos = $_FILES["imagen_dos"]['name'];
    $imagen_dos_tmp = $_FILES["imagen_dos"]['tmp_name'];
    $imagen_tres = $_FILES["imagen_tres"]['name'];
    $imagen_tres_tmp = $_FILES["imagen_tres"]['tmp_name'];

    $archivo_bat_name = $_FILES["batimetria"]['name'];
    $archivo_batimetria = $_FILES["batimetria"]['tmp_name']; //Batimetria en excel";

    //ARCHIVOS DE IMAGEN
    $aux_uno = $imagen_uno;
    $aux_dos = $imagen_dos;
    $aux_tres = $imagen_tres;

    if (!empty($imagen_uno) && count($_FILES["imagen_uno"]) > 0) {
        $i = 1;
        while (1) {
            if (file_exists('../pages/reports_images/' . $imagen_uno)) {
                $imagen_uno = $i . '-' . $aux_uno;
                $i++;
            } else {
                break;
            }
        }
    } else {
        $imagen_uno = "";
    }

    if (!empty($imagen_dos) && count($_FILES["imagen_dos"]) > 0) {
        $i = 1;
        while (1) {
            if (file_exists('../pages/reports_images/' . $imagen_dos)) {
                $imagen_dos = $i . '-' . $aux_dos;
                $i++;
            } else {
                break;
            }
        }
    } else {
        $imagen_dos = "";
    }

    if (!empty($imagen_tres) && count($_FILES["imagen_tres"]) > 0) {
        $i = 1;
        while (1) {
            if (file_exists('../pages/reports_images/' . $imagen_tres)) {
                $imagen_tres = $i . '-' . $aux_tres;
                $i++;
            } else {
                break;
            }
        }
    } else {
        $imagen_tres = "";
    }

    //ARCHIVOS DE EXCEL BATIMETRIA
    $batimetria = "";

    //NO BORRAR ESTA ES CON VERSION PHPEXCEL

    // if (!empty($archivo_bat_name) && count($_FILES["batimetria"]) > 0) {
    //     $inputFileType = PHPExcel_IOFactory::identify($archivo_batimetria);
    //     $objReader = PHPExcel_IOFactory::createReader($inputFileType);

    //     $objPHPExcel = $objReader->load($archivo_batimetria);
    //     $sheet = $objPHPExcel->getSheet(0);
    //     $highestRow = $sheet->getHighestRow();
    //     $highestColumn = $sheet->getHighestColumn();

    //     $cota_embalse = array();
    //     $cotas_embalse = array();
    //     $num = 0;
    //     for ($sht = 0; $sht < $objPHPExcel->getSheetCount(); $sht++) {
    //         $sheet = $objPHPExcel->getSheet($sht);
    //         $highestRow = $sheet->getHighestRow();
    //         for ($row = 2; $row <= $highestRow; $row++) {
    //             $num++;
    //             $cota = number_format($sheet->getCell("A" . $row)->getValue(), 3, '.', '');
    //             $area = $sheet->getCell("B" . $row)->getValue();
    //             $capacidad = $sheet->getCell("C" . $row)->getValue();
    //             $cota_embalse[$cota] = $area . "-" . $capacidad;
    //         }
    //         $cotas_embalse[$objPHPExcel->getSheetNames()[$sht]] = $cota_embalse;
    //         $cota_embalse = array();
    //     }
    //     $batimetria = json_encode($cotas_embalse);
    // }

    if (!empty($archivo_bat_name) && count($_FILES["batimetria"]) > 0) {

        if (true) {
            $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($archivo_batimetria);

            $cotas_embalse = array();
            $num = 0;

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $highestRow = $sheet->getHighestRow();

                $cota_embalse = array();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $num++;
                    // $cota = number_format($sheet->getCell('A' . $row)->getValue(), 3, '.', '');
                    // $area = $sheet->getCell('B' . $row)->getValue();
                    // $capacidad = $sheet->getCell('C' . $row)->getValue();
                    $cota = number_format($sheet->getCell('A' . $row)->getCalculatedValue(), 3, '.', '');
                    $area = $sheet->getCell('B' . $row)->getCalculatedValue();
                    $capacidad = $sheet->getCell('C' . $row)->getCalculatedValue();
                    $cota_embalse[$cota] = $area . "-" . $capacidad;
                }

                $cotas_embalse[$sheetName] = $cota_embalse;
            }
        }

        if (false) {
            $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($archivo_batimetria);

            $cotas_embalse = array();
            $num = 0;

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $highestRow = $sheet->getHighestRow();

                $cota_embalse = array();

                $row = 2; // Iniciamos en la fila 2

                while ($row <= $highestRow) {
                    for ($i = 0; $i < 50; $i++) {
                        if ($row > $highestRow) {
                            break; // Si superamos el máximo de filas, salimos del bucle
                        }
                        $num++;
                        $cota = number_format($sheet->getCell('B' . $row)->getValue(), 3, '.', '');
                        $area = $sheet->getCell('C' . $row)->getValue();
                        $capacidad = $sheet->getCell('D' . $row)->getValue();
                        $cota_embalse[$cota] = $area . "-" . $capacidad;

                        $row++;
                    }
                    $row = $row - 50;
                    for ($i = 0; $i < 50; $i++) {
                        if ($row > $highestRow) {
                            break; // Si superamos el máximo de filas, salimos del bucle
                        }
                        $num++;
                        $cota = number_format($sheet->getCell('F' . $row)->getValue(), 3, '.', '');
                        $area = $sheet->getCell('G' . $row)->getValue();
                        $capacidad = $sheet->getCell('H' . $row)->getValue();
                        $cota_embalse[$cota] = $area . "-" . $capacidad;

                        $row++;
                    }

                    // Saltamos una fila si no hemos llegado al final
                    if ($row <= $highestRow) {
                        $row++;
                    }
                }

                $cotas_embalse[$sheetName] = $cota_embalse;
            }
            $batimetria = json_encode($cotas_embalse);
            // var_dump($batimetria);
        }

        $batimetria = json_encode($cotas_embalse);
    }

    // ejecucion de la consulta
    $consulta = "INSERT INTO embalses (nombre_embalse, nombre_presa, id_estado, id_municipio, id_parroquia, este, norte, huso, cuenca_principal, afluentes_principales, area_cuenca, escurrimiento_medio, ubicacion_embalse, organo_rector, personal_encargado, operador, autoridad_responsable, proyectista, constructor, inicio_construccion, duracion_de_construccion, inicio_de_operacion, monitoreo_del_embalse, batimetria, vida_util, cota_min, cota_min_dis , cota_nor, cota_max, vol_min, vol_nor, vol_max, sup_min, sup_nor, sup_max, numero_de_presas, tipo_de_presa, altura, talud_aguas_arriba, talud_aguas_abajo, longitud_cresta, cota_cresta, ancho_cresta, volumen_terraplen, ancho_base, ubicacion_aliviadero, tipo_aliviadero, numero_compuertas_aliviadero, carga_vertedero, descarga_maxima, longitud_aliviadero, ubicacion_toma, tipo_toma, numero_compuertas_toma, mecanismos_de_emergencia, mecanismos_de_regulacion, gasto_maximo, descarga_de_fondo, posee_obra, tipo_de_obra, accion_requerida, proposito, uso_actual, sectores_estado, sectores_municipio, sectores_parroquia, poblacion_beneficiada, area_de_riego_beneficiada, area_protegida, poblacion_protegida, produccion_hidro, f_cargo, f_cedula, f_nombres, f_apellidos, f_telefono, f_correo, imagen_uno, imagen_dos, imagen_tres, region, id_encargado, estatus) 
            VALUES ('$nombre_embalse', '$nombre_presa' ,'$estado', '$municipio' ,'$parroquia' ,'$este' ,'$norte' ,'$huso' ,'$cuenca', '$afluentes', '$area_cuenca', '$escurrimiento', '$ubicacion_embalse', '$organo', '$personal', '$operador', '$autoridad', '$proyectista', '$constructor', '$inicio_construccion', '$duracion_construccion', '$inicio_operacion', '$monitoreo', '$batimetria', '$vida_util', '$cota_min', '$cota_min_dis', '$cota_nor', '$cota_max', '$volumen_min', '$volumen_nor', '$volumen_max', '$superficie_min', '$superficie_nor', '$superficie_max', '$numero_presas', '$tipo_presa', '$altura', '$talud_arriba', '$talud_abajo', '$longitud_cresta', '$cota_cresta', '$ancho_cresta', '$volumen_terraplen', '$ancho_base', '$ubicacion_aliviadero', '$tipo_aliviadero', '$numero_compuertas_aliviadero', '$carga_aliviadero', '$descarga_aliviadero', '$longitud_aliviadero', '$ubicacion_toma', '$tipo_toma', '$numero_compuertas_toma', '$emergencia_toma', '$regulacion_toma', '$gasto_toma', '$descarga_fondo', '$obra_conduccion', '$tipo_conduccion', '$accion_conduccion', '$proposito', '$uso', '$sectores_estado', '$sectores_municipio', '$sectores_parroquia', '$poblacion', '$area_riego', '$area_protegida', '$poblacion_protegida', '$produccion_hidro', '$f_cargo', '$f_cedula', '$f_nombres', '$f_apellidos', '$f_telefono', '$f_correo', '$imagen_uno', '$imagen_dos', '$imagen_tres', '$region', '$responsable', 'activo')";

    $resultado = mysqli_query($conn, $consulta);
    if ($resultado) {


        if (!empty($imagen_uno) && count($_FILES["imagen_uno"]) > 0) {

            move_uploaded_file($imagen_uno_tmp, '../pages/reports_images/' . $imagen_uno);
        }
        if (!empty($imagen_dos) && count($_FILES["imagen_dos"]) > 0) {

            move_uploaded_file($imagen_dos_tmp, '../pages/reports_images/' . $imagen_dos);
        }
        if (!empty($imagen_tres) && count($_FILES["imagen_tres"]) > 0) {

            move_uploaded_file($imagen_tres_tmp, '../pages/reports_images/' . $imagen_tres);
        }


        echo "Los datos se guardaron correctamente en la base de datos.";
        // header("Location: ../main.php?page=embalses");
        echo "<script>window.location='../main.php?page=embalses';</script>";
    } else {
        echo "Ocurrió un error al guardar los datos: " . mysqli_error($conn);
    }
    // Cerrar la conexión
}

if (isset($_POST["Update"])) {

    // Obtener los datos del formulario de edicion
    $nombre_embalse = $_POST["embalse_nombre"];
    $nombre_presa = $_POST["presa_nombre"];
    $estado = isset($_POST["estado"]) && !empty($_POST["estado"]) ? implode(",", $_POST["estado"]) : "";
    $municipio = isset($_POST["municipio"]) && !empty($_POST["municipio"]) ? implode(",", $_POST["municipio"]) : "";
    $parroquia = isset($_POST["parroquia"]) && !empty($_POST["parroquia"]) ? implode(",", $_POST["parroquia"]) : "";
    $norte = $_POST["norte"];
    $este = $_POST["este"];
    $huso = $_POST["huso"];
    $cuenca = $_POST["cuenca"];
    $afluentes = $_POST["afluentes"];
    $area_cuenca = $_POST["area"];
    $escurrimiento = $_POST["escurrimiento"];
    $ubicacion_embalse = $_POST["ubicacion_embalse"];
    $organo = $_POST["organo"];
    $personal = $_POST["personal"];
    $operador = $_POST["operador"];
    $region = $_POST["region"];
    $autoridad = $_POST["autoridad"];
    $proyectista = $_POST["proyectista"];
    $constructor = $_POST["constructor"];
    $inicio_construccion = $_POST["inicio_construccion"];
    $duracion_construccion = $_POST["duracion_construccion"];
    $inicio_operacion = $_POST["inicio_operacion"];
    $monitoreo = $_POST["monitoreo"];
    // $batimetria = $_POST["batimetria"];   // AUN NO SE COMO HACER ESTE!
    $vida_util = $_POST["vida_util"];
    $cota_min = stringFloat($_POST["cota_min"]);
    $cota_min_dis = stringFloat($_POST["cota_min_dis"]);
    $volumen_min = stringFloat($_POST["vol_min"]);
    $superficie_min = stringFloat($_POST["sup_min"]);
    $cota_nor = stringFloat($_POST["cota_nor"]);
    $volumen_nor = stringFloat($_POST["vol_nor"]);
    $superficie_nor = stringFloat($_POST["sup_nor"]);
    $cota_max = stringFloat($_POST["cota_max"]);
    $volumen_max = stringFloat($_POST["vol_max"]);
    $superficie_max = stringFloat($_POST["sup_max"]);
    $numero_presas = $_POST["numero_presas"];
    $tipo_presa = $_POST["tipo_presa"];
    $altura = $_POST["altura"];
    $talud_arriba = $_POST["talud_arriba"];
    $talud_abajo = $_POST["talud_abajo"];
    $longitud_cresta = $_POST["longitud_cresta"];
    $cota_cresta = $_POST["cota_cresta"];
    $ancho_cresta = $_POST["ancho_cresta"];
    $volumen_terraplen = $_POST["volumen_terraplen"];
    $ancho_base = $_POST["ancho_base"];
    $ubicacion_aliviadero = $_POST["ubicacion_aliviadero"];
    $tipo_aliviadero = $_POST["tipo_aliviadero"];
    $numero_compuertas_aliviadero = $_POST["numero_compuertas_aliviadero"];
    $carga_aliviadero = $_POST["carga_aliviadero"];
    $descarga_aliviadero = $_POST["descarga_aliviadero"];
    $longitud_aliviadero = $_POST["longitud_aliviadero"];
    $ubicacion_toma = $_POST["ubicacion_toma"];
    $tipo_toma = $_POST["tipo_toma"];
    $numero_compuertas_toma = $_POST["numero_compuertas_toma"];
    $emergencia_toma = $_POST["emergencia_toma"];
    $regulacion_toma = $_POST["regulacion_toma"];
    $gasto_toma = $_POST["gasto_toma"];
    $descarga_fondo = $_POST["descarga_fondo"];
    $obra_conduccion = $_POST["obra_conduccion"];
    $tipo_conduccion = $_POST["tipo_conduccion"];
    $accion_conduccion = $_POST["accion_conduccion"];
    $proposito = $_POST["proposito"];
    $uso = $_POST["uso"];
    // $sectores = $_POST["sectores"];
    $sectores_estado = isset($_POST["sectoresEstado"]) && !empty($_POST["sectoresEstado"]) ? implode(",", $_POST["sectoresEstado"]) : "";
    $sectores_municipio = isset($_POST["sectoresMunicipio"]) && !empty($_POST["sectoresMunicipio"]) ? implode(",", $_POST["sectoresMunicipio"]) : "";
    $sectores_parroquia = isset($_POST["sectoresParroquia"]) && !empty($_POST["sectoresParroquia"]) ? implode(",", $_POST["sectoresParroquia"]) : "";
    $poblacion = $_POST["poblacion"];
    $area_riego = $_POST["area_riego"];
    $area_protegida = $_POST["area_protegida"];
    $poblacion_protegida = $_POST["poblacion_prote"];
    $produccion_hidro = $_POST["produccion_hidro"];
    $f_cargo = $_POST["f_cargo"];
    $f_cedula = $_POST["f_cedula"];
    $f_nombres = $_POST["f_nombres"];
    $f_apellidos = $_POST["f_apellidos"];
    $f_telefono = $_POST["f_telefono"];
    $f_correo = $_POST["f_correo"];
    $responsable = $_POST["responsable"];
    $id_embalse = $_POST["id_embalse"];

    $imagen_uno = $_FILES["imagen_uno"]['name'];
    $imagen_uno_tmp = $_FILES["imagen_uno"]['tmp_name'];
    $imagen_dos = $_FILES["imagen_dos"]['name'];
    $imagen_dos_tmp = $_FILES["imagen_dos"]['tmp_name'];
    $imagen_tres = $_FILES["imagen_tres"]['name'];
    $imagen_tres_tmp = $_FILES["imagen_tres"]['tmp_name'];

    $pre_imagen_uno = $_POST["pre_imagen_uno"];
    $pre_imagen_dos = $_POST["pre_imagen_dos"];
    $pre_imagen_tres = $_POST["pre_imagen_tres"];

    $archivo_bat_name = $_FILES["batimetria"]['name'];
    $archivo_batimetria = $_FILES["batimetria"]['tmp_name']; //Batimetria en excel";

    $pre_batimeria = $_POST["pre_batimetria"];

    $aux_uno = $imagen_uno;
    $aux_dos = $imagen_dos;
    $aux_tres = $imagen_tres;

    $queryEmbalse = mysqli_query($conn, "SELECT * FROM embalses WHERE id_embalse = $id_embalse");
    $embalse = mysqli_fetch_assoc($queryEmbalse);

    if (!empty($imagen_uno) && count($_FILES["imagen_uno"]) > 0) {
        $i = 1;
        while (1) {
            if (file_exists('../pages/reports_images/' . $imagen_uno)) {
                $imagen_uno = $i . '-' . $aux_uno;
                $i++;
            } else {
                break;
            }
        }
    } else {
        if ($pre_imagen_uno == "") {
            $imagen_uno = "";
        } else {
            $imagen_uno = $embalse["imagen_uno"];
        }
    }

    if (!empty($imagen_dos) && count($_FILES["imagen_dos"]) > 0) {
        $i = 1;
        while (1) {
            if (file_exists('../pages/reports_images/' . $imagen_dos)) {
                $imagen_dos = $i . '-' . $aux_dos;
                $i++;
            } else {
                break;
            }
        }
    } else {
        if ($pre_imagen_dos == "") {
            $imagen_dos = "";
        } else {
            $imagen_dos = $embalse["imagen_dos"];
        }
    }

    if (!empty($imagen_tres) && count($_FILES["imagen_tres"]) > 0) {
        $i = 1;
        while (1) {
            if (file_exists('../pages/reports_images/' . $imagen_tres)) {
                $imagen_tres = $i . '-' . $aux_tres;
                $i++;
            } else {
                break;
            }
        }
    } else {
        if ($pre_imagen_tres == "") {
            $imagen_tres = "";
        } else {
            $imagen_tres = $embalse["imagen_tres"];
        }
    }

    $batimetria = "";

    if (!empty($archivo_bat_name) && count($_FILES["batimetria"]) > 0) {

        if (true) {
            $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($archivo_batimetria);

            $cotas_embalse = array();
            $num = 0;

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $highestRow = $sheet->getHighestRow();

                $cota_embalse = array();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $num++;
                    // $cota = number_format($sheet->getCell("A" . $row)->getValue(), 3, '.', '');
                    // $area = $sheet->getCell("B" . $row)->getValue();
                    // $capacidad = $sheet->getCell("C" . $row)->getValue();
                    $cota = number_format($sheet->getCell("A" . $row)->getCalculatedValue(), 3, '.', '');
                    $area = $sheet->getCell("B" . $row)->getCalculatedValue();
                    $capacidad = $sheet->getCell("C" . $row)->getCalculatedValue();
                    $cota_embalse[$cota] = $area . "-" . $capacidad;
                }

                $cotas_embalse[$sheetName] = $cota_embalse;
            }

            $batimetria = json_encode($cotas_embalse);
        }

        if (false) {
            $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($archivo_batimetria);

            $cotas_embalse = array();
            $num = 0;

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $highestRow = $sheet->getHighestRow();

                $cota_embalse = array();

                $row = 2; // Iniciamos en la fila 2

                // while ($row <= $highestRow) {
                //     for ($i = 0; $i < 50; $i++) {
                //         if ($row > $highestRow) {
                //             break; // Si superamos el máximo de filas, salimos del bucle
                //         }
                //         $num++;
                //         $cota = number_format($sheet->getCellByColumnAndRow(2, $row)->getValue(), 3, '.', '');
                //         $area = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                //         $capacidad = $sheet->getCellByColumnAndRow(4, $row)->getValue();
                //         $cota_embalse[$cota] = $area . "-" . $capacidad;

                //         $row++;
                //     }
                //     $row = $row - 50;
                //     for ($i = 0; $i < 50; $i++) {
                //         if ($row > $highestRow) {
                //             break; // Si superamos el máximo de filas, salimos del bucle
                //         }
                //         $num++;
                //         $cota = number_format($sheet->getCellByColumnAndRow(6, $row)->getValue(), 3, '.', '');
                //         $area = $sheet->getCellByColumnAndRow(7, $row)->getValue();
                //         $capacidad = $sheet->getCellByColumnAndRow(8, $row)->getValue();
                //         $cota_embalse[$cota] = $area . "-" . $capacidad;

                //         $row++;
                //     }

                //     // Saltamos una fila si no hemos llegado al final
                //     if ($row <= $highestRow) {
                //         $row++;
                //     }
                // }

                while ($row <= $highestRow) {
                    for ($i = 0; $i < 50; $i++) {
                        if ($row > $highestRow) {
                            break; // Si superamos el máximo de filas, salimos del bucle
                        }
                        $num++;
                        $cota = number_format($sheet->getCell('B' . $row)->getValue(), 3, '.', '');
                        $area = $sheet->getCell('C' . $row)->getValue();
                        $capacidad = $sheet->getCell('D' . $row)->getValue();
                        $cota_embalse[$cota] = $area . "-" . $capacidad;

                        $row++;
                    }
                    $row = $row - 50;
                    for ($i = 0; $i < 50; $i++) {
                        if ($row > $highestRow) {
                            break; // Si superamos el máximo de filas, salimos del bucle
                        }
                        $num++;
                        $cota = number_format($sheet->getCell('F' . $row)->getValue(), 3, '.', '');
                        $area = $sheet->getCell('G' . $row)->getValue();
                        $capacidad = $sheet->getCell('H' . $row)->getValue();
                        $cota_embalse[$cota] = $area . "-" . $capacidad;

                        $row++;
                    }

                    // Saltamos una fila si no hemos llegado al final
                    if ($row <= $highestRow) {
                        $row++;
                    }
                }

                $cotas_embalse[$sheetName] = $cota_embalse;
            }
            $batimetria = json_encode($cotas_embalse);
            // var_dump($batimetria);
        }
    } else {
        if ($pre_batimeria == "") {
            $batimetria = "";
        } else {
            $batimetria = $embalse["batimetria"];
        }
    }


    $consulta = "UPDATE embalses 
    SET nombre_embalse = '$nombre_embalse',
    nombre_presa = '$nombre_presa',
    id_estado = '$estado',
    id_municipio = '$municipio',
    id_parroquia = '$parroquia',
    este = '$este',
    norte = '$norte',
    huso = '$huso',
    cuenca_principal = '$cuenca',
    afluentes_principales = '$afluentes',
    area_cuenca = '$area_cuenca',
    escurrimiento_medio = '$escurrimiento',
    ubicacion_embalse = '$ubicacion_embalse',
    organo_rector = '$organo',
    personal_encargado = '$personal',
    operador = '$operador',
    autoridad_responsable = '$autoridad',
    proyectista = '$proyectista',
    constructor = '$constructor',
    inicio_construccion = '$inicio_construccion', 
    duracion_de_construccion = '$duracion_construccion',
    inicio_de_operacion = '$inicio_operacion',
    monitoreo_del_embalse = '$monitoreo',
    batimetria = '$batimetria',
    vida_util = '$vida_util',
    cota_min = '$cota_min',
    cota_min_dis = '$cota_min_dis',
    cota_nor = '$cota_nor',
    cota_max = '$cota_max',
    vol_min = '$volumen_min',
    vol_nor = '$volumen_nor',
    vol_max = '$volumen_max',
    sup_min = '$superficie_min',
    sup_nor = '$superficie_nor',
    sup_max = '$superficie_max',
    numero_de_presas = '$numero_presas',
    tipo_de_presa = '$tipo_presa',
    altura = '$altura',
    talud_aguas_arriba = '$talud_arriba',
    talud_aguas_abajo = '$talud_abajo',
    longitud_cresta = '$longitud_cresta',
    cota_cresta = '$cota_cresta',
    ancho_cresta = '$ancho_cresta',
    volumen_terraplen = '$volumen_terraplen',
    ancho_base = '$ancho_base',
    ubicacion_aliviadero = '$ubicacion_aliviadero',
    tipo_aliviadero = '$tipo_aliviadero',
    numero_compuertas_aliviadero = '$numero_compuertas_aliviadero',
    carga_vertedero = '$carga_aliviadero',
    descarga_maxima = '$descarga_aliviadero',
    longitud_aliviadero = '$longitud_aliviadero',
    ubicacion_toma = '$ubicacion_toma',
    tipo_toma = '$tipo_toma',
    numero_compuertas_toma = '$numero_compuertas_toma',
    mecanismos_de_emergencia = '$emergencia_toma',
    mecanismos_de_regulacion = '$regulacion_toma',
    gasto_maximo = '$gasto_toma',
    descarga_de_fondo = '$descarga_fondo',
    posee_obra = '$obra_conduccion',
    tipo_de_obra = '$tipo_conduccion',
    accion_requerida = '$accion_conduccion',
    proposito = '$proposito',
    uso_actual = '$uso',
    sectores_estado = '$sectores_estado',
    sectores_municipio = '$sectores_municipio',
    sectores_parroquia = '$sectores_parroquia',
    poblacion_beneficiada = '$poblacion',
    area_de_riego_beneficiada = '$area_riego',
    area_protegida = '$area_protegida',
    poblacion_protegida = '$poblacion_protegida',
    produccion_hidro = '$produccion_hidro',
    f_cargo = '$f_cargo',
    f_cedula = '$f_cedula',
    f_nombres = '$f_nombres',
    f_apellidos = '$f_apellidos',
    f_telefono = '$f_telefono',
    f_correo = '$f_correo',
    imagen_uno = '$imagen_uno',
    imagen_dos = '$imagen_dos',
    imagen_tres = '$imagen_tres',
    region = '$region',
    id_encargado = '$responsable'
    WHERE id_embalse = '$id_embalse'";

    $resultado = mysqli_query($conn, $consulta);
    if ($resultado) {

        if (!empty($imagen_uno) && count($_FILES["imagen_uno"]) > 0) {

            move_uploaded_file($imagen_uno_tmp, '../pages/reports_images/' . $imagen_uno);
        }
        if (!empty($imagen_dos) && count($_FILES["imagen_dos"]) > 0) {

            move_uploaded_file($imagen_dos_tmp, '../pages/reports_images/' . $imagen_dos);
        }
        if (!empty($imagen_tres) && count($_FILES["imagen_tres"]) > 0) {

            move_uploaded_file($imagen_tres_tmp, '../pages/reports_images/' . $imagen_tres);
        }

        echo "Los datos se actualizaron correctamente en la base de datos.";
        // header("Location: ../main.php?page=embalses");
        echo "<script>window.location='../main.php?page=embalses';</script>";
    } else {
        echo "Ocurrió un error al actualizar los datos: " . mysqli_error($conn);
    }
}

if (isset($_POST["eliminar"])) {
    $id_embalse = $_POST["id_embalse"];

    $consulta = "UPDATE embalses 
    SET estatus = 'inactivo'
    WHERE id_embalse = '$id_embalse'";

    $resultado = mysqli_query($conn, $consulta);
    if ($resultado) {

        // echo "Los datos se actualizaron correctamente en la base de datos.";
        // header("Location: ../main.php?page=embalses");
        echo "<script>window.location='../main.php?page=embalses';</script>";
    } else {
        echo "Ocurrió un error al actualizar los datos: " . mysqli_error($conn);
    }
}

if (isset($_POST["restaurar"])) {
    $id_embalse = $_POST["id_embalse"];

    $consulta = "UPDATE embalses 
    SET estatus = 'activo'
    WHERE id_embalse = '$id_embalse'";

    $resultado = mysqli_query($conn, $consulta);
    if ($resultado) {

        // echo "Los datos se actualizaron correctamente en la base de datos.";
        // header("Location: ../main.php?page=embalses");
        echo "<script>window.location='../main.php?page=embalses';</script>";
    } else {
        echo "Ocurrió un error al actualizar los datos: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
