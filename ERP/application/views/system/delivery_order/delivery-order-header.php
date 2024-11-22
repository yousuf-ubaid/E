<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_maraketing_transaction', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$masterID = $this->input->post('page_id');
$tittle = (empty($masterID))? $this->lang->line('sales_marketing_add_new_delivery_order'): $this->lang->line('sales_marketing_edit_delivery_order');

echo head_page($tittle, false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop_active();
$finance_year_arr = all_financeyear_drop(true);
//$customer_arr = all_customer_drop();
$pID = $this->input->post('page_id');
$customer_arr = all_customer_drop(true,1);
if($pID != '') {
    $contractAutoID = $pID;
    $Documentid = 'DO';
     $customeridcurrentdoc = all_customer_drop_isactive_inactive($pID,$Documentid);
    if(!empty($customeridcurrentdoc) && $customeridcurrentdoc['isActive'] == 0)
    {
        $customer_arr[trim($customeridcurrentdoc['customerAutoID'] ?? '')] = (trim($customeridcurrentdoc['customerSystemCode'] ?? '') ? trim($customeridcurrentdoc['customerSystemCode'] ?? '') . ' | ' : '') . trim($customeridcurrentdoc['customerName'] ?? '') . (trim($customeridcurrentdoc['customerCountry'] ?? '') ? ' | ' . trim($customeridcurrentdoc['customerCountry'] ?? '') : '');
    }
}
$projectExist = project_is_exist();
$finance_year_periodYN = getPolicyValues('FPC', 'All');
$customerCategory    = party_category(1);
$orderType = [
    'Direct' =>$this->lang->line('sales_marketing_delivery_direct') /*'Direct'*/,
    'Quotation' =>$this->lang->line('sales_markating_transaction_add_new_customer_quotation_based') /*'Quotation Based'*/,
    'Contract' =>$this->lang->line('sales_markating_transaction_add_new_customer_contract_based') /*'Contract Based'*/,
    'Sales Order' => $this->lang->line('sales_markating_transaction_add_new_customer_sales_order_based') /*'Sales Order Based'*/,
];

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
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

         <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_one');?> - <?php echo $this->lang->line('sales_marketing_order_header');?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_details();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_two');?> - <?php echo $this->lang->line('sales_marketing_order_details');?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_confirmation();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('sales_markating_transaction_step_three');?> - <?php echo $this->lang->line('sales_marketing_order_confirmation');?></span>
            </a>
        </div>
   
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="delivery_order_form" autocomplete="off"'); ?>
        <input type="hidden" id="customerCreditPeriodhn" name="customerCreditPeriodhn">
        <div class="row">
            <div class="form-group col-sm-4">
                <label> <?php echo $this->lang->line('sales_marketing_order_type');?> <?php required_mark(); ?></label>
                <?php echo form_dropdown('invoiceType', $orderType, 'Direct', 'class="form-control select2" onchange="validatenarration()" id="invoiceType" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></label><!--Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
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
                <label><?php echo $this->lang->line('sales_markating_transaction_document_reference');?> # </label><!--Reference-->
                <input type="text" name="referenceNo" id="referenceNo" class="form-control">
            </div>
        </div>
        <div class="row">

            <?php if($createmasterrecords==1){?>
                <div class="form-group col-sm-4">
                    <label for="customerName"><?php echo $this->lang->line('common_customer_name');?><?php  required_mark(); ?></label><!--Customer Name-->
                    <div class="input-group">
                        <div id="div_customer_drop">
                        <?php echo form_dropdown('customerID', $customer_arr_masterlevel, '', 'class="form-control select2" id="customerID"  onchange="Load_customer_currency(this.value)"'); ?>
                        </div>
                        <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Customer" rel="tooltip" onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                </div>
            <?php } else { ?>
                <div class="form-group col-sm-4">
                    <label for="customerID"><span id="party_text"><?php echo $this->lang->line('common_customer_name');?> </span> <?php required_mark(); ?></label><!--Customer Name-->
                    <?php echo form_dropdown('customerID',$customer_arr , '', 'class="form-control select2" id="customerID" onchange="Load_customer_currency(this.value)"'); ?>



                </div>
            <?php }?>


            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('sales_marketing_order_currency');?> <?php required_mark(); ?></label>
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="currency_validation(this.value,\'DO\')" id="transactionCurrencyID" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('sales_markating_transaction_sales_person');?></label><!--Sales person-->
                <?php echo form_dropdown('salesPersonID', all_srp_erp_sales_person_drop(),'','class="form-control select2" id="salesPersonID"'); ?>
            </div>
        </div>
        <div class="row">

            <?php
            if($finance_year_periodYN==1){
                ?>
                <div class="form-group col-sm-4">
                    <label for="finance_year"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year');?>  <?php required_mark(); ?></label><!--Financial Year-->
                    <?php echo form_dropdown('finance_year', $finance_year_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="finance_year" required onchange="fetch_finance_year_period(this.value)"'); ?>
                </div>
                <div class="form-group col-sm-4">
                    <label for="finance_period"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period');?> <?php required_mark(); ?></label><!--Financial Period-->
                    <?php echo form_dropdown('finance_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="finance_period" required'); ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="row">
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
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_narration');?> </label><!--Narration-->
                <textarea class="form-control" rows="3" name="invoiceNarration" id="invoiceNarration"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-12">
                <label><?php echo $this->lang->line('common_notes');?> </label><!--Notes-->
                <textarea class="form-control notes_termsandcond" rows="60" name="invoiceNote" id="invoiceNote"></textarea>
            </div>
        </div>
        <button class="btn btn-primary-new size-sm" type="button" onclick="open_all_notes('DO')"><i class="fa fa-bookmark" aria-hidden="true"></i></button>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">

    </div>
    <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title"><span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> <?php echo $this->lang->line('common_attachments');?> </h4>
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
                    <tbody id="delivery_order_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?> </td><!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default-new size-lg prev"><?php echo $this->lang->line('common_previous');?></button><!--Previous-->
            <button class="btn btn-primary-new size-lg " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?> </button><!--Save as Draft-->
            <button class="btn btn-success-new size-lg submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?></button><!--Confirm-->
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="all_item_edit_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 99%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_edit').' '.$this->lang->line('sales_markating_transaction_document_item_detail'); ?></h4>
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
                            <th style="width:80px;"><abbr title="Current Stock">Stock</abbr></th>
                            <th style="width:80px;"><abbr title="Current Stock">Park Qty</abbr></th>
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
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                <button class="btn btn-primary" type="button" onclick="save_notes()"><?php echo $this->lang->line('common_add_note'); ?><!--Add Note--></button>
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

    <div class="modal fade" id="access_denied_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:55%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" style="color: red;" id="title_generate_exceed">You cannot use this Item. This item has been pulled for following documents.</h5>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Document ID</th>
                        <th>Document Code</th>
                        <th>Reference No</th>
                        <th>Document Date</th>
                        <th>WareHouse</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                    </tr>
                    </thead>
                    <tbody id="access_denied_item_body">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">
    var orderAutoID = null;
    var invoiceType = null;
    var customerID = null;
    var currencyID = null;
    var EIdNo;
    var changeInvoiceDueDate = 0;

    $(document).ready(function () {
        
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

        EIdNo  = null;
        $('.headerclose').click(function(){
            fetchPage('system/delivery_order/delivery-order-master', orderAutoID, 'Delivery Order');
        });

        $('.select2').select2();

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


        finance_yearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;

        p_id = <?php echo json_encode(trim($masterID)); ?>;

        if (p_id) {
            orderAutoID = p_id;
            <?php if($createmasterrecords==1){?>
            fetch_customerdrop('',orderAutoID);
            <?php }?>
            get_order_header_details();
        }
        else {
            $('.btn-wizard').addClass('disabled');
            <?php if($createmasterrecords==1){?>
            fetch_customerdrop();
            <?php }?>
            CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
            currency_validation_modal(CurrencyID,'DO','','');
            load_default_note('DO');
            fetch_finance_year_period(finance_yearID,periodID);
        }

        $('#delivery_order_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}},/*Segment is required*/
                invoiceDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_date_is_required');?>.'}}},/*Document Date is required*/
                customerInvoiceDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_customer_date_is_required');?>.'}}},/*Customer Date is required*/
                InvoiceDueDate : {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_invoice_due_date_is_required');?>.'}}},/*Invoice Due Date is required*/
                //customerID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_customer_is_required');?>.'}}},/*Customer is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_currency_is_required');?>.'}}},/*Transaction Currency is required*/
                invoiceDueDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_invoice_due_date_is_required');?>.'}}},/*Invoice Due Date is required*/
                location: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_warehouse_location_is_required');?>.'}}},/*Warehouse Location is required*/
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();

            $("#invoiceType,#customerID,#transactionCurrencyID").prop("disabled", false);
            tinymce.triggerSave();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'orderAutoID', 'value': orderAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#finance_year option:selected').text()});
            data.push({'name': 'location_dec', 'value': $('#location option:selected').text()});
            data.push({'name' : 'currency_code', 'value' : $('#transactionCurrencyID option:selected').text()});
            data.push({'name' : 'salesPerson', 'value' : $('#salesPersonID option:selected').text()});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Delivery_order/save_delivery_order_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        orderAutoID = data['last_id'];
                        customerID = $('#supplier').val();
                        invoiceType = $('#invoiceType').prop("disabled", true).val();
                        currencyID = $('#transactionCurrencyID').prop("disabled", true).val();

                        $("#a_link").attr("href", "<?php echo site_url('Delivery_order/load_order_confirmation_view'); ?>/" + orderAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice');?>/" + orderAutoID + '/DO');

                        $('[href=#step2]').tab('show');
                        $('.btn-wizard').removeClass('disabled');
                        $('a[data-toggle="tab"]').removeClass('btn-primary').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default').addClass('btn-primary');
                        fetch_details();

                        $("#customerID").prop("disabled", true);

                    }

                },
                error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                }
            });
        });

    });

    function confirmation() {
        if (orderAutoID) {
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
                        data: {'orderAutoID': orderAutoID},
                        url: "<?php echo site_url('Delivery_order/order_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();

                            myAlert(data[0], data[1]);

                            if(data[0]=='e' && data[1]=='Below items are with negative wac amount'){
                                 
                                 $('#wac_minus_calculation_validation_body').empty();
                              x = 1;
                              if (jQuery.isEmptyObject(data[2])) {
                                  $('#wac_minus_calculation_validation_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                             } else {
                                 $.each(data[2], function (key, value) {
                        
                                  $('#wac_minus_calculation_validation_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>'+value['itemName']+'</td><td>' + value['Amount'] + '</td></tr>');
                                 x++;
                       
         
                             });
                             }
                                 $('#wac_minus_calculation_validation').modal('show');
                             }
                            else if(!$.isEmptyObject(data['in-suf-qty'])){
                                confirm_all_item_detail_modal(data['in-suf-items']);
                            }

                            else if(data[0] == 's'){
                                fetchPage('system/delivery_order/delivery-order-master', orderAutoID, 'Delivery Order');
                            }

                        }, error: function () {
                            myAlert('e', 'An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });
                }
            );
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

    function load_confirmation() {
        if (orderAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'orderAutoID': orderAutoID, 'html': true},
                url: "<?php echo site_url('Delivery_order/load_order_confirmation_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);

                    attachment_modal_delivery_order(orderAutoID, "<?php echo $this->lang->line('sales_markating_invoice');?>", "DO");/*Invoice*/
                    stopLoad();

                }, error: function () {
                    stopLoad();
                    myAlert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }
    }

    function fetch_detail(type, customerID, currencyID, tab) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'orderAutoID': orderAutoID,
                'invoiceType': type,
                'customerID': customerID,
                'currencyID': currencyID,
                'tab': tab
            },
            url: "<?php echo site_url('Delivery_order/fetch_delivery_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#step2').html(data);
                //check_detail_dataExist(orderAutoID);
                $('[href=#step2]').tab('show');
                $('a[data-toggle="tab"]').removeClass('btn-primary').addClass('btn-default');
                $('[href=#step2]').removeClass('btn-default').addClass('btn-primary');
                setTimeout(function () {
                    //tab_active(1);
                }, 300);
            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again') ?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function tab_active(id) {
        $(".nav-tabs a[href='#tab_" + id + "']").tab('show');
    }

    function check_detail_dataExist(orderAutoID){
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : {'orderAutoID':orderAutoID},
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
                    $("#addcustomer").prop("disabled", false);
                }else {
                    $("#invoiceType").prop("disabled", true);
                    $("#customerID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#addcustomer").prop("disabled", true);
                }
            },error : function(){
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID,'fyDepartmentID':'DO'},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                var mySelect = $('#finance_period');
                mySelect.empty();
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_select_financial_period');?>'));/*Select Financial Period*/
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                    });
                    if (select_value) {
                        $("#finance_period").val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function get_order_header_details() {
        if (orderAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'orderAutoID': orderAutoID},
                url: "<?php echo site_url('Delivery_order/get_order_header_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (jQuery.isEmptyObject(data['masterData'])) {
                        myAlert('e', '<b>Details not found</b>. Please refresh the page and try again.');
                        return false;
                    }

                    var masterData = data['masterData'];

                    invoiceType = masterData['DOType'];
                    customerID = masterData['customerID'];
                    currencyID = masterData['transactionCurrencyID'];
                    $("#a_link").attr("href", "<?php echo site_url('Delivery_order/load_order_confirmation_view'); ?>/" + orderAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + orderAutoID + '/CIN');
                    $('#finance_year').val(masterData['companyFinanceYearID']);


                    var financePeriods = data['financePeriods'];
                    var mySelect = $('#finance_period');
                    mySelect.empty();
                    mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_select_financial_period');?>'));
                    if (!jQuery.isEmptyObject(financePeriods)) {
                        $.each(financePeriods, function (val, text) {
                            mySelect.append($('<option></option>').val(text['companyFinancePeriodID']).html(text['dateFrom'] + ' - ' + text['dateTo']));
                        });
                    }
                    mySelect.val(masterData['companyFinancePeriodID']);


                    $('#invoiceDate').val(masterData['orderDate']);
                    $('#invoiceNarration').val(masterData['narration']);

                    setTimeout(function(){
                        if(masterData['note'])
                        {
                            tinyMCE.get("invoiceNote").setContent(masterData['note']);
                        }else
                        {
                            tinyMCE.get("invoiceNote").setContent('');
                        }
                    },300);


                    $('#invoiceType').val(invoiceType).change();
                    $('#referenceNo').val(masterData['referenceNo']);
                    $('#salesPersonID').val(masterData['salesPersonID']).change();
                    $('#contactPersonName').val(masterData['contactPersonName']);
                    $('#contactPersonNumber').val(masterData['contactPersonNumber']);

                    $('#customerID').val(masterData['customerID']);
                    var customer_data = data['customer_det'];
                    $("#customerCreditPeriodhn").val(customer_data['customerCreditPeriod']);
                    change_invoice_due_date();
                    if (currencyID) {
                        $("#transactionCurrencyID").val(currencyID).change()
                    } else {
                        if (customer_data['customerCurrencyID']) {
                            $("#transactionCurrencyID").val(customer_data['customerCurrencyID']).change();
                        }
                    }

                    fetch_detail(invoiceType, masterData['customerID'], masterData['transactionCurrencyID']);
                    $('#segment').val(masterData['segmentID'] + '|' + masterData['segmentCode']).change();

                    validatenarration();
                    $('[href=#step2]').tab('show');
                    $('a[data-toggle="tab"]').removeClass('btn-primary').addClass('btn-default');
                    $('[href=#step2]').removeClass('btn-default').addClass('btn-primary');
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }
    }

    function save_draft() {
        if (orderAutoID) {
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
                    fetchPage('system/delivery_order/delivery-order-master', orderAutoID, 'Delivery Order');
                }
            );
        }
    }

    function attachment_modal_delivery_order(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#delivery_order_attachment').empty().append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
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
        if(period>0 && changeInvoiceDueDate==0 && orderAutoID < 1){
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
            data: {'invoiceAutoID': orderAutoID},
            url: "<?php echo site_url('Delivery_order/fetch_direct_delivery_order_all_details'); ?>",
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
                }
                else {
                    $.each(data, function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" onchange="convertPrice_DO(this)" required')) ?>';
                        var wareHouseAutoID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ware_\'+key+\'"'), form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID"  onchange="checkitemavailable(this)" required')) ?>';
                        var taxfield = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="taxfield_\'+key+\'"'), form_dropdown('item_text[]', all_tax_drop(1), '', 'class="form-control item_text input-mini"  onchange="select_text_item(this)"')) ?>';

                        var string = '<tr><td> <input type="text" class="form-control search f_search" name="search[]" id="f_search_'+ x +'" placeholder="Item Id,Item Description" ';
                        string += 'value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" ';
                        string += 'class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]"> <input type="hidden" class="form-control invoiceDetailsAutoID" ';
                        string += 'name="invoiceDetailsAutoID[]" value="' + value['DODetailsAutoID'] + '"> </td> <td> '+ wareHouseAutoID +' </td>';
                        string += '<td><input class="hidden conversionRate_DO" id="conversionRate_DO" value="' + value['conversionRateUOM'] + '" name="conversionRate_DO"> '+ UOM +' </td> <td> <div class="input-group">';
                        string += '<input type="text" name="currentstock[]" value="' + value['currentStock'] + '" class="form-control currentstock" required disabled> </div> </td> <td><input type="text" ';
                        string += 'onfocus="this.select();" name="quantityRequested[]" onkeyup="checkCurrentStock(this)" value="' + value['requestedQty'] + '" placeholder="0.00" class="form-control number ';
                        string += 'input-mini quantityRequested" required></td> <td> <div class="input-group"> <input type="text" onfocus="this.select();" name="estimatedAmount[]" ';
                        string += 'value="' + value['unittransactionAmount'] + '" onkeyup="change_amount(this)" onkeypress="return validateFloatKeyPress(this,event)" placeholder="0.00" class="form-control ';
                        string += 'number estimatedAmount input-mini"> </div> </td> <td style="width: 100px;"> <div class="input-group"> <input type="text" name="discount[]" placeholder="0.00" ';
                        string += 'onkeyup="cal_discount(this)" onfocus="this.select();" value="' + value['discountPercentage'] + '" class="form-control number discount"> <span class="input-group-addon">%</span>';
                        string += '</div> </td> <td style="width: 100px;"> <input type="text" name="discount_amount[]" placeholder="0.00" value="' + value['discountAmount'] + '" onkeyup="cal_discount_amount(this)" ';
                        string += 'onfocus="this.select();" class="form-control number discount_amount"> </td> <td> '+ taxfield +' </td> <td style="width: 120px"> <div class="input-group"> <input type="text" ';
                        string += 'name="item_taxPercentage[]" placeholder="0.00" onfocus="this.select();" class="form-control number item_taxPercentage input-mini" id="item_taxPercentage_all_'+ x +'" ';
                        string += 'value="' + value['taxPercentage'] + '" value="0" readonly> <span class="input-group-addon input-group-addon-mini">%</span> </div> </td> <td> <textarea class="form-control ';
                        string += 'input-mini text-area-style" rows="1" name="remarks[]" placeholder="Item Remarks..." style="width: 115px">' + value['remarks'] + '</textarea> </td> <td class="remove-td">';
                        string += '<a onclick="delete_Delivery_order_DetailsEdit(' + value['DODetailsAutoID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" ';
                        string += 'class="glyphicon glyphicon-trash"></span></a></td></tr>';

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

    function load_default_note(docid){
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
                    setTimeout(function(){
                        tinyMCE.get("invoiceNote").setContent(data['description']);
                    },300);
                }else{
                    //myAlert('w','Default Note not set')
                }
            }
        });
    }

    function open_all_notes(docid){
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
                tinyMCE.get("invoiceNote").setContent('');
                tinyMCE.get("invoiceNote").setContent(data['description']);
                $("#all_notes_modal").modal('hide');
            }, error: function () {
                stopLoad();
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
      // data.push({'name' : 'customerAutoID', 'value' : $('#customerID option:selected').val()});
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


    function fetch_customerdrop(id,orderAutoID) {
        var customer_id;
        var page = '';
        
        if(orderAutoID)
        {
            page = orderAutoID
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
            data: {customer:customer_id,DocID:page,Documentid:'DO'},
            url: "<?php echo site_url('Invoices/fetch_customer_Dropdown_all'); ?>",
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

    function check_item_not_approved_in_document(itemAutoID,id,documentcode) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {itemAutoID: itemAutoID,'documentcode':documentcode,'DODetailsAutoID':invoiceDetailsAutoID},
            url: "<?php echo site_url('Inventory/check_item_not_approved_in_document_new'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#access_denied_item_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['usedDocs'])) {
                    $('#access_denied_item_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                } else {
                    $.each(data['usedDocs'], function (key, value) {
                        $('#access_denied_item_body').append('<tr><td>' + x + '</td><td>' + value['documentID'] + '</td><td><a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'' + value['documentID'] + '\',' + value['documentAutoID'] + ')">' + value['documentCode'] + '</a></td><td>' + value['referenceNo'] + '</td><td>' + value['documentDate'] + '</td><td>' + value['warehouse'] + '</td><td>' + value['Uom'] + '</td><td>' + value['stock'] + '</td></tr>');
                        x++;
                    });
                     
             /*    /*     $('#f_search_'+id).val('');
                    $('#f_search_'+id).closest('tr').find('.f_search').val('');
                    $('#f_search_'+id).closest('tr').find('.itemAutoID').val('');
                    $('#f_search_'+id).closest('tr').find('.wareHouseAutoID').val('');
                    $('#f_search_'+id).closest('tr').find('.umoDropdown').val('');
                    $('#f_search_'+id).closest('tr').find('.currentstock').val('');
                    $('#f_search_'+id).closest('tr').find('.quantityRequested').val('');
                    $('#f_search_'+id).closest('tr').find('.estimatedAmount').val(''); */ 
                    $('#access_denied_item').modal('show');
                }


            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }



</script>
<?php
