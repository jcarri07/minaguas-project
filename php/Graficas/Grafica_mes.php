<script src="./assets/js/Chart.js"></script>
<script src="./assets/js/date-fns.js"></script>
<!-- <<script src="../../assets/js/jquery/jquery.min.js"></script> -->
<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");

$id = $_POST['id_embalse'];
$tipo = $_POST['tipo'];
$mes = $_POST['mes'];
$array_aux = explode("-", $mes);
$anio = $array_aux["0"];
$me = $array_aux["1"];
$periodo = $_POST['periodo'];
$y = $anio;
$ver = $_POST['ver'];

$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo' AND id_embalse = '$id';");
$count = mysqli_num_rows($r);
if ($count >= 1) {

    if ($tipo == "bar") {
        $aux = "SELECT id_registro, d.fecha, (SELECT MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND id_embalse = d.id_embalse) AS hora, (SELECT cota_actual 
    FROM datos_embalse 
    WHERE id_embalse = d.id_embalse AND fecha = d.fecha AND hora = (select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND cota_actual <> 0 AND id_embalse = d.id_embalse) ORDER BY cota_actual DESC LIMIT 1) AS cota_actual
FROM datos_embalse d, embalses e 
WHERE e.id_embalse = d.id_embalse AND e.estatus = 'activo' AND d.estatus = 'activo' AND d.id_embalse = '$id' AND YEAR(d.fecha) = '$y' AND MONTH(d.fecha) = '$me'
GROUP BY d.fecha 
ORDER BY d.fecha ASC;";
    }
    if ($tipo == "line") {
        $aux = "SELECT id_registro, d.fecha,d.hora, cota_actual
FROM datos_embalse d, embalses e
WHERE e.id_embalse = d.id_embalse AND e.estatus = 'activo' AND d.estatus = 'activo' AND d.id_embalse = '$id'  AND cota_actual <> 0 AND MONTH(d.fecha) = '$me' AND YEAR(d.fecha) >= '$periodo'
ORDER BY d.fecha ASC;";
    }
    $bati = new Batimetria($id, $conn);
    $batimetria = $bati->getBatimetria();
    $res = mysqli_query($conn, $aux);

    $count = mysqli_num_rows($res);
    $datos_embalses = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

?>
    <canvas id="chart" class="border border-radius-lg"></canvas>
    <script>
        $(document).ready(function() {

            const point = {
                id: 'point',
                afterDatasetsDraw: function(chart, arg, options) {
                    const {
                        ctx
                    } = chart;
                    const dataset = chart.data.datasets[0];
                    const meta = chart.getDatasetMeta(0);

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
                                $pivote = $pivote = date("Y", strtotime($datos_embalses[0]['fecha']));

                                $fech = $anio;
                                while (($pivote <= $fech)) {

                                    $aux = 0;
                                    while ($aux < count($datos_embalses)) {
                                        if ($datos_embalses[$aux]["cota_actual"] != NULL && $fech == date("Y", strtotime($datos_embalses[$aux]['fecha']))) { 
                                            echo "{label:'Volumen del año " . $fech . "',pointRadius: 0,data: [";
                                            $aux = 0;
                                            break;
                                         }else{$aux++;}
                                    } 

                                    $j = 0;


                                    while ($j < count($datos_embalses)) {

                                        if ($datos_embalses[$j]["cota_actual"] != NULL && $fech == date("Y", strtotime($datos_embalses[$j]['fecha']))) { ?> {
                                                x: '<?php echo $anio . "-" . date("m-d", strtotime($datos_embalses[$j]['fecha'])) . " " . $datos_embalses[$j]["hora"];  ?>',
                                                y: <?php echo $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];  ?>
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
                                    if($aux==0){
                                        echo "],";
                                        if($fech == $anio){
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
                                        };
                                    $fech--;
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
                                        color: 'red',
                                        h: -15,
                                    },
                                    {
                                        yvalue: <?php echo round($bati->getByCota($anio, $embalses[0]["cota_nor"])[1], 2); ?>,
                                        cota: "Volumen normal",
                                        color: 'green',
                                        h: 15,

                                    },
                                    {
                                        yvalue: <?php echo round($bati->getByCota($anio, $embalses[0]["cota_max"])[1], 2); ?>,
                                        cota: "Volumen máximo",
                                        color: 'blue',
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
                                    text: 'Año <?php echo $anio; ?>',
                                    font: {
                                        size: 18
                                    },
                                },
                                type: 'time',
                                time: {
                                    unit: 'day'
                                },
                                min: '<?php echo $anio . "-" . $me . "-01" ?>',
                                max: '<?php echo $anio . "-" . $me . "-" . date('t', strtotime("$anio-$me-01")) ?>',

                                ticks: {
                                    callback: (value, index, ticks) => {

                                        const date = new Date(value);
                                        //console.log(date);
                                        return new Intl.DateTimeFormat('es-ES', {
                                            day: 'numeric',

                                        }).format(value);
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
                                            echo round($bati->getByCota($anio, $max)[1] + 10,0);
                                        } else {
                                            echo round($bati->getByCota($anio, $embalses[0]["cota_max"])[1] + 10,0);
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
                                $pivote = $pivote = date("Y", strtotime($datos_embalses[0]['fecha']));

                                $fech = $anio;
                                while (($pivote <= $fech)) {

                                    $aux = 0;
                                    while ($aux < count($datos_embalses)) {
                                        if ($datos_embalses[$aux]["cota_actual"] != NULL && $fech == date("Y", strtotime($datos_embalses[$aux]['fecha']))) { 
                                            echo "{label:'Cota del año " . $fech . "',pointRadius: 0,data: [";
                                            $aux = 0;
                                            break;
                                         }else{$aux++;}
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
                                    if($aux==0){
                                        echo "],";
                                        if($fech == $anio){
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
                                        };
                                    $fech--;
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
                                        color: 'red',
                                        h: -15,
                                    },
                                    {
                                        yvalue: <?php echo $embalses[0]["cota_nor"]; ?>,
                                        cota: "Cota normal",
                                        color: 'green',
                                        h: 15,

                                    },
                                    {
                                        yvalue: <?php echo $embalses[0]["cota_max"]; ?>,
                                        cota: "Cota maxima",
                                        color: 'blue',
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
                                    text: 'Año <?php echo $anio; ?>',
                                    font: {
                                        size: 18
                                    },
                                },
                                type: 'time',
                                time: {
                                    unit: 'day'
                                },
                                min: '<?php echo $anio . "-" . $me . "-01" ?>',
                                max: '<?php echo $anio . "-" . $me . "-" . date('t', strtotime("$anio-$me-01")) ?>',

                                ticks: {
                                    callback: (value, index, ticks) => {

                                        const date = new Date(value);
                                        //console.log(date);
                                        return new Intl.DateTimeFormat('es-ES', {
                                            day: 'numeric',

                                        }).format(value);
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
                                            if (($embalses[0]["cota_min"] - 2) < 0) {
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

            };

        })
    </script>
<?php

} else {
    echo '<div class="row justify-content-center"><div class="col-6 text-center"><h5 class="font-weight-bolder">Error:Embalse inactivo o inexistente</h5></div></div>';
}
closeConection($conn);
?>