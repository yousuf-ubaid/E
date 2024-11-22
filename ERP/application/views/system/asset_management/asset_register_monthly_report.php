<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_monthly_report');
echo head_page($title, false);

/*
echo head_page('Asset Monthly Depreciation Report', false);*/

$companyId = current_companyID();
$financeyear_arr = all_financeyear_drop();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="tab-content">
    <div id="step1" class="tab-pane active" style="box-shadow: none;">
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="financeyear"><?php echo $this->lang->line('assetmanagement_financial_year');?><!--Financial Year--></label>
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear""'); ?>
            </div>
            <div class="col-sm-1">
                <label for="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&emsp;</label>
                <button type="button" class="btn btn-primary btn-flat" id="" onclick="generateAssetMonthlyDepreciation()">
                    <?php echo $this->lang->line('common_generate');?><!--Generate-->
                </button>
            </div>
        </div>
        <div class="col-md-12 no-padding" id="appendData"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {


    });

    function generateAssetMonthlyDepreciation() {
        var financeyear = $('#financeyear').val();

        if (financeyear == '') {
            notification("Financial Year is required");
            return false;
        }

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('AssetManagement/assetMonthlyDepreciationSummary'); ?>",
            data: {financeyear: financeyear},
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#appendData').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }
</script>