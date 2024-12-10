<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_create_sales_return');
echo head_page($title, false);
/*echo head_page('Create Sales Return', false);*/
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop_active();
$financeyear_arr = all_financeyear_drop(true);
//$customer_arr = all_customer_drop();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$country        = load_country_drop();
$gl_code_arr    = supplier_gl_drop();
$currncy_arr    = all_currency_new_drop();
$country_arr    = array('' => 'Select Country');
$taxGroup_arr    = customer_tax_groupMaster();
$customerCategory    = party_category(1);
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
$createmasterrecords = getPolicyValues('CMR','All');
$customer_arr_masterlevel = array('' => 'Select Customer');
$location_arr_default = default_delivery_location_drop();
$pID = $this->input->post('page_id');
$customer_arr = all_customer_drop(true,1);
if($pID != '') {
    $Documentid = 'SLR';
    $customeridcurrentdoc = all_customer_drop_isactive_inactive($pID,$Documentid);
    if(!empty($customeridcurrentdoc) && $customeridcurrentdoc['isActive'] == 0)
    {
        $customer_arr[trim($customeridcurrentdoc['customerAutoID'] ?? '')] = (trim($customeridcurrentdoc['customerSystemCode'] ?? '') ? trim($customeridcurrentdoc['customerSystemCode'] ?? '') . ' | ' : '') . trim($customeridcurrentdoc['customerName'] ?? '') . (trim($customeridcurrentdoc['customerCountry'] ?? '') ? ' | ' . trim($customeridcurrentdoc['customerCountry'] ?? '') : '');
    }

    $warehouseidcurrentdoc = all_warehouse_drop_isactive_inactive($pID,$Documentid);
    if(!empty($warehouseidcurrentdoc) && $warehouseidcurrentdoc['isActive'] == 0)
    {
        $location_arr[trim($warehouseidcurrentdoc['wareHouseAutoID'] ?? '')] = trim($warehouseidcurrentdoc['wareHouseCode'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseLocation'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseDescription'] ?? '');
    }
}
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

    <div class="steps">
        <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_one'); ?>
        - <?php echo $this->lang->line('sales_markating_transaction_sales_return_header'); ?></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_detail()" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_two'); ?>
        - <?php echo $this->lang->line('sales_markating_transaction_sales_return_detail'); ?> </span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_three'); ?>
        - <?php echo $this->lang->line('sales_markating_transaction_sales_return_confirmation'); ?></span>
        </a>
    </div>

   
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="sales_return_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_sales_return_date'); ?><?php required_mark(); ?></label>
                <!--Return Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="returnDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="returnDate"
                           class="form-control" required>
                </div>
            </div>
            <?php
            if($financeyearperiodYN==1){
            ?>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year'); ?><?php required_mark(); ?></label>
                <!--Financial Year-->
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear_period"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period'); ?><?php required_mark(); ?></label>
                <!--Financial Period-->
                <?php echo form_dropdown('financeyear_period', array('' => 'Finance Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
                <?php
            }
            ?>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_sales_reference_no'); ?> </label>
                <!--Reference No-->
                <input type="text" class="form-control " id="referenceNo" name="referenceNo">
            </div>


            <?php if($createmasterrecords==1){?>
                <div class="form-group col-sm-4">
                    <label for="customerName"><?php echo $this->lang->line('common_customer_name');?><?php  required_mark(); ?></label><!--Customer Name-->
                    <div class="input-group">
                        <div id="div_customer_drop">
                            <?php echo form_dropdown('customerID', $customer_arr_masterlevel, '', 'class="form-control select2" id="customerID"  onchange="fetch_supplier_currency_by_id(this.value)"'); ?>
                        </div>
                        <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Customer" rel="tooltip" onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                </div>
            <?php } else { ?>
                <div class="form-group col-sm-4">
                    <label for="customerID"><?php echo $this->lang->line('common_customer_name'); ?><?php required_mark(); ?></label>
                    <!--Customer-->
                    <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="customerID" onchange="fetch_supplier_currency_by_id(this.value)"'); ?>
                </div>

            <?php }?>



            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('common_currency'); ?><?php required_mark(); ?></label>
                <!--Currency-->
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation_modal(this.value,\'SLR\',\'\',\'\')" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_sales_warehouse_location'); ?><?php required_mark(); ?></label>
                <!--Warehouse Location-->
                <?php echo form_dropdown('location', $location_arr, $location_arr_default , 'class="form-control select2" id="location" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_narration'); ?></label>
                <!--Narration-->
                <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg"
                    type="submit"><?php echo $this->lang->line('common_save_and_next'); ?> </button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i
                            class="fa fa-hand-o-right"></i><?php echo $this->lang->line('sales_markating_transaction_sales_add_item_detail'); ?>
                </h4></div><!--Add Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_document_add_item'); ?>
                </button><!--Add Item-->
            </div>
        </div>
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th colspan="4"><?php echo $this->lang->line('sales_markating_transaction_document_item_details'); ?> </th>
                <!--Item Details-->
                <th colspan="2"><?php echo $this->lang->line('sales_markating_transaction_document_qty'); ?> </th>
                <!--Qty-->
                <th>&nbsp;</th>
            </tr>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_item_code'); ?>  </th>
                <!--Item Code-->
                <th style="min-width: 30%"><?php echo $this->lang->line('sales_markating_transaction_secondary_item_description'); ?> </th>
                <!--Item Description-->
                <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_document_uom'); ?></th>
                <!--UOM-->
                <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_return'); ?></th>
                <!--return-->
                <th style="min-width: 15%"><?php echo $this->lang->line('sales_markating_transaction_recived'); ?></th>
                <!--Received-->
                <th style="min-width: 10%">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="item_table_body">
            <tr class="danger">
                <td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?> </b>
                </td><!--No Records Found-->
            </tr>
            </tbody>
        </table>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick=""><?php echo $this->lang->line('common_previous'); ?></button>
            <!--Previous-->
            <!-- <button class="btn btn-primary next" onclick="load_conformation();" >Save & Next</button> -->
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <!--    <div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank" href="<?php /*echo site_url('Double_entry/fetch_double_entry_sales_return/'); */ ?>"><span class="glyphicon glyphicon-random" aria-hidden="true"></span>  &nbsp;&nbsp;&nbsp;Account Review entries
                </a>
                <a class="btn btn-default btn-sm" id="a_link" target="_blank" href="<?php /*echo site_url('Inventory/load_sales_return_conformation/'); */ ?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div><hr>-->
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title"
                id="purchaseReturn_attachment_label"><?php echo $this->lang->line('sales_markating_transaction_model_title'); ?> </h4>
            <!--Modal title-->
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?> </th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description'); ?> </th><!--Description-->
                        <th><?php echo $this->lang->line('common_type'); ?></th><!--Type-->
                        <th><?php echo $this->lang->line('common_action'); ?> </th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="purchaseReturn_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5"
                            class="text-center"><?php echo $this->lang->line('common_no_attachment_found'); ?> </td>
                        <!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <!--<button class="btn btn-default prev">Previous XX</button>-->
            <button class="btn btn-primary "
                    onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?></button>
            <!--Save as Draft-->
            <button class="btn btn-success submitWizard"
                    onclick="confirmation()"><?php echo $this->lang->line('common_confirm'); ?></button><!--Confirm-->
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
                    id="exampleModalLabel">Add New Customer</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <?php echo form_open('','role="form" id="customermaster_form"'); ?>
                <input type="hidden" id="isActive" name="isActive" value="1">
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Customer Secondary Code <?php  required_mark(); ?></label><!--Customer Secondary Code-->
                            <input type="text" class="form-control" id="customercode" name="customercode">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerName">Customer Name<?php  required_mark(); ?></label><!--Customer Name-->
                            <input type="text" class="form-control" id="customerName" name="customerName" required>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">Category</label><!--Category-->
                            <?php  echo form_dropdown('partyCategoryID', $customerCategory, '','class="form-control select2"  id="partyCategoryID"'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="receivableAccount">Receivable Account <?php  required_mark(); ?></label><!--Receivable Account-->
                            <?php  echo form_dropdown('receivableAccount', $gl_code_arr,$this->common_data['controlaccounts']['ARA'],'class="form-control select2" id="receivableAccount" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerCurrency">Customer Currency<?php  required_mark(); ?></label><!--Customer Currency-->
                            <?php  echo form_dropdown('customerCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'] ,'class="form-control select2" onchange="changecreditlimitcurr()" id="customerCurrency" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">Customer Country<?php  required_mark(); ?></label><!--Customer Country-->
                            <?php  echo form_dropdown('customercountry', $country_arr, $this->common_data['company_data']['company_country'] ,'class="form-control select2"  id="customercountry" required'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Tax Group </label><!--Tax Group-->
                            <?php  echo form_dropdown('customertaxgroup', $taxGroup_arr, '','class="form-control select2"  id="customertaxgroup"'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">VAT Identification No</label>
                            <input type="text" class="form-control" id="vatIdNo" name="vatIdNo">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">ID card number </label>
                            <input type="text" class="form-control" id="IdCardNumber" name="IdCardNumber">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="customerTelephone"><?php echo $this->lang->line('common_telephone');?></label><!--Telephone-->
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="customerTelephone" name="customerTelephone">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerEmail"><?php echo $this->lang->line('common_email');?></label><!--Email-->
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="customerEmail" name="customerEmail">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerFax"><?php echo $this->lang->line('common_fax');?></label><!--Fax-->
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="customerFax" name="customerFax" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="customercustomerCreditPeriod">Credit Period</label><!--Credit Period-->
                            <div class="input-group">
                                <div class="input-group-addon"><?php echo $this->lang->line('common_month');?> </div><!--Month-->
                                <input type="text" class="form-control number" id="customerCreditPeriod" name="customerCreditPeriod">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customercustomerCreditLimit">Credit Limit</label><!--Credit Limit-->
                            <div class="input-group">
                                <div class="input-group-addon"><span class="currency">LKR</span></div>
                                <input type="text" class="form-control number" id="customerCreditLimit" name="customerCreditLimit">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerUrl">URL</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-link" aria-hidden="true"></i></div>
                                <input type="text" class="form-control" id="customerUrl" name="customerUrl">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="customerAddress1">Primary Address</label><!--Primary Address-->
                            <textarea class="form-control" rows="2" id="customerAddress1" name="customerAddress1"></textarea>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="customerAddress2">Secondary Address</label><!--Secondary Address-->
                            <textarea class="form-control" rows="2" id="customerAddress2" name="customerAddress2"></textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary"
                        onclick="save_customer_master()">Add Customer </button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var salesReturnAutoID;
    var salesreturnDetailsID;
    var documentCurrency;
    $(document).ready(function () {
        
        $('.headerclose').click(function () {
            fetchPage('system/invoices/sales_return', '', 'Sales Return ')
        });
        $('.select2').select2();
        number_validation();
        salesReturnAutoID = null;
        salesreturnDetailsID = null;
        documentCurrency = null;
        //initializeitemTypeahead();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#sales_return_form').bootstrapValidator('revalidateField', 'returnDate');
        });
        var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            salesReturnAutoID = p_id;
            <?php if($createmasterrecords==1){?>
            fetch_customerdrop('',salesReturnAutoID)
            <?php }?>
            load_sales_return_header();
            $("#a_link").attr("href", "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>/" + salesReturnAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_sales_return'); ?>/" + salesReturnAutoID + '/SLR');
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            <?php if($createmasterrecords==1){?>
            fetch_customerdrop()
            <?php }?>
            var CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
            currency_validation_modal(CurrencyID, 'PR', '', '');
        }

        var FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        var DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        var periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);
        $('#sales_return_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //customerID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_customer_id_required');?>.'}}}, /*Customer ID is required*/
                //financeyear: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year_is_required');?>.'}}}, /*Financial Year is required*/
                //financeyear_period: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period_is_required');?>.'}}}, /*Financial Period is required*/
                returnDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_return_date_is_required');?>.'}}}, /*Return Date is required*/
                location: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_location_is_required');?>.'}}}, /*Location is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_cutomer_currency_is_required');?>.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#transactionCurrencyID").prop("disabled", false);
            $("#customerID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'salesReturnAutoID', 'value': salesReturnAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'location_dec', 'value': $('#location option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Inventory/save_sales_return_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        salesReturnAutoID = data['last_id'];
                        $("#a_link").attr("href", "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>/" + salesReturnAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_sales_return'); ?>/" + salesReturnAutoID + '/SLR');
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#customerID").prop("disabled", true);
                        fetch_detail();
                        $('[href=#step2]').tab('show');
                    }
                    ;
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#item_detail_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                itemAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}}, /*Item is required*/
                search: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}}, /*Item is required*/
                itemSystemCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}}, /*Item is required*/
                itemDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_item_is_required');?>.'}}}, /*Item is required*/
                unitOfMeasure: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_uom_is_required');?>.'}}}, /*Unit Of Measure is required*/
                return_QTY: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_return_qty_is_required');?>.'}}}/*return Quantity is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'salesReturnAutoID', 'value': salesReturnAutoID});
            data.push({'name': 'salesreturnDetailsID', 'value': salesreturnDetailsID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Inventory/save_stock_return_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    salesreturnDetailsID = null;
                    refreshNotifications(true);
                    stopLoad();
                    $('#item_detail_modal').modal('hide');
                    fetch_material_item_detail();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
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

    function load_sales_return_header() {
        if (salesReturnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'salesReturnAutoID': salesReturnAutoID},
                url: "<?php echo site_url('Inventory/load_sales_return_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        documentCurrency = data['transactionCurrencyID'];
                        salesReturnAutoID = data['salesReturnAutoID'];
                        $('#returnDate').val(data['returnDate']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $("#location").val(data['wareHouseAutoID']).change();
                        $('#customerID').val(data['customerID']).change();
                        $('#narration').val(data['comment']);
                        $('#referenceNo').val(data['referenceNo']);
                        fetch_detail();
                        check_detail_dataExist(salesReturnAutoID);
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }


    function item_detail_modal() {
        if (salesReturnAutoID) {
            $('#item_detail_form')[0].reset();
            $('#item_detail_form').bootstrapValidator('resetForm', true);
            $("#item_detail_modal").modal({backdrop: "static"});
        }
    }

    function fetch_detail() {
        if (salesReturnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'salesReturnAutoID': salesReturnAutoID},
                url: "<?php echo site_url('Inventory/fetch_sales_return_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#step2').html(data);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
        ;
    }

    function check_detail_dataExist(salesReturnAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'salesReturnAutoID': salesReturnAutoID},
            url: "<?php echo site_url('Inventory/fetch_sales_return_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (jQuery.isEmptyObject(data['detail'])) {
                    $("#customerID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                } else {
                    $("#customerID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function fetch_related_uom(short_code, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'short_code': short_code},
            url: "<?php echo site_url('dashboard/fetch_related_uom'); ?>",
            success: function (data) {
                $('#unitOfMeasure').empty();
                var mySelect = $('#unitOfMeasure');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitShortCode']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $("#unitOfMeasure").val(select_value);
                        $('#item_detail_form').bootstrapValidator('revalidateField', 'unitOfMeasure');
                    }
                    ;
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_conformation() {
        if (salesReturnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'salesReturnAutoID': salesReturnAutoID, 'html': true},
                url: "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>/" + salesReturnAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_sales_return'); ?>/" + salesReturnAutoID + '/SLR');
                    attachment_modal_purchaseReturn(salesReturnAutoID, "<?php echo $this->lang->line('sales_markating_transaction_sales_return');?>", "SLR");
                    /*Sales Return*/
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                }
            });
        }
    }



    function confirmation() {
        if (salesReturnAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>", /*You want to confirm this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"

                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'salesReturnAutoID': salesReturnAutoID},
                        url: "<?php echo site_url('Inventory/sales_return_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();

                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            } else if (data['error'] == 2) {
                                myAlert('w', data['message']);
                            }
