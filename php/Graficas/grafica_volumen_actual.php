<script src="./assets/js/Chart.js"></script>
<script src="./assets/js/date-fns.js"></script>
<script src="./assets/js/jquery/jquery.min.js"></script>
<script src="./assets/js/chartjs-plugin-datalabels@2.js"></script>
<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");




$año = date('Y');
$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");
$count = mysqli_num_rows($r);
if ($count >= 1) {
    $res = mysqli_query($conn, "SELECT e.id_embalse, MAX(d.fecha),e.nombre_embalse, 
                    (SELECT cota_actual 
                       FROM datos_embalse h 
                       WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.estatus = 'activo' AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND estatus = 'activo' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual
FROM embalses e
LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
WHERE e.estatus = 'activo'
GROUP BY id_embalse 
ORDER BY e.nombre_embalse ASC;");
    $count = mysqli_num_rows($res);
    if ($count >= 1) {
        $datos_embalses = mysqli_fetch_all($res, MYSQLI_ASSOC);
        $embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

?>
        <canvas id="chart"></canvas>
        <script>
            $(document).ready(function() {
                <?php $bati = new Batimetria($datos_embalses[1]["id_embalse"], $conn);
                $batimetria = $bati->getBatimetria();

                $x = $bati->getByCota($año, $datos_embalses[1]["cota_actual"])[1];
                // echo "console.log('volúmen:" . $x . ",cota:" . $datos_embalses[1]["cota_actual"] . "');";
                ?>
                const maxValues = [
                    <?php

                    $j = 0;

                    while ($j < count($datos_embalses)) {
                        $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
                        $batimetria = $bati->getBatimetria();
                        $min = $bati->volumenMinimo();
                        $nor = $bati->volumenNormal() != 0 ? $bati->volumenNormal() : 100;
                        $max = ($nor - $min);
                        echo $max;
                        $j++;
                        if ($j < count($datos_embalses)) {
                            echo ",";
                        };
                    } ?>
                ];
                let cha = new Chart(chart, {
                    type: 'bar',
                    title: 'grafica',
                    label: 'Embalses',
                    data: {
                        datasets: [

                            <?php

                            $j = 0;

                            while ($j < count($datos_embalses)) {
                                if ($datos_embalses[$j]["cota_actual"] != NULL) {
                                    $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
                                    $batimetria = $bati->getBatimetria();
                                    echo "{backgroundColor: '";
                                    $x = $bati->getByCota($año, $datos_embalses[$j]["cota_actual"])[1];
                                    $min = $bati->volumenMinimo();
                                    $max = $bati->volumenMaximo();
                                    $nor = $bati->volumenNormal() != 0 ? $bati->volumenNormal() : 100;
                                    if (($x - $min) <= 0) {
                                        $sum = 0;
                                    } else {
                                        $sum = $x - $min;
                                    }
                                    $div = ($nor - $min) != 0? ($nor - $min):1;
                                    if ($x == 0 || ((abs(($sum)) * (100 / $div)) >= 0 && (abs(($sum)) * (100 / $div)) < 30)) {
                                        echo "#fd0200',";
                                    }; //rojo
                                    if ((abs(($sum)) * (100 / $div)) >= 30 && (abs(($sum)) * (100 / $div)) < 60) {
                                        echo "#72dffd',";
                                    }; //anaranjado
                                    /*if ((abs(($sum)) * (100 / $div)) > 35 && (abs(($sum)) * (100 / $div)) <= 45) {
                                        echo "#f1d710',";
                                    };*/ //amarillo
                                    if ((abs(($sum)) * (100 / $div)) >= 60 && (abs(($sum)) * (100 / $div)) < 90) {
                                        echo "#0066eb',";
                                    }; //verde
                                    if ((abs(($sum)) * (100 / $div)) >= 90 && (abs(($sum)) * (100 / $div)) <= 100) {
                                        echo "#3ba500',";
                                    }; //azul
                                    if ((abs(($sum)) * (100 / $div)) > 100) {
                                        echo "#55fe01',";
                                    }; //rojo
                                    echo "label:'Embalse " . $datos_embalses[$j]["nombre_embalse"] . " (" . round((abs(($sum)) * (100 / $div)), 0) . "%)',
                                        data: [";

                            ?> {
                                        y: '',
                                        x: <?php echo ($sum); ?>,
                                    },
                            <?php


                                    $j++;
                                    echo "],borderWidth:1,categoryPercentage:1,barPercentage: 0.7,},";
                                } else {
                                    echo "{backgroundColor: '#fd0200',";
                                    echo "label:'Embalse " . $datos_embalses[$j]["nombre_embalse"] . " (0%)',
                                    data: [{y: '',x:0,}],borderWidth:1,categoryPercentage:1,barPercentage: 0.7,},";
                                    $j++;
                                }
                            };

                            ?>
                        ],
                    },

                    options: {

                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis:'y',
                        interaction: {
                            intersect: false,
                            axis: 'y',
                        },
                        elements:{
                            borderWidth:5,
                        },
                        plugins: {

                            legend: {
                                position: 'bottom',
                                align: 'start',
                                display: false,
                                labels: {

                                    // This more specific font property overrides the global property
                                    font: {
                                        size: 10
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
                                formatter: function(value, context) {
                                    return Math.round(value.x * 100) / 100;
                                },
                                labels: {
                                    title: {
                                        font: {
                                            weight: 'bold',
                                            family: 'Arial',
                                        },
                                        color: function(context) {
                                            // Obtén el valor actual del dato y su valor máximo correspondiente
                                            const value = context.dataset.data[context.dataIndex].x;
                                            const maxValue = maxValues[context.datasetIndex] != 0 ? maxValues[context.datasetIndex] : 100;
                                            // Calcula el porcentaje
                                            const percentage = value * 100 / maxValue;
                                            // Si el porcentaje es menor que 30, cambia el color a rojo
                                            return percentage < 30 ? '#fd0200' : 'black';
                                        },
                                    },
                                },
                            },

                        },
                        scales: {

                            x: {
                                
                                title: {
                                    display: true,
                                    text: 'Volumen (Hm³)',
                                    
                                    font: {
                                        size: 16
                                    },
                                },
                                ticks: {

                                    font: {
                                        size: 14
                                    },
                                },

                            },
                            y: {

                                
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
                    plugins: [ChartDataLabels],
                });
            });
        </script>
<?php
    } else {

        echo '<div class="row justify-content-center"><div class="col-6 text-center"><h5 class="font-weight-bolder">ningun dato en el Año seleccionado</h5></div></div>';
    }
} else {
    echo '<div class="row justify-content-center"><div class="col-6 text-center"><h5 class="font-weight-bolder">Error:Embalse inactivo o inexistente</h5></div></div>';
}
closeConection($conn);
?>