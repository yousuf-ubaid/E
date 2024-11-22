<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .boldtab{
        font-weight: bold;
        border-left-color: #ead8d8 !important;
    }
    .form-group .select2-container {
        position: relative;
        z-index: 2;
        float: left;
        width: 150%;
        margin-bottom: 0;
        display: table;
        table-layout: fixed;
    }
</style>
<?php
$projectExist = project_is_exist();
$project = get_all_project_invoice();
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('sales_maraketing_masters_customer_master', $primaryLanguage);
$itemBatchPolicy = getPolicyValues('IB', 'All');
//$rebate = getPolicyValues('CRP', 'All');
$rebate = 0;
$this->lang->load('common', $primaryLanguage);
$projectExist = project_is_exist();
$umo_arr = array('' => 'Select UOM');
$stylewidth1='';
$stylewidth2='';
$stylewidth3='';
$stylewidth4='';
$stylewidth5='';
$stylewidth6=' ';
if($projectExist == 1)
{
    $stylewidth1='width: 12%';
    $stylewidth6='width: 18%';
    $stylewidth2='width: 10%';
    $stylewidth3='width: 10%';
    $stylewidth4='width: 6%';
    $stylewidth5='width: 6%;';
}
?>
<input type="hidden" id="rebetval" value="<?php echo $rebate; ?>">
<?php
switch ($RVType) {

    case "Direct": case "DirectItem": case "DirectIncome": ?>
        <div class="nav-tabs-custom">
        <ul class="nav nav-tabs pull-right">
            <?php if($RVType == 'Direct' || $RVType == 'DirectIncome' || $RVType == 'DirectItem') { ?>
                <li class="<?php if($RVType == 'Direct'){ echo 'active';}?>"><a data-toggle="tab" class="boldtab" href="#tab_1" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_receivable_common_income'); ?> GL<!--Income--></a></li>
            <?php } ?>
            <?php if($RVType == 'Direct' || $RVType == 'DirectItem') { ?>
                <li class="<?php if($RVType == 'DirectItem'){ echo 'active';}?>"><a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false">
                        <?php echo $this->lang->line('common_item'); ?><!--Item--></a></li>
            <?php } ?>
            <!-- <li><a data-toggle="tab" class="boldtab" href="#tab_4" aria-expanded="false">Advance</a></li> -->
            <li class="pull-left header"><i class="fa fa-hand-o-right"></i>
                <?php echo $this->lang->line('accounts_receivable_common_direct_receipt_for'); ?><!--Direct Receipt for-->
                :
                - <?php echo $master['customerName']; ?></li>
        </ul>
        <div class="tab-content">

            <?php if($RVType == 'Direct' || $RVType == 'DirectIncome' || $RVType == 'DirectItem') { ?>
                <div id="tab_1" class="tab-pane <?php if($RVType == 'Direct'){ echo 'active';}?>">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                            <?php if($group_based_tax == 1) {?> 
                                <th colspan="4"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } else { ?>
                                <th colspan="3"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } ?>
                            <th>
                                <button type="button" onclick="rv_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i
                                            class="fa fa-plus"> <?php echo $this->lang->line('accounts_receivable_common_add_gl'); ?></i>
                                    <!--Add GL-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                            <th style="min-width: 30%">
                                <?php echo $this->lang->line('common_gl_code_description'); ?><!--GL Code Description--></th>
                            <th style="min-width: 12%">
                                <?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                            <th style="min-width: 12%"><?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 12%"><?php echo $this->lang->line('common_discount'); ?><!--Discount --><span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <?php if($group_based_tax == 1) {?> 
                                <th style="min-width: 12%"><?php echo $this->lang->line('common_tax'); ?></th>
                            <?php } ?>
                            <th style="min-width: 12%"><?php echo $this->lang->line('common_total'); ?><!--Total--> <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>

                            <th style="min-width: 10%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <?php if($group_based_tax == 1) {?> 
                                <td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            <?php } else { ?>
                                <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            <?php } ?>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
            <?php } ?>
        <?php if($RVType == 'Direct' || $RVType == 'DirectItem') { ?>
                <!-- Items -->
                <div id="tab_2" class="tab-pane <?php if($RVType == 'DirectItem'){ echo 'active';}?>">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="6">
                                <?php echo $this->lang->line('accounts_receivable_common_item_details'); ?><!--Item Details--></th>
                            <?php if($group_based_tax == 1) { ?>
                                <th colspan="3"><?php echo $this->lang->line('common_price'); ?><!--Price--> <span class="currency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <?php } else { ?>
                                <th colspan="2"><?php echo $this->lang->line('common_price'); ?><!--Price--> <span class="currency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <?php } ?>
                            <th>
                                <button type="button" onclick="rv_item_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                                </button>

                                <button type="button" onclick="edit_all_item_detail_modal()" style="margin-right:8px;"
                                        id="editallbtn" class="btn  btn-xs btn-default hidden pull-right"><span
                                            class="glyphicon glyphicon-pencil"></span><?php echo $this->lang->line('common_document_edit_all'); ?><!--Edit All-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                            <?php if ($itemBatchPolicy == 1) { ?>
                                <th>Batch Number</th>
                            <?php } ?>
                            <th style="min-width: 36%" class="text-left">
                                <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th class='theadtr' style="min-width: 45%"><?php echo $this->lang->line('common_remarks');?><!--Remarks--></th>
                            <th style="min-width: 7%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                            <th style="min-width: 7%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                            <?php if($group_based_tax == 1) { ?>
                                <th style="min-width: 10%"><?php echo $this->lang->line('common_tax'); ?><!--Unit--></th>
                            <?php } ?>
                            <th style="min-width: 15%"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                        </tr>
                        </thead>
                        <tbody id="item_table_body">
                        <tr class="danger">
                            <?php if($group_based_tax == 1) { ?>
                                <td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
                            <?php } else { ?>
                                <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
                            <?php } ?>
                        </tr>
                        </tbody>
                        <tfoot id="item_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
            <?php } ?>

            <div id="tab_4" class="tab-pane">
                <table class="table table-bordered table-striped table-condesed">
                    <thead>
                    <tr>
                        <?php if($group_based_tax == 1) {?>
                            <th colspan="3"><?php echo $this->lang->line('accounts_receivable_tr_advance_details'); ?><!--Advance Details--></th>
                            <th colspan="2"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                        <?php } else { ?>
                            <th colspan="2"><?php echo $this->lang->line('accounts_receivable_tr_advance_details'); ?><!--Advance Details--></th>
                            <th> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                        <?php } ?>
                        <th>
                            <button type="button" onclick="rv_advance_detail_modal()"
                                    class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                <?php echo $this->lang->line('accounts_receivable_tr_add_advance'); ?><!--Add Advance-->
                            </button>
                        </th>
                    </tr>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 40%">
                            <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <?php if($group_based_tax == 1) {?>
                            <th style="min-width: 10%">Quotation Code</th>
                            <th style="min-width: 15%"><?php echo $this->lang->line('common_tax'); ?><!--Tax--> <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                        <?php } ?>
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                        <!-- <th style="min-width: 15%">Local <span
                                    class="locurrency">(<?php //echo $master['companyLocalCurrency']; ?>)</span></th>
                            <th style="min-width: 15%">Customer <span
                                    class="sucurrency">(<?php //echo $master['customerCurrency']; ?>)</span></th> -->
                        <th style="min-width: 10%">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="advance_table_body">
                    <tr class="danger">
                        <td colspan="6" class="text-center"><b>
                                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot id="advance_table_tfoot">

                    </tfoot>
                </table>
            </div><!-- /.tab-pane -->
        </div><!-- /.tab-content -->
        <?php if($group_based_tax != 1) { ?>
            <br>
            <div class="row">
                <div class="col-md-5">
                    <label for="exampleInputName2" id="tax_tot">
                        <?php echo $this->lang->line('accounts_receivable_common_tax_for'); ?><!--Tax for--> </label>
                    <form class="form-inline" id="tax_form">
                        <div class="form-group">
                            <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this)" style="width: 150px;"'); ?>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control number" id="percentage" name="percentage"
                                    style="width: 80px;" onkeyup="cal_tax(this.value)">
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                style="width: 100px;" onkeyup="cal_tax_amount(this.value)"
                                onkeypress="return validateFloatKeyPress(this,event)">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                    </form>
                </div>
                <div class="col-md-7">
                    <table class="<?php echo table_class(); ?>">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>
                                <?php echo $this->lang->line('accounts_receivable_common_tax_type'); ?><!--Tax Type--></th>
                            <th><?php echo $this->lang->line('accounts_receivable_common_detail'); ?><!--Detail--></th>
                            <th><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                            <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span class="currency">(LKR)</span>
                            </th>
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
        <?php } ?>
        <br>
    </div>
    <hr>
    <!--<div class="text-right m-t-xs">
        <button class="btn btn-default prev" onclick="">Previous</button>
    </div>-->
    <div aria-hidden="true" role="dialog" id="rv_detail_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog" style="width:90%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title"></h5>
                    <?php echo $this->lang->line('accounts_receivable_tr_add_gl_code'); ?><!--Add GL Code--></h5>
                </div>
                <form role="form" id="rv_detail_form" class="form-horizontal">
                    <div class="modal-body">
                        <table class="table table-bordered table-condensed no-color" id="income_add_table">
                            <thead>
                            <tr>
                                <th style="width: 380px">
                                    <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></th>
                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--></th>
                                    <th>Project Category</th>
                                    <th>Project Subcategory</th>
                                <?php } ?>
                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span><?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('common_discount_percentagae'); ?><!--Discount Percentage--></th>
                                <th><?php echo $this->lang->line('common_discount_amount'); ?><!--Discount Amount--></th>
                                <?php if($group_based_tax == 1) {?>
                                    <th colspan="2"><?php echo $this->lang->line('common_tax'); ?></th>
                                <?php } ?>
                                <th><?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span><?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs"
                                            onclick="add_more_income()">
                                        <i
                                                class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php echo form_dropdown('gl_code[]', $gl_code_arr, '', 'class="form-control select2" required'); ?>
                                </td>
                                <td>
                                    <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment_glAdd" onchange="load_segmentBase_projectID_income(this)"'); ?>
                                </td>
                                <?php if ($projectExist == 1) { ?>
                                    <td class="form-group" style="<?php echo $stylewidth1?>">
                                        <div class="div_projectID_income">
                                            <select name="projectID" class="form-control select2">
                                                <option value="">
                                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="form-group" style="<?php echo $stylewidth2?>">
                                        <?php echo form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                    </td>
                                    <td class="form-group" style="<?php echo $stylewidth2?>">
                                        <?php echo form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                    </td>
                                <?php } ?>
                                <td>
                                    <input type="text" name="amount[]" onchange="load_gl_line_tax_amount(this)"
                                           onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'amount')" value="00"
                                           class="form-control number amount">
                                </td>
                                <td><input type="text" name="discountPercentage[]" onkeypress="return validateFloatKeyPress(this,event)" onchange="load_gl_line_tax_amount(this)" onkeyup="calculateNetAmount(this,'discountPercentage')" value="00" class="form-control number discountPercentage"></td>
                                <td><input type="text" name="discountAmount[]" onkeypress="return validateFloatKeyPress(this,event)" onchange="load_gl_line_tax_amount(this)" onkeyup="calculateNetAmount(this,'discountAmount')" value="00" class="form-control number discountAmount"></td>
                                <?php if($group_based_tax == 1) { ?>
                                    <td>
                                        <?php echo form_dropdown('gl_text[]', all_tax_formula_drop_groupByTax(1), '', 'class="form-control gl_text input-mini" id="" onchange="load_gl_line_tax_amount(this)"'); ?>
                                    </td>
                                    <td><span class="gl_linetaxamnt pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <?php } ?>
                                <td><input type="text" name="Netamount[]" value="00" class="form-control number Netnumber" readonly></td>
                                <td>
                                        <textarea class="form-control" rows="1"
                                                  name="description[]"></textarea>
                                </td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="saveDirectRvDetails()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="edit_rv_income_detail_modal" class="modal fade"
         style="display: none;">
        <div class="modal-dialog" style="width:90%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('accounts_receivable_tr_edit_gl_code'); ?><!--Edit GL Code--></h4>
                </div>
                <form role="form" id="edit_rv_income_detail_form" class="form-horizontal">
                    <div class="modal-body">
                        <table class="table table-bordered table-condensed no-color" id="income_edit_table">
                            <thead>
                            <tr>
                                <th style="width: 380px">
                                    <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></th>
                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                    <th>Project Category</th>
                                    <th>Project Subcategory</th>
                                <?php } ?>
                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span><?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('common_discount_percentagae'); ?><!--Discount Percentage--></th>
                                <th><?php echo $this->lang->line('common_discount_amount'); ?><!--Discount Amount--></th>
                                <?php if($group_based_tax == 1) {?>
                                    <th colspan="2"><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <?php } ?>
                                <th><?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span><?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="form-group" style="<?php echo $stylewidth6?>">
                                    <?php echo form_dropdown('gl_code', $gl_code_arr, '', 'class="form-control select2" id="edit_gl_code" required'); ?>
                                </td>
                                <td>
                                    <?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="edit_segment_gl" onchange="load_segmentBase_projectID_incomeEdit(this)"'); ?>
                                </td>
                                <?php if ($projectExist == 1) { ?>
                                    <td class="form-group" style="<?php echo $stylewidth1?>">
                                        <div id="edit_div_projectID_income">
                                            <select name="projectID" id="projectID" class="form-control select2">
                                                <option value="">
                                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="form-group" style="<?php echo $stylewidth2?>">
                                        <?php echo form_dropdown('project_categoryID',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                    </td>
                                    <td class="form-group" style="<?php echo $stylewidth2?>">
                                        <?php echo form_dropdown('project_subCategoryID',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit"'); ?>
                                    </td>
                                <?php } ?>
                                <td>
                                    <input type="text" name="amount" onchange="load_gl_line_tax_amount_edit(this)"
                                           onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'amount')" value="00"
                                           id="edit_amount"
                                           class="form-control number">
                                </td>
                                <td><input type="text" name="discountPercentage" id="discountPercentage_edit" onkeypress="return validateFloatKeyPress(this,event)" onchange="load_gl_line_tax_amount_edit(this)" onkeyup="calculateNetAmount_edit(this,'discountPercentage')" value="00" class="form-control number "></td>
                                <td><input type="text" name="discountAmount" id="discountAmount_edit" onkeypress="return validateFloatKeyPress(this,event)" onchange="load_gl_line_tax_amount_edit(this)" onkeyup="calculateNetAmount_edit(this,'discountAmount')" value="00" class="form-control number "></td>
                                <?php if ($group_based_tax== 1) { ?>
                                    <td><?php echo form_dropdown('gl_text_type', all_tax_formula_drop_groupByTax(1), '', 'class="form-control gl_text_type" style="width: 134px;" id="gl_text_type_edit" onchange="load_gl_line_tax_amount_edit(this)" '); ?></td>
                                    
                                    <td><span class="gl_linetaxamnt pull-right" id="gl_linetaxamnt_edit" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <?php } ?>
                                <td><input type="text" name="Netamount" id="Netamount_edit" value="00" class="form-control number " readonly></td>
                                <td>
                                        <textarea class="form-control" rows="1" name="description"
                                                  id="edit_description"></textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="updateDirectRvDetails()">
                            <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="rv_item_detail_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog modal-lg" style="width: 90%">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">
                        <?php echo $this->lang->line('accounts_receivable_common_add_item_detail'); ?><!--Add Item Detail--></h5>
                </div>
                <form role="form" id="rv_item_detail_form" class="form-horizontal">
                    <div class="modal-body">
                        <table class="table table-bordered table-condensed no-color" id="item_add_table">
                            <thead>
                            <tr>
                                <th>
                                    <?php echo $this->lang->line('accounts_receivable_common_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_warehouse'); ?><!--Ware House--> <?php required_mark(); ?></th>
                                <?php if ($itemBatchPolicy == 1) { ?>
                                   <th>Batch Number <?php required_mark(); ?></th>
                                <?php } ?>

                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                    <th>Project Category</th>
                                    <th>Project Subcategory</th>
                                <?php } ?>
                                <th>
                                    <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>

                                <th>
                                    <?php echo $this->lang->line('accounts_receivable_current_stock'); ?><!--Current Stock-->
                                </th>
                                <th style="width:80px;"><abbr title="Park Qty">Park Qty</abbr></th>

                                <th>
                                    <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>


                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                <?php if($group_based_tax == 1) { ?>
                                    <th colspan="2"><?php echo $this->lang->line('common_tax'); ?> </th>
                                <?php } ?>
                                            
                                <th style="width: 120px;">
                                    <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                    <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                                <th style="display: none;">
                                    <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                <th>
                                    <?php echo $this->lang->line('accounts_receivable_common_remarks'); ?><!--Remarks--></th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more_item()">
                                        <i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="text" onkeyup="clearitemAutoID(event,this)"
                                           class="form-control search f_search" name="search[]" id="f_search_1"
                                           placeholder="<?php echo $this->lang->line('common_item_id'); ?>, <?php echo $this->lang->line('common_item_description'); ?>...">
                                    <!--Item ID--><!--Item Description-->
                                    <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                    <input type="hidden" class="form-control itemcat" name="itemcat[]">
                                  
                                </td>
                                <td>
                                    <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop_active(), '', 'class="form-control select2 wareHouseAutoID" onchange="checkitemavailable(this)"  required'); ?>
                                </td>
                                <?php if($itemBatchPolicy == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('batch_number[0][]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_1" multiple="multiple" required'); ?>
                                        </td>
                                <?php } ?>
                                <?php if ($projectExist == 1) { ?>
                                    <td>
                                        <div class="div_projectID_item">
                                            <select name="projectID" class="form-control select2">
                                                <option value="">
                                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="form-group" style="<?php echo $stylewidth2?>">
                                        <?php echo form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                    </td>
                                    <td class="form-group" style="<?php echo $stylewidth2?>">
                                        <?php echo form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                    </td>
                                <?php } ?>
                                <td>
                                    <input class="hidden conversionRate" id="conversionRate" name="conversionRate">
                                    <?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown" onchange="convertPrice_RV(this)" required'); ?>
                                </td>

                                <td>

                                    <input type="text" name="currentstock"
                                           id="currentstock"
                                           class="form-control currentstock" required disabled>

                                    <input type="hidden" name="currentstock_pulleddoc"
                                           id="currentstock_pulleddoc"
                                           class="form-control currentstock_pulleddoc">

                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" name="parkQty[]" class="form-control parkQty" required readonly>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" onchange="change_amount(this,1),checkCurrentStock_pulleddocument(this), load_line_tax_amount(this)"
                                           onkeyup="checkCurrentStock(this)"
                                           name="quantityRequested[]"
                                           placeholder="0.00" class="form-control quantityRequested number"
                                           required>
                                

                                           
                                </td>
                                <td>
                                    <input type="text" onchange="change_amount(this,1), load_line_tax_amount(this)" name="estimatedAmount[]"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number estimatedAmount">
                                </td>
                                <?php if($group_based_tax == 1) { ?>
                                    <td>
                                        <?php $taxDrop = ($group_based_tax == 1 ? array(''=>'Select Tax Types'):all_tax_drop(1));
                                        echo form_dropdown('item_text[]', $taxDrop, '', 'class="form-control item_text input-mini" id="" onchange="load_line_tax_amount(this)"'); ?>
                                    </td>
                                    <td><span class="linetaxamnt pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <?php } ?>
                                <td>
                                    <input type="text" onchange="change_amount(this,2), load_line_tax_amount(this)" name="netAmount[]"

                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number netAmount input-mini">
                                </td>
                                <td style="display: none;">
                                        <textarea class="form-control" rows="3" name="comment[]"
                                                  placeholder="<?php echo $this->lang->line('accounts_receivable_common_item_comments'); ?>..."></textarea>
                                    <!--Item Comment-->
                                </td>
                                <td>
                                        <textarea class="form-control" rows="1" name="remarks[]"
                                                  placeholder="<?php echo $this->lang->line('accounts_receivable_common_item_remarks'); ?>..."></textarea>
                                    <!--Item Remarks-->
                                </td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="saveRvItemDetail()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="edit_rv_item_detail_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog modal-lg" style="width: 90%">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('accounts_receivable_tr_edit_item_detail'); ?><!--Edit Item Detail--></h4>
                </div>
                <form role="form" id="edit_rv_item_detail_form" class="form-horizontal">
                    <div class="modal-body">
                        <table class="table table-bordered table-condensed no-color" id="item_edit_table">
                            <thead>
                            <tr>
                                <th>
                                    <?php echo $this->lang->line('accounts_receivable_common_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_warehouse'); ?><!--Ware House --><?php required_mark(); ?></th>
                                <?php if ($itemBatchPolicy == 1) { ?>
                                <th>Batch Number <?php required_mark(); ?></th>
                                <?php } ?>

                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                    <th>Project Category</th>
                                    <th>Project Subcategory</th>
                                <?php } ?>
                                <th>
                                    <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('accounts_receivable_current_stock'); ?><!--Current Stock-->
                                </th>
                                <th>
                                    Park Qty
                                </th>   
                                <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span> <?php required_mark(); ?></th>

                                <?php if($group_based_tax == 1) { ?>
                                    <th colspan="2"><?php echo $this->lang->line('common_tax'); ?> </th>
                                <?php } ?>

                                <th style="width: 120px;">
                                    <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                    <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                                <th style="display: none;"><?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                <th><?php echo $this->lang->line('accounts_receivable_common_remarks'); ?><!--Remarks--></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="text" onkeyup="clearitemAutoIDEdit(event,this)"
                                           class="form-control"
                                           name="search" id="search"
                                           placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?>...">
                                    <!--Item ID--><!--Item Description-->
                                    <input type="hidden" class="form-control" name="itemAutoID"
                                           id="edit_itemAutoID">

                                           <input type="hidden" class="form-control" name="edit_itemcate"
                                           id="edit_itemcate">
                                     <input type="hidden" name="currentstock_pulleddoc_edit"
                                           id="currentstock_pulleddoc_edit"
                                           class="form-control">
                                </td>
                                <td>
                                    <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop_active(), '', 'class="form-control select2" id="edit_wareHouseAutoID" onchange="editstockwhreceiptvoucher(this),load_batch_number_single_edit_r_voucher(this)" required'); ?>
                                </td>
                                <?php if($itemBatchPolicy == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('batch_number[]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_edit" multiple="multiple" required'); ?>
                                        </td>
                                <?php } ?>
                                <?php if ($projectExist == 1) { ?>
                                    <td>
                                        <div id="edit_div_projectID_item">
                                            <select name="projectID" id="projectID" class="form-control select2">
                                                <option value="">
                                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('project_categoryID',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit1" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('project_subCategoryID',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit1"'); ?>
                                    </td>
                                <?php } ?>
                                <td>
                                    <input class="hidden conversionRateRVEdit" id="conversionRateRVEdit" name="conversionRateRVEdit">
                                    <?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control" onchange="convertPrice_RV_edit(this)" required id="edit_UnitOfMeasureID"'); ?>
                                </td>

                                <td>
                                    <div class="input-group">
                                        <input type="text" name="currentstock_edit"
                                               id="currentstock_edit"
                                               class="form-control" required disabled>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="parkQty_edit"
                                           id="parkQty_edit"
                                           class="form-control parkQty" required readonly>
                                </td>
                                <td>
                                    <input type="text" onchange="change_amount_edit(this,1),checkCurrentStockEditunapproveddocument(this), load_line_tax_amount_edit(this)"
                                           onkeyup="checkCurrentStockEdit(this)"
                                    
                                           name="quantityRequested"
                                           placeholder="0.00" class="form-control number"
                                           id="edit_quantityRequested" required>
                                </td>
                                <td>
                                    <input type="text" onchange="change_amount_edit(this,1), load_line_tax_amount_edit(this)" name="estimatedAmount"
                                           placeholder="0.00" onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number" id="edit_estimatedAmount">
                                </td>

                                <?php if($group_based_tax == 1) { ?>
                                    <td>
                                        <?php $taxDrop = ($group_based_tax == 1 ? array(''=>'Select Tax Types'):all_tax_drop(1));
                                        echo form_dropdown('item_text', $taxDrop, '', 'class="form-control item_text input-mini" id="edit_item_text" onchange="load_line_tax_amount_edit(this)"'); ?>
                                    </td>
                                    <td><span class="linetaxamnt_edit pull-right" id="linetaxamnt_edit" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <?php } ?>

                                <td>
                                    <input type="text" onchange="change_amount_edit(this,2), load_line_tax_amount_edit(this)" id="editNetAmount"
                                           name="netAmount[]" placeholder="0.00"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number netAmount input-mini">
                                </td>
                                <td style="display: none;">
                                        <textarea class="form-control" rows="3" name="comment" id="edit_comment"
                                                  placeholder="<?php echo $this->lang->line('accounts_receivable_common_item_comments'); ?>..."></textarea>
                                    <!--Item Comment-->
                                </td>
                                <td>
                                        <textarea class="form-control" rows="1" name="remarks" id="edit_remarks"
                                                  placeholder="<?php echo $this->lang->line('accounts_receivable_common_item_remarks'); ?>..."></textarea>
                                    <!--Item Remarks-->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="update_Rv_ItemDetail()">
                            <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    break;

    case "Invoices": case "InvoicesAdvance": case "CustomerInvoices": case "InvoicesItem": case "InvoicesIncome": ?>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs pull-right">
            <?php if($RVType == 'CustomerInvoices') { ?>
                <li><a data-toggle="tab" class="boldtab" href="#tab_6" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_receivable_common_expense'); ?> GL<!--Income--></a></li>
            <?php } ?>
            <?php if($RVType == 'Invoices' || $RVType == 'InvoicesIncome' || $RVType == 'InvoicesItem' || $RVType == 'CustomerInvoices') { ?>
                <li><a data-toggle="tab" class="boldtab" href="#tab_1" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_receivable_common_income'); ?> GL<!--Income--></a></li>
            <?php } ?>
            <?php if($RVType == 'Invoices' || $RVType == 'InvoicesItem') { ?>
                <li class=""><a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false">
                        <?php echo $this->lang->line('common_item'); ?><!--Item--></a></li>
            <?php } ?>
            <?php if($RVType == 'Invoices' || $RVType == 'CustomerInvoices' || $RVType == 'InvoicesItem') { ?>

                <li class="tab_7_Item">
                    <a data-toggle="tab" class="boldtab" href="#tab_7" aria-expanded="false">
                        <?php echo 'Supplier Invoice'  ?><!--Invoices--></a></li>
                
                <li class="tab_3_Item <?php if($RVType == 'CustomerInvoices') { echo 'active';}?> <?php if($RVType == 'InvoicesItem') { echo 'hide';}?>">
                    <a data-toggle="tab" class="boldtab" href="#tab_3" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_receivable_common_invoices'); ?><!--Invoices--></a></li>
            <?php } ?>
            <?php if($RVType == 'Invoices' || $RVType == 'CustomerInvoices' || $RVType == 'InvoicesItem') { ?>
                <li class="tab_5_Item <?php if($RVType == 'InvoicesItem') { echo 'hide';}?>">
                    <a data-toggle="tab" class="boldtab" href="#tab_5" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_receivable_ap_credit_note'); ?><!--Credit Note--></a></li>
            <?php } ?>
            <?php if($RVType == 'Invoices' || $RVType == 'InvoicesAdvance' || $RVType == 'InvoicesItem'  || $RVType == 'CustomerInvoices') { ?>
                <li class="tab_4_Item <?php if($RVType == 'InvoicesAdvance') { echo 'active';}?> <?php if($RVType == 'InvoicesItem') { echo 'hide';}?>">
                    <a data-toggle="tab" class="boldtab" href="#tab_4" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_receivable_common_advance'); ?><!--Advance--></a></li>
            <?php } ?>

            <li class="pull-left header"><i class="fa fa-hand-o-right"></i>
                <?php echo $this->lang->line('accounts_receivable_tr_invoice_base_receipt_for'); ?><!--Invoices Base Receipt for-->
                :
                - <?php echo $master['customerName']; ?></li>
        </ul>
        <div class="tab-content">
            <div id="tab_1" class="tab-pane ">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('accounts_receivable_common_income').' '; echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                            <?php if($group_based_tax == 1) {?> 
                                <th colspan="4"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } else { ?>
                                <th colspan="3"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } ?>
                            <th>
                                <button type="button" onclick="rv_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('accounts_receivable_common_add_gl'); ?><!--Add GL-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                            <th style="min-width: 30%">
                                <?php echo $this->lang->line('common_gl_code_description'); ?><!--GL Code Description--></th>
                            <th style="min-width: 12%">
                                <?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                            <th style="min-width: 12%">
                                <?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 12%"><?php echo $this->lang->line('common_discount'); ?><!--Discount--> <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <?php if($group_based_tax == 1) {?> 
                                <th style="min-width: 12%"><?php echo $this->lang->line('common_tax'); ?></th>
                            <?php } ?>
                            <th style="min-width: 12%"><?php echo $this->lang->line('common_total'); ?><!--Total--> <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 10%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <?php if($group_based_tax == 1) {?> 
                                <td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            <?php } else { ?>
                                <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            <?php } ?>
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
            </div><!-- /.tab-pane -->
            <div id="tab_6" class="tab-pane ">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('accounts_receivable_common_expense').' '; echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                            <?php if(false) {?> 
                                <th colspan="4"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } else { ?>
                                <th colspan="3"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } ?>
                            <th>
                                <button type="button" onclick="rv_detail_modal('Expense')"
                                        class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('accounts_receivable_common_add_gl'); ?><!--Add GL-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                            <th style="min-width: 30%">
                                <?php echo $this->lang->line('common_gl_code_description'); ?><!--GL Code Description--></th>
                            <th style="min-width: 12%">
                                <?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                            <th style="min-width: 12%">
                                <?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 12%"><?php echo $this->lang->line('common_discount'); ?><!--Discount--> <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <?php if(false) {?> 
                                <th style="min-width: 12%" id="taxTblHeader"><?php echo $this->lang->line('common_tax'); ?></th>
                            <?php } ?>
                            <th style="min-width: 12%"><?php echo $this->lang->line('common_total'); ?><!--Total--> <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 10%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="gl_table_body_expense">
                            <tr class="danger">
                                <?php if($group_based_tax == 1) {?> 
                                    <td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                <?php } else { ?>
                                    <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                <?php } ?>
                            </tr>
                            </tbody>
                        <tfoot id="gl_table_expense_tfoot">

                        </tfoot>
                    </table>
            </div><!-- /.tab-pane -->
            <div id="tab_2" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="6"><?php echo $this->lang->line('accounts_receivable_common_item_details'); ?><!--Item Details--></th>
                            <?php if($group_based_tax == 1) { ?>
                                <th colspan="3"><?php echo $this->lang->line('common_price'); ?><!--Price--> <span class="currency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <?php } else { ?>
                                <th colspan="2"><?php echo $this->lang->line('common_price'); ?><!--Price--> <span class="currency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <?php } ?>
                            
                            <th>
                                <button type="button" onclick="rv_item_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i
                                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?>
                                    <!--Add Item-->
                                </button>

                                <button type="button" onclick="edit_all_item_detail_modal()" style="margin-right:8px;"
                                        id="editallbtn" class="btn  btn-xs btn-default hidden pull-right"><span
                                            class="glyphicon glyphicon-pencil"></span> <?php echo $this->lang->line('common_document_edit_all'); ?><!--Edit All-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                            <?php if ($itemBatchPolicy == 1) { ?>
                                <th>Batch Number</th>
                            <?php } ?>
                            <th style="min-width: 36%" class="text-left">
                                <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th>Remark</th>
                            <th style="min-width: 7%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                            <th style="min-width: 7%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                            <?php if($group_based_tax == 1) { ?>
                                <th style="min-width: 10%"><?php echo $this->lang->line('common_tax'); ?><!--tax--></th>
                            <?php } ?>
                            <th style="min-width: 15%"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                        </tr>
                        </thead>
                        <tbody id="item_table_body">
                        <tr class="danger">
                            <?php if($group_based_tax == 1) { ?>
                                <td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
                            <?php } else { ?>
                                <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b></td>
                            <?php } ?>
                        </tr>
                        </tbody>
                        <tfoot id="item_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
            <div id="tab_3" class="tab-pane <?php if($RVType == 'CustomerInvoices') { echo 'active';}?>">
                    <?php
                    $amntcolspan=4;
                    $norec=9;
                    if($rebate==1){
                        $amntcolspan=5;
                        $norec=10;
                    }
                    ?>
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4">
                                <?php echo $this->lang->line('accounts_receivable_common_invoice_details'); ?><!--Invoice Details--></th>
                            <th colspan="<?=$amntcolspan; ?>"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                        class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                            <th>
                                <button type="button" data-toggle="modal" data-target="#inv_base_modal"
                                        class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_receivable_rebate'); ?><!--Add Invoice-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                            <th style="min-width: 15%" class="text-left">
                                <?php echo $this->lang->line('common_reference'); ?><!--Reference--></th>
                            <th style="min-width: 11%"><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                            <th style="min-width: 11%">
                                <?php echo $this->lang->line('accounts_receivable_common_invoice'); ?><!--Invoice--></th>
                            <th style="min-width: 11%">
                                <?php echo $this->lang->line('accounts_receivable_common_due'); ?><!--Due--></th>
                            <th style="min-width: 11%">
                                <?php echo $this->lang->line('accounts_receivable_common_paid'); ?><!--Paid--></th>
                            <?php
                            if($rebate==1){
                                ?>
                                <th style="min-width: 11%"><?php echo $this->lang->line('accounts_receivable_rebate'); ?><!--Rebate--></th>
                                <?php
                            }
                            ?>
                            <th style="min-width: 11%">
                                <?php echo $this->lang->line('accounts_receivable_common_balance'); ?><!--Balance--></th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                        </tr>
                        </thead>
                        <tbody id="invoice_table_body">
                        <tr class="danger">
                            <td colspan="<?=$norec; ?>" class="text-center"><b>
                                    <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot id="invoice_table_tfoot">

                        </tfoot>
                    </table>
            </div><!-- /.tab-pane -->
            <div id="tab_5" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('accounts_receivable_credit_note_details'); ?><!--Credit Note Details--></th>
                            <th colspan="4"><?php echo $this->lang->line('common_discount_amount'); ?><!--Amount--> <span
                                        class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                            <th>
                                <button type="button" data-toggle="modal" data-target="#creditNote_base_modal"
                                        class="btn btn-primary pull-right btn-xs"><i
                                            class="fa fa-plus"> </i><?php echo $this->lang->line('accounts_receivable_tr_cn_add_credit_note'); ?><!--Add Credit Notes-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                            <th style="min-width: 15%" class="text-left"><?php echo $this->lang->line('common_reference'); ?><!--Reference--></th>
                            <th style="min-width: 11%"><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                            <th style="min-width: 11%"><?php echo $this->lang->line('accounts_receivable_ap_credit_note'); ?><!--Credit Note--></th>
                            <th style="min-width: 11%"><?php echo $this->lang->line('common_due'); ?><!--Due--></th>
                            <th style="min-width: 11%"><?php echo $this->lang->line('accounts_receivable_matched'); ?><!--Matched--></th>
                            <th style="min-width: 11%"><?php echo $this->lang->line('common_balance'); ?><!--Balance--></th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                        </tr>
                        </thead>
                        <tbody id="creditNote_table_body">
                        <tr class="danger">
                            <td colspan="9" class="text-center"><b>
                                    <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot id="creditNote_table_tfoot">

                        </tfoot>
                    </table>
                </div>
            <div id="tab_4" class="tab-pane <?php if($RVType == 'InvoicesAdvance') { echo 'active';}?>">
                <table class="table table-bordered table-striped table-condesed">
                    <thead>
                    <tr>
                        <?php if($group_based_tax == 1) {?>
                            <th colspan="3"><?php echo $this->lang->line('accounts_receivable_tr_advance_details'); ?><!--Advance Details--></th>
                            <th colspan="2"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                        <?php } else { ?>
                            <th colspan="2"><?php echo $this->lang->line('accounts_receivable_tr_advance_details'); ?><!--Advance Details--></th>
                            <th> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                        <?php } ?>
                        <th>
                            <button type="button" onclick="rv_advance_detail_modal()"
                                    class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                <?php echo $this->lang->line('accounts_receivable_tr_add_advance'); ?><!--Add Advance-->
                            </button>
                        </th>
                    </tr>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 50%">
                            <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <?php if($group_based_tax == 1) {?>
                            <th style="min-width: 10%">Quotation Code</th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_tax'); ?><!--Tax--> <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                        <?php } ?>
                        <th style="min-width: 10%">
                            <?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span
                                    class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                        <!-- <th style="min-width: 15%">Local <span
                                class="locurrency">(<?php //echo $master['companyLocalCurrency']; ?>)</span></th>
                        <th style="min-width: 15%">Customer <span
                                class="sucurrency">(<?php //echo $master['customerCurrency']; ?>)</span></th> -->
                        <th style="min-width: 10%">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="advance_table_body">
                    <tr class="danger">
                        <td colspan="8" class="text-center"><b>
                                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                        </td>
                    </tr>
                    </tbody>
                    <tfoot id="advance_table_tfoot">

                    </tfoot>
                </table>
            </div><!-- /.tab-pane -->  
            <br>

            <div id="tab_7" class="tab-pane">
                <?php
                $amntcolspan=4;
                $norec=9;
                if($rebate==1){
                    $amntcolspan=5;
                    $norec=10;
                }
                ?>
                <table class="table table-bordered table-striped table-condesed">
                    <thead>
                    <tr>
                        <th colspan="4">
                            <?php echo $this->lang->line('accounts_receivable_common_invoice_details'); ?><!--Invoice Details--></th>
                        <th colspan="<?=$amntcolspan; ?>"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                    class="currency">(<?php echo $master['transactionCurrency']; ?>
                                )</span></th>
                        <th>
                            <button type="button" data-toggle="modal" data-target="#sup_inv_base_modal"
                                    class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_receivable_rebate'); ?><!--Add Invoice-->
                            </button>
                        </th>
                    </tr>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                        <th style="min-width: 15%" class="text-left">
                            <?php echo $this->lang->line('common_reference'); ?><!--Reference--></th>
                        <th style="min-width: 11%"><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                        <th style="min-width: 11%">
                            <?php echo $this->lang->line('accounts_receivable_common_invoice'); ?><!--Invoice--></th>
                        <th style="min-width: 11%">
                            <?php echo $this->lang->line('accounts_receivable_common_due'); ?><!--Due--></th>
                        <th style="min-width: 11%">
                            <?php echo $this->lang->line('accounts_receivable_common_paid'); ?><!--Paid--></th>
                        <?php
                        if($rebate==1){
                            ?>
                            <th style="min-width: 11%"><?php echo $this->lang->line('accounts_receivable_rebate'); ?><!--Rebate--></th>
                            <?php
                        }
                        ?>
                        <th style="min-width: 11%">
                            <?php echo $this->lang->line('accounts_receivable_common_balance'); ?><!--Balance--></th>
                        <th style="min-width: 10%">
                            <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="sup_invoice_table_body">
                        <tr class="danger">
                            <td colspan="<?=$norec; ?>" class="text-center"><b>
                                    <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot id="sup_invoice_table_tfoot">

                    </tfoot>
                </table>
            </div><!-- /.tab-pane -->
            
            <?php if($group_based_tax != 1) { ?>
                    <br>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="exampleInputName2" id="tax_tot">
                                <?php echo $this->lang->line('accounts_receivable_common_tax_for'); ?><!--Tax for--> </label>
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(1), '', 'class="form-control" id="text_type" required onchange="select_text(this)" style="width: 150px;"'); ?>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control number" id="percentage" name="percentage"
                                            style="width: 80px;" onkeyup="cal_tax(this.value)">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control number" id="tax_amount" name="tax_amount"
                                        style="width: 100px;" onkeyup="cal_tax_amount(this.value)">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                            </form>
                        </div>
                        <div class="col-md-7">
                            <table class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>
                                        <?php echo $this->lang->line('accounts_receivable_common_tax_type'); ?><!--Tax Type--></th>
                                    <th>
                                        <?php echo $this->lang->line('accounts_receivable_common_detail'); ?><!--Detail--></th>
                                    <th><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
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
                <?php } ?>
            
            
            
            </div><!-- /.tab-content -->
         
            
    </div>
    <!--<div class="text-right m-t-xs">
        <button class="btn btn-default prev" onclick="">Previous</button>
    </div>-->
    <div class="modal fade" id="inv_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         data-width="95%" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog" role="document" style="width: 70%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <?php echo $this->lang->line('accounts_receivable_tr_invoice_base'); ?><!--Invoice Base--></h4>
                </div>
                <div class="row">
                    <div class="form-group col-sm-10">
                    </div>
                    <div class="form-group col-sm-2">
                        <div class="skin skin-square">
                            <div class="skin-section extraColumns"><?php echo $this->lang->line('common_select_all'); ?><!--Select All-->&nbsp;<input id="issubtask" type="checkbox"
                                                                                                                                                      data-caption="" class="columnSelected add_allinvoices"
                                                                                                                                                      name="issubtask" value="1"><label
                                        for="checkbox">&nbsp;</label></div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">

                    <div class="form-group col-sm-2">
                        &nbsp; &nbsp;<strong style="font-size:13px;color: #4a8cdb;"><?php echo $this->lang->line('accounts_receivable_settlement_amount'); ?><!--Settlement Amount-->&nbsp;  </strong>
                    </div>
                    <div class="form-group col-sm-2">
                        <input type="text" name="amount_total" style="text-align: right;"
                               id="amount_total" value="<?php echo $master['settlementTotal'];?>"
                               class="form-control" onkeyup="deduct_total_amount();"onkeypress="return validateFloatKeyPress(this,event)";
                               required>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="table-responsive">
                            <div class="col-md-12" style="font-size:13px;color: #4a8cdb">
                                <div class="col-md-12" style="text-align: right;"><strong><?php echo $this->lang->line('accounts_receivable_utilized_amount'); ?><!--Utilized  Amount--></strong>&nbsp;
                                    <span id="total_invoice_total"><?php echo number_format($totalamountreceipt['totalamounttransaction'] ?? 0,$master['transactionCurrencyDecimalPlaces']) ;?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <div class="table-responsive">
                            <div class="col-md-12" style="font-size:13px;color: #4a8cdb">
                                <div class="col-md-12" style="text-align: right;"><strong><?php echo $this->lang->line('common_balance'); ?><!--Balance--></strong>&nbsp;
                                    <span id="grandtotal_amount"><?php echo number_format(($master['settlementTotal'] ?? 0 -$totalamountreceipt['totalamounttransaction'] ?? 0),$master['transactionCurrencyDecimalPlaces']) ;?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="modal-body">
                    <?php
                    if($rebate==1){
                        ?>
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <tr>
                                <th colspan="4">
                                    <?php echo $this->lang->line('accounts_receivable_common_invoice_details'); ?><!--Invoice Details--></th>
                                <th colspan="5"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                            </tr>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('accounts_receivable_common_invoice_code'); ?><!--Invoice Code--></th>
                                    <th style="width: 20%">Invoice Date</th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('accounts_receivable_common_reference_no'); ?><!--Reference No--></th>
                                <th style="width: 12%">
                                    <?php echo $this->lang->line('accounts_receivable_common_invoice_total'); ?><!--Invoice Total--></th>
                                <th style="width: 10%"><?php echo $this->lang->line('accounts_receivable_rebate_amount'); ?><!--Rebate Amount--></th>
                                <th style="width: 5%"> <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--></th>
                                <th style="width: 10%">
                                    <?php echo $this->lang->line('accounts_receivable_common_balance'); ?><!--Balance--></th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            </tr>
                            </thead>
                            <tbody id="table_body" class="invoice_base">
                            <input type="hidden" class="form-control" id="totalamount" name="totalamount" value="<?php echo $totalamountreceipt['totalamounttransaction'] ?? 0;?>">
                            <?php
                            if (!empty($customer_inv)) {
                                $x = 1;
                                for ($i = 0; $i < count($customer_inv); $i++) {
                                    $rebateamnt=$customer_inv[$i]['transactionAmount']*($customer_inv[$i]['rebatePercentage']/100);
                                    $ttamnt=$customer_inv[$i]['transactionAmount']-$rebateamnt;
                                    $balances = ($customer_inv[$i]['transactionAmount']) - ($customer_inv[$i]['receiptTotalAmount'] + $customer_inv[$i]['creditNoteTotalAmount'] + $customer_inv[$i]['advanceMatchedTotal']); // + $customer_inv[$i]['salesreturnvalue']
                                    $balance=$balances-($balances*($customer_inv[$i]['rebatePercentage']/100));
                                    if (round($balance,$master['transactionCurrencyDecimalPlaces']) > 0) {
                                        echo "<tr>";
                                        echo "<td>" .$x . "</td>";
                                        echo "<td>" . $customer_inv[$i]['invoiceCode'] . "</td>";
                                        echo "<td>" . $customer_inv[$i]['invoiceDate'] . "</td>";
                                        echo "<td>" . $customer_inv[$i]['referenceNo'] . "</td>";
                                        echo "<td class='text-right'>" . number_format($customer_inv[$i]['transactionAmount'], $master['transactionCurrencyDecimalPlaces']) . "</td>";
                                        echo "<td class='text-right'>" . $rebateamnt . " (".$customer_inv[$i]['rebatePercentage']."%)". "</td>";
                                        echo "<td class='text-right'>" . number_format($ttamnt, $master['transactionCurrencyDecimalPlaces']). "</td>";
                                        echo "<td class='text-right'><span class='receiptvoucherdetails'>" . number_format($balance, $master['transactionCurrencyDecimalPlaces']) . "&nbsp;<a class='hoverbtn invoiceaddbtn'  onclick='total_calculation(),applybtn(this,". $customer_inv[$i]['invoiceAutoID']. ",".round($balance,$master['transactionCurrencyDecimalPlaces']).")'><i class='fa fa-arrow-circle-right' aria-hidden='true'></i></a></span></td>";
                                        echo '<td ><input style="width: 88%;" type="text" name="amount[]" id="amount_' . $customer_inv[$i]['invoiceAutoID'] . '" onkeyup="total_calculation(),select_check_box(this,' . $customer_inv[$i]['invoiceAutoID'] . ',' . round($balance,$master['transactionCurrencyDecimalPlaces']) . ')" class="number amountadd">&nbsp;&nbsp;<i class="fa fa-times" onclick="clear_invoice_selected(this,' . $customer_inv[$i]['invoiceAutoID'] .')" aria-hidden="true"></i>

                                            <input type="hidden" class="InvoiceAutoID" value="' . $customer_inv[$i]['invoiceAutoID'] . '">
                                            <input type="hidden" id="Invoiceamount_' . $customer_inv[$i]['invoiceAutoID'] . '" value="' . $ttamnt . '">
                                            <input type="hidden" id="rebetamnt_' . $customer_inv[$i]['invoiceAutoID'] . '" value="' . $rebateamnt . '">
                                        </td>';
                                        echo '<td class="text-right" style="display:none;"><input class="checkbox" id="check_' . $customer_inv[$i]['invoiceAutoID'] . '" type="checkbox" value="' . $customer_inv[$i]['invoiceAutoID'] . '"></td>';
                                        echo "</tr>";
                                        $x ++;
                                    }
                                }
                            } else {
                                $norecordfound = $this->lang->line('common_no_records_found');
                                echo '<tr class="danger"><td colspan="7" class="text-center"><b>' . $norecordfound . '<!--No Records Found--></b></td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                    }else{
                        ?>
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <tr>
                                <th colspan="4">
                                    <?php echo $this->lang->line('accounts_receivable_common_invoice_details'); ?><!--Invoice Details--></th>
                                <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                            </tr>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">
                                    <?php echo $this->lang->line('accounts_receivable_common_invoice_code'); ?><!--Invoice Code--></th>
                                    <th style="width: 20%">Invoice Date</th>
                                <th style="width: 20%">
                                    <?php echo $this->lang->line('accounts_receivable_common_reference_no'); ?><!--Reference No--></th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('accounts_receivable_common_invoice_total'); ?><!--Invoice Total--></th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('accounts_receivable_common_balance'); ?><!--Balance--></th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            </tr>
                            </thead>
                            <tbody id="table_body" class="invoice_base">
                            <input type="hidden" class="form-control" id="totalamount" name="totalamount" value="<?php echo $totalamountreceipt['totalamounttransaction'] ?? 0;?>">
                            <?php
                            if (!empty($customer_inv)) {
                                $x = 1;
                                for ($i = 0; $i < count($customer_inv); $i++) {
                                    $balance = $customer_inv[$i]['transactionAmount'] - ($customer_inv[$i]['receiptTotalAmount'] + $customer_inv[$i]['creditNoteTotalAmount'] + $customer_inv[$i]['advanceMatchedTotal']); // + $customer_inv[$i]['salesreturnvalue']
                                    if (round($balance,$master['transactionCurrencyDecimalPlaces']) > 0) {
                                        echo "<tr>";
                                        echo "<td>" .$x . "</td>";
                                        echo "<td>" . $customer_inv[$i]['invoiceCode'] . "</td>";
                                        echo "<td>" . $customer_inv[$i]['invoiceDate'] . "</td>";
                                        echo "<td>" . $customer_inv[$i]['referenceNo'] . "</td>";
                                        echo "<td class='text-right'>" . number_format($customer_inv[$i]['transactionAmount'], $master['transactionCurrencyDecimalPlaces']) . "</td>";
                                        echo "<td class='text-right'><span class='receiptvoucherdetails'>" . number_format($balance, $master['transactionCurrencyDecimalPlaces']) . "&nbsp;<a class='hoverbtn invoiceaddbtn'  onclick='total_calculation(),applybtn(this,". $customer_inv[$i]['invoiceAutoID']. ",".round($balance,$master['transactionCurrencyDecimalPlaces']).")'><i class='fa fa-arrow-circle-right' aria-hidden='true'></i></a></span></td>";
                                        echo '<td ><input style="width: 88%;" type="text" name="amount[]" id="amount_' . $customer_inv[$i]['invoiceAutoID'] . '" onkeyup="total_calculation(),select_check_box(this,' . $customer_inv[$i]['invoiceAutoID'] . ',' . round($balance,$master['transactionCurrencyDecimalPlaces']) . ')" class="number amountadd">&nbsp;&nbsp;<i class="fa fa-times" onclick="clear_invoice_selected(this,' . $customer_inv[$i]['invoiceAutoID'] .')" aria-hidden="true"></i>

                                            <input type="hidden" class="InvoiceAutoID" value="' . $customer_inv[$i]['invoiceAutoID'] . '">
                                        </td>';
                                        echo '<td class="text-right" style="display:none;"><input class="checkbox" id="check_' . $customer_inv[$i]['invoiceAutoID'] . '" type="checkbox" value="' . $customer_inv[$i]['invoiceAutoID'] . '"></td>';
                                        echo "</tr>";
                                        $x ++;
                                    }
                                }
                            } else {
                                $norecordfound = $this->lang->line('common_no_records_found');
                                echo '<tr class="danger"><td colspan="7" class="text-center"><b>' . $norecordfound . '<!--No Records Found--></b></td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                    }
                    ?>

                    <div class="row">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" class="btn btn-primary" onclick="save_inv_base_items()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sup_inv_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         data-width="95%" data-keyboard="false" data-backdrop="static" >
        <div class="modal-dialog" role="document" style="width: 70%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <?php echo $this->lang->line('accounts_receivable_tr_invoice_base'); ?><!--Invoice Base--></h4>
                </div>
            
                <br>
                
                <div class="modal-body">
                
                <table class="table table-bordered table-striped table-condesed ">
                                <thead>
                                <tr>
                                    <th colspan="5">
                                        <?php echo $this->lang->line('accounts_payable_invoice_details'); ?><!--Invoice Details--></th>
                                    <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('accounts_payable_invoice_code'); ?><!--Invoice Code--></th>
                                        <th style="width: 15%">
                                        Invoice Date</th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('accounts_payable_supplier_invoice_no'); ?><!--Supplier Invoice No--></th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('accounts_payable_reference_no'); ?><!--Reference No--></th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_invoice_total'); ?><!--Invoice Total--></th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('accounts_payable_balance'); ?><!--Balance--></th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                                    <!-- <th >&nbsp;</th> -->
                                </tr>
                                </thead>
                                <tbody id="table_body" class="invoice_base">
                                <input type="hidden" class="form-control" id="totalamount" name="totalamount"
                                       value="<?php echo $totalamountreceipt['totalamounttransaction'] ?? 0; ?>">
                                <?php
                                if (!empty($supplier_inv)) {
                                    $x = 1;
                                    for ($i = 0; $i < count($supplier_inv); $i++) {
                                        //$id=$supplier_inv[$i]['InvoiceAutoID'];
                                        if (round($supplier_inv[$i]['transactionAmount'] - ($supplier_inv[$i]['paymentTotalAmount'] + $supplier_inv[$i]['DebitNoteTotalAmount'] + $supplier_inv[$i]['advanceMatchedTotal']), $master['transactionCurrencyDecimalPlaces']) > 0) {
                                            $id = $supplier_inv[$i]['InvoiceAutoID'];
                                            $type = 'BSI';

                                            echo "<tr>";
                                            echo "<td >" . $x . "</td>";
                                            echo "<td><a class='a_link' target=\"_blank\" onclick=\"documentPageView_modal('BSI','$id')\">" . $supplier_inv[$i]['bookingInvCode'] . "</a></td>";
                                            echo "<td>" . $supplier_inv[$i]['bookingDate'] . "</td>";
                                            echo "<td>" . $supplier_inv[$i]['supplierInvoiceNo'] . "</td>";
                                            echo "<td>" . $supplier_inv[$i]['RefNo'] . "</td>";
                                            echo "<td class='text-right'>" . number_format($supplier_inv[$i]['transactionAmount'], $master['transactionCurrencyDecimalPlaces']) . "</td>";

                                            echo "<td class='text-right'>
                                         <span class='supplierinvoicebalance'>
                                      " . number_format(($supplier_inv[$i]['transactionAmount'] - ($supplier_inv[$i]['paymentTotalAmount'] + $supplier_inv[$i]['DebitNoteTotalAmount'] + $supplier_inv[$i]['advanceMatchedTotal'])), $master['transactionCurrencyDecimalPlaces']) . "
                                            </span>
                                  
                                    
                                    
                                        <a class='hoverbtn invoiceaddbtn'  onclick='applybtn(this," . $supplier_inv[$i]['InvoiceAutoID'] . "," . round($supplier_inv[$i]['transactionAmount'] - ($supplier_inv[$i]['paymentTotalAmount'] + $supplier_inv[$i]['DebitNoteTotalAmount'] + $supplier_inv[$i]['advanceMatchedTotal']), $master['transactionCurrencyDecimalPlaces']) . ")'><i class='fa fa-arrow-circle-right' aria-hidden='true'></i></a>
             

                                    </td>";
                                            echo '<td><input type="text" name="amount[]" style="width: 82%;" id="amount_' . $supplier_inv[$i]['InvoiceAutoID'] . '" onkeypress="return validateFloatKeyPress(this,event);"  onkeyup="total_calculation(),select_check_box(this,' . $supplier_inv[$i]['InvoiceAutoID'] . ',' . round($supplier_inv[$i]['transactionAmount'] - ($supplier_inv[$i]['paymentTotalAmount'] + $supplier_inv[$i]['DebitNoteTotalAmount'] + $supplier_inv[$i]['advanceMatchedTotal']), $master['transactionCurrencyDecimalPlaces']) . ')" class="amountadd number">&nbsp;<i class="fa fa-times" onclick="clear_invoice_selected(this,' . $supplier_inv[$i]['InvoiceAutoID'] . ',' . round($supplier_inv[$i]['transactionAmount'] - ($supplier_inv[$i]['paymentTotalAmount'] + $supplier_inv[$i]['DebitNoteTotalAmount'] + $supplier_inv[$i]['advanceMatchedTotal']), $master['transactionCurrencyDecimalPlaces']) . ')" aria-hidden="true"></i>&nbsp;
                                        &nbsp;
                                            <input type="hidden" class="InvoiceAutoID" value="' . $supplier_inv[$i]['InvoiceAutoID'] . '"></td>';
                                            echo '<td class="text-right;" style="display:none;"  ><input class="checkbox" id="check_' . $supplier_inv[$i]['InvoiceAutoID'] . '" type="checkbox" value="' . $supplier_inv[$i]['InvoiceAutoID'] . '"></td>';
                                            echo "</tr>";
                                            $x++;
                                        }


                                    }
                                } else {
                                    $norecfound = $this->lang->line('common_no_records_found');
                                    echo '<tr class="danger"><td colspan="7" class="text-center"><b>' . $norecfound . '<!--No Records Found--></b></td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>

                    <div class="row">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" class="btn btn-primary" onclick="save_inv_base_items('SUP')">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div aria-hidden="true" role="dialog" id="rv_detail_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">
                        <?php echo $this->lang->line('accounts_receivable_common_add_gl_detail'); ?><!--Add GL Detail--></h5>
                </div>
                <form role="form" id="rv_detail_form" class="form-horizontal">
                    <div class="modal-body">

                        <input type="hidden" name="GL_Type" id="GL_Type" value="" />

                        <table class="table table-bordered table-condensed no-color" id="income_add_table">
                            <thead>
                            <tr>
                                <th style="width: 380px">
                                    <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></th>
                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                    <th>Project Category</th>
                                    <th>Project Subcategory</th>

                                <?php } ?>
                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span><?php required_mark(); ?></th>
                                <th>Discount Percentage</th>
                                <th>Discount Amount</th>
                                <?php if($group_based_tax == 1) { ?>
                                    <th colspan="2" id="taxColumn"><?php echo $this->lang->line('common_tax'); ?></th>
                                <?php } ?>
                                <th>Net Amount <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span><?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs"
                                            onclick="add_more_income()">
                                        <i
                                                class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php echo form_dropdown('gl_code[]', $gl_code_arr, '', 'class="form-control select2" required'); ?>
                                </td>
                                <td>
                                    <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" onchange="load_segmentBase_projectID_income(this)"'); ?>
                                </td>
                                <?php if ($projectExist == 1) { ?>
                                    <td class="form-group" style="<?php echo $stylewidth1?>">
                                        <div class="div_projectID_income">
                                            <select name="projectID" class="form-control select2">
                                                <option value="">
                                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="form-group" style="<?php echo $stylewidth2?>">
                                        <?php echo form_dropdown('project_categoryID[]',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                    </td>
                                    <td class="form-group" style="<?php echo $stylewidth2?>">
                                        <?php echo form_dropdown('project_subCategoryID[]',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                    </td>
                                <?php } ?>
                                <td>
                                    <input type="text" name="amount[]" onchange="load_gl_line_tax_amount(this)"
                                           onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'amount')" value="00"
                                           class="form-control number amount">
                                </td>
                                <td><input type="text" name="discountPercentage[]" onkeypress="return validateFloatKeyPress(this,event)" onchange="load_gl_line_tax_amount(this)" onkeyup="calculateNetAmount(this,'discountPercentage')" value="00" class="form-control number discountPercentage"></td>
                                <td><input type="text" name="discountAmount[]" onkeypress="return validateFloatKeyPress(this,event)" onchange="load_gl_line_tax_amount(this)" onkeyup="calculateNetAmount(this,'discountAmount')" value="00" class="form-control number discountAmount"></td>
                                <?php if($group_based_tax == 1) { ?>
                                    <td id="taxColumnData">
                                        <?php echo form_dropdown('gl_text[]', all_tax_formula_drop_groupByTax(1), '', 'class="form-control gl_text input-mini" id="" onchange="load_gl_line_tax_amount(this)"'); ?>
                                    </td>
                                    <td id="taxColumnDataValue"><span class="gl_linetaxamnt pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <?php } ?>
                                <td ><input type="text" name="Netamount[]" value="00" class="form-control number Netnumber" readonly></td>
                                <td>
                                        <textarea class="form-control" rows="1"
                                                  name="description[]"></textarea>
                                </td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="saveDirectRvDetails()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="edit_rv_income_detail_modal" class="modal fade"
         style="display: none;">
        <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('accounts_receivable_tr_edit_gl_code'); ?><!--Edit GL Code--></h4>
                </div>
                <form role="form" id="edit_rv_income_detail_form" class="form-horizontal">

                    <input type="hidden" name="type" id="gl_type_edit" value="" />

                    <div class="modal-body">
                        <table class="table table-bordered table-condensed no-color" id="income_edit_table">
                            <thead>
                            <tr>
                                <th style="width: 380px">
                                    <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></th>

                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                    <th>Project Category</th>
                                    <th>Project Subcategory</th>
                                <?php } ?>
                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span><?php required_mark(); ?></th>
                                <th>Discount Percentage</th>
                                <th>Discount Amount</th>
                                <?php if ($group_based_tax== 1) { ?>
                                    <th colspan="2" id="edittaxColumn"><?php echo $this->lang->line('common_tax'); ?></th>
                                <?php } ?>
                                <th>Net Amount <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span><?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php echo form_dropdown('gl_code', $gl_code_arr, '', 'class="form-control select2" id="edit_gl_code" required'); ?>
                                </td>
                                <td>
                                    <?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="edit_segment_gl" onchange="load_segmentBase_projectID_incomeEdit(this)"'); ?>
                                </td>
                                <?php if ($projectExist == 1) { ?>
                                    <td>
                                        <div id="edit_div_projectID_income">
                                            <select name="projectID" id="projectID" class="form-control select2">
                                                <option value="">
                                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('project_categoryID',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('project_subCategoryID',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit"'); ?>
                                    </td>
                                <?php } ?>
                                <td>
                                    <input type="text" name="amount" onchange="load_gl_line_tax_amount_edit(this)"
                                           onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'amount')" value="00"
                                           id="edit_amount"
                                           class="form-control number">
                                </td>
                                <td><input type="text" name="discountPercentage" id="discountPercentage_edit" onkeypress="return validateFloatKeyPress(this,event)" onchange="load_gl_line_tax_amount_edit(this)" onkeyup="calculateNetAmount_edit(this,'discountPercentage')" value="00" class="form-control number "></td>
                                <td><input type="text" name="discountAmount" id="discountAmount_edit" onkeypress="return validateFloatKeyPress(this,event)" onchange="load_gl_line_tax_amount_edit(this)" onkeyup="calculateNetAmount_edit(this,'discountAmount')" value="00" class="form-control number "></td>
                                <?php if ($group_based_tax== 1) { ?>
                                    <td id="edittaxColumnData"><?php echo form_dropdown('gl_text_type', all_tax_formula_drop_groupByTax(1), '', 'class="form-control gl_text_type" style="width: 134px;" id="gl_text_type_edit" onchange="load_gl_line_tax_amount_edit(this)" '); ?></td>
                                    
                                    <td id="edittaxColumnDataValue"><span class="gl_linetaxamnt pull-right" id="gl_linetaxamnt_edit" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <?php } ?>
                                <td><input type="text" name="Netamount" id="Netamount_edit" value="00" class="form-control number " readonly></td>
                                <td>
                                        <textarea class="form-control" rows="1" name="description"
                                                  id="edit_description"></textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="updateDirectRvDetails()">
                            <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="rv_item_detail_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">
                        <?php echo $this->lang->line('accounts_receivable_common_add_item_detail'); ?><!--Add Item Detail--></h5>
                </div>
                <form role="form" id="rv_item_detail_form" class="form-horizontal">
                    <div class="modal-body">
                        <table class="table table-bordered table-condensed no-color" id="item_add_table">
                            <thead>
                            <tr>
                                <th>
                                    <?php echo $this->lang->line('accounts_receivable_common_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_warehouse'); ?><!--Ware House--> <?php required_mark(); ?></th>
                                <?php if ($itemBatchPolicy == 1) { ?>
                                <th>Batch Number <?php required_mark(); ?></th>
                                <?php } ?>
                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                    <th>Project Category</th>
                                    <th>Project Subcategory</th>

                                <?php } ?>
                                <th>
                                    <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                <th>Current Stock</th>
                                <th style="width:80px;"><abbr title="Park Qty">Park Qty</abbr></th>

                                <th>
                                    <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                <?php if($group_based_tax == 1) { ?>
                                    <th class="hideTaxpolicy_edit" colspan="2"><?php echo $this->lang->line('common_tax'); ?> </th>
                                <?php } ?>
                                <th style="width: 120px;">
                                    <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                    <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                                <th style="display: none;">
                                    <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                <th>
                                    <?php echo $this->lang->line('accounts_receivable_common_remarks'); ?><!--Remarks--></th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary btn-xs" onclick="add_more_item()">
                                        <i class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="text" onkeyup="clearitemAutoID(event,this)"
                                           class="form-control search f_search" name="search[]" id="f_search_1"
                                           placeholder="<?php echo $this->lang->line('common_item_id'); ?>, <?php echo $this->lang->line('common_item_description'); ?>...">
                                    <!--Item ID--><!--Item Description-->
                                    <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                    <input type="hidden" class="form-control itemcatype" name="itemcatype[]">

                                </td>
                                <td>
                                    <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop_active(), '', 'class="form-control select2 wareHouseAutoID" onchange="checkitemavailable(this)"  required'); ?>
                                </td>
                                <?php if($itemBatchPolicy == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('batch_number[0][]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_1" multiple="multiple" required'); ?>
                                        </td>
                                <?php } ?>
                                <?php if ($projectExist == 1) { ?>
                                    <td>
                                        <div class="div_projectID_item">
                                            <select name="projectID" class="form-control select2">
                                                <option value="">
                                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
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
                                    <input class="hidden conversionRate" id="conversionRate" name="conversionRate">
                                    <?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown" onchange="convertPrice_RV(this)" required'); ?>
                                </td>

                                <td>
                                    <div class="input-group">
                                        <input type="text" name="currentstock[]"
                                               class="form-control currentstock" required disabled>
                                    <input type="hidden" name="currentstock_pulleddoc" id="currentstock_pulleddoc" class="form-control currentstock_pulleddoc" >
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" name="parkQty[]" class="form-control parkQty" required readonly>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" onchange="change_amount(this,1),checkCurrentStock_pulleddocument(this), load_line_tax_amount(this)"
                                           onkeyup="checkCurrentStock(this)"
                                           name="quantityRequested[]"
                                           class="form-control number quantityRequested"
                                           onfocus="this.select();" required>
                                </td>
                                <td>
                                    <input type="text" onchange="change_amount(this,1), load_line_tax_amount(this)" name="estimatedAmount[]"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           onfocus="this.select();"
                                           class="form-control number estimatedAmount">
                                </td>
                                <?php if($group_based_tax == 1) { ?>
                                    <td>
                                        <?php $taxDrop = ($group_based_tax == 1 ? array(''=>'Select Tax Types'):all_tax_drop(1));
                                        echo form_dropdown('item_text[]', $taxDrop, '', 'class="form-control item_text input-mini" id="" onchange="load_line_tax_amount(this)"'); ?>
                                    </td>
                                    <td><span class="linetaxamnt pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <?php } ?>
                                <td>
                                    <input type="text" onchange="change_amount(this,2)" name="netAmount[]"
                                           onkeypress="return validateFloatKeyPress(this,event), load_line_tax_amount(this)"
                                           class="form-control number netAmount input-mini">
                                </td>
                                <td style="display: none;">
                                        <textarea class="form-control" rows="3" name="comment[]"
                                                  placeholder="<?php echo $this->lang->line('accounts_receivable_common_item_comments'); ?>..."></textarea>
                                    <!--Item Comment-->
                                </td>
                                <td>
                                        <textarea class="form-control" rows="1" name="remarks[]"
                                                  placeholder="<?php echo $this->lang->line('accounts_receivable_common_item_remarks'); ?>..."></textarea>
                                    <!--Item Remarks-->
                                </td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="saveRvItemDetail()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div aria-hidden="true" role="dialog" id="edit_rv_item_detail_modal" class="modal fade" style="display: none;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('accounts_receivable_tr_edit_item_detail'); ?><!--Edit Item Detail--></h4>
                </div>
                <form role="form" id="edit_rv_item_detail_form" class="form-horizontal">
                    <div class="modal-body">
                        <table class="table table-bordered table-condensed no-color" id="item_edit_table">
                            <thead>
                            <tr>
                                <th>
                                    <?php echo $this->lang->line('accounts_receivable_common_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                <th>
                                    <?php echo $this->lang->line('common_warehouse'); ?><!--Ware House--> <?php required_mark(); ?></th>
                                <?php if ($itemBatchPolicy == 1) { ?>
                                <th>Batch Number <?php required_mark(); ?></th>
                                <?php } ?>

                                <?php if ($projectExist == 1) { ?>
                                    <th>
                                        <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                    <th>Project Category</th>
                                    <th>Project Subcategory</th>

                                <?php } ?>
                                <th>
                                    <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>

                                <th>
                                    Current Stock
                                </th>
                                <th>
                                   Park Qty
                                </th>
                                <th>
                                    <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                
                                <?php if($group_based_tax == 1) { ?>
                                    <th colspan="2" id="edittaxColumnExpense"><?php echo $this->lang->line('common_tax'); ?><!--tax--> <?php required_mark(); ?></th>
                                <?php } ?>

                                <th style="width: 120px;">
                                    <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                    <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                                <th style="display: none;">
                                    <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                <th style="display: none;">
                                    <?php echo $this->lang->line('accounts_receivable_common_remarks'); ?><!--Remarks--></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="text" onkeyup="clearitemAutoIDEdit(event,this)"
                                           class="form-control"
                                           name="search" id="search"
                                           placeholder="<?php echo $this->lang->line('common_item_id'); ?>, <?php echo $this->lang->line('common_item_description'); ?>...">
                                    <!--Item ID--><!--Item Description-->
                                    <input type="hidden" class="form-control" name="itemAutoID"
                                           id="edit_itemAutoID">

                                  
                                <input type="hidden" class="form-control" name="edit_itemcate" id="edit_itemcate">
                                <input type="hidden" name="currentstock_pulleddoc_edit" id="currentstock_pulleddoc_edit" class="form-control">
                                 
                                </td>
                                <td>
                                    <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop_active(), '', 'class="form-control select2" id="edit_wareHouseAutoID" onchange="editstockwhreceiptvoucher(this),load_batch_number_single_edit_r_voucher(this)"  required'); ?>
                                </td>
                                <?php if($itemBatchPolicy == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('batch_number[]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_edit" multiple="multiple" required'); ?>
                                        </td>
                                <?php } ?>
                                <?php if ($projectExist == 1) { ?>
                                    <td>
                                        <div id="edit_div_projectID_item">
                                            <select name="projectID" id="projectID" class="form-control select2">
                                                <option value="">
                                                    <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('project_categoryID',  array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit1" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('project_subCategoryID',  array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit1"'); ?>
                                    </td>

                                <?php } ?>
                                <td>
                                    <input class="hidden conversionRateRVEdit" id="conversionRateRVEdit" name="conversionRateRVEdit">
                                    <?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control" onchange="convertPrice_RV_edit(this)" required id="edit_UnitOfMeasureID" '); ?>
                                </td>

                                <td>

                                    <input type="text" name="currentstock_edit"
                                           id="currentstock_edit"
                                           class="form-control" required disabled>
                                 


                                </td>
                                <td>
                                    <input type="text" name="parkQty_edit"
                                           id="parkQty_edit"
                                           class="form-control parkQty" required readonly>
                                </td>
                                <td>
                                    <input type="text" onchange="change_amount_edit(this,1),checkCurrentStockEditunapproveddocument(this), load_line_tax_amount_edit(this)"
                                           onkeyup="checkCurrentStockEdit(this)"
                                    
                                           name="quantityRequested"
                                           placeholder="0.00" class="form-control number"
                                           id="edit_quantityRequested" required>
                                </td>
                                <td>
                                    <input type="text" onchange="change_amount_edit(this,1), load_line_tax_amount_edit(this)" name="estimatedAmount"
                                           placeholder="0.00" class="form-control number"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           onfocus="this.select();" id="edit_estimatedAmount">
                                </td>
                                <?php if($group_based_tax == 1) { ?>
                                    <td id="edittaxColumnDataExpense">
                                        <?php $taxDrop = ($group_based_tax == 1 ? array(''=>'Select Tax Types'):all_tax_drop(1));
                                        echo form_dropdown('item_text', $taxDrop, '', 'class="form-control item_text input-mini" id="edit_item_text" onchange="load_line_tax_amount_edit(this)"'); ?>
                                    </td>
                                    <td id="edittaxColumnDataValueExpense"><span class="linetaxamnt_edit pull-right" id="linetaxamnt_edit" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                                <?php } ?>

                                <td>
                                    <input type="text" onchange="change_amount_edit(this,2), load_line_tax_amount_edit(this)" id="editNetAmount"
                                           name="netAmount[]" placeholder="0.00"
                                           onkeypress="return validateFloatKeyPress(this,event)"
                                           class="form-control number netAmount input-mini">
                                </td>
                                <td style="display: none;">
                                        <textarea class="form-control" rows="3" name="comment" id="edit_comment"
                                                  placeholder="<?php echo $this->lang->line('accounts_receivable_common_item_comments'); ?>..."></textarea>
                                    <!--Item Comment-->
                                </td>
                                <td style="display: none;">
                                        <textarea class="form-control" rows="3" name="remarks" id="edit_remarks"
                                                  placeholder="<?php echo $this->lang->line('accounts_receivable_common_item_remarks'); ?>..."></textarea>
                                    <!--Item Remarks-->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="update_Rv_ItemDetail()">
                            <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="creditNote_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         data-width="95%" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Credit Note Base</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped table-condesed ">
                        <thead>
                        <tr>
                            <th colspan="5">Credit Note Details</th>
                            <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                        class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 20%">Credit Note Code</th>
                            <th style="width: 5%">Type</th>
                            <th style="width: 5%">Date</th>
                            <th style="width: 20%">Reference No</th>
                            <th style="width: 15%">Credit Note Total</th>
                            <th style="width: 15%">Balance</th>
                            <th style="width: 15%">Amount</th>
                            <!-- <th >&nbsp;</th> -->
                        </tr>
                        </thead>
                        <tbody id="table_body">
                        <?php
                        /*echo '<pre>';
                        print_r($credit_note);
                        echo '</pre>';*/
                        $d = $master['transactionCurrencyDecimalPlaces'];
                        if (!empty($credit_note)) {
                            $i = 0;
                            foreach ($credit_note as $val) {
                                $dif = $val['transactionAmount'] - $val['RVTransactionAmount'];
                                if ($dif > 0) {
                                    echo "<tr>";
                                    echo "<td>" . ($i + 1) . "</td>";
                                    echo "<td>" . $val['creditNoteCode'] . "</td>";
                                    echo "<td>" . $val['type'] . "</td>";
                                    echo "<td>" . $val['creditNoteDate'] . "</td>";
                                    echo "<td>" . $val['RefNo'] . "</td>";
                                    echo "<td class='text-right'>" . number_format($val['transactionAmount'], $d) . "</td>";
                                    echo '<td class="text-right" id="bal_'.$val['creditNoteMasterAutoID'].'">' . number_format($dif, $d) . ' <i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(\''.$val['creditNoteMasterAutoID'].'\',\''.$val['transactionAmount'].'\')" aria-hidden="true"></i></td>';
                                    $onclickVal = 'this,' . $val['creditNoteMasterAutoID'] . ',' . $dif;
                                    ?>
                                    <td>
                                        <input type="hidden"
                                               value="<?php echo $val['transactionAmount'] ?>"
                                               id="CNTransAmount_<?php echo $val['creditNoteMasterAutoID'] ?>"
                                               name="transactionAmount[]">
                                        <input type="text" name="amount[]"
                                               id="CNamount_<?php echo $val['creditNoteMasterAutoID'] ?>"
                                               onkeypress="return validateFloatKeyPress(this,event);"
                                               onkeyup="select_check_boxCN(<?php echo $onclickVal ?>)"
                                               class="number amountinput"
                                               data-creditNoteMasterAutoID ="<?php echo $val['creditNoteMasterAutoID'] ?>"
                                               data-type ="<?php echo $val['type'] ?>"
                                               data-transactionAmount ="<?php echo $val['transactionAmount'] ?>"
                                                >
                                    </td>
                                    <?php
                                    echo '<td class="text-right;" style="display:none;"  ><input class="checkbox" id="CNcheck_' . $val['creditNoteMasterAutoID'] . '" type="checkbox" value="' . $val['creditNoteMasterAutoID'] . '"><input type="hidden" value="' . $val['type'] . '" id="type_' . $val['creditNoteMasterAutoID'] . '"></td>';
                                    echo "</tr>";
                                    $i++;
                                }
                            }
                        } else {
                            $norecfounds = $this->lang->line('common_no_records_found');
                            echo '<tr class="danger"><td colspan="7" class="text-center"><b>' . $norecfounds . '<!--No Records Found--></b></td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button type="button" class="btn btn-primary" onclick="save_creditNote_base_items()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
    break;
    default:
        echo "Conntect system Admin";
}
?>

<div aria-hidden="true" role="dialog" id="rv_advance_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width:60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">
                    <?php echo $this->lang->line('accounts_receivable_tr_add_advance_detail'); ?><!--Add Advance Detail--></h5>
            </div>
            <form role="form" id="rv_advance_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="advance_add_table">
                        <thead>
                        <tr>
                            <?php if($group_based_tax == 1){ ?>
                                <th>Document</th>
                                <th>Document Amount</th>
                                <th>Paid Amount</th>
                                <th>Balance Amount</th>
                            <?php }?>
                            <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span> <?php required_mark(); ?></th>
                            <?php if($group_based_tax == 1){ ?><th colspan="2">Tax</th><?php }?>
                            <th><?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                            <?php if($projectExist== 1){ ?><th>Project</th><?php }?>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_advance()">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <?php if($group_based_tax == 1) { ?>
                                <td>
                                    <?php echo form_dropdown('contractAutoID_advance[]', $detail['contract_documents'], '', 'class="select2 form-control contractAutoID_advance input-mini" onchange="load_line_tax_amount_advance(this), load_document_amounts(this)"'); ?>
                                </td>
                                <td><input type="text" value="00" class="form-control number contract_amount" readonly></td>
                                <td><input type="text" value="00" class="form-control number contract_paid_amount" readonly></td>
                                <td><input type="text" value="00" class="form-control number contract_balance_amount" readonly></td>
                            <?php }?>
                            <td>
                                <input type="text" name="amount[]"  onchange="load_line_tax_amount_advance(this)" onkeypress="return validateFloatKeyPress(this,event)" value="00" class="form-control number amount_advance">
                            </td>
                            <?php if($group_based_tax == 1) { ?>
                                <td>
                                    <?php echo form_dropdown('item_text_advance[]', all_tax_formula_drop_groupByTax(1), '', 'class="form-control item_text_advance input-mini" onchange="load_line_tax_amount_advance(this)"'); ?>
                                </td>
                                <td><span class="linetaxamnt_advance pull-right" style="font-size: 14px;text-align: right;margin-top: 8%;">0</span></td>
                            <?php }?>
                            <td>
                                <textarea class="form-control" rows="1" name="description[]"></textarea>
                            </td>
                            <?php if($projectExist== 1){ ?>
                                <td>
                                    <?php echo form_dropdown('projectID[]', $project,'','class="form-control select2" id="projectID"'); ?>
                                </td>
                            <?php }?>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="saveRvAdvanceDetail()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var search_id = 1;
    var receiptVoucherAutoId;
    var receiptVoucherDetailAutoID;
    var RVType;
    var customerID;
    var currencyID;
    var tax_total;
    var tax_total_expense;
    var projectID_income;
    var projectID_item;
    var currentEditWareHouseAutoID='';
    var currentEditTextBatchData='';
    var defaultSegment = <?php echo json_encode($this->common_data['company_data']['default_segment']); ?>;
    var projectcategory;
    var projectsubcat;
    var isGroupWiseTax = <?php echo json_encode(trim($group_based_tax)); ?>;
    var select_VAT_value = '';
    var select_gl_VAT_value = '';
    $(document).ready(function () {
        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        $('.select2').select2();
        receiptVoucherDetailAutoID = null;
        projectID_income = null;
        projectID_item = null;
        receiptVoucherAutoId = <?php echo json_encode(trim($master['receiptVoucherAutoId'] ?? '')); ?>;
        RVType = <?php echo json_encode(trim($master['RVType'] ?? '')); ?>;
        customerID = <?php echo json_encode(trim($master['customerID'] ?? '')); ?>;
        currencyID = <?php echo json_encode(trim($master['transactionCurrency'] ?? '')); ?>;
        currency_decimal = <?php echo json_encode(trim($master['transactionCurrencyDecimalPlaces'] ?? '')); ?>;
        setTimeout(function () {
            fetch_rv_details(<?php echo json_encode(trim($tab)); ?>);
        }, 300);
        initializeitemTypeahead();
        initializeitemTypeahead_edit();
        $('.currency').html('(' + currencyID + ')');
        number_validation();
        $('#tax_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                tax_amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_tr_tax_amount_is_required');?>.'}}}, /*Tax Amount is required*/
                text_type: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_tr_tax_type_is_required');?>.'}}}, /*Tax Type is required*/
                percentage: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_receivable_tr_percentage_is_required');?>.'}}}/*Percentage is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'receiptVoucherAutoId', 'value': receiptVoucherAutoId});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Receipt_voucher/save_inv_tax_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data['type'], data['data'], 1000);
                    stopLoad();
                    if (data['status']) {
                        $form.bootstrapValidator('resetForm', true);
                        setTimeout(function () {
                            fetch_details(1);
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

    function rv_item_detail_modal() {
        if (receiptVoucherAutoId) {
            $('.search').typeahead('destroy');
            $("#wareHouseAutoID").val(null).trigger("change");
            $('#rv_item_detail_form')[0].reset();
            $('.search').typeahead('val', '');
            $('.itemAutoID').val('');
            initializeitemTypeahead(1);
            $('.select2').select2('');
            load_segmentBase_projectID_item();
            $('#item_add_table tbody tr').not(':first').remove();
            $('.f_search').closest('tr').css("background-color", 'white');
            $('.wareHouseAutoID ').closest('tr').css("background-color", 'white');
            $('.quantityRequested').closest('tr').css("background-color", 'white');
            $("#rv_item_detail_modal").modal({backdrop: "static"});
        }
    }

    function rv_detail_modal(type='income') {
        if (receiptVoucherAutoId) {
            $("#gl_code").val(null).trigger("change");
            $('#rv_detail_form')[0].reset();
            $('.segment_glAdd').val(defaultSegment).change();

            if(type == 'income'){
                $('#GL_Type').val('GL');
                $('#taxColumn').removeClass('hide');
                $('#taxColumnData').removeClass('hide');
                $('#taxColumnDataValue').removeClass('hide');
            }else{
                $('#GL_Type').val('EXGL');
                $('#taxColumn').addClass('hide');
                $('#taxColumnData').addClass('hide');
                $('#taxColumnDataValue').addClass('hide');
                
            }

            $("#rv_detail_modal").modal({backdrop: "static"});
            $('#income_add_table tbody tr').not(':first').remove();
        }
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
            $(ths).closest('tr').find('#edit_itemAutoID').val('');
        }

    }

    function initializeitemTypeahead(id) {
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        /**var item = new Bloodhound({
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
            $(this).closest('tr').find('.itemAutoID').val(datum.itemAutoID);
            fetch_sales_price(datum.companyLocalSellingPrice,this,datum.defaultUnitOfMeasureID,datum.itemAutoID);
            fetch_related_uom_id(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID, this);
        });*/

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Receipt_voucher/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
            onSelect: function (suggestion) {
                //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);

                // if(itemBatchPolicy==1){
                //       getItemBatchDetails(suggestion.itemAutoID,id);
                // }

                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    $('#f_search_' + id).closest('tr').find('.itemcatype').val(suggestion.mainCategory);
                }, 200);
                fetch_sales_price(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);

                if ($('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val()) {
                    fetch_rv_warehouse_item_receipt(suggestion.itemAutoID, this, $('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val());
                }

                $(this).closest('tr').find('.quantityRequested').focus();
                $(this).closest('tr').css("background-color", 'white');
                $(this).closest('tr').find('.wareHouseAutoID').val('').change();
                checkitemavailable(this);
                fetch_line_tax_and_vat(suggestion.itemAutoID, this);
                if(suggestion.revanueGLCode==null || suggestion.revanueGLCode=='' || suggestion.revanueGLCode==0){
                    $('#f_search_' + id).val('');
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                    }, 200);
                    myAlert('w','Revenue GL code not assigned for selected item');
                }
                if(suggestion.mainCategory=='Service'){
                    $(this).closest('tr').find('.wareHouseAutoID').removeAttr('onchange');
                }else{
                    $(this).closest('tr').find('.wareHouseAutoID').attr('onchange', 'checkitemavailable(this)');
                }
                // check_item_not_approved_in_document(suggestion.itemAutoID,id);
                check_item_not_approved_in_document(suggestion.itemAutoID,id,'');
            }
        });
    }

    function initializeitemTypeahead_edit() {
        /** var item = new Bloodhound({
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
            $('#edit_itemAutoID').val(datum.itemAutoID);
            fetch_sales_price_edit(datum.companyLocalSellingPrice,datum.defaultUnitOfMeasureID,datum.itemAutoID);
            fetch_related_uom_id_edit(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID);
        }); */

        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Receipt_voucher/fetch_itemrecode_po/?column='+ 'allowedtoSellYN',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#edit_itemAutoID').val(suggestion.itemAutoID);
                    $('#edit_itemcate').val(suggestion.mainCategory);
                    $('#edit_quantityRequested').val(0);
                }, 200);
                fetch_sales_price_edit(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                edit_fetch_line_tax_and_vat(suggestion.itemAutoID, this);
                $(this).closest('tr').find('#edit_quantityRequested').focus();
                $(this).closest('tr').find('#edit_wareHouseAutoID').val('').change();

                if(suggestion.revanueGLCode==null || suggestion.revanueGLCode=='' || suggestion.revanueGLCode==0){
                    $('#edit_itemAutoID').closest('tr').find('.itemAutoID').val('');
                    $('#edit_itemAutoID').val('');
                    $('#edit_itemAutoID').closest('tr').css("background-color", '#ffb2b2 ');
                    myAlert('w','Revenue GL code not assigned for selected item')
                }

                if(suggestion.mainCategory=='Service'){
                    $('#edit_wareHouseAutoID').removeAttr('onchange');
                }else{
                    $('#edit_wareHouseAutoID').attr('onchange', 'editstockwhreceiptvoucher(this),load_batch_number_single_edit_r_voucher(this)');
                }
                // check_item_not_approved_in_document(suggestion.itemAutoID);
                check_item_not_approved_in_document(suggestion.itemAutoID,receiptVoucherAutoId,'RV');
            }
        });
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

                /*var mySelect = $('#UnitOfMeasureID');*/
                //var mySelect = $(element).closest('tr').find('input[name="UnitOfMeasureID"]')
                var mySelect = $(element).parent().closest('tr').find('.umoDropdown');

                mySelect.append($('<option></option>').val('').html('Select  UOM'))
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value)
                        /*$("#UnitOfMeasureID").val(select_value);*/
                        /*$('#invoice_item_detail_form').bootstrapValidator('revalidateField', 'UnitOfMeasureID');*/
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function getItemBatchDetails(itemAutoID,id,wareHouseAutoID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemId': itemAutoID,'wareHouseAutoID':wareHouseAutoID},
            url: "<?php echo site_url('Inventory/fetch_batch_details_byId'); ?>",
            success: function (data) {
                $('#batch_number_'+id).empty();
                var mySelect = $('#batch_number_'+id);
                mySelect.append($('<option></option>').val('').html('<?php echo "Select Batch"?>'));
                /*Select batch*/
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['batchNumber']).html(text['batchNumber']+' - - '+text['batchExpireDate']));
                    });
                    
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

    function save_inv_base_items(type = '') {
        var rebetval = $('#rebetval').val();
        if((rebetval ==1)){
            var selected = [];
            var amount = [];
            var INVamount = [];
            var rebetamount = [];
            $('#table_body input:checked').each(function () {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
                INVamount.push($('#Invoiceamount_' + $(this).val()).val());
                rebetamount.push($('#rebetamnt_' + $(this).val()).val());
            });
            if (!jQuery.isEmptyObject(selected)) {
                var totalsettlement = $('#amount_total').val();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'invoiceAutoID': selected,'type':type, 'receiptVoucherAutoId': receiptVoucherAutoId, 'amount': amount,'settlementAmount':totalsettlement,'INVamount':INVamount,'rebetamount':rebetamount},
                    url: "<?php echo site_url('Receipt_voucher/save_inv_base_items'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#inv_base_modal').modal('hide');
                        $('#sup_inv_base_modal').modal('hide');
                        refreshNotifications(true);
                        setTimeout(function () {
                            fetch_details(1);
                        }, 300);
                    }, error: function () {
                        $('#inv_base_modal').modal('hide');
                        $('#sup_inv_base_modal').modal('hide');
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            }
        }else{
            var selected = [];
            var amount = [];
            $('#table_body input:checked').each(function () {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
            });
            if (!jQuery.isEmptyObject(selected)) {
                var totalsettlement = $('#amount_total').val();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'invoiceAutoID': selected,'type':type,'receiptVoucherAutoId': receiptVoucherAutoId, 'amount': amount,'settlementAmount':totalsettlement},
                    url: "<?php echo site_url('Receipt_voucher/save_inv_base_items'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#inv_base_modal').modal('hide');
                        $('#sup_inv_base_modal').modal('hide');
                        refreshNotifications(true);
                        setTimeout(function () {
                            fetch_details(1);
                        }, 300);
                    }, error: function () {
                        $('#inv_base_modal').modal('hide');
                        $('#sup_inv_base_modal').modal('hide');
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            }
        }

    }

    // function select_check_box(data,id){
    //     $( "#check_"+id ).prop( "checked", false)
    //     if(data.value > 0){
    //         $( "#check_"+id ).prop( "checked", true);
    //     }
    // }

    function select_check_box(data, id, total) {
        $("#check_" + id).prop("checked", false);
        if (data.value > 0) {
            if (total >= data.value) {
                $("#check_" + id).prop("checked", true);
            } else {
                $("#check_" + id).prop("checked", false);
                $("#amount_" + id).val('');
                /*$("#total_invoice_total").text('0.00');*/
                myAlert('w', '<?php echo $this->lang->line('accounts_receivable_tr_you_canot_grv_bal_am');?>');
                total_calculation();
                /*You can not enter an invoice amount greater than selected GRV Balance Amount*/
            }
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
            $('#percentage').val(((parseFloat(discount_amount) / tax_total) * 100).toFixed(2));
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
    $('#inv_base_modal').on('shown.bs.modal', function () {
        $('#amount_total').focus();
    })
    function delete_tax(id, value) {
        if (receiptVoucherAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
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
                        data: {'taxDetailAutoID': id},
                        url: "<?php echo site_url('Receipt_voucher/delete_tax_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            setTimeout(function () {
                                fetch_details(1);
                                ;
                            }, 300);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function fetch_rv_details(tab) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'receiptVoucherAutoId': receiptVoucherAutoId},
            url: "<?php echo site_url('Receipt_voucher/fetch_rv_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                tax_total = 0;
                transactionDecimalPlaces = 2;
                $('#gl_table_body,#gl_table_body_expense,#item_table_body,#invoice_table_body,#sup_invoice_table_body,#advance_table_body,#creditNote_table_body').empty();
                $('#item_table_tfoot,#invoice_table_tfoot,#advance_table_tfoot,#gl_table_tfoot,#gl_table_expense_tfoot,#creditNote_table_tfoot').empty();
                if (jQuery.isEmptyObject(data['detail'])) {
                    $("#customerID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                    $("#vouchertype").prop("disabled", false);
                    $("#addcustomer").prop("disabled", false);
                    currencyID = null;
                    if(isGroupWiseTax == 1){ 
                        $('#gl_table_body,#item_table_body,#advance_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $('#gl_table_body,#item_table_body,#advance_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>');
                    }
                    $("#editallbtn").addClass("hidden");
                } else {
                    $("#customerID").prop("disabled", true);
                    $("#transactionCurrencyID").prop("disabled", true);
                    $("#vouchertype").prop("disabled", true)
                    $("#addcustomer").prop("disabled", true);
                    $("#editallbtn").removeClass("hidden");
                    x = 1;
                    y = 1;
                    z = 1;
                    //data['detail['currency']['transactionCurrencyDecimalPlaces'];
                    LocalDecimalPlaces = 2;//data['detail['currency']['companyLocalCurrencyDecimalPlaces'];
                    partyDecimalPlaces = 2;//data['detail['currency']['customerCurrencyDecimalPlaces'];
                    gl_trans_amount = 0;
                    gl_trans_amount_expense = 0;
                    var advance_footerspan = 2;
                    gl_local_amount = 0;
                    gl_party_amount = 0;
                    po_trans_amount = 0;
                    po_local_amount = 0;
                    po_party_amount = 0;
                    item_trans_amount = 0;
                    item_local_amount = 0;
                    item_party_amount = 0;
                    invoice_amount = 0;
                    sup_invoice_amount = 0;
                    due_amount = 0;
                    paid_amount = 0;
                    sup_paid_amount = 0;
                    Balance_amount = 0;
                    cdTotal_amount = 0;
                    $.each(data['detail'], function (key, value) {
                        var wareloc='';

                        if (value['mainCategory'] == 'Service') {
                            wareloc='';
                        }else{
                            wareloc=value['wareHouseLocation'];
                        }

                        if (value['type'] == 'Item') {

                            $('#item_table_tfoot').empty();

                            var footerspan = 7;
                            var taxamount = 0;
                            var taxView = 0;
                            if(isGroupWiseTax == 1){ 
                                footerspan = 8;
                                taxamount =  value['taxAmount'];
                                if(taxamount > 0) {
                                    taxView = '<td class="text-right"><a onclick="open_tax_dd(\'\',' + receiptVoucherAutoId + ',\'RV\',' + currency_decimal +', '+ value['receiptVoucherDetailAutoID'] +', \'srp_erp_customerreceiptdetail\', \'receiptVoucherDetailAutoID\')">' + parseFloat(taxamount).formatMoney(currency_decimal, '.', ',') + '</a></td>';
                                } else {
                                    taxView = '<td class="text-right">0.00</td>';
                                }
                            }
                            var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
                            if (value['isSubitemExist'] == 1) {
                                var colour = 'color: #dad835 !important';
                                colour = '';

                                if(itemBatchPolicy==1){
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber']+'</td><td>' + value['Itemdescriptionpartno'] + ' - ' + wareloc + '</td><td>' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>'+taxView+'<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['receiptVoucherDetailAutoID'] + ',\'RV\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; |&nbsp;&nbsp;<a onclick="edit_item(' + value['receiptVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }else{
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['Itemdescriptionpartno'] + ' - ' + wareloc + '</td><td>' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>'+taxView+'<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['receiptVoucherDetailAutoID'] + ',\'RV\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; |&nbsp;&nbsp;<a onclick="edit_item(' + value['receiptVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }
                                
                            } else {

                                if(itemBatchPolicy==1){
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber']+'</td><td>' + value['Itemdescriptionpartno'] + ' - ' + wareloc + '</td><td>' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>'+taxView+'<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['receiptVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }else{
                                    string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['Itemdescriptionpartno'] + ' - ' + wareloc + '</td><td>' + value['remarks'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>'+taxView+'<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['receiptVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';  
                                }
                            }
                            $('#item_table_body').append(string);

                            //$('#item_table_body').append();

                            x++;
                            item_trans_amount += (parseFloat(value['transactionAmount']));
                            item_local_amount += (parseFloat(value['companyLocalAmount']));
                            item_party_amount += (parseFloat(value['customerAmount']));
                            tax_total += (parseFloat(value['transactionAmount']));

                            $('#item_table_tfoot').append('<tr><td colspan="'+footerspan+'" class="text-right"> Total </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                            //<a onclick="edit_item('+value['receiptVoucherDetailAutoID']+',\''+value['itemDescription']+'\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                        }else if (value['type'] == 'Invoice') {
                            $('.tab_3_Item').removeClass('hide');
                            if(value['detailInvoiceType'] == 'SUP'){
                                $('#sup_invoice_table_tfoot').empty();
                                $('#sup_invoice_table_body').append('<tr><td>' + y + '</td><td>' + value['invoiceCode'] + '</td><td>' + value['referenceNo'] + '</td><td class="text-center">' + value['invoiceDate'] + '</td><td class="text-right">' + parseFloat(value['Invoice_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['due_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ', 1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                sup_paid_amount += (parseFloat(value['transactionAmount']));
                                y++;
                                
                                sup_invoice_amount += (parseFloat(value['Invoice_amount']));
                                due_amount += (parseFloat(value['due_amount']));

                                Balance_amount += (parseFloat(value['balance_amount']));
                                $('#sup_invoice_table_tfoot').append('<tr><td colspan="6" class="text-right"> Total Paid </td><td class="text-right total">' + parseFloat(sup_paid_amount).formatMoney(currency_decimal, '.', ',') + '</td><td colspan="2"></td></tr>');
                            }else{
                                $('#invoice_table_tfoot').empty();
                                <?php if($rebate==1){ ?>
                                $('#invoice_table_body').append('<tr><td>' + y + '</td><td>' + value['invoiceCode'] + '</td><td>' + value['referenceNo'] + '</td><td class="text-center">' + value['invoiceDate'] + '</td><td class="text-right">' + parseFloat(value['Invoice_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['due_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']-value['rebateAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['rebateAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ', 1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                paid_amount += (parseFloat(value['transactionAmount'])-value['rebateAmount']);
                                <?php } else { ?>
                                $('#invoice_table_body').append('<tr><td>' + y + '</td><td>' + value['invoiceCode'] + '</td><td>' + value['referenceNo'] + '</td><td class="text-center">' + value['invoiceDate'] + '</td><td class="text-right">' + parseFloat(value['Invoice_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['due_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ', 1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                paid_amount += (parseFloat(value['transactionAmount']));
                                <?php } ?>
                                y++;
                                invoice_amount += (parseFloat(value['Invoice_amount']));
                                due_amount += (parseFloat(value['due_amount']));

                                Balance_amount += (parseFloat(value['balance_amount']));
                                <?php if($rebate==1){ ?>
                                $('#invoice_table_tfoot').append('<tr><td colspan="6" class="text-right"> Total Paid </td><td class="text-right total">' + parseFloat(paid_amount).formatMoney(currency_decimal, '.', ',') + '</td><td colspan="3"></td></tr>');
                                <?php } else { ?>
                                $('#invoice_table_tfoot').append('<tr><td colspan="6" class="text-right"> Total Paid </td><td class="text-right total">' + parseFloat(paid_amount).formatMoney(currency_decimal, '.', ',') + '</td><td colspan="2"></td></tr>');
                                <?php } ?>
                                
                            }
                        
                     
                            //<a onclick="edit_item('+value['receiptVoucherDetailAutoID']+',\''+value['GLDescription']+'\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                        }else if (value['type'] == 'Advance') {
                            advance_footerspan = 2;
                            var taxamount = 0;
                            var taxView = 0;
                            if(isGroupWiseTax == 1){
                                advance_footerspan = 4;
                                taxamount =  value['taxAmount'];
                                if(taxamount > 0) {
                                    taxView = '<td>'+value['contractCode']+'</td><td class="text-right"><a onclick="open_tax_dd(\'\',' + receiptVoucherAutoId + ',\'RV\',' + currency_decimal +', '+ value['receiptVoucherDetailAutoID'] +', \'srp_erp_customerreceiptdetail\', \'receiptVoucherDetailAutoID\',0,1)">' + parseFloat(taxamount).formatMoney(currency_decimal, '.', ',') + '</a></td>';
                                } else {
                                    taxView = '<td>'+value['contractCode']+'</td><td class="text-right">0.00</td>';
                                }
                            }
                            $('.tab_4_Item').removeClass('hide');
                            $('#advance_table_body').append('<tr><td>' + y + '</td><td>' + value['comment'] + '</td>'+taxView+'<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            y++;
                            //<td class="text-right">' + parseFloat(value['companyLocalAmount']).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['customerAmount']).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                            po_trans_amount += (parseFloat(value['transactionAmount']));
                            //po_local_amount += (parseFloat(value['companyLocalAmount']));
                            //po_party_amount += (parseFloat(value['customerAmount']));
                            //<a onclick="edit_advance_item('+value['receiptVoucherDetailAutoID']+',\''+value['GLDescription']+'\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                        }else if (value['type'] == 'creditnote' || value['type'] == 'SLR') {
                            $('.tab_5_Item').removeClass('hide');
                            $('#creditNote_table_body').append('<tr><td>' + z + '</td><td>' + value['invoiceCode'] + '</td><td>' + value['referenceNo'] + '</td><td class="text-center">' + value['invoiceDate'] + '</td><td class="text-right">' + parseFloat(value['Invoice_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['due_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',5);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            z++;
                            /*invoice_amount += (parseFloat(value['Invoice_amount']));
                             due_amount += (parseFloat(value['due_amount']));*/
                            cdTotal_amount += (parseFloat(value['transactionAmount']));
                            //Balance_amount += (parseFloat(value['balance_amount']));

                        } else {
                            if(value['type'] == 'GL'){
                                $('#gl_table_tfoot').empty();
                            }else if(value['type'] == 'EXGL'){
                                $('#gl_table_expense_tfoot').empty();
                            }
                           
                            var transamnt=parseFloat(value['transactionAmount'])+parseFloat(value['discountAmount']);
                            var footerspan = 6;
                            var taxamount = 0;
                            var taxView = 0;
                            if(isGroupWiseTax == 1){ 
                                footerspan = 6;
                                if(value['type'] == 'GL'){
                                    footerspan = 7;
                                }
                               
                                taxamount =  value['taxAmount'];
                                if(taxamount > 0) {
                                    transamnt = transamnt - taxamount;
                                    taxView = '<td class="text-right"><a onclick="open_tax_dd(\'\',' + receiptVoucherAutoId + ',\'RV\',' + currency_decimal +', '+ value['receiptVoucherDetailAutoID'] +', \'srp_erp_customerreceiptdetail\', \'receiptVoucherDetailAutoID\')">' + parseFloat(taxamount).formatMoney(currency_decimal, '.', ',') + '</a></td>';
                                } else {
                                    taxView = '<td class="text-right">0.00</td>';
                                }
                            }
                            
                            if(value['type'] == 'GL'){
                                $('#gl_table_body').append('<tr><td>' + y + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' +  value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">('+ parseFloat(value['discountPercentage']).formatMoney(2, '.', ',') +' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>'+taxView+'<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_income_item(' + value['receiptVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            }else if(value['type'] == 'EXGL'){
                                $('#gl_table_body_expense').append('<tr><td>' + y + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' +  value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">('+ parseFloat(value['discountPercentage']).formatMoney(2, '.', ',') +' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>'+'<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_income_item(' + value['receiptVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            }
                            
                           
                            y++;
                            //<td class="text-right">' + parseFloat(value['companyLocalAmount']).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['customerAmount']).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                            if(value['type'] == 'EXGL'){
                                gl_trans_amount_expense += (parseFloat(value['transactionAmount']));
                                tax_total_expense += (parseFloat(value['transactionAmount']));
                            }else{
                                gl_trans_amount += (parseFloat(value['transactionAmount']));
                                tax_total += (parseFloat(value['transactionAmount']));
                            }

                           
                            //gl_local_amount += (parseFloat(value['companyLocalAmount']));
                            //gl_party_amount += (parseFloat(value['customerAmount']));
                            if(value['type'] == 'GL'){
                                $('#gl_table_tfoot').append('<tr><td colspan="'+footerspan+'" class="text-right"><?php echo $this->lang->line('common_total');?> <!--Total--> </td><td class="text-right total">' + parseFloat(gl_trans_amount).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                            }else if(value['type'] == 'EXGL'){
                                $('#gl_table_expense_tfoot').append('<tr><td colspan="'+footerspan+'" class="text-right"><?php echo $this->lang->line('common_total');?> <!--Total--> </td><td class="text-right total">' + parseFloat(gl_trans_amount_expense).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                            }
                            
                            //<td class="text-right total">' + parseFloat(gl_local_amount).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right total">' + parseFloat(gl_party_amount).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                            //<a onclick="edit_item('+value['receiptVoucherDetailAutoID']+',\''+value['GLDescription']+'\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                        }
                    });
                    $('#advance_table_tfoot').append('<tr><td colspan="'+advance_footerspan+'" class="text-right"> <?php echo $this->lang->line('common_total');?><!--Total--> </td><td class="text-right total">' + parseFloat(po_trans_amount).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                    $('#creditNote_table_tfoot').append('<tr><td colspan="6" class="text-right">Total Paid </td><td class="text-right total">' + parseFloat(cdTotal_amount).formatMoney(currency_decimal, '.', ',') + '</td><td colspan="2"></td></tr>');
                    //<td class="text-right total">' + parseFloat(po_local_amount).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right total">' + parseFloat(po_party_amount).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                }
                if (tab == 1) {
                    $('.nav-tabs a[href="#tab_3"]').tab('show')
                }
                if (tab == 2) {
                    $('.nav-tabs a[href="#tab_2"]').tab('show')
                }
                if (tab == 3) {
                    $('.nav-tabs a[href="#tab_1"]').tab('show')
                }
                if (tab == 4) {
                    $('.nav-tabs a[href="#tab_4"]').tab('show')
                }

                if (tab == 5) {
                    $('.nav-tabs a[href="#tab_5"]').tab('show')
                }

                $('#tax_tot').text('Tax Applicable Amount ( ' + parseFloat(tax_total).formatMoney(currency_decimal, '.', ',') + ' )');
                $('#tax_table_body_recode,#tax_table_footer').empty();
                if (jQuery.isEmptyObject(data['tax_detail'])) {
                    $('#tax_table_body_recode').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                } else {
                    x = 1;
                    t_total = 0;
                    $.each(data['tax_detail'], function (key, value) {
                        $('#tax_table_body_recode').append('<tr><td>' + x + '</td><td>' + value['taxShortCode'] + '</td><td>' + value['taxDescription'] + '</td><td class="text-right">' + value['taxPercentage'] + '% </td><td class="text-right">' + parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_tax(' + value['taxDetailAutoID'] + ',\'' + value['taxShortCode'] + '\',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                        x++;
                        t_total += parseFloat((parseFloat(value['taxPercentage']) / 100) * tax_total);
                    });
                    if (t_total > 0) {
                        $('#tax_table_footer').append('<tr><td colspan="4" class="text-right"><?php echo $this->lang->line('common_tax_total');?><!--Tax Total--> </td><td class="text-right total">' + parseFloat(t_total).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                    }
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

    function delete_item_direct(id, tab) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
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
                    data: {'receiptVoucherDetailAutoID': id},
                    url: "<?php echo site_url('Receipt_voucher/delete_item_direct'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        setTimeout(function () {
                            //fetch_rv_details(tab);
                            //fetch_details(tab);
                            fetch_details(tab);
                        }, 300);
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function rv_advance_detail_modal() {
        if (receiptVoucherAutoId) {
            $('#rv_advance_detail_form')[0].reset();
            $('#advance_add_table tbody tr').not(':first').remove();
            $("#rv_advance_detail_modal").modal({backdrop: "static"});
        }
    }

    function add_more_income() {
        $('select.select2').select2('destroy');
        var appendData = $('#income_add_table tbody tr:first').clone();

        appendData.find('input,select,textarea').val('')

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $("#income_add_table").append(appendData);
        $(".select2").select2();
        number_validation();
    }

    function saveDirectRvDetails() {
        var data = $('#rv_detail_form').serializeArray()
        data.push({'name': 'receiptVoucherAutoId', 'value': receiptVoucherAutoId})
        data.push({'name': 'receiptVoucherDetailAutoID', 'value': receiptVoucherDetailAutoID})
        $('select[name="gl_code[]"] option:selected').each(function () {
            data.push({'name': 'gl_code_des[]', 'value': $(this).text()})
        })

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Receipt_voucher/save_direct_rv_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                receiptVoucherDetailAutoID = null;
                refreshNotifications(true);
                stopLoad();

                if (data != false) {
                    setTimeout(function () {
                        fetch_rv_details();
                    }, 300);
                    $('#rv_detail_modal').modal('hide');
                    $('#rv_detail_form')[0].reset();
                    $('.select2').select2('')
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    var batch_number=0;

    function add_more_item() {
        search_id += 1;
        batch_number += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#item_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        //appendData.find('.umoDropdown').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');

        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';

        if(itemBatchPolicy==1){
            appendData.find('.b_number').empty();
            appendData.find('.b_number').attr('id', 'batch_number_' + search_id);
            appendData.find('.b_number').attr('name', 'batch_number[' + batch_number+'][]');
        }

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#item_add_table').append(appendData);
        var lenght = $('#item_add_table tbody tr').length - 1;
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        $(".select2").select2();
        number_validation();
        initializeitemTypeahead(search_id)
    }

    function saveRvItemDetail() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#rv_item_detail_form').serializeArray();
        if (receiptVoucherAutoId) {
            data.push({'name': 'receiptVoucherAutoId', 'value': receiptVoucherAutoId});
            data.push({'name': 'receiptVoucherDetailAutoID', 'value': receiptVoucherDetailAutoID});

            $('select[name="wareHouseAutoID[]"] option:selected').each(function () {
                data.push({'name': 'wareHouse[]', 'value': $(this).text()})
            })

            $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
                data.push({'name': 'uom[]', 'value': $(this).text()})
            });
            $('.itemAutoID').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });
            $('.wareHouseAutoID ').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });
            $('.quantityRequested ').each(function () {
                if (this.value == '' || this.value == 0) {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Receipt_voucher/save_rv_item_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {
                    receiptVoucherDetailAutoID = null;
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#rv_item_detail_form')[0].reset();
                        setTimeout(function () {
                            fetch_rv_details(2);
                        }, 300);
                        $('#rv_item_detail_modal').modal('hide');
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    myAlert(data[0], data[1]);
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


    function add_more_advance() {
        $('select.select2').select2('destroy');
        var appendData = $('#advance_add_table tbody tr:first').clone();
        appendData.find('input,select,textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $("#advance_add_table").append(appendData);
        $(".select2").select2();
        number_validation();
    }

    function saveRvAdvanceDetail() {
        $('.item_text_advance').attr('disabled', false);
        var data = $('#rv_advance_detail_form').serializeArray();
        data.push({'name': 'receiptVoucherAutoId', 'value': receiptVoucherAutoId});
        data.push({'name': 'receiptVoucherDetailAutoID', 'value': receiptVoucherDetailAutoID});

        /*$('select[name="po_code[]"] option:selected').each(function () {
         data.push({'name': 'po_des[]', 'value': $(this).text()})
         })*/

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Receipt_voucher/save_rv_advance_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                payVoucherDetailAutoID = null;
                refreshNotifications(true);
                stopLoad();
                $('.item_text_advance').attr('disabled', false);
                if (data != false) {
                    setTimeout(function () {
                        fetch_rv_details(4);
                    }, 300);
                    $('#rv_advance_detail_modal').modal('hide');
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_sales_price(salesprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: receiptVoucherAutoId,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_customerreceiptmaster',
                primaryKey: 'receiptVoucherAutoId',
                customerAutoID: '<?php echo $master['customerID']; ?>'
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

    function fetch_sales_price_edit(salesprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: receiptVoucherAutoId,
                salesprice: salesprice,
                unitOfMeasureID: unitOfMeasureID,
                itemAutoID: itemAutoID,
                tableName: 'srp_erp_customerreceiptmaster',
                primaryKey: 'receiptVoucherAutoId',
                customerAutoID: '<?php echo $master['customerID']; ?>'
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

    function edit_income_item(id) {
        if (receiptVoucherAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('accounts_receivable_common_you_want_to_edit_this_file');?>", /*You want to edit this file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit');?>", /*Edit*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'receiptVoucherDetailAutoID': id},
                        url: "<?php echo site_url('Receipt_voucher/fetch_income_all_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            receiptVoucherDetailAutoID = data['receiptVoucherDetailAutoID'];
                            projectID_income = data['projectID'];
                            select_gl_VAT_value = data['taxCalculationformulaID'];
                            projectcategory = data['project_categoryID'];
                            projectsubcat = data['project_subCategoryID'];
                            $('#edit_gl_code').val(data['GLAutoID']).change();
                            $('#edit_description').val(data['description']);
                            if(data['taxAmount'] != null) {
                                $('#edit_amount').val((parseFloat(data['transactionAmount'])+parseFloat(data['discountAmount']) - parseFloat(data['taxAmount'])).toFixed(currency_decimal));
                            } else {
                                $('#edit_amount').val((parseFloat(data['transactionAmount'])+parseFloat(data['discountAmount'])).toFixed(currency_decimal));
                            }
                            $('#discountPercentage_edit').val(parseFloat(data['discountPercentage']).toFixed(2));
                            $('#discountAmount_edit').val(parseFloat(data['discountAmount']).toFixed(currency_decimal));
                            $('#Netamount_edit').val(parseFloat(data['transactionAmount']).toFixed(currency_decimal));
                            $('#edit_segment_gl').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            $('#gl_text_type_edit').val(data['taxCalculationformulaID']).change();
                            $('#gl_type_edit').val(data['type']);

                            if(data['type'] == 'GL'){
                                $('#edittaxColumn').removeClass('hide');
                                $('#edittaxColumnData').removeClass('hide');
                                $('#edittaxColumnDataValue').removeClass('hide');
                            }else if(data['type'] == 'EXGL'){
                                $('#edittaxColumn').addClass('hide');
                                $('#edittaxColumnData').addClass('hide');
                                $('#edittaxColumnDataValue').addClass('hide');
                            }
                            
                            load_gl_line_tax_amount_edit(data['GLAutoID']);
                            $("#edit_rv_income_detail_modal").modal({backdrop: "static"});
                            stopLoad();
                            //refreshNotifications(true);
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function updateDirectRvDetails() {
        var data = $('#edit_rv_income_detail_form').serializeArray();
        data.push({'name': 'receiptVoucherAutoId', 'value': receiptVoucherAutoId});
        data.push({'name': 'receiptVoucherDetailAutoID', 'value': receiptVoucherDetailAutoID});
        data.push({'name': 'gl_code_des', 'value': $('#edit_gl_code option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Receipt_voucher/update_direct_rv_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    receiptVoucherDetailAutoID = null;
                    setTimeout(function () {
                        fetch_rv_details();
                    }, 300);
                    $('#edit_rv_income_detail_modal').modal('hide');
                    $('#edit_rv_income_detail_form')[0].reset();
                    $('.select2').select2('')

                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function edit_item(id) {
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        $("#edit_wareHouseAutoID").val(null).trigger("change");
        $('#edit_rv_item_detail_form')[0].reset();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>", /*You want to edit this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_edit');?>", /*Edit*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'receiptVoucherDetailAutoID': id},
                    url: "<?php echo site_url('Receipt_voucher/fetch_income_all_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        currentEditWareHouseAutoID=data['wareHouseAutoID'];
                        currentEditTextBatchData=data['batchNumber'];

                        //batch number update
                        if(itemBatchPolicy==1){
                            var textBatchData=data['batchNumber'];
                            $.ajax({
                                async: true,
                                type: 'post',
                                dataType: 'json',
                                data: {'itemId': data['itemAutoID'],'wareHouseAutoID':data['wareHouseAutoID']},
                                url: "<?php echo site_url('Inventory/fetch_batch_details_byId'); ?>",
                                success: function (data) {
                                    $('#batch_number_edit').empty();
                                    var mySelect = $('#batch_number_edit');
                                    //mySelect.append($('<option></option>').val('').html('<?php echo "Select Batch"?>'));
                                    /*Select batch*/
                                    if (!jQuery.isEmptyObject(data)) {
                                        $.each(data, function (val, text) {
                                            mySelect.append($('<option></option>').val(text['batchNumber']).html(text['batchNumber']));
                                        });

                                        var optionsToSelect = textBatchData.split(",");
                                        var select = document.getElementById( 'batch_number_edit' );

                                        for ( var i = 0, l = select.options.length, o; i < l; i++ )
                                        {
                                            o = select.options[i];
                                            if ( optionsToSelect.indexOf( o.text ) != -1 )
                                            {
                                                o.selected = true;
                                            }
                                        }
                                        
                                    }
                                }, error: function () {
                                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                                }
                            });

                        }
                        //pv_item_detail_modal();
                        receiptVoucherDetailAutoID = data['receiptVoucherDetailAutoID'];
                        select_VAT_value = data['taxCalculationformulaID'];
                        projectID_item = data['projectID'];
                        projectcategory = data['project_categoryID'];
                        projectsubcat = data['project_subCategoryID'];
                        load_segmentBase_projectID_itemEdit(data['segmentID']);
                        $('#search').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - "+data['seconeryItemCode']);
                        fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                        $('#edit_quantityRequested').val(data['requestedQty']);
                        $('#conversionRateRVEdit').val(data['conversionRateUOM']);
                        $('#editNetAmount').val(data['transactionAmount']);
                        $('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount'])));
                        $('#edit_search_id').val(data['itemSystemCode']);
                        $('#edit_itemSystemCode').val(data['itemSystemCode']);
                        $('#edit_itemAutoID').val(data['itemAutoID']);
                        $('#edit_itemDescription').val(data['itemDescription']);
                        $('#edit_wareHouseAutoID').val(data['wareHouseAutoID']).change();
                        $('#edit_comment').val(data['comment']);
                        $('#edit_remarks').val(data['remarks']);

                        if(data['type'] == 'GL'){
                            $('#edittaxColumnExpense').removeClass('hide');
                            $('#edittaxColumnDataExpense').removeClass('hide');
                            $('#edittaxColumnDataValueExpense').removeClass('hide');
                        }else if(data['type'] == 'EXGL'){
                            $('#edittaxColumnExpense').addClass('hide');
                            $('#edittaxColumnDataExpense').addClass('hide');
                            $('#edittaxColumnDataValueExpense').addClass('hide');
                        }
                            

                        edit_fetch_line_tax_and_vat(data['itemAutoID']);
                        $("#edit_rv_item_detail_modal").modal({backdrop: "static"});
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function load_batch_number_single_edit_r_voucher(){
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        var itemAutoID = $('#edit_itemAutoID').val();
        var wareHouseAutoID = $('#edit_wareHouseAutoID').val();

        if(currentEditWareHouseAutoID!='' && currentEditTextBatchData!=''){
            if(currentEditWareHouseAutoID ==wareHouseAutoID){

                    var textBatchData=currentEditTextBatchData;
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'itemId': itemAutoID,'wareHouseAutoID':wareHouseAutoID},
                        url: "<?php echo site_url('Inventory/fetch_batch_details_byId'); ?>",
                        success: function (data) {
                            $('#batch_number_edit').empty();
                            var mySelect = $('#batch_number_edit');
                            //mySelect.append($('<option></option>').val('').html('<?php echo "Select Batch"?>'));
                            /*Select batch*/
                            if (!jQuery.isEmptyObject(data)) {
                                $.each(data, function (val, text) {
                                    mySelect.append($('<option></option>').val(text['batchNumber']).html(text['batchNumber']));
                                });

                                var optionsToSelect = textBatchData.split(",");
                                var select = document.getElementById( 'batch_number_edit' );

                                for ( var i = 0, l = select.options.length, o; i < l; i++ )
                                {
                                    o = select.options[i];
                                    if ( optionsToSelect.indexOf( o.text ) != -1 )
                                    {
                                        o.selected = true;
                                    }
                                }
                                
                            }
                        }, error: function () {
                            swal("Cancelled", "Your " + value + " file is safe :)", "error");
                        }
                    });


            }else{
                
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'itemId': itemAutoID,'wareHouseAutoID':wareHouseAutoID},
                    url: "<?php echo site_url('Inventory/fetch_batch_details_byId'); ?>",
                    success: function (data) {
                        $('#batch_number_edit').empty();
                        var mySelect = $('#batch_number_edit');
                        //mySelect.append($('<option></option>').val('').html('<?php echo "Select Batch"?>'));
                        /*Select batch*/
                        if (!jQuery.isEmptyObject(data)) {
                            $.each(data, function (val, text) {
                                mySelect.append($('<option></option>').val(text['batchNumber']).html(text['batchNumber']));
                            });
                            
                        }
                    }, error: function () {
                        swal("Cancelled", "Your " + value + " file is safe :)", "error");
                    }
                });
                
            }
        }
    }

    function update_Rv_ItemDetail() {
        $('#edit_UnitOfMeasureID').prop("disabled", false);
        var data = $('#edit_rv_item_detail_form').serializeArray();
        if (receiptVoucherAutoId) {
            data.push({'name': 'receiptVoucherAutoId', 'value': receiptVoucherAutoId});
            data.push({'name': 'receiptVoucherDetailAutoID', 'value': receiptVoucherDetailAutoID});
            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
            data.push({'name': 'wareHouse', 'value': $('#edit_wareHouseAutoID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Receipt_voucher/update_rv_item_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        receiptVoucherDetailAutoID = null;
                        setTimeout(function () {
                            fetch_rv_details();
                        }, 300);
                        $('#edit_rv_item_detail_modal').modal('hide');
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    myAlert(data[0], data[1]);
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

    function load_segmentBase_projectID_incomeEdit(segment) {
        var type = 'income';
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase"); ?>',
            dataType: 'html',
            data: {segment: segment.value, type: type},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#edit_div_projectID_income').html(data);
                $('.select2').select2();
                if (projectID_income) {
                    $("#projectID_income").val(projectID_income).change()
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function load_segmentBase_projectID_item() {
        var segment = $('#segment').val();
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

    function load_segmentBase_projectID_itemEdit(segment, selectValue) {
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
                if (projectID_item) {
                    $("#projectID_item").val(projectID_item).change()
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function change_amount(field, val) {
        var quantityRequested = 0;
        var estimatedAmount = 0;
        var netAmount = 0;
        if (val == 1) {
            quantityRequested = $(field).closest('tr').find('.quantityRequested').val();
            estimatedAmount = $(field).closest('tr').find('.estimatedAmount').val();
            var totamt = quantityRequested * estimatedAmount;
            $(field).closest('tr').find('.netAmount').val(parseFloat(totamt).toFixed(currency_decimal));
        } else {
            quantityRequested = $(field).closest('tr').find('.quantityRequested').val();
            netAmount = $(field).closest('tr').find('.netAmount').val();
            var unitamt = netAmount / quantityRequested;

            if (unitamt != 'Infinity') {
                $(field).closest('tr').find('.estimatedAmount').val(parseFloat(unitamt).toFixed(currency_decimal));
            }
            else {
                $(field).closest('tr').find('.estimatedAmount').val('');
            }

        }
    }

    function change_amount_edit(field, val) {
        var quantityRequested = 0;
        var estimatedAmount = 0;
        var editNetAmount = 0;
        if (val == 1) {
            quantityRequested = $('#edit_quantityRequested').val();
            estimatedAmount = $('#edit_estimatedAmount').val();
            var totamt = quantityRequested * estimatedAmount;
            $('#editNetAmount').val(parseFloat(totamt).toFixed(currency_decimal));
        } else {
            quantityRequested = $('#edit_quantityRequested').val();
            editNetAmount = $('#editNetAmount').val();
            var unitamt = editNetAmount / quantityRequested;
            $('#edit_estimatedAmount').val(parseFloat(unitamt).toFixed(currency_decimal));
        }


    }

    function checkitemavailable(det) {

        var itmID = $(det).closest('tr').find('.itemAutoID').val();
        var warehouseid = det.value;
        var searchID = $(det).closest('tr').find('.f_search').attr('id');
        var concatarr = new Array();

        var arrSearchID =searchID.split("f_search_");
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        if(itemBatchPolicy==1){

            if(itmID!="" && warehouseid!="" && arrSearchID.length>0){
                getItemBatchDetails(itmID,arrSearchID[1],warehouseid);
            }
            
        }

        var mainconcat;
        if (itmID && warehouseid) {
            mainconcat = itmID.concat('|').concat(warehouseid);
        }

        $('.itemAutoID').each(function (key, value) {
            var itm = this.value;
            var searchID2 = $(this).closest('tr').find('.f_search').attr('id');
            var wareHouseAutoID = $(this).closest('tr').find('.wareHouseAutoID').val();
            var concatvalue = itm.concat('|').concat(wareHouseAutoID);
            if (searchID != searchID2) {
                if (mainconcat) {
                    concatarr.push(concatvalue);
                }
            }
        });
        if (warehouseid != '') {
            fetch_rv_warehouse_item_receipt(itmID, det, warehouseid)
        }
        /*if(concatarr.length>1){*/
        // if (jQuery.inArray(mainconcat, concatarr) !== -1) {
        //     $(det).closest('tr').find('.f_search').val('');
        //     $(det).closest('tr').find('.itemAutoID').val('');
        //     $(det).closest('tr').find('.wareHouseAutoID').val('').change();
        //     $(det).closest('tr').find('.quantityRequested').val('');
        //     $(det).closest('tr').find('.estimatedAmount').val('');
        //     $(det).closest('tr').find('.netAmount').val('');
        //     myAlert('w', 'Selected item is already selected');
        // }
        /*}*/
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }

    function fetch_rv_warehouse_item_receipt(itmID, det, warehouseid) {
        var warehouseautoid = $(det).closest('tr').find('.wareHouseAutoID').val();
        if (warehouseautoid != '') {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'wareHouseAutoID': warehouseid, 'itemAutoID': itmID},
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item_deduct_qty_new'); ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {
                    if (data['status']) {
                        /** ADDED BY : SAFEENA  (TASK : SME-2314)*/
                        var conversionRate = $(det).closest('tr').find('.conversionRate').val();
        
                        if(conversionRate !== '') {
                            data['currentStock'] = data['currentStock'] * conversionRate;
                        }
                        /** END  (TASK : SME-2314)*/
                        if(data['mainCategory']=='Service'){
                            $(det).closest('tr').find('.currentstock').val('');
                            $(det).closest('tr').find('.currentstock_pulleddoc').val('');
                            $(det).closest('tr').find('.parkQty').val('');

                        }else if(data['mainCategory']=='Non Inventory'){
                            $(det).closest('tr').find('.currentstock').val('');
                            $(det).closest('tr').find('.currentstock_pulleddoc').val('');
                            $(det).closest('tr').find('.parkQty').val('');

                        }else{
                            $(det).closest('tr').find('.currentstock').val(data['currentStock']);
                            $(det).closest('tr').find('.currentstock_pulleddoc').val(data['pulledstock']);
                            $(det).closest('tr').find('.parkQty').val(data['parkQty']);


                        }
                    } else {

                        $(det).typeahead('val', '');
                        $(det).closest('tr').find('.currentstock').val('');
                        $(det).closest('tr').find('.parkQty').val('');


                    }
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

    }

    function fetch_rv_warehouse_item_directreceipt(itmID, det, warehouseid) {
        var warehouseautoid = $(det).closest('tr').find('.wareHouseAutoID').val();
        if (warehouseautoid != '') {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'wareHouseAutoID': warehouseid, 'itemAutoID': itmID},
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item_deduct_qty'); ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {
                    if (data['status']) {

                        $(det).closest('tr').find('.currentstock').val(data['currentStock']);
                        if(data['mainCategory']=='Service'){
                            $('#currentstock_edit').val('');
                            $('#currentstock_pulleddoc_edit').val('');
                            // //$('#parkQty_edit').val('');
                            // $(det).closest('tr').find('.parkQty').val('');

                        }else if(data['mainCategory']=='Non Inventory'){
                            $('#currentstock_edit').val('');
                            $('#currentstock_pulleddoc_edit').val('');
                            // $('#parkQty_edit').val('');
                            // $(det).closest('tr').find('.parkQty').val('');

                        }else{
                            $('#currentstock_edit').val(data['currentStock']);
                            $('#currentstock_pulleddoc_edit').val(data['pulledstock']);
                            // $('#parkQty_edit').val(data['parkQty']);
                            //$(det).closest('tr').find('.parkQty').val(data['parkQty']);

                        }

                    } else {

                        $(det).typeahead('val', '');
                        $(det).closest('tr').find('.currentstock').val('');
                        $(det).closest('tr').find('.currentstock').val('');
                        $(det).closest('tr').find('.parkQty').val('');

                    }
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

    }

    function checkCurrentStock(det) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        var category = $(det).closest('tr').find('.itemcatype').val();
        var parkQty = $(det).closest('tr').find('.parkQty').val();

        if(category !=='Service') {
            parkQty = (parkQty === '')? 0: parkQty;
        // if (det.value > (parseFloat(currentStock) - parseFloat(parkQty))) {
        if (det.value > (parseFloat(currentStock) )) {
            myAlert('w', 'Transfer quantity should be less than or equal to available stock');
            $(det).val(0);
        }
        }
        
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }

    }

    
    function checkCurrentStock_pulleddocument(det) {
        var currentStock_pulled = $(det).closest('tr').find('.currentstock').val();
        var wareHouseAutoID = $(det).closest('tr').find('.wareHouseAutoID').val();
        var itemAutoID = $(det).closest('tr').find('.itemAutoID').val();
        var currentstock_pulleddoc = $(det).closest('tr').find('.currentstock_pulleddoc').val();
        var category = $(det).closest('tr').find('.itemcatype').val();
        var UoM =$(det).closest('tr').find('.umoDropdown option:selected').text().split('|');
        var conversionRate =$(det).closest('tr').find('.conversionRate').val();
 
         if(category !=='Service') {

        if(det.value > parseFloat(currentstock_pulleddoc)){
         
            document_by_warehouse_qty(itemAutoID,wareHouseAutoID,'',receiptVoucherAutoId,UoM[0],conversionRate,parseFloat(currentStock_pulled));
           
            $(det).val(0);
            }
    }

    }

    function editstockwhreceiptvoucher(det) {
        var itemAutoID = $('#edit_itemAutoID').val();
        var wareHouseAutoID = $('#edit_wareHouseAutoID').val();
        if (wareHouseAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                // data: {'wareHouseAutoID': wareHouseAutoID, 'itemAutoID': itemAutoID},
                //url: "<?php //echo site_url('Receipt_voucher/fetch_rv_warehouse_item_deduct_qty'); ?>//",
                data: {'wareHouseAutoID': wareHouseAutoID, 'itemAutoID': itemAutoID,'documentID':'RV','documentDetAutoID':receiptVoucherDetailAutoID},
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item_deduct_qty_new'); ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {
                    if (data['status']) {
                        if(data['mainCategory']=='Service'){
                            $('#currentstock_edit').val('');
                            $('#parkQty_edit').val('');

                        }else if(data['mainCategory']=='Non Inventory'){
                            $('#currentstock_edit').val('');
                            $('#parkQty_edit').val('');

                        }else{
                            /** ADDED BY : SAFEENA  (TASK : SME-2314)*/
                            var conversionRate = $('#conversionRateRVEdit').val();
                            if(parseFloat(conversionRate) > 0 && data['currentStock'] != null) {
                                data['currentStock'] = parseFloat(data['currentStock']) * parseFloat(conversionRate);

                            }
                            /** END  (TASK : SME-2314)*/
                            $('#currentstock_edit').val(data['currentStock']);
                            $('#currentstock_pulleddoc_edit').val(data['pulledstock']);
                            $('#parkQty_edit').val(data['parkQty']);
                            $(det).closest('tr').find('.parkQty').val(data['parkQty']);

                        }

                    } else {
                        $('#currentstock_edit').val('');
                        $('#parkQty_edit').val('');


                    }
                    refreshNotifications(true);
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

    }

    function checkCurrentStockEdit() {
        var currentStock = $('#currentstock_edit').val();
        var TransferQty = $('#edit_quantityRequested').val();
        var category = $('#edit_itemcate').val();
        var parkQty = $('#parkQty_edit').val();

        if(category !=='Service') {
            parkQty = (parkQty === '')? 0: parkQty;
        // if (parseFloat(TransferQty) > (parseFloat(currentStock) - parseFloat(parkQty))) {
        if (parseFloat(TransferQty) > (parseFloat(currentStock) )) {
            myAlert('w', 'Transfer quantity should be less than or equal to available stock');
            $('#edit_quantityRequested').val(0);
        }
        }

    }

    function checkCurrentStockEditunapproveddocument() {
      
        var currentStock = $('#currentstock_edit').val();
        var TransferQty = $('#edit_quantityRequested').val();
        var mainCategory = $('#edit_itemcate').val();
        var currentStock_pulled = $('#currentstock_pulleddoc_edit').val();
        var itemAutoID = $('#edit_itemAutoID').val();
        var wareHouseAutoID = $('#edit_wareHouseAutoID').val();
        var UOMID = $('#edit_UnitOfMeasureID').val();
        var UoM =$('#edit_UnitOfMeasureID option:selected').text().split('|');
        var conversionRate =$('#conversionRateRVEdit').val();
        
        if(mainCategory !=='Service'){

            if (parseFloat(TransferQty) > parseFloat(currentStock_pulled)) {
                // document_by_warehouse_qty(itemAutoID,wareHouseAutoID,' ',receiptVoucherAutoId,UoM[0],conversionRate,parseFloat(currentStock))
                document_by_warehouse_qty(itemAutoID,wareHouseAutoID,'RV',receiptVoucherAutoId,UoM[0],conversionRate,parseFloat(currentStock),receiptVoucherDetailAutoID)

                $('#edit_quantityRequested').val(0);
           
        }


        }

    }
    


    function edit_all_item_detail_modal() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'receiptVoucherAutoId': receiptVoucherAutoId},
            url: "<?php echo site_url('Receipt_voucher/fetch_rv_details_all'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                var descm = 2;
                $('#edit_item_table_body').empty();
                var x = 2;
                var parkQty=0;
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    /* <!--No Records Found--> */
                } else {
                    $.each(data['detail'], function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown" onchange="convertPrice_RV(this)" required')) ?>';
                        var wareHouseAutoID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ware_\'+key+\'"'), form_dropdown('wareHouseAutoID[]', all_delivery_location_drop_active(), '', 'class="form-control select2 wareHouseAutoID"  onchange="checkitemavailable(this)" required')) ?>';
                        var project = '';
                        <?php if ($projectExist == 1) { ?>
                        project = ' <td> <div class="div_projectID_item"> <select name="projectID" class="form-control select2"> <option value="">Select Project</option> </select> </div> </td>';
                        <?php
                        } ?>
                        var parkQtyStr = '<td> <div class="input-group"> <input type="text" name="parkQty[]" value="' + parkQty + '" class="form-control parkQty" required disabled> </div> </td> ';

                        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
                        if(itemBatchPolicy==1){
                            var textBatchData=value['batchNumber'];

                            var batchNumberDropdown = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="batchNumber_\'+key+\'"'), form_dropdown('batch_number[\'+key+\'][]', [], '', 'class="form-control select2 input-mini batchNumberEditAll" multiple="multiple" required')) ?>';


                            $.ajax({
                                async: true,
                                type: 'post',
                                dataType: 'json',
                                data: {'itemId': value['itemAutoID'],'wareHouseAutoID':value['wareHouseAutoID']},
                                url: "<?php echo site_url('Inventory/fetch_batch_details_byId'); ?>",
                                success: function (data) {
                                    $('#batchNumber_'+key).empty();
                                    var mySelect = $('#batchNumber_'+key);
                                    //mySelect.append($('<option></option>').val('').html('<?php echo "Select Batch"?>'));
                                    /*Select batch*/
                                    if (!jQuery.isEmptyObject(data)) {
                                        $.each(data, function (val, text) {
                                            mySelect.append($('<option></option>').val(text['batchNumber']).html(text['batchNumber']));
                                        });

                                        var optionsToSelect = textBatchData.split(",");
                                        var select = document.getElementById( 'batchNumber_'+key );

                                        for ( var i = 0, l = select.options.length, o; i < l; i++ )
                                        {
                                            o = select.options[i];
                                            if ( optionsToSelect.indexOf( o.text ) != -1 )
                                            {
                                                o.selected = true;
                                            }
                                        }
                                        
                                    }
                                }, error: function () {
                                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                                }
                            });

                            var string = '<tr><td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control search f_search" name="search[]" id="f_search_' + x + '" value="' + value['itemDescription'] + ' - ' + value['itemSystemCode'] + ' - ' + value['seconeryItemCode'] + '" placeholder="Item ID,Item Description...">  <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '"  name="itemAutoID[]"> <input type="hidden" class="form-control receiptVoucherDetailAutoID" value="' + value['receiptVoucherDetailAutoID'] + '"  name="receiptVoucherDetailAutoID[]"> </td> <td>' + wareHouseAutoID + '</td> <td>'+batchNumberDropdown+'</td>' + project + ' <td><input class="hidden conversionRate" id="conversionRate" value="' + value['conversionRateUOM'] + '" name="conversionRate">' + UOM + '</td> <td> <div class="input-group"> <input type="text" name="currentstock[]" class="form-control currentstock" required disabled></div></td>' +parkQtyStr+' <td> <input type="text" onchange="change_amount(this,1)" onkeyup="checkCurrentStock(this)" name="quantityRequested[]" placeholder="0.00" class="form-control number quantityRequested" onfocus="this.select();" value="' + value['requestedQty'] + '" required> </td><td> <input type="text" onchange="change_amount(this,1)" name="estimatedAmount[]" onkeypress="return validateFloatKeyPress(this,event)" value="' + value['unittransactionAmount'] + '" onfocus="this.select();" placeholder="0.00" class="form-control number estimatedAmount"> </td><td> <input type="text" onchange="change_amount(this,2)" name="netAmount[]" placeholder="0.00" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number netAmount input-mini" value="' + value['transactionAmount'] + '"> </td><td class="remove-td"><a onclick="delete_receipt_voucherDetailsEdit(' + value['receiptVoucherDetailAutoID'] + ',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';

                        }else{
                            var string = '<tr><td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control search f_search" name="search[]" id="f_search_' + x + '" value="' + value['itemDescription'] + ' - ' + value['itemSystemCode'] + ' - ' + value['seconeryItemCode'] + '" placeholder="Item ID,Item Description...">  <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '"  name="itemAutoID[]"> <input type="hidden" class="form-control receiptVoucherDetailAutoID" value="' + value['receiptVoucherDetailAutoID'] + '"  name="receiptVoucherDetailAutoID[]"> </td> <td>' + wareHouseAutoID + '</td> ' + project + ' <td><input class="hidden conversionRate" id="conversionRate" value="' + value['conversionRateUOM'] + '" name="conversionRate">' + UOM + '</td> <td> <div class="input-group"> <input type="text" name="currentstock[]" class="form-control currentstock" required disabled></div></td>' +parkQtyStr+' <td> <input type="text" onchange="change_amount(this,1)" onkeyup="checkCurrentStock(this)" name="quantityRequested[]" placeholder="0.00" class="form-control number quantityRequested" onfocus="this.select();" value="' + value['requestedQty'] + '" required> </td><td> <input type="text" onchange="change_amount(this,1)" name="estimatedAmount[]" onkeypress="return validateFloatKeyPress(this,event)" value="' + value['unittransactionAmount'] + '" onfocus="this.select();" placeholder="0.00" class="form-control number estimatedAmount"> </td><td> <input type="text" onchange="change_amount(this,2)" name="netAmount[]" placeholder="0.00" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number netAmount input-mini" value="' + value['transactionAmount'] + '"> </td><td class="remove-td"><a onclick="delete_receipt_voucherDetailsEdit(' + value['receiptVoucherDetailAutoID'] + ',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                        }

                        
                        $('#edit_item_table_body').append(string);
                        //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                        $('#ware_' + key).val(value['wareHouseAutoID']).change();
                        fetch_related_uom_id(value['defaultUOMID'], value['unitOfMeasureID'], $('#uom_' + key));
                        initializeitemTypeahead(x);
                        x++;
                    });
                    $('.select2').select2();
                    search_id = x - 1;
                    $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                }
                stopLoad();
               /*  <!--Total--> */

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function add_more_edit_receipt_voucher() {
        var batch_number_edit_all =search_id-1;
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#receipt_voucher_detail_all_edit_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');

        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';

        if(itemBatchPolicy==1){
            appendData.find('.batchNumberEditAll').empty();
            appendData.find('.batchNumberEditAll').attr('id', 'batch_number_' + search_id);
            appendData.find('.batchNumberEditAll').attr('name', 'batch_number[' + batch_number_edit_all +'][]');
        }

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#receipt_voucher_detail_all_edit_table').append(appendData);
        var lenght = $('#receipt_voucher_detail_all_edit_table tbody tr').length - 1;
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        $(".select2").select2();
        initializeitemTypeahead(search_id);
        number_validation();
    }


    function update_receipt_voucher_edit_all_Item() {
        $('.umoDropdown').prop("disabled", false);
        var data = $('#edit_all_item_detail_form').serializeArray();
        data.push({'name': 'receiptVoucherAutoId', 'value': receiptVoucherAutoId});
        //data.push({'name': 'invoiceDetailsAutoID', 'value': invoiceDetailsAutoID});
//            data.push({'name': 'wareHouse', 'value': $('#wareHouseAutoID option:selected').text()});
//            data.push({'name': 'uom', 'value': $('#UnitOfMeasureID option:selected').text()});
        $('#edit_all_item_detail_form select[name="wareHouseAutoID[]"] option:selected').each(function () {
            data.push({'name': 'wareHouse[]', 'value': $(this).text()})
        });

        $('#edit_all_item_detail_form select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2 ');
            }
        });
        $('.quantityRequested ').each(function () {
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
                url: "<?php echo site_url('Receipt_voucher/updateReceiptVoucher_edit_all_Item'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('.umoDropdown').prop("disabled", true);
                },
                success: function (data) {
                    receiptVoucherDetailAutoID = null;
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        load_conformation();
                        setTimeout(function () {
                            fetch_rv_details();
                        }, 300);
                        $('#all_item_edit_detail_modal').modal('hide');
                        $('#edit_all_item_detail_form')[0].reset();
                        $('.select2').select2('');
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

    }


    function delete_receipt_voucherDetailsEdit(id,det) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record!*/
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
                    data: {'receiptVoucherDetailAutoID': id},
                    url: "<?php echo site_url('Receipt_voucher/delete_item_direct'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        load_conformation();
                        setTimeout(function () {
                            //fetch_rv_details(tab);
                            fetch_rv_details(2);
                        }, 300);
                        $(det).closest('tr').remove();
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function select_check_boxCN(data, id, total) {

        $("#check_" + id).prop("checked", false);
        if (data.value > 0) {
            if (total >= data.value) {
                $("#CNcheck_" + id).prop("checked", true);




            } else {
                $("#CNcheck_" + id).prop("checked", false);
                $("#CNamount_" + id).val('');
                myAlert('w', 'You can not enter an invoice amount greater than selected Credit note Balance Amount');
                /*You can not enter an invoice amount greater than selected Debit note Balance Amount*/
            }
        }
    }

    function save_creditNote_base_items() {
        var selected = [];
        var amount = [];
        var types = [];
        var transactionAmount = [];
        /* $('#table_body input:checked').each(function () {
            selected.push($(this).val());
            amount.push($('#CNamount_' + $(this).val()).val());
            types.push($('#type_' + $(this).val()).val());
            transactionAmount.push($('#CNTransAmount_' + $(this).val()).val());
        }); */
        $('.amountinput').each(function () {
            if ($(this).val() != "") {
                amount.push($(this).val());
                types.push($(this).attr('data-type')  );
                selected.push(  $(this).attr('data-creditNoteMasterAutoID') );
                transactionAmount.push( $(this).attr('data-transactionAmount'));
            }
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'creditNoteMasterAutoID': selected,
                    'receiptVoucherAutoId': receiptVoucherAutoId,
                    'amount': amount,
                    'types': types,
                    'transactionAmount': transactionAmount
                },
                url: "<?php echo site_url('Receipt_voucher/save_creditNote_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#creditNote_base_modal').modal('hide');
                    refreshNotifications(true);

                    setTimeout(function () {
                        //fetch_rv_details(5);
                        //fetch_rv_details(5);
                        fetch_details(5);
                    }, 300);
                }, error: function () {
                    $('#creditNote_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }
    function applybtn(data, id, total) {
        $(data).closest('tr').find('.amountadd').val(total);

        var tot_TotalCostoverhead = 0;
        var amounttot = $('#totalamount').val();
        var totalamount = 0;
        if(amounttot)
        {
            totalamount = amounttot;
        }
        $('.invoice_base tr').each(function () {
            <?php
            if($rebate==1){
            ?>
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(7).find('input').val());
            <?php }else{
            ?>
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(5).find('input').val());
            <?php
            }
            ?>

            tot_TotalCostoverhead += tot_valueoverhead;
        });
        $("#total_invoice_total").text(commaSeparateNumber(parseFloat(tot_TotalCostoverhead)+parseFloat(totalamount), currency_decimal));
        deduct_total_amount();
        $("#check_" + id).prop("checked", false)
        if (total > 0) {
            if (total >= total) {
                $("#check_" + id).prop("checked", true);
            } else {
                $("#check_" + id).prop("checked", false);
                $("#amount_" + id).val('');

                myAlert('w', '<?php echo $this->lang->line('accounts_payable_tr_cannot_enter_an_invoice');?>');
                /*You can not enter an invoice amount greater than selected invoice Balance Amount*/
            }
        }
    }

    $('input').on('ifChecked', function (event) {
        if ($(this).hasClass('add_allinvoices')) {
            add_all_invoices(1);

            //

        }
    });


    $('input').on('ifUnchecked', function (event) {
        if ($(this).hasClass('add_allinvoices')) {
            add_all_invoices(2);

        }

    });
    function add_all_invoices(id) {
        var amounttot = $('#totalamount').val();
        var totalamount = 0;
        if(amounttot)
        {
            totalamount = amounttot;
        }


        if(id == 1)
        {
            $("#table_body tr").each(function () {
                var balance = ($(this).find('.receiptvoucherdetails').text().replace(/,/g,''));
                balance = balance.trim();
                var invoiceautoid = $(this).find('.InvoiceAutoID').val();
                $(this).find('.amountadd').val(balance);
                if (balance > 0) {
                    if (balance >= balance) {
                        var tot_TotalCostoverhead = 0;


                        $("#check_" + invoiceautoid).prop("checked", true);
                        $('.invoice_base tr').each(function () {
                            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(5).find('input').val());
                            tot_TotalCostoverhead += tot_valueoverhead;
                        });
                        $("#total_invoice_total").text(commaSeparateNumber(parseFloat(tot_TotalCostoverhead)+parseFloat(totalamount), currency_decimal));
                        deduct_total_amount();
                    } else {
                        $("#check_" + invoiceautoid).prop("checked", false);
                        $("#amount_" + invoiceautoid).val('');

                        myAlert('w', '<?php echo $this->lang->line('accounts_payable_tr_cannot_enter_an_invoice');?>');
                        /*You can not enter an invoice amount greater than selected invoice Balance Amount*/
                    }
                }

            });
        }else
        {
            $("#table_body tr").each(function () {
                var invoiceautoid = $(this).find('.InvoiceAutoID').val();
                $(this).find('.amountadd').val('');
                $("#check_" + invoiceautoid).prop("checked", false);
                $("#total_invoice_total").text(commaSeparateNumber(parseFloat(totalamount), currency_decimal));
                deduct_total_amount();
                $("#amount_" + invoiceautoid).val('');
            });
        }

    }


    function getNumberAndValidate(thisVal) {
        thisVal = $.trim(thisVal);
        thisVal = removeCommaSeparateNumber(thisVal);
        if ($.isNumeric(thisVal)) {
            return parseFloat(thisVal);
        }
        else {
            return parseFloat(0);
        }
    }
    function clear_invoice_selected(data, id) {
        $("#check_" + id).prop("checked", false);
        $("#amount_" + id).val('');
        total_calculation();
    }

    function total_calculation()
    {
        var amounttot = $('#totalamount').val();
        var totalamount = 0;
        var tot_TotalCostoverhead = 0;
        $('.invoice_base tr').each(function () {
            <?php
            if($rebate==1){
            ?>
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(7).find('input').val());
            <?php }else{
            ?>
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(5).find('input').val());
            <?php
            }
            ?>
            tot_TotalCostoverhead += tot_valueoverhead;
        });
        if(amounttot)
        {
            totalamount = amounttot;
        }
        $("#total_invoice_total").text(commaSeparateNumber((parseFloat(tot_TotalCostoverhead)+ parseFloat(totalamount)), currency_decimal));
        deduct_total_amount();
    }
    function deduct_total_amount()
    {
        var tot_TotalCost = parseFloat($('#total_invoice_total').text().replace(/,/g, ''));
        var amount = 0;
        var settlement_amount = $('#amount_total').val();
        if(settlement_amount)
        {
            amount = settlement_amount
        }

        $("#grandtotal_amount").text(commaSeparateNumber((  parseFloat(amount) - parseFloat(tot_TotalCost)),currency_decimal));
    }

    function calculateNetAmount(val,fld){
        var incamount=$(val).closest('tr').find('.amount').val();
        var incdiscountPercentage=$(val).closest('tr').find('.discountPercentage').val();
        var incdiscountAmount=$(val).closest('tr').find('.discountAmount').val();

        if(fld=='amount'){
            if (jQuery.isEmptyObject(incdiscountPercentage) || incdiscountPercentage==0) {
                $(val).closest('tr').find('.Netnumber').val(parseFloat(incamount).toFixed(currency_decimal));
            }else{
                var discamnt=(parseFloat(incamount)*parseFloat(incdiscountPercentage))/100;
                $(val).closest('tr').find('.discountAmount').val(parseFloat(discamnt).toFixed(currency_decimal));
                $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount)-parseFloat(discamnt)).toFixed(currency_decimal));
            }
        }else if(fld=='discountPercentage'){
            if (jQuery.isEmptyObject(incamount) || incamount==0) {
                myAlert('w','Enter Discount Amount');
                $(val).closest('tr').find('.discountPercentage').val(0);
                $(val).closest('tr').find('.discountAmount').val(0);
                $(val).closest('tr').find('.Netnumber').val(0);
            }else{
                var discamnt=(parseFloat(incamount)*parseFloat(incdiscountPercentage))/100;
                $(val).closest('tr').find('.discountAmount').val(parseFloat(discamnt).toFixed(currency_decimal));
                $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount)-parseFloat(discamnt)).toFixed(currency_decimal));
            }
        }else{
            if (jQuery.isEmptyObject(incamount) || incamount==0) {
                myAlert('w','Enter Discount Amount');
                $(val).closest('tr').find('.discountPercentage').val(0);
                $(val).closest('tr').find('.discountAmount').val(0);
                $(val).closest('tr').find('.Netnumber').val(0);
            }else{
                var discprc=(parseFloat(incdiscountAmount)*100)/parseFloat(incamount);

                $(val).closest('tr').find('.discountPercentage').val(parseFloat(discprc));
                $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount)-parseFloat(incdiscountAmount)).toFixed(currency_decimal));
            }
        }
    }

    function calculateNetAmount_edit(val,fld){
        var incamount=$('#edit_amount').val();
        var incdiscountPercentage=$('#discountPercentage_edit').val();
        var incdiscountAmount=$('#discountAmount_edit').val();

        if(fld=='amount'){
            if (jQuery.isEmptyObject(incdiscountPercentage) || incdiscountPercentage==0) {
                $('#Netamount_edit').val(incamount);
            }else{
                var discamnt=(parseFloat(incamount)*parseFloat(incdiscountPercentage))/100;
                $('#discountAmount_edit').val(parseFloat(discamnt).toFixed(currency_decimal));
                $('#Netamount_edit').val((parseFloat(incamount)-parseFloat(discamnt)).toFixed(currency_decimal));
            }
        }else if(fld=='discountPercentage'){
            if (jQuery.isEmptyObject(incamount) || jQuery.isEmptyObject(incdiscountPercentage) || incamount==0 ) {
                myAlert('w','Enter Discount Amount');
                //$('#discountPercentage_edit').val(0);
                $('#discountAmount_edit').val(0);
                $('#Netamount_edit').val(0);
            }else{
                var discamnt=(parseFloat(incamount)*parseFloat(incdiscountPercentage))/100;
                $('#discountAmount_edit').val(parseFloat(discamnt).toFixed(currency_decimal));
                $('#Netamount_edit').val((parseFloat(incamount)-parseFloat(discamnt)).toFixed(currency_decimal));
            }
        }else{
            if (jQuery.isEmptyObject(incamount) || jQuery.isEmptyObject(incdiscountAmount) || incamount==0) {
                myAlert('w','Enter Discount Amount');
                $('#discountPercentage_edit').val(0);
               //$('#discountAmount_edit').val(0);
                $('#Netamount_edit').val(0);
            }else{
                var discprc=(parseFloat(incdiscountAmount)*100)/parseFloat(incamount);

                $('#discountPercentage_edit').val(parseFloat(discprc));
                $('#Netamount_edit').val((parseFloat(incamount)-parseFloat(incdiscountAmount)).toFixed(currency_decimal));
            }
        }
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

    /** ADDED BY : SAFEENA
     *  TASK : SME-2314*/
    function convertPrice_RV(element) {
        var itemAutoID = $(element).closest('tr').find('.itemAutoID').val();
        var wareHouseAutoID = $(element).closest('tr').find('.wareHouseAutoID option:selected').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID,
                'uomID' : element.value,
                'wareHouseAutoID' : wareHouseAutoID,
                'tableName': 'srp_erp_customerreceiptmaster',
                'primaryKey': 'receiptVoucherAutoId',
                'id': receiptVoucherAutoId,
                'customerAutoID': '<?php echo $master['customerID']; ?>'},
            url: '<?php echo site_url("Invoices/fetch_converted_price_qty_invoice_new"); ?>',
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {

                 

                    $(element).closest('tr').find('.currentstock').val(data['qty']);
                    $(element).closest('tr').find('.estimatedAmount').val(data['price']);
                    $(element).closest('tr').find('.conversionRate').val(data['conversionRate']);
                    $(element).closest('tr').find('.currentstock_pulleddoc').val(data['qty_pulleddoc']);
                    $(element).closest('tr').find('.quantityRequested').val(' ');
                    $(element).closest('tr').find('.parkQty').val(data['Unapproved_stock']);
                
                 

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

    function convertPrice_RV_edit(element) {
        var itemAutoID = $(element).closest('tr').find('#edit_itemAutoID').val();
        var wareHouseAutoID = $(element).closest('tr').find('#edit_wareHouseAutoID option:selected').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID,
                'uomID' : element.value,
                'wareHouseAutoID' : wareHouseAutoID,
                'tableName': 'srp_erp_customerreceiptmaster',
                'primaryKey': 'receiptVoucherAutoId',
                'id': receiptVoucherAutoId,
                'customerAutoID': '<?php echo $master['customerID']; ?>',
                'documentcode':'RV',
                'detailID':receiptVoucherDetailAutoID
                },
            url: '<?php echo site_url("Invoices/fetch_converted_price_qty_invoice_new"); ?>',
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('#currentstock_edit').val(data['qty']);
                    $(element).closest('tr').find('#edit_estimatedAmount').val(data['price']);
                    $(element).closest('tr').find('#conversionRateRVEdit').val(data['conversionRate']);
                    $(element).closest('tr').find('#currentstock_pulleddoc_edit').val(data['qty_pulleddoc']);
                    $(element).closest('tr').find('#edit_quantityRequested').val(' ');
                    $(element).closest('tr').find('#parkQty_edit').val(data['Unapproved_stock']);

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
    /** end
     *  TASK : SME-2314*/

      function fetch_line_tax_and_vat(itemAutoID, element)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'receiptVoucherAutoId': receiptVoucherAutoId,'itemAutoID':itemAutoID},
            url: "<?php echo site_url('Receipt_voucher/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                if(data['isGroupByTax'] == 1) {
                    $(element).closest('tr').find('.item_text').empty();
                    var mySelect = $(element).parent().closest('tr').find('.item_text');
                    mySelect.append($('<option></option>').val('').html('Select Tax'));
                    if (!jQuery.isEmptyObject(data['dropdown'])) {
                        $.each(data['dropdown'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                        });
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
        var discoun = 0;
        var taxtype = $(ths).closest('tr').find('.item_text').val();

        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(qut)) {
            qut=0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }

        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt=(qut*amount);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'receiptVoucherAutoId': receiptVoucherAutoId, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'discount':discoun},
                url: "<?php echo site_url('Receipt_voucher/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $(ths).closest('tr').find('.linetaxamnt').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.netAmount').val((parseFloat(data)+parseFloat(lintaxappamnt)-parseFloat(discoun)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $(ths).closest('tr').find('.linetaxamnt').text('0');
            $(ths).closest('tr').find('.netAmount').val((parseFloat(amount *qut)-parseFloat(discoun)).toFixed(currency_decimal));
        }
    }

    function load_line_tax_amount_edit(ths){
        var qut = $('#edit_quantityRequested').val();
        var amount = $('#edit_estimatedAmount').val();
        var discoun = 0;
        var taxtype = $('#edit_item_text').val();
        var itemAutoID = $('#edit_itemAutoID').val();

        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(qut)) {
            qut=0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = (qut*amount);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'receiptVoucherAutoId': receiptVoucherAutoId, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'discount':discoun},
                url: "<?php echo site_url('Receipt_voucher/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $('#linetaxamnt_edit').text(data.toFixed(currency_decimal));
                    $('#editNetAmount').val((parseFloat(data)+parseFloat(lintaxappamnt)-parseFloat(discoun)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $('#linetaxamnt_edit').text('0');
            $('#editNetAmount').val((parseFloat(amount *qut)-parseFloat(discoun)).toFixed(currency_decimal));
        }
    }

    function edit_fetch_line_tax_and_vat(itemAutoID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'receiptVoucherAutoId': receiptVoucherAutoId, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Receipt_voucher/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                if(data['isGroupByTax'] == 1) {
                    $('#edit_item_text').empty();
                    var mySelect = $('#edit_item_text');
                    mySelect.append($('<option></option>').val('').html('Select Tax'));
                    if (!jQuery.isEmptyObject(data['dropdown'])) {
                        $.each(data['dropdown'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['taxMasterAutoID']).html(text['taxDescription']));
                        });
                        if (select_VAT_value) {
                            $('#edit_item_text').val(select_VAT_value);
                            load_line_tax_amount_edit();
                        }
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function load_gl_line_tax_amount(ths){
        var amount = $(ths).closest('tr').find('.amount').val();
        var discoun = $(ths).closest('tr').find('.discountAmount').val();
        var taxtype = $(ths).closest('tr').find('.gl_text').val();

        var lintaxappamnt=0;
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun=0;
        }
        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt=(amount);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'receiptVoucherAutoId': receiptVoucherAutoId, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'discount':discoun},
                url: "<?php echo site_url('Receipt_voucher/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $(ths).closest('tr').find('.gl_linetaxamnt').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.Netnumber').val((parseFloat(data)+parseFloat(lintaxappamnt)-parseFloat(discoun)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $(ths).closest('tr').find('.gl_linetaxamnt').text('0');
            $(ths).closest('tr').find('.Netnumber').val((parseFloat(amount)-parseFloat(discoun)).toFixed(currency_decimal));
        }
    }

    function load_gl_line_tax_amount_edit(ths)
    {
        var amount = $('#edit_amount').val();
        var discoun = $('#discountAmount_edit').val();
        var taxtype = $('#gl_text_type_edit').val();
        var lintaxappamnt=0;
        
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun=0;
        }
        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt=(amount);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'receiptVoucherAutoId': receiptVoucherAutoId, 'applicableAmnt':lintaxappamnt, 'taxtype':taxtype, 'discount':discoun},
                url: "<?php echo site_url('Receipt_voucher/load_line_tax_amount'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $('#gl_linetaxamnt_edit').text(data.toFixed(currency_decimal));
                    $('#Netamount_edit').val((parseFloat(data)+parseFloat(lintaxappamnt)-parseFloat(discoun)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }else{
            $('#gl_linetaxamnt_edit').text('0');
            $('#Netamount_edit').val((parseFloat(amount)-parseFloat(discoun)).toFixed(currency_decimal));
        }
    }

    function setQty(id,amount) {
        var data = {value:amount};
        var CNTransAmount = "#CNTransAmount_"+id;
        var CNamount = "#CNamount_"+id;
        $(CNTransAmount).val(amount);
        $(CNamount).val(amount);
        $(CNamount).data("creditnotemasterautoid", id);
        $(CNamount).data("transactionamount", amount);
        select_check_boxCN(data,id,amount);
    }

    function load_line_tax_amount_advance(ths)
    {
        var amount = $(ths).closest('tr').find('.amount_advance').val();
        var taxtype = $(ths).closest('tr').find('.item_text_advance').val();
        var contractAutoID = $(ths).closest('tr').find('.contractAutoID_advance').val();
        if (jQuery.isEmptyObject(amount)) {
            amount=0;
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'receiptVoucherAutoId': receiptVoucherAutoId, 'appliedAmount':amount, 'taxtype':taxtype, 'discount':0, 'contractAutoID':contractAutoID},
            url: "<?php echo site_url('Receipt_voucher/load_line_tax_amount_advance'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data['contract_validation'] == 1) {
                    myAlert('w', 'Amount is Greater than Document Balance Amount!');
                    $(ths).closest('tr').find('.gl_linetaxamnt').text('0');
                    $(ths).closest('tr').find('.amount_advance').val('').change();
                    $(ths).closest('tr').find('.item_text_advance').val('').change();
                    $(ths).closest('tr').find('.linetaxamnt_advance').text(0);
                } else {
                    $(ths).closest('tr').find('.linetaxamnt_advance').text(data['amnt'].toFixed(currency_decimal));
                }

                if (jQuery.isEmptyObject(contractAutoID)) {
                    $(ths).closest('tr').find('.item_text_advance').attr('disabled', false);
                } else {
                    $(ths).closest('tr').find('.item_text_advance').attr('disabled', true);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                stopLoad();
            }
        });
    }

    function load_document_amounts(ths)
    {
        var contractAutoID = $(ths).closest('tr').find('.contractAutoID_advance').val();
        if(contractAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'contractAutoID':contractAutoID},
                url: "<?php echo site_url('Receipt_voucher/load_contract_balance_amount_advance'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $(ths).closest('tr').find('.contract_amount').val(parseFloat(data['contract_amount']));
                    $(ths).closest('tr').find('.contract_paid_amount').val(parseFloat(data['paidAmount']));
                    $(ths).closest('tr').find('.contract_balance_amount').val(parseFloat(parseFloat(data['contract_amount']) - parseFloat(data['paidAmount'])));

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }
    }
</script>