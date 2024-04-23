<?php
require_once '../../Conexion.php';
date_default_timezone_set("America/Caracas");
session_start();

$opc = $_POST['opc'];


if ($opc == "delete_data_excel") {
    $id_embalse = $_POST['id_registro'];
    $archivo_excel = $_POST['archivo_excel'];
    $fecha_excel = $_POST['fecha_excel'];

    $sql = "UPDATE datos_embalse SET estatus = 'inactivo' WHERE id_embalse = '$id_embalse' AND archivo_importacion = '$archivo_excel' AND fecha_importacion = '$fecha_excel';";
    $res = mysqli_query($conn, $sql);

    if ($res == 1)
        echo 'si';
}

closeConection($conn);
