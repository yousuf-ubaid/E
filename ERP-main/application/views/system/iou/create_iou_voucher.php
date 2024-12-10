<?php echo head_page($_POST['page_name'], false);
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
$this->lang->load('iou', $primaryLanguage);

$this->load->helper('buyback_helper');
$this->load->helper('iou_helper');

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();
$farms_arr = load_all_farms();
$currency_arr = all_currency_new_drop();//array('' => 'Select Currency');
$location_arr = all_delivery_location_drop();
$location_arr_default = default_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$uom_arr = array('' => $this->lang->line('common_select_uom'));
$batch_arr = array('' => $this->lang->line('iou_select_batch'));
$gl_code_arr = company_PL_account_drop();
$segment_arr = fetch_segment();
$employeedrop = fetch_users_iou();
$voucherCategory_arr = fetch_voucher_category();/**SMSD */
$financeyearperiodYN = getPolicyValues('FPC', 'All');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }
</style>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('iou_step_one') . '- ' . $this->lang->line('iou_iou_voucher_header')?></a>
    <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab" onclick="load_confirmation();"><?php echo $this->lang->line('iou_step_two') . '- ' . $this->lang->line('iou_voucher_confirmation')?></a>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="iovoucher_header_form"'); ?>
        <input type="hidden" name="voucherautoid" id="voucherautoid_edit">

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('iou_iou_voucher_header')?></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('iou_voucherdate')?></label>
                    </div>
                    <div class="form-group col-sm-4">
                       <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                            <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="voucherdate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="voucherdate" class="form-control">
                        </div>
                       <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>


                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_employee')?></label>
                    </div>
                    <div class="form-group col-sm-4">
                      <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                      <?php echo form_dropdown('employeeid', $employeedrop, '', 'class="form-control select2" id="employeeid" onchange="fetch_emp_segment(this.value)"'); ?><!--SMSD : onchange="fetch_emp_segment(this.value)"  -->
                          <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>


                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_currency')?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                        <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID"  required'); ?>
                            <span class="input-req-inner"></span>
                    </div>

                    <div id="div_ClassBank">
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common__bank_or_cash')?></label>
                        </div>
                        <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                                <?php echo form_dropdown('PVbankCode', company_bank_account_drop(), '', 'class="form-control select2" id="PVbankCode" onchange="fetch_cheque_number(this.value)"'); ?>
                            <span class="input-req-inner"></span></span>
                        </div>
                    </div>

                </div>
                <?php
                if($financeyearperiodYN==1){
                ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('iou_financial_year')?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                            <span class="input-req-inner"></span>
                    </div>


                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('iou_financial_period')?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                <?php echo form_dropdown('financeyear_period', array('' => $this->lang->line('iou_financial_period')), '', 'class="form-control" id="financeyear_period" required'); ?>
                            <span class="input-req-inner"></span>
                    </div>
                </div>
                <?php } ?>

                <div class="row paymentType hide" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_payment_type')?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                               <?php echo form_dropdown('paymentType', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, '1' => $this->lang->line('common_cheque'), '2' => $this->lang->line('common_bank_transfer')), ' ', 'class="form-control select2" id="paymentType" onchange="show_payment_method(this.value)"'); ?>
                            <span class="input-req-inner"></span></span>
                    </div>
                    <div class="paymentmoad">
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_payee_only')?></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="skin skin-square">
                                <div class="skin-section extraColumns"><input id="accountPayeeOnly" type="checkbox"
                                                                              data-caption="" class="columnSelected"
                                                                              name="accountPayeeOnly" value="1"><label
                                            for="checkbox">&nbsp;</label></div>
                            </div>


                        </div>
                    </div>


                    <div class="hide" id="employeerdirect">
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_bank_transfer_details')?></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <textarea class="form-control" rows="3" name="bankTransferDetails"
                                      id="bankTransferDetails"></textarea>
                        </div>
                    </div>

                </div>

                <div class="row paymentmoad" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('iou_cheque_number')?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                                <input type="text" name="PVchequeNo" id="PVchequeNo" class="form-control">
                            <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('iou_cheque_date')?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="PVchequeDate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="PVchequeDate" class="form-control">
                        </div>
                        <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_segment')?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                        <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
                            <span class="input-req-inner"></span></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">Voucher Category</label><!--SMSD -->
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>"><!--SMSD -->
                        <?php echo form_dropdown('voucherCategory', $voucherCategory_arr, '', 'class="form-control select2" id="voucherCategory" required'); ?><!--SMSD -->
                            <span class="input-req-inner"></span></span><!--SMSD -->
                    </div>

                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2"><!--SMSD -->
                        <label class="title"><?php echo $this->lang->line('common_reference_no')?></label><!--SMSD -->
                    </div><!--SMSD -->
                    <div class="form-group col-sm-4"><!--SMSD -->
                        <input type="text" name="referenceno" id="referenceno" class="form-control"><!--SMSD -->
                    </div><!--SMSD -->

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_narration')?></label>
                    </div>
                    <div class="form-group col-sm-4">
                         <span class="input-req" title="<?php echo $this->lang->line('iou_required_field')?>">
                        <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
                         <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-12">
                        <button class="btn btn-primary pull-right" type="submit" id="save_btn"><?php echo $this->lang->line('common_save')?></button>
                    </div>
                </div>
            </div>
        </div>
        </form>

        <div class="row addTableView">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('iou_iou_voucher_details')?></h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="iou_voucher_model()">
                            <i class="fa fa-plus"></i> <?php echo $this->lang->line('iou_add_iou_voucher')?>
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="iou_voucher_Detial_item"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>


        <br>
    </div>

    <div id="step2" class="tab-pane">
        <div id="confirm_body"></div>
        <hr>
        <div id="conform_body_attachement">
                <h4 class="modal-title" id="receiptVoucher_attachment_label">Modal title</h4>
                <br>

                <div class="table-responsive" style="width: 60%">
                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                            <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                            <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
                        </tr>
                        </thead>
                        <tbody id="receiptVoucher_attachment" class="no-padding">
                        <tr class="danger">
                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous')?></button>
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft')?></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm')?></button>
        </div>
    </div>

