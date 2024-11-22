<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .boldtab {
        font-weight: bold;
        border-left-color: #ead8d8 !important;
    }

    .a_link:hover {
        cursor: pointer;
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
<?php
$pullEC = getPolicyValues('EPV', 'All');
if (empty($pullEC)) {
    $pullEC = 0;
}
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$itemBatchPolicy = getPolicyValues('IB', 'All');

$projectExist = project_is_exist();
$umo_arr = array('' => 'Select UOM');
$stylewidth1 = '';
$stylewidth2 = '';
$stylewidth3 = '';
$stylewidth4 = '';
$stylewidth5 = '';
$stylewidth6 = ' ';
if ($projectExist == 1) {
    $stylewidth1 = 'width: 12%';
    $stylewidth6 = 'width: 18%';
    $stylewidth2 = 'width: 10%';
    $stylewidth3 = 'width: 10%';
    $stylewidth4 = 'width: 6%';
    $stylewidth5 = 'width: 6%;';
}
$showPurchasePrice = getPolicyValues('SPP', 'All');
if ($showPurchasePrice == ' ' || $showPurchasePrice == null || empty($showPurchasePrice)) {
    $showPurchasePrice = 0;
}

$advanceCostCapturing = getPolicyValues('ACC', 'All');
$activityCode_arr = get_activity_codes();

switch ($pvType) {
    case "Direct":
    case "DirectItem":
    case "DirectExpense": ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1" aria-expanded="false">
                        <?php //echo $this->lang->line('accounts_payable_tr_pv_expences'); ?><!--Expenses-->
                        <?php echo $this->lang->line('common_expence'); ?> GL<!--Expense GL--></a></li>
                <?php if ($pvType == 'Direct' || $pvType == 'DirectItem') { ?>
                    <li class=""><a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false">
                            <?php echo $this->lang->line('common_item'); ?><!--Item--></a></li>
                <?php } ?>

                <li class="pull-left header"><i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('accounts_payable_tr_pv_direct_payment_for'); ?><!--Direct Payment for-->
                    :- <?php echo $master['partyName']; ?></li>
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                            <?php if ($groupBasedTax == 1) { ?>
                                <th colspan="4"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } else { ?>
                                <th colspan="3"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } ?>
                            <th>
                                <button type="button" onclick="pv_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_add_gl'); ?><!--Add GL-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                            <th style="min-width: 30%">
                                <?php echo $this->lang->line('accounts_payable_gl_code_description'); ?><!--GL Code Description--></th>

                            <th style="min-width: 15%">
                                <?php echo $this->lang->line('common_remarks'); ?><!--Segment--></th>
                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="min-width: 15%">Activity Code</th>
                            <?php } ?>
                            <th style="min-width: 12%">
                                <?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 12%">Discount <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <?php if ($groupBasedTax == 1) { ?>
                                <th style="min-width: 12%">Tax <span
                                            class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                            <?php } ?>
                            <th style="min-width: 12%">Total <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 10%"></th>

                        </tr>
                        </thead>
                        <tbody id="gl_table_body">
                        <tr class="danger">
                            <td colspan="8" class="text-center"><b>
                                    <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <?php if ($pvType == 'Direct' || $pvType == 'DirectItem') { ?>
                    <div id="tab_2" class="tab-pane">
                        <table class="table table-bordered table-striped table-condesed">
                            <thead>
                            <tr>
                                <th colspan="5">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_item_details'); ?><!--Item Details--></th>
                                <?php if ($groupBasedTax == 1) { ?>
                                    <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                <?php } else { ?>
                                    <th colspan="2"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                <?php } ?>
                                <th>
                                    <button type="button" onclick="pv_item_detail_modal()"
                                            class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                        <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                                <?php if ($itemBatchPolicy == 1) { ?>
                                <th>Batch Number</th>
                                <?php } ?>
                                <th style="min-width: 30%">
                                    <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <?php if($advanceCostCapturing == 1){ ?>
                                    <th style="min-width: 15%">Activity Code</th>
                                <?php } ?>
                                <th style="min-width: 7%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                                <th style="min-width: 7%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                                <?php if ($groupBasedTax == 1) { ?>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                                <?php } ?>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                            </tr>
                            </thead>
                            <tbody id="item_table_body">
                            <tr class="danger">
                                <td colspan="8" class="text-center"><b>
                                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot id="item_table_tfoot">

                            </tfoot>
                        </table>
                    </div><!-- /.tab-pane -->
                <?php } ?>
                <?php if ($groupBasedTax != 1) { ?>
                    <br>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="exampleInputName2" id="tax_tot">
                                <?php echo $this->lang->line('accounts_payable_tr_pv_tax_for'); ?><!--Tax for--> </label>
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(2), '', 'class="form-control" id="text_type" required onchange="select_text(this)" style="width: 150px;"'); ?>
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
                                           style="width: 100px;" onkeypress="return validateFloatKeyPress(this,event);"
                                           onkeyup="cal_tax_amount(this.value)">
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
                                        <?php echo $this->lang->line('accounts_payable_tax_type'); ?><!--Tax Type--></th>
                                    <th>
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_detail'); ?><!--Detail--></th>
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
                <br>
            </div><!-- /.tab-content -->
        </div>
        <div aria-hidden="true" role="dialog" id="pv_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 100%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_add_expenses_detail'); ?><!--Add Expenses Detail--></h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="pv_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed no-color" id="payment_voucher_table">
                                <thead>
                                <tr>
                                    <th style="width: 350px;">
                                        <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?>
                                    </th>
                                    <th>
                                        <?php echo $this->lang->line('common_segment'); ?><!--Segment-->
                                    </th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--></th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2">Tax</th>
                                    <?php } ?>
                                    <th>Net Amount 
                                        <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?>
                                    </th>
                                    <th style="width: 40px;">
                                        <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                                    class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="form-group" style="<?php echo $stylewidth6 ?>">
                                        <?php echo form_dropdown('gl_code[]', $gl_code_arr_income, '', 'class="form-control select2" id="gl_code" required'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment_glAdd" id="segment_gl" onchange="load_segmentBase_projectID_income(this)"'); ?>
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                            <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div class="div_projectID_income">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID[]', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID[]', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount[]" id="amount"
                                               onkeyup="calculateNetAmount(this,'amount')"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control amount number">
                                    </td>
                                    <td><input type="text" name="discountPercentage[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeyup="calculateNetAmount(this,'discountPercentage')" value="00"
                                               class="form-control number discountPercentage"></td>
                                    <td><input type="text" name="discountAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeyup="calculateNetAmount(this,'discountAmount')" value="00"
                                               class="form-control number discountAmount"></td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td><?php echo form_dropdown('gl_text_type[]', all_tax_formula_drop_groupByTax(), '', 'class="form-control gl_text_type" style="width: 134px;" onchange="load_gl_line_tax_amount(this)" '); ?></td>
                                        <td><span class="gl_linetaxamnt pull-right"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td><input type="text" name="Netamount[]" value="00"
                                               class="form-control number Netnumber" readonly></td>
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
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="savePaymentVoucher_Expenses()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" id="edit_pv_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 96%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_edit_expenses_detail'); ?><!--Edit Expenses Detail--></h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="edit_pv_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed no-color"
                                   id="edit_payment_voucher_table">
                                <thead>
                                <tr>
                                    <th style="width: 350px;">
                                        <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?>
                                    </th>
                                    <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2"> Tax</th>
                                    <?php } ?>
                                    <th>Net Amount <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="form-group" style="<?php echo $stylewidth6 ?>">
                                        <?php echo form_dropdown('gl_code', $gl_code_arr_income, '', 'class="form-control select2" id="edit_gl_code" required'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="edit_segment_gl" onchange="load_segmentBase_projectID_incomeEdit(this)"'); ?>

                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div id="edit_div_projectID_income">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>

                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit"'); ?>
                                        </td>

                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount"
                                               onkeyup="calculateNetAmount_edit(this,'amount')" id="edit_amount"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number">
                                    </td>
                                    <td><input type="text" name="discountPercentage" id="discountPercentage_edit"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeyup="calculateNetAmount_edit(this,'discountPercentage')" value="00"
                                               class="form-control number "></td>
                                    <td><input type="text" name="discountAmount" id="discountAmount_edit"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeyup="calculateNetAmount_edit(this,'discountAmount')" value="00"
                                               class="form-control number "></td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td><?php echo form_dropdown('gl_text_type', all_tax_formula_drop_groupByTax(), '', 'class="form-control gl_text_type" style="width: 134px;" id="gl_text_type_edit" onchange="load_gl_line_tax_amount_edit(this)" '); ?></td>

                                        <td><span class="gl_linetaxamnt pull-right" id="gl_linetaxamnt_edit"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td><input type="text" name="Netamount" id="Netamount_edit" value="00"
                                               class="form-control number " readonly></td>
                                    <td>
                                        <textarea class="form-control" rows="1" id="edit_description"
                                                  name="description"></textarea>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="Update_PaymentVoucher_Expenses()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" tabindex="-1" id="pv_item_detail_modal" class="modal fade"
             style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 97%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_add_item_detail'); ?><!--Add Item Detail--></h4>
                    </div>

                    <div class="modal-body">
                        <form role="form" id="pv_item_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed" id="payment_voucher_Item_table">
                                <thead>
                                <tr>
                                    <th>
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                    <th>
                                        <?php echo $this->lang->line('common_warehouse'); ?><!--Warehouse--> <?php required_mark(); ?></th>
                                    <?php if($itemBatchPolicy == 1){ ?>
                                        <th>
                                         Batch Number <?php required_mark(); ?>
                                        </th>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th>
                                        <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                    <th style="width: 100px;">
                                        <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>

                                        <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <?php required_mark(); ?>
                                        <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2"
                                            style="width: 120px;"><?php echo $this->lang->line('common_tax'); ?></th>
                                    <?php } ?>
                                    
                                    <th style="width: 130px;font-size: 11px !important;">
                                        <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_remarks'); ?><!--remarks--></th>
                                    <th style="width: 40px;">
                                        <button type="button" class="btn btn-primary btn-xs"
                                                onclick="add_more_item()"><i
                                                    class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control search input-mini f_search"
                                               name="search[]"
                                               placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?> ..."
                                               id="f_search_1"><!--Item ID--><!--Item Description-->
                                        <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]"
                                               onkeydown="remove_item_all_description(event,this)">

                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID" onchange="checkitemavailable(this)"'); ?>
                                    </td>
                                    <?php if($itemBatchPolicy == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('batch_number[0][]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_1" multiple="multiple" required'); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div class="div_projectID_item">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID[]', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID[]', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini"  required'); ?>
                                    </td>
                                    <td><input type="text" onchange="load_line_tax_amount(this), change_amount(this,1)"
                                               name="quantityRequested[]"
                                               onkeyup="validatetb_row(this)"
                                               onfocus="this.select();"
                                               class="form-control number quantityRequested input-mini" required>
                                    </td>
                                    <td>
                                        <input type="text" onchange="load_line_tax_amount(this), change_amount(this,1)"
                                               name="estimatedAmount[]"

                                               onkeyup="validatetb_row(this)"
                                               onfocus="this.select();"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number estimatedAmount input-mini">
                                    </td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td>
                                            <?php echo form_dropdown('item_text[]', array('' => 'Select Tax Types'), '', 'class="form-control item_text input-mini" id="" onchange="load_line_tax_amount(this)"'); ?>
                                        </td>
                                        <td><span class="linetaxamnt pull-right"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" onchange="change_amount(this,2)" name="netAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number netAmount input-mini">
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <td><textarea class="form-control input-mini" rows="1" name="comment[]"
                                                  placeholder="<?php echo $this->lang->line('accounts_payable_tr_pv_item_comment'); ?>..."></textarea>
                                        <!--Item Comment-->
                                    </td>
                                    <td class="remove-td" style="vertical-align: middle;text-align: center">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="savePaymentVoucher_ID_item()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" tabindex="-1" id="edit_pv_item_detail_modal" class="modal fade"
             style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 97%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_edit_item_detail'); ?><!--Edit Item Detail--></h4>
                    </div>

                    <div class="modal-body">
                        <form role="form" id="edit_pv_item_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed" id="edit_payment_voucher_Item_table">
                                <thead>
                                <tr>
                                    <th>
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                    <th>
                                        <?php echo $this->lang->line('common_warehouse'); ?><!--Warehouse--> <?php required_mark(); ?></th>
                                    <?php if($itemBatchPolicy == 1){ ?>
                                        <th>
                                         Batch Number <?php required_mark(); ?>
                                        </th>
                                    <?php } ?>
                                    
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th>
                                        <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                    <th style="width: 100px;">
                                        <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                    <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2" style="width: 120px;">
                                            <?php echo $this->lang->line('common_tax'); ?><!--Tax--> </th>
                                    <?php } ?>
                                    <th style="width: 130px;font-size: 11px !important;">
                                        <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_segment'); ?><!--Comment--></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control input-mini" name="search"
                                               placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?> ..."
                                               id="search"
                                               onkeydown="remove_item_all_description_edit(event,this)"><!--Item ID-->
                                        <!--Item Description-->
                                        <input type="hidden" class="form-control" name="itemAutoID"
                                               id="edit_itemAutoID">

                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2 input-mini" onChange="load_batch_number_single_edit_p_voucher(this)" id="edit_wareHouseAutoID"'); ?>
                                    </td>
                                    <?php if($itemBatchPolicy == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('batch_number[]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_edit" multiple="multiple" required'); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div id="edit_div_projectID_item">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>

                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit1" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit1"'); ?>
                                        </td>

                                    <?php } ?>
                                    <td><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control input-mini"  id="edit_UnitOfMeasureID" required'); ?>
                                    </td>
                                    <td><input type="text"
                                               onchange="load_line_tax_amount_edit(this), change_amount_edit(this,1)"
                                               name="quantityRequested"
                                               onfocus="this.select();"
                                               class="form-control number input-mini" id="edit_quantityRequested"
                                               required>
                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,1)" name="estimatedAmount"
                                               onfocus="this.select();"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number input-mini" id="edit_estimatedAmount">
                                    </td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td>
                                            <?php echo form_dropdown('item_text', array('' => 'Select Tax Types'), '', 'class="form-control item_text input-mini" id="edit_item_text" onchange="load_line_tax_amount_edit(this)"'); ?>
                                        </td>
                                        <td><span class="pull-right linetaxamnt_edit" id="linetaxamnt_edit"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,2)" id="editNetAmount"
                                               name="netAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number netAmount input-mini">
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <td><textarea class="form-control input-mini" rows="1" name="comment"
                                                  placeholder="<?php echo $this->lang->line('accounts_payable_tr_pv_item_comment'); ?>..."
                                                  id="edit_comment"></textarea><!--Item Comment-->
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="Update_PaymentVoucher_ID_item()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    case "Employee":
    case "EmployeeExpense":
    case "EmployeeItem":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1" aria-expanded="false">
                        <?php //echo $this->lang->line('accounts_payable_tr_pv_expences');
                        ?><!--Expenses-->
                        <?php echo $this->lang->line('common_expence'); ?> GL<!--Expense GL--></a></li>
                <?php if ($pvType == 'Employee' || $pvType == 'EmployeeItem') { ?>
                    <li class=""><a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false">
                            <?php echo $this->lang->line('common_item'); ?><!--Item--></a></li>
                <?php } ?>

                <li class="pull-left header"><i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('accounts_payable_tr_pv_employee_payment_for'); ?><!--Employee Payment for-->
                    :
                    - <?php echo $master['partyName'] . " ( " . $master['partyCode'] . " )"; ?></li>
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                            <?php if ($groupBasedTax == 1) { ?>
                                <th colspan="4"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } else { ?>
                                <th colspan="3"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } ?>
                            <th>
                                <button type="button" onclick="Emp_EC_pv_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_add_gl'); ?><!--Add GL-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('accounts_payable_tr_pv_add_gl'); ?><!--GL Code--></th>
                            <th style="min-width: 30%">
                                <?php echo $this->lang->line('common_gl_code_description'); ?><!--GL Code Description--></th>
                            <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="min-width: 15%">Activity Code</th>
                            <?php } ?> 
                            <th style="min-width: 15%">
                            <th style="min-width: 12%">
                                <?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 12%">Discount <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <?php if ($groupBasedTax == 1) { ?>
                                <th style="min-width: 12%">Tax <span
                                            class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                            <?php } ?>
                            <th style="min-width: 12%">Total <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>

                            <th style="min-width: 10%">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="gl_table_body">

                        <tr class="danger">
                            <td colspan="8" class="text-center"><b>
                                    <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <?php if ($pvType == 'Employee' || $pvType == 'EmployeeItem') { ?>
                    <div id="tab_2" class="tab-pane">
                        <table class="table table-bordered table-striped table-condesed">
                            <thead>
                            <tr>
                                <th colspan="5"><?php echo $this->lang->line('accounts_payable_tr_pv_item_details'); ?>
                                    <!--Item Details--></th>
                                <?php if ($groupBasedTax == 1) { ?>
                                    <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                <?php } else { ?>
                                    <th colspan="2"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                <?php } ?>
                                <th>
                                    <button type="button" onclick="pv_item_detail_modal()"
                                            class="btn btn-primary pull-right btn-xs"><i
                                                class="fa fa-plus"></i><?php echo $this->lang->line('common_add_item'); ?>
                                        <!--Add Item-->
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                                <?php if ($itemBatchPolicy == 1) { ?>
                                <th>Batch Number</th>
                                <?php } ?>
                                <th style="min-width: 36%">
                                    <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <?php if($advanceCostCapturing == 1){ ?>
                                    <th style="min-width: 15%">Activity Code</th>
                                <?php } ?>
                                <th style="min-width: 7%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                                <th style="min-width: 7%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                                <?php if ($groupBasedTax == 1) { ?>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                                <?php } ?>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                            </tr>
                            </thead>
                            <tbody id="item_table_body">

                            <tr class="danger">
                                <td colspan="8" class="text-center"><b>
                                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot id="item_table_tfoot">

                            </tfoot>
                        </table>
                    </div><!-- /.tab-pane -->
                <?php } ?>
            </div><!-- /.tab-content -->
            <?php if ($groupBasedTax != 1) { ?>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2" id="tax_tot">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_tax_for'); ?><!--Tax for--> </label>
                        <form class="form-inline" id="tax_form">
                            <div class="form-group">
                                <?php echo form_dropdown('text_type', all_tax_drop(2), '', 'class="form-control" id="text_type" required onchange="select_text(this)" style="width: 150px;"'); ?>
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
                                       style="width: 100px;" onkeypress="return validateFloatKeyPress(this,event);"
                                       onkeyup="cal_tax_amount(this.value)">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('accounts_payable_tax_type'); ?><!--Tax Type--></th>
                                <th><?php echo $this->lang->line('accounts_payable_tr_pv_detail'); ?><!--Detail--></th>
                                <th><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency">(LKR)</span>
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
        <div aria-hidden="true" role="dialog" id="pv_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 96%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_add_expenses_detail'); ?><!--Add Expenses Detail--></h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="pv_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed no-color" id="payment_voucher_table">
                                <thead>
                                <tr>
                                    <th style="width: 350px;">
                                        <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                    <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2">Tax</th>
                                    <?php } ?>
                                    <th>Net Amount <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                                    <th style="width: 40px;">
                                        <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                                    class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="form-group" style="<?php echo $stylewidth6 ?>">
                                        <?php echo form_dropdown('gl_code[]', $gl_code_arr_income, '', 'class="form-control select2" id="gl_code" required'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment_glAdd" id="segment_gl" onchange="load_segmentBase_projectID_income(this)"'); ?>
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div class="div_projectID_income">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID[]', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID[]', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount[]" id="amount"
                                               onkeyup="calculateNetAmount(this,'amount')"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control amount number">
                                    </td>
                                    <td><input type="text" name="discountPercentage[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeyup="calculateNetAmount(this,'discountPercentage')" value="00"
                                               class="form-control number discountPercentage"></td>
                                    <td><input type="text" name="discountAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeyup="calculateNetAmount(this,'discountAmount')" value="00"
                                               class="form-control number discountAmount"></td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td><?php echo form_dropdown('gl_text_type[]', all_tax_formula_drop_groupByTax(), '', 'class="form-control gl_text_type" style="width: 134px;" onchange="load_gl_line_tax_amount(this)" '); ?></td>
                                        <td><span class="gl_linetaxamnt pull-right"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td><input type="text" name="Netamount[]" value="00"
                                               class="form-control number Netnumber" readonly></td>
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
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="savePaymentVoucher_Expenses()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" id="edit_pv_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 96%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_edit_expenses_detail'); ?><!--Edit Expenses Detail--></h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="edit_pv_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed no-color"
                                   id="edit_payment_voucher_table">
                                <thead>
                                <tr>
                                    <th style="width: 350px;">
                                        <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                    <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>

                                    <?php } ?>
                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2"> Tax</th>
                                    <?php } ?>
                                    <th>Net Amount <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="form-group" style="<?php echo $stylewidth6 ?>">
                                        <?php echo form_dropdown('gl_code', $gl_code_arr_income, '', 'class="form-control select2" id="edit_gl_code" required'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="edit_segment_gl" onchange="load_segmentBase_projectID_incomeEdit(this)"'); ?>

                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div id="edit_div_projectID_income">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount"
                                               onkeyup="calculateNetAmount_edit(this,'amount')" id="edit_amount"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number">
                                    </td>
                                    <td><input type="text" name="discountPercentage" id="discountPercentage_edit"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeyup="calculateNetAmount_edit(this,'discountPercentage')" value="00"
                                               class="form-control number "></td>
                                    <td><input type="text" name="discountAmount" id="discountAmount_edit"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeyup="calculateNetAmount_edit(this,'discountAmount')" value="00"
                                               class="form-control number "></td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td><?php echo form_dropdown('gl_text_type', all_tax_formula_drop_groupByTax(), '', 'class="form-control gl_text_type" style="width: 134px;" id="gl_text_type_edit" onchange="load_gl_line_tax_amount_edit(this)" '); ?></td>

                                        <td><span class="gl_linetaxamnt pull-right" id="gl_linetaxamnt_edit"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td><input type="text" name="Netamount" id="Netamount_edit" value="00"
                                               class="form-control number " readonly></td>
                                    <td>
                                        <textarea class="form-control" rows="1" id="edit_description"
                                                  name="description"></textarea>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="Update_PaymentVoucher_Expenses()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" tabindex="-1" id="pv_item_detail_modal" class="modal fade"
             style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 97%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h5 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_add_item_detail'); ?><!--Add Item Detail--></h5>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="pv_item_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed" id="payment_voucher_Item_table">
                                <thead>
                                <tr>
                                    <th>
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                    <th>
                                        <?php echo $this->lang->line('common_warehouse'); ?><!--Warehouse--> <?php required_mark(); ?></th>
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
                                    <th style="width: 100px;">
                                        <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                    <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2"
                                            style="width: 120px;"><?php echo $this->lang->line('common_tax'); ?></th>
                                    <?php } ?>
                                    <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_remarks'); ?><!--remarks--></th>
                                    <th style="width: 40px;">
                                        <button type="button" class="btn btn-primary btn-xs"
                                                onclick="add_more_item()"><i
                                                    class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control search input-mini f_search"
                                               name="search[]" id="f_search_1"
                                               placeholder="<?php echo $this->lang->line('common_item_id'); ?>, <?php echo $this->lang->line('common_item_description'); ?>..."
                                               onkeydown="remove_item_all_description(event,this)"><!--Item ID-->
                                        <!--Item Description-->
                                        <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">

                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID" onchange="checkitemavailable(this)"'); ?>
                                    </td>
                                    <?php if($itemBatchPolicy == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('batch_number[0][]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_1" multiple="multiple" required'); ?>
                                        </td>
                                    <?php } ?>

                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div class="div_projectID_item">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID[]', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID[]', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                        </td>

                                    <?php } ?>
                                    <td><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini"  required'); ?>
                                    </td>
                                    <td><input type="text" onchange="load_line_tax_amount(this), change_amount(this,1)"
                                               name="quantityRequested[]"
                                               placeholder="0.00"
                                               class="form-control quantityRequested number input-mini" required>
                                    </td>
                                    <td>
                                        <input type="text" onchange="load_line_tax_amount(this), change_amount(this,1)"
                                               name="estimatedAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number estimatedAmount input-mini">
                                    </td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td>
                                            <?php echo form_dropdown('item_text[]', array('' => 'Select Tax Types'), '', 'class="form-control item_text input-mini" id="" onchange="load_line_tax_amount(this)"'); ?>
                                        </td>
                                        <td><span class="linetaxamnt pull-right"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" onchange="change_amount(this,2)" name="netAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number netAmount input-mini">
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                            <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <td><textarea class="form-control input-mini" rows="1" name="comment[]"
                                                  placeholder="<?php echo $this->lang->line('accounts_payable_tr_pv_item_comment'); ?>..."></textarea>
                                        <!--Item Comment-->
                                    </td>
                                    <td class="remove-td" style="vertical-align: middle;text-align: center">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="savePaymentVoucher_ID_item()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" tabindex="-1" id="edit_pv_item_detail_modal" class="modal fade"
             style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 97%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_edit_item_detail'); ?><!--Edit Item Detail--></h4>
                    </div>

                    <div class="modal-body">
                        <form role="form" id="edit_pv_item_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed" id="edit_payment_voucher_Item_table">
                                <thead>
                                <tr>
                                    <th>
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                    <th>
                                        <?php echo $this->lang->line('common_warehouse'); ?><!--Warehouse--> <?php required_mark(); ?></th>
                                    
                                    <?php if($itemBatchPolicy == 1){ ?>
                                        <th>
                                         Batch Number <?php required_mark(); ?>
                                        </th>
                                    <?php } ?>

                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th>
                                        <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                    <th style="width: 100px;">
                                        <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                    <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2" style="width: 120px;">
                                            <?php echo $this->lang->line('common_tax'); ?><!--Tax--> </th>
                                    <?php } ?>
                                    <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control input-mini" name="search"
                                               placeholder="<?php echo $this->lang->line('common_item_id'); ?>, <?php echo $this->lang->line('common_item_description'); ?>..."
                                               id="search"
                                               onkeydown="remove_item_all_description_edit(event,this)"><!--Item ID-->
                                        <!--Item Description-->
                                        <input type="hidden" class="form-control" name="itemAutoID"
                                               id="edit_itemAutoID">

                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2 input-mini" onChange="load_batch_number_single_edit_p_voucher(this)" id="edit_wareHouseAutoID"'); ?>
                                    </td>
                                    <?php if($itemBatchPolicy == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('batch_number[]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_edit"  multiple="multiple" required'); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div id="edit_div_projectID_item">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit1" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit1"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control input-mini"  id="edit_UnitOfMeasureID" required'); ?>
                                    </td>
                                    <td><input type="text"
                                               onchange="load_line_tax_amount_edit(this), change_amount_edit(this,1)"
                                               name="quantityRequested" placeholder="0.00"
                                               class="form-control number input-mini" id="edit_quantityRequested"
                                               required>
                                    </td>
                                    <td>
                                        <input type="text"
                                               onchange="load_line_tax_amount_edit(this), change_amount_edit(this,1)"
                                               name="estimatedAmount"
                                               placeholder="0.00"
                                               class="form-control number input-mini"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               id="edit_estimatedAmount">
                                    </td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td>
                                            <?php echo form_dropdown('item_text', array('' => 'Select Tax Types'), '', 'class="form-control item_text input-mini" id="edit_item_text" onchange="load_line_tax_amount_edit(this)"'); ?>
                                        </td>
                                        <td><span class="pull-right linetaxamnt_edit" id="linetaxamnt_edit"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,2)" id="editNetAmount"
                                               name="netAmount[]" placeholder="0.00"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number netAmount input-mini">
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <td><textarea class="form-control input-mini" rows="1" name="comment"
                                                  placeholder="<?php echo $this->lang->line('accounts_payable_tr_pv_item_comment'); ?>..."
                                                  id="edit_comment"></textarea><!--Item Comment-->
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="Update_PaymentVoucher_ID_item()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" id="pv_ec_and_gl_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 96%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_add_expenses_detail'); ?><!--Add Expenses Detail--></h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="pv_ec_and_gl_add_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed no-color" id="pv_ec_and_gl_add_table">
                                <thead>
                                <tr>
                                    <?php if ($pullEC == 1) { ?>
                                        <th style="width: 150px;">Type</th>
                                    <?php } ?>

                                    <th style="width: 200px;"><?php echo $this->lang->line('common_gl_code'); ?> /
                                        Expence Claim<!--GL Code--> <?php required_mark(); ?>
                                    </th>

                                    <th class="pAmount hidden" style="width: 150px;">
                                        <?php echo "Provision Amount"; ?><!--Provision Amount--> 
                                        <span class="currency"> (<?php echo $master['transactionCurrency']; ?>) </span> <?php required_mark(); ?>
                                    </th>

                                    <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>

                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>

                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> 
                                        </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>

                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> 
                                        <span class="currency"> (<?php echo $master['transactionCurrency']; ?>) </span> <?php required_mark(); ?>
                                    </th>

                                    <th style="width: 100px;">Discount Percentage</th>

                                    <th>Discount Amount</th>

                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th class="tax" colspan="2" style="width: 150px;">
                                            <?php echo $this->lang->line('common_tax'); ?><!--Tax--> 
                                        </th>
                                    <?php } ?>

                                    <th>Net Amount 
                                        <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>

                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_remarks'); ?><!--Description--> <?php required_mark(); ?>
                                    </th>

                                    <th style="width: 40px;">
                                        <button type="button" class="btn btn-primary btn-xs"
                                                onclick="add_more_ec_and_gl()"><i
                                                class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <?php if ($pullEC == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth6 ?>">
                                            <?php echo form_dropdown('expenseType[]', array('' => 'Select Type', 1 => 'Expense Claim', 2 => 'GL', 3 => 'Leave Salary'), '2', 'class="form-control select2" id="expenseType" onchange="load_expenseType(this, this.value)" required'); ?>
                                            <input class="hidden" id="docTypeID" name="docTypeID[]">
                                        </td>
                                    <?php } ?>

                                    <td class="form-group expenseTypeLoad" style="<?php echo $stylewidth6 ?>">
                                        <div class="glType">
                                            <?php echo form_dropdown('gl_code[]', $gl_code_arr_income, '', 'class="form-control select2" id="gl_code" onchange="selectedType(this, \'GL\')"'); ?>
                                        </div>
                                        <div class="ecType hidden">
                                            <?php echo form_dropdown('expenseClaimMasterAutoID[]', array(), '', 'class="form-control select2" id="expenseClaimMasterAutoID" onchange="selectedType(this, \'EC\')"'); ?>
                                        </div>
                                        <div class="expenseGlType hidden">
                                            <?php
                                                $defaultSelectedValue = fetch_expensegl();
                                                echo form_dropdown('expenseGLCode[]', array(), $defaultSelectedValue, 'class="form-control select2" id="expenseGLCode" onchange="selectedType(this, \'EG\')"');
                                            ?>
                                        </div>
                                    </td>

                                    <td class="pAmount hidden">
                                            <input type="text" name="pamount[]" id="pamount" onchange="load_gl_line_tax_amount_emp(this)"
                                                onkeyup="calculateNetAmount(this,'pamount')"
                                                onkeypress="return validateFloatKeyPress(this,event)"
                                                class="form-control pamount number">
                                    </td>

                                    <td>
                                        <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment_glAdd" id="segment_gl" onchange="load_segmentBase_projectID_income(this)"'); ?>

                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div class="div_projectID_income">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID[]', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID[]', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount[]" id="amount" onchange="load_gl_line_tax_amount_emp(this)"
                                               onkeyup="calculateNetAmount(this,'amount')"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control amount number">
                                    </td>
                                    <td>
                                        <input type="text" name="discountPercentage[]"
                                               onkeypress="return validateFloatKeyPress(this,event)" onchange="load_gl_line_tax_amount_emp(this)"
                                               onkeyup="calculateNetAmount(this,'discountPercentage')" value="00"
                                               class="form-control number discountPercentage">
                                    </td>
                                    <td>
                                        <input type="text" name="discountAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)" onchange="load_gl_line_tax_amount_emp(this)"
                                               onkeyup="calculateNetAmount(this,'discountAmount')" value="00"
                                               class="form-control number discountAmount">
                                    </td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                            <td class="tax">
                                                <?php echo form_dropdown('item_text', all_tax_formula_drop_groupByTax(), '', 'class="form-control item_text input-mini" id="edit_item_text" onchange="load_gl_line_tax_amount_emp(this)"'); ?>
                                            </td>
                                            <td class="tax"><span class="pull-right linetaxamnt_edit" id="linetaxamnt_edit"
                                                        style="font-size: 14px;text-align: center;margin-top: 8%;width: 20px;">0</span>
                                            </td>                            
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="Netamount[]" value="00"
                                               class="form-control number Netnumber" readonly id="Netamount">
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
                        <button data-dismiss="modal" class="btn btn-default"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> <!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="savePaymentVoucher_ExpensesAndClaim()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <?php
        break;
    case "Supplier":
    case "SupplierAdvance":
    case "SupplierInvoice":
    case "SupplierItem":
    case "SupplierExpense":
        $po_arr = array('' => 'Select PO');
        if (isset($supplier_po)) {
            foreach ($supplier_po as $row) {
                $po_arr[trim($row['purchaseOrderID'] ?? '') . '|' . $row['expectedDeliveryDate']] = trim($row['purchaseOrderCode'] ?? '') . ' | ' . trim($row['narration'] ?? '') . ' - ' . trim($row['referenceNumber'] ?? '');
            }
        }
        ?>
        <div class="modal fade" id="inv_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             data-width="95%" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document" style="width: 70%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_invoice_base'); ?><!--Invoice Base--></h4>

                        <div class="row">
                            <div class="form-group col-sm-10">

                            </div>
                            <div class="form-group col-sm-2">
                                <div class="skin skin-square">
                                    <div class="skin-section extraColumns">Select All &nbsp;<input id="issubtask"
                                                                                                   type="checkbox"
                                                                                                   data-caption=""
                                                                                                   class="columnSelected add_allinvoices"
                                                                                                   name="issubtask"
                                                                                                   value="1"><label
                                                for="checkbox">&nbsp;</label></div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">

                            <div class="form-group col-sm-2">
                                &nbsp; &nbsp;<strong style="font-size:13px;color: #4a8cdb;">Settlement Amount </strong>
                            </div>
                            <div class="form-group col-sm-2">
                                <input type="text" style="text-align: right;" name="amount_total"
                                       id="amount_total" value="<?php echo $master['settlementTotal']; ?>"
                                       class="form-control" onkeyup="deduct_total_amount();"
                                       onkeypress="return validateFloatKeyPress(this,event)" ;
                                       required>
                            </div>
                            <div class="form-group col-sm-4">
                                <div class="table-responsive">
                                    <div class="col-md-12" style="font-size:13px;color: #4a8cdb">
                                        <div class="col-md-12" style="text-align: right;"><strong>Utilized
                                                Amount</strong>&nbsp;
                                            <span id="total_invoice_total"><?php echo number_format($totalamountreceipt['totalamounttransaction'], $master['transactionCurrencyDecimalPlaces']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <div class="table-responsive">
                                    <div class="col-md-12" style="font-size:13px;color: #4a8cdb">
                                        <div class="col-md-12" style="text-align: right;"><strong>Balance</strong>&nbsp;
                                            <span id="grandtotal_amount"><?php echo number_format(($master['settlementTotal'] - $totalamountreceipt['totalamounttransaction']), $master['transactionCurrencyDecimalPlaces']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


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
                                       value="<?php echo $totalamountreceipt['totalamounttransaction']; ?>">
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
        </div>

        <div class="modal fade" id="inv_base_customer_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             data-width="95%" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document" style="width: 70%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_invoice_base'); ?><!--Invoice Base--></h4>

                            <div class="row">
                                <div class="form-group col-sm-10">

                            </div>
                            <div class="form-group col-sm-2">
                                <div class="skin skin-square">
                                    <div class="skin-section extraColumns">Select All &nbsp;
                                        <input id="issubtask"
                                                type="checkbox"
                                                data-caption=""
                                                class="columnSelected add_allinvoices"
                                                name="issubtask"
                                                value="1"><label
                                                for="checkbox">&nbsp;</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                     


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
                                        <?php echo $this->lang->line('accounts_payable_customer_invoice_no'); ?><!--Supplier Invoice No--></th>
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
                                       value="<?php echo $totalamountreceipt['totalamounttransaction']; ?>">
                                <?php
                                if (!empty($customer_inv)) {
                                    $x = 1;
                                    for ($i = 0; $i < count($customer_inv); $i++) {
                                        //$id=$supplier_inv[$i]['InvoiceAutoID'];
                                        if (round($customer_inv[$i]['transactionAmount'])  > 0) {
                                            $id = $customer_inv[$i]['InvoiceAutoID'];
                                            $type = 'BSI';

                                            echo "<tr>";
                                            echo "<td >" . $x . "</td>";
                                            echo "<td><a class='a_link' target=\"_blank\" onclick=\"documentPageView_modal('BSI','$id')\">" . $customer_inv[$i]['bookingInvCode'] . "</a></td>";
                                            echo "<td>" . $customer_inv[$i]['bookingDate'] . "</td>";
                                            echo "<td>" . $customer_inv[$i]['supplierInvoiceNo'] . "</td>";
                                            echo "<td>" . $customer_inv[$i]['RefNo'] . "</td>";
                                            echo "<td class='text-right'>" . number_format($customer_inv[$i]['transactionAmount'], $master['transactionCurrencyDecimalPlaces']) . "</td>";

                                            echo "<td class='text-right'>
                                         <span class='supplierinvoicebalance'>
                                      " . number_format(($customer_inv[$i]['transactionAmount']), $master['transactionCurrencyDecimalPlaces']) . "
                                            </span>

                                        <a class='hoverbtn invoiceaddbtn'  onclick='applybtn(this," . $customer_inv[$i]['InvoiceAutoID'] . "," . round(($customer_inv[$i]['transactionAmount']), $master['transactionCurrencyDecimalPlaces']) . ")'><i class='fa fa-arrow-circle-right' aria-hidden='true'></i></a>
             

                                        </td>";
                                            echo '<td><input type="text" name="amount[]" style="width: 82%;" id="amount_' . $customer_inv[$i]['InvoiceAutoID'] . '" onkeypress="return validateFloatKeyPress(this,event);"  onkeyup="total_calculation(),select_check_box(this,' . $customer_inv[$i]['InvoiceAutoID'] . ',' . round(($customer_inv[$i]['transactionAmount']), $master['transactionCurrencyDecimalPlaces']) . ')" class="amountadd number">&nbsp;<i class="fa fa-times" onclick="clear_invoice_selected(this,' . $customer_inv[$i]['InvoiceAutoID'] . ',' . round(($customer_inv[$i]['transactionAmount']), $master['transactionCurrencyDecimalPlaces']) . ')" aria-hidden="true"></i>&nbsp;
                                        &nbsp;
                                            <input type="hidden" class="InvoiceAutoID" value="' . $customer_inv[$i]['InvoiceAutoID'] . '"></td>';
                                            echo '<td class="text-right;" style="display:none;"  ><input class="checkbox" id="check_' . $customer_inv[$i]['InvoiceAutoID'] . '" type="checkbox" value="' . $customer_inv[$i]['InvoiceAutoID'] . '"></td>';
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                            <button type="button" class="btn btn-primary" onclick="save_inv_base_items('CUS')">
                                <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="debitNote_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             data-width="95%" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_debit_note_base'); ?>
                            <!--Debit Note Base-->/ Purchase Return</h4>
                    </div>
                    <div class="modal-body" style="height: 500px;overflow: auto;">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <tr>
                                <th colspan="5">
                                    Debit Note / Purchase Return
                                </th>
                                <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                            </tr>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_debit_note_code'); ?><!--Debit Note Code--></th>
                                <th style="width: 5%">Type</th>
                                <th style="width: 5%">Date</th>
                                <th style="width: 20%">
                                    <?php echo $this->lang->line('accounts_payable_reference_no'); ?><!--Reference No--></th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_debit_note_total'); ?><!--Debit Note Total--></th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('accounts_payable_balance'); ?><!--Balance--></th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                                <!-- <th >&nbsp;</th> -->
                            </tr>
                            </thead>
                            <tbody id="table_body">
                            <?php
                            /*echo '<pre>';
                            print_r($debit_note);
                            echo '</pre>';*/
                            $d = $master['transactionCurrencyDecimalPlaces'];
                            
                            if (!empty($debit_note)) {
                                $i = 0;
                                foreach ($debit_note as $val) {
                                    $dif = $val['transactionAmount'] - $val['PVTransactionAmount'];
                                    
                                    if ($dif > 0) {
                                        echo "<tr>";
                                        echo "<td>" . ($i + 1) . "</td>";
                                        echo "<td>" . $val['debitNoteCode'] . "</td>";
                                        echo "<td>" . $val['type'] . "</td>";
                                        echo "<td>" . $val['debitNoteDate'] . "</td>";
                                        echo "<td>" . $val['RefNo'] . "</td>";
                                        echo "<td class='text-right'>" . number_format($val['transactionAmount'], $d) . "</td>";
                                        echo "<td class='text-right'>
                                        <span>" . number_format($dif,$d) . "</span> 
                                        <a class='hoverbtn invoiceaddbtn' onclick='passVal(this,".$val['debitNoteMasterAutoID'].','.round($val['transactionAmount'] - $val['PVTransactionAmount'],$d).")'>
                                        <i class='fa fa-arrow-circle-right'  aria-hidden='true'></i>
                                        </a>
                                        </td>";
                                        $onclickVal = 'this,' . $val['debitNoteMasterAutoID'] . ',' . $dif.','.$d;
                                        ?>
                                        
                                        <td>
                                            <input type="hidden"
                                                   value="<?php echo $val['transactionAmount'] ?>"
                                                   id="DNTransAmount_<?php echo $val['debitNoteMasterAutoID'] ?>"
                                                   name="transactionAmount[]">
                                            <input type="text" name="amount[]"
                                                   id="DNamount_<?php echo $val['debitNoteMasterAutoID'] ?>"
                                                   onkeyup="select_check_boxDN(<?php echo $onclickVal ?>)"
                                                   onkeypress="return validateFloatKeyPress(this,event)"
                                                   class="amountadd number">
                                        </td>
                                        <?php
                                        echo '<td class="text-right;" style="display:none;"  ><input class="checkbox" id="DNcheck_' . $val['debitNoteMasterAutoID'] . '" type="checkbox" value="' . $val['debitNoteMasterAutoID'] . '"><input type="hidden" value="' . $val['type'] . '" id="type_' . $val['debitNoteMasterAutoID'] . '"></td>';
                                        echo "</tr>";
                                        $i++;
                                    }
                                }
                            } else {
                                $norecfounds = $this->lang->line('common_no_records_found');
                                echo '<tr class="danger"><td colspan="8" class="text-center"><b>' . $norecfounds . '<!--No Records Found--></b></td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button type="button" class="btn btn-primary" onclick="save_debitNote_base_items()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <?php if ($pvType == 'SupplierInvoice') { ?>
                    <li class=""><a data-toggle="tab" class="boldtab" href="#tab_6" aria-expanded="false">
                            <?php //echo $this->lang->line('accounts_payable_tr_pv_expences'); ?><!--Expenses-->
                            <?php echo $this->lang->line('common_income'); ?> GL<!--Expense GL--></a></li>
                <?php } ?>
                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierExpense' || $pvType == 'SupplierItem' || $pvType == 'SupplierInvoice') { ?>
                    <li class=""><a data-toggle="tab" class="boldtab" href="#tab_1" aria-expanded="false">
                            <?php //echo $this->lang->line('accounts_payable_tr_pv_expences'); ?><!--Expenses-->
                            <?php echo $this->lang->line('common_expence'); ?> GL<!--Expense GL--></a></li>
                <?php } ?>
                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierItem') { ?>
                    <li class=""><a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false">
                            <?php echo $this->lang->line('common_item'); ?><!--Item--></a></li>
                <?php } ?>
                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierInvoice' || $pvType == 'SupplierItem') { ?>
                    <li class="tab_3Item <?php if ($pvType == 'SupplierInvoice') {
                        echo 'active';
                    } ?> <?php if ($pvType == 'SupplierItem') {
                        echo 'hide';
                    } ?>">
                        <a data-toggle="tab" class="boldtab" href="#tab_3" aria-expanded="false">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_invoice'); ?><!--Invoice--></a></li>

                    <li class="tab_3Item <?php if ($pvType == 'SupplierInvoice') {
                        echo 'active';
                    } ?> <?php if ($pvType == 'SupplierItem') {
                        echo 'hide';
                    } ?>">
                        <a data-toggle="tab" class="boldtab" href="#tab_7" aria-expanded="false">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_invoice_cus'); ?><!--Invoice--></a></li>
                      
                <?php } ?>
                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierInvoice' || $pvType == 'SupplierItem') { ?>
                    <li class="tab_5Item <?php if ($pvType == 'SupplierItem') {
                        echo 'hide';
                    } ?>">
                        <a data-toggle="tab" class="boldtab" href="#tab_5" aria-expanded="false">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_debit_note'); ?><!--Debit Note--> /
                            Purchase Return</a></li>
                <?php } ?>
                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierItem' || $pvType == 'SupplierAdvance') { ?>
                    <li class="tab_4Item <?php if ($pvType == 'SupplierAdvance') {
                        echo 'active';
                    } ?> <?php if ($pvType == 'SupplierItem') {
                        echo 'hide';
                    } ?>">
                        <a data-toggle="tab" class="boldtab" href="#tab_4" aria-expanded="false">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_debit_advance'); ?><!--Advance--></a>
                    </li>
                <?php } ?>

                <li class="pull-left header"><i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('accounts_payable_tr_pv_supplier_payment_for'); ?><!--Supplier Payment for-->
                    :
                    - <?php echo $master['partyName'] . " ( " . $master['partyCode'] . " )"; ?></li>
            </ul>
            <div class="tab-content">
                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierExpense' || $pvType == 'SupplierItem' || $pvType == 'SupplierItem' || $pvType == 'SupplierInvoice') { ?>
                    <div id="tab_1" class="tab-pane ">
                        <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                            <thead>
                            <tr>
                                <th colspan="4">
                                    <?php echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                                <?php if ($groupBasedTax == 1) { ?>
                                    <th colspan="4"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                                <?php } else { ?>
                                    <th colspan="3"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                                <?php } ?>
                                <th>
                                    <button type="button" onclick="pv_detail_modal()"
                                            class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_add_gl'); ?><!--Add GL-->
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th style="min-width: 3%">#</th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                                <th style="min-width: 30%">
                                    <?php echo $this->lang->line('common_gl_code_description'); ?><!--GL Code Description--></th>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                                <?php if($advanceCostCapturing == 1){ ?>
                                    <th style="min-width: 15%">Activity Code</th>
                                <?php } ?>
                                <th style="min-width: 12%">
                                    <?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span
                                            class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                                <th style="min-width: 12%">Discount <span
                                            class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                                <?php if ($groupBasedTax == 1) { ?>
                                    <th> Tax <span
                                                class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                <?php } ?>
                                <th style="min-width: 12%">Total <span
                                            class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>

                                <th style="min-width: 10%">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="gl_table_body">

                            <tr class="danger">
                                <td colspan="8" class="text-center"><b>
                                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot id="gl_table_tfoot">

                            </tfoot>
                        </table>
                    </div><!-- /.tab-pane -->

                    <div id="tab_6" class="tab-pane ">
                        <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                            <thead>
                            <tr>
                                <th colspan="4">
                                    <?php echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                                <?php if ($groupBasedTax == 1) { ?>
                                    <th colspan="4"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                                <?php } else { ?>
                                    <th colspan="3"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                                <?php } ?>
                                <th>
                                    <button type="button" onclick="pv_detail_modal('income')"
                                            class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_add_gl'); ?><!--Add GL-->
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th style="min-width: 3%">#</th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                                <th style="min-width: 30%">
                                    <?php echo $this->lang->line('common_gl_code_description'); ?><!--GL Code Description--></th>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                                <?php if($advanceCostCapturing == 1){ ?>
                                    <th style="min-width: 15%">Activity Code</th>
                                <?php } ?>   
                                <th style="min-width: 12%">
                                    <?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span
                                            class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                                <th style="min-width: 12%">Discount <span
                                            class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                                <?php if ($groupBasedTax == 1) { ?>
                                    <th> Tax <span
                                                class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                <?php } ?>
                                <th style="min-width: 12%">Total <span
                                            class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>

                                <th style="min-width: 10%">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody id="gl_table_income_body">
                            <tr class="danger">
                                <td colspan="8" class="text-center"><b>
                                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot id="gl_table_income_tfoot">

                            </tfoot>
                        </table>
                    </div><!-- /.tab-pane -->
                <?php } ?>
                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierItem') { ?>
                    <div id="tab_2" class="tab-pane">
                        <table class="table table-bordered table-striped table-condesed">
                            <thead>
                            <tr>
                                <th colspan="5"><?php echo $this->lang->line('accounts_payable_tr_pv_item_details'); ?>
                                    <!--Item Details--></th>
                                <?php if ($groupBasedTax == 1) { ?>
                                    <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                <?php } else { ?>
                                    <th colspan="2"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                <?php } ?>
                                <th>
                                    <button type="button" onclick="pv_item_detail_modal()"
                                            class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                        <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_code'); ?><!--Code--></th>

                                <?php if ($itemBatchPolicy == 1) { ?>
                                <th>Batch Number</th>
                                <?php } ?>

                                <th style="min-width: 36%">
                                    <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <?php if($advanceCostCapturing == 1){ ?>
                                    <th style="min-width: 15%">Activity Code</th>
                                <?php } ?>
                                <th style="min-width: 7%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                                <th style="min-width: 7%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                                <?php if ($groupBasedTax == 1) { ?>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                                <?php } ?>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                            </tr>
                            </thead>
                            <tbody id="item_table_body">

                            <tr class="danger">
                                <td colspan="8" class="text-center"><b>
                                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot id="item_table_tfoot">

                            </tfoot>
                        </table>
                    </div><!-- /.tab-pane -->
                <?php } ?>
                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierInvoice' || $pvType == 'SupplierItem') { ?>
                    <div id="tab_3" class="tab-pane <?php if ($pvType == 'SupplierInvoice') {
                        echo 'active';
                    } ?>">
                    <table class="table table-bordered table-striped table-condesed">
                            <thead>
                            <tr>
                                <th colspan="4"><?php echo $this->lang->line('accounts_payable_invoice_details'); ?>
                                    <!--Invoice Details--></th>
                                <th colspan="4"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                                <th>
                                    <button type="button" data-toggle="modal" data-target="#inv_base_modal"
                                            class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                        <?php echo $this->lang->line('accounts_payable_add_invoice'); ?><!--Add Invoice-->
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                                <th style="min-width: 15%" class="text-left">
                                    <?php echo $this->lang->line('common_reference'); ?><!--Reference--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_invoice'); ?><!--Invoice--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_due'); ?><!--Due--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_paid'); ?><!--Paid--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_balance'); ?><!--Balance--></th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                            </tr>
                            </thead>
                            <tbody id="invoice_table_body">
                            <tr class="danger">
                                <td colspan="9" class="text-center"><b>
                                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot id="invoice_table_tfoot">

                            </tfoot>
                        </table>
                    </div><!-- /.tab-pane -->
                <?php } ?>
                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierInvoice' || $pvType == 'SupplierItem') { ?>
                    <div id="tab_5" class="tab-pane">
                        <table class="table table-bordered table-striped table-condesed">
                            <thead>
                            <tr>
                                <th colspan="4"><?php echo $this->lang->line('accounts_payable_tr_pv_debit_note_details'); ?>
                                    Debit Note / Purchase Return
                                </th>
                                <th colspan="4"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                                <th>
                                    <button type="button" data-toggle="modal" data-target="#debitNote_base_modal"
                                            class="btn btn-primary pull-right btn-xs"><i
                                                class="fa fa-plus"> </i><?php echo $this->lang->line('accounts_payable_tr_pv_add_debit_note'); ?>
                                        <!--Add Debit Note-->
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                                <th style="min-width: 15%" class="text-left">
                                    <?php echo $this->lang->line('common_reference'); ?><!--Reference--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_debit_note'); ?><!--Debit Note-->
                                    / Purchase Return
                                </th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_due'); ?><!--Due--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_matched'); ?><!--Matched--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_balance'); ?><!--Balance--></th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                            </tr>
                            </thead>
                            <tbody id="debitNote_table_body">
                            <tr class="danger">
                                <td colspan="9" class="text-center"><b>
                                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot id="debitNote_table_tfoot">

                            </tfoot>
                        </table>
                    </div><!-- /.tab-pane -->
                <?php } ?>
                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierAdvance' || $pvType == 'SupplierItem') { ?>
                    <div id="tab_4" class="tab-pane <?php if ($pvType == 'SupplierAdvance') {
                        echo 'active';
                    } ?>">
                        <table class="table table-bordered table-striped table-condesed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_po_code'); ?><!--PO Code--></th>
                                <th style="min-width: 45%">
                                    <?php echo $this->lang->line('common_description'); ?><!--PO Description--></th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_date'); ?><!--DATE--></th>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span
                                            class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                                <th style="min-width: 10%">
                                    <button type="button" onclick="pv_po_detail_modal()"
                                            class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                        <?php echo $this->lang->line('accounts_payable_tr_add_advance'); ?><!--Add Advance-->
                                    </button>
                                </th>
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
                <?php } ?>

                <?php if ($pvType == 'Supplier' || $pvType == 'SupplierInvoice' || $pvType == 'SupplierItem') { ?>
                    <div id="tab_7" class="tab-pane <?php if ($pvType == 'SupplierInvoice') {
                        echo 'active';
                    } ?>">
                    <table class="table table-bordered table-striped table-condesed">
                            <thead>
                            <tr>
                                <th colspan="4"><?php echo $this->lang->line('accounts_payable_customer_invoice_details'); ?>
                                    <!--Invoice Details--></th>
                                <th colspan="4"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                                <th>
                                    <button type="button" data-toggle="modal" data-target="#inv_base_customer_modal"
                                            class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                        <?php echo $this->lang->line('accounts_payable_add_invoice'); ?>
                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 15%">
                                    <?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                                <th style="min-width: 15%" class="text-left">
                                    <?php echo $this->lang->line('common_reference'); ?><!--Reference--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_customer_invoice_no'); ?><!--Invoice--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_due'); ?><!--Due--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_paid'); ?><!--Paid--></th>
                                <th style="min-width: 11%">
                                    <?php echo $this->lang->line('accounts_payable_balance'); ?><!--Balance--></th>
                                <th style="min-width: 10%">
                                    <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                            </tr>
                            </thead>
                            <tbody id="cus_invoice_table_body">
                                <tr class="danger">
                                    <td colspan="9" class="text-center"><b>
                                            <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot id="cus_invoice_table_tfoot">

                            </tfoot>
                        </table>
                    </div><!-- /.tab-pane -->
                <?php } ?>
            </div><!-- /.tab-content -->
            <br>
            <?php if ($groupBasedTax != 1) { ?>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <label for="exampleInputName2" id="tax_tot">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_tax_for'); ?><!--Tax for--> </label>
                        <form class="form-inline" id="tax_form">
                            <div class="form-group">
                                <?php echo form_dropdown('text_type', all_tax_drop(2), '', 'class="form-control" id="text_type" required onchange="select_text(this)" style="width: 150px;"'); ?>
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
                                       style="width: 100px;" onkeypress="return validateFloatKeyPress(this,event);"
                                       onkeyup="cal_tax_amount(this.value)">
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <table class="<?php echo table_class(); ?>">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo $this->lang->line('accounts_payable_tax_type'); ?><!--Tax Type--></th>
                                <th><?php echo $this->lang->line('accounts_payable_tr_pv_detail'); ?><!--Detail--></th>
                                <th><?php echo $this->lang->line('common_tax'); ?><!--Tax--></th>
                                <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency">(LKR)</span>
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
        <div aria-hidden="true" role="dialog" id="pv_po_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h5 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_add_po_detail'); ?><!--Add PO Detail--></h5>
                    </div>
                    <form role="form" id="pv_po_detail_form" class="form-horizontal">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_po_code'); ?><!--PO Code--> </label>

                                <div class="col-sm-6">
                                    <?php echo form_dropdown('po_code', $po_arr, '', 'class="form-control select2" onchange="get_po_amount()" id="po_code" '); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">PO Amount</label>
                                <div class="col-sm-6">
                                    <input type="text" id="poamount" class="form-control number" value="0" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Advanced Amount</label>
                                <div class="col-sm-6">
                                    <input type="text" id="advancedamount" class="form-control number" value="0"
                                           readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Balance Amount</label>
                                <div class="col-sm-6">
                                    <input type="text" id="balancedamount" class="form-control number" value="0"
                                           readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('common_amount'); ?><!--Amount--> </label>

                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <div class="input-group-addon"><span
                                                    class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                                )</span>
                                        </div>
                                        <input type="text" onkeypress="return validateFloatKeyPress(this,event)"
                                               onkeyup="check_low_than_poa_mount()" name="amount" id="amount" value="00"
                                               class="form-control number">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('common_description'); ?><!--Description--> </label>

                                <div class="col-sm-6">
                                    <textarea class="form-control" rows="2" id="description"
                                              name="description"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">
                                <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                            <button class="btn btn-primary" type="">
                                <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" id="pv_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 96%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_add_expenses_detail'); ?><!--Add Expenses Detail--></h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="pv_detail_form" class="form-horizontal">

                        <input type="hidden" name="GL_Type" id="GL_Type" value="" />

                            <table class="table table-bordered table-condensed no-color" id="payment_voucher_table">
                                <thead>
                                <tr>
                                    <th style="width: 350px;">
                                        <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                    <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>

                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } 
                                    if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2" id="taxColumn">Tax</th>
                                    <?php } ?>
                                    <th style="width: 130px;font-size: 11px !important;">Net Amount <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_remarks'); ?><!--Description--> <?php required_mark(); ?></th>
                                    <th style="width: 40px;">
                                        <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                                    class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="form-group" style="<?php echo $stylewidth6 ?>">
                                        <?php echo form_dropdown('gl_code[]', $gl_code_arr_income, '', 'class="form-control select2" id="gl_code" required'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment_glAdd" id="segment_gl" onchange="load_segmentBase_projectID_income(this)"'); ?>

                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                            <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                        </td>
                                    <?php } 
                                    if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div class="div_projectID_income">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID[]', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID[]', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount[]" id="amount"
                                               onkeyup="calculateNetAmount(this,'amount')"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control amount number">
                                    </td>
                                    <td><input type="text" name="discountPercentage[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeyup="calculateNetAmount(this,'discountPercentage')" value="00"
                                               class="form-control number discountPercentage"></td>
                                    <td><input type="text" name="discountAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeyup="calculateNetAmount(this,'discountAmount')" value="00"
                                               class="form-control number discountAmount"></td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td id="taxColumnData"><?php echo form_dropdown('gl_text_type[]', all_tax_formula_drop_groupByTax(), '', 'class="form-control gl_text_type" style="width: 134px;" onchange="load_gl_line_tax_amount(this)" '); ?></td>
                                        <td id="taxColumnDataValue"><span class="gl_linetaxamnt pull-right"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td><input type="text" name="Netamount[]" value="00"
                                               class="form-control number Netnumber" readonly></td>
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
                        <button data-dismiss="modal" class="btn btn-default"
                                type="button"><?php echo $this->lang->line('common_Close'); ?> <!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="savePaymentVoucher_Expenses()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" id="edit_pv_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 96%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_edit_expenses_detail'); ?><!--Edit Expenses Detail--></h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="edit_pv_detail_form" class="form-horizontal">
                            <input type="hidden" id="edit_gl_type" name="type" value="" />
                            <table class="table table-bordered table-condensed no-color"
                                   id="edit_payment_voucher_table">
                                <thead>
                                <tr>
                                    <th style="width: 350px;">
                                        <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                    <th><!--Segment--><?php echo $this->lang->line('common_segment'); ?></th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php }
                                    if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2" id="edittaxColumn"> Tax</th>
                                    <?php } ?>
                                    <th>Net Amount <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="form-group" style="<?php echo $stylewidth6 ?>">
                                        <?php echo form_dropdown('gl_code', $gl_code_arr_income, '', 'class="form-control select2" id="edit_gl_code" required '); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="edit_segment_gl" onchange="load_segmentBase_projectID_incomeEdit(this)"'); ?>

                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                            <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                        </td>
                                    <?php }
                                    if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div id="edit_div_projectID_income">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount"
                                               onkeyup="calculateNetAmount_edit(this,'amount')" id="edit_amount"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number">
                                    </td>
                                    <td><input type="text" name="discountPercentage" id="discountPercentage_edit"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeyup="calculateNetAmount_edit(this,'discountPercentage')" value="00"
                                               class="form-control number "></td>
                                    <td><input type="text" name="discountAmount" id="discountAmount_edit"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeyup="calculateNetAmount_edit(this,'discountAmount')" value="00"
                                               class="form-control number "></td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td id="edittaxColumnData"><?php echo form_dropdown('gl_text_type', all_tax_formula_drop_groupByTax(), '', 'class="form-control gl_text_type" style="width: 134px;" id="gl_text_type_edit" onchange="load_gl_line_tax_amount_edit(this)" '); ?></td>

                                        <td id="edittaxColumnDataValue"><span class="gl_linetaxamnt pull-right" id="gl_linetaxamnt_edit"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td><input type="text" name="Netamount" id="Netamount_edit" value="00"
                                               class="form-control number " readonly></td>
                                    <td>
                                        <textarea class="form-control" rows="1" id="edit_description"
                                                  name="description"></textarea>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="Update_PaymentVoucher_Expenses()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" id="pv_item_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 97%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h5 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_add_item_detail'); ?><!--Add Item Detail--></h5>
                    </div>

                    <div class="modal-body">
                        <form role="form" id="pv_item_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed" id="payment_voucher_Item_table">
                                <thead>
                                <tr>
                                    <th>
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                    <th>
                                        <?php echo $this->lang->line('common_warehouse'); ?><!--Warehouse--> <?php required_mark(); ?></th>
                                    
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
                                    <th style="width: 100px;">
                                        <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                    <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2"
                                            style="width: 120px;"><?php echo $this->lang->line('common_tax'); ?></th>
                                    <?php } ?>
                                    <th style="width: 130px;font-size: 11px !important;">
                                        <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_remarks'); ?><!--Comment--></th>
                                    <th style="width: 40px;">
                                        <button type="button" class="btn btn-primary btn-xs"
                                                onclick="add_more_item()"><i
                                                    class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control search input-mini f_search"
                                               name="search[]" id="f_search_1"
                                               placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?>..."
                                               onkeydown="remove_item_all_description(event,this)"><!--Item ID-->
                                        <!--Item Description-->
                                        <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 input-mini wareHouseAutoID" onchange="checkitemavailable(this)" '); ?>
                                    </td>

                                    <?php if($itemBatchPolicy == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('batch_number[0][]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_1" multiple="multiple" required'); ?>
                                        </td>
                                    <?php } ?>


                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div class="div_projectID_item">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID[]', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID[]', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                        </td>

                                    <?php } ?>
                                    <td><?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown input-mini"  required'); ?>
                                    </td>
                                    <td><input type="text" onchange="load_line_tax_amount(this), change_amount(this,1)"
                                               name="quantityRequested[]"

                                               onkeyup="validatetb_row(this)"
                                               class="form-control quantityRequested number input-mini" required>
                                    </td>
                                    <td>
                                        <input type="text" onchange="load_line_tax_amount(this), change_amount(this,1)"
                                               name="estimatedAmount[]"
                                               onkeyup="validatetb_row(this)"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number estimatedAmount input-mini">
                                    </td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td>
                                            <?php echo form_dropdown('item_text[]', array('' => 'Select Tax Types'), '', 'class="form-control item_text input-mini" id="" onchange="load_line_tax_amount(this)"'); ?>
                                        </td>
                                        <td><span class="linetaxamnt pull-right"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" onchange="change_amount(this,2)" name="netAmount[]"

                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number netAmount input-mini">
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                            <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <td><textarea class="form-control input-mini" rows="1" name="comment[]"
                                                  placeholder="<?php echo $this->lang->line('accounts_payable_tr_pv_item_comment'); ?>..."></textarea>
                                        <!--Item Comment-->
                                    </td>
                                    <td class="remove-td" style="vertical-align: middle;text-align: center">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="savePaymentVoucher_ID_item()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" tabindex="-1" id="edit_pv_item_detail_modal" class="modal fade"
             style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 97%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_edit_item_detail'); ?><!--Edit Item Detail--></h4>
                    </div>

                    <div class="modal-body">
                        <form role="form" id="edit_pv_item_detail_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed" id="edit_payment_voucher_Item_table">
                                <thead>
                                <tr>
                                    <th>
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_item_code'); ?><!--Item Code--> <?php required_mark(); ?></th>
                                    <th>
                                        <?php echo $this->lang->line('common_warehouse'); ?><!--Warehouse--> <?php required_mark(); ?>
                                    </th>
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
                                    <th style="width: 100px;">
                                        <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                    <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2" style="width: 120px;">
                                            <?php echo $this->lang->line('common_tax'); ?><!--Tax--> </th>
                                    <?php } ?>
                                    <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                        <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="form-control input-mini" name="search"
                                               placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?>..."
                                               id="search"
                                               onkeydown="remove_item_all_description_edit(event,this)"><!--Item ID-->
                                        <!--Item Description-->
                                        <input type="hidden" class="form-control" name="itemAutoID"
                                               id="edit_itemAutoID">

                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2 input-mini" onChange="load_batch_number_single_edit_p_voucher(this)" id="edit_wareHouseAutoID"'); ?>
                                    </td>
                                    <?php if($itemBatchPolicy == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('batch_number[]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_edit"  multiple="multiple" required'); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div id="edit_div_projectID_item">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>

                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit1" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit1"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control input-mini"  id="edit_UnitOfMeasureID" required'); ?>
                                    </td>
                                    <td><input type="text"
                                               onchange="load_line_tax_amount_edit(this), change_amount_edit(this,1)"
                                               name="quantityRequested" placeholder="0.00"
                                               class="form-control number input-mini" id="edit_quantityRequested"
                                               required>
                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,1)" name="estimatedAmount"
                                               class="form-control number input-mini"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               id="edit_estimatedAmount">
                                    </td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td>
                                            <?php echo form_dropdown('item_text', array('' => 'Select Tax Types'), '', 'class="form-control item_text input-mini" id="edit_item_text" onchange="load_line_tax_amount_edit(this)"'); ?>
                                        </td>
                                        <td><span class="pull-right linetaxamnt_edit" id="linetaxamnt_edit"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,2)" id="editNetAmount"
                                               name="netAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number netAmount input-mini">
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <td><textarea class="form-control input-mini" rows="1" name="comment"
                                                  placeholder="<?php echo $this->lang->line('accounts_payable_tr_pv_item_comment'); ?>..."
                                                  id="edit_comment"></textarea><!--Item Comment-->
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="Update_PaymentVoucher_ID_item()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    case "SC":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class=""><a data-toggle="tab" class="boldtab" href="#tab_1" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_payable_tr_commission'); ?><!--Commission--></a></li>
                <li class="pull-left header"><i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('accounts_payable_tr_sales_person'); ?><!--Sales Person--> :
                    - <?php echo $master['partyName'] . " ( " . $master['partyCode'] . " )"; ?></li>
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4">
                                <?php echo $this->lang->line('accounts_payable_tr_commission_details'); ?><!--Commission Details--></th>
                            <th colspan="4"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                        class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                            <th>
                                <button type="button" data-toggle="modal" data-target="#commission_base_modal"
                                        class="btn btn-primary pull-right btn-xs"><i
                                            class="fa fa-plus"></i> <?php echo $this->lang->line('accounts_payable_tr_add_commission'); ?>
                                    <!--Add Commission-->
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
                                <?php echo $this->lang->line('accounts_payable_tr_commission'); ?><!--Commission--></th>
                            <th style="min-width: 11%">
                                <?php echo $this->lang->line('accounts_payable_tr_pv_due'); ?><!--Due--></th>
                            <th style="min-width: 11%">
                                <?php echo $this->lang->line('accounts_payable_tr_pv_paid'); ?><!--Paid--></th>
                            <th style="min-width: 11%">
                                <?php echo $this->lang->line('accounts_payable_balance'); ?><!--Balance--></th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                        </tr>
                        </thead>
                        <tbody id="commission_table_body">
                        <tr class="danger">
                            <td colspan="9" class="text-center"><b>
                                    <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot id="commission_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
            </div><!-- /.tab-content -->
            <br>
        </div>

        <div class="modal fade" id="commission_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             data-width="95%" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">
                            <?php echo $this->lang->line('accounts_payable_tr_commission_base'); ?><!--Commission Base--></h4>
                    </div>
                    <div class="modal-body">
                        <form id="sales_commission_detail_form">
                            <input type="hidden" name="payVoucherAutoId"
                                   value="<?php echo $master['payVoucherAutoId']; ?>">
                            <input type="hidden" name="salesPersonID" value="<?php echo $master['partyID']; ?>">
                            <table class="table table-bordered table-striped table-condesed ">
                                <thead>
                                <tr>
                                    <th colspan="3">
                                        <?php echo $this->lang->line('accounts_payable_tr_commission_details'); ?><!--Commission Details--></th>
                                    <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 20%">
                                        <?php echo $this->lang->line('accounts_payable_tr_commission_code'); ?><!--Commission Code--></th>
                                    <th style="width: 20%">
                                        <?php echo $this->lang->line('accounts_payable_reference_no'); ?><!--Reference No--></th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('accounts_payable_tr_commission_total'); ?><!--Commission Total--></th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('accounts_payable_balance'); ?><!--Balance--></th>
                                    <th style="width: 15%">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                                    <!-- <th >&nbsp;</th> -->
                                </tr>
                                </thead>
                                <tbody id="commission_detail_table_body">
                                <?php
                                $i = 1;
                                if (!empty($sales_commission)) {
                                    foreach ($sales_commission as $val) {
                                        echo "<tr>";
                                        echo "<td>" . $i . "</td>";
                                        echo "<td>" . $val["salesCommisionCode"] . "</td>";
                                        echo "<td>" . $val["referenceNo"] . "</td>";
                                        echo "<td class='text-right'>" . number_format($val["netCommision"], $val["transactionCurrencyDecimalPlaces"]) . "</td>";
                                        echo "<td class='text-right'>" . number_format($val["balance"], $val["transactionCurrencyDecimalPlaces"]) . "</td>";
                                        echo '<td><input type="text" name="amount[]" data-balance="' . $val["balance"] . '" data-invoiceautoid="' . $val["salesCommisionID"] . '" data-dueamount="' . $val["balance"] . '"  id="sales_amount_' . $val['salesCommisionID'] . '" onkeyup="amount_validation(this)" onkeypress="return validateFloatKeyPress(this,event)"  class="number"></td>';
                                        echo "</tr>";
                                        $i++;
                                    }
                                } else {
                                    $norec = $this->lang->line('common_no_records_found');
                                    echo '<tr class="danger"><td colspan="7" class="text-center"><b>' . $norec . '<!--No Records Found--></b></td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button type="button" class="btn btn-primary" onclick="save_commission_base_items()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    case "PurchaseRequest":
        ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1" aria-expanded="false">
                        <?php //echo $this->lang->line('accounts_payable_tr_pv_expences');
                        ?><!--Expenses-->
                        <?php echo $this->lang->line('common_expence'); ?> GL<!--Expense GL--></a></li>
                <li class=""><a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false">Purchase
                        Request</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table id="debit_note_detail_table" class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                            <?php if ($groupBasedTax == 1) { ?>
                                <th colspan="4"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } else { ?>
                                <th colspan="3"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            <?php } ?>
                            <th>
                                <button type="button" onclick="pv_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('accounts_payable_tr_pv_add_gl'); ?><!--Add GL-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 3%">#</th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--></th>
                            <th style="min-width: 30%">
                                <?php echo $this->lang->line('accounts_payable_gl_code_description'); ?><!--GL Code Description--></th>

                            <th style="min-width: 15%">
                                <?php echo $this->lang->line('common_remarks'); ?><!--Segment--></th>

                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="min-width: 15%">Activity Code</th>
                            <?php } ?>    
                            <th style="min-width: 12%">
                                <?php echo $this->lang->line('common_transaction'); ?><!--Transaction--> <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 12%">Discount <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <?php if ($groupBasedTax == 1) { ?>
                                <th style="min-width: 12%">Tax <span
                                            class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                            <?php } ?>
                            <th style="min-width: 12%">Total <span
                                        class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 10%"></th>

                        </tr>
                        </thead>
                        <tbody id="gl_table_body">

                        <tr class="danger">
                            <td colspan="8" class="text-center"><b>
                                    <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot id="gl_table_tfoot">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <div id="tab_2" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="5">
                                <?php echo $this->lang->line('accounts_payable_tr_pv_item_details'); ?><!--Item Details--></th>
                            <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                        class="currency">(LKR)</span>
                            </th>
                            <th>
                                <button type="button" onclick="purchase_request_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                            <th style="min-width: 25%" class="text-left">
                                <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th style="min-width: 5%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                            <th style="min-width: 5%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                            <th style="min-width: 10%">
                                <?php echo $this->lang->line('common_net_cost'); ?><!--Net Cost--></th>
                            <th style="min-width: 12%"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                            <th style="min-width: 8%">
                                <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                        </tr>
                        </thead>
                        <tbody id="table_body_prq">
                        <tr class="danger">
                            <td colspan="9" class="text-center"><b>
                                    <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records Found--></b>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot id="table_tfoot_prq">

                        </tfoot>
                    </table>
                </div><!-- /.tab-pane -->
                <?php if ($groupBasedTax != 1) { ?>
                    <br>
                    <div class="row">
                        <div class="col-md-5">
                            <label for="exampleInputName2" id="tax_tot">
                                <?php echo $this->lang->line('accounts_payable_tr_pv_tax_for'); ?><!--Tax for--> </label>
                            <form class="form-inline" id="tax_form">
                                <div class="form-group">
                                    <?php echo form_dropdown('text_type', all_tax_drop(2), '', 'class="form-control" id="text_type" required onchange="select_text(this)" style="width: 150px;"'); ?>
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
                                           style="width: 100px;" onkeypress="return validateFloatKeyPress(this,event);"
                                           onkeyup="cal_tax_amount(this.value)">
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
                                        <?php echo $this->lang->line('accounts_payable_tax_type'); ?><!--Tax Type--></th>
                                    <th>
                                        <?php echo $this->lang->line('accounts_payable_tr_pv_detail'); ?><!--Detail--></th>
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
                <br>
            </div><!-- /.tab-content -->
        </div>


        <div aria-hidden="true" role="dialog" id="pv_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 96%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_add_expenses_detail'); ?><!--Add Expenses Detail-->
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="pv_detail_form" class="form-horizontal">

                           

                            <table class="table table-bordered table-condensed no-color" id="payment_voucher_table">
                                <thead>
                                <tr>
                                    <th style="width: 350px;">
                                        <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                    <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>

                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="width: 100px">Activity Code</th>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2">Tax</th>
                                    <?php } ?>
                                    <th>Net Amount <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                                    <th style="width: 40px;">
                                        <button type="button" class="btn btn-primary btn-xs" onclick="add_more()"><i
                                                    class="fa fa-plus"></i></button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="form-group" style="<?php echo $stylewidth6 ?>">
                                        <?php echo form_dropdown('gl_code[]', $gl_code_arr_income, '', 'class="form-control select2" id="gl_code" required'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment_glAdd" id="segment_gl" onchange="load_segmentBase_projectID_income(this)"'); ?>
                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div class="div_projectID_income">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID[]', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"'); ?>

                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID[]', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"'); ?>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount[]" id="amount"
                                               onkeyup="calculateNetAmount(this,'amount')"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control amount number">
                                    </td>
                                    <td><input type="text" name="discountPercentage[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeyup="calculateNetAmount(this,'discountPercentage')" value="00"
                                               class="form-control number discountPercentage"></td>
                                    <td><input type="text" name="discountAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount(this)"
                                               onkeyup="calculateNetAmount(this,'discountAmount')" value="00"
                                               class="form-control number discountAmount"></td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td><?php echo form_dropdown('gl_text_type[]', all_tax_formula_drop_groupByTax(), '', 'class="form-control gl_text_type" style="width: 134px;" onchange="load_gl_line_tax_amount(this)" '); ?></td>
                                        <td><span class="gl_linetaxamnt pull-right"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td><input type="text" name="Netamount[]" value="00"
                                               class="form-control number Netnumber" readonly></td>
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
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="savePaymentVoucher_Expenses()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <div aria-hidden="true" role="dialog" id="edit_pv_detail_modal" class="modal fade" style="display: none;">
            <div class="modal-dialog" style="width: 90%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            <?php echo $this->lang->line('accounts_payable_tr_pv_edit_expenses_detail'); ?><!--Edit Expenses Detail--></h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="edit_pv_detail_form" class="form-horizontal">

                            <table class="table table-bordered table-condensed no-color"
                                   id="edit_payment_voucher_table">
                                <thead>
                                <tr>
                                    <th style="width: 350px;">
                                        <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                    <th><?php echo $this->lang->line('common_segment'); ?><!--Segment--></th>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <th style="min-width: 100px;">Activity Code</th>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> </th>
                                        <th>Project Category</th>
                                        <th>Project Subcategory</th>
                                    <?php } ?>
                                    <th style="width: 150px;">
                                        <?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <th colspan="2" > Tax</th>
                                    <?php } ?>
                                    <th>Net Amount <span
                                                class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <th style="width: 200px;">
                                        <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="form-group" style="<?php echo $stylewidth6 ?>">
                                        <?php echo form_dropdown('gl_code', $gl_code_arr_income, '', 'class="form-control select2" id="edit_gl_code" required'); ?>
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('segment_gl', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="edit_segment_gl" onchange="load_segmentBase_projectID_incomeEdit(this)"'); ?>

                                    </td>
                                    <?php if($advanceCostCapturing == 1){ ?>
                                        <td>
                                        <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($projectExist == 1) { ?>
                                        <td class="form-group" style="<?php echo $stylewidth1 ?>">
                                            <div id="edit_div_projectID_income">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_categoryID', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID_edit" onchange="fetch_project_sub_category(this, this.value)"'); ?>
                                        </td>
                                        <td class="form-group" style="<?php echo $stylewidth2 ?>">
                                            <?php echo form_dropdown('project_subCategoryID', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID_edit"'); ?>
                                        </td>

                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount" id="edit_amount"
                                               onkeyup="calculateNetAmount_edit(this,'amount')"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number">
                                    </td>
                                    <td><input type="text" name="discountPercentage" id="discountPercentage_edit"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeyup="calculateNetAmount_edit(this,'discountPercentage')" value="00"
                                               class="form-control number "></td>
                                    <td><input type="text" name="discountAmount" id="discountAmount_edit"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onchange="load_gl_line_tax_amount_edit(this)"
                                               onkeyup="calculateNetAmount_edit(this,'discountAmount')" value="00"
                                               class="form-control number "></td>
                                    <?php if ($groupBasedTax == 1) { ?>
                                        <td id="edittaxColumnData"><?php echo form_dropdown('gl_text_type', all_tax_formula_drop_groupByTax(), '', 'class="form-control gl_text_type" style="width: 134px;" id="gl_text_type_edit" onchange="load_gl_line_tax_amount_edit(this)" '); ?></td>

                                        <td id="edittaxColumnDataValue"><span class="gl_linetaxamnt pull-right" id="gl_linetaxamnt_edit"
                                                  style="font-size: 14px;text-align: right;margin-top: 8%;">0</span>
                                        </td>
                                    <?php } ?>
                                    <td><input type="text" name="Netamount" id="Netamount_edit" value="00"
                                               class="form-control number " readonly></td>
                                    <td>
                                        <textarea class="form-control" rows="1" id="edit_description"
                                                  name="description"></textarea>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="Update_PaymentVoucher_Expenses()">
                            <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                        </button>
                    </div>

                </div>
            </div>
        </div>


        <div class="modal fade" id="prq_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             data-width="95%" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width: 85%">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Purchase Request Base</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="box box-widget widget-user-2">
                                    <div class="widget-user-header bg-yellow">
                                        <h5>Purchase Request</h5>
                                    </div>
                                    <div class="box-footer no-padding">
                                        <ul class="nav nav-stacked" id="prqcode">


                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <table class="table table-bordered table-striped table-condesed ">
                                    <thead>
                                    <tr>
                                        <th colspan='4'>Item</th>
                                        <th colspan='2'>Requested Item <span
                                                    class="currency"> </span></th>
                                        <th colspan='4'>Purchased Item <span
                                                    class="currency"> </span></th>
                                    <tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th class="text-left">Description</th>
                                        <th class="text-left">Warehouse</th>
                                        <th>UOM</th>
                                        <th>Qty</th>
                                        <th>Cost</th>
                                        <th>Qty</th>
                                        <th>Cost</th>
                                        <th>Total</th>
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
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="save_prq_base_items()">Save changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div aria-hidden="true" role="dialog" tabindex="-1" id="purchase_request_detail_edit_modal" class="modal fade"
             style="display: none;">
            <div class="modal-dialog modal-lg" style="width: 90%;">
                <div class="modal-content">
                    <div class="color-line"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h5 class="modal-title">Edit Item Detail</h5>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="purchase_request_detail_edit_form" class="form-horizontal">
                            <table class="table table-bordered table-condensed no-color" id="po_detail_edit_table">
                                <thead>
                                <tr>
                                    <th style="width: 200px;">Item Code <?php required_mark(); ?></th>
                                    <th style="width: 200px;">Warehouse <?php required_mark(); ?></th>
                                    <th style="width: 150px;">UOM <?php required_mark(); ?></th>
                                    <th style="width: 100px;">PV Qty <?php required_mark(); ?></th>
                                    <th style="width: 150px;">Unit Cost <span
                                                class="currency"> (LKR)</span> <?php required_mark(); ?></th>
                                    <th style="width: 100px;">Net Amount</th>
                                    <th style="width: 150px;">Comment</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <input type="text" id="search_edit"
                                               class="form-control" name="search"
                                               placeholder="Item ID, Item Description..." readonly>
                                        <input type="hidden" id="itemAutoID_edit" class="form-control"
                                               name="itemAutoID">
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2 input-mini" id="edit_wareHouseAutoID"'); ?>
                                    </td>
                                    <td><?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control" id="edit_UnitOfMeasureID"  required'); ?></td>

                                    <td><input type="text" name="quantityRequested" id="quantityRequested_edit"
                                               value="0"
                                               onkeyup="change_qty_edit()" class="form-control number"
                                               required onfocus="this.select();"><input type="hidden" id="prQtyEdit">
                                    </td>
                                    <td><input type="text" name="estimatedAmount" value="0" id="estimatedAmount_edit"
                                               placeholder="0.00" onkeyup="change_amount_edit_prq()"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number" onfocus="this.select();"></td>
                                    <td>&nbsp;<span id="totalAmount_edit" class="net_amount pull-right"
                                                    style="font-size: 14px;text-align: right;margin-top: 8%;">0.00</span>
                                    </td>
                                    <td><textarea class="form-control" rows="1" id="comment_edit" name="comment"
                                                  placeholder="Item Comment..."></textarea></td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        <button class="btn btn-primary" type="button" onclick="updatePurchaseRequestDetails()">Update
                            changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php
        break;
    default:
        echo "No records found";
}
?>

<script type="text/javascript">
    var search_id = 1;
    var payVoucherAutoId;
    var payVoucherDetailAutoID;
    var pvType;
    var partyID;
    var currencyID;
    var tax_total;
    var tab;
    var projectID_income;
    var projectID_item;
    var currency_decimal;
    var currentEditWareHouseAutoID='';
    var currentEditTextBatchData='';
    var defaultSegment = <?php echo json_encode($this->common_data['company_data']['default_segment']); ?>;
    var projectcategory;
    var projectsubcat;
    var isGroupWiseTax = <?php echo json_encode(trim($groupBasedTax)); ?>;
    var showPurchasePrice = <?php echo $showPurchasePrice ?>;
    $(document).ready(function () {
        $('.select2').select2();
        payVoucherDetailAutoID = null;
        projectID_income = null;
        projectID_item = null;
        payVoucherAutoId = <?php echo json_encode(trim($master['payVoucherAutoId'] ?? '')); ?>;
        pvType = <?php echo json_encode(trim($master['pvType'] ?? '')); ?>;
        partyID = <?php echo json_encode(trim($master['partyID'] ?? '')); ?>;
        currencyID = <?php echo json_encode(trim($master['transactionCurrency'] ?? '')); ?>;
        currency_decimal = <?php echo json_encode(trim($master['transactionCurrencyDecimalPlaces'] ?? '')); ?>;
        setTimeout(function () {
            fetch_pv_direct_details(<?php echo json_encode(trim($tab)) ?>);
        }, 300);
        initializeitemTypeahead();
        initializeitemTypeahead_edit();
        $('.currency').html('( ' + currencyID + ' )');
        number_validation();
        $('#sales_rep_payment').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                salesPersonID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_sales_person_is_required');?>.'}}}, /*Sales Person is required*/
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}, /*Description is required*/
                transactionAmount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_ammount_is_required');?>.'}}}/*Amount is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'payVoucherAutoId', 'value': payVoucherAutoId});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payment_voucher/save_sales_rep_payment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data['type'], data['data'], 1000);
                    stopLoad();
                    if (data['status']) {
                        $form.bootstrapValidator('resetForm', true);
                        setTimeout(function () {
                            load_conformation();
                            $('[href=#step3]').tab('show');
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

        $('#tax_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                tax_amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_tax_amount_is_required');?>.'}}}, /*Tax Amount is required*/
                text_type: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_tax_type_is_required');?>.'}}}, /*Tax Type is required*/
                percentage: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_percentage_is_required');?>.'}}}/*Percentage is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'payVoucherAutoId', 'value': payVoucherAutoId});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payment_voucher/save_inv_tax_detail'); ?>",
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

        $('#pv_po_detail_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                /* po_code     : {validators : {notEmpty:{message:'PO Code Requested is required.'}}},*/
                amount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_ammount_is_required');?>.'}}}, /*Amount is required*/
                description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}/*Description is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'payVoucherAutoId', 'value': payVoucherAutoId});
            data.push({'name': 'payVoucherDetailAutoID', 'value': payVoucherDetailAutoID});
            data.push({'name': 'po_des', 'value': $('#po_code option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payment_voucher/save_pv_po_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $form.bootstrapValidator('resetForm', true);
                    payVoucherDetailAutoID = null;
                    refreshNotifications(true);
                    stopLoad();
                    $('#pv_po_detail_modal').modal('hide');
                    if (data['status']) {
                        setTimeout(function () {
                            fetch_pv_direct_details(1);
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


        $('#pv_item_detail_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                search: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_item_is_required');?>.'}}}, /*Item is required*/
                itemAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_item_is_required');?>.'}}}, /*Item is required*/
                wareHouseAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_warehouse_required');?>.'}}}, /*Ware House is required*/
                UnitOfMeasureID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_uom_is_required');?>.'}}}, /*Unit Of Measure is required*/
                quantityRequested: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_tr_qty_is_required');?>.'}}}, /*Quantity is required*/
                estimatedAmount: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_ammount_is_required');?>.'}}}/*Amount is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            if (payVoucherAutoId) {
                data.push({'name': 'payVoucherAutoId', 'value': payVoucherAutoId});
                data.push({'name': 'payVoucherDetailAutoID', 'value': payVoucherDetailAutoID});
                data.push({'name': 'wareHouse', 'value': $('#wareHouseAutoID option:selected').text()});
                data.push({'name': 'uom', 'value': $('#UnitOfMeasureID option:selected').text()});
                $.ajax(
                    {
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Payment_voucher/save_pv_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            $form.bootstrapValidator('resetForm', true);
                            payVoucherDetailAutoID = null;
                            $('#pv_item_detail_modal').modal('hide');
                            stopLoad();
                            refreshNotifications(true);
                            if (data['status']) {
                                setTimeout(function () {
                                    fetch_pv_direct_details(3);
                                }, 300);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
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
        });


        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
    });

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

    function pv_item_detail_modal() {
        if (payVoucherAutoId) {
            $('.search').typeahead('destroy');
            $('select[name="wareHouseAutoID[]"]').val('').change();
            $('#pv_item_detail_form')[0].reset();
            $('#payment_voucher_Item_table tbody tr').not(':first').remove();
            initializeitemTypeahead(1);
            load_segmentBase_projectID_item();
            $('.f_search').closest('tr').css("background-color", 'white');
            $('.quantityRequested').closest('tr').css("background-color", 'white');
            $('.estimatedAmount').closest('tr').css("background-color", 'white');
            $('.wareHouseAutoID').closest('tr').css("background-color", 'white');
            $("#pv_item_detail_modal").modal({backdrop: "static"});
        }
    }

    function pv_detail_modal(type="expense") {
        if (payVoucherAutoId) {
            $("#gl_code").val(null).trigger("change");
            $("#activityCode").val('').trigger("change");
            $('#pv_detail_form')[0].reset();
            $('.segment_glAdd').val(defaultSegment).change();
            $('#payment_voucher_table tbody tr').not(':first').remove();

            if(type == 'expense'){
                $('#GL_Type').val('GL');
                $('#taxColumn').removeClass('hide');
                $('#taxColumnData').removeClass('hide');
                $('#taxColumnDataValue').removeClass('hide');
            }else{
                $('#GL_Type').val('INGL');
                $('#taxColumn').addClass('hide');
                $('#taxColumnData').addClass('hide');
                $('#taxColumnDataValue').addClass('hide');
            }

    
            //$('#pv_detail_form').bootstrapValidator('resetForm', true);
            $("#pv_detail_modal").modal({backdrop: "static"});
        }
    }

    function Emp_EC_pv_detail_modal() {
        if (payVoucherAutoId) {
            $("#gl_code").val(null).trigger("change");
            $('#pv_ec_and_gl_add_form')[0].reset();
            $('.segment_glAdd').val(defaultSegment).change();
            $('.segment_glAdd').prop('disabled', false);
            $('.amount').prop('disabled', false);
            $('.discountPercentage').prop('disabled', false);
            $('.discountAmount').prop('disabled', false);
            $('#pv_ec_and_gl_add_table tbody tr').not(':first').remove();
            $('.glType').removeClass('hidden');
            $('.ecType').addClass('hidden');
            //$('#pv_detail_form').bootstrapValidator('resetForm', true);
            $("#pv_ec_and_gl_detail_modal").modal({backdrop: "static"});
        }
    }

    function pv_po_detail_modal() {
        if (payVoucherAutoId) {
            $("#po_code").val(null).trigger("change");
            $('#pv_po_detail_form')[0].reset();
            $('#poamount').val(0);
            $('#pv_po_detail_form').bootstrapValidator('resetForm', true);
            $("#pv_po_detail_modal").modal({backdrop: "static"});
        }
    }

    function initializeitemTypeahead(id) {
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN',
            onSelect: function (suggestion) {

                // if(itemBatchPolicy==1){
                //       getItemBatchDetails(suggestion.itemAutoID,id);
                // }

                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                if (showPurchasePrice == 1) {
                    fetch_purchase_price(suggestion.companyLocalPurchasingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                }
                fetch_line_tax_and_vat(suggestion.itemAutoID, this);
                $(this).closest('tr').find('.quantityRequested').focus();
                $(this).closest('tr').css("background-color", 'white');
            }
        });
    }

    function initializeitemTypeahead_edit() {
        /**var item = new Bloodhound({
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
            fetch_related_uom_id_edit(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID);

            /!*            $('#itemAutoID').val(datum.itemAutoID);
             $('#pv_item_detail_form').bootstrapValidator('revalidateField', 'itemAutoID');
             $('#pv_item_detail_form').bootstrapValidator('revalidateField', 'UnitOfMeasureID');*!/
        });*/


        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Procurement/fetch_itemrecode_po/?column='+ 'allowedtoBuyYN',
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#edit_itemAutoID').val(suggestion.itemAutoID);
                }, 200);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                edit_fetch_line_tax_and_vat(suggestion.itemAutoID, this);
                $(this).closest('tr').find('#edit_quantityRequested').focus();
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

                mySelect.append($('<option></option>').val('').html('Select  UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $(element).closest('tr').find('.umoDropdown').val(select_value);
                        /*$("#UnitOfMeasureID").val(select_value);*/
                        /*$('#invoice_item_detail_form').bootstrapValidator('revalidateField', 'UnitOfMeasureID');*/
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

    function save_inv_base_items(type = 'SUP') {
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
                data: {
                    'InvoiceAutoID': selected,
                    'payVoucherAutoId': payVoucherAutoId,
                    'amount': amount,
                    'type': type,
                    'settlementAmount': totalsettlement
                },
                url: "<?php echo site_url('Payment_voucher/save_inv_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#inv_base_modal').modal('hide');
                    $('#inv_base_customer_modal').modal('hide');
                    refreshNotifications(true);
                    setTimeout(function () {
                        if(type == 'CUS'){
                            fetch_details(7);
                        }else{
                            fetch_details(2);
                        }
                      
                    }, 300);
                }, error: function () {
                    $('#inv_base_modal').modal('hide');
                    $('#inv_base_customer_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function save_debitNote_base_items() {
        var selected = [];
        var amount = [];
        var types = [];
        var transactionAmount = [];
        $('#table_body input:checked').each(function () {
            selected.push($(this).val());
            amount.push($('#DNamount_' + $(this).val()).val());
            types.push($('#type_' + $(this).val()).val());
            transactionAmount.push($('#DNTransAmount_' + $(this).val()).val());
        });

        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'debitNoteMasterID': selected,
                    'payVoucherAutoId': payVoucherAutoId,
                    'amount': amount,
                    'types': types,
                    'transactionAmount': transactionAmount
                },
                url: "<?php echo site_url('Payment_voucher/save_debitNote_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#debitNote_base_modal').modal('hide');
                    refreshNotifications(true);
                    setTimeout(function () {
                        fetch_details(5);
                    }, 300);
                }, error: function () {
                    $('#debitNote_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function select_check_box(data, id, total) {
        $("#check_" + id).prop("checked", false)
        if (data.value > 0) {
            if (total >= data.value) {
                $("#check_" + id).prop("checked", true);
            } else {
                $("#check_" + id).prop("checked", false);
                $("#amount_" + id).val('');
                myAlert('w', '<?php echo $this->lang->line('accounts_payable_tr_cannot_enter_an_invoice');?>');
                total_calculation();
                /*You can not enter an invoice amount greater than selected invoice Balance Amount*/
            }
        }
    }

    function select_check_boxDN(data, id, total) {
        $("#check_" + id).prop("checked", false)
        if (data.value > 0) {
            if (total >= data.value) {
                $("#DNcheck_" + id).prop("checked", true);
            } else {
                $("#DNcheck_" + id).prop("checked", false);
                $("#DNamount_" + id).val('');
                myAlert('w', '<?php echo $this->lang->line('accounts_payable_tr_cannot_enter_an_invoice_debit_note');?>');
                /*You can not enter an invoice amount greater than selected Debit note Balance Amount*/
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
        if (payVoucherAutoId) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this record*/
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
                        url: "<?php echo site_url('Payment_voucher/delete_tax_detail'); ?>",
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


    function fetch_pv_direct_details(tab) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'payVoucherAutoId': payVoucherAutoId},
            url: "<?php echo site_url('Payment_voucher/fetch_pv_direct_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                tax_total = 0;
                transactionDecimalPlaces = 2;
                $('#gl_table_body,#gl_table_income_body,#item_table_body,#invoice_table_body,#advance_table_body,#commission_table_body,#debitNote_table_body,#table_body_prq').empty();

                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#gl_table_body,#gl_table_income_body,#item_table_body,#invoice_table_body,#advance_table_body,#commission_table_body,#debitNote_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                    $('#item_table_tfoot,#invoice_table_tfoot,#advance_table_tfoot,#gl_table_tfoot,#gl_table_income_tfoot,#commission_table_tfoot,#table_tfoot_prq').empty();
                    $("#vouchertype").prop("disabled", false);
                    $("#pvtype").prop("disabled", false);
                    <?php if($pvType != 'SC'){ ?>
                    $("#transactionCurrencyID").prop("disabled", false);
                    <?php
                    }
                    ?>
                    $("#partyID").prop("disabled", false);

                } else {
                    $("#vouchertype").prop("disabled", true);
                    $("#pvtype").prop("disabled", true);
                    <?php if($pvType != 'SC'){ ?>
                    $("#transactionCurrencyID").prop("disabled", true);
                    <?php
                    }
                    ?>
                    $("#partyID").prop("disabled", true);

                    x = 1;
                    y = 1;
                    z = 1;
                    transactionDecimalPlaces = data['currency']['transactionCurrencyDecimalPlaces'];
                    LocalDecimalPlaces = data['currency']['companyLocalCurrencyDecimalPlaces'];
                    partyDecimalPlaces = data['currency']['partyCurrencyDecimalPlaces'];
                    gl_trans_amount = 0;
                    gl_trans_amount_income = 0;
                    gl_local_amount = 0;
                    gl_party_amount = 0;
                    po_trans_amount = 0;
                    po_local_amount = 0;
                    po_party_amount = 0;
                    item_trans_amount = 0;
                    prq_trans_amount = 0;
                    item_local_amount = 0;
                    item_party_amount = 0;
                    invoice_amount = 0;
                    due_amount = 0;
                    paid_amount = 0;
                    cus_paid_amount = 0;
                    Balance_amount = 0;
                    dbTotal_amount = 0;
                    var gl_footerspan = 6;
                    var item_footerspan = 6;
                    $('#item_table_tfoot,#invoice_table_tfoot,#advance_table_tfoot,#gl_table_tfoot,#cus_invoice_table_body,#gl_table_income_tfoot,#commission_table_tfoot,#debitNote_table_tfoot,#table_tfoot_prq').empty();
                    var string = '';
                    $.each(data['detail'], function (key, value) {

                        if (value['type'] == 'Item') {
                            taxView = '';
                            var taxamount = 0;
                            if (isGroupWiseTax == 1) {
                                if (value['taxAmount'] > 0) {
                                    if (currency_decimal == 3) {
                                        taxamount = value['taxAmount'];
                                        taxamount = Math.round(taxamount * 1000) / 1000;
                                    } else {
                                        taxamount = value['taxAmount'];
                                        taxamount = Math.round(taxamount * 100) / 100;
                                    }
                                    value['transactionAmount'] = parseFloat(value['transactionAmount']) + parseFloat(taxamount);
                                    taxView = '<td class="text-right"><a onclick="open_tax_dd(\'\',' + payVoucherAutoId + ',\'PV\',' + currency_decimal + ', ' + value['payVoucherDetailAutoID'] + ', \'srp_erp_paymentvoucherdetail\', \'payVoucherDetailAutoID\')">' + parseFloat(taxamount).formatMoney(currency_decimal, '.', ',') + '</a></td>';
                                } else {
                                    taxView = '<td class="text-right">' + parseFloat(taxamount).formatMoney(currency_decimal, '.', ',') + '</td>';
                                }
                                item_footerspan = 7;
                            }


                            var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
                            if (value['isSubitemExist'] == 1) {
                                var colour = 'color: #dad835 !important';
                                colour = '';
                                if(itemBatchPolicy==1){
                                    if(<?php echo $advanceCostCapturing; ?> == 1){
                                        string = '<tr><td>' + x + '</td><td class="text-danger">' + value['itemSystemCode'] +'</td><td class="">' + value['batchNumber'] + '</td><td>' + value['itemDescription'] + ' - ' + value['Itemdescriptionpartno'] + '</td><td>' + value['activityCodeName'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_modal(' + value['payVoucherDetailAutoID'] + ',\'PV\')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }else{
                                        string = '<tr><td>' + x + '</td><td class="text-danger">' + value['itemSystemCode'] +'</td><td class="">' + value['batchNumber'] + '</td><td>' + value['itemDescription'] + ' - ' + value['Itemdescriptionpartno'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_modal(' + value['payVoucherDetailAutoID'] + ',\'PV\')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    } 
                                }else{
                                    if(<?php echo $advanceCostCapturing; ?> == 1){
                                        string = '<tr><td>' + x + '</td><td class="text-danger">' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + ' - ' + value['Itemdescriptionpartno'] + '</td><td>' + value['activityCodeName'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_modal(' + value['payVoucherDetailAutoID'] + ',\'PV\')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }else{
                                        string = '<tr><td>' + x + '</td><td class="text-danger">' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + ' - ' + value['Itemdescriptionpartno'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_modal(' + value['payVoucherDetailAutoID'] + ',\'PV\')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }
                                }
                                
                            } else {
                                if(itemBatchPolicy==1){
                                    if(<?php echo $advanceCostCapturing; ?> == 1){
                                        string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber']+ '</td><td>' + value['itemDescription'] + ' - ' + value['Itemdescriptionpartno'] + '</td><td>' + value['activityCodeName'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }else{
                                        string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber']+ '</td><td>' + value['itemDescription'] + ' - ' + value['Itemdescriptionpartno'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }
                                   
                                }else{
                                    if(<?php echo $advanceCostCapturing; ?> == 1){
                                        string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + ' - ' + value['Itemdescriptionpartno'] + '</td><td>' + value['activityCodeName'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }else{
                                        string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + ' - ' + value['Itemdescriptionpartno'] + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>'; 
                                    } 
                                }
                                

                            }

                            $('#item_table_body').append(string);
                            x++;
                            item_trans_amount += (parseFloat(value['transactionAmount']));
                            tax_total += (parseFloat(value['transactionAmount']));

                        } else if (value['type'] == 'Invoice') {
                            $('.tab_3Item').removeClass('hide');
                            if( value['detailInvoiceType'] == 'CUS'){
                                cus_paid_amount += (parseFloat(value['transactionAmount']));
                                value['due_amount'] = value['Invoice_amount'];
                                value['balance_amount'] = value['due_amount'] - value['transactionAmount'];
                                $('#cus_invoice_table_body').append('<tr><td>' + y + '</td><td>' + value['bookingInvCode'] + '</td><td>' + value['referenceNo'] + '</td><td class="text-center">' + value['bookingDate'] + '</td><td class="text-right">' + parseFloat(value['Invoice_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['due_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            }else{
                                paid_amount += (parseFloat(value['transactionAmount']));
                                $('#invoice_table_body').append('<tr><td>' + y + '</td><td>' + value['bookingInvCode'] + '</td><td>' + value['referenceNo'] + '</td><td class="text-center">' + value['bookingDate'] + '</td><td class="text-right">' + parseFloat(value['Invoice_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['due_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            }
                            
                            y++;
                            invoice_amount += (parseFloat(value['Invoice_amount']));
                            due_amount += (parseFloat(value['due_amount']));
                            Balance_amount += (parseFloat(value['balance_amount']));

                        } else if (value['type'] == 'debitnote' || value['type'] == 'SR') {
                            $('.tab_5Item').removeClass('hide');

                            $('#debitNote_table_body').append('<tr><td>' + z + '</td><td>' + value['bookingInvCode'] + '</td><td>' + value['referenceNo'] + '</td><td class="text-center">' + value['bookingDate'] + '</td><td class="text-right">' + parseFloat(value['Invoice_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['due_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',5);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            z++;
                            /*invoice_amount += (parseFloat(value['Invoice_amount']));
                             due_amount += (parseFloat(value['due_amount']));*/
                            dbTotal_amount += (parseFloat(value['transactionAmount']));
                            //Balance_amount += (parseFloat(value['balance_amount']));

                        } else if (value['type'] == 'SC') {

                            $('#commission_table_body').append('<tr><td>' + y + '</td><td>' + value['bookingInvCode'] + '</td><td>' + value['referenceNo'] + '</td><td class="text-center">' + value['bookingDate'] + '</td><td class="text-right">' + parseFloat(value['Invoice_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['due_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            y++;
                            invoice_amount += (parseFloat(value['Invoice_amount']));
                            due_amount += (parseFloat(value['due_amount']));
                            paid_amount += (parseFloat(value['transactionAmount']));
                            Balance_amount += (parseFloat(value['balance_amount']));
                            //<a onclick="edit_invoice_item('+value['payVoucherDetailAutoID']+',\''+value['GLDescription']+'\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                        } else if (value['type'] == 'Advance') {
                            $('.tab_4Item').removeClass('hide');
                            var poCode = value['POCode'];
                            var poDescription = value['PODescription'];
                            var poDate = value['PODate'];
                            if (poCode == null) {
                                poCode = '';
                            }
                            if (poDescription == null) {
                                poDescription = '';
                            }
                            if (poDate == null) {
                                poDate = '';
                            }

                            $('#advance_table_body').append('<tr><td>' + y + '</td><td>' + poCode + '</td><td>' + poDescription + '</td><td class="text-center">' + poDate + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            y++;
                            po_trans_amount += (parseFloat(value['transactionAmount']));

                        } else if (value['type'] == 'PRQ') {

                            $('#table_body_prq').append('<tr><td>' + x + '</td><td>' + value['purchaseRequestCode'] + ' - ' + value['itemSystemCode'] + ' </td><td><b>Description :</b> ' + value['itemDescription'] + ' <br> <b>comment :</b> ' + value['comment'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + (parseFloat(value['unittransactionAmount'])).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(transactionDecimalPlaces, '.', ',') + '</td><td class="text-right"><a onclick="edit_prq_item(' + value['payVoucherDetailAutoID'] + ',' + value['prQty'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            x++;
                            prq_trans_amount += parseFloat(value['transactionAmount']);
                            tax_total += parseFloat(value['transactionAmount']);
                        } else {
                            taxView = '';
                            var taxamount = 0;
                            if (isGroupWiseTax == 1) {
                                if (value['taxAmount'] > 0) {
                                    if (currency_decimal == 3) {
                                        taxamount = value['taxAmount'];
                                        taxamount = Math.round(taxamount * 1000) / 1000;
                                    } else {
                                        taxamount = value['taxAmount'];
                                        taxamount = Math.round(taxamount * 100) / 100;
                                    }
                                    value['transactionAmount'] = parseFloat(value['transactionAmount']);
                                    taxView = '<td class="text-right"><a onclick="open_tax_dd(\'\',' + payVoucherAutoId + ',\'PV\',' + currency_decimal + ', ' + value['payVoucherDetailAutoID'] + ', \'srp_erp_paymentvoucherdetail\', \'payVoucherDetailAutoID\')">' + parseFloat(taxamount).formatMoney(currency_decimal, '.', ',') + '</a></td>';
                                } else {
                                    taxView = '<td>' + parseFloat(taxamount).formatMoney(currency_decimal, '.', ',') + '</td>';
                                }
                                gl_footerspan = 8;
                            }
                            if(value['type'] == 'INGL'){
                                var transamnt_income = parseFloat(value['transactionAmount']) + parseFloat(value['discountAmount']);
                                var tot_amt_income = parseFloat(value['transactionAmount']) + parseFloat(taxamount);
                            }else{
                                var transamnt = parseFloat(value['transactionAmount']) + parseFloat(value['discountAmount']);
                                var tot_amt = parseFloat(value['transactionAmount']) + parseFloat(taxamount);
                            }
                            

                            if (value['expenseClaimMasterAutoID']) {
                                if(value['type'] == 'INGL'){
                                    if(<?php echo $advanceCostCapturing; ?> == 1){
                                        $('#gl_table_income_body').append('<tr><td>' + y + '</td>' +
                                    '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'EC\', ' + value['expenseClaimMasterAutoID'] + ')">' + value['expenseClaimCode'] + '</a><br><strong>GL Code : </strong>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' + value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-center">' + value['activityCodeName'] + '</td><td class="text-right">' + parseFloat(transamnt_income).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">(' + parseFloat(value['discountPercentage']).formatMoney(2, '.', ',') + ' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_expense_direct(' + value['payVoucherDetailAutoID'] + ',' + value['expenseClaimMasterAutoID'] + ',\'' + value['expenseClaimCode'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                    }else{
                                        $('#gl_table_income_body').append('<tr><td>' + y + '</td>' +
                                    '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'EC\', ' + value['expenseClaimMasterAutoID'] + ')">' + value['expenseClaimCode'] + '</a><br><strong>GL Code : </strong>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' + value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(transamnt_income).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">(' + parseFloat(value['discountPercentage']).formatMoney(2, '.', ',') + ' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_expense_direct(' + value['payVoucherDetailAutoID'] + ',' + value['expenseClaimMasterAutoID'] + ',\'' + value['expenseClaimCode'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                    }
                                    
                                }else{
                                    if(<?php echo $advanceCostCapturing; ?> == 1){
                                        $('#gl_table_body').append('<tr><td>' + y + '</td>' +
                                    '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'EC\', ' + value['expenseClaimMasterAutoID'] + ')">' + value['expenseClaimCode'] + '</a><br><strong>GL Code : </strong>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' + value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-center">' + value['activityCodeName'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">(' + parseFloat(value['discountPercentage']).formatMoney(2, '.', ',') + ' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_expense_direct(' + value['payVoucherDetailAutoID'] + ',' + value['expenseClaimMasterAutoID'] + ',\'' + value['expenseClaimCode'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                    }else{
                                        $('#gl_table_body').append('<tr><td>' + y + '</td>' +
                                    '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'EC\', ' + value['expenseClaimMasterAutoID'] + ')">' + value['expenseClaimCode'] + '</a><br><strong>GL Code : </strong>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' + value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">(' + parseFloat(value['discountPercentage']).formatMoney(2, '.', ',') + ' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_expense_direct(' + value['payVoucherDetailAutoID'] + ',' + value['expenseClaimMasterAutoID'] + ',\'' + value['expenseClaimCode'] + '\');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                    }
                                    
                                }
                               
                            } else {
                                if(value['type'] == 'INGL'){
                                    if(<?php echo $advanceCostCapturing; ?> == 1){
                                        $('#gl_table_income_body').append('<tr><td>' + y + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' + value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-center">' + value['activityCodeName'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">(' + parseFloat(value['discountPercentage']).formatMoney(currency_decimal, '.', ',') + ' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(tot_amt_income).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_gl_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',4);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                    }else{
                                        $('#gl_table_income_body').append('<tr><td>' + y + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' + value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">(' + parseFloat(value['discountPercentage']).formatMoney(currency_decimal, '.', ',') + ' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(tot_amt_income).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_gl_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',4);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                    }
                                }else{
                                    if(<?php echo $advanceCostCapturing; ?> == 1){
                                        $('#gl_table_body').append('<tr><td>' + y + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' + value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-center">' + value['activityCodeName'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">(' + parseFloat(value['discountPercentage']).formatMoney(currency_decimal, '.', ',') + ' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(tot_amt).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_gl_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',4);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                    }else{
                                    $('#gl_table_body').append('<tr><td>' + y + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' + value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">(' + parseFloat(value['discountPercentage']).formatMoney(currency_decimal, '.', ',') + ' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' + taxView + '<td class="text-right">' + parseFloat(tot_amt).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_gl_item(' + value['payVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['payVoucherDetailAutoID'] + ',4);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                                    }
                                }
                            }

                            y++;

                            if(value['type'] == 'INGL'){
                                gl_trans_amount_income += (parseFloat(tot_amt_income));
                            }else{
                                gl_trans_amount += (parseFloat(tot_amt));
                            }
                            
                            
                            tax_total += (parseFloat(value['transactionAmount']));

                        }
                    });

                    $('#item_table_tfoot').append('<tr><td colspan="' + item_footerspan + '" class="text-right"><?php echo $this->lang->line('common_total');?> <!--Total--> </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                    $('#invoice_table_tfoot').append('<tr><td colspan="6" class="text-right"><?php echo $this->lang->line('accounts_payable_tr_total_paid');?> <!--Total Paid --></td><td class="text-right total">' + parseFloat(paid_amount).formatMoney(currency_decimal, '.', ',') + '</td><td colspan="2"></td></tr>');
                    $('#cus_invoice_table_tfoot').append('<tr><td colspan="6" class="text-right"><?php echo $this->lang->line('accounts_payable_tr_total_paid');?> <!--Total Paid --></td><td class="text-right total">' + parseFloat(cus_paid_amount).formatMoney(currency_decimal, '.', ',') + '</td><td colspan="2"></td></tr>');
                    $('#commission_table_tfoot').append('<tr><td colspan="6" class="text-right"> <?php echo $this->lang->line('accounts_payable_tr_total_paid');?><!--Total Paid--> </td><td class="text-right total">' + parseFloat(paid_amount).formatMoney(currency_decimal, '.', ',') + '</td><td colspan="2"></td></tr>');
                    $('#gl_table_tfoot').append('<tr><td colspan="' + gl_footerspan + '" class="text-right"><?php echo $this->lang->line('common_total');?> <!--Total--> </td><td class="text-right total">' + parseFloat(gl_trans_amount).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                    $('#gl_table_income_tfoot').append('<tr><td colspan="' + gl_footerspan + '" class="text-right"><?php echo $this->lang->line('common_total');?> <!--Total--> </td><td class="text-right total">' + parseFloat(gl_trans_amount_income).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');


                    $('#advance_table_tfoot').append('<tr><td colspan="4" class="text-right"><?php echo $this->lang->line('common_total');?> <!--Total--> </td><td class="text-right total">' + parseFloat(po_trans_amount).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                    $('#debitNote_table_tfoot').append('<tr><td colspan="6" class="text-right"><?php echo $this->lang->line('accounts_payable_tr_total_paid');?> <!--Total Paid--> </td><td class="text-right total">' + parseFloat(dbTotal_amount).formatMoney(currency_decimal, '.', ',') + '</td><td colspan="2"></td></tr>');

                    $('#table_tfoot_prq').append('<tr><td colspan="7" class="text-right">Total </td><td class="text-right total">' + parseFloat(prq_trans_amount).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right" >&nbsp;</td></tr>');
                }

                $('.nav-tabs a[href="#tab_1"]').tab('show');
                if (tab == 1) {

                    $('.nav-tabs a[href="#tab_4"]').tab('show');
                }
                if (tab == 2) {
                    $('.nav-tabs a[href="#tab_3"]').tab('show');
                }
                if (tab == 3) {
                    $('.nav-tabs a[href="#tab_2"]').tab('show');
                }
                if (tab == 4) {
                    $('.nav-tabs a[href="#tab_1"]').tab('show');
                }
                if (tab == 5) {
                    $('.nav-tabs a[href="#tab_5"]').tab('show');
                }
                if (tab == 6) {
                    $('.nav-tabs a[href="#tab_6"]').tab('show');
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
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>", /*You want to delete this this record!*/
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
                    data: {'payVoucherDetailAutoID': id},
                    url: "<?php echo site_url('Payment_voucher/delete_item_direct'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        setTimeout(function () {
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

    function edit_gl_item(id) {
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
                $("#edit_gl_code").val(null).trigger("change");
                $('#edit_pv_detail_form').trigger("reset");
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'payVoucherDetailAutoID': id},
                    url: "<?php echo site_url('Payment_voucher/fetch_payment_voucher_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        payVoucherDetailAutoID = data['payVoucherDetailAutoID'];
                        projectID_income = data['projectID'];
                        projectcategory = data['project_categoryID'];
                        projectsubcat = data['project_subCategoryID'];
                        $('#edit_gl_code').val(data['GLAutoID']).change();
                        if(<?php echo $advanceCostCapturing; ?> == 1){
                            $('#edit_activityCode').val(data['activityCodeID']).change();
                        }
                        $('#gl_text_type_edit').val(data['taxCalculationformulaID']).change();
                        $('#edit_segment_gl').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#edit_amount').val((parseFloat(data['transactionAmount']) + parseFloat(data['discountAmount'])).toFixed(currency_decimal));
                        $('#discountPercentage_edit').val(parseFloat(data['discountPercentage']).toFixed(2));
                        $('#discountAmount_edit').val(parseFloat(data['discountAmount']).toFixed(currency_decimal));
                        $('#Netamount_edit').val(parseFloat(data['transactionAmount']).toFixed(currency_decimal));
                        $('#edit_description').val(data['description']);
                        $('#edit_gl_type').val(data['type']);
                        load_gl_line_tax_amount_edit(data['GLAutoID']);

                        if(data['type'] == 'GL'){
                            $('#edittaxColumn').removeClass('hide');
                            $('#edittaxColumnData').removeClass('hide');
                            $('#edittaxColumnDataValue').removeClass('hide');
                        }else if(data['type'] == 'INGL'){
                            $('#edittaxColumn').addClass('hide');
                            $('#edittaxColumnData').addClass('hide');
                            $('#edittaxColumnDataValue').addClass('hide');
                        }
                        // $('#itemSystemCode').val(data['itemSystemCode']);
                        // $('#itemAutoID').val(data['itemAutoID']);
                        // $('#itemDescription').val(data['itemDescription']);
                        // $('#comment').val(data['comment']);
                        // $('#remarks').val(data['remarks']);
                        // $('#discount').val(data['discountPercentage']);
                        $("#edit_pv_detail_modal").modal({backdrop: "static"});
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function edit_item(id, value) {
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        $("#edit_wareHouseAutoID").val('').change();
        $('#edit_pv_item_detail_form')[0].reset();
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
                    data: {'payVoucherDetailAutoID': id},
                    url: "<?php echo site_url('Payment_voucher/fetch_payment_voucher_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        currentEditWareHouseAutoID=data['wareHouseAutoID'];
                        currentEditTextBatchData=data['batchNumber'];

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

                        payVoucherDetailAutoID = data['payVoucherDetailAutoID'];
                        projectcategory = data['project_categoryID'];
                        projectsubcat = data['project_subCategoryID'];
                        load_segmentBase_projectID_itemEdit(data['segmentID'], data['projectID']);
                        $('#search').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - " + data['seconeryItemCode']);
                        fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                        select_VAT_value = data['taxCalculationformulaID'];
                        $('#edit_quantityRequested').val(data['requestedQty']);
                        $('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount']).toFixed(currency_decimal)));
                        $('#editNetAmount').val((parseFloat(data['transactionAmount'])));
                        $('#edit_search_id').val(data['itemSystemCode']);
                        $('#edit_itemSystemCode').val(data['itemSystemCode']);
                        $('#edit_itemAutoID').val(data['itemAutoID']);
                        $('#edit_itemDescription').val(data['itemDescription']);
                        $('#edit_wareHouseAutoID').val(data['wareHouseAutoID']).change();
                        $('#edit_comment').val(data['comment']);
                        
                        if(<?php echo $advanceCostCapturing; ?> == 1){
                            $('#edit_activityCode').val(data['activityCodeID']).change();
                        }

                        if(data['type'] == 'GL'){
                            $('#edittaxColumn').removeClass('hide');
                            $('#edittaxColumnData').removeClass('hide');
                            $('#edittaxColumnDataValue').removeClass('hide');
                        }else if(data['type'] == 'INGL'){
                            $('#edittaxColumn').addClass('hide');
                            $('#edittaxColumnData').addClass('hide');
                            $('#edittaxColumnDataValue').addClass('hide');
                        }

                        edit_fetch_line_tax_and_vat(data['itemAutoID']);
                        $("#edit_pv_item_detail_modal").modal({backdrop: "static"});
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function load_batch_number_single_edit_p_voucher(){
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

    function add_more() {
        $('select.select2').select2('destroy');
        var appendData = $('#payment_voucher_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#payment_voucher_table').append(appendData);
        var lenght = $('#payment_voucher_table tbody tr').length - 1;
        $(".select2").select2();
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function savePaymentVoucher_Expenses() {
        var $form = $('#pv_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'payVoucherAutoId', 'value': payVoucherAutoId});
        data.push({'name': 'payVoucherDetailAutoID', 'value': payVoucherDetailAutoID});
        $('select[name="gl_code[]"] option:selected').each(function () {
            data.push({'name': 'gl_code_des[]', 'value': $(this).text()})
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Payment_voucher/save_direct_pv_detail_multiple'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    payVoucherDetailAutoID = null;
                    $('#pv_detail_form')[0].reset();
                    $("#segment_gl").select2("");
                    $("#activityCode").select2("");
                    $("#gl_code").select2("");
                    setTimeout(function () {
                        var gl_type = $('#GL_Type').val();
                        if(gl_type != 'expense'){
                            fetch_pv_direct_details(6);
                        }else{
                            fetch_pv_direct_details(4);
                        }
                        
                        $('#pv_detail_modal').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }, 300);

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
        var appendData = $('#payment_voucher_Item_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('input').val('');
        appendData.find('textarea').val('');

        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';

        if(itemBatchPolicy==1){
            appendData.find('.b_number').empty();
            appendData.find('.b_number').attr('id', 'batch_number_' + search_id);
            appendData.find('.b_number').attr('name', 'batch_number[' + batch_number+'][]');
        }

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#payment_voucher_Item_table').append(appendData);
        var lenght = $('#payment_voucher_Item_table tbody tr').length - 1;
        //$('#f_search_' + search_id).closest('tr').css("background-color", 'white');

        $(".select2").select2();
        initializeitemTypeahead(search_id);
        number_validation();
    }

    function savePaymentVoucher_ID_item() {
        var $form = $('#pv_item_detail_form');
        var data = $form.serializeArray();
        if (payVoucherAutoId) {
            data.push({'name': 'payVoucherAutoId', 'value': payVoucherAutoId});
            data.push({'name': 'payVoucherDetailAutoID', 'value': payVoucherDetailAutoID});

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
            $('.wareHouseAutoID').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2 ');
                }
            });
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payment_voucher/save_pv_item_detail_multiple'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        payVoucherDetailAutoID = null;
                        $('#pv_item_detail_form')[0].reset();
                        setTimeout(function () {
                            fetch_pv_direct_details(3);
                            $('#pv_item_detail_modal').modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        }, 300);

                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
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

    function Update_PaymentVoucher_Expenses() {
        var $form = $('#edit_pv_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'payVoucherAutoId', 'value': payVoucherAutoId});
        data.push({'name': 'payVoucherDetailAutoID', 'value': payVoucherDetailAutoID});
        data.push({'name': 'gl_code_des', 'value': $('#edit_gl_code option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Payment_voucher/save_direct_pv_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    payVoucherDetailAutoID = null;
                    $('#edit_pv_detail_form')[0].reset();
                    $("#edit_segment_gl").select2("");
                    $("#activityCode").select2("");
                    $("#edit_gl_code").select2("");
                    setTimeout(function () {
                        fetch_pv_direct_details(4);
                        $('#edit_pv_detail_modal').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }, 300);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function Update_PaymentVoucher_ID_item() {
        var $form = $('#edit_pv_item_detail_form');
        var data = $form.serializeArray();
        if (payVoucherAutoId) {
            data.push({'name': 'payVoucherAutoId', 'value': payVoucherAutoId});
            data.push({'name': 'payVoucherDetailAutoID', 'value': payVoucherDetailAutoID});
            data.push({'name': 'wareHouse', 'value': $('#edit_wareHouseAutoID option:selected').text()});
            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Payment_voucher/save_pv_item_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        payVoucherDetailAutoID = null;
                        $('#pv_item_detail_form')[0].reset();
                        setTimeout(function () {
                            fetch_pv_direct_details(3);
                            $('#edit_pv_item_detail_modal').modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        }, 300);
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
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

    function remove_item_all_description(e, ths) {
        //$('#edit_itemAutoID').val('');
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $(ths).closest('tr').find('.itemAutoID').val('');
        }
    }

    function amount_validation(element) {
        if (parseFloat($(element).val()) > parseFloat($(element).data("balance"))) {
            var currentvalue = $(element).val();
            $(element).val(currentvalue.slice(0, -1));
            myAlert('w', '<?php echo $this->lang->line('accounts_payable_tr_cannot_enter_an_amount_greater_than');?>');
            /*You can not enter an amount greater than selected balance amount*/
        }
    }

    function remove_item_all_description_edit(e, ths) {
        //$('#edit_itemAutoID').val('');
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        } else {
            $('#edit_itemAutoID').val('');
        }
    }

    function save_commission_base_items() {
        var selected = [];
        $('#commission_detail_table_body input[type="text"]').each(function () {
            if ($(this).val() != "") {
                selected.push({name: 'amount[]', value: $(this).val()});
                selected.push({name: 'InvoiceAutoID[]', value: $(this).data('invoiceautoid')});
                selected.push({name: 'due_amount[]', value: $(this).data('dueamount')});
            }
        });
        if (!jQuery.isEmptyObject(selected)) {
            selected.push({
                name: 'salesPersonID',
                value:<?php echo isset($master['partyID']) ? $master['partyID'] : 'test'; ?>});
            selected.push({name: 'payVoucherAutoId', value: payVoucherAutoId});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: selected,
                url: "<?php echo site_url('Payment_voucher/save_commission_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (data.status) {
                        stopLoad();
                        $('#commission_base_modal').modal('hide');
                        myAlert(data.type, data.message);
                        setTimeout(function () {
                            fetch_details(2);
                        }, 300);
                    } else {
                        stopLoad();
                        myAlert(data.type, data.message);
                    }
                }, error: function () {
                    $('#commission_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        } else {
            swal("Please enter an amount", "Try Again ", "error");
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
                if (selectValue) {
                    $("#projectID_item").val(selectValue).change()
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
        var linetaxamnt = 0;

        if (isGroupWiseTax == 1) {
            linetaxamnt = $(field).closest('tr').find('.linetaxamnt').val();
        }
        if (val == 1) {

            quantityRequested = $(field).closest('tr').find('.quantityRequested').val();
            estimatedAmount = $(field).closest('tr').find('.estimatedAmount').val();
            var totamt = (quantityRequested * estimatedAmount) + linetaxamnt;
            $(field).closest('tr').find('.netAmount').val(totamt);
        } else {

            quantityRequested = $(field).closest('tr').find('.quantityRequested').val();
            netAmount = $(field).closest('tr').find('.netAmount').val();

            var unitamt = netAmount / quantityRequested;

            if (unitamt != 'Infinity') {
                $(field).closest('tr').find('.estimatedAmount').val(unitamt);
            } else {
                (field).closest('tr').find('.estimatedAmount').val('');

            }

        }
        if (isGroupWiseTax == 1) {
            load_line_tax_amount(field)
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
            $('#editNetAmount').val(totamt);
        } else {
            quantityRequested = $('#edit_quantityRequested').val();
            editNetAmount = $('#editNetAmount').val();
            var unitamt = editNetAmount / quantityRequested;
            $('#edit_estimatedAmount').val(unitamt);


        }
        if (isGroupWiseTax == 1) {
            load_line_tax_amount_edit(field)
        }

    }

    function checkitemavailable(det) {
        var itmID = $(det).closest('tr').find('.itemAutoID').val();
        var warehouseid = det.value;
        var concatarr = new Array();
        var searchID = $(det).closest('tr').find('.f_search').attr('id');
        var arrSearchID =searchID.split("f_search_");
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        if(itemBatchPolicy==1){

            if(itmID!="" && warehouseid!="" && arrSearchID.length>0){
                getItemBatchDetails(itmID,arrSearchID[1],warehouseid);
            }
            
        }

        if (itmID && warehouseid) {
            var mainconcat = itmID.concat('|').concat(warehouseid);
        }

        $('.itemAutoID').each(function () {
            if (this.value) {
                var itm = this.value;
                var wareHouseAutoID = $(this).closest('tr').find('.wareHouseAutoID').val();
                var concatvalue = itm.concat('|').concat(wareHouseAutoID);
                if (mainconcat) {
                    concatarr.push(concatvalue);
                }
            }
        });
        if (concatarr.length > 1) {
            if (jQuery.inArray(mainconcat, concatarr)) {

            } else {
                // $(det).closest('tr').find('.f_search').val('');
                // $(det).closest('tr').find('.itemAutoID').val('');
                // $(det).closest('tr').find('.wareHouseAutoID').val('').change();
                // $(det).closest('tr').find('.quantityRequested').val('');
                // $(det).closest('tr').find('.estimatedAmount').val('');
                // $(det).closest('tr').find('.netAmount').val('');
                // $(det).closest('tr').find('.umoDropdown').val('');
                // myAlert('w', 'Selected item is already selected');
            }
        }
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }

    function validatetb_row(det) {
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }

    function get_po_amount() {
        var pocode = $('#po_code').val();
        $('#poamount').val(0);
        $('#amount').val('');
        $('#advancedamount').val(0);
        $('#balancedamount').val(0);
        if (!jQuery.isEmptyObject(pocode)) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Payment_voucher/get_po_amount"); ?>',
                dataType: 'json',
                data: {pocode: pocode, payVoucherAutoId: payVoucherAutoId},
                async: true,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data[0] == 'w') {
                        $('#po_code').val('').change();
                        $('#poamount').val(0);
                        $('#advancedamount').val(0);
                        $('#balancedamount').val(0);
                        myAlert(data[0], data[1]);
                    }

                    if (data[0] == 's') {
                        $('#poamount').val(data[1]);
                        $('#advancedamount').val(data[2]);
                        $('#balancedamount').val(data[3]);
                    }

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    stopLoad();
                }
            });
        }

    }

    function check_low_than_poa_mount() {
        var potype_selected = $('#po_code').val();
        if (potype_selected !== '') {
            var balancedamount = $('#balancedamount').val();
            var amount = $('#amount').val();
            if (parseInt(balancedamount) > 0) {
                if (parseInt(amount) > parseInt(balancedamount)) {
                    myAlert('w', 'Amount should be less than or equel to Balance amount');
                    $('#amount').val('');
                }
            } else {
                myAlert('w', 'PO Amount is fully matched!');
                $('#amount').val('');
            }
        }
    }

    function purchase_request_detail_modal() {
        load_prq_codes();
        $("#prq_base_modal").modal({backdrop: "static"});
    }

    function load_prq_codes() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'payVoucherAutoId': payVoucherAutoId},
            url: "<?php echo site_url('Payment_voucher/fetch_prq_code'); ?>",
            success: function (data) {
                $('#prqcode').empty();
                $('#table_body_pr_detail').empty();
                var mySelect = $('#prqcode');
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, value) {
                        var bal = value['requestedQty'] - value['prQty'];
                        if (bal > 0) {
                            var id = 'pull-' + value['purchaseRequestID'];
                            mySelect.append('<li id="' + id + '" class="pull-li" title="PR Date :- ' + value['documentDate'] + ' Requestd By:- ' + value['requestedByName'] + '"  rel="tooltip"><a onclick="fetch_prq_detail_table(' + value['purchaseRequestID'] + ')">' + value['purchaseRequestCode'] + ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>');
                            $("[rel=tooltip]").tooltip();
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

    function fetch_prq_detail_table(purchaseRequestID) {
        $('.pull-li').removeClass('pulling-based-li');
        $('#pull-' + purchaseRequestID).addClass('pulling-based-li');
        if (purchaseRequestID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'purchaseRequestID': purchaseRequestID},
                url: "<?php echo site_url('Payment_voucher/fetch_prq_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#table_body_pr_detail').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['detail'])) {
                        $('#table_body_pr_detail').append('<tr class="danger"><td colspan="9" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        tot_amount = 0;
                        receivedQty = 0;
                        $.each(data['detail'], function (key, value) {
                            if (value['receivedQty'] != null && value['receivedQty'] !== undefined) {
                                receivedQty = value['receivedQty'];
                            }
                            cost_status = '<input type="text" class="number" size="10" id="amount_' + value['purchaseRequestDetailsID'] + '" value="' + parseFloat(value['unitAmount']).toFixed(currency_decimal) + '" onkeypress="return validateFloatKeyPress(this,event);" onkeyup="select_value_prq(' + value['purchaseRequestDetailsID'] + ')" >';
                            warehouse = '';
                            //discount_status = '<td> <input type="text" placeholder="0" id="discount_prq_' + value['purchaseRequestDetailsID'] + '" size="5" class="number" value="0" onkeyup="cal_discount_prq(' + value['purchaseRequestDetailsID'] + ')" onfocus="this.select();"> </td> <td><input type="text" size="3" id="discount_amount_prq_' + value['purchaseRequestDetailsID'] + '" style="width: 80px;" placeholder="0.00" class="number" onkeyup="cal_discount_amt(' + value['purchaseRequestDetailsID'] + ')" value="0" ></td>';
                            // var qty=value['requestedQty'] - value['prQty'];
                            var qty = value['qtyFormated'];

                            if (qty > 0) {
                                $('#table_body_pr_detail').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center"><select class="whre_drop" style="width: 110px;"  id="whre_' + value['purchaseRequestDetailsID'] + '"><option value="">Select WareHouse</option></select></td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right" id="balQty_' + value['purchaseRequestDetailsID'] + '">' + qty + '</td><td class="text-right">' + parseFloat(value['unitAmount']).toFixed(currency_decimal) + '</td><td class="text-center"><i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' + value['purchaseRequestDetailsID'] + ',' + value['unitAmount'] + ',' + qty + ')" aria-hidden="true"></i> <input type="text" class="number" size="8" id="qty_' + value['purchaseRequestDetailsID'] + '" onkeyup="select_check_box_prq(this,' + value['purchaseRequestDetailsID'] + ',' + value['unitAmount'] + ',' + (value['qtyFormated']) + ' )" ></td><td class="text-center">' + cost_status + '</td><td class="text-center"><p id="tot_' + value['purchaseRequestDetailsID'] + '"> </p></td><td class="text-right" style="display: none;"><input class="checkbox" id="check_' + value['purchaseRequestDetailsID'] + '" type="checkbox" value="' + value['purchaseRequestDetailsID'] + '"></td></tr>');
                            }
                            x++;
                            tot_amount += (parseFloat(value['totalAmount']).toFixed(currency_decimal));
                            //.formatMoney(currency_decimal, '.', ',')
                        });

                        if (!jQuery.isEmptyObject(data['ware_house'])) {
                            $('.whre_drop').empty();
                            var mySelect = $('.whre_drop');
                            mySelect.append($('<option></option>').val('').html('Select WareHouse'));
                            $.each(data['ware_house'], function (val, text) {
                                mySelect.append($('<option></option>').val(text['wareHouseAutoID']).html(text['wareHouseCode'] + ' | ' + text['wareHouseLocation']));
                            });
                        }
                    }
                    number_validation();
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

    function select_check_box_prq(data, id, amount, reqqty) {
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
            $('#qty_' + id).val(0);
            $('#tot_' + id).text('');
            swal("Ordered Qty should be less than requested Qty", "error");
        }
    }

    function select_value_prq(id) {
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
    }

    function save_prq_base_items() {
        var selected = [];
        var amount = [];
        var qty = [];
        var discount = [];
        var discountamt = [];
        var wareHouseAutoID = [];
        $('#table_body_pr_detail input:checked').each(function () {
            if ($('#amount_' + $(this).val()).val() == '') {
                swal("Cancelled", "Ordered Item cannot be blank !", "error");
            } else if ($('#whre_' + $(this).val()).val() == '') {
                myAlert('w', 'Please Select Warehouse');
            } else {
                selected.push($(this).val());
                amount.push($('#amount_' + $(this).val()).val());
                qty.push($('#qty_' + $(this).val()).val());
                wareHouseAutoID.push($('#whre_' + $(this).val()).val());
            }
        });
        if (!jQuery.isEmptyObject(selected)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'DetailsID': selected,
                    'payVoucherAutoId': payVoucherAutoId,
                    'amount': amount,
                    'qty': qty,
                    'wareHouseAutoID': wareHouseAutoID
                },
                url: "<?php echo site_url('Payment_voucher/save_prq_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        $('#prq_base_modal').modal('hide');
                        setTimeout(function () {
                            fetch_pv_direct_details(3);
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


    function edit_prq_item(id, prQty) {
        if (payVoucherAutoId) {
            swal({
                    title: "Are you sure?",
                    text: "You want to edit this record!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Edit"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'payVoucherDetailAutoID': id},
                        url: "<?php echo site_url('Payment_voucher/fetch_purchase_request_based_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            var totAmount = parseFloat(data['transactionAmount']);
                            var unitAmount = parseFloat(data['unittransactionAmount']);
                            payVoucherDetailAutoID = data['payVoucherDetailAutoID'];
                            $('#search_edit').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#quantityRequested_edit').val(data['requestedQty']);
                            $('#prQtyEdit').val(data['prQty']);
                            $('#estimatedAmount_edit').val((parseFloat(data['unittransactionAmount'])));
                            $('#edit_wareHouseAutoID').val(data['wareHouseAutoID']).change();
                            //$('#net_unit_cost_edit').text((unitAmount).formatMoney(2, '.', ','));
                            //$('#search_id').val(data['itemSystemCode']);
                            //$('#itemSystemCode').val(data['itemSystemCode']);
                            $('#itemAutoID_edit').val(data['itemAutoID']);
                            //$('#itemDescription').val(data['itemDescription']);
                            $('#comment_edit').val(data['comment']);
                            $('#totalAmount_edit').text((totAmount).formatMoney(2, '.', ','));
                            $("#purchase_request_detail_edit_modal").modal({backdrop: "static"});
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function change_qty_edit() {
        var prQtyEdit = getNumberAndValidate($('#prQtyEdit').val());
        var quantityRequested = getNumberAndValidate($('#quantityRequested_edit').val());
        if (quantityRequested <= prQtyEdit) {
            net_amount_edit();
        } else {
            $('#quantityRequested_edit').val(0);
            net_amount_edit();
            swal("PV Qty should be less than requested Qty", "error");
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

    function net_amount_edit() {
        var qut = $('#quantityRequested_edit').val();
        var amount = $('#estimatedAmount_edit').val();

        if (qut == null || qut == 0) {
            $('#totalAmount_edit').text('0.00');
            //$('#net_unit_cost_edit').text('0.00');
        } else {
            $('#totalAmount_edit').text((((parseFloat(1) * parseFloat(amount))) * parseFloat(qut)).formatMoney(currency_decimal, '.', ','));
            //$('#net_unit_cost_edit').text(((parseFloat(1) * parseFloat(amount)) ).formatMoney(currency_decimal, '.', ','));
        }
    }

    function change_amount_edit_prq() {
        net_amount_edit();
    }

    function updatePurchaseRequestDetails() {
        var data = $('#purchase_request_detail_edit_form').serializeArray();
        if (payVoucherAutoId) {
            data.push({'name': 'payVoucherAutoId', 'value': payVoucherAutoId});
            data.push({'name': 'payVoucherDetailAutoID', 'value': payVoucherDetailAutoID});
            data.push({'name': 'uom', 'value': $('#edit_UnitOfMeasureID option:selected').text()});
            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('Payment_voucher/update_purchase_request_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data) {
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                payVoucherDetailAutoID = null;
                                $('#purchase_request_detail_edit_modal').modal('hide');
                                fetch_pv_direct_details(3);
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

    function passVal(data, id, total) {
        $(data).closest('tr').find('.amountadd').val(total);
        var tot_TotalCostoverhead = 0;
        var amounttot = $('#totalamount').val();
        var totalamount = 0;
        if (amounttot) {
            totalamount = amounttot;
        }
        $('.invoice_base tr').each(function () {
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(6).find('input').val());
            tot_TotalCostoverhead += tot_valueoverhead;
        });
        $("#total_invoice_total").text(commaSeparateNumber(parseFloat(tot_TotalCostoverhead) + parseFloat(totalamount), currency_decimal));
        deduct_total_amount();
        $("#DNcheck_" + id).prop("checked", false)
        if (total > 0) {
            if (total >= total) {
                $("#DNcheck_" + id).prop("checked", true);
            } else {
                $("#DNcheck_" + id).prop("checked", false);
                $("#DNamount_" + id).val('');
                myAlert('w', '<?php echo $this->lang->line('accounts_payable_tr_cannot_enter_an_invoice');?>');
                /*You can not enter an invoice amount greater than selected invoice Balance Amount*/
            }
        }
    }
  
    function applybtn(data, id, total) {
        $(data).closest('tr').find('.amountadd').val(total);
        var tot_TotalCostoverhead = 0;
        var amounttot = $('#totalamount').val();
        var totalamount = 0;
        if (amounttot) {
            totalamount = amounttot;
        }
        $('.invoice_base tr').each(function () {
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(6).find('input').val());
            tot_TotalCostoverhead += tot_valueoverhead;
        });
        $("#total_invoice_total").text(commaSeparateNumber(parseFloat(tot_TotalCostoverhead) + parseFloat(totalamount), currency_decimal));
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


    function select_check_box(data, id, total) {
        $("#check_" + id).prop("checked", false)
        if (data.value > 0) {
            if (total >= data.value) {
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
        if (amounttot) {
            totalamount = amounttot;
        }
        if (id == 1) {
            $("#table_body tr").each(function () {
                var balance = ($(this).find('.supplierinvoicebalance').text().replace(/,/g, ''));
                balance = balance.trim();
                var invoiceautoid = $(this).find('.InvoiceAutoID').val();
                $(this).find('.amountadd').val(balance);
                if (balance > 0) {
                    if (balance >= balance) {
                        $("#check_" + invoiceautoid).prop("checked", true);
                        var tot_TotalCostoverhead = 0;
                        $('.invoice_base tr').each(function () {
                            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(6).find('input').val());
                            tot_TotalCostoverhead += tot_valueoverhead;
                        });
                        $("#total_invoice_total").text(commaSeparateNumber(parseFloat(tot_TotalCostoverhead) + parseFloat(totalamount), currency_decimal));
                        deduct_total_amount();
                    } else {
                        $("#check_" + invoiceautoid).prop("checked", false);
                        $("#amount_" + invoiceautoid).val('');
                        myAlert('w', '<?php echo $this->lang->line('accounts_payable_tr_cannot_enter_an_invoice');?>');
                        /*You can not enter an invoice amount greater than selected invoice Balance Amount*/
                    }
                }

            });
        } else {
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

    function clear_invoice_selected(data, id, total) {
        $("#check_" + id).prop("checked", false);
        $("#amount_" + id).val('');
        total_calculation();
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

    function total_calculation() {
        var amounttot = $('#totalamount').val();
        var totalamount = 0;
        var tot_TotalCostoverhead = 0;
        $('.invoice_base tr').each(function () {
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(6).find('input').val());
            tot_TotalCostoverhead += tot_valueoverhead;
        });
        if (amounttot) {
            totalamount = amounttot;
        }
        $("#total_invoice_total").text(commaSeparateNumber((parseFloat(tot_TotalCostoverhead) + parseFloat(totalamount)), currency_decimal));
        deduct_total_amount();
    }

    function deduct_total_amount() {
        var tot_TotalCost = parseFloat($('#total_invoice_total').text().replace(/,/g, ''));
        var amount = 0;
        var settlement_amount = $('#amount_total').val();
        if (settlement_amount) {
            amount = settlement_amount
        }

        $("#grandtotal_amount").text(commaSeparateNumber(parseFloat(amount) - (parseFloat(tot_TotalCost)), currency_decimal));
    }



    var isAlertShown = false;   //new
    function calculateNetAmount(val, fld) {
        var incamount = $(val).closest('tr').find('.amount').val();
        var incdiscountPercentage = $(val).closest('tr').find('.discountPercentage').val();
        var incdiscountAmount = $(val).closest('tr').find('.discountAmount').val();
        var pamount = $(val).closest('tr').find('.pamount').val();                  //new
        var expenseType = $(val).closest('tr').find('#expenseType').val();          //new
               
        if(expenseType == 3 /*$('#pAmountId').is(':visible')*/){                     //new
            if (!isNaN(incamount) && incamount > pamount && expenseType == 3) {
                if (incamount > pamount && !isAlertShown) { 
                    myAlert('w', 'Amount should not be greater than provision-amount');//new 
                    isAlertShown = true;                                                //new
                    $(val).closest('tr').find('.amount').val('');                     //new
                                                                 
                }
            } else {                    //new
                isAlertShown = false;   //new

                if (fld == 'amount') {
                    if (jQuery.isEmptyObject(incdiscountPercentage) || incdiscountPercentage == 0) {
                        $(val).closest('tr').find('.Netnumber').val(parseFloat(incamount).toFixed(currency_decimal));
                    } else {
                        var discamnt = (parseFloat(incamount) * parseFloat(incdiscountPercentage)) / 100;
                        $(val).closest('tr').find('.discountAmount').val(parseFloat(discamnt).toFixed(currency_decimal));
                        $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount) - parseFloat(discamnt)).toFixed(currency_decimal));   
                    }
                    } else if (fld == 'discountPercentage') {
                        if (jQuery.isEmptyObject(incamount) || incamount == 0) {
                            myAlert('w', 'Enter Discount Amount');
                            $(val).closest('tr').find('.discountPercentage').val(0);
                            $(val).closest('tr').find('.discountAmount').val(0);
                            $(val).closest('tr').find('.Netnumber').val(0);
                        } else {
                            var discamnt = (parseFloat(incamount) * parseFloat(incdiscountPercentage)) / 100;
                            $(val).closest('tr').find('.discountAmount').val(parseFloat(discamnt).toFixed(currency_decimal));
                            $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount) - parseFloat(discamnt)).toFixed(currency_decimal));
                        }
                    } else {
                        if (jQuery.isEmptyObject(incamount) || incamount == 0) {
                            myAlert('w', 'Enter Discount Amount');
                            $(val).closest('tr').find('.discountPercentage').val(0);
                            $(val).closest('tr').find('.discountAmount').val(0);
                            $(val).closest('tr').find('.Netnumber').val(0);
                        } else {
                            var discprc = (parseFloat(incdiscountAmount) * 100) / parseFloat(incamount);

                            $(val).closest('tr').find('.discountPercentage').val(parseFloat(discprc));
                            $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount) - parseFloat(incdiscountAmount)).toFixed(currency_decimal));
                        }
                    }
   
            }

        }else{ //new

                if (fld == 'amount') {
                if (jQuery.isEmptyObject(incdiscountPercentage) || incdiscountPercentage == 0) {
                    $(val).closest('tr').find('.Netnumber').val(parseFloat(incamount).toFixed(currency_decimal));
                } else {
                    var discamnt = (parseFloat(incamount) * parseFloat(incdiscountPercentage)) / 100;
                    $(val).closest('tr').find('.discountAmount').val(parseFloat(discamnt).toFixed(currency_decimal));
                    $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount) - parseFloat(discamnt)).toFixed(currency_decimal));
                }
                } else if (fld == 'discountPercentage') {
                    if (jQuery.isEmptyObject(incamount) || incamount == 0) {
                        myAlert('w', 'Enter Discount Amount');
                        $(val).closest('tr').find('.discountPercentage').val(0);
                        $(val).closest('tr').find('.discountAmount').val(0);
                        $(val).closest('tr').find('.Netnumber').val(0);
                    } else {
                        var discamnt = (parseFloat(incamount) * parseFloat(incdiscountPercentage)) / 100;
                        $(val).closest('tr').find('.discountAmount').val(parseFloat(discamnt).toFixed(currency_decimal));
                        $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount) - parseFloat(discamnt)).toFixed(currency_decimal));
                    }
                } else {
                    if (jQuery.isEmptyObject(incamount) || incamount == 0) {
                        myAlert('w', 'Enter Discount Amount');
                        $(val).closest('tr').find('.discountPercentage').val(0);
                        $(val).closest('tr').find('.discountAmount').val(0);
                        $(val).closest('tr').find('.Netnumber').val(0);
                    } else {
                        var discprc = (parseFloat(incdiscountAmount) * 100) / parseFloat(incamount);

                        $(val).closest('tr').find('.discountPercentage').val(parseFloat(discprc));
                        $(val).closest('tr').find('.Netnumber').val((parseFloat(incamount) - parseFloat(incdiscountAmount)).toFixed(currency_decimal));
                    }
                }        
        }
        
    } 

    function calculateNetAmount_edit(val, fld) {
        var incamount = $('#edit_amount').val();
        var incdiscountPercentage = $('#discountPercentage_edit').val();
        var incdiscountAmount = $('#discountAmount_edit').val();

        if (fld == 'amount') {
            if (jQuery.isEmptyObject(incdiscountPercentage) || incdiscountPercentage == 0) {
                $('#Netamount_edit').val(incamount);
            } else {
                var discamnt = (parseFloat(incamount) * parseFloat(incdiscountPercentage)) / 100;
                $('#discountAmount_edit').val(parseFloat(discamnt).toFixed(currency_decimal));
                $('#Netamount_edit').val((parseFloat(incamount) - parseFloat(discamnt)).toFixed(currency_decimal));
            }
        } else if (fld == 'discountPercentage') {
            if (jQuery.isEmptyObject(incamount) || incamount == 0) {
                myAlert('w', 'Enter Discount Amount');
                $('#discountPercentage_edit').val(0);
                $('#discountAmount_edit').val(0);
                $('#Netamount_edit').val(0);
            } else {
                var discamnt = (parseFloat(incamount) * parseFloat(incdiscountPercentage)) / 100;
                $('#discountAmount_edit').val(parseFloat(discamnt).toFixed(currency_decimal));
                $('#Netamount_edit').val((parseFloat(incamount) - parseFloat(discamnt)).toFixed(currency_decimal));
            }
        } else {
            if (jQuery.isEmptyObject(incamount) || incamount == 0) {
                myAlert('w', 'Enter Discount Amount');
                $('#discountPercentage_edit').val(0);
                $('#discountAmount_edit').val(0);
                $('#Netamount_edit').val(0);
            } else {
                var discprc = (parseFloat(incdiscountAmount) * 100) / parseFloat(incamount);

                $('#discountPercentage_edit').val(parseFloat(discprc));
                $('#Netamount_edit').val((parseFloat(incamount) - parseFloat(incdiscountAmount)).toFixed(currency_decimal));
            }
        }
    }

    $('#inv_base_modal').on('shown.bs.modal', function () {
        $('#amount_total').focus();
    })

    function load_project_segmentBase_category(element, projectID) {
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
                var mySelect = $(element).parent().closest('tr').find('.project_categoryID');
                mySelect.append($('<option></option>').val('').html('Select Project Category'));

                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['categoryID']).html(text['categoryCode'] + ' - ' + text['categoryDescription']));
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

    function fetch_project_sub_category(element, categoryID) {
        var projectID = $(element).closest('tr').find('.projectID').val();

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/fetch_project_sub_category"); ?>',
            dataType: 'json',
            data: {categoryID: categoryID, projectID: projectID},
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $(element).parent().closest('tr').find('.project_subCategoryID').empty();
                var mySelect = $(element).parent().closest('tr').find('.project_subCategoryID');
                mySelect.append($('<option></option>').val('').html('Select Project Subcategory'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['subCategoryID']).html(text['description']));
                    });
                    if (projectsubcat) {
                        $("#project_subCategoryID_edit").val(projectsubcat).change();
                        $("#project_subCategoryID_edit1").val(projectsubcat).change();

                    }
                    ;
                }
                $('.select2').select2();
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }

    function fetch_purchase_price(purchaseprice, element, unitOfMeasureID, itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                id: payVoucherAutoId,
                purchaseprice: purchaseprice,
                //unitOfMeasureID: unitOfMeasureID,
                //itemAutoID: itemAutoID,
                tableName: 'srp_erp_paymentvouchermaster',
                primaryKey: 'payVoucherAutoId',
            },
            url: "<?php echo site_url('ItemMaster/fetch_purchase_price'); ?>",
            success: function (data) {
                $(element).closest('tr').find('.estimatedAmount').empty();
                //$(element).parent().closest('tr').find('.project_subCategoryID').empty();
                if (data['status']) {
                    $(element).closest('tr').find('.estimatedAmount').val(data.amount);
                }
                refreshNotifications(true);
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function load_expenseType(element, value) {
        
        $(element).closest('tr').find('#docTypeID').val('');
        $v = parseInt(value);
        if ($v == 1) {
            $(element).closest('tr').find('.glType').addClass('hidden');
            $(element).closest('tr').find('.expenseGlType').addClass('hidden');
            $(element).closest('tr').find('.ecType').removeClass('hidden');
           // $(element).closest('tr').find('.tax').removeClass('hidden');
            $('.tax').removeClass('hidden');
            $('.pAmount').addClass('hidden');
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Payment_voucher/fetch_expense_claim_code"); ?>',
                dataType: 'json',
                data: {
                    payVoucherAutoId: payVoucherAutoId
                },
                async: false,
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['details'])) {
                        $(element).closest('tr').find('#expenseClaimMasterAutoID').empty();
                        var mySelect = $(element).closest('tr').find('#expenseClaimMasterAutoID');
                        mySelect.append($('<option></option>').val('').html('Select Expense Claim'));
                        $.each(data['details'], function (val, text) {
                            mySelect.append($('<option></option>').val(text['expenseClaimMasterAutoID']).html(text['expenseClaimCode'] + ' | ' + text['comments']));
                        });
                    } else {
                        $(element).closest('tr').find('#expenseClaimMasterAutoID').empty();
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
        else if($v == 3){
            $(element).closest('tr').find('.glType').removeClass('hidden');
           // $(element).closest('tr').find('.ecType').addClass('hidden');
           // $(element).closest('tr').find('.expenseGlType').removeClass('hidden');
            $('.tax').addClass('hidden');
            $('.pAmount').removeClass('hidden');
            $.ajax({
                type: 'get',
                url: '<?php echo site_url("Payment_voucher/fetch_expense_gl_code"); ?>',
                dataType: 'json',
                //data: {
                //   payVoucherAutoId: payVoucherAutoId
                // },
                async: false,
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['details'])) {
                        $(element).closest('tr').find('#expenseGLCode').empty();
                        var mySelect = $(element).closest('tr').find('#expenseGLCode');
                        //mySelect.append($('<option></option>').val('').html('Select Expense GL'));
                        var value_str = '';
                        $.each(data['details'], function (val, text) {
                          //  mySelect.append($('<option></option>').val(text['expenseGLAutoID']).html(text['expenseglcode'] + '|' +text['expenseGLDescription']));
                         
                            //   $(element).closest('tr').find('.glType').find('#gl_code').prop('readonly',true);
                            if(text['expenseGLAutoID']){
                                value_str = text['expenseGLAutoID'];
                            }
                            
                        });
                       
                        $(element).closest('tr').find('.glType #gl_code').val(value_str).change();
                        $(element).closest('tr').find('.glType #gl_code').attr('readonly',true);

                        // $(element).closest('tr').find('#gl_code').attr('readonly',true);
                    } else {
                        $(element).closest('tr').find('#expenseGLCode').empty();
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });

            $.ajax({
                type: 'post',
                url: '<?php echo site_url("Payment_voucher/fetch_provision_amount"); ?>',
                dataType: 'json',
                data: {
                  payVoucherAutoId: payVoucherAutoId
                },
                async: false,
                success: function (data) {
                   if(data){
                        $(element).closest('tr').find('#pamount').val(parseFloat(data.amount).toFixed(2));
                        $(element).closest('tr').find('#pamount').prop('disabled',true);
                   }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
         else {
            $(element).closest('tr').find('.glType').removeClass('hidden');
            $(element).closest('tr').find('.ecType').addClass('hidden');
            $(element).closest('tr').find('.expenseGlType').addClass('hidden');
            $('.tax').removeClass('hidden');
            $('.pAmount').addClass('hidden');
        }
    }

    function selectedType(element, code) {
        $(element).closest('tr').find('.segment_glAdd').prop('disabled', false);
        $(element).closest('tr').find('.amount').prop('disabled', false);
        $(element).closest('tr').find('.discountPercentage').prop('disabled', false);
        $(element).closest('tr').find('.discountAmount').prop('disabled', false);
        if (code == 'EC') {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Payment_voucher/fetch_expense_claim_details"); ?>',
                dataType: 'json',
                data: {
                    payVoucherAutoId: payVoucherAutoId,
                    expenseClaimMasterAutoID: element.value
                },
                async: false,
                success: function (data) {
                    if (data) {
                        $(element).closest('tr').find('.segment_glAdd').val('').change();
                        $(element).closest('tr').find('.amount').val(parseFloat(data['amount']).toFixed(data['transactionCurrencyDecimalPlaces']));
                        $(element).closest('tr').find('.discountPercentage').val('');
                        $(element).closest('tr').find('.discountAmount').val('');
                        $(element).closest('tr').find('.Netnumber').val(parseFloat(data['amount']).toFixed(data['transactionCurrencyDecimalPlaces']));
                        $(element).closest('tr').find('.segment_glAdd').prop('disabled', true);
                        $(element).closest('tr').find('.amount').prop('disabled', true);
                        $(element).closest('tr').find('.discountPercentage').prop('disabled', true);
                        $(element).closest('tr').find('.discountAmount').prop('disabled', true);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }
        $(element).closest('tr').find('#docTypeID').val(code + '_' + element.value);
    }

    function add_more_ec_and_gl() {
        $('select.select2').select2('destroy');
        var appendData = $('#pv_ec_and_gl_add_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.glType').removeClass('hidden');
        appendData.find('.ecType').addClass('hidden');
        appendData.find('.segment_glAdd').prop('disabled', false);
        appendData.find('.amount').prop('disabled', false);
        appendData.find('.discountPercentage').prop('disabled', false);
        appendData.find('.discountAmount').prop('disabled', false);

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#pv_ec_and_gl_add_table').append(appendData);
        var lenght = $('#pv_ec_and_gl_add_table tbody tr').length - 1;
        $(".select2").select2();
        number_validation();
    }

    function savePaymentVoucher_ExpensesAndClaim() {
        <?php if($pullEC == 1) { ?>
        var url = "<?php echo site_url('Payment_voucher/save_emp_expense_multiple'); ?>";
        <?php } else { ?>
        var url = "<?php echo site_url('Payment_voucher/save_direct_pv_detail_multiple'); ?>"
        <?php } ?>

        $('.segment_glAdd').prop('disabled', false);
        $('.amount').prop('disabled', false);
        $('.discountPercentage').prop('disabled', false);
        $('.discountAmount').prop('disabled', false);
        var $form = $('#pv_ec_and_gl_add_form');
        var data = $form.serializeArray();
        data.push({'name': 'payVoucherAutoId', 'value': payVoucherAutoId});
        data.push({'name': 'payVoucherDetailAutoID', 'value': payVoucherDetailAutoID});
        $('select[name="gl_code[]"] option:selected').each(function () {
            data.push({'name': 'gl_code_des[]', 'value': $(this).text()})
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: url,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    payVoucherDetailAutoID = null;
                    $('#pv_ec_and_gl_add_form')[0].reset();
                    $("#segment_gl").select2("");
                    $("#gl_code").select2("");
                    setTimeout(function () {
                        fetch_pv_direct_details(4);
                        $('#pv_ec_and_gl_detail_modal').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }, 300);
                } else {
                    $('.segment_glAdd').prop('disabled', true);
                    $('.amount').prop('disabled', true);
                    $('.discountPercentage').prop('disabled', true);
                    $('.discountAmount').prop('disabled', true);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_expense_direct(id, expenseClaimMasterAutoID, claimCode) {
        swal({
                title: "Are you sure?",
                text: "You want to delete all records of Expense Claim " + claimCode,
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
                    data: {'pvDetailID': id, 'expenseClaimMasterAutoID': expenseClaimMasterAutoID},
                    url: "<?php echo site_url('Payment_voucher/delete_pv_expense_claim_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        setTimeout(function () {
                            fetch_pv_direct_details(4);
                        }, 300);

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function setQty(purchaseRequestDetailsID, amount, reqqty) {
        var reqQtyId = "#balQty_" + purchaseRequestDetailsID;
        var ordQtyId = "#qty_" + purchaseRequestDetailsID;
        $(ordQtyId).val($(reqQtyId).text());
        var data = {value: $(ordQtyId).val()};
        select_check_box_prq(data, purchaseRequestDetailsID, amount, reqqty);
    }

    function load_gl_line_tax_amount(ths) {
        var amount = $(ths).closest('tr').find('.amount').val();
        var discoun = $(ths).closest('tr').find('.discountAmount').val();
        var taxtype = $(ths).closest('tr').find('.gl_text_type').val();

        var lintaxappamnt = 0;
        if (jQuery.isEmptyObject(amount)) {
            amount = 0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun = 0;
        }
        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = amount;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'payVoucherAutoId': payVoucherAutoId,
                    'applicableAmnt': lintaxappamnt,
                    'taxtype': taxtype,
                    'discount': discoun
                },
                url: "<?php echo site_url('Payment_voucher/load_line_tax_amount_vat'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $(ths).closest('tr').find('.gl_linetaxamnt').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.Netnumber').val((parseFloat(data) + parseFloat(lintaxappamnt) - parseFloat(discoun)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        } else {
            $(ths).closest('tr').find('.gl_linetaxamnt').text('0');
            $(ths).closest('tr').find('.Netnumber').val((parseFloat(amount) - parseFloat(discoun)).toFixed(currency_decimal));
        }
    }

    function load_gl_line_tax_amount_edit(ths) {
        var amount = $('#edit_amount').val();
        var discoun = $('#discountAmount_edit').val();
        var taxtype = $('#gl_text_type_edit').val();
        var lintaxappamnt = 0;

        if (jQuery.isEmptyObject(amount)) {
            amount = 0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun = 0;
        }
        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = amount;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'payVoucherAutoId': payVoucherAutoId,
                    'applicableAmnt': lintaxappamnt,
                    'taxtype': taxtype,
                    'discount': discoun
                },
                url: "<?php echo site_url('Payment_voucher/load_line_tax_amount_vat'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    if (currency_decimal == 3) {
                        taxamount = data;
                        taxamount = Math.round(taxamount * 1000) / 1000;
                    } else {
                        taxamount = data;
                        taxamount = Math.round(taxamount * 100) / 100;
                    }
                    $('#gl_linetaxamnt_edit').text(taxamount.toFixed(currency_decimal));
                    $('#Netamount_edit').val((parseFloat(taxamount) + parseFloat(lintaxappamnt) - parseFloat(discoun)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        } else {
            $('#gl_linetaxamnt_edit').text('0');
            $('#Netamount_edit').val((parseFloat(amount) - parseFloat(discoun)).toFixed(currency_decimal));
        }
    }

    function load_line_tax_amount(ths) {
        var qut = $(ths).closest('tr').find('.quantityRequested').val();
        var amount = $(ths).closest('tr').find('.estimatedAmount').val();
        var taxtype = $(ths).closest('tr').find('.item_text').val();

        var lintaxappamnt = 0;
        if (jQuery.isEmptyObject(qut)) {
            qut = 0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount = 0;
        }
        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = (qut * amount);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'payVoucherAutoId': payVoucherAutoId,
                    'applicableAmnt': lintaxappamnt,
                    'taxtype': taxtype,
                    'discount': 0
                },
                url: "<?php echo site_url('Payment_voucher/load_line_tax_amount_vat'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    $(ths).closest('tr').find('.linetaxamnt').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.netAmount').val((parseFloat(data) + parseFloat(lintaxappamnt)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        } else {
            $(ths).closest('tr').find('.linetaxamnt').text('0');
            $(ths).closest('tr').find('.netAmount').val((parseFloat(qut * amount)).toFixed(currency_decimal));
        }
    }

    function fetch_line_tax_and_vat(itemAutoID, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'payVoucherAutoId': payVoucherAutoId, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Payment_voucher/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                if (data['isGroupByTax'] == 1) {
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

    function edit_fetch_line_tax_and_vat(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'payVoucherAutoId': payVoucherAutoId, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Payment_voucher/fetch_line_tax_and_vat'); ?>",
            success: function (data) {
                if (data['isGroupByTax'] == 1) {
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

    function load_line_tax_amount_edit(ths) {
        var qut = $('#edit_quantityRequested').val();
        var amount = $('#edit_estimatedAmount').val();
        var taxtype = $('#edit_item_text').val();
        var itemAutoID = $('#edit_itemAutoID').val();
        var lintaxappamnt = 0;
        if (jQuery.isEmptyObject(qut)) {
            qut = 0;
        }
        if (jQuery.isEmptyObject(amount)) {
            amount = 0;
        }
        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = (qut * amount);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'payVoucherAutoId': payVoucherAutoId,
                    'applicableAmnt': lintaxappamnt,
                    'taxtype': taxtype,
                    'itemAutoID': itemAutoID,
                    'discount': 0
                },
                url: "<?php echo site_url('Payment_voucher/load_line_tax_amount_vat'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    if (currency_decimal == 3) {
                        taxamount = data;
                        taxamount = Math.round(taxamount * 1000) / 1000;
                    } else {
                        taxamount = data;
                        taxamount = Math.round(taxamount * 100) / 100;
                    }
                    $('#linetaxamnt_edit').text(taxamount.toFixed(currency_decimal));
                    $('#editNetAmount').val((parseFloat(taxamount) + parseFloat(lintaxappamnt)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        } else {
            $('#linetaxamnt_edit').text('0');
            $('#editNetAmount').val((parseFloat(qut * amount)).toFixed(currency_decimal));
        }
    }
    
    function load_gl_line_tax_amount_emp(ths) 
    {
        var amount = $(ths).closest('tr').find('.amount').val();
        var discoun = $(ths).closest('tr').find('.discountAmount').val();
        var taxtype = $(ths).closest('tr').find('.item_text').val();

        var lintaxappamnt = 0;
        if (jQuery.isEmptyObject(amount)) {
            amount = 0;
        }
        if (jQuery.isEmptyObject(discoun)) {
            discoun = 0;
        }
        if (!jQuery.isEmptyObject(taxtype)) {
            lintaxappamnt = amount;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'payVoucherAutoId': payVoucherAutoId,
                    'applicableAmnt': lintaxappamnt,
                    'taxtype': taxtype,
                    'discount': discoun
                },
                url: "<?php echo site_url('Payment_voucher/load_line_tax_amount_vat'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications();
                    stopLoad();
                    // 

                    $(ths).closest('tr').find('.linetaxamnt_edit').text(data.toFixed(currency_decimal));
                    $(ths).closest('tr').find('.Netamount').val((parseFloat(data) + parseFloat(lintaxappamnt) - parseFloat(discoun)).toFixed(currency_decimal));
                    
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        } else {
            $(ths).closest('tr').find('.linetaxamnt_edit').text('0');
            $(ths).closest('tr').find('.Netamount').val((parseFloat(amount) - parseFloat(discoun)).toFixed(currency_decimal));
        }
    }

</script>