<!DOCTYPE html>
<html lang="en">
<?php include "consulta.php"; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../assets/js/Chart.js"></script>
    <!--script src="../../assets/js/date-fns.js"></script-->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../assets/js/html2canvas.min.js"></script>

    <title>Document</title>
</head>

<body>
    <!--div style=" width: 1200px;"-->
    <div>
        <?php
        $aux = $embalses[0]["id_embalse"];
        $j = 0;
        $nom = array("Cota " . date("Y"), "Cota " . date("Y") - 1);
        $pivote = 0;

        for ($t = 0; $t <  count($embalses); $t++) {
        ?>
            <canvas class="al" id="mes<?php echo $t; ?>"></canvas>
            <canvas class="alM" id="semana<?php echo $t; ?>"></canvas>

        <?php

        }
        ?>

    </div>
    <div class="loaderPDF">
        <div class="lds-dual-ring"></div>
    </div>
</body>

<script>
    $(document).ready(function() {

        <?php
        for ($t = 0; $t <  count($embalses); $t++) {
        ?>
            const arbitra = {
                id: 'arbitra',
                beforeDatasetsDraw(chart, args, plugins) {
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
                    ctx.save();
                    dr(<?php echo $embalses[$t]["cota_min"]; ?>, 'Cota mínima');
                    dr(<?php echo $embalses[$t]["cota_max"]; ?>, 'Cota máxima');

                    function dr(yvalue, cota) {

                        ctx.beginPath();
                        ctx.lineWidth = 2;
                        ctx.moveTo(left, y.getPixelForValue(yvalue));
                        ctx.lineTo(right, y.getPixelForValue(yvalue));
                        ctx.strokeStyle = 'black'; // Cambiar color según tus preferencias
                        ctx.fillText(cota + ": " + yvalue + " m.s.n.m.", right - 320, y.getPixelForValue(yvalue) + 25);
                        ctx.stroke();
                    }
                }
            };
            const dibu = {
                id: 'dibu',
                beforeDatasetsDraw(chart, args, plugins) {
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
                    ctx.save();

                    ctx = chart.ctx;
                    dataset = chart.data.datasets[0];
                    meta = chart.getDatasetMeta(0);

                    // Coordenadas del último punto
                    lastPoint = meta.data[meta.data.length - 1];

                    // Dibujar la etiqueta del último punto
                    ctx.fillStyle = 'black'; // Cambiar color según tus preferencias
                    ctx.font = 'bold 14px Arial';
                    ctx.fillText(dataset.label + ': ' + lastPoint._model.y.toFixed(2), lastPoint._model.x + 10, lastPoint._model.y - 10);
                }
            };
            semana<?php echo $t; ?> = document.getElementById("semana<?php echo $t; ?>");
            mes<?php echo $t; ?> = document.getElementById("mes<?php echo $t; ?>");


            let chart = new Chart(mes<?php echo $t; ?>, {
                type: 'line',
                title: 'grafica',
                //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
                data: {
                    datasets: [

                        <?php echo "{label:'" . $nom[0] . "',tension: 0.4,                                borderColor: '#36a1eb',
        backgroundColor: '#36a1eb',data: [";
                        $j = 0;
                        $pivote = date("Y");
                        while ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

                            if (date("Y", strtotime($datos_embalses[$j]["fecha"])) != $pivote) {

                                echo "";
                                $j++;
                            } else {

                                $arFecha = explode('-', $datos_embalses[$j]["fecha"]);

                        ?> {
                                    x: '<?php echo $datos_embalses[$j]["fecha"];  ?>',
                                    y: <?php echo $datos_embalses[$j]["cota_actual"];  ?>
                                },

                        <?php
                                $j++;
                            }
                            if ($j >= count($datos_embalses)) {
                                break;
                            }
                        };
                        echo "]},"; ?>

                        <?php echo "{label:'" . $nom[1] . "',tension: 0.4,borderColor: '#e4c482',backgroundColor: '#e4c482',
                        data: [";
                        $j = 0;
                        $pivote = date("Y") - 1;
                        while ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

                            if (date("Y", strtotime($datos_embalses[$j]["fecha"])) != $pivote) {

                                echo "";
                                $j++;
                            } else {


                                $arFecha = explode('-', $datos_embalses[$j]["fecha"]);

                        ?> {
                                    x: '<?php echo (date("Y", strtotime($datos_embalses[$j]["fecha"])) + 1) . '-' . date("m", strtotime($datos_embalses[$j]["fecha"])) ?>',
                                    y: <?php echo $datos_embalses[$j]["cota_actual"];  ?>
                                },

                        <?php
                                $j++;
                                if ($j >= count($datos_embalses)) {
                                    break;
                                }
                            }
                        }
                        echo "]},"; ?>


                    ],
                },

                options: {
                    animations: false,
                    responsive: true,
                    interaction: {
                        intersect: false,
                        axis: 'x',
                    },
                    plugins: {
                        legend: {
                            labels: {
                                // This more specific font property overrides the global property
                                font: {
                                    size: 35
                                },

                            }
                        },
                        title: {
                            display: true,
                            text: '<?php echo $embalses[$t]['nombre_embalse']; ?>',
                            fullSize: true,
                            font: {
                                size: 40
                            }
                        },

                    },
                    scales: {

                        x: {
                            title: {
                                display: true,
                                text: 'Año <?php echo date('Y'); ?>',
                                font: {
                                    size: 28
                                },
                            },
                            type: 'time',
                            time: {
                                unit: 'month'
                            },
                            min: '2024-01',
                            max: '2024-12',

                            ticks: {
                                callback: (value, index, ticks) => {

                                    const date = new Date(value);
                                    //console.log(date);
                                    return new Intl.DateTimeFormat('es-ES', {
                                        month: 'short',

                                    }).format(value);
                                },
                                font: {
                                    size: 28
                                },
                            },
                            grid: {
                                color: function(context) {},
                            },

                        },

                        y: {
                            title: {
                                display: true,
                                text: 'Cota (m.s.n.m.)',
                                font: {
                                    size: 28
                                },
                            },
                            min: <?php echo round($embalses[$t]["cota_min"] - 30, 2); ?>,
                            max: <?php echo round($embalses[$t]["cota_max"] + 20, 2); ?>,
                            border: {
                                display: false,
                            },
                            ticks: {
                                font: {
                                    size: 28
                                },
                            },
                            /*grid: {
                                color: function(context) {
                                    if (context.tick.value > <?php echo round($embalses[$t]["cota_min"], 0); ?> && context.tick.value < <?php echo round($embalses[$t]["cota_max"], 0); ?>) {
                                        return '#2ea043';
                                    } else {}
                                    if (context.tick.value < <?php echo round($embalses[$t]["cota_min"], 0); ?>) {
                                        return '#f85149';
                                    }
                                    if (context.tick.value > <?php echo round($embalses[$t]["cota_max"], 0); ?>) {
                                        return '#0a86da';
                                    }
                                },
                            },*/
                        },


                    },
                },
                plugins: [arbitra],

            });
            let chartsM = new Chart(semana<?php echo $t; ?>, {
                type: 'line',
                labels: [<?php
                            foreach ($fechasSemana as $dia) {
                                echo ',"' . $dia . '"';
                            }

                            ?>],
                data: {
                    datasets: [

                        <?php echo "{label:'" . $nom[0] . "',tension: 0.4,                                borderColor: '#36a1eb',
                                backgroundColor: '#36a1eb',data: [";
                        $j = 0;
                        $pivote = date("Y");
                        while ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

                            if (date("Y", strtotime($datos_embalses[$j]["fecha"])) != $pivote) {

                                echo "";
                                $j++;
                            } else {

                                $arFecha = explode('-', $datos_embalses[$j]["fecha"]);

                        ?> {
                                    x: '<?php echo $datos_embalses[$j]["fecha"];  ?>',
                                    y: <?php echo $datos_embalses[$j]["cota_actual"];  ?>
                                },

                        <?php
                                $j++;
                            }
                            if ($j >= count($datos_embalses)) {
                                break;
                            }
                        };
                        echo "]},"; ?>

                        <?php echo "{label:'" . $nom[1] . "',tension: 0.4,borderColor: '#e4c482',
                                backgroundColor: '#e4c482',data: [";
                        $j = 0;
                        $pivote = date("Y") - 1;
                        while ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

                            if (date("Y", strtotime($datos_embalses[$j]["fecha"])) != $pivote) {

                                echo "";
                                $j++;
                            } else {


                                $arFecha = explode('-', $datos_embalses[$j]["fecha"]);

                        ?> {
                                    x: '<?php echo (date("Y", strtotime($datos_embalses[$j]["fecha"])) + 1) . '-' . date("m", strtotime($datos_embalses[$j]["fecha"])) ?>',
                                    y: <?php echo $datos_embalses[$j]["cota_actual"];  ?>
                                },

                        <?php
                                $j++;
                                if ($j >= count($datos_embalses)) {
                                    break;
                                }
                            }
                        }
                        echo "]},"; ?>


                    ],
                },
                options: {
                    animations: false,
                    locale: 'es',
                    interaction: {
                        intersect: false,
                    },
                    plugins: {

                        legend: {
                            labels: {
                                // This more specific font property overrides the global property
                                font: {
                                    size: 28
                                },

                            }
                        },
                        title: {
                            display: true,
                            text: '<?php echo $embalses[$t]['nombre_embalse']; ?>',
                            fullSize: true,
                            font: {
                                size: 28
                            }
                        },
                    },
                    scales: {

                        x: {
                            label: 'Año',
                            type: 'time',
                            time: {
                                unit: 'day'
                            },
                            min: '<?php echo $fechasSemana[0]; ?>',
                            max: '<?php echo end($fechasSemana); ?>',
                            ticks: {
                                callback: (value, index, ticks) => {

                                    const date = new Date(value);
                                    //console.log(date);
                                    return new Intl.DateTimeFormat('es-ES', {
                                        day: 'numeric',
                                        month: 'short',
                                    }).format(value);
                                },
                                font: {
                                    size: 28
                                },
                            },
                            grid: {
                                color: function(context) {},
                            },

                        },
                        y: {
                            label: 'Cota (m.s.n.m.)',
                            min: <?php echo round($embalses[$t]["cota_min"] - 30, 2); ?>,
                            max: <?php echo round($embalses[$t]["cota_max"] + 20, 2); ?>,
                            border: {
                                display: false,
                            },
                            ticks: {
                                font: {
                                    size: 28
                                },
                            },
                            /*grid: {
                                color: function(context) {
                                    if (context.tick.value > <?php echo round($embalses[$t]["cota_min"], 0); ?> && context.tick.value < <?php echo round($embalses[$t]["cota_max"], 0); ?>) {
                                        return '#2ea043';
                                    } else {}
                                    if (context.tick.value < <?php echo round($embalses[$t]["cota_min"], 0); ?>) {
                                        return '#f85149';
                                    }
                                    if (context.tick.value > <?php echo round($embalses[$t]["cota_max"], 0); ?>) {
                                        return '#0a86da';
                                    }
                                },
                            },*/
                        },
                    },


                },
                plugins: [arbitra],
            });
        <?php
        }
        ?>






        <?php

        for ($t = 0; $t <  count($embalses); $t++) {
        ?>
            const x<?php echo $t; ?> = document.querySelector("#mes<?php echo $t; ?>");
            const y<?php echo $t; ?> = document.querySelector("#semana<?php echo $t; ?>");
            var i = 1;
            html2canvas(x<?php echo $t; ?>).then(function(canvas) { //PROBLEMAS
                //$("#ca").append(canvas);
                canvas.willReadFrequently = true,
                    dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo $embalses[$t]['id_embalse']; ?>&numero=' + 1);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {

                        console.log("listo");

                    } else {
                        console.log("fallo");
                    }
                }
            });
            html2canvas(y<?php echo $t; ?>).then(function(canva) { //PROBLEMAS
                //$("#ca").append(canvas);
                canva.willReadFrequently = true,
                    dataURL = canva.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo $embalses[$t]['id_embalse']; ?>&numero=' + 2);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log("listo");

                    } else {
                        console.log("fallo");
                    }
                }
            });


        <?php

        }
        ?>





    });
</script>

</html>