<?php
require_once '../../php/Conexion.php';
$fullPath = getcwd();
$parts = explode(DIRECTORY_SEPARATOR, $fullPath);
date_default_timezone_set('America/Caracas');
require_once '../../php/batimetria.php';

if (count($parts) >= 4) {
  $projectName = $parts[3];
  echo "Nombre del proyecto: " . $projectName;
} else {
  echo "No se pudo obtener el nombre del proyecto desde la ruta.";
}

function getMonthName()
{
  $fecha_actual = getdate();

  $numero_mes = $fecha_actual['mon'];

  $nombres_meses = array(
    1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril",
    5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto",
    9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
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

$stringPrioritarios = "0";
$queryPrioritarios = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'prioritarios'");
if (mysqli_num_rows($queryPrioritarios) > 0) {
  $stringPrioritarios = mysqli_fetch_assoc($queryPrioritarios)['configuracion'];
}

$result = mysqli_query($conn, "SELECT * FROM embalses WHERE id_embalse IN ($stringPrioritarios)");

$mes_actual = date('m');

$num_rows = $result->num_rows;

$image_logo =  "/" . $projectName . "/assets/img/logos/cropped-mminaguas.jpg";
$logo_letters =  "/" . $projectName . "/assets/img/logos/MinaguasLetters.png";
$area =  "/" . $projectName . "/pages/reports_images/Area_cuenca.png";
$codigo = "08RHL0101";
$titulo = "EMBALSE CAMATAGUA - ESTADO ARAGUA";
$cota = 289.87;
$mes = "Noviembre";
$area_cuenta = 636.49;
$variacion_semanal = "VARIACION SEMANAL";
$fecha = "02";
$fecha2 = "08";
$variacion_mensual = getMonthName();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Embalses Priorizados</title>
</head>
<style>
  hr {
    top: 120px;
    background-color: #2E86C1;
    height: 2px;
    width: 80%;
    margin-bottom: 100px;
    border: none;
    position: absolute;
  }

  .square {
    top: 50px;
    position: absolute;
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
    top: 25px;
    height: 40px;
    left: 100px;
    position: absolute;
    color: #2E86C1;
    font-size: 20px;
    font-weight: bold;
  }

  .code-container {
    top: 30px;
    left: 75px;
    position: absolute;
    width: 500px;
  }

  .title-container {
    margin-left: 260px;
    width: 700px;
    color: #2E86C1;
  }

  .img-logo {
    position: absolute;
    top: 50px;
    float: right;
    width: 50px;
    margin-bottom: 50px;
  }

  .img-letters {
    position: absolute;
    top: 30px;
    float: right;
    right: px;
    width: 100px;
    background-color: red;
  }

  .container-letters {
    position: absolute;
    top: 15px;
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
    border: 1px solid #dddddd;
    width: fit-content;
  }

  th {
    text-align: center;
    background-color: #f2f2f2;
  }

  .text-celd {
    width: 60px;
    text-align: center;
  }
</style>

<body>
  <?php if ($num_rows > 0) {
    $indice = 0;
  ?>
    <?php while ($row = $result->fetch_assoc()) {
      $id = $row['id_embalse'];
      $sqlMonths = "SELECT cota_actual, fecha, id_embalse FROM datos_embalse WHERE MONTH(fecha) = $mes_actual AND DAY(fecha) BETWEEN 2 AND 8 AND id_embalse = '$id' GROUP BY (fecha);";
    ?>
      <div class="square">
        <?php echo $indice + 1; ?>
      </div>
      <div class="code-container">
        <h1 class="code">Código <?php echo $codigo ?></h1>
      </div>
      <div class="title-container">
        <h1 style="text-align: center"><?php echo $row['nombre_embalse'] ?></h1>
      </div>
      <img class="img-logo" src="http://<?php echo $_SERVER['HTTP_HOST'];
                                        echo $image_logo ?>" />
      <div style="position: absolute; top: 15px; left: 950px;">
        <img class="img-letters" src="http://<?php echo $_SERVER['HTTP_HOST'];
                                              echo $logo_letters ?>" />
      </div>
      <hr>
      <div style="position: absolute; left: 500px; top: 120px; width: 460px;">
        <h1 style="text-align: center; color: #2E86C1;"><?php echo getMonthName() ?></h1>
      </div>
      <img style="position: absolute; height: 210px; width: 230px; left: 100px; top: 150px;" src="http://<?php echo $_SERVER['HTTP_HOST'];
                                                                                                          echo $area ?>" />
      <div style="position: absolute; width: 230px; left: 100px; top: 345px;">
        <h5>Área de la Cuenca: <?php echo number_format(floatVal($row['area_cuenca']), 2, ',', '.'); ?> Km2</h5>
      </div>

      <div style="position: absolute; height: 100px; width: 300px; left: 65px; top: 380px; border: gray 1px solid; background-color: black">

      </div>

      <div style="position: absolute; height: 160px; width: 500px; left: 480px; top: 320px; border: gray 1px solid;">
        <img style="height: 100%; width: 100%;" src="<?php echo "../../assets/img/temp/imagen-$id-3.png" ?>">
      </div>

      <div style="position: absolute; left: 650px; top: 287px;">
        <h5 style="color: #2E86C1;"><?php echo $variacion_semanal . ' ' . $fecha . ' al ' . $fecha2 ?></h5>
      </div>

      <div style="position: absolute; left: 650px; top: 470px;">
        <h5 style="color: #2E86C1;"><?php echo 'VARIACION MENSUAL' . strtoupper($variacion_mensual) ?></h5>
      </div>

      <div style="position: absolute; height: 230px; width: 915px; left: 65px; top: 500px; border: gray 1px solid;">
        <img style="height: 100%; width: 100%;" src="<?php echo "../../assets/img/temp/imagen-$id-2.png" ?>">
      </div>

      <table style="position: absolute; top: 180px; left: 500px;">
        <tr>
          <th class="text-celd">DÍAS</th>
          <?php for ($i = 0; $i < 7; $i++) {
            $dia = $i + 2;
            echo "<th>0$dia" . "-" . getShortName() . "</th>";
          } ?>
        </tr>
        <tr>
          <td class="text-celd">COTA <?php getYear(); ?>
            (m.s.n.m.)</td>
          <?php
          $resultado = $conn->query($sqlMonths);
          $celdas_rellenadas = 0;
          $cotas = array();
          if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
              $cota = $fila['cota_actual'];
              $fecha = date("j", strtotime($fila["fecha"]));
              $cotas[$fecha] = $cota . "-" . date("y", strtotime($fila["fecha"]));
            }
          }

          for ($i = 2; $i < 9; $i++) {
            if (array_key_exists((string)$i, $cotas)) {
              echo "<td>" . explode("-", $cotas[$i])[0] . "</td>";
            } else {
              echo "<td>N/A</td>";
            }
          }

          ?>
        </tr>
        <tr>
          <td class="text-celd">VOLUMEN
            (Hm3
            )</td>
          <?php
          $batimetria = new Batimetria($id, $conn);
          for ($i = 2; $i < 9; $i++) {
            if (array_key_exists((string)$i, $cotas)) {
              //explode("-", $cotas[$i])[0]
              echo "<td>" . $batimetria->getByCota('2012', explode("-", $cotas[$i])[0])[1] . "</td>";
            } else {
              echo "<td>N/A</td>";
            }
          }
          $cotas = [];
          $indice++;
          ?>
        </tr>
      </table>
      <?php if ($indice > 0 && $indice !== $num_rows) { ?>
        <div style="height: 1000px;"></div>
      <?php
      } ?>
    <?php }
    ?>
  <?php
  } ?>
</body>

</html>