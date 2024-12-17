<script src="./assets/js/Chart.js"></script>
<!-- <script src="./assets/js/chart-zoom.js"></script> -->
<script src="./assets/js/date-fns.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> -->
<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");
ini_set('max_execution_time', 400);
$id = $_POST['id_embalse'];
$tipo = $_POST['tipo'];
$y = $_POST['anio'];
$ver = $_POST['ver'];
$periodo = $_POST['periodo'];
$anio = $y;
//$menor = $y - 5;

if ($tipo == "bar") {
    $aux = "SELECT id_registro, d.fecha, hora, (SELECT cota_actual 
    FROM datos_embalse 
    WHERE id_embalse = d.id_embalse AND fecha = d.fecha AND hora = (SELECT hora FROM datos_embalse WHERE fecha = d.fecha AND estatus = 'activo' AND cota_actual <> 0 AND id_embalse = d.id_embalse ORDER BY hora DESC LIMIT 1) ORDER BY cota_actual DESC LIMIT 1) AS cota_actual
FROM datos_embalse d, embalses e 
WHERE e.id_embalse = d.id_embalse AND e.estatus = 'activo' AND d.estatus = 'activo' AND d.id_embalse = '$id' AND YEAR(fecha) >= '$periodo'  
GROUP BY d.fecha;";
}
if ($tipo == "line") {
    $aux = "SELECT * FROM datos_embalse WHERE estatus = 'activo' AND id_embalse = '$id' AND cota_actual <> 0 AND YEAR(fecha) >= '$periodo' ORDER BY fecha,hora DESC;";
}

