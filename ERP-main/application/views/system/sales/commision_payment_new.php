<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales&marketing_salescom', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);


$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop();
$salesperson_arr = all_sales_person_drop();
$financeyear_arr = all_financeyear_drop(true);
$financeyearperiodYN = getPolicyValues('FPC', 'All');
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

    <div class="steps">
        <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('salescommision_step_one');?><!--Step 1--> - <?php echo $this->lang->line('salescommision_commision_payment_header');?><!--Commission Payment Header--></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_details(4)" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('salescommision_step_two');?><!--Step 2--> - <?php echo $this->lang->line('salescommision_commission_payment_detail');?><!--Commission Payment Detail--></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('salescommision_step_three');?><!--Step 3--> - <?php echo $this->lang->line('salescommision_commission_payment_confirmation');?><!--Commission Payment Confirmation--></span>
        </a>
    </div>
    
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="paymentvoucher_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('salescommision_voucher_type');?><!--Voucher Type--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('vouchertype', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'SC' => $this->lang->line('salescommision_sales_commission')/*'Sales Commission'*/), 'SC', 'class="form-control select2" id="vouchertype" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_segment');?><!--Segment--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('salescommision_commission_payment_date');?><!--Commission Payment Date--> <?php required_mark(); ?></label>

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="PVdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="PVdate"
                           class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_reference');?><!--Reference--> # </label>
                <input type="text" name="referenceno" id="referenceno" class="form-control">
            </div>
            <div class="form-group col-sm-4">
                <label for="partyID"><span id="party_text"><?php echo $this->lang->line('salescommision_sales_person');?><!--Sales Person--> </span> <?php required_mark(); ?></label>
                <?php echo form_dropdown('partyID', $salesperson_arr,"", 'class="form-control select2" id="partyID" required'); ?>

            </div>
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('salescommision_payment_currency');?><!--Payment Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="currency_validation(this.value,\'PV\')" id="transactionCurrencyID" required disabled'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="PVbankCode"><?php echo $this->lang->line('salescommision_bank_or_cash');?><!--Bank or Cash--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('PVbankCode', company_bank_account_drop(), '', 'class="form-control select2" id="PVbankCode" onchange="fetch_cheque_number(this.value)" required'); ?>
            </div>
            <?php
            if($financeyearperiodYN==1){
            ?>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('salescommision_financial_year');?><!--Financial Year--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('salescommision_financial_period');?><!--Financial Period--> <?php required_mark(); //?></label>
                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
            <?php } ?>
        </div>
        <div class="row">
             <div class="form-group col-sm-4 paymentModeType">
                <label>Mode of Payment <?php required_mark(); ?></label>
                <?php echo form_dropdown('paymentType', array('' => 'Select Payment', '1' => ' Cheque', '2' => ' Bank Transfer'), '', 'class="form-control " id="paymentType" onchange="set_payment_method_commission(this.value)" '); ?>
            </div>
            <div class="form-group col-sm-4 paymentmoad">
                <label><?php echo $this->lang->line('salescommision_cheque_number');?><!--Cheque Number--> <?php required_mark(); ?></label>
                <input type="text" name="PVchequeNo" id="PVchequeNo" class="form-control">
            </div>
            <div class="form-group col-sm-4 paymentmoad">
                <label><?php echo $this->lang->line('salescommision_cheque_date');?><!--Cheque Date--> <?php required_mark(); ?></label>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="PVchequeDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="PVchequeDate"
                           class="form-control" required>
                </div>
            </div>
            <!-- <div class="form-group col-sm-4">
                    <label>Account <?php //required_mark(); ?></label>
                    <select name="account" id="account" class="form-control ">
                        <option value="">Select Account</option>
                    </select>
                </div> -->
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('salescommision_memo');?><!--Memo--> </label>
                <textarea class="form-control" rows="3" name="narration" id="narration"></textarea>
            </div>
            <div class="form-group col-sm-4 employeerdirect" id="employeerdirect">
                <label>Bank Transfer Details </label>
                <textarea class="form-control" rows="3" name="bankTransferDetails" id="bankTransferDetails"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button id="submitbtn" class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <!-- <div class="row">
            <div class="col-md-9">
            </div>
            <div class="col-md-3 text-right">
                <button type="button" class="btn btn-primary pull-right" onclick="payment_voucher_detail_model()"><i class="fa fa-plus"></i> New Payment Voucher </button>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table id="payment_voucher_detail_table" class="<?php //echo table_class(); ?>">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 15%">Booking Invoice Code</th>
                    <th style="min-width: 40%">Invoice No</th>
                    <th style="min-width: 15%">Invoice Date</th>
                    <th style="min-width: 5%">Invoice Amount</th>
                    <th style="min-width: 5%">Balance Amount</th>
                    <th style="min-width: 15%">Payment Amount</th>
                </tr>
                </thead>
            </table>
        </div> -->
    </div>
    <div id="step3" class="tab-pane">
        <!-- <div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank"
                   href="<?php /*echo site_url('Double_entry/fetch_double_entry_payment_voucher/'); */ ?>"><span
                        class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review
                    entries
                </a>
                <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank"
                   href="<?php /*echo site_url('Payment_voucher/load_pv_conformation/'); */ ?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div>-->
        <hr>
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="paymentVoucher_attachment_label">Modal title</h4>
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
                    <tbody id="paymentVoucher_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<?php
