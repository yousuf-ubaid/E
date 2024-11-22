<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>

<div class="box box-success {">
    <div class="box-header with-border">
        <h4 class="box-title">Top Suppliers</h4>
        <div class="box-tools pull-right">
            <div class="box-tools pull-right">
                <div style="margin-top: 7px"><label><?php echo $this->lang->line('common_currency');?><!--Currency-->:</label> <select id="toptensuppliercurrency<?php echo $userDashboardID ?>">
                        <option value="1" selected>Local Currency - <?php echo $this->common_data['company_data']['company_default_currency']?></option>
                        <option value="2">Reporting Currency - <?php echo  $this->common_data['company_data']['company_reporting_currency']?></option>
                    </select></div>
            </div>
        </div>
    </div>

    <div class="box-body" style="display: block;width: 100%">
        <div id="toptensupplier_view<?php echo $userDashboardID ?>"></div>
    </div>
    <div class="overlay" id="overlay7<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
</div>

<script>

    $('#toptensuppliercurrency'+<?php echo $userDashboardID ?>).change(function () {
        toptensupplier<?php echo $userDashboardID ?>();
    });

    toptensupplier<?php echo $userDashboardID ?>();
    function toptensupplier<?php echo $userDashboardID ?>(){
        var currencyID =  $("#toptensuppliercurrency"+<?php echo $userDashboardID ?>).val();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/toptensupplier_view'); ?>",
            data: {currencyID: currencyID,userDashboardID:<?php echo $userDashboardID ?>},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#toptensupplier_view<?php echo $userDashboardID ?>").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }
</script>

<?php
