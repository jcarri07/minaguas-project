<?php
require_once '../../Conexion.php';
date_default_timezone_set("America/Caracas");
session_start();

$opc = $_POST['opc'];

if ($opc == "add") {
    $cota = $_POST['cota'];
    $id_encargado = $_SESSION["Id_usuario"];
    $id_embalse = $_POST["id_embalse"];

    $tipo_extraccion = json_decode($_POST['tipo_extraccion']);
    $valor_extraccion = json_decode($_POST['valor_extraccion']);

    if ($_SESSION["Tipo"] == "Admin") {
        $fecha = $_POST["fecha"];
        $hora = $_POST["hora"];
    } else {
        $fecha = date("Y-m-d");
        $hora = date("H:i") . ":00";
    }

    $res = mysqli_query($conn, "INSERT INTO datos_embalse (id_embalse, fecha, hora, cota_actual, id_encargado, archivo_importacion, fecha_importacion, estatus) VALUES ('$id_embalse', '$fecha', '$hora', '$cota', '$id_encargado', '', NULL, 'activo');");
    //sleep(0.3);
    usleep(300000); // 0.3 segundos = 300000 microsegundos

    if ($res == 1) {
        $sql = "SELECT id_registro FROM datos_embalse WHERE id_embalse = '$id_embalse' AND id_encargado = '$id_encargado' ORDER BY id_registro DESC LIMIT 1;";
        $query = mysqli_query($conn, $sql);
        $id_registro = mysqli_fetch_array($query)['id_registro'];

        for ($i = 0; $i < count($tipo_extraccion); $i++) {
            $tipo_extraccion_aux = $tipo_extraccion[$i];
            $valor_extraccion_aux = $valor_extraccion[$i];

            $sql = "INSERT INTO detalles_extraccion (id_codigo_extraccion, extraccion, id_registro, estatus) VALUES ('$tipo_extraccion_aux', '$valor_extraccion_aux', '$id_registro', 'activo');";

            mysqli_query($conn, $sql);
        }

        echo 'si';
    }
}
if ($opc == "delete") {
    $id_registro = $_POST['id_registro'];

    $sql = "UPDATE datos_embalse SET estatus = 'inactivo' WHERE id_registro = '$id_registro';";
    $res = mysqli_query($conn, $sql);

    if ($res == 1)
        echo 'si';
}

closeConection($conn);