$bati = new Batimetria($id, $conn);
$batimetria = $bati->getBatimetria();
$res = mysqli_query($conn, $aux);
$r = mysqli_query($conn, "SELECT cota_min,cota_nor,cota_max FROM embalses WHERE estatus = 'activo' AND id_embalse = '$id';");
$count = mysqli_num_rows($r);
if ($count >= 1) {
    $count = mysqli_num_rows($res);
    if ($count >= 1) {

        function obtenerEtiquetas($y)
        {

            $etiquetas = array();
            $meses = range(1, 12);

            foreach ($meses as $mes) {
                /*$primerDia = strftime('%b',  strtotime("$y-$mes-01")) . date('-01', strtotime("$y-$mes-01"));
            $ultimoDia = strftime('%b',  strtotime("$y-$mes-01")) . date('-t', strtotime("$y-$mes-01"));*/

                $primerDia = date('M-d', strtotime("$y-$mes-01"));
                $ultimoDia = date('M-t', strtotime("$y-$mes-01"));

                $etiquetas[] = "(" . $primerDia . "/" . $ultimoDia . ")";
            }
            $etiquetas[count($etiquetas) - 1] = rtrim(end($etiquetas), ',');
            echo json_encode($etiquetas);
        }
        $count = mysqli_num_rows($res);
        $datos_embalses = mysqli_fetch_all($res, MYSQLI_ASSOC);
        $embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

?>

        <canvas id="chart" class="border border-radius-lg"></canvas>

        <script>
            $(document).ready(function() {


                const label = <?php echo obtenerEtiquetas($y); ?>;
                const point = {
                    id: 'point',
                    afterDatasetsDraw: function(chart, arg, options) {
                        const {
                            ctx
                        } = chart;
                        const dataset = chart.data.datasets[0];
                        const meta = chart.getDatasetMeta(chart.data.datasets.length-1);

                        if (meta.hidden) return;

                        const lastElement = meta.data[meta.data.length - 1];
                        const fontSize = 13;
                        const fontStyle = 'bold';
                        const fontFamily = 'Arial';
                        if (dataset.data.length > 0) {
                            ctx.save();
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                            ctx.fillStyle = 'blue';
                            total = dataset.data[dataset.data.length - 1].y;
                            tot = parseFloat(total);
                            formateado = new Intl.NumberFormat('de-DE', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(tot);
                            ctx.fillText(formateado, lastElement.x, lastElement.y - 5);
                        }
                        ctx.restore();
                    },
                };
                if ('<?php echo $ver; ?>' == 'volumen') {

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
                                ctx.fillText(cota + ": " + yvalue.toLocaleString("de-DE") + " (Hm³)", right * 4.2 / 6, y.getPixelForValue(yvalue) + h);
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
                        },
                    };


                    let cha = new Chart(chart, {
                        type: '<?php echo $tipo; ?>',
                        title: 'grafica',
                        //labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                        data: {
                            datasets: [

                                <?php
                                $splice = 0;
                                $min = $embalses[0]["cota_min"];
                                $max = $embalses[0]["cota_max"];
                                if ($count) {
                                    $fech = $periodo;

                                    $pivote = $anio;
                                    while (($pivote >= $fech)) {

                                        //echo "{label:'Volumen del año " . $fech . "',pointRadius: 0,data: [";
                                        $aux = 0;
                                        while ($aux < count($datos_embalses)) {
                                            if ($datos_embalses[$aux]["cota_actual"] != NULL && $fech == date("Y", strtotime($datos_embalses[$aux]['fecha']))) {
                                                echo "{label:'Volumen del año " . $fech . "',pointRadius: 0,data: [";
                                                $aux = 0;
                                                break;
                                            } else {
                                                $aux++;
                                            }
                                        }

                                        $j = 0;

                                        while ($j < count($datos_embalses)) {

                                            if ($datos_embalses[$j]["cota_actual"] != NULL && $fech == date("Y", strtotime($datos_embalses[$j]['fecha']))) { ?> {x: '<?php echo $anio . "-" . date("m-d", strtotime($datos_embalses[$j]['fecha'])) . " " . $datos_embalses[$j]["hora"];  ?>',y: <?php echo $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];  ?>},<?php
                                                if ($max < $datos_embalses[$j]["cota_actual"]) {
                                                    $max = $datos_embalses[$j]["cota_actual"];
                                                }
                                                if ($min > $datos_embalses[$j]["cota_actual"]) {
                                                    $min = $datos_embalses[$j]["cota_actual"];
                                                } ?> <?php
                                            }

                                            $j++;
                                        };
                                        if ($aux == 0) {
                                            echo "],";
                                            if ($fech == $anio) {
                                                echo "pointBackgroundColor: function(context) {var index = context.dataIndex;var value = context.dataset.data[index];return index === context.dataset.data.length - 1 ? '#ff0000' : '#4472c4';},
                                        pointRadius: function(context) {var index = context.dataIndex;var value = context.dataset.data[index];return index === context.dataset.data.length - 1 ? '6' : '0';},";
                                            };
                                            echo "categoryPercentage:1,},";
                                        }
                                        $fech++;
                                    }
                                }
                                ?>




                            ],
                        },

                        options: {
                            animations: true,


                            responsive: true,
                            maintainAspectRatio: false,
                            // interaction: {
                            //     intersect: false,
                            //     axis: 'x',
                            // },
                            layout: {
                                padding: 23,
                            },
                            plugins: {

                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.dataset.label || '';
                                            const value = context.raw;
                                            const labelName = context.label; // Muestra el nombre de la etiqueta única
                                            return label + ': ' + value.y.toLocaleString("de-DE");
                                        }
                                    }
                                },

                                arbitra: {


                                    lines: [{
                                            yvalue: <?php echo round($bati->getByCota($anio, $embalses[0]["cota_min"])[1], 2); ?>,
                                            cota: "Volumen mínimo",
                                            color: 'black',
                                            h: -15,
                                        },
                                        {
                                            yvalue: <?php echo round($bati->getByCota($anio, $embalses[0]["cota_nor"])[1], 2); ?>,
                                            cota: "Volumen normal",
                                            color: 'black',
                                            h: 15,

                                        },
                                        {
                                            yvalue: <?php echo round($bati->getByCota($anio, $embalses[0]["cota_max"])[1], 2); ?>,
                                            cota: "Volumen máximo",
                                            color: 'black',
                                            h: -15,
                                        }
                                        // Agrega más líneas según sea necesario
                                    ]
                                },
                                legend: {
                                    position: 'bottom',

                                    labels: {

                                        // This more specific font property overrides the global property
                                        font: {
                                            size: 18,
                                            family: 'Arial',
                                        },

                                    }
                                },
                                title: {
                                    display: false,
                                    text: 'Embalse',
                                    fullSize: true,
                                    font: {
                                        size: 30
                                    }
                                },

                            },
                            scales: {

                                x: {
                                    title: {
                                        display: false,
                                        text: 'Año <?php echo date('Y'); ?>',
                                        font: {
                                            size: 18
                                        },
                                    },
                                    stacked: true,
                                    type: 'time',
                                    time: {
                                        unit: 'month'
                                    },
                                    min: '<?php echo $anio; ?>-01-01',
                                    max: '<?php echo $anio; ?>-12-31',

                                    ticks: {
                                        callback: (value, index, ticks) => {

                                            const date = new Date(value);
                                            //console.log(date);
                                            const f = new Intl.DateTimeFormat('es-ES', {
                                                month: 'short',

                                            }).format(value);
                                            str = f.charAt(0).toUpperCase();
                                            return str + f.slice(1);
                                        },
                                        font: {
                                            size: 18,
                                            family: 'Arial',
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
                                            size: 16,
                                            family: 'Arial',
                                            weight: 'bold',
                                        },
                                    },
                                    min: <?php if ($min < $embalses[0]["cota_min"]) {
                                                echo $bati->getByCota($anio, $min)[1];
                                            } else {
                                                if ($bati->getByCota($anio, $embalses[0]["cota_min"])[1] - 200 < 0) {
                                                    echo 0;
                                                } else {
                                                    echo $bati->getByCota($anio, $embalses[0]["cota_min"])[1] - 200;
                                                }
                                            }; ?>,
                                    max: <?php if ($max > $embalses[0]["cota_max"]) {
                                                echo round($bati->getByCota($anio, $max)[1] + 10, 0);
                                            } else {
                                                echo round($bati->getByCota($anio, $embalses[0]["cota_max"])[1] + 10, 0);
                                            }; ?>,
                                    border: {
                                        display: false,
                                    },
                                    ticks: {
                                        font: {
                                            size: 14,
                                            family: 'Arial',
                                        },
                                        callback: function(valor, index, valores) {
                                            return valor.toLocaleString("de-DE");
                                        },

                                    },

                                },


                            },
                        },
                        plugins: [arbitra, point],

                    });

                };

                if ('<?php echo $ver; ?>' == 'cota') {

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
                                ctx.fillText(cota + ": " + yvalue.toLocaleString("de-DE") + " (m.s.n.m)", right - 250, y.getPixelForValue(yvalue) + h);
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


                    let cha = new Chart(chart, {
                        type: '<?php echo $tipo; ?>',
                        title: 'grafica',
                        //labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                        data: {
                            datasets: [

                                <?php
                                $min = $embalses[0]["cota_min"];
                                $max = $embalses[0]["cota_max"];
                                if ($count) {
                                    $fech = $periodo;

                                    $pivote = $anio;
                                    while (($pivote >= $fech)) {
                                        $aux = 0;
                                        while ($aux < count($datos_embalses)) {
                                            if ($datos_embalses[$aux]["cota_actual"] != NULL && $fech == date("Y", strtotime($datos_embalses[$aux]['fecha']))) {
                                                echo "{label:'Cota del año " . $fech . "',pointRadius: 0,data: [";
                                                $aux = 0;
                                                break;
                                            } else {
                                                $aux++;
                                            }
                                        }

                                        $j = 0;


                                        while ($j < count($datos_embalses)) {

                                            if ($datos_embalses[$j]["cota_actual"] != NULL && $fech == date("Y", strtotime($datos_embalses[$j]['fecha']))) { ?> {
                                                    x: '<?php echo $anio . "-" . date("m-d", strtotime($datos_embalses[$j]['fecha'])) . " " . $datos_embalses[$j]["hora"];  ?>',
                                                    y: <?php echo $datos_embalses[$j]["cota_actual"];  ?>
                                                },
                                                <?php
                                                if ($max < $datos_embalses[$j]["cota_actual"]) {
                                                    $max = $datos_embalses[$j]["cota_actual"];
                                                }
                                                if ($min > $datos_embalses[$j]["cota_actual"]) {
                                                    $min = $datos_embalses[$j]["cota_actual"];
                                                } ?>

                                <?php
                                            }

                                            $j++;
                                        };
                                        if ($aux == 0) {
                                            echo "],";
                                            if ($fech == $anio) {
                                                echo "pointBackgroundColor: function(context) {
                                            var index = context.dataIndex;
                                            var value = context.dataset.data[index];
                                            return index === context.dataset.data.length - 1 ? '#ff0000' : '#4472c4';
                                            },
                                            pointRadius: function(context) {
                                            var index = context.dataIndex;
                                            var value = context.dataset.data[index];
                                            return index === context.dataset.data.length - 1 ? '6' : '0';
                                            },";
                                            };
                                            echo "categoryPercentage:1,},";
                                        }
                                        $fech++;
                                    }
                                }
                                ?>




                            ],
                        },

                        options: {
                            animations: true,
                            responsive: true,
                            maintainAspectRatio: false,
                            // interaction: {
                            //     intersect: false,
                            //     axis: 'x',
                            // },
                            layout: {
                                padding: 23,
                            },
                            plugins: {

                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.dataset.label || '';
                                            const value = context.raw;
                                            const labelName = context.label; // Muestra el nombre de la etiqueta única
                                            return label + ': ' + value.y.toLocaleString("de-DE");
                                        }
                                    }
                                },

                                arbitra: {


                                    lines: [{
                                            yvalue: <?php echo $embalses[0]["cota_min"]; ?>,
                                            cota: "Cota minima",
                                            color: 'black',
                                            h: -15,
                                        },
                                        {
                                            yvalue: <?php echo $embalses[0]["cota_nor"]; ?>,
                                            cota: "Cota normal",
                                            color: 'black',
                                            h: 15,

                                        },
                                        {
                                            yvalue: <?php echo $embalses[0]["cota_max"]; ?>,
                                            cota: "Cota maxima",
                                            color: 'black',
                                            h: -15,
                                        }
                                        // Agrega más líneas según sea necesario
                                    ]
                                },
                                legend: {
                                    position: 'bottom',

                                    labels: {

                                        // This more specific font property overrides the global property
                                        font: {
                                            size: 18,
                                            family: 'Arial',
                                        },

                                    }
                                },
                                title: {
                                    display: false,
                                    text: 'Embalse',
                                    fullSize: true,
                                    font: {
                                        size: 30
                                    }
                                },

                            },
                            scales: {

                                x: {
                                    title: {
                                        display: false,
                                        text: 'Año <?php echo date('Y'); ?>',
                                        font: {
                                            size: 18
                                        },
                                    },
                                    stacked: true,
                                    type: 'time',
                                    time: {
                                        unit: 'month'
                                    },
                                    min: '<?php echo $anio; ?>-01-01',
                                    max: '<?php echo $anio; ?>-12-31',

                                    ticks: {
                                        callback: (value, index, ticks) => {

                                            const date = new Date(value);
                                            //console.log(date);
                                            const f = new Intl.DateTimeFormat('es-ES', {
                                                month: 'short',

                                            }).format(value);
                                            str = f.charAt(0).toUpperCase();
                                            return str + f.slice(1);
                                        },
                                        font: {
                                            size: 18,
                                            family: 'Arial',
                                        },
                                    },
                                    grid: {
                                        color: function(context) {},
                                    },

                                },

                                y: {
                                    title: {
                                        display: true,
                                        text: 'Cota (m.s.n.m)',
                                        font: {
                                            size: 16,
                                            family: 'Arial',
                                            weight: 'bold',
                                        },
                                    },
                                    min: <?php if ($min < $embalses[0]["cota_min"]) {
                                                echo $min;
                                            } else {
                                                if ($embalses[0]["cota_min"] - 2 < 0) {
                                                    echo 0;
                                                } else {
                                                    echo $embalses[0]["cota_min"] - 2;
                                                }
                                            }; ?>,
                                    max: <?php if ($max > $embalses[0]["cota_max"]) {
                                                echo $max + 2;
                                            } else {
                                                echo $embalses[0]["cota_max"] + 5;
                                            }; ?>,
                                    border: {
                                        display: false,
                                    },
                                    ticks: {
                                        font: {
                                            size: 14,
                                            family: 'Arial',
                                        },
                                        callback: function(valor, index, valores) {
                                            return valor.toLocaleString("de-DE");
                                        },
                                    },

                                },


                            },
                        },
                        plugins: [arbitra, point],

                    });

                }

            });
        </script>
<?php
    } else {
        echo '<div class="row justify-content-center" style="height:600px;min-width:1000px"><div class="d-flex justify-content-center align-items-center"><div><h5 class="align-middle font-weight-bolder align-middle">No hay datos registrados en el embalse</h5></div></div></div>';
    }
} else {
    echo '<div class="row justify-content-center" style="height:600px;min-width:1000px"><div class="d-flex justify-content-center align-items-center"><div><h5 class="align-middle font-weight-bolder align-middle">Error:Embalse inactivo o inexistente</h5></div></div></div>';
}



closeConection($conn);
?>