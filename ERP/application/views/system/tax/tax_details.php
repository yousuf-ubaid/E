<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('tax', $primaryLanguage);
$title = $this->lang->line('sales_markating_sales_order_report');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
echo head_page($this->lang->line('tax_statement'), false);
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
        <?php echo form_open('login/loginSubmit', ' name="frm_rpt_tax_details" id="frm_rpt_tax_details" class="form-group" role="form"'); ?>
            <div class="col-md-12">
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date') . ' ' . $this->lang->line('common_from'); ?></label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="datefrom"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $start_date; ?>" id="datefrom" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-2">
                    <label for=""><?php echo $this->lang->line('common_date') . ' ' . $this->lang->line('common_to'); ?></label>
                    <div class="input-group datepicto">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="dateto"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="<?php echo $current_date; ?>" id="dateto" class="form-control">
                    </div>
                </div>
                <div class="form-group col-sm-3">
                    <label for=""><?php echo $this->lang->line('tax_type'); ?></label>
                    <br>
                    <?php echo form_dropdown('taxType[]', fetch_tax_type(true,false), '', ' class="form-control" multiple="multiple" id="taxType" required'); ?>
                </div>
                <div class="form-group col-sm-3">
                    <label for=""><?php echo $this->lang->line('common_currency'); ?></label>
                    <select name="currency" class="form-control " id="currency" onchange="get_tax_details()">
                        <option value="1"><?php echo $this->lang->line('common_local_currency'); ?></option>
                        <option value="2" selected=""><?php echo $this->lang->line('common_reporting_currency'); ?></option>
                    </select>
                </div>

                <div class="form-group col-sm-2">
                    <label for=""></label>
                    <button style="margin-top: 25px" type="button" onclick="get_tax_details()"
                            class="btn btn-primary-new size-sm">
                        <?php echo $this->lang->line('common_generate'); ?></button>
                </div>
            </div>
        <?php echo form_close(); ?>
    </fieldset>
</div>
<div id="div_leave_history">
</div>
<div class="modal fade" id="returndrilldownModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title drilldown-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <table id="tbl_rpt_salesreturn" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_document_code'); ?></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?></th>
                        <th><?php echo $this->lang->line('common_currency'); ?></th>
                        <th><?php echo $this->lang->line('common_amount'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="salesreturn">

                    </tbody>
                    <tfoot id="salesreturnfooter" class="table-borded">

                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $(document).ready(function (e) {
        $('.select2').select2();
        $('#taxType').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#taxType").multiselect2('selectAll', false);
        $("#taxType").multiselect2('updateButtonText');
        $('.headerclose').click(function () {
            fetchPage('system/tax/tax_details', '', '<?php echo $this->lang->line('tax_statement'); ?>')
        });
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        $('.datepicto').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        get_tax_details();
    });

    function get_tax_details() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Tax/get_tax_details') ?>",
            data: $("#frm_rpt_tax_details").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_leave_history").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_tax_details');
        form.target = '_blank';
        form.action = '<?php echo site_url('Tax/get_tax_details_report_pdf'); ?>';
        form.submit();
    }


</script>
