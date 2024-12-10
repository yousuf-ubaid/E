<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$umo_arr = array('' => 'Select UOM');//all_umo_drop();
$location_arr_to = all_delivery_location_drop_active();
$location_arr_from = all_delivery_location_drop_active();
$financeyear_arr = all_financeyear_drop(true);
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();
$projectExist = project_is_exist();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$jobNumberMandatory = getPolicyValues('JNP', 'All');
$itemBatch = getPolicyValues('IB', 'All');
$jobs = all_mfq_jobs_drop(TRUE);
$pID = $this->input->post('page_id');
if($pID != '') {
    $Documentid = 'ST';
    $warehouseidcurrentdoc = all_warehouse_drop_isactive_inactive($pID,$Documentid);
    if(!empty($warehouseidcurrentdoc) && $warehouseidcurrentdoc['isActive'] == 0)
    {
        $location_arr_from[trim($warehouseidcurrentdoc['wareHouseAutoID'] ?? '')] = trim($warehouseidcurrentdoc['wareHouseCode'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseLocation'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseDescription'] ?? '');
       
    }
    $Documentid = 'ST2';
    $warehouseidcurrentdoc = all_warehouse_drop_isactive_inactive($pID,$Documentid);
    if(!empty($warehouseidcurrentdoc) && $warehouseidcurrentdoc['isActive'] == 0)
    {
        $location_arr_to[trim($warehouseidcurrentdoc['wareHouseAutoID'] ?? '')] = trim($warehouseidcurrentdoc['wareHouseCode'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseLocation'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseDescription'] ?? '');
        
    }
}
$advanceCostCapturing = getPolicyValues('ACC', 'All');
$activityCode_arr = get_activity_codes();
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .pulling-based-li{
        background: #547698;
    }

    .pulling-based-li > a{
        color: #ffffff !important;
    }

    .nav>li.pull-li>a:hover{
        color: #444 !important;
        cursor: pointer;
        background: #d4d3d3 !important
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one');?> - <?php echo $this->lang->line('transaction_stock_transfer_header');?> </span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_detail();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_two');?> - <?php echo $this->lang->line('transaction_stock_transfer_detail');?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_three');?> - <?php echo $this->lang->line('transaction_stock_transfer_confirmation');?></span>
            </a>
           
        </div>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="stock_transfer_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="itemType"><?php echo $this->lang->line('transaction_stock_transfer_transfer_type');?> <?php required_mark(); ?></label><!--Item Type-->
                <?php echo form_dropdown('transferType', array('' =>  $this->lang->line('common_select_type')/*'Select Type'*/, 'standard' => $this->lang->line('transaction_direct'), 'materialRequest' => $this->lang->line('transaction_material_request')), 'standard', 'class="form-control select2" id="transferType" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="itemType"><?php echo $this->lang->line('transaction_item_type');?> <?php required_mark(); ?></label><!--Item Type-->
                <?php echo form_dropdown('itemType', array('' =>  $this->lang->line('common_select_type')/*'Select Type'*/, 'Inventory' => $this->lang->line('transaction_inventory')/*'Inventory'*/, 'Non Inventory' => $this->lang->line('transaction_non_inventory')/*'Non Inventory'*/), 'Inventory', 'class="form-control select2" id="itemType" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('transaction_primary_segment');?> <?php required_mark(); ?></label><!--Primary Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('transaction_transfer_date');?> <?php required_mark(); ?></label><!--Transfer Date-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="tranferDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="tranferDate"
                           class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('transaction_from_location');?> <?php required_mark(); ?></label><!--From Location-->
                <div id="div_fromwarehouse_drop">
                    <?php echo form_dropdown('form_location', $location_arr_from, '', 'class="form-control select2" id="form_location" required'); ?>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('transaction_to_location');?> <?php required_mark(); ?></label><!--To Location-->
                <input class="hidden" id="mfqWarehouseAutoID" name="mfqWarehouseAutoID">
                <div id="div_towarehouse_drop">
                    <?php echo form_dropdown('to_location', $location_arr_to, '', 'class="form-control select2" id="to_location" onchange="validate_mfq_warehouse(this.value)" required'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('transaction_common_referenc_no');?></label><!--Reference No-->
                <input type="text" class="form-control " id="referenceNo" name="referenceNo">
            </div>
            <?php
            if($financeyearperiodYN==1){
            ?>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('transaction_common_financial_year');?> <?php required_mark(); ?></label><!--Financial Year-->
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="financeyear_period"><?php echo $this->lang->line('transaction_common_financial_period');?> <?php required_mark(); ?></label><!--Financial Period-->
                <?php echo form_dropdown('financeyear_period', array('' => 'Finance Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
            <?php } ?>
            <?php
            if($jobNumberMandatory == 1){
                ?>
                <div class="form-group col-sm-4" id="jobNumberAdd">
                    <label for="financeyear"><?php echo $this->lang->line('inventory_job_number');?> <?php required_mark(); ?></label>
                    <!-- <div class="div_job_number">
                        <select name="jobID" id="jobID" onchange="fetchReference_number()" class="form-control select2">
                            <option value="">
                                Select Job Number</option>
                        </select>
                    </div> -->
                  <div class="div_job_number">
                        <?php echo form_dropdown('jobID', $jobs, '', 'class="form-control select2" id="jobID"'); ?>
                  </div>
                </div>
            <?php } ?>
        <!--</div>
        <div class="row">-->
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('transaction_common_narration');?> </label><!--Narration-->
                <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?> </button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('transaction_common_add_item_detail');?> </h4></div><!--Add Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item');?>
                </button><!--Add Item-->
                <button type="button" onclick="edit_all_item_detail_modal()" style="margin-right:8px;" id="editallbtn" class="btn btn-default hidden pull-right"><span class="glyphicon glyphicon-pencil"></span> Edit All</button>
            </div>
        </div>
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%" class="mrBaseDiveTable ">MR Code<!--MR Code--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code');?></th><!--Item Code-->
                <?php if($itemBatch == 1){ ?>
                    <th>Batch Number </th><!--Code-->
                <?php } ?>
                <th style="min-width: 20%"><?php echo $this->lang->line('transaction_common_item_description');?></th><!--Item Description-->
                <th style="min-width: 20%">Bin Location</th>
                <?php if($advanceCostCapturing == 1){ ?>
                    <th id="activityCode_header" style="min-width: 10%">Activity Code</th>
                <?php } ?>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_uom');?></th><!--UOM-->
                <th style="min-width: 5%"><?php echo $this->lang->line('common_qty');?></th><!--QTY-->
                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac');?></th><!--WAC-->
                <!--<th style="min-width: 10%">Received</th>-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_total_value');?>
                    (<?php echo $this->common_data['company_data']['company_default_currency']; ?>)<!--Value-->
                </th>
                <th style="min-width: 10%">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="item_table_body">
            <tr class="danger">
                <td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td><!--No Records Found-->
            </tr>
            </tbody>
            <tfoot id="item_table_tfoot">

            </tfoot>
        </table>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev" onclick=""><?php echo $this->lang->line('common_previous');?> </button><!--Previous-->
            <!-- <button class="btn btn-primary next" onclick="load_conformation();" >Save & Next</button> -->
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <!--      <div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank" href="<?php /*echo site_url('Double_entry/fetch_double_stock_transfer/'); */ ?>"><span class="glyphicon glyphicon-random" aria-hidden="true"></span>  &nbsp;&nbsp;&nbsp;Account Review entries
                </a>
                <a class="btn btn-default btn-sm" id="a_link" target="_blank" href="<?php /*echo site_url('Inventory/load_stock_transfer_conformation/'); */ ?>">
                    <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                </a>
                </span>
            </div>
        </div><hr>-->
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title" id="stockTransfer_attachment_label"><?php echo $this->lang->line('transaction_goods_received_voucher_modal_title');?> </h4><!--Modal title-->
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name');?></th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description');?></th><!--Description-->
                        <th><?php echo $this->lang->line('common_type');?></th><!--Type-->
                        <th><?php echo $this->lang->line('common_action');?></th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="stockTransfer_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?></td><!--No Attachment Found-->
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?></button><!--Previous-->
            <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?></button><!--Save as Draft-->
            <button class="btn btn-success submitWizard" onclick="confirmation()"><?php echo $this->lang->line('common_confirm');?></button><!--Confirm-->
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<div aria-hidden="true" role="dialog" id="item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('transaction_add_item_transfer');?></h5><!--Add Item Transfer-->
            </div>
            <div class="modal-body">
                <form role="form" id="item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="StockTransfer_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 200px;"><?php echo $this->lang->line('transaction_common_item_code');?> <?php required_mark(); ?></th><!--Item Code-->
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom');?> <?php required_mark(); ?></th><!--UOM-->
                            <?php if($itemBatch == 1){ ?>
                                <th>Batch Number <?php required_mark(); ?></th><!--Code-->
                            <?php } ?>
                            <th style="width:150px;"><?php echo $this->lang->line('transaction_current_stock');?> <?php required_mark(); ?></th><!--Current Stock-->
                            <th style="width:80px;"><abbr title="Park Qty">Park Qty</abbr></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></th><!--Segment-->
                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="width: 100px">Activity Code</th>
                            <?php } ?>
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project');?></th><!--Project-->
                                <th>Project Category</th>
                                <th>Project Subcategory</th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_transfer_qty');?> <?php required_mark(); ?></th><!--Transfer Qty-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_material_issue()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoID(event,this)" class="form-control search f_search"
                                       name="search[]"
                                       placeholder="<?php echo $this->lang->line('common_item_id');?>,<?php echo $this->lang->line('common_item_description');?>..." id="f_search_1"><!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                <input type="hidden" class="form-control currentStock" name="currentStock[]">
                                <input type="hidden" class="form-control itemcatergory" name="itemcatergory">
                            </td>
                            <td>
                                <?php echo form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown" onchange="convertPrice(this)"   required'); ?>
                                <input type="hidden" class="form-control conversionRate" name="conversionRate">
                            </td>
                            <?php if($itemBatch == 1){ ?>
                            <td>
                               <?php echo form_dropdown('batch_number[0][]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_1" multiple="multiple" required'); ?>
                            </td>
                            <?php } ?>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="currentWareHouseStockQty[]"
                                           onchange="validatetb_row(this)"
                                           class="form-control currentWareHouseStock" required readonly>
                                        
                                    <input type="hidden" name="currentstock_pulleddocument[]"
                                           class="form-control currentstock_pulleddocument" readonly>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="parkQty[]" class="form-control parkQty" required readonly>
                                </div>
                            </td>
                            <td>
                                <?php echo form_dropdown('a_segment[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment" required onchange="load_segmentBase_projectID_item(this)"'); ?>
                            </td>
                            <?php if($advanceCostCapturing == 1){ ?>
                                <td>
                                    <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                </td>
                            <?php } ?>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div class="div_projectID_income">
                                        <select name="projectID"  class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('common_select_project');?></option><!--Select Project-->
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
                            <td style="width: 100px">
                                <input type="text" name="transfer_QTY[]" onfocus="this.select();" onkeyup="checkCurrentStock(this)" onchange="checkCurrentStock_unapproveddocument(this)" class="form-control number transferqty"
                                       required>
                                <input type="hidden" name="currentstock_pulleddocument[]"
                                           class="form-control currentstock_pulleddocument" readonly>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                <button class="btn btn-primary" type="button" onclick="saveStockTransfer_addItem()"><?php echo $this->lang->line('common_save_change');?>
                </button><!--Save changes-->
            </div>

        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="item_edit_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('transaction_edit_item_transfer');?></h4><!--Edit Item Transfer-->
            </div>
            <div class="modal-body">
                <form role="form" id="edit_item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="StockTransfer_detail_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;"><?php echo $this->lang->line('transaction_common_item_code');?> <?php required_mark(); ?></th><!--Item Code-->
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_common_uom');?> <?php required_mark(); ?></th><!--UOM-->
                            <?php if($itemBatch == 1){ ?>
                                <th>Batch Number <?php required_mark(); ?></th><!--Code-->
                            <?php } ?>
                            <th style="width:200px;"><?php echo $this->lang->line('transaction_current_stock');?> <?php required_mark(); ?></th><!--Current Stock-->
                            <th style="width:100px;">Park Qty</th>
                            <th style="width: 150px;" class="mitypecoloumn"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></th><!--Segment-->
                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="width: 100px">Activity Code</th>
                                <?php } ?>
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project');?>  </th><!--Project-->
                                <th>Project Category</th>
                                <th>Project Subcategory</th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_transfer_qty');?> <?php required_mark(); ?></th><!--Transfer Qty-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" class="form-control" id="search"
                                       name="search"
                                       placeholder="Item ID, Item Description...">
                                <input type="hidden" class="form-control" id="itemAutoID_edit" name="itemAutoID">
                                <input type="hidden" class="form-control" id="currentStock_edit" name="currentStock">
                                <input type="hidden" class="form-control" id="itemcatergory_Edit" name="itemcatergory_Edit">
                            </td>
                            <td>
                                <input type="hidden" name="conversionRateEdit" id="conversionRateEdit"> 
                                <?php echo form_dropdown('unitOfMeasureID', $umo_arr, '', 'class="form-control select2" id="UnitOfMeasureID_edit" onchange="convertPrice_edit(this)"   required'); ?>
                            </td>
                            <?php if($itemBatch == 1){ ?>
                            <td>
                               <?php echo form_dropdown('batch_number[]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_edit" multiple="multiple" required'); ?>

                               
                            </td>
                            <?php } ?>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="currentWareHouseStockQty"
                                           id="currentWareHouseStockQty_edit"
                                           class="form-control" required readonly>

                                    <input type="hidden" name="pulledcurrentstock_edit"
                                           class="form-control" id="pulledcurrentstock_edit" required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="parkQty_edit"
                                           id="parkQty_edit"
                                           class="form-control" required readonly>
                                </div>
                            </td>
                            <td class="mitypecoloumn">
                                <?php echo form_dropdown('a_segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment_edit" required onchange="load_segmentBase_projectID_itemEdit(this)"'); ?>
                            </td>
                            <?php if($advanceCostCapturing == 1){ ?>
                                <td>
                                    <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                </td>
                            <?php } ?>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_item">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option value=""><?php echo $this->lang->line('transaction_goods_received_select_project');?> </option><!--Select Project-->
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
                            <td style="width: 100px">
                                <input type="text" name="transfer_QTY" onfocus="this.select();" onkeyup="checkCurrentStockEdit(this)" 
                                onchange="checkCurrentStockEditunapproveddocument(this)"
                                class="form-control number" id="transferqty_edit"
                                       required>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                <button class="btn btn-primary" type="button" onclick="updateStockTransfer_addItem()"><?php echo $this->lang->line('common_update_changes');?>
                </button><!--Update changes-->
            </div>

        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" id="all_item_edit_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('transaction_edit_item_transfer');?></h4><!--Edit Item Transfer-->
            </div>
            <div class="modal-body">
                <form role="form" id="edit_all_item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="StockTransfer_detail_all_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;"><?php echo $this->lang->line('transaction_common_item_code');?> <?php required_mark(); ?></th><!--Item Code-->
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_common_uom');?> <?php required_mark(); ?></th><!--UOM-->
                            <?php if($itemBatch == 1){ ?>
                                <th>Batch Number <?php required_mark(); ?></th><!--Code-->
                            <?php } ?>
                            <th style="width:200px;"><?php echo $this->lang->line('transaction_current_stock');?> <?php required_mark(); ?></th><!--Current Stock-->
                            <th style="width: 150px;"><?php echo $this->lang->line('common_segment');?> <?php required_mark(); ?></th><!--Segment-->
                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="width: 100px">Activity Code</th>
                            <?php } ?>
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project');?>  </th><!--Project-->
                                <th>Project Category</th>
                                <th>Project Subcategory</th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_transfer_qty');?> <?php required_mark(); ?></th><!--Transfer Qty-->
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_edit_material_issue()"><i
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
                <button class="btn btn-primary" type="button" onclick="updateStockTransfer_edit_all_Item()"><?php echo $this->lang->line('common_update_changes');?>
                </button><!--Update changes-->
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="mr_base_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Material Request Base</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h5>Material Issue</h5>
                            </div>
                            <div class="box-footer no-padding">
                                <ul class="nav nav-stacked" id="mrcode">


                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th class="text-left">Description</th>
                                <th>UOM</th>
                                <th>Requested Qty</th>
                                <th>Issued Qty</th>
                                <th>Balance Qty</th>
                                <th>Current Qty</th>
                                <th>Qty</th>
                            </tr>
                            </thead>
                            <tbody id="table_body_mr_detail">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="mrbasebtn" class="btn btn-primary" onclick="save_mr_base_ST_items()">Save
                    changes
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="access_denied_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="document" style="width:55%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" style="color: red;" id="title_generate_exceed">You cannot use this Item. This item has been pulled for following docuemnts.</h5>
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
<?php
$data['documentID'] = 'ST';
$this->load->view('system/item/itemmastersub/inc-item-master-sub-config-edit', $data);
?>
<script type="text/javascript">
    var search_id = 1;
    var type;
    var stockTransferAutoID;
    var stockTransferDetailsID;
    var transferType;
    var projectID;
    var referenceNo = null;
    var wareHouseAutoID = null;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/inventory/stock_transfer_management', stockTransferAutoID, 'Stock Transfer');
        });
        $('.select2').select2();
        number_validation();
        type = 'Inventory';
        stockTransferAutoID = null;
        stockTransferDetailsID = null;
        projectID = null;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#stock_transfer_form').bootstrapValidator('revalidateField', 'tranferDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            stockTransferAutoID = p_id;
            load_stock_transfer_header();
            $("#a_link").attr("href", "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>/" + stockTransferAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + stockTransferAutoID + '/ST');
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            initializeitemTypeahead(type);
            initializeitemTypeahead_edit(type);
            CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
            currency_validation_modal(CurrencyID, 'ST', '', '');
        }

        FinanceYearID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinanceYearID'])); ?>;
        DateFrom = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateFrom'])); ?>;
        DateTo = <?php echo json_encode(trim($this->common_data['company_data']['FYPeriodDateTo'])); ?>;
        periodID = <?php echo json_encode(trim($this->common_data['company_data']['companyFinancePeriodID'])); ?>;
        fetch_finance_year_period(FinanceYearID, periodID);

        $('#stock_transfer_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                transferType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_transfer_type_is_required');?>.'}}},/*Transfer Type is required*/
                tranferDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_goods_received_grv_date_is_required');?>.'}}},/*GRV Date is required*/
                form_location: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_from_location_is_required');?>.'}}},/*From Location is required*/
                to_location: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_to_location_is_required');?>.'}}},/*To Location is required*/
                itemType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_item_type_is_required');?>.'}}},/*Item Type is required*/
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_primary_segment_is_required');?>.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#form_location").prop("disabled", false);
            $("#to_location").prop("disabled", false);
            $("#itemType").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'stockTransferAutoID', 'value': stockTransferAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'form_location_dec', 'value': $('#form_location option:selected').text()});
            data.push({'name': 'to_location_dec', 'value': $('#to_location option:selected').text()});
            data.push({'name': 'jobNumber', 'value': $('#jobID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Inventory/save_stock_transfer_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        stockTransferAutoID = data['last_id'];
                        transferType = $('#transferType').val();
                        wareHouseAutoID = $('#form_location').val();
                        fetch_detail();
                        $("#a_link").attr("href", "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>/" + stockTransferAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + stockTransferAutoID + '/ST');
                        $('[href=#step2]').tab('show');
                    }
                    ;
                    stopLoad();
                    type = $('#itemType').val();
                    /*$('#search').typeahead('destroy');
                     initializeitemTypeahead(type);*/
                }, error: function () {
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

    function load_stock_transfer_header() {
        if (stockTransferAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'stockTransferAutoID': stockTransferAutoID},
                url: "<?php echo site_url('Inventory/laad_stock_transfer_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        stockTransferAutoID = data['stockTransferAutoID'];
                        transferType = data['transferType'];
                        $('#tranferDate').val(data['tranferDate']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $("#form_location").val(data['from_wareHouseAutoID']).change();
                        $('#to_location').val(data['to_wareHouseAutoID']).change();
                        $('#narration').val(data['comment']);
                        $('#referenceNo').val(data['referenceNo']);
                        referenceNo = data['referenceNo'];
                        $('#transferType').val(data['transferType']).change();
                        $('#itemType').val(data['itemType']).change();
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#mfqWarehouseAutoID').val(data['mfqWarehouseAutoID']);
                        setTimeout(function () {
                            $('#jobID').val(data['jobID']).change();
                        }, 1500);
                        wareHouseAutoID = data['from_wareHouseAutoID'];
                        type = data['itemType'];
                        $('.search').typeahead('destroy');
                        initializeitemTypeahead(type);
                        fetch_detail();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function item_detail_modal() 
    {
        var mfqWarehouse = $('#mfqWarehouseAutoID').val();
        var jobID = $('#jobID').val();
        if(mfqWarehouse != '' && jobID == '') {
            myAlert('e', 'Please Assign Job Before adding Details!');
        } else {
            if (stockTransferAutoID) {
                if (transferType == 'materialRequest') {
                    load_MR_codes();
                    $("#mr_base_modal").modal({backdrop: "static"});
                } else {
                    $('.search').typeahead('destroy');
                    $('#item_detail_form')[0].reset();
                    $('#StockTransfer_detail_add_table tbody tr').not(':first').remove();
                    $('.f_search').closest('tr').css("background-color", 'white');
                    $('.transferqty').closest('tr').css("background-color", 'white');
                    $('.currentWareHouseStock').closest('tr').css("background-color", 'white');
                    $('.search').text('');
                    $('.itemAutoID').val('');
                    initializeitemTypeahead(type, 1);
                    $("#item_detail_modal").modal({backdrop: "static"});
                }
            }
        }
 
    }

    function load_MR_codes() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'stockTransferAutoID': stockTransferAutoID},
            url: "<?php echo site_url('Inventory/fetch_MR_code_ST'); ?>",
            success: function (data) {
                $('#mrcode').empty();
                $('#table_body_mr_detail').empty();
                var mySelect = $('#mrcode');
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, value) {
                        var balanceQTY = value['usageQty'];
                        if(balanceQTY>0){
                            var id = 'pull-'+value['mrAutoID'];
                            mySelect.append('' +
                                '<li id="'+id+'" title="MR Date :- ' + value['requestedDate'] + ' Requested By:- ' + value['employeeName'] + '"  rel="tooltip" class="pull-li">' +
                                    '<a onclick="fetch_mr_detail_table(' + value['mrAutoID'] + ', '+stockTransferAutoID+')">' + value['MRCode'] + ' ' +
                                        '<span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span>' +
                                    '</a>' +
                                '</li>');
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

    function fetch_mr_detail_table(mrAutoID,stockTransferAutoID) {
        $('.pull-li').removeClass('pulling-based-li');
        $('#pull-'+mrAutoID).addClass('pulling-based-li');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'mrAutoID': mrAutoID, stockTransferAutoID:stockTransferAutoID},
            url: "<?php echo site_url('Inventory/fetch_mr_detail_table_ST'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#table_body_mr_detail').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#table_body_mr_detail').append('<tr class="danger"><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    tot_amount = 0;
                    var issuedQTY = 0;
                    var balanceQTY;
                    var issuedQTYs;
                    $.each(data['detail'], function (key, value) {
                        if (value['qtyIssued']) {
                            issuedQTY = value['qtyIssued'];
                        } else {
                            issuedQTY = 0;
                        }
                        // balanceQTY = value['qtyRequested'] - issuedQTY;
                        balanceQTY = value['balanceQTY'];
                        if (issuedQTY != value['qtyRequested']) {
                            masterCurrentStock = (value['stock'] - value['miQtyIssued']).toFixed(1);
                            //currentStock = parseFloat(value['stock']).toFixed(1);
                            issuedQTYs = '<input type="text" class="number" size="10" id="qtyIssued_' + value['mrDetailID'] + '" value="0" onkeyup="check_qty_available(' + value['mrDetailID'] + ',' + balanceQTY + ',' + masterCurrentStock + ')" > <input class="checkbox" id="check_' + value['mrDetailID'] + '" type="hidden" value="' + value['mrDetailID'] + '">';
                            $('#table_body_mr_detail').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + (value['qtyRequested'] ) + '</td><td class="text-right">' + issuedQTY + '</td><td class="text-right">' + balanceQTY + ' <i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' + value['mrDetailID'] + ',' + balanceQTY + ',' + masterCurrentStock + ')" aria-hidden="true"></i></td><td class="text-right">' + masterCurrentStock + ' </td><td class="text-right">' + issuedQTYs + '</td></tr>');
                        }
                        x++;
                    });

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

    function fetch_detail() {
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        var advanceCostCapturing = '<?php echo getPolicyValues('ACC', 'All'); ?>';
        var transfer_Type = $('#transferType').val();
        if (stockTransferAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'stockTransferAutoID': stockTransferAutoID},
                url: "<?php echo site_url('Inventory/fetch_stockTransfer_detail_table'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    var total = 0;
                    var descm = '<?php echo $this->common_data['company_data']['company_default_decimal']?>';
                    $('#item_table_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#item_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                        $("#form_location").prop("disabled", false);
                        $("#to_location").prop("disabled", false);
                        $("#itemType").prop("disabled", false);
                        $("#transferType").prop("disabled", false);
                        $("#editallbtn").addClass("hidden");
                        if(transfer_Type == 'materialRequest'){
                            $('#activityCode_header').addClass('hide');
                        }else{
                            $('#activityCode_header').removeClass('hide');
                        }
                    } else {
                        $("#form_location").prop("disabled", true);
                        $("#to_location").prop("disabled", true);
                        $("#itemType").prop("disabled", true);
                        $("#transferType").prop("disabled", true);
                        if(data.transferType == 'materialRequest'){
                            $('#activityCode_header').addClass('hide');
                        }else{
                            $('#activityCode_header').removeClass('hide');
                        }
                        tot_amount = 0;
                        $.each(data, function (key, value) {
                            if (value['isSubitemExist'] == 1) {
                                var subItemapplicableon = 1;
                                var colour = 'color: #dad835 !important';
                                colour = '';
                                if(value['subItemapplicableon'] == 2){
                                    subItemapplicableon = (value['subItemapplicableon']);
                                }

                                if(itemBatchPolicy==1){
                                    if(advanceCostCapturing == 1){
                                        string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber']+ '</td><td>' + value['itemDescription'] + '</td><td>' + value['binlocation'] + '</td><td class="text-center">' + value['activityCodeName'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['transfer_QTY'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(descm, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(descm, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['stockTransferDetailsID'] + ',\'ST\',' + value['from_wareHouseAutoID'] + ',0,'+ subItemapplicableon +'), table_fetch()"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['stockTransferDetailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a>  &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_stock_transferDetails(' + value['stockTransferDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }else{
                                        string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber']+ '</td><td>' + value['itemDescription'] + '</td><td>' + value['binlocation'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['transfer_QTY'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(descm, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(descm, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['stockTransferDetailsID'] + ',\'ST\',' + value['from_wareHouseAutoID'] + ',0,'+ subItemapplicableon +'), table_fetch()"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['stockTransferDetailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a>  &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_stock_transferDetails(' + value['stockTransferDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }
                                    
                                }else{
                                    if(advanceCostCapturing == 1){
                                        string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['binlocation'] + '</td><td class="text-center">' + value['activityCodeName'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['transfer_QTY'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(descm, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(descm, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['stockTransferDetailsID'] + ',\'ST\',' + value['from_wareHouseAutoID'] + ',0,'+ subItemapplicableon +'), table_fetch()"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['stockTransferDetailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a>  &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_stock_transferDetails(' + value['stockTransferDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }else{
                                        string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['binlocation'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['transfer_QTY'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(descm, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(descm, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['stockTransferDetailsID'] + ',\'ST\',' + value['from_wareHouseAutoID'] + ',0,'+ subItemapplicableon +'), table_fetch()"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="edit_item(' + value['stockTransferDetailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a>  &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_stock_transferDetails(' + value['stockTransferDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }
                                    
                                }

                                
                            } else {

                                if(itemBatchPolicy==1){
                                    if(advanceCostCapturing == 1){
                                        string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber']+ '</td><td>' + value['itemDescription'] + '</td><td>' + value['binlocation'] + '</td><td class="text-center">' + value['activityCodeName'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['transfer_QTY'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(descm, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(descm, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['stockTransferDetailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_stock_transferDetails(' + value['stockTransferDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }else{
                                        string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber']+ '</td><td>' + value['itemDescription'] + '</td><td>' + value['binlocation'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['transfer_QTY'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(descm, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(descm, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['stockTransferDetailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_stock_transferDetails(' + value['stockTransferDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                    }
                                    
                                }else{
                                    if(advanceCostCapturing == 1){
                                        string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['binlocation'] + '</td><td class="text-center">' + value['activityCodeName'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['transfer_QTY'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(descm, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(descm, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['stockTransferDetailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_stock_transferDetails(' + value['stockTransferDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>'; 
                                    }else{
                                        string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['binlocation'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['transfer_QTY'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(descm, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(descm, '.', ',') + '</td><td class="text-right"><a onclick="edit_item(' + value['stockTransferDetailsID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_stock_transferDetails(' + value['stockTransferDetailsID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>'; 
                                    }
                                }

                                

                            }
                            $('#item_table_body').append(string);


                            /*  <a onclick="edit_item(' + value['stockTransferDetailsID'] + ',\'' + value['itemDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; | &nbsp;&nbsp;*/
                            total = total + parseFloat(value['totalValue']);
                            x++;

                        });
                        $("#editallbtn").removeClass("hidden");
                    }
                    if(transferType == 'materialRequest'){
                        $('.mrBaseDiveTable').removeClass('hide');
                        $('#editallbtn').addClass('hide');
                        $('#item_table_tfoot').html('<tr> <td class="text-right" colspan="8"> <?php echo $this->lang->line('common_total');?> </td><td class="text-right total">' + total.formatMoney(descm, '.', ',') + '</td><td></td></tr>')

                        /*$('#table_tfoot').html('<tr> <td class="text-right" colspan="7"><?php echo $this->lang->line('common_total');?></td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>');*/
                    }else{
                        /*$('#table_tfoot').html('<tr> <td class="text-right" colspan="6"><?php echo $this->lang->line('common_total');?> </td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>');*/
                        $('.mrBaseDiveTable').addClass('hide');
                        $('#editallbtn').removeClass('hide');
                        $('#item_table_tfoot').html('<tr> <td class="text-right" colspan="7"> <?php echo $this->lang->line('common_total');?> </td><td class="text-right total">' + total.formatMoney(descm, '.', ',') + '</td><td></td></tr>')
                    }
                    stopLoad();/* <!--Total--> */

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }
    }

    function check_qty_available(mrDetailID, balanceQTY, currentStock) {
        var issuedqty = $('#qtyIssued_' + mrDetailID).val();
        if (issuedqty > currentStock) {
            myAlert('w', 'Qty cannot be greater than Current Qty');
            $('#qtyIssued_' + mrDetailID).val(0);
            $('#mrbasebtn').attr('disabled', true);
        } else if (issuedqty > balanceQTY) {
            myAlert('w', 'Qty cannot be greater than Balance Qty');
            $('#qtyIssued_' + mrDetailID).val(0);
            $('#mrbasebtn').attr('disabled', true);
        } else {
            $('#mrbasebtn').attr('disabled', false);
        }
    }

    function clearitemAutoID(e,ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        }else{
            $(ths).closest('tr').find('.itemAutoID').val('');
        }

    }

    function clearitemAutoIDEdit(e,ths) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 || keyCode == 13) {
            //e.preventDefault();
        }else{
            $(ths).closest('tr').find('#itemAutoID_edit').val('');
        }
    }

    function initializeitemTypeahead(type, id) {
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        /** var inv_item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Inventory/fetch_inv_item/?q=%QUERY&t=" + type
        });

         inv_item.initialize();
         $('.search').typeahead(null, {
            minLength: 3,
            highlight: true,
            displayKey: 'Match',
            source: inv_item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $(this).closest('tr').find('.itemAutoID').val(datum.itemAutoID);
            $(this).closest('tr').find('.currentStock').val(datum.currentStock);
            $(this).closest('tr').find('.d_uom').val(datum.defaultUnitOfMeasure);
            fetch_related_uom_id(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID, this);
            fetch_st_warehouse_item(datum.itemAutoID, this);
            return false;
        });*/

        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Inventory/fetch_inv_item_stock_adjustment/?&t=' + type,
            onSelect: function (suggestion) {
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
                            myAlert('w', 'Selected item is already selected');
                            cont = false;
                        }
                    }
                });
                if(cont) {
                setTimeout(function () {
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                }, 200);

                if(itemBatchPolicy==1){
                    var warehouseIDSelect = $('#form_location').val();
                    if(warehouseIDSelect!=''){
                        getItemBatchDetails(suggestion.itemAutoID,id,warehouseIDSelect);
                    }
                       
                }

                $(this).closest('tr').find('.currentStock').val(suggestion.currentStock);
                $(this).closest('tr').find('.d_uom').text(suggestion.defaultUnitOfMeasure);
                $(this).closest('tr').find('.itemcatergory').val(suggestion.mainCategory);
                fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                fetch_st_warehouse_item(suggestion.itemAutoID, this);
                $(this).closest('tr').find('.transferqty').focus();
                $(this).closest('tr').css("background-color", 'white');
                // check_item_not_approved_in_document(suggestion.itemAutoID,id);
                check_item_not_approved_in_document(suggestion.itemAutoID,id,'');
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
            highlight: true,
            displayKey: 'Match',
            source: inv_item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $('#itemAutoID_edit').val(datum.itemAutoID);
            $('#currentStock_edit').val(datum.currentStock);
            $('#d_uom_edit').val(datum.defaultUnitOfMeasure);
            fetch_related_uom_id_edit(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID);
            fetch_st_warehouse_item_edit(datum.itemAutoID);
        });*/


        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Inventory/fetch_inv_item_stock_adjustment/?&t=' + type,
            onSelect: function (suggestion) {
                setTimeout(function(){
                    $('#itemAutoID_edit').val(suggestion.itemAutoID);
                    $('#currentStock_edit').val(suggestion.currentStock);
                    $('#d_uom_edit').val(suggestion.defaultUnitOfMeasure);
                    $('#itemcatergory_Edit').val(suggestion.mainCategory);
                    $('#transferqty_edit').val(0);
                }, 200);
                $(this).closest('tr').find('#transferqty_edit').focus();
               
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                fetch_st_warehouse_item_edit(suggestion.itemAutoID);
                // check_item_not_approved_in_document(suggestion.itemAutoID);
                check_item_not_approved_in_document(suggestion.itemAutoID,stockTransferAutoID,'ST');
            }
        });
        $('#search').off('focus.autocomplete');
    }

    // function check_item_not_approved_in_document(itemAutoID,id) {
    function check_item_not_approved_in_document(itemAutoID,id,documentcode) {

            $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            //data: {itemAutoID: itemAutoID},
            //url: "<?php //echo site_url('Inventory/check_item_not_approved_in_document'); ?>//",
            data: {itemAutoID: itemAutoID,'documentcode':documentcode,'stockTransferDetailsID':stockTransferDetailsID},
            url: "<?php echo site_url('Inventory/check_item_not_approved_in_document_new'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#access_denied_item_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['usedDocs'])) {
                    $('#access_denied_item_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                } else {
                    $.each(data['usedDocs'], function (key, value) {
                        $('#access_denied_item_body').append('<tr><td>' + x + '</td><td>' + value['documentID'] + '</td><td><a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'' + value['documentID'] + '\',' + value['documentAutoID'] + ')">' + value['documentCode'] + '</a></td><td>' + value['referenceNo'] + '</td><td>' + value['documentDate'] + '</td><td>' + value['warehouse'] + '</td><td>' + value['Uom'] + '</td><td>' + value['stock'] + '</td></tr>');
                        x++;
                    });
                   
                   /*  $('#f_search_' + id).val('');
                    $('#f_search_' + id).closest('tr').find('.f_search').val('');
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                    $('#f_search_' + id).closest('tr').find('.UnitOfMeasureID').val('');
                    $('#f_search_' + id).closest('tr').find('.currentWareHouseStock').val('');
                    $('#f_search_' + id).closest('tr').find('.segment').val('');
                    $('#f_search_' + id).closest('tr').find('.transferqty').val('');
                      */
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

    function check_item_not_approved_in_document_edit(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {itemAutoID: itemAutoID},
            url: "<?php echo site_url('Inventory/check_item_not_approved_in_document'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#access_denied_item_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['usedDocs'])) {
                    $('#access_denied_item_body').append('<tr class="danger"><td colspan="2" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                } else {
                    $.each(data['usedDocs'], function (key, value) {
                        $('#access_denied_item_body').append('<tr><td>' + x + '</td><td><a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'' + value['documentID'] + '\',' + value['documentAutoID'] + ')">' + value['documentCode'] + '</a></td></tr>');
                        x++;
                    });


                    $('#itemAutoID_edit').val('');
                    $('#UnitOfMeasureID_edit').val('');
                    $('#currentWareHouseStockQty_edit').val('');
                    $('#segment_edit').val(null).trigger('change');
                    $('#transferqty_edit').val('');
                    $('#search').val('');
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




    function fetch_st_warehouse_item(itemAutoID, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            //data: {'stockTransferAutoID': stockTransferAutoID, 'itemAutoID': itemAutoID},
            //url: "<?php //echo site_url('Inventory/fetch_st_warehouse_item'); ?>//",
            data: {'stockTransferAutoID': stockTransferAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Inventory/fetch_st_warehouse_item_new'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                /* if (data['status']) {
                    $(element).closest('tr').find('.currentWareHouseStock').val(data['currentStock']);
                } else {
                    $(element).typeahead('val', '');
                    $(element).closest('tr').find('.itemAutoID').val('');
                } */

                if (data['status']) {
                        var conversionRate = $(element).closest('tr').find('.conversionRate').val();
                        if(conversionRate !== '') {
                          
                            data['currentStock'] = data['currentStock'] * conversionRate;
                        }
                      
                        if(data['mainCategory']=='Service'){
                            $(element).closest('tr').find('.currentWareHouseStock').val('');
                            $(element).closest('tr').find('.currentstock_pulleddocument').val('');
                            $(element).closest('tr').find('.parkQty').val('');
                        }else if(data['mainCategory']=='Non Inventory'){
                            $(element).closest('tr').find('.currentWareHouseStock').val('');
                            $(element).closest('tr').find('.currentstock_pulleddocument').val('');
                            $(element).closest('tr').find('.parkQty').val('');
                        }else{
                            
                            $(element).closest('tr').find('.currentWareHouseStock').val(data['currentStock']);
                            $(element).closest('tr').find('.currentstock_pulleddocument').val(data['pulledstock']);
                            $(element).closest('tr').find('.parkQty').val(data['parkQty']);
                        }
                    } else {
                         
                        $(element).typeahead('val', '');
                        $(element).closest('tr').find('.currentWareHouseStock').val('');
                        $(element).closest('tr').find('.currentstock_pulleddocument').val('');
                        $(element).closest('tr').find('.itemAutoID').val('');
                        $(element).closest('tr').find('.f_search').val('');
                        $(element).closest('tr').find('.parkQty').val('');
                }


                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_st_warehouse_item_edit(itemAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            //data: {'stockTransferAutoID': stockTransferAutoID, 'itemAutoID': itemAutoID},
            //url: "<?php //echo site_url('Inventory/fetch_st_warehouse_item'); ?>//",
            data: {'stockTransferAutoID': stockTransferAutoID, 'itemAutoID': itemAutoID,'stockTransferDetailsID':stockTransferDetailsID,'documentcode':'ST'},
            url: "<?php echo site_url('Inventory/fetch_st_warehouse_item_new'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
 

                if (data['status']) {

                if(data['mainCategory']=='Service'){
                    $('#currentWareHouseStockQty_edit').val('');
                    $('#pulledcurrentstock_edit').val('');
                    $('#parkQty_edit').val('');
                }else if(data['mainCategory']=='Non Inventory'){
                    $('#currentWareHouseStockQty_edit').val('');
                    $('#pulledcurrentstock_edit').val('');
                    $('#parkQty_edit').val('');
                }else{
                    var conversionRate = $('#conversionRateEdit').val();
                    if(parseFloat(conversionRate) > 0 && data['currentStock'] != null) {
                    data['currentStock'] = parseFloat(data['currentStock']) * parseFloat(conversionRate);
                }
                    $('#currentWareHouseStockQty_edit').val(data['currentStock']);
                    $('#pulledcurrentstock_edit').val(data['pulledstock']);
                    $('#parkQty_edit').val(data['parkQty']);
                }
            } else {
                    $('#currentstock_edit').val('');
                    $('#pulledcurrentstock_edit').val('');
                    $('#parkQty_edit').val('');
                }

                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
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

    function edit_item(id) {
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        if (stockTransferAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_edit_this_record');?>",/*You want to edit this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_edit');?>",/*Edit*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    var location = $('#form_location').val();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'stockTransferDetailsID': id, location: location},
                        url: "<?php echo site_url('Inventory/load_stock_transfer_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            if(itemBatchPolicy==1){
                                var textBatchData=data['batchNumber'];
                                var wareHouseAutoIDSelect=$('#form_location').val();
                                $.ajax({
                                    async: true,
                                    type: 'post',
                                    dataType: 'json',
                                    data: {'itemId': data.itemAutoID,'wareHouseAutoID': wareHouseAutoIDSelect},
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

                            stockTransferDetailsID = data['stockTransferDetailsID'];
                            projectID = data['projectID'];
                            projectcategory = data['project_categoryID'];
                            projectsubcat = data['project_subCategoryID'];
                            $('#search').typeahead('destroy');
                            $('#search').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - "+data['seconeryItemCode']);
                            $('#itemAutoID_edit').val(data.itemAutoID);
                            $('#currentStock_edit').val(data.currentStock);
                            $('#currentWareHouseStockQty_edit').val(data.wareHouseStock);
                            $('#d_uom_edit').text(data.defaultUOM);
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#transferqty_edit').val(data['transfer_QTY']);
                            $('#segment_edit').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            $('#edit_activityCode').val(data.activityCodeID).change();
                            initializeitemTypeahead_edit(type);

                            if (transferType == 'materialRequest') {
                                $('#search').attr('disabled',true);
                                $('#UnitOfMeasureID_edit').attr('disabled',true);
                                $('#comment_edit').attr('readonly',true);
                                $('.mitypecoloumn').addClass('hidden');
                            }else{
                                $('#search').attr('disabled',false);
                                $('#UnitOfMeasureID_edit').attr('disabled',false);
                                $('#comment_edit').attr('readonly',false);
                                $('.mitypecoloumn').removeClass('hidden');
                            }
                            $('#itemcatergory_Edit').val(data['mainCategory']);
                            $('#conversionRateEdit').val(data['conversionRateUOM']);
                            $("#item_edit_detail_modal").modal({backdrop: "static"});
                            fetch_st_warehouse_item_edit(data['itemAutoID']);
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

    function load_conformation() {
        if (stockTransferAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'stockTransferAutoID': stockTransferAutoID, 'html': true},
                url: "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>/" + stockTransferAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + stockTransferAutoID + '/ST');
                    attachment_modal_stockTransfer(stockTransferAutoID, "<?php echo $this->lang->line('transaction_stock_transfer');?>", "ST");/*Stock Transfer*/
                    stopLoad();
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                }
            });
        }
    }

    function confirmation() {
        if (stockTransferAutoID) {
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
                        data: {'stockTransferAutoID': stockTransferAutoID},
                        url: "<?php echo site_url('Inventory/stock_transfer_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                                stopLoad();
                             if (data['error'] == 1) {
                                 myAlert('e', data['message']);
                                 if(data['message']=='Some Item quantities are not sufficient to confirm this transaction.'){
                                 confirm_all_item_detail_modal(data['itemAutoID']);
                                 }
                             }  else if(data['error']==2){
                                 myAlert('w',data['message']);
                             }  else {
                             myAlert('s', data['message']);
                                }

                         }, error: function () {
                             swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (stockTransferAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_save_this_document');?>",/*You want to save this document!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_save_as_draft');?>",/*Save as Draft*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    fetchPage('system/inventory/stock_transfer_management', 'Test', 'Stock Transfer');
                });
        }
        ;
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID,'fyDepartmentID':'GRV'},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('Select  Finance Period'));
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

    function attachment_modal_stockTransfer(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#stockTransfer_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");<!--Attachments-->
                    $('#stockTransfer_attachment').empty();
                    $('#stockTransfer_attachment').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_stockTransfer_attachement(stockTransferAutoID, DocumentSystemCode, myFileName) {
        if (stockTransferAutoID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete_this_attachment_file');?>",/*You want to delete this attachment file!*/
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
                        data: {'attachmentID': stockTransferAutoID, 'myFileName': myFileName},
                        url: "<?php echo site_url('inventory/delete_stockTransfer_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');/*Deleted Successfully*/
                                attachment_modal_stockTransfer(DocumentSystemCode, "Stock Transfer", "ST");
                            } else {
                                myAlert('e', '<?php echo $this->lang->line('common_deletion_failed');?>');/*Deletion Failed*/
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }
    function delete_stock_transferDetails(stockReturnDetailsID) {
        if (stockReturnDetailsID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record !*/
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
                        data: {'stockReturnDetailsID': stockReturnDetailsID},
                        url: "<?php echo site_url('inventory/delete_stockTransfer_details'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_detail();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    var batch_number=0;

    function add_more_material_issue() {
        //$('.search').typeahead('destroy');
        search_id += 1;
        batch_number += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#StockTransfer_detail_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');

        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';

        if(itemBatchPolicy==1){
            appendData.find('.b_number').empty();
            appendData.find('.b_number').attr('id', 'batch_number_' + search_id);
            appendData.find('.b_number').attr('name', 'batch_number[' + batch_number+'][]');
        }

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#StockTransfer_detail_add_table').append(appendData);
        var lenght = $('#StockTransfer_detail_add_table tbody tr').length - 1;
        $('#f_search_'+ search_id).closest('tr').css("background-color",'white');
        $(".select2").select2();
        initializeitemTypeahead(type, search_id);
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function saveStockTransfer_addItem() {
        var $form = $('#item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'stockTransferAutoID', 'value': stockTransferAutoID});
        data.push({'name': 'stockTransferDetailsID', 'value': stockTransferDetailsID});
        $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });

        $('.itemAutoID').each(function () {
            if(this.value == ''){
                $(this).closest('tr').css("background-color",'#ffb2b2');
            }
        });
        $('.transferqty').each(function () {
            if(this.value == '' || this.value == 0){
                $(this).closest('tr').css("background-color",'#ffb2b2');
            }
        });
        $('.currentWareHouseStock').each(function () {
            if(this.value == '' || this.value == 0){
                $(this).closest('tr').css("background-color",'#ffb2b2');
            }
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/save_stock_transfer_detail_multiple'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data['error'] == 2) {  /*Inventory_modal : Line No.1506 */
                    myAlert('e', data['message']);
                    clearStockTransferItemDetail();
                } else {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        stockTransferDetailsID = null;
                        $('#activityCode').val('').change();
                        $('#item_detail_form')[0].reset();
                        setTimeout(function () {
                            fetch_detail();
                            $('#item_detail_modal').modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        }, 300);

                    }
                }

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function clearStockTransferItemDetail() {
        $("#item_detail_form").closest('form').find("input[type=text], textarea").val("");
        $(".itemAutoID").val("");
        $(".currentStock").val("");
        initializeitemTypeahead(type);
    }

    function updateStockTransfer_addItem() {
        $('#search').attr('disabled',false);
        $('#UnitOfMeasureID_edit').attr('disabled',false);
        $('#comment_edit').attr('readonly',false);
        var $form = $('#edit_item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'stockTransferAutoID', 'value': stockTransferAutoID});
        data.push({'name': 'stockTransferDetailsID', 'value': stockTransferDetailsID});
        data.push({'name': 'transferType', 'value': transferType});
        data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/save_stock_transfer_detail'); ?>",
            beforeSend: function () {
                $('#search').attr('disabled',true);
                $('#UnitOfMeasureID_edit').attr('disabled',true);
                $('#comment_edit').attr('readonly',true);
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    stockTransferDetailsID = null;
                    $('#edit_activityCode').val('').change();
                    $('#edit_item_detail_form')[0].reset();
                    setTimeout(function () {
                        fetch_detail();
                        $('#item_edit_detail_modal').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }, 300);

                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
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
            data: {segment: segment.value,type:type},
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

    function checkCurrentStock(det){
        var currentStock= $(det).closest('tr').find('.currentWareHouseStock').val();
        if(det.value > parseFloat(currentStock)){
            myAlert('w','Transfer quantity should be less than or equal to current stock');
            $(det).val(0);
        }

            if (det.value > 0) {
                $(det).closest('tr').css("background-color", 'white');
            }

    }

    function checkCurrentStockEdit(){
        var currentStock=$('#currentWareHouseStockQty_edit').val();
        var TransferQty=$('#transferqty_edit').val();
        if(parseFloat(TransferQty) > parseFloat(currentStock)){
            myAlert('w','Transfer quantity should be less than or equal to current stock');
            $('#transferqty_edit').val(0);
        }
    }

    function edit_all_item_detail_modal(){
        var location = $('#form_location').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'stockTransferAutoID': stockTransferAutoID,location: location},
                url: "<?php echo site_url('Inventory/fetch_stockTransfer_all_detail_edit'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    var total = 0;
                    var descm = 2;
                    $('#edit_item_table_body').empty();
                    var x = 2;
                    if (jQuery.isEmptyObject(data)) {
                        $('#edit_item_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                        $("#form_location").prop("disabled", false);
                        $("#to_location").prop("disabled", false);
                        $("#itemType").prop("disabled", false);
                        $("#transferType").prop("disabled", false);
                    } else {
                        $("#form_location").prop("disabled", true);
                        $("#to_location").prop("disabled", true);
                        $("#itemType").prop("disabled", true);
                        $("#transferType").prop("disabled", true);
                        $.each(data['details'], function (key, value) {
                            var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown"  required')) ?>';
                            var Segment = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="seg_\'+key+\'"'), form_dropdown('a_segment[]', $segment_arr, '', 'class="form-control select2 segment"  required onchange="load_segmentBase_projectID_item(this)"')) ?>';
                            var activityCode = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="edit_activityCode_\'+key+\'"'), form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2 activityCode"  required"')) ?>';
                            var Project = '<div class="div_projectID_income"><select name="projectID"  class="form-control select2"><option value=""><?php echo $this->lang->line('common_select_project');?></option></select></div>';

                            var ProjectCat = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="project_cat\'+key+\'"'), form_dropdown('project_categoryID[]', array('' => 'Select Project Category'), '', 'class="project_categoryID form-control select2" id="project_categoryID" onchange="fetch_project_sub_category(this, this.value)"')) ?>';
                            var ProjectSubCat = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="project_subCat\'+key+\'"'), form_dropdown('project_subCategoryID[]', array('' => 'Select Project Subcategory'), '', 'class="project_subCategoryID form-control select2" id="project_subCategoryID"')) ?>';

                            var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
                            if(itemBatchPolicy==1){
                                var textBatchData=value['batchNumber'];
                                var wareHouseAutoIDSelect=$('#form_location').val();
                                var batchNumberDropdown = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="batchNumber_\'+key+\'"'), form_dropdown('batch_number[\'+key+\'][]', [], '', 'class="form-control select2 input-mini batchNumberEditAll" multiple="multiple" required')) ?>';

                                $.ajax({
                                    async: true,
                                    type: 'post',
                                    dataType: 'json',
                                    data: {'itemId': value['itemAutoID'],'wareHouseAutoID':wareHouseAutoIDSelect},
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

                                <?php if($projectExist ==1){?>
                                    var string = '<tr>' +
                                        ' <td> <input type="text" onkeyup="clearitemAutoID(event,this)" value="' + value['itemDescription'] + ' - ' + value['itemSystemCode'] + ' - '+ value['seconeryItemCode']+'" class="form-control search f_search" name="search[]" placeholder="Item ID,Item Description..." id="f_search_'+ x +'"> ' +
                                        '<input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" value="' + value['itemAutoID'] + '">' +
                                        ' <input type="hidden" class="form-control stockTransferDetailsID" name="stockTransferDetailsID[]" value="' + value['stockTransferDetailsID'] + '">' +
                                        ' <input type="hidden" class="form-control currentStock" name="currentStock[]"></td>' +
                                        ' <td>'+ UOM +'</td> ' +
                                        ' <td>'+ batchNumberDropdown +'</td> ' +
                                        '<td> <div class="input-group"> <input type="text" name="currentWareHouseStockQty[]"class="form-control currentWareHouseStock" value="' + value['wareHouseStock'] + '" required readonly> </div> </td> ' +
                                        '<td>'+ Segment +'</td> ' +
                                        '<td>'+ activityCode +'</td> ' +
                                        ' <td>'+ Project +'</td> ' +
                                        ' <td>'+ ProjectCat +'</td> ' +
                                        ' <td>'+ ProjectSubCat +'</td> ' +
                                        '<td style="width: 100px"> <input type="text" name="transfer_QTY[]" onfocus="this.select();" onkeyup="checkCurrentStock(this)" value="' + value['transfer_QTY'] + '" class="form-control number transferqty"required> </td>' +
                                        '<td class="remove-td"><a onclick="delete_stock_transferDetailsEdit(' + value['stockTransferDetailsID'] + ',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a>' +
                                        '</td></tr>';
                                <?php }else {?>
                                    var string = '<tr>' +
                                    ' <td> <input type="text" onkeyup="clearitemAutoID(event,this)" value="' + value['itemDescription'] + ' - ' + value['itemSystemCode'] + ' - '+ value['seconeryItemCode']+'" class="form-control search f_search" name="search[]" placeholder="Item ID,Item Description..." id="f_search_'+ x +'"> ' +
                                        '<input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" value="' + value['itemAutoID'] + '">' +
                                        ' <input type="hidden" class="form-control stockTransferDetailsID" name="stockTransferDetailsID[]" value="' + value['stockTransferDetailsID'] + '">' +
                                        ' <input type="hidden" class="form-control currentStock" name="currentStock[]"></td>' +
                                        ' <td>'+ UOM +'</td> ' +
                                        ' <td>'+ batchNumberDropdown +'</td> ' +
                                        '<td> <div class="input-group"> <input type="text" name="currentWareHouseStockQty[]"class="form-control currentWareHouseStock" value="' + value['wareHouseStock'] + '" required readonly> </div> </td> ' +
                                        '<td>'+ Segment +'</td> ' +
                                        '<td>'+ activityCode +'</td> ' +
                                        '<td style="width: 100px"> <input type="text" name="transfer_QTY[]" onfocus="this.select();" onkeyup="checkCurrentStock(this)" value="' + value['transfer_QTY'] + '" class="form-control number transferqty"required> </td>' +
                                        '<td class="remove-td"><a onclick="delete_stock_transferDetailsEdit(' + value['stockTransferDetailsID'] + ',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a>' +
                                        '</td></tr>';

                                <?php }?>

                            }else{
                            
                                <?php if($projectExist ==1){?>
                                    var string = '<tr>' +
                                        ' <td> <input type="text" onkeyup="clearitemAutoID(event,this)" value="' + value['itemDescription'] + ' - ' + value['itemSystemCode'] + ' - '+ value['seconeryItemCode']+'" class="form-control search f_search" name="search[]" placeholder="Item ID,Item Description..." id="f_search_'+ x +'"> ' +
                                        '<input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" value="' + value['itemAutoID'] + '">' +
                                        ' <input type="hidden" class="form-control stockTransferDetailsID" name="stockTransferDetailsID[]" value="' + value['stockTransferDetailsID'] + '">' +
                                        ' <input type="hidden" class="form-control currentStock" name="currentStock[]"></td>' +
                                        ' <td>'+ UOM +'</td> ' +
                                        '<td> <div class="input-group"> <input type="text" name="currentWareHouseStockQty[]"class="form-control currentWareHouseStock" value="' + value['wareHouseStock'] + '" required readonly> </div> </td> ' +
                                        '<td>'+ Segment +'</td> ' +
                                        '<td>'+ activityCode +'</td> ' +
                                        ' <td>'+ Project +'</td> ' +
                                        ' <td>'+ ProjectCat +'</td> ' +
                                        ' <td>'+ ProjectSubCat +'</td> ' +
                                        '<td style="width: 100px"> <input type="text" name="transfer_QTY[]" onfocus="this.select();" onkeyup="checkCurrentStock(this)" value="' + value['transfer_QTY'] + '" class="form-control number transferqty"required> </td>' +
                                        '<td class="remove-td"><a onclick="delete_stock_transferDetailsEdit(' + value['stockTransferDetailsID'] + ',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a>' +
                                        '</td></tr>';
                                <?php }else {?>
                                    var string = '<tr>' +
                                    ' <td> <input type="text" onkeyup="clearitemAutoID(event,this)" value="' + value['itemDescription'] + ' - ' + value['itemSystemCode'] + ' - '+ value['seconeryItemCode']+'" class="form-control search f_search" name="search[]" placeholder="Item ID,Item Description..." id="f_search_'+ x +'"> ' +
                                        '<input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" value="' + value['itemAutoID'] + '">' +
                                        ' <input type="hidden" class="form-control stockTransferDetailsID" name="stockTransferDetailsID[]" value="' + value['stockTransferDetailsID'] + '">' +
                                        ' <input type="hidden" class="form-control currentStock" name="currentStock[]"></td>' +
                                        ' <td>'+ UOM +'</td> ' +
                                        '<td> <div class="input-group"> <input type="text" name="currentWareHouseStockQty[]"class="form-control currentWareHouseStock" value="' + value['wareHouseStock'] + '" required readonly> </div> </td> ' +
                                        '<td>'+ Segment +'</td> ' +
                                        '<td>'+ activityCode +'</td> ' +
                                        '<td style="width: 100px"> <input type="text" name="transfer_QTY[]" onfocus="this.select();" onkeyup="checkCurrentStock(this)" value="' + value['transfer_QTY'] + '" class="form-control number transferqty"required> </td>' +
                                        '<td class="remove-td"><a onclick="delete_stock_transferDetailsEdit(' + value['stockTransferDetailsID'] + ',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a>' +
                                        '</td></tr>';

                                <?php }?>
                            }

                            $('#edit_item_table_body').append(string);
                            //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                            $('#seg_'+key).val(value['segmentID'] + '|' + value['segmentCode']);
                            //fetch_related_uom_id( value['defaultUOMID'],value['unitOfMeasureID'],$('#uom_'+key));
                            $('#edit_activityCode_'+key).val(value['activityCodeID']).change();

                            $('#uom_'+key).closest('tr').find('.umoDropdown').empty();
                            var mySelect = $('#uom_'+key).parent().closest('tr').find('.umoDropdown');
                            mySelect.append($('<option></option>').val('').html('Select  UOM'));
                            if (!jQuery.isEmptyObject(data['alluom'])) {
                                $.each(data['alluom'], function (val, text) {
                                    if(value['defaultUOMID']==text['masterUnitID']){
                                        mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                                    }

                                });
                                if (value['unitOfMeasureID']) {
                                    $('#uom_'+key).closest('tr').find('.umoDropdown').val(value['unitOfMeasureID']);
                                }
                            }




                            initializeitemTypeahead(type, x);
                            x++;
                        });
                        search_id=x-1;
                        $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                    }
                    stopLoad();<!--Total-->

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
    }

    function add_more_edit_material_issue() {
        //$('.search').typeahead('destroy');
        var batch_number_edit_all =search_id-1;
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#StockTransfer_detail_all_edit_table tbody tr:first').clone();
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
        $('#StockTransfer_detail_all_edit_table').append(appendData);
        var lenght = $('#StockTransfer_detail_all_edit_table tbody tr').length - 1;
        $('#f_search_'+ search_id).closest('tr').css("background-color",'white');
        $(".select2").select2();
        initializeitemTypeahead(type, search_id);
        number_validation();
    }


    function updateStockTransfer_edit_all_Item() {
        var $form = $('#edit_all_item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'stockTransferAutoID', 'value': stockTransferAutoID});
        //data.push({'name': 'stockTransferDetailsID', 'value': stockTransferDetailsID});
        $('#edit_all_item_detail_form select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()});
        });

        $('.itemAutoID').each(function () {
            if(this.value == ''){
                $(this).closest('tr').css("background-color",'#ffb2b2');
            }
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/save_stock_transfer_detail_edit_all_multiple'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data['error'] == 2) {  /*Inventory_modal : Line No.1506 */
                    myAlert('e', data['message']);
                    clearStockTransferEditAllItemDetail();
                } else {
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        stockTransferDetailsID = null;
                        $('#edit_all_item_detail_form')[0].reset();
                        setTimeout(function () {
                            fetch_detail();
                            load_conformation();
                            $('#all_item_edit_detail_modal').modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        }, 300);

                    }
                }

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function clearStockTransferEditAllItemDetail() {
        $("#edit_all_item_detail_form").closest('form').find("input[type=text], textarea").val("");
        $(".itemAutoID").val("");
        $(".currentStock").val("");
        initializeitemTypeahead(type);
    }


    function delete_stock_transferDetailsEdit(stockReturnDetailsID,det) {
        if (stockReturnDetailsID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this record !*/
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
                        data: {'stockReturnDetailsID': stockReturnDetailsID},
                        url: "<?php echo site_url('inventory/delete_stockTransfer_details'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_detail();
                            load_conformation();
                            stopLoad();
                            refreshNotifications(true);
                            $(det).closest('tr').remove();
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function confirm_all_item_detail_modal(itemAutoIdArr){
        var location = $('#form_location').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'stockTransferAutoID': stockTransferAutoID,location: location},
            url: "<?php echo site_url('Inventory/fetch_stockTransfer_all_detail_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                var descm = 2;
                $('#edit_item_table_body').empty();
                var x = 2;
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                    $("#form_location").prop("disabled", false);
                    $("#to_location").prop("disabled", false);
                    $("#itemType").prop("disabled", false);
                    $("#transferType").prop("disabled", false);
                } else {
                    $("#form_location").prop("disabled", true);
                    $("#to_location").prop("disabled", true);
                    $("#itemType").prop("disabled", true);
                    $("#transferType").prop("disabled", true);
                    $.each(data['details'], function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $umo_arr, '', 'class="form-control select2 umoDropdown"  required')) ?>';
                        var Segment = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="seg_\'+key+\'"'), form_dropdown('a_segment[]', $segment_arr, '', 'class="form-control select2 segment"  required onchange="load_segmentBase_projectID_item(this)"')) ?>';
                        var string = '<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" class="form-control search f_search" name="search[]" placeholder="Item ID,Item Description..." id="f_search_'+ x +'"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" value="' + value['itemAutoID'] + '"> <input type="hidden" class="form-control stockTransferDetailsID" name="stockTransferDetailsID[]" value="' + value['stockTransferDetailsID'] + '"> <input type="hidden" class="form-control currentStock" name="currentStock[]"></td> <td>'+ UOM +'</td> <td> <div class="input-group"> <input type="text" name="currentWareHouseStockQty[]"class="form-control currentWareHouseStock" value="' + value['wareHouseStock'] + '" required readonly> </div> </td> <td>'+ Segment +'</td> <td style="width: 100px"> <input type="text" name="transfer_QTY[]" onfocus="this.select();" onkeyup="checkCurrentStock(this)" value="' + value['transfer_QTY'] + '" class="form-control number transferqty"required> </td><td class="remove-td"><a onclick="delete_stock_transferDetailsEdit(' + value['stockTransferDetailsID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                        $('#edit_item_table_body').append(string);
                        //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                        $('#seg_'+key).val(value['segmentID'] + '|' + value['segmentCode']);
                        //fetch_related_uom_id( value['defaultUOMID'],value['unitOfMeasureID'],$('#uom_'+key));
                        $('#uom_'+key).closest('tr').find('.umoDropdown').empty();
                        var mySelect = $('#uom_'+key).parent().closest('tr').find('.umoDropdown');
                        mySelect.append($('<option></option>').val('').html('Select  UOM'));
                        if (!jQuery.isEmptyObject(data['alluom'])) {
                            $.each(data['alluom'], function (val, text) {
                                if(value['defaultUOMID']==text['masterUnitID']){
                                    mySelect.append($('<option></option>').val(text['UnitID']).html(text['UnitShortCode'] + ' | ' + text['UnitDes']));
                                }

                            });
                            if (value['unitOfMeasureID']) {
                                $('#uom_'+key).closest('tr').find('.umoDropdown').val(value['unitOfMeasureID']);
                            }
                        }
                        initializeitemTypeahead(type, x);
                        x++;
                    });
                    search_id=x-1;
                    $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                }
                $.each(itemAutoIdArr, function (key, valu) {
                    $('.itemAutoID').each(function () {
                        if(this.value == valu['itemAutoID']){
                            $(this).closest('tr').css("background-color",'#ffb2b2');
                        }
                    });
                });

                /*$('.itemAutoID').each(function () {
                    if(this.value == ''){
                        $(this).closest('tr').css("background-color",'#ffb2b2');
                    }

                });*/
                stopLoad();<!--Total-->

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    function validatetb_row(det) {
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }

    function validate_mfq_warehouse(warehouseAutoID)
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
                    $('#mfqWarehouseAutoID').val(data['mfqWarehouseAutoID']);
                    $('.div_job_number').html(data['dropdown']);
                    $('.select2').select2();
                    $('#jobNumberAdd').removeClass('hidden');
                    $('#jobID').val('').change();
                    $('#jobID').attr('onchange', 'fetchReference_number()');
                } else {
                    $('#jobNumberAdd').addClass('hidden');
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function save_mr_base_ST_items()
    {
        var qty = [];
        var mrDetailID = [];
        $('#table_body_mr_detail input:hidden').each(function () {
            mrDetailID.push($(this).val());
            qty.push($('#qtyIssued_' + $(this).val()).val());
        });
        if (!jQuery.isEmptyObject(mrDetailID)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'mrDetailID': mrDetailID, 'qty': qty, 'stockTransferAutoID': stockTransferAutoID},
                url: "<?php echo site_url('Inventory/save_mr_base_ST_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        $('#mr_base_modal').modal('hide');
                        setTimeout(function () {
                            fetch_detail();
                        }, 300);
                    } else {
                        myAlert('w', data['data'], 1000);
                    }

                }, error: function () {
                    $('#mr_base_modal').modal('hide');
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function fetchReference_number()
    {
        var jobID = $('#jobID').val();
        if(referenceNo) {
            $('#referenceNo').val(referenceNo);
        } else if(jobID) {
            $('#referenceNo').val($('#jobID option:selected').text());
        }
    }
    function convertPrice(element) {
        var itemAutoID = $(element).closest('tr').find('.itemAutoID').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                'itemAutoID': itemAutoID,
                'uomID' : element.value,
                'wareHouseAutoID' : wareHouseAutoID,
                'id': stockTransferAutoID
            },
                
            //url: '<?php //echo site_url("Inventory/fetch_converted_price_qty_invoice"); ?>//',
            url: '<?php echo site_url("Inventory/fetch_converted_price_qty_invoice_new"); ?>',
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('.currentWareHouseStock').val(data['qty']);
                    $(element).closest('tr').find('.conversionRate').val(data['conversionRate']);
                    $(element).closest('tr').find('.currentstock_pulleddocument').val(data['qty_pulleddoc']);
                    $(element).closest('tr').find('.transferqty').val(' ');
                    $(element).closest('tr').find('.parkQty').val(data['parkQty']);
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
    function checkCurrentStock_unapproveddocument(det) {
        var currentStock = $(det).closest('tr').find('.currentWareHouseStock').val();
        var currentStock_pulled = $(det).closest('tr').find('.currentstock_pulleddocument').val();
        var category = $(det).closest('tr').find('.itemcatergory').val();
        var itemAutoID = $(det).closest('tr').find('.itemAutoID').val();
   
        var UoM =$(det).closest('tr').find('.umoDropdown option:selected').text().split('|');
        var conversionRate =$(det).closest('tr').find('.conversionRate').val();
        if(category !=='Service') {

        if(det.value > parseFloat(currentStock_pulled)){
            // document_by_warehouse_qty(itemAutoID,wareHouseAutoID,' ',stockTransferAutoID,UoM[0],conversionRate,parseFloat(currentStock))
            document_by_warehouse_qty(itemAutoID,wareHouseAutoID,' ',stockTransferAutoID,UoM[0],conversionRate,parseFloat(currentStock),stockTransferDetailsID)
                    $(det).val(0);
        }
        }
    }
    function convertPrice_edit(element) {
        var itemAutoID = $(element).closest('tr').find('#itemAutoID_edit').val();
      
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                'itemAutoID': itemAutoID,
                'uomID' : element.value,
                'wareHouseAutoID' : wareHouseAutoID,
                'id': stockTransferAutoID,
                'documentcode':'ST',
                'detailID':stockTransferDetailsID

            },
            //url: '<?php //echo site_url("Inventory/fetch_converted_price_qty_invoice"); ?>//',
            url: '<?php echo site_url("Inventory/fetch_converted_price_qty_invoice_new"); ?>',
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {
                    $(element).closest('tr').find('#currentWareHouseStockQty_edit').val(data['qty']);
                  
                    $(element).closest('tr').find('#conversionRateEdit').val(data['conversionRate']);
                    $(element).closest('tr').find('#pulledcurrentstock_edit').val(data['qty_pulleddoc']);
                    $(element).closest('tr').find('#transferqty_edit').val(' ');
                    $(element).closest('tr').find('#parkQty_edit').val(data['parkQty']);

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
    function checkCurrentStockEditunapproveddocument() {
        var currentStock = $('#currentWareHouseStockQty_edit').val();
        var TransferQty = $('#transferqty_edit').val();
        var mainCategory = $('#itemcatergory_Edit').val();
        var currentStock_pulled = $('#pulledcurrentstock_edit').val();
        var itemAutoID = $('#itemAutoID_edit').val();
        // var wareHouseAutoID = warehouseID;
        var wareHouseAutoID=$('#form_location').val();
        var UoM =$('#UnitOfMeasureID_edit option:selected').text().split('|');
        var conversionRate =$('#conversionRateEdit').val();
        if(mainCategory !=='Service'){
            if (parseFloat(TransferQty) > parseFloat(currentStock_pulled)) {
                // document_by_warehouse_qty(itemAutoID,wareHouseAutoID,' ',itemIssueAutoID,UoM[0],conversionRate,parseFloat(currentStock))
                document_by_warehouse_qty(itemAutoID,wareHouseAutoID,'ST',stockTransferAutoID,UoM[0],conversionRate,parseFloat(currentStock),stockTransferDetailsID)
                $('#transferqty_edit').val(0);
                $('#transferqty_edit').val(0);
            }
        }
    }

    function setQty(mrDetailID, balanceQTY, currentStock){
        var ordQtyId = "#qtyIssued_"+mrDetailID;
        $(ordQtyId).val(currentStock);
        check_qty_available(mrDetailID, balanceQTY, currentStock);
    }

    function table_fetch() {
        setTimeout(function(){
            fetch_detail();
        }, 300);
    }
</script>