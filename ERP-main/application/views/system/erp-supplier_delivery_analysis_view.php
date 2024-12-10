<div id="sup_delivery_analysis_<?php echo $userDashboardID ?>" style="height: 299px"></div>

<div class="modal fade" id="drilldownModal<?php echo $userDashboardID ?>" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title drilldown-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div id="supplier_delivery_analysis_drilldown<?php echo $userDashboardID ?>"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    <?php if(!empty($details['suppliers'])) { ?>
    Highcharts.chart('sup_delivery_analysis_<?php echo $userDashboardID ?>', {
        colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
            '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            title: {
                text: 'Top Suppliers'
            },
            labels:{
                formatter: function(){
                    var rV = '';
                    var someCat = this.value;
                    rV += someCat.substring(0, <?php echo $details['codelength']; ?>);
                    return rV ;
                }
            },
            categories: [<?php echo join(',', $details['suppliers']); ?>]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'PO Count'
            }
        },
        legend: {
            reversed: true
        },
        plotOptions: {
            series: {
                stacking: 'normal',
                cursor: 'pointer',
                point: {
                    events: {
                        click: function () {
                            load_supplier_delivery_analysis_drilldown(this.category);
                        }
                    }
                }
            }
        },

        tooltip: {
            headerFormat: '',
            pointFormat: '{point.Supplier}<br>Total PO : {point.TotalPO}'
        },

        series: [
            {
                name: 'over a months delay',
                data: [<?php
                    if(!empty($details['overMonth'])){
                        $i = 0;
                        foreach ($details['overMonth'] as $overMonth) { ?>
                            {"Supplier" : "<?php echo $details['Supplier'][$i] ?>", "TotalPO":"<?php echo $details['totalCount'][$i] ?> <br> Over a month Delay : <?php echo $overMonth ?>", "y":<?php echo $overMonth;  ?>},
                        <?php  $i++;
                        }
                    }

                    ?>]
            }, {
                name: '3 Weeks delay',
                data: [<?php
                    if(!empty($details['threeWeek'])){
                        $i = 0;
                    foreach ($details['threeWeek'] as $threeWeek) { ?>
                    {"Supplier" : "<?php echo $details['Supplier'][$i] ?>","TotalPO":"<?php echo $details['totalCount'][$i] ?> <br> 3 Weeks delay : <?php echo $threeWeek ?>","y":<?php echo $threeWeek;  ?>},

                    <?php   $i++; }
                    }

                    ?>]
            }, {
                name: '2 Weeks delay',
                data: [<?php
                    if(!empty($details['twoWeek'])){
                        $i = 0;
                    foreach ($details['twoWeek'] as $twoWeek) { ?>
                    {"Supplier" : "<?php echo $details['Supplier'][$i] ?>","TotalPO":"<?php echo $details['totalCount'][$i] ?> <br> 2 Weeks delay : <?php echo $twoWeek ?>","y":<?php echo $twoWeek;  ?>},

                    <?php  $i++; }
                    }

                    ?>]
            }, {
                name: '1 Week delay',
                data: [<?php
                    if(!empty($details['oneWeek'])){
                    $i = 0;
                    foreach ($details['oneWeek'] as $oneWeek) { ?>
                    {"Supplier" : "<?php echo $details['Supplier'][$i] ?>","TotalPO":"<?php echo $details['totalCount'][$i] ?> <br> 1 Week delay : <?php echo $oneWeek ?>","y":<?php echo $oneWeek;  ?>},

                    <?php $i++; }
                    }

                    ?>]
            },{
                name: 'On time',
                data:  [<?php
                    if(!empty($details['ontime'])){
                    $i = 0;
                    foreach ($details['ontime'] as $ontime) { ?>
                    {"Supplier" : "<?php echo $details['Supplier'][$i] ?>","TotalPO":"<?php echo $details['totalCount'][$i] ?> <br> On time : <?php echo $ontime ?>","y":<?php echo $ontime;  ?>},

                    <?php  $i++; }
                    }

                    ?>]
            }
        ]
    });
    <?php } ?>

    function load_supplier_delivery_analysis_drilldown(supplier) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Finance_dashboard/supplier_delivery_analysis_drilldown'); ?>",
            data: {'supplier' : supplier},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#drilldownModal<?php echo $userDashboardID ?>').modal('show');
                $("#supplier_delivery_analysis_drilldown<?php echo $userDashboardID ?>").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }
</script>
