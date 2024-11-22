<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);
$this->load->helper('quotation_contract');
$SalesPerson = all_sales_person_drop();
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('sales_markating_transaction_quotation_contract');
echo head_page($title, false);

$operation_flow = getPolicyValues('OPF', 'All'); // policy for operation

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$umo_arr = array('' => 'Select UOM'); //all_umo_drop();
//$customer_arr = all_customer_drop();
$pID = $this->input->post('page_id');
$customer_arr = all_customer_drop(true,1);
$amendmentType_arr = array('1'=>'Change in Price','2'=>'Other Details');

if($pID != '') {
    $contractAutoID = $pID;
    $Documentid = $this->input->post('policy_id');
    $customeridcurrentdoc = all_customer_drop_isactive_inactive($pID,$Documentid);
    if(!empty($customeridcurrentdoc) && $customeridcurrentdoc['isActive'] == 0)
    {
        $customer_arr[trim($customeridcurrentdoc['customerAutoID'] ?? '')] = (trim($customeridcurrentdoc['customerSystemCode'] ?? '') ? trim($customeridcurrentdoc['customerSystemCode'] ?? '') . ' | ' : '') . trim($customeridcurrentdoc['customerName'] ?? '') . (trim($customeridcurrentdoc['customerCountry'] ?? '') ? ' | ' . trim($customeridcurrentdoc['customerCountry'] ?? '') : '');
    }

    $crew_group_to =fetch_group_to(true,1,$pID);
    $asset_group_to = fetch_group_to(true,2,$pID);
    $category_group_to =fetch_group_to_category(true,$pID);
    $contract_details = get_contract_master_with_amendments($contractAutoID);

    if($contract_details){
        $amendmentTypeSelect = explode(',',$contract_details['amendmentType']);
    }

}

$country        = load_country_drop();
$gl_code_arr    = supplier_gl_drop();
$currncy_arr    = all_currency_new_drop();
$country_arr    = array('' => 'Select Country');
$taxGroup_arr    = customer_tax_groupMaster();
$customerCategory    = party_category(1);
$activity_arr    = all_activity_type_drop(true);
if (isset($country)) {
    foreach ($country as $row) {
        $country_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
    }
}
$createmasterrecords = getPolicyValues('CMR','All');
$AdvanceCostCapture = getPolicyValues('ACC','All');
$hideWacAmount = getPolicyValues('HWC','All');
$group_based_tax =  is_null(getPolicyValues('GBT', 'All'))?0:getPolicyValues('GBT', 'All') ;
$customer_arr_masterlevel = array('' => 'Select Customer');
$segment_arr = fetch_segment();
$projectExist = project_is_exist();

$docType =fetch_docType(true);
$ticket =fetch_ticket_template(true);
$contract_type =fetch_contract_type(true);
$uom =all_umo_new_drop();

$main_category_arr = all_main_category_drop();
$revenue_gl_arr = all_revenue_gl_drop();
$cost_gl_arr = all_cost_gl_drop();
// contract employees
$employees_drop = get_contract_employee_crew(true);
$assets_drop = get_contract_assets(true);

$dalilTemplatey=fetch_dalilTemplatey_report(true);
$section = fetch_section_visibility(true);

$user =get_employee_current_company();


?>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<style>
    .chkboxlabl {
        border:1px solid #ccc;
        padding:10px;
        margin:0 0 10px;
        display:block;
        font-weight: normal;
    }

    .chkboxlabl:hover {
        background:#eee;
        cursor:pointer;
    }
    .thumbnail_custom {
        position: relative;
        z-index: 0
    }

    .thumbnail_custom:hover {
        background-color: transparent;
        z-index: 50
    }

    .thumbnail_custom span {
        position: absolute;
        background-color: #ffffe0;
        padding: 5px;
        left: -1000px;
        border: 1px dashed gray;
        visibility: hidden;
        color: #000;
        text-decoration: none
    }

    .thumbnail_custom span img {
        border-width: 0;
        padding: 2px
    }

    .thumbnail_custom:hover span {
        visibility: visible;
        top: 0;
        left: 60px
    }

    #contract_assets_table tr > *:nth-child(1) {
        display: none;
    }

    #contract_assets_table tr > *:nth-child(2) {
        display: none;
    }

    #contract_assets_table tr > *:nth-child(8) {
        display: none;
    }
    
    #contract_crew_table tr > *:nth-child(1) {
        display: none;
    }

    #contract_crew_table tr > *:nth-child(2) {
        display: none;
    }

    #contract_crew_table tr > *:nth-child(9) {
        display: none;
    }


</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>" />
<div id="filter-panel" class="collapse filter-panel"></div>

<div class="col-md-12 hide" style="margin-bottom: 20px;" id="amendmentNotice">
    <span class="badge badge-danger" style="background:red; color:white;">Documnet Open for Amendments</span>
</div>

