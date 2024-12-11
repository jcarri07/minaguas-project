<!-- <script src="./assets/js/Chart.js"></script> -->
<script src="./assets/js/date-fns.js"></script>
<!-- <script src="./assets/js/jquery/jquery.min.js"></script> -->
<script src="./assets/js/chartjs-plugin-datalabels@2.js"></script>
<?php
require_once '../Conexion.php';
require_once '../batimetria.php';
$volumen_fechas = array(
    "0" => 0,
    "1" => 0,
    "2" => 0,
    "3" => 0,
);
// Obtener la fecha actual
$fechaActual = new DateTime();
$datos = [];
$div = [];
$array = [];
// Restarle 15 días
$fechaActual->modify('-7 days');
// Obtener un string de la fecha en formato deseado (por ejemplo, 'Y-m-d' para año-mes-día)
$fecha_dia = $fechaActual->format('Y-m-d');

// Obtener la fecha actual
$fechaActual = new DateTime();
// Restarle 15 días
$fechaActual->modify('-1 years');
// Obtener un string de la fecha en formato deseado (por ejemplo, 'Y-m-d' para año-mes-día)
$fecha_anio = $fechaActual->format('Y-m-d');

// $queryInameh = mysqli_query($conn, "SELECT nombre_config, configuracion FROM configuraciones WHERE nombre_config = 'fecha_sequia' OR nombre_config = 'fecha_lluvia' ORDER BY id_config ASC;");
// $fechas = mysqli_fetch_all($queryInameh, MYSQLI_ASSOC);
// $fecha1 = $fechas[0]['configuracion'];
// $fecha2 = $fechas[1]['configuracion'];
//$anio = date('Y', strtotime($fecha1));
$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo'ORDER BY nombre_embalse ASC;");
$count = mysqli_num_rows($r);
if ($count >= 1) {

    //     $almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse,MAX(d.fecha) AS fecha,
    //   e.nombre_embalse, (SELECT cota_actual 
    //        FROM datos_embalse h 
    //        WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT da.fecha FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 ORDER BY da.fecha DESC LIMIT 1) AND h.estatus = 'activo' AND cota_actual <> 0 ORDER BY h.hora DESC LIMIT 1) AS cota_actual
    //   FROM embalses e
    //   LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.cota_actual <> 0
    //   WHERE e.estatus = 'activo'
    //   GROUP BY id_embalse 
    //   ORDER BY id_embalse ASC;");

    $condiciones_dias = mysqli_query($conn, " SELECT e.id_embalse,MAX(d.fecha) AS fecha,
  e.nombre_embalse, (SELECT cota_actual 
       FROM datos_embalse h 
       WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT da.fecha FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.fecha <= '$fecha_dia' AND da.estatus = 'activo' AND da.cota_actual <> 0 ORDER BY da.fecha DESC LIMIT 1) AND h.estatus = 'activo' AND cota_actual <> 0 ORDER BY h.hora DESC LIMIT 1) AS cota_actual
  FROM embalses e
  LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha_dia' AND d.cota_actual <> 0
  WHERE e.estatus = 'activo'
  GROUP BY id_embalse 
  ORDER BY id_embalse ASC;");

    $condiciones_anio = mysqli_query($conn, "SELECT e.id_embalse,MAX(d.fecha) AS fecha,
  e.nombre_embalse, (SELECT cota_actual 
       FROM datos_embalse h 
       WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT da.fecha FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.fecha <= '$fecha_anio' AND da.estatus = 'activo' AND da.cota_actual <> 0 ORDER BY da.fecha DESC LIMIT 1) AND h.estatus = 'activo' AND cota_actual <> 0 ORDER BY h.hora DESC LIMIT 1) AS cota_actual
  FROM embalses e
  LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha_anio' AND d.cota_actual <> 0
  WHERE e.estatus = 'activo'
  GROUP BY id_embalse 
  ORDER BY id_embalse ASC;");

    $datos_embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);
    $volumen_dias = mysqli_fetch_all($condiciones_dias, MYSQLI_ASSOC);
    $volumen_anio = mysqli_fetch_all($condiciones_anio, MYSQLI_ASSOC);

    $j = 0;

    while ($j < count($datos_embalses)) {
        $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
        //$min = $bati->volumenMinimo();
        //$max = $bati->volumenMaximo();
        //$nor = $bati->volumenNormal();
        //if ($datos_embalses[$j]["cota_actual"] != NULL) {

        // $x = $bati->getByCota(date('Y', strtotime($datos_embalses[$j]["fecha"])), $datos_embalses[$j]["cota_actual"])[1];

        // //$bati->getByCota($anio, $datos_embalses[$j]["cota_max"])[1]-$bati->getByCota($anio, $datos_embalses[$j]["cota_min"])[1];
        // if (($x - $min) <= 0) {
        //     $sum = 0;
        // } else {
        //     $sum = $x - $min;
        // }
        $min = $bati->volumenMinimo();
        $nor = $bati->volumenNormal();
        $d = ($nor - $min) > 0 ? ($nor - $min) : 1;
        array_push($div,$d);

        $max = ($nor - $min) > 0 ? ($nor - $min) : 0;
        array_push($array, round($max, 3));

        $l = $bati->volumenActualDisponible();
        array_push($datos, $l);

        $volumen_fechas[1] += $l;
        //}
        $volumen_fechas[0] += $bati->volumenDisponible();
        if ($volumen_dias[$j]['cota_actual'] != NULL) {
            $volumen_fechas[2] += ($bati->volumenDisponibleByCota(date('Y', strtotime($volumen_dias[$j]["fecha"])), $volumen_dias[$j]["cota_actual"]));
        }
        if ($volumen_anio[$j]['cota_actual'] != NULL) {
            $volumen_fechas[3] += ($bati->volumenDisponibleByCota(date('Y', strtotime($volumen_anio[$j]["fecha"])), $volumen_anio[$j]["cota_actual"]));
        }
        $j++;
    };