</div>

<div aria-hidden="true" role="dialog" id="iou_voucher_detail_add_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('iou_add_iou_voucher_detail')?></h4>
            </div>
            <div class="modal-body">
                <form role="form" id="iou_voucher_detail_add_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="iou_voucher_detail_add_table">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('common_description')?> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_amount')?> <span class="currency"></span> <?php required_mark(); ?></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_vouchers()">
                                    <i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control" rows="1" id="description"
                                       name="description[]">
                            </td>
                            <td>
                                <input type="text" name="amount[]" id="amount" class="form-control number"
                                       onkeypress="return validateFloatKeyPress(this,event)">
                            </td>

                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close')?></button>
                <button class="btn btn-primary" type="button" onclick="save_Voucher_details()"><?php echo $this->lang->line('common_save_change')?></button>
            </div>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="iou_voucher_detail_edit_modal" data-backdrop="static"
     data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('iou_edit_iou_voucher_detail')?></h4>
            </div>
            <form role="form" id="iou_voucher_detail_edit_form" class="form-horizontal">
                <input type="hidden" id="iouvoucherdetails_edit" name="iouvoucherdetails_edit">

                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('common_description')?> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_amount')?> <span class="currency"></span> <?php required_mark(); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control" id="description_edit" name="description_edit">
                            </td>
                            <td>
                                <input type="text" id="amount_edit" name="amount_edit" class="form-control number"
                                       onkeypress="return validateFloatKeyPress(this,event)">
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close')?></button>
                <button class="btn btn-primary" type="button" onclick="update_voucher_details()"><?php echo $this->lang->line('common_update_changes')?>
                </button>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script>
    var IOUmasterid;
    var currency_decimal;
    $(document).ready(function () {
        number_validation();
        $('.select2').select2();
        $(".paymentmoad").hide();
        $('.headerclose').click(function () {
            fetchPage('system/iou/iou_voucher', '', '<?php echo $this->lang->line('iou_voucher')?>')
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

        });
        Inputmask().mask(document.querySelectorAll("input"));

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
    });


    p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    type = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
    if (p_id) {
        IOUmasterid = p_id;
        load_voucherHeader();
        iou_voucher_exist();
        $('.btn-wizard').removeClass('disabled');
    }
    else {
        $('.btn-wizard').addClass('disabled');
        $('.addTableView').addClass('hide');
        $('#bankTransferDetails').wysihtml5({
            toolbar: {
                "font-styles": false,
                "emphasis": false,
                "lists": false,
                "html": false,
                "link": false,
                "image": false,
                "color": false,
                "blockquote": false
            }
        });
    }

