<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('tax', $primaryLanguage);
echo head_page($this->lang->line('tax_vat_report'), false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$cdate=current_date(FALSE);
$startdate =date('Y-01-01', strtotime($cdate));
$start_date = convert_date_format($startdate);
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

<div class="row" style="margin: 1%">
    <ul class="nav nav-tabs mainpanel">
        <li class="active">
            <a onclick="load_output_vat_return_filling_report()" data-id="0" href="#step1" data-toggle="tab" aria-expanded="true">
                <span>
                <i class="fa fa-list tachometerColor" aria-hidden="true" style="color: #50749f;font-size: 16px;"></i>
                    &nbsp;&nbsp;<?php echo $this->lang->line('tax_output_vat_summary_report');?> <!--Output VAT Summary Report-->
                </span>
            </a>
        </li>
        <li>
            <a onclick="load_input_vat_return_filling_report()" id="" data-id="0" href="#step2" data-toggle="tab" aria-expanded="true">
                <span>
                    <i class="fa fa-list tachometerColor" aria-hidden="true" style="color: #50749f;font-size: 16px;"></i>
                    &nbsp;<?php echo $this->lang->line('tax_input_vat_summary_report');?><!--Input VAT Summary Report-->
                </span>
            </a>
        </li>
    </ul>
</div>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <div>
            <fieldset class="scheduler-border">
                <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                <?php echo form_open('login/loginSubmit', ' name="frm_output_vat_return_filling_report" id="frm_output_vat_return_filling_report" class="form-group" role="form"'); ?>
                    <div class="col-md-12">
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_date') . ' ' . $this->lang->line('common_from'); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="datefrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $start_date; ?>" id="datefrom_output" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_date') . ' ' . $this->lang->line('common_to'); ?></label>
                            <div class="input-group datepicto">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateto" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="dateto_output" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('tax_type'); ?></label>
                            <br>
                            <?php echo form_dropdown('taxType[]', vat_type_dropdown(false), '', ' class="form-control" multiple="multiple" id="taxType_output" required'); ?>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_document_types'); ?></label>
                            <br>
                            <?php echo form_dropdown('documentType[]', array('CINV' => 'Customer Invoice', 'DO' => 'Delivery Order', 'CN' => 'Credit Note', 'RV' => 'Receipt Voucher','POS'=>'General POS','RPOS'=>'Restaurant POS','RET'=>'Sales Return'), '', ' class="form-control" multiple="multiple" id="documentType_output" required'); ?>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_customer'); ?></label>
                            <br>
                            <?php echo form_dropdown('customerAutoID[]', all_customer_drop(false,null,'vat_report'), '', ' class="form-control" multiple="multiple" id="customerAutoID_output" required'); ?>
                        </div>
                        <div class="form-group col-sm-2 hide">
                            <label for=""><?php echo 'Acount Type'; ?></label>
                            <br>
                            <?php echo form_dropdown('accountType[]', array('1'=>'Control Account', '2'=>'Transfer Account'), '', ' class="form-control" multiple="multiple" id="accountType_output" required'); ?>
                        </div>
                        <div class="form-group col-sm-2">
                            <button style="margin-top: 25px" type="button" onclick="load_output_vat_return_filling_report()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_generate'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </fieldset>
        </div>
        <hr style="margin: 0px;">
        <div id="div_output_vat_return_filling_report" style="padding-right: 20px;"></div>
        <br>
    </div>

    <div id="step2" class="tab-pane">
        <div>
            <fieldset class="scheduler-border">
                <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                <?php echo form_open('login/loginSubmit', ' name="frm_input_vat_return_filling_report" id="frm_input_vat_return_filling_report" class="form-group" role="form"'); ?>
                    <div class="col-md-12">
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_date') . ' ' . $this->lang->line('common_from'); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="datefrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $start_date; ?>" id="datefrom_input" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_date') . ' ' . $this->lang->line('common_to'); ?></label>
                            <div class="input-group datepicto">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="dateto" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="dateto_input" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('tax_type'); ?></label>
                            <br>
                            <?php echo form_dropdown('taxType[]', vat_type_dropdown(false), '', ' class="form-control" multiple="multiple" id="taxType_input" required'); ?>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_document_types'); ?></label>
                            <br>
                            <?php echo form_dropdown('documentType[]', array('BSI' => 'Supplier Invoice', 'PV' => 'Payment Voucher', 'DN' => 'Debit Note', 'GRV' => 'Good Received Voucher'), '', ' class="form-control" multiple="multiple" id="documentType_input" required'); ?>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_supplier'); ?></label>
                            <br>
                            <?php echo form_dropdown('supplierAutoID[]', all_supplier_drop(false), '', ' class="form-control" multiple="multiple" id="supplierAutoID_input" required'); ?>
                        </div>
                        <div class="form-group col-sm-2 hide">
                            <label for=""><?php echo 'Acount Type'; ?></label>
                            <br>
                            <?php echo form_dropdown('accountType[]', array('1'=>'Control Account', '2'=>'Transfer Account'), '', ' class="form-control" multiple="multiple" id="accountType_input" required'); ?>
                        </div>

                        <div class="form-group col-sm-2">
                            <button style="margin-top: 25px" type="button" onclick="load_input_vat_return_filling_report()" class="btn btn-primary btn-xs"><?php echo $this->lang->line('common_generate'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </fieldset>
        </div>
        <hr style="margin: 0px;">
        <div id="div_input_vat_return_filling_report" style="padding-right: 20px;"></div>
        <br>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    $(document).ready(function (e) {
        $('.select2').select2();
        Inputmask().mask(document.querySelectorAll("input"));
        $('.headerclose').click(function () {
            fetchPage('system/tax/vat_report', '', '<?php echo $this->lang->line('tax_vat_report'); ?>')
        });
        
        $('#taxType_output').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#taxType_output").multiselect2('selectAll', false);
        $("#taxType_output").multiselect2('updateButtonText');
      
        $('#documentType_output').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#documentType_output").multiselect2('selectAll', false);
        $("#documentType_output").multiselect2('updateButtonText');
      
        $('#customerAutoID_output').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#customerAutoID_output").multiselect2('selectAll', false);
        $("#customerAutoID_output").multiselect2('updateButtonText');
      
        $('#accountType_output').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#accountType_output").multiselect2('selectAll', false);
        $("#accountType_output").multiselect2('updateButtonText');
        
        $('#taxType_input').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#taxType_input").multiselect2('selectAll', false);
        $("#taxType_input").multiselect2('updateButtonText');
      
        $('#documentType_input').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#documentType_input").multiselect2('selectAll', false);
        $("#documentType_input").multiselect2('updateButtonText');
      
        $('#supplierAutoID_input').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#supplierAutoID_input").multiselect2('selectAll', false);
        $("#supplierAutoID_input").multiselect2('updateButtonText');
      
      $('#accountType_input').multiselect2({
          enableCaseInsensitiveFiltering: true,
          includeSelectAllOption: true,
          selectAllValue: 'select-all-value',
          buttonWidth: 150,
          maxHeight: 200,
          numberDisplayed: 1
      });
      $("#accountType_input").multiselect2('selectAll', false);
      $("#accountType_input").multiselect2('updateButtonText');
      
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {});

        $('.datepicto').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {});

        load_output_vat_return_filling_report();
    });

    function load_output_vat_return_filling_report() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Tax/load_output_vat_return_filling_report') ?>",
            data: $("#frm_output_vat_return_filling_report").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_output_vat_return_filling_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_input_vat_return_filling_report() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Tax/load_input_vat_return_filling_report') ?>",
            data: $("#frm_input_vat_return_filling_report").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_input_vat_return_filling_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf() {
        var form = document.getElementById('frm_output_vat_return_filling_report');
        form.target = '_blank';
        form.action = '<?php echo site_url('Tax/load_output_vat_return_filling_report_pdf'); ?>';
        form.submit();
    }

    function generateReportPdfInputVat() {
        var form = document.getElementById('frm_input_vat_return_filling_report');
        form.target = '_blank';
        form.action = '<?php echo site_url('Tax/load_input_vat_return_filling_report_pdf'); ?>';
        form.submit();
    }
    </script>
