<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="box box-info">
    <div class="box-header with-border">
        <h4 class="box-title">Top Customers</h4>
        <div class="box-tools pull-right">
            <div style="margin-top: 7px"><label><?php echo $this->lang->line('common_currency');?><!--Currency-->:</label> <select id="toptencustomercurrency<?php echo $userDashboardID ?>">
                    <option value="1" selected>Local Currency - <?php echo $this->common_data['company_data']['company_default_currency']?></option>
                    <option value="2">Reporting Currency - <?php echo  $this->common_data['company_data']['company_reporting_currency']?></option>
                </select></div>
        </div>
    </div>

    <div class="box-body" style="display: block;width: 100%">
        <div id="toptencustomer_view<?php echo $userDashboardID ?>"></div>
    </div>
    <div class="overlay" id="overlay120<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
</div>

<script>

    $('#toptencustomercurrency'+<?php echo $userDashboardID ?>).change(function () {
        toptencustomer_view<?php echo $userDashboardID ?>();
    });

    toptencustomer_view<?php echo $userDashboardID ?>();


    function toptencustomer_view<?php echo $userDashboardID ?>(){
        var currencyID =  $("#toptencustomercurrency"+<?php echo $userDashboardID ?>).val();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/toptencustomer_view'); ?>",
            data: {currencyID: currencyID,userDashboardID:<?php echo $userDashboardID ?>},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#toptencustomer_view<?php echo $userDashboardID ?>").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }
</script>

<?php
