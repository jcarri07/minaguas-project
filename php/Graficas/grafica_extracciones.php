<?php
require_once '../Conexion.php';
require_once '../batimetria.php';

$colores = array(
    "3" => "#9dc3e6",
    "2" => "#5b9bd5",
    "1" => "#2e75b6",
    "0" => "#4679a7",
);
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

    $almacenamiento_actual = mysqli_query($conn, "SELECT tce.nombre AS 'tipo_extraccion', 
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
 WHERE dem.fecha >= '2024-03-22'
 GROUP BY ce.id_tipo_codigo_extraccion
) AS sumas ON tce.id = sumas.id_tipo_codigo_extraccion
ORDER BY tce.id;");


$datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);
?>
                
<canvas id="extracciones" class="border border-radius-lg"></canvas>
<script src="./assets/js/chartjs-plugin-datalabels@2.js"></script>
            

    <script>
        $(document).ready(function() {
            let extra = new Chart(extracciones, {
            type: 'pie',
            title: 'grafica',

            data: {
                labels: [<?php $j = 0; while ($j < count($datos_embalses)) {

                echo '"'.$datos_embalses[$j]['tipo_extraccion'].'"';
                    $j++;
                    if ($j < count($datos_embalses)) {
                echo ",";
                };
                    }; ?>],
                datasets: [

                    <?php
                    $j = 0;
                    
                    echo '{
                            
                            label:[';
                            while ($j < count($datos_embalses)) {

                                echo '"'.$datos_embalses[$j]['tipo_extraccion'].'"';
                                $j++;
                                if ($j < count($datos_embalses)) {
                                    echo ",";
                                };
                            };
                            $j = 0;
                        echo'],
                            data:[';
                    while ($j < count($datos_embalses)) {

                        echo $datos_embalses[$j]['suma'];
                        $j++;
                        if ($j < count($datos_embalses)) {
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
                    padding: 30,
                },
                plugins: {

                    legend: {
                        position: 'bottom',

                        labels: {
                            padding: 10,
                            display: true,
                            // This more specific font property overrides the global property
                            font: {
                                weight: 'bold',
                                size: 8,
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
                                    weight: 'bold',
                                    size:8,
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
closeConection($conn); ?>