<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");

$fecha1 = date('Y-m-d', strtotime('-1 months', strtotime(date('Y-m-d'))));
$fecha2 = date('Y');
$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];
$id = $_GET['id'];
$name = $_GET['name'];


$numeroSemana = strftime('%W', strtotime($fecha1));
$ex = strftime('%W', strtotime($fecha1));
function calcularSemanas($fecha, $fecha2)
{
    // Obtener la fecha actual y la fecha proporcionada
    $fechaActual = new DateTime($fecha2);
    $fechaProporcionada = new DateTime($fecha);

    // Verificar que la fecha proporcionada sea anterior a la fecha actual
    if ($fechaProporcionada >= $fechaActual) {
        return 0; // La fecha proporcionada es igual o posterior a la fecha actual
    }

    // Ajustar la fecha actual para que sea el primer día de la semana
    $fechaActual->modify('last monday');

    // Calcular la diferencia en semanas
    $diferencia = $fechaActual->diff($fechaProporcionada);
    $semanas = floor($diferencia->days / 7);

    return $semanas;
}
$semanas = calcularSemanas($fecha1, $fecha2) + $numeroSemana;
$j = 0;
$i = 0;
$l = 1;
$t = mysqli_query($conn, "SET time_zone = '-4:30'");
$r = mysqli_query($conn, "SELECT fecha,DAYOFWEEK(fecha) AS dia,WEEK(fecha,3) semana , MAX(CONCAT(fecha, ' ', hora)) AS fecha_hora, d.id_embalse
FROM  datos_embalse d
RIGHT JOIN embalses e ON e.id_embalse = d.id_embalse AND e.id_embalse = '$id'
WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND d.estatus = 'activo' AND (DAYOFWEEK(fecha) = 2 OR ( fecha = '$fecha1' AND DAYOFWEEK('$fecha1') != 2))
GROUP BY fecha ORDER BY fecha;");

$res = mysqli_query($conn, "SELECT fecha,DAYOFWEEK(fecha) AS dia,WEEK(fecha,3) semana , MAX(CONCAT(fecha, ' ', hora)) AS fecha_hora, d.id_embalse
FROM  datos_embalse d
RIGHT JOIN embalses e ON e.id_embalse = d.id_embalse AND e.id_embalse = '$id'
WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND d.estatus = 'activo' AND (DAYOFWEEK(fecha) = 1 OR ( fecha = '$fecha2' AND DAYOFWEEK('$fecha2') != 1))
GROUP BY fecha ORDER BY fecha;");

$datos1 = mysqli_fetch_all($r, MYSQLI_ASSOC);
$datos2 = mysqli_fetch_all($res, MYSQLI_ASSOC);
$datos_json1 = json_encode($datos1);
$datos_json2 = json_encode($datos2);
?>
<script>
    console.log('<?php echo $datos_json1; ?>');
    console.log('<?php echo $datos_json2; ?>');
    console.log('<?php echo $numeroSemana; ?>');
</script>
<?php
echo "id embalse:".$id."<br><br>";
while ($numeroSemana <= $semanas) {
    if($numeroSemana == $ex){echo "<br>grafica numero:".$l;$l++;}
    if($numeroSemana % 9 == 0){echo "<br>grafica numero:".$l;$l++;}
    echo '<br>Semana '.$numeroSemana.':<br>';

    if (isset($datos1[$i]['semana'])) {
        if ($numeroSemana == ($datos1[$i]['semana'])) {
            echo 'lunes:' . $datos1[$i]['fecha'] . '<br>';
            $i++;
        } else {
            echo 'lunes:vacio<br>';
        }
    } else {
        echo 'lunes:vacio<br>';
    }
    if (isset($datos2[$j]['semana'])) {
        if ($numeroSemana == $datos2[$j]['semana']) {
            echo 'domingo:' . $datos2[$j]['fecha'] . '<br>';
            $j++;
        } else {
            echo 'domingo:vacio<br>';
        }
    } else {
        echo 'domingo:vacio<br>';
    }
    $numeroSemana++;



};
?>