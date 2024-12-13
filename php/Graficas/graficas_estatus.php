<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

$lista = array(
    "0" => 0,
    "1" => 0,
    "2" => 0,
    "3" => 0,
);
$condicion = array(
    "0" => 0,
    "1" => 0,
    "2" => 0,
    "3" => 0,
);
$colores = array(
    "0" => "#9dc3e6",
    "1" => "#5b9bd5",
    "2" => "#2e75b6",
    "3" => "#4679a7",
);

$colores2 = array(
    "0" => "#548235",
    "1" => "#70ad47",
    "2" => "#a9d18e",
    "3" => "#e2f0d9",
);

$colores3 = array(
    "0" => "#ff0000",
    "1" => "#ffaa00",
    "2" => "#ffff00",
    "3" => "#70ad47",
);

$volumen_fechas = array(
    "0" => 0,
    "1" => 0,
    "2" => 0,
    "3" => 0,
    "4" => 0,
    "5" => 0,
);
$suma_extracciones;
$evaporacion = 0;
//$calculo_evaporacion = ($area_embalse * ($evaporacion/1000)*0.8*30.5)/1000000;
$filtracion = 0;
//$calculo_filtracion = ($x*($filtracion(%)/100))/30.5
// Obtener la fecha actual
$fechaActual = new DateTime();
// Restarle 15 días
$fechaActual->modify('-15 days');
// Obtener un string de la fecha en formato deseado (por ejemplo, 'Y-m-d' para año-mes-día)
$fechaFormateada1 = $fechaActual->format('Y-m-d');

// Obtener la fecha actual
$fechaActual = new DateTime();
// Restarle 15 días
$fechaActual->modify('-7 days');
// Obtener un string de la fecha en formato deseado (por ejemplo, 'Y-m-d' para año-mes-día)
$fechaFormateada2 = $fechaActual->format('Y-m-d');

$valores = $_GET["valores"];

$array_excluidos = 0;
$embalses_excluidos = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'consumo_humano';");
if (mysqli_num_rows($embalses_excluidos) > 0) {
    $array_excluidos = mysqli_fetch_assoc($embalses_excluidos)['configuracion'];
}

