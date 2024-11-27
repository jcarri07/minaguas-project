<?php
require_once '../../php/Conexion.php';
// $fullPath = getcwd();
// $parts = explode(DIRECTORY_SEPARATOR, $fullPath);
date_default_timezone_set('America/Caracas');
require_once '../../php/batimetria.php';

//Consultar los embalses.
//por cada embalse se llama a NewBatimetria en cada objeto (array de objetos)
//por cada objeto se consulta su ultima medicion, y se llama al metodo getByCota con el año y la cota de esa medicion.
//A su vez necesitas la cota minima de ese objeto
//Eso te va a dar volumen actual [1] y el volumen  minimo [1]
//Luego se restan y se obtiene el didponible

$valores = json_decode($_GET["valores"]);

//cantidad de embalses con los porcentajes
$datos_codificados = $_GET['lista'];

// Decodificar los datos codificados en base64
$datos_decodificados = base64_decode($datos_codificados);

// Decodificar el JSON para obtener el array original
$lista = json_decode($datos_decodificados, true);

$datos_codificados = $_GET['volumenes'];

// Decodificar los datos codificados en base64
$datos_decodificados = base64_decode($datos_codificados);

// Decodificar el JSON para obtener el array original
$volumenes = json_decode($datos_decodificados, true);

$queryInameh = mysqli_query($conn, "SELECT nombre_config, configuracion FROM configuraciones WHERE nombre_config = 'fecha_sequia' OR nombre_config = 'fecha_lluvia' ORDER BY id_config ASC;");
$fechas = mysqli_fetch_all($queryInameh, MYSQLI_ASSOC);
$fecha1 = $fechas[0]['configuracion'];
$fecha2 = $fechas[1]['configuracion'];
$anio = date('Y', strtotime($fecha1));

$fecha_actual = date("Y-m-d");
$fecha3 = date("Y-m-d", strtotime("-7 days", strtotime($fecha_actual)));

$almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse,operador,region,nombre_embalse,MAX(d.fecha) AS fech,               (
SELECT SUM(extraccion)
        FROM detalles_extraccion dex, codigo_extraccion ce
        WHERE ce.id = dex.id_codigo_extraccion AND dex.id_registro = (SELECT id_registro
           FROM datos_embalse h 
           WHERE h.id_embalse = d.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND estatus = 'activo' AND id_embalse = d.id_embalse) AND cota_actual <> 0  LIMIT 1) AND (ce.id_tipo_codigo_extraccion = '1' OR ce.id_tipo_codigo_extraccion = '2' OR ce.id_tipo_codigo_extraccion = '3' OR ce.id_tipo_codigo_extraccion = '4')
      ) AS 'extraccion',
      e.nombre_embalse, (SELECT cota_actual 
           FROM datos_embalse h 
           WHERE h.id_embalse = d.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND estatus = 'activo' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual
      FROM embalses e
LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
WHERE e.estatus = 'activo'
GROUP BY id_embalse 
ORDER BY id_embalse ASC;");

$condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse,operador,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha1' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
FROM datos_embalse h 
WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 AND da.fecha <= '$fecha1') AND cota_actual <> 0 ORDER BY h.hora DESC LIMIT 1) AS cota_actual 
FROM embalses e
LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha1'
WHERE e.estatus = 'activo' 
GROUP BY id_embalse;");

$condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse,operador,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha2' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
FROM datos_embalse h 
WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 AND da.fecha <= '$fecha2') AND cota_actual <> 0 ORDER BY h.hora DESC LIMIT 1) AS cota_actual 
FROM embalses e
LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha2'
WHERE e.estatus = 'activo'
GROUP BY id_embalse;");

$condiciones_actuales3 = mysqli_query($conn, "SELECT e.id_embalse,operador,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha3' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
FROM datos_embalse h 
WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 AND da.fecha <= '$fecha3') AND cota_actual <> 0 ORDER BY h.hora DESC LIMIT 1) AS cota_actual 
FROM embalses e
LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha3'
WHERE e.estatus = 'activo'
GROUP BY id_embalse;");

$hidro = mysqli_query($conn, "SELECT COUNT(e.id_embalse),e.operador
                FROM embalses e
                WHERE e.estatus = 'activo'
                GROUP BY e.operador
                ORDER BY id_embalse ASC;");

$queryOp =  mysqli_query($conn, "SELECT * FROM operadores WHERE estatus = 'activo'");
$totalop = [];

while ($op = mysqli_fetch_array($queryOp)) {
  $totalop[$op["id_operador"]] = $op["operador"];
}

$queryReg =  mysqli_query($conn, "SELECT * FROM regiones WHERE estatus = 'activo'");
$totalreg = [];

while ($reg = mysqli_fetch_array($queryReg)) {
  $totalreg[$reg["id_region"]] = $reg["region"];
}

$datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);
$volumen_primer_periodo = mysqli_fetch_all($condiciones_actuales1, MYSQLI_ASSOC);
$volumen_segundo_periodo = mysqli_fetch_all($condiciones_actuales2, MYSQLI_ASSOC);
$volumen_tercer_periodo = mysqli_fetch_all($condiciones_actuales3, MYSQLI_ASSOC);

$embalses_variacion = [];
$operadores = [];
$countOp = [];
$variacion_total_op = [];

$num = 0;
while ($num < count($volumen_primer_periodo)) {
  $row = $volumen_primer_periodo[$num];
  $row2 = $volumen_segundo_periodo[$num];
  $row3 = $volumen_tercer_periodo[$num];
  $bat = new Batimetria($row["id_embalse"], $conn);
  // $fecha = date($row['fecha']);
  $anio = date("Y", strtotime($row['fecha']));
  $final = $bat->volumenActualDisponible();
  $inicial = $bat->volumenDisponibleByCota($anio, $row["cota_actual"]);
  $variacion = $final - $inicial;
  $porcentaje = $inicial != 0 ? (100 * (($final - $inicial) / ($inicial))) : 0;

  $anio2 = date("Y", strtotime($row2['fecha']));
  $final2 = $bat->volumenActualDisponible();
  $inicial2 = $bat->volumenDisponibleByCota($anio2, $row2["cota_actual"]);
  $variacion2 = $final2 - $inicial2;
  $porcentaje2 = $inicial2 != 0 ? (100 * (($final2 - $inicial2) / ($inicial2))) : 0;

  $anio3 = date("Y", strtotime($row3['fecha']));
  $final3 = $bat->volumenActualDisponible();
  $inicial3 = $bat->volumenDisponibleByCota($anio3, $row3["cota_actual"]);
  $variacion3 = $final3 - $inicial3;
  $porcentaje3 = $inicial3 != 0 ? (100 * (($final3 - $inicial3) / ($inicial3))) : 0;


  if (!in_array($totalop[$row["operador"]], $operadores)) {
    array_push($operadores, $totalop[$row["operador"]]);
    $countOp[$totalop[$row["operador"]]] = 1;
    $variacion_total_op[$totalop[$row["operador"]]] = [$inicial, $inicial2, $variacion, $variacion2, $porcentaje, $porcentaje2, $variacion3, $porcentaje3];
  } else {
    $countOp[$totalop[$row["operador"]]] += 1;
    $variacion_total_op[$totalop[$row["operador"]]][0] += $inicial;
    $variacion_total_op[$totalop[$row["operador"]]][1] += $inicial2;
    $variacion_total_op[$totalop[$row["operador"]]][2] += $variacion;
    $variacion_total_op[$totalop[$row["operador"]]][3] += $variacion2;
    $variacion_total_op[$totalop[$row["operador"]]][4] += $porcentaje;
    $variacion_total_op[$totalop[$row["operador"]]][5] += $porcentaje2;
    $variacion_total_op[$totalop[$row["operador"]]][6] += $variacion3;
    $variacion_total_op[$totalop[$row["operador"]]][7] += $porcentaje3;
  }

  $array = [$totalop[$row["operador"]], $row["nombre_embalse"], $variacion, $porcentaje, $variacion2, $porcentaje2, $variacion3, $porcentaje3];
  array_push($embalses_variacion, $array);
  $num++;
}


$meses = array(
  1 => 'Enero',
  2 => 'Febrero',
  3 => 'Marzo',
  4 => 'Abril',
  5 => 'Mayo',
  6 => 'Junio',
  7 => 'Julio',
  8 => 'Agosto',
  9 => 'Septiembre',
  10 => 'Octubre',
  11 => 'Noviembre',
  12 => 'Diciembre'
);

// var_dump($embalses_variacion);


$condiciones = [];
$CT = [0, 0, 0, 0, 0, 0];

$queryEmbalses = mysqli_query($conn, "SELECT id_embalse, nombre_embalse, norte, este, huso, operador FROM embalses WHERE estatus = 'activo';");

while ($row = mysqli_fetch_array($queryEmbalses)) {

  $bat = new Batimetria($row["id_embalse"], $conn);
  $porcentaje = $bat->volumenDisponible() != 0 ? (($bat->volumenActualDisponible() * 100) / $bat->volumenDisponible()) : 0;

  if ($porcentaje < 30) {
    agregarACondiciones($row["operador"], $condiciones, 1, $totalop);
    $CT[0] += 1;
    $CT[1] += 1;
  } else if ($porcentaje >= 30 && $porcentaje < 60) {
    agregarACondiciones($row["operador"], $condiciones, 2, $totalop);
    $CT[0] += 1;
    $CT[2] += 1;
  } else if ($porcentaje >= 60 && $porcentaje < 90) {
    agregarACondiciones($row["operador"], $condiciones, 3, $totalop);
    $CT[0] += 1;
    $CT[3] += 1;
  } else if ($porcentaje >= 90 && $porcentaje <= 100) {
    agregarACondiciones($row["operador"], $condiciones, 4, $totalop);
    $CT[0] += 1;
    $CT[4] += 1;
  } else {
    agregarACondiciones($row["operador"], $condiciones, 5, $totalop);
    $CT[0] += 1;
    $CT[5] += 1;
  }
}

$embalse_abast = [];
$operador_abast = [];
$t_op_a = [0, 0, 0, 0, 0];
$regiones = [];
$countReg = [];
$embalses_condiciones = [[], [], [], [], []];

