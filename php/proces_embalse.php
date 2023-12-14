<?php
// Conectar a la base de datos minagua_db
// $host = "localhost";
// $username = "root";
// $password = "";
// $database = "minagua_db";

// $conexion = mysqli_connect($host, $username, $password, $database) or die(mysqli_error());

// require_once "../database/Conexion.php";
include '../php/Conexion.php';

if (isset($_POST["Guardar"])) {

    // Obtener los datos del formulario
    $nombre_embalse = $_POST["embalse_nombre"];
    $nombre_presa = $_POST["presa_nombre"];
    $estado = $_POST["estado"];
    $municipio = $_POST["municipio"];
    $parroquia = $_POST["parroquia"];
    $norte = $_POST["norte"];
    $este = $_POST["este"];
    $huso = $_POST["huso"];
    $cuenca = $_POST["cuenca"];
    $afluentes = $_POST["afluentes"];
    $area = $_POST["area"];
    $escurrimiento = $_POST["escurrimiento"];
    $ubicacion_embalse = $_POST["ubicacion_embalse"];
    $organo = $_POST["organo"];
    $personal = $_POST["personal"];
    $operador = $_POST["operador"];
    $autoridad = $_POST["autoridad"];
    $proyectista = $_POST["proyectista"];
    $constructor = $_POST["constructor"];
    $inicio_construccion = $_POST["inicio_construccion"];
    $duracion_construccion = $_POST["duracion_construccion"];
    $inicio_operacion = $_POST["inicio_operacion"];
    $monitoreo = $_POST["monitoreo"];
    $batimetria = $_POST["batimetria"];
    $vida_util = $_POST["vida_util"];
    $cota_min = ["cota_min"];
    $volumen_min = ["vol_min"];
    $superficie_min = ["sup_min"];
    $cota_nor = ["cota_nor"];
    $volumen_nor = ["vol_nor"];
    $superficie_nor = ["sup_nor"];
    $cota_max = ["cota_max"];
    $volumen_max = ["vol_max"];
    $superficie_max = ["sup_max"];
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
    $sectores = $_POST["sectores"];
    $poblacion = $_POST["poblacion"];
    $area_riego = $_POST["area_riego"];
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

    $aux_uno = $imagen_uno;
    $aux_dos = $imagen_dos;

    $i = 1;
    while (1) {
        if (file_exists('../pages/reports_images/' . $imagen_uno)) {
            $imagen_uno = $i . '-' . $aux_uno;
            $i++;
        } else {
            break;
        }
    }

    $i = 1;
    while (1) {
        if (file_exists('../pages/reports_images/' . $imagen_dos)) {
            $imagen_dos = $i . '-' . $aux_dos;
            $i++;
        } else {
            break;
        }
    }
    // Insertar los datos en la tabla embalse
    $consulta = "INSERT INTO embalses (Nombre_embalse, Nombre_presa, Id_estado, Id_municipio, Id_parroquia, este, norte, huso, cuenca_principal, afluentes_principales, area_cuenca, escurrimiento_medio, ubicacion_embalse, organo_rector, personal_encargado, operador, autoridad_responsable, proyectista, constructor, inicio_construccion, duracion_de_construccion, inicio_de_operacion, monitoreo_del_embalse, batimetria, vida_util, cota_min, cota_nor, cota_max, vol_min, vol_nor, vol_max, sup_min, sup_nor, sup_max, numero_de_presas, tipo_de_presa, altura, talud_aguas_arriba, talud_aguas_abajo, longitud_cresta, cota_cresta, ancho_cresta, volumen_terraplen, ancho_base, ubicacion_aliviadero, tipo_aliviadero, numero_compuertas_aliviadero, carga_vertedero, descarga_maxima, longitud_aliviadero, ubicacion_toma, tipo_toma, numero_compuertas_toma, mecanismos_de_emergencia, mecanismos_de_regulacion, gasto_maximo, descarga_de_fondo, posee_obra, tipo_de_obra, accion_requerida, proposito, uso_actual, sectores_beneficiados, poblacion_beneficiada, area_de_riego_beneficiada, f_cargo, f_cedula, f_nombres, f_apellidos, f_telefono, f_correo, imagen_uno, imagen_dos, id_encargado) 
            VALUES ('$nombre_embalse', '$nombre_presa' ,'$estado', '$municipio' ,'$parroquia' ,'$este' ,'$norte' ,'$huso' ,'$cuenca', '$afluentes', '$area', '$escurrimiento', '$ubicacion_embalse', '$organo', '$personal', '$operador', '$autoridad', '$proyectista', '$constructor', '$inicio_construccion', '$duracion_construccion', '$inicio_operacion', '$monitoreo', '$batimetria', '$vida_util', '$cota_min', '$cota_nor', '$cota_max', '$volumen_min', '$volumen_nor', '$volumen_max', '$superficie_min', '$superficie_nor', '$superficie_max', '$numero_presas', '$tipo_presa', '$altura', '$talud_arriba', '$talud_abajo', '$longitud_cresta', '$cota_cresta', '$ancho_cresta', '$volumen_terraplen', '$ancho_base', '$ubicacion_aliviadero', '$tipo_aliviadero', '$numero_compuertas_aliviadero', '$carga_aliviadero', '$descarga_aliviadero', '$longitud_aliviadero', '$ubicacion_toma', '$tipo_toma', '$numero_compuertas_toma', '$emergencia_toma', '$regulacion_toma', '$gasto_toma', '$descarga_fondo', '$obra_conduccion', '$tipo_conduccion', '$accion_conduccion', '$proposito', '$uso', '$sectores', '$poblacion', '$area_riego', '$f_cargo', '$f_cedula', '$f_nombres', '$f_apellidos', '$f_telefono', '$f_correo', '$imagen_uno', '$imagen_dos', '$responsable')";

    // Ejecutar la consulta y verificar si se realizó correctamente
    $resultado = mysqli_query($conn, $consulta);
    if ($resultado) {


        move_uploaded_file($imagen_uno_tmp, '../pages/reports_images/' . $imagen_uno);
        move_uploaded_file($imagen_dos_tmp, '../pages/reports_images/' . $imagen_dos);


        echo "Los datos se guardaron correctamente en la base de datos.";
    } else {
        echo "Ocurrió un error al guardar los datos: " . mysqli_error($conn);
    }
    // Cerrar la conexión
}
mysqli_close($conn);