$queryInameh = mysqli_query($conn, "SELECT nombre_config, configuracion FROM configuraciones WHERE nombre_config = 'fecha_sequia' OR nombre_config = 'fecha_lluvia' ORDER BY id_config ASC;");
$fechas = mysqli_fetch_all($queryInameh, MYSQLI_ASSOC);
$fecha1 = $fechas[0]['configuracion'];
$fecha2 = $fechas[1]['configuracion'];
$anio = date('Y', strtotime($fecha1));
$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo' AND FIND_IN_SET('1', uso_actual);");
$count = mysqli_num_rows($r);
if ($count >= 1) {

    $almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse,operador,region,nombre_embalse,(SELECT da.fecha FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 ORDER BY da.fecha DESC LIMIT 1) AS fech,               (
        SELECT SUM(extraccion)
        FROM detalles_extraccion dex, codigo_extraccion ce
        WHERE ce.id = dex.id_codigo_extraccion AND dex.id_registro = (SELECT id_registro
           FROM datos_embalse h 
           WHERE h.id_embalse = d.id_embalse AND h.estatus = 'activo' AND h.fecha = fech ORDER BY h.hora DESC LIMIT 1) AND (ce.id_tipo_codigo_extraccion = '1' OR ce.id_tipo_codigo_extraccion = '2' OR ce.id_tipo_codigo_extraccion = '3' OR ce.id_tipo_codigo_extraccion = '4') 
      ) AS 'extraccion',
      e.nombre_embalse, (SELECT cota_actual 
           FROM datos_embalse h 
           WHERE h.id_embalse = d.id_embalse AND h.estatus = 'activo' AND h.fecha = fech ORDER BY h.hora DESC LIMIT 1) AS cota_actual
      FROM embalses e
      LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
      WHERE e.estatus = 'activo' AND FIND_IN_SET('1', e.uso_actual)
      GROUP BY id_embalse 
      ORDER BY id_embalse ASC;");

   $condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse,operador,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha1' AND h.estatus = 'activo' AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha1' AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
    FROM embalses e
    LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha1'
    WHERE e.estatus = 'activo' AND FIND_IN_SET('1', e.uso_actual)
    GROUP BY id_embalse;");

$condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse,operador,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha2' AND h.estatus = 'activo' AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha2' AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
    FROM embalses e
    LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha2'
    WHERE e.estatus = 'activo' AND FIND_IN_SET('1', e.uso_actual)
    GROUP BY id_embalse;");

    $condiciones_actuales3 = mysqli_query($conn, "SELECT e.id_embalse,operador,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT da.fecha FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 AND da.fecha <= '$fechaFormateada1' ORDER BY da.fecha DESC LIMIT 1) AND cota_actual <> 0 ORDER BY h.hora DESC LIMIT 1) AS cota_actual 
 FROM embalses e
 LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fechaFormateada1'
 WHERE e.estatus = 'activo' AND FIND_IN_SET('1', e.uso_actual)
 GROUP BY id_embalse;");

    $condiciones_actuales4 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT da.fecha FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 AND da.fecha <= '$fechaFormateada2' ORDER BY da.fecha DESC LIMIT 1) AND cota_actual <> 0 ORDER BY h.hora DESC LIMIT 1) AS cota_actual 
 FROM embalses e
 LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fechaFormateada2'
 WHERE e.estatus = 'activo' AND FIND_IN_SET('1', e.uso_actual)
 GROUP BY id_embalse;");

    $evaporacionFiltracion = [];
    $queryEvaporacionFiltracion = mysqli_query($conn, "SELECT * FROM configuraciones WHERE nombre_config = 'evap_filt';");
    if (mysqli_num_rows($queryEvaporacionFiltracion) > 0) {
        $evaporacionFiltracion = json_decode(mysqli_fetch_assoc($queryEvaporacionFiltracion)['configuracion'], true);
    }

    $datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);
    $volumen_primer_periodo = mysqli_fetch_all($condiciones_actuales1, MYSQLI_ASSOC);
    $volumen_segundo_periodo = mysqli_fetch_all($condiciones_actuales2, MYSQLI_ASSOC);
    $volumen_quince = mysqli_fetch_all($condiciones_actuales3, MYSQLI_ASSOC);
    $volumen_siete = mysqli_fetch_all($condiciones_actuales4, MYSQLI_ASSOC);

    $embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

    $j = 0;

    while ($j < count($datos_embalses)) {
        $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
        if ($datos_embalses[$j]["cota_actual"] != NULL && $datos_embalses[$j]["fech"] != NULL) {

            $x = $bati->getByCota(date('Y', strtotime($datos_embalses[$j]["fech"])), $datos_embalses[$j]["cota_actual"])[1];
            $min = $bati->volumenMinimo();
            //$max = $bati->volumenMaximo();
            $nor = $bati->volumenNormal();
            //$bati->getByCota($anio, $datos_embalses[$j]["cota_max"])[1]-$bati->getByCota($anio, $datos_embalses[$j]["cota_min"])[1];
            if (($x - $min) <= 0) {
                $sum = 0;
            } else {
                $sum = $x - $min;
                $volumen_fechas[1] += $sum;
            }
            $div = ($nor - $min) != 0 ? ($nor - $min) : 1;
            if ($x == 0 || ((abs(($sum)) * (100 / $div)) >= 0 && (abs(($sum)) * (100 / $div)) < 30)) {
                $lista[0]++;
            };
            if ((abs(($sum)) * (100 / $div)) >= 30 && (abs(($sum)) * (100 / $div)) < 60) {
                $lista[1]++;
            };
            if ((abs(($sum)) * (100 / $div)) >= 60 && (abs(($sum)) * (100 / $div)) < 90) {
                $lista[2]++;
            };
            if ((abs(($sum)) * (100 / $div)) >= 90 || (abs(($sum)) * (100 / $div)) >= 100) {
                $lista[3]++;
            };

            //cuenta de dias//
            if ($datos_embalses[$j]['extraccion'] != NULL) {
                $dat = $datos_embalses[$j]['extraccion'] != 0 ? $datos_embalses[$j]['extraccion'] : 1;
                if ($dat == 1) {
                    $suma_extracciones[] = 0;
                } else {
                    if (array_key_exists($datos_embalses[$j]["id_embalse"], $evaporacionFiltracion)) {
                        $evaporacio = $evaporacionFiltracion[$datos_embalses[$j]["id_embalse"]]["evaporacion"];
                        $filtracion = $evaporacionFiltracion[$datos_embalses[$j]["id_embalse"]]["filtracion"];
                        $suma_extracciones[] = $bati->abastecimiento($datos_embalses[$j]["extraccion"], $evaporacio, $filtracion);
                    } else {
                        $suma_extracciones[] = $bati->abastecimiento($datos_embalses[$j]["extraccion"]);
                    }
                    // $suma_extracciones[] = round(($bati->volumenActualDisponible() * 1000 / (($dat + $evaporacion + $filtracion))) / 30);
                }
            } else {
                $suma_extracciones[] = 0;
            }
            //----//

        } else {

            $lista[0]++;
            $suma_extracciones[] = 0;
        };





        $volumen_fechas[0] += $bati->volumenDisponible();
        if ($volumen_primer_periodo[$j]['cota_actual'] != NULL) {
            $volumen_fechas[2] += ($bati->volumenDisponibleByCota(date('Y', strtotime($volumen_primer_periodo[$j]["fecha"])), $volumen_primer_periodo[$j]["cota_actual"]));
        }
        if ($volumen_segundo_periodo[$j]['cota_actual'] != NULL) {
            $volumen_fechas[3] += ($bati->volumenDisponibleByCota(date('Y', strtotime($volumen_segundo_periodo[$j]["fecha"])), $volumen_segundo_periodo[$j]["cota_actual"]));
        }
        if ($volumen_quince[$j]['cota_actual'] != NULL) {
            $volumen_fechas[4] += ($bati->volumenDisponibleByCota(date('Y', strtotime($volumen_quince[$j]["fecha"])), $volumen_quince[$j]["cota_actual"]));
        }
        if ($volumen_siete[$j]['cota_actual'] != NULL) {
            $volumen_fechas[5] += ($bati->volumenDisponibleByCota(date('Y', strtotime($volumen_siete[$j]["fecha"])), $volumen_siete[$j]["cota_actual"]));
        }
        $j++;
    };

    $j = 0;
    while ($j < count($suma_extracciones)) {
        if ($suma_extracciones[$j] <= 4 && $suma_extracciones[$j] >= 0) {
            $condicion[0]++;
        };
        if ($suma_extracciones[$j] > 4 && $suma_extracciones[$j] <= 8) {
            $condicion[1]++;
        };
        if ($suma_extracciones[$j] > 8 && $suma_extracciones[$j] <= 12) {
            $condicion[2]++;
        };
        if ($suma_extracciones[$j] > 12) {
            $condicion[3]++;
        };
        $j++;
    };




