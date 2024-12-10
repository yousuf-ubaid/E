<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_customer_invoice');
echo head_page($title, true);
$financeyear_arr = all_financeyear_drop(true);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$policyPIE = getPolicyValues('PIE', 'All');
/*echo head_page('Customer Invoice',true);*/
 $customer_arr = all_customer_drop(false);
$date_format_policy = date_format_policy();

if ($policyPIE && $policyPIE == 1) {
    $status_arr = array('all' => $this->lang->line('common_all') /*'All'*/, '1' => $this->lang->line('sales_markating_transaction_customer_draft') /*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'common_confirmed'*/, '5' => 'Preliminary Submitted', '3' => $this->lang->line('common_approved') /*'Approved'*/, '4' => 'Refer-back');
} else {
    $status_arr = array('all' => $this->lang->line('common_all') /*'All'*/, '1' => $this->lang->line('sales_markating_transaction_customer_draft') /*'Draft'*/, '2' => $this->lang->line('common_confirmed')/*'common_confirmed'*/, '3' => $this->lang->line('common_approved') /*'Approved'*/, '4' => 'Refer-back');
}
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.3/skins/all.css">
<div id="filter-panel" class="collapse filter-panel" style="border: 1px solid #80808038;margin-bottom: 15px;">
<span class="label label-default" style="border-radius: 0px;">CUSTOMER INVOICE FILTER</span> 
    <?php echo form_open('', 'role="form" id="invoicemaster_filter_form" style="padding: 5px;"'); ?>
                <div class="row">
                <div class="form-group col-sm-2">
                        <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_from'); ?> </label>
                        <!--From-->
                        <input type="date" name="IncidateDateFrom" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateFrom" class="input-small form-control">
                </div>
                <div class="form-group col-sm-2">
                    <label for="supplierPrimaryCode">&nbsp&nbsp<?php echo $this->lang->line('sales_markating_transaction_to'); ?>&nbsp&nbsp</label>
                    <!--To-->
                    <input type="date" name="IncidateDateTo" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" size="16" onchange="Otable.draw()" value="" id="IncidateDateTo" class="input-small form-control">
                </div>

                <div class="form-group col-sm-4">
                    <label for="financeyear"><?php echo $this->lang->line('common_customer_name');?></label><!--Financial Period-->
                    <div id="customer_div">
                        <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customer_array" multiple="multiple" onchange="Otable.draw()"'); ?>
                    </div>
                </div>

          <!--   <div class="form-group col-sm-3">
                <label for="supplierPrimaryCode"> <?php echo $this->lang->line('common_customer_name'); ?> </label> <br>
                <?php echo form_dropdown('customerCode[]', $customer_arr, '', 'class="form-control" id="customerCode" onchange="Otable.draw()" multiple="multiple"'); ?>
            </div> -->
            <div class="form-group col-sm-2">
                <label for="supplierPrimaryCode"><?php echo $this->lang->line('common_status'); ?> </label><br>
                <!--Status-->
                <div> <?php echo form_dropdown('status', $status_arr, '', 'class="form-control" id="status" onchange="Otable.draw()"'); ?></div>
            </div>
            <div class="form-group text-center col-sm-2">
                <button type="button" class="btn btn-default" onclick="clear_all_filters()" style="margin-top: +13%;"><i class="fa fa-ban"></i> Clear filters
                </button>
            </div>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-5">
        <table class="<?php echo table_class(); ?>">
            <tr>
                <td><span class="label label-success">&nbsp;</span> <?php echo $this->lang->line('common_confirmed'); ?> / <?php echo $this->lang->line('common_approved'); ?>
                    <!--Confirmed-->
                    <!--Approved-->
                </td>
                <td><span class="label label-danger">&nbsp;</span> <?php echo $this->lang->line('common_not_confirmed'); ?>
                    /<?php echo $this->lang->line('common_not_approved'); ?>
                    <!-- Not Confirmed-->
                    <!--Not Approved-->
                </td>
                <td><span class="label label-warning">&nbsp;</span> <?php echo $this->lang->line('common_refer_back'); ?>
                    <!--Refer-back-->
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4 text-center">
    </div>
    <div class="col-md-3 text-right">
        <button type="button" class="btn btn-primary-new size-sm " onclick="fetchPage('system/invoices/erp_invoices',null,'<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice'); ?>','PV');"><i class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_create_invoice'); ?></button>
        <!--Create Invoice-->
        <a href="#" type="button" class="btn btn-success-new size-sm " style="margin-left: 2px" onclick="excel_export()">
            <i class="fa fa-file-excel-o"></i> Excel <!--Excel-->
        </a>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table id="invoice_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_invoice_code'); ?></th>
                <!--Invoice Code-->
                <th style="min-width: 43%"><?php echo $this->lang->line('common_details'); ?></th>
                <!--Details-->
                <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_total_value'); ?></th>
                <!--Total Value-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_confirmed'); ?></th>
                <!--Confirmed-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_approved'); ?></th>
                <!--Approved-->
                <th style="min-width: 6%"><?php echo $this->lang->line('common_action'); ?></th>
                <!--Action-->
            </tr>
        </thead>
    </table>
