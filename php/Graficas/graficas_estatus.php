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

$fecha1 = "2024-01-01";
$fecha2 = "2024-03-01";
$anio = date('Y');
$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");
$count = mysqli_num_rows($r);
if ($count >= 1) {

    $almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse, MAX(d.fecha),MAX(d.hora),(select MAX(hora) from datos_embalse                                                                                                                                                            where fecha = MAX(d.fecha) AND id_embalse = d.id_embalse) AS hora,
    e.nombre_embalse, (SELECT MAX(cota_actual) 
                       FROM datos_embalse h 
                       WHERE h.id_embalse = d.id_embalse AND h.fecha = MAX(d.fecha) AND h.hora = hora) AS cota_actual
                    FROM embalses e
                    LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
                    WHERE e.estatus = 'activo'
                    GROUP BY id_embalse 
                    ORDER BY id_embalse ASC;");

    $condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse, d.fecha,(select MAX(hora) from datos_embalse                                                                                                                                                            where fecha = MAX(d.fecha) AND id_embalse = d.id_embalse) AS hora,
e.nombre_embalse, (SELECT MAX(cota_actual) 
                   FROM datos_embalse h 
                   WHERE h.id_embalse = d.id_embalse AND h.fecha = d.fecha AND h.hora = hora) AS cota_actual
                FROM embalses e
                LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha = '$fecha1'
                WHERE e.estatus = 'activo'
                GROUP BY id_embalse 
                ORDER BY id_embalse ASC;");

    $condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse, d.fecha,(select MAX(hora) from datos_embalse                                                                                                                                                            where fecha = MAX(d.fecha) AND id_embalse = d.id_embalse) AS hora,
e.nombre_embalse, (SELECT MAX(cota_actual) 
                   FROM datos_embalse h 
                   WHERE h.id_embalse = d.id_embalse AND h.fecha = d.fecha AND h.hora = hora) AS cota_actual
                FROM embalses e
                LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha = '$fecha2'
                WHERE e.estatus = 'activo'
                GROUP BY id_embalse 
                ORDER BY id_embalse ASC;");


    $datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);
    $volumen_primer_periodo = mysqli_fetch_all($condiciones_actuales1, MYSQLI_ASSOC);
    $volumen_segundo_periodo = mysqli_fetch_all($condiciones_actuales2, MYSQLI_ASSOC);
    $embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

    $j = 0;

    while ($j < count($datos_embalses)) {
        if ($datos_embalses[$j]["cota_actual"] != NULL) {
            $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
            $batimetria = $bati->getBatimetria();
            $x = $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];
            $min = $bati->volumenMinimo();
            $max = $bati->volumenMaximo();
            $nor = $bati->volumenNormal();
            $volumen_fechas[0] += $max;
            $volumen_fechas[1] += $x;
            $volumen_fechas[2] += $bati->getByCota($anio, $volumen_primer_periodo[$j]["cota_actual"])[1];
            $volumen_fechas[3] += $bati->getByCota($anio, $volumen_segundo_periodo[$j]["cota_actual"])[1];

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

            <div style="width:900px !important; height:300px;"><canvas id="chart" class="border border-radius-lg"></canvas></div>
            <div style="width:900px !important; height:300px;"><canvas id="barra1" class="border border-radius-lg"></canvas></div>
            <div style="width:900px !important; height:300px;"><canvas id="barra2" class="border border-radius-lg"></canvas></div>
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
                    animations: true,
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
                    animations: true,
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: true,
                        axis: 'x',
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
                                    size: 12,
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
                    animations: true,
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: true,
                        axis: 'x',
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
                                    size: 12,
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
                    animations: true,
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
        });
    </script>

<?php };
closeConection($conn);
?>