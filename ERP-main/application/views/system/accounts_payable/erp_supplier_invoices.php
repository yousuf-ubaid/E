<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();//array('' => 'Select Currency');
$financeyear_arr = all_financeyear_drop(true);
$segment_arr = fetch_segment(true);
$segment_arr_default = default_segment_drop(true);
$invoice_type_arr = array('' => $this->lang->line('common_select_type')/*'Select Type'*/, /*'Standard' => $this->lang->line('accounts_payable_direct')*//*'Direct'*//*,*/
    'GRV Base' => $this->lang->line('accounts_payable_grv_base') /*'GRV Base'*/,
    'StandardPO' => 'Direct PO',
    'StandardItem' => 'Direct Item',
    'StandardExpense' => 'Direct Expense');
$projectExist = project_is_exist();
$location_arr = all_delivery_location_drop();
$location_arr_default = default_delivery_location_drop();
$financeyearperiodYN = getPolicyValues('FPC', 'All');

$pID = $this->input->post('page_id');
$supplier_arr = all_supplier_drop(true, 1);
if ($pID != '') {
    $grvAutoid = $pID;
    $Documentid = 'BSI';
    $supplieridcurrentdoc = all_supplier_drop_isactive_inactive($pID, $Documentid);
    if ($supplieridcurrentdoc['isActive'] == 0) {
        $supplier_arr[trim($supplieridcurrentdoc['supplierAutoID'] ?? '')] = (trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') ? trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') . ' | ' : '') . trim($supplieridcurrentdoc['supplierName'] ?? '') . (trim($supplieridcurrentdoc['supplierCountry'] ?? '') ? ' | ' . trim($supplieridcurrentdoc['supplierCountry'] ?? '') : '');

    }
}

$createmasterrecords = getPolicyValues('CMR', 'All');
$supplier_arr_master = array('' => 'Select Supplier');
$country = load_country_drop();
$gl_code_arr = supplier_gl_drop();
$currncy_arr = all_currency_new_drop();
$country_arr = array('' => $this->lang->line('common_select_country'));/*Select Country*/
$customerCategory = party_category(2);
$taxGroup_arr = supplier_tax_groupMaster();
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}

$invoicedReceivedDatePlolicy = getPolicyValues('IRDt', 'All');

