<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('procurement_approval', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$hidepostandard = getPolicyValues('HPOSTS', 'All');
if($hidepostandard==1){
    $type_arr = array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'PR' => $this->lang->line('procurement_approval_purchase_request')/*'Purchase Request'*/,'BQUT' => $this->lang->line('procurement_approval_quotation_order_back_to_back')); //'BCO' => $this->lang->line('procurement_approval_customer_order_back_to_back')
}else{
    $type_arr = array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'Standard' => $this->lang->line('common_standard')/*'Standard'*/, 'PR' => $this->lang->line('procurement_approval_purchase_request')/*'Purchase Request'*/,'BQUT' => $this->lang->line('procurement_approval_quotation_order_back_to_back'), 'LOG' => 'Logistic'); //'BCO' => $this->lang->line('procurement_approval_customer_order_back_to_back')
}

$currency_arr = all_currency_new_drop();
$supplier_arr = all_supplier_drop(true, 1);
$supplier_arr_master = array('' => 'Select Supplier');
$sold_arr = sold_to();
$ship_arr = ship_to();
$projectExist = project_is_exist();
$invoice_arr = invoice_to();
$umo_arr = array('' => $this->lang->line('common_select_uom')/*'Select UOM'*/);
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();
$frequency_arr = all_frequency_list_drop(true);
$location_arr = all_delivery_location_drop_active();
$location_arr_default = default_delivery_location_drop();
$owners=load_employee_drop();
$incoterms_arr = all_incoterms_drop(true);
$transaction_total = 100;
$budegtControl = getPolicyValues('BDC', 'All');
$showPurchasePrice = getPolicyValues('SPP', 'All');
$showItemHistory = getPolicyValues('SPPHI', 'All');

$addowner = getPolicyValues('POOW', 'All');
if ($showPurchasePrice == ' ' || $showPurchasePrice == null || empty($showPurchasePrice)) {
    $showPurchasePrice = 0;
}

$approved_qut_list = get_quotation_detail_status(3);

$pID = $this->input->post('page_id');
if ($pID != '') {
    $grvAutoid = $pID;
    $Documentid = 'PO';
    $supplieridcurrentdoc = all_supplier_drop_isactive_inactive($pID, $Documentid);
    if ($supplieridcurrentdoc['isActive'] == 0) {
        $supplier_arr[trim($supplieridcurrentdoc['supplierAutoID'] ?? '')] = (trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') ? trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') . ' | ' : '') . trim($supplieridcurrentdoc['supplierName'] ?? '') . (trim($supplieridcurrentdoc['supplierCountry'] ?? '') ? ' | ' . trim($supplieridcurrentdoc['supplierCountry'] ?? '') : '');

    }

    $approved_qut_list = get_quotation_detail_status(3,$supplieridcurrentdoc['contractAutoID']);

}
$createmasterrecords = getPolicyValues('CMR', 'All');
$activeIncotermsPolicy = getPolicyValues('AITS', 'All');
$activeRetensionPolicy = getPolicyValues('ACRT', 'All');
$blanketPoEnable = getPolicyValues('BPOE', 'All');

$country = load_country_drop();
$gl_code_arr = supplier_gl_drop();
$currncy_arr = all_currency_new_drop();
$country_arr = array('' => $this->lang->line('common_select_country'));/*Select Country*/
$customerCategory = party_category(2);
$taxGroup_arr = supplier_tax_groupMaster();
$customer_order_btb = get_customer_order(1);
$itemList = fetch_item_data_by_company(1);

$modeOfPayment = get_mode_of_travel(null,1);
$isbacktoback = 1;

if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
//item master creation elements start//

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$main_category_arr = all_main_category_drop();
$revenue_gl_arr = all_revenue_gl_drop();
$cost_gl_arr = all_cost_gl_drop();
$asset_gl_arr = all_asset_gl_drop();
$uom_arr = all_umo_new_drop();
$stock_adjustment = stock_adjustment_control_drop();
$fetch_cost_account = fetch_cost_account();
$fetch_dep_gl_code = fetch_gl_code(array('masterCategory' => 'PL', 'subCategory' => 'PLE'));
$fetch_disposal_gl_code = fetch_gl_code(array('masterCategory' => 'PL'));
$ware_house_binlocations = companyWarehouseBinLocations();
$companyBinLocations = companyBinLocations();
$secondaryUOM = getPolicyValues('SUOM', 'All');
//item master creation elements end//

$group_based_tax = getPolicyValues('GBT', 'All');
$current_stock_po = getPolicyValues('SCP', 'All');

$assignCatPO = getPolicyValues('ECPO', 'All');
$warehouseSelectPO = getPolicyValues('WSPO', 'All');

?>
<style>
    .chkboxlabl {
        border: 1px solid #ccc;
        padding: 10px;
        margin: 0 0 10px;
        display: block;
        font-weight: normal;
    }

    .chkboxlabl:hover {
        background: #eee;
        cursor: pointer;
    }

    .pulling-based-li {
        background: #547698;
    }

    .pulling-based-li > a {
        color: #ffffff !important;
    }

    .nav > li.pull-li > a:hover {
        color: #444 !important;
        cursor: pointer;
        background: #d4d3d3 !important
    }

</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

<div class="steps">
  <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
    <span class="step__icon"></span>
    <span class="step__label"><?php echo $this->lang->line('procurement_approval_purchase_order_header'); ?></span>
  </a>
  <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_po_detail_table()" data-toggle="tab">
    <span class="step__icon"></span>
    <span class="step__label"><?php echo $this->lang->line('procurement_approval_purchase_order_detail'); ?></span>
  </a>
  <a class="step-wiz step--incomplete step--inactive btn-wizard hide" href="#step3" onclick="fetch_cost_addon_po()" data-toggle="tab" id="po_addon">
    <span class="step__icon"></span>
    <span class="step__label"><?php echo 'Extra Cost'; ?></span>
  </a>
  <a class="step-wiz step--incomplete step--inactive btn-wizard hide" href="#step5" onclick="fetch_version_po()" data-toggle="tab" id="version_tab">
    <span class="step__icon"></span>
    <span class="step__label"><?php echo 'Version'; ?></span>
  </a>
  <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step4" onclick="load_conformation();" data-toggle="tab">
    <span class="step__icon"></span>
    <span class="step__label"><?php echo $this->lang->line('procurement_approval_purchase_order_confirmation'); ?></span>
   </a>
