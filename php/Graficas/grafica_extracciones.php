<?php
require_once '../Conexion.php';
require_once '../batimetria.php';


function getRandomColor()
{
    $letters = str_split('0123456789ABCDEF');
    $color = '#';

    for ($i = 0; $i < 6; $i++) {
        $color .= $letters[rand(0, 15)];
    }

    return $color;
}

// Obtener la fecha actual
$fechaActual = new DateTime();
// Restarle 15 días
$fechaActual->modify('-7 days');
// Obtener un string de la fecha en formato deseado (por ejemplo, 'Y-m-d' para año-mes-día)
$fechaFormateada1 = $fechaActual->format('Y-m-d');

// Obtener la fecha actual
$fechaActual = new DateTime();
// Restarle 15 días
$fechaActual->modify('-1 years');
// Obtener un string de la fecha en formato deseado (por ejemplo, 'Y-m-d' para año-mes-día)
$fechaFormateada2 = $fechaActual->format('Y-m-d');

$queryInameh = mysqli_query($conn, "SELECT nombre_config, configuracion FROM configuraciones WHERE nombre_config = 'fecha_sequia' OR nombre_config = 'fecha_lluvia' ORDER BY id_config ASC;");
$fechas = mysqli_fetch_all($queryInameh, MYSQLI_ASSOC);
$fecha1 = $fechas[0]['configuracion'];
$fecha2 = $fechas[1]['configuracion'];
$anio = date('Y', strtotime($fecha1));
$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");
$count = mysqli_num_rows($r);
if ($count >= 1) {

    $hace_30_dias = date("Y-m-d", strtotime(date("Y-m-d") . "- 30 days"));

    $almacenamiento_actual = mysqli_query(
        $conn,
        "SELECT tce.nombre AS 'tipo_extraccion', 
        COALESCE(ROUND(suma_extracciones, 5), 0) AS 'suma'
            FROM (
            SELECT tce.id, tce.nombre
            FROM tipo_codigo_extraccion tce
            WHERE tce.id IN ('1', '2', '3', '4')
        ) AS tce
            LEFT JOIN (
            SELECT ce.id_tipo_codigo_extraccion, 
                    SUM(CAST(de.extraccion AS DOUBLE)) AS suma_extracciones
            FROM detalles_extraccion de
            JOIN codigo_extraccion ce ON de.id_codigo_extraccion = ce.id
            JOIN datos_embalse dem ON de.id_registro = dem.id_registro
            WHERE dem.fecha >= '$hace_30_dias' AND dem.estatus = 'activo'
            GROUP BY ce.id_tipo_codigo_extraccion
        ) AS sumas ON tce.id = sumas.id_tipo_codigo_extraccion
        ORDER BY tce.id;"
    );


    $datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);



    $colores_fixed_array = array(
        "6" => "#109F2D",
        "5" => "#7091DC",
        "4" => "#703374",
        "3" => "#F17900",
        "2" => "#F5CD5A",
        "1" => "#A6B748",
        "0" => "#F28894",
    );

    $colors = array();

    $j = 0;
    $sumatoria_pa_condicion = 0;
    while ($j < count($datos_embalses)) {
        $sumatoria_pa_condicion += $datos_embalses[$j]['suma'];
        $color = getRandomColor();
        $colors[$datos_embalses[$j]['tipo_extraccion']] = $colores_fixed_array[$j];
        $j++;
    }

    $j = 0;


    if ($sumatoria_pa_condicion > 0) {
?>

        <canvas id="extracciones" class="border border-radius-lg"></canvas>
        <script src="./assets/js/chartjs-plugin-datalabels@2.js"></script>


        <script>
            $(document).ready(function() {
                let extra = new Chart(extracciones, {
                    type: 'pie',
                    title: 'grafica',

                    data: {
                        labels: [<?php $j = 0;
                                    while ($j < count($datos_embalses)) {
                                        if ($datos_embalses[$j]['suma'] > 0) {
                                            echo '"' . $datos_embalses[$j]['tipo_extraccion'] . '"';
                                            $j++;
                                            if ($j < count($datos_embalses)) {
                                                echo ",";
                                            };
                                        } else {
                                            $j++;
                                        }
                                    }; ?>],
                        datasets: [

                            <?php
                            $j = 0;

                            echo '{
                            
                            label:[';
                            while ($j < count($datos_embalses)) {
                                if ($datos_embalses[$j]['suma'] > 0) {
                                    echo '"' . $datos_embalses[$j]['tipo_extraccion'] . '"';
                                    $j++;
                                    if ($j < count($datos_embalses)) {
                                        echo ",";
                                    };
                                } else {
                                    $j++;
                                }
                            };
                            $j = 0;
                            echo '],
                            data:[';
                            while ($j < count($datos_embalses)) {
                                if ($datos_embalses[$j]['suma'] > 0) {
                                    echo $datos_embalses[$j]['suma'];
                                    $j++;
                                    if ($j < count($datos_embalses)) {
                                        echo ",";
                                    };
                                } else {
                                    $j++;
                                }
                            };
                            echo "],
                    backgroundColor:[";
                            $j = 0;
                            /*while ($j < count($colores)) {
                    echo "'" . $colores[$j] . "'";
                    $j++;
                    if ($j < count($colores)) {
                        echo ",";
                    };
                }*/
                            while ($j < count($datos_embalses)) {
                                //$color = getRandomColor();
                                //$colors = [$color];
                                if ($datos_embalses[$j]['suma'] > 0) {
                                    echo "'" . $colors[$datos_embalses[$j]['tipo_extraccion']] . "'";
                                    $j++;
                                    if ($j < count($datos_embalses)) {
                                        echo ",";
                                    };
                                } else {
                                    $j++;
                                }
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
                            intersect: false,
                            axis: 'y',
                        },
                        layout: {
                            padding: {
                                top: 25,
                                bottom: 25,
                            },
                        },
                        plugins: {

                            legend: {
                                position: 'bottom',

                                labels: {
                                    padding: 5,
                                    display: true,
                                    // This more specific font property overrides the global property
                                    font: {
                                        weight: 'bold',
                                        size: 9,
                                    },

                                },
                                display: false,
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
                                    porcentaje = (value / totalSum * 100).toFixed(1);
                                    porcentaje = porcentaje.toLocaleString('de_DE');
                                    return `${porcentaje}%`;
                                }),
                                labels: {
                                    title: {
                                        font: {
                                            weight: 'bold',
                                            size: 12,
                                            family: 'Arial',
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
<?php
    } else {
        echo "<h6 class='text-center' style='margin: 118px 0;'>No hay carga de extracciones<br> recientes.</h4>";
    }
} //Fin if de inameh




closeConection($conn); ?>


<script>
    var datos_embalse = <?php echo json_encode($datos_embalses); ?>;
    var colors2 = <?php echo json_encode($colors); ?>;

    var string = "";
    var suma = 0;
    for (var i = 0; i < datos_embalse.length; i++) {
        string += '<div class="d-flex flex-row" style="margin-bottom: 2px;">';
        string += '     <div style="width: 20px; height: 10px; background:' + colors2[datos_embalse[i]['tipo_extraccion']] + ';">';
        string += '     </div>';
        string += '     <span style="font-size: 14px; margin-top: -6px; margin-left: 5px;">' + datos_embalse[i]['tipo_extraccion'] + '</span>';
        string += '</div>';

        suma += parseFloat(datos_embalse[i]['suma']);
    }

    $("#leyenda-4").html(string);



    $("#title-4").html("<span class='text-center' style='font-size: 18px;'>EXTRACCIONES DE LOS ÚLTIMOS 30 DÍAS<br> (" + new Intl.NumberFormat("de-DE").format(suma.toFixed(2)) + " x10<sup>3</sup>m<sup>3</sup>) </span>");
    </script>