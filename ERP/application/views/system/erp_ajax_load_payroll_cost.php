<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<div class="box box-info">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_external_payroll_cost');?><!--Payroll Cost--></h4>

        <div class="pull-right">
            <?php
            $financeYear_drop = all_financeyear_report_drop(true);
            echo form_dropdown('financeYear_payrollCost[]', $financeYear_drop, $financeYear, 'id="financeYear_payrollCost'.$userDashboardID.'"
                    onchange="load_payroll_cost_view'.$userDashboardID.'()"');
            ?>
        </div>

    </div>

    <div class="box-body" style="display: block;width: 100%">
        <div id="payrollCostView_<?php echo $userDashboardID ?>"></div>
    </div>
    <div class="overlay" id="overlay17<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
</div>


<script>
    load_payroll_cost_view<?php echo $userDashboardID ?>();


    function load_payroll_cost_view<?php echo $userDashboardID ?>(){
        var financeyearid=$('#financeYear_payrollCost<?php echo $userDashboardID ?>').val();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/load_payroll_cost_view'); ?>",
            data: {financeyearid: financeyearid,userDashboardID:<?php echo $userDashboardID ?>},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#payrollCostView_<?php echo $userDashboardID ?>").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }
</script>
