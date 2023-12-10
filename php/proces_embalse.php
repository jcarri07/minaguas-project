<?php
// Conectar a la base de datos minagua_db
// $host = "localhost";
// $username = "root";
// $password = "";
// $database = "minagua_db";

// $conexion = mysqli_connect($host, $username, $password, $database) or die(mysqli_error());

require_once "../database/conexion.php";

// Obtener los datos del formulario
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
$funcionario = $_POST["funcionario"];
$imagenes = $_POST["imagenes"];

// Insertar los datos en la tabla embalse
$consulta = "INSERT INTO embalse (cuenca, afluentes, area, escurrimiento, ubicacion_embalse, organo, personal, operador, autoridad, proyectista, constructor, inicio_construccion, duracion_construccion, inicio_operacion, monitoreo, batimetria, vida_util, numero_presas, tipo_presa, altura, talud_arriba, talud_abajo, longitud_cresta, cota_cresta, ancho_cresta, volumen_terraplen, ancho_base, ubicacion_aliviadero, tipo_aliviadero, numero_compuertas_aliviadero, carga_aliviadero, descarga_aliviadero, longitud_aliviadero, ubicacion_toma, tipo_toma, numero_compuertas_toma, emergencia_toma, regulacion_toma, gasto_toma, descarga_fondo, obra_conduccion, tipo_conduccion, accion_conduccion, proposito, uso, sectores, poblacion, area_riego, funcionario, imagenes) 
            VALUES ('$cuenca', '$afluentes', '$area', '$escurrimiento', '$ubicacion_embalse', '$organo', '$personal', '$operador', '$autoridad', '$proyectista', '$constructor', '$inicio_construccion', '$duracion_construccion', '$inicio_operacion', '$monitoreo', '$batimetria', '$vida_util', '$numero_presas', '$tipo_presa', '$altura', '$talud_arriba', '$talud_abajo', '$longitud_cresta', '$cota_cresta', '$ancho_cresta', '$volumen_terraplen', '$ancho_base', '$ubicacion_aliviadero', '$tipo_aliviadero', '$numero_compuertas_aliviadero', '$carga_aliviadero', '$descarga_aliviadero', '$longitud_aliviadero', '$ubicacion_toma', '$tipo_toma', '$numero_compuertas_toma', '$emergencia_toma', '$regulacion_toma', '$gasto_toma', '$descarga_fondo', '$obra_conduccion', '$tipo_conduccion', '$accion_conduccion', '$proposito', '$uso', '$sectores', '$poblacion', '$area_riego', '$funcionario', '$imagenes')";

// Ejecutar la consulta y verificar si se realizó correctamente
$resultado = mysqli_query($conn, $consulta);
if ($resultado) {
    echo "Los datos se guardaron correctamente en la base de datos.";
} else {
    echo "Ocurrió un error al guardar los datos: " . mysqli_error($conn);
}

// Cerrar la conexión
mysqli_close($conn);