$row = 0;

while ($row < count($datos_embalses)) {
  $emb = new Batimetria($datos_embalses[$row]["id_embalse"], $conn);

  // CALCULO DE ABASTECIMIENTO!!

  $abastecimiento = 0;
  if ($datos_embalses[$row]["extraccion"] > 0) {
    $abastecimiento = round((($emb->volumenActualDisponible() * 1000) / $datos_embalses[$row]["extraccion"]) / 30);
  }
  if ($datos_embalses[$row]["extraccion"] == NULL) {
    $abastecimiento = 0;
  }

  if (!in_array($totalreg[$datos_embalses[$row]["region"]], $regiones)) {
    array_push($regiones, $totalreg[$datos_embalses[$row]["region"]]);
    $countReg[$totalreg[$datos_embalses[$row]["region"]]] = 1;
  } else {
    $countReg[$totalreg[$datos_embalses[$row]["region"]]] += 1;
  }

  if (array_key_exists($totalop[$datos_embalses[$row]["operador"]], $operador_abast)) {
    if (($abastecimiento) <= 4) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][0] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[0] += 1;
      $t_op_a[4] += 1;
    }
    if (($abastecimiento) > 4 && ($abastecimiento) <= 8) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][1] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[1] += 1;
      $t_op_a[4] += 1;
    }
    if (($abastecimiento) > 8 && ($abastecimiento) <= 12) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][2] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[2] += 1;
      $t_op_a[4] += 1;
    }
    if (($abastecimiento) > 12) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][3] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[3] += 1;
      $t_op_a[4] += 1;
    }
  } else {
    $operador_abast[$totalop[$datos_embalses[$row]["operador"]]] = [0, 0, 0, 0, 0];

    if (($abastecimiento) <= 4) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][0] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[0] += 1;
      $t_op_a[4] += 1;
    }
    if (($abastecimiento) > 4 && ($abastecimiento) <= 8) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][1] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[1] += 1;
      $t_op_a[4] += 1;
    }
    if (($abastecimiento) > 8 && ($abastecimiento) <= 12) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][2] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[2] += 1;
      $t_op_a[4] += 1;
    }
    if (($abastecimiento) > 12) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][3] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[3] += 1;
      $t_op_a[4] += 1;
    }
  }

  $array = [$totalreg[$datos_embalses[$row]["region"]], $totalop[$datos_embalses[$row]["operador"]], $datos_embalses[$row]["nombre_embalse"], $abastecimiento];
  array_push($embalse_abast, $array);

  // CALCULO DE CONDICION ACTUAL!!
  if ($datos_embalses[$row]["cota_actual"] != NULL) {
    $x = $emb->getByCota($anio, $datos_embalses[$row]["cota_actual"])[1];

    $min = $emb->volumenMinimo();
    $max = $emb->volumenMaximo();
    $nor = $emb->volumenNormal();

    if (($x - $min) <= 0) {
      $sum = 0;
    } else {
      $sum = $x - $min;
    }
    $div = ($nor - $min) != 0 ? ($nor - $min) : 1;
    if (((abs(($sum)) * (100 / $div)) >= 0 && (abs(($sum)) * (100 / $div)) < 30)) {
      $array_condicion = [$datos_embalses[$row]["nombre_embalse"], round($sum, 2), $totalop[$datos_embalses[$row]['operador']],(abs(($sum)) * (100 / $div))];
      array_push($embalses_condiciones[0], $array_condicion);
    }
    if ((abs(($sum)) * (100 / $div)) >= 30 && (abs(($sum)) * (100 / $div)) < 60) {
      $array_condicion = [$datos_embalses[$row]["nombre_embalse"], round($sum, 2), $totalop[$datos_embalses[$row]['operador']],(abs(($sum)) * (100 / $div))];
      array_push($embalses_condiciones[1], $array_condicion);
    }
    if ((abs(($sum)) * (100 / $div)) >= 60 && (abs(($sum)) * (100 / $div)) < 90) {
      $array_condicion = [$datos_embalses[$row]["nombre_embalse"], round($sum, 2), $totalop[$datos_embalses[$row]['operador']],(abs(($sum)) * (100 / $div))];
      array_push($embalses_condiciones[2], $array_condicion);
    }
    if ((abs(($sum)) * (100 / $div)) >= 90 && (abs(($sum)) * (100 / $div)) <= 100) {
      $array_condicion = [$datos_embalses[$row]["nombre_embalse"], round($sum, 2), $totalop[$datos_embalses[$row]['operador']],(abs(($sum)) * (100 / $div))];
      array_push($embalses_condiciones[3], $array_condicion);
    }
    if ((abs(($sum)) * (100 / $div)) > 100) {
      $array_condicion = [$datos_embalses[$row]["nombre_embalse"], round($sum, 2), $totalop[$datos_embalses[$row]['operador']],(abs(($sum)) * (100 / $div))];
      array_push($embalses_condiciones[4], $array_condicion);
    }
  } else {
    $array_condicion = [$datos_embalses[$row]["nombre_embalse"], 0, $totalop[$datos_embalses[$row]['operador']], 0];
    array_push($embalses_condiciones[0], $array_condicion);
  }

  $row++;
}

function agregarACondiciones($operador, &$array, $porcentaje, $totalop)
{
  if (array_key_exists($operador, $array)) {
    $array[$operador][0] += 1;
    $array[$operador][$porcentaje] += 1;
    return;
  } else {
    $array[$operador] = [0, 0, 0, 0, 0, 0, $totalop[$operador]];
    agregarACondiciones($operador, $array, $porcentaje, $totalop);
  }
}

function getMonthName()
{
  $fecha_actual = getdate();

  $numero_mes = $fecha_actual['mon'];

  $nombres_meses = array(
    1 => "ENERO",
    2 => "FEBRERO",
    3 => "MARZO",
    4 => "ABRIL",
    5 => "MAYO",
    6 => "JUNIO",
    7 => "JULIO",
    8 => "AGOSTO",
    9 => "SEPTIEMBRE",
    10 => "OCTUBRE",
    11 => "NOVIEMBRE",
    12 => "DICIEMBRE"
  );

  $nombre_mes = $nombres_meses[$numero_mes];

  return  $nombre_mes;
}

function getShortName()
{
  $fecha_actual = getdate();

  $numero_mes = $fecha_actual['mon'];

  $nombres_meses_abreviados = array(
    1 => "Ene",
    2 => "Feb",
    3 => "Mar",
    4 => "Abr",
    5 => "May",
    6 => "Jun",
    7 => "Jul",
    8 => "Ago",
    9 => "Sep",
    10 => "Oct",
    11 => "Nov",
    12 => "Dic"
  );

  $nombre_mes_abreviado = $nombres_meses_abreviados[$numero_mes];

  return $nombre_mes_abreviado;
}

function getYear()
{
  $fecha_actual = getdate();

  $year = $fecha_actual['year'];

  return $year;
}

// $stringPrioritarios = "0";
// $queryPrioritarios = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'prioritarios'");
// if (mysqli_num_rows($queryPrioritarios) > 0) {
//     $stringPrioritarios = mysqli_fetch_assoc($queryPrioritarios)['configuracion'];
// }

$result = mysqli_query($conn, "SELECT nombre_embalse, operador FROM embalses");

$mes_actual = date('m');
$dia_actual = date('d');

$año_actual = date('Y');
$año_pasado = date('Y', strtotime('-1 year'));

/*$image_logo =  "/" . $projectName . "/assets/img/logos/cropped-mminaguas.jpg";
$logo_letters =  "/" . $projectName . "/assets/img/logos/MinaguasLetters.png";*/
// $area =  "/" . $projectName . "/pages/reports_images/Area_cuenca.png";


if (1) {
  $image_logo = "../../assets/img/logos/cropped-mminaguas.jpg";
  $logo_letters = "../../assets/img/logos/MinaguasLetters.png";
  $area =  "../../pages/reports_images/Area_cuenca.png";
  $logo_combinado = "../../assets/img/logos/logo_combinado.jpg";
  $mapa = "../../assets/img/temp/imagen-estatus-mapa-1.png";
  $flecha_arriba = "../../assets/icons/f-arriba.png";
  $flecha_abajo = "../../assets/icons/f-abajo.png";
  $sin_cambio = "../../assets/icons/f-igual.png";
  $status_pie_1 = "../../assets/img/temp/imagen-estatus-pie-1.png";
  $status_pie_2 = "../../assets/img/temp/imagen-estatus-pie-2.png";
  $status_barra_1 = "../../assets/img/temp/imagen-estatus-barra-1.png";
  $status_barra_2 = "../../assets/img/temp/imagen-estatus-barra-2.png";
  $status_mapa = "../../assets/img/temp/imagen-estatus-mapa-2.png";
  $status_mapa_3 = "../../assets/img/temp/imagen-estatus-mapa-3.png";
} else {
  $image_logo = "../../assets/img/logos/cropped-mminaguas.jpg";
  $logo_letters =  "../../assets/img/logos/MinaguasLetters.png";
  $area =  "../../pages/reports_images/Area_cuenca.png";
  $logo_combinado = "../../assets/img/logos/logo_combinado.jpg";
  $mapa = "../../assets/img/temp/imagen-estatus-mapa-1.png";
  $flecha_arriba = "../../assets/icons/f-arriba.png";
  $flecha_abajo = "../../assets/icons/f-abajo.png";
  $sin_cambio = "../../assets/icons/f-igual.png";
  $status_pie_1 = "../../assets/img/temp/imagen-estatus-pie-1.png";
  $status_pie_2 = "../../assets/img/temp/imagen-estatus-pie-2.png";
  $status_barra_1 = "../../assets/img/temp/imagen-estatus-barra-1.png";
  $status_barra_2 = "../../assets/img/temp/imagen-estatus-barra-2.png";
  $status_mapa = "../../assets/img/temp/imagen-estatus-mapa-2.png";
  $status_mapa_3 = "../../assets/img/temp/imagen-estatus-mapa-3.png";
}

