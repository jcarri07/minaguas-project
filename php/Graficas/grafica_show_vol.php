<script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
<script src="./assets/js/chartjs-plugin-datalabels@2.js"></script>


<?php

$vol_original = number_format($embalse->volumenDisponibleOriginal(), 2, ".", "");
$vol_batimetria = number_format($embalse->volumenDisponible(), 2, ".", "");
// $vol_actual = number_format($embalse->volumenActualDisponible(),2,".","");

// $porcentaje = $vol_batimetria != 0 ? ($vol_actual * 100) / $vol_batimetria : 0;
// $porcentaje = number_format($porcentaje,2,",",".");



?>

<script>
    $(document).ready(function() {
        const ctx = document.getElementById('chart-vol');

        new Chart(ctx, {
            type: 'bar',
            data: {
                // labels: [['Diseño','(Hm³)'], ['Batimetría','(Hm³)'], ['Actual (<?php //echo $porcentaje."%"
                                                                                    ?>)','(Hm³)']],
                labels: [
                    ['Diseño(<?php echo $embalse->getDisenio() ?>)', '(Hm³)'],
                    ['Batimetría (<?php echo $embalse->getCloseYear() ?>)', '(Hm³)']
                ],
                datasets: [{
                    data: [<?php echo $vol_original . "," . $vol_batimetria ?>],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        // 'rgba(255, 205, 86, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235)',
                        'rgba(54, 162, 235)',
                        // 'rgba(255, 205, 86)'
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
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString("de-DE");
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'start',
                        align: 'end',
                        formatter: function(value, context) {
                            return value.toLocaleString("de-DE");
                        },
                        labels: {
                            title: {
                                font: {
                                    size: 11,
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
                            },
                            callback: function(valor, index, valores) {
                                return valor.toLocaleString("de-DE");
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