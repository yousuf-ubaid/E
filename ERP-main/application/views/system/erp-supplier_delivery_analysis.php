<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$financeYear = $this->common_data["company_data"]["companyFinanceYearID"];
?>
<div class="box box-danger">
    <div class="box-header with-border">
        <h4 class="box-title">Supplier Delivery Analysis</h4>
    </div>
    <div class="box-body" style="display: block;width: 100%">
        <div id="supplier_delivery_analysis<?php echo $userDashboardID; ?>" style="height: 300px"></div>
    </div>
    <div class="overlay" id="overlay13<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
</div>

<script>

    load_PO_localVSinternational<?php echo $userDashboardID ?>();

    function load_PO_localVSinternational<?php echo $userDashboardID ?>(){
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/supplier_delivery_analysis_view'); ?>",
            data: {userDashboardID:<?php echo $userDashboardID ?>},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#supplier_delivery_analysis<?php echo $userDashboardID ?>").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }
</script>