</div>


   
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="purchase_order_form"'); ?>
        <input type="hidden" id="rcmApplicable" name="rcmApplicable" value="0">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="purchaseOrderType">
                        <?php echo $this->lang->line('common_type'); ?><!--Type--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('purchaseOrderType', $type_arr, 'Standard', 'class="form-control select2" id="purchaseOrderType" onchange="change_order_type(this)" required'); ?>
                </div>
            </div>
            <?php
           
            if ($assignCatPO ==1 ) { ?>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="itemCatType">
                        Category </label>
                        <select name="itemCatType" class="form-control select2 select2-hidden-accessible" id="itemCatType" required="">
                            <option value="" selected="selected">Select Category</option>
                            <option value="1">Inventory</option>
                            <option value="2">Service</option>
                            <option value="3">Fixed Assets</option>
                            <option value="4">Non Inventory</option>
                        </select>
                </div>
            </div>
            <?php } ?>
            <div class="form-group col-sm-4">

                <?php
                $str = '';
                if ($projectExist == 1) {
                    $str = 'onchange="load_segmentBase_projectID()" ';
                } ?>
                <label for="segment">
                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default, 'class="form-control select2" id="segment" required ' . $str); ?>
            </div>
            <div class="form-group col-sm-4">
                <div class="form-group">
                    <label for="referenceNumber"> <?php echo $this->lang->line('common_reference'); ?><!--Reference-->
                        # </label>
                    <input type="text" class="form-control" id="referenceNumber" name="referenceNumber"
                           placeholder="<?php echo $this->lang->line('common_reference'); ?> #"><!--Reference-->
                    
                </div>
            </div>
        </div>
        <div class="row">
            <?php if ($createmasterrecords == 1) { ?>
                <div class="form-group col-sm-4">
                    <label for="supplierPrimaryCode">
                        <?php echo $this->lang->line('common_supplier'); ?><!--Supplier--> <?php required_mark(); ?></label>
                    <div class="input-group">
                        <div id="div_supplier_drop">

                            <?php echo form_dropdown('supplierPrimaryCode', $supplier_arr_master, '', 'class="form-control select2" id="supplierPrimaryCode" onchange="fetch_supplier_currency_by_id(this.value)"'); ?>
                        </div>
                        <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Supplier" rel="tooltip"
                                onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                </div>
            <?php } else { ?>
                <div class="form-group col-sm-4">
                    <label for="supplierPrimaryCode">
                        <?php echo $this->lang->line('common_supplier'); ?><!--Supplier--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('supplierPrimaryCode', $supplier_arr, '', 'class="form-control select2" id="supplierPrimaryCode" onchange="fetch_supplier_currency_by_id(this.value)"'); ?>
                </div>
            <?php } ?>
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID">
                    <?php echo $this->lang->line('common_currency'); ?><!--Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '', 'class="form-control select2" id="transactionCurrencyID" onchange="currency_validation(this.value,\'PO\')" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="">
                    <?php echo $this->lang->line('procurement_approval_po_date'); ?><!--PO Date--> <?php required_mark(); ?></label>

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="POdate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="POdate" class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <label>
                    <?php echo $this->lang->line('procurement_approval_expected_delivery_date'); ?><!--Expected Delivery Date--> <?php required_mark(); ?></label>

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="expectedDeliveryDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="expectedDeliveryDate" class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4" style="display: none;">
                <label for="soldToAddressID">
                    <?php echo $this->lang->line('procurement_approval_sold_to'); ?><!--Sold To--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('soldToAddressID', $sold_arr, '3', 'class="form-control select2" id="soldToAddressID" '); ?>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="shippingAddressID">
                        <?php echo $this->lang->line('procurement_approval_ship_to'); ?><!--Ship To--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('shippingAddressID', $ship_arr, '1', 'class="form-control select2" id="shippingAddressID" required onchange="fetch_ship_to(this.value)"'); ?>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="shippingAddressDescription">
                        <?php echo $this->lang->line('procurement_approval_shipping_address'); ?><!--Shipping Address--> </label>
                    <textarea class="form-control" id="shippingAddressDescription" name="shippingAddressDescription"
                              rows="2"></textarea>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group " style="display: none;">
                    <label for="delivery_terms">
                        <?php echo $this->lang->line('procurement_approval_invoice_to'); ?><!--Invoice To--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('invoiceToAddressID', $invoice_arr, '2', 'class="form-control select2" id="invoiceToAddressID"'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="contactperson">
                    <?php echo $this->lang->line('procurement_approval_contact_person'); ?><!--Contact Person--></label>
                <input type="text" class="form-control" id="contactperson" name="contactperson"
                       placeholder="<?php echo $this->lang->line('procurement_approval_contact_person'); ?>">
                <!--Contact Person-->
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="creditPeriod">
                        <?php echo $this->lang->line('common_contact_number'); ?><!--Contact Number--></label>

                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                        <input type="text" class="form-control" id="contactnumber" name="contactnumber"
                               placeholder="<?php echo $this->lang->line('common_contact_number'); ?>">
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="creditPeriod">
                        <?php echo $this->lang->line('procurement_approval_credit_period'); ?><!--Credit Period--> (
                        <?php echo $this->lang->line('common_days'); ?><!--Days--> )</label>

                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-qrcode" aria-hidden="true"></i></div>
                        <input type="text" class="form-control" id="creditPeriod" name="creditPeriod" placeholder="00">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if ($projectExist == 1) { ?>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="projectID">
                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </label>
                        <div id="div_projectID">
                            <select name="projectID" id="projectID" class="form-control select2">
                                <option value="">
                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                            </select>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="deliveryTerms">
                        <?php echo $this->lang->line('procurement_approval_delivery_terms'); ?><!--Delivery Terms--></label>
                    <textarea class="form-control" id="deliveryTerms" name="deliveryTerms" rows="2"></textarea>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="deliveryTerms">  <?php echo $this->lang->line('procurement_penalty_terms'); ?></label>
                    <!--Penalty Terms-->
                    <textarea class="form-control" id="penaltyTerms" name="penaltyTerms" rows="2"></textarea>
                </div>
            </div>
            <div class="col-sm-4 isRcmApplicableYN">
                <div class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('procurment_document_tax_type'); ?><!--Document Tax Type--> <?php required_mark(); ?></label>
                    <select name="documentTaxType" class="form-control" id="documentTaxType">
                        <option value="0">General Tax</option>
                        <option value="1">Lineby Tax</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group ">
                    <label for="narration">
                        <?php echo $this->lang->line('procurement_approval_narration'); ?><!--Narration--> </label>
                    <textarea class="form-control" id="narration" name="narration" rows="2"></textarea>
                </div>
            </div>

            <div class="form-group col-sm-4 hide" id="customer_drop">
                <label for="soldToAddressID">
                    <?php echo $this->lang->line('procurement_approval_customer_order'); ?><!--Sold To--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('customer_order_id', $customer_order_btb, '', 'class="form-control select2" id="customer_order_id" '); ?>
            </div>

            <div class="form-group col-sm-4 hide" id="qut_drop">
                <label for="soldToAddressID">
                    <?php echo $this->lang->line('procurement_approval_quotation_order'); ?><!--Sold To--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('contractAutoID', $approved_qut_list, '', 'class="form-control select2" id="contractAutoID" onchange="get_quotation_detail(this)"'); ?>
            </div>

        </div>
        <div class="row">
        <div class="col-sm-4 reverse-charge-mechanism">
                <div class="form-group ">
                    <label for="narration">Reverse Charge Mechanism</label>
                    <div class="skin skin-square">
                        <div class="skin-section extraColumns">
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
       
        <div class="row">
            <?php if($activeIncotermsPolicy==1){ ?>
            <div class="form-group col-sm-4">
                <label>Incoterms</label><!--Notes-->
                <?php echo form_dropdown('incoterms', $incoterms_arr, '', 'class="form-control select2" id="incoterms"'); ?>
            </div>
            <?php } ?>

            <?php if($activeRetensionPolicy==1){ ?>
                <div class="form-group col-sm-4">
                    <label>Retension Date</label><!--Notes-->
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="retension_date" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                            value="<?php echo $current_date; ?>" id="retension_date" class="form-control" required>
                    </div>
                </div>

                <div class="form-group col-sm-4">
                    <label>Retension percentage</label><!--Notes-->
                    <input type="text" class="form-control" id="retension_percentage" name="retension_percentage" placeholder="00">
                </div>
            <?php } ?>
        </div>
        
            <?php if ($blanketPoEnable == 1) { ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                        <label class="col-sm-4 control-label">Blanket PO</label>
                        <div class="col-sm-4" style="top: 5px;">
                            <input type="checkbox" value="" id="blanketpo" name="blanketpo">
                            <input type="hidden" value="0" id="blanketpo_val" name="blanketpo_val">
                        </div>
                    
                </div>
            </div>
            
            <div class="row hide" id="blanket_Po_view">
                <div class="col-sm-4">
                    <div class="form-group " >
                        <label for="delivery_terms">
                            Frequency <?php required_mark(); ?></label>
                        <?php echo form_dropdown('frequencyID', $frequency_arr, '', 'class="form-control select2" id="frequencyID"'); ?>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="creditPeriod">Frequency Amount</label>
                
                        <div class="input-group">
                           
                            <input type="text" class="form-control" id="frequencyAmount" name="frequencyAmount" placeholder="00">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="row">
            <?php if($warehouseSelectPO==1){ ?>
            <div class="form-group col-sm-4">
            <label for="">Warehouse Location</label>
                <input class="hidden" name="mfqWarehouseAutoID" id="mfqWarehouseAutoID">
                <!--Delivery Location-->
                <?php echo form_dropdown('location', $location_arr, $location_arr_default, 'class="form-control select2" id="location" onchange=""'); ?>
            </div>
            <?php } ?>
              
          
             <div class="col-sm-4">
                <div class="form-group" id="logisticOption">
                    <label for="narration">Logistic</label>
                    <div class="skin skin-square">
                        <div class="skin-section extraColumns">
                            <input id="logisticYN" type="checkbox"
                                   data-caption="" class="columnSelected" name="logisticYN" value="1">
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($addowner==1){ ?>
              <div class="form-group col-sm-4">
              <label for="">Owner</label>
                  <?php echo form_dropdown('owner', $owners, '', 'class="form-control select2" id="owner" onchange=""'); ?>
              </div>
            <?php } ?>

        </div>

        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>
                    Mode of Shipment </label>
                    <?php echo form_dropdown('modeofship', $modeOfPayment , '', 'class="form-control select2" id="modeofship" onchange=""'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-12">
            <label for="paymentTerms">
                    <?php echo $this->lang->line('procurement_approval_payment_terms'); ?></label>
                <textarea class="form-control PAYTERMS" rows="6" name="paymentTerms" id="paymentTerms"></textarea>
                <br>
                <button class="btn btn-primary" type="button" onclick="open_all_notes(11)"><i class="fa fa-bookmark"
                                                                                            aria-hidden="true"></i>
                </button>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-12">
                <label><?php echo $this->lang->line('common_notes'); ?> </label><!--Notes-->
                <textarea class="form-control Note" rows="6" name="Note" id="Note"></textarea>
                <br>
                <button class="btn btn-primary" type="button" onclick="open_all_notes(10)"><i class="fa fa-bookmark"
                                                                                            aria-hidden="true"></i>
                </button>
                <hr>
            </div>
        </div>


        <div class="row">
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg" type="submit">
                <?php echo $this->lang->line('common_save_and_next'); ?><!--Save & Next--></button>
        </div>
        </form>
    </div>

    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('procurement_approval_item_details'); ?><!--Item Detail--> </h4>
                <div class="rcmStatus hide">
                    <span class="label label-danger" style="font-size: 9px;" title="Not Received" rel="tooltip">Reverse Charge Mechanism Activated</span>
                </div>


                <h4></h4></div>
            <div class="col-md-4">
                <button type="button" id="add_purchase_detail" onclick="purchase_order_detail_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                </button>
                <button type="button" id="add_po_addon" onclick="purchase_order_detail_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                </button>
            </div>
        </div>
        <br>

        
        <table class="table table-bordered table-striped table-condensed" id="po_table_details">
            <thead>
            <tr>
                    <th colspan="7">
                        <?php echo $this->lang->line('procurement_approval_item_details'); ?></th>
                     <th colspan="5"><?php echo 'Amounts'; ?> <span class="currency">(LKR)</span> </th>
           
                    <th>&nbsp;</th>
            </tr>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 7%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>

             
                <th id="pr_code" style="min-width: 7%">Purchase Request<!--Purchase Request Code-->                   
                </th>
                <th style="min-width: 21%" class="text-left">
                    <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                <th style="min-width: 5%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_discount'); ?><!--Discount--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_net_unit_price'); ?><!--Net Unit Price--></th>                
                <th style="min-width: 12%" class="backtoback"><?php echo $this->lang->line('common_net_total'); ?><!--Net Total--></th>

                <th style="min-width: 12%"><?php  echo $this->lang->line('common_tax'); ?> 
                <th style="min-width: 5%"><?php  echo $this->lang->line('common_tax').' '.$this->lang->line('common_value'); ?> 
                
                <th style="min-width: 10%" class="backtoback"><?php echo 'Grand Total'; ?><!--Net Total--></th>
                
                <!-- <th style="min-width: 10%" class="backtoback hide">%</th>
                <th style="min-width: 5%" class="backtoback hide">Value</th> -->
               
                <!-- <th style="min-width: 10%" class="lintax notbacktoback hide"><?php // echo $this->lang->line('common_tax'); ?></th> -->
                <!-- <th style="min-width: 10%" class="lintax notbacktoback hide"> -->
                <th style="min-width: 12%" class="notbacktoback hide"><?php echo $this->lang->line('common_net_total'); ?><!--Net Total--></th>
            
                
                
                <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
            </thead>
            <tbody id="table_body">
            <tr class="danger">
                <td colspan="10" class="text-center"><b>
                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
            </tr>
            </tbody>
            <tfoot id="table_tfoot">

            </tfoot>
        </table>

        <table class="table table-bordered table-striped table-condensed" id="logistic_po_table_details">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 7%">PO Code<!--Code--></th>
                <th style="min-width: 5%">Logistic Amount</th>
                <th style="min-width: 10%">Logistic Balance</th>
                <th style="min-width: 10%">Matching Amount</th>
                <th style="min-width: 10%">Actual Logistic Amount</th>
                <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
            </tr>
            </thead>
            <tbody id="logistic_table_body">
            <tr class="danger">
                <td colspan="10" class="text-center"><b>
                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
            </tr>
            </tbody>
            <tfoot id="table_tfoot">

            </tfoot>
        </table>



        <br>
        <div class="row discount_general_view">
            <div class="col-md-5">
                <label for="exampleInputName2" id="discount_tot">
                    <?php echo $this->lang->line('procurement_discount_for') ?><!--Discount for--> </label>

                <form class="form-inline" id="discount_form">
                    <div class="form-group">
                        <input type="text" class="form-control" id="disctype" name="disctype" disabled value="Discount">
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control number" id="discpercentage" name="discpercentage"
                                   style="width: 80px;" onkeyup="cal_disc(this.value)">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control number" id="disc_amount" name="disc_amount"
                               style="width: 100px;" onkeypress="return validateFloatKeyPress(this,event);"
                               onkeyup="cal_disc_amount(this.value)">
                    </div>
                    <button type="submit" class="btn btn-primary" id="discsubmitbtn"><i class="fa fa-plus"></i></button>
                </form>
            </div>
            <div class="col-md-7">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>
                            <?php echo $this->lang->line('common_discount_percentagae'); ?><!--Discount Percentage--></th>
                        <th><?php echo $this->lang->line('common_discount_amount'); ?><!--Discount Amount--> <span
                                    class="currency">(LKR)</span></th>
                        <th style="width: 75px !important;">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="discount_table_body_recode">

                    </tbody>
                    <tfoot id="discount_table_footer">

                    </tfoot>
                </table>
            </div>
        </div>
        <br>
        <div class="row" id="genTax">
            <div class="col-md-5">
                <label for="exampleInputName2" id="tax_tot">
                    <?php echo $this->lang->line('procurement_tax_applicable_amount') ?><!--Tax Applicable Amount-->
                    <span class="taxAmount_tot"> (0.00) </span> </label>
                <input type="hidden" id="taxaplcamnt">
                <form class="form-inline" id="tax_form">
                    <div class="form-group">

                        <?php $taxDrop = ($group_based_tax == 1 ? all_tax_formula_drop_groupByTax() : all_tax_formula_drop()) ?>

                        <?php echo form_dropdown('text_type', $taxDrop, '', 'class="form-control" id="text_type" required style="width: 150px;"'); ?>


                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                </form>
            </div>

            <div class="col-md-7">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_details'); ?><!--Details--></th>
                        <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                    class="currency">(LKR)</span></th>
                        <th style="width: 75px !important;">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="tax_table_body_recode">

                    </tbody>
                    <tfoot id="tax_table_footer">

                    </tfoot>
                </table>
            </div>
        </div>


        <hr>
        <hr>
          
    </div>
    
    <div id="step3" class="tab-pane">
        <button type="button" onclick="item_extra_cost_modal()" class="btn btn-primary pull-right"><i
                class="fa fa-plus"></i><?php echo $this->lang->line('sales_markating_transaction_add_extra_cost');?> <!--Add Item-->
        </button>

        <div id="cost_addon_po"></div>
    </div>

    <div id="step5" class="tab-pane">
      <div class="row">

      </div>

        <div class="table-responsive">
            <table id="purchase_order_table_version" class="<?php echo table_class() ?>">
                <thead>
                <tr>
                    <th style="min-width: 2%">#</th>
                    <th style="min-width: 15%">
                        <?php echo $this->lang->line('procurement_approval_po_number'); ?><!--PO Number--></th>
                    <th style="min-width: 40%"><?php echo $this->lang->line('common_details'); ?><!--Details--></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('common_total_value'); ?><!--Total Value--></th>
                    
                    <th style="min-width: 13%;min-width:200px"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="step4" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="purchaseOrder_attachment_label">
                <?php echo $this->lang->line('procurement_modal_title'); ?><!--Modal title--></h4>
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--> </th>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--> </th>
                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--> </th>
                        <th><?php echo $this->lang->line('common_action'); ?><!--Action--> </th>
                    </tr>
                    </thead>
                    <tbody id="purchaseOrder_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--> </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default-new size-lg prev">
                <?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
            <button class="btn btn-primary-new size-lg " onclick="save_draft()">
                <?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
            <?php
            if ($budegtControl == 1) {
                ?>
                <button class="btn btn-success-new size-lg submitWizard" onclick="confirmation_Inventory_check()">
                    <?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
                <?php
            } else {
                ?>
                <button class="btn btn-success-new size-lg submitWizard" onclick="confirmation()">
                    <?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
                <?php
            }
            ?>
        </div>
    </div>

</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div aria-hidden="true" role="dialog" tabindex="-1" id="purchase_order_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 99%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"
                    style="width: 98%;"><?php echo $this->lang->line('procurement_approval_add_Item_detail'); ?>
                    <button class="btn btn-primary pull-right" type="button" onclick="add_item_master()">Create New
                        Item
                    </button>
                </h5>

            </div>
            <div class="modal-body">
                <form role="form" id="purchase_order_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;">
                                <?php echo $this->lang->line('procurement_approval_item_code'); ?><!--Item Code --><?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                            <?php if ($current_stock_po == 1) { ?>
                                <th style="width:150px;">
                                    <?php echo $this->lang->line('procurement_current_stock'); ?><!--Current Stock--></th>
                            <?php } ?>
                            <th style="width: 100px;">PO
                                <?php echo $this->lang->line('common_qty'); ?><!--PO Qty--> <?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_unit_price'); ?><!--Unit Price--> <span
                                        class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th colspan="2" style="width: 50px;">
                                <?php echo $this->lang->line('common_discount'); ?><!--Discount--> %
                            </th>
                            <th style="width: 100px;">
                                <?php echo $this->lang->line('common_net_unit_price'); ?><!--Net Unit Price--></th>
                            <th class="lintax" style="width: 10%"><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                            <th class="lintax" style="width: 5%">
                                <?php echo $this->lang->line('procurement_tax_amount'); ?><!--Tax Amount--></th>
                            <th style="width: 100px;">
                                <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control f_search"
                                       name="search[]"
                                       placeholder="item id , description, part no or secondary code "
                                       title="search by itemDescription - itemSystemCode -  partNo - secondary ItemCode"
                                       id="f_search_1">
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                            </td>
                            <td><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown"  required'); ?></td>
                            <?php if ($current_stock_po == 1) { ?>
                                <td>
                                    <input type="text" name="currentstock[]" value="0" onfocus="this.select();"
                                           class="form-control currentstock number" readonly/>
                                </td>
                            <?php } ?>
                            <td><input type="text" name="quantityRequested[]" value="0"
                                       onchange="load_line_tax_amount(this)" onkeyup="change_qty(this)"
                                       class="form-control number quantityRequested" onfocus="this.select();" required>
                            </td>
                            <td><input type="text" name="estimatedAmount[]" value="0"
                                       onkeyup="change_amount(this)"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number estimatedAmount" onchange="load_line_tax_amount(this)"
                                       onfocus="this.select();">

                                       <?php if($showItemHistory ==1){ ?>
                                       <a class ="item_history" id ="item_history_1"><span style="" title="view Item History" class="glyphicon glyphicon-info-sign"></span></a>
                                        <?php } ?>
                            </td>
                            <td style="width: 100px">
                                <div class="input-group">
                                    <input type="text" name="discount[]" onchange="load_line_tax_amount(this)"
                                           placeholder="0"
                                           class="form-control number discount" value="0"
                                           onkeyup="cal_discount(this.value, this)" onfocus="this.select();">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 8%;"><input type="text" onchange="load_line_tax_amount(this)"
                                                          name="discount_amount"
                                                          onkeyup="cal_discount_amount(this.value, this)"
                                                          onkeypress="return validateFloatKeyPress(this,event)"
                                                          class="form-control number discount_amount" value="0"
                                                          onfocus="this.select();">
                            </td>
                            <td>&nbsp;<span class="net_unit_cost pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>

                            <td class="lintax">
                                <?php echo form_dropdown('text_type[]', all_tax_formula_drop(), '', 'class="form-control text_type" style="width: 174px;" onchange="load_line_tax_amount(this)" '); ?>
                            </td>

                            <td class="lintax"><span class="linetaxamnt pull-right"
                                                     style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                            </td>
                            <td>&nbsp;<span class="net_amount pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td><textarea class="form-control" rows="1" name="comment[]"
                                          placeholder="Item Comment..."></textarea></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="savePurchaseOrderDetails()">
                    <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" tabindex="-1" id="purchase_order_detail_edit_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">
                    <?php echo $this->lang->line('procurement_approval_edit_item_detail'); ?><!--Edit Item Detail--></h5>
            </div>
            <div class="modal-body">
                <form role="form" id="purchase_order_detail_edit_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="po_detail_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;">
                                <?php echo $this->lang->line('procurement_approval_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                            <?php if ($current_stock_po == 1) { ?>
                                <th style="width:150px;">
                                    <?php echo $this->lang->line('procurement_current_stock'); ?><!-- Current Stock --></th>
                            <?php } ?>
                            <th style="width: 100px;">PO
                                <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_unit_price'); ?><!--Unit Price--> <span
                                        class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                            <th colspan="2" style="width: 50px;">
                                <?php echo $this->lang->line('common_discount'); ?><!--Discount--> %
                            </th>
                            <th style="width: 100px;">
                                <?php echo $this->lang->line('common_net_unit_price'); ?><!--Net Unit Price--></th>
                            <th class="lintax"><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                            <th class="lintax">
                                <?php echo $this->lang->line('procurement_tax_amount'); ?><!--Tax Amount--></th>
                            <th style="width: 100px;">
                                <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--></th>
                            <th style="width: 150px;">
                                <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" id="search_edit"
                                       class="form-control" name="search"
                                       placeholder="Item ID, Item Description...">
                                <input type="hidden" id="itemAutoID_edit" class="form-control" name="itemAutoID">
                            </td>
                            <td><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control" id="UnitOfMeasureID_edit"  required'); ?></td>
                            <?php if ($current_stock_po == 1) { ?>
                                <td>
                                    <input type="text" name="currentstock[]" value="0" id="currentstock"
                                           onfocus="this.select();"
                                           class="form-control currentstock number" readonly/>
                                </td>
                            <?php } ?>
                            <td><input type="text" onchange="load_line_tax_amount_edit(this)" name="quantityRequested"
                                       id="quantityRequested_edit" value="0"
                                       onkeyup="change_qty_edit()" class="form-control number"
                                       required onfocus="this.select();"><input type="hidden" id="prQtyEdit"></td>
                            <td><input type="text" onchange="load_line_tax_amount_edit(this)" name="estimatedAmount"
                                       value="0" id="estimatedAmount_edit"
                                       placeholder="0.00" onkeyup="change_amount_edit()"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number" onfocus="this.select();"></td>
                            <td style="width: 100px">
                                <div class="input-group">
                                    <input type="text" onchange="load_line_tax_amount_edit(this)" name="discount"
                                           placeholder="0" id="discount_edit"
                                           class="form-control number" value="0"
                                           onkeyup="cal_discount_edit(this.value)" onfocus="this.select();">
                                    <span class="input-group-addon">%</span></div>
                            </td>
                            <td style="width: 8%;"><input type="text" onchange="load_line_tax_amount_edit(this)"
                                                          id="discount_amount_edit" name="discount_amount"
                                                          onkeyup="cal_discount_amount_edit()"
                                                          class="form-control number"
                                                          onkeypress="return validateFloatKeyPress(this,event)"
                                                          value="0" onfocus="this.select();"></td>
                            <td>&nbsp;<span id="net_unit_cost_edit" class="pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>

                            <td class="lintax"><?php echo form_dropdown('text_type', array('' => 'Select Tax'), '', 'class="form-control" id="text_type_edit" style="width: 134px;" onchange="load_line_tax_amount_edit(this)" '); ?></td>

                            <td class="lintax"><span class="pull-right" id="linetaxamnt_edit"
                                                     style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                            </td>
                            <td>&nbsp;<span id="totalAmount_edit" class="net_amount pull-right"
                                            style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td><textarea class="form-control" rows="1" id="comment_edit" name="comment"
                                          placeholder="Item Comment..."></textarea></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_close'); ?></button>
                <button class="btn btn-primary" type="button" onclick="updatePurchaseOrderDetails()">
                    <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="prq_base_modal"  role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 99%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo $this->lang->line('procurement_purchase_request_base'); ?><!--Purchase Request Base--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="box box-widget widget-user-2" style="margin-bottom:0px;">
                            <div class="widget-user-header bg-yellow">
                                <h5>
                                    <?php echo $this->lang->line('procurement_purchase_request'); ?><!--Purchase Request--></h5>

                                <div class="skin skin-square">
                                <div class="skin-section">
                                    <input id="itemSearch" type="checkbox"
                                           data-caption="" class="columnSelected" name="itemSearch" value="1" onchange="load_prq_codes()">
                                    <label for="checkbox">
                                        Search with item
                                    </label>
                                </div>
                                </div>
                                <div class="hide" id="itemSearchdiv">
                                    <label for=""><?php echo $this->lang->line('common_item'); ?><!--Category--></label>
                                    <?php echo form_dropdown('pr_itemAutoID', $itemList, '', 'class="form-control" multiple="multiple" id="pr_itemAutoID" onchange="load_prq_codes()" '); ?>
                                </div>
                               

                            </div>
                            <div class="box-footer no-padding" style="height: 520px;overflow: auto;">
                                <ul class="nav nav-stacked" id="prqcode">


                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <tr class="hide">
                                <th colspan='4'><?php echo $this->lang->line('common_item'); ?><!--Item--></th>
                                <th colspan='2'>
                                    <?php echo $this->lang->line('procurement_requested_item'); ?><!--Requested Item-->
                                    <span
                                            class="currency"> </span></th>
                                <th class="lintaxcols" colspan='5'>
                                    <?php echo $this->lang->line('procurement_ordered_item'); ?><!--Ordered Item -->
                                    <span
                                            class="currency"> </span></th>
                                <th class="lintax" colspan='7'>
                                    <?php echo $this->lang->line('procurement_ordered_item'); ?><!--Ordered Item --><span
                                            class="currency"> </span></th>
                            <tr>
                            <tr>
                                <th>#</th>
                                <th><?php echo 'Purchase Request'; ?><!--Code--></th>
                                <th><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                                <th class="text-left"><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <th><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                                <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                                <th><?php echo $this->lang->line('common_cost'); ?><!--Cost--></th>
                                <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                                <th><?php echo $this->lang->line('common_cost'); ?><!--Cost--></th>
                                <th colspan="2"><?php echo $this->lang->line('common_discount'); ?><!--Discount-->(%)
                                </th>
                                <th class="lintax"><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                                <th class="lintax">
                                    <?php echo $this->lang->line('procurement_tax_amount'); ?><!--Tax Amount--></th>
                                <th><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                                <th style="display: none;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="table_body_pr_detail">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">
                    <?php echo $this->lang->line('common_close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary-new size-lg" onclick="save_prq_base_items()">
                    <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exceeded_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog " role="document" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Budget Amount/Qty exceeded</h4>
            </div>

            <form class="form-horizontal" id="">
                <div class="modal-body">
                    <div id="exceeded_item">
                        <table class="table table-condensed table-bordered" id="itemexceedamnt">
                            <thead>
                            <tr>
                                <th colspan="4" style="text-align: center;">Service / Non Inventory</th>
                            </tr>
                            <tr>
                                <th>GL Code</th>
                                <th>Total Consumption</th>
                                <th>Budget Amount</th>
                                <th>Exceeded Amount
                                    (<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)
                                </th>
                            </tr>
                            </thead>
                            <tbody id="exceeded_item_body">

                            </tbody>
                        </table>
                        <br>
                        <table class="table table-condensed table-bordered" id="itemexceedqty">
                            <thead>
                            <th colspan="4" style="text-align: center;">Inventory</th>
                            <tr>
                                <th>Item Description</th>
                                <th>Total Consumption</th>
                                <th>Maximum Qty</th>
                                <th>Exceeded Qty</th>
                            </tr>
                            </thead>
                            <tbody id="exceeded_item_bodyqty">

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="unlimited_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Un limited Amount or Qty</h4>
            </div>

            <form class="form-horizontal" id="">
                <div class="modal-body">
                    <div id="exceeded_item_unlimited">
                        <div id="itemexceedamnt_unlimited">
                            <span style="color: red;">Note :-</span><h5> Budget has not created for these GL Codes.
                                System will consider budgeted amount for these GL Codes as un limited.</h5>
                            <table class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th>Item Description</th>
                                    <th>GL Code</th>
                                    <th>PO Amount
                                        (<?php echo $this->common_data['company_data']['company_reporting_currency'] ?>)
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="exceeded_item_body_unlimited">

                                </tbody>
                            </table>
                        </div>

                        <br>
                        <div id="itemexceedqty_unlimited">
                            <span style="color: red;">Note :-</span> <h5> Max Qty for following items are assigned as
                                zero. System will consider these items allowed maximum Qty as un limited.</h5>
                            <table class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th>Item Description</th>
                                    <th>PO Qty</th>
                                </tr>
                                </thead>
                                <tbody id="exceeded_item_bodyqty_unlimited">

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                    <button type="button" class="btn btn-primary-new size-lg" onclick="confirmation()">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" id="all_notes_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Notes</h5>
            </div>
            <div class="modal-body">
                <form role="form" id="all_notes_form" class="form-group">
                    <div id="allnotebody">

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_notes()">Add Note</button>
            </div>
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

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="add_itemmaster">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New Item</h4>
            </div>
            <?php echo form_open('', 'role="form" id="itemmaster_form"'); ?>
            <div class="modal-body">
                <div class="col-sm-3" align="" style="padding-left: 0px;">
                    <div class="fileinput-new thumbnail" style="margin-bottom: 4px;width: 200px; height: 150px;">
                        <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg">
                        <input type="file" name="itemImage" id="itemImage" style="display: none;"
                               onchange="loadImage(this)"/>
                    </div>
                    <div class="form-group col-sm-12 no-padding">
                        <div id="barcodeDiv"></div>
                    </div>
                </div>
                <div class="col-md-9" style="padding-left: 0px;">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label>
                                <?php echo $this->lang->line('transaction_main_category'); ?><!--Main Category--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="load_sub_cat(),validate_itempull(this.value,1);"'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label>
                                <?php echo $this->lang->line('transaction_sub_category'); ?><!--Sub Category--> <?php required_mark(); ?></label>
                            <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                                    onchange="load_sub_sub_cat(),load_gl_codes(),validate_itempull(this.value,2);">
                                <option value="">
                                    <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                            </select>
                        </div>
                        <div class="form-group col-sm-4">
                            <label>
                                <?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?><!--Sub Sub Category--> </label>
                            <select name="subSubCategoryID" id="subSubCategoryID" class="form-control searchbox">
                                <option value="">
                                    <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label><?php echo $this->lang->line('erp_item_master_short_description'); ?><?php required_mark(); ?></label>
                            <!--Short Description-->
                            <input type="text" class="form-control" id="itemName" name="itemName">
                        </div>
                        <div class="form-group col-sm-8">
                            <label><?php echo $this->lang->line('erp_item_master_long_description'); ?><?php required_mark(); ?></label>
                            <!--Long Description-->
                            <input type="text" class="form-control" id="itemDescription" name="itemDescription">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">
                                <?php echo $this->lang->line('erp_item_master_secondary_code'); ?><!--Secondary Code--> <?php required_mark(); ?></label>
                            <input type="text" class="form-control" id="seconeryItemCode" name="seconeryItemCode">
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">
                                <?php echo $this->lang->line('transaction_unit_of_measure'); ?><!--Unit of Measure--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('defaultUnitOfMeasureID', $uom_arr, 'Each', 'class="form-control" id="defaultUnitOfMeasureID" onchange="validate_itempull(this.value,3);" required'); ?>
                        </div>
                        <?php
                        if ($secondaryUOM == 1) {
                            ?>
                            <div class="form-group col-sm-4">
                                <label for="">Secondary Unit of Measure <?php required_mark(); ?></label>
                                <?php echo form_dropdown('secondaryUOMID', $uom_arr, 'Each', 'class="form-control" id="secondaryUOMID" onchange="validate_itempull(this.value,4);"'); ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="form-group col-sm-4">
                            <label for="">
                                Purchasing Price</label>

                            <div class="input-group">
                                <div
                                        class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                                <input type="text" step="any" class="form-control number"
                                       id="companyLocalPurchasingPrice"
                                       name="companyLocalPurchasingPrice" value="0">
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="">
                                <?php echo $this->lang->line('transaction_selling_price'); ?><!--Selling Price--> <?php required_mark(); ?></label>

                            <div class="input-group">
                                <div
                                        class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                                <input type="text" step="any" class="form-control number" id="companyLocalSellingPrice"
                                       name="companyLocalSellingPrice" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for=""><?php echo $this->lang->line('transaction_barcode'); ?><!--Barcode--></label>
                        <input type="text" class="form-control" id="barcode" name="barcode"
                               onchange="validateBarCode(this.value)">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for=""><?php echo $this->lang->line('transaction_part_no'); ?><!--Part No--> </label>
                        <input type="text" class="form-control" id="partno" name="partno">
                    </div>
                    <div class="form-group col-sm-2" id="cls_maximunQty">
                        <label for="">
                            <?php echo $this->lang->line('transaction_maximum_qty'); ?><!--Maximum Qty--></label>
                        <input type="text" class="form-control number" id="maximunQty" name="maximunQty">
                    </div>
                    <div class="form-group col-sm-2" id="cls_minimumQty">
                        <label for="">
                            <?php echo $this->lang->line('transaction_minimum_qty'); ?><!--Minimum Qty--></label>
                        <input type="text" class="form-control number" id="minimumQty" name="minimumQty">
                    </div>
                    <div class="form-group col-sm-2" id="cls_reorderPoint">
                        <label for="">
                            <?php echo $this->lang->line('erp_item_master_recorder_level'); ?><!--Reorder Level--></label>
                        <input type="text" class="form-control number" id="reorderPoint" name="reorderPoint">
                    </div>

                </div>
                <div class="row" id="inventry_row_div">
                    <div class="form-group col-sm-4">
                        <label for="">
                            <?php echo $this->lang->line('erp_item_master_revenue_gl_code'); ?><!--Revenue GL Code --></label>
                        <?php echo form_dropdown('revanueGLAutoID', $revenue_gl_arr, '', 'class="form-control select2" id="revanueGLAutoID" onchange="validate_itempull(this.value,5);" '); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">
                            <?php echo $this->lang->line('erp_item_master_cost_gl_code'); ?><!--Cost GL Code--></label>
                        <?php echo form_dropdown('costGLAutoID', $cost_gl_arr, '', 'class="form-control select2" id="costGLAutoID" onchange="validate_itempull(this.value,6);" '); ?>
                    </div>
                    <div class="form-group col-sm-4" id="assetGlCode_div">
                        <label for="">
                            <?php echo $this->lang->line('erp_item_master_asset_gl_code'); ?><!--Asset GL Code--></label>
                        <?php echo form_dropdown('assteGLAutoID', $asset_gl_arr, $this->common_data['controlaccounts']['INVA'], 'class="form-control select2" id="assteGLAutoID" onchange="validate_itempull(this.value,7);"'); ?>
                    </div>


                </div>
                <div class="row hide" id="fixed_row_div">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">
                                <?php echo $this->lang->line('erp_item_master_cost_account'); ?><!--Cost Account--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('COSTGLCODEdes', $fetch_cost_account, '', 'class="form-control form1 select2" id = "COSTGLCODEdes" onchange="validate_itempull(this.value,9);"'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">
                                <?php echo $this->lang->line('erp_item_master_acc_dep_gl_code'); ?><!--Acc Dep GL Code --><?php required_mark(); ?></label>
                            <?php echo form_dropdown('ACCDEPGLCODEdes', $fetch_cost_account, '', 'class="form-control form1 select2" id = "ACCDEPGLCODEdes"'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">
                                <?php echo $this->lang->line('erp_item_master_dep_gl_code'); ?><!--Dep GL Code--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('DEPGLCODEdes', $fetch_dep_gl_code, '', 'class="form-control form1 select2" id = "DEPGLCODEdes" '); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">
                                <?php echo $this->lang->line('erp_item_master_disposal_gl_code'); ?><!--Disposal GL Code--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('DISPOGLCODEdes', $fetch_disposal_gl_code, '', 'class="form-control form1 select2" id = "DISPOGLCODEdes"'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="form-group" id="stockadjustment">
                                <label for="">Stock Adjustment Control</label>
                                <?php echo form_dropdown('stockadjust', $stock_adjustment, '', 'class="form-control form1 select2" id="stockadjust" onchange="validate_itempull(this.value,8);"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">
                                <?php echo $this->lang->line('erp_item_master_is_active'); ?><!--isActive--></label>

                            <div class="skin skin-square">
                                <div class="skin-section extraColumns">
                                    <input id="checkbox_isActive" type="checkbox"
                                           data-caption="" class="columnSelected" name="isActive" value="1" checked>
                                    <label for="checkbox">
                                        &nbsp;
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">
                                <?php echo $this->lang->line('erp_item_master_is_sub_item_applicable'); ?><!--is Sub-item Applicable--> </label>

                            <div class="skin skin-square">
                                <div class="skin-section extraColumns">
                                    <input id="checkbox_isSubitemExist" type="checkbox"
                                           data-caption="" class="columnSelected" name="isSubitemExist" value="1">
                                    <label for="checkbox">
                                        &nbsp;
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span>
                        <?php echo $this->lang->line('common_save') ?><!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade invoice_setup" id="addcontractextracost" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title" id="exampleModalLabel">Add Extra Charge</h4>        
      </div>
      <div class="modal-body">

      <div class="row">
        <div class="col-md-12">
            <div class="contract_customer_details_content">
                <form>
                    <div class="form-group row">
                        <label for="text1" class="col-md-4 col-form-label">Extra Charge</label> 
                        <div class="col-md-8">
                        <?php  echo form_dropdown('extraChargeID', all_discount_drop(2),'','class="form-control select2" id="extraChargeID" required'); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="text" class="col-md-4 col-form-label">Value</label> 
                        <div class="col-md-8">
                            <input id="extraChargeValue" name="extraChargeValue" type="number" class="form-control">
                        </div>
                    </div>
                                 
                </form>    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="add_extra_charge_detail()">Add</button>
            </div>
        </div>
     </div>
    </div>
      
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" id="capitalize_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="exampleModalLabel">
                    </h4>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart('', 'id="capitalize_form" class="form-horizontal"'); ?>
                    <input type="hidden" name="det_id" id="det_id">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">
                        Capitalization date</label>
                        <div class="col-sm-7">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="capitalizeDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                    value="<?php echo $current_date; ?>" id="capitalizeDate" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button type="button" class="btn btn-primary" onclick="save_capitalize_detail_po()">
                    Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="po_base_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
        <?php echo form_open('', 'role="form" id="po_addon_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"
                    id="myModalLabel"> <?php echo $this->lang->line('transaction_goods_purchase_order_base'); ?> </h4>
                <!--Purchase Order Base-->
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h4> <?php echo $this->lang->line('transaction_goods_purchase_orders'); ?> </h4>
                                <!--Purchase Orders-->
                            </div>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked" id="logistic_po">

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('common_code'); ?> </th>
                                <th>Logistic Amount </th>
                                <th>Logistic Balance </th>
                                <th>Matching Amount </th>
                                <th>Actual Logistic Amount </th>

                                <th style="display: none;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="addon_table_body">
                            <tr class="danger">
                                <td colspan="9" class="text-center" id="no_rec_colspan">
                                    <b><?php echo $this->lang->line('common_no_records_found'); ?> </b></td>
                                <!--No Records Found-->
                            </tr>
                            </tbody>
                            <tfoot id="table_addon_foot">

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                <button type="button" class="btn btn-primary" onclick="save_po_addon_items()"
                        ><?php echo $this->lang->line('common_save_change'); ?> </button>
                <!--Save changes-->
            </div>
        </form>
        </div>
    </div>
</div>


<div class="modal fade" id="edit_po_base_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 75%">
        <div class="modal-content">
            <?php echo form_open('', 'role="form" id="po_addon_edit_form"'); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"
                    id="myModalLabel"> <?php echo $this->lang->line('transaction_goods_purchase_order_base'); ?> </h4>
                <!--Purchase Order Base-->
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                            <tr>
                                <th><?php echo $this->lang->line('common_code'); ?> </th>
                                <th>Logistic Amount </th>
                                <th>Logistic Balance </th>
                                <th>Matching Amount </th>
                                <th>Actual Logistic Amount </th>

                                <th style="display: none;">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="addon_table_body">
                                <tr>
                                    <td><span id="addon_purchaseOrderCode"></span>
                                        <input type="hidden" name="poLogisticID" id="addon_poLogisticID">
                                    </td>
                                    <td class="text-right"><span id="addon_addonAmount"></span></td>
                                    <td class="text-right"><span id="addon_addonBalance"></span></td>
                                    <td class="text-center"> <input type="text" name="matchedAmount" class="number" size="8" id="addon_matchedAmount" onkeyup="changeMatchedAmountEdit(this)"></td>
                                    <td class="text-center"><input type="text" name="actualLogisticAmount" class="number" size="8" id="addon_actualLogisticAmount"></td>
                                </tr>
                            </tbody>
                            <tfoot id="table_addon_foot">

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                <button type="button" class="btn btn-primary" onclick="update_po_addon_items()"
                ><?php echo $this->lang->line('common_save_change'); ?> </button>
                <!--Save changes-->
            </div>
            </form>
        </div>
    </div>
</div>

<!--Detail Attachment Modal -->
<div class="modal fade" id="pop_purchase_attachement" tabindex="-1" role="dialog" aria-labelledby="pop_purchaseOrder_attachment_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="pop_purchaseOrder_attachment_label">Attachment</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive" style="width: 100%">
                    <div class="col-md-12">
                        <span class="pull-right">
                        <form id="purchase_form" class="form-inline" enctype="multipart/form-data" method="post">
                            <input type="hidden" name="detailID" id="detailID">
                            <input type="hidden" class="form-control" id="purchaseID" name="purchaseID">
                            <input type="hidden" class="form-control" id="documentID" value="PO" name="documentID">
                            <input type="hidden" class="form-control" id="document_name" value="Purchase Order" name="document_name">
                            <div class="form-group">
                                <input type="text" class="form-control" id="attachmentDescription" name="attachmentDescription" placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                            </div>
                            <div class="form-group ">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                    style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                                class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                class="fileinput-filename set-w-file-name"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                                class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                            aria-hidden="true"></span></span><span
                                                class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                                aria-hidden="true"></span></span><input
                                                type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                        data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="uplode_purchase()"><span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                        </form>
                        </span>
                    </div>
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
                        <tbody id="purchaseOrder_attachment_pop" class="no-padding">
                            <tr class="danger">
                                <td colspan="5" class="text-center">
                                    <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $this->lang->line('common_close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">
    var search_id = 1;
    var itemAutoID;
    var purchaseOrderID;
    var purchaseOrderDetailsID;
    var currency_decimal;
    var documentCurrency;
    var projectID;
    var tax_total = 0;
    var disc_total_am = 0;
    var purchaseOrderType;
    var select_VAT_value = '';
    var item;
    var rcmApplicableYN = '';
    var showPurchasePrice = <?php echo $showPurchasePrice ?>;
    // var defaultSegment = <?php echo json_encode($this->common_data['company_data']['default_segment']); ?>;
    var defaultSegment = <?php echo json_encode($segment_arr_default); ?>;
    var isGroupBasedTaxYN = 0;
    var current_stock_po = '<?php echo $current_stock_po ?>';
    var isGrpApplicable = '<?php echo getPolicyValues('GBT', 'All') ?>';
    var assignCatPR = '<?php echo getPolicyValues('ECPR', 'All'); ?>'?'<?php echo getPolicyValues('ECPR', 'All'); ?>':0;
    var activeincoterms = '<?php echo getPolicyValues('AITS', 'All'); ?>'?'<?php echo getPolicyValues('AITS', 'All'); ?>':0;
    var activeretension = '<?php echo getPolicyValues('ACRT', 'All'); ?>'?'<?php echo getPolicyValues('ACRT', 'All'); ?>':0;
    var blanketPoEnable = '<?php echo getPolicyValues('BPOE', 'All'); ?>'?'<?php echo getPolicyValues('BPOE', 'All'); ?>':0;
    var enableCapitalizeDate  = '<?php echo getPolicyValues('CCDPO', 'All'); ?>'?'<?php echo getPolicyValues('CCDPO', 'All'); ?>':0;

    var current_date = '<?php echo current_format_date(); ?>';
  
    $(document).ready(function () {
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('#pr_itemAutoID').multiselect2({
                enableCaseInsensitiveFiltering: true,
                includeSelectAllOption: true,
                numberDisplayed: 1,
                buttonWidth: '180px',
                maxHeight: '30px'
        });



        $('#add_po_addon').hide();

        item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Procurement/fetch_itemrecode/?q=%QUERY"
        });
        item.initialize();

        $('.headerclose').click(function () {
            fetchPage('system/procurement/erp_purchase_order', purchaseOrderID, 'Purchase Order');
        });

        tinymce.init({
            selector: ".Note,.PAYTERMS",
            height: 400,
            browser_spellcheck: true,
            plugins: [
                "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
            ],
            toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
            toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
            toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft code",

            menubar: false,
            toolbar_items_size: 'small',

            style_formats: [{
                title: 'Bold text',
                inline: 'b'
            }, {
                title: 'Red text',
                inline: 'span',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Red header',
                block: 'h1',
                styles: {
                    color: '#ff0000'
                }
            }, {
                title: 'Example 1',
                inline: 'span',
                classes: 'example1'
            }, {
                title: 'Example 2',
                inline: 'span',
                classes: 'example2'
            }, {
                title: 'Table styles'
            }, {
                title: 'Table row 1',
                selector: 'tr',
                classes: 'tablerow1'
            }],

            templates: [{
                title: 'Test template 1',
                content: 'Test 1'
            }, {
                title: 'Test template 2',
                content: 'Test 2'
            }]
        });

        $('.select2').select2();
        purchaseOrderID = null;
        purchaseOrderDetailsID = null;
        itemAutoID = null;
        currency_decimal = 2;
        documentCurrency = null;
        projectID = null;
        initializeitemTypeahead_edit();
        number_validation();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#purchase_order_form').bootstrapValidator('revalidateField', 'POdate');
            $('#purchase_order_form').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if (p_id) {
            purchaseOrderID = p_id;
            <?php if($createmasterrecords == 1){?>
            fetch_supplierdrop('', purchaseOrderID)
            <?php }?>

            laad_po_header();
            $("#a_link").attr("href", "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/" + purchaseOrderID);
            $('.btn-wizard').removeClass('disabled');
        } else {
            // $("#Note").wysihtml5();
            load_default_note(10);
            load_default_note(11);
            //load_default_note(12);
            <?php if($createmasterrecords == 1){?>
            fetch_supplierdrop();
            <?php }?>

            $('.btn-wizard').addClass('disabled');
            $('#segment').val(defaultSegment).change();

            if (isGrpApplicable == 1) {
                $('.isRcmApplicableYN').addClass('hide');
                $('#documentTaxType').val(1);
                $('.reverse-charge-mechanism').removeClass('hide');
            } else {
                $('.isRcmApplicableYN').removeClass('hide');
                $('.reverse-charge-mechanism').addClass('hide');
            }


        }

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;

        $('#discount_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                disc_amount: {validators: {notEmpty: {message: 'Discount Amount is required.'}}},
                discpercentage: {validators: {notEmpty: {message: 'Percentage is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
            data.push({'name': 'taxtotal', 'value': $('#taxaplcamnt').val()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Procurement/save_inv_disc_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data['type'], data['data'], 1000);
                    stopLoad();
                    if (data['status']) {
                        $form.bootstrapValidator('resetForm', true);
                        setTimeout(function () {
                            fetch_po_detail_table();
                        }, 300);
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#purchase_order_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //supplierPrimaryCode: {validators: {notEmpty: {message: 'Supplier is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Supplier Currency is required.'}}},
                expectedDeliveryDate: {validators: {notEmpty: {message: 'Delivery Date is required.'}}},
                POdate: {validators: {notEmpty: {message: 'PO Date is required.'}}},
                purchaseOrderType: {validators: {notEmpty: {message: 'Type is required.'}}},
                segment: {validators: {notEmpty: {message: 'Segment is required.'}}},
                shippingAddressID: {validators: {notEmpty: {message: 'Ship To is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            tinymce.triggerSave();
            $("#purchaseOrderType").prop("disabled", false);
            $("#segment").prop("disabled", false);
            $("#documentTaxType").prop("disabled", false);
            $("#supplierPrimaryCode").prop("disabled", false);
            $("#itemCatType").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#rcmYN").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Procurement/save_purchase_order_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    var result = $('#transactionCurrencyID option:selected').text().split('|');
                    $('.currency').html('( ' + result[0] + ' )');
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        purchaseOrderID = data['last_id'];
                        purchaseOrderType = data['purchaseOrderType'];
                        $("#a_link").attr("href", "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>/" + purchaseOrderID);
                        $("#purchaseOrderType").prop("disabled", true);
                        $("#rcmYN").prop("disabled", true);
                        //$("#segment").prop("disabled", true);
                        $("#documentTaxType").prop("disabled", true);
                        $("#supplierPrimaryCode").prop("disabled", true);
                        $("#itemCatType").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        fetch_po_detail_table();
                        $('[href=#step2]').tab('show');
                    }
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
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

        $('#tax_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                tax_amount: {validators: {notEmpty: {message: 'Tax Amount is required.'}}}, /*Tax Amount is required*/
                text_type: {validators: {notEmpty: {message: 'Tax Type is required.'}}}, /*Tax Type is required*/
                percentage: {validators: {notEmpty: {message: 'Tax Amount is required.'}}}/*Percentage is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
            data.push({'name': 'tax_total', 'value': tax_total});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Procurement/save_po_general_tax'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    myAlert(data['type'], data['data']);
                    if (data['status']) {
                        setTimeout(function () {
                            fetch_po_detail_table();
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

    });

    function fetch_po_detail_table() {
        if (purchaseOrderID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseOrderID': purchaseOrderID},
                url: "<?php echo site_url('Procurement/fetch_po_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('.currency').html('( ' + data['currency']['transactionCurrency'] + ' )');
                    currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];

                    var docType = data['currency']['purchaseOrderType'];

                    if (data['group_based_tax'] == 1) {
                        $('.discount_general_view').addClass('hide')
                        isGroupBasedTaxYN = 1;
                    } else {
                        $('.discount_general_view').removeClass('hide')
                        isGroupBasedTaxYN = 0;
                    }
                    if (data['isRcmDocument'] == 1) {
                        $('.rcmStatus').removeClass('hide');
                    } else {
                        $('.rcmStatus').addClass('hide');
                    }

                    $('#table_body').empty();
                    $('#table_tfoot').empty();
                    x = 1;
                    if ($('#documentTaxType').val() == 0) {
                        $('#genTax').removeClass('hidden');
                        $('.lintax').addClass('hidden');
                        $('.lintaxcols').removeClass('hidden');
                    } else {
                        $('#genTax').addClass('hidden');
                        $('.lintax').removeClass('hidden');
                        $('.lintaxcols').addClass('hidden');
                    }
                    purchaseOrderType = data['extra']['master']['purchaseOrderType'];

                    $('#logistic_po_table_details').hide();

                    if(purchaseOrderType === 'LOG'){
                        $('#po_table_details').hide();
                        $('#logistic_po_table_details').show();
                        $('#add_purchase_detail').hide();
                        $('#add_po_addon').show();
                    }

                    $('#taxaplcamnt').val(0);
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $("#purchaseOrderType").prop("disabled", false);
                        $("#documentTaxType").prop("disabled", false);
                        $("#segment").prop("disabled", false);
                        $("#rcmYN").prop("disabled", false);
                        $("#supplierPrimaryCode").prop("disabled", false);
                        $("#transactionCurrencyID").prop("disabled", false);
                        $("#itemCatType").prop("disabled", false);
                        $('#discount_tot').text('Discount Total (0.00)');
                        $('#tax_tot').text('Tax Applicable Amount (0.00)');
                        $('#taxaplcamnt').val(0);
                        if ($("#documentTaxType").val() == 0) {
                            $('#table_body').append('<tr class="danger"><td colspan="10" class="text-center"><b>No Records Found</b></td></tr>');
                        } else {
                            $('#table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b>No Records Found</b></td></tr>');
                        }
                    } else {
                        $("#purchaseOrderType").prop("disabled", true);
                        $("#documentTaxType").prop("disabled", true);
                        //$("#segment").prop("disabled", true);
                        $("#rcmYN").prop("disabled", true);
                        $("#supplierPrimaryCode").prop("disabled", true);
                        $("#itemCatType").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        tot_amount = 0;
                        commission_amount = 0;
                        tot_amount_line = 0;
                        tax_total = 0;
                        disc_total_am = 0;
                        var disc_foottotal = 0;
                        currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];

                        $.each(data['detail'], function (key, value) {
                            var tax_select = '<select class="form-control select2 taxDrop" onchange="change_tax_detail(this)">';
                            tax_select += '<option value="">'+'Select Tax'+'</option>'
                            $.each(data['tax_detail_master'], function (key, taxvalue) { 
                                if(key != ''){
        
                                    var selected = (key == value['taxCalculationformulaID']) ? 'selected' : '';
                                    tax_select += '<option value="'+key+'" '+selected+'>'+taxvalue+'</option>'
                                }
                            });

                            tax_select += '</select>'

                            var purchaseDetailID_Row = '<input type="hidden" class="purchaseDetailID" id="purchaseDetailID" value="'+value['purchaseOrderDetailsID']+'" />';

                            tot_amount_line = parseFloat(value['totalAmount']) + parseFloat(value['taxAmount']);
                            if ($("#documentTaxType").val() == 0) {
                                tot_amount_line = value['totalAmount'];
                            }

                            if (isGroupBasedTaxYN == 1 && value['taxAmount'] > 0) {
                                if (docType == 'PR') {
                                    documentID = "\'PO-PRQ\'"
                                } else {
                                    documentID = "\'PO\'"
                                }

                                taxCalAmount = '<a href="#" class="drill-down-cursor" onclick="open_tax_dd(' + value['taxDetailAutoID'] + ',' + value['purchaseOrderID'] + ',' + documentID + ',' + currency_decimal + ',' + value['purchaseOrderDetailsID'] + ',\'srp_erp_purchaseorderdetails\',\'purchaseOrderDetailsID\')"> ' + parseFloat(value['taxAmount']).formatMoney(currency_decimal, '.', ',') + ' </a>';

                            } else {
                                taxCalAmount = parseFloat(value['taxAmount']).formatMoney(currency_decimal, '.', ',');
                            }
                            
                            purchaseRequestCode = value['purchaseRequestCode'];

                            var commision_percentage = value['lineTaxDesc'];
                            var commision_value = taxCalAmount;
                            var hide_edit = '';
                        
                            var tax_select_vlaue = '';
                            var capitalize_date = '';

                            if(enableCapitalizeDate ==1){
                                if(value['itemType'] =="Fixed Assets"){
                                    capitalize_date = '&nbsp;&nbsp;<a onclick="open_capitalize_model(' + value['purchaseOrderDetailsID'] +');"><span style="" title="Add Capitalization Date" class="glyphicon glyphicon-list-alt"></span></a>';
                            
                                }
                            }

                            if(purchaseRequestCode == 'BQUT'){

                                commision_percentage = '<input class="text-right commision_percentage" type="number" onchange="change_commission_per(this,'+value['purchaseOrderDetailsID']+')" id="com_percentage" name="com_percentage" value="'+value['comission_percentage']+'" > <a class="btn btn-default btn-sm" onclick="apply_to_all_commission(this)"><i class="fa fa-arrow-down"></i></a>';
                                commision_value = value['commision_value'];
                                hide_edit = 'hide';

                                commission_amount += parseFloat(commision_value);

                            }else{

                            }

                            let ex_date_val = current_date;

                            if(value['detailExpectedDeliveryDate']){
                                ex_date_val = '';//value['detailExpectedDeliveryDate'];
                            }
                            const ex_date = '';//'<div class="input-group " ><input type="date" onchange="set_po_detail_delivery_date(this,' + value['purchaseOrderDetailsID'] + ')" name="detailDate" data-inputmask="alias: '+date_format_policy+'" value="'+ex_date_val+'" id="' + value['purchaseOrderDetailsID'] + '" class="form-control "></div>';

                            if (purchaseOrderType == 'PR'){
                                //<td class="text-right lintax">' + parseFloat(value['totalAmount']).formatMoney(currency_decimal, '.', ',') + '</td>
                                $('#table_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + purchaseRequestCode + '</td><td><b>Description :</b> ' + value['Itemdescriptionpartno'] + ' <br> <b>comment :</b> ' + value['comment'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + (parseFloat(value['unitAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" style="width: 80px;">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + ' (' + parseFloat(value['discountPercentage']).toFixed(2) + '%) </td><td class="text-right">' + parseFloat(value['unitAmount']).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-left lintax">' + value['lineTaxDesc'] + '</td><td class="text-right lintax">' + taxCalAmount + '</td><td class="text-right">' + parseFloat(tot_amount_line).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="fetchPOAttachments(' + value['purchaseOrderID'] + ',' + value['purchaseOrderDetailsID'] +');"><span class="	glyphicon glyphicon-paperclip"></span></a> &nbsp;&nbsp;<a onclick="edit_item(' + value['purchaseOrderDetailsID'] + ',' + value['prQty'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;<a onclick="delete_item(' + value['purchaseOrderDetailsID'] + ',' + value['totalAmount'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a>'+capitalize_date+'</td></tr>');

                            } else{
                                $("#pr_code").attr('style','display:none');
                              
                                if(purchaseRequestCode == 'BQUT'){
                                    $('#table_body').append('<tr class="lineitem"><td>' + x + purchaseDetailID_Row +'</td><td>' + value['itemSystemCode'] + '</td><td><b>Description :</b> ' + value['Itemdescriptionpartno'] + ' <br> <b>comment :</b> ' + value['comment'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + (parseFloat(value['unitAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" style="width: 80px;">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + ' (' + parseFloat(value['discountPercentage']).toFixed(2) + '%) </td><td class="text-right">' + parseFloat(value['unitAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(parseFloat(tot_amount_line) - parseFloat(value['taxAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + tax_select + '</td><td class="text-right">' + parseFloat(value['taxAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(tot_amount_line).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">'+commision_percentage+'</td><td class="text-right">'+(parseFloat(commision_value)).formatMoney(currency_decimal, '.', ',')+'</td><td colspan="2" class="text-right"><a class='+hide_edit+' onclick="edit_item(' + value['purchaseOrderDetailsID'] + ',' + value['prQty'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;<a class="hide" onclick="delete_item(' + value['purchaseOrderDetailsID'] + ',' + value['totalAmount'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                }else{
                                    // $('#table_body').append('<tr class="lineitem"><td>' + x + purchaseDetailID_Row +'</td><td>' + value['itemSystemCode'] + '</td><td><b>Description :</b> ' + value['Itemdescriptionpartno'] + ' <br> <b>comment :</b> ' + value['comment'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + (parseFloat(value['unitAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" style="width: 80px;">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + ' (' + parseFloat(value['discountPercentage']).toFixed(2) + '%) </td><td class="text-right">' + parseFloat(value['unitAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">'+commision_percentage+'</td><td class="text-right">'+(parseFloat(commision_value)).formatMoney(currency_decimal, '.', ',')+'</td><<td class="text-right">' + parseFloat(tot_amount_line).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a  onclick="edit_item(' + value['purchaseOrderDetailsID'] + ',' + value['prQty'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;<a onclick="delete_item(' + value['purchaseOrderDetailsID'] + ',' + value['totalAmount'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a>'+capitalize_date+'</td></tr>');
                                    $('#table_body').append('<tr class="lineitem"><td>' + x + purchaseDetailID_Row +'</td><td>' + value['itemSystemCode'] + '</td><td><b>Description :</b> ' + value['Itemdescriptionpartno'] + ' <br> <b>comment :</b> ' + value['comment'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + (parseFloat(value['unitAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" style="width: 80px;">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + ' (' + parseFloat(value['discountPercentage']).toFixed(2) + '%) </td><td class="text-right">' + parseFloat(value['unitAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">'+taxCalAmount+'</td><td class="text-right">'+parseFloat(value['taxAmount']).formatMoney(currency_decimal, '.', ',')+'</td><<td class="text-right">' + parseFloat(tot_amount_line).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a  onclick="edit_item(' + value['purchaseOrderDetailsID'] + ',' + value['prQty'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;<a onclick="delete_item(' + value['purchaseOrderDetailsID'] + ',' + value['totalAmount'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a>'+capitalize_date+'</td></tr>');
                                }
                            }

                            x++;
                            tot_amount += parseFloat(tot_amount_line);
                            tax_total += (parseFloat(value['totalAmount']) + parseFloat(value['taxAmount']));
                            disc_total_am += (parseFloat(value['totalAmount']) + parseFloat(value['taxAmount']));
                        });

                        if (purchaseOrderType == 'PR'){
                            if ($("#documentTaxType").val() == 0) {
                                $('#table_tfoot').append('<tr><td colspan="8" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + parseFloat(tot_amount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                            } else {
                                $('#table_tfoot').append('<tr><td colspan="11" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + parseFloat(tot_amount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                            }
                        } else{
                             if(purchaseOrderType == 'BQUT'){
                                if ($("#documentTaxType").val() == 0) {
                                    $('#table_tfoot').append('<tr><td colspan="6" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + parseFloat(tot_amount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" colspan="4" >Total Commission ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="total text-right">'+ parseFloat(commission_amount).formatMoney(currency_decimal, '.', ',') +'</td></tr>');
                                } else {
                                    $('#table_tfoot').append('<tr><td colspan="8" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + parseFloat(tot_amount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" colspan="4" >Total Commission ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="total text-right">'+ parseFloat(commission_amount).formatMoney(currency_decimal, '.', ',') +'</td></tr>');
                                }
                            }else{
                                if ($("#documentTaxType").val() == 0) {
                                    $('#table_tfoot').append('<tr><td colspan="8" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + parseFloat(tot_amount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                                } else {
                                    $('#table_tfoot').append('<tr><td colspan="10" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + parseFloat(tot_amount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                                }
                            }
                        }

                        $('#discount_tot').text('Discount Applicable Amount ( ' + parseFloat(disc_total_am).formatMoney(currency_decimal, '.', ',') + ' )');
                        if (isGroupBasedTaxYN != 1) {
                            $('#tax_tot').text('Tax Applicable Amount ( ' + parseFloat(tax_total).formatMoney(currency_decimal, '.', ',') + ' )');
                            $('.taxAmount_tot').removeClass('hide');
                        } else {
                            $('.taxAmount_tot').addClass('hide');
                        }

                        $('#taxaplcamnt').val(parseFloat(tax_total).toFixed(currency_decimal));
                    }

                    $('#tax_table_body_recode,#tax_table_footer').empty();
                    if (jQuery.isEmptyObject(data['tax_detail'])) {
                        $('#tax_table_body_recode').append('<tr class="danger"><td colspan="4" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        x = 1;
                        t_total = 0;
                        tax_DD = '0.00';
                        tax_policy = '<?php echo getPolicyValues('GBT', 'All')?>';
                        $.each(data['tax_detail'], function (key, value) {
                            if (tax_policy == 1) {
                                tax_DD = '<a onclick="open_tax_dd(' + value['taxDetailAutoID'] + ',' + value['purchaseOrderAutoID'] + ',\'PO\',' + currency_decimal + ',' + value['purchaseOrderDetailsID'] + ',\'srp_erp_purchaseorderdetails\',\'purchaseOrderDetailsID\')">' + parseFloat(value['amount']).formatMoney(currency_decimal, '.', ',') + ' </a>';
                            } else {
                                tax_DD = parseFloat(value['amount']).formatMoney(currency_decimal, '.', ',');

                            }


                            $('#tax_table_body_recode').append('<tr><td>' + x + '</td><td>' + value['taxDescription'] + '</td><td class="text-right">' + tax_DD + '</td><td class="text-right"><a onclick="delete_tax_po(' + value['taxDetailAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            x++;
                            t_total += parseFloat(value['amount']);
                        });
                        if (t_total > 0) {
                            $('#tax_table_footer').append('<tr><td colspan="2" class="text-right">Tax Total </td><td class="text-right total">' + parseFloat(t_total).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                        }
                    }

                    $('#discount_table_body_recode,#discount_table_footer').empty();
                    if (data['currency']['generalDiscountPercentage'] <= 0) {
                        $('#discount_table_body_recode').append('<tr class="danger"><td colspan="3" class="text-center"><b>No Records Found</b></td></tr>');
                        $('#discsubmitbtn').removeClass('hidden');
                    } else {
                        $('#discsubmitbtn').addClass('hidden');
                        $('#discount_table_body_recode').append('<tr><td class="text-right">' + parseFloat(data['currency']['generalDiscountPercentage']).formatMoney(2, '.', ',') + '% </td><td class="text-right">' + parseFloat((data['currency']['generalDiscountPercentage'] / 100) * disc_total_am).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_discount(' + purchaseOrderID + ');"><span  class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_discount(' + purchaseOrderID + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        disc_foottotal = parseFloat((data['currency']['generalDiscountPercentage'] / 100) * disc_total_am);
                        $('#tax_tot').text('Tax Applicable Amount ( ' + parseFloat(tax_total - disc_foottotal).formatMoney(currency_decimal, '.', ',') + ' )');
                        $('#taxaplcamnt').val(parseFloat(tax_total - disc_foottotal).toFixed(currency_decimal));
                        tax_total = tax_total - disc_foottotal;
                    }
                    if ($('#documentTaxType').val() == 0) {
                        $('#genTax').removeClass('hidden');
                        $('.lintax').addClass('hidden');
                        $('.lintaxcols').removeClass('hidden');
                    } else {
                        $('#genTax').addClass('hidden');
                        $('.lintax').removeClass('hidden');
                        $('.lintaxcols').addClass('hidden');
                    }

                    $('#logistic_po').empty();
                    if(purchaseOrderType === 'LOG'){
                        $('#logistic_table_body').empty();
                        if (data['logistic_po'].length > 0) {
                            $.each(data['logistic_detail'], function (key, value) {
                                $('#logistic_table_body').append(
                                    '<tr>' +
                                    '<td>' + x + '</td>' +
                                    '<td>' + value['purchaseOrderCode'] + '</td>' +
                                    '<td class="text-right">' + parseFloat(value['addonAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' +
                                    '<td class="text-right">' + parseFloat(value['addonBalance']).formatMoney(currency_decimal, '.', ',') + '</td>' +
                                    '<td class="text-right">' + parseFloat(value['matchedAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' +
                                    '<td class="text-right">' + parseFloat(value['actualLogisticAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' +
                                    '<td class="text-right"><a onclick="edit_logistic_item(' + value['poLogisticID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;<a onclick="delete_logistic_item(' + value['poLogisticID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td>' +
                                    '</tr>'
                                )
                            });
                        }else{
                            $('#logistic_table_body').append('<tr class="danger"><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>');
                        }
                        if (data['logistic_po'].length > 0) {
                            $.each(data['logistic_po'], function(index, item) {
                                let id = 'pull-' + item.purchaseOrderID;
                                let listItem = '<li id="' + id + '" title="PO Date :- ' + item.documentDate + '" rel="tooltip" class="pull-li">' +
                                    '<a onclick="fetch_logistic_po_detail_table(' + item.purchaseOrderID + ')">' +
                                    item.purchaseOrderCode + ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>';
                                $('#logistic_po').append(listItem);
                            });
                        } else {
                            let norec = 'No Records found';
                            let listItem = '<li><a>' + norec + '</a></li>';
                            $('#logistic_po').append(listItem);
                        }
                    }

                    stopLoad();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
    }

    function fetchPOAttachments(PurchaseId,deatilID)
    {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/fetch_PO_attachments"); ?>',
            dataType: 'json',
            data: {'deatilID':deatilID,'PurchaseId':PurchaseId},
            success: function (data) {
                $('#purchaseOrder_attachment_pop').empty();
                $('#purchaseOrder_attachment_pop').append('' +data+ '');
                $("#pop_purchase_attachement").modal({ backdrop: "static", keyboard: true });
                $('#detailID').val(deatilID);
                $("#purchaseID").val(PurchaseId);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#ajax_nav_container').html(xhr.responseText);
            }
        });
    }

    function uplode_purchase(){
        var detailID=$('#detailID').val();
        var purchaseRequestID=$('#purchaseID').val();
        var formData = new FormData($('#purchase_form')[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/uplode_Purchase_Attachment'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    fetchPOAttachments(purchaseRequestID,detailID);
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function change_tax_detail(ev){
        var taxCalculationID = $(ev).val();
        var purchaseDetailID = $(ev).closest('tr').find('.purchaseDetailID').val();

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taxCalculationID':taxCalculationID,'purchaseDetailID':purchaseDetailID},
                url: "<?php echo site_url('Procurement/save_po_tax_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    setTimeout(function () {
                        fetch_po_detail_table();
                    }, 300);
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
    }

    function clearitemAutoID(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }

    function clearitemAutoIDEdit(e, ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('#itemAutoID_edit').val('');
        }
    }

    function initializeitemTypeahead(id) {

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN&type=2'+ '&category='+purchaseOrderID,
            onSelect: function (suggestion) {

                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);

                    var new_id = suggestion.itemAutoID;
                    
                    $('#f_search_' + id).closest('tr').find('.item_history').attr('onclick', 'openItemHistoryModel('+new_id+',"PO")');
                }, 200);
                //LoaditemUnitPrice_againtsExchangerate(datum.companyLocalWacAmount, this);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);


                if (showPurchasePrice == 1) {
                    fetch_purchase_price(suggestion.companyLocalPurchasingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);

                } else {
                    fetch_last_grn_amount(this, suggestion.itemAutoID, $('#transactionCurrencyID').val(), $('#supplierPrimaryCode').val());
                }
                fetch_line_tax_and_vat(suggestion.itemAutoID, this);

                $(this).closest('tr').find('.quantityRequested').focus();
                $(this).closest('tr').css("background-color", 'white');

                if (current_stock_po == 1) {
                    $(this).closest('tr').find('.currentstock').val(suggestion.currentstockitemled);
                }
            },
            /*showNoSuggestionNotice: true,
             noSuggestionNotice:'No record found',*/
        });
        $(".tt-dropdown-menu").css("top", "");
    }

    function initializeitemTypeahead_edit() {

        $('#search_edit').autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN&type=2'+ '&category='+purchaseOrderID,
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#estimatedAmount_edit').val('0');
                    $('#discount_amount_edit').val('0');
                    $('#totalAmount_edit').text('0');
                    $('#net_unit_cost_edit').text('0');
                    $('#quantityRequested_edit').val(0);
                    $('#discount_edit').val(0);
                    $('#itemAutoID_edit').val(suggestion.itemAutoID);
                }, 200);

                //LoaditemUnitPrice_againtsExchangerate_edit(datum.companyLocalWacAmount);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                fetch_last_grn_amount_edit(suggestion.itemAutoID, $('#transactionCurrencyID').val(), $('#supplierPrimaryCode').val());
                edit_fetch_line_tax_and_vat(suggestion.itemAutoID);
                $(this).closest('tr').find('#quantityRequested_edit').focus();
                $(this).closest('tr').find('#currentstock').val(suggestion.currentstockitemled);

            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate(LocalWacAmount, element) {
        poID = purchaseOrderID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'poID': purchaseOrderID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('ItemMaster/load_unitprice_exchangerate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data['amount']);
                    net_amount(element);
                }
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate_edit(LocalWacAmount) {
        poID = purchaseOrderID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'poID': purchaseOrderID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('ItemMaster/load_unitprice_exchangerate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $('#estimatedAmount_edit').val(data['amount']);
                    $('#discount_edit').val('');
                    $('#discount_amount_edit').val('');
                }
                refreshNotifications(true);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_sub_cat(select_val) {
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subSubCategoryID').val("");
        $('#subSubCategoryID option').remove();
        var subid = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function load_sub_sub_cat() {
        $('#subSubCategoryID option').remove();
        $('#subSubCategoryID').val("");
        var subsubid = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subSubCategoryID').empty();
                    var mySelect = $('#subSubCategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function select_text(data) {
        if (data.value != 0) {
            var result = $('#text_type option:selected').text().split('|');
            $('#percentage').val(parseFloat(result[2]));
            cal_tax(parseFloat(result[2]), tax_total);
            $('#discount_form').bootstrapValidator('revalidateField', 'percentage');
        }
    }

    function cal_tax_amount(discount_amount) {
        if (tax_total && discount_amount) {
            $('#percentage').val(((parseFloat(discount_amount) / tax_total) * 100).toFixed(2));
        } else {
            $('#percentage').val(0);
        }
    }

    function cal_disc_amount(discount_amount) {
        if (disc_total_am >= discount_amount) {
            if (disc_total_am && discount_amount) {
                $('#discpercentage').val(((parseFloat(discount_amount) / disc_total_am) * 100));
            } else {
                $('#discpercentage').val(0);
            }
        } else {
            myAlert('w', 'discount amount canot be greater than PO amount');
            $('#disc_amount').val(0);
            $('#discpercentage').val(0);
        }

    }

    function cal_tax(discount) {
        if (tax_total && discount) {
            $('#tax_amount').val(((tax_total / 100) * parseFloat(discount)).toFixed(currency_decimal));
        } else {
            $('#tax_amount').val(0);
        }
    }

    function cal_disc(discount) {
        if (disc_total_am && discount) {
            $('#disc_amount').val(((disc_total_am / 100) * parseFloat(discount)).toFixed(currency_decimal));
        } else {
            $('#disc_amount').val(0);
        }
    }

    function delete_tax(id, value) {
        if (purchaseOrderID) {
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
                        data: {'taxDetailAutoID': id},
                        url: "<?php echo site_url('Procurement/delete_tax_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            setTimeout(function () {
                                fetch_po_detail_table();
                            }, 300);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function delete_tax_po(id) {
        if (purchaseOrderID) {
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
                        data: {'taxDetailAutoID': id, 'purchaseOrderID': purchaseOrderID},
                        url: "<?php echo site_url('Procurement/delete_tax_po'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            setTimeout(function () {
                                fetch_po_detail_table();
                            }, 300);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function fetch_supplier_currency_by_id(supplierAutoID, select_value) {
        if (supplierAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'supplierAutoID': supplierAutoID},
                url: "<?php echo site_url('Procurement/fetch_supplier_currency_by_id'); ?>",
                success: function (data) {
                    if (documentCurrency) {
                        $("#transactionCurrencyID").val(documentCurrency).change()
                    } else {
                        if (data.supplierCurrencyID) {
                            $("#transactionCurrencyID").val(data.supplierCurrencyID).change();
                            currency_validation_modal(data.supplierCurrencyID, 'PO', supplierAutoID, 'SUP');
                        }
                    }

                    $("#paymentTerms").val(data.paymentTerms);
                }
            });
            fetch_rcm_enableYN(supplierAutoID)
        }
    }

    function fetch_related_uom_id(masterUnitID, select_value, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.umoDropdown').empty();

                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');

                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                        ;
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_related_uom_id_edit(masterUnitID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $('#UnitOfMeasureID_edit').empty();
                var mySelect = $('#UnitOfMeasureID_edit');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $("#UnitOfMeasureID_edit").val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function laad_po_header() {
        if (purchaseOrderID) {
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'purchaseOrderID': purchaseOrderID},
                    url: "<?php echo site_url('Procurement/load_purchase_order_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            fetch_po_detail_table();
                            projectID = data['projectID'];
                            $('.currency').html('( ' + data['transactionCurrency'] + ' )');
                            $('#referenceNumber').val(data['referenceNumber']);


                            setTimeout(function () {
                                $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            }, 300);

                            documentCurrency = data['transactionCurrencyID'];
                            $("#supplierPrimaryCode").val(data['supplierPrimaryCode']).change();
                            $('#expectedDeliveryDate').val(data['expectedDeliveryDate']);
                            $('#POdate').val(data['documentDate']);
                            $('#narration').val(data['narration']);
                            $('#documentTaxType').val(data['documentTaxType']);
                            if (data['documentTaxType'] == 0) {
                                $('#genTax').removeClass('hidden');
                            } else {
                                $('#genTax').addClass('hidden');
                            }
                            $('#soldToAddressID').val(data['soldToAddressID']);
                            $('#shippingAddressID').val(data['shippingAddressID']).change();
                            $('#invoiceToAddressID').val(data['invoiceToAddressID']);
                            $('#deliveryTerms').val(data['deliveryTerms']);
                            $('#penaltyTerms').val(data['penaltyTerms']);
                           /// $('#paymentTerms').val(data['paymentTerms']);
                            $('#purchaseOrderType').val(data['purchaseOrderType']).change();
                            purchaseOrderType = data['purchaseOrderType'];
                            $('#contactnumber').val(data['contactPersonNumber']);
                            $('#contactperson').val(data['contactPersonName']);
                            $('#creditPeriod').val(data['creditPeriod']);
                            $('#shippingAddressDescription').val(data['shippingAddressDescription']);
                            $('#customer_order_id').val(data['customerOrderID']).change();
                            $('#contractAutoID').val(data['contractAutoID']).change();
                            $('#location').val(data['mfqWarehouseAutoID']).change();
                            $('#owner').val(data['ownerID']).change();
                           // $('#payTerm').val(data['payTerm']).change();
                            $('#modeofship').val(data['modeOfShipID']).change();
                            // $("#Note").wysihtml5();
                            // $('#Note').val(data['termsandconditions']);
                            setTimeout(function () {
                                tinyMCE.get("Note").setContent(data['termsandconditions']);
                                tinyMCE.get("paymentTerms").setContent(data['paymentTerms']);
                            }, 300);

                           if(purchaseOrderType == 'BQUT'){
                                $('#add_purchase_detail').addClass('hide');
                            }else{
                                $('#add_purchase_detail').removeClass('hide');
                            }

                            if(assignCatPR==1){
                                $("#itemCatType").val(data['itemCategoryID']).change();
                            }

                            if(activeincoterms==1){
                                $("#incoterms").val(data['incotermsID']).change();
                            }

                            if(activeretension==1){
                                $("#retension_date").val(data['retensionDate']);
                                $("#retension_percentage").val(data['retensionPercentage']);
                            }

                            //enable version tab
                            
                            if(data['versionNo']==0){
                                $('#version_tab').addClass('hide');
                            }else{
                                $('#version_tab').removeClass('hide');
                            }

                            // fetch_rcm_enableYN(data['supplierPrimaryCode']);

                            rcmApplicableYN = data['rcmApplicableYN'];
                            if (rcmApplicableYN == 1) {
                                setTimeout(function () {
                                    $('#rcmYN').iCheck('check');
                                });
                            }
                            if (isGrpApplicable == 1) {
                                $('.isRcmApplicableYN').addClass('hide');
                                $('#documentTaxType').val(1);

                            } else {
                                $('.isRcmApplicableYN').removeClass('hide');
                            }


                            if (data['isGroupBasedTax'] == 1 || isGrpApplicable == 1) {
                                $('.reverse-charge-mechanism').removeClass('hide');
                                $('#documentTaxType').val(1);
                            } else {
                                $('.reverse-charge-mechanrcmApplicableYNism').addClass('hide');
                            }

                            if(blanketPoEnable==1){

                                if(data['isBlanketPo']==1){
                                    $('#blanketpo').iCheck('check');
                                    $('#blanket_Po_view').removeClass('hide');
                                    $('#blanketpo_val').val(1);
                                    $("#frequencyID").val(data['frequencyID']).change();
                                    $("#frequencyAmount").val(data['frequencyAmount']);
                                }

                            }

                            if (data['logisticYN'] == 1)
                            {
                                $('#logisticYN').iCheck('check');
                            }

                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');

                        }
                        stopLoad();
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                }
            )
            ;
        }
    }

    function fetch_ship_to(val) {
        if (val) {
            var ship = $('#shippingAddressID option:selected').text();
            var res = ship.split(" | ");
            $('#shippingAddressDescription').val(res[2]);
        }
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function purchase_order_detail_modal() {
        if (purchaseOrderID) {
            if (purchaseOrderType == 'PR') {
                load_prq_codes();
                $("#prq_base_modal").modal({backdrop: "static"});   
            } else if (purchaseOrderType == 'LOG') {
                $("#po_base_modal").modal({backdrop: "static"});
            } else {
                $('.f_search').typeahead('destroy');
                purchaseOrderDetailsID = null;
                $('#purchase_order_detail_form')[0].reset();
                $('#discount').val(0);
                $('#discount_amount').val(0);
                $('#po_detail_add_table tbody tr').not(':first').remove();
                $('.net_amount,.net_unit_cost').text('0');
                $('.f_search').typeahead('val', '');
                $('.itemAutoID').val('');
                $('.linetaxamnt ').html('0.00');
                initializeitemTypeahead(1);
                $('.f_search').closest('tr').css("background-color", 'white');
                $('.quantityRequested').closest('tr').css("background-color", 'white');
                $('.estimatedAmount').closest('tr').css("background-color", 'white');
                $("#purchase_order_detail_modal").modal({backdrop: "static"});
            }
        }
    }

    function load_conformation() {
        if (purchaseOrderID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'purchaseOrderID': purchaseOrderID, 'html': true},
                url: "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    stopLoad();
                    refreshNotifications(true);
                    attachment_modal_purchaseOrder(purchaseOrderID, "Purchase Order", "PO");
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        $("#unlimited_item_modal").modal("hide");
        if (purchaseOrderID) {
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
                        data: {'purchaseOrderID': purchaseOrderID},
                        url: "<?php echo site_url('Procurement/purchase_order_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data[0] == 'exceeded') {
                                $('#exceeded_item_body').html('');
                                if (!jQuery.isEmptyObject(data[2])) {
                                    $('#itemexceedamnt').removeClass('hidden');
                                    $.each(data[2], function (item, value) {
                                        $('#exceeded_item_body').append('<tr> <td style="text-align: center;">' + value['glCode'] + '</td><td style="text-align: right;">' + value['consumption'] + '</td> <td style="text-align: right;">' + value['budgetamount'] + '</td><td style="text-align: right;">' + value['exceededamnt'] + '</td></tr>')
                                    });
                                } else {
                                    $('#itemexceedamnt').addClass('hidden');
                                }
                                $('#exceeded_item_bodyqty').html('');
                                if (!jQuery.isEmptyObject(data[3])) {
                                    $('#itemexceedqty').removeClass('hidden');
                                    $.each(data[3], function (item, value) {
                                        var exceededamnt = value['consumption'] - value['budgetamount'];
                                        $('#exceeded_item_bodyqty').append('<tr><td>' + value['itemname'] + '</td> <td style="text-align: right;">' + value['consumption'] + '</td> <td style="text-align: right;">' + value['budgetamount'] + '</td><td style="text-align: right;">' + exceededamnt + '</td></tr>')
                                    });
                                } else {
                                    $('#itemexceedqty').addClass('hidden');
                                }
                                $("#exceeded_item_modal").modal({backdrop: "static"});

                            } else {
                                refreshNotifications(true);
                                if (data) {
                                    fetchPage('system/procurement/erp_purchase_order', purchaseOrderID, 'Purchase Order');
                                }
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    //check budget controls if inventory
    function confirmation_Inventory_check() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': purchaseOrderID},
            url: "<?php echo site_url('Procurement/confirmation_Inventory_check'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 'exceeded') {
                    $('#exceeded_item_body_unlimited').html('');
                    if (!jQuery.isEmptyObject(data[2])) {
                        $('#itemexceedamnt_unlimited').removeClass('hidden');
                        $.each(data[2], function (item, value) {
                            $('#exceeded_item_body_unlimited').append('<tr><td>' + value['itemname'] + '</td> <td style="text-align: right;">' + value['Glcode'] + '</td><td style="text-align: right;">' + value['poamount'] + '</td></tr>')
                        });
                    } else {
                        $('#itemexceedamnt_unlimited').addClass('hidden');
                    }
                    $('#exceeded_item_bodyqty_unlimited').html('');
                    if (!jQuery.isEmptyObject(data[3])) {
                        $('#itemexceedqty_unlimited').removeClass('hidden');
                        $.each(data[3], function (item, value) {
                            $('#exceeded_item_bodyqty_unlimited').append('<tr><tr><td>' + value['itemname'] + '</td> <td style="text-align: right;">' + value['Poqty'] + '</td></tr>')
                        });
                    } else {
                        $('#itemexceedqty_unlimited').addClass('hidden');
                    }
                    $("#unlimited_item_modal").modal({backdrop: "static"});
                } else {
                    confirmation()
                }

            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function save_draft() {
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/procurement/erp_purchase_order', purchaseOrderID, 'Purchase Order');
                });
        }
    }

    function currency_validation(CurrencyID, documentID) {
        if (CurrencyID) {
            partyAutoID = $('#supplierPrimaryCode').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'SUP');
        }
    }

    function delete_item(id, totalAmount) {
        if (purchaseOrderID) {
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
                        data: {
                            'purchaseOrderDetailsID': id,
                            'purchaseOrderID': purchaseOrderID,
                            'taxtotal': $('#taxaplcamnt').val(),
                            'totalAmount': totalAmount
                        },
                        url: "<?php echo site_url('Procurement/delete_purchase_order_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            fetch_po_detail_table();

                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_item(id, prQty) {
   
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to edit this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Edit"
                },
                function () {
                    $('#po_detail_add_table tbody tr').not(':first').remove();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'purchaseOrderDetailsID': id},
                        url: "<?php echo site_url('Procurement/fetch_purchase_order_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            var taxAmount = parseFloat(data['taxAmount']);
                            var totAmount = parseFloat(data['totalAmount']);
                            var unitAmount = parseFloat(data['unitAmount']);
                            select_VAT_value = data['taxCalculationformulaID'];
                            purchaseOrderDetailsID = data['purchaseOrderDetailsID'];
                            $('#search_edit').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - " + data['seconeryItemCode']);
                            //$('#search_edit').typeahead('val', data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#quantityRequested_edit').val(data['requestedQty']);
                            $('#prQtyEdit').val(data['prQty']);
                            $('#estimatedAmount_edit').val((parseFloat(data['unitAmount']) + parseFloat(data['discountAmount'])));
                            $('#net_unit_cost_edit').text((unitAmount).formatMoney(currency_decimal, '.', ','));
                            $('#discount_amount_edit').val(data['discountAmount']);
                            $('#search_id').val(data['itemSystemCode']);
                            $('#itemSystemCode').val(data['itemSystemCode']);
                            $('#itemAutoID_edit').val(data['itemAutoID']);
                            $('#itemDescription').val(data['itemDescription']);
                            $('#comment_edit').val(data['comment']);
                            $('#discount_edit').val(data['discountPercentage']);
                            //  $('#text_type_edit').val(data['taxCalculationformulaID']);
                            $('#linetaxamnt_edit').text((taxAmount).formatMoney(currency_decimal, '.', ','));
                            $('#totalAmount_edit').text((totAmount + taxAmount).formatMoney(currency_decimal, '.', ','));
                            edit_fetch_line_tax_and_vat(data['itemAutoID']);
                            if (current_stock_po == 1) {
                                var stock = data['itemledstock'];
                                $('#currentstock').val(stock);
                            }
                            $("#purchase_order_detail_edit_modal").modal({backdrop: "static"});
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function cal_discount_amount(discount_amount, element) {
        var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
        if (estimatedAmount) {
            // $(element).closest('tr').find('.discount').val(((parseFloat(discount_amount) / estimatedAmount) * 100).toFixed(currency_decimal));
            $(element).closest('tr').find('.discount').val(((parseFloat(discount_amount) / estimatedAmount) * 100));
        }
        net_amount(element);
    }

    function cal_discount(discount, element) {
        if (discount < 0 || discount > 100) {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $(element).closest('tr').find('.discount').val(parseFloat(0));
        } else {
            var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
            if (estimatedAmount) {
                $(element).closest('tr').find('.discount_amount').val((estimatedAmount / 100) * parseFloat(discount).toFixed(currency_decimal));
            }
            net_amount(element);
        }
    }

    function change_amount(element) {
        if (element.value > 0) {
            $(element).closest('tr').css("background-color", 'white');
        }
        $(element).closest('tr').find('.discount').val(parseFloat(0));
        $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
        net_amount(element);
    }

    function change_qty(element) {
        if (element.value > 0) {
            $(element).closest('tr').css("background-color", 'white');
        }
        net_amount(element);
    }

    function net_amount(element) {
        var qut = $(element).closest('tr').find('.quantityRequested').val();
        var amount = $(element).closest('tr').find('.estimatedAmount').val();
        var discoun = $(element).closest('tr').find('.discount_amount').val();
        if (qut == null || qut == 0) {
            $(element).closest('tr').find('.net_amount,.net_unit_cost').text('0');
        } else {

            $(element).closest('tr').find('.net_amount').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(currency_decimal, '.', ','));
            $(element).closest('tr').find('.net_unit_cost').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(currency_decimal, '.', ','));
        }
    }

    //update function for inline editing
    function cal_discount_amount_edit() {
        var estimatedAmount = parseFloat($('#estimatedAmount_edit').val());
        var discount_amount = parseFloat($('#discount_amount_edit').val());
        if (discount_amount > estimatedAmount) {
            swal("Cancelled", "Discount Amount should be less than the Unit Price", "error");
            $('#discount_amount_edit').val(0);
            $('#discount_edit').val(0);
            net_amount_edit(estimatedAmount);
        } else {
            if (estimatedAmount) {
                $('#discount_edit').val(((parseFloat(discount_amount) / estimatedAmount) * 100));
            }
            net_amount_edit(estimatedAmount);
        }
    }

    function cal_discount_edit(discount) {
        var estimatedAmount = parseFloat($('#estimatedAmount_edit').val());
        if (discount < 0 || discount > 100) {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $('#discount_edit').val(0);
            $('#discount_amount_edit').val(0);
            net_amount_edit(estimatedAmount);
        } else {

            if (estimatedAmount) {
                $('#discount_amount_edit').val((estimatedAmount / 100) * parseFloat(discount));
            }
            net_amount_edit(estimatedAmount);
        }

    }

    function change_qty_edit() {
        if (purchaseOrderType == 'PR') {
            var prQtyEdit = getNumberAndValidate($('#prQtyEdit').val());
            var quantityRequested = getNumberAndValidate($('#quantityRequested_edit').val());
            if (quantityRequested <= prQtyEdit) {
                net_amount_edit();
            } else {
                $('#quantityRequested_edit').val(0);
                net_amount_edit();
                swal("PO Qty should be less than requested Qty", "error");
            }
        } else {
            net_amount_edit();
        }


    }

    function getNumberAndValidate(thisVal, dPlace = 2) {
        thisVal = $.trim(thisVal);
        thisVal = removeCommaSeparateNumber(thisVal);
        thisVal = thisVal.toFixed(dPlace);
        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        } else {
            return parseFloat(0);
        }
    }

    function change_amount_edit() {
        $('#discount_edit').val(parseFloat(0));
        $('#discount_amount_edit').val(parseFloat(0));
        net_amount_edit();
    }

    function net_amount_edit() {
        var qut = $('#quantityRequested_edit').val();
        var amount = $('#estimatedAmount_edit').val();
        var discoun = $('#discount_amount_edit').val();
        if (qut == null || qut == 0) {
            $('#totalAmount_edit').text('0');
            $('#net_unit_cost_edit').text('0');
        } else {
            $('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(currency_decimal, '.', ','));
            $('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(currency_decimal, '.', ','));
        }
    }

    function attachment_modal_purchaseOrder(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': 0},
                success: function (data) {
                    $('#purchaseOrder_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " Attachments");
                    $('#purchaseOrder_attachment').empty();
                    $('#purchaseOrder_attachment').append('' + data + '');

                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_purchaseOrder_delete(purchaseOrderID, DocumentSystemCode, fileName) {
        if (purchaseOrderID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this attachment file!",
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
                        data: {'attachmentID': purchaseOrderID, 'myFileName': fileName},
                        url: "<?php echo site_url('Procurement/delete_purchaseOrder_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            attachment_modal_purchaseOrder(DocumentSystemCode, "Purchase Order", "PO");
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function add_more() {
        //$('#f_search_1').typeahead('destroy');
        search_id += 1;
        //$('.f_search').typeahead('destroy');
        $('select.select2').select2('destroy');
        var appendData = $('#po_detail_add_table tbody tr:first').clone();
        appendData.find('.umoDropdown,.item_text').empty();
        appendData.find('.net_amount,.net_unit_cost').text('0');
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('.linetaxamnt').html('0.00');
        appendData.find('textarea').val('');
        appendData.find('.discount_amount').val('0');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#po_detail_add_table').append(appendData);
        var lenght = $('#po_detail_add_table tbody tr').length - 1;
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        //  $('#po_detail_add_table tr').last().attr('f_search_' + search_id)
        $(".select2").select2();
        number_validation();
        initializeitemTypeahead(search_id);
        //initializeitemTypeahead(1);
    }

    function savePurchaseOrderDetails() {
        var data = $('#purchase_order_detail_form').serializeArray();
        if (purchaseOrderID) {
            data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
            data.push({'name': 'taxtotal', 'value': $('#taxaplcamnt').val()});
            data.push({'name': 'purchaseOrderDetailsID', 'value': purchaseOrderDetailsID});
            $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
            });

            $('.itemAutoID').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });
            $('.quantityRequested').each(function () {
                if (this.value == '' || this.value == 0) {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });
            $('.estimatedAmount').each(function () {
                if (this.value == '' || this.value == 0) {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });

            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Procurement/save_purchase_order_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            purchaseOrderDetailsID = null;
                            fetch_po_detail_table();
                            $('#purchase_order_detail_modal').modal('hide');
                        }
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
        } else {
            alert('An Error Occurred! Please Try Again.');
            stopLoad();
        }
    }

    function updatePurchaseOrderDetails() {
        var data = $('#purchase_order_detail_edit_form').serializeArray();
        if (purchaseOrderID) {
            data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
            data.push({'name': 'purchaseOrderDetailsID', 'value': purchaseOrderDetailsID});
            data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
            data.push({'name': 'taxtotal', 'value': $('#taxaplcamnt').val()});
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Procurement/update_purchase_order_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data) {
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                purchaseOrderDetailsID = null;
                                $('#purchase_order_detail_edit_modal').modal('hide');
                                fetch_po_detail_table();
                            }
                        }

                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
        } else {
            swal({
                title: "Good job!",
                text: "You clicked the button!",
                type: "success"
            });
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

    //thanks: http://javascript.nwbox.com/cursor_position/
    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function load_segmentBase_projectID() {
        var segment = $('#segment').val();
        var type = 'master';
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase"); ?>',
            dataType: 'html',
            data: {segment: segment, type: type, 'post_doc': 'PO'},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_projectID').html(data);
                $('.select2').select2();
                if (projectID) {
                    $("#projectID_master").val(projectID).change()
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function load_prq_codes() {

        var itemAutoID = $('#pr_itemAutoID').val();
        var itemSearch = $('#itemSearch').is(':checked');

        if(itemSearch == true){
            $('#itemSearchdiv').removeClass('hide');
        }else{
            $('#itemSearchdiv').addClass('hide');
        }

        if(itemSearch == false || itemAutoID){

            $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': purchaseOrderID,'itemAutoID':itemAutoID,'itemSearch':itemSearch},
            url: "<?php echo site_url('Procurement/fetch_prq_code'); ?>",
            success: function (data) {
                $('#prqcode').empty();
                $('#table_body_pr_detail').empty();
                var mySelect = $('#prqcode');
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, value) {
                        var bal = value['requestedQty'] - value['prQty'];
                       if (bal > 0) {
                        var id = 'pull-' + value['purchaseRequestID'];
                        mySelect.append('<li id="' + id + '" title="PR Date :- ' + value['documentDate'] + ' Requestd By:- ' + value['requestedByName'] + '"  rel="tooltip" class="pull-li"><a onclick="fetch_prq_detail_table(' + value['purchaseRequestID'] + ')">' + value['purchaseRequestCode'] + ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>');
                        $("[rel=tooltip]").tooltip();
                       }
                      if(itemSearch == true){
                        fetch_prq_detail_table(value['purchaseRequestID'],2);
                      }

                    });
                } else {
                    mySelect.append('<li><a>No Records found</a></li>');
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }

            });
        }

      

    }

    function fetch_prq_detail_table(purchaseRequestID,withoutempty = 1) {
        $('.pull-li').removeClass('pulling-based-li');
        $('#pull-' + purchaseRequestID).addClass('pulling-based-li');

        var itemAutoID = $('#pr_itemAutoID').val();
        var itemSearch = $('#itemSearch').is(':checked');

        if (purchaseRequestID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseRequestID': purchaseRequestID, 'purchaseOrderID': purchaseOrderID,'itemAutoID':itemAutoID,'itemSearch':itemSearch},
                url: "<?php echo site_url('Procurement/fetch_prq_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if(withoutempty == 1){
                        $('#table_body_pr_detail').empty();
                    }
                    
                    x = 1;

                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#table_body_pr_detail').append('<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        tot_amount = 0;
                        receivedQty = 0;

                        var click_qty_arry = [];

                        $.each(data['detail'], function (key, value) {
                            if (value['receivedQty'] != null && value['receivedQty'] !== undefined) {
                                receivedQty = value['receivedQty'];
                            }
                            cost_status = '<input type="text"  onchange="load_line_tax_amount_prq(' + value['purchaseRequestDetailsID'] + ')" class="number" size="10" id="amount_' + value['purchaseRequestDetailsID'] + '" onkeypress="return validateFloatKeyPress(this,event)" value="' + parseFloat(value['unitAmount']) + '" onkeyup="select_value(' + value['purchaseRequestDetailsID'] + ')" >';
                            discount_status = '<td> <input type="text" placeholder="0" onchange="load_line_tax_amount_prq(' + value['purchaseRequestDetailsID'] + ')" id="discount_prq_' + value['purchaseRequestDetailsID'] + '"  class="number" value="0" onkeyup="cal_discount_prq(' + value['purchaseRequestDetailsID'] + ')" onfocus="this.select();"> </td>'+
                                                '<td><input type="text" size="3" onchange="load_line_tax_amount_prq(' + value['purchaseRequestDetailsID'] + ')" id="discount_amount_prq_' + value['purchaseRequestDetailsID'] + '" style="width: 80px;" placeholder="0.00" class="number" onkeyup="cal_discount_amt(' + value['purchaseRequestDetailsID'] + ');" onkeypress="return validateFloatKeyPress(this,event);"  value="0" ></td>';
                            /*  var qty=value['requestedQty'] - value['prQty'];*/
                            var qty = value['balQty'];
                            if (qty > 0) {

                                var taxDrop = '<td class="lintax"><select class="lntax_drop" onchange="load_line_tax_amount_prq(' + value['purchaseRequestDetailsID'] + ')" style="width: 110px;"  id="text_type_' + value['purchaseRequestDetailsID'] + '"><option value="">Select Tax Type</option></select></td>';
                                if (isGroupBasedTaxYN == 1) {

                                    taxDrop = '<td class="lintax"> <select class="lntax_drop" onchange="load_line_tax_amount_prq(' + value['purchaseRequestDetailsID'] + ')" style="width: 110px;"  id="text_type_' + value['purchaseRequestDetailsID'] + '"><option value="">Select Tax Type </option></select></td>';

                                }


                                $('#table_body_pr_detail').append(
                                    '<tr><td>' + x + '</td><td>' + 
                                    value['purchaseRequestCode'] + '</td><td>' + 
                                    value['itemSystemCode'] + '</td><td>' + 
                                    value['itemDescription'] + '</td><td class="text-center">' + 
                                    value['unitOfMeasure'] + '</td><td class="text-right" id="requested_qty_' + 
                                    value['purchaseRequestDetailsID'] + '">' + 
                                    qty + '</td><td class="text-right">' + 
                                    parseFloat(value['unitAmount']).toFixed(currency_decimal) + 
                                    '</td><td class="text-center"><i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' + 
                                    value['purchaseRequestDetailsID'] + ',' + 
                                    value['unitAmount'] + ',' + qty + ')" aria-hidden="true"></i> <input type="text" onchange="load_line_tax_amount_prq(' + 
                                    value['purchaseRequestDetailsID'] + ')" class="number" size="8" id="qty_' + 
                                    value['purchaseRequestDetailsID'] + '" onkeyup="select_check_box(this,' + 
                                    value['purchaseRequestDetailsID'] + ',' + 
                                    value['unitAmount'] + ',' + qty + ' )" ></td><td class="text-center">' + 
                                    cost_status + '</td>' + discount_status + ' ' + taxDrop + '<td class="lintax"><span class="pull-right" id="lintaxamntprq_' + 
                                    value['purchaseRequestDetailsID'] + '" >0</span></td> <td class="text-center"><p id="tot_' + value['purchaseRequestDetailsID'] + 
                                    '"> </p></td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + 
                                    value['purchaseRequestDetailsID'] + '" type="checkbox" value="' + 
                                    value['purchaseRequestDetailsID'] + '"></td></tr>');
                                setTimeout(() => {

                                    fetch_tax_drop(value['purchaseRequestDetailsID'], value['itemAutoID']);
                                })

                                //create array for fill po qty automaticaly
                                var qty_row = {"id":value['purchaseRequestDetailsID'],"val":value['unitAmount'],"qty":qty};

                                click_qty_arry.push(qty_row);
                            }
                            x++;
                            tot_amount += (parseFloat(value['totalAmount']).toFixed(currency_decimal));
                            //.formatMoney(currency_decimal, '.', ',')
                        });

                        if (isGroupBasedTaxYN != 1) {
                            if (!jQuery.isEmptyObject(data['taxdrop'])) {
                                $('.lntax_drop').empty();
                                var mySelect = $('.lntax_drop');
                                mySelect.append($('<option></option>').val('').html('Select Tax Type'));
                                $.each(data['taxdrop'], function (val, text) {
                                    mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                                });
                            }


                        }


                        if ($('#documentTaxType').val() == 0) {
                            $('#genTax').removeClass('hidden');
                            $('.lintax').addClass('hidden');
                            $('.lintaxcols').removeClass('hidden');
                        } else {
                            $('#genTax').addClass('hidden');
                            $('.lintax').removeClass('hidden');
                            $('.lintaxcols').addClass('hidden');
                        }
                    }
                    number_validation();

                    //fill po qty automaticaly
                    if (!jQuery.isEmptyObject(click_qty_arry)) {
                        $.each(click_qty_arry, function (k, v) {
                            setQty(v.id,v.val,v.qty);
                        });
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
        ;
    }

    function select_check_box(data, id, amount, reqqty) {
        var qty = $('#qty_' + id).val();
        if (qty <= reqqty) {

            $("#check_" + id).prop("checked", false);
            if (data.value > 0) {
                $("#check_" + id).prop("checked", true);
            }
            amount = $('#amount_' + id).val();
            if (amount < 0) {
                amount = 0;
            }
            var total = qty * amount;
            var totalnew = (parseFloat(total).toFixed(currency_decimal));
            $('#tot_' + id).text(totalnew);
        } else {
            $('#discount_prq_' + id).val(0);
            $('#discount_amount_prq_' + id).val(0);
            $('#qty_' + id).val(0);
            $('#tot_' + id).text('');
            swal("Ordered Qty should be less than requested Qty", "error");
        }
        cal_discount_prq(id)
    }

    function select_value(id) {
        var qty = $('#qty_' + id).val();
        if (qty < 0) {
            qty = 0;
        }
        amount = $('#amount_' + id).val();
        if (amount < 0) {
            amount = 0;
        }
        var total = qty * amount;
        var totalnew = (parseFloat(total).toFixed(currency_decimal));//.formatMoney(currency_decimal, '.', ',')
        $('#tot_' + id).text(totalnew);
        cal_discount_prq(id)
    }

    function save_prq_base_items() {

        var selected = [];
        var amount = [];
        var qty = [];
        var discount = [];
        var discountamt = [];
        var taxtype = [];
        $('#table_body_pr_detail input:checked').each(function () {
            if ($('#amount_' + $(this).val()).val() == '') {
                swal("Cancelled", "Ordered Item cannot be blank !", "error");
            } else {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
                qty.push($('#qty_' + $(this).val()).val());
                discount.push($('#discount_prq_' + $(this).val()).val());
                discountamt.push($('#discount_amount_prq_' + $(this).val()).val());
                taxtype.push($('#text_type_' + $(this).val()).val());
            }
        });

        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'DetailsID': selected,
                    'purchaseOrderID': purchaseOrderID,
                    'amount': amount,
                    'qty': qty,
                    'discount': discount,
                    'discountamt': discountamt,
                    'taxtotal': $('#taxaplcamnt').val(),
                    'taxtype': taxtype
                },
                url: "<?php echo site_url('Procurement/save_prq_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        $('#prq_base_modal').modal('hide');
                        setTimeout(function () {
                            fetch_po_detail_table();
                        }, 300);
                    } else {
                        myAlert('w', data['data'], 1000);
                    }

                }, error: function () {
                    $('#prq_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function cal_discount_prq(id) {
        var discount = $('#discount_prq_' + id).val();
        var qty = $('#qty_' + id).val();
        var amount = $('#amount_' + id).val();
        var estimatedAmount = parseFloat(amount * qty);
        var totalval = 0;
        var discountval = 0;
        if (discount < 0 || discount > 100) {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $('#discount_prq_' + id).val(0);
            $('#discount_amount_prq_' + id).val(0);
        } else {
            if (discount > 0) {
                discountval = (parseFloat(amount) / 100) * parseFloat(discount);
                totalval = (amount - discountval) * qty;
                $('#tot_' + id).text(parseFloat(totalval).toFixed(2));
                $('#discount_amount_prq_' + id).val(discountval);
            }
        }
        if (discount <= 0) {
            $('#discount_prq_' + id).val(0);
            $('#discount_amount_prq_' + id).val(0);
        }

    }

    function cal_discount_amt(id) {
        var discountamt = parseFloat($('#discount_amount_prq_' + id).val());
        var qty = $('#qty_' + id).val();
        var amount = $('#amount_' + id).val();
        var totalval = 0;
        var discountpercent = 0;
        if (discountamt < 0 || discountamt > amount) {
            swal("Cancelled", "Discount Amount should be less than unit price", "error");
            $('#discount_prq_' + id).val(0);
            $('#discount_amount_prq_' + id).val(0);
        } else {
            if (discountamt > 0) {
                //discountpercent = (parseFloat(discountamt) / amount) *100;
                discountpercent = (parseFloat(discountamt) / amount) * 100;
                totalval = (amount - discountamt) * qty;
                $('#tot_' + id).text(parseFloat(totalval).toFixed(2));
                $('#discount_prq_' + id).val(discountpercent);
            } else {
                $('#discount_prq_' + id).val(0);
                $('#discount_amount_prq_' + id).val(0);
            }
        }
    }


    function fetch_last_grn_amount(det, itemAutoId, currencyID, supplierPrimaryCode) {
        //$(det).closest('tr').find('.estimatedAmount').val('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoId': itemAutoId, 'currencyID': currencyID, 'supplierPrimaryCode': supplierPrimaryCode,'purchaseOrderID': purchaseOrderID},
            url: "<?php echo site_url('Procurement/fetch_last_grn_amount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    $(det).closest('tr').find('.estimatedAmount').val(data['receivedAmount']);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function fetch_last_grn_amount_edit(itemAutoId, currencyID, supplierPrimaryCode) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoId': itemAutoId, 'currencyID': currencyID, 'supplierPrimaryCode': supplierPrimaryCode},
            url: "<?php echo site_url('PurchaseRequest/fetch_last_grn_amount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    $('#estimatedAmount_edit').val(data['receivedAmount']);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function delete_discount(id) {
        if (purchaseOrderID) {
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
                        data: {'purchaseOrderID': id, 'taxtotal': $('#taxaplcamnt').val()},
                        url: "<?php echo site_url('Procurement/delete_purchase_order_discount'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1])
                            if (data[0] == 's') {
                                fetch_po_detail_table();
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_discount(id) {
        $('#discsubmitbtn').removeClass('hidden');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': id},
            url: "<?php echo site_url('Procurement/edit_discount'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    $('#discpercentage').val(data['generalDiscountPercentage']);
                    $('#disc_amount').val(data['generalDiscountAmount']);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function open_all_notes(val) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'docid': 'PO','type':val},
            url: "<?php echo site_url('Procurement/open_all_notes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#allnotebody').empty();
                    var x = 1;
                    $.each(data, function (key, value) {
                        $('#allnotebody').append('<label class="chkboxlabl" ><input type="hidden" name="check_type" value="'+val+'"><input type="radio" name="allnotedesc" value="' + value['autoID'] + '" id="chkboxlabl_' + value['autoID'] + '">' + value['description'] + '</label>')
                        x++;
                    });
                    $("#all_notes_modal").modal({backdrop: "static"});
                } else {
                    myAlert('w', 'No Notes assigned')
                }
            }
        });
    }

    function load_default_note(val) {
        if (p_id) {

        } else {
            var docid = 'PO';
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'docid': docid,'type':val},
                url: "<?php echo site_url('Procurement/load_default_note'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
                        if(val==10){
                            tinyMCE.get("Note").setContent(data['description']);
                        }
                        if(val==11){
                            tinyMCE.get("paymentTerms").setContent(data['description']);
                        }
                    }
                }
            });
        }
    }

    function save_notes() {
        var data = $("#all_notes_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Procurement/load_notes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                // $('#Note ~ iframe').contents().find('.wysihtml5-editor').html('');
                // $('#Note ~ iframe').contents().find('.wysihtml5-editor').html(data['description']);
                if(data['typeID']==10){
                    tinyMCE.get("Note").setContent(data['description']);
                }

                if(data['typeID']==11){

                    tinyMCE.get("paymentTerms").setContent(data['description']);
                }

                $("#all_notes_modal").modal('hide');
            }, error: function () {
                stopLoad();
            }
        });
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
                if (data['status'] == true) {
                    $('#emp_model').modal('hide');
                    fetch_supplierdrop(data['last_id'], ' ');
                    fetch_supplier_currency_by_id(data['last_id']);

                } else if (data['status'] == false) {
                    $('#emp_model').modal('show');

                }
                stopLoad();
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
            data: {supplier: supplier_id, DocID: page, Documentid: 'PO'},
            url: "<?php echo site_url('Procurement/fetch_supplier_Dropdown_all'); ?>",
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

    function load_line_tax_amount(ths) {

        var qut = $(ths).closest('tr').find('.quantityRequested').val();
        var itemAutoID = $(ths).closest('tr').find('.itemAutoID').val();
        var amount = $(ths).closest('tr').find('.estimatedAmount').val();
        var discoun = $(ths).closest('tr').find('.discount_amount').val();
        var taxtype = $(ths).closest('tr').find('.text_type').val();

        var lintaxappamnt = 0;
        if (jQuery.isEmptyObject(qut)) {
            qut = 0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount = 0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun = 0;
        }
        lintaxappamnt = (qut * (amount));

        if (!jQuery.isEmptyObject(taxtype)) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'purchaseOrderID': purchaseOrderID,
                    'applicableAmnt': lintaxappamnt,
                    'taxtype': taxtype,
                    'itemAutoID': itemAutoID,
                    'discount': discoun,
                    'quantityRequested': qut,
                },
                url: "<?php echo site_url('Procurement/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $(ths).closest('tr').find('.linetaxamnt').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.net_amount').text((((parseFloat(data) + parseFloat(lintaxappamnt))) - (parseFloat(discoun) * parseFloat(qut))).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        } else {

            $(ths).closest('tr').find('.linetaxamnt').text('0');
            $(ths).closest('tr').find('.net_amount').text(((parseFloat(lintaxappamnt) - (parseFloat(discoun) * parseFloat(qut)))).toFixed(currency_decimal));
        }

    }

    function load_line_tax_amount_edit(ths) {

        var qut = $('#quantityRequested_edit').val();
        var amount = $('#estimatedAmount_edit').val();
        var discoun = $('#discount_amount_edit').val();
        var taxtype = $('#text_type_edit').val();
        var itemAutoID = $('#itemAutoID_edit').val();
        var lintaxappamnt = 0;
        if (jQuery.isEmptyObject(qut)) {
            qut = 0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount = 0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun = 0;
        }
        lintaxappamnt = (qut * (amount));
        if (!jQuery.isEmptyObject(taxtype)) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'purchaseOrderID': purchaseOrderID,
                    'applicableAmnt': lintaxappamnt,
                    'taxtype': taxtype,
                    'itemAutoID': itemAutoID,
                    'purchaseOrderDetailsID': purchaseOrderDetailsID,
                    'discount': discoun,
                    'quantityRequested': qut
                },
                url: "<?php echo site_url('Procurement/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $('#linetaxamnt_edit').text(data.toFixed(currency_decimal));
                    $('#totalAmount_edit').text((((parseFloat(data) + parseFloat(lintaxappamnt))) - (parseFloat(discoun) * parseFloat(qut))).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        } else {
            $('#linetaxamnt_edit').text('0');
            $('#totalAmount_edit').text((((parseFloat(lintaxappamnt))) - (parseFloat(discoun) * parseFloat(qut))).toFixed(currency_decimal));
        }

    }


    function load_line_tax_amount_prq(id) {

        var qut = $('#qty_' + id).val();

        var amount = $('#amount_' + id).val();
        var discoun = $('#discount_amount_prq_' + id).val();
        var taxtype = $('#text_type_' + id).val();
        var grosamount = amount-discoun;

        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = (qut * (grosamount));
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    purchaseOrderID: purchaseOrderID,
                    applicableAmnt: lintaxappamnt,
                    taxtype: taxtype,
                    'purchaseRequestDetailsID': id,
                    'discount': discoun,
                    'quantityRequested': qut
                },
                url: "<?php echo site_url('Procurement/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    refreshNotifications();
                    stopLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $('#lintaxamntprq_' + id).text(data.toFixed(currency_decimal));
                    $('#tot_' + id).text((data + lintaxappamnt).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        } else {
            $('#lintaxamntprq_' + id).text('0');
        }

    }

    $('#itemmaster_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            /*revanueGLAutoID: {validators: {notEmpty: {message: 'Revanue GL Code is required.'}}},
             costGLAutoID: {validators: {notEmpty: {message: 'Cost GL Code is required.'}}},
             assteGLAutoID: {validators: {notEmpty: {message: 'Asste GL Code is required.'}}},*/
            seconeryItemCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_item_code_is_required');?>.'}}},/*Item Code is required*/
            itemName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_item_name_is_required');?>.'}}},/*Item Name is required*/
            itemDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_item_description_is_required');?>.'}}},/*Item Description is required*/
            mainCategoryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_main_category_is_required');?>.'}}},/*Main category is required*/
            subcategoryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_sub_category_is_required');?>.'}}},/*Sub category is required*/
            defaultUnitOfMeasureID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_unit_of_measure_is_required');?>.'}}},/*Unit of measure is required*/
            /*                    maximunQty              : {validators: {notEmpty: {message: 'Maximun Qty is required.'}}},
             minimumQty              : {validators: {notEmpty: {message: 'Minimum Qty is required.'}}},
             reorderPoint            : {validators: {notEmpty: {message: 'Reorder Point is required.'}}},*/
        },
    }).on('success.form.bv', function (e) {
        $('#submitbtn').prop('disabled', false);
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'itemAutoID', 'value': itemAutoID});
        data.push({'name': 'revanue', 'value': $('#revanueGLAutoID option:selected').text()});
        data.push({'name': 'cost', 'value': $('#costGLAutoID option:selected').text()});
        data.push({'name': 'asste', 'value': $('#assteGLAutoID option:selected').text()});
        data.push({'name': 'mainCategory', 'value': $('#mainCategoryID option:selected').text()});
        data.push({'name': 'uom', 'value': $('#defaultUnitOfMeasureID option:selected').text()});
        data.push({'name': 'stockadjustment', 'value': $('#stockadjust option:selected').text()});
        data.push({'name': 'generatedtype', 'value': 'third'});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('ItemMaster/save_itemmaster'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                //refreshNotifications(true);
                myAlert(data[0], data[1], data[2], data[3], data[4], data[5]);
                if (data[0] == 's') {
                    setTimeout(function () {
                        $('#f_search_' + search_id).closest('tr').find('.itemAutoID').val(data[2]);
                    }, 200);

                    $('#f_search_' + search_id).closest('tr').find('.f_search').val(data[4]);
                    fetch_related_uom_id(data[5], data[5], '#f_search_' + search_id);
                    fetch_last_grn_amount('#f_search_' + search_id, data[2], $('#transactionCurrencyID').val(), $('#supplierPrimaryCode').val());

                    $('#add_itemmaster').modal('hide');
                    if (data[3]) {
                        $('#barcode').val(data[3]);
                    }
                    //$("#mainCategoryID").readonly();
                    //$('#mainCategoryID').prop('readonly', true);
                    //$("#mainCategoryID").prop("disabled", false);
                    $("#defaultUnitOfMeasureID").prop("disabled", false);
                    $("#secondaryUOMID").prop("disabled", false);
                    faID = data[2];

                    var imgageVal = new FormData();
                    imgageVal.append('faID', faID);

                    var files = $("#itemImage")[0].files[0];
                    imgageVal.append('files', files);

                    if (files == undefined) {
                        //$('#itemmaster_form')[0].reset();
                        $('.btn-wizard').removeClass('disabled');
                        //$('#itemmaster_form').bootstrapValidator('resetForm', true);
                        $("#itm_documentSystemCode").val(faID);
                        // attachment_modal_itemMaster(faID, "Item Master", "ITM");
                        $('[href=#step2]').tab('show');
                        return false;
                    }

                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        data: imgageVal,
                        contentType: false,
                        cache: false,
                        processData: false,
                        url: "<?php echo site_url('ItemMaster/item_image_upload'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            //$('#itemmaster_form')[0].reset();
                            $('.btn-wizard').removeClass('disabled');
                            $("#itm_documentSystemCode").val(faID);
                            // attachment_modal_itemMaster(faID, "Item Master", "ITM");
                            //$('#itemmaster_form').bootstrapValidator('resetForm', true);
                            $('[href=#step2]').tab('show');
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                } else {
                    $('.btn-primary').attr('disabled', false);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    function load_sub_sub_cat() {
        $('#subSubCategoryID option').remove();
        $('#subSubCategoryID').val("");
        var subsubid = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subSubCategoryID').empty();
                    var mySelect = $('#subSubCategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function load_gl_codes() {
        $('#revanueGLAutoID').val("");
        $('#costGLAutoID').val("");
        $('#stockadjust').val("");
        $('#assteGLAutoID').val("");
        $('#COSTGLCODEdes').val("");
        $('#ACCDEPGLCODEdes').val("");
        $('#DEPGLCODEdes').val("");
        $('#DISPOGLCODEdes').val("");
        itemCategoryID = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_gl_codes"); ?>',
            dataType: 'json',
            data: {'itemCategoryID': itemCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#revanueGLAutoID").val(data['revenueGL']).change();
                    $("#costGLAutoID").val(data['costGL']).change();
                    $("#assteGLAutoID").val(data['assetGL']).change();
                    $("#COSTGLCODEdes").val(data['faCostGLAutoID']).change();
                    $("#ACCDEPGLCODEdes").val(data['faACCDEPGLAutoID']).change();
                    $("#DEPGLCODEdes").val(data['faDEPGLAutoID']).change();
                    $("#DISPOGLCODEdes").val(data['faDISPOGLAutoID']).change();
                    $("#stockadjust").val(data['stockAdjustmentGL']).change();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


    $('#changeImg').click(function () {
        $('#itemImage').click();
    });

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };

            reader.readAsDataURL(obj.files[0]);
        }
    }

    function itemMaster_document_uplode() {
        var formData = new FormData($("#itemMaster_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    //attachment_modal_itemMaster($('#itm_documentSystemCode').val(), 'Item Master', 'ITM');
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function validate_itempull(id, type) {
        if (itemAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'typevalue': id, 'Type': type, 'itemAutoID': itemAutoID},
                url: "<?php echo site_url('ItemMaster/item_type_pull'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    if (data['typechange'] == 1) {
                        if (!jQuery.isEmptyObject(data['item'])) {
                            changeFormCode();
                            switch (data['cattype']) {
                                case "Main":
                                    $('#mainCategoryID').val(data['typevalue']);
                                    changeFormCode();
                                    load_sub_cat(data['typevaluesub']);
                                    $('#subcategoryID').val(data['typevaluesub']);
                                    load_sub_sub_cat(data['typevaluesubsub']);
                                    $('#subSubCategoryID').val(data['typevaluesubsub']);
                                    break;
                                case "Sub":
                                    load_sub_cat(data['typevalue']);
                                    $('#subcategoryID').val(data['typevalue']);
                                    load_sub_sub_cat(data['typevaluesubsub']);
                                    $('#subSubCategoryID').val(data['typevaluesubsub']);
                                    break;
                                case "UomDe":
                                    $('#defaultUnitOfMeasureID').val(data['typevalue']);
                                    break;
                                case "SecUom":
                                    $('#secondaryUOMID').val(data['typevalue']);
                                    break;
                                case "revenueGL":
                                    $('#revanueGLAutoID').val(data['typevalue']).change();
                                    break;
                                case "costGL":
                                    $('#costGLAutoID').val(data['typevalue']).change();
                                    break;
                                case "assetGL":
                                    $('#assteGLAutoID').val(data['typevalue']).change();
                                    break;
                                case "stockAdjustment":
                                    $('#stockadjust').val(data['typevalue']).change();
                                    break;

                            }
                            $('#access_denied_body').empty();
                            x = 1;
                            if (jQuery.isEmptyObject(data['item'])) {
                                $('#access_denied_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                            } else {
                                $.each(data['item'], function (key, value) {
                                    $('#access_denied_body').append('<tr><td>' + x + '</td><td>' + value['documentcode'] + '</td><td>' + value['documentType'] + '</td><td>' + value['referanceNo'] + '</td></tr>');
                                    x++;
                                });
                            }
                            $('#access_denied').modal('show');

                        }
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

    function load_sub_cat(select_val) {
        changeFormCode();
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subSubCategoryID').val("");
        $('#subSubCategoryID option').remove();
        var subid = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function changeFormCode() {
        itemCategoryID = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_category_type_id"); ?>',
            dataType: 'json',
            data: {'itemCategoryID': itemCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    if ((data['categoryTypeID'] == 2) || (data['categoryTypeID'] == 4)) {
                        $("#assetGlCode_div").addClass("hide");
                        $("#cls_maximunQty").addClass("hide");
                        $("#cls_minimumQty").addClass("hide");
                        $("#cls_reorderPoint").addClass("hide");
                        $("#stockadjustment").addClass("hide");

                    } else {
                        $("#assetGlCode_div").removeClass("hide");
                        $("#cls_maximunQty").removeClass("hide");
                        $("#cls_minimumQty").removeClass("hide");
                        $("#cls_reorderPoint").removeClass("hide");
                        $("#stockadjustment").removeClass("hide");


                    }
                    if (data['categoryTypeID'] == 3) {
                        $("#inventry_row_div").addClass("hide");
                        $("#fixed_row_div").removeClass("hide");
                        $("#stockadjustment").addClass("hide");

                    } else {
                        $("#inventry_row_div").removeClass("hide");
                        $("#fixed_row_div").addClass("hide");


                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function validateBarCode(code) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'barCode': code, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('ItemMaster/item_barcode_validate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#barcode_validate_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#barcode_validate_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                    } else {
                        var barcodeVal = $('#barcode').val();
                        $.each(data, function (key, value) {
                            $('#barcode_validate_body').append('<tr><td>' + x + '</td><td>' + value['documentcode'] + '</td><td>' + value['item'] + '</td><td>' + barcodeVal + '</td></tr>');
                            x++;
                        });
                        $('#barcode').val('');
                    }
                    $('#barcode_validate').modal('show');
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

    function add_item_master() {
        $('#itemmaster_form')[0].reset();
        $('#itemmaster_form').bootstrapValidator('resetForm', true);
        $('#mainCategoryID').val('');
        $('#defaultUnitOfMeasureID').val('');
        $('#itemautoid_item').val('');
        $('#secondaryUOMID').val('');
        $('#subcategoryID').val('');
        $('#subSubCategoryID').val('');
        $('#companyLocalPurchasingPrice').val(0);
        $('#companyLocalSellingPrice').val(0);
        $('#itemName').val('');
        $('#itemDescription').val('');
        $('#seconeryItemCode').val('');
        $('#barcode').val('');
        $('#partno').val('');
        $('#maximunQty').val('');
        $('#minimumQty').val('');
        $('#reorderPoint').val('');
        $('#add_itemmaster').modal('show');
    }

    function fetch_purchase_price(purchaseprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: purchaseOrderID,
                purchaseprice: purchaseprice,
                //unitOfMeasureID: unitOfMeasureID,
                //itemAutoID: itemAutoID,
                tableName: 'srp_erp_purchaseordermaster',
                primaryKey: 'purchaseOrderID',

            },
            url: "<?php echo site_url('ItemMaster/fetch_purchase_price'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.estimatedAmount').empty();
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function fetch_line_tax_and_vat(itemAutoID, element) {
        select_VAT_value = '';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': purchaseOrderID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Procurement/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.text_type').empty();
                var mySelect = $(element).parent().closest('tr').find('.text_type');
                mySelect.append($('<option></option>').val('').html('Select Tax'));
                load_line_tax_amount(element)

                if (!jQuery.isEmptyObject(data['tax_drop'])) {
                    $.each(data['tax_drop'], function (val, text) {
                        mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                    });
                    if(select_VAT_value == ''){

                        if(data['selected_itemTax']!=0){
                            $(element).closest('tr').find('.text_type').val(data['selected_itemTax']).change();
                        }else{

                            $(element).closest('tr').find('.text_type').val(null).change();
                        }
                        change_amount(element);
                    }

                    if (select_VAT_value) {
                        $(element).closest('tr').find('.text_type').val(select_VAT_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function edit_fetch_line_tax_and_vat(itemAutoID) {
        var selected_itemAutoID = $('#itemAutoID_edit').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID, 'purchaseOrderID': purchaseOrderID},
            url: "<?php echo site_url('Procurement/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                $('#text_type_edit').empty();
                var mySelect = $('#text_type_edit');
                mySelect.append($('<option></option>').val('').html('Select Tax'));

                if (!jQuery.isEmptyObject(data['tax_drop'])) {
                    $.each(data['tax_drop'], function (val, text) {
                        mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                    });

                    if(selected_itemAutoID!=itemAutoID){

                        if(data['selected_itemTax']!=0){
                            $('#text_type_edit').val(data['selected_itemTax']);
                        }else{
                            $('#text_type_edit').val(null);
                        }
                        change_amount_edit();
                    }else{
                        if (select_VAT_value) {
                            $('#text_type_edit').val(select_VAT_value);
                        }
                    }
                }
                load_line_tax_amount_edit()
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function fetch_tax_drop(id, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Procurement/fetch_tax_drop_itemwise'); ?>",
            success: function (data) {
                $('#text_type_' + id).empty();
                var mySelect = $('#text_type_' + id);
                mySelect.append($('<option></option>').val('').html('Select Tax Type'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                    });
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function setQty(purchaseRequestDetailsID, amount, reqqty) {
        var reqQtyId = "#requested_qty_" + purchaseRequestDetailsID;
        var ordQtyId = "#qty_" + purchaseRequestDetailsID;
        $(ordQtyId).val($(reqQtyId).text());

        var data = {value: $(ordQtyId).val()};//preparing object for following function.
        select_check_box(data, purchaseRequestDetailsID, amount, reqqty);
    }

    function fetch_rcm_enableYN(supplierID) {
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
                setTimeout(function () {
                    $('#rcmYN').iCheck('uncheck');
                });
                if (rcmApplicableYN == 1) {
                    setTimeout(function () {
                        $('#rcmYN').iCheck('check');
                    });
                }
                if (data['isEligibleRCM'] == 1 && purchaseOrderID == null) {
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

    function change_order_type(ev){

        var doc_value = $(ev).val();

        if(doc_value == 'BCO'){
            $('#customer_drop').removeClass('hide');
            $('#add_purchase_detail').removeClass('hide');
            $('#po_addon').addClass('hide');
           
            $('.notbacktoback').each(function() {
                $(this).removeClass('hide');
            });
            $('.backtoback').each(function() {
                $(this).addClass('hide');
            });
            $('#logisticOption').show();
        }else if(doc_value == 'BQUT'){

            $('#customer_drop').addClass('hide');
            $('#qut_drop').removeClass('hide');
            $('#add_purchase_detail').addClass('hide');
            $('#po_addon').removeClass('hide');
          
            $('.notbacktoback').each(function() {
                $(this).addClass('hide');
            });

            $('.backtoback').each(function() {
                $(this).removeClass('hide');
            });
            $('#logisticOption').show();
          
        }else if(doc_value == 'LOG') {
            $('#customer_drop').addClass('hide');
            $('#qut_drop').addClass('hide');
            $('#add_purchase_detail').removeClass('hide');
            $('.notbacktoback').each(function () {
                $(this).removeClass('hide');
            });
            $('.backtoback').each(function () {
                $(this).addClass('hide');
            });
            $('#logisticOption').hide();
            $('#logisticYN').iCheck('uncheck');
        }
        else{
            $('#customer_drop').addClass('hide');
            $('#qut_drop').addClass('hide');
            $('#add_purchase_detail').removeClass('hide');
            $('#po_addon').addClass('hide');

            $('.notbacktoback').each(function() {
                $(this).removeClass('hide');
            });
            $('.backtoback').each(function() {
                $(this).addClass('hide');
            });
            $('#logisticOption').show();
        }

    }
          
   function change_commission_per(ev,detailAutoID){
        var percentage = $(ev).val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'percentage': percentage,'detailAutoID':detailAutoID},
            url: "<?php echo site_url('Procurement/update_commission_btb'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                fetch_po_detail_table();
              
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });

    }

    function apply_to_all_commission(ev){
        var percentage = $(ev).closest('tr').find('.commision_percentage').val();
        var row_id = $(ev).closest('tr').find('td:eq(0)').text();

            swal({
                    title: "Are you sure?",
                    text: "You want to apply to all records below",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function () {
                    $('.lineitem').each(function(i,row) {
                        if(i > (row_id - 1)){
                            $(row).closest('tr').find('.commision_percentage').val(percentage).change();
                        }
                    });
                });

    }
  
   $('#blanketpo').on('change', function(){
        if($('#blanketpo').is(":checked")){
            $('#blanket_Po_view').removeClass('hide');
            $('#blanketpo_val').val(1);
        }else {
            $('#blanket_Po_view').addClass('hide');
            $('#blanketpo_val').val(0);
        }

    });

    function fetch_cost_addon_po(){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'purchaseOrderID': purchaseOrderID},
            url: "<?php echo site_url('Procurement/get_cost_allocation_extracharges'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#cost_addon_po').empty();
                $('#cost_addon_po').html(data);
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Something went wrong", "error");
            }
        });


    }

    function add_extra_charge_detail(){

        var extraChargeID = $('#extraChargeID :selected').val();
        var extraChargeValue = $('#extraChargeValue').val();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID': purchaseOrderID,'extraChargeID':extraChargeID,'extraChargeValue':extraChargeValue},
            url: "<?php echo site_url('Procurement/set_contract_extra_charge'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                if (data[0] == 's') {
                    fetch_cost_addon_po();
                    $("#addcontractextracost").modal('hide');
                }
            
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function validate_mfq_warehouse_po(warehouseAutoID)
    {
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'warehouseAutoID': warehouseAutoID},
            url: "<?php echo site_url('inventory/check_mfq_warehouse'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['status'] === 1)
                {
                    if(data['mfqWarehouseAutoID']) {
                        $('#mfqWarehouseAutoID').val(data['mfqWarehouseAutoID']);
                    } else {
                        $('#mfqWarehouseAutoID').val();
                    }

                   
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function fetch_version_po(){
        purchase_order_table_version();
    }

    function purchase_order_table_version(selectedID=null) {
        var masterid = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        var Otable = $('#purchase_order_table_version').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Procurement/fetch_purchase_order_version'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "versionAutoID"},
                {"mData": "po_code"},
                {"mData": "po_detail"},
                {"mData": "total_value"},
                {"mData": "edit"}
            ],
            "columnDefs": [{
                "targets": [2],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "masterid", "value": masterid});
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

    function get_quotation_detail(ev){

        var qut_id = $(ev).val();

        if(qut_id){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': qut_id},
                url: "<?php echo site_url('Procurement/get_contract_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                stopLoad();
                    if(data){

                        $('#supplierPrimaryCode').val(data.supplierID).change();
                        $('#transactionCurrencyID').val(data.transactionCurrencyID).change();
                        $('#referenceNumber').val(data.referenceNo + ' - ' + data.contractCode).change();
                        $('#narration').val(data.narration).change();
                    }
                }
                , error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
             
            });
        }
                   
    }

    function open_capitalize_model(id){
        
        $('#det_id').val(id);
        
        $('#capitalize_model').modal('show');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'det_id':id},
            url: "<?php echo site_url('Procurement/fetch_po_details_by_id'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                
                if(data['capitalizationDate']){
                    $('#capitalizeDate').val(data['capitalizationDate']);
                }
                
                
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function save_capitalize_detail_po(){
        var data_det = $('#capitalize_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data_det,
            url: "<?php echo site_url('Procurement/save_capitalize_detail_po'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
                if(data[0]=='s'){
                    $('#capitalize_model').modal('hide');
                    fetch_po_detail_table();
                }
                
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function fetch_logistic_po_detail_table(poID) {
        $('.pull-li').removeClass('pulling-based-li');
        $('#pull-'+purchaseOrderID).addClass('pulling-based-li');
        if (purchaseOrderID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseOrderID': poID, 'currentPurchaseOrderID': purchaseOrderID},
                url: "<?php echo site_url('Procurement/getLogisticPoAddons'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#addon_table_body').empty();
                    $('#table_addon_foot').empty();
                    x = 1;

                    if (jQuery.isEmptyObject(data)) {
                        $('#addon_table_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                    } else {
                        $.each(data, function (key, value) {
                            $('#addon_table_body').append(
                                '<tr>' +
                                '<td>' + x + '</td>' +
                                '<td>' + value['addonCatagory'] + '<input type="hidden" name="addonDetailID[]" value="' + value['id'] + '"><input type="hidden" name="poMasterID[]" value="' + value['poAutoID'] + '"></td>' +
                                '<td class="text-right">' + parseFloat(value['bookingCurrencyAmount']).toFixed(currency_decimal) + '<input class="hidden" name="addonAmount[]" id="addonAmount_' + value['id'] + '" value="' + value['bookingCurrencyAmount'] + '"></td>' +
                                '<td class="text-right">' + parseFloat(value['logisticBalance']).toFixed(currency_decimal) + '<input class="hidden" name="addonBalance[]" id="addonBalance_' + value['id'] + '" value="' + value['logisticBalance'] + '"></td>' +
                                '<td class="text-center"><input type="text" name="matchedAmount[]" class="number" size="8" id="matchedAmount_' + value['id'] + '" onkeyup="changeMatchedAmount(this,' + value['id'] + ')" value="0"></td>' +
                                '<td class="text-center"><input type="text" name="actualLogisticAmount[]" class="number" size="8" id="actualLogisticAmount_' + value['id'] + '" value="0"></td>' +
                                '</tr>'
                            );
                            x++;
                        });
                    }
                    number_validation();
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function save_po_addon_items() {
        let data = $('#po_addon_form').serializeArray();
        data.push({'name': 'purchaseOrderID', 'value': purchaseOrderID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Procurement/saveLogisticPoAddon'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                fetch_po_detail_table();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
        refreshNotifications(true);
        stopLoad();
    }

    function edit_logistic_item(logisticId)
    {
        $('#edit_po_base_modal').modal('show');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'logisticId': logisticId},
            url: "<?php echo site_url('Procurement/getLogisticPoAddonByID'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#addon_poLogisticID').val(data['poLogisticID']);
                $('#addon_purchaseOrderCode').text(data['purchaseOrderCode']);
                $('#addon_addonAmount').text(data['addonAmount']);
                $('#addon_addonBalance').text(data['addonBalance']);
                $('#addon_matchedAmount').val(data['matchedAmount']);
                $('#addon_actualLogisticAmount').val(data['actualLogisticAmount']);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }

    function update_po_addon_items() {
        let data = $('#po_addon_edit_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Procurement/updateLogisticPoAddon'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                fetch_po_detail_table();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
        refreshNotifications(true);
        stopLoad();
    }


    function delete_logistic_item(logisticId){
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
                    data: {'poLogisticID': logisticId},
                    url: "<?php echo site_url('Procurement/deleteLogisticPoAddon'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stopLoad();
                        fetch_po_detail_table();
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function changeMatchedAmount(event, id){
        let addonBalance = $('#addonBalance_' + id).val();
        let matchedValue = $(event).val();
        if(parseFloat(addonBalance) < parseFloat(matchedValue)){
            $(event).val(0);
        }
    }

    function changeMatchedAmountEdit(event){
        let addonBalance = $('#addon_addonBalance').html();
        let matchedValue = $(event).val();
        if(parseFloat(addonBalance) < parseFloat(matchedValue)){
            $(event).val(0);
        }
    }

</script>