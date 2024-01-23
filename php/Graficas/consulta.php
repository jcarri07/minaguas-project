<?php

include "../Conexion.php";
$fecha_actual = date("Y");
$f = $fecha_actual-1;
$res = mysqli_query($conn, "SELECT * FROM datos_embalse WHERE YEAR(fecha) = '$fecha_actual' OR YEAR(fecha) = '$f' GROUP BY fecha DESC;");
$count = mysqli_num_rows($res);
if ($count >= 1) {

    $re = mysqli_query($conn, "SELECT * FROM embalses ;");
    $count = mysqli_num_rows($re);
    if ($count >= 1) {

       $embalses = mysqli_fetch_all($re,MYSQLI_ASSOC);
       $datos_embalses = mysqli_fetch_all($res,MYSQLI_ASSOC);

    }

}
