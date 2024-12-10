<?php echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$segment_arr = fetch_segment();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$this->load->helpers('paymentreversal');
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Step 1 - Payment Reversal Header</a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_detail();" data-toggle="tab">Step 2 - Payment
        Reversal Detail</a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">Step 3 -
        Payment Reversal Confirmation</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="paymentreversal_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label>Type <?php required_mark(); ?></label>
                <?php echo form_dropdown('Type', array('' => 'Select Type', 'Direct' => 'Direct Payment', 'Supplier' => 'Supplier Payment'), 'Direct', 'class="form-control select2" id="Type" required onchange="voucher_type(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label>Document Date <?php required_mark(); ?></label>

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate"
                           class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label>Reference # <?php required_mark(); ?></label>
                <input type="text" name="referenceNo" id="referenceNo" class="form-control" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="partyName"><span id="party_text">Payee Name </span> <?php required_mark(); ?></label>
                <span id="party_textbox">
                        <input type="text" name="partyName" id="partyName" class="form-control">
                        </span>
            </div>
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID">Payment Currency <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="currency_validation(this.value,\'PV\')" id="transactionCurrencyID" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="PVbankCode">Bank or Cash <?php required_mark(); ?></label>
                <?php echo form_dropdown('PVbankCode', bank_accounts_drop(), '', 'class="form-control select2" id="PVbankCode" onchange="fetch_cheque_number(this.value)" required'); ?>
            </div>
        </div>
        <div class="row">

            <!--<div class="form-group col-sm-4 paymentmoad">
                <label>Cheque Number <?php /*required_mark(); */ ?></label>
                <input type="text" name="PVchequeNo" id="PVchequeNo" class="form-control">
            </div>
            <div class="form-group col-sm-4 paymentmoad">
                <label>Cheque Date <?php /*required_mark(); */ ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="PVchequeDate" data-inputmask="'alias': '<?php /*echo $date_format_policy */ ?>'"
                           value="<?php /*echo $current_date; */ ?>" id="PVchequeDate"
                           class="form-control" >
                </div>
            </div>-->
            <div class="form-group col-sm-4">
                <label for="financeyear">Financial Year <?php required_mark(); ?></label>
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <!--</div>
            <div class="row">-->
            <div class="form-group col-sm-4">
                <label for="financeyear">Financial Period <?php required_mark(); //?></label>
                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label>Memo <?php required_mark(); ?></label>
                <textarea class="form-control" rows="3" name="narration" id="narration" required></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button id="submitbtn" class="btn btn-primary" type="submit">Save & Next</button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <button type="button" onclick="payment_voucher_detail_modal()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> Add PV
                </button>
            </div>
        </div>
        <br>
        <table class="table table-bordered table-striped table-condesed">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%">PV Code</th>
                <th style="min-width: 25%" class="text-left">PV Date</th>
                <th style="min-width: 5%">Cheque No</th>
                <th style="min-width: 5%">Cheque Date</th>
                <th style="min-width: 10%">Amount</th>
                <th style="min-width: 8%">Action</th>
            </tr>
            </thead>
            <tbody id="table_body">
            <tr>
                <td colspan="7" class="text-center"><b>No Records Found</b></td>
            </tr>
            </tbody>
            <tfoot id="table_tfoot">

            </tfoot>
        </table>
    </div>
    <div id="step3" class="tab-pane">

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
                        <th>File Name</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="paymentVoucher_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">No Attachment Found</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev">Previous</button>
            <button class="btn btn-primary " onclick="save_draft()">Save as Draft</button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">Confirm</button>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="Payment_vaucher_model" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Payment Reversal Details</h5>
            </div>
            <div class="modal-body">
                <form role="form" id="payment_reversale_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%">PV Code</th>
                            <th style="min-width: 25%" class="text-left">PV Date</th>
                            <th style="min-width: 5%">Cheque No</th>
                            <th style="min-width: 5%">Cheque Date</th>
                            <th style="min-width: 10%">Amount</th>
                            <th style="min-width: 8%">Action</th>
                        </tr>
                        </thead>
                        <tbody id="tablePV_body">
                        <tr>
                            <td colspan="7" class="text-center"><b>No Records Found</b></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="savePaymentReversaleDetails()">Save changes
                </button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<?php
/** sub item master modal created by Shafry */
/*$this->load->view('system/grv/sub-views/inc-sub-item-master');
*/ ?>

<script type="text/javascript">
    var paymentReversalAutoID;
    var Type;
    var partyID;
    var currencyID;
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/PaymentReversal/erp_payment_reversal', paymentReversalAutoID, 'Payment Reversal');
        });

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#paymentreversal_form').bootstrapValidator('revalidateField', 'documentDate');
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            paymentReversalAutoID = p_id;
            load_payment_reversal_header();
            //$("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + paymentReversalAutoID + '/PV');
        } else {
            $('.btn-wizard').addClass('disabled');
            currency_validation(<?php echo json_encode(trim($this->common_data['company_data']['company_default_currencyID'])); ?>, 'PRVR');
        }

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        $('#paymentreversal_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                Type: {validators: {notEmpty: {message: 'Type is required.'}}},
                documentDate: {validators: {notEmpty: {message: 'Document Date is required.'}}},
                referenceNo: {validators: {notEmpty: {message: 'Reference # is required.'}}},
                supplier: {validators: {notEmpty: {message: 'Supplier is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Transaction Currency is required.'}}},
                /*paymentvouchercode: {validators: {notEmpty: {message: 'Payment Voucher Code is required.'}}},*/
                financeyear: {validators: {notEmpty: {message: 'Financial Year is required.'}}},
                financeyear_period: {validators: {notEmpty: {message: 'Financial Period is required.'}}},
                PVbankCode: {validators: {notEmpty: {message: 'Bank or Cash is required.'}}},
//                PVchequeDate: {validators: {notEmpty: {message: 'Cheque Date is required.'}}},
                narration: {validators: {notEmpty: {message: 'Cheque Date is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#vouchertype").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#partyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'paymentReversalAutoID', 'value': paymentReversalAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'SupplierDetails', 'value': $('#supplier option:selected').text()});
            data.push({'name': 'bank', 'value': $('#PVbankCode option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('PaymentReversal/save_paymentreversal_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data_arr) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data_arr['status']) {
                        paymentReversalAutoID = data_arr['last_id'];
                        $("#a_link").attr("href", "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + paymentReversalAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + paymentReversalAutoID + '/PV');
                        Type = $('#Type').val();
                        partyID = $('#supplier').val();
                        var result = $('#transactionCurrencyID option:selected').text().split('|');
                        currencyID = result[0];
                        $('.btn-wizard').removeClass('disabled');
                        //fetch_details(4);
                        $("#Type").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#partyID").prop("disabled", true);
                        $('[href=#step2]').tab('show');
                    } else {
                        $('#submitbtn').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }).on('error.form.bv', function (e) {

            $('#submitbtn').prop('disabled', false);

        });
    });

    function voucher_type(value, select_value) {
        if (select_value == 'undefined') {
            select_value = '';
        }
        if (value == 'Direct') {
            $('#party_text').text('Payee Name');
            $('#party_textbox').html('<input type="text" name="partyName" id="partyName" value="" class="form-control" >');
        } else {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'select_value': select_value, 'value': value},
                url: "<?php echo site_url('PaymentReversal/load_html'); ?>",
                success: function (data) {
                    $('#party_text').text(value);
                    $('#party_textbox').html(data);
                    $('.select2').select2();
                }
            });
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
                    if (data['isCash'] == 1) {
                        $(".paymentmoad").hide();
                    } else {
                        $(".paymentmoad").show();
                    }
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

    function load_payment_reversal_header() {
        if (paymentReversalAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'paymentReversalAutoID': paymentReversalAutoID},
                url: "<?php echo site_url('PaymentReversal/load_payment_reversal_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        /*$("#a_link").attr("href", "<?php //echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + PayVoucherAutoId);
                         $("#de_link").attr("href", "<?php //echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + PayVoucherAutoId + '/PV');*/
                        pvType = data['Type'];
                        partyID = data['partyID'];
                        currencyID = data['transactionCurrency'];
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $('#documentDate').val(data['documentDate']);
                        $('#narration').val(data['narration']);
                        $('#Type').val(data['Type']).change();
                        $('#referenceNo').val(data['referenceNo']);
                        $('#PVbankCode').val(data['PVbankCode']).change();

                        //$('#paymentMode').val(data['modeOfPayment']);
                        /*if (data['modeOfPayment'] == 0) {
                         $(".paymentmoad").show();
                         }*/
                        if (data['pvType'] == 'Direct') {
                            voucher_type(data['Type'], data['partyName']);
                        } else if (data['pvType'] == 'Employee') {
                            voucher_type(data['Type'], data['partyID']);
                        } else {
                            voucher_type(data['Type'], data['partyID']);
                        }
                        fetch_detail();
                        setTimeout(function () {
                            $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                        }, 1000);
                        $('#partyID').val(partyID).change();
                        $('#partyName').val(data['partyName']);
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function fetch_detail() {
        if (paymentReversalAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'paymentReversalAutoID': paymentReversalAutoID},
                url: "<?php echo site_url('PaymentReversal/fetch_PRVR_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('.currency').html('( ' + data['currency']['transactionCurrency'] + ' )');
                    $('#table_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $("#Type").prop("disabled", false);
                        $("#transactionCurrencyID").prop("disabled", false);
                        $('#table_body').append('<tr ><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $("#Type").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        tot_amount = 0;
                        currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                        $.each(data['detail'], function (key, value) {
                            $('#table_body').append('<tr><td>' + x + '</td><td>' + value['PVcode'] + '</td><td>' + value['pvDate'] + '</td><td>' + value['PVchequeNo'] + '</td><td >' + value['PVchequeDate'] + '</td><td class="text-right">' + (parseFloat(value['pvAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"> <a onclick="delete_item(' + value['paymentReversalDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            /* <a onclick="edit_item(' + value['purchaseOrderDetailsID'] + ',\'' + value['itemDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |*/
                            x++;
                            tot_amount += parseFloat(value['pvAmount']);

                        });
                         $('#table_tfoot').append('<tr><td colspan="5" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');

                    }
                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function payment_voucher_detail_modal() {
        if (paymentReversalAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'paymentReversalAutoID': paymentReversalAutoID},
                url: "<?php echo site_url('PaymentReversal/fetch_Pv_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    //$('.currency').html('( ' + data['currency']['transactionCurrency'] + ' )');
                    $('#tablePV_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#tablePV_body').append('<tr ><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $.each(data['detail'], function (key, value) {
                            currency_decimal = value['transactionCurrencyDecimalPlaces'];
                            tax = (value['amount'] / 100) * value['taxPercentage'];
                            total = parseFloat(value['amount']) + parseFloat(tax);
                            $('#tablePV_body').append('<tr><td>' + x + '</td><td>' + value['PVcode'] + '</td><td>' + value['PVdate'] + '</td><td>' + value['PVchequeNo'] + '</td><td >' + value['PVchequeDate'] + '</td><td class="text-right">' + (parseFloat(total)).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"> <input class="checkboxprvr" name="checkboxprvr[]" id="PRVRcheck_' + value['payVoucherAutoId'] + '" type="checkbox" value="' + value['payVoucherAutoId'] + '"></td></tr>');
                            x++;
                        });
                        // $('#table_tfoot').append('<tr><td colspan="8" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');

                    }
                    stopLoad();
                    $("#Payment_vaucher_model").modal({backdrop: "static"});
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }


    function savePaymentReversaleDetails() {
        var data = $('#payment_reversale_detail_form').serializeArray();
        data.push({'name': 'paymentReversalAutoID', 'value': paymentReversalAutoID});
        $.ajax(
            {
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('PaymentReversal/save_Payment_Reversale_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetch_detail();
                        $('#Payment_vaucher_model').modal('hide');
                    }
                }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
            });
    }

    function delete_item(id) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'paymentReversalDetailID': id},
                    url: "<?php echo site_url('PaymentReversal/delete_payment_reversale_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetch_detail();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_conformation() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'paymentReversalAutoID': paymentReversalAutoID, 'html': true},
            url: "<?php echo site_url('PaymentReversal/load_payment_reversal_conformation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#conform_body').html(data);
                stopLoad();
                refreshNotifications(true);
                attachment_modal_paymentReversal(paymentReversalAutoID, "Payment Reversal", "PRVR");
            }, error: function () {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    function attachment_modal_paymentReversal(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#purchaseOrder_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " Attachments");
                    $('#purchaseOrder_attachment').empty();
                    $('#purchaseOrder_attachment').append('' +data+ '');

                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }


    function confirmation() {
        if (paymentReversalAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to confirm this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'paymentReversalAutoID': paymentReversalAutoID},
                        url: "<?php echo site_url('PaymentReversal/payment_reversal_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            fetchPage('system/PaymentReversal/erp_payment_reversal', paymentReversalAutoID, 'Payment Reversal');
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (paymentReversalAutoID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/PaymentReversal/erp_payment_reversal', paymentReversalAutoID, 'Payment Reversal');
                });
        }
    }


</script>