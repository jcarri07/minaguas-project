<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../../assets/js/Chart.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <title>Document</title>
</head>

<body>
    <div>
        <canvas id="chart"></canvas>
    </div>
</body>

<script>
    $(document).ready(function(){

        charts = document.getElementById("chart");

    let chart = new Chart(charts, {
    type: 'line',
    data: {
        datasets: [{
            data: [{
                x: '2021-11-06 23:39:30',
                y: 50
            }, {
                x: '2021-11-07 01:00:28',
                y: 60
            }, {
                x: '2021-11-07 09:00:28',
                y: 20
            }]
        }],
    },
    options: {
        scales: {
            x: {
                min: '2021-11-07 00:00:00',
            }
        }
    }
});
    });
    
</script>

</html>