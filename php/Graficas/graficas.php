<!DOCTYPE html>
<html lang="en">
<?php
include "../Conexion.php";
require_once '../batimetria.php';
date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");
$fecha_actual = date("Y");
$año = $fecha_actual;
$f = $fecha_actual - 1;
$pri = $_GET['pri'];
$text = "";


if ($pri) {
    $stringPrioritarios = "0";
    $queryPrioritarios = mysqli_query($conn, "SELECT configuracion FROM configuraciones WHERE nombre_config = 'prioritarios'");
    if (mysqli_num_rows($queryPrioritarios) > 0) {
        $stringPrioritarios = mysqli_fetch_assoc($queryPrioritarios)['configuracion'];
    }
    $text = "AND id_embalse IN ($stringPrioritarios)";
}

$re = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");
$count = mysqli_num_rows($re);
if ($count >= 1) {

    $res = mysqli_query($conn, "SELECT * FROM datos_embalse WHERE estatus = 'activo' AND (YEAR(fecha) = '$fecha_actual' OR YEAR(fecha) = '$f') $text ORDER BY fecha,hora ASC;");
    $count = mysqli_num_rows($res);
    if ($count >= 1) {

        $embalses = mysqli_fetch_all($re, MYSQLI_ASSOC);
        $datos_embalses = mysqli_fetch_all($res, MYSQLI_ASSOC);




        function get_days_of_week($month_num, $week_num)
        {
            $year = date("Y");
            $first_day_of_month = date('N', strtotime("$year-$month_num-01"));
            $days_in_month = date('t', strtotime("$year-$month_num-01"));
            $days = array();
            for ($day = 1; $day <= $days_in_month; $day++) {
                $week_of_month = ceil(($day + $first_day_of_month - 1) / 7);
                if ($week_of_month == $week_num) {
                    $days[] = $day;
                }
            }
            return $days;
        };

        function obtenerFechasSemana($fecha)
        {
            // Crear un objeto DateTime a partir de la fecha proporcionada
            $fechaObj = new DateTime($fecha);

            // Obtener el número del día de la semana (1 = lunes, 7 = domingo)
            $diaSemana = $fechaObj->format('N');

            // Calcular la diferencia entre el día actual y el lunes de la misma semana
            $diferencia = $diaSemana - 1;

            // Restar la diferencia para obtener la fecha del lunes
            $lunes = $fechaObj->modify("-$diferencia days");

            // Inicializar un array para almacenar las fechas de la semana
            $fechasSemana = array();

            // Agregar las fechas de la semana al array
            for ($i = 0; $i < 7; $i++) {
                $fechasSemana[] = $lunes->format('Y-m-d');
                $lunes->modify('+1 day');
            }

            return $fechasSemana;
        }

        // Ejemplo de uso
        $fechasSemana = obtenerFechasSemana(date('Y-m-d'));
?>

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script src="../../assets/js/Chart.js"></script>
            <!--script src="../../assets/js/date-fns.js"></script-->
            <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
            <script src="../../assets/js/html2canvas.min.js"></script>
            <link href="../../assets/css/style-spinner.css" rel="stylesheet" />

            <title>Document</title>
        </head>

        <body style="height:800px">
            <!--div style=" width: 1200px;"-->
            <div>
                <?php
                $j = 0;
                $nom = array("Cota " . date("Y"), "Cota " . (date("Y") - 1));
                $pivote = 0;

                for ($t = 0; $t <  count($embalses); $t++) {
                ?>
                    <div style="width:1830px !important; height:600px; position:absolute; top:-100%;"><canvas class="alA" id="ano<?php echo $t; ?>"></canvas></div>
                    <div style="width:1830px !important; height:460px; position:absolute; top:-100%;"><canvas class="alM" id="mes<?php echo $t; ?>"></canvas></div>
                    <div style="width:900px !important; height:300px; position:absolute; top:-100%;"><canvas class="alS" id="semana<?php echo $t; ?>"></canvas></div>

                <?php

                }
                ?>

            </div>
            <div class="loaderPDF">
                <div class="lds-dual-ring"></div>
            </div>
        </body>

        <script>
const arbitra = {
                    id: 'arbitra',
                    dr: function(lines, ctx, left, right, y) {
                        ctx.save();

                        lines.forEach(line => {
                            const {
                                yvalue,
                                cota,
                                color,
                                h
                            } = line;

                            ctx.beginPath();
                            ctx.lineWidth = 1;
                            ctx.moveTo(left, y.getPixelForValue(yvalue));
                            ctx.lineTo(right, y.getPixelForValue(yvalue));
                            ctx.strokeStyle = color; // Cambiar color según tus preferencias
                            ctx.fillText(cota + ": " + yvalue + " (Hm³)", right - 200, y.getPixelForValue(yvalue) + h);
                            ctx.stroke();
                        });

                        ctx.restore();
                    },
                    beforeDatasetsDraw: function(chart, args, plugins) {
                        const {
                            ctx,
                            scales: {
                                x,
                                y
                            },
                            chartArea: {
                                left,
                                right
                            }
                        } = chart;

                        // Obtener las líneas específicas para este gráfico
                        const lines = chart.options.plugins.arbitra.lines;

                        // Llamada a la función dr() dentro del contexto del plugin con las líneas específicas
                        this.dr(lines, ctx, left, right, y);

                        // Resto del código del plugin
                    }
                };
            $(document).ready(function() {





                <?php

                for ($t = 0; $t <  count($embalses); $t++) {
                    $bati = new Batimetria($embalses[$t]["id_embalse"], $conn);
                    $batimetria = $bati->getBatimetria(); ?>
                    año<?php echo $t; ?> = document.getElementById("ano<?php echo $t; ?>");
                    mes<?php echo $t; ?> = document.getElementById("mes<?php echo $t; ?>");
                    semana<?php echo $t; ?> = document.getElementById("semana<?php echo $t; ?>");



                    let chartA<?php echo $t; ?> = new Chart(ano<?php echo $t; ?>, {
                        type: 'line',
                        title: 'grafica',
                        //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
                        data: {
                            datasets: [

                                {
                                    label: '<?php echo $nom[0] ?>',
                                    borderColor: '#36a1eb',
                                    backgroundColor: '#36a1eb',
                                    pointRadius: 5,
                                    data: [<?php
                                            $j = 0;
                                            $pivote = date("Y");
                                            $min = $embalses[$t]["cota_min"];
                                            $max = $embalses[$t]["cota_max"];
                                            while ($j < count($datos_embalses)) {

                                                if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"])) {



                                            ?> {
                                                    x: '<?php echo $datos_embalses[$j]["fecha"] . " " . $datos_embalses[$j]["hora"];  ?>',
                                                    y: <?php echo $bati->getByCota($año, $datos_embalses[$j]["cota_actual"])[1];  ?>
                                                },
                                                <?php if ($max < $datos_embalses[$j]["cota_actual"]) {
                                                        $max = $datos_embalses[$j]["cota_actual"];
                                                    }
                                                    if ($min > $datos_embalses[$j]["cota_actual"]) {
                                                        $min = $datos_embalses[$j]["cota_actual"];
                                                    } ?>

                                        <?php

                                                };
                                                $j++;
                                            };
                                        ?>
                                    ],
                                    pointBackgroundColor: function(context) {
                                        var index = context.dataIndex;
                                        var value = context.dataset.data[index];
                                        return index === context.dataset.data.length - 1 ? '#ff0000' : '#4472c4';
                                    },
                                    <?php
                                    ?>
                                },

                                {
                                    label: '<?php echo $nom[1] ?>',
                                    borderColor: '#e4c482',
                                    backgroundColor: '#e4c482',
                                    data: [
                                        <?php
                                        $j = 0;
                                        $pivote = date("Y") - 1;
                                        while ($j < count($datos_embalses)) {

                                            if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"])) {



                                        ?> {
                                                    x: '<?php echo (date("Y", strtotime($datos_embalses[$j]["fecha"])) + 1) . '-' . date("m-d", strtotime($datos_embalses[$j]["fecha"])) . " " . $datos_embalses[$j]["hora"] ?>',
                                                    y: <?php echo $bati->getByCota($año, $datos_embalses[$j]["cota_actual"])[1];  ?>
                                                },
                                                <?php if ($max < $datos_embalses[$j]["cota_actual"]) {
                                                    $max = $datos_embalses[$j]["cota_actual"];
                                                }
                                                if ($min > $datos_embalses[$j]["cota_actual"]) {
                                                    $min = $datos_embalses[$j]["cota_actual"];
                                                } ?>

                                        <?php

                                            };
                                            $j++;
                                        }
                                        ?>
                                    ],
                                    pointBackgroundColor: function(context) {
                                        var index = context.dataIndex;
                                        var value = context.dataset.data[index];
                                        return index === context.dataset.data.length - 1 ? '#ff0000' : '#4472c4';
                                    },
                                    <?php
                                    ?>
                                },


                            ],
                        },

                        options: {
                            animations: false,
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                axis: 'x',
                            },
                            plugins: {
                                arbitra: {


                                    lines: [{
                                            yvalue: <?php echo $bati->getByCota($año, $embalses[$t]["cota_min"])[1]; ?>,
                                            cota: "Volumen minimo",
                                            color: 'black',
                                            h: 15,
                                        },
                                        {
                                            yvalue: <?php echo $bati->getByCota($año, $embalses[$t]["cota_nor"])[1]; ?>,
                                            cota: "Volumen normal",
                                            color: 'black',
                                            h: 15,
                                        },
                                        {
                                            yvalue: <?php echo $bati->getByCota($año, $embalses[$t]["cota_max"])[1]; ?>,
                                            cota: "Volumen maximo",
                                            color: 'black',
                                            h: -15,
                                        }
                                        // Agrega más líneas según sea necesario
                                    ]
                                },
                                legend: {
                                    display: false,
                                    labels: {

                                        // This more specific font property overrides the global property
                                        font: {
                                            size: 20
                                        },

                                    }
                                },
                                title: {
                                    display: false,
                                    text: 'Embalse <?php echo $embalses[$t]['nombre_embalse']; ?>',
                                    fullSize: true,
                                    font: {
                                        size: 30
                                    }
                                },

                            },
                            scales: {

                                x: {
                                    title: {
                                        display: true,
                                        text: 'Año <?php echo date('Y'); ?>',
                                        font: {
                                            size: 18
                                        },
                                    },
                                    type: 'time',
                                    time: {
                                        unit: 'month'
                                    },
                                    min: '<?php echo $año; ?>-01',
                                    max: '<?php echo $año; ?>-12',

                                    ticks: {
                                        callback: (value, index, ticks) => {

                                            const date = new Date(value);
                                            //console.log(date);
                                            return new Intl.DateTimeFormat('es-ES', {
                                                month: 'short',

                                            }).format(value);
                                        },
                                        font: {
                                            size: 18
                                        },
                                    },
                                    grid: {
                                        color: function(context) {},
                                    },

                                },

                                y: {
                                    title: {
                                        display: true,
                                        text: 'Volumen (Hm³)',
                                        font: {
                                            size: 20
                                        },
                                    },
                                    min: <?php if ($min < $embalses[$t]["cota_min"]) {
                                                echo $bati->getByCota($año, $min)[1];
                                            } else {
                                                echo $bati->getByCota($año, $embalses[0]["cota_min"])[1] - 200;
                                            }; ?>,
                                    max: <?php if ($max > $embalses[$t]["cota_max"]) {
                                                echo $bati->getByCota($año, $max)[1] + 200;
                                            } else {
                                                echo $bati->getByCota($año, $embalses[$t]["cota_max"])[1] + 200;
                                            }; ?>,
                                    border: {
                                        display: false,
                                    },
                                    ticks: {
                                        font: {
                                            size: 14
                                        },
                                    },
                                },
                            },
                        },
                        plugins: [arbitra],

                    });
                    let chartM<?php echo $t; ?> = new Chart(mes<?php echo $t; ?>, {
                        type: 'line',
                        title: 'grafica',
                        //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
                        data: {
                            datasets: [

                                {
                                    label: '<?php echo $nom[0] ?>',
                                    borderColor: '#36a1eb',
                                    backgroundColor: '#36a1eb',
                                    pointRadius: 5,
                                    data: [<?php
                                            $j = 0;
                                            $pivote = date("Y");
                                            $min = $embalses[$t]["cota_min"];
                                            $max = $embalses[$t]["cota_max"];
                                            while ($j < count($datos_embalses)) {

                                                if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"])) {



                                            ?> {
                                                    x: '<?php echo $datos_embalses[$j]["fecha"] . " " . $datos_embalses[$j]["hora"];  ?>',
                                                    y: <?php echo $bati->getByCota($año, $datos_embalses[$j]["cota_actual"])[1];  ?>
                                                },
                                                <?php if ($max < $datos_embalses[$j]["cota_actual"]) {
                                                        $max = $datos_embalses[$j]["cota_actual"];
                                                    }
                                                    if ($min > $datos_embalses[$j]["cota_actual"]) {
                                                        $min = $datos_embalses[$j]["cota_actual"];
                                                    } ?>

                                        <?php

                                                };
                                                $j++;
                                            };
                                        ?>
                                    ],
                                    pointBackgroundColor: function(context) {
                                        var index = context.dataIndex;
                                        var value = context.dataset.data[index];
                                        return index === context.dataset.data.length - 1 ? '#ff0000' : '#4472c4';
                                    },
                                },

                                {
                                    label: '<?php echo $nom[1] ?>',
                                    borderColor: '#e4c482',
                                    backgroundColor: '#e4c482',
                                    data: [
                                        <?php
                                        $j = 0;
                                        $pivote = date("Y") - 1;
                                        while ($j < count($datos_embalses)) {

                                            if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"])) {



                                        ?> {
                                                    x: '<?php echo (date("Y", strtotime($datos_embalses[$j]["fecha"])) + 1) . '-' . date("m-d", strtotime($datos_embalses[$j]["fecha"])) . " " . $datos_embalses[$j]["hora"] ?>',
                                                    y: <?php echo $bati->getByCota($año, $datos_embalses[$j]["cota_actual"])[1];  ?>
                                                },

                                        <?php

                                            };
                                            $j++;
                                        }
                                        ?>
                                    ],
                                    pointBackgroundColor: function(context) {
                                        var index = context.dataIndex;
                                        var value = context.dataset.data[index];
                                        return index === context.dataset.data.length - 1 ? '#ff0000' : '#4472c4';
                                    },
                                    <?php
                                    ?>
                                },


                            ],
                        },

                        options: {
                            animations: false,
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                axis: 'x',
                            },
                            plugins: {
                                arbitra: {


                                    lines: [{
                                            yvalue: <?php echo $bati->getByCota($año, $embalses[$t]["cota_min"])[1]; ?>,
                                            cota: "Volumen minimo",
                                            color: 'black',
                                            h: 15,
                                        },
                                        {
                                            yvalue: <?php echo $bati->getByCota($año, $embalses[$t]["cota_nor"])[1]; ?>,
                                            cota: "Volumen normal",
                                            color: 'black',
                                            h: 15,
                                        },
                                        {
                                            yvalue: <?php echo $bati->getByCota($año, $embalses[$t]["cota_max"])[1]; ?>,
                                            cota: "Volumen maximo",
                                            color: 'black',
                                            h: -15,
                                        }
                                        // Agrega más líneas según sea necesario
                                    ]
                                },
                                legend: {
                                    display: false,
                                    labels: {

                                        // This more specific font property overrides the global property
                                        font: {
                                            size: 20
                                        },

                                    }
                                },
                                title: {
                                    display: false,
                                    text: 'Embalse <?php echo $embalses[$t]['nombre_embalse']; ?>',
                                    fullSize: true,
                                    font: {
                                        size: 30
                                    }
                                },

                            },
                            scales: {

                                x: {
                                    title: {
                                        display: true,
                                        text: ' <?php echo date('M Y'); ?>',
                                        font: {
                                            size: 18
                                        },
                                    },
                                    type: 'time',
                                    time: {
                                        unit: 'day'
                                    },
                                    min: '<?php echo date('Y-m') . '-01';  ?>',
                                    max: '<?php echo date('Y-m') . '-' . date('t') ?>',

                                    ticks: {
                                        callback: (value, index, ticks) => {

                                            const date = new Date(value);
                                            //console.log(date);
                                            return new Intl.DateTimeFormat('es-ES', {
                                                month: 'short',
                                                day: 'numeric',

                                            }).format(value);
                                        },
                                        font: {
                                            size: 18
                                        },
                                    },
                                    grid: {
                                        color: function(context) {},
                                    },

                                },

                                y: {
                                    title: {
                                        display: true,
                                        text: 'Volumen (Hm³)',
                                        font: {
                                            size: 20
                                        },
                                    },
                                    min: <?php if ($min < $embalses[$t]["cota_min"]) {
                                                echo $bati->getByCota($año, $min)[1];
                                            } else {
                                                echo $bati->getByCota($año, $embalses[0]["cota_min"])[1] - 200;
                                            }; ?>,
                                    max: <?php if ($max > $embalses[$t]["cota_max"]) {
                                                echo $bati->getByCota($año, $max)[1] + 200;
                                            } else {
                                                echo $bati->getByCota($año, $embalses[$t]["cota_max"])[1] + 200;
                                            }; ?>,
                                    border: {
                                        display: false,
                                    },
                                    ticks: {
                                        font: {
                                            size: 14
                                        },
                                    },
                                },
                            },
                        },
                        plugins: [arbitra],

                    });
                    let chartS<?php echo $t; ?> = new Chart(semana<?php echo $t; ?>, {
                        type: 'line',
                        labels: [<?php
                                    foreach ($fechasSemana as $dia) {
                                        echo ',"' . $dia . '"';
                                    }

                                    ?>],
                        data: {
                            datasets: [

                                {
                                    label: '<?php echo $nom[0]; ?>',
                                    borderColor: '#36a1eb',
                                    backgroundColor: '#36a1eb',
                                    pointRadius: 5,
                                    data: [<?php
                                            $j = 0;
                                            $pivote = date("Y");
                                            $min = $embalses[$t]["cota_min"];
                                            $max = $embalses[$t]["cota_max"];
                                            while ($j < count($datos_embalses)) {

                                                if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"])) {



                                            ?> {
                                                    x: '<?php echo $datos_embalses[$j]["fecha"] . " " . $datos_embalses[$j]["hora"];  ?>',
                                                    y: <?php echo $bati->getByCota($año, $datos_embalses[$j]["cota_actual"])[1];  ?>
                                                },
                                                <?php if ($max < $datos_embalses[$j]["cota_actual"]) {
                                                        $max = $datos_embalses[$j]["cota_actual"];
                                                    }
                                                    if ($min > $datos_embalses[$j]["cota_actual"]) {
                                                        $min = $datos_embalses[$j]["cota_actual"];
                                                    } ?>

                                        <?php

                                                };
                                                $j++;
                                            };
                                        ?>
                                    ],
                                    pointBackgroundColor: function(context) {
                                        var index = context.dataIndex;
                                        var value = context.dataset.data[index];
                                        return index === context.dataset.data.length - 1 ? '#ff0000' : '#4472c4';
                                    },
                                },
                                {
                                    label: '<?php echo $nom[1] ?>',
                                    borderColor: '#e4c482',
                                    backgroundColor: '#e4c482',
                                    data: [<?php
                                            $j = 0;
                                            $pivote = date("Y") - 1;
                                            while ($j < count($datos_embalses)) {

                                                if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"])) {



                                            ?> {
                                                    x: '<?php echo (date("Y", strtotime($datos_embalses[$j]["fecha"])) + 1) . '-' . strftime('%B',  strtotime($datos_embalses[$j]["fecha"])) . '-' . date("d", strtotime($datos_embalses[$j]["fecha"])) . " " . $datos_embalses[$j]["hora"] ?>',
                                                    y: <?php echo $bati->getByCota($año, $datos_embalses[$j]["cota_actual"])[1];  ?>
                                                },

                                        <?php

                                                };
                                                $j++;
                                            }
                                        ?>
                                    ],
                                    pointBackgroundColor: function(context) {
                                        var index = context.dataIndex;
                                        var value = context.dataset.data[index];
                                        return index === context.dataset.data.length - 1 ? '#ff0000' : '#4472c4';
                                    },
                                },


                            ],
                        },
                        options: {
                            animations: false,
                            maintainAspectRatio: false,
                            locale: 'es',
                            interaction: {
                                intersect: false,
                            },
                            plugins: {
                                arbitra: {


                                    lines: [{
                                            yvalue: <?php echo $bati->getByCota($año, $embalses[$t]["cota_min"])[1]; ?>,
                                            cota: "Volumen minimo",
                                            color: 'black',
                                            h: 15,
                                        },
                                        {
                                            yvalue: <?php echo $bati->getByCota($año, $embalses[$t]["cota_nor"])[1]; ?>,
                                            cota: "Volumen normal",
                                            color: 'black',
                                            h: 15,
                                        },
                                        {
                                            yvalue: <?php echo $bati->getByCota($año, $embalses[$t]["cota_max"])[1]; ?>,
                                            cota: "Volumen maximo",
                                            color: 'black',
                                            h: -15,
                                        }
                                        // Agrega más líneas según sea necesario
                                    ]
                                },
                                legend: {
                                    display: false,
                                    labels: {
                                        // This more specific font property overrides the global property
                                        font: {
                                            size: 16
                                        },

                                    }
                                },
                                title: {
                                    display: false,
                                    text: 'Embalse <?php echo $embalses[$t]['nombre_embalse']; ?>',
                                    fullSize: true,
                                    font: {
                                        size: 30
                                    }
                                },
                            },
                            scales: {

                                x: {
                                    title: {
                                        display: true,
                                        text: 'Semana <?php echo date("W", strtotime($fechasSemana[0])) . " " . strftime('del mes de %B', DateTime::createFromFormat("Y-m-d", end($fechasSemana))->getTimestamp()); ?>',
                                        font: {
                                            size: 16
                                        },
                                    },
                                    label: 'Año',
                                    type: 'time',
                                    time: {
                                        unit: 'day'
                                    },
                                    min: '<?php /*$dateString = date('Y-m-d');

                                            // Convertir la cadena de fecha a un objeto DateTime
                                            $date = new DateTime($dateString);

                                            // Restar 6 días al objeto DateTime
                                            $date->sub(new DateInterval('P6D'));

                                            // Imprimir la fecha resultante
                                            echo $date->format('Y-m-d');*/ echo date('Y-m') . '-02';  ?>',
                                    max: '<?php echo date('Y-m') . '-08'; ?>',
                                    ticks: {
                                        callback: (value, index, ticks) => {

                                            const date = new Date(value);
                                            //console.log(date);
                                            return new Intl.DateTimeFormat('es-ES', {
                                                day: 'numeric',
                                                month: 'short',
                                            }).format(value);
                                        },
                                        font: {
                                            size: 14
                                        },
                                    },
                                    grid: {
                                        color: function(context) {},
                                    },

                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Volumen (Hm³)',
                                        font: {
                                            size: 20
                                        },
                                    },
                                    min: <?php if ($min < $embalses[$t]["cota_min"]) {
                                                echo $bati->getByCota($año, $min)[1];
                                            } else {
                                                echo $bati->getByCota($año, $embalses[0]["cota_min"])[1] - 200;
                                            }; ?>,
                                    max: <?php if ($max > $embalses[$t]["cota_max"]) {
                                                echo $bati->getByCota($año, $max)[1] + 200;
                                            } else {
                                                echo $bati->getByCota($año, $embalses[$t]["cota_max"])[1] + 200;
                                            }; ?>,
                                    border: {
                                        display: false,
                                    },
                                    ticks: {
                                        font: {
                                            size: 14
                                        },
                                    },
                                },
                            },


                        },
                        plugins: [arbitra],
                    });
                <?php
                }

                closeConection($conn);
                for ($t = 0; $t <  count($embalses); $t++) {
                ?>
                    const x<?php echo $t; ?> = document.querySelector("#ano<?php echo $t; ?>");
                    const y<?php echo $t; ?> = document.querySelector("#mes<?php echo $t; ?>");
                    const z<?php echo $t; ?> = document.querySelector("#semana<?php echo $t; ?>");
                    var i = 1;
                    html2canvas(x<?php echo $t; ?>).then(function(canvas) { //PROBLEMAS
                        //$("#ca").append(canvas);
                        canvas.willReadFrequently = true,
                            dataURL = canvas.toDataURL("image/jpeg", 0.9);
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../guardar-imagen.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.send('imagen=' + dataURL + '&nombre=<?php echo $embalses[$t]['id_embalse']; ?>&numero=' + 1);
                        xhr.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {

                                console.log("listo");

                            } else {

                            }
                        }
                    });
                    html2canvas(y<?php echo $t; ?>).then(function(canva) { //PROBLEMAS
                        //$("#ca").append(canvas);
                        canva.willReadFrequently = true,
                            dataURL = canva.toDataURL("image/jpeg", 0.9);
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../guardar-imagen.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.send('imagen=' + dataURL + '&nombre=<?php echo $embalses[$t]['id_embalse']; ?>&numero=' + 2);
                        xhr.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                console.log("listo");

                            } else {

                            }
                        }
                    });
                    html2canvas(z<?php echo $t; ?>).then(function(canva) { //PROBLEMAS
                        //$("#ca").append(canvas);
                        canva.willReadFrequently = true,
                            dataURL = canva.toDataURL("image/jpeg", 0.9);
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '../guardar-imagen.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.send('imagen=' + dataURL + '&nombre=<?php echo $embalses[$t]['id_embalse']; ?>&numero=' + 3);
                        xhr.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                console.log("listo");
                                <?php if ($t == (count($embalses) - 1)) echo "location.href = '../../pages/reports/print_embalses_prioritarios.php?pri=" . $pri . "';"; ?> //AQUI CARRIZALES

                            } else {
                                console.log('error al generar graficas');
                            }
                        }
                    });


                <?php

                }
                ?>

            });
        </script>
<?php
    } else {
        echo "<p>No existen Embalses</p></body>";
    }
} else {
    echo "<p>No existen Embalses</p></body>";
}
?>

</html>