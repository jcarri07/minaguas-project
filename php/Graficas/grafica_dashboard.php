<?php

require_once '../Conexion.php';
require_once '../batimetria.php';

$condicion = array(
    "0" => 0,
    "1" => 0,
    "2" => 0,
    "3" => 0,
);
$colores = array(
    "0" => "#5b9bd5",
    "1" => "#9dc3e6",
    "2" => "#2e75b6",
    "3" => "#4679a7",
);

$colores2 = array(
    "0" => "#548235",
    "1" => "#70ad47",
    "2" => "#a9d18e",
    "3" => "#e2f0d9",
);

$colores3 = array(
    "0" => "#ff0000",
    "1" => "#ffaa00",
    "2" => "#ffff00",
    "3" => "#70ad47",
);

$volumen_fechas = array(
    "0" => 0,
    "1" => 0,
    "2" => 0,
    "3" => 0,
    "4" => 0,
    "5" => 0,
);
$suma_extracciones;
$evaporacion = 0;
//$calculo_evaporacion = ($area_embalse * ($evaporacion/1000)*0.8*30.5)/1000000;
$filtracion = 0;
//$calculo_filtracion = ($x*($filtracion(%)/100))/30.5
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

    // $almacenamiento_actual = mysqli_query($conn, "SELECT e.id_embalse, MAX(d.fecha) AS fech,(SELECT MAX(hora) FROM datos_embalse WHERE fecha = d.fecha AND cota_actual <> 0 AND id_embalse = d.id_embalse) AS hora,
    //     e.nombre_embalse, (SELECT cota_actual 
    //                        FROM datos_embalse h 
    //                        WHERE h.id_embalse = d.id_embalse AND h.fecha = (SELECT MAX(da.fecha) FROM datos_embalse da WHERE da.id_embalse = d.id_embalse AND da.cota_actual <> 0) AND h.hora = (SELECT MAX(hora) FROM datos_embalse WHERE fecha = h.fecha AND id_embalse = d.id_embalse) AND cota_actual <> 0 LIMIT 1) AS cota_actual
    // FROM embalses e
    // LEFT JOIN datos_embalse d ON d.id_embalse = e.id_embalse AND d.estatus = 'activo'
    // WHERE e.estatus = 'activo'
    // GROUP BY id_embalse 
    // ORDER BY id_embalse ASC;");
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
    $embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

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
            $volumen_fechas[2] += $bati->volumenDisponibleByCota(date('Y', strtotime($volumen_primer_periodo[$j]["fecha"])), $volumen_primer_periodo[$j]["cota_actual"])-$min;
        }
        if ($volumen_segundo_periodo[$j]['cota_actual'] != NULL) {
            $volumen_fechas[3] += $bati->volumenDisponibleByCota(date('Y', strtotime($volumen_segundo_periodo[$j]["fecha"])), $volumen_segundo_periodo[$j]["cota_actual"])-$min;
        }
        $j++;
    };
?>



    <head>
    <script src="./assets/js/Chart.js"></script>
    <script src="./assets/js/chartjs-plugin-datalabels@2.js"></script>
    <!--script src="../../assets/js/date-fns.js"></script-->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="./assets/js/jquery/jquery.min.js"></script>
    <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="./assets/js/fontawesome/42d5adcbca.js"></script>
    <link href="./assets/css/argon-dashboard.css?v=2.0.4" rel="stylesheet" />

    </head>

    <!--div style=" width: 1200px;"-->
    

        
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

                    ctx.beginPath();
                    ctx.lineWidth = 1;
                    ctx.moveTo(left, y.getPixelForValue(yvalue));
                    ctx.lineTo(right, y.getPixelForValue(yvalue));
                    ctx.strokeStyle = color; // Cambiar color según tus preferencias
                    ctx.fillText( <?php echo round($volumen_fechas[1]*100/$volumen_fechas[0], 2)?> + " %", right -340, y.getPixelForValue(yvalue) + h);
                    //ctx.stroke();
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

        let cha1 = new Chart(barra1, {
            type: 'bar',
            title: 'grafica',

            data: {
                labels: [



                ],
                datasets: [

                    <?php
                    $pivote = $anio;
                    echo '{
                            
                            label:"Dato",
                            data:[{x:"",y:' . round($volumen_fechas[0], 2) . '},{x:"",y:' . round($volumen_fechas[1], 2) . '}';

                    echo "],backgroundColor:['#dae8f6','#2e75b6'],borderColor:'#2e75b6',borderWidth:2},";


                    ?>




                ],
            },

            options: {
                animations: false,
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: true,
                    axis: 'x',
                },
                layout: {
                    padding: 20,
                },
                plugins: {

                    legend: {
                        position: 'bottom',
                        display: false,
                        labels: {

                            // This more specific font property overrides the global property
                            font: {
                                weight: 'bold',
                                size: 12,
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
                    arbitra: {


                        lines: [{
                                yvalue: <?php echo round($volumen_fechas[1], 2) ?>,
                                cota: "",
                                color: 'black',
                                h: 0,
                            },
                            // Agrega más líneas según sea necesario
                        ]
                    },
                },
                scales: {

                    x: {

                        ticks: {

                            font: {
                                weight: 'bold',
                                size: 10,
                            },
                        },
                    },

                    y: {
                        title: {
                            display: true,
                            text: 'Volumen (Hm³)',
                            font: {
                                weight: 'bold',
                                size: 14,
                            },
                        },
                    },
                },

            },
            plugins: [arbi],

        });

        $("#contenedor-2").html('<?php 
        $valor = 100*(($volumen_fechas[1]-$volumen_fechas[3])/$volumen_fechas[1]);
        if($valor >= 0 ){
            
            echo '<h2 class="row col-12 item-align-center"><div class="col-3"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="fill:#2dce89 !important"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg></div><span class=" col-3">' . round(abs($valor),3) . '</span></h2>';
            
        };
        if($valor < 0 ){
            
            echo '<h2 class="row col-12 item-align-center"><div <div class="col-3"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="text-danger"><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg></div><span class=" col-3">' . round(abs($valor),3) . '</span></h2>';
            
        };

        ?>');
        $("#contenedor-3").html('<?php 
        $valor = 100*(($volumen_fechas[1]-$volumen_fechas[2])/$volumen_fechas[1]);
        if($valor >= 0 ){
            
            echo '<h2 class="row col-12 item-align-center"><div <div class="col-3"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="fill:#2dce89 !important"><path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/></svg></div><span class=" col-3">' . round(abs($valor),3) . '</span></h2>';
            
        };
        if($valor < 0 ){
            
            echo '<h2 class="row col-12 item-align-center"><div <div class="col-3"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="text-danger"><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg></div><span class=" col-3">' . round(abs($valor),3) . '</span></h2>';
            
        };

        ?>');
        <?php closeConection($conn); ?>
    });
    </script>
<?php }?>