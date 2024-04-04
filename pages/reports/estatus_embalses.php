<?php
require_once '../../php/Conexion.php';
$fullPath = getcwd();
$parts = explode(DIRECTORY_SEPARATOR, $fullPath);
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
$almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,operador,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND h.hora = (select MAX(hora) FROM datos_embalse                                                                                                                                                            WHERE fecha = MAX(d.fecha) AND id_embalse = d.id_embalse) ORDER BY cota_actual DESC LIMIT 1) AS cota_actual 
 FROM embalses e
 LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
 WHERE e.estatus = 'activo' 
 GROUP BY id_embalse;");

$condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,operador,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha1' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha1' AND h.hora = (select MAX(hora) FROM datos_embalse                                                                                                                                                            WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha1' AND id_embalse = d.id_embalse) ORDER BY cota_actual DESC LIMIT 1) AS cota_actual 
 FROM embalses e
 LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha1'
 WHERE e.estatus = 'activo' 
 GROUP BY id_embalse;");

$condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,operador,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha2' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha2' AND h.hora = (select MAX(hora) FROM datos_embalse                                                                                                                                                            WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha2' AND id_embalse = d.id_embalse) ORDER BY cota_actual DESC LIMIT 1) AS cota_actual 
 FROM embalses e
 LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha2'
 WHERE e.estatus = 'activo' 
 GROUP BY id_embalse;");

$hidro = mysqli_query($conn, "SELECT COUNT(e.id_embalse),e.operador
                FROM embalses e
                WHERE e.estatus = 'activo'
                GROUP BY e.operador
                ORDER BY id_embalse ASC;");

$datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);
$volumen_primer_periodo = mysqli_fetch_all($condiciones_actuales1, MYSQLI_ASSOC);
$volumen_segundo_periodo = mysqli_fetch_all($condiciones_actuales2, MYSQLI_ASSOC);

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

$image_logo = "https://embalsesminaguas.000webhostapp.com/assets/img/logos/cropped-mminaguas.jpg";
$logo_letters =  "https://embalsesminaguas.000webhostapp.com/assets/img/logos/MinaguasLetters.png";
$area =  "https://embalsesminaguas.000webhostapp.com/pages/reports_images/Area_cuenca.png";
$logo_combinado = "../../assets/img/logos/logo_combinado.jpg";
$mapa = "../../assets/img/temp/imagen-estatus-mapa-1.png";


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

</head>

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
    text-align: center;
    padding: 5px;
    border: 1px solid #707273;
    /*width: fit-content;*/
    font-size: 10px;
  }

  th {
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

<body>

  <!-- PAGINA 1 -->
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div>
    <h1 style="position: absolute; top: 70px; left: 50px; text-align:center; color:#2E86C1; font-size: 23px;">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    <h2 style="position: absolute; top: 100px; text-align: center; text-justify: center; color:#021568">Estatus de Fuentes Hídricas para Consumo Humano</h2>
    <div style="width: 1000px; height: 535px; background-color: lightgray; margin: 10px, 0, 0, 35px;">
      <!-- Mapa --> <img style="width:1000px ; height: 535px;" src="<?php echo $mapa ?>" />
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

    <h4 style="position: absolute; top: 720px; text-align: center; text-justify: center;"><?php echo "$dia_actual DE " . getMonthName() . " $año_actual" ?></h4>
  </div>

  <!-- PAGINA 2 -->

  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="position: absolute; top: 70px; left: 20px; font-size: 18px; color:#000000;"><b>Bajo (< 30 %)</b>

        <table>
          <tr>
            <th class="text-celd">EMBALSE</th>
            <th class="text-celd">VOL. DISP. (HM3)</th>
            <th class="text-celd">HIDROLÓGICA</th>
          </tr>

          <?php
          $j = 0;
          $cuenta = 0;
          while ($j < count($datos_embalses)) {
            if ($datos_embalses[$j]["cota_actual"] != NULL) {
              $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
              $batimetria = $bati->getBatimetria();
              $x = $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];

              $min = $bati->volumenMinimo();
              $max = $bati->volumenMaximo();
              $nor = $bati->volumenNormal();
              if (((abs(($x - $min)) * (100 / ($max - $min))) >= 0 && (abs(($x - $min)) * (100 / ($max - $min))) < 30)) {
                $cuenta++; ?>
                <tr>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]["nombre_embalse"]; ?> </td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo round($x, 2) ?></td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]['operador']; ?> </td>
                </tr>

              <?php }
            } else {
              $cuenta++; ?>
              <tr>
                <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]["nombre_embalse"]; ?> </td>
                <td class="text-celd" style="font-size: 12px;"><?php echo 0 ?></td>
                <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]['operador']; ?> </td>
              </tr>
          <?php }
            $j++;
          }
          ?>

          <tr>
            <td class="text-celd total"><b> TOTAL </b></td>
            <td class="text-celd total" colspan="2"><b></b> <?php echo $cuenta . " "; ?>Embalses<?php echo " (" . ($cuenta * 100 / count($datos_embalses)) . "%)" ?></td>
          </tr>
        </table>


        <div style="font-size: 18px; color:#000000;  margin-top: 40px;"><b>Normal Bajo (30 % < A < 60%)</b>
        </div>

        <table>
          <tr>
            <th class="text-celd">EMBALSE</th>
            <th class="text-celd">VOL. DISP. (HM3)</th>
            <th class="text-celd">HIDROLÓGICA</th>
          </tr>
          <?php
          $j = 0;
          $cuenta = 0;
          while ($j < count($datos_embalses)) {
            if ($datos_embalses[$j]["cota_actual"] != NULL) {
              $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
              $batimetria = $bati->getBatimetria();
              $x = $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];
              $min = $bati->volumenMinimo();
              $max = $bati->volumenMaximo();
              $nor = $bati->volumenNormal();
              if ((abs(($x - $min)) * (100 / ($max - $min))) >= 30 && (abs(($x - $min)) * (100 / ($max - $min))) < 60) {
                $cuenta++; ?>

                <tr>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]["nombre_embalse"]; ?> </td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo round($x, 2) ?></td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]['operador']; ?> </td>
                </tr>

          <?php }
            }
            $j++;
          }
          ?>
          <tr>
            <td class="text-celd total"><b> TOTAL </b></td>
            <td class="text-celd total" colspan="2"><b></b> <?php echo $cuenta . " "; ?>Embalses<?php echo " (" . ($cuenta * 100 / count($datos_embalses)) . "%)" ?></td>
          </tr>
        </table>

        <!-- <div class="box-note"> Nota:</div> -->

  </div>



  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 560px;"><b>Normal Alto (60 % < A < 90 %) </b>

        <table>
          <tr>
            <th class="text-celd">EMBALSE</th>
            <th class="text-celd">VOL. DISP. (HM3)</th>
            <th class="text-celd">HIDROLÓGICA</th>
          </tr>
          <?php
          $j = 0;
          $cuenta = 0;
          while ($j < count($datos_embalses)) {
            if ($datos_embalses[$j]["cota_actual"] != NULL) {
              $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
              $batimetria = $bati->getBatimetria();
              $x = $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];
              $min = $bati->volumenMinimo();
              $max = $bati->volumenMaximo();
              $nor = $bati->volumenNormal();
              if ((abs(($x - $min)) * (100 / ($max - $min))) >= 60 && (abs(($x - $min)) * (100 / ($max - $min))) < 90) {
                $cuenta++; ?>

                <tr>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]["nombre_embalse"]; ?> </td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo round($x, 2) ?></td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]['operador']; ?> </td>
                </tr>

          <?php }
            }
            $j++;
          }
          ?>
          <tr>
            <td class="text-celd total"><b> TOTAL </b></td>
            <td class="text-celd total" colspan="2"><b></b> <?php echo $cuenta . " "; ?>Embalses<?php echo " (" . ($cuenta * 100 / count($datos_embalses)) . "%)" ?></td>
          </tr>
        </table>

  </div>


  <!-- PAGINA 3 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 20px;"><b>Buena ( 90 % < A < 100 %) </b>

        <table>
          <tr>
            <th class="text-celd">EMBALSE</th>
            <th class="text-celd">VOL. DISP. (HM3)</th>
            <th class="text-celd">HIDROLÓGICA</th>
          </tr>
          <?php
          $j = 0;
          $cuenta = 0;
          while ($j < count($datos_embalses)) {
            if ($datos_embalses[$j]["cota_actual"] != NULL) {
              $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
              $batimetria = $bati->getBatimetria();
              $x = $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];
              $min = $bati->volumenMinimo();
              $max = $bati->volumenMaximo();
              $nor = $bati->volumenNormal();
              if ((abs(($x - $min)) * (100 / ($max - $min))) >= 90 && (abs(($x - $min)) * (100 / ($max - $min))) <= 100) {
                $cuenta++; ?>

                <tr>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]["nombre_embalse"]; ?> </td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo round($x, 2) ?></td>
                  <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]['operador']; ?> </td>
                </tr>

          <?php }
            }
            $j++;
          }
          ?>
          <tr>
            <td class="text-celd total"><b> TOTAL </b></td>
            <td class="text-celd total" colspan="2"><b></b> <?php echo $cuenta . " "; ?>Embalses<?php echo " (" . ($cuenta * 100 / count($datos_embalses)) . "%)" ?></td>
          </tr>
        </table>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 560px;"><b>Condición de Alivio </b>

    <table>
      <tr>
        <th class="text-celd">EMBALSE</th>
        <th class="text-celd">VOL. DISP. (HM3)</th>
        <th class="text-celd">HIDROLÓGICA</th>
      </tr>
      <?php
      $j = 0;
      $cuenta = 0;
      while ($j < count($datos_embalses)) {
        if ($datos_embalses[$j]["cota_actual"] != NULL) {
          $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
          $batimetria = $bati->getBatimetria();
          $x = $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];
          $min = $bati->volumenMinimo();
          $max = $bati->volumenMaximo();
          $nor = $bati->volumenNormal();
          if ((abs(($x - $min)) * (100 / ($max - $min))) > 100) {
            $cuenta++; ?>

            <tr>
              <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]["nombre_embalse"]; ?> </td>
              <td class="text-celd" style="font-size: 12px;"><?php echo round($x, 2) ?></td>
              <td class="text-celd" style="font-size: 12px;"><?php echo $datos_embalses[$j]['operador']; ?> </td>
            </tr>

      <?php }
        }
        $j++;
      }
      ?>
      <tr>
        <td class="text-celd total"><b> TOTAL </b></td>
        <td class="text-celd total" colspan="2"><b></b> <?php echo $cuenta . " "; ?>Embalses<?php echo " (" . ($cuenta * 100 / count($datos_embalses)) . "%)" ?></td>
      </tr>
    </table>
  </div>

  <!-- PAGINA 4 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>
  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 70px; margin-left: 5px;"><b>CONDICIONES ACTUALES DE ALMACENAMIENTO DE EMBALSES POR HIDROLÓGICA </b>

    <table style="margin-top: 20px;">
      <tr>
        <th class="tablaDos" rowspan="2">HIDROLÓGICA</th>
        <th class="tablaDos">BAJA</th>
        <th class="tablaDos" rowspan="2">% TOTAL</th>
        <th class="tablaDos">NORMAL- <br> BAJO</th>
        <th class="tablaDos" rowspan="2">% TOTAL</th>
        <th class="tablaDos">NORMAL- <br> ALTO</th>
        <th class="tablaDos" rowspan="2">% TOTAL</th>
      </tr>

      <tr>
        <th class="tablaDos">
          < 30 %</th>
        <th class="tablaDos">30% < A < 60% </th>
        <th class="tablaDos">60% < A < 90% </th>
      </tr>
      <?php foreach ($condiciones as $key => $values) { ?>
        <tr>
          <td class="tablaDos" style="font-size: 12px;"><?php echo $key ?></td>
          <td class="tablaDos" style="font-size: 12px;"><?php echo $values[1] . "/" . $values[0] ?></td>
          <td class="tablaDos" style="font-size: 12px;"><?php echo $values[0] != 0 ? ($values[1] * 100) / $values[0] : 0 ?>%</td>
          <td class="tablaDos" style="font-size: 12px;"><?php echo $values[2] . "/" . $values[0] ?></td>
          <td class="tablaDos" style="font-size: 12px;"><?php echo $values[0] != 0 ? ($values[2] * 100) / $values[0] : 0 ?>%</td>
          <td class="tablaDos" style="font-size: 12px;"><?php echo $values[3] . "/" . $values[0] ?></td>
          <td class="tablaDos" style="font-size: 12px;"><?php echo $values[0] != 0 ? ($values[3] * 100) / $values[0] : 0 ?>%</td>
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
        <th class="tablaDos" colspan="2"><b></b> NORMAL-BAJO</th>
      </tr>


      <tr>
        <td class="text-celdas total" style="font-size: 12px;"><b>TOTAL</b></td>
        <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[1]."/".$CT[0] ?></b></td>
        <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[2]."/".$CT[0] ?></b></td>
        <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[3]."/".$CT[0] ?></b></td>
      </tr>

      <tr>
        <td class="text-celdas total" style="font-size: 12px;"><b>%</b></td>
        <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[0] != 0 ? ($CT[1]*100)/$CT[0] : 0 ?>%</b></td>
        <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[0] != 0 ? ($CT[2]*100)/$CT[0] : 0 ?>%</b></td>
        <td class="tablaDos" style="font-size: 12px;" colspan="2"><b><?php echo $CT[0] != 0 ? ($CT[3]*100)/$CT[0] : 0 ?>%</b></td>
      </tr>

    </table>

    <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 39px; margin-left: 620px;"><b> </b>

      <table>
        <tr>
          <th style="height: 38px;" class="tablaDos" rowspan="2">HIDROLÓGICA</th>
          <th class="tablaDos">BUENA</th>
          <th class="tablaDos">ALIVIANDO</th>
          <th class="tablaDos" rowspan="2"> TOTAL</th>
          <th class="tablaDos" rowspan="2">% TOTAL</th>
        </tr>

        <tr>
          <th class="tablaDos">90% < B < 100% </th>
          <th class="tablaDos">
            < 100 %</th>
        </tr>

        <?php foreach ($condiciones as $key => $values) { ?>
          <tr>
            <td class="tablaDos" style="font-size: 12px;"><?php echo $key ?></td>
            <td class="tablaDos" style="font-size: 12px;"><?php echo $values[4] . "/" . $values[0] ?></td>
            <td class="tablaDos" style="font-size: 12px;"><?php echo $values[5] . "/" . $values[0] ?></td>
            <td class="tablaDos" style="font-size: 12px;"><?php echo ($values[4] + $values[5]) . "/" . $values[0] ?></td>
            <td class="tablaDos" style="font-size: 12px;"><?php echo $values[0] != 0 ? (($values[4] + $values[5]) * 100) / $values[0] : 0 ?>%</td>
          </tr>
        <?php
        } ?>

        <tr>
          <th class="spazio" colspan="7"></th>
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
          <td class="tablaDos" style="font-size: 12px;"><b><?php echo $CT[4]."/".$CT[0] ?></b></td>
          <td class="tablaDos" style="font-size: 12px;"><b><?php echo $CT[5]."/".$CT[0] ?></b></td>
          <td class="tablaDos" style="font-size: 12px;"><b><?php echo ($CT[4]+$CT[5])."/".$CT[0] ?></b></td>
          <td class="tablaDos" style="font-size: 12px;"><b><?php echo $CT[0] != 0 ? (($CT[4]+$CT[5])*100)/$CT[0] : 0 ?>%</b></td>
        </tr>


      </table>
    </div>

  </div>

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

  <img style="width: 550px; height: 450px; background-color: lightgray; margin-top: 50px; margin-left: 35px;" src="<?php echo "../../assets/img/temp/imagen-estatus-pie-1.png" ?>">


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

  <img style="width: 450px; height: 450px; background-color: lightgray; margin-top: 80px; margin-left: 35px;" src="<?php echo "../../assets/img/temp/imagen-estatus-barra-1.png" ?>">


  <div style="position: absolute; width: 0.5px; height: 600px; background-color: #7F7F7F; margin-top: 110px; margin-left: 525px;"></div>

  <div style="font-size: 17px; color: #0070C0; position: absolute;  margin-top: 120px; margin-left: 550px;"><b>DESDE EL <?php echo date("d/m/Y", strtotime($fecha2)); ?> HASTA HOY</b>
  </div>

  <img style="width: 450px; height: 450px; background-color: lightgray; position: absolute; margin-top: 180px; margin-left: 570px;" src="<?php echo "../../assets/img/temp/imagen-estatus-barra-2.png" ?>">

  <div style="position: absolute; margin-top: 670px; margin-left: 50px; width: 95%; height: 100px;">
    <div style="position: absolute; font-size: 18px; color:red; text-align: center;"> <b> (Varió <?php
                                                                                                  if (abs(($volumenes[5] - $volumenes[2])) != 0) {
                                                                                                    echo round((abs($volumenes[1] - $volumenes[2]) - abs($volumenes[5] - $volumenes[2])) * 100 / abs($volumenes[5] - $volumenes[2]), 2);
                                                                                                  } else {
                                                                                                    echo 0;
                                                                                                  };

                                                                                                  ?>% comparado con la semana pasada y <br> <?php
                                                                                                                                            if (abs(($volumenes[4] - $volumenes[2])) != 0) {
                                                                                                                                              echo round((abs($volumenes[1] - $volumenes[2]) - abs($volumenes[4] - $volumenes[2])) * 100 / abs(($volumenes[4] - $volumenes[2])), 2);
                                                                                                                                            } else {
                                                                                                                                              echo 0;
                                                                                                                                            };

                                                                                                                                            ?>% con respecto a hace 15 días)</b></div>
    <div style="position: absolute; margin-left: 550px; font-size: 18px; color:red; text-align: center;"><b> (Varió <?php

                                                                                                                    if (abs(($volumenes[5] - $volumenes[3])) != 0) {
                                                                                                                      echo round((abs($volumenes[1] - $volumenes[3]) - abs($volumenes[5] - $volumenes[3])) * 100 / abs(($volumenes[5] - $volumenes[3])), 2);
                                                                                                                    } else {
                                                                                                                      echo 0;
                                                                                                                    };
                                                                                                                    ?>% comparado con la semana pasada y <br> <?php
                                                                                                                                                              if (abs(($volumenes[4] - $volumenes[3])) != 0) {
                                                                                                                                                                echo round((abs($volumenes[1] - $volumenes[3]) - abs($volumenes[4] - $volumenes[3])) * 100 / abs(($volumenes[4] - $volumenes[3])), 2);
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
      <img style="width:1000px ; height: 535px;" src="../../assets/img/temp/imagen-estatus-mapa-2.png" />
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
    <div style="position: absolute; left: 20px; top: 2px; background-color: green;
         border-radius: 5; height: 10px; width: 10px;"></div>Aumento de Volumen <b> <?php echo $valores[1][0] ?> Embalses</b></p>


    <p style="position: absolute; top: 85px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 20px; top: 2px; background-color: red;
         border-radius: 5; height: 10px; width: 10px;"></div>Disminución de Volumen <b> <?php echo $valores[1][1] ?> Embalses</b></p>


    <p style="position: absolute; top: 110px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 20px; top: 2px; background-color: black;
         border-radius: 5; height: 10px; width: 10px;"></div>Sin Cambioss <b> <?php echo $valores[1][2] ?> Embalses</b></p>

  </div>
  <?php setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp'); // Establecer la localización a español
  ?>

  <h4 style="position: absolute; top: 640px; text-align: right; text-justify: right;">DESDE EL <?php echo mb_convert_case(strftime('%d DE %B', strtotime($fecha1)), MB_CASE_UPPER, 'UTF-8'); ?> </h4>

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
      <img style="width:1000px ; height: 535px;" src="../../assets/img/temp/imagen-estatus-mapa-3.png" />
    </div>
  </div>

  <div style="position: absolute; height: 160px; width: 350px; left: 38px; top: 485px; border: gray 1px solid; background-color: #FFFFFF">
    <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
    <p style="position: absolute; top: 35px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 20px; top: 2px; background-color: blue;
         border-radius: 5; height: 10px; width: 10px;"></div> Embalses</p>


    <p style="position: absolute; top: 70px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 20px; top: 2px; background-color: green;
         border-radius: 5; height: 10px; width: 10px;"></div>Aumento de Volumen <b> <?php echo $valores[2][0] ?> Embalses</b></p>


    <p style="position: absolute; top: 105px;
        text-align: left; padding-left: 40px; font-size: 15px;">
    <div style="position: absolute; left: 20px; top: 2px; background-color: red;
         border-radius: 5; height: 10px; width: 10px;"></div>Disminución de Volumen <b> <?php echo $valores[2][1] ?> Embalses</b></p>

  </div>

  <h4 style="position: absolute; top: 640px;  right: 0; text-justify: right;"> DESDE EL <?php echo mb_convert_case(strftime('%d DE %B', strtotime($fecha2)), MB_CASE_UPPER, 'UTF-8'); ?></h4>

  <!-- PAGINA 9 -->

  <div style="page-break-before: always;"></div>
  <div class="header">
    <hr style="top: 55px; color:#1B569D">
    <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
    <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
  </div>

  <div style="font-size: 18px; color:#000000; position: absolute;  margin-top: 50px; margin-left: 500px;"><b>HIDROCAPITAL</b>
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
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VAR. VOL. <br>(HM3)</th>
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

    <div style="position: absolute; margin-left: 550px; font-size: 18px; text-align: right;"><b>FECHA</b>
      <table>
        <tr>
          <th class="text-celd">EMBALSE</th>
          <th class="text-celd">VAR. VOL. <br>(HM3)</th>
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
  <!-- PAGINA 10 -->

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

  <!-- PAGINA 11 -->

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
  <!-- PAGINA 12 -->

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
  <!-- PAGINA 11 -->

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
  </div>

</body>

</html>