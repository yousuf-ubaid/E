<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="box box-danger">
    <div class="box-header with-border">
        <h4 class="box-title">Total PO generated</h4>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                    class="fa fa-times"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;width: 100%">
        <div id="total_PO_generated<?php echo $userDashboardID; ?>" style="height: 250px"></div>
        <div class="col-sm-5">
            <span style="width: 80px;"><li class="fa fa-circle" style="color:#2b908f;"></li>&nbsp <b>Local : </b>&nbsp</span> <span><input class="POgenerated" id="local" value="<?php echo $local;?>" style="border: none;font-weight: bold; width: 50px; text-align: center" disabled></span>
        </div>
        <div class="col-sm-6">
            <span style="width: 130px;"><li class="fa fa-circle" style="color:#90ee7e;"></li>&nbsp <b>International : </b>&nbsp</span><span><input class="POgenerated" id="international" value="<?php echo $international;?>" style="border: none;font-weight: bold; width: 50px; text-align: center" disabled></span>
        </div>
    </div>
    <div class="overlay" id="overlay5<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>

<script>
    var reflowchartPerformanceSummaryWidget<?php echo $userDashboardID; ?> = 0;
    var chartPerformanceSummary<?php echo $userDashboardID; ?>;
    $(function () {
        chartPerformanceSummary<?php echo $userDashboardID; ?> = Highcharts.chart('total_PO_generated<?php echo $userDashboardID; ?>',{
            colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
                '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                        }
                    }
                },
                series: [{
                    name: 'Total PO',
                    colorByPoint: true,
                    data: [{
                        name: 'Local',
                        y: <?php echo $local ?>,
                        sliced: true,
                        selected: true
                    }, {
                        name: 'International',
                        y: <?php echo $international ?>
                    }]
                }]
            });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($('#template' +<?php echo $userDashboardID; ?>).hasClass('active') && reflowchartPerformanceSummaryWidget<?php echo $userDashboardID; ?> == 0) {
                chartPerformanceSummary<?php echo $userDashboardID; ?>.reflow();
                reflowchartPerformanceSummaryWidget<?php echo $userDashboardID; ?>= 1;
            }
        });
    });
</script>
