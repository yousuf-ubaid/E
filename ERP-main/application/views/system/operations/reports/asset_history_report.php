<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('accounts_payable', $primaryLanguage);
$title = $this->lang->line('accounts_payable_vendor_balance_summary');
$date_format_policy = date_format_policy();
$financeyear_arr = all_financeyear_drop(true);
$segment_arr = fetch_segment(true,false);
$from = convert_date_format($this->common_data['company_data']['FYBegin']);
$todt = convert_date_format(current_date());
$supplier_arr = all_supplier_drop(false);
$suppliergrp = all_group_supplier_drop(false);
echo head_page('Asset History', false);
?>
<style>
    .bgc {
        background-color: #e1f1e1;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>
<div>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <?php echo form_open('login/loginSubmit', ' name="frm_asset_history_report" id="frm_asset_history_report" class="form-group" role="form"'); ?>
        <input type="hidden" id="grouptyp" name="grouptyp">
            <div class="col-md-12">

                <div class="form-group col-sm-2">
                    <label for="">From</label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="from"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $from; ?>" id="from" class="form-control">
                    </div>
                </div>

                <div class="form-group col-sm-2">
                    <label for="">To</label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="to"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $todt; ?>" id="to" class="form-control">
                    </div>
                </div>



                <div class="form-group col-sm-1">
                    <label for="" style="color: white;">button</label>
                    <button style="margin-top: 5px" type="button" onclick="get_asset_history_report()"
                            class="btn btn-primary btn-xs">
                        <?php echo $this->lang->line('common_generate'); ?></button>
                </div>


            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<hr style="margin: 0px;">
<div id="div_asset_history">
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>




<script type="text/javascript">


    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });
    $('.headerclose').click(function () {
        fetchPage('system/operations/reports/asset_history_report.php', '', 'Asset History')
    });
    $(document).ready(function (e) {

        get_asset_history_report();

    });

    function get_asset_history_report() {
        var data = $("#frm_asset_history_report").serialize();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Operation/get_asset_history_report') ?>",
            data: data,
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_asset_history").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_asset_history_report');
        form.target = '_blank';
        form.action = '<?php echo site_url('Operation/get_asset_history_report_pdf'); ?>';
        form.submit();
    }



</script>