// $codigo = "08RHL0101";
// $titulo = "EMBALSE CAMATAGUA - ESTADO ARAGUA";
// $cota = 289.87;
// $mes = "Noviembre";
// $area_cuenta = 636.49;
// $variacion_semanal = "VARIACION SEMANAL";
// $fecha = "02";
// $fecha2 = "08";
// $variacion_mensual = getMonthName();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estatus Embalses</title>

  <style>
    hr {
      background-color: #2E86C1;
      height: 2px;
      width: 80%;
      top: 65px;
      color: #2E86C1;
      position: absolute;
    }

    .square {
      width: 60px;
      height: 60px;
      background-color: #2E86C1;
      border-radius: 10;
      margin-left: auto;
      margin-right: auto;
      text-align: center;
      vertical-align: middle;
      line-height: 100px;

      color: #fff;
      font-size: 40px;
      font-weight: bold;
    }

    .code {
      height: 40px;
      left: 100px;
      color: #2E86C1;
      font-size: 20px;
      font-weight: bold;
    }

    .code-container {
      left: 75px;
      bottom: 10px;
      width: 500px;
      background-color: red;
    }

    .box-title {

      font-size: 18px;
      color: #FFFFFF;
      background-color: #0070C0;
      /* box-shadow: 50px 50px 50px grey;  */
      width: 95%;
      height: 30px;
      text-align: center;
      position: absolute;
      vertical-align: middle;
      margin-top: 650px;
      margin-left: 35px;
    }

    .box-note {
      font-size: 14px;
      ;
      width: 95%;
      height: 25px;
      text-align: left;
      /* position: absolute;  */
      vertical-align: middle;
      margin-top: 60px;
      /* margin-left: 20px;  */
      border: 0.5px solid red;

    }

    .img-logo {
      float: left;
      width: 50px;
      margin-bottom: 50px;
    }

    .img-letters {
      float: right;
      width: 100px;
      background-color: red;
    }

    .container-letters {
      left: 950px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      vertical-align: middle;
      text-align: center;
      padding: 5px;
      border: 1px solid #707273;
      /*width: fit-content;*/
      font-size: 10px;
    }

    th {
      margin-top: auto;
      margin-bottom: auto;
      vertical-align: middle;
      text-align: center;
      background-color: #0070C0;
      color: #FFFFFF;
    }

    .text-celd {
      vertical-align: middle;
      width: 150px;
      text-align: center;
      font-size: 16px;
      border: 1px solid #707273;


    }

    .text-celd-variacion {
      vertical-align: middle;
      width: 75px;
      text-align: center;
      font-size: 16px;
      border: 1px solid #707273;


    }

    .text-celdas {
      vertical-align: middle;
      width: 130px;
      text-align: center;
      font-size: 16px;
      border: 1px solid #707273;


    }

    .celd-table {
      vertical-align: middle;
      width: 80px;
      text-align: center;
      font-size: 14px;
      border: 1px solid #707273;
    }

    .celd-table-2 {
      vertical-align: middle;
      width: 80px;
      text-align: center;
      font-size: 12px;
      border: 1px solid #707273;
    }

    .text-big {
      height: 100px;
      font-size: 16px;
      vertical-align: middle;
    }

    .total {

      font-size: 16px;
      background-color: #DAE3F3;
      border: 1px solid #707273;

    }

    .header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 50px;
      /* background-color: lightgray;*/
      text-align: center;
    }

    .tablaDos {

      vertical-align: middle;
      text-align: center;
      font-size: 12px;

    }

    .spazio {
      background-color: #FFFFFF;
      color: #FFFFFF;
      border: none;
    }
  </style>
</head>

