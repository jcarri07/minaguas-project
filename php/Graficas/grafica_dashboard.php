<?php
require_once '../Conexion.php';
require_once '../batimetria.php';
$volumen_fechas = array(
    "0" => 0,
    "1" => 0,
    "2" => 0,
    "3" => 0,
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

    $almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse,MAX(d.fecha) AS fech,
  e.nombre_embalse, (SELECT cota_actual 
       FROM datos_embalse h 
       WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual
  FROM embalses e
  LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
  WHERE e.estatus = 'activo'
  GROUP BY id_embalse 
  ORDER BY id_embalse ASC;");

    $condiciones_actuales1 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, d.fecha AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha <= '$fechaFormateada1' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = (SELECT da.fecha FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.cota_actual <> 0 AND da.fecha <= '$fechaFormateada1' LIMIT 1) AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha <= '$fechaFormateada1' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual  
  FROM embalses e
  LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha <= '$fechaFormateada1'
  WHERE e.estatus = 'activo'
  GROUP BY id_embalse;");

    $condiciones_actuales2 = mysqli_query($conn, "SELECT e.id_embalse,cota_min,cota_max,e.nombre_embalse, d.fecha AS fecha,(select MAX(hora) FROM datos_embalse WHERE fecha = MAX(d.fecha) AND fecha = '$fechaFormateada2' AND id_embalse = d.id_embalse) AS horas,(SELECT cota_actual 
    FROM datos_embalse h 
    WHERE h.id_embalse = e.id_embalse AND h.fecha = (SELECT da.fecha FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.cota_actual <> 0 AND da.fecha = '$fechaFormateada2' LIMIT 1) AND h.hora = (select MAX(hora) FROM datos_embalse WHERE fecha = '$fechaFormateada2' AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual
   FROM embalses e
   LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo' AND d.fecha = '$fechaFormateada2'
   WHERE e.estatus = 'activo'
   GROUP BY id_embalse;");

    $datos_embalses = mysqli_fetch_all($almacenamiento_actual, MYSQLI_ASSOC);
    $volumen_primer_periodo = mysqli_fetch_all($condiciones_actuales1, MYSQLI_ASSOC);
    $volumen_segundo_periodo = mysqli_fetch_all($condiciones_actuales2, MYSQLI_ASSOC);

    $j = 0;

    while ($j < count($datos_embalses)) {
        $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
        $min = $bati->volumenMinimo();
        $max = $bati->volumenMaximo();
        $nor = $bati->volumenNormal();
        if ($datos_embalses[$j]["cota_actual"] != NULL) {

            $x = $bati->getByCota(date('Y', strtotime($datos_embalses[$j]["fech"])), $datos_embalses[$j]["cota_actual"])[1];

            //$bati->getByCota($anio, $datos_embalses[$j]["cota_max"])[1]-$bati->getByCota($anio, $datos_embalses[$j]["cota_min"])[1];
            if (($x - $min) <= 0) {
                $sum = 0;
            } else {
                $sum = $x - $min;
            }
            $volumen_fechas[1] += $sum;
        }
        $volumen_fechas[0] += $bati->volumenDisponible();
        if ($volumen_primer_periodo[$j]['cota_actual'] != NULL) {
            $volumen_fechas[2] += $bati->volumenDisponibleByCota(date('Y', strtotime($volumen_primer_periodo[$j]["fecha"])), $volumen_primer_periodo[$j]["cota_actual"]);
        }
        if ($volumen_segundo_periodo[$j]['cota_actual'] != NULL) {
            $volumen_fechas[3] += $bati->volumenDisponibleByCota(date('Y', strtotime($volumen_segundo_periodo[$j]["fecha"])), $volumen_segundo_periodo[$j]["cota_actual"]);
        }
        $j++;
    };
?>
                
                    <canvas id="barra1" class="border border-radius-lg"></canvas>
            

    <script>
        $(document).ready(function() {
            arbi = {
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
                        const fontSize = 14;
                        const fontStyle = 'normal';
                        const fontFamily = 'Arial';
                        ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                        ctx.beginPath();
                        ctx.lineWidth = 1;
                        ctx.moveTo(left, y.getPixelForValue(yvalue));
                        ctx.lineTo(right, y.getPixelForValue(yvalue));
                        ctx.strokeStyle = color; // Cambiar color según tus preferencias
                        ctx.fillText(<?php echo round($volumen_fechas[1] * 100 / $volumen_fechas[0], 2) ?> + "%", right*1.65/3, y.getPixelForValue(yvalue) + h);
                        //ctx.stroke();
                    });
                    ctx.restore();
                },
                afterDatasetsDraw: function(chart, args, plugins) {
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
            let cha1 = new Chart(barra1, {
                type: 'bar',
                title: 'grafica',

                data: {
                    labels: [],
                    datasets: [
                        <?php
                        $pivote = $anio;
                        echo '{
                            
                            label:"Dato",
                            data:[{x:"",y:' . round($volumen_fechas[0], 2) . '},{x:"",y:' . round($volumen_fechas[1], 2) . '}';

                        echo "],backgroundColor:['#9fe3a3','#2e75b6'],borderColor:'#2e75b6',borderWidth:2},";
                        ?>
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    
                    interaction: {
                        intersect: true,
                        axis: 'x',
                    },
                    layout: {
                        padding: 10,
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            display: false,
                            labels: {

                                // This more specific font property overrides the global property
                                font: {
                                    
                                    size: 12,
                                },
                            }
                        },
                        title: {
                            display: false,
                            text: 'Embalse',
                            fullSize: true,
                            font: {
                                size: 20
                            }
                        },
                        arbitra: {
                            lines: [{
                                yvalue: <?php echo round($volumen_fechas[1], 2) ?>,
                                cota: "",
                                color: 'black',
                                h: -5,
                            }, ]
                        },
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    
                                    size: 10,
                                },
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Volumen (Hm³)',
                                font: {
                                    
                                    size: 12,
                                    family:'Arial',
                                    weight: 'bold',
                                },

                            },
                            ticks:{
                                    font:{
                                        size:12,
                                        family:'Arial',
                                    },
                                },
                        },
                    },
                },
                plugins: [arbi],
            });
        });
            $("#contenedor-2").html('<?php
                                        $valor = 100 * (($volumen_fechas[1] - $volumen_fechas[3]) / $volumen_fechas[1]);
                                        if ($valor >= 0) {

                                            echo '<h1 class="row col-12 align-items-center"><div class="col-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="fill:#2dce89 !important"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg></div><span class=" col-8">' . round(abs($valor), 2) . '%</span></h1>';
                                        };
                                        if ($valor < 0) {

                                            echo '<h1 class="row col-12 align-items-center"><div class="col-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="fill:#fd0200 !important"><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg></div><span class=" col-8">' . round(abs($valor), 2) . '%</span></h1>';
                                        };

                                        ?>');
            $("#contenedor-3").html('<?php
                                        $valor = 100 * (($volumen_fechas[1] - $volumen_fechas[2]) / $volumen_fechas[1]);
                                        if ($valor >= 0) {

                                            echo '<h1 class="row col-12 align-items-center"><div class="col-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="fill:#2dce89 !important"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg></div><span class=" col-8">' . round(abs($valor), 2) . '%</span></h1>';
                                        };
                                        if ($valor < 0) {

                                            echo '<h1 class="row col-12 align-items-center"><div class="col-4"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="fill:#fd0200 !important"><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg></div><span class=" col-8">' . round(abs($valor), 2) . '%</span></h1>';
                                        };

                                        ?>');
        
    </script>
<?php };
closeConection($conn); ?>