else if (data['error'] == 4){ 
                                
                             $('#wac_minus_calculation_validation_body').empty();
                             x = 1;
                             if (jQuery.isEmptyObject(data['message'])) {
                                 $('#wac_minus_calculation_validation_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                            } else {
                                $.each(data['message'], function (key, value) {
                       
                                 $('#wac_minus_calculation_validation_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>'+value['itemName']+'</td><td>' + value['Amount'] + '</td></tr>');
                                x++;
                      
        
                            });
                            }
                                $('#wac_minus_calculation_validation').modal('show');
                            }

                            else {
                                refreshNotifications(true);
                                //fetchPage('system/inventory/stock_return_management', salesReturnAutoID, 'Stock Return');
                                fetchPage('system/invoices/sales_return', '', 'Sales Return ')
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

    function save_draft() {
        if (salesReturnAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>", /*You want to save this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>", /*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/invoices/sales_return', '', 'Sales Return ')
                    /*fetchPage('system/inventory/invoices_management', salesReturnAutoID, 'Stock Return');*/
                });
        }
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID,'fyDepartmentID':'RET'},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Finance Period'));
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

    function attachment_modal_purchaseReturn(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#purchaseReturn_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "<?php echo $this->lang->line('common_attachments');?>");
                    <!--Attachments-->
                    $('#purchaseReturn_attachment').empty();
                    $('#purchaseReturn_attachment').append('' +data+ '');


                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_purchaseReturn_attachement(salesReturnAutoID, DocumentSystemCode, myFileName) {
        if (salesReturnAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_delete_attach_this_file');?>", /*You want to delete this attachment file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>", /*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': salesReturnAutoID, 'myFileName': myFileName},
                        url: "<?php echo site_url('inventory/delete_purchaseReturn_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                attachment_modal_purchaseReturn(DocumentSystemCode, "Sales Return", "SLR");
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

    function link_employee_model() {
        $('#customercode').val('');
        $('#customerName').val('');
        $('#IdCardNumber').val('');
        $('#customerTelephone').val('');
        $('#customerEmail').val('');
        $('#customerFax').val('');
        $('#customerCreditPeriod').val('');
        $('#customerCreditLimit').val('');
        $('#customerUrl').val('');
        $('#customerAddress1').val('');
        $('#customerAddress2').val('');
        $('#partyCategoryID').val(null).trigger('change');
        $('#receivableAccount').val('<?php echo $this->common_data['controlaccounts']['ARA']?>').change();
        $('#customerCurrency').val('<?php echo $this->common_data['company_data']['company_default_currencyID']?>').change();
        $('#customercountry').val('<?php echo $this->common_data['company_data']['company_country']?>').change();
        $('#customertaxgroup').val(null).trigger('change');
        $('#vatIdNo').val(null).trigger('change');
        $('#emp_model').modal('show');

    }
    function save_customer_master() {
        var data = $("#customermaster_form").serializeArray();
        data.push({'name' : 'currency_code', 'value' : $('#customerCurrency option:selected').text()});
        //data.push({'name' : 'customerAutoID', 'value' : $('#customerID option:selected').val()});
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Customer/save_customer'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if(data['status'] == true)
                    {
                        $('#emp_model').modal('hide');
                        fetch_customerdrop(data['last_id'],' ');
                        fetch_supplier_currency_by_id(data['last_id']);

                    }else
                    {
                        $('#emp_model').modal('show');

                    }


                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    $('#emp_model').modal('show');
                    refreshNotifications(true);
                }
            });
    }
    function changecreditlimitcurr(){
        var currncy;
        var split;
        currncy=  $('#customerCurrency option:selected').text();
        split= currncy.split("|");
        $('.currency').html(split[0]);
        CurrencyID = $('#customerCurrency').val();
        currency_validation_modal(CurrencyID,'CUS','','CUS');
    }

    function fetch_supplier_currency_by_id(supplierAutoID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'customerAutoID': supplierAutoID},
            url: "<?php echo site_url('Payable/fetch_customer_currency_by_id'); ?>",
            success: function (data) {
                if (documentCurrency) {
                    $("#transactionCurrencyID").val(documentCurrency).change()
                } else {
                    if (data.customerCurrencyID) {
                        $("#transactionCurrencyID").val(data.customerCurrencyID).change();
                    }
                }
            }
        });
    }


    function fetch_customerdrop(id,salesReturnAutoID) {
        var customer_id;
        var page = '';
        if(salesReturnAutoID)
        {
            page = salesReturnAutoID
        }
        if(id)
        {
            customer_id = id
        }else
        {
            customer_id = '';
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {customer:customer_id,DocID:page,Documentid:'SLR'},
            url: "<?php echo site_url('Invoices/fetch_customer_Dropdown_all_sales_return'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_customer_drop').html(data);
                stopLoad();
                $('.select2').select2();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

</script>