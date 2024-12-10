<?php
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
$segment_arr_default = default_segment_drop();
$umo_arr = all_umo_drop();
$location_arr = all_delivery_location_drop_active();
$financeyear_arr = all_financeyear_drop(true);
$customer_arr = all_customer_drop();
$projectExist = project_is_exist();
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
$defaultBankGl=load_default_bank();
?>
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
                <?php echo form_dropdown('invoiceType', array('' =>$this->lang->line('common_select_type') /*'Select Type'*/, 'Direct' =>$this->lang->line('sales_markating_transaction_add_new_customer_direct_invoice') /*'Direct Invoice'*/,'Quotation' =>$this->lang->line('sales_markating_transaction_add_new_customer_quotation_based') /*'Quotation Based'*/,'Contract' =>$this->lang->line('sales_markating_transaction_add_new_customer_contract_based') /*'Contract Based'*/,'Sales Order' => $this->lang->line('sales_markating_transaction_add_new_customer_sales_order_based') /*'Sales Order Based'*/,'Manufacturing' => 'Manufacturing' /*'Manufacturing'*/,'Insurance' => 'Insurance' /*'Manufacturing'*/), 'Insurance', 'class="form-control select2" onchange="validatenarration()" id="invoiceType" required'); ?>
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
            <?php if($createmasterrecords==1){?>
                <div class="form-group col-sm-4">
                    <label for="customerName"><?php echo $this->lang->line('common_customer_name');?><?php  required_mark(); ?></label><!--Customer Name-->
                    <div class="input-group">
                        <div id="div_customer_drop">
                            <?php echo form_dropdown('customerID', $customer_arr_masterlevel, '', 'class="form-control select2" id="customerID" onchange="Load_customer_currency(this.value)"'); ?>
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
                    <?php echo form_dropdown('customerID', $customer_arr, '', 'class="form-control select2" id="customerID" required onchange="Load_customer_currency(this.value)"'); ?>
                </div>
            <?php }?>


        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="transactionCurrencyID"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_currency');?> <?php required_mark(); ?></label><!--Invoice Currency-->
                <?php echo form_dropdown('transactionCurrencyID', $currency_arr, $this->common_data['company_data']['company_default_currencyID'], 'class="form-control select2" onchange="currency_validation(this.value,\'CINV\')" id="transactionCurrencyID" required'); ?>
            </div>
            <?php
            if($financeyearperiodYN==1){
                ?>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year');?>  <?php required_mark(); ?></label><!--Financial Year-->
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period');?> <?php required_mark(); ?></label><!--Financial Period-->
                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
                <?php
            }
            ?>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="RVbankCode"><?php echo $this->lang->line('sales_markating_transaction_add_new_customer_remittance_details');?> </label><!--Remittance Details-->
                <?php echo form_dropdown('RVbankCode', company_bank_account_drop(), $defaultBankGl, 'class="form-control select2" id="RVbankCode" '); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="contactPersonName"><?php echo $this->lang->line('sales_markating_transaction_document_contact_person_name');?> </label><!--Contact Person Name-->
                <input type="text" class="form-control " id="contactPersonName" name="contactPersonName">
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_telephone');?> <?php echo $this->lang->line('common_number');?> </label><!--Telephone Number--> <?php required_mark(); ?>
                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                    <input type="text" class="form-control " id="contactPersonNumber" name="contactPersonNumber">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_type');?> <?php required_mark(); ?></label><!--Type-->
                <?php echo form_dropdown('isPrintDN', array('0' => $this->lang->line('sales_markating_transaction_add_new_customer_print_invoice_only')/*'Print Invoice only'*/, '1' =>$this->lang->line('sales_markating_transaction_add_new_customer_print_invoice_and_delivery_note') /*'Print Invoice & Delivery note'*/), 0, 'class="form-control select2" id="isPrintDN" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('sales_markating_transaction_sales_person');?></label><!--Sales person-->
                <?php echo form_dropdown('salesPersonID', all_srp_erp_sales_person_drop(),'','class="form-control select2" id="salesPersonID"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('sales_markating_transaction_document_narration');?> </label><!--Narration-->
                <textarea class="form-control" rows="3" name="invoiceNarration" id="invoiceNarration"></textarea>
            </div>
        </div>
        <div class="row insurancetyp ">
            <div class="form-group col-sm-4 ">
                <label for=""><?php echo $this->lang->line('sales_marketing_insurance_type');?> <!--Insurance Type--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('insurancetypeid', all_insurance_type_drop(),'','class="form-control select2" onchange="load_sub_type()" id="insurancetypeid"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_sub_type');?> <!--Sub Type--> <?php required_mark(); ?></label>
                <select name="insuranceSubTypeID" id="insuranceSubTypeID" class="form-control searchbox" onchange="load_policy_enddate()">
                    <option value="">Select Type</option>
                </select>
            </div>

            <div class="form-group col-sm-2">
                <label><?php echo $this->lang->line('sales_marketing_policy_start_date');?> <!--Policy Start Date--> <?php required_mark(); ?></label>
                <div class="input-group policydatestart">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="policyStartDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  value="<?php echo $current_date; ?>" id="policyStartDate"
                           class="form-control invdat">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for=""><?php echo $this->lang->line('sales_marketing_policy_end_date');?> <!--Policy End Date--> <?php required_mark(); ?></label>
                <div class="input-group policydateend">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="policyEndDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="" id="policyEndDate" class="form-control invdat">
                </div>
            </div>

        </div>
        <div class="row">
            <div class="form-group col-sm-12">
                <label><?php echo $this->lang->line('common_notes');?> </label><!--Notes-->
                <textarea class="form-control" rows="6" name="invoiceNote" id="invoiceNote"></textarea>
            </div>
        </div>
        <button class="btn btn-primary-new size-sm" type="button" onclick="open_all_notes('CINV')"><i class="fa fa-bookmark" aria-hidden="true"></i></button>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary-new size-lg" type="submit"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
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
                            <th class="hideTaxpolicy" colspan="2"><?php echo $this->lang->line('sales_markating_transaction_tax'); ?>  </th>
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
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script type="text/javascript">
    var invoiceAutoID;
    var invoiceType;
    var customerID;
    var currencyID;
    var changeInvoiceDueDate = 0;
    $(document).ready(function () {
        <?php if($createmasterrecords==1){?>
        fetch_customerdrop()
        <?php }?>
        $('.headerclose').click(function(){
            fetchPage('system/invoices/invoice_insurance',invoiceAutoID,'Customer Invoices');
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

        $('.policydatestart').datetimepicker({
            useCurrent: false,
            format: date_format_policy
        });
        $('.policydatestart').on('dp.change', function(e){ load_policy_enddate(); });

        $('.policydateend').datetimepicker({
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
            currency_validation_modal(CurrencyID,'CINV','','');
            $("#invoiceNote").wysihtml5();
            load_default_note('CINV');
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
                //customerID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_customer_is_required');?>.'}}},/*Customer is required*/
                transactionCurrencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_currency_is_required');?>.'}}},/*Transaction Currency is required*/
                //financeyear: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_year_is_required');?>.'}}},/*Financial Year is required*/
                //financeyear_period: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_financial_period_is_required');?>.'}}},/*Financial Period is required*/
                invoiceDueDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_invoice_due_date_is_required');?>.'}}},/*Invoice Due Date is required*/
                location: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_warehouse_location_is_required');?>.'}}},/*Warehouse Location is required*/
                //invoiceNarration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_transaction_add_new_customer_invoice_narration_is_required');?>.'}}}/*Invoice Narration is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#invoiceType").prop("disabled", false);
            $("#insurancetypeid").prop("disabled", false);
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
                url: "<?php echo site_url('Invoices/save_invoice_header_insurance'); ?>",
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
                        $("#a_link").attr("href", "<?php echo site_url('Invoices/load_invoices_conformation_invoicetype'); ?>/" + invoiceAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice');?>/" + invoiceAutoID + '/CINV');
                        $('[href=#step2]').tab('show');
                        $('.btn-wizard').removeClass('disabled');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        fetch_details();
                        $("#invoiceType").prop("disabled", true);
                        $("#insurancetypeid").prop("disabled", true);
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
                        url: "<?php echo site_url('Invoices/invoice_confirmation'); ?>",
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
                                    fetchPage('system/invoices/invoice_insurance', invoiceAutoID, 'Invoices');
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
                url: "<?php echo site_url('Invoices/load_invoices_conformation_invoicetype'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Invoices/load_invoices_conformation_invoicetype'); ?>/" + invoiceAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice');?>/" + invoiceAutoID + '/CINV');
                    attachment_modal_customerInvoice(invoiceAutoID, "<?php echo $this->lang->line('sales_markating_invoice');?>", "CINV");/*Invoice*/
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
            url: "<?php echo site_url('Invoices/fetch_detail'); ?>",
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
                    $("#insurancetypeid").prop("disabled", false);
                    $("#customerID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                }else {
                    $("#invoiceType").prop("disabled", true);
                    $("#insurancetypeid").prop("disabled", true);
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
                url: "<?php echo site_url('Invoices/load_invoice_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        invoiceAutoID = data['invoiceAutoID'];
                        invoiceType = data['invoiceType'];
                        customerID = data['customerID'];
                        currencyID = data['transactionCurrencyID'];
                        $("#a_link").attr("href", "<?php echo site_url('Invoices/load_invoices_conformation_invoicetype'); ?>/" + invoiceAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + invoiceAutoID + '/CIN');
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
                        $('#referenceNo').val(data['referenceNo']);
                        $('#customerID').val(data['customerID']).change();
                        $('#salesPersonID').val(data['salesPersonID']).change();
                        $('#contactPersonName').val(data['contactPersonName']);
                        $('#contactPersonNumber').val(data['contactPersonNumber']);
                        $('#RVbankCode').val(data['bankGLAutoID']);
                        $('#insurancetypeid').val(data['insuranceTypeID']).change();
                        $('#insuranceSubTypeID').val(data['insuranceSubTypeID']);
                        $('#policyStartDate').val(data['policyStartDate']);
                        $('#policyEndDate').val(data['policyEndDate']);
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
                    fetchPage('system/invoices/invoice_insurance', invoiceAutoID, 'Invoices');
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
                            attachment_modal_customerInvoice(DocumentSystemCode, "Invoice", "CINV");
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

        if(invoiceType=='Insurance'){
            $('.insurancetyp').removeClass('hidden');
        }else{
            $('.insurancetyp').addClass('hidden');
        }
    }

    function confirm_all_item_detail_modal(itemAutoIdArr){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'invoiceAutoID': invoiceAutoID},
            url: "<?php echo site_url('Invoices/fetch_customer_invoice_all_detail_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                var descm = 2;
                var taxTotal= 0;
                $('#edit_item_table_body').empty();
                var x = 2;
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                } else {
                    $.each(data, function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown input-mini" disabled  required')) ?>';
                        var wareHouseAutoID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ware_\'+key+\'"'), form_dropdown('wareHouseAutoID[]', all_delivery_location_drop_active(), '', 'class="form-control select2 input-mini wareHouseAutoID"  onchange="checkitemavailable(this)" required')) ?>';
                        var taxfield = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="taxfield_\'+key+\'"'), form_dropdown('item_text[]', all_tax_drop(1), '', 'class="form-control item_text input-mini"  onchange="select_text_item(this)"')) ?>';

                        var string = '<tr><td> <input type="text" class="form-control search f_search" name="search[]" id="f_search_'+ x +'" placeholder="Item Id,Item Description" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" onkeydown="remove_item_all_description(event,this)"> <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '" name="itemAutoID[]"> <input type="hidden" class="form-control invoiceDetailsAutoID" name="invoiceDetailsAutoID[]" value="' + value['invoiceDetailsAutoID'] + '"> </td> <td> '+ wareHouseAutoID +' </td> <td> '+ UOM +' </td> <td> <div class="input-group"> <input type="text" name="currentstock[]" value="' + value['currentStock'] + '" class="form-control currentstock" required disabled> </div> </td> <td><input type="text" onfocus="this.select();" name="quantityRequested[]" onkeyup="checkCurrentStock(this)" value="' + value['requestedQty'] + '" placeholder="0.00" class="form-control number input-mini quantityRequested" required></td> <td> <div class="input-group"> <input type="text" onfocus="this.select();" name="estimatedAmount[]" value="' + value['unittransactionAmount'] + '" onkeyup="change_amount(this)" onkeypress="return validateFloatKeyPress(this,event)" placeholder="0.00" class="form-control number estimatedAmount input-mini"> </div> </td> <td style="width: 100px;"> <div class="input-group"> <input type="text" name="discount[]" placeholder="0.00" onkeyup="cal_discount(this)" onfocus="this.select();" value="' + value['discountPercentage'] + '" class="form-control number discount"> <span class="input-group-addon">%</span></div> </td> <td style="width: 100px;"> <input type="text" name="discount_amount[]" placeholder="0.00" value="' + value['discountAmount'] + '" onkeyup="cal_discount_amount(this)" onfocus="this.select();" class="form-control number discount_amount"> </td> <td class="taxshowYN" >'+ taxfield +' </td> <td class="taxshowYN" style="width: 120px"> <div class="input-group"> <input type="text" name="item_taxPercentage[]" placeholder="0.00" onfocus="this.select();" class="form-control number item_taxPercentage input-mini" id="item_taxPercentage_all_'+ x +'" value="' + value['taxPercentage'] + '" value="0" readonly> <span class="input-group-addon input-group-addon-mini">%</span> </div> </td> <td> <textarea class="form-control input-mini" rows="1" name="remarks[]" placeholder="Item Remarks...">' + value['remarks'] + '</textarea> </td> <td class="remove-td"><a onclick="delete_customer_invoiceDetailsEdit(' + value['invoiceDetailsAutoID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';


                        $('#edit_item_table_body').append(string);
                        //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                        $('#ware_'+key).val(value['wareHouseAutoID']).change();
                        $('#taxfield_'+key).val(value['taxMasterAutoID']);
                        if (data['taxPercentage'] != 0) {
                            $('#item_taxPercentage_all_'+key).prop('readonly', false);
                        } else {
                            $('#item_taxPercentage_all_'+key).prop('readonly', true);
                        }
                        taxTotal+= parseFloat(value['taxAmount']);
                        fetch_related_uom_id( value['defaultUOMID'],value['unitOfMeasureID'],$('#uom_'+key));
                        initializeitemTypeahead(x);
                        x++;
                    });
                    if(taxTotal > 0 ){
                        var taxexist= 1;
                    }else{
                        var taxexist= 0;
                    }
                    if(taxYN == 0 && taxexist !=1)
                    {
                        $('.hideTaxpolicy').addClass('hide');
                        $('.taxshowYN').addClass('hide');
                    }else{
                        $('.hideTaxpolicy').removeClass('hide');
                        $('.taxshowYN').removeClass('hide');
                    }
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
                    $('#invoiceNote ~ iframe').contents().find('.wysihtml5-editor').html(data['description']);
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
                $('#invoiceNote ~ iframe').contents().find('.wysihtml5-editor').html('');
                $('#invoiceNote ~ iframe').contents().find('.wysihtml5-editor').html(data['description']);
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
                        fetch_customerdrop(data['last_id']);
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


    function fetch_customerdrop(id) {
        var customer_id;
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
            data: {customer:customer_id},
            url: "<?php echo site_url('Invoices/fetch_customer_Dropdown_all_insurance'); ?>",
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
            data: {customer: customerid},
            url: "<?php echo site_url('Invoices/fetch_customer_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#contactPersonNumber').val(data['customerTelephone']);
                $("#transactionCurrencyID").val(data['customerCurrencyID']).change();

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function load_sub_type(select_val) {
        $('#insuranceSubTypeID').val("");
        $('#insuranceSubTypeID option').remove();

        var insuranceTypeID = $('#insurancetypeid').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Invoices/load_sub_type"); ?>',
            dataType: 'json',
            data: {'insuranceTypeID': insuranceTypeID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#insuranceSubTypeID').empty();
                    var mySelect = $('#insuranceSubTypeID');
                    mySelect.append($('<option></option>').val('').html('Select Sub Type'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['insuranceTypeID']).html(text['insuranceType']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function load_policy_enddate(){
        var insuranceTypeID = $('#insuranceSubTypeID').val();
        var policyStartDate = $('#policyStartDate').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Invoices/get_sub_type_months"); ?>',
            dataType: 'json',
            data: {'insuranceTypeID': insuranceTypeID,'policyStartDate': policyStartDate},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    if(data!=0){
                        $('#policyEndDate').val(data)
                    }

                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

</script>