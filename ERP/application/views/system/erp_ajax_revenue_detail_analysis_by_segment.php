<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="box box-danger">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_revenue_by_segment');?><!--Revenue by Segment--></h4>

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
    <div class="box-body" style="display: block;height: 250px;overflow: auto "
         id="revenuedetailanalysisbysegment<?php echo $userDashboardID; ?>">
        <?php
        $color = array("#00C0EF", "#DD4B39", "#00A65A", "#F39C12", "#4B94C0", "#666666", "#ffc0cb", "#c39797", "#6dc066", "#794044", "#6f5b57", "#b2c4ff", "#ffc7b2", "#ffb2c4", "#339988", "#D4E79E", "#78A6B0", "#9BBFA6", "#723F00", "#FFA459");
        if (!empty($revenueDetailAnalysisBySegment)) {
            foreach ($revenueDetailAnalysisBySegment as $key => $val) {
                $percentage = 0;
                if (!empty($totalRevenue)) {
                    $percentage = (($val["companyReportingAmount"] / $totalRevenue) * 100);
                } else {
                    $percentage = 0;
                }
                ?>
                <div class="progress-group">
                    <div style=" cursor: pointer;" onclick="">
                        <span class="progress-text"><?php echo $val["description"] ?></span><span
                                class="progress-number"><span
                                    style="color: #4B94C0"><?php echo number_format($val["companyReportingAmount"]) ?></span> ► <b><span
                                        class="text-orange"><?php echo round($percentage) . "%" ?></span></b></span>
                    </div>

                    <div class="progress sm">
                        <div class="progress-bar"
                             style="background-color: <?php echo $color[$key] ?>;width: <?php echo $percentage . "%" ?>;"></div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="overlay" id="overlay19<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>
<!--modal report-->
<div class="modal fade" id="finance_report_drilldown_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span class="myModalLabel"></span></h4>
            </div>
            <div class="modal-body">
                <div id="reportContentDrilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.modal').on('hidden.bs.modal', function (e) {
        if ($('.modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }
    });
    /*call report Revenue By Segment content*/
    function dashboardRevenueBySegment<?php echo $userDashboardID; ?>(glCode, masterCategory, glDescription, currency, month) {
        var captionChk = ['Reporting Currency'];
        var RptID = 'FIN_IS';
        var rptType = 3;
        var fieldNameChk = [currency];
        var year = <?php echo $this->input->post("period");?>;
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Report/dashboardReportDrilldownView') ?>",
            data: {
                RptID: RptID,
                fieldNameChk: fieldNameChk,
                captionChk: captionChk,
                rptType: rptType,
                glCode: glCode,
                currency: currency,
                masterCategory: masterCategory,
                year: year
            },
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#reportContentDrilldown").html(data);
                $(".myModalLabel").html(glDescription);
                $('#finance_report_drilldown_modal').modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;
    }
</script>
