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
        <canvas class="al" id="chart"></canvas>
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
    $nom = array("Cota ".date("Y"),"Cota ".date("Y")-1);
    $pivote = 0;
    
    ?>
    pru = '<?php echo $nom[0]." ".$nom[1]; ?>';
    console.log(pru);


    let chart = new Chart(charts, {
                type: 'line',
                //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
                data: {
                    datasets: [{

                            <?php echo "label:'".$nom[0]."',
                                data: [";
                                $pivote = date("Y",strtotime($datos_embalses[0]["fecha"]));
                            while ($embalses[$i]["id_embalse"] == $datos_embalses[$j]["id_embalse"]) {

                                
                                
                                $arFecha = explode('-',$datos_embalses[$j]["fecha"]);
                                
                                ?>{
                                    x: '<?php echo $datos_embalses[$j]["fecha"];  ?>',
                                    y: <?php echo $datos_embalses[$j]["cota_actual"];  ?>
                                },

                            <?php
                                $j++;
                                if ($j >= count($datos_embalses)) {
                                    break;
                                }
                                if(date("Y",strtotime($datos_embalses[$i]["fecha"])) != $pivote){ 

                                    echo "], label:'".$nom[1]."',
                                    data: [";
                                    $pivote = date("Y",strtotime($datos_embalses[$i]["fecha"]));
    
                                }
                             } echo ?> 
                        
                }],
                },
                options: {
                    scales: {

                        x: {
                        type:'time',
                        time: {
                    unit: 'month'
                },
                min:'2024-01',
                    max:'2024-12',
                }
                    },
                
                y: {
                    min: 200<?php //echo round($embalses[$i]["cota_min"], 2); 
                                ?>,
                    max: <?php echo $embalses[$i]["cota_max"]; ?>,
                    <?php
                    $i++;
                    ?>
                }
            },
        });

        let chartsM = new Chart(chartM, {
                type: 'line',
                //labels: ["2024-01", "2024-02", "2024-03", "2024-04", "2024-05", "2024-06", "2024-07", "2024-08", "2024-09", "2024-10", "2024-11", "2024-12"],
                data: {
                    datasets: [{
                        data: [
                        ],
                    }],
                },
                options: {
                    scales: {

                        x: {
                        type:'time',
                        time: {
                    unit: 'week'
                },
                    min:'lunes',
                    max:'martes',
                }
                    },
                
                y: {
                    min: 200,
                    max:270,
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