?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_one'); ?>
        - <?php echo $this->lang->line('accounts_payable_invoice_header'); ?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_details();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_two'); ?>
        - <?php echo $this->lang->line('accounts_payable_invoice_detail'); ?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('accounts_payable_step_three'); ?>
        - <?php echo $this->lang->line('accounts_payable_invoice_confirmation'); ?> </span>
            </a>
        </div>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="invoice_form"'); ?>

        <input type="hidden" id="supplierCreditPeriodhn" name="supplierCreditPeriodhn">
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="invoiceType">
                    <?php echo $this->lang->line('accounts_payable_invoice_invoice_type'); ?><!--Invoice Type--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('invoiceType', $invoice_type_arr, '', 'class="form-control select2" id="invoiceType" onchange="" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="">
                    <?php echo $this->lang->line('common_supplier_document_date'); ?><!--Document Date(or Invoice Date)--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="bookingDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="bookingDate" class="form-control docdt"  required>
                </div>
            </div>
            <?php if($invoicedReceivedDatePlolicy == 1){ ?>
            <div class="form-group col-sm-2">
                <label for="">
                    Invoice Received Date<?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="invoiceReceivedDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="invoiceReceivedDate" class="form-control docdt"  required>
                </div>
            </div>
            <?php } ?>
            <div class="form-group col-sm-2">
                <label for="">
                    <?php echo $this->lang->line('accounts_payable_reference_no'); ?><!--Reference No--></label>
                <input type="text" class="form-control " id="referenceno" name="referenceno">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <?php if ($createmasterrecords == 1) { ?>
                    <label for="supplierID">
                        <?php echo $this->lang->line('common_supplier'); ?><!--Supplier--> <?php required_mark(); ?></label>
                    <div class="input-group">
                        <div id="div_supplier_drop">

                            <?php echo form_dropdown('supplierID', $supplier_arr_master, '', 'class="form-control select2" id="supplierID" onchange="fetch_supplier_currency_by_id(this.value); "'); ?>
                        </div>
                        <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Supplier" rel="tooltip"
                                onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>

                <?php } else { ?>
                    <label for="supplierID">
                        <?php echo $this->lang->line('common_supplier'); ?><!--Supplier--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('supplierID', $supplier_arr, '', 'class="form-control select2" id="supplierID" onchange="fetch_supplier_currency_by_id(this.value)"'); ?>
                <?php } ?>


            </div>
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID">
                    <?php echo $this->lang->line('accounts_payable_supplier_currency'); ?><!--Supplier Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'BSI\')" required'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="">
                    <?php echo $this->lang->line('accounts_payable_supplier_invoice_number'); ?><!--Supplier Invoice No--></label>
                <input type="text" class="form-control " id="supplier_invoice_no" name="supplier_invoice_no">
            </div>
            <div class="form-group col-sm-2">
                <label for="">
                    <?php echo $this->lang->line('accounts_payable_supplier_invoice_date'); ?><!--Supplier Invoice Date--> <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="invoiceDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="invoiceDate" class="form-control invdat" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="">
                    <?php echo $this->lang->line('accounts_payable_supplier_invoice_due_date'); ?><!--Supplier Invoice Due Date--> <?php required_mark(); ?></label>
                <div class="input-group datepicinvduedat">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="supplierInvoiceDueDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="supplierInvoiceDueDate"
                           class="form-control " required>
                </div>
            </div>
            <?php if ($financeyearperiodYN == 1) { ?>
                <div class="form-group col-sm-4">
                    <label for="financeyear"><?php echo $this->lang->line('accounts_payable_financial_year'); ?><?php required_mark(); ?></label>
                    <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="financeyear">
                        <?php echo $this->lang->line('accounts_payable_financial_period'); ?><!--Financial Period--> <?php required_mark(); //?></label>
                    <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
                </div>
            <?php } ?>
        </div>
        <div class="row">
                <div class="form-group col-sm-4 hide" id="primaryPoDiv">
                    <label for="financeyear"><?php echo $this->lang->line('accounts_payable_select_po'); ?><?php required_mark(); ?></label>
                    <?php echo form_dropdown('priamryPo', array(''=>'Select PO'),'', 'class="form-control" id="primaryPo" onchange="get_selected_po_details(this)"'); ?>
                </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></label>
                <!--Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default, 'class="form-control select2" id="segment"'); ?>
            </div>

            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_retension'); ?><?php required_mark(); ?></label>
                <!--Retension-->
                <input type="text" name="retensionPercentage" id="retensionPercentage" class="form-control" value="" />
            </div>

            <div class="form-group col-sm-4">
                    <label for="segment"><?php echo 'Retension GL' ?><?php required_mark(); ?></label>
                    <!--Retension GL-->
                    <?php echo form_dropdown('retensionGL', fetch_by_gl_codes(), '', 'class="form-control select2" id="retensionGL"'); ?>
            </div>

        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_narration'); ?><!--Narration--> </label>
                <textarea class="form-control" rows="2" id="comments" name="comments"></textarea>
            </div>

            <div class="col-sm-4 reverse-charge-mechanism hide">
                <div class="form-group ">
                    <label for="narration">Reverse Charge Mechanism</label>
                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="rcmYN" type="checkbox"
                                   data-caption="" class="columnSelected" name="rcmYN" value="1">
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary size-lg" type="submit">
                <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">

    </div>
    <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="supplierInvoice_attachment_label">
                <?php echo $this->lang->line('accounts_payable_modal_title'); ?><!--Modal title--></h4>
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="supplierInvoice_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev">
                <?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()">
                <?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">
                <?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="emp_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Add New Supplier</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'role="form" id="suppliermaster_form"'); ?>
                <input type="hidden" id="isActive" name="isActive" value="1">
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Secondary Code<!--Secondary Code--> <?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="suppliercode" name="suppliercode">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierName">Company Name / Name
                                <!--Company Name / Name--> <?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="supplierName" name="supplierName" required>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierName">Name On Cheque
                                <!--Name On Cheque--> <?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="nameOnCheque" name="nameOnCheque">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for=""><?php echo $this->lang->line('common_category'); ?><!--Category--></label>
                            <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="liabilityAccount">Liability Account
                                <!--Liability Account--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('liabilityAccount', $gl_code_arr, $this->common_data['controlaccounts']['APA'], 'class="form-control select2" id="liabilityAccount" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierCurrency">Currency<!--Currency--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('supplierCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="supplierCurrency" required'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">
                                <?php echo $this->lang->line('common_Country'); ?><!--Country--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('suppliercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="suppliercountry" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">Tax Group<!--Tax Group--></label>
                            <?php echo form_dropdown('suppliertaxgroup', $taxGroup_arr, '', 'class="form-control select2"  id="suppliertaxgroup"'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">VAT Identification No</label>
                            <input type="text" class="form-control" id="vatIdNo" name="vatIdNo">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="supplierTelephone">
                                <?php echo $this->lang->line('common_telephone'); ?><!--Telephone--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierTelephone" name="supplierTelephone">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierEmail">
                                <?php echo $this->lang->line('common_email'); ?><!--Email--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierEmail" name="supplierEmail">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierFax">FAX<!--FAX--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierFax" name="supplierFax">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="suppliersupplierCreditPeriod">Credit Period<!--Credit Period--></label>
                            <div class="input-group">
                                <div class="input-group-addon">Month<!--Month--></div>
                                <input type="text" class="form-control number" id="supplierCreditPeriod"
                                       name="supplierCreditPeriod">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="suppliersupplierCreditLimit">Credit Limit<!--Credit Limit--></label>
                            <div class="input-group">
                                <div class="input-group-addon"><span class="currency">LKR</span></div>
                                <input type="text" class="form-control number" id="supplierCreditLimit"
                                       name="supplierCreditLimit">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierUrl">URL</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="supplierUrl" name="supplierUrl">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="supplierAddress1">Address 1<!--Address 1--></label>
                            <textarea class="form-control" rows="2" id="supplierAddress1"
                                      name="supplierAddress1"></textarea>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="supplierAddress2">Address 2<!--Address 2--></label>
                            <textarea class="form-control" rows="2" id="supplierAddress2"
                                      name="supplierAddress2"></textarea>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close
                </button>
                <button type="button" class="btn btn-primary"
                        onclick="save_supplier_master()">Add Supplier
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- <script src="<?php //echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
 -->
<script type="text/javascript">
    var InvoiceAutoID;
    var documentCurrency;
    var currency_decimal = 2;
    var changeInvoiceDueDate = 0;
    var rcmApplicableYN = 0;
    var isGrpApplicable = '<?php echo getPolicyValues('GBT', 'All') ?>';
    var supplier_po_credit_period = 0;
    var purchaseOrderID = 0;

    $(document).ready(function () {

        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('.headerclose').click(function () {
            fetchPage('system/accounts_payable/supplier_invoices_management', InvoiceAutoID, 'Supplier Invoices');
        });
        $('.select2').select2();
        InvoiceAutoID = null;
        documentCurrency = null;

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        });
        $('.datepicinvduedat').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });

        $('.datepic').on('dp.change', function (e) {
           // change_invoice_due_date();
            calculate_invoice_due_date();
        });
        $('.datepicinvduedat').on('dp.change', function (e) {
            changeInvoiceDueDateFlag();
        });

        Inputmask({
            alias: date_format_policy, "oncomplete": function (e) {
              //  change_invoice_due_date();
                calculate_invoice_due_date();
            }
        }).mask(document.querySelectorAll('.invdat'));
        Inputmask({
            alias: date_format_policy, "oncomplete": function (e) {
                changeInvoiceDueDateFlag();
            }
        }).mask(document.querySelectorAll('.invduedat'));
        Inputmask({alias: date_format_policy,}).mask(document.querySelectorAll('.docdt'));

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            InvoiceAutoID = p_id;
            laad_supplier_invoice_header();
            <?php if($createmasterrecords == 1){?>
            fetch_supplierdrop('', InvoiceAutoID)
            <?php }?>

            if(isGrpApplicable == 1 ) {

                $('.reverse-charge-mechanism').addClass('hide');
            }else {

                $('.reverse-charge-mechanism').addClass('hide');
            }

            $("#a_link").attr("href", "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>/" + InvoiceAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_supplier_invoices'); ?>/" + InvoiceAutoID + '/BSI');
            $('.btn-wizard').removeClass('disabled');
        } else {

            <?php if($createmasterrecords == 1){?>
            fetch_supplierdrop();
            <?php }?>
            $('.btn-wizard').addClass('disabled');

            if(isGrpApplicable == 1 ) {

                $('.reverse-charge-mechanism').addClass('hide');
            }else {

                $('.reverse-charge-mechanism').addClass('hide');
            }
        }

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        $('#invoice_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                invoiceType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_invoice_type_is_required');?>.'}}},/*Invoice Type is required*/
                supplierID : {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_supplier_is_required');?>.'}}},/*Supplier is required*/
                invoiceDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_invoice_date_is_required');?>.'}}},/*Invoice Date is required*/
                bookingDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_date_is_required');?>.'}}},/*Document Date is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_supplier_currency_is_required');?>.'}}},/*Supplier Currency is required*/
                supplierInvoiceDueDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_supplier_invoice_due_date_is_required');?>.'}}},/*Supplier Invoice Due Date is required*/
                //comments: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('accounts_payable_comments_are_required');?>.'}}}/*Comments are required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#invoiceType").prop("disabled", false);
            $("#supplierID").prop("disabled", false);
            $("#rcmYN").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'InvoiceAutoID', 'value': InvoiceAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payable/save_supplier_invoice_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        InvoiceAutoID = data['last_id'];
                        $("#a_link").attr("href", "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>/" + InvoiceAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_supplier_invoices'); ?>/" + InvoiceAutoID + '/BSI');
                        laad_supplier_invoice_header();
                        //fetch_supplier_invoice_detail();
                        $('[href=#step2]').tab('show');

                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

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
    });


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
            data: {'companyFinanceYearID': companyFinanceYearID,'documentID':'BSI'},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('accounts_payable_select_financial_period');?>'));/*Select  Financial Period*/
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

    function laad_supplier_invoice_header() {
        if (InvoiceAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'InvoiceAutoID': InvoiceAutoID},
                url: "<?php echo site_url('Payable/laad_supplier_invoice_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        purchaseOrderID = data['purchaseOrderIDMaster'];
                        $('.currency').html('( ' + data['transactionCurrency'] + ' )');
                        $('#invoiceType').val(data['invoiceType']).change();
                        $('#invoiceType').val(data['typedrop']).change();
                        $('#bookingDate').val(data['bookingDate']);
                        <?php if($invoicedReceivedDatePlolicy == 1){ ?>
                            $('#invoiceReceivedDate').val(data['InvoiceReceivedDate']);
                        <?php } ?>
                        $('#supplierInvoiceDueDate').val(data['invoiceDueDate']);
                        documentCurrency = data['transactionCurrencyID'];
                        currency_decimal = data['transactionCurrencyDecimalPlaces'];
                        setTimeout(function () {
                            $("#supplierID").val(data['supplierID']).change();
                        }, 500);
                        $('#supplierInvoiceDate').val(data['supplierInvoiceDate']);
                        $('#comments').val(data['comments']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        $('#referenceno').val(data['RefNo']);
                        $('#supplier_invoice_no').val(data['supplierInvoiceNo']);
                        $('#segment').val(data['segmentID']).change();
                        $('#location').val(data['warehouseAutoID']).change();
                        $('#invoiceDate').val(data['invoiceDate']);

                        $('#retensionPercentage').val(data['retensionPercentage']);
                        $('#retensionGL').val(data['retensionGL']).change();
                
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        //fetch_supplier_currency_by_id(data['supplierID']);
                        fetch_supplier_invoice_detail(data['invoiceType'], data['supplierID'], data['transactionCurrencyID']);

                        if (data['invoiceType'] == 'GRV Base' || data['invoiceType'] == 'StandardPO') {

                            setTimeout(function () {
                                $('#rcmYN').iCheck('uncheck');
                            });

                            $('.reverse-charge-mechanism').addClass('hide');
                            rcmApplicableYN = 0;

                        } else {
                            fetch_rcm_enableYN(data['supplierID']);
                            rcmApplicableYN = data['rcmApplicableYN'];
                        }
                        isGrpApplicable = data['isGroupBasedTax'];
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

    function fetch_taxation() {

    }

    function fetch_details() {
        fetch_supplier_invoice_detail(invoiceType, supplierID, currencyID);
        //laad_grv_header();
    }

    function fetch_supplier_invoice_detail(type, supplierID, currencyID, tabid = 1) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'InvoiceAutoID': InvoiceAutoID,
                'invoiceType': type,
                'supplierID': supplierID,
                'currencyID': currencyID
            },
            url: "<?php echo site_url('Payable/fetch_supplier_invoice_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#step2').html(data);
                check_detail_dataExist(InvoiceAutoID);
                if (type == 'Standard' || type == 'StandardItem' || type == 'StandardExpense') {
                    $('.itmtb').removeClass('active');
                    $('#tab_' + tabid).addClass('active');
                    $('#tabli_' + tabid).addClass('active');
                } else if (type == 'StandardPO') {
                    $('.itmtb').removeClass('active');
                    $('#tab_' + 3).addClass('active');
                    $('#tabli_' + 3).addClass('active');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function check_detail_dataExist(InvoiceAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'InvoiceAutoID': InvoiceAutoID},
            url: "<?php echo site_url('Payable/fetch_detail_header_lock'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (jQuery.isEmptyObject(data['detail']) && jQuery.isEmptyObject(data['ItemDetail']) && jQuery.isEmptyObject(data['poDetail'])) {
                    $("#invoiceType").prop("disabled", false);
                    $("#supplierID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                    $("#addcustomer").prop("disabled", false);
                    $("#rcmYN").prop("disabled", false);
                } else {
                    $("#invoiceType").prop("disabled", true);
                    $("#supplierID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#addcustomer").prop("disabled", true);
                    $("#rcmYN").prop("disabled", true);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_conformation() {
        if (InvoiceAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'InvoiceAutoID': InvoiceAutoID, 'html': true},
                url: "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>/" + InvoiceAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_supplier_invoices'); ?>/" + InvoiceAutoID + '/BSI');
                    attachment_modal_supplierInvoice(InvoiceAutoID, "<?php echo $this->lang->line('accounts_payable_trans_supplier_invoice');?> ", "BSI");/*Supplier Invoice*/
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

    function confirmation() {
        if (InvoiceAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document!*/
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
                        data: {'InvoiceAutoID': InvoiceAutoID},
                        url: "<?php echo site_url('Payable/supplier_invoice_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            $('#wac_minus_calculation_validation_body').empty();
                            if (data['error'] == 4) {


                                x = 1;
                                if (jQuery.isEmptyObject(data['message'])) {
                                    $('#wac_minus_calculation_validation_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                                } else {
                                    $.each(data['message'], function (key, value) {

                                        $('#wac_minus_calculation_validation_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemName'] + '</td><td>' + value['Amount'] + '</td></tr>');
                                        x++;
                                    });
                                }
                                $('#wac_minus_calculation_validation').modal('show');
                            } else {
                                if (data) {
                                    fetchPage('system/accounts_payable/supplier_invoices_management', InvoiceAutoID, 'Supplier Invoices');
                                }

                            }


                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (InvoiceAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to save this Document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/accounts_payable/supplier_invoices_management', InvoiceAutoID, 'Supplier Invoice');
                });
        }
    }

    function attachment_modal_supplierInvoice(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': 0},
                success: function (data) {
                    $('#supplierInvoice_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "<?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                    $('#supplierInvoice_attachment').empty();
                    $('#supplierInvoice_attachment').append('' + data + '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_supplierInvoice_attachment(InvoiceAutoID, DocumentSystemCode, myFileName) {
        if (InvoiceAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>!",/*You want to delete this attachment file*/
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
                        data: {'attachmentID': InvoiceAutoID, 'myFileName': myFileName},
                        url: "<?php echo site_url('Payable/delete_supplierInvoices_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                attachment_modal_supplierInvoice(DocumentSystemCode, "Supplier Invoice", "BSI");
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function changeInvoiceDueDateFlag() {
        changeInvoiceDueDate = 1;
    }

    function change_invoice_due_date() {
        var startDate = $('#invoiceDate').val();
        var period = $('#supplierCreditPeriodhn').val();
        // var CurrentDate='';
        if (period > 0 && changeInvoiceDueDate == 0 && InvoiceAutoID < 1) {
            var endDateMoment = moment(startDate, "<?php echo strtoupper($date_format_policy)  ?>"); // moment(...) can also be used to parse dates in string format
            endDateMoment.add(period, 'months');
            var convertDate = moment(endDateMoment, "YYYY-MM-DD").format("<?php echo strtoupper($date_format_policy)  ?>");
            $('#supplierInvoiceDueDate').val(convertDate);
        }
    }

    function link_employee_model() {
        $('#suppliercode').val('');
        $('#supplierName').val('');
        $('#vatIdNo').val('');
        $('#supplierTelephone').val('');
        $('#nameOnCheque').val('');
        $('#supplierEmail').val('');
        $('#supplierFax').val('');
        $('#supplierCreditPeriod').val('');
        $('#supplierCreditLimit').val('');
        $('#supplierUrl').val('');
        $('#supplierAddress2').val('');
        $('#supplierAddress1').val('');
        $('#partyCategoryID').val(null).trigger('change');
        $('#liabilityAccount').val('<?php echo $this->common_data['controlaccounts']['APA']?>').change();
        $('#supplierCurrency').val('<?php echo $this->common_data['company_data']['company_default_currencyID']?>').change();
        $('#suppliercountry').val('<?php echo $this->common_data['company_data']['company_country']?>').change();
        $('#suppliertaxgroup').val(null).trigger('change');
        $('#emp_model').modal('show');

    }

    function changecreditlimitcurr() {
        var currncy;
        var split;
        currncy = $('#supplierCurrency option:selected').text();
        split = currncy.split("|");
        $('.currency').html(split[0]);
        CurrencyID = $('#supplierCurrency').val();
        currency_validation_modal(CurrencyID, 'SUP', '', 'SUP');
    }

    function save_supplier_master() {
        var data = $('#suppliermaster_form').serializeArray();
        data.push({'name': 'currency_code', 'value': $('#supplierCurrency option:selected').text()});
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Supplier/save_suppliermaster'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0]=='s'){
                    $('#emp_model').modal('hide');
                    fetch_supplierdrop(data[2], ' ');
                    fetch_supplier_currency_by_id(data[2]);
                }else{
                    $('#emp_model').modal('show');

                }
                // if (data['status'] == true) {
                //     $('#emp_model').modal('hide');
                //     fetch_supplierdrop(data['last_id'], ' ');
                //     fetch_supplier_currency_by_id(data['last_id']);
                //
                // } else if (data['status'] == false) {
                //     $('#emp_model').modal('show');
                //
                // }

            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                $('#emp_model').modal('show');
                refreshNotifications(true);
            }
        });


    }

    function fetch_supplierdrop(id, purchaseOrderID) {
        var supplier_id;
        var page = '';

        if (id) {
            supplier_id = id
        } else {
            supplier_id = '';
        }
        if (purchaseOrderID) {
            page = purchaseOrderID
        }

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {supplier: supplier_id, DocID: page, Documentid: 'BSI'},
            url: "<?php echo site_url('Procurement/fetch_supplier_Dropdown_all_grv'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_supplier_drop').html(data);
                stopLoad();
                $('.select2').select2();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_supplier_currency_by_id(supplierAutoID, select_value) {

        var invoiceType = $('#invoiceType').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'supplierAutoID': supplierAutoID},
            url: "<?php echo site_url('Procurement/fetch_supplier_currency_by_id'); ?>",
            success: function (data) {
                $("#supplierCreditPeriodhn").val(data['supplierCreditPeriod']);
                // change_invoice_due_date();
                calculate_invoice_due_date();
                if (documentCurrency) {
                    $("#transactionCurrencyID").val(documentCurrency).change()
                } else {
                    if (data.supplierCurrencyID) {
                        $("#transactionCurrencyID").val(data.supplierCurrencyID).change();
                        currency_validation_modal(data.supplierCurrencyID, 'BSI', supplierAutoID, 'SUP');
                    }
                }
            }
        });

        if (invoiceType != 'GRV Base' && invoiceType != 'StandardPO') {

            fetch_rcm_enableYN(supplierAutoID)
        } else {

            setTimeout(function () {
                $('#rcmYN').iCheck('uncheck');
            });

            $('.reverse-charge-mechanism').addClass('hide');

            load_invoice_po_list();

        }

        

    }

    function fetch_rcm_enableYN(supplierID) {
        if (supplierID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'supplierID': supplierID},
                url: "<?php echo site_url('Procurement/fetch_rcmDetails'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#rcmApplicable').val(data['isEligibleRCM']);
                    if(isGrpApplicable == 1 ){
                        $('.reverse-charge-mechanism').removeClass('hide');
                    }else {
                        $('.reverse-charge-mechanism').addClass('hide');
                    }


                    setTimeout(function () {
                        $('#rcmYN').iCheck('uncheck');
                    });

                    if (rcmApplicableYN == 1) {
                        setTimeout(function () {
                            $('#rcmYN').iCheck('check');
                        });
                    }

                    if (data['isEligibleRCM'] == 1 && InvoiceAutoID == null) {
                        setTimeout(function () {
                            $('#rcmYN').iCheck('check');
                        });
                    }


                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }

    }

    $("#invoiceType").change(function () {
        if ($(this).val() == 'GRV Base' || $(this).val() == 'StandardPO') {
            setTimeout(function () {
                $('#rcmYN').iCheck('uncheck');
            });
            $('.reverse-charge-mechanism').addClass('hide');
        } else {
            fetch_rcm_enableYN($('#supplierID').val());

        }

        if($(this).val()== 'StandardPO'){
            $('#primaryPoDiv').removeClass('hide');
        }else{
            $('#primaryPoDiv').addClass('hide');
        }
    });

   
    function load_invoice_po_list(){

        var supplierID = $('#supplierID').val();
        var invoiceType = $("#invoiceType").val();

        if(supplierID && invoiceType == 'StandardPO'){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'supplierID': supplierID},
                url: "<?php echo site_url('Payable/fetch_supplier_po_list'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#primaryPo').empty();
                    var mySelect = $('#primaryPo');
                    mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('accounts_payable_select_po');?>'));/*Select  Financial Period*/
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['purchaseOrderID']).html(text['purchaseOrderCode'] + ' - ' + text['referenceNumber']));
                        });

                        if(purchaseOrderID){
                            $('#primaryPo').val(purchaseOrderID).change();
                        }
                       
                    }


                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });

        }
    }

    function get_selected_po_details(ev){

        var purchaseOrderID = $(ev).val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': purchaseOrderID},
            url: "<?php echo site_url('Payable/fetch_po_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                
                $('#referenceno').val(data['referenceNumber']);
                $('#comments').val(data['narration']);

                var set_segment = data['segmentID'];
                $('#segment').val(set_segment).trigger('change');

                purchaseOrderID = data['purchaseOrderID'];
                supplier_po_credit_period = data['creditPeriod'];
                calculate_invoice_due_date(data['creditPeriod']);

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });

    }

    function calculate_invoice_due_date(period = null){

        var startDate=$('#bookingDate').val();
        var invoiceType = $("#invoiceType").val();

        if(invoiceType != 'StandardPO'){
            change_invoice_due_date();
        }
       
        if(period == null){
            period = supplier_po_credit_period;
        }

        var endDateMoment = moment(startDate,"<?php echo strtoupper($date_format_policy)  ?>"); // moment(...) can also be used to parse dates in string format
        endDateMoment.add(period, 'days');
        var convertDate= moment(endDateMoment, "DD-MM-YYYY").format("<?php echo strtoupper($date_format_policy)  ?>");
        $('#supplierInvoiceDueDate').val(convertDate);

        
    }


</script>