<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('srm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('accounts_payable', $primaryLanguage);
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
$ApprovalforSupplierMaster= getPolicyValues('ASM', 'All');
if($ApprovalforSupplierMaster==NULL){
    $ApprovalforSupplierMaster=0;
}
$location_arr = fetch_all_location();

echo head_page($_POST['page_name'], false);
$this->load->helper('srm_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$customerArr = all_srm_customers();
$currency_arr = all_currency_new_drop();
//$countries_arr = load_all_countrys();
//$groupmaster_arr = all_crm_groupMaster();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: #060606
    }

    .contact-box .align-left {
        float: left;
        margin: 0 7px 0 0;
        padding: 2px;
        border: 1px solid #ccc;
    }

    img {
        vertical-align: middle;
        border: 0;
        -ms-interpolation-mode: bicubic;
    }

    .posts-holder {
        padding: 0 0 10px 4px;
        margin-right: 10px;
    }

    #toolbar, .past-info .toolbar {
        background: #f8f8f8;
        font-size: 13px;
        font-weight: bold;
        color: #000;
        border-radius: 3px 3px 0 0;
        -webkit-border-radius: 3px 3px 0 0;
        border: #dcdcdc solid 1px;
        padding: 5px 15px 12px 10px;
        height: 20px;
    }

    .past-info {
        background: #fff;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        padding: 0 0 8px 10px;
        margin-left: 2px;
    }

    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .search_cancel {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;
        vertical-align: middle;
        padding: 3px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 5px 0 6px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .custome {
        width: 60%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
    }

    .customestyle {
        width: 60%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -46%
    }

    .customestyle2 {
        width: 80%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -94%
    }

    .customestyle3 {
        width: 80%;
        background-color: #f2f2f2;
        font-size: 14px;
        font-weight: 500;
        margin-left: -94%
    }

    #search_cancel img {
        background-color: #f3f3f3;
        border: solid 1px #dcdcdc;

        padding: 4px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }

    .textClose {
        text-decoration: line-through;
        font-weight: 500;
        text-decoration-color: #3c8dbc;
    }

    .btn-group {
        width: 100%;
    }

</style>
<div class="set-poweredby">Powered by &nbsp;<a href=""><img src="https://ilooopssrm.rbdemo.live/images/logo-dark.png" width="75" alt="MaxSRM"></a></div>
<div class="m-b-md" id="wizardControl">
        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('srm_step_one');?><!--Step 1--> - <?php echo $this->lang->line('srm_step_inquiry_header');?><!--Inquiry Header--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="getCustomerInquiryItem_tableView();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('srm_step_two');?><!--Step 2--> - <?php echo $this->lang->line('srm_step_order_details');?><!--Order Details--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard generate-rfq" href="#step3" onclick="generate_rfq();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('srm_step_three');?><!--Step 3--> - <?php echo $this->lang->line('srm_generated_rfq');?><!--Generated RFQ--></span>
            </a>
        </div>    
</div>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="customer_order_inquiry_form"'); ?>
        <input type="hidden" name="inquiryID" id="inquiryID_master">
        <br>

        <div class="row">
            <div class="form-group col-sm-4">
                <label>Inquiry Type<?php required_mark(); ?></label>
                <select name="inquiryType" class="form-control" id="inquiryType" onchange="load_type_view()">
                    <option value="" selected="selected">Select Type</option>
                    <!--<option value="Direct">Direct</option>-->
                    <option value="PRQ">Purchase Request</option>
                    <option value="Customer">Customer Order</option>
                </select>
            </div>

            <div class="form-group col-sm-4">
                <label>Template<?php required_mark(); ?></label>
                <select name="templateType" class="form-control" id="templateType">
                   
                    <option value="1">Item Base</option>
                    <option value="2">Supplier Base</option>
                </select>
            </div>

            <div class="form-group col-sm-4">
                <label>Currency <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID" onchange="load_customer_orderID(),load_purchase_requestID()" required'); ?>
            </div>

            <div class="form-group col-sm-4 pt-10">
                <label>Document Date <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="documentDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-4 pt-10">
                <label>RFQ Expiry Date <?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="linkExpire" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="linkExpire" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row pb-10">
            <div class="form-group col-sm-4 customerbase hidden">
                <label>Customer Name <?php required_mark(); ?></label>
                <?php echo form_dropdown('customerID', $customerArr, '', 'class="form-control select2" onchange="load_customer_orderID()" id="customerID" '); ?>
            </div>

            <div class="form-group col-sm-4 prqbase hidden">
                <label>PRQ ID <?php required_mark(); ?></label>
                <!-- <div id="div_requestID">
                    <select name="purchaseRequestID" id="purchaseRequestID" onchange="load_prq_view()" class="form-control select2" >
                    </select>
                </div>
                <div class="prq-btn">
                    <a onclick="selectPRQ();"><i class="fa fa-plus fa-2x"></i></a>
                </div> -->

                <div class="input-group">
                    <input type="text" class="form-control" id="purchaseRequestName" name="purchaseRequestName" onchange="load_prq_view()" readonly>
                    <input type="hidden" class="form-control" id="purchaseRequestID" name="purchaseRequestID" >
                    <span class="input-group-btn">
                        <!--<button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clearEmployee()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>-->
                        <button class="btn btn-default" type="button" title="Add PR" rel="tooltip"
                                onclick="selectPRQ()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                    </span>
   
             </div>
            </div>

            <div class="form-group col-sm-4 customerbase hidden">
                <label>Order ID<?php required_mark(); ?></label>
                <div id="div_orderID">
                    <select name="customer_orderID[]" id="customer_orderID" class="form-control" multiple="">
                    </select>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label>Narration </label>
                <textarea class="form-control" rows="3" name="narration" id="narration"></textarea>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="form-group col-sm-12">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save Next--></button>
                </div>
            </div>
        </div>
        </form>

        <div class="row" id="prqview">

        </div>
    </div>
    <div id="step2" class="tab-pane">
        <br>

        <div class="row">
            <div class="col-md-10 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('srm_order_item_details');?><!--ORDERED ITEM DETAILS--></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <div id="orderBase_item"></div>
                    </div>
                    <div class="col-sm-5">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <div class="row generatebtn hide" style="margin-top: 10px;">
            <div class="col-sm-10">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary-new size-lg" onclick="generate_order_itemView()"><?php echo $this->lang->line('common_generate');?><!--Generate--></button>
                </div>
            </div>
            <div class="col-sm-5">
                &nbsp;
            </div>
        </div>
        <div class="row hide" id="supplier_detail_div">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('srm_order_item_supplier_details');?><!--ITEM SUPPLIER DETAILS--></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <div id="itemBase_suppliers"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <br>

        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('srm_suppier_rfq');?><!--SUPPLIER RFQ--></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-8">
                        <div id="generated_supplier_rfq_view"></div>
                    </div>
                    <div class="col-sm-4">
                        &nbsp;
                    </div>
                </div>
            </div>
            <br>

            <div class="row hide" style="margin-top: 10px;">
                <div class="col-sm-8">
                    <div class="text-right m-t-xs">
                        <button class="btn btn-primary" onclick="generate_rfq()" style="margin-right: 2%;"><?php echo $this->lang->line('srm_send_all_rfq');?><!--Send All RFQ-->
                        </button>
                    </div>
                </div>
                <div class="col-sm-4">
                    &nbsp;
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="prq_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="closed_user_label">Purchase Request for Quotation</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-sm-12">                        
                        <div id="prq_modal_content"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<!--Supplier Portal link show modal-->