<body>


  <!-- PAGINA 1 -->
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <!-- <div> -->
  <h1 style="position: absolute; top: 45px; left: 50px; text-align:center; color:#2E86C1; font-size: 23px;">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  <h2 style="position: absolute; top: 85px; text-align: center; text-justify: center; color:#021568">Estatus de Fuentes Hídricas para Consumo Humano</h2>
  <div style="width: 1000px; height: 535px; background-color: lightgray; margin: 10px, 0, 0, 35px;">
    <!-- Mapa --> <img style="width:1000px ; height: 535px;" src="<?php echo $mapa ?>" />
  </div>
  <div style="position: absolute; height: 160px; width: 350px; left: 38px; top: 525px; border: gray 1px solid; background-color: #FFFFFF">
    <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
    <p style="position: absolute; top: 20px;
        text-align: left; padding-left: 40px; font-size: 12px;">
    <div style="position: absolute; left: 20px; top: 2px; background-color: red;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición baja (< 30%) <b> <?php echo $valores[0][0] ?> Embalses</b></p>


      <p style="position: absolute; top: 40px;
        text-align: left; padding-left: 40px; font-size: 12px;">
      <div style="position: absolute; left: 20px; top: 2px; background-color: #44BEF0;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición Normal Bajo (30% < A> 60%) <b> <?php echo $valores[0][1] ?> Embalses</b></p>


        <p style="position: absolute; top: 60px;
        text-align: left; padding-left: 40px; font-size: 12px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: blue;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición Normal Alto (60% < A> 90%) <b> <?php echo $valores[0][2] ?> Embalses</b></p>


          <p style="position: absolute; top: 80px;
        text-align: left; padding-left: 40px; font-size: 12px;">
          <div style="position: absolute; left: 20px; top: 2px; background-color: green;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición Buena (> 90%) <b> <?php echo $valores[0][3] ?> Embalses</b></p>


          <p style="position: absolute; top: 100px;
        text-align: left; padding-left: 40px; font-size: 12px;">
          <div style="position: absolute; left: 20px; top: 2px; background-color: #58F558;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición de Alivio <b> <?php echo $valores[0][4] ?> Embalses</b></p>


          <p style="position: absolute; top: 120px;
        text-align: left; padding-left: 40px; font-size: 12px;">
          <div style="position: absolute; left: 20px; top: 2px; width: 0; height: 0;
        border-left: 5px solid transparent; border-right: 5px solid transparent; border-bottom: 10px solid black;"></div> EDC (Embalse de Compensación)</p>

  </div>
  <h4 style="position: absolute; top: 690px; text-align: center; text-justify: center;"><?php echo "$dia_actual DE " . getMonthName() . " $año_actual" ?></h4>

  <!-- </div> -->

  <!-- PAGINA 2 -->

  <?php
  $inicial = false;
  $total_filas = 60;
  $extras = 5; //cabecera y total
  $acumulado = 0;
  $top_margin = 0;
  $titulos_condiciones = [
    "Bajo (< 30 %)",
    "Normal Bajo (30 % < A < 60%)",
    "Normal Alto (60 % < A < 90 %)",
    "Buena ( 90 % < A < 100 %)",
    "Condición de Alivio"
  ]
  ?>

  <?php foreach ($embalses_condiciones as $key => $embalses_condicion) {

    usort($embalses_condicion, function ($a, $b) {
      return strcmp($a[2], $b[2]); // Comparar las cadenas en el índice 2 (tipo)
    });

    $typeCount = array_reduce($embalses_condicion, function ($counts, $item) {
      $tipo = $item[2]; // Índice del tipo
      $counts[$tipo] = ($counts[$tipo] ?? 0) + 1; // Incrementar el conteo para este tipo
      return $counts;
    }, []);

    $filas_tablas = count($embalses_condicion);

    if (($filas_tablas + $extras) <= ($total_filas - $acumulado)) {
      if ($acumulado == 0) {
        if (!$inicial) {
          $inicial = true;
        } else {
          $inicial = false;
        }
      } else {
        $inicial = false;
      }
      $acumulado = $acumulado + $filas_tablas + $extras;
    } else {
      $inicial = true;
      $acumulado = 0;
    }
  ?>

    <?php if ($inicial) { ?>
      <page orientation="portrait">

        <div class="header">
          <hr style="top: 55px; color:#1B569D">
          <h1 style="position: absolute; top: 45px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
          <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
          <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
        </div>
        <!-- <div style="position: absolute; top: 80px; left: 115px; font-size: 18px; color:#000000; margin-bottom:5px;"> -->
      <?php } ?>

      <b style="margin-left: 100px;"><?php echo $titulos_condiciones[$key]; ?> </b>

      <table style="margin-bottom: 10px; margin-left: 100px;">
        <tr>
          <th class="text-celd" style=" padding-top:1px; padding-bottom:1px;">EMBALSE</th>
          <th class="text-celd" style=" padding-top:1px; padding-bottom:1px; width:100px; font-size: 12px;">VOL. DISP. (HM3)</th>
          <th class="text-celd" style=" padding-top:1px; padding-bottom:1px; width:60px; ">%</th>
          <th class="text-celd" style=" padding-top:1px; padding-bottom:1px;">HIDROLÓGICA</th>
        </tr>

        <?php
        $j = 0;
        $cuenta = 0;
        while ($j < count($embalses_condicion)) {
          $cuenta++; ?>
          <tr>
            <td class="text-celd" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $embalses_condicion[$j][0]; ?> </td>
            <td class="text-celd" style="font-size: 12px; padding-top:1px; padding-bottom:1px; width:100px; "><?php echo number_format($embalses_condicion[$j][1],2,",","."); ?></td>
            <td class="text-celd" style="font-size: 12px; padding-top:1px; padding-bottom:1px; width:60px; "><?php echo number_format($embalses_condicion[$j][3],2,",","."); ?>%</td>

            <?php if ($j == 0 || $embalses_condicion[$j][2] != $embalses_condicion[$j - 1][2]) { ?>
              <td class="text-celd" rowspan="<?php echo $typeCount[$embalses_condicion[$j][2]] ?>" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $embalses_condicion[$j][2]; ?> </td>
            <?php } else { ?>
            <?php } ?>
          </tr>

        <?php
          $j++;
        }
        ?>

        <tr style="height: 10px;">
          <td class="" style="font-size: 16px; background-color: #DAE3F3; border: 1px solid #707273;"><b> TOTAL </b></td>
          <td class="" style="font-size:12px; background-color: #DAE3F3; border: 1px solid #707273;" colspan="3"><b><?php echo $cuenta . " "; ?>Embalses<?php echo " (" . number_format(($cuenta * 100 / count($datos_embalses)), 2, ",", ".") . "%)" ?></b> </td>
        </tr>
      </table>

      <?php if ($inicial) { ?>
        <!-- </div> -->
      </page>
    <?php } ?>
  <?php

    // $inicial = false;
  } ?>


  <!-- PAGINA 3 -->

  <!-- ESTA PAGINA SE ELIMINÓ, YA QUE EN LA PAGINA DOS SE USÓ UN CICLO DE PÁGINAS -->

  <!-- PAGINA 4 -->
  <page orientation="landscape">
    <!-- <div style="page-break-before: always;"></div> -->
    <div class="header">
      <hr style="top: 55px; color:#1B569D">
      <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
      <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
      <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div>
    <h1 style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;">CONDICIONES ACTUALES DE ALMACENAMIENTO DE EMBALSES POR HIDROLÓGICA</h1>

    <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 100px; margin-left: 100px;"><b></b>


      <table style="margin-top: 20px;">
        <tr>
          <th class="tablaDos" rowspan="2">HIDROLÓGICA</th>
          <th class="tablaDos">BAJA</th>
          <th class="tablaDos" rowspan="2">% TOTAL</th>
          <th class="tablaDos">NORMAL- <br> BAJO</th>
          <th class="tablaDos" rowspan="2">% TOTAL</th>
          <th class="tablaDos">NORMAL- <br> ALTO</th>
          <th class="tablaDos" rowspan="2">% TOTAL</th>
          <th class="tablaDos">BUENA</th>
          <th class="tablaDos">ALIVIANDO</th>
          <th class="tablaDos" rowspan="2"> TOTAL</th>
          <th class="tablaDos" rowspan="2">% TOTAL</th>
        </tr>

        <tr>
          <th class="tablaDos">
            < 30 %</th>
          <th class="tablaDos">30% < A < 60% </th>
          <th class="tablaDos">60% < A < 90% </th>
          <th class="tablaDos">90% < A < 100% </th>
          <th class="tablaDos">
            < 100 %</th>
        </tr>
        <?php

        usort($condiciones, function ($a, $b) {
          return strcmp($a[6], $b[6]); // Comparar las cadenas en el índice 2 (tipo)
        });

        foreach ($condiciones as $key => $values) { ?>
          <tr>
            <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $values[6] ?></td>
            <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $values[1] . "/" . $values[0] ?></td>
            <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $values[0] != 0 ? (number_format((($values[1] * 100) / $values[0]), 2, '.', '')) : 0 ?>%</td>
            <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $values[2] . "/" . $values[0] ?></td>
            <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $values[0] != 0 ? (number_format((($values[2] * 100) / $values[0]), 2, '.', '')) : 0 ?>%</td>
            <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $values[3] . "/" . $values[0] ?></td>
            <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $values[0] != 0 ? (number_format((($values[3] * 100) / $values[0]), 2, '.', '')) : 0 ?>%</td>
              <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $values[4] . "/" . $values[0] ?></td>
              <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $values[5] . "/" . $values[0] ?></td>
              <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo ($values[4] + $values[5]) . "/" . $values[0] ?></td>
              <td class="tablaDos" style="font-size: 12px; padding-top:1px; padding-bottom:1px;"><?php echo $values[0] != 0 ? (number_format(((($values[4] + $values[5]) * 100) / $values[0]), 2, '.', '')) : 0 ?>%</td>

          </tr>
        <?php
        } ?>

        <tr>
          <th class="spazio" colspan="7"></th>
        </tr>

        <tr>
          <th class="spazio" style="border-bottom: 1px solid #707273; border-right: 1px solid #707273;"><b></b></th>
          <th class="tablaDos" colspan="2"><b> BAJA </b></th>
          <th class="tablaDos" colspan="2"><b></b> NORMAL-BAJO</th>
          <th class="tablaDos" colspan="2"><b></b> NORMAL-ALTO</th>
          <th class="tablaDos"><b> BUENA </b></th>
          <th class="tablaDos"><b></b> ALIVIANDO</th>
          <th class="tablaDos"><b></b> TOTAL</th>
          <th class="tablaDos"><b></b> % </th>
        </tr>


        <tr>
          <td class="text-celdas total" style="font-size: 12px;"><b>TOTAL</b></td>
          <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[1] . "/" . $CT[0] ?></b></td>
          <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[2] . "/" . $CT[0] ?></b></td>
          <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[3] . "/" . $CT[0] ?></b></td>
            <td class="tablaDos" style="font-size: 12px;" rowspan="2"><b><?php echo $CT[4] . "/" . $CT[0] ?></b></td>
            <td class="tablaDos" style="font-size: 12px;" rowspan="2"><b><?php echo $CT[5] . "/" . $CT[0] ?></b></td>
            <td class="tablaDos" style="font-size: 12px;" rowspan="2"><b><?php echo ($CT[4] + $CT[5]) . "/" . $CT[0] ?></b></td>
            <td class="tablaDos" style="font-size: 12px;" rowspan="2"><b><?php echo $CT[0] != 0 ? (number_format(((($CT[4] + $CT[5]) * 100) / $CT[0]), 2, '.', '')) : 0 ?>%</b></td>
        </tr>

        <tr>
          <td class="text-celdas total" style="font-size: 12px;"><b>%</b></td>
          <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[0] != 0 ? (number_format((($CT[1] * 100) / $CT[0]), 2, '.', '')) : 0 ?>%</b></td>
          <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[0] != 0 ? (number_format((($CT[2] * 100) / $CT[0]), 2, '.', '')) : 0 ?>%</b></td>
          <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[0] != 0 ? (number_format((($CT[3] * 100) / $CT[0]), 2, '.', '')) : 0 ?>%</b></td>
        </tr>

      </table>

       <!-- <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 39px; margin-left: 620px;"><b> </b>

        <table>
          <tr>
            <th style="height: 38px;" class="tablaDos" rowspan="2">HIDROLÓGICA</th>
            <th class="tablaDos">BUENA</th>
            <th class="tablaDos">ALIVIANDO</th>
            <th class="tablaDos" rowspan="2"> TOTAL</th>
            <th class="tablaDos" rowspan="2">% TOTAL</th>
          </tr>

          <tr>
            <th class="tablaDos">90% < A < 100% </th>
            <th class="tablaDos">
              < 100 %</th>
          </tr>

         <?php foreach ($condiciones as $key => $values) { ?>
            <tr>
              <td class="tablaDos" style="font-size: 12px;"><?php echo $values[6] ?></td>
              <td class="tablaDos" style="font-size: 12px;"><?php echo $values[4] . "/" . $values[0] ?></td>
              <td class="tablaDos" style="font-size: 12px;"><?php echo $values[5] . "/" . $values[0] ?></td>
              <td class="tablaDos" style="font-size: 12px;"><?php echo ($values[4] + $values[5]) . "/" . $values[0] ?></td>
              <td class="tablaDos" style="font-size: 12px;"><?php echo $values[0] != 0 ? (number_format(((($values[4] + $values[5]) * 100) / $values[0]), 2, '.', '')) : 0 ?>%</td>
            </tr>
          <?php
          } ?> 

          <tr>
            <th class="spazio" colspan="5"></th> antes era  colspan="7" 
           </tr>

          <tr>
            <th class="spazio" style="border-bottom: 1px solid #707273; border-right: 1px solid #707273;"><b></b></th>
            <th class="tablaDos"><b> BUENA </b></th>
            <th class="tablaDos"><b></b> ALIVIANDO</th>
            <th class="tablaDos"><b></b> TOTAL</th>
            <th class="tablaDos"><b></b> % </th>
          </tr>

          <tr>
            <td class="text-celdas total" style="font-size: 12px; height: 27px;"><b>TOTAL</b></td>
            <td class="tablaDos" style="font-size: 12px;"><b><?php echo $CT[4] . "/" . $CT[0] ?></b></td>
            <td class="tablaDos" style="font-size: 12px;"><b><?php echo $CT[5] . "/" . $CT[0] ?></b></td>
            <td class="tablaDos" style="font-size: 12px;"><b><?php echo ($CT[4] + $CT[5]) . "/" . $CT[0] ?></b></td>
            <td class="tablaDos" style="font-size: 12px;"><b><?php echo $CT[0] != 0 ? (number_format(((($CT[4] + $CT[5]) * 100) / $CT[0]), 2, '.', '')) : 0 ?>%</b></td>
          </tr>


        </table>
      </div> -->

    </div>
  </page>
  <!-- PAGINA 5 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>
  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>CONDICIONES ACTUALES DE ALMACENAMIENTO DE EMBALSES</b>
  </div>

  <img style="width: 550px; height: 450px; background-color: lightgray; margin-top: 50px; margin-left: 35px;" src="<?php echo $status_pie_1 ?>">


  <div style="font-size: 15px; color:#000000; position: absolute;  margin-top: 200px; margin-left: 640px;"><b> <u><?php echo $lista[3] ?> EMBALSES</u> EN CONDICIONES BUENAS Y MUY BUENAS <br> ( > 90% Y ALIVIANDO )</b></div>

  <div style="font-size: 15px; color:#000000; position: absolute;  margin-top: 300px; margin-left: 640px;"><b> <u><?php echo $lista[2] ?> EMBALSES</u> EN CONDICIONES NORMALES ALTO <br> ( 60 % < A < 90 % )</b>
  </div>

  <div style="font-size: 15px; color:#000000; position: absolute;  margin-top: 400px; margin-left: 640px;"><b> <u><?php echo $lista[1] ?> EMBALSES</u> EN CONDICIONES NORMALES BAJO <br> ( 30 % < A < 60% )</b>
  </div>

  <div style="font-size: 15px; color:#000000; position: absolute;  margin-top: 500px; margin-left: 640px;"><b> <u><?php echo $lista[0] ?> EMBALSES</u> EN CONDICIONES BAJAS ( < 30 %)</b>
  </div>

  <div class="box-title"><b> <?php echo round(($lista[2] + $lista[3]) * 100 / ($lista[2] + $lista[3] + $lista[1] + $lista[0]), 2) ?>% DE LOS EMBALSES SE ENCUENTRAN EN CONDICIONES NORMALES A MUY BUENAS</b></div>

  <!-- PAGINA 6 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>
  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>CONDICIONES ACTUALES DE ALMACENAMIENTO DE EMBALSES</b>
  </div>

  <div style="font-size: 17px; color: #0070C0; position: absolute;  margin-top: 120px; margin-left: 7px;"><b>DESDE EL <?php echo date("d/m/Y", strtotime($fecha1)); ?> HASTA HOY</b>
  </div>

  <img style="width: 450px; height: 450px; background-color: lightgray; margin-top: 80px; margin-left: 35px;" src="<?php echo $status_barra_1 ?>">


  <div style="position: absolute; width: 0.5px; height: 600px; background-color: #7F7F7F; margin-top: 110px; margin-left: 525px;"></div>

  <div style="font-size: 17px; color: #0070C0; position: absolute;  margin-top: 120px; margin-left: 550px;"><b>DESDE EL <?php echo date("d/m/Y", strtotime($fecha2)); ?> HASTA HOY</b>
  </div>

  <img style="width: 450px; height: 450px; background-color: lightgray; position: absolute; margin-top: 180px; margin-left: 570px;" src="<?php echo $status_barra_2 ?>">

  <div style="position: absolute; margin-top: 670px; margin-left: 50px; width: 95%; height: 100px;">
    <div style="position: absolute; font-size: 18px; color:red; text-align: center;"> <b> (Varió
        <?php
        if (abs(($volumenes[5] - $volumenes[2])) != 0) {
          echo round(($volumenes[5] - $volumenes[2]) * 100 /  $volumenes[2], 2);
        } else {
          echo 0;
        };

        ?>% comparado con la semana pasada y <br>
        <?php
        if (abs(($volumenes[4] - $volumenes[2])) != 0) {
          echo round(($volumenes[4] - $volumenes[2]) * 100 / $volumenes[2], 2);
        } else {
          echo 0;
        };

        ?>% con respecto a hace 15 días)</b></div>
    <div style="position: absolute; margin-left: 550px; font-size: 18px; color:red; text-align: center;"><b> (Varió
        <?php

        if (abs(($volumenes[5] - $volumenes[3])) != 0) {
          echo round(($volumenes[5] - $volumenes[3]) * 100 / $volumenes[3], 2);
        } else {
          echo 0;
        };
        ?>% comparado con la semana pasada y <br>
        <?php
        if (abs(($volumenes[4] - $volumenes[3])) != 0) {
          echo round(($volumenes[4] - $volumenes[3]) * 100 / $volumenes[3], 2);
        } else {
          echo 0;
        };


        ?>% con respecto a hace 15 días)</b></div>
  </div>

  <!-- PAGINA 7 -->
  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>VARIACIÓN DE VOLUMEN DE ALMACENAMIENTO NACIONAL</b>
  </div>

  <div>
    <div style="width: 1000px; height: 535px; background-color: lightgray; margin: 10px, 0, 0, 35px;">
      <img style="width:1000px ; height: 535px;" src="<?php echo $status_mapa ?>" />
    </div>
  </div>

  <div style="position: absolute; height: 160px; width: 350px; left: 38px; top: 485px; border: gray 1px solid; background-color: #FFFFFF">
    <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
    <p style="position: absolute; top: 35px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 20px; top: 2px; background-color: blue;
         border-radius: 5; height: 10px; width: 10px;"></div> Embalses</p>


    <p style="position: absolute; top: 60px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 15px; top: 2px; height: 20px; width: 20px;"><img style="width: 20px; height: 15px;" src="<?php echo $flecha_arriba ?>">
    </div>Aumento de Volumen <b> <?php echo $valores[1][0] ?> Embalses</b></p>


    <p style="position: absolute; top: 85px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 15px; top: 2px; height: 20px; width: 20px;"><img style="width: 20px; height: 15px;" src="<?php echo $flecha_abajo ?>">
    </div>Disminución de Volumen <b> <?php echo $valores[1][1] ?> Embalses</b></p>


    <p style="position: absolute; top: 110px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 15px; top: 2px; height: 20px; width: 20px;"><img style="width: 20px; height: 15px;" src="<?php echo $sin_cambio ?>">
    </div>Sin Cambios <b> <?php echo $valores[1][2] ?> Embalses</b></p>

  </div>
  <?php setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp'); // Establecer la localización a español
  ?>

  <h4 style="position: absolute; top: 640px; text-align: right; text-justify: right;">DESDE EL <?php echo mb_convert_case(date('d', strtotime($fecha1)) . ' DE ' . $meses[date('n', strtotime($fecha1))], MB_CASE_UPPER, 'UTF-8'); ?> </h4>

  <!-- PAGINA 8 -->
  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>VARIACIÓN DE VOLUMEN DE ALMACENAMIENTO NACIONAL</b>
  </div>

  <div>
    <div style="width: 1000px; height: 535px; background-color: lightgray; margin: 10px, 0, 0, 35px;">
      <img style="width:1000px ; height: 535px;" src="<?php echo $status_mapa_3 ?>" />
    </div>
  </div>

  <div style="position: absolute; height: 160px; width: 350px; left: 38px; top: 485px; border: gray 1px solid; background-color: #FFFFFF">
    <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
    <p style="position: absolute; top: 35px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 20px; top: 2px; background-color: blue;
         border-radius: 5; height: 10px; width: 10px;"></div> Embalses</p>


    <p style="position: absolute; top: 60px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 15px; top: 2px; height: 20px; width: 20px;"><img style="width: 20px; height: 15px;" src="<?php echo $flecha_arriba ?>">
    </div>Aumento de Volumen <b> <?php echo $valores[2][0] ?> Embalses</b></p>


    <p style="position: absolute; top: 85px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 15px; top: 2px; height: 20px; width: 20px;"><img style="width: 20px; height: 15px;" src="<?php echo $flecha_abajo ?>">
    </div>Disminución de Volumen <b> <?php echo $valores[2][1] ?> Embalses</b></p>


    <p style="position: absolute; top: 110px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 15px; top: 2px; height: 20px; width: 20px;"><img style="width: 20px; height: 15px;" src="<?php echo $sin_cambio ?>">
    </div>Sin Cambios <b> <?php echo $valores[2][2] ?> Embalses</b></p>


  </div>

  <h4 style="position: absolute; top: 640px; text-align: right; text-justify: right;"> DESDE EL <?php echo mb_convert_case(date('d', strtotime($fecha2)) . ' DE ' . $meses[date('n', strtotime($fecha2))], MB_CASE_UPPER, 'UTF-8'); ?></h4>
  <!--aqui-->
  <!-- PAGINA 9 -->

  <?php
  $A_operador = 85;
  $A_tabla = 120;
  $incremento = 0;
  $acumulado = 0;
  $disponible = 24;
  $inicial = true;
  $tituloini = true;
  ?>

  <?php foreach ($operadores as $operador) { ?>


    <?php //if ($inicial) { 
    ?>
    <div style="page-break-before: always;"></div>
    <div class="header">
      <hr style="top: 55px; color:#1B569D">
      <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
      <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div>
    <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 50px; margin-left: 5px;"><b>VARIACIONES DE VOLUMEN DE LOS EMBALSES HASTA HOY</b>
    </div>

    <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: <?php echo $A_operador; ?>px; margin-left: 500px;"><b><?php echo strtoupper($operador); ?></b></div>
    <?php
    // } else {
    // } 
    ?>

    <div style="width: 500px; height: 280px; background-color: lightgray; margin-top: 40px; margin-left: 10px;"><?php echo mb_convert_case(date('d', strtotime($fecha1)) . ' DE ' . $meses[date('n', strtotime($fecha1))], MB_CASE_UPPER, 'UTF-8'); ?>
    </div>

    <div style="width: 500px; height: 280px; background-color: lightgray; position: absolute; margin-top: 450px; margin-left: 10px;"><?php echo mb_convert_case(date('d', strtotime($fecha2)) . ' DE ' . $meses[date('n', strtotime($fecha2))], MB_CASE_UPPER, 'UTF-8'); ?>
    </div>

    <div style="position: absolute; margin-top: <?php echo $A_tabla ?>px; margin-left: 10px; width: 95%; height: 100px;">


      <div style="position: absolute; margin-left: 525px; font-size: 18px; text-align: right;"><b><?php echo date("d/m/Y", strtotime($fecha2)); ?></b>
        <table>
          <tr>
            <th style="height: 38px;" class="text-celd-variacion" rowspan="2">EMBALSE</th>
            <th class="text-celd-variacion" colspan="2"><b><?php echo mb_convert_case(date('d', strtotime($fecha1)) . ' DE ' . $meses[date('n', strtotime($fecha1))], MB_CASE_UPPER, 'UTF-8'); ?></b></th>
            <th class="text-celd-variacion" colspan="2"><b><?php echo mb_convert_case(date('d', strtotime($fecha2)) . ' DE ' . $meses[date('n', strtotime($fecha2))], MB_CASE_UPPER, 'UTF-8'); ?></b></th>
          </tr>

          <tr>
            <th class="text-celd-variacion">VAR. VOL.(HM3)</th>
            <th class="text-celd-variacion">% VAR. VOL.</th>
            <th class="text-celd-variacion">VAR. VOL.(HM3)</th>
            <th class="text-celd-variacion">% VAR. VOL.</th>
          </tr>
          <?php
          $tot_vol_1 = 0;
          $tot_por_1 = 0;
          $tot_vol_2 = 0;
          $tot_por_2 = 0;
          foreach ($embalses_variacion as $value) {
            if (strtolower(trim($value[0])) == strtolower(trim($operador))) {
              $tot_vol_1 += $value[2];
              $tot_por_1 += $value[3];
              $tot_vol_2 += $value[4];
              $tot_por_2 += $value[5];
          ?>
              <tr>
                <td class="text-celd" style="font-size: 12px; width: 125px;"><?php echo $value[1] ?></td>
                <td class="text-celd-variacion" style="font-size: 12px; color:<?php if ($value[2] < 0) {
                                                                                echo "red";
                                                                              } else {
                                                                                echo "green";
                                                                              } ?>"><?php echo number_format($value[2], 2, ",", ".") ?></td>
                <td class="text-celd-variacion" style="font-size: 12px; color:<?php if ($value[3] < 0) {
                                                                                echo "red";
                                                                              } else {
                                                                                echo "green";
                                                                              } ?>"><?php echo number_format($value[3], 2, ",", ".") ?>%</td>
                <td class="text-celd-variacion" style="font-size: 12px; color:<?php if ($value[4] < 0) {
                                                                                echo "red";
                                                                              } else {
                                                                                echo "green";
                                                                              } ?>"><?php echo number_format($value[4], 2, ",", "."); ?></td>
                <td class="text-celd-variacion" style="font-size: 12px; color:<?php if ($value[5] < 0) {
                                                                                echo "red";
                                                                              } else {
                                                                                echo "green";
                                                                              } ?>"><?php echo number_format($value[5], 2, ",", "."); ?>%</td>
              </tr>
          <?php }
          } ?>
          <tr>
            <td class="text-celd" style="font-size: 16px; width: 125px;"><b>TOTAL</b></td>
            <td class="text-celd-variacion" style="font-size: 12px; color:<?php if ($tot_vol_1 < 0) {
                                                                            echo "red";
                                                                          } else {
                                                                            echo "green";
                                                                          } ?>"><b><?php echo number_format($tot_vol_1, 2, ",", "."); ?></b></td>
            <td class="text-celd-variacion" style="font-size: 12px; color:<?php if ($tot_por_1 < 0) {
                                                                            echo "red";
                                                                          } else {
                                                                            echo "green";
                                                                          } ?>"><b><?php echo number_format($tot_por_1, 2, ",", "."); ?>%</b></td>
            <td class="text-celd-variacion" style="font-size: 12px; color:<?php if ($tot_vol_2 < 0) {
                                                                            echo "red";
                                                                          } else {
                                                                            echo "green";
                                                                          } ?>"><b><?php echo number_format($tot_vol_2, 2, ",", "."); ?></b></td>
            <td class="text-celd-variacion" style="font-size: 12px; color:<?php if ($tot_por_2 < 0) {
                                                                            echo "red";
                                                                          } else {
                                                                            echo "green";
                                                                          } ?>"><b><?php echo number_format($tot_por_2, 2, ",", "."); ?>%</b></td>
          </tr>
        </table>

      </div>
    </div>

  <?php }
  ?>

  <!-- PAGINA 10

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 55px; margin-left: 5px;"><b>VARIACIONES DE VOLUMEN DE LOS EMBALSES HASTA HOY</b>
  </div>

  <div style="position: absolute; margin-top: 100px; margin-left: 10px; width: 95%; height: 100px;">

    <div style="position: absolute; font-size: 18px; text-align: right;"> <b> HIDROBOLÍVAR DESDE EL FECHA</b></div>
    <div style="width: 520px; height: 520px; background-color: lightgray; margin-top: 50px; margin-left: 10px;">
    </div>


    <div style="position: absolute; margin-left: 550px; font-size: 18px; text-align: right; margin-top: 50px;">
      <table>
        <tr>
          <th class="text-celd text-big">EMBALSE</th>
          <th class="text-celd text-big">VAR. VOL. <br><br><br> (HM3)</th>
          <th class="text-celd text-big">% VAR. VOL.</th>
        </tr>
        <tr>
          <td class="text-celd" style="font-size: 12px; height: 50px;">PRUEBA</td>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>

        <tr>
          <td class="text-celd" style="font-size: 12px; height: 50px;"><b>TOTAL</b></td>
          <td class="text-celd" style="font-size: 12px;"><b>PRUEBA</b></td>
          <td class="text-celd" style="font-size: 12px;"><b>PRUEBA</b></td>
        </tr>

      </table>


    </div>
  </div>

  <!-- PAGINA 11 

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 50px; margin-left: 500px;"><b>HIDROCENTRO</b>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 85px; margin-left: 5px;"><b>VARIACIONES DE VOLUMEN DE LOS EMBALSES HASTA HOY</b>
  </div>

  <div style="width: 520px; height: 320px; background-color: lightgray; margin-top: 20px; margin-left: 10px;">
  </div>

  <div style="width: 520px; height: 320px; background-color: lightgray; position: absolute; margin-top: 120px; margin-left: 560px;">
  </div>

  <div style="position: absolute; margin-top: 430px; margin-left: 10px; width: 95%; height: 100px;">
    <div style="position: absolute; font-size: 18px; text-align: right;"> <b>FECHA</b>
      <table>
        <tr>
          <th class="text-celd" rowspan="2">EMBALSE</th>
          <th class="text-celd" colspan="2">DESDE fecha</th>
        </tr>
        <tr>
          <th class="text-celd" style="height: 20px;">VAR. VOL. <br>(HM3)</th>
          <th class="text-celd" rowspan="">% VAR. <br> VOL.</th>
        </tr>
        <?php foreach ($embalses_variacion as $value) {
          if (strtolower(trim($value[0])) == strtolower(trim("hidrocentro"))) {
        ?>
            <tr>
              <td class="text-celd" style="font-size: 12px;"><?php echo $value[1] ?></td>
              <td class="text-celd" style="font-size: 12px;"><?php echo $value[2] ?></td>
              <td class="text-celd" style="font-size: 12px;"><?php echo $value[3] ?>%</td>
            </tr>
        <?php }
        } ?>
        <tr>
          <td class="text-celd" style="font-size: 16px;"><b>TOTAL</b></td>
          <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
          <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        </tr>

      </table>

    </div>

    <div style="position: absolute; margin-left: 550px; font-size: 18px; text-align: right;"><b>FECHA</b>
      <table>
        <tr>
          <th class="text-celd" rowspan="2">EMBALSE</th>
          <th class="text-celd" colspan="2">DESDE fecha</th>
        </tr>
        <tr>
          <th class="text-celd" style="height: 20px;">VAR. VOL. <br>(HM3)</th>
          <th class="text-celd" rowspan="">% VAR. <br> VOL.</th>
        </tr>

        <tr>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>

        <tr>
          <td class="text-celd" style="font-size: 16px;"><b>TOTAL</b></td>
          <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
          <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        </tr>


      </table>

    </div>
  </div>
  <!-- PAGINA 12 

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 50px; margin-left: 500px;"><b>HIDROPÁEZ</b>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 85px; margin-left: 5px;"><b>VARIACIONES DE VOLUMEN DE LOS EMBALSES HASTA HOY</b>
  </div>

  <div style="width: 520px; height: 320px; background-color: lightgray; margin-top: 20px; margin-left: 10px; text-align: right; font-size: 18px;"><b>FECHA</b>
  </div>

  <div style="width: 520px; height: 320px; background-color: lightgray; position: absolute; margin-top: 120px; margin-left: 560px; text-align: right; font-size: 18px;"><b>FECHA</b>
  </div>

  <div style="position: absolute; margin-top: 430px; margin-left: 10px; width: 95%; height: 100px;">
    <div style="position: absolute; margin-top: 30px;">
      <table>
        <tr>
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VAR. VOL. <br> (HM3)</th>
          <th class="text-celd">% VAR. VOL.</th>
        </tr>
        <tr>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
          <td class="text-celd" style="font-size: 16px;"><b>TOTAL</b></td>
          <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
          <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        </tr>
      </table>

    </div>

    <div style="position: absolute; margin-left: 550px; margin-top: 30px;">
      <table>
        <tr>
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VAR. VOL. <br> (HM3)</th>
          <th class="text-celd">% VAR. VOL.</th>
        </tr>
        <tr>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
          <td class="text-celd" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
          <td class="text-celd" style="font-size: 16px;"><b>TOTAL</b></td>
          <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
          <td class="text-celd" style="font-size: 16px;"><b>PRUEBA</b></td>
        </tr>
      </table>

    </div>
  </div>
  <!-- PAGINA 12 

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="width: 520px; height: 320px; background-color: lightgray;  position: absolute; margin-top: 80px; margin-left: 10px; text-align: right; font-size: 18px;"><b>FECHA</b>
  </div>

  <div style="width: 520px; height: 320px; background-color: lightgray; position: absolute; margin-top: 420px; margin-left: 10px; text-align: right; font-size: 18px;"><b>FECHA</b>
  </div>

  <div style="width: 520px; height: 320px; position: absolute; margin-top: 80px; margin-left: 550px;">
    <table>
      <tr>
        <th class="celd-table" colspan="5">HIDROLAGO</th>
      </tr>
      <tr>
        <th class="celd-table" rowspan="2">EMBALSE</th>
        <th style="font-size: 12px" colspan="2">DESDE 05/ENE/2023</th>
        <th style="font-size: 12px" colspan="2">DESDE 01/JUN/2023</th>
      </tr>
      <tr>
        <th class="celd-table">VAR. VOL. <br>(HM3)</th>
        <th class="celd-table">% VAR. VOL.</th>
        <th class="celd-table">VAR. VOL. <br>(HM3)</th>
        <th class="celd-table">% VAR. VOL.</th>
      </tr>
      <tr>
        <td class="" style="font-size: 12px; width:10px">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
        <td class="" style="font-size: 12px;">PRUEBA</td>
      </tr>
      <tr>
        <td class="" style="font-size: 12px;"><b>TOTAL</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
      </tr>
    </table>

  </div>

  <div style="position: absolute; margin-top: 420px; margin-left: 10px; width: 95%; height: 100px;">

    <div style="position: absolute; margin-left: 540px; font-size: 18px; text-align: right;">
      <table>
        <tr>
          <th class="celd-table" colspan="5">FALCÓN</th>
        </tr>
        <tr>
          <th class="celd-table" rowspan="2">EMBALSE</th>
        </tr>
        <tr>
          <th class="celd-table">VAR. VOL. <br>(HM3)</th>
          <th class="celd-table">% VAR. VOL.</th>
          <th class="celd-table">VAR. VOL. <br>(HM3)</th>
          <th class="celd-table">% VAR. VOL.</th>
        </tr>
        <tr>
          <td class="" style="font-size: 12px; width:10px">PRUEBA</td>
          <td class="" style="font-size: 12px;">PRUEBA</td>
          <td class="" style="font-size: 12px;">PRUEBA</td>
          <td class="" style="font-size: 12px;">PRUEBA</td>
          <td class="" style="font-size: 12px;">PRUEBA</td>
        </tr>
        <tr>
          <td class="" style="font-size: 12px;"><b>TOTAL</b></td>
          <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
          <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
          <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
          <td class="" style="font-size: 12px;"><b>PRUEBA</b></td>
        </tr>
      </table>
    </div>
  </div> -->
  <!-- PAGINA 13 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>VARIACIONES DE VOLUMEN DE LOS EMBALSES HASTA HOY <?php echo $fecha3 . " - " . $fecha2; ?></b>
  </div>

  <div style="position: absolute; margin-top: 80px; margin-left: 30px; width: 95%; height: 100px;">
    <div style="position: absolute; margin-top: 30px;">
      <table>
        <tr>
          <th style=" width: 100px;" class="text-celdas" rowspan="2">HIDROLÓGICA</th>
          <th style=" width: 90px; " class="text-celdas" colspan="2">
            <p>VOLUMEN DISPONIBLE (HM3)</p>
          </th>
          <th style=" width: 90px;" class="text-celdas" colspan="2">
            <p style="padding-top: 25px;">VARIACIÓN DEL VOLUMEN DISPONIBLE (HM3)</p>
          </th>
          <th style=" width: 90px;" class="text-celdas" colspan="2">
            <p style=" padding-top: 36px;">VARIACIÓN PORCENTUAL DE VOLUMEN HASTA HOY (%)</p>
          </th>
          <th style=" width: 90px;" class="text-celdas">VARIACIÓN DE VOLUMEN HACE UNA SEMANA</th>
          <th style=" width: 90px;" class="text-celdas">VARIACIÓN PORCENTUAL DE VOLUMEN HACE UNA SEMANA</th>

        </tr>
        <tr>
          <th class="text-celdas" style="font-size: 12px; width: 90px;">Al <?php echo date("d/m/Y", strtotime($fecha1)); ?></th>
          <th class="text-celdas" style="font-size: 12px; width: 90px;">Al <?php echo date("d/m/Y", strtotime($fecha2)); ?></th>
          <th class="text-celdas" style="font-size: 12px; width: 90px;">Desde<?php echo date("d/m/Y", strtotime($fecha1)); ?></th>
          <th class="text-celdas" style="font-size: 12px; width: 90px;">Desde<?php echo date("d/m/Y", strtotime($fecha2)); ?></th>
          <th class="text-celdas" style="font-size: 12px; width: 90px;">Desde<?php echo date("d/m/Y", strtotime($fecha1)); ?></th>
          <th class="text-celdas" style="font-size: 12px; width: 90px;">Desde<?php echo date("d/m/Y", strtotime($fecha2)); ?></th>
          <th class="text-celdas" style="font-size: 12px; width: 90px;">(Hm3)</th>
          <th class="text-celdas" style="font-size: 12px; width: 90px;">(%)</th>
        </tr>
        <?php
        ksort($variacion_total_op);

        foreach ($variacion_total_op as $key => $value) { ?>
          <tr>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo $key ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo number_format($value[0], 2, ",", ""); ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo number_format($value[1], 2, ",", ""); ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo number_format($value[2], 2, ",", ""); ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo number_format($value[3], 2, ",", ""); ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo number_format($value[4], 2, ",", "") . "%"; ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo number_format($value[5], 2, ",", "") . "%"; ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo number_format($value[6], 2, ",", ""); ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo number_format($value[7], 2, ",", "") . "%"; ?></td>
          </tr>
        <?php } ?>
      </table>

    </div>
  </div>
  <!-- PAGINA 14 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic; text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <h2 style="position: absolute; top: 50px; text-align: center; text-justify: center; color:#000000">GARANTÍA DE ABASTECIMIENTO DE LOS EMBALSES</h2>

  <div>
    <div style="width: 1000px; height: 565px; background-color: lightgray; margin: 10px, 0, 0, 35px;">
      <!-- Mapa --> <img style="width:1000px ; height: 565px;" src="<?php echo $mapa ?>" />
    </div>
    <div style="position: absolute; height: 160px; width: 350px; left: 38px; top: 540px; border: gray 1px solid; background-color: #FFFFFF">
      <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
      <p style="position: absolute; top: 25px;
        text-align: left; padding-left: 40px; font-size: 12px;">
      <div style="position: absolute; left: 20px; top: 2px; background-color: red;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición baja (< 30%) <b> <?php echo $valores[0][0] ?> Embalses</b></p>


        <p style="position: absolute; top: 45px;
        text-align: left; padding-left: 40px; font-size: 12px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: #44BEF0;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición Normal Bajo (30% < A> 60%) <b> <?php echo $valores[0][1] ?> Embalses</b></p>


          <p style="position: absolute; top: 65px;
        text-align: left; padding-left: 40px; font-size: 12px;">
          <div style="position: absolute; left: 20px; top: 2px; background-color: blue;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición Normal Alto (60% < A> 90%) <b> <?php echo $valores[0][2] ?> Embalses</b></p>


            <p style="position: absolute; top: 85px;
        text-align: left; padding-left: 40px; font-size: 12px;">
            <div style="position: absolute; left: 20px; top: 2px; background-color: green;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición Buena (> 90%) <b> <?php echo $valores[0][3] ?> Embalses</b></p>


            <p style="position: absolute; top: 105px;
        text-align: left; padding-left: 40px; font-size: 12px;">
            <div style="position: absolute; left: 20px; top: 2px; background-color: #58F558;
         border-radius: 5; height: 10px; width: 10px;"></div>Condición de Alivio <b> <?php echo $valores[0][4] ?> Embalses</b></p>


            <p style="position: absolute; top: 125px;
        text-align: left; padding-left: 40px; font-size: 12px;">
            <div style="position: absolute; left: 20px; top: 2px; width: 0; height: 0;
        border-left: 5px solid transparent; border-right: 5px solid transparent; border-bottom: 10px solid black;"></div> EDC (Embalse de Compensación)</p>

    </div>
  </div>
  <!-- PAGINA 15 -->

  <?php
  $A_operador = 120;
  $A_tabla = 100;
  $incremento = 0;
  $acumulado = 0;
  $disponible = 25;
  $disponible_right = 25;
  $inicial = true;
  $tituloini = true;
  $right = false;
  $margin_left = 550;
  $espacio = 90;

  function descripcion($meses)
  {
    $meses = intval($meses);
    // if ($meses < 1) return "0 meses";
    if ($meses <= 4) return ["0-4 meses", "#ff0000"];
    if ($meses > 4 && $meses <= 8) return ["5-8 meses", "#ffaa00"];
    if ($meses > 8 && $meses <= 12) return ["9-12 meses", "#ffff00"];
    if ($meses > 12) return ["+12 meses", "#70ad47"];
  }

  ?>

  <?php foreach ($regiones as $region) { ?>


    <div style="page-break-before: always;"></div>
    <div class="header">
      <hr style="top: 55px; color:#1B569D">
      <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
      <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div>

    <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>GARANTÍA DE ABASTECIMIENTO DE LOS EMBALSES</b>
    </div>

    <div style="width: 500px; height: 280px; background-color: lightgray; margin-top: 20px; margin-left: 10px;"><?php echo mb_convert_case(date('d', strtotime($fecha1)) . ' DE ' . $meses[date('n', strtotime($fecha1))], MB_CASE_UPPER, 'UTF-8'); ?>
    </div>

    <div style="position: absolute; margin-top: <?php echo $A_tabla + $incremento; ?>px; margin-left: <?php echo $margin_left; ?>px; font-size: 18px; text-align: right;"><b>Región <?php echo $region ?></b>
      <table>
        <tr>
          <th class="celd-table">ALERTA</th>
          <th class="celd-table">DESCRIPCION</th>
          <th class="celd-table">EMBALSE</th>
          <th class="celd-table">MESES DE <br>ABAST.</th>
          <th class="celd-table-2">HIDROLÓGICA</th>
        </tr>
        <?php

        usort($embalse_abast, function ($a, $b) {
          return $a[3] <=> $b[3]; // Comparar las cadenas en el índice 2 (tipo)
        });

        foreach ($embalse_abast as $abast) {
          if (strtolower(trim($abast[0])) == strtolower(trim($region))) {
        ?>
            <tr>
              <td class="" style="font-size: 12px;">
                <div style="background-color:<?php echo descripcion($abast[3])[1] ?>; border-radius: 5; height: 10px; width: 10px; border: 0.5px solid black;"></div>
              </td>
              <td class="" style="font-size: 12px;"><?php echo descripcion($abast[3])[0] ?></td>
              <td class="" style="font-size: 12px;"><?php echo $abast[2] ?></td>
              <td class="" style="font-size: 12px;"><?php echo intval($abast[3]) ?></td>
              <td class="" style="font-size: 12px;"><?php echo $abast[1] ?></td>
            </tr>
        <?php }
        } ?>
      </table>


      <!-- <div style="position: absolute; margin-left: 20px; font-size: 18px; text-align: right;">
        <div><b>OPERADOR</b>
          <table>
            <tr>
              <th class="celd-table">SIMBOLO</th>
              <th class="celd-table">DESCRIPCION</th>
              <th class="celd-table">EMBALSE</th>
              <th class="celd-table">MESES DE <br>ABAST.</th>
              <th class="celd-table">HIDROLÓGICA</th>
            </tr>
            <tr>
              <td class="" style="font-size: 12px;">
                <div style="background-color: orange; border-radius: 5; height: 10px; width: 10px; border: 0.5px solid black;"></div>
              </td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
            </tr>
            <tr>
              <td class="" style="font-size: 12px;">
                <div style="background-color: yellow; border-radius: 5; height: 10px; width: 10px; border: 0.5px solid black;"></div>
              </td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
            </tr>
            <tr>
              <td class="" style="font-size: 12px;">
                <div style="background-color: #88FE31; border-radius: 5; height: 10px; width: 10px; border: 0.5px solid black;"></div>
              </td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
            </tr>
            <tr>
              <td class="" style="font-size: 12px;">
                <div style="background-color: green; border-radius: 5; height: 10px; width: 10px; border: 0.5px solid black;"></div>
              </td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
              <td class="" style="font-size: 12px;">PRUEBA</td>
            </tr>
          </table>
        </div>
      </div> -->
    </div>

  <?php } ?>
  <!-- PAGINA 16 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>GARANTÍA DE ABASTECIMIENTO DE LOS EMBALSES</b>
  </div>

  <div style="position: absolute; margin-top: 80px; margin-left: 55px; width: 95%; height: 100px;">
    <div style="position: absolute; margin-top: 30px;">
      <table>
        <tr>
          <th style="height: 80px;" class="text-celd" rowspan="2">HIDROLÓGICA</th>
          <th class="text-celd" colspan="4">ALERTAS</th>
          <th class="text-celd" rowspan="2">TOTAL</th>
          <th class="text-celd" rowspan="2">% TOTAL</th>

        </tr>
        <tr>
          <td class="text-celdas" style="font-size: 12px; width: 90px;">
            <div style="background-color: red; border-radius: 5; height: 10px; width: 10px; border: 0.5px solid black;"></div>
          </td>
          <td class="text-celdas" style="font-size: 12px; width: 90px;">
            <div style="background-color: orange; border-radius: 5; height: 10px; width: 10px; border: 0.5px solid black;"></div>
          </td>
          <td class="text-celdas" style="font-size: 12px; width: 90px;">
            <div style="background-color: yellow; border-radius: 5; height: 10px; width: 10px; border: 0.5px solid black;"></div>
          </td>
          <td class="text-celdas" style="font-size: 12px; width: 90px;">
            <div style="background-color: #70ad47; border-radius: 5; height: 10px; width: 10px; border: 0.5px solid black;"></div>
          </td>
        </tr>
        <?php

        ksort($operador_abast);

        foreach ($operador_abast as $key => $value) {
        ?>
          <tr>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo $key; ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo $value[0] . "/" . $value[4]; ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo $value[1] . "/" . $value[4]; ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo $value[2] . "/" . $value[4]; ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo $value[3] . "/" . $value[4]; ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo $value[4] . "/" . $value[4]; ?></td>
            <td class="text-celdas" style="font-size: 12px; width: 90px;"><?php echo $value[4] != 0 ? ($value[4] * 100) / $value[4] . "%" : 0 . "%"; ?></td>
          </tr>

        <?php } ?>
        <tr>
          <td class="total" style="font-size: 12px; height: 20px;"><b>TOTAL</b></td>
          <td class="total" style="font-size: 12px;"><b><?php echo $t_op_a[0] ?></b></td>
          <td class="total" style="font-size: 12px;"><b><?php echo $t_op_a[1] ?></b></td>
          <td class="total" style="font-size: 12px;"><b><?php echo $t_op_a[2] ?></b></td>
          <td class="total" style="font-size: 12px;"><b><?php echo $t_op_a[3] ?></b></td>
          <td class="total" style="font-size: 12px;"><b><?php echo $t_op_a[4] . "/" . $t_op_a[4] ?></b></td>
          <td class="total" style="font-size: 12px;"><b><?php echo $value[4] != 0 ? ($t_op_a[4] * 100) / $t_op_a[4] . "%" : 0 . "%" ?></b></td>

        </tr>
      </table>

    </div>
  </div>

  <!-- PAGINA 17 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 55px; margin-left: 5px;"><b>GARANTÍA DE ABASTECIMIENTO DE LOS EMBALSES</b>
  </div>

  <div style="position: absolute; margin-top: 150px; margin-left: 280px; width: 95%; height: 100px;">

    <div style="position: absolute; text-align: center;">
      <img style="width: 450px; height: 520px; background-color: lightgray; margin-top: 0px; margin-left: 50px;" src="<?php echo $status_pie_2 ?>">
    </div>

  </div>

  <!-- PAGINA 17 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 55px; margin-left: 5px;"><b>GARANTÍA DE ABASTECIMIENTO DE LOS EMBALSES</b>
  </div>

  <div style="position: absolute; margin-left: 55px; font-size: 18px; text-align: center; margin-top: 100px;">
    <h3>0 Meses < Alerta <span style="color:red;">Roja</span>
        < 4 Meses</h3>

          <table>
            <tr>
              <th class="text-celd">EMBALSE</th>
              <th class="" style="width:100px; font-size: 16px;">MESES <br> DE <br> GARANTÍA</th>
              <th class="text-celd">HIDROLÓGICA</th>
            </tr>
            <?php
            $cant = 0;
            foreach ($embalse_abast as $value) {
              if (($value[3]) <= 4) {
                $cant++;
            ?>
                <tr>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $value[2]; ?></td>
                  <td class="" style="font-size: 12px;"><?php echo intval($value[3]); ?></td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $value[1]; ?></td>
                </tr>
            <?php }
            } ?>
            <tr>
              <td class="height: 20px; text-celd total" style="font-size: 12px;"><b>TOTAL</b></td>
              <td class="" style="font-size: 12px;"><b><?php echo intval($cant) ?></b></td>
              <td class="" style="font-size: 12px;" rowspan="2"><b></b></td>

            </tr>
            <tr>
              <td class="text-celd total" style="font-size: 12px;"><b>%</b></td>
              <td class="" style="font-size: 12px;"><b> <?php echo number_format((($cant * 100) / count($embalse_abast)), "2", ",", ".") . "%" ?></b></td>
            </tr>
          </table>
  </div>

  <div style="position: absolute; margin-left: 550px; font-size: 18px; text-align: center; margin-top: 100px;">
    <h3>5 Meses < Alerta <span style="color: orange;">Naranja</span>
        < 8 Meses</h3>

          <table>
            <tr>
              <th class="text-celd">EMBALSE</th>
              <th class="" style="width:100px; font-size: 16px;">MESES <br> DE <br> GARANTÍA</th>
              <th class="text-celd">HIDROLÓGICA</th>
            </tr>
            <?php
            $cant = 0;
            foreach ($embalse_abast as $value) {
              if (($value[3]) > 4 && ($value[3]) <= 8) {
                $cant++;
            ?>
                <tr>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $value[2]; ?></td>
                  <td class="" style="font-size: 12px;"><?php echo intval($value[3]); ?></td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $value[1]; ?></td>
                </tr>
            <?php }
            } ?>
            <tr>
              <td class="height: 20px; text-celd total" style="font-size: 12px;"><b>TOTAL</b></td>
              <td class="" style="font-size: 12px;"><b><?php echo intval($cant) ?></b></td>
              <td class="" style="font-size: 12px;" rowspan="2"><b></b></td>

            </tr>
            <tr>
              <td class="text-celd total" style="font-size: 12px;"><b>%</b></td>
              <td class="" style="font-size: 12px;"><b> <?php echo number_format((($cant * 100) / count($embalse_abast)), "2", ",", ".") . "%" ?></b></td>
            </tr>
          </table>
  </div>

  <!-- PAGINA 18 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 55px; margin-left: 5px;"><b>GARANTÍA DE ABASTECIMIENTO DE LOS EMBALSES</b>
  </div>

  <div style="position: absolute; margin-left: 55px; font-size: 18px; text-align: center; margin-top: 100px;">
    <h3>9 Meses < Alerta <span style="color:#ffc219;">Amarilla</span>
        < 12 Meses</h3>

          <table>
            <tr>
              <th class="text-celd">EMBALSE</th>
              <th class="" style="width:100px; font-size: 16px;">MESES <br> DE <br> GARANTÍA</th>
              <th class="text-celd">HIDROLÓGICA</th>
            </tr>
            <?php
            $cant = 0;
            foreach ($embalse_abast as $value) {
              if (($value[3]) >= 8 && ($value[3]) < 12) {
                $cant++;
            ?>
                <tr>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $value[2]; ?></td>
                  <td class="" style="font-size: 12px;"><?php echo intval($value[3]); ?></td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $value[1]; ?></td>
                </tr>
            <?php }
            } ?>
            <tr>
              <td class="height: 20px; text-celd total" style="font-size: 12px;"><b>TOTAL</b></td>
              <td class="" style="font-size: 12px;"><b><?php echo intval($cant) ?></b></td>
              <td class="" style="font-size: 12px;" rowspan="2"><b></b></td>

            </tr>
            <tr>
              <td class="text-celd total" style="font-size: 12px;"><b>%</b></td>
              <td class="" style="font-size: 12px;"><b> <?php echo number_format((($cant * 100) / count($embalse_abast)), "2", ",", ".") . "%" ?></b></td>
            </tr>
          </table>
  </div>

  <div style="position: absolute; margin-left: 550px; font-size: 18px; text-align: center; margin-top: 100px;">
    <h3>Alerta <span style="color: green;">Verde</span>
      > 12 Meses</h3>

    <table>
      <tr>
        <th class="text-celd">EMBALSE</th>
        <th class="" style="width:100px; font-size: 16px;">MESES <br> DE <br> GARANTÍA</th>
        <th class="text-celd">HIDROLÓGICA</th>
      </tr>
      <?php
      $cant = 0;
      foreach ($embalse_abast as $value) {
        if (($value[3]) >= 12) {
          $cant++;
      ?>
          <tr>
            <td class="text-celd" style="font-size: 12px;"><?php echo $value[2]; ?></td>
            <td class="" style="font-size: 12px;"><?php echo intval($value[3]); ?></td>
            <td class="text-celd" style="font-size: 12px;"><?php echo $value[1]; ?></td>
          </tr>
      <?php }
      } ?>
      <tr>
        <td class="height: 20px; text-celd total" style="font-size: 12px;"><b>TOTAL</b></td>
        <td class="" style="font-size: 12px;"><b><?php echo intval($cant) ?></b></td>
        <td class="" style="font-size: 12px;" rowspan="2"><b></b></td>

      </tr>
      <tr>
        <td class="text-celd total" style="font-size: 12px;"><b>%</b></td>
        <td class="" style="font-size: 12px;"><b> <?php echo ($cant * 100) / count($embalse_abast) . "%" ?></b></td>
      </tr>
    </table>
  </div>




</body>

</html>
<?php closeConection($conn); ?>