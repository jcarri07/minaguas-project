<script src="./assets/js/Chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
$y = $anio;
$año = $anio;

$bati = new Batimetria($id, $conn);
$batimetria = $bati->getBatimetria();
$res = mysqli_query($conn, "SELECT * FROM datos_embalse WHERE estatus = 'activo' AND id_embalse = '$id' AND YEAR(fecha) = '$y' GROUP BY fecha ORDER BY fecha ASC;");
$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo' AND id_embalse = '$id';");
$count = mysqli_num_rows($r);
if ($count >= 1) {


        $datos_embalses = mysqli_fetch_all($res, MYSQLI_ASSOC);
        $embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

?>
        <canvas id="chart"></canvas>
        <script>
            $(document).ready(function() {
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
                            ctx.fillText(cota + ": " + yvalue + " (Hm³)", right - 250, y.getPixelForValue(yvalue) + h);
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

                            <?php echo "{label:'Volumen del mes',data: [";
                            $min = $embalses[0]["cota_min"];
                            $max = $embalses[0]["cota_max"];
                            $j = 0;
                            $pivote = $y;
                            while ($j < count($datos_embalses)) {

                            ?> {
                                    x: '<?php echo $datos_embalses[$j]["fecha"];  ?>',
                                    y: <?php echo $bati->getByCota($año, $datos_embalses[$j]["cota_actual"])[1];  ?>
                                },
                                <?php
                                if ($max < $datos_embalses[$j]["cota_actual"]) {
                                    $max = $datos_embalses[$j]["cota_actual"];
                                }
                                if ($min > $datos_embalses[$j]["cota_actual"]) {
                                    $min = $datos_embalses[$j]["cota_actual"];
                                } ?>

                            <?php


                                $j++;
                            };
                            echo "]},";
                            ?>




                        ],
                    },

                    options: {
                        animations: true,
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            arbitra: {


                                lines: [{
                                        yvalue: <?php echo $bati->getByCota($año, $embalses[0]["cota_min"])[1]; ?>,
                                        cota: "Volumen minimo",
                                        color: 'black',
                                        h: 15,
                                    },
                                    {
                                        yvalue: <?php echo $bati->getByCota($año, $embalses[0]["cota_nor"])[1]; ?>,
                                        cota: "Volumen normal",
                                        color: 'black',
                                        h: 15,

                                    },
                                    {
                                        yvalue: <?php echo $bati->getByCota($año, $embalses[0]["cota_max"])[1]; ?>,
                                        cota: "Volumen maximo",
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
                                        size: 18
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
                                    display: true,
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
                                        size: 16
                                    },
                                },
                                min: <?php if ($min < $embalses[0]["cota_min"]) {
                                            echo 0;
                                        } else {
                                            if($bati->getByCota($año, $embalses[0]["cota_min"])[1] - 200 < 0){echo 0;}else{
                                                echo $bati->getByCota($año, $embalses[0]["cota_min"])[1] - 200;}
                                        }; ?>,
                                max: <?php if ($max > $embalses[0]["cota_max"]) {
                                            echo $bati->getByCota($año, $max)[1] + 200;
                                        } else {
                                            echo $bati->getByCota($año, $embalses[0]["cota_max"])[1] + 200;
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
            })
        </script>
<?php

} else {
    echo '<div class="row justify-content-center"><div class="col-6 text-center"><h5 class="font-weight-bolder">Error:Embalse inactivo o inexistente</h5></div></div>';
}
closeConection($conn);
?>