<div class="modal fade" id="getLinkModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                                        
                <h4 class="modal-title" id="myModalLabel2"> Supplier Portal Link </h4>
            </div>
            <div class="modal-body" id="getLink" style="color: #696CFF;">
                //Supplier Portal Link content here.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeGetLinkModal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="srm_rfq_modelView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle"><?php echo $this->lang->line('srm_request_for_quotation');?><!--Request For Quotation--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="srm_rfqPrint_Content"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="srm_rfq_modelView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle"><?php echo $this->lang->line('srm_request_for_quotation');?><!--Request For Quotation--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="srm_rfqPrint_Content"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="assignSupplier_item_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle"><?php echo $this->lang->line('srm_request_for_quotation');?><!--Request For Quotation--></h4>
            </div>
            <div class="modal-body">

            <div class="row" style="margin: 6px 0px;">
                    <div class="col-sm-5">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchOrder" type="text" class="form-control input-sm"
                                       placeholder="Search"
                                       id="searchOrder" onkeyup="startMasterSearch()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
            </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="assignedSupplier_itemID"></div>
                        <div id="assignSupplier_item_Content"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-sm-12 pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button class="btn btn-primary" onclick="assign_supplier()"><?php echo $this->lang->line('srm_assign');?><!--Assign--></button>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignSupplier_view_item_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle"><?php echo $this->lang->line('srm_request_for_quotation');?><!--Request For Quotation--></h4>
            </div>
            <div class="modal-body">

            <div class="row" style="margin: 6px 0px;">
                    <div class="col-sm-5">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="searchOrder_sup" type="text" class="form-control input-sm"
                                       placeholder="Search"
                                       id="searchOrder_sup" onkeyup="startMasterSearch_supplier_view()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
            </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="assignedSupplier_itemID_view"></div>
                        <div id="assignSupplier_item_Content_view"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-sm-12 pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button class="btn btn-primary" onclick="assign_supplier_view_template()"><?php echo $this->lang->line('srm_assign');?><!--Assign--></button>
                </div>

            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="prq_list_table_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_add'); ?><?php echo $this->lang->line('sales_markating_transaction_document_item_detail'); ?> </h4>
                <!--Add Item Detail-->
            </div>

            <div class="modal-body">
                <div class="row pt-0">
                    <div class="col-sm-12">
                        
                            <form role="form" id="crew_contract_select_form" class="form-horizontal">
                                
                                    <div class="modal-body">
                                        
                                        <div class="table-responsive">
                                            <table class="<?php echo table_class(); ?>" id="prq_list_table">
                                                <thead>
                                                <tr>
                                                        <th>#</th>
                                                        <th>Doc Number</th>
                                                        <th>Date</th>
                                                        <th>Narration</th>
                                                        <th>Exp Delivery Date</th>
                                                        <th>Requester</th>
                                                        <th>Value</th>
                                                        <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                            
                                                </tbody>
                                            </table>
                                        </div>    
                                    </div>
                                    <div class="modal-footer">
                                        
                                        <button data-dismiss="modal" class="btn btn-default size-sm"
                                                type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                                    
                                    </div>
                            </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="new_supplier_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">New Supplier</h4>
            </div>
            <?php echo form_open('', 'role="form" id="suppliermaster_new_form"'); ?>
            <div class="modal-body">
           
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('accounts_payable_sm_secondary_code');?><!--Secondary Code--> <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="suppliercode" name="suppliercode">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierName"><?php echo $this->lang->line('accounts_payable_sm_company_name');?><!--Company Name / Name--> <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="supplierName" name="supplierName" required>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierName"><?php echo $this->lang->line('accounts_payable_sm_name_on_cheque');?><!--Name On Cheque--> <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="nameOnCheque" name="nameOnCheque">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('common_category');?><!--Category--></label>
                        <?php echo form_dropdown('partyCategoryID', $customerCategory, '', 'class="form-control select2"  id="partyCategoryID"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="liabilityAccount"><?php echo $this->lang->line('accounts_payable_sm_liability_account');?><!--Liability Account--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('liabilityAccount', $gl_code_arr, $this->common_data['controlaccounts']['APA'], 'class="form-control select2" id="liabilityAccount" required'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierCurrency"><?php echo $this->lang->line('common_currency');?><!--Currency--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('supplierCurrency', $currncy_arr, $this->common_data['company_data']['company_default_currency'], 'class="form-control select2" onchange="changecreditlimitcurr()" id="supplierCurrency" required'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('common_Country');?><!--Country--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('suppliercountry', $country_arr, $this->common_data['company_data']['company_country'], 'class="form-control select2"  id="suppliercountry" required'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('accounts_payable_sm_tax_group');?><!--Tax Group--></label>
                        <?php echo form_dropdown('suppliertaxgroup', $taxGroup_arr, '', 'class="form-control select2"  id="suppliertaxgroup"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for=""><?php echo $this->lang->line('common_Location');?><!--Location--></label>
                        <?php echo form_dropdown('supplierLocationID', $location_arr, '', 'class="form-control select2"  id="supplierLocationID"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="supplierTelephone"><?php echo $this->lang->line('common_telephone');?><!--Telephone--></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="supplierTelephone" name="supplierTelephone">
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierEmail"><?php echo $this->lang->line('common_email');?><!--Email--></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="supplierEmail" name="supplierEmail">
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierFax"><?php echo $this->lang->line('accounts_payable_sm_fax');?><!--FAX--></label>
                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-fax" aria-hidden="true"></i></div>
                            <input type="text" class="form-control" id="supplierFax" name="supplierFax">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="suppliersupplierCreditPeriod"><?php echo $this->lang->line('accounts_payable_sm_credit_period');?><!--Credit Period--></label>
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo $this->lang->line('common_month');?><!--Month--></div>
                            <input type="text" class="form-control number" id="supplierCreditPeriod"
                                name="supplierCreditPeriod">
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="suppliersupplierCreditLimit"><?php echo $this->lang->line('accounts_payable_sm_credit_limit');?><!--Credit Limit--></label>
                        <div class="input-group">
                            <div class="input-group-addon"><span class="currency">LKR</span></div>
                            <input type="text" class="form-control number" id="supplierCreditLimit" name="supplierCreditLimit">
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
                        <label for="vatEligible"><?php echo $this->lang->line('accounts_payable_vat_eligible');?><!--VAT Eligible--></label>
                        <?php echo form_dropdown('vatEligible', array('1'=> $this->lang->line('common_no'), '2'=> $this->lang->line('common_yes')), 1, 'class="form-control select2" id="vatEligible" required'); ?>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="">VAT Identification No</label>
                        <input type="text" class="form-control" id="vatIdNo" name="vatIdNo">
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="vatPercentage">VAT <?php echo $this->lang->line('common_percentage');?><!--VAT Percentage--></label>
                        <input type="text" class="form-control" id="vatPercentage" name="vatPercentage">
                    </div>

                </div>
                <div class="row">

                    <div class="form-group col-sm-4">
                        <label for="supplierAddress1"><?php echo $this->lang->line('accounts_payable_sm_address_one');?><!--Address 1--></label>
                        <textarea class="form-control" rows="2" id="supplierAddress1" name="supplierAddress1"></textarea>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="supplierAddress2"><?php echo $this->lang->line('accounts_payable_sm_address_two');?><!--Address 2--></label>
                        <textarea class="form-control" rows="2" id="supplierAddress2" name="supplierAddress2"></textarea>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for=""><?php echo $this->lang->line('accounts_payable_sm_is_active');?><!--isActive--></label>

                            <div class="skin skin-square">
                                <div class="skin-section" id="extraColumns">
                                    <input id="checkbox_isActive" type="checkbox"
                                        data-caption="" class="columnSelected" name="isActive" value="1" checked>
                                    <label for="checkbox">
                                        &nbsp;
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            </form>
            <div class="modal-footer">
                <div class="col-sm-12 pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button class="btn btn-primary" onclick="save_new_supplier()">Save</button>
                </div>
            </div>
            
        </div>
    </div>
</div>


<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var ordArr;
    var search_id = 1;
    var selectedItemsSync = [];
    var selectedSupplierSync = [];
    var assignSupplierItemSync = [];
    var assignSupplierview = [];
    var assignSupplierViewSync =[];

    var inquiryID = '';
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/srm/srm_order_inquiry', '', 'Request for Quotation')
        });

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            //$('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });

        $('.select2').select2();

        $('#customer_orderID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '100%',
            maxHeight: '30px'
        });

        Inputmask().mask(document.querySelectorAll("input"));

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            inquiryID = p_id;
            load_customerInquiry_header();
            $('.btn-wizard').removeClass('disabled');
            $('.generate-rfq').addClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            //save_customer_order();
        }

        $('#customer_order_inquiry_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                inquiryType: {validators: {notEmpty: {message: 'Inquiry Type is Requested.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}},/*Currency is required*/
                documentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_date_is_required');?>.'}}},/*Document Date is required*/
                linkExpire: {validators: {notEmpty: {message: 'RFQ url expire Date is required.'}}}/*Document Date is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#inquiryType").prop("disabled", false);
            $("#purchaseRequestID").prop("disabled", false);
            $("#customer_orderID").prop("disabled", false);
            $('#transactionCurrencyID').prop('disabled', false);
            $('#purchaseRequestID').prop('disabled', false);
            $('#customerID').prop('disabled', false);
            $("#customer_orderID").multiselect2("enable");
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('srm_master/save_order_inquiry'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        recordExists(data[2])
                        $('#inquiryID_master').val(data[2]);
                        getCustomerInquiryItem_tableView();
                        $('.btn-wizard').removeClass('disabled');
                        $('[href=#step2]').tab('show');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
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

    function getCustomerInquiryItem_tableView() {
        var orderID = $('#customer_orderID').val();
        var inquiryType = $('#inquiryType').val();
        var inquiry_ID = $('#inquiryID_master').val();
        $('.generatebtn').addClass('hide');
        /*if(orderID)
        {*/
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {orderID: orderID, inquiry_ID: inquiry_ID, inquiryType: inquiryType},
                url: "<?php echo site_url('srm_master/load_customer_inquiry_detail_items_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#orderBase_item').html(data);
                    $('.generatebtn').removeClass('hide');
                    stopLoad();
                    if (inquiryID != '') {
                        load_customerInquiry_header();
                        setTimeout(function () {
                            generate_supplierView();
                        }, 500);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
       /* }*/

    }


    function save_customer_order() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {},
            url: "<?php echo site_url('srm_master/save_customer_ordermaster_add'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    $('#customerOrderID_edit').val(data[2]);
                    $('#customerOrderID_orderDetail').val(data[2]);
                    load_customer_order_autoGeneratedID(data[2]);
                    getCustomerOrderItem_tableView(data[2]);
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function saveCustomerOrderDetails() {
        var customerOrderID = $('#customerOrderID_orderDetail').val();
        var data = $('#customer_order_detail_form').serializeArray();
        $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $.ajax(
            {
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('srm_master/save_customer_order_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        getCustomerOrderItem_tableView(customerOrderID);
                        $('#customer_order_detail_modal').modal('hide');
                    }
                }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
            });

    }

    function load_customerInquiry_header() {
        if (inquiryID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'inquiryID': inquiryID},
                url: "<?php echo site_url('srm_master/load_customerInquiry_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['header'])) {
                        $('#inquiryID_master').val(inquiryID);
                        $('#inquiryType').val(data['header']['inquiryType']);
                        $('#customerID').val(data['header']['customerID']).change();
                        $('#transactionCurrencyID').val(data['header']['transactionCurrencyID']).change();
                        $('#documentDate').val(data['header']['documentDate']);
                        $('#linkExpire').val(data['header']['rfqExpDate']);
                        $('#narration').val(data['header']['narration']);
                        $('#deliveryTerms').val(data['header']['deliveryTerms']);
                        $('#purchaseRequestID').val(data['header']['purchaseRequestID']);
                        $('#purchaseRequestName').val(data['header']['purchaseRequestName']);
                        $('#templateType').val(data['header']['templateType']);
                        $('.btn-wizard').addClass('disabled');
                        setTimeout(function () {
                            $('.btn-wizard').removeClass('disabled');
                        }, 1500);


                        if (data['header']['confirmedYN'] == 1) {
                            $('.btn-wizard').addClass('disabled');
                            $('.generate-rfq').removeClass('disabled');
                            $('[href=#step3]').tab('show');
                        }
                        //getCustomerOrderItem_tableView(customerOrderID);
                    }
                    if (!jQuery.isEmptyObject(data['ordersdrp'])) {
                        var selectedItems = [];
                        $.each(data['ordersdrp'], function (key, value) {
                            selectedItems.push(value.customerOrderID);
                        });
                        setTimeout(function () {
                            $('#customer_orderID').val(selectedItems).multiselect2("refresh");
                        }, 1000);
                    }else{
                        var selectedItems = [];
                        $.each(data['orders'], function (key, value) {
                            selectedItems.push(value.customerOrderID);
                        });
                        setTimeout(function () {
                            $('#customer_orderID').val(selectedItems).multiselect2("refresh");
                        }, 1000);
                    }
                    setTimeout(function () {
                        $('#purchaseRequestID').val(data['header']['purchaseRequestID']).change();
                    }, 1000);

                    load_type_view();
                    if (!jQuery.isEmptyObject(data['orderItem'])) {
                        $.each(data['orderItem'], function (key, value) {
                            $('#isAttended_' + value.itemAutoID).iCheck('check');
                        });

                    }

                    if (!jQuery.isEmptyObject(data['orderItem'])) {
                        if (data['header']['inquiryType'] == 'PRQ') {
                            $("#inquiryType").prop("disabled", true);
                            setTimeout(function () {
                                $("#purchaseRequestID").prop("disabled", true);
                            }, 1200);
                        } else {
                            $("#inquiryType").prop("disabled", true);
                            setTimeout(function () {
                                $("#customer_orderID").prop("disabled", true);
                            }, 1200);
                        }
                    }else {
                        if (data['header']['inquiryType'] == 'PRQ') {
                            $("#inquiryType").prop("disabled", false);
                            $("#purchaseRequestID").prop("disabled", false);
                        } else {
                            $("#inquiryType").prop("disabled", false);
                            $("#customer_orderID").prop("disabled", false);
                        }
                    }
                    recordExists(data['header']['inquiryID']);
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

    function confirmation() {
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
                var data = $('#customer_order_form').serializeArray();
                data.push({'name': 'confirmedYN', 'value': 1});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('srm_master/save_customer_order_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/srm/srm_customer_order', '', 'Customer Order');
                        } else {

                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function save_draft() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('srm_you_want_to_save_this_customer_order');?>",/*You want to save this Customer Order!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                var data = $('#customer_order_form').serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('srm_master/save_customer_order_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/srm/srm_customer_order', '', 'Customer Order');
                        } else {

                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }
    // added for order form


    function load_customerOrder_BaseItem(select_val) {
        $('#order_itemID').val("");
        $('#order_itemID option').remove();
        var customerID = $('#customerID').val();
        var customerOrderID = $('#customer_orderID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("srm_master/load_customerOrder_BaseItem"); ?>',
            dataType: 'json',
            data: {'customerOrderID': customerOrderID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#order_itemID').empty();
                    var mySelect = $('#order_itemID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemAutoID']).html(text['itemDescription']));
                    });
                    if (select_val) {
                        $("#order_itemID").val(select_val);
                    }
                    load_OrderID_BaseCurrency();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


    function load_OrderID_BaseCurrency() {
        var customerOrderID = $('#customer_orderID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("srm_master/load_OrderID_BaseCurrency"); ?>',
            dataType: 'json',
            data: {'customerOrderID': customerOrderID},
            async: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#transactionCurrencyID').val(data['transactionCurrencyID']).change();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function orderItem_selected_check(item) {
      var value = $(item).val();
        if ($(item).is(':checked')) {
            var inArray = $.inArray(value, selectedItemsSync);
            if (inArray == -1) {
                selectedItemsSync.push(value);
            }
        }
        else {
            var i = selectedItemsSync.indexOf(value);
            if (i != -1) {
                selectedItemsSync.splice(i, 1);
            }
        }
    }

    function generate_order_itemView() {
        var orderID = $('#customer_orderID').val();
        var inquiryID = $('#inquiryID_master').val();
        var inquiryType = $('#inquiryType').val();
        var linecomment = [];
        var pr_doc = [];
        if (!jQuery.isEmptyObject(selectedItemsSync)) {
            $.each(selectedItemsSync, function (val, text) {
                var res = text.split("_");
                var cmnt=$('#lineWiseComment_'+res[0]).val();
                linecomment.push(cmnt);

                var cmnt_doc=$('#pr_doc_'+res[0]).val();
                pr_doc.push(cmnt_doc);
            });

            //return false;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'selectedItemsSync': selectedItemsSync, orderID: orderID, inquiryID: inquiryID, inquiryType: inquiryType, linecomment: linecomment,pr_doc:pr_doc},
                url: "<?php echo site_url('srm_master/save_order_inquiry_itemDetail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data) {
                        generate_supplierView();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }else{
            myAlert('e','Select Item');
        }

    }

    function generate_supplierView() {
        var inquiryID = $('#inquiryID_master').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {inquiryID: inquiryID},
            url: "<?php echo site_url('srm_master/load_customer_inquiry_detail_sellars_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $.each(data['rfq_gen'], function (key, value) {
                    selectedSupplierSync.push(value['inquiryDetailID']);
                    });
              




                $('#supplier_detail_div').removeClass('hide');
                $('#itemBase_suppliers').html(data['view']);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function supplier_selected_check(supplier) {
        
        var value = $(supplier).val();
        if ($(supplier).is(':checked')) {
       
            var inArray = $.inArray(value, selectedSupplierSync);
            if (inArray == -1) {
                selectedSupplierSync.push(value);
            }
           
        }
        else {
         
            var i = selectedSupplierSync.indexOf(value);
            if (i != -1) {
                selectedSupplierSync.splice(i, 1);
            }
        
         
        }
    }

    function supplier_selected_check_supplier_view(supplier) {
        
        var value = $(supplier).val();
        if ($(supplier).is(':checked')) {
       
            var inArray = $.inArray(value, assignSupplierview);
            if (inArray == -1) {
                assignSupplierview.push(value);
            }
           
        }
        else {
         
            var i = assignSupplierview.indexOf(value);
            if (i != -1) {
                assignSupplierview.splice(i, 1);
            }
        
         
        }
    }


    function generated_supplier_RFQ_View() {
        var inquiryID = $('#inquiryID_master').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryID: inquiryID},
            url: "<?php echo site_url('srm_master/load_orderbase_generated_rfq_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#generated_supplier_rfq_div').removeClass('hide');
                $('#generated_supplier_rfq_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function view_rfq_printModel(inquiryMasterID, supplierID) {
        var html = 'html';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryMasterID: inquiryMasterID, supplierID: supplierID, html: html},
            url: "<?php echo site_url('srm_master/supplier_rfq_print_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#documentPageViewTitle').html(title);
                $('#srm_rfqPrint_Content').html(data);
                $("#srm_rfq_modelView").modal({backdrop: "static"});
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function add_url_expire_date(inquiryMasterID,supplierID,companyID) {
       
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "You want to add URL expire date",/*You want to confirm this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                var date = $('#link_expire_date').val();
                
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        inquiryMasterID: inquiryMasterID,
                        supplierID: supplierID,
                        companyID: companyID,
                        expireDate: date
                    },
                    url: "<?php echo site_url('srm_master/add_url_expire_date'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        // if (data[0] == 's') {
                        //     $('#generated_supplier_rfq_div').removeClass('hide');
                        //     generated_supplier_RFQ_View();
                        //     $('.generate-rfq').removeClass('disabled');
                        //     $('.btn-wizard').removeClass('disabled');
                        //     $('[href=#step3]').tab('show');
                        //     $(document).scrollTop(0);
                        // } else {

                        // }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Something Went Wrong ! :)", "error");
                    }
                });
            });
    }

    function load_customer_orderID() {
        var inquiryType = $('#inquiryType').val();
        var customerID = $('#customerID').val();
        var currency = $('#transactionCurrencyID').val();
        if(inquiryType=='Customer' ){
            if (!jQuery.isEmptyObject(customerID) && !jQuery.isEmptyObject(currency)) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("srm_master/load_customerbase_ordersID"); ?>',
                    dataType: 'html',
                    data: {customerID: customerID, currency: currency},
                    async: true,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#div_orderID').html(data);
                        $('#customer_orderID').multiselect2({
                            enableCaseInsensitiveFiltering: true,
                            includeSelectAllOption: true,
                            numberDisplayed: 1,
                            buttonWidth: '100%',
                            maxHeight: '30px'
                        });
                        stopLoad();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                    }
                });
            }
        }

    }

    function clearCurrency() {
        $("#transactionCurrencyID").val(null).trigger("change");
    }

    function view_supplierAssignModel(itemAutoID,type,search) {
       var search_index = '';
        if(type == 0){ 
            $('#assignedSupplier_itemID').val('');
        }else { 
            search_index  = search;
        }
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {itemAutoID: itemAutoID,Search:search_index},
            url: "<?php echo site_url('srm_master/assignItem_supplier_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#documentPageViewTitle').html(title);
                $('#assignedSupplier_itemID').val(itemAutoID);
                $('#assignSupplier_item_Content').html(data);
                if(type == 0){ 
                    $("#assignSupplier_item_model").modal({backdrop: "static"});
                }
            
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function view_supplierViewAssignModel(type,search) {
       var search_index = '';
        if(type == 0){ 
            $('#assignedSupplier_itemID_view').val('');
        }else { 
            search_index  = search;
        }
        var inquiryID = $('#inquiryID_master').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryID: inquiryID,Search:search_index},
            url: "<?php echo site_url('srm_master/assignItem_supplier_template_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#documentPageViewTitle').html(title);
                $('#assignedSupplier_itemID_view').val(inquiryID);
                $('#assignSupplier_item_Content_view').html(data);
                if(type == 0){ 
                    $("#assignSupplier_view_item_model").modal({backdrop: "static"});
                }
            
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function startMasterSearch(){ 
        var itemAutoID =  $('#assignedSupplier_itemID').val();
        var search = $('#searchOrder').val();
        view_supplierAssignModel(itemAutoID,1,search);
    }

    function startMasterSearch_supplier_view(){
        //var itemAutoID =  $('#assignedSupplier_itemID').val();
        var search = $('#searchOrder_sup').val();
        view_supplierViewAssignModel(1,search);
    }

    function assign_supplier_selected_check(supplier) {
        var value = $(supplier).val();
        if ($(supplier).is(':checked')) {
            var inArray = $.inArray(value, assignSupplierItemSync);
            if (inArray == -1) {
                assignSupplierItemSync.push(value);
            }
        }
        else {
            var i = assignSupplierItemSync.indexOf(value);
            if (i != -1) {
                assignSupplierItemSync.splice(i, 1);
            }
        }
    }

    function assign_supplier_selected_check_supplier_template(supplier) {
        var value = $(supplier).val();
        if ($(supplier).is(':checked')) {
            var inArray = $.inArray(value, assignSupplierViewSync);
            if (inArray == -1) {
                assignSupplierViewSync.push(value);
            }
        }
        else {
            var i = assignSupplierViewSync.indexOf(value);
            if (i != -1) {
                assignSupplierViewSync.splice(i, 1);
            }
        }
    }

    function assign_supplier() {
        var itemAutoID = $('#assignedSupplier_itemID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'assignSupplierItemSync': assignSupplierItemSync,
                itemAutoID: itemAutoID,
            },
            url: "<?php echo site_url('srm_master/assignItems_supplier_orderInquiry'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    generate_order_itemView();
                    $("#assignSupplier_item_model").modal('hide');
                } else {

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function assign_supplier_view_template() {
       // var itemAutoID = $('#assignedSupplier_itemID').val();
       var inquiryID = $('#inquiryID_master').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'assignSupplierItemSync': assignSupplierViewSync,
                inquiryID:inquiryID
            },
            url: "<?php echo site_url('srm_master/assignItems_supplier_view_orderInquiry'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    generate_order_itemView();
                    $("#assignSupplier_view_item_model").modal('hide');
                } else {

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function delete_supplier_srm(id,det) {
        var row = $(det).closest("tr");
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
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
                    data: {'inquiryDetailID': id},
                    url: "<?php echo site_url('srm_master/delete_supplier_srm'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data["error"] == 1) {
                            myAlert('e', data["message"]);
                        } else if (data['error'] == 0) {
                           // $('#row_' + id).hide();
                           row.hide();
                            myAlert('s', data["message"]);
                        }

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function confirm_order_inquiry() {
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
                var orderID = $('#customer_orderID').val();
                var inquiryID = $('#inquiryID_master').val();
                var deliveryTerms = $('#deliveryTerms').val();
                var confirmed = 1;
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'selectedSupplierSync': selectedSupplierSync,
                        inquiryID: inquiryID,
                        deliveryTerms: deliveryTerms,
                        confirmed: confirmed,
                        orderID: orderID
                    },
                    url: "<?php echo site_url('srm_master/order_inquiry_generate_supplier_rfq'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $('#generated_supplier_rfq_div').removeClass('hide');
                           // generated_supplier_RFQ_View();
                           fetchPage('system/srm/srm_order_inquiry', '', 'Request for Quotation');
                            $('.generate-rfq').removeClass('disabled');
                            $('.btn-wizard').removeClass('disabled');
                            $('[href=#step3]').tab('show');
                            $(document).scrollTop(0);
                        } else {

                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Something Went Wrong ! :)", "error");
                    }
                });
            });
    }

    function confirm_order_inquiry_supplier_view() {
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
                var orderID = $('#customer_orderID').val();
                var inquiryID = $('#inquiryID_master').val();
                var deliveryTerms = $('#deliveryTermsSupplier').val();
                var confirmed = 1;
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'selectedSupplierSync': assignSupplierview,
                        inquiryID: inquiryID,
                        deliveryTerms: deliveryTerms,
                        confirmed: confirmed,
                        orderID: orderID
                    },
                    url: "<?php echo site_url('srm_master/order_inquiry_generate_supplier_view_rfq'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $('#generated_supplier_rfq_div').removeClass('hide');
                           // generated_supplier_RFQ_View(); //system/srm/srm_order_inquiry
                            fetchPage('system/srm/srm_order_inquiry', '', 'Request for Quotation');
                            $('.generate-rfq').removeClass('disabled');
                            $('.btn-wizard').removeClass('disabled');
                            $('[href=#step3]').tab('show');
                            $(document).scrollTop(0);
                        } else {

                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Something Went Wrong ! :)", "error");
                    }
                });
            });
    }

    function draft_order_inquiry() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to Save this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                var inquiryID = $('#inquiryID_master').val();
                var deliveryTerms = $('#deliveryTerms').val();
                var confirmed = 0;
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'selectedSupplierSync': selectedSupplierSync,
                        inquiryID: inquiryID,
                        deliveryTerms: deliveryTerms,
                        confirmed: confirmed
                    },
                    url: "<?php echo site_url('srm_master/order_inquiry_generate_supplier_rfq'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/srm/srm_order_inquiry', '', 'Order Inquiry');

                        } else {

                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function send_rfq_supplier(inquiryMasterID, supplierID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'inquiryMasterID': inquiryMasterID, supplierID: supplierID},
            url: "<?php echo site_url('srm_master/send_rfq_email_suppliers'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    generated_supplier_RFQ_View();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }    

    function load_purchase_requestID() {
        var currency = $('#transactionCurrencyID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("srm_master/load_purchase_requestID"); ?>',
            dataType: 'html',
            data: {currency: currency},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_requestID').html(data);
                $('.select2').select2();
                load_prq_view();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function load_type_view() {
        var type=$('#inquiryType').val();

        if(type=='PRQ'){
            $('.prqbase').removeClass('hidden');
            $('.customerbase').addClass('hidden');
            $('#prqview').empty();
        }else if(type=='Customer'){
            $('.prqbase').addClass('hidden');
            $('.customerbase').removeClass('hidden');
            $('#prqview').empty();
        }else{
            $('.prqbase').addClass('hidden');
            $('.customerbase').addClass('hidden');
            $('#prqview').empty();
        }
    }
    
    
    function load_prq_view() {
       // $('#narration').val(" "); 
        var purchaseRequestID=$('#purchaseRequestID').val();
        var inquiryType = $('#inquiryType').val();
        $('#prqview').empty();
        if(inquiryType =='PRQ' ){
        if (purchaseRequestID) {
            
            // var narration  =  $('#purchaseRequestID option:selected').text();
            // var split= narration.split("|");
            //$('#narration').val(split[2]);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'purchaseRequestID': purchaseRequestID, 'html': true,'approval':1},
                url: "<?php echo site_url('PurchaseRequest/load_pr_conformation_on_srm'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#prqview').html(data);
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
    }
    function recordExists(inquiryID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'inquiryID': inquiryID},
            url: "<?php echo site_url('Srm_master/fetch_inquiryheader'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#transactionCurrencyID').prop('disabled', true);
                    $('#customerID').prop('disabled', true);
                    $('#purchaseRequestID').prop('disabled', true);

                    $("#customer_orderID").multiselect2("disable");
                }else {
                    $('#transactionCurrencyID').prop('disabled', false);
                    $('#purchaseRequestID').prop('disabled', false);
                    $('#customerID').prop('disabled', false);
                    $("#customer_orderID").multiselect2("enable");
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function selectPRQ(){
        var transactionCurrencyID = $('#transactionCurrencyID :selected').val();
        if(transactionCurrencyID !=""){
            prq_list_table();
            $("#prq_list_table_modal").modal({backdrop: "static"});
        }else{
            myAlert('w', 'Please select the currency');
        }
    }

    function prq_list_table(){
      
        var transactionCurrencyID = $('#transactionCurrencyID :selected').val();

        if(transactionCurrencyID){

            Otable = $('#prq_list_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('srm_master/prqViewTable'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {
            
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }

            },
            "aoColumns": [
                {"mData": "purchaseRequestID"},
                {"mData": "purchaseRequestCode"},
                {"mData": "documentDate"},
                {"mData": "narration"},
                {"mData": "expectedDeliveryDate"},
                {"mData": "requestedByName"},
                {"mData": "transactionAmount"},
                {"mData": "action"}

            ],
            "columnDefs": [
            
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "transactionCurrencyID", "value": transactionCurrencyID});

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

        
    }

    function add_prq_id(id,code,text){
        $('#purchaseRequestID').val(id);
        $('#purchaseRequestName').val(code+' | '+text);
        $('#prq_list_table_modal').modal('hide');
        load_prq_view();
    }

    function srm_rfq_document_upload_line_wise(key,id) {
        var formData = new FormData($("#srm_vendor_portal_attachment_uplode_form_"+key)[0]);

        // var cmnt=$('#lineWiseComment_'+id).val();
        // var pr_doc=$('#pr_doc_'+id).val();

        // formData.append('lineWiseComment',cmnt);
        // formData.append('pr_doc',pr_doc);
        $.ajax({
            type: 'post',
            url: "<?php echo site_url('Srm_master/srm_rfq_document_upload_line_wise'); ?>",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
            
                stopLoad();
                        if(data[0]){
                            myAlert(data[0], data[1]);
                           
                        }

                        if(data[0]=="s"){
                            //getCustomerInquiryItem_tableView();
                            $('#old_'+key).addClass('hide');
                            $('#not_submit_'+key).addClass('hide');
                            $('#submit_'+key).html("");
                            
                            var ht = '<a onclick="srm_rfq_document_delete_line_wise('+data[2]+','+key+')" ><span title="Edit" rel="tooltip"  aria-hidden="true" class="glyphicon glyphicon-trash glyphicon-trash-btn color"></span></a>&nbsp;&nbsp;<a target="_blank" href="'+data[3]+'" ><i class="fa fa-download fa-download-btn" aria-hidden="true"></i></a>';
                            $('#submit_'+key).html(ht);
                            // $("#company_request_doc_approve_modal").modal("hide");
                           //fetch_assign_document_table();
                        }
            },
            error: function () {
            // stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
        // return false;
    }

    function srm_rfq_document_delete_line_wise(id,key){

        swal({
            title: "Are you sure?",
            text: "You want to remove this document!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel"
        },


        function () {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data:{'id': id},
                url: "<?php echo site_url('Srm_master/srm_rfq_document_delete_line_wise'); ?>",
                beforeSend: function () {

                },
                success: function (data) {

                    stopLoad();
                    if(data[0]){
                            myAlert(data[0], data[1]);
                           
                        }
                    if(data[0]=="s"){
                       // getCustomerInquiryItem_tableView();
                        $('#old_'+key).addClass('hide');
                        $('#not_submit_'+key).removeClass('hide');
                        $('#submit_'+key).html("");
                          
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                }
            });
        });

    }

    function save_new_supplier(){
        var data = $('#suppliermaster_new_form').serializeArray();
        data.push({'name': 'currency_code', 'value': $('#supplierCurrency option:selected').text()});

        var templateType = $('#templateType').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data:data,
            url: "<?php echo site_url('Srm_master/save_new_supplier_quick'); ?>",
            beforeSend: function () {

            },
            success: function (data) {

                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0]=='s'){
                    $("#new_supplier_model").modal('hide');
                    if(templateType ==2){
                        $("#assignSupplier_view_item_model").modal('hide');
                       // view_supplierViewAssignModel(0,null);
                    }else{
                        $("#assignSupplier_item_model").modal('hide');
                        

                        var itemAutoID1 =  $('#assignedSupplier_itemID').val();
        
                       // view_supplierAssignModel(itemAutoID1,0,null);
                    }
                    
                } 
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }

</script>
