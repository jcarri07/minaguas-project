<!DOCTYPE html>
<html lang="en">

<?php
$srcLogo = "../../assets/img/logos/cropped-mminaguas.jpg";
$srcLogoLetters = "../../assets/img/logos/MinaguasLetters.png";
$grafica = '../../assets/img/grafica-tacarigua.png';

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
        border: 1px solid #0000;
    }

    th,
    td {
        font-size: 12px;
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

    .head-table {
        text-align: left;
        width: 50px;
        background-color: #2E86C1;
    }

    .head-table-2 {
        width: 90px;
        text-align: left;
        background-color: #2E86C1;
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
    <div style="height: 50px;">
        <h4 style="color:#2E86C1">SEGUIMIENTO Y MONITOREO DE LOS NIVELES DEL LAGO LOS TACARIGUAS ( LAGO DE VALENCIA )</h4>
        <hr style="color:#2E86C1;">
        <div style="width: 500px; height: 10px; background-color: #2E86C1; padding-left: 30px;">
            <h4 style="margin-left: 5px; color: white;">VARIACIÓN MENSUAL DEL LAGO LOS TACARIGUAS - <?php echo $año_actual; ?></h4>
        </div>
        <div style="position: absolute; top: 60px; padding-left: 900px;">
            <h4><?php echo "$dia_actual de " . getMonthName() . " de $año_actual" ?></h4>
        </div>
    </div>
    <div>
        <h4>VARIACIÓNDE COTA POR MES</h4>
        <table>
            <thead>
                <tr>
                    <th class="head-table">DIA</th>
                    <th class="head-table">Enero</th>
                    <th class="head-table">Febrero</th>
                    <th class="head-table">Marzo</th>
                    <th class="head-table">Abril</th>
                    <th class="head-table">Mayo</th>
                    <th class="head-table">Junio</th>
                    <th class="head-table">Julio</th>
                    <th class="head-table">Agosto</th>
                    <th class="head-table">Septiembre</th>
                    <th class="head-table">Octubre</th>
                    <th class="head-table">Noviembre</th>
                    <th class="head-table">Variación desde Enero</th>
                    <th class="head-table">Variación desde 19 de Abril</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left;">Variacion (cm)</td>
                    <td>-10 cm</td>
                    <td>-14 cm</td>
                    <td>-15 cm</td>
                    <td>-7 cm</td>
                    <td>+18 cm</td>
                    <td>+16 cm</td>
                    <td>+23 cm</td>
                    <td>+7 cm</td>
                    <td>+8 cm</td>
                    <td>+10 cm</td>
                    <td>+14 cm</td>
                    <td style="font-weight: bold;">+54 cm</td>
                    <td style="font-weight: bold;">+104 cm</td>
                </tr>
                <tr>
                    <td style="text-align: left;">Variacion (hm3)</td>
                    <td>-37,57 cm</td>
                    <td>-52,54 cm</td>
                    <td>-56,24 cm</td>
                    <td>-26,22 cm</td>
                    <td>+67,46 cm</td>
                    <td>+60,05 cm</td>
                    <td>+86,45 cm</td>
                    <td>+26,34 cm</td>
                    <td>+30,13 cm</td>
                    <td>+37,68 cm</td>
                    <td>+52,81 cm</td>
                    <td style="font-weight: bold;">+203,37 cm</td>
                    <td style="font-weight: bold;">+390,91 cm</td>
                </tr>
            </tbody>
        </table>
        <div style="margin-top: 10px; height: 180px; width: 600px; padding-left: 200px; top: 10px;">
            <img src="<?php echo $grafica; ?>" />
        </div>

        <div style="height: 50px; width: 100px; position: absolute; top: 700px; left: 100px;">
            <img src="../../assets/icons/bar-chart.png" style=" width: 20px; height: 18px;" alt="">
        </div>
        <div style="position: absolute; top: 692px; left: 130px;">
            <p>Variación de la cota 2023 (m.s.n.m.)</p>
        </div>
        <div style="height: 50px; width: 100px; position: absolute; top: 700px; left: 400px;">
            <div style=" width: 0; height: 0;border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 16px solid lightblue;"></div>
        </div>
        <div style="position: absolute; top: 692px; left: 430px;">
            <p>Precipitación (mm)</p>
        </div>
        <div style="height: 50px; width: 100px; position: absolute; top: 700px; left: 700px;">
            <img src="../../assets/icons/remove.png" style=" width: 15px; height: 18px;" alt="">
        </div>
        <div style="position: absolute; top: 692px; left: 730px;">
            <p>Nivel de alerta (m.s.n.m.)</p>
        </div>

        <div style="height: 50px; width: 100px; position: absolute; top: 735px; left: 100px;">
            <img src="../../assets/icons/bar-chart.png" style=" width: 20px; height: 18px;" alt="">
        </div>
        <div style="position: absolute; top: 722px; left: 130px;">
            <p>Variación de la cota 2022 (m.s.n.m.)</p>
        </div>
        <div style="height: 50px; width: 100px; position: absolute; top: 735px; left: 400px;">
            <img src="../../assets/icons/remove.png" style=" width: 20px; height: 18px;" alt="">
        </div>
        <div style="position: absolute; top: 722px; left: 430px;">
            <p>Nivel máximo (m.s.n.m.)</p>
        </div>

        <div style="position: absolute; top: 722px; left: 730px;">
            <div style="position: absolute; right: 120px; height: 10px; top: 15px; width: 10px; background-color: yellow; border: 1px solid #0000;"></div>
            <p>Punto de inflexión</p>
        </div>
        <div style="position: absolute; top: 770px; left: 430px;">
            <div style="position: absolute; right: 80px; height: 10px; width: 10px; background-color: red; border: 1px solid #0000;"></div>
            Cota actual
        </div>
    </div>
    <div>
        <h4 style="color:#2E86C1; position:absolute; top: 15px;">SEGUIMIENTO Y MONITOREO DE LOS NIVELES DEL LAGO LOS TACARIGUAS ( LAGO DE VALENCIA )</h4>
        <hr style="color:#2E86C1;">
        <div style="width: 500px; height: 10px; background-color: #2E86C1; padding-left: 30px;">
            <h4 style="margin-left: 5px; color: white;">REPORTE DEL 20 MAYO AL 08 DE NOVIEMBRE</h4>
        </div>
        <div style="position: absolute; top: 60px; padding-left: 900px;">
            <h4><?php echo "$dia_actual de " . getMonthName() . " de $año_actual" ?></h4>
        </div>
        <h4>APORTES AL LAGO</h4>
        <div>
            <h5>ACUMULADO- PRECIPITACIÓN - PLUVIÓMETRO VACH</h5>
            <table border="1">
                <tr>
                    <th class="head-table-2">Columna 1</th>
                    <th class="head-table-2">Columna 2</th>
                    <th class="head-table-2">Columna 3</th>
                    <th class="head-table-2">Columna 4</th>
                    <th class="head-table-2">Columna 5</th>
                    <th class="head-table-2">Columna 6</th>
                    <th class="head-table-2">Columna 7</th>
                    <th class="head-table-2">Columna 8</th>
                    <th class="head-table-2">Columna 9</th>
                </tr>
                <tr>
                    <td>Fila 1 - Celda 1</td>
                    <td>Fila 1 - Celda 2</td>
                    <td>Fila 1 - Celda 3</td>
                    <td>Fila 1 - Celda 4</td>
                    <td>Fila 1 - Celda 5</td>
                    <td>Fila 1 - Celda 6</td>
                    <td>Fila 1 - Celda 7</td>
                    <td>Fila 1 - Celda 8</td>
                    <td>Fila 1 - Celda 9</td>
                </tr>
            </table>
        </div>
        <div>
            <h5>ACUMULADO- PRECIPITACIÓN - PLUVIÓMETRO VACH</h5>
            <table border="1">
                <tr>
                    <th class="head-table-2">Columna 1</th>
                    <th class="head-table-2">Columna 2</th>
                    <th class="head-table-2">Columna 3</th>
                    <th class="head-table-2">Columna 4</th>
                    <th class="head-table-2">Columna 5</th>
                    <th class="head-table-2">Columna 6</th>
                    <th class="head-table-2">Columna 7</th>
                    <th class="head-table-2">Columna 8</th>
                    <th class="head-table-2">Columna 9</th>
                </tr>
                <tr>
                    <td>Fila 1 - Celda 1</td>
                    <td>Fila 1 - Celda 2</td>
                    <td>Fila 1 - Celda 3</td>
                    <td>Fila 1 - Celda 4</td>
                    <td>Fila 1 - Celda 5</td>
                    <td>Fila 1 - Celda 6</td>
                    <td>Fila 1 - Celda 7</td>
                    <td>Fila 1 - Celda 8</td>
                    <td>Fila 1 - Celda 9</td>
                </tr>
            </table>
        </div>
        <div>
            <h5>ACUMULADO- PRECIPITACIÓN - PLUVIÓMETRO VACH</h5>
            <table border="1">
                <tr>
                    <th class="head-table-2">Columna 1</th>
                    <th class="head-table-2">Columna 2</th>
                    <th class="head-table-2">Columna 3</th>
                    <th class="head-table-2">Columna 4</th>
                    <th class="head-table-2">Columna 5</th>
                    <th class="head-table-2">Columna 6</th>
                    <th class="head-table-2">Columna 7</th>
                    <th class="head-table-2">Columna 8</th>
                    <th class="head-table-2">Columna 9</th>
                </tr>
                <tr>
                    <td>Fila 1 - Celda 1</td>
                    <td>Fila 1 - Celda 2</td>
                    <td>Fila 1 - Celda 3</td>
                    <td>Fila 1 - Celda 4</td>
                    <td>Fila 1 - Celda 5</td>
                    <td>Fila 1 - Celda 6</td>
                    <td>Fila 1 - Celda 7</td>
                    <td>Fila 1 - Celda 8</td>
                    <td>Fila 1 - Celda 9</td>
                </tr>
            </table>
        </div>
        <h4>SALIDAS AL LAGO</h4>
        <div>
            <h5>ACUMULADO- PRECIPITACIÓN - PLUVIÓMETRO VACH</h5>
            <table border="1">
                <tr>
                    <th class="head-table-2">Columna 1</th>
                    <th class="head-table-2">Columna 2</th>
                    <th class="head-table-2">Columna 3</th>
                    <th class="head-table-2">Columna 4</th>
                    <th class="head-table-2">Columna 5</th>
                    <th class="head-table-2">Columna 6</th>
                    <th class="head-table-2">Columna 7</th>
                    <th class="head-table-2">Columna 8</th>
                    <th class="head-table-2">Columna 9</th>
                </tr>
                <tr>
                    <td>Fila 1 - Celda 1</td>
                    <td>Fila 1 - Celda 2</td>
                    <td>Fila 1 - Celda 3</td>
                    <td>Fila 1 - Celda 4</td>
                    <td>Fila 1 - Celda 5</td>
                    <td>Fila 1 - Celda 6</td>
                    <td>Fila 1 - Celda 7</td>
                    <td>Fila 1 - Celda 8</td>
                    <td>Fila 1 - Celda 9</td>
                </tr>
            </table>
        </div>
        <div>
            <h5>ACUMULADO- PRECIPITACIÓN - PLUVIÓMETRO VACH</h5>
            <table border="1">
                <tr>
                    <th class="head-table-2">Columna 1</th>
                    <th class="head-table-2">Columna 2</th>
                    <th class="head-table-2">Columna 3</th>
                    <th class="head-table-2">Columna 4</th>
                    <th class="head-table-2">Columna 5</th>
                    <th class="head-table-2">Columna 6</th>
                    <th class="head-table-2">Columna 7</th>
                    <th class="head-table-2">Columna 8</th>
                    <th class="head-table-2">Columna 9</th>
                </tr>
                <tr>
                    <td>Fila 1 - Celda 1</td>
                    <td>Fila 1 - Celda 2</td>
                    <td>Fila 1 - Celda 3</td>
                    <td>Fila 1 - Celda 4</td>
                    <td>Fila 1 - Celda 5</td>
                    <td>Fila 1 - Celda 6</td>
                    <td>Fila 1 - Celda 7</td>
                    <td>Fila 1 - Celda 8</td>
                    <td>Fila 1 - Celda 9</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>