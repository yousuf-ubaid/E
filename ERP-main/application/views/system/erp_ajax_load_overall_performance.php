<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard', $primaryLanguage);
?>
<div class="box box-warning">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_overall_performance');?><!--Overall Performance--></h4>
        <div class="box-tools pull-right">
            <strong class="btn-box-tool"><?php echo $this->lang->line('common_currency');?><!--Currency--> : (<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)</strong>

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
        <div class="col-md-2 text-center">
            <div class="small">
                <i class="fa fa-bolt"></i> <?php echo $this->lang->line('dashboard_total_revenue');?><!--Total Revenue-->
            </div>
            <div class="box box-solid primary-gradient" style="margin-top: 70px">
                <div class="box-body no-padding">
                    <div id="totalRevenue<?php echo $userDashboardID; ?>">
                        <?php
                        if ($totalRevenue == 0) {
                            echo "<h3 style='cursor: pointer;' onclick='totalRevenue".$userDashboardID."()'><i class='fa fa-angle-left'></i> " . format_number($totalRevenue) . "</h3>";
                        } else if ($totalRevenue > 0) {
                            echo "<h3 style='cursor: pointer;' onclick='totalRevenue".$userDashboardID."()'><i class='fa fa-angle-up'></i> " . format_number($totalRevenue) . "</h3>";
                        } else {
                            echo "<h3 style='cursor: pointer;' onclick='totalRevenue".$userDashboardID."()'><i class='fa fa-angle-down'></i> " . format_number($totalRevenue) . "</h3>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="small text-center">
                <i class="fa fa-line-chart" aria-hidden="true"></i> <?php echo $this->lang->line('dashboard_profit_and_loss');?><!--Profit and Loss-->
            </div>
            <div id="overallperformance<?php echo $userDashboardID; ?>" style="height: 250px"></div>
        </div>
        <div class="col-md-2 text-center">
            <div class="small">
                <i class="fa fa-clock-o"></i> <?php echo $this->lang->line('dashboard_net_profit');?><!--Net Profit-->
            </div>
            <div class="box box-solid primary-gradient" style="margin-top: 70px">
                <div class="box-body no-padding">
                    <div id="netProfit<?php echo $userDashboardID; ?>">
                        <?php
                        if ($netProfit == 0) {
                            echo "<h3 style='cursor: pointer;' onclick='totalRevenue".$userDashboardID."()'><i class='fa fa-angle-left'></i> " . format_number($netProfit) . "</h3>";
                        } else if ($netProfit > 0) {
                            echo "<h3 style='cursor: pointer;' onclick='totalRevenue".$userDashboardID."()'><i class='fa fa-angle-up'></i> " . format_number($netProfit) . "</h3>";
                        } else {
                            echo "<h3 style='cursor: pointer;' onclick='totalRevenue".$userDashboardID."()'><i class='fa fa-angle-down'></i> " . format_number($netProfit) . "</h3>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay" id="overlay1<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
    <!--modal report-->
    <div class="modal fade" id="finance_report_modal<?php echo $userDashboardID; ?>" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" style="width: 100%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('dashboard_income_statement');?><!--Income Statement--></h4>
                </div>
                <div class="modal-body">
                    <div id="reportContent<?php echo $userDashboardID; ?>"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>

    <!-- /.box-body -->
    <!--modal report-->
    <div class="modal fade" id="finance_report_modal_dd<?php echo $userDashboardID; ?>" tabindex="1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" style="width: 100%" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel<?php echo $userDashboardID; ?>"><?php echo $this->lang->line('dashboard_income_statement');?><!--Income Statement--></h4>
                </div>
                <div class="modal-body">
                    <div id="reportContentDD<?php echo $userDashboardID; ?>"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$overallPerformanceArr = array();
$amount = 0;
if (!empty($overallPerformance)) {
    foreach ($overallPerformance as $val) {
        $data = array();
        $color = "";
        if ($val["description"] == 'Revenue') {
            $color = '#00A65A';
        }
        if ($val["description"] == 'COGS') {
            $color = '#F56954';
        }
        if ($val["description"] == 'Other Expense') {
            $color = '#3C8DBC';
        }
        if ($val["description"] == 'GP') {
            $color = '#00C0EF';
        }
        $series = "{name:'" . $val["description"] . "',color: '$color',data:[";
        if (!empty($months)) {
            foreach ($months as $val2) {
                $amount += abs((float)$val[$val2]);
                $data[] = abs((float)$val[$val2]);
            }
        }
        $series .= join(",", $data);
        $series .= "]}";
        $overallPerformanceArr[] = $series;
    }
}
if ($amount == 0) {
    $overallPerformanceArr = array();
}
?>
<script>
    let reflowoverallperformanceWidget<?php echo $userDashboardID; ?> = 0;
    let overallperformance<?php echo $userDashboardID; ?>;
    $(document).ready(function () {
        $('.modal').on('hidden.bs.modal', function (e) {
            if($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });
        Highcharts.setOptions({
            lang: {
                thousandsSep: ','
            }
        });
        overallperformance<?php echo $userDashboardID; ?> =  Highcharts.chart('overallperformance<?php echo $userDashboardID; ?>', {
            chart: {
                type: 'areaspline',
                scrollablePlotArea: {
                    minWidth: 600,
                    scrollPositionX: 1
                }
            },
            title: {
                text: '',
                x: -20 //center
            },
            subtitle: {
                text: '',
                x: -20
            },
            plotOptions: {
                areaspline: {
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 2
                    }
                }
            },
            xAxis: {
                categories: [<?php echo "'" . join("','", $months2) . "'" ?>],
                labels: {
                    rotation: -60
                },
                gridLineColor: '#F0F0F0',
                gridLineWidth: 1
            },

            yAxis: {
                title: {
                    text: ''
                },
                gridLineColor: '#F0F0F0',
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:2px">{series.name}: </td>' +
                ' <td style="padding:2px"><b>{point.y:,.0f}</b></td></tr>',
                footerFormat: '</table>',
                shared: false,
                useHTML: true
            },
         
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'top',
                floating: false,
                borderWidth: 0,
                backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                shadow: false,
                itemStyle: {
                    font: '8pt Trebuchet MS, Verdana, sans-serif',
                    color: '#A0A0A0'
                },
                symbolWidth: 10,
                symbolHeight: 9
            },
            series: [<?php echo join(',', $overallPerformanceArr) ?>]
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if($('#template'+<?php echo $userDashboardID; ?>).hasClass('active') && reflowoverallperformanceWidget<?php echo $userDashboardID; ?> == 0) {
                overallperformance<?php echo $userDashboardID; ?>.reflow();
                reflowoverallperformanceWidget<?php echo $userDashboardID; ?>=1;
            }
        });
    });

    function totalRevenue<?php echo $userDashboardID; ?>(){
        const year = '<?php echo $this->input->post("period");?>';
        const RptID = 'FIN_IS';
        const fieldNameChk = ['companyReportingAmount'];
        const captionChk = new Array('Reporting Currency');
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/dashboardReportView') ?>",
            data: {RptID: RptID, year:year,fieldNameChk:fieldNameChk,captionChk:captionChk,userDashboardID:<?php echo $userDashboardID; ?>},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContent"+<?php echo $userDashboardID; ?>).html(data);
                $('#finance_report_modal'+<?php echo $userDashboardID; ?>).modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;
    }

    function generateDrilldownReport<?php echo $userDashboardID; ?>(glCode,masterCategory,glDescription,currency,month){
        const captionChk = ['Reporting Currency'];
        const RptID = 'FIN_IS';
        const rptType = 5;
        const fieldNameChk = [currency];
        const year = '<?php echo $this->input->post("period");?>';
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/dashboardReportDrilldownView') ?>",
            data: {RptID : RptID,fieldNameChk : fieldNameChk,captionChk : captionChk,rptType:rptType,glCode:glCode,currency:currency,masterCategory:masterCategory,year:year},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContentDD"+<?php echo $userDashboardID; ?>).html(data);
                $("#myModalLabel"+<?php echo $userDashboardID; ?>).html(glDescription);
                $('#finance_report_modal_dd'+<?php echo $userDashboardID; ?>).modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;
    }

</script>
