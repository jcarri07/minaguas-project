<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");

//$fecha1 = date('Y-m-d', strtotime('-1 months', strtotime(date('Y-m-d'))));
//$fecha2 = date('Y');
$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];
$id = $_GET['id'];
$name = $_GET['name'];
$anio = date('Y', strtotime($fecha2));
$volumen;
$bati = new Batimetria($id, $conn);
$batimetria = $bati->getBatimetria();

function getMonthName($numero_mes_aux)
{
    if($numero_mes_aux == "") {
        $fecha_actual = getdate();

        $numero_mes = $fecha_actual['mon'];
    }
    else{
        $numero_mes = $numero_mes_aux;
    }

    $nombres_meses = array(
        1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril",
        5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto",
        9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
    );

    $nombre_mes = $nombres_meses[$numero_mes];

    return  $nombre_mes;
}

/*$numeroSemana = strftime('%W', strtotime($fecha1)); //semana del año
$ex = strftime('%W', strtotime($fecha1));*/

$numeroSemana = date('W', strtotime($fecha1));
$ex = date('W', strtotime($fecha1));
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

function obtenerFechasSemanas($fechaInicial, $fechaFinal)
{
    $fechasSemanas = array();

    // Convertir las fechas a objetos DateTime
    $fechaInicio = new DateTime($fechaInicial);
    $fechaFin = new DateTime($fechaFinal);

    // Obtener el día de la semana de la fecha inicial
    $diaSemanaInicio = $fechaInicio->format('N');

    // Si la fecha inicial no es un lunes, establecer el inicio de la semana en la fecha inicial
    if ($diaSemanaInicio != 1) {
        $inicioSemana = clone $fechaInicio;
    } else {
        // Si es un lunes, obtener el primer día de la semana
        $inicioSemana = clone $fechaInicio;
        $inicioSemana->modify('this week');
    }

    // Iterar para obtener las fechas de las semanas
    while ($inicioSemana <= $fechaFin) {
        // Obtener el número de semana del año
        $numeroSemana = $inicioSemana->format("W");

        // Obtener el último día de la semana (domingo)
        $finSemana = clone $inicioSemana;
        $finSemana->modify('this week');
        $finSemana->modify('next sunday');

        // Verificar si la semana termina después de la fecha final
        if ($finSemana > $fechaFin) {
            $finSemana = $fechaFin;
        }

        // Agregar al array en el formato deseado (dd/mm-dd/mm)
        $fechasSemanas[] = array(
            'fechas' => $inicioSemana->format('d/m') . '-' . $finSemana->format('d/m'),
            'semana' => $numeroSemana
        );

        // Mover la fecha al inicio de la siguiente semana
        $inicioSemana->modify('next monday');
    }

    return $fechasSemanas;
}

$fsemanas = obtenerFechasSemanas($fecha1, $fecha2); //array de labels
$nse = calcularSemanas($fecha1, $fecha2); //pivote de numero de semanas

$sem = 9;
if ($nse < $sem) {
    $sem = $nse;
}