/** sub item master modal created by Shafry */
$this->load->view('system/grv/sub-views/inc-sub-item-master');
?>
<div class="modal fade" id="payment_voucher_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="payment_voucher_detail_form">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('salescommision_commision_payment_modal');?><!--Commision Payment Modal--></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="box box-widget widget-user-2">
                                <div class="widget-user-header bg-yellow">
                                    <h4><?php echo $this->lang->line('salescommision_payment');?><!--Commision Payment--></h4>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">

                                        <?php
                                        $norecfound=$this->lang->line('salescommision_payment');
                                        echo '<li><a>'.$norecfound.'<!--No Records found--></a></li>'; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-bordered table-striped table-condesed ">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 15%"><?php echo $this->lang->line('salescommision_booking_invoice_code');?><!--Booking Invoice Code--></th>
                                    <th style="min-width: 40%"><?php echo $this->lang->line('salescommision_invoice_no');?><!--Invoice No--></th>
                                    <th style="min-width: 15%"><?php echo $this->lang->line('common_invoice_date');?><!--Invoice Date--></th>
                                    <th style="min-width: 5%"><?php echo $this->lang->line('salescommision_invoice_amount');?><!--Invoice Amount--></th>
                                    <th style="min-width: 5%"><?php echo $this->lang->line('salescommision_balance_amount');?><!--Balance Amount--></th>
                                    <th style="min-width: 15%"><?php echo $this->lang->line('salescommision_payment_amount');?><!--Payment Amount--></th>
                                </tr>
                                </thead>
                                <tbody id="table_body">
                                <tr class="danger">
                                    <td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td>
                                </tr>
                                </tbody>
                                <tfoot id="table_tfoot">

                                </tfoot>
                            </table>
                                   </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    var PayVoucherAutoId;
    var pvType;
    var partyID;
    var currencyID;
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/sales/commision_payment', PayVoucherAutoId, 'Commision Payment');
        });
        PayVoucherAutoId = null;
        pvType = null;
        partyID = null;
        currencyID = null;

        $(".paymentmoad").hide();
        $(".paymentModeType").hide();
        $(".employeerdirect").hide();

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#paymentvoucher_form').bootstrapValidator('revalidateField', 'PVdate');
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            PayVoucherAutoId = p_id;
            load_payment_voucher_header();
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + PayVoucherAutoId + '/PV');
        } else {
            $('.btn-wizard').addClass('disabled');
            currency_validation(<?php echo json_encode(trim($this->common_data['company_data']['company_default_currencyID'])); ?>, 'PV');

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

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        $('#paymentvoucher_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                vouchertype: {validators: {notEmpty: {message: '<?php echo $this->lang->line('salescommision_voucher_type_is_required');?>.'}}},/*Voucher Type is required*/
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}},/*Segment is required*/
                PVdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('salescommision_payment_voucher_date_is_required');?>.'}}},/*Payment Voucher Date is required*/
                partyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('salescommision_sales_person_is_required');?>.'}}},/*Sales person is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('salescommision_transaction_currency_is_required');?>.'}}},/*Transaction Currency is required*/
                paymentvouchercode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('salescommision_payment_voucher_code_is_required');?>.'}}},/*Payment Voucher Code is required*/
               // financeyear: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('salescommision_fianancial_year_is_required');?>.'}}},/*Financial Year is required*/
                //financeyear_period: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('salescommision_financial_period_is_required');?>.'}}},/*Financial Period is required*/
                PVbankCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('salescommision_bank_cash_is_required');?>.'}}},/*Bank or Cash is required*/
                //PVchequeDate: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('salescommision_bank_cash_is_required');?>//.'}}},/*Bank or Cash is required*/
                //paymentType: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('salescommision_mode_of_payment_is_required');?>//.'}}}/*Mode of Payment is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#vouchertype").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#partyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'PayVoucherAutoId', 'value': PayVoucherAutoId});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'salesPersonDetails', 'value': $('#partyID option:selected').text()});
            data.push({'name': 'pvtype', 'value': $('#vouchertype').val()});
            data.push({'name': 'bank', 'value': $('#PVbankCode option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payment_voucher/save_paymentvoucher_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data_arr) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data_arr['status']) {
                        PayVoucherAutoId = data_arr['last_id'];
                        $("#a_link").attr("href", "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + PayVoucherAutoId);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + PayVoucherAutoId + '/PV');
                        pvType = $('#vouchertype').val();                       
                        partyID = $('#supplier').val();
                        var result = $('#transactionCurrencyID option:selected').text().split('|');
                        currencyID = result[0];
                        $('.btn-wizard').removeClass('disabled');
                        fetch_details(4);
                        $("#vouchertype").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#partyID").prop("disabled", true);
                        $('[href=#step2]').tab('show');
                    }else{
                        $('#submitbtn').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }).on('error.form.bv', function (e) {

            $('#submitbtn').prop('disabled', false);

        });
    });

    function fetch_supplier_currency_by_id(supplierAutoID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'supplierAutoID': supplierAutoID},
            url: "<?php echo site_url('Procurement/fetch_supplier_currency_by_id'); ?>",
            success: function (data) {
                if (data.supplierCurrencyID) {
                    $("#transactionCurrencyID").val(data.supplierCurrencyID).change();
                    //currency_validation_modal(data.supplierCurrencyID,'PV',supplierAutoID,'SUP');
                }
                ;
            }
        });
    }

    function currency_validation(CurrencyID, documentID) {
        if (CurrencyID) {
            partyAutoID = $('#supplierID').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'SUP');
        }
    }

    function fetch_cheque_number(GLAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'GLAutoID': GLAutoID},
            url: "<?php echo site_url('Chart_of_acconts/fetch_cheque_number'); ?>",
            success: function (data) {
                if (data) {
                    $("#PVchequeNo").val((parseFloat(data['bankCheckNumber']) + 1));
                    if (data['master']['isCash'] == 1) {
                        $(".paymentmoad").hide();
                        $(".paymentModeType").hide();
                    } else {
                        $(".paymentModeType").show();
                        $(".paymentmoad").hide();
                        $(".employeerdirect").hide();
                        set_payment_method_commission();
                    }
                }
            }
        });
    }
    
    function set_payment_method_commission() {
        if($("#paymentType").val() == 1){
            $(".paymentmoad").show();
            $(".employeerdirect").hide();

        } else if ($("#paymentType").val() == 2) {
            $(".paymentmoad").hide();
            $(".employeerdirect").show();
            var invoiceNote='<p><p>Beneficiary Name : </p><p>Bank Name : </p><p>Beneficiary Bank Address : </p><p>Bank Account : </p><p>Beneficiary Swift Code : </p><p>Beneficiary ABA/Routing :</p><p>Reference : </p><br></p>';
            $('#bankTransferDetails ~ iframe').contents().find('.wysihtml5-editor').html(invoiceNote);
        }
    }

    function confirmation() {
        if (PayVoucherAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*you want to confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'PayVoucherAutoId': PayVoucherAutoId},
                        url: "<?php echo site_url('Payment_voucher/payment_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            } else if(data['error']== 2)
                                {
                                    myAlert('w',data['message']);
                                }
                             else {
                                //refreshNotifications(true);
                                myAlert('s', data['message']);
                                fetchPage('system/sales/commision_payment', PayVoucherAutoId, 'Commission Payment');
                            }

                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function fetch_details(tab) {
        $('.nav-tabs a[href="#tab_1"]').tab('show');
        if (tab == 1) {

            $('.nav-tabs a[href="#tab_4"]').tab('show');
        }
        if (tab == 2) {
            $('.nav-tabs a[href="#tab_3"]').tab('show');
        }
        if (tab == 3) {
            $('.nav-tabs a[href="#tab_2"]').tab('show');
        }
        if (tab == 4) {
            $('.nav-tabs a[href="#tab_1"]').tab('show');
        }

        fetch_detail(pvType, partyID, currencyID, tab);
    }

    function load_conformation() {
        if (PayVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'payVoucherAutoId': PayVoucherAutoId, 'html': true},
                url: "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + PayVoucherAutoId);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + PayVoucherAutoId + '/PV');
                    attachment_modal_paymentVoucher(PayVoucherAutoId, "<?php echo $this->lang->line('salescommision_payment_voucher');?>", "PV");/*Payment Voucher*/
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function fetch_detail(type, partyID, currencyID, tab) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'PayVoucherAutoId': PayVoucherAutoId,
                'pvType': type,
                'partyID': partyID,
                'currencyID': currencyID,
                'tab': tab
            },
            url: "<?php echo site_url('Payment_voucher/fetch_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#step2').html(data);
                //check_detail_dataExist(PayVoucherAutoId);

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function check_detail_dataExist(PayVoucherAutoId) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'PayVoucherAutoId': PayVoucherAutoId},
            url: "<?php echo site_url('Grv/fetch_detail_header_lock'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (jQuery.isEmptyObject(data)) {
                    $("#vouchertype").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    setTimeout(function () {
                        $("#partyID").prop("disabled", true);
                    }, 500);
                } else {
                    $("#vouchertype").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    setTimeout(function () {
                        $("#partyID").prop("disabled", true);
                    }, 500);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
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
                mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function payment_voucher_detail_model() {
        $("#payment_voucher_model").modal({backdrop: "static"});
    }

    function load_payment_voucher_header() {
        if (PayVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'PayVoucherAutoId': PayVoucherAutoId},
                url: "<?php echo site_url('Payment_voucher/load_payment_voucher_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $("#a_link").attr("href", "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + PayVoucherAutoId);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + PayVoucherAutoId + '/PV');
                        pvType = data['pvType'];
                        partyID = data['partyID'];
                        currencyID = data['transactionCurrency'];
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $("#paymentvouchercode").val(data['PVcode']);
                        $('#PVdate').val(data['PVdate']);
                        $('#paymentbank').val(data['BPVbank']);
                        $('#account').val(data['PVAccount']);
                        $('#narration').val(data['PVNarration']);
                        $('#vouchertype').val(data['pvType']).change();
                        $('#referenceno').val(data['referenceNo']);
                        $('#PVbankCode').val(data['PVbankCode']).change();
                        $('#PVchequeNo').val(data['PVchequeNo']);
                        $('#PVchequeDate').val(data['PVchequeDate']);

                        $('#paymentType').val(data['paymentType']);
                        if (data['modeOfPayment'] == 0) {
                            $(".paymentmoad").show();
                        }

                        if (data['paymentType'] == 1) {
                            $(".paymentmoad").show();
                        } else if(data['paymentType'] == 2) {
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

                            $("#bankTransferDetails").val( data['bankTransferDetails']);
                            $("#employeerdirect").removeClass('hide');
                            $(".banktrans").addClass('show');
                        }



                        fetch_detail(data['pvType'], data['partyID'], data['transactionCurrencyID']);
                        setTimeout(function () {
                            $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        }, 1000);
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#partyID').val(partyID).change();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function save_draft() {
        if (PayVoucherAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('salescommision_you_want_to_save_this_file');?>",/*You want to save this file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/sales/commision_payment', PayVoucherAutoId, 'Commission Payment');
                });
        }
    }

    function attachment_modal_paymentVoucher(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#paymentVoucher_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "<?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                    $('#paymentVoucher_attachment').empty();
                    $('#paymentVoucher_attachment').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_paymentVoucher_attachment(PayVoucherAutoId, DocumentSystemCode,myFileName) {
        if (PayVoucherAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>",/*You want to delete this attachment file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': PayVoucherAutoId,'myFileName': myFileName},
                        url: "<?php echo site_url('Payable/delete_paymentVoucher_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s','Deleted Successfully');
                                attachment_modal_paymentVoucher(DocumentSystemCode, "Payment Voucher", "PV");
                            }else{
                                myAlert('e','Deletion Failed');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
</script>