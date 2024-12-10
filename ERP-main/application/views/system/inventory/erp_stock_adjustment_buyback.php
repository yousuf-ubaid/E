<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);


echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$umo_arr = array('' => 'Select UOM');//all_umo_drop();
$location_arr = all_delivery_location_drop();
$location_arr_default = default_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$segment_arr = fetch_segment();
$adjustmentType_arr = array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'Inventory' => $this->lang->line('transaction_inventory') /*'Inventory'*/, 'Non Inventory' => $this->lang->line('transaction_non_inventory') /*'Non Inventory'*/);
$projectExist = project_is_exist();
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1"
       data-toggle="tab"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one'); ?>
        - <?php echo $this->lang->line('transaction_stock_adjustment_header'); ?> </a><!--Step 1-->
    <!--Stock Adjustment Header-->
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_detail()"
       data-toggle="tab"><?php echo $this->lang->line('transaction_goods_received_voucher_step_two'); ?>
        - <?php echo $this->lang->line('transaction_stock_adjustment_detail'); ?> </a><!--Step 2-->
    <!-- Stock Adjustment Detail-->
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_conformation()"
       data-toggle="tab"><?php echo $this->lang->line('transaction_goods_received_voucher_step_three'); ?>
        - <?php echo $this->lang->line('transaction_stock_adjustment_confirmation'); ?></a><!--Step 3-->
    <!--Stock Adjustment Confirmation-->
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="stock_adjustment_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="adjustmentType">Category Type<?php required_mark(); ?></label>
                <!--Adjustment Type-->
                <?php echo form_dropdown('adjustmentType', $adjustmentType_arr, '', 'class="form-control select2" id="adjustmentType" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('transaction_primary_segment'); ?><?php required_mark(); ?></label>
                <!--Primary Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('transaction_common_referenc_no'); ?></label><!--Reference No-->
                <input type="text" class="form-control " id="referenceNo" name="referenceNo">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('transaction_adjustment_date'); ?><?php required_mark(); ?></label>
                <!--Adjustment Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="stockAdjustmentDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="stockAdjustmentDate"
                           class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('transaction_common_financial_year'); ?><?php required_mark(); ?></label>
                <!--Financial Year-->
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear_period"><?php echo $this->lang->line('transaction_common_financial_period'); ?><?php required_mark(); ?></label>
                <!--Financial Period-->
                <?php echo form_dropdown('financeyear_period', array('' => 'Finance Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_Location'); ?><?php required_mark(); ?></label>
                <!--Location-->
                <?php echo form_dropdown('location', $location_arr, $location_arr_default, 'class="form-control select2" id="location" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="adjustmentType">Adjustment Type <?php required_mark(); ?></label>
                <select name="adjsType" class="form-control select2" id="adjsType" tabindex="-1" aria-hidden="true">
                    <option value="" selected="selected">Select Type</option>
                    <option value="0">Stock</option>
                    <option value="1">Wac</option>
                </select>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_narration'); ?><?php required_mark(); ?></label>
                <!--Narration-->
                <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary"
                    type="submit"><?php echo $this->lang->line('common_save_and_next'); ?></button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i
                            class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('transaction_add_adjustment_detail'); ?>
                </h4><!--Add Adjustment Detail-->
            </div>
            <div class="col-md-4">
                <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('transaction_add_adjustment'); ?>
                </button><!--Add Adjustment-->
            </div>
        </div>
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th colspan="4"><?php echo $this->lang->line('transaction_common_item_details'); ?></th>
                <!--Item Details-->
                <th colspan="2"><?php echo $this->lang->line('common_previous'); ?> (
                    <?php echo $this->common_data['company_data']['company_default_currency']; ?><!--Previous-->
                    )
                </th>
                <th colspan="2"><?php echo $this->lang->line('transaction_common_currenct'); ?> (
                    <?php echo $this->common_data['company_data']['company_default_currency']; ?><!--Current-->
                    )
                </th>
                <th colspan="6" id="col_adj"><?php echo $this->lang->line('transaction_common_adjusted'); ?> (
                    <?php echo $this->common_data['company_data']['company_default_currency']; ?><!--Adjusted-->
                    )
                </th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <th style="min-width: 3%">#</th>
                <th style="min-width: 9%"><?php echo $this->lang->line('transaction_common_item_code'); ?></th>
                <!--Item Code-->
                <th style="min-width: 20%"><?php echo $this->lang->line('transaction_common_item_description'); ?></th>
                <!--Item Description-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_uom'); ?></th><!--UOM-->
                <th style="min-width: 5%"><?php echo $this->lang->line('transaction_common_stock'); ?></th><!--Stock-->
                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac'); ?></th><!--Wac-->
                <th style="min-width: 5%"><?php echo $this->lang->line('transaction_common_stock'); ?></th><!--Stock-->
                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac'); ?></th><!--Wac-->

                <th style="min-width: 10%" class="typestock">Gross Weight </th>
                <th style="min-width: 10%" class="typestock">Buckets </th>
                <th style="min-width: 10%" class="typestock">B weight </th>

                <th style="min-width: 5%"><?php echo $this->lang->line('transaction_common_stock'); ?></th><!--Stock-->
                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac'); ?></th><!--Wac-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_amount'); ?></th><!--Amount-->
                <th style="min-width: 3%">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="item_table_body">
            <tr class="danger">
                <td colspan="15" id="adjs_no_rec" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?> </b>
                </td><!--No Records Found-->
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td class="text-right sub_total"
                    colspan="10" id="total_colspan"><?php echo $this->lang->line('transaction_adjustment_item_total'); ?>
                    (<?php echo $this->common_data['company_data']['company_default_currency']; ?>)
                </td><!--Adjustment Item Total-->
                <td id="tot" class="text-right total">00</td>
                <td>&nbsp;</td>
            </tr>
            </tfoot>
        </table>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"
                    onclick=""><?php echo $this->lang->line('common_previous'); ?> </button><!--Previous-->
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title"
                id="stockAdjustment_attachment_label"><?php echo $this->lang->line('transaction_goods_received_voucher_modal_title'); ?> </h4>
            <!--Modal title-->
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?> </th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description'); ?></th><!--Description-->
                        <th><?php echo $this->lang->line('common_type'); ?></th><!--Type-->
                        <th><?php echo $this->lang->line('common_action'); ?> </th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="stockAdjustment_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5"
                            class="text-center"><?php echo $this->lang->line('common_no_attachment_found'); ?></td>
                        <!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous'); ?> </button>
            <!--Previous-->
            <button class="btn btn-primary"
                    onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?> </button>
            <!--Save as Draft-->
            <button class="btn btn-success submitWizard"
                    onclick="confirmation()"><?php echo $this->lang->line('common_confirm'); ?> </button><!--Confirm-->
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" id="item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('transaction_add_adjustment'); ?></h5>
                <!--Add Adjustment-->
            </div>
            <div class="modal-body">
                <form role="form" id="item_detail_form" class="form-horizontal">
                    <input type="hidden" name="type" id="type">
                    <table class="table table-bordered table-condensed no-color" id="StockAdjustment_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 300px;"><?php echo $this->lang->line('transaction_common_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th style="width: 150px;"><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                            <!--Segment-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th style="width:200px;"><?php echo $this->lang->line('common_system_stock'); ?><?php required_mark(); ?></th>
                            <!--Current Stock-->
                            <th style="width:200px;"><?php echo $this->lang->line('common_system_wac'); ?><?php required_mark(); ?></th>
                            <!--Current Wac-->

                            <th style="width:200px;" class="typewac"><?php echo $this->lang->line('common_actual_wac'); ?><?php required_mark(); ?></th>
                            <!--Adjustment Stock-->
                            <th style="width:200px;" class="typestock">No of Birds</th>
                             <th style="width:200px;" class="typestock">Gross Weight</th>
                             <th style="width:200px;" class="typestock">Buckets</th>
                             <th style="width:200px;" class="typestock">B weight</th>
                            <th style="width:200px;" class="typestock"><?php echo $this->lang->line('common_actual_stock'); ?><?php required_mark(); ?></th>
                            <!--Adjustment WAC-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_stock_adjustment()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control search input-mini f_search" name="search[]"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?>..."
                                       id="f_search_1"><!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                <input type="hidden" class="form-control currentStock" name="currentStock[]">
                            </td>
                            <td>
                                <?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control umoDropdown"  required'); ?>
                            </td>
                            <td>
                                <?php echo form_dropdown('a_segment[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 a_segment" required onchange="load_segmentBase_projectID_item(this)"'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div class="div_projectID_income">
                                        <select name="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('transaction_goods_received_select_project'); ?></option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                            <?php } ?>
                            <td>
                                <div class="input-group">
                                    <!--<span class="input-group-addon input-group-addon-mini d_uom">Each</span>-->
                                    <input type="text" name="currentWareHouseStock[]"
                                           class="form-control currentWareHouseStock" readonly required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span
                                            class="input-group-addon input-group-addon-mini"><?php echo $this->common_data['company_data']['company_default_currency']; ?></span>
                                    <input type="text" name="currentWac[]" class="form-control currentWac" readonly
                                           required>
                                </div>
                            </td>

                            <td style="width: 100px" class="typewac">
                                <div class="input-group">
                                    <span
                                            class="input-group-addon input-group-addon-mini"><?php echo $this->common_data['company_data']['company_default_currency']; ?></span>
                                    <input type="text" name="adjustment_wac[]" onfocus="this.select();"
                                           class="form-control number adjustment_wac"
                                           required>
                                </div>
                            </td>

                            <td class="typestock"><input type="text" onfocus="this.select();" name="noOfItems[]"
                                       onkeyup="checkCurrentStock(this)" placeholder="0.00"
                                       class="form-control number input-mini noOfItems" required>
                            </td>
                            <td class="typestock"><input type="text" onfocus="this.select();" name="grossQty[]"
                                       onkeyup="checkCurrentStock(this),cal_deduction_netQty(this)" placeholder="0.00"
                                       class="form-control number input-mini grossQty" required>
                            </td>
                            <td class="typestock"><input type="text" onfocus="this.select();" name="noOfUnits[]"
                                       onkeyup="checkCurrentStock(this),cal_deduction_netQty(this);" placeholder="0.00"
                                       class="form-control number input-mini noOfUnits" required>
                            </td>
                            <td class="typestock"><?php echo form_dropdown('deduction[]',all_bucketweight_drop(),' ', 'class="form-control deduction input-mini"  onchange="cal_deduction_netQty(this)"  required'); ?>
                            </td>
                            <td class="typestock">
                                <div class="input-group">
                                    <!--<span class="input-group-addon input-group-addon-mini d_uom">Each</span>-->
                                    <input type="text" name="adjustment_Stock[]" onfocus="this.select();"
                                           onkeyup="validatetb_row(this)"
                                           class="form-control number adjustment_Stock" required>
                                </div>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?></button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="StockAdjustment_Detailadd()"><?php echo $this->lang->line('common_save_change'); ?>
                </button><!--Save changes-->
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="item_detail_modal_edit" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('transaction_common_edit_item_detail'); ?> </h5>
                <!--Edit Item Detail-->
            </div>
            <div class="modal-body">
                <form role="form" id="edit_item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="StockAdjustment_detail_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 300px;"><?php echo $this->lang->line('transaction_common_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <th style="width: 150px;"><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                            <!--Segment-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th style="width:200px;"><?php echo $this->lang->line('common_system_stock'); ?><?php required_mark(); ?></th>
                            <!--Current Stock-->
                            <th style="width:200px;"><?php echo $this->lang->line('common_system_wac'); ?><?php required_mark(); ?></th>
                            <!--Current Wac-->

                            <!--Adjustment Stock-->
                            <th style="width:200px;" class="typewac"><?php echo $this->lang->line('common_actual_wac'); ?><?php required_mark(); ?></th>
                            <!--Adjustment WAC-->
                            <th style="width:200px;" class="typestock">No of Birds<?php required_mark(); ?></th>
                            <th style="width:200px;" class="typestock">Gross Weight<?php required_mark(); ?></th>
                            <th style="width:200px;" class="typestock">Buckets<?php required_mark(); ?></th>
                            <th style="width:200px;" class="typestock">B weight<?php required_mark(); ?></th>
                            <th style="width:200px;" class="typestock"><?php echo $this->lang->line('common_actual_stock'); ?><?php required_mark(); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)"
                                       class="form-control input-mini"
                                       name="search"
                                       id="search"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?>...">
                                <!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control" id="itemAutoID_edit" name="itemAutoID">
                                <input type="hidden" class="form-control" id="currentStock_edit" name="currentStock">
                            </td>
                            <td>
                                <?php echo form_dropdown('unitOfMeasureID', $umo_arr, '', 'class="form-control" id="UnitOfMeasureID_edit"'); ?>
                            </td>
                            <td>
                                <?php echo form_dropdown('a_segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment_edit" required onchange="load_segmentBase_projectID_itemEdit(this)"'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_item">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('common_select_project'); ?></option>
                                            <!--Select Project-->
                                        </select>
                                    </div>
                                </td>
                            <?php } ?>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="currentWareHouseStock" id="currentWareHouseStock_edit"
                                           class="form-control" readonly required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span
                                            class="input-group-addon input-group-addon-mini"><?php echo $this->common_data['company_data']['company_default_currency']; ?></span>
                                    <input type="text" name="currentWac" id="currentWac_edit" class="form-control"
                                           readonly
                                           required>
                                </div>
                            </td>

                            <td class="typestock"><input type="text" onfocus="this.select();" name="noOfItems"
                                       onkeyup="checkCurrentStock(this)" placeholder="0.00"
                                       class="form-control number input-mini noOfItems" id="edit_noOfItems" required>
                            </td>
                            <td class="typestock"><input type="text" onfocus="this.select();" name="grossQty"
                                       onkeyup="checkCurrentStock(this),cal_deduction_netQtyEdit_buyback(this)" placeholder="0.00"
                                       class="form-control number input-mini grossQty" id="edit_grossQty" required>
                            </td>
                            <td class="typestock"><input type="text" onfocus="this.select();" name="noOfUnits"
                                       onkeyup="checkCurrentStock(this),cal_deduction_netQtyEdit_buyback(this)" placeholder="0.00"
                                       class="form-control number input-mini noOfUnits" id="edit_noOfUnits" onkeypress="cal_deduction_netQtyEdit_buyback(this)" required>
                            </td>
                            <td class="typestock"><!--<input type="text" onfocus="this.select();" name="deduction"
                                                         onkeyup="cal_deduction_netQty(this),cal_deduction_netQtyEdit_buyback()" placeholder="0.00"
                                                         class="form-control number input-mini deduction" id="edit_deduction">-->
                                <?php echo form_dropdown('deductionedit',all_bucketweight_drop(),' ', 'class="form-control deduction input-mini" onchange="cal_deduction_netQtyEdit_buyback(this)" id="edit_deduction"  required'); ?>
                            </td>
                            <td class="typestock">
                                <div class="input-group">
                                    <input type="text" name="adjustment_Stock" onfocus="this.select();"
                                           class="form-control number" id="adjustment_Stock_edit" required>
                                </div>
                            </td>

                            <td style="width: 100px" class="typewac">
                                <div class="input-group">
                                    <span
                                            class="input-group-addon input-group-addon-mini"><?php echo $this->common_data['company_data']['company_default_currency']; ?></span>
                                    <input type="text" name="adjustment_wac" id="adjustment_wac_edit"
                                           onfocus="this.select();"
                                           class="form-control number"
                                           required>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?></button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="StockAdjustment_Detail_Update()"><?php echo $this->lang->line('common_save_change'); ?>
                </button><!--Save changes-->
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="stockadjustmentSwitch" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('transaction_gl_account'); ?></h5><!--GL Account-->
            </div>
            <div class="modal-body" id="divglAccount">
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?></button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="stockadjustmentAccountUpdate(1)"><?php echo $this->lang->line('transaction_apply_to_all'); ?>
                </button><!--Apply to All-->
                <button class="btn btn-primary" type="button"
                        onclick="stockadjustmentAccountUpdate(0)"><?php echo $this->lang->line('common_save_change'); ?>
                </button><!--Save changes-->
            </div>
        </div>
    </div>
</div>

<?php
$data['documentID'] = 'SA';
$this->load->view('system/item/itemmastersub/inc-item-master-sub-config-edit', $data);
$this->load->view('system/grv/sub-views/inc-sub-item-master', $data);

?>

<script type="text/javascript">
    var search_id = 1;
    var type;
    var adjsType;
    var stockAdjustmentAutoID;
    var stockAdjustmentDetailsAutoID;
    var projectID;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/inventory/erp_stock_adjustment_management_buyback', stockAdjustmentAutoID, 'Stock Adjustment');
        });
        $('.select2').select2();
        number_validation();
        type = 'Inventory';
        projectID = null;
        stockAdjustmentAutoID = null;
        stockAdjustmentDetailsAutoID = null;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#stock_adjustment_form').bootstrapValidator('revalidateField', 'stockAdjustmentDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
        currency_validation_modal(CurrencyID, 'SA', '', '');
        if (p_id) {
            stockAdjustmentAutoID = p_id;
            laad_stock_adjustment_header();
            $("#a_link").attr("href", "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>/" + stockAdjustmentAutoID);
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            initializeitemTypeahead(type);
            initializeitemTypeahead_edit(type);
        }

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        $('#stock_adjustment_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                financeyear: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_financial_year_is_required');?>.'}}}, /*Financial Year is required*/
                financeyear_period: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_common_financial_period_is_required');?>.'}}}, /*Financial Period is required*/
                stockAdjustmentDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_date_is_required');?>.'}}}, /*Date is required*/
                //location: {validators: {notEmpty: {message: 'Location is required.'}}},
                adjustmentType: {validators: {notEmpty: {message: 'Category Type is required.'}}},
                adjsType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_adjustment_type_is_required');?>.'}}}, /*Adjustment Type is required*/
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_primary_segment_is_required');?>.'}}}, /*Primary Segment is required*/
                narration: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_narration_is_required');?>.'}}}/*Narration is required*/
            },
        }).on('success.form.bv', function (e) {
            $('#location').attr('disabled', false);
            $('#adjsType').attr('disabled', false);
            $('#adjustmentType').attr('disabled', false);
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'stockAdjustmentAutoID', 'value': stockAdjustmentAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'location_dec', 'value': $('#location option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Inventory/save_stock_adjustment_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    type = $('#adjustmentType').val();
                    adjsType = $('#adjsType').val();
                    stopLoad();
                    refreshNotifications(true);
                    $('.btn-wizard').removeClass('disabled');
                    $('#location').attr('disabled', true);
                    $('#adjsType').attr('disabled', true);
                    if (data['status']) {
                        $('#adjustmentType').attr('disabled', true);
                        stockAdjustmentAutoID = data['last_id'];
                        if(adjsType==1){
                            $('.typewac').show();
                            $('.typestock').hide();
                            $('#col_adj').attr('colspan', 3);
                            $('#adjs_no_rec').attr('colspan', 12);
                            $('#total_colspan').attr('colspan', 10);
                        }else{
                            $('.typestock').show();
                            $('.typewac').hide();
                            $('#col_adj').attr('colspan', 6);
                            $('#adjs_no_rec').attr('colspan', 15);
                            $('#total_colspan').attr('colspan', 13);
                        }
                        fetch_detail();
                        $("#a_link").attr("href", "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>/" + stockAdjustmentAutoID);
                        if ($('#adjustmentType').val() != 'Non Inventory') {
                            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + stockAdjustmentAutoID + '/SA');
                        } else {
                            $("#de_link").hide();
                        }

                   /*     setTimeout(function(){
                            if(adjsType==1){
                                $('#col_adj').attr('colspan', 3);
                            }else{
                                $('#col_adj').attr('colspan', 6);
                            }
                            alert('set');
                        }, 400);*/

                        $('[href=#step2]').tab('show');
                    }
                    $('#search').typeahead('destroy');
//                    initializeitemTypeahead(type);
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

    function cal_deduction_netQtyEdit_buyback() {
        var grossQty = parseFloat($('#edit_grossQty').val());
        var deduction = parseFloat($('#edit_deduction :selected').text());
        var edit_noOfUnits = parseFloat($('#edit_noOfUnits').val());
        if (grossQty) {
            $('#adjustment_Stock_edit').val((parseFloat(grossQty - (deduction * edit_noOfUnits)).toFixed(2)));

        }
    }

    function edit_glaccount(stockAdjustmentDetailsAutoID, PLGLAutoID, BLGLAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {PLGLAutoID: PLGLAutoID, BLGLAutoID: BLGLAutoID},
            url: "<?php echo site_url('Inventory/stockAdjustment_load_gldropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#divglAccount').html(data);
                $('#detailID').val(stockAdjustmentDetailsAutoID);
                $('#stockadjustmentSwitch').modal('show');

                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function stockadjustmentAccountUpdate(all) {
        var $form = $('#stock_adjustment_gl_form');
        var data = $form.serializeArray();
        data.push({name: "applyAll", value: all});
        data.push({name: "masterID", value: stockAdjustmentAutoID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/stockadjustmentAccountUpdate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                fetch_detail();
                $('#stockadjustmentSwitch').modal('hide');
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }
    function laad_stock_adjustment_header() {
        if (stockAdjustmentAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'stockAdjustmentAutoID': stockAdjustmentAutoID},
                url: "<?php echo site_url('Inventory/laad_stock_adjustment_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        stockAdjustmentAutoID = data['stockAdjustmentAutoID'];
                        if (type != 'Non Inventory') {
                            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + stockAdjustmentAutoID + '/SA');
                        } else {
                            $("#de_link").hide();
                        }
                        $('#adjustmentType').val(data['stockAdjustmentType']).change();
                        $('#adjsType').val(data['adjustmentType']).change();
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#stockAdjustmentDate').val(data['stockAdjustmentDate']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $("#location").val(data['wareHouseAutoID']).change();
                        $('#location').attr('disabled', true);
                        $('#narration').val(data['comment']);
                        $('#referenceNo').val(data['referenceNo']);
                        fetch_detail();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        type = data['stockAdjustmentType'];
                        adjsType = data['adjustmentType'];
                        if(adjsType==1){
                            $('.typewac').show();
                            $('.typestock').hide();
                            $('#col_adj').attr('colspan', 3);
                            $('#adjs_no_rec').attr('colspan', 12);
                            $('#total_colspan').attr('colspan', 10);
                        }else{
                            $('.typestock').show();
                            $('.typewac').hide();
                            $('#col_adj').attr('colspan', 6);
                            $('#adjs_no_rec').attr('colspan', 15);
                            $('#total_colspan').attr('colspan', 13);
                        }
                        $('#search').typeahead('destroy');
                        initializeitemTypeahead(type);
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
        if (stockAdjustmentAutoID) {
            $('.f_search').typeahead('destroy');
            $('#item_detail_form')[0].reset();
            $('#StockAdjustment_detail_add_table tbody tr').not(':first').remove();
            $('.f_search').closest('tr').css("background-color",'white');
            $('.adjustment_Stock').closest('tr').css("background-color",'white');
            $('.f_search').typeahead('val', '');
            $('.itemAutoID').val('');
            $('.a_segment ').val($('#segment').val()).change();
            //initializeitemTypeahead(type,1);
            $("#item_detail_modal").modal({backdrop: "static"});
            initializeitemTypeahead(type, 1);
            //$('#a_segment').val($('#segment').val());
        }
    }

    function fetch_detail() {
        if (stockAdjustmentAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'stockAdjustmentAutoID': stockAdjustmentAutoID},
                url: "<?php echo site_url('Inventory/fetch_stock_adjustment_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#item_table_body').empty();
                    x = 1;
                    tot = 0;
                    currency_decimal = 2;
                    if (jQuery.isEmptyObject(data)) {
                        $('#location').attr('disabled', false);
                        $('#adjsType').attr('disabled', false);
                        $('#adjustmentType').attr('disabled', false);
                        $('#item_table_body').append('<tr class="danger"><td colspan="15" id="adjs_no_rec" class="text-center"><b>No Records Found</b></td></tr>');
                    } else {
                        $('#location').attr('disabled', true);
                        $('#adjsType').attr('disabled', true);
                        $('#adjustmentType').attr('disabled', true);
                        tot_amount = 0;
                        $.each(data, function (key, value) {


                            currency_decimal = value['companyLocalCurrencyDecimalPlaces'];

                            var previousStock = value['previousStock'];
                            var currentStock = value['currentStock'];

                            if (value['isSubitemExist'] == 1 && previousStock != currentStock) {

                                var colour = '';
                                var jsSet = '';

                                if (previousStock < currentStock) {
                                    /** Like GRV */
                                    colour = 'color: #09b50f  !important';
                                    jsSet = '<a rel="tooltip" title="Sub Item Master - Add Items"  style="' + colour + '" onclick="load_itemMasterSub_modal(' + value['stockAdjustmentDetailsAutoID'] + ',\'SA\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; ';

                                }
                                else if (previousStock > currentStock) {
                                    colour = 'color: #b72922 !important';
                                    jsSet = '<a rel="tooltip" title="Sub Item Master - Deduct Items" style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['stockAdjustmentDetailsAutoID'] + ',\'SA\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; ';

                                }
                                if($('#adjsType').val()==1){
                                    var string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['previousStock'] + '</td><td class="text-right">' + value['previousWac'] + '</td><td class="text-center">' + value['currentStock'] + '</td><td class="text-right">' + value['currentWac'] + '</td> <td class="text-center">0</td><td class="text-right">' + value['adjustmentWac'] + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + jsSet + '<a onclick="edit_item(' + value['stockAdjustmentDetailsAutoID'] + ',\'' + value['itemDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_item(' + value['stockAdjustmentDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }else{
                                    var string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['previousWareHouseStock'] + '</td><td class="text-right">' + value['previousWac'] + '</td><td class="text-center">' + value['currentWareHouseStock'] + '</td><td class="text-right">' + value['currentWac'] + '</td><td class="text-right">' + value['grossQty'] + '</td> <td class="text-right">' + value['noOfUnits'] + '</td> <td class="text-right">' + value['deduction'] + '</td><td class="text-center">' + value['adjustmentWareHouseStock'] + '</td><td class="text-right">' + value['adjustmentWac'] + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + jsSet + '<a onclick="edit_item(' + value['stockAdjustmentDetailsAutoID'] + ',\'' + value['itemDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_item(' + value['stockAdjustmentDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }



                            } else {
                                if($('#adjsType').val()==1){
                                    var string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['previousStock'] + '</td><td class="text-right">' + value['previousWac'] + '</td><td class="text-center">' + value['currentStock'] + '</td><td class="text-right">' + value['currentWac'] + '</td><td class="text-center">0</td><td class="text-right">' + value['adjustmentWac'] + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_glaccount(' + value['stockAdjustmentDetailsAutoID'] + ',\'' + value['PLGLAutoID'] + '\',\'' + value['BLGLAutoID'] + '\');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['stockAdjustmentDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_item(' + value['stockAdjustmentDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }else{
                                    var string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + value['previousWareHouseStock'] + '</td><td class="text-right">' + value['previousWac'] + '</td><td class="text-center">' + value['currentWareHouseStock'] + '</td><td class="text-right">' + value['currentWac'] + '</td><td class="text-right">' + value['grossQty'] + '</td> <td class="text-right">' + value['noOfUnits'] + '</td> <td class="text-right">' + value['deduction'] + '</td><td class="text-center">' + value['adjustmentWareHouseStock'] + '</td><td class="text-right">' + value['adjustmentWac'] + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a onclick="edit_glaccount(' + value['stockAdjustmentDetailsAutoID'] + ',\'' + value['PLGLAutoID'] + '\',\'' + value['BLGLAutoID'] + '\');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['stockAdjustmentDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_item(' + value['stockAdjustmentDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }
                            }


                            $('#item_table_body').append(string);
                            x++;
                            tot += parseFloat(value['totalValue']);

                        });
                    }
                    $('#tot').html((tot).formatMoney(currency_decimal, '.', ','));
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
        ;
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

    function initializeitemTypeahead(type, id) {

        /**var inv_item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Inventory/fetch_inv_item/?q=%QUERY&t=" + type
        });*/

        //inv_item.initialize();
        /**$('#f_search_' + id).typeahead(null,{
            minLength: 1,
            highlight: false,
            displayKey: 'Match',
            source: inv_item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            alert(id);
            $(this).closest('tr').find('.itemAutoID').val(datum.itemAutoID);
            $(this).closest('tr').find('.currentStock').val(datum.currentStock);
            $(this).closest('tr').find('.d_uom').val(datum.defaultUnitOfMeasure);
            fetch_related_uom_id(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID, this);
            $(this).closest('tr').find('.umoDropdown').prop("disabled", true);
            fetch_warehouse_item(datum.itemAutoID, this);
            return false;
        });*/

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Inventory/fetch_inv_item_stock_adjustment/?&t=' + encodeURIComponent(type),
            onSelect: function (suggestion) {
                //$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                var cont = true;
                $('.itemAutoID').each(function () {
                    if (this.value) {
                        if (this.value == suggestion.itemAutoID) {
                            $('#f_search_' + id).val('');
                            $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                            $('#f_search_' + id).closest('tr').find('.currentWareHouseStock').val('');
                            $('#f_search_' + id).closest('tr').find('.currentWac').val('');
                            $('#f_search_' + id).closest('tr').find('.adjustment_wac').val('');
                            $('#f_search_' + id).closest('tr').find('.umoDropdown').val('');
                            $('#f_search_' + id).closest('tr').find('.currentStock').val('');
                            myAlert('w', 'Selected item is already selected');
                            cont = false;
                        }
                    }
                });
                if($('#adjsType').val()==0){
                    if(suggestion.companyLocalWacAmount < 0){
                        $('#f_search_' + id).val('');
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                        $('#f_search_' + id).closest('tr').find('.currentWareHouseStock').val('');
                        $('#f_search_' + id).closest('tr').find('.currentWac').val('');
                        $('#f_search_' + id).closest('tr').find('.adjustment_wac').val('');
                        $('#f_search_' + id).closest('tr').find('.umoDropdown').val('');
                        $('#f_search_' + id).closest('tr').find('.currentStock').val('');
                        myAlert('w', 'Wac less than zero');
                        cont = false;
                    }
                }
                if(cont) {
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    }, 200);
                    $(this).closest('tr').find('.currentStock').val(suggestion.currentStock);
                    if($('#adjsType').val()==1){
                        $(this).closest('tr').find('.currentWareHouseStock').val(suggestion.currentStock);
                    }
                    $(this).closest('tr').find('.d_uom').val(suggestion.defaultUnitOfMeasure);
                    fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                    $(this).closest('tr').find('.umoDropdown').prop("disabled", true);
                    fetch_warehouse_item(suggestion.itemAutoID, this);
                    $(this).closest('tr').find('.adjustment_Stock').focus();
                    $(this).closest('tr').css("background-color", 'white');
                    return false;
                }
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');
    }


    function initializeitemTypeahead_edit(type) {
        /** var inv_item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Inventory/fetch_inv_item/?q=%QUERY&t=" + type
        });

         inv_item.initialize();
         $('#search').typeahead(null, {
            minLength: 1,
            highlight: false,
            displayKey: 'Match',
            source: inv_item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $('#itemAutoID_edit').val(datum.itemAutoID);
            $('#currentStock_edit').val(datum.currentStock);
            $('#d_uom_edit').val(datum.defaultUnitOfMeasure);
            fetch_related_uom_id_edit(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID);
            $('#UnitOfMeasureID_edit').prop("disabled", true);
            fetch_warehouse_item_edit(datum.itemAutoID);
            return false;
        });*/

        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Inventory/fetch_inv_item_stock_adjustment/?&t=' + type,
            onSelect: function (suggestion) {

                setTimeout(function () {
                    $('#itemAutoID_edit').val(suggestion.itemAutoID);
                    $('#currentStock_edit').val(suggestion.currentStock);
                    $('#d_uom_edit').val(suggestion.defaultUnitOfMeasure);
                    if($('#adjsType').val()==1){
                        $('#currentWareHouseStock_edit').val(suggestion.currentStock);
                    }
                }, 200);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                $('#UnitOfMeasureID_edit').prop("disabled", true);
                $('#adjustment_Stock_edit').focus();
                fetch_warehouse_item_edit(suggestion.itemAutoID);
                return false;
            }
        });
        $('#search').off('focus.autocomplete');
    }

    function fetch_warehouse_item(itemAutoID, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'stockAdjustmentAutoID': stockAdjustmentAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Inventory/fetch_warehouse_item_adjustment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    //$(element).closest('tr').find('.currentWareHouseStock').val(data['currentStock']);
                    if($('#adjsType').val()==0){
                        $(element).closest('tr').find('.currentWareHouseStock').val(data['currentStock']);
                    }
                    $(element).closest('tr').find('.currentWac').val(data['currentWac']);
                    $(element).closest('tr').find('.adjustment_wac').val(data['currentWac']);
                } else {
                    $(element).typeahead('val', '');
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

    function fetch_warehouse_item_edit(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'stockAdjustmentAutoID': stockAdjustmentAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Inventory/fetch_warehouse_item_adjustment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $('#currentWac_edit').val(data['currentWac']);
                    $('#adjustment_wac_edit').val(data['currentWac']);
                    //$('#currentWareHouseStock_edit').val(data['currentStock']);
                    if($('#adjsType').val()==0){
                        $('#currentWareHouseStock_edit').val(data['currentStock']);
                    }
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
    function checkCurrentStock(det) {
        var currentStock = $(det).closest('tr').find('.currentstock').val();
        if (det.value > parseFloat(currentStock)) {
            myAlert('w', 'Transfer quantity should be less than or equal to current stock');
            $(det).val(0);
        }

        if(det.value > 0)
        {
            $(det).closest('tr').css("background-color",'white');
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
                $('#UnitOfMeasureID_edit').empty();
                var mySelect = $('#UnitOfMeasureID_edit');
                mySelect.append($('<option></option>').val('').html('Select UOM'));
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                    });
                    if (select_value) {
                        $('#UnitOfMeasureID_edit').val(select_value);
                    }
                }
            }, error: function () {
                swal("Cancelled", "Your " + value + " file is safe :)", "error");
            }
        });
    }

    function load_conformation() {
        if (stockAdjustmentAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'stockAdjustmentAutoID': stockAdjustmentAutoID, 'html': true},
                url: "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + stockAdjustmentAutoID + '/SA');
                    $("#a_link").attr("href", "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>/" + stockAdjustmentAutoID);
                    attachment_modal_stockAdjustment(stockAdjustmentAutoID, "<?php echo $this->lang->line('transaction_stock_adjustment');?>", "SA");
                    /*Stock Adjustment*/
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
        if (stockAdjustmentAutoID) {
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
                        data: {'stockAdjustmentAutoID': stockAdjustmentAutoID},
                        url: "<?php echo site_url('Inventory/stock_adjustment_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            }else if(data['error'] == 2)
                            {
                                myAlert('w', data['message']);
                            }
                            else {
                                myAlert('s', data['message']);
                                //refreshNotifications(true);
                                fetchPage('system/inventory/erp_stock_adjustment_management_buyback', stockAdjustmentAutoID, 'Stock Adjustment');
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
        if (stockAdjustmentAutoID) {
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
                    fetchPage('system/inventory/stock_agistment_management', stockAdjustmentAutoID, 'Stock Adjustment');
                });
        }
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
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('transaction_select_finance_period');?>'));
                /*Select Finance Period*/
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

    function delete_item(id) {
        if (stockAdjustmentAutoID) {
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
                        data: {'stockAdjustmentDetailsAutoID': id},
                        url: "<?php echo site_url('Inventory/delete_adjustment_item'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            fetch_detail();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_item(id) {
        if (stockAdjustmentAutoID) {
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
                    var location = $('#location').val();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'stockAdjustmentDetailsAutoID': id, location: location},
                        url: "<?php echo site_url('Inventory/load_adjustment_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            var adjustment_Stock_edit =parseFloat(data['adjustmentStock'])+parseFloat(data['previousWareHouseStock']);
                            $('#search').typeahead('destroy');
                            stockAdjustmentDetailsAutoID = data['stockAdjustmentDetailsAutoID'];
                            projectID = data['projectID'];
                            $('#search').val(data['itemDescription'] + " (" + data['itemSystemCode'] + ")");
                            $('#itemAutoID_edit').val(data.itemAutoID);
                            $('#currentStock_edit').val(data.currentStock);
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['defaultUOMID']);
                            $('#segment_edit').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            $('#UnitOfMeasureID_edit').prop("disabled", true);
                            if($('#adjsType').val()==1){
                                $('#currentWareHouseStock_edit').val(data['itemcurrentStock']);
                            }else{
                                $('#currentWareHouseStock_edit').val(data['wareHouseStock']);
                            }

                            $('#edit_noOfItems').val(data['noOfItems']);
                            $('#edit_grossQty').val(data['grossQty']);
                            $('#edit_noOfUnits').val(data['noOfUnits']);
                            $('#currentWac_edit').val(data['LocalWacAmount']);
                            $('#edit_deduction').val(data['deduction']);
                            $('#adjustment_Stock_edit').val(adjustment_Stock_edit);
                            $('#adjustment_wac_edit').val(data['currentWacstock']);
                            $('#edit_deduction').val(data['bucketWeightID']).change();
                            initializeitemTypeahead_edit(type);
                            $("#item_detail_modal_edit").modal({backdrop: "static"});
                            if(adjsType==1){
                                $('.typewac').show();
                                $('.typestock').hide();
                            }else{
                                $('.typestock').show();
                                $('.typewac').hide();
                            }
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function referback_stock_adjustment(id) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_refer_back');?>", /*You want to refer back!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>", /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'stockTransferAutoID': id},
                    url: "<?php echo site_url('Inventory/referback_stock_transfer'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        stock_transfer_table();
                        stopLoad();

                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function attachment_modal_stockAdjustment(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#stockAdjustment_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");
                    <!--Attachments-->
                    $('#stockAdjustment_attachment').empty();

                    $('#stockAdjustment_attachment').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_stockAdjustment_attachement(stockAdjustmentAutoID, DocumentSystemCode, myFileName) {
        if (stockAdjustmentAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>", /*You want to delete this attachment file!*/
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
                        data: {'attachmentID': stockAdjustmentAutoID, 'myFileName': myFileName},
                        url: "<?php echo site_url('Inventory/delete_stockAdjustment_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');
                                /*Deleted Successfully*/
                                attachment_modal_stockAdjustment(DocumentSystemCode, "Stock Adjustment", "SA");
                            } else {
                                myAlert('e', '<?php echo $this->lang->line('common_deletion_failed');?>');
                                /*Deletion Failed*/
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function add_more_stock_adjustment() {
        /* if(search_id==1) {
         $('#f_search_1').typeahead('destroy');
         }*/
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#StockAdjustment_detail_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');


        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#StockAdjustment_detail_add_table').append(appendData);
        var lenght = $('#StockAdjustment_detail_add_table tbody tr').length - 1;
        $('#f_search_'+ search_id).closest('tr').css("background-color",'white');
        $(".select2").select2();
        initializeitemTypeahead(type, search_id);
        $('#f_search_' + search_id).closest('tr').find('.a_segment').val($('#segment').val()).change();
        /* if(search_id==2){
         setTimeout(function(){
         initializeitemTypeahead(type,1);
         }, 500);

         }*/

        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function StockAdjustment_Detailadd() {
        $(".umoDropdown").prop("disabled", false);
        var $form = $('#item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'stockAdjustmentAutoID', 'value': stockAdjustmentAutoID});
        data.push({'name': 'stockAdjustmentDetailsAutoID', 'value': stockAdjustmentDetailsAutoID});
        $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });

        if(adjsType==1){
            $('.adjustment_wac').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2');
                }
            });
        }else{
            $('.adjustment_Stock').each(function () {
                if (this.value == '') {
                    $(this).closest('tr').css("background-color", '#ffb2b2');
                }
            });
        }
        $('select[name="deduction[]"] option:selected').each(function () {
            data.push({'name': 'deductionvalue[]', 'value': $(this).text()})
        })

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/save_stock_adjustment_detail_multiple_buyback'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['error'] == 2) {
                    myAlert('e', data['message']);
                    clearStockadjustmentItemDetail();
                } else {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        stockAdjustmentDetailsAutoID = null;
                        $('#item_detail_form')[0].reset();
                        setTimeout(function () {
                            fetch_detail(4);
                            $('#item_detail_modal').modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        }, 300);
                    }
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function clearStockadjustmentItemDetail() {
        $("#item_detail_form").closest('form').find("input[type=text], textarea").val("");
        $(".itemAutoID").val("");
        $(".currentStock").val("");
        initializeitemTypeahead(type);
    }


    function StockAdjustment_Detail_Update() {
        $('#UnitOfMeasureID_edit').prop("disabled", false);
        var $form = $('#edit_item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'stockAdjustmentAutoID', 'value': stockAdjustmentAutoID});
        data.push({'name': 'stockAdjustmentDetailsAutoID', 'value': stockAdjustmentDetailsAutoID});
        data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
        $('select[name="deductionedit"] option:selected').each(function () {
            data.push({'name': 'deductionvalue', 'value': $(this).text()})
        })
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/save_stock_adjustment_detail_buyback'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    stockAdjustmentDetailsAutoID = null;
                    $('#edit_item_detail_form')[0].reset();
                    setTimeout(function () {
                        fetch_detail(4);
                        $('#item_detail_modal_edit').modal('hide');
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

    function load_segmentBase_projectID_item(segment) {
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

    function load_segmentBase_projectID_itemEdit(segment) {
        var type = 'item';
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
                $('#edit_div_projectID_item').html(data);
                $('.select2').select2();
                if (projectID) {
                    $("#projectID_item").val(projectID).change()
                }
                stopLoad();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
            }
        });
    }
    function validatetb_row(det) {
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }
    $('#adjsType').change(function () {
        $('#type').val($(this).val());
    });
    function cal_deduction_netQty(element) {
        var grossQty = parseFloat($(element).closest('tr').find('.grossQty').val());
        var deduction = parseFloat($(element).closest('tr').find('.deduction :selected').text());
        var noOfUnits = parseFloat($(element).closest('tr').find('.noOfUnits').val());
        if (grossQty) {
            $(element).closest('tr').find('.adjustment_Stock').val((parseFloat(grossQty - (noOfUnits * deduction)).toFixed(2)));

        }
    }




    /*function checkCurrentStock(det){
     var currentStock= $(det).closest('tr').find('.currentWareHouseStock').val();
     if(det.value > parseFloat(currentStock)){
     myAlert('w','Adjustment stock should be less than or equal to current stock');
     $(det).val(0);
     }
     }*/

    /*function checkCurrentStockEdit(){
     var currentStock=$('#currentWareHouseStock_edit').val();
     var adjestmentStock=$('#adjustment_Stock_edit').val();
     if(parseFloat(adjestmentStock) > parseFloat(currentStock)){
     myAlert('w','Adjustment stock should be less than or equal to current stock');
     $('#adjustment_Stock_edit').val(0);
     }
     }*/
</script>