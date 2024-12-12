
<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");




$anio = date('Y');
$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo' ORDER BY nombre_embalse ASC;");
$count = mysqli_num_rows($r);
if ($count >= 1) {
//     $res = mysqli_query($conn, "WITH ultimas_fechas AS (
//     SELECT id_embalse, MAX(fecha) AS max_fecha
//     FROM datos_embalse
//     WHERE estatus = 'activo' 
//     AND cota_actual <> 0
//     GROUP BY id_embalse
    
// )
// SELECT e.id_embalse, 
//        e.nombre_embalse, 
//        d.cota_actual, 
//        uf.max_fecha
// FROM embalses e
// LEFT JOIN ultimas_fechas uf ON e.id_embalse = uf.id_embalse
// LEFT JOIN datos_embalse d ON e.id_embalse = d.id_embalse AND d.fecha = uf.max_fecha AND d.estatus = 'activo'
// WHERE e.estatus = 'activo'
// GROUP BY e.id_embalse
// ORDER BY e.nombre_embalse ASC;");
//     $count = mysqli_num_rows($res);
//     if ($count >= 1) {
        $datos_embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);
        //$embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

?>
        <canvas id="chart"></canvas>
        <script>
            $(document).ready(function() {
                <?php //$bati = new Batimetria($datos_embalses[1]["id_embalse"], $conn);
                // $batimetria = $bati->getBatimetria();
                // $x = $bati->volumenActualDisponible();
                //$x = $bati->getByCota($anio, $datos_embalses[1]["cota_actual"])[1];
                // echo "console.log('volúmen:" . $x . ",cota:" . $datos_embalses[1]["cota_actual"] . "');";
                ?>

                <?php

                $j = 0;
                $sum = [];
                $backgroundColors = [];
                $labels = [];
                $dataPoints = [];
                $array = [];
                while ($j < count($datos_embalses)) {

                    $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
                    $batimetria = $bati->getBatimetria();
                    $x = $bati->volumenActualDisponible();//$bati->getByCota($anio, $datos_embalses[$j]["cota_actual"])[1];
                    $min = $bati->volumenMinimo();
                    //$max = $bati->volumenMaximo();
                    $nor = $bati->volumenNormal();
                    // if ($datos_embalses[$j]["cota_actual"] != NULL) {

                        $sum[$j] = $x;
                        // if (($x - $min) <= 0) {
                        //     $sum[$j] = 0;
                        // } else {
                        //     $sum[$j] = $x - $min;
                        // }

                        $div = ($nor - $min) > 0 ? ($nor - $min) : 1;
                        if ($div != 1) {
                            $percentage = (abs($sum[$j]) * (100 / $div));
                        } else {
                            $percentage = 0;
                        }

                        // Determinar el color basado en el porcentaje
                        if ($x == 0 || $percentage < 30) {
                            $backgroundColors[] = "'#fd0200'"; // rojo
                        };
                        if ($percentage >= 30 && $percentage < 60) {
                            $backgroundColors[] = "'#72dffd'"; // anaranjado
                        };
                        if ($percentage >= 60 && $percentage < 90) {
                            $backgroundColors[] = "'#0066eb'"; // verde
                        };
                        if ($percentage >= 90 && $percentage <= 100) {
                            $backgroundColors[] = "'#3ba500'"; // azul
                        };
                        if ($percentage >= 100) {
                            $backgroundColors[] = "'#55fe01'"; // color extra (verde claro)
                        }

                        // Añadir etiqueta
                        $labels[] = "'Embalse " . $datos_embalses[$j]["nombre_embalse"] . " (" . round((abs($sum[$j])*(100/$div)), 0) . "%)'";

                        // Añadir el punto de datos
                        $dataPoints[] = "{ y: '" . $datos_embalses[$j]["nombre_embalse"] . "', x: " . $sum[$j] . " }";
                    // } else {
                    //     // Caso de cota_actual nulo
                    //     $backgroundColors[] = "'#fd0200'"; // color por defecto (rojo)
                    //     $labels[] = "'Embalse " . $embalses[$j]["nombre_embalse"] . " (0%)'";
                    //     $dataPoints[] = "{ y: '" . $datos_embalses[$j]["nombre_embalse"] . "', x: 0 }";
                    // }

                    $max = ($nor - $min) > 0 ? ($nor - $min) : 0;
                    array_push($array, round($max, 3));
                    // $j++;
                    // if ($j < count($datos_embalses)) {
                    //     echo ",";
                    // };
                    $j++;
                }
                ?>
                const maxValues = [<?php echo implode(", ", $array); ?>];

                let cha = new Chart(chart, {
                    type: 'bar',
                    title: 'grafica',
                    label: 'Embalses',
                    data: {
                        datasets: [{
                            backgroundColor: [
                                <?php




                                // Convertir los arrays en cadenas separadas por comas
                                echo implode(", ", $backgroundColors);
                                ?>
                            ],

                            data: [
                                <?php
                                // Convertir puntos de datos en una cadena separada por comas
                                echo implode(", ", $dataPoints);
                                ?>
                            ],
                            borderWidth: 1,
                            categoryPercentage: 1,
                            barPercentage: 0.9
                        }, ],

                    },

                    options: {

                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        interaction: {
                            intersect: false,
                            axis: 'y',
                        },
                        elements: {
                            borderWidth: 1,
                        },
                        plugins: {

                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.raw;
                                        const labelName = context.label; // Muestra el nombre de la etiqueta única
                                        return labelName + ': ' + (Math.round(value.x * 100) / 100).toLocaleString("de-DE");
                                    }
                                }
                            }, //Aqui van los cambios de minaguas nuevos


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
                                    return (Math.round(value.x * 100) / 100).toLocaleString("de-DE");
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
                                            const maxValue = maxValues[context.dataIndex];
                                            //console.log(maxValue);
                                            // Calcula el porcentaje

                                            if (maxValue == 0) {
                                                percentage = 0;
                                            } else {
                                                percentage = value * 100 / maxValue;
                                            }

                                            // Si el porcentaje es menor que 30, cambia el color a rojo
                                            return percentage <= 30 ? '#fd0200' : 'black';
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
                                    callback: function(valor, index, valores) {
                                        return valor.toLocaleString("de-DE");
                                    },
                                },

                            },
                            y: {


                                border: {
                                    display: false,
                                },
                                ticks: {
                                    font: {
                                        size: 13
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
    // } else {

    //     echo '<div class="row justify-content-center"><div class="col-6 text-center"><h5 class="font-weight-bolder">ningun dato en el Año seleccionado</h5></div></div>';
    // }
} else {
    echo '<div class="row justify-content-center"><div class="col-6 text-center"><h5 class="font-weight-bolder">Error:Embalses inactivos o inexistentes</h5></div></div>';
}
closeConection($conn);
?>