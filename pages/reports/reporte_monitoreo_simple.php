<?php
require_once '../../php/Conexion.php';

$id = $_GET['id'];

$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];

$año = $_GET['anio'];
$mes = $_GET['mes'];

$sql = "SELECT * FROM embalses WHERE id_embalse = $id";

$res = mysqli_query($conn, $sql);
$data = array();

if (mysqli_num_rows($res) > 0) {

    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    print_r($data);
} else {
    echo "No se encontraron resultados.";
}

foreach ($data as $row) {
    //INFO GENERAL
    $NOMBRE_EMBALSE = $row['nombre_embalse'];
}

if (1) {
    $srcLogo = "../../assets/img/logos/cropped-mminaguas.jpg";
    $srcLogoLetters = "../../assets/img/logos/MinaguasLetters.png";
} else {
    $srcLogo = "../../assets/img/logos/cropped-mminaguas.jpg";
    $srcLogoLetters = "../../assets/img/logos/MinaguasLetters.png";
}


date_default_timezone_set('America/Caracas');
$fecha_hora = date('j/n/Y g:i a');

$COTA_INICIAL = " 303,55 m s.n.m. (185,15 hm3)";
$FECHA_MONITOREO = "15/05/2023";
$VOLUMEN_NIVEL_NORMAL = "462,41 hm3 (Cota 328,80 m s.n.m.)";
$VOLUMEN_NIVEL_MINIMO = "76,28 hm3 (Cota 285,00 m s.n.m.)";
$TITULO = "Gráfico de Monitoreo.";



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
        position: absolute;
        top: 20px;
        float: left;
        right: 50px;
        width: 50px;
    }

    .img-logo-letters {
        position: absolute;
        float: left;
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
        <div>
            <img class="img-logo-letters" src="<?php echo $srcLogoLetters; ?>" />
            <img class="img-logo" src="<?php echo $srcLogo; ?>" />
        </div>
        <p style="position: absolute; text-align: center; text-justify: center; top: 15px;">
            VICEMINISTERIO DE ADMINISTRACIÓN DE CUENCAS HIDROGRÁFICAS<br>
            DIRECCIÓN GENERAL DE MANEJO DE EMBALSES<br>
            DIRECCIÓN DE OPERACIÓN Y MANTENIMIENTO DE EMBALSES</p>
        <div style="position: absolute; left: 1000px; width: 70px; top: 20px;">
            <p style="font-weight: 900;"><?php echo $fecha_hora; ?></p>
        </div>
    </div>
    <div style=" position: absolute;top: 80px; width: 1080px;">
        <h4 style="text-align: center; font-weight: bold; width: 1080px;"><?php echo $NOMBRE_EMBALSE; ?></h4>
    </div>
    <div style="height: 50px; top:xº 130px; position:absolute; left: 45px;">
        <table style="position: absolute; top: 160px;">
            <tbody>
                <?php
                setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp');
                if (!empty($año) && $año != 'undefined') { ?>
                    <tr>
                        <th style='text-align: left;'>AÑO:</th>
                        <td style='text-align: right;'><?php echo $año ?></td>
                    </tr>
                <?php } else if (!empty($mes) && $mes != 'undefined') { ?>
                    <tr>
                        <th style='text-align: left;'>MES:</th>
                        <td style='text-align: right;'><?php echo mb_convert_case(strftime('%B DE %Y', strtotime($mes)), MB_CASE_UPPER, 'UTF-8'); ?></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <th style='text-align: left;'>Desde:</th>
                        <td style='text-align: right;'><?php echo date("d/m/Y", strtotime($fecha1)) ?></td>
                    </tr>
                    <tr>
                        <th style='text-align: left;'>Hasta:</th>
                        <td style='text-align: right;'><?php echo date("d/m/Y", strtotime($fecha2))  ?></td>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>
    </div>
    <div style="width: 1050px;">
        <p style="position: absolute; text-align: center; top: 250px; width: 1050px;"><?php echo $TITULO ?></p>
    </div>
    <div style="position: absolute; top: 290px; width: 1085px; height: 480px; ">
        <img src="../../assets/img/temp/imagen-grafica-0.png" alt="monitoreo" style="width: 1000px; height: 450px; margin-left: 45px">
    </div>
</body>

</html>