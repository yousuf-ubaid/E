<?php
$this->load->helper('buyback_helper');
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
/*$title = $this->lang->line('sales_markating_transaction_add_new_customer_invoice');
echo head_page($title, false);*/
echo head_page($_POST['page_name'], false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$segment_arr = fetch_segment();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$customer_arr = all_customer_drop();
$projectExist = project_is_exist();
$drivermaster = buyback_driver_masterdrop();
$vehiclemaster = buyback_vehicle_numberdrop();
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>" />
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"> <?php echo $this->lang->line('sales_markating_transaction_step_one');?> - <?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_header');?> </a><!--Step 1--><!--Invoice Header-->
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_details();" data-toggle="tab"><?php echo $this->lang->line('sales_markating_transaction_step_two');?> - <?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_detail');?> </a><!--Step 2 --><!--Invoice
        Detail-->
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab"><?php echo $this->lang->line('sales_markating_transaction_step_three');?> - <?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_confirmation');?>
    </a><!--Step 3--><!--Invoice Confirmation-->
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="invoice_form"'); ?>
        <input type="hidden" id="customerCreditPeriodhn" name="customerCreditPeriodhn">
        <div class="row">
            <div class="form-group col-sm-4">
                <label> <?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_type');?> <?php required_mark(); ?></label><!--Invoice Type -->
                <?php echo form_dropdown('invoiceType', array('' =>$this->lang->line('common_select_type') /*'Select Type'*/, 'Direct' =>$this->lang->line('sales_markating_transaction_add_new_customer_direct_invoice') /*'Direct Invoice'*/,'Quotation' =>$this->lang->line('sales_markating_transaction_add_new_customer_quotation_based') /*'Quotation Based'*/,'Contract' =>$this->lang->line('sales_markating_transaction_add_new_customer_contract_based') /*'Contract Based'*/,'Sales Order' => $this->lang->line('sales_markating_transaction_add_new_customer_sales_order_based') /*'Sales Order Based'*/), 'Sales Order', 'class="form-control select2" onchange="validatenarration()" id="invoiceType" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></label><!--Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment" required'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label><?php echo $this->lang->line('common_document_date');?> <?php required_mark(); ?></label><!--Document Date-->
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="invoiceDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $current_date; ?>" id="invoiceDate"
                           class="form-control invdat" required>
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_date');?> <?php required_mark(); ?></label><!--Customer Invoice Date-->
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="customerInvoiceDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="customerInvoiceDate" class="form-control invdat" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_due_date');?>  <?php required_mark(); ?></label><!--Invoice Due Date-->
                <div class="input-group datepicinvduedat">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="invoiceDueDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $current_date; ?>" id="invoiceDueDate"
                           class="form-control invduedat" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_reference');?> # </label><!--Reference-->
                <input type="text" name="referenceNo" id="referenceNo" class="form-control">
            </div>
            <div class="form-group col-sm-4">
                <label for="customerID"><span id="party_text"><?php echo $this->lang->line('common_customer_name');?> </span> <?php required_mark(); ?></label><!--Customer Name-->
                <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="customerID" required onchange="Load_customer_currency(this.value)"'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_currency');?> <?php required_mark(); ?></label><!--Invoice Currency-->
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="currency_validation(this.value,\'HCINV\')" id="transactionCurrencyID" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year');?>  <?php required_mark(); ?></label><!--Financial Year-->
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period');?> <?php required_mark(); ?></label><!--Financial Period-->
                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>

        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="RVbankCode"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_remittance_details');?> </label><!--Remittance Details-->
                <?php echo form_dropdown('RVbankCode', company_bank_account_drop(), '24224', 'class="form-control select2" id="RVbankCode" '); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="contactPersonName"><?php echo $this->lang->line('sales_markating_transaction_document_contact_person_name');?> </label><!--Contact Person Name-->
                <input type="text" class="form-control " id="contactPersonName" name="contactPersonName">
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_telephone');?> <?php echo $this->lang->line('common_number');?> </label><!--Telephone Number-->
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <input type="text" class="form-control " id="contactPersonNumber" name="contactPersonNumber">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_type');?> <?php required_mark(); ?></label><!--Type-->
                <?php echo form_dropdown('isPrintDN', array('0' => $this->lang->line('sales_markating_transaction_add_new_customer_print_invoice_only')/*'Print Invoice only'*/, '1' =>$this->lang->line('sales_markating_transaction_add_new_customer_print_invoice_and_delivery_note') /*'Print Invoice & Delivery note'*/), 1, 'class="form-control select2" id="isPrintDN" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('sales_markating_transaction_sales_person');?></label><!--Sales person-->
                <?php echo form_dropdown('salesPersonID', all_srp_erp_sales_person_drop(),'','class="form-control select2" id="salesPersonID"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_narration');?></label></label><!--Narration-->
                <textarea class="form-control" rows="2" name="invoiceNarration" id="invoiceNarration"></textarea>
            </div>


        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label>WareHouse <?php required_mark(); ?></label>
                <?php echo form_dropdown('warehouseAutoIDtemp', all_delivery_location_drop(), '', ' id="warehouseAutoIDtemp" class="form-control select2 wareHouseAutoID"'); ?>
            </div>

            <div class="form-group col-sm-4">
                <label for="">Show Tax Summary</label>
                <select name="showTaxSummaryYN" class="form-control" id="showTaxSummaryYN">
                    <option value="1">Yes</option>
                    <option value="0" selected="selected">No</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-4">
                <label>Driver Name </label>
                <?php echo form_dropdown('driermasterID', $drivermaster, '', ' id="driermasterID" class="form-control select2 driermasterID"'); ?>
            </div>

            <div class="form-group col-sm-4">
                <label>Vehicle No </label>
                <?php echo form_dropdown('vehiclemasterID', $vehiclemaster, '', ' id="vehiclemasterID" class="form-control select2 vehiclemasterID"'); ?>
            </div>
        </div>
        <!--<hr>-->
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?></button><!--Save & Next-->
        </div>
        <hr>
        <div class="row">
            <div class="form-group col-sm-12">
                <label><?php echo $this->lang->line('common_notes');?> </label><!--Notes-->
                <textarea class="form-control" rows="6" name="invoiceNote" id="invoiceNote"></textarea>
            </div>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">

    </div>
    <div id="step3" class="tab-pane">
        <!-- <div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank"
                   href="<?php /*echo site_url('Double_entry/fetch_double_entry_customer_invoice/'); */?>"><span
                        class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review
                    entries
                </a>
                <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank"
                   href="<?php /*echo site_url('Invoices/load_pv_conformation/'); */?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div>
        <hr>-->
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="customerInvoice_attachment_label"><?php echo $this->lang->line('sales_markating_transaction_model_title');?> </h4><!--Modal title-->
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name');?> </th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                        <th><?php echo $this->lang->line('common_type');?> </th><!--Type-->
                        <th><?php echo $this->lang->line('common_action');?> </th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="customerInvoice_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?> </td><!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?></button><!--Previous-->
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?> </button><!--Save as Draft-->
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?></button><!--Confirm-->
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="all_item_edit_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 95%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Customer Invoice</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="edit_all_item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="customer_invoice_detail_all_edit_table">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('sales_markating_transaction_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th><?php echo $this->lang->line('common_warehouse'); ?><?php required_mark(); ?></th>
                            <!--Warehouse-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('sales_markating_transaction_project'); ?><?php required_mark(); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th style="width:100px;">Current Stock<?php required_mark(); ?></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?><?php required_mark(); ?></th>
                            <!--Qty-->
                            <th style="width: 120px;"><?php echo $this->lang->line('common_amount'); ?> <?php required_mark(); ?>
                                <span
                                    class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <!--Amount-->
                            <th colspan="2"><?php echo $this->lang->line('sales_markating_transaction_discount'); ?> </th>
                            <!--Discount-->
                            <th colspan="2"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?>  </th>
                            <!--Tax-->
                            <th><?php echo $this->lang->line('common_comment'); ?>  </th><!--Comment-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_edit_customer_invoice()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody id="edit_item_table_body">

                        </tbody>
                        <tfoot id="edit_item_table_tfoot">

                        </tfoot>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                <button class="btn btn-primary" type="button" onclick="updateCustomerInvoice_edit_all_Item()"><?php echo $this->lang->line('common_update_changes');?>
                </button><!--Update changes-->
            </div>

        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/keyboard-short-cut/shortcut.js'); ?>"></script>

<script type="text/javascript">
    var invoiceAutoID;
    var invoiceType;
    var customerID;
    var currencyID;
    var changeInvoiceDueDate = 0;
    $(document).ready(function () {


        $('.headerclose').click(function(){
            fetchPage('system/invoices/invoices_management_buyback',invoiceAutoID,'Customer Invoices');
        });
        $('.select2').select2();
        invoiceAutoID = null;
        invoiceType = null;
        customerID = null;
        currencyID = null;

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });

        $('.datepicinvduedat').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });

        $('.datepic').on('dp.change', function(e){ change_invoice_due_date(); });
        $('.datepicinvduedat').on('dp.change', function(e){ changeInvoiceDueDateFlag(); });


        Inputmask({alias: date_format_policy, "oncomplete": function(e){ change_invoice_due_date(); }}).mask(document.querySelectorAll('.invdat'));
        Inputmask({alias: date_format_policy, "oncomplete": function(e){ changeInvoiceDueDateFlag(); }}).mask(document.querySelectorAll('.invduedat'));


        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if (p_id) {
            invoiceAutoID = p_id;
            load_invoice_header();
        } else {
            $('.btn-wizard').addClass('disabled');
            CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
            currency_validation_modal(CurrencyID,'HCINV','','');
            $("#invoiceNote").wysihtml5();
        }

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID,periodID);

        $('#invoice_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                invoiceType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_invoice_type_is_required');?>.'}}},/*Invoice Type is required*/
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}},/*Segment is required*/
                invoiceDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_date_is_required');?>.'}}},/*Document Date is required*/
                customerInvoiceDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_customer_date_is_required');?>.'}}},/*Customer Date is required*/
                InvoiceDueDate : {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_invoice_due_date_is_required');?>.'}}},/*Invoice Due Date is required*/
                //referenceNo: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_reference_no_is_required');?>.'}}},/*Reference No is required*/
                customerID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_customer_is_required');?>.'}}},/*Customer is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_currency_is_required');?>.'}}},/*Transaction Currency is required*/
                financeyear: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year_is_required');?>.'}}},/*Financial Year is required*/
                financeyear_period: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period_is_required');?>.'}}},/*Financial Period is required*/
                invoiceDueDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_invoice_due_date_is_required');?>.'}}},/*Invoice Due Date is required*/
                location: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_warehouse_location_is_required');?>.'}}},/*Warehouse Location is required*/
                warehouseAutoIDtemp: {validators: {notEmpty: {message: 'Warehouse is required'}}},
                //invoiceNarration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_narration_is_required');?>.'}}}/*Invoice Narration is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#invoiceType").prop("disabled", false);
            $("#customerID").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'invoiceAutoID', 'value': invoiceAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'location_dec', 'value': $('#location option:selected').text()});
            data.push({'name' : 'currency_code', 'value' : $('#transactionCurrencyID option:selected').text()});
            data.push({'name' : 'salesPerson', 'value' : $('#salesPersonID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('InvoicesPercentage/save_invoice_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        invoiceAutoID = data['last_id'];
                        invoiceType = $('#invoiceType').val();
                        customerID = $('#supplier').val();
                        currencyID = $('#transactionCurrencyID').val();
                        $("#a_link").attr("href", "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>/" + invoiceAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice_buyback');?>/" + invoiceAutoID + '/HCINV');
                        $('[href=#step2]').tab('show');
                        $('.btn-wizard').removeClass('disabled');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        fetch_details();
                        $("#invoiceType").prop("disabled", true);
                        $("#customerID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });
    });



    function confirmation() {
        if (invoiceAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document*/
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
                        data: {'invoiceAutoID': invoiceAutoID},
                        url: "<?php echo site_url('InvoicesPercentage/invoice_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            //refreshNotifications(true);
                            myAlert(data[0], data[1]);
                            if(data[0]=='e' && data[1]=='Some Item quantities are not sufficient to confirm this transaction.'){
                                confirm_all_item_detail_modal(data[2]);
                            }
                            if(data[0]=='s'){
                                setTimeout(function(){
                                    fetchPage('system/invoices/invoices_management_buyback', invoiceAutoID, 'Invoices');
                                }, 500);
                            }
                            setTimeout(function(){
                                stopLoad();
                            }, 500);
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function currency_validation(CurrencyID,documentID){
        if (CurrencyID) {
            partyAutoID = $('#customerID').val();
            currency_validation_modal(CurrencyID,documentID,partyAutoID,'CUS');
        }
    }

    function fetch_details(tab) {
        fetch_detail(invoiceType, customerID, currencyID,tab);
    }

    function load_conformation() {
        if (invoiceAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'invoiceAutoID': invoiceAutoID, 'html': true},
                url: "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>/" + invoiceAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice_buyback');?>/" + invoiceAutoID + '/HCINV');
                    attachment_modal_customerInvoice(invoiceAutoID, "<?php echo $this->lang->line('sales_markating_invoice');?>", "HCINV");/*Invoice*/
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

    function fetch_detail(type, customerID, currencyID,tab) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'invoiceAutoID': invoiceAutoID,
                'invoiceType': type,
                'customerID': customerID,
                'currencyID': currencyID,
                'tab': tab,
            },
            url: "<?php echo site_url('InvoicesPercentage/fetch_detail_buyback'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#step2').html(data);
                //check_detail_dataExist(invoiceAutoID);
                $('[href=#step2]').tab('show');
                $('a[data-toggle="tab"]').removeClass('btn-primary');
                $('a[data-toggle="tab"]').addClass('btn-default');
                $('[href=#step2]').removeClass('btn-default');
                $('[href=#step2]').addClass('btn-primary');
                setTimeout(function () {
                    tab_active(1);
                }, 300);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function tab_active(id) {
        $(".nav-tabs a[href='#tab_" + id + "']").tab('show');
    }

    function check_detail_dataExist(invoiceAutoID){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'invoiceAutoID':invoiceAutoID},
            url :"<?php echo site_url('Invoices/fetch_detail_header_lock'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                if(jQuery.isEmptyObject(data['detail'])){
                    $("#invoiceType").prop("disabled", false);
                    $("#customerID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                }else {
                    $("#invoiceType").prop("disabled", true);
                    $("#customerID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                }
            },error : function(){
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
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_select_financial_period');?>'));/*Select Financial Period*/
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

    // function Invoices_detail_model(){
    //     $("#Invoices_model").modal({backdrop: "static"});
    // }

    function load_invoice_header() {
        if (invoiceAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'invoiceAutoID': invoiceAutoID},
                url: "<?php echo site_url('InvoicesPercentage/load_invoice_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        invoiceAutoID = data['invoiceAutoID'];
                        invoiceType = data['invoiceType'];
                        customerID = data['customerID'];
                        currencyID = data['transactionCurrencyID'];
                        $("#a_link").attr("href", "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>/" + invoiceAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice_buyback'); ?>/" + invoiceAutoID + '/HCINV');
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $("#paymentvouchercode").val(data['PVcode']);
                        $('#invoiceDate').val(data['invoiceDate']);
                        $('#customerInvoiceDate').val(data['customerInvoiceDate']);
                        $('#invoiceDueDate').val(data['invoiceDueDate']);
                        $('#invoiceNarration').val(data['invoiceNarration']);
                        $("#invoiceNote").wysihtml5();
                        $('#invoiceNote').val(data['invoiceNote']);
                        $('#invoiceType').val(data['invoiceType']).change();
                        $('#warehouseAutoIDtemp').val(data['warehouseAutoID']).change();
                        $('#referenceNo').val(data['referenceNo']);
                        $('#customerID').val(data['customerID']).change();
                        $('#salesPersonID').val(data['salesPersonID']).change();
                        $('#showTaxSummaryYN').val(data['showTaxSummaryYN']);
                        $('#contactPersonName').val(data['contactPersonName']);
                        $('#contactPersonNumber').val(data['contactPersonNumber']);
                        $('#RVbankCode').val(data['bankGLAutoID']);
                        fetch_detail(data['invoiceType'], data['customerID'], data['transactionCurrencyID']);
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#isPrintDN').val(data['isPrintDN']).change();
                        validatenarration();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                    //refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function save_draft() {
        if (invoiceAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to save this document*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"

                },
                function () {
                    fetchPage('system/invoices/invoices_management_buyback', invoiceAutoID, 'Invoices');
                });
        }
    }

    function attachment_modal_customerInvoice(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#customerInvoice_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                    $('#customerInvoice_attachment').empty();
                    $('#customerInvoice_attachment').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_customerInvoice_attachment(InvoiceAutoID, DocumentSystemCode,myFileName) {
        if (InvoiceAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>",/*You want to delete this attachment file*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>"/*Delete*/
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': InvoiceAutoID,'myFileName': myFileName},
                        url: "<?php echo site_url('Payable/delete_supplierInvoices_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            attachment_modal_customerInvoice(DocumentSystemCode, "Invoice", "HCINV");
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function Load_customer_currency(customerAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'customerAutoID': customerAutoID},
            url: "<?php echo site_url('Payable/fetch_customer_currency_by_id'); ?>",
            beforeSend: function () {
                $(':input[type="submit"]').prop('disabled', true);
            },
            success: function (data) {
                $(':input[type="submit"]').prop('disabled', false);
                $("#customerCreditPeriodhn").val(data['customerCreditPeriod']);
                change_invoice_due_date();
                if (currencyID) {
                    $("#transactionCurrencyID").val(currencyID).change()
                } else {
                    if (data.customerCurrencyID) {
                        $("#transactionCurrencyID").val(data.customerCurrencyID).change();
                        //currency_validation_modal(data.customerCurrencyID, 'BSI', customerAutoID, 'SUP');
                    }
                }
            }
        });
    }

    function changeInvoiceDueDateFlag(){
        changeInvoiceDueDate=1;
    }

    function change_invoice_due_date(){
        var startDate=$('#customerInvoiceDate').val();
        var period=$('#customerCreditPeriodhn').val();
        // var CurrentDate='';
        if(period>0 && changeInvoiceDueDate==0 && invoiceAutoID < 1){
            var endDateMoment = moment(startDate,"<?php echo strtoupper($date_format_policy)  ?>"); // moment(...) can also be used to parse dates in string format
            endDateMoment.add(period, 'months');
            var convertDate= moment(endDateMoment, "YYYY-MM-DD").format("<?php echo strtoupper($date_format_policy)  ?>");
            $('#invoiceDueDate').val(convertDate);
        }
    }

    function validatenarration(){
        var invoiceType = $('#invoiceType').val();
        if(invoiceType=='Direct'){
            $('.starmark').removeClass('hidden');
        }else{
            $('.starmark').addClass('hidden');
        }
    }

    function confirm_all_item_detail_modal(itemAutoIdArr){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            url: "<?php echo site_url('InvoicesPercentage/fetch_customer_invoice_all_detail_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                var descm = 2;
                $('#edit_item_table_body').empty();
                var x = 2;
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                } else {
                    $.each(data, function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" disabled  required')) ?>';
                        var wareHouseAutoID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ware_\'+key+\'"'), form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID"  onchange="checkitemavailable(this)" required')) ?>';
                        var taxfield = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="taxfield_\'+key+\'"'), form_dropdown('item_text[]', all_tax_drop(1), '', 'class="form-control item_text input-mini"  onchange="select_text_item(this)"')) ?>';

                        var string = '<tr><td> <input type="text" class="form-control search f_search" name="search[]" id="f_search_'+ x +'" placeholder="Item Id,Item Description" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]"> <input type="hidden" class="form-control invoiceDetailsAutoID" name="invoiceDetailsAutoID[]" value="' + value['invoiceDetailsAutoID'] + '"> </td> <td> '+ wareHouseAutoID +' </td> <td> '+ UOM +' </td> <td> <div class="input-group"> <input type="text" name="currentstock[]" value="' + value['currentStock'] + '" class="form-control currentstock" required disabled> </div> </td> <td><input type="text" onfocus="this.select();" name="quantityRequested[]" onkeyup="checkCurrentStock(this)" value="' + value['requestedQty'] + '" placeholder="0.00" class="form-control number input-mini quantityRequested" required></td> <td> <div class="input-group"> <input type="text" onfocus="this.select();" name="estimatedAmount[]" value="' + value['unittransactionAmount'] + '" onkeyup="change_amount(this)" onkeypress="return validateFloatKeyPress(this,event)" placeholder="0.00" class="form-control number estimatedAmount input-mini"> </div> </td> <td style="width: 100px;"> <div class="input-group"> <input type="text" name="discount[]" placeholder="0.00" onkeyup="cal_discount(this)" onfocus="this.select();" value="' + value['discountPercentage'] + '" class="form-control number discount"> <span class="input-group-addon">%</span></div> </td> <td style="width: 100px;"> <input type="text" name="discount_amount[]" placeholder="0.00" value="' + value['discountAmount'] + '" onkeyup="cal_discount_amount(this)" onfocus="this.select();" class="form-control number discount_amount"> </td> <td> '+ taxfield +' </td> <td style="width: 120px"> <div class="input-group"> <input type="text" name="item_taxPercentage[]" placeholder="0.00" onfocus="this.select();" class="form-control number item_taxPercentage input-mini" id="item_taxPercentage_all_'+ x +'" value="' + value['taxPercentage'] + '" value="0" readonly> <span class="input-group-addon input-group-addon-mini">%</span> </div> </td> <td> <textarea class="form-control input-mini" rows="1" name="remarks[]" placeholder="Item Remarks...">' + value['remarks'] + '</textarea> </td> <td class="remove-td"><a onclick="delete_customer_invoiceDetailsEdit(' + value['invoiceDetailsAutoID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';


                        $('#edit_item_table_body').append(string);
                        //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                        $('#ware_'+key).val(value['wareHouseAutoID']).change();
                        $('#taxfield_'+key).val(value['taxMasterAutoID']);
                        if (data['taxPercentage'] != 0) {
                            $('#item_taxPercentage_all_'+key).prop('readonly', false);
                        } else {
                            $('#item_taxPercentage_all_'+key).prop('readonly', true);
                        }
                        fetch_related_uom_id( value['defaultUOMID'],value['unitOfMeasureID'],$('#uom_'+key));
                        initializeitemTypeahead(x);
                        x++;
                    });
                    $('.select2').select2();
                    search_id=x-1;
                    $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                }
                $.each(itemAutoIdArr, function (key, valu) {
                    var concatval=valu['itemAutoID'] +'|'+valu['wareHouseAutoID'];
                    $('.itemAutoID').each(function () {
                        var thisconcat=this.value+'|'+$(this).closest('tr').find('.wareHouseAutoID').val();
                        if(concatval == thisconcat){
                            $(this).closest('tr').css("background-color",'#ffb2b2');
                        }
                    });
                });
                stopLoad();<!--Total-->

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function cal_deduction_netQty(element) {

        var grossQty = parseFloat($(element).closest('tr').find('.grossQty').val());
        var noOfUnits = parseFloat($(element).closest('tr').find('.noOfUnits').val());
        var deduction = parseFloat($(element).closest('tr').find('.deduction :selected').text());
        if (grossQty) {
            $(element).closest('tr').find('.quantityRequested').val((parseFloat(grossQty - (noOfUnits * deduction)).toFixed(2)));

        }
    }

    function cal_deduction_netQtyEdit() {
        var grossQty = parseFloat($('#edit_grossQty').val());
        var noofunits = parseFloat($('#edit_noOfUnits').val());
        var deduction = parseFloat($('#edit_deduction :selected').text());
        if (grossQty) {
            $('#edit_quantityRequested').val((parseFloat(grossQty - (noofunits * deduction)).toFixed(2)));

        }
    }

</script>