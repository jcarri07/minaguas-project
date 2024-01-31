<?php

include "../Conexion.php";
date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");
$fecha_actual = date("Y");
$f = $fecha_actual - 1;



$re = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");
$count = mysqli_num_rows($re);
if ($count >= 1) {

    $res = mysqli_query($conn, "SELECT * FROM datos_embalse WHERE estatus = 'activo' AND (YEAR(fecha) = '$fecha_actual' OR YEAR(fecha) = '$f') GROUP BY fecha DESC;");
    $count = mysqli_num_rows($res);
    if ($count >= 1) {

        $embalses = mysqli_fetch_all($re, MYSQLI_ASSOC);
        $datos_embalses = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
}



function get_days_of_week($month_num, $week_num)
{
    $year = date("Y");
    $first_day_of_month = date('N', strtotime("$year-$month_num-01"));
    $days_in_month = date('t', strtotime("$year-$month_num-01"));
    $days = array();
    for ($day = 1; $day <= $days_in_month; $day++) {
        $week_of_month = ceil(($day + $first_day_of_month - 1) / 7);
        if ($week_of_month == $week_num) {
            $days[] = $day;
        }
    }
    return $days;
};

function obtenerFechasSemana($fecha)
{
    // Crear un objeto DateTime a partir de la fecha proporcionada
    $fechaObj = new DateTime($fecha);

    // Obtener el número del día de la semana (1 = lunes, 7 = domingo)
    $diaSemana = $fechaObj->format('N');

    // Calcular la diferencia entre el día actual y el lunes de la misma semana
    $diferencia = $diaSemana - 1;

    // Restar la diferencia para obtener la fecha del lunes
    $lunes = $fechaObj->modify("-$diferencia days");

    // Inicializar un array para almacenar las fechas de la semana
    $fechasSemana = array();

    // Agregar las fechas de la semana al array
    for ($i = 0; $i < 7; $i++) {
        $fechasSemana[] = $lunes->format('Y-m-d');
        $lunes->modify('+1 day');
    }

    return $fechasSemana;
}

// Ejemplo de uso
$fechasSemana = obtenerFechasSemana(date('Y-m-d'));
closeConection($conn);
// Imprimir las fechas de la semana
