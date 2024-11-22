<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="box box-danger">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_performance_summary');?><!--Performance Summary--></h4>
        <div class="box-tools pull-right">
            <strong class="btn-box-tool"><?php echo $this->lang->line('common_currency');?><!--Currency--> :
                (<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)</strong>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                        class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                        class="fa fa-times"></i>
            </button>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;width: 100%">
        <div id="performanceSummary<?php echo $userDashboardID; ?>" style="height: 280px"></div>
    </div>
    <div class="overlay" id="overlay3<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>
<?php
$performanceSummaryArr = array();
$revenueArr = array();
$amount = 0;
if (!empty($performanceSummary)) {
    foreach ($performanceSummary as $val) {
        $color = '';
        if ($val["description"] == 'Revenue') {
            $color = '#00A65A';
            $revenueArr[] = "{name:'" . $val["description"] . "',y:100,x:" . abs((float)$val["amount"]) . ",color: '$color'}";
        }
        if ($val["description"] == 'COGS') {
            $color = '#F56954';
        }
        if ($val["description"] == 'Other Expense') {
           $color = '#3C8DBC';
        }
        if ($val["description"] == 'Gross Profit') {
          $color = '#00C0EF';
        }
        $amount += abs((float)$val["amount"]);
        if ($val["description"] != 'Revenue') {
            $y=0;
            if($totalRevenue) {
                $y = (abs((float)$val["amount"]) / $totalRevenue) * 100;
            }
            $performanceSummaryArr[] = "{name:'" . $val["description"] . "',y:" . round($y, 2) . ",x:" . abs((float)$val["amount"]) . ",color: '$color'}";
        }
    }
}
if ($amount == 0) {
    $performanceSummaryArr = array();
    $revenueArr = array();
}
?>
<script>
    var reflowchartPerformanceSummaryWidget<?php echo $userDashboardID; ?> = 0;
    var chartPerformanceSummary<?php echo $userDashboardID; ?>;
    $(function () {
        chartPerformanceSummary<?php echo $userDashboardID; ?> = Highcharts.chart('performanceSummary<?php echo $userDashboardID; ?>', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                marginTop: -10
            },
            title: {
                text: ''
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.x:,.1f}</b>'
            },
            labels: {
                items: [{
                    html: '',
                    style: {
                        left: '130px',
                        top: '18px',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                    }
                },
                    {
                        html: '',
                        style: {
                            left: '300px',
                            top: '18px',
                            color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                        }
                    }
                ]
            },
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            enabled: true
                        }
                    }
                }]
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                itemWidth: 120,
                symbolWidth: 10,
                symbolHight: 8,
                floating: false,
                borderWidth: 0,
                backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                shadow: false,
                itemStyle: {
                    font: '9pt Trebuchet MS, Verdana, sans-serif',
                    color: '#A0A0A0'
                }
            },
            plotOptions: {
                series: {
                  /*  innerSize: 105,
                    depth: 45,*/
                    showInLegend: true,
                    dataLabels: {
                        enabled: true,
                        formatter: function () {
                            var per = this.y;
                            return per.toFixed(2) + ' %';
                        },
                        distance: -15,
                        color: 'black'
                    }, point: {
                        events: {
                            legendItemClick: function () {
                                return false
                            }
                        }
                    },
                    allowPointSelect: true
                }
            },
            series: [{
                name: 'Types',
                colorByPoint: true,
                data: [<?php echo join(',', $revenueArr) ?>], center: [130, 100],
                size: 200,
                depth: 100,
                innerSize: 25
            }, {
                name: 'Types',
                colorByPoint: true,
                data: [<?php echo join(',', $performanceSummaryArr) ?>], center: [350, 100],
                size: 200,
                depth: 100,
                innerSize: 25,
                //showInLegend: false
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