?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="referrer" content="strict-origin-when-cross-origin" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="../../assets/img/logos/cropped-mminaguas.webp">
        <script src="../../assets/js/Chart.js"></script>
        <script src="../../assets/js/chartjs-plugin-datalabels@2.js"></script>
        <!--script src="../../assets/js/date-fns.js"></script-->
        <script src="../../assets/js/date-fns.js"></script>

        <script src="../../assets/js/jquery/jquery.min.js"></script>
        <script src="../../assets/js/html2canvas.min.js"></script>
        <link href="../../assets/css/style-spinner.css" rel="stylesheet" />
        <link id="pagestyle" href="../../assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />

        <title>Document</title>
    </head>

    <body style="height:800px">
        <!--div style=" width: 1200px;"-->
        <div>

            <div style="width:1100px !important; height:900px;position:absolute; top:-100%;z-index: -1;"><canvas id="chart" class="border border-radius-lg"></canvas></div>
            <div style="width:900px !important; height:900px;position:absolute; top:-100%;z-index: -1;"><canvas id="barra1" class="border border-radius-lg"></canvas></div>
            <div style="width:900px !important; height:900px;position:absolute; top:-100%;z-index: -1;"><canvas id="barra2" class="border border-radius-lg"></canvas></div>
            <div style="width:1040px !important; height:1240px;position:absolute; top:-100%;z-index: -1;"><canvas id="abastecimiento" class="border border-radius-lg"></canvas></div>

        </div>
        <div class="row justify-content-center h-100" style="background-color: white;">
            <div class="col-7">
                <div class="loaderPDF " style="height: 90% !important;align-items:end !important;">

                    <div class="lds-dual-ring"></div>

                </div>
            </div>
            <div class="col-7">
                <div class="progress">
                    <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
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
                        color
                    } = line;

                    ctx.beginPath();
                    ctx.lineWidth = 1.5;
                    ctx.moveTo(left, y.getPixelForValue(yvalue));
                    ctx.lineTo(right, y.getPixelForValue(yvalue));
                    ctx.strokeStyle = color; // Cambiar color según tus preferencias
                    //ctx.fillText(cota + ": " + yvalue.toLocaleString("de-DE") + " (Hm³)", right * 4.2 / 6, y.getPixelForValue(yvalue) + h);
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

        const scaleChart = {
            id: 'scaleChart',
            beforeDatasetsDraw(chart, args, plugins) {
                const {
                    ctx
                } = chart;
                ctx.save();
                if (!chart.originalOuterRadius) {
                    chart.originalOuterRadius = chart.getDatasetMeta(0).data[0].
                    outerRadius;
                }
                console.log(chart.originalOuterRadius);
                //console.log(chart.getDatasetMeta(0).data[0].outerRadius = 200)
                const scaleFactor = plugins.scaleFactor || 1;
                chart.getDatasetMeta(0).data.forEach((dataPoint, index) => {
                    dataPoint.outerRadius = chart.originalOuterRadius * scaleFactor;
                })
            }
        }

        let cha = new Chart(chart, {
            type: 'pie',
            title: 'grafica',

            data: {
                labels: ["Bajo     |", "Normal bajo      |", "Normal alto      |", "Buenas y muy buenas"],
                datasets: [

                    <?php
                    $j = 0;
                    $pivote = $anio;
                    echo '{
                            
                            label:"Dato",
                            data:[';
                    while ($j < count($lista)) {

                        echo $lista[$j];
                        $j++;
                        if ($j < count($lista)) {
                            echo ",";
                        };
                    };
                    echo "],
                        backgroundColor:[";
                    $j = 0;
                    while ($j < count($colores)) {
                        echo "'" . $colores[$j] . "'";
                        $j++;
                        if ($j < count($colores)) {
                            echo ",";
                        };
                    }
                    echo "]},";


                    ?>




                ],
            },

            options: {
                animations: false,
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: true,
                    axis: 'x',
                },
                layout: {
                    padding: {
                        top: 40,
                        bottom: 30,
                        left: 30,
                        right: 30,
                    },
                },
                plugins: {

                    legend: {
                        position: 'bottom',

                        labels: {
                            padding: 20,
                            display: true,
                            // This more specific font property overrides the global property
                            font: {
                                weight: 'bold',
                                size: 24,
                                family: 'Arial',
                            },

                        }
                    },
                    scaleChart: {
                        scaleFactor: 0.8,
                    },
                    title: {
                        display: false,
                        text: 'Embalse',
                        fullSize: true,
                        font: {
                            size: 60
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        formatter: ((value, ctx) => {
                            const totalSum = ctx.dataset.data.reduce((accumulator, currentValue) => {
                                return accumulator + currentValue
                            }, 0);
                            value = value != 0 ? value : 1;

                            return (Math.round((value / totalSum) * 10000) / 100).toLocaleString("de-DE") + "%";
                        }),
                        labels: {
                            title: {
                                font: {
                                    weight: 'bold',
                                    family: 'Arial',
                                    size: 40,
                                },
                                color: '#000000',
                            },
                        },
                    }

                },

            },
            plugins: [ChartDataLabels, scaleChart],

        });
        $('#progress-bar').attr('aria-valuenow', <?php echo 25; ?>).css('width', <?php echo 25 ?> + '%');

        let cha1 = new Chart(barra1, {
            type: 'bar',
            title: 'grafica',

            data: {
                labels: [
                    ["Volumen", "Útil", "Total(VUT)"],
                    ["Volumen", "Total", "Actual"],
                    ["Volumen", "Total", "<?php echo date('d/m/Y', strtotime($fecha1)); ?>"],
                    ["Variación  de", "Volumen", "Hasta Hoy"]
                ],
                datasets: [

                    <?php
                    $pivote = $anio;
                    echo '{
                            
                            label:"Dato",
                            data:[' . round($volumen_fechas[0], 2) . ',' . round($volumen_fechas[1], 2) . ',' . round($volumen_fechas[2], 2) . ',' . round(($volumen_fechas[1] - $volumen_fechas[2]), 2);

                    echo "],
                        backgroundColor:[";
                    $j = 2;
                    while ($j >= 0) {
                        echo "'" . $colores[$j] . "',";
                        $j--;
                    }
                    echo "'" . $colores[3] . "']},";


                    ?>




                ],
            },

            options: {
                animations: false,
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: true,
                    axis: 'x',
                },
                layout: {
                    padding: 40,
                },
                plugins: {

                    legend: {
                        position: 'bottom',
                        display: false,
                        labels: {

                            // This more specific font property overrides the global property
                            font: {
                                weight: 'bold',
                                size: 24,
                            },

                        }
                    },
                    title: {
                        display: false,
                        text: 'Embalse',
                        fullSize: true,
                        font: {
                            size: 60
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        formatter: function(value, context) {
                            return (Math.round(value * 100) / 100).toLocaleString("de-DE");
                        },
                        labels: {
                            title: {
                                font: {
                                    weight: 'bold',
                                    family: 'Arial',
                                    size: 26,
                                }
                            },
                        },
                    },
                    arbitra: {
                        lines: [{
                                yvalue: 0,
                                color: 'black',
                            },
                            // Agrega más líneas según sea necesario
                        ],
                    },
                },
                scales: {

                    x: {
                        ticks: {

                            font: {
                                weight: 'bold',
                                size: 20,
                                family: 'Arial',
                            },
                        },
                    },

                    y: {
                        title: {
                            display: true,
                            text: 'Volumen (Hm³)',
                            font: {
                                weight: 'bold',
                                size: 28,
                                family: 'Arial',
                            },
                        },
                        ticks: {
                            font: {
                                size: 24,
                                family: 'Arial',
                            },
                            callback: function(valor, index, valores) {
                                return valor.toLocaleString("de-DE");
                            }
                        },
                    },
                },

            },
            plugins: [arbitra, ChartDataLabels],

        });
        $('#progress-bar').attr('aria-valuenow', <?php echo 50; ?>).css('width', <?php echo 50 ?> + '%');
        let cha2 = new Chart(barra2, {
            type: 'bar',
            title: 'grafica',

            data: {
                labels: [
                    ["Volumen", "Útil", "Total(VUT)"],
                    ["Volumen", "Total", "Actual"],
                    ["Volumen", "Total", "<?php echo date('d/m/Y', strtotime($fecha2)); ?>"],
                    ["Variación  de", "Volumen", "Hasta Hoy"]
                ],
                datasets: [

                    <?php
                    $pivote = $anio;
                    echo '{
                            
                            label:"Dato",
                            data:[' . round($volumen_fechas[0], 2) . ',' . round($volumen_fechas[1], 2) . ',' . round($volumen_fechas[3], 2) . ',' . round(($volumen_fechas[1] - $volumen_fechas[3]), 2);

                    echo "],
                        backgroundColor:[";
                    $j = 0;
                    while ($j < count($colores2)) {
                        echo "'" . $colores2[$j] . "'";
                        $j++;
                        if ($j < count($colores2)) {
                            echo ",";
                        };
                    }
                    echo "]},";


                    ?>




                ],
            },

            options: {
                animations: false,
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: true,
                    axis: 'x',
                },
                layout: {
                    padding: 40,
                },
                plugins: {

                    legend: {
                        position: 'bottom',
                        display: false,
                        labels: {

                            // This more specific font property overrides the global property
                            font: {
                                weight: 'bold',
                                size: 24,
                            },

                        }
                    },
                    title: {
                        display: false,
                        text: 'Embalse',
                        fullSize: true,
                        font: {
                            size: 60
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        formatter: function(value, context) {
                            return (Math.round(value * 100) / 100).toLocaleString("de-DE");
                        },
                        labels: {
                            title: {
                                font: {
                                    weight: 'bold',
                                    family: 'Arial',
                                    size: 26,
                                }
                            },
                        },
                    },
                    arbitra: {
                        lines: [{
                                yvalue: 0,
                                color: 'black',
                            },
                            // Agrega más líneas según sea necesario
                        ],
                    },
                },
                scales: {

                    x: {

                        ticks: {

                            font: {
                                weight: 'bold',
                                size: 20,
                                family: 'Arial',
                            },
                        },
                    },

                    y: {
                        title: {
                            display: true,
                            text: 'Volumen (Hm³)',
                            font: {
                                weight: 'bold',
                                size: 24,
                                family: 'Arial',
                            },
                        },
                        ticks: {
                            font: {
                                size: 24,
                                family: 'Arial',
                            },
                            callback: function(valor, index, valores) {
                                return valor.toLocaleString("de-DE");
                            }
                        },
                    },
                },

            },
            plugins: [arbitra, ChartDataLabels],

        });
        $('#progress-bar').attr('aria-valuenow', <?php echo 75; ?>).css('width', <?php echo 75 ?> + '%');
        let abas = new Chart(abastecimiento, {
            type: 'pie',
            title: 'grafica',

            data: {
                labels: ["Alerta roja", "Alerta naranja", "Alerta amarilla", "Seguro"],
                datasets: [

                    <?php
                    $j = 0;
                    $pivote = $anio;
                    echo '{
                            
                            label:"Dato",
                            data:[';
                    while ($j < count($condicion)) {

                        echo $condicion[$j];
                        $j++;
                        if ($j < count($condicion)) {
                            echo ",";
                        };
                    };
                    echo "],
                        backgroundColor:[";
                    $j = 0;
                    while ($j < count($colores3)) {
                        echo "'" . $colores3[$j] . "'";
                        $j++;
                        if ($j < count($colores3)) {
                            echo ",";
                        };
                    }
                    echo "]},";


                    ?>




                ],
            },

            options: {
                animations: false,
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: true,
                    axis: 'x',
                },
                layout: {
                    padding: {
                        top: 15,
                        bottom: 15,
                        left: 50,
                        right: 50,
                    },
                },
                plugins: {

                    legend: {
                        position: 'bottom',

                        labels: {
                            padding: 20,
                            display: true,
                            // This more specific font property overrides the global property
                            font: {
                                weight: 'bold',
                                size: 24,
                                family: 'Arial',
                            },

                        }
                    },
                    title: {
                        display: false,
                        text: 'Embalse',
                        fullSize: true,
                        font: {
                            size: 60
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        formatter: ((value, ctx) => {
                            const totalSum = ctx.dataset.data.reduce((accumulator, currentValue) => {
                                return accumulator + currentValue
                            }, 0);
                            value = value != 0 ? parseFloat(value) : 1;

                            return (Math.round((value / totalSum) * 10000) / 100).toLocaleString("de-DE") + "%";
                        }),
                        labels: {
                            title: {
                                font: {
                                    size: 40,
                                    family: 'Arial',
                                    weight: 'bold',
                                },
                                color: '#000000',
                            },
                        },
                    },
                    scaleChart: {
                        scaleFactor: 0.8,
                    },

                },

            },
            plugins: [ChartDataLabels, scaleChart],

        });
        $('#progress-bar').attr('aria-valuenow', <?php echo 100; ?>).css('width', <?php echo 100 ?> + '%');
        <?php closeConection($conn);
        // Convertir el array a formato JSON
        $json_datos = json_encode($lista);

        // Codificar el JSON en base64
        $datos_codificados = base64_encode($json_datos);

        $json_datos = json_encode($volumen_fechas);

        // Codificar el JSON en base64
        $volumenes = base64_encode($json_datos);

        $datos_json = json_encode($datos_embalses);
        $datos_embalses = base64_encode($datos_json);

        $datos_json = json_encode($volumen_primer_periodo);
        $volumen_primer_periodo = base64_encode($datos_json);

        $datos_json = json_encode($volumen_segundo_periodo);
        $volumen_segundo_periodo = base64_encode($datos_json);

        $datos_json = json_encode($volumen_quince);
        $volumen_quince = base64_encode($datos_json);

        $datos_json = json_encode($volumen_siete);
        $volumen_siete = base64_encode($datos_json);


        ?>




        const x = document.querySelector("#barra1");
        html2canvas(x).then(function(canvas) { //PROBLEMAS
            //$("#ca").append(canvas);
            canvas.willReadFrequently = true,
                dataURL = canvas.toDataURL("image/jpeg", 0.9);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../guardar-imagen.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'estatus-barra'; ?>&numero=' + 1);
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {

                    console.log("listo");

                } else {

                }
            }
        });
        $(document).ready(function() {
            const y = document.querySelector("#barra2");
            html2canvas(y).then(function(canvas) { //PROBLEMAS
                //$("#ca").append(canvas);
                canvas.willReadFrequently = true,
                    dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'estatus-barra'; ?>&numero=' + 2);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {

                        console.log("listo");

                    } else {

                    }
                }
            });
            const w = document.querySelector("#abastecimiento");
            html2canvas(w).then(function(canvas) { //PROBLEMAS
                //$("#ca").append(canvas);
                canvas.willReadFrequently = true,
                    dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'estatus-pie'; ?>&numero=' + 2);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {


                    } else {

                    }
                }
            });
            const z = document.querySelector("#chart");
            html2canvas(z).then(function(canvas) { //PROBLEMAS
                //$("#ca").append(canvas);
                canvas.willReadFrequently = true,
                    dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'estatus-pie'; ?>&numero=' + 1);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText == "si") {
                            console.log(this.responseText);
                            location.href = "../../pages/reports/print_estatus_embalses.php?fecha1=<?php echo $fecha1; ?>&volumenes=<?php echo $volumenes; ?>&lista=<?php echo $datos_codificados; ?>&fecha2=<?php echo $fecha2; ?>&valores=<?php echo $valores; ?>";
                        } else {
                            console.log(this.responseText);
                        }
                    } else {

                    }
                }
            });

        });
        console.log("<?php echo $volumen_fechas[1] . ', ' . $volumen_fechas[2] . ', ' . $volumen_fechas[3] . ', ' . $volumen_fechas[4] . ', ' . $volumen_fechas[5] ?>");
        // console.log("<?php echo $fechaFormateada2 ?>");
    </script>

<?php };

?>