/**SMSD : create function */
    function fetch_emp_segment(emp_id){
        $.ajax({
            async: true,
            type: 'post',
            //dataType: 'json',
            data: {'employeeIdNo': emp_id},
            url: "<?php echo site_url('Iou/fetch_emp_segment'); ?>",
            success: function (data) {
                if(data){
                    $('#segment').val(data).change();
                }
               
                
            }, error: function () {
                swal("Cancelled", "Your  file is safe :)", "error");
            }
        });
    }


    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('a[data-toggle="tab"]').removeClass('btn-primary');
        $('a[data-toggle="tab"]').addClass('btn-default');
        $(this).removeClass('btn-default');
        $(this).addClass('btn-primary');
    });

    $('.next').click(function () {
        var nextId = $(this).parents('.tab-pane').next().attr("id");
        $('[href=#' + nextId + ']').tab('show');
    });

    $('.prev').click(function () {
        var prevId = $(this).parents('.tab-pane').prev().attr("id");
        $('[href=#' + prevId + ']').tab('show');
    });

    FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
    DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
    DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
    periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
    fetch_finance_year_period(FinanceYearID, periodID);
    IOUmasterid = null;

    $('#iovoucher_header_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            voucherdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('iou_voucher_date_is_required')?>'}}},
            employeeid: {validators: {notEmpty: {message: "<?php echo $this->lang->line('common_employee_is_required')?>"}}},
            transactionCurrencyID: {validators: {notEmpty: {message: "<?php echo $this->lang->line('common_currency_is_required')?>"}}},
            PVbankCode: {validators: {notEmpty: {message: "<?php echo $this->lang->line('iou_bank_or_cash_is_required')?>"}}},
            //financeyear: {validators: {notEmpty: {message: "<?php //echo $this->lang->line('iou_financial_year_is_required')?>//"}}},
            //financeyear_period: {validators: {notEmpty: {message: "<?php //echo $this->lang->line('iou_financial_period_is_required')?>//"}}},
            segment: {validators: {notEmpty: {message: "<?php echo $this->lang->line('common_segment_is_required')?>"}}},
            narration: {validators: {notEmpty: {message: "<?php echo $this->lang->line('iou_narrration_is_required')?>"}}}
        },
    }).on('success.form.bv', function (e) {
        $('#transactionCurrencyID').prop('disabled', false);
        $('#employeeid').prop('disabled', false);
        e.preventDefault();
        $("#PVtype").prop("disabled", false);
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'empname', 'value': $('#employeeid option:selected').text()});
        data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
        data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Iou/save_iou_voucher_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    IOUmasterid = data[2];
                    $('#voucherautoid_edit').val(IOUmasterid);
                    //$('[href=#step2]').tab('show');
                    get_iou_voucher_detail_view(IOUmasterid);
                    iou_voucher_exist();
                    $('#save_btn').html('<?php echo $this->lang->line('common_update'); ?>');
                    $('.addTableView').removeClass('hide');
                    $('.btn-wizard').removeClass('disabled');
                    $('#save_btn').prop('disabled', false);
                    $('#transactionCurrencyID').prop('disabled', true);
                    $('#employeeid').prop('disabled', true);
                } else {
                    $('.btn-primary').prop('disabled', false);
                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again')?>');
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function fetch_cheque_number(GLAutoID) {
        if (!jQuery.isEmptyObject(GLAutoID)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'GLAutoID': GLAutoID},
                url: "<?php echo site_url('Chart_of_acconts/fetch_cheque_number'); ?>",
                success: function (data) {
                    if (data) {
                        if (p_id) {
                            $("#PVchequeNo").val((parseFloat(data['bankCheckNumber'])));
                        } else {
                            $("#PVchequeNo").val((parseFloat(data['bankCheckNumber']) + 1));
                        }

                        /*if($('#vouchertype').val()=='Supplier'){*/
                        if (data['isCash'] == 1) {
                            $(".paymentmoad").hide();
                            $('.paymentType').addClass('hide');
                            $('.banktrans').addClass('hide');
                        } else {
                            $('.paymentType').removeClass('hide');
                            show_payment_method();
                            //$(".paymentmoad").show();
                        }
                        /*}else{
                            if (data['isCash'] == 1) {
                                $(".paymentmoad").hide();
                            } else {
                                $(".paymentmoad").show();
                            }
                        }*/

                    }
                    ;
                }
            });
        } else {
            $('.paymentType').addClass('hide');
            $('.banktrans').addClass('hide');
        }

    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('iou_select_financial_period');?>'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function get_iou_voucher_detail_view(IOUmasterid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'IOUmasterid': IOUmasterid},
            url: "<?php echo site_url('Iou/load_iou_voucher_detail_items_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#iou_voucher_Detial_item').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function iou_voucher_model() {
        if (IOUmasterid) {
            var transactioncurrency = $('#transactionCurrencyID').val();
            $('#iou_voucher_detail_add_form')[0].reset();
            $('#iou_voucher_detail_add_table tbody tr').not(':first').remove();
            $("#iou_voucher_detail_add_modal").modal({backdrop: "static"});
            get_currency_decimal_places(IOUmasterid);
        }
    }

    function add_more_vouchers() {
        $('select.select2').select2('destroy');
        var appendData = $('#iou_voucher_detail_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#iou_voucher_detail_add_table').append(appendData);
        var lenght = $('#iou_voucher_detail_add_table tbody tr').length - 1;
        $(".select2").select2();
        number_validation();
    }

    function save_Voucher_details() {
        var $form = $('#iou_voucher_detail_add_form');
        var data = $form.serializeArray();
        data.push({'name': 'IOUmasterid', 'value': IOUmasterid});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Iou/save_iou_voucher_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {

                    $('#iou_voucher_detail_add_form')[0].reset();
                    get_iou_voucher_detail_view(IOUmasterid);
                    iou_voucher_exist();
                    $('#iou_voucher_detail_add_modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_iou_voucher(voucherDetailID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'voucherDetailID': voucherDetailID},
                    url: "<?php echo site_url('Iou/delete_iouVoucher_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', '<?php echo $this->lang->line('iou_voucher_detail_deleted_successfully'); ?>');
                        get_iou_voucher_detail_view(IOUmasterid);
                        iou_voucher_exist();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_voucherHeader() {
        if (IOUmasterid) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'IOUmasterid': IOUmasterid},
                url: "<?php echo site_url('Iou/load_voucherHeader'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        IOUmasterid = data['voucherAutoID'];
                        $('#voucherautoid_edit').val(IOUmasterid);
                        $('#voucherdate').val(data['voucherDate']);
                        $('#employeeid').val(data['empID'] + '|' + data['userType']).change();
                        $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        $('#PVbankCode').val(data['bankGLAutoID']).change();
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        iou_voucher_exist();
                        $('#narration').val(data['narration']);
                        setTimeout(function () {
                            $('#PVchequeNo').val(data['chequeNo']);
                        }, 2000);


                        $('#PVchequeDate').val(data['chequeDate']);
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#referenceno').val(data['referenceNumber']);
                        $('#paymentType').val(data['paymentType']).change();
                        if (data['accountPayeeOnly'] == 1) {
                            $('#accountPayeeOnly').iCheck('check');
                        }
                        if (data['modeOfPayment'] == 0) {
                            $(".paymentmoad").show();
                        }
                        if (data['paymentType'] == 1) {
                            $(".banktrans").addClass('hide');
                        } else {
                            $('#bankTransferDetails').wysihtml5({
                                toolbar: {
                                    "font-styles": false,
                                    "emphasis": false,
                                    "lists": false,
                                    "html": false,
                                    "link": false,
                                    "image": false,
                                    "color": false,
                                    "blockquote": false
                                }
                            });

                            $("#bankTransferDetails").val(data['bankTransferDetails']);
                            $("#employeerdirect").removeClass('hide');
                            $(".banktrans").addClass('show');

                        }
                        get_iou_voucher_detail_view(data['voucherAutoID']);
                        load_confirmation();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        $('#save_btn').html('<?php echo $this->lang->line('common_update'); ?>');

                        
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_confirmation() {
        if (IOUmasterid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'voucherAutoID': IOUmasterid, 'html': true},
                url: "<?php echo site_url('Iou/load_iou_voucher_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#confirm_body').html(data);
                    attachment_modal_iourVoucher(IOUmasterid, "<?php echo $this->lang->line('iou_voucher');?>", "IOU");
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>');
                    refreshNotifications(true);
                }
            });
        }
    }

    function attachment_modal_iourVoucher(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                    dataType: 'json',
                    data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                    success: function (data) {
                        $('#receiptVoucher_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                        $('#receiptVoucher_attachment').empty();
                        $('#receiptVoucher_attachment').append('' +data+ '');

                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#ajax_nav_container').html(xhr.responseText);
                    }
                });
            }
    }

    function confirmation() {
        if (IOUmasterid) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('iou_you_want_confirm_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'IOUmasterid': IOUmasterid},
                        url: "<?php echo site_url('Iou/iouvoucher_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            } else if (data['error'] == 2) {
                                myAlert('w', data['message']);
                            }
                            else if (data['error'] == 0) {
                                myAlert('s', data['message']);
                                fetchPage('system/iou/iou_voucher', IOUmasterid, 'IOU Voucher');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (IOUmasterid) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/iou/iou_voucher', IOUmasterid, '<?php echo $this->lang->line('iou_voucher'); ?>');
                });
        }
    }

    function edit_paymentVoucher_advance(voucherDetailID) {
        if (voucherDetailID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit');?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'voucherDetailID': voucherDetailID},
                        url: "<?php echo site_url('Iou/fetch_iou_voucher_details'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            $('#description_edit').val(data['description']);
                            $('#amount_edit').val(data['transactionAmount']);
                            $('#iouvoucherdetails_edit').val(data['voucherDetailID']);

                            $("#iou_voucher_detail_edit_modal").modal('show');
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function update_voucher_details() {
        var data = $('#iou_voucher_detail_edit_form').serialize();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Iou/update_iou_voucher_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#iou_voucher_detail_edit_form')[0].reset();
                    get_iou_voucher_detail_view(IOUmasterid);
                    $('#iou_voucher_detail_edit_modal').modal('hide');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function show_payment_method() {
        if ($("#paymentType").val() == 1) {
            $(".paymentmoad").show();
            $('.banktrans').addClass('hide');
            $('#employeerdirect').addClass('hide');
        } else if ($("#paymentType").val() == 2) {
            $('#supplierBankMasterID').addClass('hide');
            $('#employeerdirect').removeClass('hide');
            $(".paymentmoad").hide();
            var invoiceNote = '<p><p>Beneficiary Name : </p><p>Bank Name : </p><p>Beneficiary Bank Address : </p><p>Bank Account : </p><p>Beneficiary Swift Code : </p><p>Beneficiary ABA/Routing :</p><p>Reference : </p><br></p>';
            if (p_id) {

            } else {
                $('#bankTransferDetails ~ iframe').contents().find('.wysihtml5-editor').html(invoiceNote);
            }
        } else {
            $('#employeerdirect').addClass('hide');
            $('.banktrans').addClass('hide');
            $(".paymentmoad").hide();
        }


    }

    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    function get_currency_decimal_places(IOUmasterid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {IOUmasterid: IOUmasterid},
            url: "<?php echo site_url('Iou/get_currency_decimal_places'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                currency_decimal = data['DecimalPlaces'];
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate();
            r.moveEnd('character', o.value.length);
            if (r.text == '') return o.value.length;
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function iou_voucher_exist() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'IOUmasterid': IOUmasterid},
            url: "<?php echo site_url('Iou/iou_voucher_details_exist'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#transactionCurrencyID").attr('disabled', 'disabled');
                    $("#employeeid").attr('disabled', 'disabled');

                } else {
                    $("#transactionCurrencyID").removeAttr('disabled');
                    $("#employeeid").removeAttr('disabled');
                }
                stopLoad();
                //refreshNotifications(true);
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }
</script>
