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
    .tab-style-one.active{ background-color: #696CFF; border-color: #696CFF; }
    .tab-style-one.active:hover{ background-color: #7e5871; border-color: #7e5871; }
    .tab-style-one a{ font-size:12px !important; font-weight:600 !important; }
    .nav-tabs>li.active>a{ background-color: transparent !important; box-shadow:none; color:#fff !important; }
    .nav-tabs > li > a{ box-shadow:none !important; }
    .nav-tabs > li > a:hover{ box-shadow:none !important; background-color: transparent !important; }
    .nav-tabs > li:hover a{ color:#fff !important; }
    .alert-warning{ box-shadow: 0px 10px 30px 0px rgb(82 63 105 / 5%); border: none; border-radius: 0.475rem; }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<div id="filter-panel" class="collapse filter-panel">
</div>

<div class="row" style="margin: 1%">
    <ul class="nav nav-tabs mainpanel">
        <li class="btn-default-new size-sm tab-style-one mr-1 active">
            <a data-id="0" href="#step1" data-toggle="tab" aria-expanded="true">
                <span>
                <i class="fa fa-file tachometerColor" aria-hidden="true" style="color: #ffffff;font-size: 12px;"></i>
                    &nbsp;&nbsp;<?php echo $this->lang->line('tax_output_vat_summary_report');?> <!--Output VAT Summary Report-->
                </span>
            </a>
        </li>
        <li class="btn-default-new size-sm tab-style-one ">
            <a data-id="0" href="#step2" data-toggle="tab" aria-expanded="true">
                <span>
                    <i class="fa fa-file tachometerColor" aria-hidden="true" style="color: #ffffff;font-size: 12px;"></i>
                    &nbsp;<?php echo $this->lang->line('tax_input_vat_summary_report');?><!--Input VAT Summary Report-->
                </span>
            </a>
        </li>
    </ul>
</div>
<div class="tab-content">
    <div id="step1" class="tab-pane tab-s1 active">
        <div>
            <fieldset class="scheduler-border">
                <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                <?php echo form_open('login/loginSubmit', ' name="frm_output_vat_summary_report" id="frm_output_vat_summary_report" class="form-group" role="form"'); ?>
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
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo 'Acount Type'; ?></label>
                            <br>
                            <?php echo form_dropdown('accountType[]', array('1'=>'Control Account', '2'=>'Transfer Account'), '', ' class="form-control" multiple="multiple" id="accountType_output" required'); ?>
                        </div>
                        
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo 'Tax Category'; ?></label>
                            <br>
                            <?php echo form_dropdown('taxCategory[]', array('1' => $this->lang->line('common_other')/*'Other'*/,'2' =>'VAT'/*'VAT'*/), '', ' class="form-control" multiple="multiple" id="taxCategory_output" required'); ?>
                        </div>
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_currency'); ?></label>
                            <select name="currency" class="form-control " id="currency_output" onchange="load_output_vat_summary_report()">
                                <option value="1"  selected="selected"><?php echo $this->lang->line('common_local_currency'); ?></option>
                                <option value="2"><?php echo $this->lang->line('common_reporting_currency'); ?></option>
                            </select>
                        </div>
                        <div class="form-group col-sm-2">
                            <button style="margin-top: 25px" type="button" onclick="load_output_vat_summary_report()" class="btn btn-primary-new size-sm"><?php echo $this->lang->line('common_generate'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </fieldset>
        </div>
        <br>
        <div id="div_output_vat_summary_report"></div>
        <br>
    </div>

    <div id="step2" class="tab-pane tab-s1">
        <div>
            <fieldset class="scheduler-border">
                <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
                <?php echo form_open('login/loginSubmit', ' name="frm_input_vat_summary_report" id="frm_input_vat_summary_report" class="form-group" role="form"'); ?>
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
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo 'Acount Type'; ?></label>
                            <br>
                            <?php echo form_dropdown('accountType[]', array('1'=>'Control Account', '2'=>'Transfer Account'), '', ' class="form-control" multiple="multiple" id="accountType_input" required'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-sm-2">
                            <label for=""><?php echo $this->lang->line('common_currency'); ?></label>
                            <select name="currency" class="form-control " id="currency_input" onchange="load_input_vat_summary_report()">
                                <option value="1"><?php echo $this->lang->line('common_local_currency'); ?></option>
                                <option value="2" selected=""><?php echo $this->lang->line('common_reporting_currency'); ?></option>
                            </select>
                        </div>
                        <div class="form-group col-sm-3">
                            <label for="">&nbsp;</label>
                            <div class="input-group" id="">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="viewRCMapplied" id="viewRCMapplied_input" value="1" checked>
                                </span>
                                <input type="text" class="form-control" disabled="" value="View RCM Appied Documents">
                            </div>
                        </div>
                        <div class="form-group col-sm-2">
                            <button style="margin-top: 25px" type="button" onclick="load_input_vat_summary_report()" class="btn btn-primary-new size-sm"><?php echo $this->lang->line('common_generate'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </fieldset>
        </div>
        <hr style="margin: 0px;">
        <div id="div_input_vat_summary_report"></div>
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


        $('#taxCategory_output').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });

        $("#taxCategory_output").multiselect2('selectAll', false);
        $("#taxCategory_output").multiselect2('updateButtonText');
        
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
    });

    function load_output_vat_summary_report() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Tax/load_output_vat_summary_report') ?>",
            data: $("#frm_output_vat_summary_report").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_output_vat_summary_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_input_vat_summary_report() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Tax/load_input_vat_summary_report') ?>",
            data: $("#frm_input_vat_summary_report").serialize(),
            dataType: "html",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#div_input_vat_summary_report").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportVAToutputSummaryExcel() {
        var form = document.getElementById('frm_output_vat_summary_report');
        form.target = '_blank';
        form.action = '<?php echo site_url('Tax/load_output_vat_summary_report_excel'); ?>';
        form.submit();
    }

    function generateReportVATinputSummaryExcel() {
        var form = document.getElementById('frm_input_vat_summary_report');
        form.target = '_blank';
        form.action = '<?php echo site_url('Tax/load_input_vat_summary_report_excel'); ?>';
        form.submit();
    }
    </script>