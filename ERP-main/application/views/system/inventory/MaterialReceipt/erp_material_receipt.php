<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$customer_arr = all_employee_drop(TRUE,0,1);
$location_arr = all_delivery_location_drop_active();
$location_arr_default = default_delivery_location_drop();
$segment_arr = fetch_segment();
$segment_arr_default = default_segment_drop();
$financeyear_arr = all_financeyear_drop(true);
$unitOfMeasure_arr = array('' => 'Select UOM');
$projectExist = project_is_exist();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$jobNumberMandatory = getPolicyValues('JNP', 'All');
$itemBatch = getPolicyValues('IB', 'All');
$jobs = all_mfq_jobs_drop(TRUE);
$pID = $this->input->post('page_id');
$user_id = current_userID();
$purchaseOrderList = get_purchase_order_list(1);

if($pID != '') {
    
    $Documentid = 'MRN';
    $warehouseidcurrentdoc = all_warehouse_drop_isactive_inactive($pID,$Documentid);
    if(!empty($warehouseidcurrentdoc) && $warehouseidcurrentdoc['isActive'] == 0)
    {
        $location_arr[trim($warehouseidcurrentdoc['wareHouseAutoID'] ?? '')] = trim($warehouseidcurrentdoc['wareHouseCode'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseLocation'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseDescription'] ?? '');
    }
}
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

    .flex{ display:flex }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

        <div class="steps">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one'); ?>
        - <?php echo $this->lang->line('transaction_mrn_header'); ?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_material_receipt_detail();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_two'); ?>
        - <?php echo $this->lang->line('transaction_mrn_detail'); ?></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_three'); ?>
        - <?php echo $this->lang->line('transaction_mrn_confirmation'); ?> </span>
            </a>
           
        </div>

  
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="material_receipt_form"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label
                    for="receiptType"><?php echo $this->lang->line('common_type'); ?><?php required_mark(); ?></label>
                <!--Type-->
                <?php echo form_dropdown('receiptType', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'Direct Issue' => $this->lang->line('transaction_direct'), 'Material Request' => $this->lang->line('transaction_material_request_or_bulk_transfer') /*'Material Request / Bulk Transfer'*/), 'Direct Issue', 'class="form-control select2" id="receiptType" required'); ?>
            </div>
            <div class="form-group col-sm-4 requestedSegmentDiv">
                <label
                    for="segment"><?php echo $this->lang->line('transaction_primary_segment'); ?><?php required_mark(); ?></label>
                <!--Primary Segment-->
                <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default  , 'class="form-control select2" id="segment" '); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('transaction_common_referenc_no'); ?> </label>
                <!--Reference No-->
                <input type="text" class="form-control " id="issueRefNo" name="issueRefNo">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label
                    for="SalesPersonName"><?php echo $this->lang->line('transaction_common_received_by'); ?><?php required_mark(); ?></label>
                <!--Received By-->
                <div class="input-group">
                    <input type="text" class="form-control" id="employeeName" name="employeeName" value="<?php echo isset($customer_arr[$user_id]) ? $customer_arr[$user_id] : '' ?>" required>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                onclick="clearEmployee()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-repeat"></i></button>
                        <button class="btn btn-default" type="button" title="Add Employee" rel="tooltip"
                                onclick="link_employee_model()"
                                style="height: 29px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                    </span>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label
                    for=""><?php echo $this->lang->line('transaction_date_received'); ?><?php required_mark(); ?></label>
                <!--Date Received-->

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="receivedDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="receivedDate"
                           class="form-control" required>
                </div>
            </div>
            <div class="form-group col-sm-4">
                <label
                    for=""><?php echo $this->lang->line('transaction_common_warehouse_location'); ?><?php required_mark(); ?></label>
                    <input class="hidden" name="mfqWarehouseAutoID" id="mfqWarehouseAutoID">
                <!--Warehouse Location-->
                <?php echo form_dropdown('location', $location_arr, $location_arr_default, 'class="form-control select2" id="location" onchange="validate_mfq_warehouse(this.value)" required'); ?>
            </div>
        </div>
        <div class="row">
            <?php
            if($financeyearperiodYN==1){
            ?>
            <div class="form-group col-sm-4">
                <label
                    for="financeyear"><?php echo $this->lang->line('transaction_common_financial_year'); ?><?php required_mark(); ?></label>
                <!--Financial Year-->
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label
                    for="financeyear"><?php echo $this->lang->line('transaction_common_financial_period'); ?><?php required_mark(); //?></label>
                <!--Financial Period-->
                <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
                <?php
            }
            ?>
            <div class="form-group col-sm-4">
                <label
                    for="itemType"><?php echo $this->lang->line('transaction_item_type'); ?><?php required_mark(); ?></label>
                <!--Item Type-->
                <?php echo form_dropdown('itemType', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'Inventory' => $this->lang->line('transaction_inventory')/*'Inventory'*/, 'Non Inventory' => $this->lang->line('transaction_non_inventory')/*'Non Inventory'*/), 'Inventory', 'class="form-control select2" id="itemType" required'); ?>
            </div>
        </div>
        <div class="row">
            <?php
            if($jobNumberMandatory==1){
                ?>
                <div class="form-group col-sm-4 hidden" id="jobNumberAdd">
                    <label for="financeyear"><?php echo $this->lang->line('inventory_job_number');?><?php required_mark(); ?></label><!--Job Number-->
                    <div class="div_job_number">
                        <select name="projectID" class="form-control select2">
                            <option value="">
                                Select Job Number</option>
                        </select>
                    </div>
                    <!--                    --><?php //echo form_dropdown('jobID', $jobs, '', 'class="form-control select2" id="jobID"'); ?>
                </div>
            <?php } ?>

            <div class="form-group col-sm-4 purchaseOrderDiv hide" id="purchaseOrderDiv">
                <label
                    for="purchaseOrder"><?php echo $this->lang->line('transaction_po'); ?><?php required_mark(); ?></label>
                <?php echo form_dropdown('purchaseOrderID', $purchaseOrderList, '', 'class="form-control select2" id="purchaseOrderID" onchange="get_purchase_order_details(this)" '); ?>
            </div>

            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('transaction_common_narration'); ?> </label><!--Narration-->
                <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary"
                    type="submit"><?php echo $this->lang->line('common_save_and_next'); ?>  </button><!--Save & Next-->
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i
                        class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('transaction_common_add_item_detail'); ?>
                </h4></div><!--Add Item Detail-->
            <div class="col-md-4">
                <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right"><i
                        class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item'); ?>
                </button><!--Add Item-->
            </div>
        </div>
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%" class="mrBaseDiveTable "><?php echo $this->lang->line('transaction_material_issue_code'); ?><!--MI Code--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code'); ?> </th>
                <?php if($itemBatch == 1){ ?>
                    <th>Batch Number </th><!--Code-->
                <?php } ?>
                <!--Item Code-->
                <th style="min-width: 30%"><?php echo $this->lang->line('transaction_common_item_description'); ?> </th>
                <!--Item Description-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_uom'); ?></th><!--UOM-->
                <!-- <th style="min-width: 10%">Requested</th> -->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_qty'); ?> </th><!--QTY-->
                <th style="min-width: 10%"><?php echo $this->lang->line('inventory_wac'); ?> </th><!--WAC-->
                <th style="min-width: 15%"><?php echo $this->lang->line('common_total_value'); ?>
                    (<?php echo $this->common_data['company_data']['company_default_currency']; ?>)
                </th><!--Value-->
                <th style="min-width: 10%">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="item_table_body">
            <tr class="danger">
                <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?> </b>
                </td><!--No Records Found-->
            </tr>
            </tbody>
            <tfoot id="table_tfoot">

            </tfoot>
        </table>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous'); ?> </button>
            <!--Previous-->
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title"
                id="Material_issue_attachment_label"><?php echo $this->lang->line('transaction_goods_received_voucher_modal_title'); ?> </h4>
            <!--Modal title-->
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?> </th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description'); ?> </th><!--Description-->
                        <th><?php echo $this->lang->line('common_type'); ?> </th><!--Type-->
                        <th><?php echo $this->lang->line('common_action'); ?> </th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="Material_issue_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5"
                            class="text-center"><?php echo $this->lang->line('common_no_attachment_found'); ?> </td>
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
            <button class="btn btn-primary "
                    onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?> </button>
            <!--Save as Draft-->
            <button class="btn btn-success submitWizard"
                    onclick="confirmation()"><?php echo $this->lang->line('common_confirm'); ?> </button><!--Confirm-->
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="item_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('transaction_common_add_item_detail'); ?> </h5>
                <!--Add Item Detail-->
            </div>
            <div class="modal-body">
                <form role="form" id="item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="MaterialIssue_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;"><?php echo $this->lang->line('transaction_common_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_common_uom'); ?><?php required_mark(); ?></th>
                            <?php if($itemBatch == 1){ ?>
                                <th style="width: 250px;">Batch Number</th>
                                <th style="width: 150px;">Batch Expire Date</th>
                            <?php } ?>
                            <!--UOM-->
                            <th style="width:100px;"><?php echo $this->lang->line('transaction_current_stock'); ?><?php required_mark(); ?></th>
                            <!--Current Stock-->
                            <th style="width: 150px;"><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                            <!--Segment-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project'); ?></th><!--Project-->
                                <th><?php echo $this->lang->line('common_project_category'); ?><!-- Project Category --></th>
                                <th><?php echo $this->lang->line('common_project_subcategory'); ?><!-- Project Subcategory --></th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_received_qty'); ?><?php required_mark(); ?></th>
                            <!--Received Qty-->
                            <th style="width: 150px;"><?php echo $this->lang->line('common_unit_cost'); ?><?php required_mark(); ?></th>
                            <!--Unit Cost-->
                            <th style="width: 200px;"><?php echo $this->lang->line('common_comment'); ?> </th>
                            <!--Comment-->
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
                                <input type="text" onkeyup="clearitemAutoID(event,this)"
                                       class="form-control search f_search"
                                       name="search[]"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('transaction_common_item_description'); ?>..."
                                       id="f_search_1"><!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                <input type="hidden" class="form-control currentStock" name="currentStock">
                            </td>
                            <td>
                                <?php echo form_dropdown('UnitOfMeasureID[]', $unitOfMeasure_arr, '', 'class="form-control umoDropdown"  required'); ?>
                            </td>
                            <?php if($itemBatch == 1){ ?>
                            <!-- <td>
                               <?php //echo form_dropdown('batch_number[0][]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_1" multiple="multiple" required'); ?>
                            </td> -->

                            <td>
                                <div class="input-group flex">
                                    <input type="text" name="batchNumber[]" onkeyup=""
                                            onchange="changeBatchNumber($(this))" onfocus=""
                                            class="form-control batchNumber" disabled required>
                                    <button class="btn btn-primary btnViewBatch hide" onclick="load_ex_batch_modal_data($(this))" data-toggle="modal" data-target="#existing_batch_details"><i class="fa fa-cog"></i></button>
                                </div>
                            </td>

                            <td><input type="date" name="expireDate[]" onkeyup=""
                                       onchange="" onfocus="" disabled
                                       class="form-control  expireDate" required></td>

                            <?php } ?>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="currentWareHouseStockQty[]"
                                           class="form-control currentWareHouseStock" required readonly>
                                </div>
                            </td>
                            <td>
                                <?php echo form_dropdown('a_segment[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment" required onchange="load_segmentBase_projectID_item(this)"'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div class="div_projectID_income">
                                        <select name="projectID" class="form-control select2">
                                            <option
                                                value=""><?php echo $this->lang->line('common_select_project'); ?> </option>
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
                            <td style="width: 100px">
                                <input type="text" name="quantityRequested[]" onfocus="this.select();"
                                       onkeyup="validatetb_row(this)"
                                       class="form-control number quantityRequested" required>
                            </td>
                            <td style="width: 100px">
                                <input type="text" name="unitCost[]" onfocus="this.select();"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number unitCost" required>
                            </td>
                            <td>
                                <textarea class="form-control" rows="1" name="comment[]"
                                          placeholder="<?php echo $this->lang->line('transaction_common_item_comment'); ?>..."></textarea>
                                <!--Item Comment-->
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="saveMaterialReceipt_addItem()"><?php echo $this->lang->line('common_save_change'); ?>
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
                <h4 class="modal-title"><?php echo $this->lang->line('transaction_common_edit_item_detail'); ?> </h4>
                <!--Edit Item Detail-->
            </div>
            <div class="modal-body">
                <form role="form" id="edit_item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="MaterialIssue_detail_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;"><?php echo $this->lang->line('transaction_common_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th style="width: 150px;" ><?php echo $this->lang->line('common_uom'); ?><?php required_mark(); ?></th>
                            <!--UOM-->
                            <?php if($itemBatch == 1){ ?>
                               <th style="width: 250px;">Batch Number</th>
                                <th style="width: 150px;">Batch Expire Date</th>
                            <?php } ?>
                            <th style="width:100px;"><?php echo $this->lang->line('transaction_current_stock'); ?><?php required_mark(); ?></th>
                            <!--Current Stock-->
                            <th style="width: 150px;" class="mitypecoloumn"><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                            <!--Segment-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project'); ?></th><!--Project-->
                                <th><?php echo $this->lang->line('common_project_category'); ?><!-- Project Category --></th>
                                <th><?php echo $this->lang->line('common_project_subcategory'); ?><!-- Project Subcategory --></th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_received_qty'); ?><?php required_mark(); ?></th>
                            <!--Received Qty-->
                            <th style="width: 150px;" class="mitypecoloumn"><?php echo $this->lang->line('common_unit_cost'); ?><?php required_mark(); ?></th>
                            <!--Unit Cost-->
                            <th style="width: 200px;"><?php echo $this->lang->line('common_comment'); ?> </th>
                            <!--Comment-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" class="form-control"
                                       id="search"
                                       name="search"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>, <?php echo $this->lang->line('common_item_description'); ?>...">
                                <!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control" id="itemAutoID_edit" name="itemAutoID">
                                <input type="hidden" class="form-control" id="currentStock_edit" name="currentStock">
                                <input type="hidden" class="form-control" id="mrMasterID_edit_frt" name="mrMasterID">
                                <input type="hidden" class="form-control" id="mrnDetailID_edit" name="mrnDetailID">
                            </td>
                            <td>
                                <?php echo form_dropdown('unitOfMeasureID', $unitOfMeasure_arr, '', 'class="form-control" id="UnitOfMeasureID_edit"  required'); ?>
                            </td>
                            
                            <?php if($itemBatch == 1){ ?>
                            
                            <td>
                                <div class="input-group flex">
                                    <input type="text" name="batchNumber[]" onkeyup=""
                                            onchange="changeBatchNumber($(this))" onfocus=""
                                            class="form-control batchNumber_edit" required>
                                    <button class="btn btn-primary btnViewBatch hide" onclick="load_ex_batch_modal_data($(this))" data-toggle="modal" data-target="#existing_batch_details"><i class="fa fa-cog"></i></button>
                                </div>
                            </td>

                            <td><input type="date" name="expireDate[]" onkeyup=""
                                       onchange="" onfocus=""
                                       class="form-control  expireDate_edit" required></td>

                            <?php } ?>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="currentWareHouseStockQty"
                                           class="form-control" id="currentWareHouseStockQty_edit" required readonly>
                                </div>
                            </td>
                            <td class="mitypecoloumn">
                                <?php echo form_dropdown('a_segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment_edit" onchange="load_segmentBase_projectID_itemEdit(this)"'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_item">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option
                                                value=""><?php echo $this->lang->line('common_select_project'); ?> </option>
                                            <!--Select Project-->
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
                                <input type="text" name="quantityRequested" onfocus="this.select();"
                                       class="form-control number" onkeyup="chkbalanceqty()" id="quantityRequested_edit" required>
                            </td>
                            <td style="width: 100px" class="mitypecoloumn">
                                <input type="text" name="unitCost" onfocus="this.select();"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       class="form-control number" id="unitCost_edit" required>
                            </td>
                            <td>
                                <textarea class="form-control" rows="1" name="comment"
                                          placeholder="<?php echo $this->lang->line('transaction_common_item_comment'); ?>..."
                                          id="comment_edit"></textarea><!--Item Comment-->
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="updateMaterialIssue_addItem()"><?php echo $this->lang->line('common_update_changes'); ?>
                </button><!--Update changes-->
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="emp_model" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"
                    id="exampleModalLabel"><?php echo $this->lang->line('transaction_link_employee'); ?> </h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3"
                               class="col-sm-3 control-label"><?php echo $this->lang->line('common_employee'); ?> </label>
                        <!--Employee-->
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('employee_id', $customer_arr, '', 'class="form-control select2" id="employee_id" required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                <button type="button" class="btn btn-primary"
                        onclick="fetch_employee_detail()"><?php echo $this->lang->line('common_add_employee'); ?> </button>
                <!--Add employee-->
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
                <h5 class="modal-title"><?php echo $this->lang->line('transaction_gl_account'); ?> </h5>
                <!--GL Account-->
            </div>
            <div class="modal-body" id="divglAccount">
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="materialAccountUpdate(1)"><?php echo $this->lang->line('transaction_apply_to_all'); ?>
                </button><!--Apply to All-->
                <button class="btn btn-primary" type="button"
                        onclick="materialAccountUpdate(0)"><?php echo $this->lang->line('common_save_change'); ?>
                </button><!--Save changes-->

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
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('transaction_material_request_base'); ?><!--Material Request Base--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="box box-widget widget-user-2">
                            <div class="widget-user-header bg-yellow">
                                <h5><?php echo $this->lang->line('transaction_material_issue'); ?><!--Material Issue--></h5>
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
                                <th><?php echo $this->lang->line('common_code'); ?><!--Code--></th>
                                <th class="text-left"><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                <th><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                                <th class="transferColumn hidden">Transfer Qty</th>
                                <th class="issueColumn"><?php echo $this->lang->line('transaction_requested_qty'); ?><!--Requested Qty--></th>
                                <th class="issueColumn"><?php echo $this->lang->line('transaction_material_issued_qty'); ?><!--Material Issued Qty--></th>
                                <th><?php echo $this->lang->line('transaction_received_qty'); ?><!--Received Qty--></th>
                                <th><?php echo $this->lang->line('common_balance_qty'); ?><!--Balance Qty--></th>
                                <!--<th>Current Qty</th>-->
                                <th><?php echo $this->lang->line('common_qty'); ?><!--Qty--></th>
                            </tr>
                            </thead>
                            <tbody id="table_body_mr_note_detail">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_close'); ?><!--Close--></button>
                <button type="button" id="mrbasebtn" class="btn btn-primary" onclick="save_mr_note_base_items()"><?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="existing_batch_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('transaction_material_request_base'); ?><!--Material Request Base--></h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <th></th>
                        <th></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Batch Code</td>
                            <td><span id="ex_batch_code"></span></td>
                        </tr>
                        <tr>
                            <td>Expire Date</td>
                            <td><span id="ex_expire_date"></span></td>
                        </tr>
                        <tr>
                            <td>Warehouse Name</td>
                            <td><span id="ex_warehouse"></span></td>
                        </tr>
                        <tr>
                            <td>Current Quentity</td>
                            <td><span id="ex_current_qty"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_close'); ?><!--Close--></button>
               
            </div>
        </div>
    </div>
</div>
<?php
/** sub item master modal created by Shafry */
$this->load->view('system/grv/sub-views/inc-sub-item-master');
?>
<script type="text/javascript">
    var search_id = 1;
    var type;
    var mrnAutoID;
    var mrnDetailID;
    var EIdNo;
    var ECode;
    var projectID;
    var materialReceiptNoteType;
    var Primary_Segment;
    var projectcategory;
    var projectsubcat;
    var ex_batch_details = new Array();

    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.headerclose').click(function () {
            fetchPage('system/inventory/MaterialReceipt/material_receipt_note', mrnAutoID, 'Material Receipt Note');
        });
        $('.select2').select2();
        type = 'Inventory';
        EIdNo = null;
        projectID = null;
        mrnAutoID = null;
        mrnDetailID = null;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#material_receipt_form').bootstrapValidator('revalidateField', 'receivedDate');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
        currency_validation_modal(CurrencyID, 'MRN', '', '');
        if (p_id) {
            mrnAutoID = p_id;
            load_material_receipt_header();
            $("#a_link").attr("href", "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>/" + mrnAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_material_receipt'); ?>/" + mrnAutoID + '/MRN');
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

        number_validation();
        $('#material_receipt_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                receiptType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_type_is_required');?>.'}}}, /*Type is required*/
                //segment: {validators: {notEmpty: {message: '<?php //echo $this->lang->line('common_segment_is_required');?>.'}}}, /*Segment is required*/
                /*employeeID: {validators: {notEmpty: {message: 'Employee is required.'}}},*/
                employeeName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_employee_is_required');?>.'}}}, /*Employee is required*/
                receivedDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_received_date_is_required');?>.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#receiptType").prop("disabled", false);
            $("#location").prop("disabled", false);
            $("#itemType").prop("disabled", false);
            $("#segment").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'employeeID', 'value': EIdNo});
            data.push({'name': 'mrnAutoID', 'value': mrnAutoID});
            data.push({'name': 'requested', 'value': $('#employee_id option:selected').text()});
            data.push({'name': 'location_dec', 'value': $('#location option:selected').text()});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'jobNumber', 'value': $('#jobID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MaterialReceiptNote/save_material_receipt_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        mrnAutoID = data['last_id'];
                        materialReceiptNoteType = data['receiptType'];
                        Primary_Segment = $("#segment").val();
                        $("#a_link").attr("href", "<?php echo site_url('MaterialReceiptNote/load_material_issue_conformation'); ?>/" + mrnAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + mrnAutoID + '/MI');
                        if (materialReceiptNoteType == 'Material Request') {
                            $("#segment").prop("disabled", true);
                        }else{
                            $("#segment").prop("disabled", false);
                        }
                        fetch_material_receipt_detail();
                        $('[href=#step2]').tab('show');
                    }
                    stopLoad();
                    type = $('#itemType').val();
                    /*$('#search').typeahead('destroy');
                     initializeitemTypeahead(type);*/
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

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID,'fyDepartmentID':'MRN'},
            url: "<?php echo site_url('Dashboard/fetch_finance_year_period'); ?>",
            success: function (data) {
                $('#financeyear_period').empty();
                var mySelect = $('#financeyear_period');
                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('transaction_select_financial_period');?>'));
                /*Select  Financial Period*/
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
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>MaterialReceiptNote/fetch_inv_item_stock_adjustment/?&t=' + type + '&mrnAutoID=' + mrnAutoID,
            onSelect: function (suggestion) {

                var cont = true;
                // $('.itemAutoID').each(function () {
                //     if (this.value) {
                //         if (this.value == suggestion.itemAutoID) {
                //             $('#f_search_' + id).val('');
                //             $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                //             $('#f_search_' + id).closest('tr').find('.currentWareHouseStock').val('');
                //             $('#f_search_' + id).closest('tr').find('.quantityRequested').val('');
                //             $('#f_search_' + id).closest('tr').find('.unitCost').val('');
                //             myAlert('w', 'Selected item is already selected');
                //             cont = false;
                //         }
                //     }
                // });


                if (cont) {//$(this).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    }, 200);

                    if(itemBatchPolicy==1){
                        var warehouseIDSelect = $('#location').val();
                        if(warehouseIDSelect!=''){
                            getItemBatchDetails(suggestion.itemAutoID,id,warehouseIDSelect);
                        }
                        
                    }
                    $(this).closest('tr').find('.currentStock').val(suggestion.currentStock);
                    $(this).closest('tr').find('.d_uom').text(suggestion.defaultUnitOfMeasure);
                    $(this).closest('tr').find('.unitCost').val(suggestion.companyLocalWacAmount);
                    $(this).closest('tr').find('.quantityRequested').focus();
                    $(this).closest('tr').css("background-color", 'white');
                    fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                    fetch_warehouse_item(suggestion.itemAutoID, this);
                }
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');

    }

    function initializeitemTypeahead_edit(type) {
        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>MaterialReceiptNote/fetch_inv_item_stock_adjustment/?&t=' + type + '&mrnAutoID=' + mrnAutoID,
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#quantityRequested_edit').val();
                    $('#itemAutoID_edit').val(suggestion.itemAutoID);
                    $('#currentStock_edit').val(suggestion.currentStock);
                    $('#d_uom_edit').val(suggestion.defaultUnitOfMeasure);
                    $(this).closest('tr').find('#quantityRequested_edit').focus();
                }, 200);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                fetch_warehouse_item_edit(suggestion.itemAutoID);
            }
        });
    }

    function fetch_warehouse_item(itemAutoID, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'mrnAutoID': mrnAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('MaterialReceiptNote/fetch_warehouse_item'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $(element).closest('tr').find('.currentWareHouseStock').val(data['currentStock']);
                } else {
                    $(element).closest('tr').find('.currentWareHouseStock').val(0);
                    //$(element).closest('tr').find('.itemAutoID').val('');
                }
                
                $(element).closest('tr').find('.batchNumber').prop('disabled',false);
                $(element).closest('tr').find('.expireDate').prop('disabled',false);

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
            data: {'mrnAutoID': mrnAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('MaterialReceiptNote/fetch_warehouse_item'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $('#currentWareHouseStockQty_edit').val(data['currentStock']);
                } else {
                    $('#search').typeahead('val', '');
                    $('#itemAutoID_edit').val('');
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

    function load_material_receipt_header() {
        if (mrnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'mrnAutoID': mrnAutoID},
                url: "<?php echo site_url('MaterialReceiptNote/load_material_receipt_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#receiptType').val(data['receiptType']).change();
                        materialReceiptNoteType = data['receiptType'];
                        $('#receivedDate').val(data['receivedDate']);
                        if(data['receiptType']=='Material Request'){

                        }else{
                            $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            Primary_Segment = data['segmentID'] + '|' + data['segmentCode'];
                        }

                        $('#narration').val(data['comment']);
                        $('#location').val(data['wareHouseAutoID']).change();
                        $('#purchaseOrderID').val(data['purchaseOrderID']).change();
                        $('#mfqWarehouseAutoID').val(data['mfqWarehouseAutoID']);
                        setTimeout(function () {
                            $("#jobID").val(data['jobID']).change();
                        }, 500);
                        $('#contactPersonName').val(data['contactPersonName']);
                        $('#contactPersonNumber').val(data['contactPersonNumber']);
                        if (data["employeeID"] > 0) {
                            $('#employeeName').prop('readonly', true);
                            $('#employeeName').val(data['employeeCode'] + ' | ' + data['employeeName']);
                        } else {
                            $('#employeeName').val(data['employeeName']);
                        }
                        $('#employee_id').val(data['employeeID']).change();
                        EIdNo = data['employeeID'];
                        ECode = data['employeeCode'];
                        $('#financeyear').val(data['companyFinanceYearID']);
                        $('#issueRefNo').val(data['RefNo']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        fetch_material_receipt_detail();
                        $('#itemType').val(data['itemType']).change();
                        type = data['itemType'];
                        $('#search').typeahead('destroy');
                        initializeitemTypeahead(type);
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                    stopLoad();
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

    function fetch_material_receipt_detail() {
        var receiptType = $('#receiptType').val();
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'mrnAutoID': mrnAutoID},
            url: "<?php echo site_url('MaterialReceiptNote/fetch_material_receipt_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                $('#item_table_body').empty();
                x = 1;
                var currency_decimal = <?php echo json_encode($this->common_data['company_data']['company_default_decimal']); ?>;
                if (jQuery.isEmptyObject(data['detail'])) {

                    $('#item_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                    <!--No Records Found-->
                    $("#receiptType").prop("disabled", false);
                    $("#location").prop("disabled", false);
                    $("#itemType").prop("disabled", false);
                } else {
                    $("#receiptType").prop("disabled", true);
                    $("#location").prop("disabled", true);
                    $("#itemType").prop("disabled", true);

                    $.each(data['detail'], function (key, value) {
                        if (value['isSubitemExist'] == 1) {
                            var colour = 'color: #dad835 !important';
                            colour = '';
                            if(itemBatchPolicy==1){
                                string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['itemIssueCode'] + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber']+ '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['qtyReceived'] + '</td><td class="text-right">' + parseFloat(value['unitCost']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_modal(' + value['mrnDetailID'] + ',\'MRN\')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="edit_item(' + value['mrnDetailID'] + ',this);"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['mrnDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                            }else{
                                string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['itemIssueCode'] + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['qtyReceived'] + '</td><td class="text-right">' + parseFloat(value['unitCost']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_modal(' + value['mrnDetailID'] + ',\'MRN\')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="edit_item(' + value['mrnDetailID'] + ',this);"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['mrnDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                            }
                            
                        } else {
                            if(itemBatchPolicy==1){
                                string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['itemIssueCode'] + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber']+ '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['qtyReceived'] + '</td><td class="text-right">' + parseFloat(value['unitCost']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"> <a onclick="edit_item(' + value['mrnDetailID'] + ',this);"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['mrnDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                            }else{
                                string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['itemIssueCode'] + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['qtyReceived'] + '</td><td class="text-right">' + parseFloat(value['unitCost']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"> <a onclick="edit_item(' + value['mrnDetailID'] + ',this);"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['mrnDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>'; 
                            }
                            
                        }
                        $('#item_table_body').append(string);
                        total += parseFloat(value['totalValue']);
                        if (materialReceiptNoteType == 'Material Request') {
                            $("#segment").prop("disabled", true);
                        }else {
                            $("#segment").prop("disabled", false);
                        }

                            x++;
                    });
                }
                if(receiptType == 'Material Request'){
                    $('.mrBaseDiveTable').removeClass('hide');
                    $('#table_tfoot').html('<tr> <td class="text-right" colspan="7"><?php echo $this->lang->line('common_total');?></td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>');
                }else{
                    $('#table_tfoot').html('<tr> <td class="text-right" colspan="6"><?php echo $this->lang->line('common_total');?> </td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>');
                    $('.mrBaseDiveTable').addClass('hide');
                }
                <!--Total-->
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function materialAccountUpdate(all) {
        var $form = $('#stock_adjustment_gl_form');
        var data = $form.serializeArray();
        data.push({name: "applyAll", value: all});
        data.push({name: "masterID", value: mrnAutoID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MaterialReceiptNote/materialAccountUpdate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                fetch_material_receipt_detail();
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

    function edit_glaccount(mrnDetailID, PLGLAutoID, BLGLAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {PLGLAutoID: PLGLAutoID, BLGLAutoID: BLGLAutoID},
            url: "<?php echo site_url('MaterialReceiptNote/stockAdjustment_load_gldropdown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#divglAccount').html(data);
                $('#detailID').val(mrnDetailID);
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

    function item_detail_modal() 
    {
        var mfqWarehouse = $('#mfqWarehouseAutoID').val();
        var jobID = $('#jobID').val();
        if(mfqWarehouse != '' && jobID == '') {
            myAlert('e', 'Please Assign Job Before adding Details!');
        } else {
            if (mrnAutoID) {
                if (materialReceiptNoteType == 'Material Request') {
                    load_MR_codes();
                    $("#mr_base_modal").modal({backdrop: "static"});
                } else {
                    $('.search').typeahead('destroy');
                    $('#item_detail_form')[0].reset();
                    $('#MaterialIssue_detail_add_table tbody tr').not(':first').remove();
                    $('.search').typeahead('val', '');
                    $('.itemAutoID').val('');
                    $('.segment').val(Primary_Segment).change();
                    initializeitemTypeahead(type, 1);
                    $('#a_segment').val($('#segment').val());
                    $('.f_search').closest('tr').css("background-color", 'white');
                    $('.quantityRequested').closest('tr').css("background-color", 'white');
                    $("#item_detail_modal").modal({backdrop: "static"});
                }
            }
        }
    }

    function load_conformation() {
        if (mrnAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'mrnAutoID': mrnAutoID, 'html': true},
                url: "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>/" + mrnAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_material_receipt'); ?>/" + mrnAutoID + '/MRN');
                    attachment_modal_MaterialIssue(mrnAutoID, "<?php echo $this->lang->line('transaction_material_receipt_note');?>", "MRN");
                    /*Material Receipt Note*/
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
        if (mrnAutoID) {
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
                        data: {'mrnAutoID': mrnAutoID},
                        url: "<?php echo site_url('MaterialReceiptNote/material_item_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            }else if(data['error']==2) {
                                myAlert('w',data['message']);
                            }else if (data['error'] == 4){ 
                                
                                $('#wac_minus_calculation_validation_body').empty();
                                x = 1;
                                if (jQuery.isEmptyObject(data['message'])) {
                                    $('#wac_minus_calculation_validation_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                               } else {
                                   $.each(data['message'], function (key, value) {
                          
                                    $('#wac_minus_calculation_validation_body').append('<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>'+value['itemName']+'</td><td>' + value['Amount'] + '</td></tr>');
                                   x++;
                               });
                               }
                                   $('#wac_minus_calculation_validation').modal('show');
                               } else {
                                myAlert('s', data['message']);
                                fetchPage('system/inventory/MaterialReceipt/material_receipt_note', mrnAutoID, 'Material Receipt Note');
                            }
                            /*if (data['status']) {


                             } else {
                             myAlert('w', data['data'], 1000);
                             }*/

                        }, error: function (xhr, textStatus, errorThrown) {
                            myAlert('e', 'Error (' + textStatus + ') : ' + xhr.responseText);
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (mrnAutoID) {
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
                    fetchPage('system/inventory/MaterialReceipt/material_receipt_note', mrnAutoID, 'Material Receipt Note');
                });
        }
    }

    function delete_item(id) {
        if (mrnAutoID) {
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
                        data: {'mrnDetailID': id},
                        url: "<?php echo site_url('MaterialReceiptNote/delete_material_item'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_material_receipt_detail();
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function edit_item(id,element) {
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        if (mrnAutoID) {
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
                        data: {'mrnDetailID': id},
                        url: "<?php echo site_url('MaterialReceiptNote/load_material_receipt_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            // if(itemBatchPolicy==1){
                            //     var textBatchData=data['batchNumber'];
                            //     var wareHouseAutoIDSelect=$('#location').val();
                            //     $.ajax({
                            //         async: true,
                            //         type: 'post',
                            //         dataType: 'json',
                            //         data: {'itemId': data.itemAutoID,'wareHouseAutoID': wareHouseAutoIDSelect},
                            //         url: "<?php //echo site_url('Inventory/fetch_batch_details_byId'); ?>",
                            //         success: function (data) {

                            //             $('#batch_number_edit').empty();
                            //             var mySelect = $('#batch_number_edit');
                            //             //mySelect.append($('<option></option>').val('').html('<?php echo "Select Batch"?>'));
                            //             /*Select batch*/
                            //             if (!jQuery.isEmptyObject(data)) {
                            //                 $.each(data, function (val, text) {
                            //                     mySelect.append($('<option></option>').val(text['batchNumber']).html(text['batchNumber']));
                            //                 });

                            //                 var optionsToSelect = textBatchData.split(",");
                            //                 var select = document.getElementById( 'batch_number_edit' );

                            //                 for ( var i = 0, l = select.options.length, o; i < l; i++ )
                            //                 {
                            //                     o = select.options[i];
                            //                     if ( optionsToSelect.indexOf( o.text ) != -1 )
                            //                     {
                            //                         o.selected = true;
                            //                     }
                            //                 }
                                            
                            //             }
                            //         }, error: function () {
                            //             swal("Cancelled", "Your " + value + " file is safe :)", "error");
                            //         }
                            //     });

                            // }

                            mrnDetailID = data['mrnDetailID'];
                            projectID = data['projectID'];
                            projectcategory = data['project_categoryID'];
                            projectsubcat = data['project_subCategoryID'];
                            $('#search').typeahead('destroy');
                            $('#search').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - "+data['seconeryItemCode']);
                            $('#itemAutoID_edit').val(data.itemAutoID);
                            $('#currentStock_edit').val(data.currentStock);
                            $('#mrMasterID_edit_frt').val(data.mrMasterID);
                            $('#mrnDetailID_edit').val(id);
                            $('#currentWareHouseStockQty_edit').val(data.currentWareHouseStock);
                            $('#d_uom_edit').text(data.defaultUOM);
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#quantityRequested_edit').val(data['qtyReceived']);
                            $('#unitCost_edit').val(data['unitCost']);
                            $('#segment_edit').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            $('#comment_edit').val(data['comments']);
                            initializeitemTypeahead_edit(type);
                            if (materialReceiptNoteType == 'Material Request') {
                                $('#search').attr('disabled',true);
                                $('#comment_edit').attr('readonly',true);
                                $('.mitypecoloumn').addClass('hidden');
                                $("#UnitOfMeasureID_edit").prop("disabled", true);
                            }else{
                                $('#search').attr('disabled',false);
                                $('#comment_edit').attr('readonly',false);
                                $('.mitypecoloumn').removeClass('hidden');
                                $("#UnitOfMeasureID_edit").prop("disabled", false);
                            }

                            if(itemBatchPolicy == 1){
                                 $('.batchNumber_edit').val(data.batchNumber);
                                 $('.expireDate_edit').val(data.batchExpireDate);
                            }
                           

                            $("#item_detail_modal_edit").modal({backdrop: "static"});
                            stopLoad();
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });
        }
    }

    function attachment_modal_MaterialIssue(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID,'confirmedYN': 0},
                success: function (data) {
                    $('#Material_issue_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "<?php echo $this->lang->line('common_attachments');?>");
                    <!--Attachments-->
                    $('#Material_issue_attachment').empty();
                    $('#Material_issue_attachment').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_Material_issue_attachement(mrnAutoID, DocumentSystemCode, myFileName) {
        if (mrnAutoID) {
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
                        data: {'attachmentID': mrnAutoID, 'myFileName': myFileName},
                        url: "<?php echo site_url('inventory/delete_material_Issue_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                attachment_modal_MaterialIssue(DocumentSystemCode, "Material issue", "MI");
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    var batch_number=0;

    function add_more_material_issue() {
        search_id += 1;
        batch_number += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#MaterialIssue_detail_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.batchNumber').prop('disabled', true);
        appendData.find('.expireDate').prop('disabled', true);
        appendData.find('.btnViewBatch').addClass('hide');

        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';

        if(itemBatchPolicy == 1){
            appendData.find('.b_number').empty();
            appendData.find('.b_number').attr('id', 'batch_number_' + search_id);
            appendData.find('.b_number').attr('name', 'batch_number[' + batch_number+'][]');
        }

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#MaterialIssue_detail_add_table').append(appendData);
        var lenght = $('#MaterialIssue_detail_add_table tbody tr').length - 1;
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        $('#f_search_' + search_id).closest('tr').find('.segment').val(Primary_Segment).change();
        $(".select2").select2();
        
        initializeitemTypeahead(type, search_id);
        number_validation();
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function saveMaterialReceipt_addItem() {
        var $form = $('#item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'mrnAutoID', 'value': mrnAutoID});
        data.push({'name': 'mrnDetailID', 'value': mrnDetailID});
        $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $('.quantityRequested').each(function () {
            if (this.value == '' || this.value == 0) {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MaterialReceiptNote/save_material_detail_multiple'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    mrnDetailID = null;
                    $('#item_detail_form')[0].reset();
                    setTimeout(function () {
                        fetch_material_receipt_detail(4);
                        $('#item_detail_modal').modal('hide');
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

    function updateMaterialIssue_addItem() {
        $("#UnitOfMeasureID_edit").prop("disabled", false);
        $('#search').attr('disabled',false);
        var $form = $('#edit_item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'mrnAutoID', 'value': mrnAutoID});
        data.push({'name': 'mrnDetailID', 'value': mrnDetailID});
        data.push({'name': 'materialReceiptNoteType', 'value': materialReceiptNoteType});
        data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MaterialReceiptNote/save_material_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    mrnDetailID = null;
                    $('#item_detail_form')[0].reset();
                    setTimeout(function () {
                        fetch_material_receipt_detail(4);
                        $('#item_detail_modal_edit').modal('hide');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }, 300);
                }else{
                    $("#UnitOfMeasureID_edit").prop("disabled", true);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function clearEmployee() {
        $('#employee_id').val('').change();
        $('#employeeName').val('').trigger('input');
        $('#employeeName').prop('readonly', false);
        EIdNo = null;
    }

    function link_employee_model() {
        /*$('#employee_id').val('').change();*/
        $('#emp_model').modal('show');
    }

    function fetch_employee_detail() {
        var employee_id = $('#employee_id').val();
        if (employee_id == '') {
            //swal("", "Select An Employee", "error");
            myAlert('e', '<?php echo $this->lang->line('transaction_select_an_employee');?>');
            /*Select An Employee*/
        } else {
            EIdNo = employee_id;
            var empName = $("#employee_id option:selected").text();
            /*  var empNameSplit = empName.split('|');*/
            $('#employeeName').val($.trim(empName)).trigger('input');
            $('#employeeName').prop('readonly', true);
            $('#emp_model').modal('hide');
        }
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
                /* if(projectID == 0){
                    projectID = '';
                } */
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

    function load_MR_codes() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'mrnAutoID': mrnAutoID},
            url: "<?php echo site_url('MaterialReceiptNote/fetch_material_issue_code'); ?>",
            success: function (data) {
                $('#mrcode').empty();
                $('#table_body_mr_note_detail').empty();
                var mySelect = $('#mrcode');
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, value) {
                       var balanceQTY = value['mrQty']-value['qtyReceived'];
                        if(value['documentID'] == 'STB'){
                            var id = 'pull-'+value['documentAutoID'];
                            mySelect.append('<li id="'+id+'" title="STB Date :- ' + value['documentDate'] + '"  rel="tooltip" class="pull-li"><a onclick="fetch_mr_detail_table(' + value['documentAutoID'] + ', \'' + value['documentID'] + '\', ' + value['wareHouseAutoID'] + ')">' + value['documentCode'] + ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>');
                            $("[rel=tooltip]").tooltip();
                        } else {
                            var id = 'pull-'+value['documentAutoID'];
                            mySelect.append('<li id="'+id+'" title="MR Date :- ' + value['documentDate'] + ' Requested By:- ' + value['name'] + '"  rel="tooltip" class="pull-li"><a onclick="fetch_mr_detail_table(' + value['documentAutoID'] + ', \'' + value['documentID'] + '\', ' + value['wareHouseAutoID'] + ')">' + value['documentCode'] + ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>');
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

    function fetch_mr_detail_table(itemIssueAutoID, documentID, wareHouseAutoID) {
        $('.pull-li').removeClass('pulling-based-li');
        $('#pull-'+itemIssueAutoID).addClass('pulling-based-li');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemIssueAutoID': itemIssueAutoID, 'documentID': documentID, 'wareHouseAutoID': wareHouseAutoID},
            url: "<?php echo site_url('MaterialReceiptNote/fetch_material_issue_detail_table'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#table_body_mr_note_detail').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#table_body_mr_note_detail').append('<tr class="danger"><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    //transferColumn
                    //issueColumn
                    tot_amount = 0;
                    var issuedQTY = 0;
                    var balanceQTY = 0;
                    var issuedQTYs;
                    if (data['documentID'] == 'MI') {
                        $('.transferColumn').addClass('hidden');
                        $('.issueColumn').removeClass('hidden');
                        $.each(data['detail'], function (key, value) {
                            if (value['qtyReceived']) {
                                issuedQTY = value['qtyReceived'];
                            } else {
                                issuedQTY = 0;
                            }
                            balanceQTY = value['balQTY'];
                            issuedQTYs = '<input type="text" class="number" size="10" id="qtyIssued_' + value['itemIssueDetailID'] + '" value="0" onkeyup="check_qty_available(' + value['itemIssueDetailID'] + ',' + balanceQTY + ',' + value['stock'] + ')" > <input class="checkbox" id="check_' + value['itemIssueDetailID'] + '" type="hidden" value="' + value['itemIssueDetailID'] + '"><input class="hide" id="docID" value="' + data['documentID'] + '">';
                            $('#table_body_mr_note_detail').append('<tr>' +
                                    '<td>' + x + '</td>' +
                                    '<td>' + value['itemSystemCode'] + '</td>' +
                                    '<td>' + value['itemDescription'] + '</td>' +
                                    '<td class="text-center">' + value['unitOfMeasure'] + '</td>' +
                                    '<td class="text-right">' + (value['qtyRequested'] ) + '</td>' +
                                    '<td class="text-right">' + (value['qtyIssued'] ) + '</td>' +
                                    '<td class="text-right">' + issuedQTY + '</td>' +
                                    '<td class="text-right">' + balanceQTY + ' <i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' + value['itemIssueDetailID'] + ',' + balanceQTY + ',' + value['stock'] + ')" aria-hidden="true"></i></td>' +
                                    '<td class="text-right">' + issuedQTYs + '</td>' +
                                '</tr>');
                            x++;
                        });
                    } else {
                        $('.transferColumn').removeClass('hidden');
                        $('.issueColumn').addClass('hidden');
                        $.each(data['detail'], function (key, value) {
                            if (value['qtyReceived']) {
                                issuedQTY = value['qtyReceived'];
                            } else {
                                issuedQTY = 0;
                            }
                            balanceQTY = value['balQTY'];
                            issuedQTYs = '<input type="text" class="number" size="10" id="qtyIssued_' + value['stockTransferDetailsID'] + '" value="0" onkeyup="check_qty_available(' + value['stockTransferDetailsID'] + ',' + balanceQTY + ',' + value['stock'] + ')" > <input class="checkbox" id="check_' + value['stockTransferDetailsID'] + '" type="hidden" value="' + value['stockTransferDetailsID'] + '"><input class="hide" id="docID" value="' + data['documentID'] + '">';
                            $('#table_body_mr_note_detail').append('<tr>' +
                                    '<td>' + x + '</td>' +
                                    '<td>' + value['itemSystemCode'] + '</td>' +
                                    '<td>' + value['itemDescription'] + '</td>' +
                                    '<td class="text-center">' + value['unitOfMeasure'] + '</td>' +
                                    '<td class="text-right">' + (value['transfer_QTY'] ) + '</td>' +
                                    '<td class="text-right">' + (issuedQTY ) + '</td>' +
                                    '<td class="text-right">' + balanceQTY + ' <i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' + value['stockTransferDetailsID'] + ',' + balanceQTY + ',' + value['stock'] + ')" aria-hidden="true"></i></td>' +
                                    '<td class="text-right">' + issuedQTYs + '</td>' +
                                '</tr>');
                            x++;
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

    function save_mr_note_base_items() {
        var qty = [];
        var itemIssueDetailID = [];
        var docID = $('#docID').val();
        $('#table_body_mr_note_detail input:hidden').each(function () {
            // itemIssueDetailID.push($(this).val());
            qty.push($('#qtyIssued_' + $(this).val()).val());
            itemIssueDetailID.push($('#check_' + $(this).val()).val());
        });
        if (!jQuery.isEmptyObject(itemIssueDetailID)) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemIssueDetailID': itemIssueDetailID, 'qty': qty, 'docID': docID, 'mrnAutoID': mrnAutoID},
                url: "<?php echo site_url('MaterialReceiptNote/save_material_issue_note_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        $('#mr_base_modal').modal('hide');
                        setTimeout(function () {
                            fetch_material_receipt_detail(4);
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

    function check_qty_available(mrDetailID, balanceQTY, currentStock) {
        var issuedqty = $('#qtyIssued_' + mrDetailID).val();
         if (issuedqty > balanceQTY) {
            myAlert('w', 'Qty cannot be greater than Balance Qty');
            $('#qtyIssued_' + mrDetailID).val(0);
            $('#mrbasebtn').attr('disabled', true);
        } else {
            $('#mrbasebtn').attr('disabled', false);
        }
    }
    function validatetb_row(det) {
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }

    $('#receiptType').on('change', function() {
        if(this.value == 'Material Request'){
            $('.requestedSegmentDiv').addClass('hide');
        }else{
            $('.requestedSegmentDiv').removeClass('hide');
            $("#segment").prop("disabled", false);
        }
    })

    function chkbalanceqty(){
        if (materialReceiptNoteType == 'Material Request') {
            var issuedqty = $('#quantityRequested_edit').val();
            var itemID = $('#itemAutoID_edit').val();
            var mrnDetailID_edit = $('#mrnDetailID_edit').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'issuedqty': issuedqty, 'itemAutoID': itemID, 'mrnDetailID': mrnDetailID_edit},
                url: "<?php echo site_url('MaterialReceiptNote/chk_balance_qty'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    if(data[0]=='w'){
                        myAlert(data[0],data[1])
                        $('#quantityRequested_edit').val('')
                    }

                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
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
                    /* if(projectcategory == 0){
                        projectcategory = '';
                    } */
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
                    $('#purchaseOrderDiv').addClass('hide');
                }else if(data['status'] === 3){
                    $('#purchaseOrderDiv').removeClass('hide');
                } else {
                    $('#jobNumberAdd').addClass('hidden');
                    $('#purchaseOrderDiv').addClass('hide');
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function setQty(mrDetailID, balanceQTY, currentStock){
        var ordQtyId = "#qtyIssued_"+mrDetailID;
        $(ordQtyId).val(balanceQTY);
        check_qty_available(mrDetailID, balanceQTY, currentStock);
    }

    function check_batch_exists(itemAutoID,){

    }

    function changeBatchNumber(ev){
        
        var batchNumber = ev.val();
        var itemAutoID = ev.closest('tr').find('.itemAutoID').val();
        ev.closest('tr').find('.btnViewBatch').addClass('hide');
        
        if(batchNumber && itemAutoID){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'batchNumber': batchNumber,'itemAutoID':itemAutoID,'mrnAutoID':mrnAutoID},
                url: "<?php echo site_url('MaterialReceiptNote/fetch_existing_batch_details'); ?>",
                success: function (data) {
                    
                    if(data.status == 'exists'){
                        ex_batch_details[batchNumber] = data.details;

                        $('#ex_batch_code').html(data.details.batchNumber)
                        $('#ex_expire_date').html(data.details.batchExpireDate)
                        $('#ex_warehouse').html(data.details.wareHouseAutoID)
                        $('#ex_current_qty').html(data.details.qtr)

                        //
                        ev.closest('tr').find('.btnViewBatch').removeClass('hide');

                        //add expire date
                        ev.closest('tr').find('.expireDate').val(data.details.batchExpireDate);

                        $('#existing_batch_details').modal('show');

                    }else{
                     //   ev.closest('tr').find('.btnViewBatch').addClass('hide');
                    }

                }, error: function () {
                    swal("Cancelled", "Your " + value + " file is safe :)", "error");
                }
            });
        }

    }

    function load_ex_batch_modal_data(ev){
        var batchNumber = ev.closest('tr').find('.batchNumber').val();
        var exe_details = ex_batch_details[batchNumber];

        $('#ex_batch_code').html(exe_details.batchNumber)
        $('#ex_expire_date').html(exe_details.batchExpireDate)
        $('#ex_warehouse').html(exe_details.wareHouseAutoID)
        $('#ex_current_qty').html(exe_details.qtr)

        }

    function get_purchase_order_details(ev){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'purchaseOrderID':$(ev).val()},
            url: "<?php echo site_url('Quotation_contract/get_purchase_order_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data) {
                    $('#issueRefNo').val(data.referenceNumber);
                }
                
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }

        });

    }

</script>   