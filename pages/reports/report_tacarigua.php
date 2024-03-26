<!DOCTYPE html>
<html lang="en">

<?php
$srcLogo = "../../assets/img/logos/cropped-mminaguas.jpg";
$srcLogoLetters = "../../assets/img/logos/MinaguasLetters.png";

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

$dia_actual = date('d');
$año_actual = date('Y');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
        position: absolute;
        top: 20px;
        float: right;
        margin-right: 10px;
        right: 50px;
        width: 50px;
    }

    .img-logo-letters {
        position: absolute;
        float: right;
        top: 20px;
        width: 120px;
        margin-bottom: 50px;
    }

    .img-letters {
        float: right;
        width: 100px;
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
    <div>
        <img class="img-logo-letters" src="<?php echo $srcLogoLetters; ?>" />
        <img class="img-logo" src="<?php echo $srcLogo; ?>" />
    </div>
    <div>
        <h1 style="position: absolute; top: 250px; text-align: center; text-justify: center; color:#2E86C1">SEGUIMIENTO Y MONITOREO DE LOS NIVELES<br>
            DEL LAGO LOS TACARIGUAS (LAGO DE VALENCIA)
        </h1>
        <h4 style="text-align: center;">REPORTE DE VARIACIÓN DE NIVELES Y PRECIPITACIÓN EN LA <br>
            <h4 style="text-align: center;">REGIÓN HIDROGRÁFICA CUENCA DEL LAGO LOS TACARIGUAS</h4>
        </h4>
    </div>
    <h4 style="text-align: center; margin-top: 50px;"><?php echo "$dia_actual de " . getMonthName() . " $año_actual" ?></h4>
</body>

</html>