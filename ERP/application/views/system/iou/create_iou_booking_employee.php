 <?php echo head_page($_POST['page_name'], false);
 $primaryLanguage = getPrimaryLanguage();
 $this->lang->load('common', $primaryLanguage);
 $this->lang->load('iou', $primaryLanguage);

$this->load->helper('buyback_helper');
$this->load->helper('iou_helper');

$category = fetch_claim_category_iou();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$segment_arr = fetch_segment();
$farms_arr = load_all_farms();
$currency_arr = all_currency_new_drop();//array('' => 'Select Currency');
$location_arr = all_delivery_location_drop();
$location_arr_default = default_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$uom_arr = array('' => 'Select UOM');
$batch_arr = array('' => 'Select Batch');
$cat_arr = fetch_claim_category_iou();
$gl_code_arr = company_PL_account_drop();
$segment_arr = fetch_segment();
$employeedrop = fetch_users_iou(false);
$empid = current_userID() . '|1';

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

    .titlebalance {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 12px;
        color: #151212;
        font-weight: bold;
        padding: 4px 10px 0 0;
    }

    .totalbal {
        float: left;
        width: 170px;
        text-align: left;
        font-size: 12px;
        color: #f76f01;
        font-weight: bold;
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
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('iou_step_one') . '- ' . $this->lang->line('iou_iou_expense_header')?></a>
    <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab" onclick="load_confirmation();">
            <?php echo $this->lang->line('iou_step_two') . '- ' . $this->lang->line('iou_iou_expense_submit')?></a>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="booking_header_form"'); ?>
        <input type="hidden" name="bookingautoid" id="bookingautoid">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php $this->lang->line('iou_iou_expense_header'); ?></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('iou_booking_date');?></label>
                    </div>
                    <div class="form-group col-sm-4">
                       <span class="input-req" title="<?php echo $this->lang->line('iou_required_field');?>">
                            <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="bookingdate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="bookingdate" class="form-control">
                        </div>
                       <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_employee');?></label>
                    </div>
                    <div class="form-group col-sm-4">
                      <span class="input-req" title="<?php echo $this->lang->line('iou_required_field');?>">
                          <?php echo form_dropdown('employeeid', $employeedrop, $empid, 'class="form-control select2" id="employeeid" onchange = "loadiou_voucher()" disabled'); ?>
                          <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_currency');?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field');?>">
                        <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" onchange = "loadiou_voucher()" required'); ?>
                            <span class="input-req-inner"></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_segment');?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field');?>">
                        <?php echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment" required'); ?>
                            <span class="input-req-inner"></span></span>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('iou_financial_year');?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field');?>">
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                            <span class="input-req-inner"></span>
                    </div>


                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('iou_financial_period');?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field');?>">
                <?php echo form_dropdown('financeyear_period', array('' => 'Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                            <span class="input-req-inner"></span>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('iou_voucher');?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="<?php echo $this->lang->line('iou_required_field');?>">
                              <div id="div_loadiouvoucher">
                <?php echo form_dropdown('iouvoucher', array('' => 'Select IOU Voucher'), 'Each', 'class="form-control" id="iouvoucher" '); ?>
                               </div>
                            <span class="input-req-inner"></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('common_comment');?></label>
                    </div>
                    <div class="form-group col-sm-4">
                         <span class="input-req" title="<?php echo $this->lang->line('iou_required_field');?>">
                        <textarea class="form-control" rows="3" id="comment" name="comment"></textarea>
                         <span class="input-req-inner"></span></span>
                    </div>

                </div>


                <br>
                <br>
                <br>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-12">
                        <button class="btn btn-primary pull-right" type="submit" id="save_btn"><?php echo $this->lang->line('common_save');?></button>
                    </div>
                </div>
            </div>
        </div>
        </form>

        <div class="row addTableView">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('iou_voucher_expense_details');?></h2>
                </header>
                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="titlebalance"><?php echo $this->lang->line('iou_total_voucher_amount');?> :</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="totalbal" id="totalamt">0.00</label>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="titlebalance"><?php echo $this->lang->line('iou_matched_amount');?> :</label>
                    </div>
                    <div class="form-group col-sm-1">
                        <label class="totalbal" id="matchedamount">0.00</label>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="titlebalance"><?php echo $this->lang->line('common_balance');?> :</label>
                    </div>

                    <div class="form-group col-sm-1">
                        <label class="totalbal" id="balance">0.00</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="iou_booking_detail_model()">
                            <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_detail');?>
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="iou_voucher_booking_Detial_item"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="step2" class="tab-pane">
        <div id="confirm_body"></div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?></button>
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_submit');?></button>
        </div>
    </div>

</div>

<!--<div class="modal fade" id="iou_booking_model" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="iou_booking_det_form">
                <input type="hidden" id="IOUbookingmasterid" name="IOUbookingmasterid">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">IOU Booking</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                        <thead>
                        <tr>
                            <th>Segment</th>
                            <th>Expense Category</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <td><?php /*echo form_dropdown('segmentdrop[]', $segment_arr, '', 'class="form-control segment_drop select2" onchange="change_validation(this)"  required '); */ ?></td>
                        <td><?php /*echo form_dropdown('cat_drop[]', $cat_arr, '', 'class="form-control cat_drop select2" onchange="change_validation(this)" required '); */ ?></td>
                        <td><input type="text" name="amounts[]" class="form-control number amounts" placeholder="Amount" onkeypress="change_validation(this,1)"></td>
                        <td><textarea class="form-control description" rows="1" name="comment[]" placeholder="Description" onkeypress="change_validation(this)"></textarea></td>
                        <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="save_iou_bookingamt()">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>-->

<div aria-hidden="true" role="dialog" id="iou_booking_model" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('iou_booking');?></h4>
            </div>
            <div class="modal-body">
                <form role="form" id="iou_booking_det_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                        <input type="hidden" name="IOUbookingmasterid" id="IOUbookingmasterid">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('iou_expense_category');?></th>
                            <th style="width: 350px;"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_amount');?> <?php required_mark(); ?></th>
                            <th style="width: 200px;"><?php echo $this->lang->line('common_description');?> <?php required_mark(); ?></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_dropdown('category[]', $cat_arr, '', 'class="form-control select2 category" onchange="change_validation(this)"'); ?>
                            </td>
                            <td>
                                <?php echo form_dropdown('segment[]', $segment_arr, '', 'class="form-control select2 segment" onchange="change_validation(this)" id="segment" required'); ?>
                            </td>

                            <td>
                                <input type="text" name="amounts[]" id="amount" class="form-control number amounts"
                                       onchange="change_validation(this,1)">
                            </td>
                            <td>
                                <textarea class="form-control description" rows="1" id="description"
                                          name="description[]" onchange="change_validation(this)"></textarea>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?></button>
                <button class="btn btn-primary" type="button" onclick="save_iou_bookingamt()"><?php echo $this->lang->line('common_save_change');?></button>
            </div>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="iou_booking_detail_edit_modal" data-backdrop="static"
     data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('iou_edit_iou_booking_detail'); ?></h4>
            </div>
            <form role="form" id="iou_voucher_booking_edit_form" class="form-horizontal">
                <input type="hidden" id="ioubookingdetid" name="ioubookingdetid">

                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('iou_voucher_code'); ?><?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_balance'); ?> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('iou_expense_category'); ?> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_segment'); ?> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_amount'); ?> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_description'); ?> <span class="currency"></span> <?php required_mark(); ?></th>
                        </tr>
                        </thead>
                        <tbody id="ioubokking_body">
                        <tr>
                            <td>
                                <input type="text" class="form-control" id="vouchercodeedit" name="vouchercodeedit_edit"
                                       readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control" id="balanceedit" name="balanceedit_edit">
                            </td>
                            <td>
                                <?php echo form_dropdown('expencecategory', $category, '', 'class="form-control select2" id="expencecategory" required'); ?>
                            </td>
                            <td>
                                <?php echo form_dropdown('segmentbooking', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segmentbooking" required'); ?>
                            </td>
                            <td>
                                <input type="text" class="form-control" id="amount_edit" name="amount_edit">
                            </td>

                            <td>
                                <input type="text" id="description_edit" name="description_edit"
                                       class="form-control number">
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?> </button>
                <button class="btn btn-primary" type="button" onclick="update_voucher_details()"><?php echo $this->lang->line('common_update_changes'); ?>
                </button>
            </div>

        </div>
    </div>
</div>


<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script>
    var IOUbookingmasterid;
    var iouvoucherid;
    var currency_decimal;
    $(document).ready(function () {
        loadiou_voucher();
        number_validation();
        $('.select2').select2();
        $(".paymentmoad").hide();
        $('.headerclose').click(function () {
            fetchPage('system/iou/iou_booking_employee', '', '<?php $this->lang->line('iou_voucher'); ?>')
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
    if (p_id) {
        IOUbookingmasterid = p_id;
        load_bookingvoucher_Header();
        iou_booking_details_exist();
        $("#a_link").attr("href", "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>/" + IOUbookingmasterid);
        $("#de_link").attr("href", "<?php echo site_url('Iou/fetch_double_entry_iou_booking'); ?>/" + IOUbookingmasterid + '/IOUB');
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

    IOUbookingmasterid = null;
    iouvoucherid = null;
    FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
    DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
    DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
    periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
    fetch_finance_year_period(FinanceYearID, periodID);

    $('#booking_header_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            bookingdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('iou_booking_date_is_required');?>'}}},
            financeyear: {validators: {notEmpty: {message: '<?php echo $this->lang->line('iou_financial_year_is_required');?>'}}},
            financeyear_period: {validators: {notEmpty: {message: '<?php echo $this->lang->line('iou_financial_period_is_required');?>'}}},
            segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>'}}},
            comment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('iou_comment_is_required');?>' }}},
        },
    }).on('success.form.bv', function (e) {
        $('#transactionCurrencyID').prop('disabled', false);
        $('#employeeid').prop('disabled', false);
        $('#advance_iouvoucher').prop('disabled', false);
        e.preventDefault();
        $("#PVtype").prop("disabled", false);
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'empname', 'value': $('#employeeid option:selected').text()});
        data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
        data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Iou/save_iou_booking_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    IOUbookingmasterid = data[2];
                    $('#bookingautoid').val(IOUbookingmasterid);
                    get_iou_booking_detail_view(IOUbookingmasterid);
                    $('#IOUbookingmasterid').val(IOUbookingmasterid);
                    iou_voucher_total($('#advance_iouvoucher').val())
                    $('#save_btn').html(<?php echo $this->lang->line('common_update'); ?>);
                    $('.addTableView').removeClass('hide');
                    $('.btn-wizard').removeClass('disabled');
                    iou_booking_details_exist();
                    $('#save_btn').prop('disabled', false);
                    $('#transactionCurrencyID').prop('disabled', true);
                    $('#employeeid').prop('disabled', true);
                } else {
                    $('.btn-primary').prop('disabled', false);
                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>');
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
                mySelect.append($('<option></option>').val('').html('Select Financial Period'));
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

    function iou_booking_details_exist() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'IOUbookingmasterid': IOUbookingmasterid},
            url: "<?php echo site_url('Iou/iou_booking_details_exist'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#transactionCurrencyID").attr('disabled', 'disabled');
                    setTimeout(function () {
                        $("#advance_iouvoucher").attr('disabled', 'disabled');
                    }, 500);

                } else {
                    $("#transactionCurrencyID").removeAttr('disabled');
                    setTimeout(function () {
                        $("#advance_iouvoucher").removeAttr('disabled');
                    }, 500);
                }
                stopLoad();
            }, error: function () {
                stopLoad();
                swal('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>');
            }
        });
    }

    function get_iou_booking_detail_view() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'IOUbookingmasterid': IOUbookingmasterid},
            url: "<?php echo site_url('Iou/load_iou_booking_detail_items_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#iou_voucher_booking_Detial_item').html(data);


                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function get_iou_booking_detail_voucher_generate() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'IOUbookingmasterid': IOUbookingmasterid},
            url: "<?php echo site_url('Iou/load_iou_booking_detail_voucher_generate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#iou_voucher_booking_genarate_item').html(data);


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
            $('#iou_voucher_detail_add_form')[0].reset();
            $('#iou_voucher_detail_add_table tbody tr').not(':first').remove();
            $("#iou_voucher_detail_add_modal").modal({backdrop: "static"});
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
                    $('#iou_voucher_detail_add_modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_iou_bookingdetail(bookingdetailid) {
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
                    data: {'bookingdetailid': bookingdetailid},
                    url: "<?php echo site_url('Iou/delete_ioubooking_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert('s', '<?php echo $this->lang->line('iou_expense_detail_deleted_successfully')?>');
                        get_iou_booking_detail_view(IOUbookingmasterid);

                        iou_booking_details_exist();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_bookingvoucher_Header() {
        if (IOUbookingmasterid) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'IOUbookingmasterid': IOUbookingmasterid},
                url: "<?php echo site_url('Iou/load_voucher_booking_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        IOUbookingmasterid = data['bookingMasterID'];
                        $('#bookingautoid').val(IOUbookingmasterid);
                        $('#bookingdate').val(data['bookingDate']);
                        $('#employeeid').val(data['empID'] + '|' + data['userType']).change();
                        $('#comment').val(data['comments']);
                        $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        setTimeout(function () {
                            $('#advance_iouvoucher').val(data['iouVoucherAutoID']).change();
                        }, 500);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        get_iou_booking_detail_view(IOUbookingmasterid);
                        $('#IOUbookingmasterid').val(IOUbookingmasterid);
                        load_confirmation();
                        iou_voucher_total(data['iouVoucherAutoID']);
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
        if (IOUbookingmasterid) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'IOUbookingmasterid': IOUbookingmasterid, 'html': true},
                url: "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#confirm_body').html(data);
                    iou_booking_submit_exist();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>');
                    refreshNotifications(true);
                }
            });
        }
    }

    function iou_booking_submit_exist() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'IOUbookingmasterid': IOUbookingmasterid},
            url: "<?php echo site_url('Iou/iou_book_emp_submit'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#confirmbtn").removeAttr('disabled');
                    $("#submitbtn").attr('disabled', 'disabled');
                } else {
                    $("#confirmbtn").attr('disabled', 'disabled');
                    $("#submitbtn").removeAttr('disabled');
                }
                stopLoad();
                //refreshNotifications(true);
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }

    function confirmation() {
        if (IOUbookingmasterid) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('iou_you_want_submit_this_booking'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_submit'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'IOUbookingmasterid': IOUbookingmasterid},
                        url: "<?php echo site_url('Iou/ioubooking_submit'); ?>",
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
                                fetchPage('system/iou/iou_booking_employee', IOUbookingmasterid, '<?php echo $this->lang->line('iou_booking_employee'); ?>');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (IOUbookingmasterid) {
            swal({
                    title:"<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/iou/iou_booking_employee', IOUbookingmasterid, '<?php echo $this->lang->line('iou_booking'); ?>');
                });
        }
    }

    function edit_iou_bookingdetail(bookingDetailsID, iouVoucherAutoID) {
        if (bookingDetailsID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'bookingDetailsID': bookingDetailsID, 'iouVoucherAutoID': iouVoucherAutoID},
                        url: "<?php echo site_url('Iou/fetch_iou_booking_details'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            $('#description_edit').val(data['ioubookingdescription']);
                            $('#vouchercodeedit').val(data['vouchermasterioucode']);
                            $('#expencecategory').val(data['expenseCategoryAutoID']).change();
                            $('#amount_edit').val(data['ioutransactionamt']);
                            $('#balanceedit').val(data['ioutransactionAmount']);
                            $('#segmentbooking').val(data['detailssegmentid'] + '|' + data['detailsegmentcode']).change();
                            $('#iouvoucherdetails_edit').val(data['voucherDetailID']);

                            $("#iou_booking_detail_edit_modal").modal('show');
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
                    iou_booking_details_exist();
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


    function iou_booking_detail_model() {


        $('#iou_booking_det_form')[0].reset();
        $('#iou_booking_det_form').bootstrapValidator('resetForm', true);
        $('#po_detail_add_table tbody tr').not(':first').remove();
        $(".category").val(null).trigger("change");
        $(".segment").val(null).trigger("change");
        $("#iou_booking_model").modal({backdrop: "static"});

    }

    function change_validation(element, val) {
        if (element) {
            $(element).closest('tr').css("background-color", 'white');
        }
        if (val == 1) {
            if (element.value > 0) {
                $(element).closest('tr').css("background-color", 'white');
            }
        }
    }


    function save_iou_bookingamt() {

        var data = $('#iou_booking_det_form').serializeArray();
        data.push({'name': 'employeeid', 'value': $('#employeeid').val()});
        data.push({'name': 'transactioncurrencyid', 'value': $('#transactionCurrencyID').val()});
        data.push({'name': 'voucherid', 'value': $('#advance_iouvoucher').val()});


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Iou/save_ioubooking_amt'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    $('#iou_booking_model').modal('hide');
                    get_iou_booking_detail_view();
                    iou_booking_details_exist();
                } else if (data[0] == 'e') {

                }
            }, error: function () {
                $('#iou_booking_model').modal('hide');
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });

    }

    function select_check_box(data, id, total) {
        $("#check_" + id).prop("checked", false);
        if (data.value > 0) {
            if (total >= data.value) {
                $("#check_" + id).prop("checked", true);
            } else {
                $("#check_" + id).prop("checked", false);
                $("#amount_" + id).val('');
                myAlert('w', '<?php echo $this->lang->line('iou_you_can_not_enter_booking_amount_greater_than_selected_iou_voucher_amount'); ?>' )
            }
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
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('iou_select_financial_period') ?>'));
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

    function get_currency_decimal_places(IOUbookingmasterid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {IOUbookingmasterid: IOUbookingmasterid},
            url: "<?php echo site_url('Iou/get_currency_decimal_places_booking'); ?>",
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

    function submit_booking() {
        if (IOUbookingmasterid) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    text:  "<?php echo $this->lang->line('iou_you_want_submit_this_booking'); ?>",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_submit'); ?>",
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'IOUbookingmasterid': IOUbookingmasterid},
                        url: "<?php echo site_url('Iou/ioubooking_submit'); ?>",
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
                                iou_booking_submit_exist();
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function loadiou_voucher() {
        var transactioncurrenyid = $('#transactionCurrencyID').val();
        var empid = $('#employeeid').val();
        $('#totalamt').html('0.00');
        $('#matchedamount').html('0.00');
        $('#balance').html('0.00');
        $('#iouvoucher').val('');
        if (empid != '') {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'empid': empid, 'transactioncurrenyid': transactioncurrenyid},
                url: "<?php echo site_url('Iou/fetch_iou_iouvouchers'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_loadiouvoucher').html(data);
                    $('.select2').select2();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            })
        }
    }

    function add_more() {
        $('select.select2').select2('destroy');
        var appendData = $('#po_detail_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#po_detail_add_table').append(appendData);
        var lenght = $('#po_detail_add_table tbody tr').length - 1;
        $(".select2").select2();
        number_validation();
    }

    function iou_voucher_total(iouvoucherid) {
        $('#totalamt').html('0.00');
        $('#matchedamount').html('0.00');
        $('#balance').html('0.00');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'iouvoucherid': iouvoucherid, 'IOUbookingmasterid': IOUbookingmasterid},
            url: "<?php echo site_url('Iou/fetch_iou_iouvoucher_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#totalamt').html(data['totalvoucheramount']);
                    $('#matchedamount').html(data['matchedamt']);
                    $('#balance').html((data['totalvoucheramount'] - data['matchedamt']));
                    // $('#matchedamount').val(data['matchedamt']);
                }
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        })

    }

</script>
