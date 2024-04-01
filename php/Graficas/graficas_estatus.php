<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

$lista = array(
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

$volumen_fechas = array(
    "0" => 0,
    "1" => 0,
    "2" => 0,
    "3" => 0,
);

$valores = $_GET["valores"];

$queryInameh = mysqli_query($conn, "SELECT nombre_config, configuracion FROM configuraciones WHERE nombre_config = 'fecha_sequia' OR nombre_config = 'fecha_lluvia' ORDER BY id_config ASC;");
$fechas = mysqli_fetch_all($queryInameh, MYSQLI_ASSOC);
$fecha1 = $fechas[0]['configuracion'];
$fecha2 = $fechas[1]['configuracion'];
$anio = date('Y',strtotime($fecha1));
$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");
$count = mysqli_num_rows($r);
if ($count >= 1) {

    $almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND h.hora = (select MAX(hora) FROM datos_embalse                                                                                                                                                            WHERE fecha = MAX(d.fecha) AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
 FROM embalses e
 LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
 WHERE e.estatus = 'activo' 
 GROUP BY id_embalse;");

    $condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha1' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha1' AND h.hora = (select MAX(hora) FROM datos_embalse                                                                                                                                                            WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha1' AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
 FROM embalses e
 LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha1'
 WHERE e.estatus = 'activo' 
 GROUP BY id_embalse;");

    $condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, MAX(d.fecha) AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha2' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = MAX(d.fecha) AND d.fecha <= '$fecha2' AND h.hora = (select MAX(hora) FROM datos_embalse                                                                                                                                                            WHERE fecha = MAX(d.fecha) AND fecha <= '$fecha2' AND id_embalse = d.id_embalse) LIMIT 1) AS cota_actual 
 FROM embalses e
 LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fecha2'
 WHERE e.estatus = 'activo' 
 GROUP BY id_embalse;");


    $datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);
    $volumen_primer_periodo = mysqli_fetch_all($condiciones_actuales1, MYSQLI_ASSOC);
    $volumen_segundo_periodo = mysqli_fetch_all($condiciones_actuales2, MYSQLI_ASSOC);
    $datos_json1 = json_encode($volumen_primer_periodo);
    $embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

    $j = 0;

    while ($j < count($datos_embalses)) {
        $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
        if ($datos_embalses[$j]["cota_actual"] != NULL) {

            $x = $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];
            $min = $bati->volumenMinimo();
            $max = $bati->volumenMaximo();
            $nor = $bati->volumenNormal();
            $volumen_fechas[0] += $bati->volumenDisponible(); //$bati->getByCota($anio, $datos_embalses[$j]["cota_max"])[1]-$bati->getByCota($anio, $datos_embalses[$j]["cota_min"])[1];
            $volumen_fechas[1] += $x - $min;
            $volumen_fechas[2] += $bati->volumenDisponibleByCota($anio, $volumen_primer_periodo[$j]["cota_actual"]);
            $volumen_fechas[3] += $bati->volumenDisponibleByCota($anio, $volumen_segundo_periodo[$j]["cota_actual"]);

            if ($x == NULL || ((abs(($x - $min)) * (100 / ($max - $min))) >= 0 && (abs(($x - $min)) * (100 / ($max - $min))) < 30)) {
                $lista[0]++;
            };
            if ((abs(($x - $min)) * (100 / ($max - $min))) >= 30 && (abs(($x - $min)) * (100 / ($max - $min))) < 60) {
                $lista[1]++;
            };
            if ((abs(($x - $min)) * (100 / ($max - $min))) >= 60 && (abs(($x - $min)) * (100 / ($max - $min))) < 90) {
                $lista[2]++;
            };
            if ((abs(($x - $min)) * (100 / ($max - $min))) >= 90 && (abs(($x - $min)) * (100 / ($max - $min))) <= 100) {
                $lista[3]++;
            };
        } else {
            $volumen_fechas[0] += $bati->volumenDisponible();
            $volumen_fechas[2] += $bati->volumenDisponibleByCota($anio, $volumen_primer_periodo[$j]["cota_actual"]);
            $volumen_fechas[3] += $bati->volumenDisponibleByCota($anio, $volumen_segundo_periodo[$j]["cota_actual"]);
            $lista[0]++;
        };
        $j++;
    };


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
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

        <script src="../../assets/js/jquery/jquery.min.js"></script>
        <script src="../../assets/js/html2canvas.min.js"></script>
        <link href="../../assets/css/style-spinner.css" rel="stylesheet" />

        <title>Document</title>
    </head>

    <body style="height:800px">
        <!--div style=" width: 1200px;"-->
        <div>

            <div style="width:550px !important; height:450px;"><canvas id="chart" class="border border-radius-lg"></canvas></div>
            <div style="width:450px !important; height:450px;"><canvas id="barra1" class="border border-radius-lg"></canvas></div>
            <div style="width:450px !important; height:450px;"><canvas id="barra2" class="border border-radius-lg"></canvas></div>
            <div style="width:900px !important; height:300px;"><canvas id="abastecimiento" class="border border-radius-lg"></canvas></div>

        </div>
        <!-- <div class="loaderPDF">
                <div class="lds-dual-ring"></div>
            </div> -->
    </body>
    <script>
        $(document).ready(function() {
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
                                        weight: 'bold'
                                    }
                                },
                            },
                        }

                    },

                },
                plugins: [ChartDataLabels],

            });

            let cha1 = new Chart(barra1, {
                type: 'bar',
                title: 'grafica',

                data: {
                    labels: ["Volumen Útil total(VUT)", "Volumen Disponible Actual", "Volumen Disponible <?php echo $fecha1; ?>", "Variacion de Volumen Hasta Hoy"],
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
                                        weight: 'bold'
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
                                    size: 8,
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
                                },
                            },
                        },
                    },

                },
                plugins: [ChartDataLabels],

            });

            let cha2 = new Chart(barra2, {
                type: 'bar',
                title: 'grafica',

                data: {
                    labels: ["Volumen Útil total(VUT)", "Volumen Disponible Actual", "Volumen Disponible <?php echo $fecha2; ?>", "Variacion de Volumen Hasta Hoy"],
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
                                        weight: 'bold'
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
                                    size: 8,
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
                                },
                            },
                        },
                    },

                },
                plugins: [ChartDataLabels],

            });

            let abas = new Chart(abastecimiento, {
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
                                        weight: 'bold'
                                    }
                                },
                            },
                        }

                    },

                },
                plugins: [ChartDataLabels],

            });
            <?php closeConection($conn); ?>


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
                        location.href = "../../pages/reports/print_estatus_embalses.php?fecha1=<?php echo $fecha1; ?>&fecha2=<?php echo $fecha2; ?>&valores=<?php echo $valores; ?>";

                    } else {

                    }
                }
            });

        });
    </script>

<?php };

?>