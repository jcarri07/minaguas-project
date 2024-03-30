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
        background-color: #2E86C1;
    }

    .head-table-3 {
        width: 70px;
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
        <div style="position: absolute; top: 130px; left: 160px; opacity: 0.5;">
            <img src="../../assets/img/download-arrow.png" alt="" style=" width: 50px; height: 50px;">
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
            <h5>ACUMULADO- ESTACIÓN DE BOMBEO LA PUNTA Hm3
                /mes</h5>
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
            <h5>CAUDAL DE ENTRADA (CUENCA) DESDE MAYO A OCTUBRE
            </h5>
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
        <div style="position: absolute; top: 460px; left: 160px; opacity: 0.5;">
            <img src="../../assets/img/upload-arrow.png" alt="" style=" width: 50px; height: 50px;">
        </div>
        <div>
            <h5>ACUMULADO - ESTACIÓN DE BOMBEO LOS GUAYOS</h5>
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
            <h5>EVAPORACIÓN DEL LAGO LOS TACARIGUAS</h5>
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
    <div>
        <h4 style="color:#2E86C1; position:absolute; top: 15px;">SEGUIMIENTO Y MONITOREO DE LOS NIVELES DEL LAGO LOS TACARIGUAS ( LAGO DE VALENCIA )</h4>
        <hr style="color:#2E86C1;">
        <div style="position: absolute; top: 60px; padding-left: 900px;">
            <h4><?php echo "$dia_actual de " . getMonthName() . " de $año_actual" ?></h4>
        </div>
        <div style="width: 600px; height: 10px; background-color: #2E86C1; padding-left: 15px;">
            <h4 style="margin-left: 5px; color: white;">VARIACIÓN DE LOS NIVELES DEL LAGO LOS TACARIGUAS AL 09 / 11 / 2023
            </h4>
        </div>
        <div style="margin-top: 20px;">
            <table>
                <tr>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">DIA</th>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">01</th>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">02</th>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">03</th>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">04</th>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">05</th>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">06</th>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">07</th>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">08</th>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">09</th>
                    <th class="head-table-3" style="text-align: center; padding-top: 10px;">VARIACIÓN
                        01/11 al 09/11</th>
                </tr>
                <tr>
                    <td>COTA m.s.n.m.</td>
                    <td>414,80</td>
                    <td>414,82</td>
                    <td>414,84</td>
                    <td>414,84</td>
                    <td>414,85</td>
                    <td>414,86</td>
                    <td>414,90</td>
                    <td>414,93</td>
                    <td>414,94</td>
                    <td>+ 14 cm</td>
                </tr>
                <tr>
                    <td>Vol. Inicial Hm3</td>
                    <td>414,80</td>
                    <td>414,82</td>
                    <td>414,84</td>
                    <td>414,84</td>
                    <td>414,85</td>
                    <td>414,86</td>
                    <td>414,90</td>
                    <td>414,93</td>
                    <td>14 cm</td>
                    <td>+ 14 cm</td>
                </tr>
            </table>
        </div>
        <div style="width: 230px; height: 10px; background-color: #2E86C1; padding-left: 15px; margin-top: 20px;">
            <h4 style="margin-left: 5px; color: white;">REPORTE DEL 08 / 11 / 2023</h4>
        </div>
        <div style="width: 350px; height: 400px; margin-top: 20px; padding-left: 10px;">
            <div style="padding-left: 20px;">
                <h5 style="margin: 5px;">1) Estación de Bombeo Los Guayos</h5>
                <p style="margin: 5px;">Grupo #1 24,00 horas</p>
                <p style="margin: 5px;">Grupo #1 24,00 horas</p>
                <p style="margin: 5px;">Grupo #1 24,00 horas</p>
                <p style="margin: 5px;">Grupo #1 24,00 horas</p>
                <div style="height: 20px;"></div>
                <h5 style="margin: 5px;">2) Estación de Bombeo La Punta</h5>
                <p style="margin: 5px;">Grupo #1 00,00 horas</p>
                <p style="margin: 5px;">Grupo #2 16,00 horas</p>
                <p style="margin: 5px;">Grupo #3 0 1 ,00 horas</p>
                <h5 style="margin: 5px;">3) Promedio mensual del Volumen estimado<br>
                    precipitado en la Cuenca</h5>
                <p style="margin: 5px;">Mes de Noviembre</p>
                <p style="margin: 5px;">Área de la Cuenca 2.771.270.000,00 m2</p>
                <p style="margin: 5px;">Volumen precipitado 106,85 Hm³</p>
                <h5 style="margin: 5px;">4) Pluviómetro VACH - Dique La Punta</h5>
                <p style="margin: 5px;">Promedio de Temperatura y Humedad Relativa<br>
                    30,33 °C / 68,33 %</p>
            </div>
        </div>
        <p style="position: absolute; top: 370px; margin-left: 200px; font-weight: bold;">311.040,00 m3/día </p>
        <p style="position: absolute; top: 400px; margin-left: 200px;">Qprom = 3.600,00 L/s </p>
        <p style="position: absolute; top: 510px; margin-left: 200px; font-weight: bold;">36.720,00 m3/día</p>

        <div style="position: absolute; top: 300px; left: 340px; width: 700px; height: 400px; padding-left: 60px;">
            <img src="../../assets/img/tacaragua-imagen-2.png" alt="">

            <img src="../../assets/icons/bar-chart.png" style="position: absolute; z-index: 1; top:390px; left: 75px; width: 15px; height: 18px;" alt="">
            <img src="../../assets/icons/remove.png" style="position: absolute; z-index: 1; top:390px; left: 500px; width: 15px; height: 18px;" alt="">
            <div style="height: 50px; width: 100px; position: absolute; top: 390px; left: 310px;">
                <div style=" width: 0; height: 0;border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 16px solid lightblue;"></div>
            </div>

            <img src="../../assets/icons/bar-chart.png" style="position: absolute; z-index: 1; top:420px; left: 75px; width: 15px; height: 18px;" alt="">
            <img src="../../assets/icons/remove.png" style="position: absolute; z-index: 1; top:425px; left: 310px; width: 15px; height: 18px;" alt="">
            <div style="height: 50px; width: 100px; position: absolute; top: 415px; left: 532px;">
                <div style="position: absolute; right: 120px; height: 10px; top: 15px; width: 10px; background-color: yellow; border: 1px solid #0000;"></div>
            </div>

            <div style="height: 50px; width: 100px; position: absolute; top: 413px; left: 345px;">
                <div style="position: absolute; right: 120px; height: 10px; top: 50px; width: 10px; background-color: red; border: 1px solid #0000;"></div>
            </div>

            <div style="position: absolute; top: 380px; left: 100px;">
                <p style="font-size: 12px;">Variación de la cota 2023 (m.s.n.m.)</p>
                <p style="font-size: 12px;">Variación de la cota 2022 (m.s.n.m.)</p>
            </div>
            <div style="position: absolute; top: 380px; left: 340px;">
                <p style="font-size: 12px;">Precipitación (mm)</p>
                <p style="font-size: 12px;">Nivel máximo (m.s.n.m.)</p>
            </div>
            <div style="position: absolute; top: 380px; left: 520px;">
                <p style="font-size: 12px;">Nivel de alerta (m.s.n.m.)</p>
                <p style="font-size: 12px;">Punto de inflexión</p>
            </div>
            <div style="position: absolute; top: 420px; left: 340px;">
                <p style="font-size: 12px;">Cota Actual</p>
            </div>
        </div>
        <div>
            <h4 style="color:#2E86C1; position:absolute; top: 15px;">SEGUIMIENTO Y MONITOREO DE LOS NIVELES DEL LAGO LOS TACARIGUAS ( LAGO DE VALENCIA )</h4>
            <hr style="color:#2E86C1;">
            <div style="position: absolute; top: 60px; padding-left: 900px;">
                <h4><?php echo "$dia_actual de " . getMonthName() . " de $año_actual" ?></h4>
            </div>
            <style>
                .table-large tr,
                th,
                td {
                    font-size: 8;
                }
            </style>
            <table class="table-large" style="position: absolute; top: 200px;">
                <tr>
                    <th>Columna 1</th>
                    <th>Columna 2</th>
                    <th>Columna 3</th>
                </tr>
                <!-- Filas 1 a 36 -->
                <tr>
                    <td>Fila 1, Celda 1</td>
                    <td>Fila 1, Celda 2</td>
                    <td>Fila 1, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 2, Celda 1</td>
                    <td>Fila 2, Celda 2</td>
                    <td>Fila 2, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 3, Celda 1</td>
                    <td>Fila 3, Celda 2</td>
                    <td>Fila 3, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 4, Celda 1</td>
                    <td>Fila 4, Celda 2</td>
                    <td>Fila 4, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 5, Celda 1</td>
                    <td>Fila 5, Celda 2</td>
                    <td>Fila 5, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 6, Celda 1</td>
                    <td>Fila 6, Celda 2</td>
                    <td>Fila 6, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 7, Celda 1</td>
                    <td>Fila 7, Celda 2</td>
                    <td>Fila 7, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 8, Celda 1</td>
                    <td>Fila 8, Celda 2</td>
                    <td>Fila 8, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 9, Celda 1</td>
                    <td>Fila 9, Celda 2</td>
                    <td>Fila 9, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 10, Celda 1</td>
                    <td>Fila 10, Celda 2</td>
                    <td>Fila 10, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 11, Celda 1</td>
                    <td>Fila 11, Celda 2</td>
                    <td>Fila 11, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 12, Celda 1</td>
                    <td>Fila 12, Celda 2</td>
                    <td>Fila 12, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 13, Celda 1</td>
                    <td>Fila 13, Celda 2</td>
                    <td>Fila 13, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 14, Celda 1</td>
                    <td>Fila 14, Celda 2</td>
                    <td>Fila 14, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 15, Celda 1</td>
                    <td>Fila 15, Celda 2</td>
                    <td>Fila 15, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 16, Celda 1</td>
                    <td>Fila 16, Celda 2</td>
                    <td>Fila 16, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 17, Celda 1</td>
                    <td>Fila 17, Celda 2</td>
                    <td>Fila 17, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 18, Celda 1</td>
                    <td>Fila 18, Celda 2</td>
                    <td>Fila 18, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 19, Celda 1</td>
                    <td>Fila 19, Celda 2</td>
                    <td>Fila 19, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 20, Celda 1</td>
                    <td>Fila 20, Celda 2</td>
                    <td>Fila 20, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 21, Celda 1</td>
                    <td>Fila 21, Celda 2</td>
                    <td>Fila 21, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 22, Celda 1</td>
                    <td>Fila 22, Celda 2</td>
                    <td>Fila 22, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 23, Celda 1</td>
                    <td>Fila 23, Celda 2</td>
                    <td>Fila 23, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 24, Celda 1</td>
                    <td>Fila 24, Celda 2</td>
                    <td>Fila 24, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 25, Celda 1</td>
                    <td>Fila 25, Celda 2</td>
                    <td>Fila 25, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 26, Celda 1</td>
                    <td>Fila 26, Celda 2</td>
                    <td>Fila 26, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 27, Celda 1</td>
                    <td>Fila 27, Celda 2</td>
                    <td>Fila 27, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 28, Celda 1</td>
                    <td>Fila 28, Celda 2</td>
                    <td>Fila 28, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 29, Celda 1</td>
                    <td>Fila 29, Celda 2</td>
                    <td>Fila 29, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 30, Celda 1</td>
                    <td>Fila 30, Celda 2</td>
                    <td>Fila 30, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 31, Celda 1</td>
                    <td>Fila 31, Celda 2</td>
                    <td>Fila 31, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 32, Celda 1</td>
                    <td>Fila 32, Celda 2</td>
                    <td>Fila 32, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 33, Celda 1</td>
                    <td>Fila 33, Celda 2</td>
                    <td>Fila 33, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 34, Celda 1</td>
                    <td>Fila 34, Celda 2</td>
                    <td>Fila 34, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 35, Celda 1</td>
                    <td>Fila 35, Celda 2</td>
                    <td>Fila 35, Celda 3</td>
                </tr>
                <tr>
                    <td>Fila 36, Celda 1</td>
                    <td>Fila 36, Celda 2</td>
                    <td>Fila 36, Celda 3</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>