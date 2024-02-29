<script src="./assets/js/Chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<?php

require_once './php/Conexion.php';
require_once './php/batimetria.php';

date_default_timezone_set("America/Caracas");
setlocale(LC_TIME, "spanish");




$año = date('Y');
$r = mysqli_query($conn, "SELECT * FROM embalses WHERE estatus = 'activo';");
$count = mysqli_num_rows($r);
if ($count >= 1) {
    $res = mysqli_query($conn, "SELECT d.id_embalse, MAX(d.cota_actual) AS cota_actual, e.nombre_embalse FROM datos_embalse d, embalses e WHERE d.estatus = 'activo' AND e.estatus = 'activo' AND d.id_embalse = e.id_embalse GROUP BY id_embalse ORDER BY e.nombre_embalse ASC;");
    $count = mysqli_num_rows($res);
    if ($count >= 1) {
        $datos_embalses = mysqli_fetch_all($res, MYSQLI_ASSOC);
        $embalses = mysqli_fetch_all($r, MYSQLI_ASSOC);

?>
        <canvas id="chart"></canvas>
        <script>
            $(document).ready(function() {
                
                let cha = new Chart(chart, {
                    type: 'bar',
                    title: 'grafica',
                    
                    //labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    data: {
                        datasets: [

                            <?php echo "{label:'Volumen actual',
                            backgroundColor: '#25d366',
                            data: [";
                            $j = 0;

                            while ($j < count($datos_embalses)) {
                                $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
                                $batimetria = $bati->getBatimetria();
                            ?> {
                                    x: '<?php echo $datos_embalses[$j]["nombre_embalse"];  ?>',
                                    y: <?php echo $bati->getByCota($año, $datos_embalses[$j]["cota_actual"])[1];  ?>
                                },

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

                            y: {
                                title: {
                                    display: true,
                                    text: 'Volumen (Hm³)',
                                    font: {
                                        size: 16
                                    },
                                },
                               
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
                });
            })
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