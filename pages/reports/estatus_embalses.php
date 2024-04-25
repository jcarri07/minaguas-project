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
           WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND id_embalse = d.id_embalse) AND cota_actual <> 0) AND (ce.id_tipo_codigo_extraccion = '1' OR ce.id_tipo_codigo_extraccion = '2' OR ce.id_tipo_codigo_extraccion = '3' OR ce.id_tipo_codigo_extraccion = '4')
      ) AS 'extraccion',
      e.nombre_embalse, (SELECT cota_actual 
           FROM datos_embalse h 
           WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual
      FROM embalses e
LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
WHERE e.estatus = 'activo'
GROUP BY id_embalse 
ORDER BY id_embalse ASC;");

$condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse,operador,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha1' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
FROM datos_embalse h 
WHERE h.id_embalse = e.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.cota_actual <> 0 AND da.fecha <= '$fecha1') AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha <= '$fecha1' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual 
FROM embalses e
LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha1'
WHERE e.estatus = 'activo' 
GROUP BY id_embalse;");

$condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse,operador,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha2' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
FROM datos_embalse h 
WHERE h.id_embalse = e.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.cota_actual <> 0 AND da.fecha <= '$fecha2') AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha <= '$fecha2' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual 
FROM embalses e
LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha2'
WHERE e.estatus = 'activo'
GROUP BY id_embalse;");

$condiciones_actuales3 = mysqli_query($conn, "SELECT e.id_embalse,operador,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha3' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
FROM datos_embalse h 
WHERE h.id_embalse = e.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.cota_actual <> 0 AND da.fecha <= '$fecha3') AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha <= '$fecha3' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual 
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
  $porcentaje = $inicial != 0 ? (100 * (($final - $inicial) / abs($inicial))) : 0;

  $anio2 = date("Y", strtotime($row2['fecha']));
  $final2 = $bat->volumenActualDisponible();
  $inicial2 = $bat->volumenDisponibleByCota($anio2, $row2["cota_actual"]);
  $variacion2 = $final2 - $inicial2;
  $porcentaje2 = $inicial2 != 0 ? (100 * (($final2 - $inicial2) / abs($inicial2))) : 0;

  $anio3 = date("Y", strtotime($row3['fecha']));
  $final3 = $bat->volumenActualDisponible();
  $inicial3 = $bat->volumenDisponibleByCota($anio3, $row3["cota_actual"]);
  $variacion3 = $final3 - $inicial3;
  $porcentaje3 = $inicial3 != 0 ? (100 * (($final3 - $inicial3) / abs($inicial3))) : 0;


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

// var_dump($embalses_variacion);


$condiciones = [];
$CT = [0, 0, 0, 0, 0, 0];

$queryEmbalses = mysqli_query($conn, "SELECT id_embalse, nombre_embalse, norte, este, huso, operador FROM embalses WHERE estatus = 'activo';");

while ($row = mysqli_fetch_array($queryEmbalses)) {

  $bat = new Batimetria($row["id_embalse"], $conn);
  $porcentaje = ($bat->volumenActualDisponible() * 100) / $bat->volumenDisponible();

  if ($porcentaje < 30) {
    agregarACondiciones($row["operador"], $condiciones, 1);
    $CT[0] += 1;
    $CT[1] += 1;
  } else if ($porcentaje >= 30 && $porcentaje < 60) {
    agregarACondiciones($row["operador"], $condiciones, 2);
    $CT[0] += 1;
    $CT[2] += 1;
  } else if ($porcentaje >= 60 && $porcentaje < 90) {
    agregarACondiciones($row["operador"], $condiciones, 3);
    $CT[0] += 1;
    $CT[3] += 1;
  } else if ($porcentaje >= 90 && $porcentaje <= 100) {
    agregarACondiciones($row["operador"], $condiciones, 4);
    $CT[0] += 1;
    $CT[4] += 1;
  } else {
    agregarACondiciones($row["operador"], $condiciones, 5);
    $CT[0] += 1;
    $CT[5] += 1;
  }
}