?>

    <canvas id="barra1" class="border border-radius-lg"></canvas>
    <!-- <canvas id="chart"></canvas> -->

    <script>
        $("#cont").html('<canvas id="chart"></canvas>');

        $(document).ready(function() {
            arbi = {
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
                        const fontSize = 14;
                        const fontStyle = 'normal';
                        const fontFamily = 'Arial';
                        ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                        ctx.beginPath();
                        ctx.lineWidth = 1;
                        ctx.moveTo(left, y.getPixelForValue(yvalue));
                        ctx.lineTo(right, y.getPixelForValue(yvalue));
                        ctx.strokeStyle = color; // Cambiar color según tus preferencias
                        ctx.fillText(<?php echo round($volumen_fechas[1] * 100 / $volumen_fechas[0], 2) ?>.toLocaleString("de-DE") + "%", right * 1.65 / 3, y.getPixelForValue(yvalue) + h);
                        //ctx.stroke();
                    });
                    ctx.restore();
                },
                afterDatasetsDraw: function(chart, args, plugins) {
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
            let cha1 = new Chart(barra1, {
                type: 'bar',
                title: 'grafica',

                data: {

                    datasets: [
                        <?php

                        echo '{
                            
                            label:"Volumen Util Actual(VTD)",
                            data:{Volumen:' . round($volumen_fechas[1], 2) . '},';
                        echo "backgroundColor:'#2e75b6',
                        borderColor:'#2e75b6',
                        borderWidth:2},";
                        echo '{
                            
                            label:"Volumen Util Total(VUT)",
                            data:{Volumen:' . round($volumen_fechas[0], 2) . '},';

                        echo "backgroundColor:'#9fe3a3',
                        borderColor:'#2e75b6',
                        borderWidth:2}";
                        ?>
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        intersect: true,
                        axis: 'x',
                    },
                    layout: {
                        padding: 10,
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            display: false,
                            labels: {

                                // This more specific font property overrides the global property
                                font: {

                                    size: 12,
                                },
                            }
                        },
                        title: {
                            display: false,
                            text: 'Embalse',
                            fullSize: true,
                            font: {
                                size: 20
                            }
                        },
                        arbitra: {
                            lines: [{
                                yvalue: <?php echo round($volumen_fechas[1], 2) ?>,
                                cota: "",
                                color: 'black',
                                h: -5,
                            }, ]
                        },
                    },
                    scales: {
                        x: {
                            stacked: true,
                            labels: ["Volumen"],
                            ticks: {
                                display: true,
                                font: {

                                    size: 16,
                                    family: 'Arial',
                                },
                            },
                        },
                        y: {
                            stacked: false,
                            title: {
                                display: true,
                                text: 'Volumen (Hm³)',
                                font: {

                                    size: 14,
                                    family: 'Arial',
                                    weight: 'bold',
                                },

                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    family: 'Arial',
                                },
                                callback: function(valor, index, valores) {
                                    return valor.toLocaleString("de-DE");
                                },
                            },
                        },
                    },
                },
                plugins: [arbi],
            });
            $("#contenedor-2").html('<?php
                                        $valor = $volumen_fechas[2] != 0 ? 100 * (($volumen_fechas[1] - $volumen_fechas[2]) / $volumen_fechas[2]) : 0;
                                        $valorFormat = number_format(($valor), 2, ",", ".");
                                        if ($valor > 0) {

                                            echo '<h1 class="align-items-center"><i class="fa fa-arrow-up" style="padding-right: 10px; color: green;"></i></div><span class="" style="font-size:50px !important">' . $valorFormat . '%</span></h1>';
                                        };
                                        if ($valor == 0) {

                                            echo '<h1 class="align-items-center"><i class="fa fa-minus" style="padding-right: 10px; color: gray;"></i></div><span class="" style="font-size:50px !important">' . $valorFormat . '%</span></h1>';
                                        };
                                        if ($valor < 0) {

                                            echo '<h1 class="align-items-center"><i class="fa fa-arrow-down" style="padding-right: 10px; color: red;"></i></div><span class="" style="font-size:50px !important">' . $valorFormat . '%</span></h1>';
                                        };

                                        ?>');
            $("#contenedor-3").html('<?php
                                        $valor = $volumen_fechas[3] != 0 ? 100 * (($volumen_fechas[1] - $volumen_fechas[3]) / $volumen_fechas[3]) : 0;
                                        $valorFormat = number_format(($valor), 2, ",", ".");
                                        if ($valor > 0) {

                                            echo '<h1 class="align-items-center"><i class="fa fa-arrow-up" style="padding-right: 10px; color: green;"></i></div><span class="" style="font-size:50px !important">' . $valorFormat . '%</span></h1>';
                                        };
                                        if ($valor == 0) {

                                            echo '<h1 class="align-items-center"><i class="fa fa-minus" style="padding-right: 10px; color: gray;"></i></div><span class="" style="font-size:50px !important">' . $valorFormat . '%</span></h1>';
                                        };
                                        if ($valor < 0) {

                                            echo '<h1 class="align-items-center"><i class="fa fa-arrow-down" style="padding-right: 10px; color: red;"></i></div><span class="" style="font-size:50px !important">' . $valorFormat . '%</span></h1>';
                                        };

                                        ?>');


            <?php //$bati = new Batimetria($datos_embalses[1]["id_embalse"], $conn);
            // $batimetria = $bati->getBatimetria();
            // $x = $bati->volumenActualDisponible();
            //$x = $bati->getByCota($anio, $datos_embalses[1]["cota_actual"])[1];
            // echo "console.log('volúmen:" . $x . ",cota:" . $datos_embalses[1]["cota_actual"] . "');";
            ?>

            <?php

            $j = 0;
            $sum = [];
            $backgroundColors = [];
            $labels = [];
            $dataPoints = [];
            
            while ($j < count($datos_embalses)) {

                // $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
                // $batimetria = $bati->getBatimetria();
                //$x = $datos[$j]; //$bati->volumenActualDisponible(); //$bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];
                //$min = $bati->volumenMinimo();
                //$max = $bati->volumenMaximo();
                //$nor = $bati->volumenNormal();
                // if ($datos_embalses[$j]["cota_actual"] != NULL) {

                $sum[$j] = $datos[$j];
                // if (($x - $min) <= 0) {
                //     $sum[$j] = 0;
                // } else {
                //     $sum[$j] = $x - $min;
                // }

                //$div = ($nor - $min) > 0 ? ($nor - $min) : 1;
                if ($div[$j] != 1) {
                    $percentage = (abs($sum[$j]) * (100 / $div[$j]));
                } else {
                    $percentage = 0;
                }

                // Determinar el color basado en el porcentaje
                if ($sum[$j] == 0 || $percentage < 30) {
                    $backgroundColors[] = "'#fd0200'"; // rojo
                };
                if ($percentage >= 30 && $percentage < 60) {
                    $backgroundColors[] = "'#72dffd'"; // anaranjado
                };
                if ($percentage >= 60 && $percentage < 90) {
                    $backgroundColors[] = "'#0066eb'"; // verde
                };
                if ($percentage >= 90 && $percentage <= 100) {
                    $backgroundColors[] = "'#3ba500'"; // azul
                };
                if ($percentage >= 100) {
                    $backgroundColors[] = "'#55fe01'"; // color extra (verde claro)
                }

                // Añadir etiqueta
                $labels[] = "'Embalse " . $datos_embalses[$j]["nombre_embalse"] . " (" . round((abs($sum[$j]) * (100 / $div[$j])), 0) . "%)'";

                // Añadir el punto de datos
                $dataPoints[] = "{ y: '" . $datos_embalses[$j]["nombre_embalse"] . "', x: " . $sum[$j] . " }";
                // } else {
                //     // Caso de cota_actual nulo
                //     $backgroundColors[] = "'#fd0200'"; // color por defecto (rojo)
                //     $labels[] = "'Embalse " . $embalses[$j]["nombre_embalse"] . " (0%)'";
                //     $dataPoints[] = "{ y: '" . $datos_embalses[$j]["nombre_embalse"] . "', x: 0 }";
                // }


                // $j++;
                // if ($j < count($datos_embalses)) {
                //     echo ",";
                // };
                $j++;
            }
            ?>
            const maxValues = [<?php echo implode(", ", $array); ?>];

            let cha = new Chart(chart, {
                type: 'bar',
                title: 'grafica',
                label: 'Embalses',
                data: {
                    datasets: [{
                        backgroundColor: [
                            <?php




                            // Convertir los arrays en cadenas separadas por comas
                            echo implode(", ", $backgroundColors);
                            ?>
                        ],

                        data: [
                            <?php
                            // Convertir puntos de datos en una cadena separada por comas
                            echo implode(", ", $dataPoints);
                            ?>
                        ],
                        borderWidth: 1,
                        categoryPercentage: 1,
                        barPercentage: 0.9
                    }, ],

                },

                options: {

                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    interaction: {
                        intersect: false,
                        axis: 'y',
                    },
                    elements: {
                        borderWidth: 1,
                    },
                    plugins: {

                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw;
                                    const labelName = context.label; // Muestra el nombre de la etiqueta única
                                    return labelName + ': ' + (Math.round(value.x * 100) / 100).toLocaleString("de-DE");
                                }
                            }
                        }, //Aqui van los cambios de minaguas nuevos


                        legend: {
                            position: 'bottom',
                            align: 'start',
                            display: false,
                            labels: {

                                // This more specific font property overrides the global property
                                font: {
                                    size: 10
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
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            formatter: function(value, context) {
                                return (Math.round(value.x * 100) / 100).toLocaleString("de-DE");
                            },
                            labels: {
                                title: {
                                    font: {
                                        weight: 'bold',
                                        family: 'Arial',
                                    },
                                    color: function(context) {
                                        // Obtén el valor actual del dato y su valor máximo correspondiente
                                        const value = context.dataset.data[context.dataIndex].x;
                                        const maxValue = maxValues[context.dataIndex];
                                        //console.log(maxValue);
                                        // Calcula el porcentaje

                                        if (maxValue == 0) {
                                            percentage = 0;
                                        } else {
                                            percentage = value * 100 / maxValue;
                                        }

                                        // Si el porcentaje es menor que 30, cambia el color a rojo
                                        return percentage <= 30 ? '#fd0200' : 'black';
                                    },
                                },
                            },
                        },

                    },
                    scales: {

                        x: {

                            title: {
                                display: true,
                                text: 'Volumen (Hm³)',

                                font: {
                                    size: 16
                                },
                            },
                            ticks: {

                                font: {
                                    size: 14
                                },
                                callback: function(valor, index, valores) {
                                    return valor.toLocaleString("de-DE");
                                },
                            },

                        },
                        y: {


                            border: {
                                display: false,
                            },
                            ticks: {
                                font: {
                                    size: 13
                                },

                            },

                        },


                    },
                },
                plugins: [ChartDataLabels],
            });
        });
    </script>
<?php };
closeConection($conn); ?>