<div class="m-b-md" id="wizardControl">
    
        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_one');?> - <?php echo $this->lang->line('sales_markating_transaction_header');?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_detail_table();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_two');?> - <?php echo $this->lang->line('sales_markating_transaction_detail');?> </span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="fetch_crew_table();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_three');?> - <?php echo $this->lang->line('sales_markating_transaction_crew');?> </span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step4" onclick="fetch_assets_table();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_four');?> - <?php echo $this->lang->line('sales_markating_transaction_assets');?> </span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step5" onclick="fetch_assign_checklist_table();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_five');?> - <?php echo $this->lang->line('sales_markating_transaction_checklist');?> </span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step6" onclick="fetch_visibility_table();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label">STEP 6 -  Visibility</span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step7" onclick="load_conformation();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label">STEP 7 - <?php echo $this->lang->line('sales_markating_transaction_confirmation');?></span>
            </a>
        </div>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="quotation_contract_form"'); ?>
        <div class="row">
            <?php if($createmasterrecords==1){?>
                <div class="form-group col-sm-3">
                    <label for="customerName"><?php echo $this->lang->line('common_customer_name');?><?php  required_mark(); ?></label><!--Customer Name-->
                    <div class="input-group">
                        <div id="div_customer_drop">
                            <?php echo form_dropdown('customerID', $customer_arr_masterlevel, '', 'class="form-control select2" id="customerID" onchange="Load_customer_currency(this.value);Load_customer_details(this.value);"'); ?>
                        </div>
                        <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Customer" rel="tooltip" onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                </div>
            <?php } else { ?>
            <div class="form-group col-sm-3">
                <label for="customerID"><span id="party_text"><?php echo $this->lang->line('common_customer_name');?> </span> <?php required_mark(); ?></label><!--Customer Name-->
                <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="customerID" required onchange="Load_customer_currency(this.value);Load_customer_details(this.value);"'); ?>
            </div>
            <?php }?>
            
            <input type='hidden' name="amendmentID" id="amendmentID" value="" />
        
            <div class="form-group col-sm-3">
                <label for="segment"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></label><!--Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment" required'); ?>
            </div>

            <?php if($AdvanceCostCapture ==1) { ?>
                <div class="form-group col-sm-3">
                    <label for="segment">Activity Code</label><!--Segment-->
                    <?php echo form_dropdown('activityCode', $activity_arr, '', 'class="form-control select2" id="activityCode"'); ?>
                </div>
            <?php } ?>

            <div class="form-group col-sm-3">
                <label><?php echo $this->lang->line('common_document_date');?><?php required_mark(); ?></label><!--Document Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="contractDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="contractDate"
                           class="form-control" required>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('sales_markating_transaction_document_document_currency');?>  <?php required_mark(); ?></label><!--Document Currency -->
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="currency_validation(this.value)" id="transactionCurrencyID" required'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_expiry_date');?> <?php required_mark(); ?></label><!--Document Expiry Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="contractExpDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="contractExpDate"
                           class="form-control" required>
                </div>
            </div>

            <div class="form-group col-sm-3 hide" id="amendedExpDate">
                <label><?php echo $this->lang->line('sales_markating_transaction_amended_document_expiry_date');?> <?php required_mark(); ?></label><!--Document Expiry Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="contractAmdExpDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="contractAmdExpDate"
                           class="form-control" required>
                </div>
            </div>


            <div class="form-group col-md-3">
                        <label for="">Payment Terms </label><!--Payment terms-->
                        <div class="input-group">
                            <div class="input-group-addon">Days</div>
                            <input type="text" class="form-control text-right" id="paymentTerms" name="paymentTerms">
                        </div>
            </div>

            
            
        </div>
        <div class="row">

            <div class="form-group col-sm-3">
                <label for="contactPersonName"><?php echo $this->lang->line('sales_markating_transaction_document_contact_person_name');?> </label><!--Contact Person Name-->
                <input type="text" class="form-control " id="contactPersonName" name="contactPersonName">
            </div>
           
            <div class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('sales_markating_transaction_document_persons_telephone_number');?> </label><!--Person's Telephone Number-->

                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <input type="text" class="form-control " id="contactPersonNumber" name="contactPersonNumber">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="contactPersonName"><?php echo $this->lang->line('sales_markating_transaction_email');?> </label><!--Contact Person Name-->
                <input type="text" class="form-control " id="email" name="email">
            </div> 

            <div class="form-group col-sm-3">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_reference');?> # </label><!--Reference-->
                <input type="text" name="referenceNo" id="referenceNo" class="form-control">
            </div>
                    
            
        </div>

        <div class="row">
            <div class="form-group col-md-3">
                <label><?php echo $this->lang->line('sales_markating_transaction_contract_type');?> </label><!--Sales Person-->
                <?php echo form_dropdown('contractType', $contract_type, '', 'class="form-control select2" id="contractType"'); ?>
            </div>
            <div class="form-group col-md-3">
                <label><?php echo $this->lang->line('sales_markating_transaction_sales_person');?> </label><!--Sales Person-->
                <?php echo form_dropdown('salesperson', $SalesPerson, '', 'class="form-control select2" id="salesperson"'); ?>
            </div>

            <div class="form-group col-sm-3">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_narration');?> </label><!--Narration-->
                <textarea class="form-control" rows="3" name="contractNarration" id="contractNarration"></textarea>
            </div>
        </div>

        <div class="row">

            <div class="form-group col-md-3">
                <label>Job Template </label>
                <?php echo form_dropdown('ticket', $ticket, '', 'class="form-control select2" onchange="set_job_edit_activity(this.value)" id="ticket"'); ?>
            </div>

            <div class="form-group col-md-3 hide" id="job_activity_status">
                <label for="">Allow Editing Job</label><!-- Show Item Image-->

                <div class="skin skin-square">
                    <div class="skin-section" id="extraColumns">
                        <input id="editJobBillingYN" type="checkbox"
                            data-caption="" class="columnSelected" name="editJobBillingYN" value="1">
                        <label for="checkbox">
                            &nbsp;
                        </label>
                    </div>
                </div>
            </div> 
            
            <div class="form-group col-md-3">
                <label>Daily Report Template</label>
                <?php echo form_dropdown('dalilTemplatey', $dalilTemplatey, '', 'class="form-control select2" id="dalilTemplatey"'); ?>
            </div> 

            <div class="form-group col-md-3">
                <label><?php echo $this->lang->line('sales_markating_transaction_doc_type');?> </label><!--Sales Person-->
                <?php echo form_dropdown('docType', $docType, '', 'class="form-control select2" id="docType"'); ?>
            </div>
            
            <div class="form-group col-md-3">
                <label for=""><?php echo $this->lang->line('sales_markating_transaction_link_activity');?></label><!-- Show Item Image-->

                <div class="skin skin-square">
                    <div class="skin-section" id="extraColumns">
                        <input id="LinkActivityYN" type="checkbox"
                            data-caption="" class="columnSelected" name="LinkActivityYN" value="1">
                        <label for="checkbox">
                            &nbsp;
                        </label>
                    </div>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-md-8">
                <!-- <div class="row">          
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><?php echo $this->lang->line('sales_marketing_show_item_image');?></label>

                            <div class="skin skin-square">
                                <div class="skin-section" id="extraColumns">
                                    <input id="showImageYN" type="checkbox"
                                        data-caption="" class="columnSelected" name="showImageYN" value="1">
                                    <label for="checkbox">
                                        &nbsp;
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="row">    
                    
                </div>
                <div class="row">    
                    
                    <div class="form-group col-md-4">
                        <div class="form-group">
                                <label for="">Contract Value</label><!-- Show Item Image-->

                                <input type="text" class="form-control number" id="contactValue" name="contactValue" placeholder="0.00">
                            </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label for="RVbankCode"><?php echo 'Bank or Cash' ?><!--Bank or Cash--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('RVbankCode', company_bank_account_drop(), '', 'class="form-control select2" id="RVbankCode" onchange="set_payment_method()" required'); ?>
                    </div>      
                </div>

                <div class="row"> 
                    <div class="form-group col-md-12">
                        <label><?php echo $this->lang->line('sales_markating_transaction_other_details');?> </label><!--Sales Person-->
                      
                    </div>  
                </div>
            </div>
            <div class="col-md-4 hide">
                <div class="cus_master_style_tb" id="row_dim">
                    <table>
                        <tbody>                        
                            <tr>
                                <td><strong>Address with PO box</strong></td><!--Invoice Number-->
                                <td><strong>: &nbsp;</strong></td>
                                <td width="50%"><div id="addressBox"></div></td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td><!--Document Date-->
                                <td><strong>: &nbsp;</strong></td>
                                <td><div id="emailBox"></div></td>
                            </tr>
                            <tr>
                                <td><strong>Contact Number</strong></td><!--Reference Number-->
                                <td><strong>: &nbsp;</strong></td>
                                <td><div id="contactNumberBox"></div></td>
                            </tr>
                                                                <tr>
                                <td><strong>Website</strong></td><!--Reference Number-->
                                <td><strong>: &nbsp;</strong></td>
                                <td><div id="customerUrlBox"></div></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" id="cusAutoId" name="cusAutoId" value="" />
                    <input type="hidden" id="addressBoxEditH" name="addressBoxEditH" value="" />
                    <input type="hidden" id="emailBoxEditH" name="emailBoxEditH" value="" />
                    <input type="hidden" id="contactNumberBoxEditH" name="contactNumberBoxEditH" value="" />
                    <input type="hidden" id="customerUrlBoxEditH" name="customerUrlBoxEditH" value="" />
                    <div class="icon-fa"><a onclick="contract_customer_details_edit()"><i class="fa fa-edit" aria-hidden="true"></i></a></div>
                </div>    
            </div>
        </div>
        <div class="row">
        <div class="form-group col-sm-12">
            <label><?php echo $this->lang->line('common_snotes');?> </label><!--Notes-->
            <textarea class="form-control notes_termsandcond" rows="7" name="Note" id="Note"></textarea>
        </div>
        </div>
        <button class="btn btn-primary-new size-sm" type="button" onclick="open_all_notes()"><i class="fa fa-bookmark" aria-hidden="true"></i></button>
        <hr>
        <div class="text-right m-t-xs">

            <div class="form-group col-sm-4" id="div_ammendmentType">
                <?php
                     echo form_dropdown('ammendmentType[]', $amendmentType_arr, $amendmentTypeSelect , 'class="form-control" id="ammendmentType"  multiple="multiple"');
                ?>
            </div> 

            <button class="btn btn-primary-new size-lg pull-left" type="button" id="btncreateAmendment" onclick="create_amendment_contract()"><?php echo $this->lang->line('common_create_amendment');?> </button><!--Save & Next-->
            <button class="btn btn-primary-new size-lg pull-left" type="button" id="btncloseAmendment" style="display:none" onclick="close_amendment_contract()"><?php echo $this->lang->line('common_close_amendment');?> </button><!--Save & Next-->

            <button class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8">
            <?php    
                if($operation_flow != 'Almansoori'){ ?>
                <h4><i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_markating_transaction_document_item_detail');?> </h4>
            <?php } else { ?>
                <h4><i class="fa fa-hand-o-right"></i> <?php echo "Service Details" ?> </h4>
            <?php } ?>
            </div><!--Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_document_add_item');?> <!--Add Item-->
                </button>
            </div>
        </div>
        <br>
        <!-- <div class="row">
        <div class="col-md-10"> -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-condesed table-responsive" id="quotation_item_details_tbl">
                <thead>
                <tr>
                <?php    
                if($operation_flow != 'Almansoori'){ ?>
                    <th colspan="13" class="itmimagespan"> <?php echo $this->lang->line('sales_markating_transaction_document_item_details');?></th><!--Item Details-->
                <?php } else{ ?>    
                    <th colspan="13" class="itmimagespan"> <?php echo "Service Details" ?></th><!--Item Details-->
                <?php } ?>
                    <th colspan="6" class="itmimage"> <?php echo $this->lang->line('sales_markating_transaction_document_item_details');?></th><!--Item Details-->
                    <th class="lineTaxHeaderAdd" colspan="10"><?php echo $this->lang->line('common_amount');?><!--Amount--> <span class="currency">(LKR)</span></th>
                    <th class="lineTaxHeader" colspan="4"><?php echo $this->lang->line('common_amount');?><!--Amount--> <span class="currency">(LKR)</span></th>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 7%" class="itmimage">Item Image</th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_code');?></th><!--Code-->
                    <!-- <th style="min-width: 10%">Article Code	</th>
                    <th style="min-width: 10%">Ref Code	</th> -->
                    <th style="min-width: 25%" class="text-left"><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                    <th style="min-width: 25%" >Category Group </th><!--Description-->
                    <th style="min-width: 5%"><?php echo $this->lang->line('sales_markating_transaction_document_uom');?> </th><!--UOM-->
                    <?php if($AdvanceCostCapture==1){ ?>
                        <th style="min-width: 10%"> Activity Code </th>
                    <?php } ?>
                    <th style="min-width: 10%"> Product/Service </th>
                    <th style="min-width: 10%">Main Category  </th>
                    <th style="min-width: 10%">Sub Category </th>
                    <th style="min-width: 10%">Sub Sub Category </th>
                    <th style="min-width: 10%">Item </th>
                    <th style="min-width: 10%">Revenue GL Code </th>
                    <th style="min-width: 10%"> Cost GL Code</th>

                    <th style="min-width: 5%"><?php echo $this->lang->line('sales_markating_transaction_document_qty');?> </th><!--Qty-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_document_unit');?> </th><!--Unit-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_discount');?> </th><!--Discount-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('sales_markating_transaction_net_unit_price');?> </th><!--Net Unit Price-->
                    <th class="lintax"><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                    <th class="lintax">Tax Amount<!--Tax Amount--></th>
                    <th style="min-width: 12%"><?php echo $this->lang->line('common_total');?> </th><!--Total-->
                    <th style="min-width: 10%"><?php echo $this->lang->line('common_status');?> </th><!--Total-->
                    <th style="min-width: 8%"><?php echo $this->lang->line('common_action');?> </th><!--Action-->
                </tr>
                </thead>
                <tbody id="table_body">
                <tr class="danger">
                    <td colspan="10" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
                </tr>
                </tbody>
                <tfoot id="table_tfoot">

                </tfoot>
            </table></div>
        <!-- </div ></div > -->
        <br>

        <div class="row general_tax_view">
            <div class="col-md-5">
                <label for="exampleInputName2" id="tax_tot"><?php echo $this->lang->line('sales_markating_transaction_net_tax_for');?>  </label><!--Tax for-->
                <form class="form-inline" id="tax_form">
                    <?php $taxDrop = ($group_based_tax == 1 ? all_tax_formula_drop_groupByTax(1):all_tax_drop())?>
                    <div class="form-group">
                        <?php echo form_dropdown('text_type', $taxDrop, '', 'class="form-control" id="text_type" required onchange="select_text(this)" style="width: 150px;"'); ?>
                    </div>
                    <?php if ($group_based_tax != 1) { ?>
                        <div class="form-group groupTax_hide">
                            <div class="input-group">
                                <input type="text" class="form-control number" id="percentage" name="percentage"
                                    style="width: 80px;" onkeyup="cal_tax(this.value)">
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                        <div class="form-group groupTax_hide">
                            <input type="text" class="form-control number" onkeypress="return validateFloatKeyPress(this,event);" id="tax_amount" name="tax_amount"
                                style="width: 100px;" onkeyup="cal_tax_amount(this.value)">
                        </div>
                    <?php } ?>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                </form>
            </div>
            <div class="col-md-7">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th class="groupTax_hide"><?php echo $this->lang->line('sales_markating_transaction_net_tax_type');?> </th><!--Tax Type-->
                        <th><?php echo $this->lang->line('sales_markating_transaction_detail');?> </th><!--Detail-->
                        <th class="groupTax_hide"><?php echo $this->lang->line('sales_markating_transaction_tax');?> </th><!--Tax-->
                        <th><?php echo $this->lang->line('common_amount');?><span class="currency">(LKR)</span></th><!--Amount-->
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
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg next" onclick="load_crew();"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_markating_transaction_document_crew_details');?> </h4><h4></h4></div><!--Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="add_crew_details_modal()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('sales_markating_transaction_document_add_crew');?> <!--Add Crew-->
                </button>
            </div>
        </div>
        <br>
        <div class="table-responsive">
            <table class="<?php echo table_class(); ?>" id="contract_crew_table">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th></th>
                        <th></th>
                        <th style="width: 10%"><?php echo 'Employee Code'?></th><!--Code-->
                        <th style="width: 20%" class="text-left"><?php echo 'Employee Name'?> </th><!--Name-->
                        <th style="width: 15%" class="text-left"><?php echo 'Designation'?> </th><!--Designation-->
                        <th style="width: 10%" class="text-left"><?php echo 'Is Primary'?> </th><!--Designation-->
                        <th style="width: 10%" class="text-left"><?php echo 'Group To'?>
                        <th></th>
                        <th style="width: 20%" class="text-left"><?php echo 'Note'?> </th><!--Note-->
                        <th style="width: 10%" class="text-left">&nbsp </th><!--Designation-->
                    </tr>
                </thead>
                <tbody id="table_body">
            
                </tbody>
                <tfoot id="table_tfoot">

                </tfoot>
            </table>
        </div>    
        <br>

       
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg next" onclick="load_assets();"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
    </div>
    <div id="step4" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('sales_markating_transaction_document_assets_details');?> </h4><h4></h4></div><!--Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="add_assets_details_modal()" class="btn btn-primary size-xs pull-right"><i
                        class="fa fa-plus"></i>&nbsp;<?php echo $this->lang->line('sales_markating_transaction_document_add_assets');?> <!--Add Assets-->
                </button>
            </div>
        </div>
        <br>
        <div class="table-responsive">
            <table class="<?php echo table_class(); ?>" id="contract_assets_table">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 5%;">#</th>
                    <th style="min-width: 5%;">#</th>
                    <th style="min-width: 10%"><?php echo 'Code';?></th><!--Code-->
                    <th style="min-width: 10%"><?php echo 'Name';?></th><!--Code-->
                    <th style="min-width: 10%"><?php echo 'Reference Number';?></th><!--Code-->
                    <th style="min-width: 10%"><?php echo 'Group To';?></th>
                    <th style="min-width: 5%;"></th>
                    <th style="min-width: 10%">Action</th><!--Code-->
                </tr>
                </thead>
                <tbody id="table_body">
            
                </tbody>
                <tfoot id="table_tfoot">

                </tfoot>
            </table>
        </div>    
        <br>

     
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg next" onclick="load_checklist();"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
    </div>
    <div id="step5" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Check List Details </h4><h4></h4></div><!--Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="open_Check_list_model()" class="btn btn-primary-new size-xs pull-right"><i
                        class="fa fa-plus"></i>&nbsp;<?php echo $this->lang->line('sales_markating_transaction_document_add_checklist');?> <!--Add Assets-->
                </button>
            </div>
        </div>
        <br>
        <div id="table_body_checklist"></div>
        <!-- <table class="table table-bordered" id="contract_checklist_table">
            <thead>
            <tr>
                <th style="width: 10%">#</th>
                <th style="width: 20%"><?php echo 'Code';?></th>
                <th style="width: 35%"><?php echo 'Name';?></th>
                <th style="width: 10%"><?php echo 'Action';?>
                
                <th style="width: 25%"><?php echo 'Calling';?>
                <th style="width: 10%"><?php echo 'User';?>
            </tr>
            </thead>
            <tbody id="table_body_checklist">
           
            </tbody>
            <tfoot id="table_tfoot">

            </tfoot>
        </table> -->
        <br>

     
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg next" onclick="load_visibility();"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
    </div>
    <div id="step6" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> Visibility Details</h4><h4></h4></div><!--Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="open_visibility_list_model()" class="btn btn-primary-new size-xs pull-right"><i
                        class="fa fa-plus"></i> Add
                </button>
            </div>
        </div>
        <br>
        <div class="table-responsive">
            <table class="<?php echo table_class(); ?>" id="contract_visibility_table">
                <thead>
                <tr>
                    <th style="min-width: 5%">#</th>
                    <th style="min-width: 10%"><?php echo 'Section';?></th><!--Code-->
                    <th style="min-width: 10%"><?php echo 'User';?></th><!--Code-->
                    <th style="min-width: 10%"><?php echo 'Action';?>
                    <th style="min-width: 10%"><?php echo '';?>
                </tr>
                </thead>
                <tbody id="table_body">
            
                </tbody>
                <tfoot id="table_tfoot">

                </tfoot>
            </table>
        </div>    
        <br>

     
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg next" onclick="load_conformation();"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
    </div>
    <div id="step7" class="tab-pane">
        <!--<div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank"
                   href="<?php /*echo site_url('Quotation_contract/load_contract_conformation/'); */?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div>-->
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="customerInvoice_attachment_label"><?php echo $this->lang->line('sales_markating_transaction_model_title');?> </h4><br><!--Modal title-->

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name');?> </th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                        <th><?php echo $this->lang->line('common_type');?> </th><!--Type-->
                        <th><?php echo $this->lang->line('common_action');?>  </th><!--Action-->
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
            <button class="btn btn-default-new size-lg prev"  onclick="load_assets()"><?php echo $this->lang->line('common_previous');?></button><!--Previous-->
            <button class="btn btn-primary-new size-lg " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?> </button><!--Save as Draft-->
            <button class="btn btn-success-new size-lg submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?> </button><!--Confirm-->
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" id="item_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_sales_add_item_detail');?><!--Add Item Detail--></h4>
            </div>
            <form role="form" id="item_detail_form" class="form-horizontal">
                <div class="modal-body" style="overflow-x: scroll;">
                    <table class="table table-bordered table-striped table-condesed" id="item_add_table" style="table-layout: fixed">
                        <thead>
                        <tr>
                            <th style="width: 200px;">Code <?php required_mark(); ?></th><!--Item Code--><!--/--><!--Description-->
                            <!-- <th style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_Article_Code');?></th>Article Code    -->
                            <!-- <th style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_Ref_Code');?> </th>  Ref Code       -->  
                            <th style="width: 150px;"><?php echo $this->lang->line('common_description');?>  </th><!--Item ref-->
                            <th style="width: 150px"><?php echo 'Category' ?> <button type="button" class="btn btn-primary btn-xs" onclick="add_more_groupTo_category(1,1)">
                                    <i class="fa fa-plus"></i></button> </th>
                            <?php if ($projectExist == 1) { ?>
                                <th style="width: 250px;"><?php echo $this->lang->line('common_project'); ?></th><!--Project-->
                                <th style="width: 200px;">Project Category</th>
                                <th style="width: 200px;">Project Subcategory</th>

                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_markating_transaction_document_uom');?> <?php required_mark(); ?></th><!--UOM-->
                            <?php if ($AdvanceCostCapture == 1) { ?>
                                <th style="width: 150px;">Activity Code</th><!--UOM-->
                            <?php } ?>
                            <!-- <th style="width: 100px;"><?php echo $this->lang->line('sales_marketing_current_stock'); ?> -->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty');?> <?php required_mark(); ?></th><!--Qty-->
                            <!-- <?php if($hideWacAmount != 1){ ?>
                                <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_waited_average_cost');?></th>
                            <?php } ?> -->
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_markating_transaction_sales_price');?><!--Sales Price--> <span
                                    class="currency"> (LKR)</span><?php required_mark(); ?></th>
                            <th colspan="2" style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_discount');?> %</th><!--Discount-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_net_unit_cost');?> </th><!--Net Unit Cost-->
                            <th class="lintax" style="width: 155px;"><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                            <th class="lintax" style="width: 90px;">Tax Amount<!--Tax Amount--></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_net_amount');?>  </th><!--Net Amount-->
                            <th style="width: 200px;"><?php echo $this->lang->line('common_comment');?> </th><!--Comment-->

                            <th style="width: 200px;">Product/Service </th>
                            <th style="width: 200px;">Main Category </th>
                            <th style="width: 200px;"> Sub Category</th>
                            <th style="width: 200px;"> Sub Sub Category</th>
                            <th style="width: 250px;"> Item</th>
                            <th style="width: 200px;"> Revenue GL Code</th>
                            <th style="width: 200px;">Cost GL Code</th>

                            <th style="display: none;"><?php echo $this->lang->line('sales_markating_transaction_remarks');?> </th><!--Remarks-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_item()">
                                    <i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="number"  class="form-control f_search" name="search[]" id="f_search_1">
                                <!--Item ID--><!--Secondary Item Code--><!-- Item Description-->
                                <!-- <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]"> -->
                            </td>
                            
                            <!-- <td>
                                <input type="text"  name="itemarticleNo[]" class="form-control itemarticleNo"/>
                            </td>
                            <td>
                                <input type="text"  name="itemrefeNo[]" class="form-control itemrefeNo"/>
                            </td> -->
                            <td>
                                <input type="text"  name="itemReferenceNo[]" class="form-control itemReferenceNo"/>
                            </td>
                            <td>
                                <?php echo form_dropdown('groupToCategory[]', $category_group_to, '', 'class="form-control select2 groupToCategory category_t" id="groupToCategory_1" onchange=""'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div class="div_projectID_item" style="width: 200px;">
                                        <select name="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('transaction_goods_received_select_project'); ?> </option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <?php echo form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                </td>
                                <td>
                                    <?php echo form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                </td>
                            <?php } ?>
                            <td>
                            <?php echo form_dropdown('UnitOfMeasureID[]',  $uom, '', 'class="UnitOfMeasureID form-control select2" id="UnitOfMeasureIDw"'); ?>
                                <!-- <input class="hidden conversionRate_CNT" id="conversionRate_CNT" name="conversionRate_CNT">
                                <select name="UnitOfMeasureID[]" class="form-control umoDropdown" required onchange="convertPrice_CNT(this)">
                                <option value=""><?php echo $this->lang->line('sales_markating_transaction_secondary_select_uom');?> </option>
                                </select> -->
                            </td>
                            
                            <?php if ($AdvanceCostCapture == 1) { ?>
                                <td>
                                <?php echo form_dropdown('DetailctivityCode[]', $activity_arr, '', 'class="form-control select2 DetailctivityCode" id="DetailctivityCode"'); ?>
                                </td>
                            <?php } ?>
                            
                            <!-- <td>
                                <input type="text" name="currentstock[]" value="0" onfocus="this.select();"
                                       class="form-control currentstock number" readonly/>
                            </td> -->
                            <td>
                                <input type="text" name="quantityRequested[]" value="0" onfocus="this.select();" onkeyup="change_qty(this)" onchange="load_line_tax_amount(this)"
                                       class="form-control quantityRequested number"/>
                            </td>
                            <!-- <?php if($hideWacAmount != 1){ ?>
                                <td>&nbsp;<span class="wac_cost pull-right"
                                                style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <?php } ?> -->

                            <td>
                                <input type="text" name="estimatedAmount[]"  value="0" onchange="load_line_tax_amount(this)"
                                       onkeyup="change_amount(this)" onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number estimatedAmount">
                            </td>
                            <td style="width: 100px;">
                                <div class="input-group">
                                    <input type="text" name="discount[]"  value="0" onchange="load_line_tax_amount(this)"
                                           onkeyup="cal_discount(this)" onfocus="this.select();" class="form-control number discount">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 100px;">
                                <input type="text" name="discount_amount[]"  value="0" onchange="load_line_tax_amount(this)"
                                       onkeyup="cal_discount_amount(this)" onfocus="this.select();" class="form-control number discount_amount">
                            </td>
                            <td>&nbsp;<span id="net_unit_cost" class="net_unit_cost pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>

                            <td class="lintax">
                                <?php echo form_dropdown('text_type[]', all_tax_formula_drop_groupByTax(1), '', 'class="form-control text_type" style="width: 134px;" onchange="load_line_tax_amount(this)" '); ?>
                            </td>
                            
                            <td class="lintax"><span class="linetaxamnt pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>

                            <td>&nbsp;<span class="net_amount pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <td>
                                <textarea class="form-control" rows="1" name="comment[]"
                                          placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_comment');?>..."></textarea><!--Item Comment-->
                            </td>
                            <td>
                                <?php echo form_dropdown('pOrService[]',  array('' => 'Select','1'=>'Product','2'=>'service'), '', 'class="pOrService form-control select2" id=""'); ?>
                            </td>
                            <td>
                            <?php echo form_dropdown('mainCategoryID[]', $main_category_arr, 'Each', 'class="form-control mainCategoryID" id="mainCategoryID_1" onchange="load_sub_cat(this);"'); ?>
                            </td>
                            <td>

                                <select name="subcategoryID[]" id="subcategoryID_1" class="form-control searchbox subcategoryID"
                                    onchange="load_sub_item(this);generate_sub_sub_category_drop(this)">
                                <option value="">
                                    <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                                </select>
                            </td>

                            <td>

                                <select name="subsubcategoryID[]" id="subsubcategoryID_1" class="form-control searchbox subsubcategoryID"
                                    onchange="">
                                <option value="">
                                    <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                                </select>
                            </td>

                            <td>
                                <select name="itemID[]" id="itemID_1" class="form-control select2 itemID" onchange="load_item_glcode(this);">
                                <option value="">
                                    <?php echo $this->lang->line('transaction_select_item'); ?><!--Select Item--></option>
                                </select>
                            </td>

                            <td>
                            <?php echo form_dropdown('revanueGLAutoID[]', $revenue_gl_arr, '', 'class="form-control select2 revanueGLAutoID" id="revanueGLAutoID_1" '); ?>
                            </td>

                            <td>
                            <?php echo form_dropdown('costGLAutoID[]', $cost_gl_arr, '', 'class="form-control select2 costGLAutoID" id="costGLAutoID_1" '); ?>
                            </td>

                            <td style="display: none;">
                                <textarea class="form-control" rows="1" name="remarks[]"
                                          placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_remarks');?>..."></textarea><!--Item Remarks-->
                            </td>
                            <td class="remove-td"
                                style="vertical-align: middle;text-align: center;display: block;"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default-new size-sm" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveItemOrderDetail()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="edit_item_detail_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('sales_markating_transaction_edit_item_details');?> </h4><!--Edit Item Detail-->
            </div>
            <form role="form" id="edit_item_detail_form" class="form-horizontal">
                <div class="modal-body" style="overflow-x: scroll;">
                    <table class="table table-bordered table-striped table-condesed" id="edit_item_add_form" style="table-layout: fixed">
                        <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_item_code');?><?php required_mark(); ?></th><!--Item Code / Description-->
                            <!-- <th style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_Article_Code');?></th>Article Code    -->
                            <!-- <th style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_Ref_Code');?> </th> Ref Code            -->                     -->
    
                              <th style="width: 150px;"><?php echo $this->lang->line('common_description');?></th><!--Item ref-->
                              <th style="width: 150px"><?php echo 'Category' ?> <button type="button" class="btn btn-primary btn-xs" onclick="add_more_groupTo_category(1,1)">
                                    <i class="fa fa-plus"></i></button> </th>
                            <?php if ($projectExist == 1) { ?>
                                <th style="width: 250px;"><?php echo $this->lang->line('common_project'); ?></th><!--Project-->
                                <th style="width: 200px;">Project Category</th>
                                <th style="width: 200px;">Project Subcategory</th>

                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_markating_transaction_document_uom');?> <?php required_mark(); ?></th><!--UOM-->
                            <!-- <th style="width: 150px;"><?php echo $this->lang->line('sales_marketing_current_stock'); ?> -->
                            <?php if ($AdvanceCostCapture == 1) { ?>
                                <th style="width: 150px;">Activity Code</th><!--UOM-->
                            <?php } ?>
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_document_qty');?> <?php required_mark(); ?></th><!--Qty-->
                            <!-- <?php if($hideWacAmount != 1){ ?>
                                <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_waited_average_cost');?> </th>
                            <?php } ?> -->
                            <th style="width: 150px;"><?php echo $this->lang->line('sales_markating_transaction_sales_price');?><!--Sales Price--> <span
                                    class="currency"> (LKR)</span><?php required_mark(); ?></th>
                            <th colspan="2" style="width: 200px;"><?php echo $this->lang->line('sales_markating_transaction_discount');?> %</th><!--Discount-->
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_net_unit_cost');?>  </th><!--Net Unit Cost-->
                            <th class="lintax" style="width: 156px;"><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                            <th class="lintax" style="width: 96px;">Tax Amount<!--Tax Amount--></th>
                            <th style="width: 100px;"><?php echo $this->lang->line('sales_markating_transaction_net_amount');?> </th><!--Net Amount-->
                            <th style="width: 200px;"><?php echo $this->lang->line('common_comment');?>  </th><!--Comment-->

                            <th style="width: 200px;">Product/Service </th>
                            <th style="width: 200px;">Main Category </th>
                            <th style="width: 200px;"> Sub Category</th>
                            <th style="width: 200px;"> Sub Sub Category</th>
                            <th style="width: 250px;"> Item</th>
                            <th style="width: 200px;"> Revenue GL Code</th>
                            <th style="width: 200px;">Cost GL Code</th>

                            <th style="display: none;"><?php echo $this->lang->line('sales_markating_transaction_remarks');?> </th><!--Remarks-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control" name="search" id="search"
                                       placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_id');?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_code');?>,<?php echo $this->lang->line('sales_markating_transaction_secondary_item_description');?>..."><!--Item ID,Secondary Item Code, Item Description-->
                                <!-- <input type="hidden" class="form-control" id="edit_itemAutoID" name="itemAutoID"> -->
                            </td>
                            <!-- <td>
                                <input type="text"  name="itemarticleNo" class="form-control " id="edit_itemarticleNo"/>
                            </td>
                            <td>
                                <input type="text"  name="itemrefeNo" class="form-control "  id="edit_itemrefeNo"/>
                            </td> -->
                            <td>
                                <input type="text" name="itemReferenceNo" id="edit_itemReferenceNo"
                                       class="form-control"/>
                            </td>
                            <td>
                                <?php echo form_dropdown('groupToCategory_edit', $category_group_to, '', 'class="form-control select2 groupToCategory_edit category_t" id="groupToCategory_edit" onchange=""'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_item" style="width: 200px;">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('transaction_goods_received_select_project'); ?> </option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                    <span id='projectShow'></span>
                                </td>
                                <td>
                                    <?php echo form_dropdown('project_categoryID',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit1" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                </td>
                                <td>
                                    <?php echo form_dropdown('project_subCategoryID',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit1"'); ?>
                                </td>

                            <?php } ?>
                            <td>
                                <input class="hidden conversionRateCNTEdit" id="conversionRateCNTEdit" name="conversionRateCNTEdit">
                                <?php echo form_dropdown('UnitOfMeasureID',  $uom, '', 'class="UnitOfMeasureID form-control select2" id="UnitOfMeasureID_edit"'); ?>
                                <!-- <?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control" id="edit_UnitOfMeasureID" onchange="convertPrice_CNT_edit(this)" required'); ?> -->
                            </td>
                            <?php if ($AdvanceCostCapture == 1) { ?>
                                <td>
                                <?php echo form_dropdown('DetailctivityCode_edit', $activity_arr, '', 'class="form-control select2 DetailctivityCode_edit" id="DetailctivityCode_edit"'); ?>
                                </td>
                            <?php } ?>
                            <!-- <td>
                                <input type="text" name="currentstock" value="0" id="currentstock" onfocus="this.select();"
                                       class="form-control currentstock number" readonly/>
                            </td> -->
                            <td>
                                <input type="text" name="quantityRequested" value="0" onkeyup="edit_change_qty()" onchange="load_line_tax_amount_edit(this)"
                                       id="edit_quantityRequested" onfocus="this.select();" class="form-control number">
                            </td>
                            <!-- <?php if($hideWacAmount != 1){ ?>
                                <td>&nbsp;<span id="edit_wac_cost" class="pull-right"
                                                style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span></td>
                            <?php } ?> -->
                            <td>
                                <input type="text" name="estimatedAmount" id="edit_estimatedAmount" placeholder="0.00" onchange="load_line_tax_amount_edit(this)"
                                       onkeyup="edit_change_amount()" onfocus="this.select();" onkeypress="return validateFloatKeyPress(this,event)" value="0" class="form-control number">
                            </td>
                            <td style="width: 100px;">
                                <div class="input-group">
                                    <input type="text" name="discount" placeholder="0.00" value="0" onchange="load_line_tax_amount_edit(this)"
                                           id="edit_discount" onfocus="this.select();" onkeyup="edit_cal_discount(this.value)"
                                           class="form-control number">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="width: 100px;">
                                <input type="text" name="discount_amount" id="edit_discount_amount" placeholder="0.00" onchange="load_line_tax_amount_edit(this)"
                                       onkeyup="edit_cal_discount_amount()" onfocus="this.select();" value="0"
                                       class="form-control number">
                            </td>
                            <td>&nbsp;<span id="edit_net_unit_cost" class="pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span></td>

                            <td class="lintax"><?php echo form_dropdown('text_type', all_tax_formula_drop_groupByTax(1), '', 'class="form-control" id="text_type_edit" style="width: 134px;" onchange="load_line_tax_amount_edit(this)" '); ?></td>
                            <td class="lintax"><span class="pull-right" id="linetaxamnt_edit" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>

                            <td>&nbsp;<span id="edit_totalAmount" class="pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span></td>
                            <td>
                                <textarea class="form-control" rows="1" name="comment" placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_comment');?>..."
                                          id="edit_comment"></textarea><!--Item Comment-->
                            </td>

                            <td>
                                <?php echo form_dropdown('pOrService',  array('' => 'Select','1'=>'Product','2'=>'service'), '', 'class="pOrService form-control select2" id="pOrService"'); ?>
                            </td>
                            <td>
                            <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control mainCategoryID" id="mainCategoryID" onchange="load_sub_cat_edit(this);"'); ?>
                            </td>
                            <td>
                                <select name="subcategoryID" id="subcategoryID" class="form-control searchbox subcategoryID"
                                    onchange="load_sub_item_edit(this);generate_sub_sub_category_drop_edit(this)">
                                <option value="">
                                    <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                                </select>
                            </td>

                            <td>
                                <select name="subsubcategoryID" id="subsubcategoryID" class="form-control searchbox subsubcategoryID"
                                    onchange="">
                                <option value="">
                                    <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                                </select>
                            </td>
                            <td>
                                <select name="itemID" id="itemID" class="form-control searchbox select2 itemID"
                                    onchange="load_item_glcode_edit(this);">
                                <option value="">
                                    <?php echo $this->lang->line('transaction_select_item'); ?><!--Select Category--></option>
                                </select>
                            </td>
      
                            <td>
                            <?php echo form_dropdown('revanueGLAutoID', $revenue_gl_arr, '', 'class="form-control select2 revanueGLAutoID" id="revanueGLAutoID" '); ?>
                            </td>

                            <td>
                            <?php echo form_dropdown('costGLAutoID', $cost_gl_arr, '', 'class="form-control select2 costGLAutoID" id="costGLAutoID" '); ?>
                            </td>


                            <td style="display: none">
                                <textarea class="form-control" rows="1" name="remarks" placeholder="<?php echo $this->lang->line('sales_markating_transaction_item_remarks');?>..."
                                          id="edit_remarks"></textarea><!--Item Remarks-->
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="updateItemOrderDetail()"><?php echo $this->lang->line('common_update_changes');?><!--Update changes-->
                    </button>
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

<div aria-hidden="true" role="dialog" id="crew_add_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="width:65%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo 'Add Employee Details';?><!--Add Item Detail--></h4>
            </div>
            
            <form role="form" id="crew_detail_form" class="form-horizontal">
                <table class="table table-bordered table-striped table-condesed" id="crew_add_table" style="table-layout: fixed">
                    <thead>
                        <tr>
                            <th style="width: 25%"><?php echo 'Employee'?> <?php required_mark(); ?></th><!--Item Code--><!--/--><!--Description-->
                            <th style="width: 23%"><?php echo 'Employee Designation' ?>  </th><!--Item ref-->
                            <th style="width: 10%"><?php echo 'Is Primary' ?>  </th><!--Item ref-->
                            <th style="width: 17%"><?php echo 'Group To' ?> <button type="button" class="btn btn-primary btn-xs" onclick="add_more_groupTo(1,1)">
                                    <i class="fa fa-plus"></i></button> </th>
                            <th style="width: 20%;"><?php echo 'Note' ?>  </th><!--Item ref-->
                            <th style="width: 5%">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_crew(1)">
                                    <i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="tr_cr">
                            <td>
                                <div>                                   
                                    <?php echo form_dropdown('employee[]', $employees_drop, '', 'class="form-control select2 crew_t" id="employee" onchange="Otable.draw();change_employee_crew($(this));"'); ?></div>
                                </div>
                            </td>
                            <td>
                                <input type="text"  name="crew_designation[]" id="crew_desigantion" class="form-control crew_desigantion"/>
                            </td>
                            <td>
                                <div class="text-center">
                                    <input type="hidden" class=".is_primary" name="is_primary[]" id="is_primary" value="0"/>
                                    <input type="checkbox" class="" value="1" onchange="checkbox_changed($(this))"/>
                                </div>
                            </td>
                            <td>
                                <?php echo form_dropdown('groupToCrew[]', $crew_group_to, '', 'class="form-control select2 groupToCrew crew_t" id="groupToCrew_1" onchange=""'); ?>
                            </td>
                            <td>
                                <input type="text"  name="crew_note[]" class="form-control"/>
                            </td>
                            <td class="remove-td"
                                style="vertical-align: middle;text-align: center;display: block;">
                            </td>
                        </tr>
                        </tr>
                    </tbody>
                </table>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default-new size-sm" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveCrewDetails()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<div aria-hidden="true" role="dialog" id="crew_add_modal_edit" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo 'Add Employee Details';?><!--Add Item Detail--></h5>
            </div>
            
            <form role="form" id="crew_detail_form_edit" class="form-horizontal">
                <input type="hidden" id="crew_id" name="crew_id" value="">
                <table class="table table-bordered table-striped table-condesed" id="crew_add_table_edit1" style="table-layout: fixed">
                    <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo 'Employee'?> <?php required_mark(); ?></th><!--Item Code--><!--/--><!--Description-->
                            <th style="width: 150px;"><?php echo 'Employee Designation' ?>  </th><!--Item ref-->
                            <th style="width: 150px;"><?php echo 'Is Primary' ?>  </th><!--Item ref-->
                            <th style="width: 150px;"><?php echo 'Group To' ?>  <button type="button" class="btn btn-primary btn-xs" onclick="add_more_groupTo(2,1)">
                                    <i class="fa fa-plus"></i></button></th>
                            <th style="width: 150px;"><?php echo 'Note' ?>  </th><!--Item ref-->
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>                
                                <?php echo form_dropdown('employee', $employees_drop, '', 'class="form-control select2 " id="employee_edit" onchange="Otable.draw();change_employee_crew_edit($(this));"'); ?>
                            </td>
                            <td>
                                <input type="text"  name="crew_designation" id="crew_designation_edit" class="form-control crew_designation_edit"/>
                            </td>
                            <td>
                                <input type="checkbox"  name="is_priamry_edit" id="is_priamry_edit" value="1" class=""/>
                            </td>
                            <td>
                                <?php echo form_dropdown('groupToCrew', $crew_group_to, '', 'class="form-control select2 groupToCrew_edit" id="groupToCrew_edit" onchange=""'); ?>
                            </td>
                            <td>
                                <input type="text"  name="crew_note" id="crew_note_edit" class="form-control"/>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="editCrewDetails()">
                        <?php echo 'Save';?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="assets_add_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="width: 65%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo 'Add Assets Details';?><!--Add Item Detail--></h4>
            </div>
            
            <form role="form" id="assets_detail_form" class="form-horizontal">
                
                <table class="table table-bordered table-striped table-condesed" id="assets_add_table" style="table-layout: fixed">
                        <thead>
                            <tr>
                                <th style="width: 200px;"><?php echo 'Code'?> <?php required_mark(); ?></th><!--Item Code--><!--/--><!--Description-->
                                <th style="width: 150px;"><?php echo 'Name' ?>  </th><!--Item name-->
                                <th style="width: 150px;"><?php echo 'Reference' ?>  </th><!--Item ref-->
                                <th style="width: 150px;"><?php echo 'Group To' ?>  <button type="button" class="btn btn-primary btn-xs" onclick="add_more_groupTo(1,2)">
                                    <i class="fa fa-plus"></i></button></th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more_assets()">
                                        <i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div>                                   
                                        <?php echo form_dropdown('assets[]', $assets_drop, '', 'class="form-control select2 s_asset" id="assets" onchange="get_selected_name($(this))"'); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <input type="text"  name="assets_name[]" class="form-control asset_name" placeholder="Name"/>
                                </td>
                                <td>
                                    <input type="text"  name="assets_reference[]" class="form-control" placeholder="Reference"/>
                                </td>
                                <td>
                                <?php echo form_dropdown('groupToAsset[]', $asset_group_to, '', 'class="form-control select2 groupToAsset s_asset" id="groupToAsset_1" onchange=""'); ?>
                                </td>
                                <td class="remove-td"
                                style="vertical-align: middle;text-align: center;display: block;">
                            </td>
                        </tr>
                            </tr>
                        </tbody>
                    </table>


                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default size-sm" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary size-sm" type="button"
                            onclick="saveAssetsDetails()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="checklist_add_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo 'Add Checklist';?><!--Add Item Detail--></h5>
            </div>
            
            <form role="form" id="assets_detail_form" class="form-horizontal">
                
                <table class="table table-bordered table-striped table-condesed" id="checklist_add_table" style="table-layout: fixed">
                    <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo 'Code'?> <?php required_mark(); ?></th><!--Item Code--><!--/--><!--Description-->
                            <th style="width: 150px;"><?php echo 'Name' ?>  </th><!--Item name-->
                            <th style="width: 40px;">
                                #
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>


                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default-new size-sm" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveAssetsDetails()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="assets_add_modal_edit" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo 'Add Assets Details';?><!--Add Item Detail--></h5>
            </div>
            
            <form role="form" id="assets_detail_form_edit" class="form-horizontal">
                <input type="hidden" id="asset_id" name="asset_id" value="">
                
                <table class="table table-bordered table-striped table-condesed" id="assets_add_table_edit" style="table-layout: fixed">
                        <thead>
                            <tr>
                                <th style="width: 200px;"><?php echo 'Code'?> <?php required_mark(); ?></th><!--Item Code--><!--/--><!--Description-->
                                <th style="width: 150px;"><?php echo 'Name' ?>  </th><!--Item name-->
                                <th style="width: 150px;"><?php echo 'Reference' ?>  </th><!--Item ref-->
                                <th style="width: 150px;"><?php echo 'Group To' ?>  <button type="button" class="btn btn-primary btn-xs" onclick="add_more_groupTo(2,2)">
                                    <i class="fa fa-plus"></i></button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div>                                   
                                        <?php echo form_dropdown('assets', $assets_drop, '', 'class="form-control select2" id="assets_edit" onchange="get_selected_name($(this))"'); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <input type="text"  name="assets_name" id="assets_name_edit"  class="form-control asset_name" placeholder="Name"/>
                                </td>
                                <td>
                                    <input type="text"  name="assets_reference" id="assets_reference_edit" class="form-control" placeholder="Reference"/>
                                </td>
                                <td>
                                <?php echo form_dropdown('groupToAsset', $asset_group_to, '', 'class="form-control select2 groupToAsset_edit" id="groupToAsset_edit" onchange=""'); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default-new size-sm" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary-new size-sm" type="button"
                            onclick="editAssetsDetails()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="groupTo_add_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo 'Add Group To';?><!--Add Item Detail--></h5>
            </div>
            
            <form role="form" id="crew_group_form" class="form-horizontal">
                <table class="table table-bordered table-striped table-condesed" id="crew_group_table" style="table-layout: fixed">
                    <thead>
                        <tr>
                            <th style="width: 200px; "><?php echo 'Group Name'?> <?php required_mark(); ?></th>
                            <!-- <th style="width: 150px;"><?php echo 'Type' ?>  </th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr >
                            <td>
                                <input type="hidden" name="type_open" id="type_open">
                                <input type="hidden" name="tb_open" id="tb_open">
                                <input type="hidden" name="groupType" id="groupType">
                                <input type="text"  name="groupName" id="groupName" class="form-control groupName"/>
                            </td>
                         
                            <!-- <td>
                                <?php echo form_dropdown('groupType', array('1'=>"Crew"), '1', 'class="form-control select2 groupType" id="groupType" onchange=""'); ?>
                            </td> -->
                        </tr>
                    </tbody>
                </table>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default-new size-sm" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveGroupToDetails()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="groupTo_add_modal_asset" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo 'Add Group To';?><!--Add Item Detail--></h5>
            </div>
            
            <form role="form" id="crew_group_form_asset" class="form-horizontal">
                <table class="table table-bordered table-striped table-condesed" id="crew_group_table" style="table-layout: fixed">
                    <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo 'Group Name'?> <?php required_mark(); ?></th>
                            <!-- <th style="width: 150px;"><?php echo 'Type' ?>  </th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr >
                            <td>
                                <input type="hidden" name="type_open_asset" id="type_open_asset">
                                <input type="hidden" name="tb_open_asset" id="tb_open_asset">
                                <input type="hidden" name="groupType" id="groupType_asset">
                                <input type="text"  name="groupName" id="groupName_asset" class="form-control groupName"/>
                            </td>
                         
                            <!-- <td>
                                <?php echo form_dropdown('groupType', array("2"=>'Assets'), '', 'class="form-control select2 groupType" id="groupType_asset" onchange=""'); ?>
                            </td> -->
                        </tr>
                    </tbody>
                </table>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default-new size-sm" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveGroupToDetailsAsset()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="groupTo_add_modal_category" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo 'Add Category Group To';?><!--Add Item Detail--></h5>
            </div>
            
            <form role="form" id="crew_group_form_category" class="form-horizontal">
                <table class="table table-bordered table-striped table-condesed" id="crew_group_table" style="table-layout: fixed">
                    <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo 'Category Group Name'?> <?php required_mark(); ?></th>
                            <!-- <th style="width: 150px;"><?php echo 'Type' ?>  </th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr >
                            <td>
                                <input type="hidden" name="type_open_category" id="type_open_category">
                                <input type="hidden" name="tb_open_category" id="tb_open_category">
                                <input type="hidden" name="groupType" id="groupType_category">
                                <input type="text"  name="groupName" id="groupName_category" class="form-control groupName"/>
                            </td>
                         
                            <!-- <td>
                                <?php echo form_dropdown('groupType', array("2"=>'Assets'), '', 'class="form-control select2 groupType" id="groupType_asset" onchange=""'); ?>
                            </td> -->
                        </tr>
                    </tbody>
                </table>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default-new size-sm" type="button"><?php echo $this->lang->line('sales_markating_transaction_close');?> </button><!--Close-->
                    <button class="btn btn-primary-new size-sm" type="button"
                            onclick="saveGroupToDetailsCategory()">
                        <?php echo $this->lang->line('common_save_change');?>  <!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="assignChecklist_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">Check List</h4>
            </div>
            <div class="modal-body">

            <div class="row" style="margin: 6px 0px;">
                    <div class="col-md-6">&nbsp;</div>
                    <div class="col-md-6">
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
                        
                        <div id="assignChecklist_item_Content"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-sm-12 pull-right">
                    <button type="button" class="btn btn-default-new size-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button class="btn btn-primary-new size-sm" onclick="assign_checklist()">Assign</button>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="visibility_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Add New Visibility</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <?php echo form_open('','role="form" id="visibility_form"'); ?>
                <input type="hidden" id="isActive1" name="isActive1" value="1">
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Section</label>
                            <?php  echo form_dropdown('section', $section, '','class="form-control select2"  id="section"'); ?>
                        </div>

                        <div class="form-group col-sm-4 btn-w-100">
                            <label for="customerCode">User </label><br>
                            <?php echo form_dropdown('customerCode[]', $user, '', 'class="form-control" id="customerCode" multiple="multiple"'); ?>
                        </div>

                        <div class="form-group col-sm-4 btn-w-100">
                            <label for="customerCode">Action </label><br>
                            <?php echo form_dropdown('actionAr[]', array('Edit'=>'Edit','Delete'=>'Delete','Add'=>'Add','Print'=>'Print'), '', 'class="form-control" id="action_ar" multiple="multiple"'); ?>
                        </div>
                    </div>
                    
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default size-sm"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary size-sm"
                        onclick="save_visibility()">Add Visibility </button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="visibility_model_edit" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel">Edit Visibility</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <?php echo form_open('','role="form" id="visibility_form_edit"'); ?>
                <input type="hidden" id="isActive1_edit" name="isActive1" value="1">
                <input type="hidden" name="visibilityAutoID" id="visibilityAutoID">
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label for="">Section</label>
                            <?php  echo form_dropdown('section_edit', $section, '','class="form-control select2"  id="section_edit"'); ?>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="customerCode">User </label><br>
                            <?php echo form_dropdown('customerCode_edit[]', $user, '', 'class="form-control select2" id="customerCode_edit1" multiple="multiple"'); ?>
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="customerCode">Action </label><br>
                            <?php echo form_dropdown('actionAr_edit[]', array('Edit'=>'Edit','Delete'=>'Delete','Add'=>'Add','Print'=>'Print'), '', 'class="form-control select2" id="action_ar_edit1" multiple="multiple"'); ?>
                        </div>
                    </div>
                    
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close </button>
                <button type="button" class="btn btn-primary"
                        onclick="save_visibility_edit()">Update </button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-----modal start----------->
<div aria-hidden="true" role="dialog"  id="checklist_view_modal_common" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>    
                            <h5 class="modal-title">&nbsp;</h5>            
            </div>
            <div class="modal-body" id="checklist_view_modal">

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default-new size-lg" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
            </div>
        </div>
    </div>
</div>
<!-----modal end----------->


<!-- Modal edit-->
<div class="modal fade invoice_setup" id="editContractCustomerDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title" id="exampleModalLabel">Edit</h4>        
      </div>
      <div class="modal-body">

      <div class="row">
        <div class="col-md-12">
            <div class="contract_customer_details_content">
                <form>
                    <div class="form-group row">
                        <label for="text1" class="col-md-4 col-form-label">Address with PO Box</label> 
                        <div class="col-md-8">
                        <input id="addressBoxEdit" name="addressBoxEdit" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="text" class="col-md-4 col-form-label">Email</label> 
                        <div class="col-md-8">
                        <input id="emailBoxEdit" name="emailBoxEdit" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="text2" class="col-md-4 col-form-label">Contact Number</label> 
                        <div class="col-md-8">
                        <input id="contactNumberBoxEdit" name="contactNumberBoxEdit" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="text3" class="col-md-4 col-form-label">Website</label> 
                        <div class="col-md-8">
                        <input id="customerUrlBoxEdit" name="customerUrlBoxEdit" type="text" class="form-control">
                        </div>
                    </div>                     
                </form>    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="update_contract_customer_details()">Update</button>
            </div>
        </div>
     </div>


        
      </div>
      
    </div>
  </div>
</div>





<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">
    
    var search_id = 1;
    var contractAutoID;
    var contractDetailsAutoID;
    var contractType;
    var customerID;
    var currencyID;
    var tax_total;
    var segment;
    var projectID;
    var projectcategory;
    var projectsubcat;
    var select_VAT_value = '';
    var isGroupBasedYN = '';
    var assignCheckListSync = [];
    var advanceCostCapture = '<?php echo getPolicyValues('ACC', 'All'); ?>'?'<?php echo getPolicyValues('ACC', 'All'); ?>':0;

    $(document).ready(function () {
        
        $('.headerclose').click(function () {
            fetchPage('system/quotation_contract/quotation_contract_job_management', contractAutoID, 'Customer Quotation_contract');
        });

        $('#customerCode').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#ammendmentType').multiselect2();
        
        $('#action_ar').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $('#customerCode_edit').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        
        $('#action_ar_edit').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        tinymce.init({
            selector: ".notes_termsandcond",
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
        contractAutoID = null;
        contractDetailsAutoID = null;
        contractType = null;
        customerID = null;
        currencyID = null;
        segment = null;
        projectID = null;
        initializeitemTypeahead();
        initializeitemTypeahead_edit();
        number_validation();

        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        Inputmask().mask(document.querySelectorAll("input"));

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#quotation_contract_form').bootstrapValidator('revalidateField', 'contractDate');
            $('#quotation_contract_form').bootstrapValidator('revalidateField', 'contractExpDate');
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            contractAutoID = p_id;
            <?php if($createmasterrecords==1){?>
                fetch_customerdrop('',contractAutoID);
            <?php }?>
            load_contract_header();

            fetch_visibility_table();
            fetch_assign_checklist_table();
            fetch_crew_table();
            fetch_assets_table();

        } else {
            $('.btn-wizard').addClass('disabled');
            load_default_note();
            <?php if($createmasterrecords==1){?>
                fetch_customerdrop();
            <?php }?>

            <?php if($group_based_tax == 1) { ?>
                $('.lintax').removeClass('hide');
            <?php } else { ?>
                $('.lintax').addClass('hide');
            <?php } ?>

        }

        $('#tax_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                tax_amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_tax_amount_required');?>.'}}},/*Tax Amount is required*/
                text_type: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_tax_type_required');?>.'}}},/*Tax Type is required*/
                percentage: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_percentage_is_required');?>.'}}}/*Percentage is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'contractAutoID', 'value': contractAutoID});
            data.push({'name': 'tax_total', 'value': tax_total});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/save_inv_tax_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data['type'], data['data'], 1000);
                    stopLoad();
                    if (data['status']) {
                        $form.bootstrapValidator('resetForm', true);
                        setTimeout(function () {
                            fetch_detail_table();
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#quotation_contract_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
               // contractType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_contract_type_is_required');?>.'}}},/*Contract Type is required*/
                contractDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_contract_date_is_required');?>.'}}},/*Contract Date is required*/
                contractExpDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_contract_exp_is_required');?>.'}}},/*Contract Exp Date is required.*/
                //customerID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_customer_is_required');?>.'}}},/*Customer is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_currency_is_required');?>.'}}},
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}}/*Segment is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            tinymce.triggerSave();
            $("#contractType").prop("disabled", false);
            $("#customerID").prop("disabled", false);
            $("#transactionCurrencyID").prop("disabled", false);
            $("#segment").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'contractAutoID', 'value': contractAutoID});
            data.push({'name': 'currency_code', 'value': $('#transactionCurrencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/save_quotation_contract_header_job'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        contractAutoID = data['last_id'];
                        contractType = $('#contractType').val();
                        customerID = $('#customerID').val();
                        currencyID = $('#transactionCurrencyID').val();
                        $("#a_link").attr("href", "<?php echo site_url('Quotation_contract/load_contract_conformation_job'); ?>/" + contractAutoID);
                        $('[href=#step2]').tab('show');
                        $('.btn-wizard').removeClass('disabled');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        fetch_detail_table();
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

    function loadSelectOptionDrop(){
       // $('#select_hod_emp').select2();
        $('.select_user').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
    }

    function currency_validation(CurrencyID) {
        if (CurrencyID) {
            documentID = $('#contractType').val();
            partyAutoID = $('#customerID').val();
            currency_validation_modal(CurrencyID, documentID, partyAutoID, 'CUS');
        }
    }

    function fetch_detail_table() {
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': contractAutoID},
                url: "<?php echo site_url('Quotation_contract/fetch_item_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    tax_total = 0;
                    currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                    $('.currency').html('(' + data['currency']['transactionCurrency'] + ')');
                    $('#table_body').empty();
                    $('#table_tfoot').empty();
                    x = 1;
                    //$("#contractType").prop("disabled", true);

                    if(data['master']['isGroupBasedTax'] == 1) {
                        $('.lineTaxHeaderAdd').removeClass('hide');
                        $('.lintax').removeClass('hide');
                        $('.lineTaxHeader').addClass('hide');
                        $('.general_tax_view').addClass('hide');
                    } else {
                        $('.lineTaxHeaderAdd').addClass('hide');
                        $('.lintax').addClass('hide');
                        $('.lineTaxHeader').removeClass('hide');
                        $('.general_tax_view').removeClass('hide');
                    }
                    if (jQuery.isEmptyObject(data['detail'])) {
                        if(data['currency']['showImageYN']==1){
                            $('.itmimage').removeClass('hidden');
                            $('.itmimagespan').addClass('hidden');
                            if(data['master']['isGroupBasedTax'] == 1) {
                                $('#table_body').append('<tr class="danger"><td colspan="13" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                            } else {
                                $('#table_body').append('<tr class="danger"><td colspan="11" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                            }
                        }else{
                            $('.itmimage').addClass('hidden');
                            $('.itmimagespan').removeClass('hidden');
                            if(data['master']['isGroupBasedTax'] == 1) {
                                $('#table_body').append('<tr class="danger"><td colspan="17" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                            } else {
                                $('#table_body').append('<tr class="danger"><td colspan="15" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                            }
                        }

                        $("#customerID").prop("disabled", false);
                        $("#transactionCurrencyID").prop("disabled", false);
                        $("#segment").prop("disabled", false);
                        $("#addcustomer").prop("disabled", false);
                        currencyID = null;
                    } else {
                        if(data['currency']['showImageYN']==1){
                            $('.itmimage').removeClass('hidden');
                            $('.itmimagespan').addClass('hidden');
                        }else{
                            $('.itmimage').addClass('hidden');
                            $('.itmimagespan').removeClass('hidden');
                        }
                        $("#customerID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#segment").prop("disabled", true);
                        $("#addcustomer").prop("disabled", true);
                        tot_amount = 0;
                        currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                        $.each(data['detail'], function (key, value) {
                            if(data['currency']['showImageYN']==1){
                                //var itemImage = get_all_item_images_aws()

                                var itmimg = '<td class="text-center"><a class="thumbnail_custom"><img style="width:100px;" src="' + value['awsImage'] + '" class="imgThumb img-rounded"/><span><img style="max-width: 250px !important;"src="' + value['awsImage'] + '"/></span></a></td>'
                            }else{
                                var itmimg = ''
                            }

                            var service_type ='';
                            var main_cat ='';
                            var sub_cat ='';
                            var sub_subcat ='';
                            var itm_cat = '';
                            var r_gl ='';
                            var c_gl ='';
                            var categoryGroupName='';
                            if(value['pOrService']==1){
                                service_type ="Product";
                            }
                            
                            if(value['pOrService']==2){
                                service_type ="Service";
                            }

                            if(value['mainCategory']){
                                main_cat =value['mainCategory'];
                            }else{
                                main_cat="";
                            }

                            if(value['subCategory']){
                                sub_cat =value['subCategory'];
                            }else{
                                sub_cat="";
                            }

                            if(value['subsubCategory']){
                                sub_subcat =value['subsubCategory'];
                            }else{
                                sub_subcat="";
                            }

                            if(value['itemname']){
                                itm_cat =value['itemname'];
                            }else{
                                itm_cat="";
                            }

                            if(value['categoryGroupName']){
                                categoryGroupName =value['categoryGroupName'];
                            }else{
                                categoryGroupName="";
                            }


                            if(value['revanuedes']){
                                r_gl =value['revanuedes'];
                            }else{
                                r_gl="";
                            }

                            if(value['costdes']){
                                c_gl =value['costdes'];
                            }else{
                                c_gl="";
                            }

                            var status_badge = '';
                            if(value['status'] == 1){
                                status_badge += '<span class="badge badge-success" style="background:green; color:white;">Active</span>';
                            }else{
                                status_badge += '<span class="badge badge-danger" style="background:red; color:white;">Inactive</span>';
                            }

                        
                            if(data['master']['isGroupBasedTax'] == 1) {
                                var t_des ='';
                                if(value['taxDescription']){
                                    t_des =value['taxDescription'];
                                }else{
                                    t_des="";
                                }

                                if(advanceCostCapture==1){
                                    var activity_code ='';
                                    if(value['activity_code']){
                                        activity_code =value['activity_code'];
                                    }
                                    $('#table_body').append('<tr><td>' + x + '</td>' + itmimg + '<td>' + value['typeItemName'] + '</td><td>' + value['itemReferenceNo'] + '</td><td>' + categoryGroupName + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + activity_code + '</td><td>' + service_type + '</td><td>' + main_cat + '</td><td>' + sub_cat + '</td><td>' + sub_subcat + '</td><td>' + itm_cat + '</td><td>' + r_gl + '</td><td>' + c_gl + '</td><td class="text-center">' + commaSeparateNumber(value['requestedQty'], 2) + '</td><td class="text-right">' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + ' (' + parseFloat(value['discountPercentage']).toFixed(2) + '%) </td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + t_des + '</td><td class="text-right"><a onclick="open_tax_dd('+value['taxDetailAutoID']+','+contractAutoID+',\'CNT\','+currency_decimal+', ' + value['contractDetailsAutoID'] +', \'srp_erp_contractdetails\', \'contractDetailsAutoID\')">' + parseFloat(value['taxAmount']).formatMoney(currency_decimal, '.', ',') + '</a></td><td class="text-right">' + parseFloat(parseFloat(value['transactionAmount']) + parseFloat(value['taxAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td>'+status_badge+'</td><td class="text-right"><a onclick="edit_item(' + value['contractDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; <a onclick="delete_item(' + value['contractDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                }else{
                                    $('#table_body').append('<tr><td>' + x + '</td>' + itmimg + '<td>' + value['typeItemName'] + '</td><td>' + value['itemReferenceNo'] + '</td><td>' + categoryGroupName + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td>' + service_type + '</td><td>' + main_cat + '</td><td>' + sub_cat + '</td><td>' + sub_subcat + '</td><td>' + itm_cat + '</td><td>' + r_gl + '</td><td>' + c_gl + '</td><td class="text-center">' + commaSeparateNumber(value['requestedQty'], 2) + '</td><td class="text-right">' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + ' (' + parseFloat(value['discountPercentage']).toFixed(2) + '%) </td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + t_des + '</td><td class="text-right"><a onclick="open_tax_dd('+value['taxDetailAutoID']+','+contractAutoID+',\'CNT\','+currency_decimal+', ' + value['contractDetailsAutoID'] +', \'srp_erp_contractdetails\', \'contractDetailsAutoID\')">' + parseFloat(value['taxAmount']).formatMoney(currency_decimal, '.', ',') + '</a></td><td class="text-right">' + parseFloat(parseFloat(value['transactionAmount']) + parseFloat(value['taxAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td>'+status_badge+'</td><td class="text-right"><a onclick="edit_item(' + value['contractDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; <a onclick="delete_item(' + value['contractDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                }
                                
                                tot_amount += parseFloat(parseFloat(value['transactionAmount']) + parseFloat(value['taxAmount']));
                                tax_total += parseFloat(value['transactionAmount']);
                            } else {

                                if(advanceCostCapture==1){
                                    var activity_code ='';
                                    if(value['activity_code']){
                                        activity_code =value['activity_code'];
                                    }
                                    $('#table_body').append('<tr><td>' + x + '</td>' + itmimg + '<td>' + value['typeItemName'] + '</td><td>' + value['itemarticleNo'] + '</td><td>' + value['itemReferNo'] + '</td><td>' + value['itemReferenceNo'] + '</td><td>' + categoryGroupName + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + activity_code + '</td><td>' + service_type + '</td><td>' + main_cat + '</td><td>' + sub_cat + '</td><td>' + sub_subcat + '</td><td>' + itm_cat + '</td><td>' + r_gl + '</td><td>' + c_gl + '</td><td class="text-center">' + commaSeparateNumber(value['requestedQty'], 2) + '</td><td class="text-right">' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + ' (' + parseFloat(value['discountPercentage']).toFixed(2) + '%) </td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td>'+status_badge+'</td><td class="text-right"><a onclick="edit_item(' + value['contractDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; <a onclick="delete_item(' + value['contractDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                }else{
                                    $('#table_body').append('<tr><td>' + x + '</td>' + itmimg + '<td>' + value['typeItemName'] + '</td><td>' + value['itemarticleNo'] + '</td><td>' + value['itemReferNo'] + '</td><td>' + value['itemReferenceNo'] + '</td><td>' + categoryGroupName + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td>' + service_type + '</td><td>' + main_cat + '</td><td>' + sub_cat + '</td><td>' + sub_subcat + '</td><td>' + itm_cat + '</td><td>' + r_gl + '</td><td>' + c_gl + '</td><td class="text-center">' + commaSeparateNumber(value['requestedQty'], 2) + '</td><td class="text-right">' + (parseFloat(value['unittransactionAmount']) + parseFloat(value['discountAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + ' (' + parseFloat(value['discountPercentage']).toFixed(2) + '%) </td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td>'+status_badge+'</td><td class="text-right"><a onclick="edit_item(' + value['contractDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; <a onclick="delete_item(' + value['contractDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                }
                                tot_amount += (parseFloat(value['transactionAmount']));
                                tax_total += parseFloat(value['transactionAmount']);
                            }
                            x++;
                        });
                        if(data['currency']['showImageYN']==1){
                            if(data['master']['isGroupBasedTax'] == 1) {
                                $('#table_tfoot1').append('<tr><td colspan="16" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                            } else {
                                $('#table_tfoot1').append('<tr><td colspan="14" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                            }
                        }else{
                            if(data['master']['isGroupBasedTax'] == 1) {
                                $('#table_tfoot1').append('<tr><td colspan="15" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                            } else {
                                $('#table_tfoot1').append('<tr><td colspan="13" class="text-right">Total ( ' + data['currency']['transactionCurrency'] + ' )</td><td class="text-right total">' + tot_amount.formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                            }
                        }

                    }
                    /*Tax Applicable Amount*/

                    $('#tax_tot').text('<?php echo $this->lang->line('sales_markating_transaction_tax_applicable_amount');?> ( ' + parseFloat(tax_total).formatMoney(currency_decimal, '.', ',') + ' )');
                    $('#tax_table_body_recode,#tax_table_footer').empty();
                    if (jQuery.isEmptyObject(data['tax_detail'])) {
                        $('#tax_table_body_recode').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');<!--No Records Found-->
                    } else {
                        x = 1;
                        t_total = 0;
                        $.each(data['tax_detail'], function (key, value) {
                            if(data['master']['isGroupBasedTax'] == 1) {
                                $('.groupTax_hide').addClass('hide');

                                var tax_DD = '<a onclick="open_tax_dd('+value['taxDetailAutoID']+','+contractAutoID+',\'CNT\','+currency_decimal+', ' + value['contractDetailsAutoID'] +', \'srp_erp_contractdetails\', \'contractDetailsAutoID\')">'+parseFloat(value['amount']).formatMoney(currency_decimal, '.', ',')+' </a>';

                                $('#tax_table_body_recode').append('<tr><td>' + x + '</td><td>' + value['taxDescription'] + '</td><td class="text-right">' + tax_DD + '</td><td class="text-right"><a onclick="delete_tax(' + value['taxDetailAutoID'] + ',\'' + value['taxShortCode'] + '\',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                x++;
                                t_total += parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total).toFixed(currency_decimal);
                            } else {
                                $('.groupTax_hide').removeClass('hide');
                                $('#tax_table_body_recode').append('<tr><td>' + x + '</td><td>' + value['taxShortCode'] + '</td><td>' + value['taxDescription'] + '</td><td class="text-right">' + value['taxPercentage'] + '% </td><td class="text-right">' + parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_tax(' + value['taxDetailAutoID'] + ',\'' + value['taxShortCode'] + '\',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                x++;
                                t_total += parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total).toFixed(currency_decimal);
                            }
                        });
                        if (t_total > 0) {
                            $('#tax_table_footer').append('<tr><td colspan="4" class="text-right">Tax Total </td><td class="text-right total">' + parseFloat(t_total).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                        }
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
        ;
    }

    function load_item_glcode(select_val){
        var id = $(select_val).closest('tr').find('.f_search').attr('id');
        var myArray = id.split("_");

        var num =myArray[2];
        
        $('#revanueGLAutoID_'+num).val("");
        $('#costGLAutoID_'+num).val("");
        itemAutoID = $('#itemID_'+num).val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_item_gl_code"); ?>',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#revanueGLAutoID_"+num).val(data['revanueGLAutoID']).change();
                    $("#costGLAutoID_"+num).val(data['costGLAutoID']).change();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }  

    
    function load_item_glcode_edit() {

        

        $('#revanueGLAutoID').val("");
        $('#costGLAutoID').val("");
        itemAutoID = $('#itemID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_item_gl_code"); ?>',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#revanueGLAutoID").val(data['revanueGLAutoID']).change();
                    $("#costGLAutoID").val(data['costGLAutoID']).change();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }


    function load_sub_cat(select_val) {

            //$(this).closest('tr').find('.div_projectID_income').html(data);
        var id = $(select_val).closest('tr').find('.f_search').attr('id');
        var myArray = id.split("_");

        var num =myArray[2];

       // changeFormCode();
        $('#subcategoryID_'+num).val("");
        $('#subcategoryID_'+num +' option').remove();
       // $('#subSubCategoryID').val("");
       // $('#subSubCategoryID option').remove();
        var subid = $('#mainCategoryID_'+num).val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID_'+num).empty();
                    var mySelect = $('#subcategoryID_'+num);
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

    function generate_sub_sub_category_drop(select_val){
            //$(this).closest('tr').find('.div_projectID_income').html(data);
        var id = $(select_val).closest('tr').find('.f_search').attr('id');
        var myArray = id.split("_");

        var num =myArray[2];

       // changeFormCode();
        $('#subsubcategoryID_'+num).val("");
        $('#subsubcategoryID_'+num +' option').remove();
       // $('#subSubCategoryID').val("");
       // $('#subSubCategoryID option').remove();
        var subid = $('#subcategoryID_'+num).val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subsubcategoryID_'+num).empty();
                    var mySelect = $('#subsubcategoryID_'+num);
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

    function generate_sub_sub_category_drop_edit() {
        
        $('#subsubcategoryID').val("");
        $('#subsubcategoryID option').remove();
        
        var subid = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subsubcategoryID').empty();
                    var mySelect = $('#subsubcategoryID');
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


function load_sub_item(select_val) {

            //$(this).closest('tr').find('.div_projectID_income').html(data);
        var id = $(select_val).closest('tr').find('.f_search').attr('id');
        var myArray = id.split("_");

        var num =myArray[2];

       // changeFormCode();
        $('#itemID_'+num).val("");
        $('#itemID_'+num +' option').remove();
       // $('#subSubCategoryID').val("");
       // $('#subSubCategoryID option').remove();
        var subitemid = $('#subcategoryID_'+num).val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_sub_item"); ?>',
            dataType: 'json',
            data: {'subitemid': subitemid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#itemID_'+num).empty();
                    var mySelect = $('#itemID_'+num);
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemAutoID']).html(text['itemSystemCode'] + ' - ' + text['itemName']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
}

function load_sub_item_edit() {

        //$(this).closest('tr').find('.div_projectID_income').html(data);
    // var id = $(select_val).closest('tr').find('.f_search').attr('id');
    // var myArray = id.split("_");

    // var num =myArray[2];

   // changeFormCode();
    $('#itemID').val("");
    $('#itemID option').remove();
   // $('#subSubCategoryID').val("");
   // $('#subSubCategoryID option').remove();
    var subitemid = $('#subcategoryID').val();
    $.ajax({
        type: 'POST',
        url: '<?php echo site_url("ItemMaster/load_sub_item"); ?>',
        dataType: 'json',
        data: {'subitemid': subitemid},
        async: false,
        success: function (data) {
            if (!jQuery.isEmptyObject(data)) {
                $('#itemID').empty();
                var mySelect = $('#itemID');
                mySelect.append($('<option></option>').val('').html('Select Option'));
                $.each(data, function (val, text) {
                    mySelect.append($('<option></option>').val(text['itemAutoID']).html(text['itemSystemCode'] + ' - ' + text['itemName']));
                });
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {

        }
    });
}

    function load_sub_cat_edit() {

        //     //$(this).closest('tr').find('.div_projectID_income').html(data);
        // var id = $(select_val).closest('tr').find('.f_search').attr('id');
        // var myArray = id.split("_");

        // var num =myArray[2];

        // changeFormCode();
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        // $('#subSubCategoryID').val("");
        // $('#subSubCategoryID option').remove();
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

    

    function fetch_crew_table(){
        
        Otable = $('#contract_crew_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Quotation_contract/fetch_crew_list_contract'); ?>",
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
                {"mData": "id"},
                {"mData": "id"},
                {"mData": "empID"},
                {"mData": "empCode"},
                {"mData": "empName"},
                {"mData": "empDesignation"},
                {"mData": "isPrimary"},
                {"mData": "groupToName"},
                {"mData": "groupToID"},
                {"mData": "comment"},
                {"mData": "action"}
              
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "contractAutoID", "value": contractAutoID});
              
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

    function fetch_assets_table(){
        Otable = $('#contract_assets_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('Quotation_contract/fetch_assets_list_contract'); ?>",
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
                {"mData": "contractAutoID"},
                {"mData": "id"},
                {"mData": "faID"},
                {"mData": "faCode"},
                {"mData": "assetName"},
                {"mData": "assetRef"},
                {"mData": "groupToName"},
                {"mData": "groupToID"},
                {"mData": "action"},
            ],
            "columnDefs": [
              
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
            aoData.push({"name": "contractAutoID", "value": contractAutoID});
              
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

    function checkbox_changed(ev){

        var value = ev.is(":checked") ? 1 : 0;
        ev.siblings("input[name='is_primary[]']").val(value);   
       
    }

    function change_employee_crew(ev){

        var empID = ev.val();

        if(empID){

            $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID': empID},
            url: "<?php echo site_url('Quotation_contract/get_emp_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
    
                    if(data.DesDescription){
                        $(ev).closest('tr').find('.crew_desigantion').val(data.DesDescription);
                        $(ev).closest('tr').find('.crew_desigantion').attr('readonly',true);
                    }
     
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
            });

        }
     
       

        //crew_desigantion

    }

    function change_employee_crew_edit(ev){

        var empID = ev.val();

        if(empID){

            $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID': empID},
            url: "<?php echo site_url('Quotation_contract/get_emp_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {

                    if(data.DesDescription){
                        $(ev).closest('tr').find('.crew_designation_edit').val(data.DesDescription);
                        $(ev).closest('tr').find('.crew_designation_edit').attr('readonly',true);
                    }

                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
            });

        }

}

    function initializeitemTypeahead(id) {
        /*var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Procurement/fetch_itemrecode/?q=%QUERY"
        });

        item.initialize();
        $('.search').typeahead(null, {
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            /!*$('#itemAutoID').val(datum.itemAutoID);*!/
            $(this).closest('tr').find('.itemAutoID').val(datum.itemAutoID)
            LoaditemUnitPrice_againtsExchangerate(datum.companyLocalWacAmount, this);
            fetch_sales_price(datum.companyLocalSellingPrice, this, datum.defaultUnitOfMeasureID, datum.itemAutoID);
            //alert(datum.defaultUnitOfMeasureID);
            fetch_related_uom_id(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID, this);
        });*/

        // $('#f_search_' + id).autocomplete({
        //     serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
        //     onSelect: function (suggestion) {
        //         //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
        //         setTimeout(function(){
        //             $('#f_search_'+id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
        //         }, 200);

        //         fetch_sales_price(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
        //         fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
        //         var conversionRate = $(this).closest('tr').find('.conversionRate_CNT').val();
        //         LoaditemUnitPrice_againtsExchangerate(suggestion.companyLocalWacAmount,this,conversionRate);
        //         if(conversionRate !== '' && parseFloat(conversionRate) > 0) {
        //             $(this).closest('tr').find('.currentstock').val(suggestion.currentstockitemled / conversionRate);
        //         } else {
        //             $(this).closest('tr').find('.currentstock').val(suggestion.currentstockitemled);
        //         }
        //         fetch_line_tax_and_vat(suggestion.itemAutoID, this);

        //         $(this).closest('tr').find('.itemReferenceNo').focus();
        //         $(this).closest('tr').css("background-color", 'white');
        //         if(suggestion.revanueGLCode==null || suggestion.revanueGLCode=='' || suggestion.revanueGLCode==0){
        //             setTimeout(function () {
        //                 $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
        //             }, 200);
        //             $('#f_search_' + id).val('');
        //             $(this).closest('tr').css("background-color", '#ffb2b2 ');
        //             myAlert('w','Revenue GL code not assigned for selected item')
        //         }
        //     }
        // });
    }

   /* function clearitemAutoID(element){
        $(element).closest('tr').find('.itemAutoID').val('');
    }*/

    function clearitemAutoIDEdit(e,ths){

        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode==13) {
            //e.preventDefault();
        }else{
            $(ths).closest('tr').find('#edit_itemAutoID').val('');

            $(ths).closest('tr').find('#edit_quantityRequested ').val(0);
            $(ths).closest('tr').find('#edit_estimatedAmount').val('');
            $(ths).closest('tr').find('#edit_discount').val(0);
            $(ths).closest('tr').find('#edit_discount_amount').val(0);
            $(ths).closest('tr').find('#edit_net_unit_cost').html('0');
            $(ths).closest('tr').find('#edit_totalAmount').html('0');
        }

    }

    function clearitemAutoID(e,ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode==13) {
            //e.preventDefault();
        }else{
            $(ths).closest('tr').find('.itemAutoID').val('');
            $(ths).closest('tr').find('.quantityRequested ').val(0);
            $(ths).closest('tr').find('.estimatedAmount').val('');
            $(ths).closest('tr').find('.discount').val(0);
            $(ths).closest('tr').find('.discount_amount').val(0);
            $(ths).closest('tr').find('.net_unit_cost').html('0');
            $(ths).closest('tr').find('.net_amount').html('0');
        }
    }

    function initializeitemTypeahead_edit() {
        /*var item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Procurement/fetch_itemrecode/?q=%QUERY"
        });

        item.initialize();
        $('#search').typeahead(null, {
            displayKey: 'Match',
            source: item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $('#edit_net_unit_cost').text('0.00');
            $('#edit_totalAmount').text('0.00');
            $('#edit_itemAutoID').val(datum.itemAutoID);
            LoaditemUnitPrice_againtsExchangerate_edit(datum.companyLocalWacAmount);
            fetch_sales_price_edit(datum.companyLocalSellingPrice, datum.defaultUnitOfMeasureID, datum.itemAutoID);
            //alert(datum.defaultUnitOfMeasureID);
            fetch_related_uom_id_edit(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID);
        });*/


        // $('#search').autocomplete({
        //     serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
        //     onSelect: function (suggestion) {
        //         setTimeout(function(){
        //             $('#edit_net_unit_cost').text('0');
        //             $('#edit_totalAmount').text('0');
        //             $('#edit_itemAutoID').val(suggestion.itemAutoID);
        //         }, 200);

        //         LoaditemUnitPrice_againtsExchangerate_edit_edit(suggestion.companyLocalWacAmount);
        //         fetch_sales_price_edit(suggestion.companyLocalSellingPrice, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
        //         fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
        //         $(this).closest('tr').find('#edit_itemReferenceNo').focus();
        //         edit_fetch_line_tax_and_vat(suggestion.itemAutoID);
        //         $(this).closest('tr').find('#currentstock').val(suggestion.currentstockitemled);
        //         if(suggestion.revanueGLCode==null || suggestion.revanueGLCode=='' || suggestion.revanueGLCode==0){
        //             $('#edit_itemAutoID').closest('tr').find('.itemAutoID').val('');
        //             $('#edit_itemAutoID').val('');
        //             $('#edit_itemAutoID').closest('tr').css("background-color", '#ffb2b2 ');
        //             myAlert('w','Revenue GL code not assigned for selected item')
        //         }


        //     }
        // });
    }

    function LoaditemUnitPrice_againtsExchangerate(LocalWacAmount,element,uomconvertionrate) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID, 'LocalWacAmount': LocalWacAmount,'uomexrate':uomconvertionrate},
            url: "<?php echo site_url('Quotation_contract/load_unitprice_exchangerate_convertion'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    /*$('#estimatedAmount').val(data['amount']);*/
                    $(element).closest('tr').find('.wac_cost').text(parseFloat(data).formatMoney(currency_decimal, '.', ','));
        //                    $('#item_detail_form').bootstrapValidator('revalidateField', 'estimatedAmount');
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate_edit(LocalWacAmount) {
        poID = contractAutoID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('Quotation_contract/load_unitprice_exchangerate_convertion'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $('#edit_wac_cost').text(parseFloat(data['amount']).formatMoney(currency_decimal, '.', ','));
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_item_wacAmount(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID},
            url: "<?php echo site_url('ItemMaster/load_item_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    LoaditemUnitPrice_againtsExchangerate_edit(data['companyLocalWacAmount'])
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function confirmation() {
        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document*/
                    type: "warning",/*warning*/
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
                        data: {'contractAutoID': contractAutoID},
                        url: "<?php echo site_url('Quotation_contract/contract_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if(data[0]=='s'){
                                fetchPage('system/quotation_contract/quotation_contract_management', contractAutoID, 'Quotation_contract');
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function item_detail_modal() {
        if (contractAutoID) {
       
            contractDetailsAutoID = null;
            $('.search').typeahead('destroy');
            $('#item_detail_form')[0].reset();
            $('#discount').val(0);
            $('#discount_amount').val(0);
            $('.net_amount,.net_unit_cost,.wac_cost').text('0');
            $('.search').typeahead('val', '');
            $('.itemAutoID').val('');
            initializeitemTypeahead(1);
            $('#item_add_table tbody tr').not(':first').remove();
            $('.f_search').closest('tr').css("background-color",'white');
            $('.quantityRequested').closest('tr').css("background-color",'white');
            $('.estimatedAmount').closest('tr').css("background-color",'white');
            fetch_segment_master();
            $("#item_detail_modal").modal({backdrop: "static"});

        }
    }

    function add_crew_details_modal(){
      
        $('select.crew_t').select2('destroy');
        var appendData = $('#crew_add_table tbody tr:first').clone();

        appendData.find('input').val('');
        appendData.find("input[type='checkbox']").attr('checked',false);
        
        $('#crew_add_table > tbody').html("");
        $('#crew_add_table').append(appendData);

        $(".select2").select2();

        $("#crew_add_modal").modal({backdrop: "static"});

    }

    function add_more_groupTo(type ,tab){
        
       

        if(tab==1){
            $('#groupName').val('');
            $('#groupType').val(1);
          //  $('#groupType').setAttribute("disabled", "disabled");
            $('#type_open').val(type);
            $('#tb_open').val(tab);
            $("#groupTo_add_modal").modal('show');
        }else{
            $('#groupName_asset').val('');
            $('#groupType_asset').val(2);
            $('#type_open_asset').val(type);
            $('#tb_open_asset').val(tab);
            $("#groupTo_add_modal_asset").modal('show');
        }
        
    }

    function add_more_groupTo_category(type ,tab){

        $('#groupName_category').val('');
        $('#groupType_category').val(2);
        $('#type_open_category').val(type);
        $('#tb_open_acategory').val(tab);
        $("#groupTo_add_modal_category").modal('show');
    }

    function add_assets_details_modal(){

        $('select.s_asset').select2('destroy');
        var appendData = $('#assets_add_table tbody tr:first').clone();

        appendData.find('input').val('');
        
        $('#assets_add_table > tbody').html("");
        $('#assets_add_table').append(appendData);

        $(".select2").select2();

        $("#assets_add_modal").modal({backdrop: "static"});
    }

    function add_checklist_details_modal(){

        $('select.select2').select2('destroy');
        var appendData = $('#checklist_add_table tbody tr:first').clone();

        appendData.find('input').val('');

        $('#checklist_add_table > tbody').html("");
        $('#checklist_add_table').append(appendData);

        $(".select2").select2();

        $("#checklist_add_modal").modal({backdrop: "static"});
    }

    function edit_crew_line(ev){

        var crew_id = ev.closest('tr').find('td:eq(1)').text();
        var empID = ev.closest('tr').find('td:eq(2)').text();
        var empDesignation = ev.closest('tr').find('td:eq(5)').text();
        var isPrimary = ev.closest('tr').find('td:eq(6)').text();
        var empNote = ev.closest('tr').find('td:eq(9)').text();

        var groupToCrew_edit = ev.closest('tr').find('td:eq(8)').text();
        
        $('#employee_edit').val(empID).change();
        $('#crew_designation_edit').val(empDesignation);
        $('#crew_note_edit').val(empNote);
        $('#crew_id').val(crew_id);
        $('#groupToCrew_edit').val(groupToCrew_edit).change();
        if(isPrimary == 'Primary Contract'){
            $('#is_priamry_edit').attr('checked',true);
        }else{
            $('#is_priamry_edit').attr('checked',false);
        }

        $("#crew_add_modal_edit").modal({backdrop: "static"});
     
    }

    function edit_asset_line(ev){

        var asset_id = ev.closest('tr').find('td:eq(1)').text();
        var asset_faID = ev.closest('tr').find('td:eq(2)').text();
        var asset_name = ev.closest('tr').find('td:eq(4)').text();
        var asset_reference = ev.closest('tr').find('td:eq(5)').text();

        var groupToAsset_edit = ev.closest('tr').find('td:eq(7)').text();

        $('#assets_edit').val(asset_faID).change();
        $('#assets_name_edit').val(asset_name);
        $('#assets_reference_edit').val(asset_reference);
        $('#asset_id').val(asset_id);
        $('#groupToAsset_edit').val(groupToAsset_edit).change();
        $("#assets_add_modal_edit").modal({backdrop: "static"});

    }

    function get_selected_name(ev){

        var selected = ev.find('option:selected').text();
        var index = selected.indexOf('|');
        var arr = [selected.slice(0, index), selected.slice(index + 1)];
        
        ev.closest('tr').find('.asset_name').val(arr[1]);

    }

    function delete_crew_line(ev){

        var crew_id = ev.closest('tr').find('td:eq(1)').text();

        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
                    type: "warning",/*warning*/
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
                        data: {'crew_id': crew_id, 'contractAutoID': contractAutoID},
                        url: "<?php echo site_url('Quotation_contract/delete_crew_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_crew_table();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function delete_item(id) {
        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
                    type: "warning",/*warning*/
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
                        data: {'contractDetailsAutoID': id, 'contractAutoID': contractAutoID},
                        url: "<?php echo site_url('Quotation_contract/delete_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_detail_table();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function select_text(data) {
        if (data.value != 0) {
            var result = $('#text_type option:selected').text().split('|');
            $('#percentage').val(parseFloat(result[2]));
            cal_tax(parseFloat(result[2]), tax_total);
            $('#tax_form').bootstrapValidator('revalidateField', 'percentage');
        }
    }

    function cal_tax_amount(discount_amount) {
        if (tax_total && discount_amount) {
            $('#percentage').val(((parseFloat(discount_amount) / tax_total) * 100).toFixed(currency_decimal));
        } else {
            $('#percentage').val(0);
        }
    }

    function cal_tax(discount) {
        if (tax_total && discount) {
            $('#tax_amount').val(((tax_total / 100) * parseFloat(discount)).toFixed(currency_decimal));
        } else {
            $('#tax_amount').val(0);
        }
    }

    function delete_tax(id, value) {
        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record!*/
                    type: "warning",/*warning*/
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
                        data: {'taxDetailAutoID': id, 'contractAutoID': contractAutoID},
                        url: "<?php echo site_url('Quotation_contract/delete_tax_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            setTimeout(function () {
                                fetch_detail_table();
                            }, 300);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_item(id) {
        /*$('#item_add_table tbody tr').not(':first').remove();
         $('#item_add_table tbody tr').find('td:last-child').hide();
         $('#item_add_table thead tr').find('th:last-child').hide();*/

        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_document');?>",/*You want to edit this record!*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit');?>",/*Edit*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'contractDetailsAutoID': id},
                        url: "<?php echo site_url('Quotation_contract/fetch_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            contractDetailsAutoID = data['contractDetailsAutoID'];
                            projectID = data['projectID'];
                            projectcategory = data['project_categoryID'];
                            projectsubcat = data['project_subCategoryID'];
                            var totAmount = parseFloat(data['transactionAmount']);
                            var unitAmount = parseFloat(data['unittransactionAmount']);
                            $('#search').val(data['typeItemName']);
                            $('#conversionRateCNTEdit').val(data['conversionRateUOM']);
                            //$('#search').typeahead('val', data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID'], $('#UnitOfMeasureID'));
                            //LoaditemUnitPrice_againtsExchangerate_edit(data['companyLocalWacAmount']);
                            //load_item_wacAmount(data['itemAutoID']);
                           

                            var uomexrate = (data['conversionRateUOM']>0?data['conversionRateUOM']:1);
                            

                            //load_item_wacAmount_UOMexchangerate(data['itemAutoID'],data['contractAutoID'],uomexrate);
                            $('#edit_quantityRequested').val(data['requestedQty']);
                            $('#mainCategoryID').val(data['mainCategoryID']);
                            $('#groupToCategory_edit').val(data['categoryGroupID']).change();
                            
                            load_sub_cat_edit();
                           
                            $('#subcategoryID').val(data['subcategoryID']);
                            generate_sub_sub_category_drop_edit();
                            $('#subsubcategoryID').val(data['subsubcategoryID']).change();
                            
                            $('#pOrService').val(data['pOrService']);
                            $('#UnitOfMeasureID_edit').val(data['unitOfMeasureID']).change();
                            //$('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount']) + parseFloat(data['discountAmount'])));
                            $('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount']) + parseFloat(data['discountAmount'])).formatMoney(currency_decimal, '.',''));
                            $('#edit_discount_amount').val(data['discountAmount']);
                            $('#search_id').val(data['itemSystemCode']);
                          //  $('#edit_itemSystemCode').val(data['itemSystemCode']);
                          //  $('#edit_itemAutoID').val(data['itemAutoID']);
                            $('#edit_itemReferenceNo').val(data['itemReferenceNo']);

                            if(advanceCostCapture==1){
                                $('#DetailctivityCode_edit').val(data['activityCodeID']).change();
                            }
                            
                            $('#edit_itemDescription').val(data['itemDescription']);
                            $('#edit_comment').val(data['comment']);
                            $('#edit_remarks').val(data['remarks']);
                            $('#edit_discount').val(data['discountPercentage']);
                            $('#edit_itemarticleNo').val(data['itemarticleNo']);
                            $('#edit_itemrefeNo').val(data['itemReferNo']);


                            load_sub_item_edit();
                            $('#itemID').val(data['itemAutoID'])
                            $('#revanueGLAutoID').val(data['revanueGLAutoID']);
                            $('#costGLAutoID').val(data['costGLAutoID']);
                            

                            var stock = data['itemledstock'];
                            if(parseFloat(data['conversionRateUOM']) > 0 ) {
                                stock = parseFloat(stock) * parseFloat(data['conversionRateUOM']);
                            }
                            $('#currentstock').val(stock);
                            // $('#currentstock').val(data['itemledstock']);

                            $('#edit_net_unit_cost').text((unitAmount).formatMoney(currency_decimal, '.', ','));
                            $('#edit_totalAmount').text((totAmount).formatMoney(currency_decimal, '.', ','));
                            // $('#edit_UnitOfMeasureID').prop("disabled", true);
                            select_VAT_value = data['taxCalculationformulaID'];
                            edit_fetch_line_tax_and_vat(data['itemAutoID']);
                            load_segmentBase_projectID_itemEdit();
                            $("#edit_item_detail_modal").modal({backdrop: "static"});
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
        ;
    }

    function fetch_related_uom_id(masterUnitID, select_value, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterUnitID': masterUnitID},
            url: "<?php echo site_url('dashboard/fetch_related_uom_id'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.umoDropdown').empty()
                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');
                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
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
                $('#edit_UnitOfMeasureID').empty();
                var mySelect = $('#edit_UnitOfMeasureID');
                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $('#edit_UnitOfMeasureID').val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function cal_discount_amount(element) {
        var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
        if(element.value>estimatedAmount){
            myAlert('w','Discount amount should be less than or equal to sales price');
            $(element).closest('tr').find('.discount').val(0);
            $(element).val(0)
        }else{
            if (estimatedAmount) {
                $(element).closest('tr').find('.discount').val(((parseFloat(element.value) / estimatedAmount) * 100).toFixed(currency_decimal))
            }
            net_amount(element);
        }
    }

    function change_qty(element) {
        if(element.value>0)
        {
            $(element).closest('tr').css("background-color",'white');
        }
        net_amount(element);
    }

    function cal_discount(element) {
        if (element.value < 0 || element.value > 100 || element.value =='') {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $(element).closest('tr').find('.discount').val(parseFloat(0));
            $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
        } else {
            var estimatedAmount = parseFloat($(element).closest('tr').find('.estimatedAmount').val());
            if (estimatedAmount) {
                $(element).closest('tr').find('.discount_amount').val((estimatedAmount / 100) * parseFloat(element.value).formatMoney(currency_decimal, '.', ','))
            }
            net_amount(element);
        }
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

    function change_amount(element) {
        if(element.value>0)
        {
            $(element).closest('tr').css("background-color",'white');
        }
        $(element).closest('tr').find('.discount').val(parseFloat(0));
        $(element).closest('tr').find('.discount_amount').val(parseFloat(0));
        net_amount(element);
    }

    // edit functions for Item Detail

    function edit_cal_discount_amount() {
        var estimatedAmount = parseFloat($('#edit_estimatedAmount').val());
        var discountAmount = parseFloat($('#edit_discount_amount').val());
        if (discountAmount > estimatedAmount) {
            swal("Cancelled", "Discount Amount should be less than the Sales Price", "error");
            $('#edit_discount').val(0);
            $('#edit_discount_amount').val(0);
            edit_net_amount(estimatedAmount);
        } else {
            if (estimatedAmount) {
                $('#edit_discount').val(((parseFloat(discountAmount) / estimatedAmount) * 100).toFixed(currency_decimal))
            }
            edit_net_amount(discountAmount);
        }
    }

    function edit_change_qty(element) {
        edit_net_amount(element);
    }

    function edit_cal_discount(discount) {
        var estimatedAmount = parseFloat($('#edit_estimatedAmount').val());
        if (discount < 0 || discount > 100) {
            swal("Cancelled", "Discount % should be between 0 - 100", "error");
            $('#edit_discount').val(0);
            $('#edit_discount_amount').val(0);
            edit_net_amount(estimatedAmount);
        } else {
            if (estimatedAmount) {
                $('#edit_discount_amount').val((estimatedAmount / 100) * parseFloat($('#edit_discount').val()))
            }
            edit_net_amount();
        }
    }

    function edit_net_amount() {
        var qut = $('#edit_quantityRequested').val();
        var amount = $('#edit_estimatedAmount').val();
        var discoun = $('#edit_discount_amount').val();
        if (qut == null || qut == 0) {
            $('#edit_totalAmount').text('0');
            $('#edit_net_unit_cost').text('0');
        } else {
            $('#edit_totalAmount').text((((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)) * parseFloat(qut)).formatMoney(currency_decimal, '.', ','));
            $('#edit_net_unit_cost').text(((parseFloat(1) * parseFloat(amount)) - parseFloat(discoun)).formatMoney(currency_decimal, '.', ','));
        }
    }

    function edit_change_amount(element) {
        $('#edit_discount').val(parseFloat(0));
        $('#edit_discount_amount').val(parseFloat(0));
        edit_net_amount(element);
    }

    function load_crew(){
        $('[href=#step3]').tab('show');
    }

    function load_assets(){
        $('[href=#step4]').tab('show');
    }

    function load_checklist(){
        fetch_assign_checklist_table();
        $('[href=#step5]').tab('show');
    }

    function load_visibility(){
        $('[href=#step6]').tab('show');
    }

    function load_conformation() {
        $('[href=#step7]').tab('show');
        $('.btn-wizard').removeClass('disabled');
        $('a[data-toggle="tab"]').removeClass('btn-primary');
        $('a[data-toggle="tab"]').addClass('btn-default');
        $('[href=#step7]').removeClass('btn-default');
        $('[href=#step7]').addClass('btn-primary');
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'contractAutoID': contractAutoID, 'html': true},
                url: "<?php echo site_url('Quotation_contract/load_contract_conformation_job'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Quotation_contract/load_contract_conformation_job'); ?>/" + contractAutoID);
                    //attachment_modal_customer_invoice(contractAutoID, "<?php echo $this->lang->line('sales_markating_quotation_Contract');?>", "QUT");/*Quotation / Contract*/
                    attachment_modal_Quotation_Contract(contractAutoID, "<?php echo $this->lang->line('sales_markating_quotation_Contract');?>", "QUT");/*Quotation / Contract*/
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

    function load_contract_header() {
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': contractAutoID},
                url: "<?php echo site_url('Quotation_contract/load_contract_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        if(data['isGroupBasedTax'] == 1) {
                            $('.lintax').removeClass('hide');
                            $('.lineTaxHeaderAdd').removeClass('hide');
                            $('.lineTaxHeader').addClass('hide');
                            $('.general_tax_view').addClass('hide');
                        } else {
                            $('.lintax').addClass('hide');
                            $('.lineTaxHeaderAdd').addClass('hide');
                            $('.lineTaxHeader').removeClass('hide');
                            $('.general_tax_view').removeClass('hide');
                        }
                        
                        contractAutoID = data['contractAutoID'];
                        contractType = data['contractType'];
                        customerID = data['customerID'];
                        currencyID = data['transactionCurrencyID'];
                        
                        var approvedYN = data['approvedYN'];
                        var amendmentID = data['currentAmedmentID'];

                        $('.currency').html('(' + data['transactionCurrency'] + ')');
                        $("#a_link").attr("href", "<?php echo site_url('Quotation_contract/load_Quotation_contract_conformation'); ?>/" + contractAutoID);
                        $("#paymentvouchercode").val(data['PVcode']);
                        $('#contractDate').val(data['contractDate']);
                        $('#contractExpDate').val(data['contractExpDate']);
                        $('#contractNarration').val(data['contractNarration']);
                        $('#contractType').val(data['contractType']).change();
                        $('#referenceNo').val(data['referenceNo']);

                        $('#email').val(data['email']);
                       
                        $('#docType').val(data['docTypeID']).change();
                        $('#ticket').val(data['ticketTemplate']).change();
                        $('#dalilTemplatey').val(data['dalilTemplateyID']).change();
                        $('#RVbankCode').val(data['RVbankCode']).change();
                        $('#activityCode').val(data['activityID']).change();
                        
                        if (data['LinkActivityYN'] == 1) {
                            $('#LinkActivityYN').iCheck('check');
                        } else {
                            $('#LinkActivityYN').iCheck('uncheck');
                        }

                        if (data['editJobBillingYN'] == 1) {
                            $('#editJobBillingYN').iCheck('check');
                        } else {
                            $('#editJobBillingYN').iCheck('uncheck');
                        }

                        
                        $('#contactValue').val(data['contactValue']);
                        if (data['contactPersonNumber']) {
                            // $('#customerID').removeAttr('onchange');
                            $('#customerID').val(data['customerID']).change();
                            // $('#customerID').attr("onchange", "Load_customer_details_edit(this.value)");
                            //$('#customerID').attr("onchange", "Load_customer_details(this.value)");

                            $('#contactPersonNumber').val(data['contactPersonNumber']);
                            $("#transactionCurrencyID").val(data['transactionCurrencyID']).change()
                        } else {
                            $('#customerID').val(data['customerID']).change();
                        }
                        $('#contactPersonName').val(data['contactPersonName']);

                        // $('#contactPersonName').val(data['contactPersonName']);
                        $('#salesperson').val(data['salesPersonID']).change();

                        /***********/                        
                        $('#paymentTerms').val(data['paymentTerms']);

                        //var resCM = jQuery.parseJSON(data['contractCMDetails']);                        

                        $('#addressBox').text(data['customerAddress']);
                        $('#emailBox').text(data['customerEmail']);
                        $('#contactNumberBox').text(data['customerTelephone']);
                        $('#customerUrlBox').text(data['customerWebURL']);
                        /***********/

                        if (data['showImageYN'] == 1) {
                            $('#showImageYN').iCheck('check');
                        } else {
                            $('#showImageYN').iCheck('uncheck');
                        }
                        // $('#contactPersonNumber').val(data['contactPersonNumber']);

                        setTimeout(function(){
                            tinyMCE.get("Note").setContent(data['Note']);
                        },300);

                        
                        if (data['segmentID'])
                        {
                            $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        }else
                        {
                            $('#segment').val('<?php echo $this->common_data['company_data']['default_segment']?>').change();
                        }

                        if(isGroupBasedYN ==1){
                            $('.lintax').removeClass('hide');

                        }else{
                            $('.lintax').addClass('hide');
                        }

                        fetch_detail_table();
                       // $("#contractType").prop("disabled", true);
                        $("#customerID").prop("disabled", true);
                        $("#transactionCurrencyID").prop("disabled", true);
                        $("#segment").prop("disabled", true);
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');

                        //if approved contract
                        if(approvedYN == 1){
                            $("#quotation_contract_form :input").prop("disabled",true); 
                        }

                        setTimeout(() => {
                            if(amendmentID > 0){
                                get_amendment_details(amendmentID);
                            }
                        }, 100);

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

    function get_amendment_details(amendmentID){

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Quotation_contract/fetch_amendment_details"); ?>',
            dataType: 'json',
            data: {'amendmentID': amendmentID},
            success: function (data) {
                var amendmentStatus = data.status;
                if(amendmentStatus == 0){

                    myAlert('w','Document is Open For Amendments');

                    $('#btncreateAmendment').css('display','none');
                    $('#btncloseAmendment').css('display','block').prop('disabled',false);

                    $('#amendmentNotice').removeClass('hide');

                    var amendType = data.amendmentType;
                    var amend_arr= amendType.split(',');

                    // $('#div_ammendmentType :input').prop('disabled',false);
                    $('#div_ammendmentType ').addClass('hide');
                    // $('#ammendmentType').val([amendType]);
                   

                    $('#amendedExpDate').removeClass('hide');
                    $('#amendmentID').val(amendmentID).prop('disabled',false);
                    $('.notes_termsandcond').prop('disabled',false);
                    $('#referenceNo').prop('disabled',false);
                    $('#contractAmdExpDate').prop('disabled',false);

                }else{
                    $('#btncreateAmendment').css('display','block').prop('disabled',false);;
                    $('#btncloseAmendment').css('display','none').prop('disabled',true);

                    $('#amendmentNotice').addClass('hide');
                   
                    $('#div_ammendmentType :input').prop('disabled',false);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#ajax_nav_container').html(xhr.responseText);
            }
        });

    }

    function save_draft() {
        if (contractAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to save this document*/
                    type: "warning",/*warning*/
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/quotation_contract/quotation_contract_management', contractAutoID, 'Quotation_contract');
                });
        }
        ;
    }

    function attachment_modal_Quotation_Contract(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Quotation_contract/fetch_documentID"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    attachment_modal_customer_invoice(documentSystemCode, document_name,data)
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function attachment_modal_customer_invoice(documentSystemCode, document_name, documentID) {
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
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('sales_markating_transaction_you_want_to_delete_this_attachment_file');?>",/*You want to delete this attachment file!*/
                    type: "warning",/*warning*/
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
                        data: {'attachmentID': InvoiceAutoID,'myFileName': myFileName},
                        url: "<?php echo site_url('Quotation_contract/delete_quotationContract_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s','<?php echo $this->lang->line('common_deleted_successfully');?>');/*Deleted Successfully*/
                                attachment_modal_Quotation_Contract(DocumentSystemCode, "Quotation", "QUT");
                            }else{
                                myAlert('e','<?php echo $this->lang->line('common_deletion_failed');?>');/*Deletion Failed*/
                            }
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function add_more_item() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#item_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);

        appendData.find('.mainCategoryID').attr('id', 'mainCategoryID_' + search_id);
        appendData.find('.subcategoryID').attr('id', 'subcategoryID_' + search_id);
        appendData.find('.subsubcategoryID').attr('id', 'subsubcategoryID_' + search_id);
        appendData.find('.revanueGLAutoID').attr('id', 'revanueGLAutoID_' + search_id);
        appendData.find('.costGLAutoID').attr('id', 'costGLAutoID_' + search_id);
        appendData.find('.groupToCategory').attr('id', 'groupToCategory_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(this)');
        appendData.find('.umoDropdown').empty();
        appendData.find('.itemID').attr('id', 'itemID_' + search_id).attr('onchange', 'load_item_glcode(this);');
    
        //appendData.find('.DetailctivityCode').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.number').val('0');
        appendData.find('.number,.wac_cost,.net_unit_cost,.net_amount').text('0');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#item_add_table').append(appendData);
        var lenght = $('#item_add_table tbody tr').length - 1;
        $('#f_search_'+ search_id).closest('tr').css("background-color",'white');
        $(".select2").select2();
        initializeitemTypeahead(search_id);
        number_validation();
    }

    function add_more_crew(){
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#crew_add_table tbody tr:first').clone();
        appendData.find('.groupToCrew').attr('id', 'groupToCrew_' + search_id);
        appendData.find('input').val('');
        appendData.find("input[type='checkbox']").attr('checked',false);
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#crew_add_table').append(appendData);

        $(".select2").select2();
    }

    function add_more_assets(){
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#assets_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('.groupToAsset').attr('id', 'groupToAsset_' + search_id);
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#assets_add_table').append(appendData);

        $(".select2").select2();
    }


    function saveCrewDetails(){

        var data = $('#crew_detail_form').serializeArray();
        if(contractAutoID){

            data.push({'name': 'contractAutoID', 'value': contractAutoID});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/save_contract_job_crew'); ?>",
                beforeSend: function () {
                    startLoad();
    
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if(data){
                        $('#crew_add_modal').modal('hide');
                        fetch_crew_table();
                    }
                   
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }


    }

    

    function saveGroupToDetailsAsset(){

        var data = $('#crew_group_form_asset').serializeArray();

        if(contractAutoID){
            data.push({'name': 'contractAutoID', 'value': contractAutoID});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Quotation_contract/save_contract_group_to'); ?>",
                    beforeSend: function () {
                        startLoad();

                    },
                    success: function (data) {
                        
                        stopLoad();
                       
                        //myAlert(data[0], data[1]);
                        if(data['status']==true){
                            var tab= $('#tb_open_asset').val();
                            var type= $('#type_open_asset').val();
                        
                            // if(tab==1){
                            //     if(type==1){
                            //         $("#crew_add_table tr").each(function (){
                            //             $(this).closest('tr').find('.groupToCrew').each(function() {
                            //                 var elem = $(this).attr('id');
                            //                 var mySelect = $('#'+elem);
                            //                 mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                            //             });
                            //         });

                            //         var myed =$('#groupToCrew_edit');
                            //         myed.append($('<option></option>').val(data['id']).html(data['name']));
                            //     }else{
                            //         $("#crew_add_table_edit1 tr").each(function (){
                            //             $(this).closest('tr').find('.groupToCrew_edit').each(function() {
                            //                 var elem = $(this).attr('id');
                            //                 var mySelect = $('#'+elem);
                            //                 mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                            //             });
                            //         });

                            //         $("#crew_add_table tr").each(function (){
                            //             $(this).closest('tr').find('.groupToCrew').each(function() {
                            //                 var elem = $(this).attr('id');
                            //                 var mySelect = $('#'+elem);
                            //                 mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                            //             });
                            //         });
                            //     }
                            // }else{
                                if(type==1){
                                    $("#assets_add_table tr").each(function (){
                                        $(this).closest('tr').find('.groupToAsset').each(function() {
                                            var elem = $(this).attr('id');
                                            var mySelect = $('#'+elem);
                                            mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                                        });
                                    });

                                    var myed1 =$('#groupToAsset_edit');
                                    myed1.append($('<option></option>').val(data['id']).html(data['name']));
                                }else{
                                    
                                    $("#assets_add_table_edit tr").each(function (){
                                        $(this).closest('tr').find('.groupToAsset_edit').each(function() {
                                            var elem = $(this).attr('id');
                                            var mySelect = $('#'+elem);
                                            mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                                        });
                                    });

                                    $("#assets_add_table tr").each(function (){
                                        $(this).closest('tr').find('.groupToAsset').each(function() {
                                            var elem = $(this).attr('id');
                                            var mySelect = $('#'+elem);
                                            mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                                        });
                                    });
                                }
                            // }
                            
                        

                            $('#groupTo_add_modal_asset').modal('hide');
                        }

                        refreshNotifications(true);
                        
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
        }

        }

        function saveGroupToDetailsCategory(){

            var data = $('#crew_group_form_category').serializeArray();

            if(contractAutoID){
                data.push({'name': 'contractAutoID', 'value': contractAutoID});
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Quotation_contract/save_contract_group_to_category'); ?>",
                        beforeSend: function () {
                            startLoad();

                        },
                        success: function (data) {
                            
                            stopLoad();
                        
                            //myAlert(data[0], data[1]);
                            if(data['status']==true){
                             
                            $("#item_add_table tr").each(function (){
                                $(this).closest('tr').find('.groupToCategory').each(function() {
                                    var elem = $(this).attr('id');
                                    var mySelect = $('#'+elem);
                                    mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                                });
                            });

                            var myed1 =$('#groupToCategory_edit');
                            myed1.append($('<option></option>').val(data['id']).html(data['name']));
                                   

                                $('#groupTo_add_modal_category').modal('hide');
                            }

                            refreshNotifications(true);
                            
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
            }

        }

    function saveGroupToDetails(){

        var data = $('#crew_group_form').serializeArray();

        if(contractAutoID){
             data.push({'name': 'contractAutoID', 'value': contractAutoID});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Quotation_contract/save_contract_group_to'); ?>",
                    beforeSend: function () {
                        startLoad();

                    },
                    success: function (data) {
                        
                        stopLoad();
                       
                        //myAlert(data[0], data[1]);
                        if(data['status']==true){
                            var tab= $('#tb_open').val();
                            var type= $('#type_open').val();
                        
                            // if(tab==1){
                                if(type==1){
                                    $("#crew_add_table tr").each(function (){
                                        $(this).closest('tr').find('.groupToCrew').each(function() {
                                            var elem = $(this).attr('id');
                                            var mySelect = $('#'+elem);
                                            mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                                        });
                                    });

                                    var myed =$('#groupToCrew_edit');
                                    myed.append($('<option></option>').val(data['id']).html(data['name']));
                                }else{
                                    $("#crew_add_table_edit1 tr").each(function (){
                                        $(this).closest('tr').find('.groupToCrew_edit').each(function() {
                                            var elem = $(this).attr('id');
                                            var mySelect = $('#'+elem);
                                            mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                                        });
                                    });

                                    $("#crew_add_table tr").each(function (){
                                        $(this).closest('tr').find('.groupToCrew').each(function() {
                                            var elem = $(this).attr('id');
                                            var mySelect = $('#'+elem);
                                            mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                                        });
                                    });
                                }
                            // }else{
                            //     if(type==1){
                            //         $("#assets_add_table tr").each(function (){
                            //             $(this).closest('tr').find('.groupToAsset').each(function() {
                            //                 var elem = $(this).attr('id');
                            //                 var mySelect = $('#'+elem);
                            //                 mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                            //             });
                            //         });

                            //         var myed1 =$('#groupToAsset_edit');
                            //         myed1.append($('<option></option>').val(data['id']).html(data['name']));
                            //     }else{
                                    
                            //         $("#assets_add_table_edit tr").each(function (){
                            //             $(this).closest('tr').find('.groupToAsset_edit').each(function() {
                            //                 var elem = $(this).attr('id');
                            //                 var mySelect = $('#'+elem);
                            //                 mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                            //             });
                            //         });

                            //         $("#assets_add_table tr").each(function (){
                            //             $(this).closest('tr').find('.groupToAsset').each(function() {
                            //                 var elem = $(this).attr('id');
                            //                 var mySelect = $('#'+elem);
                            //                 mySelect.append($('<option></option>').val(data['id']).html(data['name']));
                            //             });
                            //         });
                            //     }
                            // }
                            
                        

                            $('#groupTo_add_modal').modal('hide');
                        }

                        refreshNotifications(true);
                        
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
        }

    }

    function editCrewDetails(){
        var data = $('#crew_detail_form_edit').serializeArray();
        if(contractAutoID){

            data.push({'name': 'contractAutoID', 'value': contractAutoID});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/edit_contract_job_crew'); ?>",
                beforeSend: function () {
                    startLoad();
    
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if(data){
                        $('#crew_add_modal_edit').modal('hide');
                        fetch_crew_table();
                    }
                   
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }
    }

    function saveAssetsDetails(){

        var data = $('#assets_detail_form').serializeArray();
        if(contractAutoID){
            data.push({'name': 'contractAutoID', 'value': contractAutoID});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/save_contract_assets'); ?>",
                beforeSend: function () {
                    startLoad();

                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if(data){
                        $('#assets_add_modal').modal('hide');
                        fetch_assets_table();
                    }
                
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

            }
    }

    function editAssetsDetails(){
       
        var data = $('#assets_detail_form_edit').serializeArray();
        if(contractAutoID){

            data.push({'name': 'contractAutoID', 'value': contractAutoID});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/edit_contract_asset_crew'); ?>",
                beforeSend: function () {
                    startLoad();
    
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if(data){
                        $('#assets_add_modal_edit').modal('hide');
                        fetch_assets_table();
                    }
                   
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }
    }

    function saveItemOrderDetail() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#item_detail_form').serializeArray();
        if (contractAutoID) {
            data.push({'name': 'contractAutoID', 'value': contractAutoID});
            data.push({'name': 'contractDetailsAutoID', 'value': contractDetailsAutoID});

            $('#item_detail_form' + ' select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
            });
            $('#item_detail_form' + ' select[name="projectID[]"] option:selected').each(function () {
                data.push({'name': 'projectID[]', 'value': $(this).text()})
            });


            // $('.itemAutoID').each(function () {
            //     if (this.value == '') {
            //         $(this).closest('tr').css("background-color", '#ffb2b2 ');
            //     }
            // });

            $('.quantityRequested').each(function () {
                if (this.value == '' || this.value == '0') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });

            $('.estimatedAmount').each(function () {
                if (this.value == '' || this.value == '0') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });

            data.push({'name': 'isAmendment', 'value': 1})

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/save_item_order_detail_job'); ?>",
                beforeSend: function () {
                    startLoad();
                    // $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {
                    contractDetailsAutoID = null;
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetch_detail_table();
                        $('#item_detail_modal').modal('hide');
                        $('#item_detail_form')[0].reset();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
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

    function updateItemOrderDetail() {
        $('#edit_UnitOfMeasureID').prop("disabled", false);
        var data = $('#edit_item_detail_form').serializeArray();
        if (contractAutoID) {
            data.push({'name': 'contractAutoID', 'value': contractAutoID});
            data.push({'name': 'contractDetailsAutoID', 'value': contractDetailsAutoID});
            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
            data.push({'name': 'isAmendment', 'value': 1})
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json', data: data,
                url: "<?php echo site_url('Quotation_contract/update_item_order_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                    // $('#edit_UnitOfMeasureID').prop("disabled", true)
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        contractDetailsAutoID = null;
                        fetch_detail_table();
                        $('#edit_item_detail_modal').modal('hide');
                        $('#edit_item_detail_form')[0].reset();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
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

    function fetch_sales_price(salesprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: contractAutoID,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_contractmaster',
                primaryKey: 'contractAutoID',
                customerAutoID : customerID,
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price_customerWise'); ?>",
            success: function (data) {
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function fetch_sales_price_edit(salesprice, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: contractAutoID,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_contractmaster',
                primaryKey: 'contractAutoID',
                customerAutoID : customerID,
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price_customerWise'); ?>",
            success: function (data) {
                if (data['status']) {
                    $('#edit_estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

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


    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if(number.length>1 && charCode == 46){
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if( caratPos > dotPos && dotPos>-(currency_decimal-1) && (number[1] && number[1].length > (currency_decimal-1))){
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

    function load_default_note(){
        if (p_id) {

        }else{
            var docType=$('#contractType').val();
            var docid='QUT';
            if(docType=='Quotation'){
                docid='QUT';
            }else if(docType=='Contract'){
                docid='CNT';
            }else{
                docid='SO';
            }
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'docid': docid},
                url: "<?php echo site_url('Invoices/load_default_note'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
            /*            $('#Note ~ iframe').contents().find('.wysihtml5-editor').html('');
                        $('#Note ~ iframe').contents().find('.wysihtml5-editor').html(data['description']);*/
                        tinyMCE.get("Note").setContent(data['description']);
                    }else{
                        //myAlert('w','Default Note not set')
                    }
                }
            });
        }
    }

    function open_all_notes(){
        var docType=$('#contractType').val();
        var docid='QUT';
        if(docType=='Quotation'){
            docid='QUT';
        }else if(docType=='Contract'){
            docid='CNT';
        }else{
            docid='SO';
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'docid': docid},
            url: "<?php echo site_url('Invoices/open_all_notes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#allnotebody').empty();
                    var x=1;
                    $.each(data, function (key, value) {
                        $('#allnotebody').append('<label class="chkboxlabl" ><input type="radio" name="allnotedesc" value="' + value['autoID'] + '" id="chkboxlabl_' + value['autoID'] + '">' + value['description'] + '</label>')
                        x++;
                    });
                    $("#all_notes_modal").modal({backdrop: "static"});
                }else{
                    myAlert('w','No Notes assigned')
                }
            }
        });
    }

    function save_notes(){
        var data = $("#all_notes_form").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Invoices/load_notes'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
              /*  $('#Note ~ iframe').contents().find('.wysihtml5-editor').html('');
                $('#Note ~ iframe').contents().find('.wysihtml5-editor').html(data['description']);*/
                tinyMCE.get("Note").setContent(data['description']);
                $("#all_notes_modal").modal('hide');
            }, error: function () {
                stopLoad();
            }
        });
    }
    function Load_customer_details(customerid) {

      //  $('#contactPersonName').val('');
       // $('#contactPersonNumber').val('');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'customerAutoID': customerid,'contractAutoID': contractAutoID},
            url: "<?php echo site_url('Invoices/fetch_customer_details_by_id'); ?>",
            beforeSend: function () {
            },
            success: function (data) {

                $('#contactPersonNumber').val(data['contactPersonNumber']);
                $('#contactPersonName').val(data['contactPersonName']);
                if(contractAutoID){
                    $('#paymentTerms').val(data['paymentTerms']);
                }else{
                    $('#paymentTerms').val(data['customerCreditPeriod'] * 30);
                }
                
                $('#addressBox').text(data['customerAddress1']);
                $('#emailBox').text(data['customerEmail']);
                $('#contactNumberBox').text(data['customerTelephone']);
                $('#customerUrlBox').text(data['customerUrl']);
                $('#cusAutoId').attr("value",data['cusAuto']);

            }
        });

    }
    function Load_customer_details_edit(customerid) {
        $('#contactPersonNumber').val('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'customerAutoID': customerid},
            url: "<?php echo site_url('Invoices/fetch_customer_details_currency'); ?>",
            beforeSend: function () {
            },
            success: function (data) {

                $("#transactionCurrencyID").val(data['currency']['customerCurrencyID']).change();
                $('#contactPersonNumber').val(data['detail']['customerTelephone']);
            }
        });

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
                        fetch_customerdrop(data['last_id'],'');
                        Load_customer_currency(data['last_id']);
                        load_customer_master_details(data['last_id']);
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


    function fetch_customerdrop(id,contractAutoID) {
        Documentid = <?php echo json_encode(trim($this->input->post('policy_id'))); ?>;
        var customer_id;
        var page = '';
        if(contractAutoID)
        {
            page = contractAutoID
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
            data: {customer:customer_id,DocID:page,Documentid:Documentid},
            url: "<?php echo site_url('Invoices/fetch_customer_Dropdown_all_contract'); ?>",
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
    function load_customer_master_details(customerid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {customer:customerid},
            url: "<?php echo site_url('Invoices/fetch_customer_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#contactPersonNumber').val(data['customerTelephone']);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function fetch_segment_master()
    {
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID': contractAutoID},
                url: "<?php echo site_url('Invoices/fetch_quotation_segment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    load_segmentBase_projectID_item(data['segmentID'] + '|' + data['segmentCode'])
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    }
    function load_segmentBase_projectID_item(segment) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_multiple"); ?>',
            dataType: 'html',
            data: {segment: segment},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.div_projectID_item').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }
    function load_segmentBase_projectID_itemEdit() {
        var segment = $('#segment').val();
        var type = 'item';
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase"); ?>',
            dataType: 'html',
            data: {segment: segment, type: type},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#edit_div_projectID_item').html(data);
                $('.select2').select2();
                if (projectID) {
                    $("#projectID_item").val(projectID).change();
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function load_project_segmentBase_category(element,projectID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_category"); ?>',
            dataType: 'json',
            data: {projectID: projectID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(element).parent().closest('tr').find('.project_subCategoryID').empty();
                var subCat = $(element).parent().closest('tr').find('.project_subCategoryID');
                subCat.append($('<option></option>').val('').html('Select Project Subcategory'));
                $(element).parent().closest('tr').find('.project_categoryID').empty();
                var mySelect =   $(element).parent().closest('tr').find('.project_categoryID');
                mySelect.append($('<option></option>').val('').html('Select Project Category'));

                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['categoryID']).html(text['categoryCode']+' - '+text['categoryDescription']));
                    });
                    if (projectcategory) {
                        $("#project_categoryID_edit").val(projectcategory).change();
                        $("#project_categoryID_edit1").val(projectcategory).change();
                    }
                }
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }
    function fetch_project_sub_category(element,categoryID) {
        projectID = $(element).closest('tr').find('.projectID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/fetch_project_sub_category"); ?>',
            dataType: 'json',
            data: {categoryID: categoryID,projectID:projectID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(element).parent().closest('tr').find('.project_subCategoryID').empty();
                var mySelect =  $(element).parent().closest('tr').find('.project_subCategoryID');
                mySelect.append($('<option></option>').val('').html('Select Project Subcategory'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['subCategoryID']).html(text['description']));
                    });
                    if (projectsubcat) {
                        $("#project_subCategoryID_edit").val(projectsubcat).change();
                        $("#project_subCategoryID_edit1").val(projectsubcat).change();

                    };
                }
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function convertPrice_CNT(element) {
        var itemAutoID = $(element).closest('tr').find('.itemAutoID').val();
        var estimatedAmount = $(element).closest('tr').find('.estimatedAmount').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID,
                'uomID' : element.value,
                'wareHouseAutoID' : null,
                'estimatedAmount' : estimatedAmount,
                'tableName': 'srp_erp_contractmaster',
                'primaryKey': 'contractAutoID',
                'id': contractAutoID,
                'customerAutoID': customerID
            },
            url: '<?php echo site_url("Invoices/fetch_converted_price_qty_invoice"); ?>',
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('.currentstock').val(data['qty']);
                    $(element).closest('tr').find('.estimatedAmount').val(data['price']);
                    $(element).closest('tr').find('.conversionRate_CNT').val(data['conversionRate']);
                    $(element).closest('tr').find('.wac_cost').text(parseFloat(data['localwacamount']).formatMoney(currency_decimal, '.', ','));
                } else {
                    // $('#search').empty();
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function convertPrice_CNT_edit(element) {
        var itemAutoID = $(element).closest('tr').find('#edit_itemAutoID').val();
        var estimatedAmount = $(element).closest('tr').find('#edit_estimatedAmount').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                'itemAutoID': itemAutoID,
                'uomID': element.value,
                'wareHouseAutoID': null,
                'estimatedAmount': estimatedAmount,
                'tableName': 'srp_erp_contractmaster',
                'primaryKey': 'contractAutoID',
                'id': contractAutoID,
                'customerAutoID': customerID
            },
            url: '<?php echo site_url("Invoices/fetch_converted_price_qty_invoice"); ?>',
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('#currentstock').val(data['qty']);
                    $(element).closest('tr').find('#edit_estimatedAmount').val(data['price']);
                    $(element).closest('tr').find('#edit_quantityRequested').val(0);
                    $(element).closest('tr').find('#conversionRateCNTEdit').val(data['conversionRate']);
                    $(element).closest('tr').find('#edit_wac_cost').text(parseFloat(data['localwacamount']).formatMoney(currency_decimal, '.', ','));
                } else {
                    // $('#search').empty();
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }
    function load_item_wacAmount_UOMexchangerate(itemAutoID,contractAutoID,uomexrate) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID,'contractAutoID':contractAutoID,'uomexrate':uomexrate},
            url: "<?php echo site_url('Quotation_contract/fetch_converted_waccost'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    $('#edit_wac_cost').text(parseFloat(data).formatMoney(currency_decimal, '.', ','))
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function LoaditemUnitPrice_againtsExchangerate_edit_edit(LocalWacAmount) {
        poID = contractAutoID;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID, 'LocalWacAmount': LocalWacAmount},
            url: "<?php echo site_url('Quotation_contract/load_unitprice_exchangerate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $('#edit_wac_cost').text(parseFloat(data['amount']).formatMoney(currency_decimal, '.', ','));
                }
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_line_tax_and_vat(itemAutoID, element)
    {
        select_VAT_value = '';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID,'itemAutoID':itemAutoID},
            url: "<?php echo site_url('Quotation_contract/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                if(data['isGroupByTax'] == 1) {
                    $(element).closest('tr').find('.text_type').empty();
                    var mySelect = $(element).parent().closest('tr').find('.text_type');
                    mySelect.append($('<option></option>').val('').html('Select Tax'));
                    if (!jQuery.isEmptyObject(data['dropdown'])) {
                        $.each(data['dropdown'], function (val, text) {
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
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_line_tax_amount(ths){
        var qut = $(ths).closest('tr').find('.quantityRequested').val();
        var itemAutoID = $(ths).closest('tr').find('.itemAutoID').val();
        var amount = $(ths).closest('tr').find('.estimatedAmount').val();
        var discoun = $(ths).closest('tr').find('.discount_amount').val();
        var taxtype = $(ths).closest('tr').find('.text_type').val();
        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(qut)) {
            qut=0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun=0;
        }

        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = (qut * amount);
            discoun = discoun * qut;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID':contractAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'itemAutoID':itemAutoID,'discount':discoun},
                url: "<?php echo site_url('Quotation_contract/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $(ths).closest('tr').find('.linetaxamnt').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.net_amount').text((parseFloat(data)+parseFloat(lintaxappamnt)-parseFloat(discoun)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $(ths).closest('tr').find('.linetaxamnt').text('0');
            $(ths).closest('tr').find('.net_amount').text((parseFloat(qut * amount)-parseFloat(discoun)).toFixed(currency_decimal));
        }
    }

    function load_line_tax_amount_edit(ths){
        var qut = $('#edit_quantityRequested').val();
        var amount = $('#edit_estimatedAmount').val();
        var discoun = $('#edit_discount_amount').val();
        var taxtype = $('#text_type_edit').val();
        var itemAutoID = $('#edit_itemAutoID').val();
        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(qut)) {
            qut=0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun=0;
        }
        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = (qut * amount);
            discoun = discoun * qut;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID':contractAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'itemAutoID':itemAutoID, 'discount':discoun},
                url: "<?php echo site_url('Quotation_contract/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $('#linetaxamnt_edit').text(data.toFixed(currency_decimal));
                    $('#edit_totalAmount').text((parseFloat(data)+parseFloat(lintaxappamnt)-parseFloat(discoun)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $('#linetaxamnt_edit').text('0');
            $('#edit_totalAmount').text((parseFloat(qut * amount)-parseFloat(discoun)).toFixed(currency_decimal));
        }

    }
    
    function edit_fetch_line_tax_and_vat(itemAutoID)
    {
        var selected_itemAutoID = $('#edit_itemAutoID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractAutoID': contractAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Quotation_contract/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                if(data['isGroupByTax'] == 1) {
                    $('#text_type_edit').empty();
                    var mySelect = $('#text_type_edit');
                    mySelect.append($('<option></option>').val('').html('Select Tax'));
                    if (!jQuery.isEmptyObject(data['dropdown'])) {
                        $.each(data['dropdown'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                        });
                        if(selected_itemAutoID!=itemAutoID){
                            if(data['selected_itemTax']!=0){
                                $('#text_type_edit').val(data['selected_itemTax']).change();
                            }else{
                                $('#text_type_edit').val(null).change();
                            }
                            load_line_tax_amount_edit();
                        }else{
                            if (select_VAT_value) {
                                $('#text_type_edit').val(select_VAT_value);
                                load_line_tax_amount_edit();
                            }
                        }
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function open_Check_list_model(search=null) {
       var search_index = '';
        
        search_index  = search;
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {Search:search_index,contractAutoID:contractAutoID},
            url: "<?php echo site_url('Quotation_contract/assignItem_checklist_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
       
                $('#assignChecklist_item_Content').html(data);
           
                $("#assignChecklist_model").modal({backdrop: "static"});
                 
            
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function startMasterSearch(){ 
       // var itemAutoID =  $('#assignedSupplier_itemID').val();
        var search = $('#searchOrder').val();
        open_Check_list_model(search);
    }


    function assign_checklist_selected_check(sup) {
       
        var value = $(sup).val();
        if ($(sup).is(':checked')) {
            var inArray = $.inArray(value, assignCheckListSync);
            if (inArray == -1) {
                assignCheckListSync.push(value);
            }
        }
        else {
            var i = assignCheckListSync.indexOf(value);
            if (i != -1) {
                assignCheckListSync.splice(i, 1);
            }
        }
    }

    function assign_checklist() {
        var id = contractAutoID;
        if(id && assignCheckListSync.length>0){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'assignCheckListSync': assignCheckListSync,
                    'contractAutoID':contractAutoID,
                },
                url: "<?php echo site_url('Quotation_contract/assignCheckListForContract'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                    // generate_order_itemView();
                    fetch_assign_checklist_table() ;
                        assignCheckListSync =[];
                        $("#assignChecklist_model").modal('hide');
                    } else {

                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }else{
            myAlert('e', 'please select checklist');
        }
        
    }

    function fetch_assign_checklist_table_old(){
        setTimeout(loadSelectOptionDrop, 800);
        Otable = $('#contract_checklist_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Quotation_contract/fetch_check_list_contract'); ?>",
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
                {"mData": "contractChecklistAutoID"},
                {"mData": "documentID"},
                {"mData": "checklistDescription"},
                {"mData": "action"},
                {"mData": "call"},
                {"mData": "user"},
              
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "contractAutoID", "value": contractAutoID});
              
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

    function fetch_assign_checklist_table(){
        setTimeout(loadSelectOptionDrop, 500);
        if (contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'contractAutoID': contractAutoID, 'html': true},
                url: "<?php echo site_url('Quotation_contract/fetch_check_list_contract_job'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#table_body_checklist').html(data);
                    
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

    function selectCallingUpdate(thisCombo,id){

        var callID = $(thisCombo).find(':selected').attr('data-cat');

        if(callID){
            swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "You want to add this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "Confirm",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
                function () {
                    $.ajax({
                        async : true,
                        url :"<?php echo site_url('Quotation_contract/selectCallingUpdate'); ?>",
                        type : 'post',
                        dataType : 'json',
                        data : {'callID':callID,'masterID':id},
                        beforeSend: function () {
                            startLoad();
                        },
                        success : function(data){
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if( data[0] == 's'){ 
                                fetch_assign_checklist_table();
                        }
                        },error : function(){
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
            );
        }

    }

    function delete_contract_checklist(id){
        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
                    type: "warning",/*warning*/
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
                        data: {'id': id},
                        url: "<?php echo site_url('Quotation_contract/delete_checklist_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_assign_checklist_table() ;
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
 
    function open_contract_checklist(id) {
        if(id == 3){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 4){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 2){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 5){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 6){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 7){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        } else if(id == 8){
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        }  else{
            $('#checklist_view_modal_common').modal('show');
            load_checklist_single(id);
        }
        
    }

    function load_checklist_single(id) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Operation/load_checklist_single"); ?>',
            dataType: 'html',
            data: {'id': id},
            async: false,
            success: function (data) {
                $('#checklist_view_modal').html(data);
                $("#checklist_view_modal input").prop('disabled', true);
                $('#checklist_view_modal .btn-primary-new').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("error");
            }
        });
    }

    function delete_asset_line_contract(id){

        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
                    type: "warning",/*warning*/
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
                        data: {'id': id},
                        url: "<?php echo site_url('Quotation_contract/delete_asset_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_assets_table() ;
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

    }

    function delete_contract_visibility(id){

        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record*/
                    type: "warning",/*warning*/
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
                        data: {'id': id},
                        url: "<?php echo site_url('Quotation_contract/delete_contract_visibility'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_visibility_table() ;
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

    }

    function open_visibility_list_model(){

        $('#section').val('');
        $('#customerCode').val('');
        $('#action_ar').val('');
        $('#visibility_model').modal('show');
    }

    function edit_visibility_line(id){

        $.ajax({
            async : true,
            url :"<?php echo site_url('Quotation_contract/fetch_line_visibility_edit'); ?>",
            type : 'post',
            dataType : 'json',
            data : {'masterID':id},
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();

                if (!jQuery.isEmptyObject(data)) {
                    $('#section_edit').val(data['sectionCode']).change();

                    $('#visibilityAutoID').val(data['visibilityAutoID']);

                    var user = data['visibilityuserIDs'].split(",");
                   // $('#customerCode_edit').val(user).change();

                   var actions = data['actionCodes'].split(",");
                    //$('#action_ar_edit').val(actions).change();
                 //   $("#customerCode_edit1").val([1,2,3,6]).trigger("change"); 
                   // $("#customerCode_edit").select2().val(user).trigger('change.select2');
                   $("#customerCode_edit1").select2().val(user).trigger('change.select2');
                   $("#action_ar_edit1").select2().val(actions).trigger('change.select2');

                    // var optionsToSelect = data['visibilityuserIDs'].split(",");
                    // var select = document.getElementById( 'customerCode_edit' );

                    // for ( var i = 0, l = select.options.length, o; i < l; i++ )
                    // {
                    //     o = select.options[i];
                    //     if ( user.indexOf( o.text ) != -1 )
                    //     {
                    //         o.selected = true;
                    //     }
                    // }

                    $('#visibility_model_edit').modal('show');
                }
            },error : function(){
                stopLoad();
                myAlert('e', 'error');
            }
        });

    }

    function save_visibility() {
        var data = $("#visibility_form").serializeArray();
        data.push({'name' : 'contractAutoID', 'value' : contractAutoID});
        //data.push({'name' : 'customerAutoID', 'value' : $('#customerID option:selected').val()});
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/save_visibility'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if(data['status'] == true)
                    {
                        fetch_visibility_table();
                        $('#visibility_model').modal('hide');

                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    //$('#emp_model').modal('show');
                    refreshNotifications(true);
                }
            });
    }

    function save_visibility_edit() {
        var data = $("#visibility_form_edit").serializeArray();
        data.push({'name' : 'contractAutoID', 'value' : contractAutoID});
        //data.push({'name' : 'customerAutoID', 'value' : $('#customerID option:selected').val()});
        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Quotation_contract/save_visibility_edit'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if(data['status'] == true)
                    {
                        fetch_visibility_table();
                        $('#visibility_model_edit').modal('hide');

                    }

                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    //$('#emp_model').modal('show');
                    refreshNotifications(true);
                }
            });
    }

    function fetch_visibility_table(){
        
        Otable = $('#contract_visibility_table').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Quotation_contract/fetch_contract_visibility_table'); ?>",
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
                {"mData": "visibilityAutoID"},
                {"mData": "sectionCode"},
                {"mData": "name"},
                {"mData": "actionCodes"},
                {"mData": "action"},
              
            ],
            "columnDefs": [],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "contractAutoID", "value": contractAutoID});
              
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

    function selectChecklistUserUpdate(ths,id){

        var users = $(ths).closest('tr').find('.select_user').val();
        $.ajax({
            async : true,
            url :"<?php echo site_url('Quotation_contract/selectChecklistUserUpdate'); ?>",
            type : 'post',
            dataType : 'json',
            data : {'users':users,'masterID':id},
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);
                if( data[0] == 's'){ 
                    //fetch_assign_checklist_table();
                }
            },error : function(){
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    function create_amendment_contract(){
        
        var ammendmentType = $('#ammendmentType').val();

        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
            text: "Are you want to ammend this document",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'ammendmentType': ammendmentType,'contractAutoID':contractAutoID},
                url: "<?php echo site_url('Quotation_contract/create_amendment_for_document'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    fetchPage('system/quotation_contract/erp_quotation_contract_job',contractAutoID,'Edit Quotation or Contract','CNT'); 
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
    }

    function close_amendment_contract(){
        var ammendmentType = $('#ammendmentType').val();

        swal({
            title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure*/
            text: "Are you want to close the Amended document",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Delete*/
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
        },
        function () {
            $.ajax({
                async: true,
                type: 'post',
                //dataType: 'json',
                data: {'contractAutoID':contractAutoID},
                url: "<?php echo site_url('Quotation_contract/close_amendment_for_document'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    fetchPage('system/quotation_contract/erp_quotation_contract_job',contractAutoID,'Edit Quotation or Contract','CNT'); 
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });
    }

    function set_payment_method(){

    }
    
</script>

<!-- Edit Contract customer details -->
<script type="text/javascript">   
    function contract_customer_details_edit(){
        var customerAutoID = $("#cusAutoId").val();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'customerAutoID': customerAutoID,'contractAutoID': contractAutoID},
            url: "<?php echo site_url('Invoices/fetch_customer_details_by_id'); ?>",
            beforeSend: function () {
            },
            success: function (data) {

                $('#addressBoxEdit').val(data['customerAddress1']);
                $('#emailBoxEdit').val(data['customerEmail']);
                $('#contactNumberBoxEdit').val(data['customerTelephone']);
                $('#customerUrlBoxEdit').val(data['customerUrl']);

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }            
        });
        $("#editContractCustomerDetails").modal('show');
    }

    function set_job_edit_activity(val){

        if(val=='job'){
            $('#job_activity_status').removeClass('hide');
        }else{
            $('#job_activity_status').addClass('hide');
        }

    }