if ($nse % 9 == 0) {
    $Nsemanas = ($nse / 9);
} else {
    $Nsemanas = floor($nse / 9) + 1;
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

$r = mysqli_query($conn, "SELECT fecha,DAYOFWEEK(fecha) AS dia,(SELECT MAX(cota_actual) 
                                                                FROM datos_embalse 
                                                                WHERE id_embalse = e.id_embalse AND estatus = 'activo' AND fecha = d.fecha AND hora = MAX(d.hora)) AS cota_actual,WEEK(fecha,3) semana , MAX(CONCAT(fecha, ' ', hora)) AS fecha_hora, d.id_embalse
                          FROM  datos_embalse d
                          RIGHT JOIN embalses e ON e.id_embalse = d.id_embalse AND e.id_embalse = '$id'
                          WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND d.estatus = 'activo' AND (DAYOFWEEK(fecha) = 2 OR ( fecha = '$fecha1' AND DAYOFWEEK('$fecha1') != 2))
                          GROUP BY fecha 
                          ORDER BY fecha;");

$res = mysqli_query($conn, "SELECT fecha,DAYOFWEEK(fecha) AS dia,(SELECT MAX(cota_actual) 
                                                                  FROM datos_embalse 
                                                                  WHERE id_embalse = e.id_embalse AND estatus = 'activo' AND fecha = d.fecha AND hora = MAX(d.hora)) AS cota_actual,WEEK(fecha,3) semana , MAX(CONCAT(fecha, ' ', hora)) AS fecha_hora, d.id_embalse
                            FROM  datos_embalse d
                            RIGHT JOIN embalses e ON e.id_embalse = d.id_embalse AND e.id_embalse = '$id'
                            WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND d.estatus = 'activo' AND (DAYOFWEEK(fecha) = 1 OR ( fecha = '$fecha2' AND DAYOFWEEK('$fecha2') != 1))
                            GROUP BY fecha 
                            ORDER BY fecha;");

$emb = mysqli_query($conn, "SELECT * FROM embalses WHERE id_embalse = '$id' AND estatus = 'activo';");

$an = mysqli_query($conn, "SELECT * FROM datos_embalse WHERE estatus = 'activo' AND YEAR(fecha) = '$anio' AND id_embalse = '$id' AND cota_actual <> 0 GROUP BY fecha ORDER BY fecha ASC;");

$datos1 = mysqli_fetch_all($r, MYSQLI_ASSOC);
$datos2 = mysqli_fetch_all($res, MYSQLI_ASSOC);
$embalse = mysqli_fetch_all($emb, MYSQLI_ASSOC);
$datos_embalses = mysqli_fetch_all($an, MYSQLI_ASSOC);
$count = mysqli_num_rows($r);
if ($count >= 1) {
    $volumen = $bati->getByCota(date('Y', strtotime($datos1[0]["fecha"])), $datos1[0]["cota_actual"])[1];
    $cot = $datos1[0]["cota_actual"];
} else {
    $volumen = 0;
    $cot = 0;
}


?>

<?php
for ($k = $numeroSemana; $k < $semanas + 1; $k++) {
    if (isset($datos1[$i]['semana'])) {
        if ($k == ($datos1[$i]['semana'])) {
            $array1[$aux] = $bati->getByCota(date('Y', strtotime($datos1[$i]["fecha"])), $datos1[$i]["cota_actual"])[1];
            $i++;
        } else {
            $array1[$aux] = 0;
        }
    } else {
        $array1[$aux] = 0;
    }

    $aux++;
}
$aux = 0;
for ($k = $numeroSemana; $k < $semanas + 1; $k++) {
    if (isset($datos2[$j]['semana'])) {
        if ($k == ($datos2[$j]['semana'])) {
            $array2[$aux] = $bati->getByCota(date('Y', strtotime($datos2[$j]["fecha"])), $datos2[$j]["cota_actual"])[1];
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
        <?php
        $j = 0;
        $nom = array("Cota " . $anio, "Cota " . ($anio - 1));
        $pivote = 0;

        for ($t = 1; $t <=  $Nsemanas; $t++) {
        ?>
            <div style="width:1030px !important; height:450px; <?php echo 'position:absolute; top:-100%;';
                                                                ?>"><canvas class="ch" id="cha<?php echo $t; ?>"></canvas></div>

        <?php

        }
        ?>
        <div style="width:1030px !important; height:480px; <?php echo 'position:absolute; top:-100%;';
                                                            ?>"><canvas class="ch" id="anio"></canvas></div>
        <div style="width:1030px !important; height:480px; <?php echo 'position:absolute; top:-100%;';
                                                            ?>"><canvas class="ch" id="mes"></canvas></div>
        <div style="width:1030px !important; height:480px; <?php echo 'position:absolute; top:-100%;';
                                                            ?>"><canvas class="ch" id="semana"></canvas></div>

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
                        label: 'Volumeninicial (Hm³)',
                        borderColor: '#4472c4',
                        backgroundColor: '#4472c4',
                        data: [<?php
                                for ($k = $l; $k < ($t * $sem); $k++) {
                                    if (isset($datos1[$i]['semana'])) {
                                        if ($fsemanas[$k]['semana'] == ($datos1[$i]['semana'])) {
                                            echo  round($bati->getByCota(date('Y', strtotime($datos1[$i]["fecha"])), $datos1[$i]["cota_actual"])[1], 2); ?>, <?php

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
                                        if ($fsemanas[$k]['semana'] == ($datos2[$j]['semana'])) {
                                            echo  round($bati->getByCota(date('Y', strtotime($datos2[$j]["fecha"])), $datos2[$j]["cota_actual"])[1], 2); ?>, <?php

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
                                size: 18,
                                family:'Arial',
                            },

                        }
                    },
                    title: {
                        display: true,
                        text: 'Control de nivel - <?php echo $embalse[0]['nombre_embalse']; ?>',
                        fullSize: true,
                        font: {
                            size: 30,
                            family:'Arial',
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
                        labels: [<?php for ($k = $l; $k < ($t * $sem); $k++) {
                                        if (isset($fsemanas[$k]['fechas'])) { ?> '<?php echo 'Semana ' . ($k + 1); ?>', <?php }
                                                                                                                } ?>],

ticks: {
                                font: {
                                    size: 14,
                                    family:'Arial',
                                },
                            },},
                    x2: {
                        labels: [<?php for ($k = $l; $k < ($t * $sem); $k++) {
                                        if (isset($fsemanas[$k]['fechas'])) { ?> '<?php echo $fsemanas[$k]['fechas']; ?>', <?php }
                                                                                                                    } ?>],

ticks: {
                                font: {
                                    size: 14,
                                    family:'Arial',
                                },
                            },},

                    x3: {
                        border: {
                            display: false,
                        },
                        grid: {
                            display: false,
                        },
                        labels: [<?php for ($k = $l; $k < ($t * $sem); $k++) {
                                        if (isset($fsemanas[$k]['fechas'])) { ?> '<?php echo round($array2[$k] - $array1[$k], 2) . " Hm³"; ?>', <?php }
                                                                                                                                        } ?>],

ticks: {
                                font: {
                                    size: 14,
                                    family:'Arial',
                                    weight: 'bold',
                                },
                            },},


                    y: {
                        title: {
                            display: true,
                            text: 'Volumen(Hm³)',
                            font: {
                                size: 16,
                                family:'Arial',
                                weight: 'bold',
                            },
                        },
                        min: <?php if ($min < $embalse[0]["cota_min"]) {
                                    if ($bati->getByCota($anio, $min)[1] < 100) {
                                        echo 0;
                                    } else {
                                        echo $bati->getByCota($anio, $min)[1];
                                    }
                                } else {
                                    echo $bati->getByCota($anio, $embalse[0]["cota_min"])[1];
                                }; ?>,
                        max: <?php if ($max > $embalse[0]["cota_max"]) {
                                    echo $bati->getByCota($anio, $max)[1] + 200;
                                } else {
                                    echo $bati->getByCota($anio, $embalse[0]["cota_max"])[1] + 200;
                                }; ?>,
                        border: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                size: 14,
                                family:'Arial',
                            },
                        },

                    },
                },
            },
            plugins: [ChartDataLabels],
        });
        $('#progress-bar').attr('aria-valuenow', <?php echo ($t * 100 / ($Nsemanas + 2)); ?>).css('width', <?php echo ($t * 100 / ($Nsemanas + 2)); ?> + '%');

    <?php

        $l = ($t * $sem);
    }

    // Crear un objeto DateTime a partir de la cadena
    $fechaObjeto = DateTime::createFromFormat("Y-m-d", $fecha2);
    // Obtener el día, mes y año
    /*$dia = strftime("%d", $fechaObjeto->getTimestamp());
    $mes = strftime("%B", $fechaObjeto->getTimestamp()); */// Nombre completo del mes
    $dia = date('d', $fechaObjeto->getTimestamp());
    //$mes = date('F', $fechaObjeto->getTimestamp());
    $mes = getMonthName( $fechaObjeto->format('n') );

    //$anio = strftime("%A",$fechaObjeto->getTimestamp());
    // Formatear la fecha como "DÍA DE MES"
    $fechaFormateada = $dia . " de " . ucwords($mes);

    $fechaObjeto = DateTime::createFromFormat("Y-m-d", date('Y-m-d', strtotime('-9 days', strtotime($fecha2))));
    // Obtener el día, mes y año
    /*$dia = strftime("%d", $fechaObjeto->getTimestamp());
    $mes = strftime("%B", $fechaObjeto->getTimestamp()); */// Nombre completo del mes
    $dia = date('d', $fechaObjeto->getTimestamp());
    $mes = getMonthName( $fechaObjeto->format('n') );

    //$anio = strftime("%A",$fechaObjeto->getTimestamp());
    // Formatear la fecha como "DÍA DE MES"
    $fechaFormateada2 = $dia . " de " . ucwords($mes);



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
                ctx.fillText(cota + ": " + yvalue + " (Hm³)", right*4.2/6, y.getPixelForValue(yvalue) + h);
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
                total = dataset.data[dataset.data.length - 1].y;
                ctx.fillText(parseFloat(total.toFixed(3)), lastElement.x, lastElement.y - 5);
            }
            ctx.restore();
        },
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
                            $pivote = $anio;
                            $min = $embalse[0]["cota_min"];
                            $max = $embalse[0]["cota_max"];
                            while ($j < count($datos_embalses)) {

                                if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalse[0]["id_embalse"] == $datos_embalses[$j]["id_embalse"])) {



                            ?> {
                                    x: '<?php echo $datos_embalses[$j]["fecha"] . " " . $datos_embalses[$j]["hora"];  ?>',
                                    y: <?php echo $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];  ?>
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
                    pointRadius: function(context) {
                        var index = context.dataIndex;
                        var value = context.dataset.data[index];
                        return index === context.dataset.data.length - 1 ? '6' : '0';
                    },
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
                            yvalue: <?php echo round($bati->getByCota($anio, $embalse[0]["cota_min"])[1],2); ?>,
                            cota: "Volumen mínimo",
                            color: 'black',
                            h: -15,
                        },
                        {
                            yvalue: <?php echo round($bati->getByCota($anio, $embalse[0]["cota_nor"])[1],2); ?>,
                            cota: "Volumen normal",
                            color: 'black',
                            h: 15,
                        },
                        {
                            yvalue: <?php echo round($bati->getByCota($anio, $embalse[0]["cota_max"])[1],2); ?>,
                            cota: "Volumen máximo",
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
                    text: '<?php echo "Movimiento " . $embalse[0]['nombre_embalse'] . " - Año " . $anio; ?>',
                    fullSize: true,
                    font: {
                        size: 30,
                        family:'Arial',
                    }
                },

            },
            scales: {

                x: {
                    title: {
                        display: true,
                        text: '',
                        font: {
                            size: 18,
                            family:'Arial',
                        },
                    },
                    type: 'time',
                    time: {
                        unit: 'month'
                    },
                    min: '<?php echo $anio; ?>-01',
                    max: '<?php echo $anio; ?>-12',

                    ticks: {
                        callback: (value, index, ticks) => {

                            const date = new Date(value);
                            //console.log(date);
                            return new Intl.DateTimeFormat('es-ES', {
                                month: 'short',

                            }).format(value);
                        },
                        font: {
                            size: 18,
                            family:'Arial',
                        },
                    },
                    grid: {
                        color: function(context) {},
                    },

                },

                y: {
                    title: {
                        display: true,
                        text: 'Volumen(Hm³)',
                        font: {
                            size: 20,
                            family:'Arial',
                            weight: 'bold',
                        },
                    },
                    min: <?php if ($min < $embalse[0]["cota_min"]) {
                                echo $bati->getByCota($anio, $min)[1];
                            } else {
                                echo $bati->getByCota($anio, $embalse[0]["cota_min"])[1];
                            }; ?>,
                    max: <?php if ($max > $embalse[0]["cota_max"]) {
                                echo $bati->getByCota($anio, $max)[1] + 200;
                            } else {
                                echo $bati->getByCota($anio, $embalse[0]["cota_max"])[1] + 200;
                            }; ?>,
                    border: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            size: 14,
                            family:'Arial',
                        },
                    },
                },
            },
        },
        plugins: [arbitra, point],

    });
    $('#progress-bar').attr('aria-valuenow', <?php echo (($Nsemanas + 1) * 100 / ($Nsemanas + 3)); ?>).css('width', <?php echo (($Nsemanas + 1) * 100 / ($Nsemanas + 3)); ?> + '%');

    let chartM = new Chart(mes, {
        type: 'line',
        title: 'grafica',
        //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
        data: {
            datasets: [

                {
                    label: '<?php echo $nom[0] ?>',
                    borderColor: '#36a1eb',
                    backgroundColor: '#36a1eb',

                    data: [<?php
                            $j = 0;
                            $pivote = date("Y");
                            $min = $embalse[0]["cota_min"];
                            $max = $embalse[0]["cota_max"];
                            while ($j < count($datos_embalses)) {

                                if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalse[0]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) && (date("M", strtotime($datos_embalses[$j]["fecha"])) == date("M"))) {



                            ?> {
                                    x: '<?php echo $datos_embalses[$j]["fecha"] . " " . $datos_embalses[$j]["hora"];  ?>',
                                    y: <?php echo $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];  ?>
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
                    pointRadius: function(context) {
                        var index = context.dataIndex;
                        var value = context.dataset.data[index];
                        return index === context.dataset.data.length - 1 ? '6' : '0';
                    },
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
                            yvalue: <?php echo round($bati->getByCota($anio, $embalse[0]["cota_min"])[1], 2); ?>,
                            cota: "Volumen mínimo",
                            color: 'black',
                            h: 15,
                        },
                        {
                            yvalue: <?php echo round($bati->getByCota($anio, $embalse[0]["cota_nor"])[1], 2); ?>,
                            cota: "Volumen normal",
                            color: 'black',
                            h: 15,

                        },
                        {
                            yvalue: <?php echo round($bati->getByCota($anio, $embalse[0]["cota_max"])[1], 2); ?>,
                            cota: "Volumen máximo",
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
                    text: '<?php echo "Movimiento " . $embalse[0]['nombre_embalse'] . " - Mes de " . getMonthName("")." del ".date('Y'); ?>',
                    fullSize: true,
                    font: {
                        size: 30,
                        family:'Arial',
                    }
                },

            },
            scales: {

                x: {
                    title: {
                        display: false,
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
                            size: 12,
                            family:'Arial',
                        },
                    },
                    grid: {
                        color: function(context) {},
                    },

                },

                y: {
                    title: {
                        display: true,
                        text: 'Volumen(Hm³)',
                        font: {
                            size: 20,
                            family:'Arial',
                            weight: 'bold',
                        },
                    },
                    min: <?php $aux = $bati->getByCota($anio, $embalse[0]["cota_min"])[1];
                            if ($min < $embalse[0]["cota_min"]) {
                                echo $bati->getByCota($anio, $min)[1];
                            } else {
                                if ($aux - 200 < 0) {
                                    echo 0;
                                } else {
                                    echo $bati->getByCota($anio, $embalse[0]["cota_min"])[1] - 200;
                                }
                            }; ?>,
                    max: <?php if ($max > $embalse[0]["cota_max"]) {
                                echo $bati->getByCota($anio, $max)[1] + 200;
                            } else {
                                echo $bati->getByCota($anio, $embalse[0]["cota_max"])[1] + 50;
                            }; ?>,
                    border: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            size: 14,
                            family:'Arial',
                        },
                    },
                },
            },
        },
        plugins: [arbitra, point],

    });
    $('#progress-bar').attr('aria-valuenow', <?php echo (($Nsemanas + 2) * 100 / ($Nsemanas + 3)); ?>).css('width', <?php echo (($Nsemanas + 2) * 100 / ($Nsemanas + 3)); ?> + '%');
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
                            $pivote = $anio;
                            $min = $embalse[0]["cota_min"];
                            $max = $embalse[0]["cota_max"];
                            while ($j < count($datos_embalses)) {

                                if ((date("Y", strtotime($datos_embalses[$j]["fecha"])) == $pivote) && ($embalse[0]["id_embalse"] == $datos_embalses[$j]["id_embalse"])&& (date("M", strtotime($datos_embalses[$j]["fecha"])) == date("M",strtotime($fecha2)))) {



                            ?> {
                                    x: '<?php echo $datos_embalses[$j]["fecha"] . " " . $datos_embalses[$j]["hora"];  ?>',
                                    y: <?php echo $bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];  ?>
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
                    pointRadius: function(context) {
                        var index = context.dataIndex;
                        var value = context.dataset.data[index];
                        return index === context.dataset.data.length - 1 ? '6' : '0';
                    },

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
                            yvalue: <?php echo round($bati->getByCota($anio, $embalse[0]["cota_min"])[1],2); ?>,
                            cota: "Volumen mínimo",
                            color: 'black',
                            h: -15,
                        },
                        {
                            yvalue: <?php echo round($bati->getByCota($anio, $embalse[0]["cota_nor"])[1],2); ?>,
                            cota: "Volumen normal",
                            color: 'black',
                            h: 15,
                        },
                        {
                            yvalue: <?php echo round($bati->getByCota($anio, $embalse[0]["cota_max"])[1],2); ?>,
                            cota: "Volumen máximo",
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
                    text: '<?php echo "Movimiento " . $embalse[0]['nombre_embalse'] . " desde el" . $fechaFormateada2 . " al " . $fechaFormateada." del ".date('Y'); ?>',
                    fullSize: true,
                    font: {
                        size: 26,
                        family:'Arial',
                    }
                },

            },
            scales: {

                x: {
                    title: {
                        display: true,
                        text: '',
                        font: {
                            size: 18,
                            family:'Arial',
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
                            size: 18,
                            family:'Arial',
                        },
                    },
                    grid: {
                        color: function(context) {},
                    },

                },

                y: {
                    title: {
                        display: true,
                        text: 'Volumen(Hm³)',
                        font: {
                            size: 20,
                            family:'Arial',
                            weight: 'bold',
                        },
                    },
                    min: <?php if ($min < $embalse[0]["cota_min"]) {
                                echo $bati->getByCota($anio, $min)[1];
                            } else {
                                echo $bati->getByCota($anio, $embalse[0]["cota_min"])[1];
                            }; ?>,
                    max: <?php if ($max > $embalse[0]["cota_max"]) {
                                echo $bati->getByCota($anio, $max)[1] + 200;
                            } else {
                                echo $bati->getByCota($anio, $embalse[0]["cota_max"])[1] + 200;
                            }; ?>,
                    border: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            size: 14,
                            family:'Arial',
                        },
                    },
                },
            },
        },
        plugins: [arbitra, point],

    });
    $('#progress-bar').attr('aria-valuenow', <?php echo (($Nsemanas + 3) * 100 / ($Nsemanas + 3)); ?>).css('width', <?php echo (($Nsemanas + 3) * 100 / ($Nsemanas + 3)); ?> + '%');

    $(document).ready(function() {
        <?php
        closeConection($conn);
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
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'monitoreo' . $t; ?>&numero=' + 1);
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
        const y = document.querySelector("#mes");
        html2canvas(y).then(function(canvas) { //PROBLEMAS
            //$("#ca").append(canvas);
            canvas.willReadFrequently = true,
                dataURL = canvas.toDataURL("image/jpeg", 0.9);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../guardar-imagen.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('imagen=' + dataURL + '&nombre=<?php echo 'monitoreo-mes'; ?>&numero=' + 1);
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
                    location.href = "../../pages/reports/print_monitoreo.php?id=" + <?php echo $id; ?> + "&volumen=<?php echo $volumen; ?>&cota=<?php echo $cot; ?>&name=<?php echo $embalse[0]['nombre_embalse']; ?>&index=<?php echo $Nsemanas; ?>&semanas=<?php echo $nse; ?>&fecha=<?php echo $fecha1; ?>";

                } else {

                }
            }
        });
    });
</script>

</html>