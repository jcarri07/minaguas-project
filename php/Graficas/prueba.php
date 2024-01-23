<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../assets/js/Chart.js"></script>
    <script src="../../assets/js/date-fns.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>



    <script src="../../assets/js/html2canvas.min.js"></script>
    <title>Document</title>
</head>

<body>
    <div>
        <canvas class="al" id="chart" style="width: 600px; height:400px"></canvas>
        <canvas class="alM" id="chartM"></canvas>
    </div>
</body>

<script>
    $(document).ready(function() {
        chartM = document.getElementById("chartM");
        charts = document.getElementById("chart");

        <?php
        include "consulta.php";
        $aux = $embalses[0]["id_embalse"];
        $i = 0;
        $j = 0;
        $nom = array("Cota " . date("Y"), "Cota " . date("Y") - 1);
        $pivote = 0;

        ?>
        pru = '<?php echo $nom[0] . " " . $nom[1]; ?>';
        console.log(pru);

const arbi = {
    id: 'arbitraryLine',
    beforeDatasetsDraw(){}
}
        let chart = new Chart(charts, {
            type: 'line',
            title: 'grafica',
            //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
            data: {
                datasets: [

                    <?php echo "{label:'" . $nom[0] . "',
                                
                                tension: 0.4,
                                data: [";
                    $pivote = date("Y", strtotime($datos_embalses[$j]["fecha"]));
                    while ($embalses[$i]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

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
                    while ($embalses[$i]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

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
                        text: '<?php $i = 0;
                                echo $embalses[$i]['nombre_embalse'] ?>',
                        fullSize: true,
                        font: {
                            size: 28
                        }
                    },
                },
                scales: {

                    x: {
                        labelString: 'Año',
                        type: 'time',
                        time: {
                            unit: 'month'
                        },
                        min: '2024-01',
                        max: '2024-12',
                    }
                },

                y: {
                    labelString: 'Cota',
                    min: 200,
                    <?php //echo round($embalses[$i]["cota_min"], 2); 
                    ?>
                    max: <?php echo $embalses[$i]["cota_max"];
                            $i++; ?>,

                },
            },
        });

        let chartsM = new Chart(chartM, {
            type: 'line',
            //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
            data: {
                datasets: [{
                    data: [],
                }],
            },
            options: {
                plugins: {

                    legend: {
                        labels: {
                            // This more specific font property overrides the global property
                            font: {
                                size: 14
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: '<?php $i = 0;
                                echo $embalses[$i]['nombre_embalse'] ?>',
                        fullSize: true,
                        font: {
                            size: 28
                        }
                    },
                },
                scales: {

                    x: {
                        type: 'time',
                        time: {
                            unit: 'week'
                        },
                        min: 'lunes',
                        max: 'martes',
                    }
                },

                y: {
                    min: 200,
                    max: 270,
                },
            },
        });
    });


    /*

        const x = document.querySelector(".al");
        var i = 1;
        html2canvas(x).then(function(canvas) { //PROBLEMAS
            //$("#ca").append(canvas);
            dataURL = canvas.toDataURL("image/jpeg", 0.9);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../guardar-imagen.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('imagen=' + dataURL + '&numero=' + i);
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
        });*/
</script>

</html>