<?php
require_once '../../php/Conexion.php';
$fullPath = getcwd();
$parts = explode(DIRECTORY_SEPARATOR, $fullPath);
date_default_timezone_set('America/Caracas');
require_once '../../php/batimetria.php';


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

/*$image_logo =  "/" . $projectName . "/assets/img/logos/cropped-mminaguas.jpg";
$logo_letters =  "/" . $projectName . "/assets/img/logos/MinaguasLetters.png";*/
$area =  "/" . $projectName . "/pages/reports_images/Area_cuenca.png";

$image_logo = "https://embalsesminaguas.000webhostapp.com/assets/img/logos/cropped-mminaguas.jpg";
$logo_letters =  "https://embalsesminaguas.000webhostapp.com/assets/img/logos/MinaguasLetters.png";
$area =  "https://embalsesminaguas.000webhostapp.com/pages/reports_images/Area_cuenca.png";
$logo_combinado = "../../assets/img/logos/logo_combinado.jpg";


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

    .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50px;
           /* background-color: lightgray;*/
            text-align: center;
        }
     
</style>

<body>

    <div class="header">
        <hr style="top: 55px; color:#1B569D">
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div>

    <div>
        <h1 style="position: absolute; top: 70px; left: 50px; text-align:center; text-justify:; color:#2E86C1; font-size: 23px;">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
        <h2 style="position: absolute; top: 100px; text-align: center; text-justify: center; color:#021568">Estatus de Fuentes Hídricas para Consumo Humano</h2>
        <div style="width: 1000px; height: 480px; background-color: lightgray; margin: 10px, 0, 0, 35px;">
        <!-- Mapa -->
        </div>
        <div style="position: absolute; height: 130px; width: 350px; left: 65px; top: 350px; border: gray 1px solid;">
        <h5 style="text-align:center; letter-spacing: 5px; width: 100%;">LEYENDA</h5>
        <p style="position: absolute; top: 20px;
        text-align: left; padding-left: 40px;">
        <div style="position: absolute; left: 20px; top: 2px; background-color: red;
         border-radius: 5; height: 10px; width: 10px;"></div>Ultimo Reporte</p>
        <p style="position: absolute; top: 20px;
        text-align: center; text-justify: left;">N. Cresta:</p>
       
        <p style="position: absolute; top: 50px;
        text-align: left; padding-left: 40px;">
        <div style="position: absolute; left: 5px; top: 2px; background-color: orange;
          height: 3px; width: 30px;"></div>Cota <?php echo $año_pasado ?></p>
        <p style="position: absolute; top: 40px;
        text-align: center; text-justify: left;">N. Maximo:</p>
    

        <p style="position: absolute; top: 80px;
        text-align: left; padding-left: 40px;">
        <div style="position: absolute; left: 5px; top: 2px; background-color: #2E86C1;
          height: 3px; width: 30px;"></div>Cota <?php echo $año_actual ?></p>
        <p style="position: absolute; top: 70px;
        text-align: center; text-justify: left;">N. Normal:</p>
        <p style="position: absolute; top: 90px;
        text-align: center; text-justify: left;">N. Minimo:</p>
      </div>

        <h4 style="position: absolute; top: 720px; text-align: center; text-justify: center;"><?php echo "$dia_actual de " . getMonthName() . " $año_actual" ?></h4>
    </div>

    <div class="header" style="">
        <hr style="top: 55px; color:#1B569D">
        <h1 style="position: absolute; top: 10px; font-size: 16px; text-align: left; text-justify: center; color:#000000">CONDICIONES ACTUALES DE ALMACENAMIENTO</h1>
        <img style="position: absolute;  width:90px ; height: 80px; float: right; top: 5px " src="<?php echo $logo_combinado ?>" />
        <h1 style="position: absolute; top: 10px; font-size: 16px; font-style: italic;text-align: right; text-justify: center; color:#1B569D">PLAN DE RECUPERACIÓN DE FUENTES HÍDRICAS</h1>
    </div>

</body>

</html>