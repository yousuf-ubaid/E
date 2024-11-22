<div id="container" style="min-width: 500px; height: 400px; margin: 2px; margin-bottom: 3px"></div>
<?php
foreach ($fcr as  $val) {

    $feedtot = ($val['chicksTotal'] + $val['noOfBirds']) / 2;
    $feedPercentage = ($val['feed'] * 50) / $feedtot;
    $weightPercentage = (($val['noOfBirds'] == 0) ? '0' :$val['birdsweight'] / $val['noOfBirds']);
    $totalfcr = number_format(($weightPercentage == 0) ? '0' : $feedPercentage / $weightPercentage,2);


   echo'<pre>'; print_r($val['chicksTotal']); echo '</pre>';

}?>
<script>

    $(document).ready(function (e) {
        var chart = Highcharts.chart('container', {

            chart: {
                type: 'column'
            },

            title: {
                text: 'Batch Overview'
            },

            subtitle: {
            },

            legend: {
                align: 'right',
                verticalAlign: 'middle',
                layout: 'vertical'
            },

            xAxis: {
                categories: [
                    <?php if (!empty($weight)) {
                        $i = 1;
                        foreach ($weight as  $val) {
                            echo "['" . $val['batchCode'] . "']";
                            echo ',';

                            $i++;
                        }
                    }
                    ?>
                ],
                labels: {
                    x: -10
                }
            },

            yAxis: {
                allowDecimals: false,
                title: {
                    text: 'Amount'
                }
            },

            series: [{
                name: 'Weight',
                data: [
                    <?php if (!empty($weight)) {
                    foreach ($weight as  $val) {
                        echo "" .$val['trans']/$val['birds']."";
                        echo ',';
                    }
                }
                    ?>
                ]
            }, {
                name: 'F.C.R',
                data: [<?php if (!empty($fcr)) {
                    $i = 1;
                    foreach ($fcr as  $val) {
                        echo "" .($val['feed'] * 50)/($val['chicksTotal'] + $val['noOfBirds'])/2 . "";
                        echo ',';

                        $i++;
                    }
                }
                    ?> ]
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            align: 'center',
                            verticalAlign: 'bottom',
                            layout: 'horizontal'
                        },
                        yAxis: {
                            labels: {
                                align: 'left',
                                x: 0,
                                y: -5
                            },
                            title: {
                                text: null
                            }
                        },
                        subtitle: {
                            text: null
                        },
                        credits: {
                            enabled: false
                        }
                    }
                }]
            }
        });

    });

    </script>