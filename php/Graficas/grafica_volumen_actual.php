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
    $res = mysqli_query($conn, "SELECT d.id_embalse, MAX(d.fecha),MAX(d.hora),(select MAX(hora) from datos_embalse                                                                                                                                                            where fecha = MAX(d.fecha) AND id_embalse = d.id_embalse) AS hora,
    e.nombre_embalse, (SELECT MAX(cota_actual) 
                       FROM datos_embalse h 
                       WHERE h.id_embalse = d.id_embalse AND h.fecha = MAX(d.fecha) AND h.hora = hora) AS cota_actual
FROM datos_embalse d, embalses e 
WHERE d.estatus = 'activo' AND e.estatus = 'activo' AND d.id_embalse = e.id_embalse 
GROUP BY id_embalse 
ORDER BY d.fecha DESC;");
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

                    labels: ['<35', '<45', '<90', '<100', '>100'],
                    data: {
                        datasets: [

                            <?php

                            $j = 0;

                            while ($j < count($datos_embalses)) {

                                $bati = new Batimetria($datos_embalses[$j]["id_embalse"], $conn);
                                echo "{backgroundColor: '";
                                $x = $bati->getByCota($año, $datos_embalses[$j]["cota_actual"])[1];
                                $min = $bati->volumenMinimo();
                                $max = $bati->volumenMaximo();
                                $nor = $bati->volumenNormal();

                                if ($x == 0 || ((abs(($x - $min)) * (100 / ($max - $min))) >= 0 && (abs(($x - $min)) * (100 / ($max - $min))) <= 15)) {
                                    echo "#b50301',";
                                }; //rojo
                                if ((abs(($x - $min)) * (100 / ($max - $min))) > 15 && (abs(($x - $min)) * (100 / ($max - $min))) <= 35) {
                                    echo "#ff5733',";
                                }; //anaranjado
                                if ((abs(($x - $min)) * (100 / ($max - $min))) > 35 && (abs(($x - $min)) * (100 / ($max - $min))) <= 45) {
                                    echo "#f1d710',";
                                }; //amarillo
                                if ((abs(($x - $min)) * (100 / ($max - $min))) > 45 && (abs(($x - $min)) * (100 / ($max - $min))) <= 90) {
                                    echo "#25d366',";
                                }; //verde
                                if ((abs(($x - $min)) * (100 / ($max - $min))) > 90 && (abs(($x - $min)) * (100 / ($max - $min))) <= 100) {
                                    echo "#0078d4',";
                                }; //azul
                                if ((abs(($x - $min)) * (100 / ($max - $min))) > 100) {
                                    echo "#b50301',";
                                }; //rojo
                                echo "label:'Embalse " . $datos_embalses[$j]["nombre_embalse"] ." (".round((abs(($x - $min)) * (100 / ($max - $min))),0)."%)',
                                    data: [";
                                $batimetria = $bati->getBatimetria();
                            ?> {
                                    x: '<?php echo 'embalse'; //$datos_embalses[$j]["nombre_embalse"];  
                                        ?>',
                                    y: <?php echo $x;  ?>
                                },

                            <?php


                                $j++;
                                echo "]},";
                            };

                            ?>
                        ],
                    },

                    options: {

                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {

                            legend: {
                                position: 'bottom',

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