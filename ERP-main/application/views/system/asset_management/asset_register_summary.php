<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('assetmanagementnew', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('assetmanagement_asset_register_summary');
echo head_page($title, false);


/*
echo head_page('Asset Register Summary', false);*/
$companyId = current_companyID();
$company_code = $this->common_data['company_data']['company_code'];
$decimal_places = $this->common_data['company_data']['company_default_decimal'];

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="tab-content">
    <div id="step1" class="tab-pane active" style="box-shadow: none;">
        <div class="row">
            <div class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('assetmanagement_date_as_of');?><!--Date As of--> <span title="required field"
                                               style="color:red; font-weight: 600; font-size: 12px;">*</span></label>
                <div class=" input-group">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="dateAsOf" value="" id="dateAsOf" class="form-control"
                           autocomplete="off">
                </div>
            </div>
            <div class="col-sm-1">
                <label for="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&emsp;</label>
                <button type="button" class="btn btn-primary btn-flat" id="" onclick="generateAssetRegisterSummery()">
                    <?php echo $this->lang->line('common_generate');?><!--Generate-->
                </button>
            </div>
        </div>
        <div class="col-md-12 no-padding" id="appendData"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.headerclose').click(function(){
            fetchPage('system/asset_management/asset_register_summary','','Asset Register Summary');
        });
        $('#dateAsOf').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            //$('#add_new_asset_form').bootstrapValidator('revalidateField', 'dateAQ');
            $(this).datepicker('hide');
        });
        $("#dateAsOf").datepicker("setDate", new Date());
    });

    //asset_register_summary_generated.php
    function generateAssetRegisterSummery() {
        var dateAsOf = $('#dateAsOf').val();

        if (dateAsOf == '') {
            notification("Date is required");
            return false;
        }

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('AssetManagement/assetRegisterSummaryGenerated'); ?>",
            data: {dateAsOf: dateAsOf},
            dataType: "html",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                $('#appendData').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }
</script>