</script>
<!-- Update Contract customer details -->
<script type="text/javascript">   
    function update_contract_customer_details(){
        var addressBoxEdit = $("#addressBoxEdit").val();
        var emailBoxEdit = $("#emailBoxEdit").val();
        var contactNumberBoxEdit = $("#contactNumberBoxEdit").val();
        var customerUrlBoxEdit = $("#customerUrlBoxEdit").val();    

        $('#addressBoxEditH').val(addressBoxEdit);
        $('#emailBoxEditH').val(emailBoxEdit);
        $('#contactNumberBoxEditH').val(contactNumberBoxEdit);
        $('#customerUrlBoxEditH').val(customerUrlBoxEdit);

        $('#addressBox').text(addressBoxEdit);
        $('#emailBox').text(emailBoxEdit);
        $('#contactNumberBox').text(contactNumberBoxEdit);
        $('#customerUrlBox').text(customerUrlBoxEdit);

        $("#editContractCustomerDetails").modal('hide');
        myAlert('s', 'Updated Successfully');
    }
</script>

<script>
    $(function() {
        $('#row_dim').hide(); 
        $('#customerID').change(function(){
            if($('#type').val() != '') {
                $('#row_dim').show(); 
            } else {
                $('#row_dim').hide(); 
            } 
        });
    });
</script>