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

$queryInameh = mysqli_query($conn, "SELECT nombre_config, configuracion FROM configuraciones WHERE nombre_config = 'fecha_sequia' OR nombre_config = 'fecha_lluvia' ORDER BY id_config ASC;");
$fechas = mysqli_fetch_all($queryInameh, MYSQLI_ASSOC);
$fecha1 = $fechas[0]['configuracion'];
$fecha2 = $fechas[1]['configuracion'];
$anio = date('Y', strtotime($fecha1));
$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");
$count = mysqli_num_rows($r);
if ($count >= 1) {

    // $almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse, MAX(d.fecha) AS fech,(SELECT MAX(hora) FROM datos_embalse WHERE fecha = d.fecha AND cota_actual <> 0 AND id_embalse = d.id_embalse) AS hora,
    //     e.nombre_embalse, (SELECT cota_actual 
    //                        FROM datos_embalse h 
    //                        WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual
    // FROM embalses e
    // LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
    // WHERE e.estatus = 'activo'
    // GROUP BY id_embalse 
    // ORDER BY id_embalse ASC;");
    $almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse,MAX(d.fecha) AS fech,               (
        SELECT SUM(extraccion)
        FROM detalles_extraccion dex, codigo_extraccion ce
        WHERE ce.id = dex.id_codigo_extraccion AND dex.id_registro = (SELECT id_registro
           FROM datos_embalse h 
           WHERE h.id_embalse = d.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND estatus = 'activo' AND id_embalse = d.id_embalse) AND cota_actual <> 0) AND (ce.id_tipo_codigo_extraccion = '1' OR ce.id_tipo_codigo_extraccion = '2' OR ce.id_tipo_codigo_extraccion = '3' OR ce.id_tipo_codigo_extraccion = '4')
      ) AS 'extraccion',
      e.nombre_embalse, (SELECT cota_actual 
           FROM datos_embalse h 
           WHERE h.id_embalse = d.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND estatus = 'activo' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual
      FROM embalses e
      LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
      WHERE e.estatus = 'activo'
      GROUP BY id_embalse 
      ORDER BY id_embalse ASC;");

    $condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha1' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 AND da.fecha <= '$fecha1') AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha <= '$fecha1' AND estatus = 'activo' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual 
 FROM embalses e
 LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha1'
 WHERE e.estatus = 'activo' 
 GROUP BY id_embalse;");

    $condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fecha2' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 AND da.fecha <= '$fecha2') AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha <= '$fecha2' AND estatus = 'activo' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual 
 FROM embalses e
 LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha2'
 WHERE e.estatus = 'activo'
 GROUP BY id_embalse;");

    $condiciones_actuales3 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fechaFormateada1' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 AND da.fecha <= '$fechaFormateada1') AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha <= '$fechaFormateada1' AND estatus = 'activo' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual  
  FROM embalses e
  LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fechaFormateada1'
  WHERE e.estatus = 'activo'
  GROUP BY id_embalse;");

    $condiciones_actuales4 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND estatus = 'activo' AND fecha <= '$fechaFormateada2' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.estatus = 'activo' AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0 AND da.fecha <= '$fechaFormateada2') AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha <= '$fechaFormateada2' AND estatus = 'activo' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual
   FROM embalses e
   LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fechaFormateada2'
   WHERE e.estatus = 'activo'
   GROUP BY id_embalse;");



    $datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);
    $volumen_primer_periodo = mysqli_fetch_all($condiciones_actuales1, MYSQLI_ASSOC);
    $volumen_segundo_periodo = mysqli_fetch_all($condiciones_actuales2, MYSQLI_ASSOC);
    $volumen_quince = mysqli_fetch_all($condiciones_actuales3, MYSQLI_ASSOC);
    $volumen_siete = mysqli_fetch_all($condiciones_actuales4, MYSQLI_ASSOC);
    $datos_json1 = json_encode($volumen_primer_periodo);
    $embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

    $j = 0;

    while ($j < count($datos_embalses)) {
        $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
        if ($datos_embalses[$j]["cota_actual"] != NULL) {

            $x = $bati->getByCota(date('Y', strtotime($datos_embalses[$j]["fech"])), $datos_embalses[$j]["cota_actual"])[1];
            $min = $bati->volumenMinimo();
            $max = $bati->volumenMaximo();
            $nor = $bati->volumenNormal();
            //$bati->getByCota($anio, $datos_embalses[$j]["cota_max"])[1]-$bati->getByCota($anio, $datos_embalses[$j]["cota_min"])[1];
            if (($x - $min) <= 0) {
                $sum = 0;
            } else {
                $sum = $x - $min;
                $volumen_fechas[1] += $sum;
            }
            if ($x == 0 || ((abs(($sum)) * (100 / ($nor - $min))) >= 0 && (abs(($sum)) * (100 / ($nor - $min))) < 30)) {
                $lista[0]++;
            };
            if ((abs(($sum)) * (100 / ($nor - $min))) >= 30 && (abs(($sum)) * (100 / ($nor - $min))) < 60) {
                $lista[1]++;
            };
            if ((abs(($sum)) * (100 / ($nor - $min))) >= 60 && (abs(($sum)) * (100 / ($nor - $min))) < 90) {
                $lista[2]++;
            };
            if ((abs(($sum)) * (100 / ($nor - $min))) >= 90 && (abs(($sum)) * (100 / ($nor - $min))) >= 100) {
                $lista[3]++;
            };

            //cuenta de dias//

            $suma_extracciones[] = round(($sum / (($datos_embalses[$j]['extraccion'] + $evaporacion + $filtracion)/1000)));
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
        if ($suma_extracciones[$j] < 150 && $suma_extracciones[$j] >= 0) {
            $condicion[0]++;
        };
        if ($suma_extracciones[$j] >= 150 && $suma_extracciones[$j] < 240) {
            $condicion[1]++;
        };        
        if ($suma_extracciones[$j] >= 270 && $suma_extracciones[$j] < 360) {
            $condicion[2]++;
        };        
        if ($suma_extracciones[$j] >= 360) {
            $condicion[3]++;
        };
        $j++;

    }




?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="../../assets/img/logos/cropped-mminaguas.webp">
        <script src="../../assets/js/Chart.js"></script>
        <script src="../../assets/js/chartjs-plugin-datalabels@2.js"></script>
        <!--script src="../../assets/js/date-fns.js"></script-->
        <script src="./assets/js/date-fns.js"></script>

        <script src="../../assets/js/jquery/jquery.min.js"></script>
        <script src="../../assets/js/html2canvas.min.js"></script>
        <link href="../../assets/css/style-spinner.css" rel="stylesheet" />
        <link id="pagestyle" href="../../assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />

        <title>Document</title>
    </head>

    <body style="height:800px">
        <!--div style=" width: 1200px;"-->
        <div>

            <div style="width:550px !important; height:450px;position:absolute; top:-100%;"><canvas id="chart" class="border border-radius-lg"></canvas></div>
            <div style="width:450px !important; height:450px;position:absolute; top:-100%;"><canvas id="barra1" class="border border-radius-lg"></canvas></div>
            <div style="width:450px !important; height:450px;position:absolute; top:-100%;"><canvas id="barra2" class="border border-radius-lg"></canvas></div>
            <div style="width:520px !important; height:620px;position:absolute; top:-100%;"><canvas id="abastecimiento" class="border border-radius-lg"></canvas></div>

        </div>
        <div class="row justify-content-center h-100">
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
        let cha = new Chart(chart, {
            type: 'pie',
            title: 'grafica',

            data: {
                labels: ["Baja", "Normal baja", "Normal alta", "Buena y muy buena"],
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
                    padding: 20,
                },
                plugins: {

                    legend: {
                        position: 'bottom',

                        labels: {
                            padding: 25,
                            display: true,
                            // This more specific font property overrides the global property
                            font: {
                                weight: 'bold',
                                size: 12,
                                family:'Arial',
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
                        formatter: ((value, ctx) => {
                            const totalSum = ctx.dataset.data.reduce((accumulator, currentValue) => {
                                return accumulator + currentValue
                            }, 0);
                            porcentaje = value / totalSum * 100
                            return `${porcentaje.toFixed(1)}%`;
                        }),
                        labels: {
                            title: {
                                font: {
                                    weight: 'bold',
                                    family:'Arial',
                                },
                                color:'#000000',
                            },
                        },
                    }

                },

            },
            plugins: [ChartDataLabels],

        });
        $('#progress-bar').attr('aria-valuenow', <?php echo 25; ?>).css('width', <?php echo 25 ?> + '%');

        let cha1 = new Chart(barra1, {
            type: 'bar',
            title: 'grafica',

            data: {
                labels: [
                    ["Volumen", "Útil", "total(VUT)"],
                    ["Volumen", "Disponible", "Actual"],
                    ["Volumen", "Disponible", "<?php echo date('d/m/y', strtotime($fecha1)); ?>"],
                    ["Variacion de", "Volumen", "Hasta Hoy"]
                ],
                datasets: [

                    <?php
                    $pivote = $anio;
                    echo '{
                            
                            label:"Dato",
                            data:[' . round($volumen_fechas[0], 2) . ',' . round($volumen_fechas[1], 2) . ',' . round($volumen_fechas[2], 2) . ',' . round(abs($volumen_fechas[2] - $volumen_fechas[1]), 2);

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
                    padding: 20,
                },
                plugins: {

                    legend: {
                        position: 'bottom',
                        display: false,
                        labels: {

                            // This more specific font property overrides the global property
                            font: {
                                weight: 'bold',
                                size: 12,
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
                        labels: {
                            title: {
                                font: {
                                    weight: 'bold',
                                    family:'Arial',
                                }
                            },
                        },
                    },
                },
                scales: {

                    x: {

                        ticks: {

                            font: {
                                weight: 'bold',
                                size: 10,
                                family:'Arial',
                            },
                        },
                    },

                    y: {
                        title: {
                            display: true,
                            text: 'Volumen (Hm³)',
                            font: {
                                weight: 'bold',
                                size: 14,
                                family:'Arial',
                            },
                        },
                        ticks:{
                            font:{
                                size:12,
                                family:'Arial',
                            },
                        },
                    },
                },

            },
            plugins: [ChartDataLabels],

        });
        $('#progress-bar').attr('aria-valuenow', <?php echo 50; ?>).css('width', <?php echo 50 ?> + '%');
        let cha2 = new Chart(barra2, {
            type: 'bar',
            title: 'grafica',

            data: {
                labels: [
                    ["Volumen", "Útil", "total(VUT)"],
                    ["Volumen", "Disponible", "Actual"],
                    ["Volumen", "Disponible", "<?php echo date('d/m/y', strtotime($fecha2)); ?>"],
                    ["Variacion de", "Volumen", "Hasta Hoy"]
                ],
                datasets: [

                    <?php
                    $pivote = $anio;
                    echo '{
                            
                            label:"Dato",
                            data:[' . round($volumen_fechas[0], 2) . ',' . round($volumen_fechas[1], 2) . ',' . round($volumen_fechas[3], 2) . ',' . round(abs($volumen_fechas[3] - $volumen_fechas[1]), 2);

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
                    padding: 20,
                },
                plugins: {

                    legend: {
                        position: 'bottom',
                        display: false,
                        labels: {

                            // This more specific font property overrides the global property
                            font: {
                                weight: 'bold',
                                size: 12,
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
                        labels: {
                            title: {
                                font: {
                                    weight: 'bold',
                                    family:'Arial',
                                }
                            },
                        },
                    },
                },
                scales: {

                    x: {

                        ticks: {

                            font: {
                                weight: 'bold',
                                size: 10,
                                family:'Arial',
                            },
                        },
                    },

                    y: {
                        title: {
                            display: true,
                            text: 'Volumen (Hm³)',
                            font: {
                                weight: 'bold',
                                size: 14,
                                family:'Arial',
                            },
                        },
                        ticks:{
                            font:{
                                size:12,
                                family:'Arial',
                            },
                        },
                    },
                },

            },
            plugins: [ChartDataLabels],

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
                    padding: 20,
                },
                plugins: {

                    legend: {
                        position: 'bottom',

                        labels: {
                            padding: 25,
                            display: true,
                            // This more specific font property overrides the global property
                            font: {
                                weight: 'bold',
                                size: 12,
                                family:'Arial',
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
                        anchor: 'middle',
                        align: 'middle',
                        formatter: ((value, ctx) => {
                            const totalSum = ctx.dataset.data.reduce((accumulator, currentValue) => {
                                return accumulator + currentValue
                            }, 0);
                            porcentaje = value / totalSum * 100
                            return `${porcentaje.toFixed(1)}%`;
                        }),
                        labels: {
                            title: {
                                font: {
                                    size:20,
                                    family:'Arial',
                                    weight:'bold',
                                },color:'#000000',
                            },
                        },
                    }

                },

            },
            plugins: [ChartDataLabels],

        });
        $('#progress-bar').attr('aria-valuenow', <?php echo 100; ?>).css('width', <?php echo 100 ?> + '%');
        <?php closeConection($conn);
        // Convertir el array a formato JSON
        $json_datos = json_encode($lista);

        // Codificar el JSON en base64
        $datos_codificados = base64_encode($json_datos);

        $json_datos = json_encode($volumen_fechas);

        // Codificar el JSON en base64
        $volumenes = base64_encode($json_datos);?>


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

                        console.log("listo");
                        location.href = "../../pages/reports/print_estatus_embalses.php?fecha1=<?php echo $fecha1; ?>&volumenes=<?php echo $volumenes; ?>&lista=<?php echo $datos_codificados; ?>&fecha2=<?php echo $fecha2; ?>&valores=<?php echo $valores; ?>";

                    } else {

                    }
                }
            });

        });
        console.log("<?php echo$volumen_fechas[1].', '.$volumen_fechas[2].', '.$volumen_fechas[3].', '.$volumen_fechas[4].', '.$volumen_fechas[5]?>");
        // console.log("<?php echo$fechaFormateada2?>");
    </script>

<?php };

?>