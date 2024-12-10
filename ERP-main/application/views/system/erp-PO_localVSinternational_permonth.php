<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$financeYear = $this->common_data["company_data"]["companyFinanceYearID"];
?>
<div class="box box-danger">
    <div class="box-header with-border">
        <h4 class="box-title">PO Local / International</h4>
        <div class="pull-right">
            <?php
            $financeYear_drop = all_financeyear_report_drop(true);
            echo form_dropdown('financeYear_payrollCost[]', $financeYear_drop, $financeYear, 'id="financeYear_POgenerated'.$userDashboardID.'"
                    onchange="load_PO_localVSinternational'.$userDashboardID.'()"');
            ?>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;width: 100%">
        <div id="total_PO_generated_bar<?php echo $userDashboardID; ?>" style="height: 250px"></div>
    </div>
    <div class="overlay" id="overlay6<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>

<script>

    load_PO_localVSinternational<?php echo $userDashboardID ?>();

    function load_PO_localVSinternational<?php echo $userDashboardID ?>(){
        var financeyearid=$('#financeYear_POgenerated<?php echo $userDashboardID ?>').val();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/fetch_PO_permonth_view'); ?>",
            data: {financeyearid: financeyearid,userDashboardID:<?php echo $userDashboardID ?>},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#total_PO_generated_bar<?php echo $userDashboardID ?>").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }
</script>
