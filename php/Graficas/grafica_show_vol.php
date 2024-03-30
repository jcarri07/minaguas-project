<script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
<script src="./assets/js/chartjs-plugin-datalabels@2.js"></script>


<?php

$vol_original = $embalse->volumenDisponibleOriginal();
$vol_batimetria = $embalse->volumenDisponible();
$vol_actual = $embalse->volumenActualDisponible();



?>

<script>
    $(document).ready(function() {
        const ctx = document.getElementById('chart-vol');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Diseño (hm³)', 'Batimetría (hm³)', 'Actual (hm³)'],
                datasets: [{
                    data: [<?php echo $vol_original . "," . $vol_batimetria . "," . $vol_actual ?>],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 205, 86, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235)',
                        'rgba(54, 162, 235)',
                        'rgba(255, 205, 86)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                layout: {
                    padding: 20,
                },
                plugins: {
                    legend: {
                        display: false,
                        labels: {
                            display: false
                        },
                    },
                    datalabels: {
                        anchor: 'start',
                        align: 'end',
                        labels: {
                            title: {
                                font: {
                                    weight: 'bold'
                                }
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            fonts: {
                                size: 18
                            }
                        }
                    },
                    y: {
                        ticks: {
                            fonts: {
                                size: 18
                            }
                        }
                    }
                },
                responsive: true,
                mantainAspectRatio: false,
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
            },
            plugins: [ChartDataLabels],
        });
    });
</script>