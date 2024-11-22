<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .boldtab{
        font-weight: bold;
        border-left-color: #ead8d8 !important;
    }
</style>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$projectExist = project_is_exist();
$umo_arr = array('' => 'Select UOM');
switch ($RVType) {
    case "Direct": ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li class="active"><a data-toggle="tab" class="boldtab" href="#tab_1" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_receivable_common_income'); ?><!--Income--></a></li>
                <li><a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false">
                        <?php echo $this->lang->line('common_item'); ?><!--Item--></a></li>
                <!-- <li><a data-toggle="tab" class="boldtab" href="#tab_4" aria-expanded="false">Advance</a></li> -->
                <li class="pull-left header"><i class="fa fa-hand-o-right"></i>
                    <?php echo $this->lang->line('accounts_receivable_common_direct_receipt_for'); ?><!--Direct Receipt for-->
                    :
                    - <?php echo $master['customerName']; ?></li>
            </ul>
            <div class="tab-content">
                <div id="tab_1" class="tab-pane active">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4"><?php echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                            <th colspan="3"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
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
                            <th style="min-width: 12%">Discount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 12%">Total <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>

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
                <!-- Items -->
                <div id="tab_2" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="5">
                                <?php echo $this->lang->line('accounts_receivable_common_item_details'); ?><!--Item Details--></th>
                            <th colspan="2"><?php echo $this->lang->line('common_price'); ?><!--Price--> <span
                                    class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                            <th>
                                <button type="button" onclick="rv_item_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i>
                                    <?php echo $this->lang->line('common_add_item'); ?><!--Add Item-->
                                </button>

                                <button type="button" onclick="edit_all_item_detail_modal()" style="margin-right:8px;"
                                        id="editallbtn" class="btn  btn-xs btn-default hidden pull-right"><span
                                        class="glyphicon glyphicon-pencil"></span> Edit All
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                            <th style="min-width: 36%" class="text-left">
                                <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th style="min-width: 7%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                            <th style="min-width: 7%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                            <th style="min-width: 15%"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
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
                <div id="tab_4" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="2">
                                <?php echo $this->lang->line('accounts_receivable_tr_advance_details'); ?><!--Advance Details--></th>
                            <th> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
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
                            <th style="min-width: 15%">
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
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span><?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
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
                                        <?php echo form_dropdown('segment_gl[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment_glAdd" onchange="load_segmentBase_projectID_income(this)"'); ?>
                                    </td>
                                    <?php if ($projectExist == 1) { ?>
                                        <td>
                                            <div class="div_projectID_income">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'amount')" value="00"
                                               class="form-control number amount">
                                    </td>
                                    <td><input type="text" name="discountPercentage[]" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'discountPercentage')" value="00" class="form-control number discountPercentage"></td>
                                    <td><input type="text" name="discountAmount[]" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'discountAmount')" value="00" class="form-control number discountAmount"></td>
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
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span><?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
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
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount"
                                               onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'amount')" value="00"
                                               id="edit_amount"
                                               class="form-control number">
                                    </td>
                                    <td><input type="text" name="discountPercentage" id="discountPercentage_edit" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'discountPercentage')" value="00" class="form-control number "></td>
                                    <td><input type="text" name="discountAmount" id="discountAmount_edit" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'discountAmount')" value="00" class="form-control number "></td>
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
            <div class="modal-dialog modal-lg">
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
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                                    <?php } ?>
                                    <th>
                                        <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>

                                    <th>
                                        Current Stock
                                    </th>

                                    <th>
                                        <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>


                                    <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                    <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                        <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <th style="display: none;">
                                        <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                    <th style="display: none;">
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
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 wareHouseAutoID" onchange="checkitemavailable(this)"  required'); ?>
                                    </td>
                                    <?php if ($projectExist == 1) { ?>
                                        <td>
                                            <div class="div_projectID_item">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown" disabled required'); ?>
                                    </td>

                                    <td>

                                        <input type="text" name="currentstock"
                                               id="currentstock"
                                               class="form-control currentstock" required disabled>

                                    </td>

                                    <td>
                                        <input type="text" onchange="change_amount(this,1)"
                                               onkeyup="checkCurrentStock(this)"
                                               name="quantityRequested[]"
                                               placeholder="0.00" class="form-control quantityRequested number"
                                               required>
                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount(this,1)" name="estimatedAmount[]"
                                                onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number estimatedAmount">
                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount(this,2)" name="netAmount[]"

                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number netAmount input-mini">
                                    </td>
                                    <td style="display: none;">
                                        <textarea class="form-control" rows="3" name="comment[]"
                                                  placeholder="<?php echo $this->lang->line('accounts_receivable_common_item_comments'); ?>..."></textarea>
                                        <!--Item Comment-->
                                    </td>
                                    <td style="display: none;">
                                        <textarea class="form-control" rows="3" name="remarks[]"
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
            <div class="modal-dialog modal-lg">
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
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                                    <?php } ?>
                                    <th>
                                        <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                    <th>
                                        Current Stock
                                    </th>

                                    <th>
                                        <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                    <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
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
                                               placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?>...">
                                        <!--Item ID--><!--Item Description-->
                                        <input type="hidden" class="form-control" name="itemAutoID"
                                               id="edit_itemAutoID">
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2" id="edit_wareHouseAutoID" onchange="editstockwhreceiptvoucher(this)" required'); ?>
                                    </td>
                                    <?php if ($projectExist == 1) { ?>
                                        <td>
                                            <div id="edit_div_projectID_item">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control" disabled required id="edit_UnitOfMeasureID"'); ?>
                                    </td>

                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="currentstock_edit"
                                                   id="currentstock_edit"
                                                   class="form-control" required disabled>
                                        </div>
                                    </td>

                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,1)"
                                               onkeyup="checkCurrentStockEdit(this)"
                                               name="quantityRequested"
                                               placeholder="0.00" class="form-control number"
                                               id="edit_quantityRequested" required>
                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,1)" name="estimatedAmount"
                                               placeholder="0.00" onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number" id="edit_estimatedAmount">
                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,2)" id="editNetAmount"
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
                                        <textarea class="form-control" rows="3" name="remarks"
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
    case "Invoices": ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-right">
                <li><a data-toggle="tab" class="boldtab" href="#tab_1" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_receivable_common_income'); ?><!--Income--></a></li>
                <li><a data-toggle="tab" class="boldtab" href="#tab_2" aria-expanded="false">
                        <?php echo $this->lang->line('common_item'); ?><!--Item--></a></li>
                <li><a data-toggle="tab" class="boldtab" href="#tab_3" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_receivable_common_invoices'); ?><!--Invoices--></a></li>
                <li><a data-toggle="tab" class="boldtab" href="#tab_5" aria-expanded="false">Credit Note</a></li>
                <li><a data-toggle="tab" class="boldtab" href="#tab_4" aria-expanded="false">
                        <?php echo $this->lang->line('accounts_receivable_common_advance'); ?><!--Advance--></a></li>
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
                            <th colspan="4"><?php echo $this->lang->line('common_gl_details'); ?><!--GL Details--></th>
                            <th colspan="3"> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
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
                            <th style="min-width: 12%">Discount <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
                            <th style="min-width: 12%">Total <span class="trcurrency">(<?php echo $master['transactionCurrency']; ?>)</span></th>
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
                <div id="tab_2" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="5">
                                <?php echo $this->lang->line('accounts_receivable_common_item_details'); ?><!--Item Details--></th>
                            <th colspan="2"><?php echo $this->lang->line('common_price'); ?><!--Price--> <span
                                    class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                            <th>
                                <button type="button" onclick="rv_item_detail_modal()"
                                        class="btn btn-primary pull-right btn-xs"><i
                                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?>
                                    <!--Add Item-->
                                </button>

                                <button type="button" onclick="edit_all_item_detail_modal()" style="margin-right:8px;"
                                        id="editallbtn" class="btn  btn-xs btn-default hidden pull-right"><span
                                        class="glyphicon glyphicon-pencil"></span> Edit All
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                            <th style="min-width: 36%" class="text-left">
                                <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th style="min-width: 7%"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                            <th style="min-width: 7%"><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                            <th style="min-width: 10%"><?php echo $this->lang->line('common_unit'); ?><!--Unit--></th>
                            <th style="min-width: 15%"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
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
                <div id="tab_3" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4">
                                <?php echo $this->lang->line('accounts_receivable_common_invoice_details'); ?><!--Invoice Details--></th>
                            <th colspan="4"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                    class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                            <th>
                                <button type="button" data-toggle="modal" data-target="#inv_base_modal"
                                        class="btn btn-primary pull-right btn-xs"><i class="fa fa-plus"></i> Add Invoice
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
                            <th style="min-width: 11%">
                                <?php echo $this->lang->line('accounts_receivable_common_balance'); ?><!--Balance--></th>
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
                <div id="tab_5" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="4">Credit Note Details</th>
                            <th colspan="4">Amount <span
                                    class="currency">(<?php echo $master['transactionCurrency']; ?>
                                    )</span></th>
                            <th>
                                <button type="button" data-toggle="modal" data-target="#creditNote_base_modal"
                                        class="btn btn-primary pull-right btn-xs"><i
                                        class="fa fa-plus"> </i> Add Credit Notes
                                </button>
                            </th>
                        </tr>
                        <tr>
                            <th style="min-width: 5%">#</th>
                            <th style="min-width: 15%">Code</th>
                            <th style="min-width: 15%" class="text-left">Reference</th>
                            <th style="min-width: 11%">Date</th>
                            <th style="min-width: 11%">Credit Note</th>
                            <th style="min-width: 11%">Due</th>
                            <th style="min-width: 11%">Matched</th>
                            <th style="min-width: 11%">Balance</th>
                            <th style="min-width: 10%">Action</th>
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
                <div id="tab_4" class="tab-pane">
                    <table class="table table-bordered table-striped table-condesed">
                        <thead>
                        <tr>
                            <th colspan="2">
                                <?php echo $this->lang->line('accounts_receivable_tr_advance_details'); ?><!--Advance Details--></th>
                            <th> <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
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
                            <th style="min-width: 15%">
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
                <br>
            </div><!-- /.tab-content -->
        </div>
        <!--<div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick="">Previous</button>
        </div>-->
        <div class="modal fade" id="inv_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             data-width="100%" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" role="document" style="width:70%">
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
                                <div class="skin-section extraColumns">Select All &nbsp;<input id="issubtask" type="checkbox"
                                                                                               data-caption="" class="columnSelected add_allinvoices"
                                                                                               name="issubtask" value="1"><label
                                            for="checkbox">&nbsp;</label></div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">

                        <div class="form-group col-sm-2">
                            &nbsp; &nbsp;<strong style="font-size:13px;color: #4a8cdb;"> Settlement Amount </strong>
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
                                    <div class="col-md-12" style="text-align: right;"><strong>Utilized  Amount</strong>&nbsp;
                                        <span id="total_invoice_total"><?php echo number_format($totalamountreceipt['totalamounttransaction'],$master['transactionCurrencyDecimalPlaces']) ;?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="table-responsive">
                                <div class="col-md-12" style="font-size:13px;color: #4a8cdb">
                                    <div class="col-md-12" style="text-align: right;"><strong>Balance</strong>&nbsp;
                                        <span id="grandtotal_amount"><?php echo number_format(($master['settlementTotal']-$totalamountreceipt['totalamounttransaction']),$master['transactionCurrencyDecimalPlaces']) ;?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="modal-body">
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
                                <th style="width: 12%">
                                    <?php echo $this->lang->line('common_invoice_date'); ?><!--Invoice Date--></th>
                                <th style="width: 20%">
                                    <?php echo $this->lang->line('accounts_receivable_common_reference_no'); ?><!--Reference No--></th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('accounts_receivable_common_invoice_total'); ?><!--Invoice Total--></th>
                                <th style="width: 15%">
                                    <?php echo $this->lang->line('accounts_receivable_common_balance'); ?><!--Balance--></th>
                                <th style="width: 17%">
                                    <?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                            </tr>
                            </thead>
                            <tbody id="table_body" class="invoice_base">
                            <input type="hidden" class="form-control" id="totalamount" name="totalamount" value="<?php echo $totalamountreceipt['totalamounttransaction'];?>">
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
                            <table class="table table-bordered table-condensed no-color" id="income_add_table">
                                <thead>
                                <tr>
                                    <th style="width: 380px">
                                        <?php echo $this->lang->line('common_gl_code'); ?><!--GL Code--> <?php required_mark(); ?></th>
                                    <th>
                                        <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></th>
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span><?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
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
                                        <td>
                                            <div class="div_projectID_income">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'amount')" value="00"
                                               class="form-control number amount">
                                    </td>
                                    <td><input type="text" name="discountPercentage[]" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'discountPercentage')" value="00" class="form-control number discountPercentage"></td>
                                    <td><input type="text" name="discountAmount[]" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount(this,'discountAmount')" value="00" class="form-control number discountAmount"></td>
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
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                                    <?php } ?>
                                    <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span><?php required_mark(); ?></th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
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
                                    <?php } ?>
                                    <td>
                                        <input type="text" name="amount"
                                               onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'amount')" value="00"
                                               id="edit_amount"
                                               class="form-control number">
                                    </td>
                                    <td><input type="text" name="discountPercentage" id="discountPercentage_edit" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'discountPercentage')" value="00" class="form-control number "></td>
                                    <td><input type="text" name="discountAmount" id="discountAmount_edit" onkeypress="return validateFloatKeyPress(this,event)" onkeyup="calculateNetAmount_edit(this,'discountAmount')" value="00" class="form-control number "></td>
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
            <div class="modal-dialog modal-lg">
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
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                                    <?php } ?>
                                    <th>
                                        <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>
                                    <th>Current Stock</th>
                                    <th>
                                        <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                    <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
                                    <th style="width: 120px;">
                                        <?php echo $this->lang->line('common_net_amount'); ?><!--Net Amount--> <?php required_mark(); ?>
                                        <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                    </th>
                                    <th style="display: none;">
                                        <?php echo $this->lang->line('common_comment'); ?><!--Comment--></th>
                                    <th style="display: none;">
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
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 wareHouseAutoID" onchange="checkitemavailable(this)"  required'); ?>
                                    </td>
                                    <?php if ($projectExist == 1) { ?>
                                        <td>
                                            <div class="div_projectID_item">
                                                <select name="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, 'Each', 'class="form-control umoDropdown" disabled required'); ?>
                                    </td>

                                    <td>
                                        <div class="input-group">
                                            <input type="text" name="currentstock[]"
                                                   class="form-control currentstock" required disabled>
                                        </div>

                                    <td>
                                        <input type="text" onchange="change_amount(this,1)"
                                               onkeyup="checkCurrentStock(this)"
                                               name="quantityRequested[]"
                                               class="form-control number quantityRequested"
                                               onfocus="this.select();" required>
                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount(this,1)" name="estimatedAmount[]"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onfocus="this.select();"
                                                class="form-control number estimatedAmount">
                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount(this,2)" name="netAmount[]"

                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               class="form-control number netAmount input-mini">
                                    </td>
                                    <td style="display: none;">
                                        <textarea class="form-control" rows="3" name="comment[]"
                                                  placeholder="<?php echo $this->lang->line('accounts_receivable_common_item_comments'); ?>..."></textarea>
                                        <!--Item Comment-->
                                    </td>
                                    <td style="display: none;">
                                        <textarea class="form-control" rows="3" name="remarks[]"
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
            <div class="modal-dialog modal-lg">
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
                                    <?php if ($projectExist == 1) { ?>
                                        <th>
                                            <?php echo $this->lang->line('common_project'); ?><!--Project--> <?php required_mark(); ?></th>
                                    <?php } ?>
                                    <th>
                                        <?php echo $this->lang->line('common_uom'); ?><!--UOM--> <?php required_mark(); ?></th>

                                    <th>
                                        Current Stock
                                    </th>
                                    <th>
                                        <?php echo $this->lang->line('common_qty'); ?><!--Qty--> <?php required_mark(); ?></th>
                                    <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                            class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                            )</span> <?php required_mark(); ?></th>
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
                                    </td>
                                    <td>
                                        <?php echo form_dropdown('wareHouseAutoID', all_delivery_location_drop(), '', 'class="form-control select2" id="edit_wareHouseAutoID" onchange="editstockwhreceiptvoucher(this)"  required'); ?>
                                    </td>
                                    <?php if ($projectExist == 1) { ?>
                                        <td>
                                            <div id="edit_div_projectID_item">
                                                <select name="projectID" id="projectID" class="form-control select2">
                                                    <option value="">
                                                        <?php echo $this->lang->line('common_select_project'); ?><!--Select Project--></option>
                                                </select>
                                            </div>
                                        </td>
                                    <?php } ?>
                                    <td>
                                        <?php echo form_dropdown('UnitOfMeasureID', $umo_arr, 'Each', 'class="form-control" disabled required id="edit_UnitOfMeasureID" '); ?>
                                    </td>

                                    <td>

                                        <input type="text" name="currentstock_edit"
                                               id="currentstock_edit"
                                               class="form-control" required disabled>

                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,1)"
                                               name="quantityRequested" onfocus="this.select();"
                                               onkeyup="checkCurrentStockEdit(this)"
                                               placeholder="0.00" class="form-control number"
                                               id="edit_quantityRequested" required>
                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,1)" name="estimatedAmount"
                                               placeholder="0.00" class="form-control number"
                                               onkeypress="return validateFloatKeyPress(this,event)"
                                               onfocus="this.select();" id="edit_estimatedAmount">
                                    </td>
                                    <td>
                                        <input type="text" onchange="change_amount_edit(this,2)" id="editNetAmount"
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
                                        <textarea class="form-control" rows="3" name="remarks"
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
                                <th colspan="4">Credit Note Details</th>
                                <th colspan="3"><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                        class="currency"> (<?php echo $master['transactionCurrency']; ?>)</span>
                                </th>
                            </tr>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">Credit Note Code</th>
                                <th style="width: 5%">Type</th>
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
                                        echo "<td>" . $val['RefNo'] . "</td>";
                                        echo "<td class='text-right'>" . number_format($val['transactionAmount'], $d) . "</td>";
                                        echo "<td class='text-right'>" . number_format($dif, $d) . "</td>";
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
                                                   class="number">
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
    <div class="modal-dialog">
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
                            <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--> <span
                                    class="currency"> (<?php echo $master['transactionCurrency']; ?>
                                    )</span> <?php required_mark(); ?></th>
                            <th>
                                <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_advance()">
                                    <i class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input type="text" name="amount[]" onkeypress="return validateFloatKeyPress(this,event)"
                                       value="00" class="form-control number">
                            </td>
                            <td><textarea class="form-control" rows="1" name="description[]"></textarea>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                    <!-- <div class="form-group">
                                    <label class="col-sm-4 control-label">PO Code <?php //required_mark(); ?></label>
                                    <div class="col-sm-6">
                                        <?php //echo form_dropdown('po_code', $po_arr, '','class="form-control select2" id="po_code" '); ?>
                                    </div>
                                </div> -->
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
    var projectID_income;
    var projectID_item;
    var defaultSegment = <?php echo json_encode($this->common_data['company_data']['default_segment']); ?>;
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

                            fetch_details_buyback(1);
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

    function rv_detail_modal() {
        if (receiptVoucherAutoId) {
            $("#gl_code").val(null).trigger("change");
            $('#rv_detail_form')[0].reset();
            $('.segment_glAdd').val(defaultSegment).change();
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
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
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
                check_item_not_approved_document_wise(suggestion.itemAutoID,'add','RV',id);
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
                }, 200);
                fetch_sales_price_edit(suggestion.companyLocalSellingPrice, this, suggestion.defaultUnitOfMeasureID, suggestion.itemAutoID);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
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
                    $('#edit_wareHouseAutoID').attr('onchange', 'editstockwhreceiptvoucher(this)');
                }
                check_item_not_approved_document_wise(suggestion.itemAutoID,'edit','RV',1);
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

    function save_inv_base_items() {
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
                data: {'invoiceAutoID': selected, 'receiptVoucherAutoId': receiptVoucherAutoId, 'amount': amount,'settlementAmount':totalsettlement},
                url: "<?php echo site_url('Receipt_voucher/save_inv_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#inv_base_modal').modal('hide');
                    refreshNotifications(true);
                    setTimeout(function () {

                        fetch_details_buyback(1);
                    }, 300);
                }, error: function () {
                    $('#inv_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
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

                                fetch_details_buyback(1);
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
                $('#gl_table_body,#item_table_body,#invoice_table_body,#advance_table_body,#creditNote_table_body').empty();
                $('#item_table_tfoot,#invoice_table_tfoot,#advance_table_tfoot,#gl_table_tfoot,#creditNote_table_tfoot').empty();
                if (jQuery.isEmptyObject(data['detail'])) {
                    $("#customerID").prop("disabled", false);
                    $("#transactionCurrencyID").prop("disabled", false);
                    $("#vouchertype").prop("disabled", false);
                    $("#addcustomer").prop("disabled", false);
                    currencyID = null;
                    $('#gl_table_body,#item_table_body,#advance_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b>No Records Found</b></td></tr>');
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
                    gl_local_amount = 0;
                    gl_party_amount = 0;
                    po_trans_amount = 0;
                    po_local_amount = 0;
                    po_party_amount = 0;
                    item_trans_amount = 0;
                    item_local_amount = 0;
                    item_party_amount = 0;
                    invoice_amount = 0;
                    due_amount = 0;
                    paid_amount = 0;
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

                            if (value['isSubitemExist'] == 1) {
                                var colour = 'color: #dad835 !important';
                                colour = '';

                                string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['Itemdescriptionpartno'] + ' - ' + wareloc + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['receiptVoucherDetailAutoID'] + ',\'RV\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; |&nbsp;&nbsp;<a onclick="edit_item(' + value['receiptVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                            } else {

                                string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['Itemdescriptionpartno'] + ' - ' + wareloc + '</td><td>' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['requestedQty'] + '</td><td class="text-right">' + parseFloat(value['unittransactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['receiptVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',2);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';

                            }
                            $('#item_table_body').append(string);

                            //$('#item_table_body').append();

                            x++;
                            item_trans_amount += (parseFloat(value['transactionAmount']));
                            item_local_amount += (parseFloat(value['companyLocalAmount']));
                            item_party_amount += (parseFloat(value['customerAmount']));
                            tax_total += (parseFloat(value['transactionAmount']));
                            $('#item_table_tfoot').append('<tr><td colspan="6" class="text-right"> Total </td><td class="text-right total">' + parseFloat(item_trans_amount).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                            //<a onclick="edit_item('+value['receiptVoucherDetailAutoID']+',\''+value['itemDescription']+'\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                        } else if (value['type'] == 'Invoice') {
                            $('#invoice_table_tfoot').empty();
                            $('#invoice_table_body').append('<tr><td>' + y + '</td><td>' + value['invoiceCode'] + '</td><td>' + value['referenceNo'] + '</td><td class="text-center">' + value['invoiceDate'] + '</td><td class="text-right">' + parseFloat(value['Invoice_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['due_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ', 1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            y++;
                            invoice_amount += (parseFloat(value['Invoice_amount']));
                            due_amount += (parseFloat(value['due_amount']));
                            paid_amount += (parseFloat(value['transactionAmount']));
                            Balance_amount += (parseFloat(value['balance_amount']));
                            $('#invoice_table_tfoot').append('<tr><td colspan="6" class="text-right"> Total Paid </td><td class="text-right total">' + parseFloat(paid_amount).formatMoney(currency_decimal, '.', ',') + '</td><td colspan="2"></td></tr>');
                            //<a onclick="edit_item('+value['receiptVoucherDetailAutoID']+',\''+value['GLDescription']+'\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                        } else if (value['type'] == 'Advance') {
                            $('#advance_table_body').append('<tr><td>' + y + '</td><td>' + value['comment'] + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',1);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            y++;
                            //<td class="text-right">' + parseFloat(value['companyLocalAmount']).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['customerAmount']).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                            po_trans_amount += (parseFloat(value['transactionAmount']));
                            //po_local_amount += (parseFloat(value['companyLocalAmount']));
                            //po_party_amount += (parseFloat(value['customerAmount']));
                            //<a onclick="edit_advance_item('+value['receiptVoucherDetailAutoID']+',\''+value['GLDescription']+'\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                        }else if (value['type'] == 'creditnote' || value['type'] == 'SLR') {
                            $('#creditNote_table_body').append('<tr><td>' + z + '</td><td>' + value['invoiceCode'] + '</td><td>' + value['referenceNo'] + '</td><td class="text-center">' + value['invoiceDate'] + '</td><td class="text-right">' + parseFloat(value['Invoice_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['due_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['balance_amount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',5);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            z++;
                            /*invoice_amount += (parseFloat(value['Invoice_amount']));
                             due_amount += (parseFloat(value['due_amount']));*/
                            cdTotal_amount += (parseFloat(value['transactionAmount']));
                            //Balance_amount += (parseFloat(value['balance_amount']));

                        } else {
                            $('#gl_table_tfoot').empty();
                            var transamnt=parseFloat(value['transactionAmount'])+parseFloat(value['discountAmount']);
                            $('#gl_table_body').append('<tr><td>' + y + '</td><td>' + value['GLCode'] + '</td><td>' + value['GLDescription'] + ' - ' +  value['description'] + '</td><td class="text-center">' + value['segmentCode'] + '</td><td class="text-right">' + parseFloat(transamnt).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">('+ parseFloat(value['discountPercentage']).formatMoney(2, '.', ',') +' %) ' + parseFloat(value['discountAmount']).formatMoney(currency_decimal, '.', ',') + '</td> <td class="text-right">' + parseFloat(value['transactionAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_income_item(' + value['receiptVoucherDetailAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_item_direct(' + value['receiptVoucherDetailAutoID'] + ',3);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>');
                            y++;
                            //<td class="text-right">' + parseFloat(value['companyLocalAmount']).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right">' + parseFloat(value['customerAmount']).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                            gl_trans_amount += (parseFloat(value['transactionAmount']));
                            tax_total += (parseFloat(value['transactionAmount']));
                            //gl_local_amount += (parseFloat(value['companyLocalAmount']));
                            //gl_party_amount += (parseFloat(value['customerAmount']));
                            $('#gl_table_tfoot').append('<tr><td colspan="6" class="text-right"><?php echo $this->lang->line('common_total');?> <!--Total--> </td><td class="text-right total">' + parseFloat(gl_trans_amount).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
                            //<td class="text-right total">' + parseFloat(gl_local_amount).formatMoney(LocalDecimalPlaces, '.', ',') + '</td><td class="text-right total">' + parseFloat(gl_party_amount).formatMoney(partyDecimalPlaces, '.', ',') + '</td>
                            //<a onclick="edit_item('+value['receiptVoucherDetailAutoID']+',\''+value['GLDescription']+'\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;
                        }
                    });
                    $('#advance_table_tfoot').append('<tr><td colspan="2" class="text-right"> <?php echo $this->lang->line('common_total');?><!--Total--> </td><td class="text-right total">' + parseFloat(po_trans_amount).formatMoney(currency_decimal, '.', ',') + '</td><td>&nbsp;</td></tr>');
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

                            fetch_details_buyback(tab);
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

    function add_more_item() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#item_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        //appendData.find('.umoDropdown').empty();
        appendData.find('input').val('');
        appendData.find('textarea').val('');

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
                if (this.value == '') {
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
                primaryKey: 'receiptVoucherAutoId'
            },
            url: "<?php echo site_url('ItemMaster/fetch_sales_price'); ?>",
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
                            $('#edit_gl_code').val(data['GLAutoID']).change();
                            $('#edit_description').val(data['description']);
                            $('#edit_amount').val((parseFloat(data['transactionAmount'])+parseFloat(data['discountAmount'])).toFixed(currency_decimal));
                            $('#discountPercentage_edit').val(parseFloat(data['discountPercentage']).toFixed(2));
                            $('#discountAmount_edit').val(parseFloat(data['discountAmount']).toFixed(currency_decimal));
                            $('#Netamount_edit').val(parseFloat(data['transactionAmount']).toFixed(currency_decimal));
                            $('#edit_segment_gl').val(data['segmentID'] + '|' + data['segmentCode']).change();
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
                        //pv_item_detail_modal();
                        receiptVoucherDetailAutoID = data['receiptVoucherDetailAutoID'];
                        projectID_item = data['projectID'];
                        load_segmentBase_projectID_itemEdit(data['segmentID']);
                        $('#search').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                        fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                        $('#edit_quantityRequested').val(data['requestedQty']);
                        $('#editNetAmount').val(data['transactionAmount']);
                        $('#edit_estimatedAmount').val((parseFloat(data['unittransactionAmount'])));
                        $('#edit_search_id').val(data['itemSystemCode']);
                        $('#edit_itemSystemCode').val(data['itemSystemCode']);
                        $('#edit_itemAutoID').val(data['itemAutoID']);
                        $('#edit_itemDescription').val(data['itemDescription']);
                        $('#edit_wareHouseAutoID').val(data['wareHouseAutoID']).change();
                        $('#edit_comment').val(data['comment']);
                        $("#edit_rv_item_detail_modal").modal({backdrop: "static"});
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
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
        if (jQuery.inArray(mainconcat, concatarr) !== -1) {
            $(det).closest('tr').find('.f_search').val('');
            $(det).closest('tr').find('.itemAutoID').val('');
            $(det).closest('tr').find('.wareHouseAutoID').val('').change();
            $(det).closest('tr').find('.quantityRequested').val('');
            $(det).closest('tr').find('.estimatedAmount').val('');
            $(det).closest('tr').find('.netAmount').val('');
            myAlert('w', 'Selected item is already selected');
        }
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
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item'); ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {
                    if (data['status']) {
                        if(data['mainCategory']=='Service'){
                            $(det).closest('tr').find('.currentstock').val('');
                        }else if(data['mainCategory']=='Non Inventory'){
                            $(det).closest('tr').find('.currentstock').val('');
                        }else{
                            $(det).closest('tr').find('.currentstock').val(data['currentStock']);
                        }
                    } else {

                        $(det).typeahead('val', '');
                        $(det).closest('tr').find('.currentstock').val('');


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
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item'); ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {
                    if (data['status']) {

                        $(det).closest('tr').find('.currentstock').val(data['currentStock']);
                        if(data['mainCategory']=='Service'){
                            $('#currentstock_edit').val('');
                        }else if(data['mainCategory']=='Non Inventory'){
                            $('#currentstock_edit').val('');
                        }else{
                            $('#currentstock_edit').val(data['currentStock']);
                        }

                    } else {

                        $(det).typeahead('val', '');
                        $(det).closest('tr').find('.currentstock').val('');


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

        if (det.value > parseFloat(currentStock)) {
            myAlert('w', 'Transfer quantity should be less than or equal to current stock');
            $(det).val(0);
        }
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
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
                data: {'wareHouseAutoID': wareHouseAutoID, 'itemAutoID': itemAutoID},
                url: "<?php echo site_url('Receipt_voucher/fetch_rv_warehouse_item'); ?>",
                beforeSend: function () {
                    //startLoad();
                },
                success: function (data) {
                    if (data['status']) {
                        if(data['mainCategory']=='Service'){
                            $('#currentstock_edit').val('');
                        }else if(data['mainCategory']=='Non Inventory'){
                            $('#currentstock_edit').val('');
                        }else{
                            $('#currentstock_edit').val(data['currentStock']);
                        }

                    } else {
                        $('#currentstock_edit').val('');


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
        if (parseFloat(TransferQty) > parseFloat(currentStock)) {
            myAlert('w', 'Transfer quantity should be less than or equal to current stock');
            $('#edit_quantityRequested').val(0);
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
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    <!--No Records Found-->
                } else {
                    $.each(data['detail'], function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown" disabled  required')) ?>';
                        var wareHouseAutoID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="ware_\'+key+\'"'), form_dropdown('wareHouseAutoID[]', all_delivery_location_drop(), '', 'class="form-control select2 wareHouseAutoID"  onchange="checkitemavailable(this)" required')) ?>';
                        var project = '';
                        <?php if ($projectExist == 1) { ?>
                        project = ' <td> <div class="div_projectID_item"> <select name="projectID" class="form-control select2"> <option value="">Select Project</option> </select> </div> </td>';
                        <?php
                        } ?>
                        var string = '<tr><td> <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control search f_search" name="search[]" id="f_search_' + x + '" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" placeholder="Item ID,Item Description...">  <input type="hidden" class="form-control itemAutoID" value="' + value['itemAutoID'] + '"  name="itemAutoID[]"> <input type="hidden" class="form-control receiptVoucherDetailAutoID" value="' + value['receiptVoucherDetailAutoID'] + '"  name="receiptVoucherDetailAutoID[]"> </td> <td>' + wareHouseAutoID + '</td> ' + project + ' <td>' + UOM + '</td> <td> <div class="input-group"> <input type="text" name="currentstock[]" class="form-control currentstock" required disabled></div></td> <td> <input type="text" onchange="change_amount(this,1)" onkeyup="checkCurrentStock(this)" name="quantityRequested[]" placeholder="0.00" class="form-control number quantityRequested" onfocus="this.select();" value="' + value['requestedQty'] + '" required> </td><td> <input type="text" onchange="change_amount(this,1)" name="estimatedAmount[]" onkeypress="return validateFloatKeyPress(this,event)" value="' + value['unittransactionAmount'] + '" onfocus="this.select();" placeholder="0.00" class="form-control number estimatedAmount"> </td><td> <input type="text" onchange="change_amount(this,2)" name="netAmount[]" placeholder="0.00" onkeypress="return validateFloatKeyPress(this,event)" class="form-control number netAmount input-mini" value="' + value['transactionAmount'] + '"> </td><td class="remove-td"><a onclick="delete_receipt_voucherDetailsEdit(' + value['receiptVoucherDetailAutoID'] + ',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
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
                <!--Total-->

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function add_more_edit_receipt_voucher() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#receipt_voucher_detail_all_edit_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');

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
        $('#table_body input:checked').each(function () {
            selected.push($(this).val());
            amount.push($('#CNamount_' + $(this).val()).val());
            types.push($('#type_' + $(this).val()).val());
            transactionAmount.push($('#CNTransAmount_' + $(this).val()).val());
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

                        fetch_details_buyback(5);
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
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(6).find('input').val());
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
                            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(6).find('input').val());
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
            var tot_valueoverhead = getNumberAndValidate($('td', this).eq(6).find('input').val());
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
            if (jQuery.isEmptyObject(incamount) || incamount==0) {
                myAlert('w','Enter Discount Amount');
                $('#discountPercentage_edit').val(0);
                $('#discountAmount_edit').val(0);
                $('#Netamount_edit').val(0);
            }else{
                var discamnt=(parseFloat(incamount)*parseFloat(incdiscountPercentage))/100;
                $('#discountAmount_edit').val(parseFloat(discamnt).toFixed(currency_decimal));
                $('#Netamount_edit').val((parseFloat(incamount)-parseFloat(discamnt)).toFixed(currency_decimal));
            }
        }else{
            if (jQuery.isEmptyObject(incamount) || incamount==0) {
                myAlert('w','Enter Discount Amount');
                $('#discountPercentage_edit').val(0);
                $('#discountAmount_edit').val(0);
                $('#Netamount_edit').val(0);
            }else{
                var discprc=(parseFloat(incdiscountAmount)*100)/parseFloat(incamount);

                $('#discountPercentage_edit').val(parseFloat(discprc));
                $('#Netamount_edit').val((parseFloat(incamount)-parseFloat(incdiscountAmount)).toFixed(currency_decimal));
            }
        }
    }

</script>