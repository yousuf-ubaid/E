<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$currency_arr = all_currency_new_drop();
$pID = $this->input->post('page_id');
$customer_arr = all_customer_drop(true,1);
if($pID != 'CN') {
    $Documentid = '';
    $customeridcurrentdoc = all_customer_drop_isactive_inactive($pID,$Documentid);
    if(!empty($customeridcurrentdoc) && $customeridcurrentdoc['isActive'] == 0)
    {
        $customer_arr[trim($customeridcurrentdoc['customerAutoID'] ?? '')] = (trim($customeridcurrentdoc['customerSystemCode'] ?? '') ? trim($customeridcurrentdoc['customerSystemCode'] ?? '') . ' | ' : '') . trim($customeridcurrentdoc['customerName'] ?? '') . (trim($customeridcurrentdoc['customerCountry'] ?? '') ? ' | ' . trim($customeridcurrentdoc['customerCountry'] ?? '') : '');
    }
}
$financeyear_arr = all_financeyear_drop(true);
$gl_code_arr = fetch_all_gl_codes();
$segment_arr = fetch_segment();
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
?>
<style>
    .boldtab{
        font-weight: bold;
        border-left-color: #ead8d8 !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/pagination/styles.css'); ?>" class="employee_master_styles">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('accounts_receivable_common_step_one');?><!--Step 1--> - <?php echo $this->lang->line('accounts_receivable_tr_cn_credit_note_header');?><!--CN Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_cn_details()" data-toggle="tab"><?php echo $this->lang->line('accounts_receivable_common_step_two');?><!--Step 2--> - <?php echo $this->lang->line('accounts_receivable_tr_cn_credit_note_detail');?><!--CN Detail--></a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab"><?php echo $this->lang->line('accounts_receivable_common_step_three');?><!--Step 3--> - <?php echo $this->lang->line('accounts_receivable_tr_cn_credit_note_confirmation');?><!--CN Confirmation--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="cn_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">

                <?php if($createmasterrecords==1){?>
                    <label for="customerName"><?php echo $this->lang->line('common_customer_name');?><?php  required_mark(); ?></label><!--Customer Name-->
                    <div class="input-group">
                        <div id="div_customer_drop">
                            <?php echo form_dropdown('customer', $customer_arr_masterlevel, '', 'class="form-control select2" id="customer" onchange="Load_customer_currency(this.value);"'); ?>
                        </div>
                        <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Customer" rel="tooltip" onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>
                <?php } else { ?>
                    <label for="customerID"><?php echo $this->lang->line('common_customer');?><!--Customer--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('customer', $customer_arr, '', 'class="form-control select2" id="customer" required onchange="Load_customer_currency(this.value)"'); ?>
                <?php }?>

            </div>
            <div class="form-group col-sm-4">
                <label for="customer_currencyID"><?php echo $this->lang->line('accounts_receivable_common_customer_currency');?><!--Customer Currency--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('customer_currencyID', $currency_arr, '', 'class="form-control select2" onchange="currency_validation(this.value,\'CN\')" id="customer_currencyID" required '); ?>
            </div>
            <div class="form-group col-sm-4" style="display: none;">
                <label for=""><?php echo $this->lang->line('accounts_receivable_common_exchange_rate');?><!--Exchange Rate--></label>
                <input type="number" step="any" class="form-control " id="exchangerate" name="exchangerate">
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_reference');?><!--Reference--> #</label>
                <input type="text" class="form-control " id="referenceno" name="referenceno">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_date');?><!--Date--></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="cnDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'" value="<?php echo $current_date; ?>" id="cnDate"
                           class="form-control" required>
                </div>
            </div>
            <?php
            if($financeyearperiodYN==1){
            ?>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_year');?><!--Financial Year--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('accounts_receivable_common_financial_period');?><!--Financial Period--> <?php required_mark(); //?></label>
                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_comments');?><!--Comments--></label>
                <textarea class="form-control" rows="3" id="comments" name="comments"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs pull-right" role="tablist">
                <li role="presentation">
                    <a href="#glCodeTab" aria-controls="glCodeTab" role="tab" data-toggle="tab" class="boldtab"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></a>
                </li>
                <li role="presentation" class="active">
                    <a href="#invoiceTab" aria-controls="invoiceTab" role="tab" data-toggle="tab" class="boldtab">Invoice </a>
                </li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane " id="glCodeTab">
                    <div class="row">
                        <div class="col-md-12" style="margin-top: 10px;">

                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th colspan="4"><?php echo $this->lang->line('common_gl_details');?><!--GL Details--></th>
                                <th class="taxColspan"><?php echo $this->lang->line('common_amount');?> <!--Amount--></th>
                                <th>
                                    <button type="button" onclick="dn_detail_GLCode_modal()"class="btn btn-primary btn-xs pull-right">
                                        <i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?><!--Add-->
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                                <th style="min-width: 30%"><?php echo $this->lang->line('common_gl_code_description');?><!--GL Code Description--></th>
                                <th style="min-width: 10%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                                <th class= "groupTaxPolicy" style="min-width: 10%"><?php echo $this->lang->line('common_amount');?><!--tax--></th>
                                <th class= "groupTaxPolicy" style="min-width: 10%"><?php echo $this->lang->line('common_tax');?><!--tax--></th>
                                <th style="min-width: 15%"><?php echo $this->lang->line('common_transaction');?><!--Transaction--> <span class="trcurrency">()</span></th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?> <!--Action--></th>
                            </tr>
                            </thead>
                            <tbody id="table_body_GLCode">
                            <tr class="danger">
                                <td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td>
                            </tr>
                            </tbody>
                            <tfoot id="table_tfoot_GLCode">

                            </tfoot>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane active" id="invoiceTab">

                    <div class="row">
                        <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('common_add_detail');?><!--Add Detail--> </h4><h4></h4>
                        </div>

                    </div>
                    <br>

                    <div class="table-responsive">
                        <table id="debit_note_detail_table" class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th colspan="5"><?php echo $this->lang->line('accounts_receivable_common_invoice_details');?><!--Invoice Details--></th>
                                <th> <?php echo $this->lang->line('common_amount');?><!--Amount--></th>
                                <th><button type="button" onclick="cn_detail_modal()" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?><!--Add-->
                                    </button></th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%"><?php echo $this->lang->line('accounts_receivable_common_invoice_code');?><!--Invoice Code--></th>
                                <th style="min-width: 10%"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--></th>
                                <th style="min-width: 30%"><?php echo $this->lang->line('common_gl_code_description');?><!--GL Code Description--></th>
                                <th style="min-width: 10%"><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                                <th style="min-width: 15%"><?php echo $this->lang->line('common_transaction');?><!--Transaction--> <span class="trcurrency">()</span></th>
                                <th style="min-width: 10%">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="table_body">
                            <tr class="danger">
                                <td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td>
                            </tr>
                            </tbody>
                            <tfoot id="table_tfoot">

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">

        </div>
    </div>
    <div id="step3" class="tab-pane">
        <!--   <div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank" href="<?php /*echo site_url('Double_entry/fetch_double_entry_credit_note/'); */ ?>"><span class="glyphicon glyphicon-random" aria-hidden="true"></span>  &nbsp;&nbsp;&nbsp;Account Review entries
                </a>
                <a class="btn btn-default btn-sm no-print pull-right" id="a_link" target="_blank" href="<?php /*echo site_url('Receivable/load_cn_conformation/'); */ ?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div><hr>-->
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="creditNote_attachment_label">Modal title</h4>
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
                        <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="creditNote_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="cn_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('accounts_receivable_common_add_item_detail');?><!--Add Item Detail--></h5>
            </div>
            <div id="div_invoice">


            </div>

            <div class="col-xs-12" style="padding-right: 5px;">
                <div class="pagination-content clearfix" id="emp-master-pagination" style="padding-top: 10px">
                    <p id="filterDisplay"></p>

                    <nav>
                        <ul class="list-inline" id="pagination-ul">

                        </ul>
                    </nav>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" onclick="save_debit_base_items()">Save changes</button>
            </div>

        </div>
    </div>
</div>




<?php
$gl_code_arr = fetch_all_gl_codes();
$gl_code_arr_new = dropdown_all_revenue_gl();
?>


<div aria-hidden="true" role="dialog" id="GLCode_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add GL Detail </h5>
            </div>
            <div class="modal-body">
                <form role="form" id="multi_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color"
                           id="supplier_invoice_detail_table">
                        <thead>
                        <tr>
                            <th style="width: 350px;"><?php echo $this->lang->line('common_gl_code');?><!--GL Code--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('common_segment');?><!--Segment--></th>
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project');?><!--Project--> </th>
                                <th>Project Category</th>
                                <th>Project Subcategory</th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_amount');?><!--Amount--> <?php required_mark(); ?></th>
                            <th class="groupTaxPolicy" colspan="2" style="width: 150px;"><?php echo $this->lang->line('common_tax');?><!--Tax--></th>
                            <th class="groupTaxPolicy" style="width: 150px;"><?php echo $this->lang->line('common_net_amount');?><!--Amount--> <?php required_mark(); ?></th>
                            <th style="width: 200px;"><?php echo $this->lang->line('common_description');?><!--Description--> <?php required_mark(); ?></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>

                                <?php echo form_dropdown('gl_code_array[]', $gl_code_arr_new, '', 'class="form-control select2" id="gl_code_array" required'); ?>
                                <!--<input type="text" name="gl_code[]" id="gl_code"  class="form-control" required>-->
                            </td>
                            <td>
                                <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment_gl" onchange="load_segmentBase_projectID_income(this)"'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div class="div_projectID_income">
                                        <select name="projectID"  class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('common_select_project');?><!--Select Project--></option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <?php echo form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category_gl(this, this.value)"'); ?>

                                </td>
                                <td>
                                    <?php echo form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                </td>
                            <?php } ?>
                            <td>
                                <input type="text" name="amount[]" id="amount" onfocus="this.select();" onchange="load_gl_line_tax_amount(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control amount number">
                            </td>
                            <td class="groupTaxPolicy">
                                <?php echo form_dropdown('gl_text[]', all_tax_formula_drop_groupByTax(1), '', 'class="form-control gl_text input-mini" id="" onchange="load_gl_line_tax_amount(this)"'); ?>
                            </td>
                            <td class="groupTaxPolicy">
                                <span class="gl_linetaxamnt pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                <input type="text" name="real_amount[]" id="real_amount" class="form-control real_amount number hidden">
                            </td>
                            <td class="groupTaxPolicy">
                                <input type="text" name="net_amount[]" id="net_amount" onfocus="this.select();" onchange="load_gl_line_tax_amount(this)" onkeypress="return validateFloatKeyPress(this,event)" class="form-control amount net_amount">
                            </td>
                            <td>
                                        <textarea class="form-control" rows="1" id="description"
                                                  name="description[]"></textarea>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><!--Close--><?php echo $this->lang->line('common_Close');?></button>
                <button class="btn btn-primary" type="button" onclick="creditNote_Details_GLCode()"><!--Save Changes--><?php echo $this->lang->line('common_save_change');?>
                </button>
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
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script type="text/javascript">
    var creditNoteMasterAutoID;
    var creditNoteDetailsID;
    var currencyID;
    var currency_decimal;
    var per_page = 500;
    var isGroupBasedTax = 0;
    $(document).ready(function () {

        $('.headerclose').click(function(){
            fetchPage('system/accounts_receivable/credit_note_management',creditNoteMasterAutoID,'Credit Note');
        });
        $('.select2').select2();
        creditNoteMasterAutoID = null;
        creditNoteDetailsID = null;
        currencyID = null;

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#cn_form').bootstrapValidator('revalidateField', 'cnDate');
        });
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            creditNoteMasterAutoID = p_id;
            <?php if($createmasterrecords==1){?>
                fetch_customerdrop('',creditNoteMasterAutoID);
            <?php }?>
            load_credit_note_header();
        } else {
            $('.btn-wizard').addClass('disabled');
            <?php if($createmasterrecords==1){?>
                fetch_customerdrop();
            <?php }?>
            CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
            currency_validation_modal(CurrencyID,'CN','','');
        }

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID,periodID);

        $('#cn_detail_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            //feedbackIcons   : { valid: 'glyphicon glyphicon-ok',invalid: 'glyphicon glyphicon-remove',validating: 'glyphicon glyphicon-refresh' },
            excluded: [':disabled'],
            fields: {
                gl_code: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_gl_code_is_required');?>.'}}},/*GL code is required*/
                amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_amount_is_required');?>.'}}},/*Amount is required*/
                segment_gl: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}},/*Segment is required*/
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'creditNoteMasterAutoID', 'value': creditNoteMasterAutoID});
            data.push({'name': 'creditNoteDetailsID', 'value': creditNoteDetailsID});
            data.push({'name': 'gl_code_des', 'value': $('#gl_code option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Receivable/save_cn_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    creditNoteDetailsID = null;
                    refreshNotifications(true);
                    stopLoad();
                    $('#cn_detail_modal').modal('hide');
                    if (data['status']) {
                        fetch_cn_details();
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });

        $('#cn_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                customer_currencyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_customer_currency_is_required');?>.'}}},/*customer Currency is required*/
              //  customer: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_common_customer_is_required');?>.'}}},/*customer is required*/
                //exchangerate            : {validators : {notEmpty:{message:'Exchange Rate is required.'}}},
                cnDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_date_is_required');?>.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#customer").prop("disabled", false);
            $("#customer_currencyID").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'creditNoteMasterAutoID', 'value': creditNoteMasterAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'customerDetails', 'value': $('#customer option:selected').text()});
            data.push({'name': 'currency_code', 'value': $('#customer_currencyID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Receivable/save_creditnote_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data_arr) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data_arr['status']) {
                        creditNoteMasterAutoID = data_arr['last_id'];
                        $('.btn-wizard').removeClass('disabled');
                        $("#a_link").attr("href", "<?php echo site_url('Receivable/load_cn_conformation'); ?>/" + creditNoteMasterAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + creditNoteMasterAutoID + '/CN');
                        $("#customer").prop("disabled", true);
                        $("#customer_currencyID").prop("disabled", true);
                        fetch_cn_details();
                        $('[href=#step2]').tab('show');
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

    function pagination(obj) {
        $('.employee-pagination').removeClass('paginationSelected');
        $(obj).addClass('paginationSelected');

        var data_pagination = $('.employee-pagination.paginationSelected').attr('data-emp-pagination');
        var uriSegment = (data_pagination == undefined) ? per_page : ((parseInt(data_pagination) - 1) * per_page);
        cn_detail_modal(data_pagination, uriSegment);
    }

    function load_credit_note_header() {
        if (creditNoteMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'creditNoteMasterAutoID': creditNoteMasterAutoID},
                url: "<?php echo site_url('Receivable/load_credit_note_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        currencyID = data['transactionCurrencyID'];
                        creditNoteMasterAutoID = data['creditNoteMasterAutoID'];
                        $("#a_link").attr("href", "<?php echo site_url('Receivable/load_cn_conformation'); ?>/" + creditNoteMasterAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + creditNoteMasterAutoID + '/CN');
                        $('#customer').val(data['customerID']).change();
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $('#exchangerate').val(data['transactionExchangeRate']);
                        $('#cnDate').val(data['creditNoteDate']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        $('#comments').val(data['comments']);
                        $('#referenceno').val(data['docRefNo']);
                        fetch_cn_details();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
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

    function fetch_cn_details() {
        if (creditNoteMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'creditNoteMasterAutoID': creditNoteMasterAutoID},
                url: "<?php echo site_url('Receivable/fetch_cn_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                    isGroupBasedTax = data['currency']['isGroupBasedTax'];
                    if(isGroupBasedTax == 1) {
                        $('.groupTaxPolicy').removeClass('hidden');
                        $('.taxColspan').attr('colspan', 3);
                    } else {
                        $('.groupTaxPolicy').addClass('hidden');
                    }

                    $('.trcurrency').html('( ' + data['currency']['transactionCurrency'] + ' )');
                    $('.locurrency').html('( ' + data['currency']['companyLocalCurrency'] + ' )');
                    $('.sucurrency').html('( ' + data['currency']['customerCurrency'] + ' )');
                    $('#table_body').empty();
                    $('#table_tfoot').empty();
                    $('#table_body_GLCode').empty();
                    $('#table_tfoot_GLCode').empty();
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                        if(isGroupBasedTax == 1) {
                            $('#table_body_GLCode').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                        } else {
                            $('#table_body_GLCode').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                        }
                        $("#customer").prop("disabled", false);
                        $("#customer_currencyID").prop("disabled", false);
                        $("#addcustomer").prop("disabled", false);
                        currencyID = null;
                    } else {
                        $("#customer").prop("disabled", true);
                        $("#customer_currencyID").prop("disabled", true);
                        $("#addcustomer").prop("disabled", true);
                        var trtot = 0;
                        var lotot = 0;
                        var suptot = 0;
                        var trtotGLCode = 0;
                        var footerspan = 4;
                        transaction_currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                        currency_decimal = data['currency']['transactionCurrencyDecimalPlaces'];
                        company_currency_decimal = data['currency']['companyLocalCurrencyDecimalPlaces'];
                        customer_currency_decimal = data['currency']['customerCurrencyDecimalPlaces'];
                        var x = 1;
                        var i = 1;
                        $.each(data['detail'], function (key, value) {


                            if (value['isFromInvoice'] == 1) {
                                $('#table_body').append('<tr><td>' + x + '</td><td>' + value['invoiceSystemCode'] + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + '</td><td>' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(transaction_currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_cn_detail(' + value['creditNoteDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                x++;
                                trtot += (parseFloat(value['transactionAmount']));

                            } else if (value['isFromInvoice'] == 0) {
                                var taxamount = 0;
                                var taxView = 0;
                                if(isGroupBasedTax == 1){ 
                                    footerspan = 6;
                                    taxamount =  value['taxAmount'];
                                    if(taxamount > 0) {
                                        taxView = '<td>'+ parseFloat(parseFloat(value['transactionAmount']) - parseFloat(value['taxAmount'])).formatMoney(transaction_currency_decimal, '.', ',') +'</td><td class="text-right"><a onclick="open_tax_dd(\'\',' + creditNoteMasterAutoID + ',\'CN\',' + currency_decimal +', '+ value['creditNoteDetailsID'] +', \'srp_erp_creditnotedetail\', \'creditNoteDetailsID\')">' + parseFloat(taxamount).formatMoney(currency_decimal, '.', ',') + '</a></td>';
                                    } else {
                                        taxView = '<td>'+ +'</td><td class="text-right">0.00</td>';
                                    }
                                }

                                $('#table_body_GLCode').append('<tr><td>' + i + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] +' : '+value['description']+ '</td><td>' + value['segmentCode'] + '</td>'+ taxView +'<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(transaction_currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_cn_detail(' + value['creditNoteDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                i++;
                                trtotGLCode += (parseFloat(value['transactionAmount']));
                            }



                        });
                        $('#table_tfoot').append('<tr><td colspan="5" style="text-align: right;"><?php echo $this->lang->line('common_total');?><!--Total--> </td><td class="text-right total">' + trtot.formatMoney(transaction_currency_decimal, '.', ',') + '</td></tr>');
                        $('#table_body_GLCode').append('<tr><td colspan="'+ footerspan +'" style="text-align: right;"><?php echo $this->lang->line('common_total');?><!--Total--> </td><td class="text-right total">' + trtotGLCode.formatMoney(transaction_currency_decimal, '.', ',') + '</td></tr>');
                    }

                    number_validation();
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        };
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID,'fyDepartmentID':'CN'},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('accounts_receivable_common_select_financial_period');?>'));/*Select  Financial Period*/
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

    function currency_validation(CurrencyID,documentID){
        if (CurrencyID) {
            partyAutoID = $('#customer').val();
            currency_validation_modal(CurrencyID,documentID,partyAutoID,'CUS');
        }
    }

    function cn_detail_modal(data_pagination, uriSegment = 0) {
        if (creditNoteMasterAutoID) {
            $('.currency').html('( ' + $('#supplier_currency').val() + ' )');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'pageID':data_pagination,'creditNoteMasterAutoID': creditNoteMasterAutoID},
                url: "<?php echo site_url('Receivable/fetch_custemer_data_invoice'); ?>/" + uriSegment,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_invoice').html(data['view']);
                   $('#pagination-ul').html(data.pagination);
                   // $('#filterDisplay').html(data.filterDisplay);
                    $("#cn_detail_modal").modal({backdrop: "static"});
                    $(".select2").select2();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }




    }


    function edit_cn_header(id) {
        if (creditNoteMasterAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('accounts_receivable_common_you_want_to_edit_this_file');?>",/*You want to edit this file!*/
                    type: "warning",
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
                        data: {'creditNoteDetailsID': id},
                        url: "<?php echo site_url('Receivable/fetch_cn_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            creditNoteDetailsID = data['creditNoteDetailsID'];

                            $('#gl_code').val(data['GLCode']);
                            $('#amount').val(data['transactionAmount']);
                            $('#description').val(data['description']);
                            $('#segment_gl').val(data['segmentID'] + '|' + data['segmentCode']);
                            $("#cn_detail_modal").modal({backdrop: "static"});
                            stopLoad();
                            //refreshNotifications(true);
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
        ;
    }


    function delete_cn_detail(id) {
        if (creditNoteMasterAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('accounts_receivable_common_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
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
                        data: {'creditNoteDetailsID': id},
                        url: "<?php echo site_url('Receivable/delete_cn_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            fetch_cn_details();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function load_conformation() {
        if (creditNoteMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'creditNoteMasterAutoID': creditNoteMasterAutoID, 'html': true},
                url: "<?php echo site_url('Receivable/load_cn_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Receivable/load_cn_conformation'); ?>/" + creditNoteMasterAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + creditNoteMasterAutoID + '/CN');
                    attachment_modal_creditNote(creditNoteMasterAutoID, "<?php echo $this->lang->line('accounts_receivable_ap_credit_note');?>", "CN");/*Credit Note*/
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
        if (creditNoteMasterAutoID) {
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
                        data: {'creditNoteMasterAutoID': creditNoteMasterAutoID},
                        url: "<?php echo site_url('Receivable/cn_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            if(data[0]=='s'){
                                fetchPage('system/accounts_receivable/credit_note_management',creditNoteMasterAutoID, 'Credit Note');
                                refreshNotifications(true);
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }

    function save_draft() {
        if (creditNoteMasterAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('accounts_receivable_common_you_want_to_save_this_file');?>",/*You want to save this file !*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                },
                function () {
                    fetchPage('system/accounts_receivable/credit_note_management', creditNoteMasterAutoID, 'Credit Note');
                });
        }
        ;
    }

    function attachment_modal_creditNote(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#creditNote_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!-- Attachments-->
                    $('#creditNote_attachment').empty();

                    $('#creditNote_attachment').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_creditNote_attachment(creditNoteMasterAutoID, DocumentSystemCode,myFileName) {
        if (creditNoteMasterAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>",/*You want to delete this attachment file !*/
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
                        data: {'attachmentID': creditNoteMasterAutoID,'myFileName': myFileName},
                        url: "<?php echo site_url('Receivable/delete_creditNote_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s','<?php echo $this->lang->line('common_deleted_successfully');?>');/*Deleted Successfully*/
                                attachment_modal_creditNote(DocumentSystemCode, "Credit Note", "CN");
                            }else{
                                myAlert('e','<?php echo $this->lang->line('common_deletion_failed');?>');/*Deletion Failed*/
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function dn_detail_GLCode_modal() {
        if (creditNoteMasterAutoID) {
            $('#multi_detail_form')[0].reset();
            $('#gl_code_array').val('').change();
            $('#supplier_invoice_detail_table tbody tr').not(':first').remove();
            $("#GLCode_detail_modal").modal({backdrop: "static"});

            if(isGroupBasedTax == 1) {
                $('.groupTaxPolicy').removeClass('hidden');
            } else {
                $('.groupTaxPolicy').addClass('hidden');
            }
        }
        $("#GLCode_detail_modal").modal;
    }

    function add_more() {
        $('select.select2').select2('destroy');
        var appendData = $('#supplier_invoice_detail_table tbody tr:first').clone();

        appendData.find('input').val('');
        appendData.find('textarea').val('');

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#supplier_invoice_detail_table').append(appendData);
        var lenght = $('#supplier_invoice_detail_table tbody tr').length - 1;

        $(".select2").select2();
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });



    function creditNote_Details_GLCode() {
        var $form = $('#multi_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'creditNoteMasterAutoID', 'value': creditNoteMasterAutoID});
        //data.push({'name': 'InvoiceDetailAutoID', 'value': InvoiceDetailAutoID});

        $('select[name="gl_code_array[]"] option:selected').each(function () {
            data.push({'name': 'gl_code_des[]', 'value': $(this).text()})
        });

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Receivable/save_crditNote_detail_GLCode_multiple'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    InvoiceDetailAutoID = null;
                    $('#multi_detail_form')[0].reset();
                    $("#segment_gl").select2("");
                    $("#gl_code_array").select2("");
                    fetch_cn_details();
                    $('#GLCode_detail_modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_segmentBase_projectID_income(segment) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_multiple"); ?>',
            dataType: 'html',
            data: {segment: segment.value},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(segment).closest('tr').find('.div_projectID_income').html(data);
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
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

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
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


    function fetch_customerdrop(id,creditNoteMasterAutoID) {
        var customer_id;
        var page = '';
        if(creditNoteMasterAutoID)
        {
            page = creditNoteMasterAutoID
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
            data: {customer:customer_id,DocID:page,Documentid:'CN'},
            url: "<?php echo site_url('Receivable/fetch_customer_Dropdown_all'); ?>",
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
                // $('#contactPersonNumber').val(data['customerTelephone']);
                $("#customer_currencyID").val(data['customerCurrencyID']).change();

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
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
                if(data!=null){
                    $(':input[type="submit"]').prop('disabled', false);
                    $("#customerCreditPeriodhn").val(data['customerCreditPeriod']);
                    //change_invoice_due_date();
                    if (currencyID) {
                        $("#customer_currencyID").val(currencyID).change()
                    } else {
                        if (data.customerCurrencyID) {
                            $("#customer_currencyID").val(data.customerCurrencyID).change();
                            //currency_validation_modal(data.customerCurrencyID, 'BSI', customerAutoID, 'SUP');
                        }
                    }
                }
            }
        });
    }

    function fetch_project_sub_category_gl(element,categoryID) {
        var projectID = $(element).closest('tr').find('.projectID').val();
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
                    /* if (projectsubcat) {
                        $("#project_subCategoryID_edit").val(projectsubcat).change();
                        $("#project_subCategoryID_edit1").val(projectsubcat).change();

                    }; */
                }
                $('.select2').select2();
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
                    // if (projectcategory) {
                    //     $("#project_categoryID_edit").val(projectcategory).change();
                    //     $("#project_categoryID_edit1").val(projectcategory).change();
                    // }
                }
                //$('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function load_gl_line_tax_amount(ths){
        var amount = $(ths).closest('tr').find('.amount').val();
        var real_amount = $(ths).closest('tr').find('.real_amount').val();
        var taxtype = $(ths).closest('tr').find('.gl_text').val();
        var discoun = 0;
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        if (real_amount == 0) {
            real_amount = amount;
        }
        var lintaxappamnt = amount;
        if (!jQuery.isEmptyObject(taxtype)) {           
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'creditNoteMasterAutoID': creditNoteMasterAutoID, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'discount':discoun},
                url: "<?php echo site_url('Receivable/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $(ths).closest('tr').find('.gl_linetaxamnt').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.net_amount').val((parseFloat(data)+parseFloat(lintaxappamnt)).toFixed(currency_decimal));
                    $(ths).closest('tr').find('.real_amount').val((parseFloat(real_amount)).toFixed(currency_decimal));
                
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $(ths).closest('tr').find('.gl_linetaxamnt').text('0');
            $(ths).closest('tr').find('.net_amount').val((parseFloat(lintaxappamnt)).toFixed(currency_decimal));
            $(ths).closest('tr').find('.real_amount').val((parseFloat(amount)).toFixed(currency_decimal));
        }
    }
</script>