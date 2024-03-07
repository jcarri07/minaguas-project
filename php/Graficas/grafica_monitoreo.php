<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");

$fecha1 = date('Y-m-d', strtotime('-1 months', strtotime(date('Y-m-d'))));
$fecha2 = date('Y');
$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];
$id = $_GET['id'];
$name = $_GET['name'];
$año = date('Y', strtotime($fecha2));

$bati = new Batimetria($id, $conn);
$batimetria = $bati->getBatimetria();

$numeroSemana = strftime('%W', strtotime($fecha1)); //semana del año
$ex = strftime('%W', strtotime($fecha1));
function calcularSemanas($fecha, $fecha2)
{
    // Obtener la fecha actual y la fecha proporcionada
    $fechaActual = new DateTime($fecha2);
    $fechaProporcionada = new DateTime($fecha);

    // Verificar que la fecha proporcionada sea anterior a la fecha actual
    if ($fechaProporcionada >= $fechaActual) {
        return 0; // La fecha proporcionada es igual o posterior a la fecha actual
    }

    // Ajustar la fecha de inicio para incluir la semana
    $fechaProporcionada->modify('last monday');

    // Ajustar la fecha final para incluir la semana
    $fechaActual->modify('next sunday');

    // Calcular la diferencia en semanas
    $intervalo = $fechaActual->diff($fechaProporcionada);
    $dias = $intervalo->days;
    $semanas = ceil($dias / 7);

    return $semanas;
}

function obtenerFechasSemanas($fechaInicial, $numeroSemanas)
{
    $fechasSemanas = array();

    // Convertir la fecha inicial a objeto DateTime
    $fecha = new DateTime($fechaInicial);

    // Iterar para obtener las fechas de las semanas
    for ($i = 0; $i < $numeroSemanas; $i++) {
        // Obtener el primer día de la semana (lunes)
        $inicioSemana = clone $fecha;
        $inicioSemana->modify('this week');

        // Obtener el último día de la semana (domingo)
        $finSemana = clone $fecha;
        $finSemana->modify('this week');
        $finSemana->modify('next sunday');

        // Agregar al array en el formato deseado (dd/mm-dd/mm)
        $fechasSemanas[] = $inicioSemana->format('d/m') . '-' . $finSemana->format('d/m');

        // Mover la fecha al inicio de la siguiente semana
        $fecha->modify('next monday');
    }

    return $fechasSemanas;
}

$fsemanas = obtenerFechasSemanas($fecha1, calcularSemanas($fecha1, $fecha2)); //array de labels
$nse = calcularSemanas($fecha1, $fecha2); //pivote de numero de semanas

$sem = 9;
if ($nse < $sem) {
    $sem = $nse;
}

if ($nse % 9 == 0) {
    $Nsemanas = ($nse / 9);
} else {
    $Nsemanas = ($nse / 9) + 1;
}


$array1 = array();
$array1 = array_fill(0, $nse, null);
$array2 = array();
$array2 = array_fill(0, $nse, null);





$semanas = calcularSemanas($fecha1, $fecha2) + $numeroSemana - 1;

$j = 0; //indice arreglo de lunes
$i = 0; //indice arreglo de domingo
$l = 0;
$aux = 0;

