<?php
require_once '../../php/Conexion.php';
require_once '../../php/batimetria.php';

$id = $_GET['id'];
$index = $_GET['index'];
$semanas = $_GET['semanas'];
$fecha_inicio = $_GET['fecha'];
$sql = "SELECT * FROM embalses WHERE id_embalse = $id";
$volumen_inicial = $_GET['volumen'];
$cota_inicial = $_GET['cota'];

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
$titulo_reporte = "Gráfico";
$TITULO = "Gráfico 1";
$TITULO2 = "Gráfico 2. Monitoreo por semana 9-17.";
$TITULO3 = "Gráfico 3. Monitoreo por semana 17-25.";
$TITULO4 = "Gráfico 4. Monitoreo diario desde el 30/10 al 07/11.";
$TITULO5 = "Gráfico 5";
$TITULO6 = "Gráfico 6";

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
    <div style="width: 1050px;">
        <table style="position: absolute; top: 160px;">
            <tbody>
                <tr>
                    <th style="text-align: left;">Cota inicial de Monitoreo:</th>
                    <td style="text-align: right;"><?php echo number_format($cota_inicial, 2, ".", "") . " m s.n.m (" . number_format($volumen_inicial, 3, ".", "") . " hm3)" ?></td>
                </tr>
                <tr>
                    <th style="text-align: left;">Fecha:</th>
                    <td style="text-align: right;"><?php echo $fecha_inicio ?></td>
                </tr>
                <tr>
                    <th style="text-align: left;">Volumen Nivel Normal:</th>
                    <?php $batimetria = new Batimetria($id, $conn); ?>
                    <td style="text-align: right;"><?php echo number_format($batimetria->volumenNormal(), 4, ".", "") . " hm3 (Cota " . $batimetria->cotaNormal() . " m s.n.m)" ?></td>
                </tr>
                <tr>
                    <th style="text-align: left;">Volumen Nivel Minimo:</th>
                    <td style="text-align: right;"><?php echo number_format($batimetria->volumenMinimo(), 4, ".", "") . " hm3 (Cota " . $batimetria->cotaMinima() . " m s.n.m)" ?></td>
                </tr>
            </tbody>
        </table>
        <p style="position: absolute; text-align: center; top: 250px; width: 1050px;"><?php echo $TITULO ?></p>
    </div>
    <div style="position: absolute; top: 270px; width: 1030px; height: 455px; border-color: grey;border-width: 2px;border-style: solid; border-radius: 10px;">
        <img src="../../assets/img/temp/imagen-monitoreo1-1.png" alt="monitoreo" style="width: 1000px; height: 450px; margin-top: 2px; margin-left: 20px;">
    </div>

    <?php
    for ($i = 2; $i <= $index; $i++) {
        $imagen = "../../assets/img/temp/imagen-monitoreo" . $i . "-1.png";
        echo
        '<div style="height: 400px;"></div>
        <div>
            <div>
                <img class="img-logo-letters" src=" ' . $srcLogoLetters . '" />
                <img class="img-logo" src="' . $srcLogo . '" />
            </div>
            <p style="position: absolute; text-align: center; text-justify: center; top: 15px;">
                VICEMINISTERIO DE ADMINISTRACIÓN DE CUENCAS HIDROGRÁFICAS<br>
                DIRECCIÓN GENERAL DE MANEJO DE EMBALSES<br>
                DIRECCIÓN DE OPERACIÓN Y MANTENIMIENTO DE EMBALSES</p>
            <div style="position: absolute; left: 1000px; width: 70px; top: 20px;">
                <p style="font-weight: 900;">' . $fecha_hora . '</p>
            </div>
        </div>
        <div style="position: absolute; width: 1030px; height: 500px; ">
            <p style="position: absolute; text-align: center; top:80px; width: 1050px;">' . $titulo_reporte . ' ' . $i . '</p>
        </div>
        <div style="position: absolute; top: 140px; width: 1085px; height: 480px; border-color: grey; border-width: 2px;border-style: solid; border-radius: 10px;">
            <img src="' . $imagen . '" alt="monitoreo" style="width: 1000px; height: 480px; margin-left: 45px; margin-top: 10px;">
        </div>';
    }
    ?>

    <div style="height: 400px;"></div>
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
    <div style=" position: absolute; width: 1080px;">
        <p style="position: absolute; text-align: center; top:80px; width: 1050px;"><?php echo "Grafico Mensual"; ?></p>
    </div>
    <div style="position: absolute; top: 140px; width: 1030px; height: 485px; border-color: grey;border-width: 2px; border-style: solid; margin-left: 20px; border-radius: 10px;">
        <img src="../../assets/img/temp/imagen-monitoreo-mes-1.png" alt="monitoreo" style="width: 1000px; height: 480px; margin-left: 10px;margin-top: 2px">
    </div>

    <div style="height: 400px;"></div>
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
    <div style=" position: absolute; width: 1080px;">
        <p style="position: absolute; text-align: center; top:80px; width: 1050px;"><?php echo "Grafico Anual"; ?></p>
    </div>
    <div style="position: absolute; top: 140px; width: 1030px; height: 485px; border-color: grey;border-width: 2px; border-style: solid; margin-left: 20px; border-radius: 10px;">
        <img src="../../assets/img/temp/imagen-monitoreo-anio-1.png" alt="monitoreo" style="width: 1000px; height: 480px; margin-left: 10px;margin-top: 2px">
    </div>

    <div style="height: 400px;"></div>
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
    <div style=" position: absolute; width: 1080px;">
        <p style="position: absolute; text-align: center; top:80px; width: 1050px;"><?php echo "Grafico Movimiento" ?></p>
    </div>
    <div style="position: absolute; top: 140px; width: 1030px; height: 480px; border-color: grey;border-width: 2px; border-style: solid; margin-left: 20px; border-radius: 10px;">
        <img src="../../assets/img/temp/imagen-monitoreo-semana-1.png" alt="monitoreo" style="width: 1000px; height: 470px; margin-left: 10px;margin-top: 2px">
    </div>
</body>

</html>