$embalse_abast = [];
$operador_abast = [];
$t_op_a = [0, 0, 0, 0, 0];
$regiones = [];
$countReg = [];

$row = 0;

while ($row < count($datos_embalses)) {
  $emb = new Batimetria($datos_embalses[$row]["id_embalse"], $conn);

  $abastecimiento = 0;
  if ($datos_embalses[$row]["extraccion"] > 0) {
    $abastecimiento = (($emb->volumenActualDisponible() * 1000) / $datos_embalses[$row]["extraccion"]) / 30;
  }

  if (!in_array($totalreg[$datos_embalses[$row]["region"]], $regiones)) {
    array_push($regiones, $totalreg[$datos_embalses[$row]["region"]]);
    $countReg[$totalreg[$datos_embalses[$row]["region"]]] = 1;
  } else {
    $countReg[$totalreg[$datos_embalses[$row]["region"]]] += 1;
  }

  if (array_key_exists($totalop[$datos_embalses[$row]["operador"]], $operador_abast)) {
    if (intval($abastecimiento) < 5) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][0] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[0] += 1;
      $t_op_a[4] += 1;
    }
    if (intval($abastecimiento) > 4 && intval($abastecimiento) < 9) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][1] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[1] += 1;
      $t_op_a[4] += 1;
    }
    if (intval($abastecimiento) > 8 && intval($abastecimiento) < 13) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][2] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[2] += 1;
      $t_op_a[4] += 1;
    }
    if (intval($abastecimiento) > 12) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][3] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[3] += 1;
      $t_op_a[4] += 1;
    }
  } else {
    $operador_abast[$totalop[$datos_embalses[$row]["operador"]]] = [0, 0, 0, 0, 0];

    if (intval($abastecimiento) < 5) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][0] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[0] += 1;
      $t_op_a[4] += 1;
    }
    if (intval($abastecimiento) > 4 && intval($abastecimiento) < 9) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][1] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[1] += 1;
      $t_op_a[4] += 1;
    }
    if (intval($abastecimiento) > 8 && intval($abastecimiento) < 13) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][2] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[2] += 1;
      $t_op_a[4] += 1;
    }
    if (intval($abastecimiento) > 12) {
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][3] += 1;
      $operador_abast[$totalop[$datos_embalses[$row]["operador"]]][4] += 1;
      $t_op_a[3] += 1;
      $t_op_a[4] += 1;
    }
  }

  $array = [$totalreg[$datos_embalses[$row]["region"]], $totalop[$datos_embalses[$row]["operador"]], $datos_embalses[$row]["nombre_embalse"], $abastecimiento];
  array_push($embalse_abast, $array);
  $row++;
}

function agregarACondiciones($operador, &$array, $porcentaje)
{
  if (array_key_exists($operador, $array)) {
    $array[$operador][0] += 1;
    $array[$operador][$porcentaje] += 1;
    return;
  } else {
    $array[$operador] = [0, 0, 0, 0, 0, 0];
    agregarACondiciones($operador, $array, $porcentaje);
  }
}

function getMonthName()
{
  $fecha_actual = getdate();

  $numero_mes = $fecha_actual['mon'];

  $nombres_meses = array(
    1 => "ENERO", 2 => "FEBRERO", 3 => "MARZO", 4 => "ABRIL",
    5 => "MAYO", 6 => "JUNIO", 7 => "JULIO", 8 => "AGOSTO",
    9 => "SEPTIEMBRE", 10 => "OCTUBRE", 11 => "NOVIEMBRE", 12 => "DICIEMBRE"
  );

  $nombre_mes = $nombres_meses[$numero_mes];

  return  $nombre_mes;
}

function getShortName()
{
  $fecha_actual = getdate();

  $numero_mes = $fecha_actual['mon'];

  $nombres_meses_abreviados = array(
    1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr",
    5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Ago",
    9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic"
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

  


</body>

</html>
<?php closeConection($conn); ?>