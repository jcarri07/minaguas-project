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
$dia_actual = date('d');

$año_actual = date('Y');
$año_pasado = date('Y', strtotime('-1 year'));

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

  .title-container {

    margin-left: 260px;
    width: 700px;
    color: #2E86C1;
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
  <img style="position: absolute; width: ; height: 50px; height: 50px; float: right; top: 12px " src="http://<?php echo $_SERVER['HTTP_HOST'];
                                                                                                              echo $image_logo ?>" />
  <img style="position: absolute;  height: 55px; float: right;" src="http://<?php echo $_SERVER['HTTP_HOST'];
                                                                            echo $logo_letters ?>" />
  <div style="height: 900px; ">
    <h1 style="position: absolute; top: 350px; text-align: center; text-justify: center; color:#2E86C1">REPORTE DE VARIACIÓN DE NIVELES Y VOLÚMENES
      EN EMBALSES PRIORIZADOS</h1>
    <h4 style="position: absolute; top: 650px; text-align: center; text-justify: center;"><?php echo "$dia_actual de " . getMonthName() . " $año_actual" ?></h4>
  </div>

  <?php if ($num_rows > 0) {
    $indice = 0;
  ?>
    <?php while ($row = $result->fetch_assoc()) {
      $id = $row['id_embalse'];
      $normal = number_format(floatVal($row['cota_nor']), 2, ',', '.');
      $minimo = number_format(floatVal($row['cota_min']), 2, ',', '.');
      $maximo = number_format(floatVal($row['cota_max']), 2, ',', '.');
      $cresta = number_format(floatVal($row['cota_cresta']), 2, ',', '.');
      $sqlMonths = "SELECT cota_actual, fecha, id_embalse FROM datos_embalse WHERE MONTH(fecha) = $mes_actual AND DAY(fecha) BETWEEN 2 AND 8 AND id_embalse = '$id' GROUP BY (fecha);";
    ?>
      <div class="square">
        <?php echo $indice + 1; ?>
      </div>
      <h3 style="position: absolute; top: 55px; color: #2E86C1">Código <?php echo $codigo ?></h3>
      <h1 style="position: absolute; text-align: center; color:#2E86C1"><?php echo $row['nombre_embalse'] ?></h1>
      <img style="position: absolute; width: 50px; height: 50px; float: right;" src="http://<?php echo $_SERVER['HTTP_HOST'];
                                                                                            echo $image_logo ?>" />
      <div style="position: absolute; top: 15px; left: 950px; top: 2px;">
        <img style="position: absolute; width: ; height: 55px;" src="http://<?php echo $_SERVER['HTTP_HOST'];
                                                                            echo $logo_letters ?>" />
      </div>
      <hr>
      <div style="position: absolute; left: 500px; top: 80px; width: 460px;">
        <h1 style="text-align: center; color: #2E86C1;"><?php echo getMonthName() ?></h1>
      </div>
      <img style="position: absolute; height: 210px; width: 230px; left: 100px; top: 120px;" src="http://<?php echo $_SERVER['HTTP_HOST'];
                                                                                                          echo $area ?>" />
      <div style="position: absolute; width: 230px; left: 100px; top: 320px;">
        <h5 style="text-align: center;">Área de la Cuenca: <?php echo number_format(floatVal($row['area_cuenca']), 2, ',', '.'); ?> Km2</h5>
      </div>

      <div style="position: absolute; height: 130px; width: 350px; left: 65px; top: 350px; border: gray 1px solid;">
        <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
        <p style="position: absolute; top: 20px;
        text-align: left; padding-left: 40px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: red;
         border-radius: 5; height: 10px; width: 10px;"></div>Cota del dia</p>
        <p style="position: absolute; top: 20px;
        text-align: center; text-justify: left;">N. Cresta:</p>
        <p style="position: absolute; top: 20px;
        text-align: right; padding-right: 10px;"><?php echo $cresta ?> msnm</p>

        <p style="position: absolute; top: 50px;
        text-align: left; padding-left: 40px;">
        <div style="position: absolute; left: 5px; top: 2px; background-color: orange;
          height: 3px; width: 30px;"></div>Cota <?php echo $año_pasado ?></p>
        <p style="position: absolute; top: 40px;
        text-align: center; text-justify: left;">N. Maximo:</p>
        <p style="position: absolute; top: 40px;
        text-align: right; padding-right: 10px;"><?php echo $maximo ?> msnm</p>

        <p style="position: absolute; top: 80px;
        text-align: left; padding-left: 40px;">
        <div style="position: absolute; left: 5px; top: 2px; background-color: #2E86C1;
          height: 3px; width: 30px;"></div>Cota <?php echo $año_actual ?></p>
        <p style="position: absolute; top: 70px;
        text-align: center; text-justify: left;">N. Normal:</p>
        <p style="position: absolute; top: 90px;
        text-align: center; text-justify: left;">N. Minimo:</p>
        <p style="position: absolute; top: 70px;
        text-align: right; padding-right: 10px;"><?php echo $normal ?> msnm</p>
        <p style="position: absolute; top: 90px;
        text-align: right; padding-right: 10px;"><?php echo $minimo ?> msnm</p>
      </div>

      <div style="position: absolute; height: 160px; width: 500px; left: 480px; top: 300px; border: gray 1px solid;">
        <img style="height: 100%; width: 100%;" src="<?php echo "../../assets/img/temp/imagen-$id-3.png" ?>">
      </div>

      <div style="position: absolute; left: 650px; top: 260px;">
        <h5 style="color: #2E86C1;"><?php echo $variacion_semanal . ' ' . $fecha . ' al ' . $fecha2 ?></h5>
      </div>

      <div style="position: absolute; left: 650px; top: 460px;">
        <h5 style="color: #2E86C1;"><?php echo 'VARIACION MENSUAL ' . strtoupper($variacion_mensual) ?></h5>
      </div>

      <div style="position: absolute; height: 230px; width: 915px; left: 65px; top: 500px; border: gray 1px solid;">
        <img style="height: 100%; width: 100%;" src="<?php echo "../../assets/img/temp/imagen-$id-2.png" ?>">
      </div>

      <table style="position: absolute; top: 150px; left: 500px;">
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
          ?>
        </tr>
      </table>
      <div style="height: 1000px;">
      </div>

      <!-- PAGINA 2 -->

      <div>
        <div class="square">
          <?php echo $indice + 1; ?>
        </div>
        <h3 style="position: absolute; top: 75px; color: #2E86C1">Código <?php echo $codigo ?></h3>
        <h1 style="position: absolute; text-align: center; color:#2E86C1; top: 20px;"><?php echo $row['nombre_embalse'] ?></h1>
        <img style="position: absolute; width: 50px; height: 50px; float: right; top: 20px;" src="http://<?php echo $_SERVER['HTTP_HOST'];
                                                                                                          echo $image_logo ?>" />
        <div style="position: absolute; top: 15px; left: 950px; top: 20px;">
          <img style="position: absolute; width: ; height: 55px;" src="http://<?php echo $_SERVER['HTTP_HOST'];
                                                                              echo $logo_letters ?>" />
        </div>
        <hr style="top: 85px;">
        <div style="position: absolute; left: 500px; top: 80px; width: 460px;">
          <h1 style="text-align: center; color: #2E86C1;"><?php echo getMonthName() ?></h1>
        </div>
        <img style="position: absolute; height: 210px; width: 230px; left: 100px; top: 120px;" src="http://<?php echo $_SERVER['HTTP_HOST'];
                                                                                                            echo $area ?>" />
        <div style="position: absolute; width: 230px; left: 100px; top: 320px;">
          <h5 style="text-align: center;">Área de la Cuenca: <?php echo number_format(floatVal($row['area_cuenca']), 2, ',', '.'); ?> Km2</h5>
        </div>

        <div style="position: absolute; height: 130px; width: 350px; left: 560px; top: 280px; border: gray 1px solid;">
          <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
          <p style="position: absolute; top: 20px;
        text-align: left; padding-left: 40px;">
          <div style="position: absolute; left: 20px; top: 2px; background-color: red;
         border-radius: 5; height: 10px; width: 10px;"></div>Cota del dia</p>
          <p style="position: absolute; top: 20px;
        text-align: center; text-justify: left;">N. Cresta:</p>
          <p style="position: absolute; top: 20px;
        text-align: right; padding-right: 10px;"><?php echo $cresta ?> msnm</p>

          <p style="position: absolute; top: 50px;
        text-align: left; padding-left: 40px;">
          <div style="position: absolute; left: 5px; top: 2px; background-color: orange;
          height: 3px; width: 30px;"></div>Cota <?php echo $año_pasado ?></p>
          <p style="position: absolute; top: 40px;
        text-align: center; text-justify: left;">N. Maximo:</p>
          <p style="position: absolute; top: 40px;
        text-align: right; padding-right: 10px;"><?php echo $maximo ?> msnm</p>

          <p style="position: absolute; top: 80px;
        text-align: left; padding-left: 40px;">
          <div style="position: absolute; left: 5px; top: 2px; background-color: #2E86C1;
          height: 3px; width: 30px;"></div>Cota <?php echo $año_actual ?></p>
          <p style="position: absolute; top: 70px;
        text-align: center; text-justify: left;">N. Normal:</p>
          <p style="position: absolute; top: 90px;
        text-align: center; text-justify: left;">N. Minimo:</p>
          <p style="position: absolute; top: 70px;
        text-align: right; padding-right: 10px;"><?php echo $normal ?> msnm</p>
          <p style="position: absolute; top: 90px;
        text-align: right; padding-right: 10px;"><?php echo $minimo ?> msnm</p>
        </div>

        <div style="position: absolute; left: 450px; top: 410px;">
          <h5 style="color: #2E86C1;"><?php echo 'VARIACION ANUAL ' . $año_pasado . " - " . $año_actual ?></h5>
        </div>

        <div style="position: absolute; height: 290px; width: 915px; left: 65px; top: 450px; border: gray 1px solid;">
          <img style="height: 100%; width: 100%;" src="<?php echo "../../assets/img/temp/imagen-$id-1.png" ?>">
        </div>

        <table style="position: absolute; top: 150px; left: 500px;">
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
      </div>
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