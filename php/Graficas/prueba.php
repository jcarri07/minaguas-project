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
    <div style=" width: 1200px;">
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

            semana<?php echo $t; ?> = document.getElementById("semana<?php echo $t; ?>");
            mes<?php echo $t; ?> = document.getElementById("mes<?php echo $t; ?>");


            let chart = new Chart(mes<?php echo $t; ?>, {
                type: 'line',
                title: 'grafica',
                //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
                data: {
                    datasets: [

                        <?php echo "{label:'" . $nom[0] . "',
                                
                                tension: 0.4,
                                data: [";
                        $j = 0;
                        $pivote = date("Y", strtotime($datos_embalses[$j]["fecha"]));
                        while ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

                            if (date("Y", strtotime($datos_embalses[$j]["fecha"])) != $pivote) {

                                echo "]},";
                                break;
                            } else {

                                $arFecha = explode('-', $datos_embalses[$j]["fecha"]);

                        ?> {
                                    x: '<?php echo $datos_embalses[$j]["fecha"];  ?>',
                                    y: <?php echo $datos_embalses[$j]["cota_actual"];  ?>
                                },

                        <?php
                                $j++;
                                if ($j >= count($datos_embalses)) {
                                    break;
                                }
                            }
                        }; ?>

                        <?php echo "{label:'" . $nom[1] . "',
                               tension: 0.4,
                                data: [";
                        $pivote = date("Y", strtotime($datos_embalses[$j]["fecha"]));
                        while ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

                            if (date("Y", strtotime($datos_embalses[$j]["fecha"])) != $pivote) {

                                echo "],";
                                break;
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
                                        year: 'numeric'
                                    }).format(value);
                                },
                            },

                        }
                    },

                    y: {
                        label: 'Cota (m.s.n.m.)',
                        min: <?php echo round($embalses[$t]["cota_min"] - 30, 2);
                                ?>,
                        max: <?php echo round($embalses[$t]["cota_max"] + 20, 2); ?>,


                    },
                },
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

                        <?php echo "{label:'" . $nom[0] . "',tension: 0.4,data: [";
                        $j = 0;
                        $pivote = date("Y", strtotime($datos_embalses[$j]["fecha"]));
                        while ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

                            if (date("Y", strtotime($datos_embalses[$j]["fecha"])) != $pivote) {

                                echo "]},";
                                break;
                            } else {

                                $arFecha = explode('-', $datos_embalses[$j]["fecha"]);

                        ?> {
                                    x: '<?php echo $datos_embalses[$j]["fecha"];  ?>',
                                    y: <?php echo $datos_embalses[$j]["cota_actual"];  ?>
                                },

                        <?php
                                $j++;
                                if ($j >= count($datos_embalses)) {
                                    break;
                                }
                            }
                        }; ?>

                        <?php echo "{label:'" . $nom[1] . "',tension: 0.4,data: [";
                        $pivote = date("Y", strtotime($datos_embalses[$j]["fecha"]));
                        while ($embalses[$t]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

                            if (date("Y", strtotime($datos_embalses[$j]["fecha"])) != $pivote) {

                                echo "],";
                                break;
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
                    animations:false,
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
                            },

                        }
                    },

                    y: {
                        label: 'Cota (m.s.n.m.)',
                        min: <?php echo round($embalses[$t]["cota_min"] - 30, 2);
                                ?>,
                        max: <?php echo round($embalses[$t]["cota_max"] + 20, 2); ?>,


                    },
                },
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
                dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo $embalses[$t]['nombre_embalse']; ?>&numero=' + 1);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                        console.log("listo");
                        i++;
                        //$("#can").remove();
                        //window.close();
                    } else {
                        console.log(this.responseText);
                    }
                }
            });
            html2canvas(y<?php echo $t; ?>).then(function(canvas) { //PROBLEMAS
                //$("#ca").append(canvas);
                dataURL = canvas.toDataURL("image/jpeg", 0.9);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../guardar-imagen.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('imagen=' + dataURL + '&nombre=<?php echo $embalses[$t]['nombre_embalse']; ?>&numero=' + 2);
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                        console.log("listo");
                        i++;
                        //$("#can").remove();
                        //window.close();
                    } else {
                        console.log(this.responseText);
                    }
                }
            });


        <?php

        }
        ?>





    });
</script>

</html>