</div>
<div class="modal fade" id="Email_modal" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <form method="post" id="Send_Email_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <input type="hidden" name="invoiceid" id="email_invoiceid" value="">
                    <h4 class="modal-title" id="EmailHeader">Email</h4>
                </div>
                <div class="modal-body">
                    <div id="emailview"> </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="tracing_modal" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Document Tracing <button class="btn btn-default pull-right" onclick="print_tracing_view()"><i class="fa fa-print"></i> </button>
            </div>
            </h4>
            <div class="modal-body">
                <input type="hidden" id="tracingId" name="tracingId">
                <input type="hidden" id="tracingCode" name="tracingCode">
                <div id="mcontainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="deleteDocumentTracing()">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="receipt_voucher_modal" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <form method="post" id="receiptvoucher_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="" style="color: #0276FD;font-family: sans-serif;">Create Receipt Voucher</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="vouchertype" name="vouchertype" value="Invoices">
                    <input type="hidden" id="invoicID" name="invoicID">
                    <input type="hidden" id="segment" name="segment">
                    <input type="hidden" id="customerID" name="customerID">
                    <input type="hidden" id="transactionCurrencyID" name="transactionCurrencyID">
                    <div class="row">
                        <div class="col-sm-4"><span style="color: black;font-family: sans-serif;" id="invoiceBal"></span></div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('accounts_receivable_ap_rv_receipt_voucher_date'); ?>
                                <!--Receipt Voucher Date--> <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="RVdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVdate" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('common_reference'); ?>
                                <!--Reference--> # </label>
                            <input type="text" name="referenceno" id="referenceno" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        if ($financeyearperiodYN == 1) {
                            ?>
                            <div class="form-group col-sm-4">
                                <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_year'); ?>
                                    <!--Financial Year--> <?php required_mark(); ?></label>
                                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_period'); ?>
                                    <!--Financial Period--> <?php required_mark(); //
                                    ?></label>
                                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="RVbankCode"><?php echo $this->lang->line('accounts_receivable_common_bank_or_cash'); ?>
                                <!--Bank or Cash--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('RVbankCode', company_bank_account_drop(), '', 'class="form-control select2" id="RVbankCode" onchange="set_payment_method()" required'); ?>
                        </div>
                        <div class="form-group col-sm-4 paymentmoad">
                            <label><?php echo $this->lang->line('accounts_receivable_common_cheque_number'); ?>
                                <!--Cheque Number--></label>
                            <input type="text" name="RVchequeNo" id="RVchequeNo" class="form-control">
                        </div>
                        <div class="form-group col-sm-4 paymentmoad">
                            <label><?php echo $this->lang->line('accounts_receivable_common_cheque_date'); ?>
                                <!--Cheque Date--> <?php required_mark(); ?></label>
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="RVchequeDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="RVchequeDate" class="form-control">
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="print_temp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Invoice Print Template</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="inviceautoid">
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label><?php echo $this->lang->line('common_type');?></label><!--Type-->
                        <select name="isPrintDN" id="isPrintDN" class="form-control select2 ">
                            <!--Select Category-->
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="print_invoice_template_base()">Print</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="retension_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Create Retension Invoice</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="retensionInviceautoid">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('common_retension');?></label><!--Type-->
                        <input type="text" class="form-control pull-right" name="retensionValue" id="retensionValue" readonly/>
                    </div>
                    <div class="form-group col-sm-4">
                        <label><?php echo 'Balance Amount';?></label><!--Type-->
                        <input type="text" class="form-control pull-right" name="retensionBalancedValue" id="retensionBalancedValue" readonly/>
                    </div>
                    <div class="form-group col-sm-4">
                        <label><?php echo 'Invoiced Amount';?></label><!--Type-->
                        <input type="text" class="form-control pull-right" name="retensionInvoicedValue" id="retensionInvoicedValue" />
                    </div>
                   
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" onclick="create_retension_invoice()">Create</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="Recurring_model" role="dialog" aria-labelledby="myModalLabel" data-width="95%" data-keyboard="false" ><!--data-backdrop="static"-->
    <div class="modal-dialog modal-lg" style="width: 50%">
        <?php echo form_open('', 'role="form" id="recurring_form"'); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close mclose" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <input type="hidden" name="invoiceAutoID" id="invoiceAutoID">
                    <input type="hidden" name="isRecurring" id="isRecurring">
                    <h4 class="modal-title" id="recurring_header">Recurring Configuration</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label for="is_recurring_check" style="font-weight: 400;">Is Recurring</label>
                            </div>
                            <div class="col-sm-1">
                                <label for="is_recurring_check">:</label>
                            </div>
                            <div class="col-sm-6">
                                <div class="skin skin-square">
                                    <div class="skin-section" id="extraColumns">
                                        <input id="is_recurring_check" type="checkbox" data-caption="" class="columnSelected" name="is_recurring_check" value="1">
                                    </div>
                                </div>
                            </div> 
                        </div>
                       
                    </div>
                    <div class="row rec">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label for="frequency_days" style="font-weight: 400;">Frequency Days <?php required_mark(); ?></label>
                            </div>
                            <div class="col-sm-1">
                                <label for="frequency_days">:</label>
                            </div>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="frequency_days" id="frequency_days" min="0" step="1">
                            </div> 
                        </div>
                    </div>
                    <div class="row rec">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label for="next_invoice_date" style="font-weight: 400;">Next Invoice Date <?php required_mark(); ?></label>
                            </div>
                            <div class="col-sm-1">
                                <label for="next_invoice_date">:</label>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="next_invoice_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $current_date; ?>" id="next_invoice_date"
                                        class="form-control invdat">
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="row rec">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label for="start_date" style="font-weight: 400;">Start Date <?php required_mark(); ?></label>
                            </div>
                            <div class="col-sm-1">
                                <label for="start_date">:</label>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="start_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $current_date; ?>" id="start_date"
                                        class="form-control strdat">
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="row rec">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label for="end_date" style="font-weight: 400;">End Date <?php required_mark(); ?></label>
                            </div>
                            <div class="col-sm-1">
                                <label for="end_date">:</label>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="end_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $current_date; ?>" id="end_date"
                                        class="form-control enddat">
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="row rec">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label for="email" style="font-weight: 400;">To <?php required_mark(); ?></label>
                            </div>
                            <div class="col-sm-1">
                                <label for="email">:</label>
                            </div>
                            <div class="col-sm-6">
                                <input type="email" name="email" id="email" class="form-control" value=""
                                   placeholder="example@example.com" style="margin-left: -10px">
                            </div> 
                        </div>
                    </div>
                    <div class="row rec">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label for="ccemail" style="font-weight: 400;">Cc</label>
                            </div>
                            <div class="col-sm-1">
                                <label for="ccemail">:</label>
                            </div>
                            <div class="col-sm-6">
                                <input type="email" name="ccemail" id="ccemail" class="form-control"
                                   placeholder="example@example.com" style="margin-left: -10px">
                            </div> 
                        </div>
                    </div>
                </div>
            </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary-new size-lg save" onclick="save_recurring()">Save</button>
                    <button type="button" class="btn btn-default size-lg mclose" data-dismiss="modal">Close</button>
                </div>
            </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.3/icheck.min.js"></script>
<script type="text/javascript">
    var invoiceAutoID;
    var currentRequest = null;
    var Otable;
    $(document).ready(function() {
        $('.headerclose').click(function() {
            fetchPage('system/invoices/invoices_management', '', 'Customer Invoices');
        });
        invoiceAutoID = null;
        number_validation();
        invoice_table();

        Inputmask().mask(document.querySelectorAll("input"));
        $(".paymentmoad").hide();
        $('.select2').select2();

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function(ev) {
            $('#receiptvoucher_form').bootstrapValidator('revalidateField', 'RVdate');
        });
        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'] ?? '')); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'] ?? '')); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'] ?? '')); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'] ?? '')); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        $('#receiptvoucher_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid'); ?>.',
            /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                RVdate: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('accounts_receivable_common_receipt_voucher_date_is_required'); ?>.'
                        }
                    }
                },
                /*Receipt Voucher Date is required*/
                RVbankCode: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo $this->lang->line('accounts_receivable_common_bank_or_cash_is_required'); ?>.'
                        }
                    }
                } /*Bank or Cash is required*/
            },
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({
                'name': 'companyFinanceYear',
                'value': $('#financeyear option:selected').text()
            });
            data.push({
                'name': 'bank',
                'value': $('#RVbankCode option:selected').text()
            });
            data.push({
                'name': 'invoiceAutoID',
                'value': $('#invoicID').val()
            });
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*Are you sure?*/
                    text: "You want to create this document!",
                    /*You want to confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                    /*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function() {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Invoices/save_receiptvoucher_from_CINV_header'); ?>",
                        beforeSend: function() {
                            startLoad();
                        },
                        success: function(data) {
                            if (data[0] == 's') {
                                $("#segment").val('');
                                $("#customerID").val('');
                                $("#invoicID").val('');
                                $("#transactionCurrencyID").val('');
                                $("#referenceno").val('');
                                $("#RVbankCode").val('').change();
                                $("#RVchequeNo").val('');
                                $("#receipt_voucher_modal").modal('hide');
                                confirmReceiptVoucher(data[2])
                            } else {
                                stopLoad();
                                myAlert(data[0], data[1]);
                            }
                        },
                        error: function() {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.'); /*An Error Occurred! Please Try Again*/
                            stopLoad();
                        }
                    });
                });
        });

        $('#customer_array').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '365px',
            maxHeight: '30px',
            templates: {
                filter: '<li class="multiselect2-item multiselect2-filter"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect2-search customer_select" id="customer_select" type="text" onkeyup=""></div></li>',
                filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect2-clear-filter" type="button" onclick="load_customer_dropdown()"><i class="fa fa-search"></i></button></span>',
            }
        }); 

        $('#Recurring_model').hide();
        $('.rec').hide();
        
         // Initialize iCheck plugin for checkboxes
         $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

          // Show/hide input field based on checkbox status
          $('#is_recurring_check').on('ifChanged', function () {
            if ($(this).is(':checked')) {
                $('#isRecurring').val(1);
                $('.rec').show();
                $('.save').prop('disabled', false);
            } else {
                $('#isRecurring').val('');
                $('.rec').hide();
                $('.save').prop('disabled', true);
            }
        });

        // Reset checkbox state when modal is closed
        $('#Recurring_model').on('hidden.bs.modal', function () {
            $('#is_recurring_check').iCheck('uncheck');
            $('#isRecurring').val('');
            $('#invoiceAutoID').val('');
            $('#frequency_days').val('');
            $('#next_invoice_date').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#email').val('');
            $('#ccemail').val('');
            $('.rec').hide();
        });

    });
    

    function Recurring_model(id){
        $('#invoiceAutoID').val(id);
        $('#isRecurring').val('');
        $('#frequency_days').val('');
        $('#next_invoice_date').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        $('#email').val('');
        $('#ccemail').val('');
        //$('#recurning_checkID_' + id).iCheck('check').trigger('ifChanged');
        $('#Recurring_model').modal({backdrop: "static"});
        recurring_det(id);
        customer_email(id);
    }

    function save_recurring(){
        var data = $('#recurring_form').serializeArray();
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Invoices/save_recurring'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                refreshNotifications(true);
                if(data){
                    $('#invoiceAutoID').val('');
                    $('#frequency_days').val('');
                    $('#next_invoice_date').val('');
                    $('#start_date').val('');
                    $('#end_date').val('');
                    $('#isRecurring').val('');
                    // $('#Recurring_model').hide();
                    $('#Recurring_model').modal('hide');
                    
                    //invoice_table();
                    Otable.draw();
                } 
            },
            error: function() {
                stopLoad();
                MyAlert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    function recurring_det(id){
        $('#email_invoiceid').val(id);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'invoiceAutoID': id
            },
            url: "<?php echo site_url('Invoices/recurring_det'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                if (!jQuery.isEmptyObject(data.rec_det)) {
                    if(data.rec_det['isRecurring'] == 1){
                        $('#is_recurring_check').iCheck('check');
                    }
                    $('#invoiceAutoID').val(data.rec_det['invoiceAutoID']);
                    $('#isRecurring').val(data.rec_det['isRecurring']);
                    $('#frequency_days').val(data.rec_det['frequencyDays']);
                    $('#next_invoice_date').val(data.rec_det['nexInvoiceDate']);
                    $('#start_date').val(data.rec_det['policyStartDate']);
                    $('#end_date').val(data.rec_det['policyEndDate']);

                }

                if (!jQuery.isEmptyObject(data.rec_email)) {

                    $.each(data.rec_email, function(index, valu) {
                        var toEmailAddress = valu.toEmailAddress;
                        var type = valu.type;

                        if(type == 1){
                            $('#email').val(toEmailAddress);
                        }else if(type == 2){
                            $('#ccemail').val(toEmailAddress);
                        }
                    });
  
                }
                
               
                stopLoad();
                refreshNotifications(true);
            },
            error: function() {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    function invoice_table(selectedID = null) {
        Otable = $('#invoice_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Invoices/fetch_invoices'); ?>",
            "aaSorting": [
                [0, 'desc']
            ],
            "lengthMenu": [
                [10, 20, 50, -1],
                [10, 20, 50, "All"]
            ],
            "pageLength": 20,
            "fnInitComplete": function() {

            },
            "fnDrawCallback": function(oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i;
                    (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['invoiceAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
                $('.xEditableDate').editable({
                    url: '<?php echo site_url('Invoices/update_acknowledgementDate_CINV') ?>',
                    send: 'always',
                    ajaxOptions: {
                        type: 'post',
                        dataType: 'json',
                        success: function(data) {
                            if(data[0]=='e')
                            {
                                myAlert(data[0], data[1],data[2],data[3]);
                                //Otable.draw();
                                /* alert(data[2]); */
                                setTimeout(function () {
                                    $('.date_change_'+data[3]).editable('setValue', data[2],true);
                                 }, 1500);
                               // $('.xEditableDate').editable('setValue',data[2]);
                            }
                        },
                        error: function(xhr) {
                            myAlert('e', xhr.responseText);
                        }
                    }
                });

                $('.xEditableDate').editable({
                    format: 'DD-MM-YYYY',
                    viewformat: 'DD.MM.YYYY',
                    template: 'D/MMMM/YYYY ',
                    combodate: {
                        minYear: 1930,
                        maxYear: <?php echo format_date_getYear() + 10 ?>,
                        minuteStep: 1
                    },
                    success: function(response) {
                        if (response) {
                            myAlert('s', 'Acknowledgement Date Updated Successfully!');
                        }
                    }
                });

                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [{
                    "mData": "invoiceAutoID"
                },
                {
                    "mData": "invoiceCode"
                },
                {
                    "mData": "invoice_detail"
                },
                {
                    "mData": "total_value"
                },
                {
                    "mData": "confirmed"
                },
                {
                    "mData": "approved"
                },
                {
                    "mData": "edit"
                },
                {
                    "mData": "invoiceNarration"
                },
                {
                    "mData": "customermastername"
                },
                {
                    "mData": "invoiceDate"
                },
                {
                    "mData": "invoiceDueDate"
                },
                {
                    "mData": "invoiceType"
                },
                {
                    "mData": "referenceNo"
                },
                {
                    "mData": "total_value_search"
                },
                {
                    "mData": "transactionCurrency"
                }
            ],
            "columnDefs": [{
                "targets": [6],
                "orderable": false
            }, {
                "visible": false,
                "searchable": true,
                "targets": [7, 8, 9, 10, 11, 12, 13, 14]
            }, {
                "targets": [0, 3],
                "visible": true,
                "searchable": false
            }],
            "fnServerData": function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "datefrom",
                    "value": $("#IncidateDateFrom").val()
                });
                aoData.push({
                    "name": "dateto",
                    "value": $("#IncidateDateTo").val()
                });
                aoData.push({
                    "name": "status",
                    "value": $("#status").val()
                });
                aoData.push({
                    "name": "customerCode",
                    "value": $("#customer_array").val()
                });
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }


    function colorLabel(labelID) {
        // $('#msg-div').show();
    }

    $('.table-row-select tbody').on('click', 'tr', function() {
        $('.table-row-select tr').removeClass('dataTable_selectedTr');
        $(this).toggleClass('dataTable_selectedTr');
    });

    function delete_item(id, value) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                /*You want to delete this record*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_delete'); ?>",
                /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'invoiceAutoID': id
                    },
                    url: "<?php echo site_url('Invoices/delete_invoice_master'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        refreshNotifications(true);
                        stopLoad();
                        Otable.draw();
                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function referback_customer_invoice(id, isSytemGenerated) {
        if (isSytemGenerated != 1) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*/!*Are you sure?*!/*/
                    text: "<?php echo $this->lang->line('common_you_want_to_refer_back'); ?>",
                    /*You want to refer back!*/
                    type: "warning",
                    /*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                    /*Yes*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function() {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'invoiceAutoID': id
                        },
                        url: "<?php echo site_url('Invoices/referback_customer_invoice'); ?>",
                        beforeSend: function() {
                            startLoad();
                        },
                        success: function(data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                Otable.draw();
                            }
                        },
                        error: function() {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        } else {
            swal(" ", "This is System Generated Document,You Cannot Refer Back this document", "error");
        }

    }

    function clear_all_filters() {
        $('#IncidateDateFrom').val("");
        $('#IncidateDateTo').val("");
        $('#status').val("all");
        $('#customer_array').multiselect2('deselectAll', false);
        $('#customer_array').multiselect2('updateButtonText');
        Otable.draw();
    }

    function reOpen_contract(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_re_open'); ?>",
                /*You want to re open!*/
                type: "warning",
                /*warning*/
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                /*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'invoiceAutoID': id
                    },
                    url: "<?php echo site_url('Invoices/re_open_invoice'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        Otable.draw();
                        stopLoad();
                        refreshNotifications(true);
                    },
                    error: function() {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function customer_email(id){
        $('#email_invoiceid').val(id);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'invoiceAutoID': id
            },
            url: "<?php echo site_url('Invoices/customer_invoiceloademail'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                if (!jQuery.isEmptyObject(data)) {
                        $('#email').val(data['customerEmail']);
                    }
               
                stopLoad();
                refreshNotifications(true);
            },
            error: function() {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    function sendemail(id) {
        $('#email_invoiceid').val(id);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'invoiceAutoID': id
            },
            url: "<?php echo site_url('Invoices/invoiceloademail'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                $("#emailview").html(data);
                $("#Email_modal").modal();
                /*   alert(data['customerEmail']);
                   $("#Email_modal").modal();
                   if (!jQuery.isEmptyObject(data)) {
                       $('#email').val(data['customerEmail']);
                   }*/
                load_mail_history();
                stopLoad();
                refreshNotifications(true);
            },
            error: function() {
                stopLoad();
                alert('An Error Occurred! Please Try Again.');
                refreshNotifications(true);
            }
        });
    }

    function SendQuotationMail() {
        var form_data = $("#Send_Email_form").serialize();
        swal({
                title: "Are You Sure?",
                text: "You Want To Send This Mail",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            },
            function() {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: form_data,
                    url: "<?php echo site_url('Invoices/send_invoice_email'); ?>",
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $("#Email_modal").modal('hide');
                            save_document_email_history(data[2], 'CINV', data[3]);
                        }
                    },
                    error: function() {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function confirmCustomerInvoicefront(invoiceAutoID) {
        swal({
                title: "Are you sure?",
                text: "You want to confirm this document?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: true
            },
            function() {
                $.ajax({
                    url: "<?php echo site_url('Invoices/invoice_confirmation'); ?>",
                    type: 'post',
                    data: {
                        invoiceAutoID: invoiceAutoID
                    },
                    dataType: 'json',
                    cache: false,
                    beforeSend: function() {
                        startLoad();
                    },
                    success: function(data) {

                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            setTimeout(function() {
                                Otable.draw();
                            }, 500);
                        }
                        setTimeout(function() {
                            stopLoad();
                        }, 500);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }

    function traceDocument(cinvID, DocumentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'invoiceAutoID': cinvID,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/trace_cinv_document'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                //myAlert(data[0], data[1]);
                $(window).scrollTop(0);
                load_document_tracing(cinvID, DocumentID);
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function load_document_tracing(id, DocumentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'purchaseOrderID': id,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/select_tracing_documents'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $("#mcontainer").empty();
                $("#mcontainer").html(data);
                $("#tracingId").val(id);
                $("#tracingCode").val(DocumentID);

                $("#tracing_modal").modal('show');

            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function deleteDocumentTracing() {
        var purchaseOrderID = $("#tracingId").val();
        var DocumentID = $("#tracingCode").val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'purchaseOrderID': purchaseOrderID,
                'DocumentID': DocumentID
            },
            url: "<?php echo site_url('Tracing/deleteDocumentTracing'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $("#tracing_modal").modal('hide');
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }

    function load_mail_history() {
        var Otables = $('#mailhistory').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Invoices/load_mail_history'); ?>",
            aaSorting: [
                [0, 'desc']
            ],
            "bFilter": false,
            "bInfo": false,
            "bLengthChange": false,
            "fnInitComplete": function() {

            },
            "fnDrawCallback": function(oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i;
                    (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [{
                    "mData": "autoID"
                },
                {
                    "mData": "invoiceCode"
                },
                {
                    "mData": "ename"
                },
                {
                    "mData": "toEmailAddress"
                },
                {
                    "mData": "sentDateTime"
                }

            ],
            //"columnDefs": [{"targets": [0], "visible": false,"searchable": true}],
            "fnServerData": function(sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "invoiceAutoID",
                    "value": $("#email_invoiceid").val()
                });
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function issystemgenerateddoc() {
        swal(" ", "This is System Generated Document,You Cannot Edit this document", "error");
    }

    function open_receipt_voucher_modal(id) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'invoiceAutoID': id
            },
            url: "<?php echo site_url('Invoices/open_receipt_voucher_modal'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $(".paymentmoad").hide();

                $("#invoicID").val(id);
                $("#segment").val(data['master']['segmentID']);
                $("#customerID").val(data['master']['customerID']);
                $("#transactionCurrencyID").val(data['master']['transactionCurrencyID']);
                $("#referenceno").val(data['master']['invoiceCode']);
                $("#RVchequeNo").val('');
                if (!jQuery.isEmptyObject(data['GL'])) {
                    $("#RVbankCode").val(data['GL']['GLAutoID']).change();
                }
                $('#invoiceBal').html('Invoice Balance :- ' + data['balance'] + ' (' + data['master']['transactionCurrency'] + ')');

                $("#receipt_voucher_modal").modal('show');
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function set_payment_method() {
        val = $('#RVbankCode option:selected').text();
        res = val.split(" | ")
        if (res[5] == 'Cash') {
            $(".paymentmoad").hide();
        } else {
            $(".paymentmoad").show();
        }
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'companyFinanceYearID': companyFinanceYearID
            },
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function(data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Financial Period'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function(val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#financeyear_period").val(select_value);
                    };
                }
            },
            error: function() {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }


    function confirmReceiptVoucher(receiptVoucherAutoId) {
        if (receiptVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'receiptVoucherAutoId': receiptVoucherAutoId
                },
                url: "<?php echo site_url('Receipt_voucher/receipt_confirmation'); ?>",
                beforeSend: function() {
                    startLoad();
                },
                success: function(data) {
                    stopLoad();
                    if (data['error'] == 1) {
                        myAlert('e', data['message']);
                    } else if (data['error'] == 2) {
                        myAlert('w', data['message']);
                    } else {
                        //myAlert('s',data['message']);
                        swal("Success", "Receipt Voucher " + data['code'] + " Created Successfully ", "success");
                    }
                },
                error: function() {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });

        };
    }

    function excel_export() {
        var form = document.getElementById('invoicemaster_filter_form');
        form.target = '_blank';
        form.method = 'post';
        form.post = $('#invoicemaster_filter_form').serializeArray();
        form.action = '<?php echo site_url('Invoices/export_excel_invoice'); ?>';
        form.submit();
    }

    function update_preliminary_print_status(invoiceAutoID) {
        var checked = 0;
        if ($('#isprimilinaryPrinted_' + invoiceAutoID).is(":checked")) {
            checked = 1;
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'invoiceAutoID': invoiceAutoID, 'checked' : checked
            },
            url: "<?php echo site_url('Invoices/update_preliminaryPrint_status_update'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                if (data) {
                    myAlert('s', 'Preliminary Print Status Updated Successfully!');
                } else {
                    myAlert('e', 'Falied to Update Preliminary Print Status!');
                }
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function printTemplate_select(invoiceAutoID)
    {
        $('#inviceautoid').val(invoiceAutoID);
        $('#isPrintDN').empty();
        var mySelect = $('#isPrintDN');
        /*mySelect.append($('<option></option>').val(0).html('Default template'));
        mySelect.append($('<option></option>').val(1).html('Simplified VAT invoice'));
        mySelect.append($('<option></option>').val(2).html('Invoice Only'));
        mySelect.append($('<option></option>').val(2).html('Tax Invoice Only'));
        mySelect.append($('<option></option>').val(3).html('Delivery Note Only'));
        mySelect.append($('<option></option>').val(4).html('Tax Invoice with Tax Details Only'));
        $('#print_temp_modal').modal('show');*/

        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            //data: {'invoiceTemplateMasterID': id},
            url: "<?php echo site_url('Invoices/load_invoice_template_list'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                mySelect.append($('<option></option>').val(0).html('Default template'));
                mySelect.append($('<option></option>').val(1).html('Simplified VAT invoice'));
                mySelect.append($('<option></option>').val(2).html('Invoice Only'));
                
                mySelect.append($('<option></option>').val(2).html('Tax Invoice Only'));
                mySelect.append($('<option></option>').val(3).html('Delivery Note Only'));
                mySelect.append($('<option></option>').val(4).html('Tax Invoice with Tax Details Only'));
                mySelect.append($('<option></option>').val(5).html('SVAT invoice'));
              
                $.each(data, function(index) {
                    mySelect.append($('<option></option>').val(data[index].invoiceTemplateMasterID).html(data[index].invoiceTemplateName));
                });
                
                stopLoad();

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
        $('#print_temp_modal').modal('show');

    }

    function print_invoice_template_base(){
        var printtype =  $('#isPrintDN').val();
        var invoiceID =   $('#inviceautoid').val();

       if(invoiceID==''){
            myAlert('e', 'Select Print Type');
        }else{
            window.open("<?php echo site_url('Invoices/load_invoices_conformation') ?>" +'/'+ invoiceID +'/'+ printtype);
        }
    }
    
    function load_customer_dropdown()
    {
        var typed = $('.customer_select').val();
        var selected_customers = $('#customer_array').val();
        if(typed != '')
        {
            currentRequest = $.ajax({
                async: false,
                dataType: "html",
                type: "POST",
                url: "<?php echo site_url('Invoices/load_customer_dropdown'); ?>",
                data: {search_value: typed, selected_customers: selected_customers},
                beforeSend: function() {
                    if (currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success: function(data) {
                    $('#customer_div').empty();
                    $('#customer_div').html(data);
                    $('#customer_array').multiselect2({
                        enableCaseInsensitiveFiltering: true,
                        includeSelectAllOption: true,
                        numberDisplayed: 1,
                        buttonWidth: '365px',
                        maxHeight: '30px',
                        templates: {
                            filter: '<li class="multiselect2-item multiselect2-filter"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect2-search customer_select" id="customer_select" type="text" onkeyup=""></div></li>',
                            filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect2-clear-filter" type="button" onclick="load_customer_dropdown()"><i class="fa fa-search"></i></button></span>',
                        }
                    });
                    if(selected_customers) {
                        $('#customer_array').val(selected_customers).trigger('change');
                    }
                }
            });
            $('.btn-group').addClass('open');
        }
    }

    function Retension_model(id){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'invoiceAutoID': id,'documentID' : 'CINV'
            },
            url: "<?php echo site_url('Invoices/get_retension_details'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                $('#retensionInviceautoid').val(id);
                $('#retensionValue').val(data['retensionAmount']);
                $('#retensionBalancedValue').val(data['balance']);
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

      

        $('#retension_model').modal('show');

    }

    function create_retension_invoice(){

        var retensionInviceautoid = $('#retensionInviceautoid').val();
        var retensionBalancedValue = $('#retensionBalancedValue').val();
        var retensionInvoicedValue = $('#retensionInvoicedValue').val();


        if(parseFloat(retensionInvoicedValue) > parseFloat(retensionBalancedValue)){
            myAlert('e','Invoiced Amount Cannot be Greater than Balanced Amount');
            return false;
        }

  
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'invoiceAutoID': retensionInviceautoid,'documentID' : 'CINV','amount' : retensionInvoicedValue
            },
            url: "<?php echo site_url('Invoices/create_retension_invoice'); ?>",
            beforeSend: function() {
                startLoad();
            },
            success: function(data) {
                stopLoad();
                myAlert('s','Retension Invoice Created Successfully');
                $('#retension_model').modal('hide');
            },
            error: function() {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }
</script>