$r = mysqli_query($conn, "SELECT fecha,DAYOFWEEK(fecha) AS dia,cota_actual,WEEK(fecha,3) semana , MAX(CONCAT(fecha, ' ', hora)) AS fecha_hora, d.id_embalse
FROM  datos_embalse d
RIGHT JOIN embalses e ON e.id_embalse = d.id_embalse AND e.id_embalse = '$id'
WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND d.estatus = 'activo' AND (DAYOFWEEK(fecha) = 2 OR ( fecha = '$fecha1' AND DAYOFWEEK('$fecha1') != 2))
GROUP BY fecha ORDER BY fecha;");

$res = mysqli_query($conn, "SELECT fecha,DAYOFWEEK(fecha) AS dia,cota_actual,WEEK(fecha,3) semana , MAX(CONCAT(fecha, ' ', hora)) AS fecha_hora, d.id_embalse
FROM  datos_embalse d
RIGHT JOIN embalses e ON e.id_embalse = d.id_embalse AND e.id_embalse = '$id'
WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND d.estatus = 'activo' AND (DAYOFWEEK(fecha) = 1 OR ( fecha = '$fecha2' AND DAYOFWEEK('$fecha2') != 1))
GROUP BY fecha ORDER BY fecha;");

$emb = mysqli_query($conn, "SELECT * FROM embalses WHERE id_embalse = '$id';");

$anio = mysqli_query($conn, "SELECT * FROM datos_embalse WHERE estatus = 'activo' AND YEAR(fecha) = '$año' AND id_embalse = '$id' GROUP BY fecha ORDER BY fecha ASC;");

$datos1 = mysqli_fetch_all($r, MYSQLI_ASSOC);
$datos2 = mysqli_fetch_all($res, MYSQLI_ASSOC);
$embalse = mysqli_fetch_all($emb, MYSQLI_ASSOC);
$datos_embalses = mysqli_fetch_all($anio, MYSQLI_ASSOC);
$datos_json1 = json_encode($datos1);

$datos_json2 = json_encode($datos2);

?>

<script>
    console.log('<?php echo $datos_json1; ?>');

    console.log('<?php echo $datos_json2; ?>');
</script><?php
            for ($k = $numeroSemana; $k < $semanas; $k++) {
                if (isset($datos1[$i]['semana'])) {
                    if ($k == ($datos1[$i]['semana'])) {
                        $array1[$aux] = $bati->getByCota($año, $datos1[$i]["cota_actual"])[1];
                        $i++;
                    } else {
                        $array1[$aux] = 0;
                    }
                } else {
                    $array1[$aux] = 0;
                }
                if (isset($datos2[$j]['semana'])) {
                    if ($k == ($datos2[$j]['semana'])) {
                        $array2[$aux] = $bati->getByCota($año, $datos2[$j]["cota_actual"])[1];
                        $j++;
                    } else {
                        $array2[$aux] = 0;
                    }
                } else {
                    $array2[$aux] = 0;
                }
                $aux++;
            }
            $i = 0;
            $j = 0;
            //print_r(obtenerFechasSemanas($fecha1, calcularSemanas($fecha1, $fecha2)));
            ?>
<!DOCTYPE html>
<html lang="en">

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

        for ($t = 1; $t <=  $Nsemanas; $t++) {
        ?>
            <div style="width:1830px !important; height:600px; <?php echo 'position:absolute; top:-100%;';
                                                                ?>"><canvas class="ch" id="cha<?php echo $t; ?>"></canvas></div>

        <?php

        }
        ?>
        <div style="width:1830px !important; height:600px; <?php echo 'position:absolute; top:-100%;';
                                                            ?>"><canvas class="ch" id="anio"></canvas></div>
        <div style="width:1830px !important; height:600px; <?php echo 'position:absolute; top:-100%;';
                                                            ?>"><canvas class="ch" id="semana"></canvas></div>

    </div>
    <div class="loaderPDF">
        <div class="lds-dual-ring"></div>
    </div>
</body>
<script>
    <?php
    for ($t = 1; $t <=  $Nsemanas; $t++) {
        $min = $embalse[0]["cota_min"];
        $max = $embalse[0]["cota_max"];
        echo "console.log('uno-" . $t . "');";
    ?>
        let chart<?php echo $t; ?> = new Chart(cha<?php echo $t; ?>, {
            type: 'bar',
            title: 'grafica',
            data: {
                datasets: [{
                        label: 'Volumen inicial (Hm³)',
                        borderColor: '#4472c4',
                        backgroundColor: '#4472c4',
                        data: [<?php
                                for ($k = $l; $k < ($t * $sem); $k++) {
                                    if (isset($datos1[$i]['semana'])) {
                                        if (($numeroSemana + $k) == ($datos1[$i]['semana'])) {
                                            echo  $bati->getByCota($año, $datos1[$i]["cota_actual"])[1]; ?>, <?php

                                                                                                                if ($max < $datos1[$i]["cota_actual"]) {
                                                                                                                    $max = $datos1[$i]["cota_actual"];
                                                                                                                }
                                                                                                                if ($min > $datos1[$i]["cota_actual"]) {
                                                                                                                    $min = $datos1[$i]["cota_actual"];
                                                                                                                }
                                                                                                                $i++;
                                                                                                            } else {
                                                                                                                echo 0; ?>, <?php
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 0; ?>, <?php
                                                                                                                                }
                                                                                                                            }
                                                                                                                                    ?>],
                    },
                    {
                        label: 'Volumen final (Hm³)',
                        tension: 0.4,
                        borderColor: '#ed7d31',
                        backgroundColor: '#ed7d31',
                        data: [<?php
                                for ($k = $l; $k < ($t * $sem); $k++) {
                                    if (isset($datos2[$j]['semana'])) {
                                        if (($numeroSemana + $k) == ($datos2[$j]['semana'])) {
                                            echo  $bati->getByCota($año, $datos2[$j]["cota_actual"])[1]; ?>, <?php

                                                                                                                if ($max < $datos2[$j]["cota_actual"]) {
                                                                                                                    $max = $datos2[$j]["cota_actual"];
                                                                                                                }
                                                                                                                if ($min > $datos2[$j]["cota_actual"]) {
                                                                                                                    $min = $datos2[$j]["cota_actual"];
                                                                                                                }
                                                                                                                $j++;
                                                                                                            } else {
                                                                                                                echo 0; ?>, <?php
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo 0; ?>, <?php
                                                                                                                                }
                                                                                                                            }
                                                                                                                                    ?>],
                    },
                ],
            },
            options: {
                animations: true,
                responsive: true,
                maintainAspectRatio: false,
                plugins: {

                    legend: {
                        position: 'bottom',

                        labels: {

                            // This more specific font property overrides the global property
                            font: {
                                size: 18
                            },

                        }
                    },
                    title: {
                        display: true,
                        text: 'Control de nivel - <?php echo $embalse[0]['nombre_embalse']; ?>',
                        fullSize: true,
                        font: {
                            size: 30
                        }
                    },

                },
                scales: {

                    x: {
                        labels: [<?php for ($k = $l; $k < ($t * $sem); $k++) {
                                        if (isset($fsemanas[$k])) { ?> '<?php echo 'Semana ' . ($k + 1); ?>', <?php }
                                                                                                        } ?>],

                    },
                    x2: {
                        labels: [<?php for ($k = $l; $k < ($t * $sem); $k++) {
                                        if (isset($fsemanas[$k])) { ?> '<?php echo $fsemanas[$k]; ?>', <?php }
                                                                                                } ?>],

                    },

                    x3: {
                        border: {
                            display: false,
                        },
                        grid: {
                            display: false,
                        },
                        labels: [<?php for ($k = $l; $k < ($t * $sem); $k++) {
                                        if (isset($fsemanas[$k])) { ?> '<?php echo round($array2[$k] - $array1[$k], 2) . " Hm³"; ?>', <?php }
                                                                                                                                } ?>],

                    },


                    y: {
                        title: {
                            display: true,
                            text: 'Volumen (Hm³)',
                            font: {
                                size: 16
                            },
                        },
                        min: <?php if ($min < $embalse[0]["cota_min"]) {
                                    if ($bati->getByCota($año, $min)[1] < 100) {
                                        echo 0;
                                    } else {
                                        echo $bati->getByCota($año, $min)[1];
                                    }
                                } else {
                                    echo $bati->getByCota($año, $embalse[0]["cota_min"])[1];
                                }; ?>,
                        max: <?php if ($max > $embalse[0]["cota_max"]) {
                                    echo $bati->getByCota($año, $max)[1] + 200;
                                } else {
                                    echo $bati->getByCota($año, $embalse[0]["cota_max"])[1] + 200;
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
            }
        });
    <?php

        $l = ($t * $sem);
    }

    // Crear un objeto DateTime a partir de la cadena
    $fechaObjeto = DateTime::createFromFormat("Y-m-d", $fecha2);
    // Obtener el día, mes y año
    $dia = strftime("%d", $fechaObjeto->getTimestamp());
    $mes = strftime("%B", $fechaObjeto->getTimestamp()); // Nombre completo del mes
    //$anio = strftime("%A",$fechaObjeto->getTimestamp());
    // Formatear la fecha como "DÍA DE MES"
    $fechaFormateada = $dia . " de " . ucwords($mes);

    $fechaObjeto = DateTime::createFromFormat("Y-m-d", date('Y-m-d', strtotime('-9 days', strtotime($fecha2))));
    // Obtener el día, mes y año
    $dia = strftime("%d", $fechaObjeto->getTimestamp());
    $mes = strftime("%B", $fechaObjeto->getTimestamp()); // Nombre completo del mes
    //$anio = strftime("%A",$fechaObjeto->getTimestamp());
    // Formatear la fecha como "DÍA DE MES"
    $fechaFormateada2 = $dia . " de " . ucwords($mes);


    closeConection($conn);
    $t = 0;
    ?>
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
        beforedatasetsDraw: function(chart, args, plugins) {
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
    //grafica anual
    let chartA = new Chart(anio, {
        type: 'line',
        title: '',
        //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
        data: {
            datasets: [

                {
                    label: '<?php echo $nom[0] ?>',
                    borderColor: '#36a1eb',
                    backgroundColor: '#36a1eb',
                    pointRadius: 0,
                    data: [<?php
                            $j = 0;
                            $pivote = date("Y");
                            $min = $embalse[$t]["cota_min"];
                            $max = $embalse[$t]["cota_max"];
                            while ($j < count($datos_embalses)) {

                                if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalse[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"])) {



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
                }
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
                            yvalue: <?php echo $bati->getByCota($año, $embalse[$t]["cota_min"])[1]; ?>,
                            cota: "Volumen minimo",
                            color: 'black',
                            h: 15,
                        },
                        {
                            yvalue: <?php echo $bati->getByCota($año, $embalse[$t]["cota_nor"])[1]; ?>,
                            cota: "Volumen normal",
                            color: 'black',
                            h: 15,
                        },
                        {
                            yvalue: <?php echo $bati->getByCota($año, $embalse[$t]["cota_max"])[1]; ?>,
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
                    display: true,
                    text: '<?php echo "Movimiento " . $embalse[$t]['nombre_embalse'] . " - Año " . $año; ?>',
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
                        text: '',
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
                    min: <?php if ($min < $embalse[$t]["cota_min"]) {
                                echo $bati->getByCota($año, $min)[1];
                            } else {
                                echo $bati->getByCota($año, $embalse[0]["cota_min"])[1] - 200;
                            }; ?>,
                    max: <?php if ($max > $embalse[$t]["cota_max"]) {
                                echo $bati->getByCota($año, $max)[1] + 200;
                            } else {
                                echo $bati->getByCota($año, $embalse[$t]["cota_max"])[1] + 200;
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
    //grafica de semana usando la fecha fina y 6 dias antes
    let chartS = new Chart(semana, {
        type: 'line',
        title: '',
        //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
        data: {
            datasets: [

                {
                    label: '<?php echo $nom[0] ?>',
                    borderColor: '#36a1eb',
                    backgroundColor: '#36a1eb',
                    pointRadius: 0,
                    data: [<?php
                            $j = 0;
                            $pivote = date("Y");
                            $min = $embalse[$t]["cota_min"];
                            $max = $embalse[$t]["cota_max"];
                            while ($j < count($datos_embalses)) {

                                if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalse[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"])) {



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
                }
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
                            yvalue: <?php echo $bati->getByCota($año, $embalse[$t]["cota_min"])[1]; ?>,
                            cota: "Volumen minimo",
                            color: 'black',
                            h: 15,
                        },
                        {
                            yvalue: <?php echo $bati->getByCota($año, $embalse[$t]["cota_nor"])[1]; ?>,
                            cota: "Volumen normal",
                            color: 'black',
                            h: 15,
                        },
                        {
                            yvalue: <?php echo $bati->getByCota($año, $embalse[$t]["cota_max"])[1]; ?>,
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
                    display: true,
                    text: '<?php echo "Movimiento " . $embalse[$t]['nombre_embalse'] . " desde " . $fechaFormateada2 . " al " . $fechaFormateada; ?>',
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
                        text: '',
                        font: {
                            size: 18
                        },
                    },
                    type: 'time',
                    time: {
                        unit: 'day'
                    },
                    min: '<?php echo date('Y-m-d', strtotime('-9 days', strtotime($fecha2))); ?>',
                    max: '<?php echo $fecha2; ?>',

                    ticks: {
                        callback: (value, index, ticks) => {

                            const date = new Date(value);
                            //console.log(date);
                            return new Intl.DateTimeFormat('es-ES', {
                                day: 'numeric',
                                month: "numeric",
                                year: "2-digit",

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
                    min: <?php if ($min < $embalse[$t]["cota_min"]) {
                                echo $bati->getByCota($año, $min)[1];
                            } else {
                                echo $bati->getByCota($año, $embalse[0]["cota_min"])[1] - 200;
                            }; ?>,
                    max: <?php if ($max > $embalse[$t]["cota_max"]) {
                                echo $bati->getByCota($año, $max)[1] + 200;
                            } else {
                                echo $bati->getByCota($año, $embalse[$t]["cota_max"])[1] + 200;
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
    for ($t = 1; $t <=  $Nsemanas; $t++) {
    ?>

        const y<?php echo $t; ?> = document.querySelector("#cha<?php echo $t; ?>");

        var i = 1;
        html2canvas(y<?php echo $t; ?>).then(function(canvas) { //PROBLEMAS
            //$("#ca").append(canvas);
            canvas.willReadFrequently = true,
                dataURL = canvas.toDataURL("image/jpeg", 0.9);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../guardar-imagen.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'monitoreo'.$t; ?>&numero=' + 1);
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {

                    console.log("listo");

                } else {

                }
            }
        });
    <?php }; ?>
    const x = document.querySelector("#anio");
    html2canvas(x).then(function(canvas) { //PROBLEMAS
            //$("#ca").append(canvas);
            canvas.willReadFrequently = true,
                dataURL = canvas.toDataURL("image/jpeg", 0.9);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../guardar-imagen.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'monitoreo-anio'; ?>&numero=' + 1);
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {

                    console.log("listo");

                } else {

                }
            }
        });
    const z = document.querySelector("#semana");
    html2canvas(z).then(function(canvas) { //PROBLEMAS
            //$("#ca").append(canvas);
            canvas.willReadFrequently = true,
                dataURL = canvas.toDataURL("image/jpeg", 0.9);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../guardar-imagen.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'monitoreo-semana'; ?>&numero=' + 1);
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {

                    console.log("listo");
                    location.href = "../../pages/reports/print_monitoreo.php?id="+<?php echo $id;?>+"&name='<?php echo $embalse[0]['nombre_embalse'];?>'";

                } else {

                }
            }
        });
</script>

</html>