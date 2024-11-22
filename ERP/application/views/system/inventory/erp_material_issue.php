<?php

 $primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$customer_arr = all_employee_drop();
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
if($pID != '') {
    
    $Documentid = 'MI';
    $warehouseidcurrentdoc = all_warehouse_drop_isactive_inactive($pID,$Documentid);
    if(!empty($warehouseidcurrentdoc) && $warehouseidcurrentdoc['isActive'] == 0)
    {
        $location_arr[trim($warehouseidcurrentdoc['wareHouseAutoID'] ?? '')] = trim($warehouseidcurrentdoc['wareHouseCode'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseLocation'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseDescription'] ?? '');
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
            <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one'); ?>
        - <?php echo $this->lang->line('transaction_issue_header'); ?></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_material_item_detail()" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_two'); ?>
        - <?php echo $this->lang->line('transaction_issue_detail'); ?></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_three'); ?>
        - <?php echo $this->lang->line('transaction_issue_confirmation'); ?></span>
        </a>
    </div>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="material_issue_form"'); ?>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label
                        for="issueType"><?php echo $this->lang->line('transaction_issue_type'); ?><?php required_mark(); ?></label>
                    <!--Issue Type-->
                    <?php echo form_dropdown('issueType', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'Direct Issue' => $this->lang->line('transaction_direct_issue'), 'Material Request' => $this->lang->line('transaction_material_request')), 'Direct Issue', 'class="form-control select2" id="issueType"'); ?>
                </div>
                <div class="form-group col-sm-4 requestedSegmentDiv">
                    <label
                        for="segment"><?php echo $this->lang->line('transaction_primary_segment'); ?><?php required_mark(); ?></label>
                    <!--Primary Segment-->
                    <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" '); ?>
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
                        for=""><?php echo $this->lang->line('transaction_date_issued'); ?><?php required_mark(); ?></label>
                    <!--Date Issued-->

                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="issueDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                            value="<?php echo $current_date; ?>" id="issueDate"
                            class="form-control" >
                    </div>
                </div>
                <?php
                    if($financeyearperiodYN==1){
                    ?>
                        <div class="form-group col-sm-4">
                            <label
                                for="financeyear"><?php echo $this->lang->line('transaction_common_financial_year'); ?><?php required_mark(); ?></label>
                            <!--Financial Year-->
                            <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear"  onchange="fetch_finance_year_period(this.value)" required'); ?>
                        </div>
                        <div class="form-group col-sm-4">
                            <label
                                for="financeyear"><?php echo $this->lang->line('transaction_common_financial_period'); ?><?php required_mark(); //?></label>
                            <!--Financial Period-->
                            <?php echo form_dropdown('financeyear_period', array('' => 'Select Financial Period'), '', 'class="form-control" id="financeyear_period"'); ?>
                        </div>
                    <?php } 
                ?>
            </div>
           
            <div class="form-group col-sm-4 requestedSegmentDiv">
                <label for="SalesPersonName"><?php echo $this->lang->line('transaction_material_request_by'); ?><?php required_mark(); ?></label>
                <!--Requested By-->
                <div class="input-group">
                    <input type="text" class="form-control" id="employeeName" name="employeeName">
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
                    for=""><?php echo $this->lang->line('transaction_common_warehouse_location'); ?><?php required_mark(); ?></label>
                <!--Warehouse Location-->
                <?php echo form_dropdown('location', $location_arr, $location_arr_default, 'class="form-control select2" id="location" onchange="validate_mfq_warehouse(this.value)"'); ?>
            </div>
            <div class="form-group col-sm-4 hidden requestedLocationDiv">
                <label for=""><?php echo $this->lang->line('transaction_common_requested_warehouse_location'); ?><?php required_mark(); ?></label>
                <!--Warehouse Location-->
                <?php echo form_dropdown('requested_location', $location_arr, $location_arr_default, 'class="form-control select2" id="requested_location"'); ?>
            </div>
        
            <div class="form-group col-sm-4">
                <label
                    for="itemType"><?php echo $this->lang->line('transaction_item_type'); ?><?php required_mark(); ?></label>
                <!--Item Type-->
                <?php echo form_dropdown('itemType', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'Inventory' => $this->lang->line('transaction_inventory')/* 'Inventory'*/, 'Non Inventory' => $this->lang->line('transaction_non_inventory')/*'Non Inventory'*/), 'Inventory', 'class="form-control select2" id="itemType"'); ?>
            </div>
        <div class="form-group col-sm-4">
            <label for=""><?php echo $this->lang->line('transaction_common_narration'); ?> </label><!--Narration-->
            <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
        </div>
        <div class="form-group col-sm-4">
            <label for=""><?php echo $this->lang->line('common_reserved'); ?> </label><!--reserved-->
            <div class="custom-control custom-checkbox">
                <input type="checkbox"  id="reserved" name="reserved" value="1">
            </div>
        </div>
        <?php //if($jobNumberMandatory==1){ ?>
            <div class="form-group col-sm-4 hidden" id="jobNumberAdd_MI">
                <label for="financeyear"><?php echo $this->lang->line('inventory_job_number');?> <?php required_mark(); ?></label><!--Job Number-->
                <div class="div_job_number_MI">
                    <select name="jobID" class="form-control select2">
                        <option value="">
                            Select Job Number</option>
                    </select>
                </div>
            </div>
        <?php //} ?>
        <div class="row col-md-12">
            <div class="text-right m-t-xs">
                <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next'); ?> </button><!--Save & Next-->
            </div>
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
                <button type="button" onclick="edit_all_item_detail_modal()" style="margin-right:8px;" id="editallbtn"
                        class="btn btn-default hidden pull-right"><span class="glyphicon glyphicon-pencil"></span> Edit
                    All
                </button>
            </div>
        </div>
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%" class="mrBaseDiveTable ">MR Code</th>
                <th style="min-width: 8%"><?php echo $this->lang->line('transaction_common_item_code'); ?> </th>
                <?php if($itemBatch == 1){ ?>
                    <th>Batch Number </th><!--Code-->
                <?php } ?>
                <!--Item Code-->
                <th style="min-width: 23%"><?php echo $this->lang->line('common_item_description'); ?> </th>
                <!--Item Description-->
                <?php if($advanceCostCapturing == 1){ ?>
                    <th id="activityCode_header" style="width: 12%;">Activity Code </th>
                <?php } ?>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_uom'); ?> </th><!--UOM-->
                <!-- <th style="min-width: 10%">Requested</th> -->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_qty'); ?> </th><!--QTY-->
                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_adjustment_wac'); ?> </th>
                <!--WAC-->
                <th style="min-width: 15%"><?php echo $this->lang->line('common_total_value'); ?>
                    (<?php echo $this->common_data['company_data']['company_default_currency']; ?>)
                </th><!--Value-->
                <th style="min-width: 12%">&nbsp;</th>
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
                id="Material_issue_attachment_label"><?php echo $this->lang->line('transaction_goods_received_voucher_modal_title'); ?></h4>
            <!--Modal title-->
            <br>

            <div class="table-responsive" style="width: 60%">
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?> </th><!--File Name-->
                        <th><?php echo $this->lang->line('common_description'); ?> </th><!--Description-->
                        <th><?php echo $this->lang->line('common_type'); ?></th><!--Type-->
                        <th><?php echo $this->lang->line('common_action'); ?></th><!--Action-->
                    </tr>
                    </thead>
                    <tbody id="Material_issue_attachment" class="no-padding">
                    <tr class="danger">
                        <td colspan="5"
                            class="text-center"><?php echo $this->lang->line('common_no_attachment_found'); ?> </td>
                    </tr><!--No Attachment Found-->
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous'); ?> </button>
            <!--Previous-->
            <button class="btn btn-primary "
                    onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft'); ?>  </button>
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
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom'); ?><?php required_mark(); ?></th>

                            <?php if($itemBatch == 1){ ?>
                               <th>Batch Number <?php required_mark(); ?></th><!--Code-->
                            <?php } ?>
                            <!--UOM-->
                            <th style="width:200px;"><?php echo $this->lang->line('transaction_current_stock'); ?><?php required_mark(); ?></th>
                            <!--Current Stock-->
                            <th style="width:80px;"><abbr title="Park Qty">Park Qty</abbr></th>

                            <th style="width: 150px;"><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                            <!--Segment-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project'); ?><!-- Project --> </th>
                                <th><?php echo $this->lang->line('common_project_category'); ?><!-- Project Category --></th>
                                <th><?php echo $this->lang->line('common_project_subcategory'); ?><!-- Project Subcategory --></th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_material_issued_qty'); ?><?php required_mark(); ?></th>
                            <!--Issued Qty-->
                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="width: 100px;">Activity Code</th>
                            <?php } ?>
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
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('common_item_description'); ?>..."
                                       id="f_search_1"><!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                <input type="hidden" class="form-control currentStock" name="currentStock">
                                <input type="hidden" class="form-control itemcatergory" name="itemcatergory">
                            </td>
                            <td>
                                <?php echo form_dropdown('UnitOfMeasureID[]', $unitOfMeasure_arr, '', 'class="form-control umoDropdown" onchange="convertPrice(this)"  required'); ?>
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
                                <input type="text" name="quantityRequested[]" onkeyup="checkCurrentStock(this)"
                                       onchange="checkCurrentStock_unapproveddocument(this)"
                                       onfocus="this.select();"
                                       class="form-control number quantityRequested" required>
                            </td>
                            <?php if($advanceCostCapturing == 1){ ?>
                                <td>
                                    <?php echo form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2" id="activityCode" '); ?>
                                </td>
                            <?php } ?>
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
                        onclick="saveMaterialIssue_addItem()"><?php echo $this->lang->line('common_save_change'); ?>
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
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom'); ?><?php required_mark(); ?></th>
                            <?php if($itemBatch == 1){ ?>
                               <th>Batch Number <?php required_mark(); ?></th><!--Code-->
                            <?php } ?>
                            <!--UOM-->
                            <th style="width:150px;"><?php echo $this->lang->line('transaction_current_stock'); ?><?php required_mark(); ?></th>
                            <!--Current Stock-->
                            <th style="width:100px;">Park Qty<?php required_mark(); ?></th>

                            <th style="width: 150px;" class="mitypecoloumn"><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                            <?php if ($projectExist == 1) { ?><!--Segment-->
                            <th><?php echo $this->lang->line('common_project'); ?></th>
                            <th><?php echo $this->lang->line('common_project_category'); ?><!-- Project Category --></th>
                            <th><?php echo $this->lang->line('common_project_subcategory'); ?><!-- Project Subcategory --></th>

                            <?php } ?><!--Project-->
                            <th style="width: 120px;"><?php echo $this->lang->line('transaction_material_issued_qty'); ?><?php required_mark(); ?></th>
                            <!--Issued Qty-->
                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="width: 100px;">Activity Code</th>
                            <?php } ?>
                            <th style="width: 180px;"><?php echo $this->lang->line('common_comment'); ?> </th>
                            <!--Comment-->
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" class="form-control"
                                       id="search"
                                       name="search"
                                       placeholder="<?php echo $this->lang->line('common_item_id'); ?>,<?php echo $this->lang->line('transaction_common_item_description'); ?>...">
                                <!--Item ID--><!--Item Description-->
                                <input type="hidden" class="form-control" id="itemAutoID_edit" name="itemAutoID">
                                <input type="hidden" class="form-control" id="currentStock_edit" name="currentStock">
                                <input type="hidden" class="form-control" id="mrAutoID_detail_updateEdit" name="mrAutoID">
                                <input type="hidden" class="form-control" id="itemcatergory_Edit" name="itemcatergory_Edit">
                            </td>
                            <td>
                                <input type="hidden" name="conversionRateEdit" id="conversionRateEdit"> 
                                <?php echo form_dropdown('unitOfMeasureID', $unitOfMeasure_arr, '', 'class="form-control" id="UnitOfMeasureID_edit" onchange="convertPrice_edit(this)"  required'); ?>
                            </td>
                            <?php if($itemBatch == 1){ ?>
                            <td>
                               <?php echo form_dropdown('batch_number[]', array('' => 'Batch Number'), '', 'class="form-control select2 b_number" id="batch_number_edit" multiple="multiple" required'); ?>

                               
                            </td>
                            <?php } ?>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="currentWareHouseStockQty"
                                           class="form-control" id="currentWareHouseStockQty_edit" required readonly>
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
                                <?php echo form_dropdown('a_segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment_edit" onchange="load_segmentBase_projectID_itemEdit(this)"'); ?>
                            </td>
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_item">
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option
                                                value=""><?php echo $this->lang->line('transaction_goods_received_select_project'); ?> </option>
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
                                       onkeyup="checkCurrentStockEdit(this)"
                                       onchange="checkCurrentStockEditunapproveddocument(this)"
                                       class="form-control number" id="quantityRequested_edit" required>

                                <input type="hidden" name="pulledcurrentstock_edit"
                                           id="pulledcurrentstock_edit"
                                           class="form-control" >
                            </td>
                            <?php if($advanceCostCapturing == 1){ ?>
                                <td>
                                    <?php echo form_dropdown('activityCode', $activityCode_arr, '', 'class="form-control select2" id="edit_activityCode" '); ?>
                                </td>
                            <?php } ?>
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

<div aria-hidden="true" role="dialog" id="all_item_edit_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 85%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Item Detail</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="edit_all_item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color"
                           id="MaterialIssue_detail_all_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;"><?php echo $this->lang->line('transaction_common_item_code'); ?><?php required_mark(); ?></th>
                            <!--Item Code-->
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_common_uom'); ?><?php required_mark(); ?></th>
                            <?php if($itemBatch == 1){ ?>
                               <th>Batch Number <?php required_mark(); ?></th><!--Code-->
                            <?php } ?>
                            <!--UOM-->
                            <th style="width:200px;"><?php echo $this->lang->line('transaction_current_stock'); ?><?php required_mark(); ?></th>
                            <!--Current Stock-->
                            <!--<th style="width:100px;">Park Qty</th>-->

                            <th style="width: 150px;"><?php echo $this->lang->line('common_segment'); ?><?php required_mark(); ?></th>
                            <!--Segment-->
                            <?php if($advanceCostCapturing == 1){ ?>
                                <th style="width: 100px;">Activity Code <?php required_mark(); ?></th>
                            <?php } ?>
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project'); ?><?php required_mark(); ?></th><!--Project-->
                            <?php } ?>
                            <th style="width: 150px;">Issued Qty <?php required_mark(); ?></th>
                            <th style="width: 150px;">Comment</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_edit_material_issue()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody id="edit_item_table_body">

                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                <button class="btn btn-primary" type="button"
                        onclick="updateMaterialIssue_edit_all_item()"><?php echo $this->lang->line('common_update_changes'); ?>
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
                <button type="button" id="mrbasebtn" class="btn btn-primary" onclick="save_mr_base_items()">Save
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
$data['documentID'] = 'MI';
$this->load->view('system/item/itemmastersub/inc-item-master-sub-config-edit', $data);
?>
<script type="text/javascript">
    var search_id = 1;
    var type;
    var itemIssueAutoID;
    var itemIssueDetailID;
    var EIdNo;
    var ECode;
    var projectID;
    var materialIssueType;
    var currency_decimal;
    var Primary_Segment;
    var warehouseID;
    var projectcategory;
    var projectsubcat;
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.headerclose').click(function () {
            fetchPage('system/inventory/material_issue_management', itemIssueAutoID, 'Material Issue');
        });
        $('.select2').select2();
        type = 'Inventory';
        EIdNo = null;
        projectID = null;
        itemIssueAutoID = null;
        itemIssueDetailID = null;
        currency_decimal = 2;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#material_issue_form').bootstrapValidator('revalidateField', 'issueDate');
        });

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
        currency_validation_modal(CurrencyID, 'MI', '', '');
        if (p_id) {
            itemIssueAutoID = p_id;
            load_material_issue_header();
            $("#a_link").attr("href", "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>/" + itemIssueAutoID);
            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + itemIssueAutoID + '/MI');
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

        $('#material_issue_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                issueType: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_issue_type_is_required');?>.'}}},
                issueDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('transaction_material_issue_date_is_required');?>.'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#issueType").prop("disabled", false);
            $("#segment").prop("disabled", false);
            $("#location").prop("disabled", false);
            $("#requested_location").prop("disabled", false);
            $("#itemType").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'employeeID', 'value': EIdNo});
            data.push({'name': 'itemIssueAutoID', 'value': itemIssueAutoID});
            data.push({'name': 'requested', 'value': $('#employee_id option:selected').text()});
            data.push({'name': 'location_dec', 'value': $('#location option:selected').text()});
            data.push({'name': 'requested_location_dec', 'value': $('#requested_location option:selected').text()});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'jobNumber', 'value': $('#jobID option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Inventory/save_material_issue_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        itemIssueAutoID = data['last_id'];
                        materialIssueType = data['issueType'];
                        Primary_Segment = $("#segment").val();
                        $("#a_link").attr("href", "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>/" + itemIssueAutoID);
                        $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + itemIssueAutoID + '/MI');
                        warehouseID = $('#location').val();
                        if (materialIssueType == 'Material Request') {
                            $("#segment").prop("disabled", true);
                        }else{
                            $("#segment").prop("disabled", false);
                        }
                        $('[href=#step2]').tab('show');
                        fetch_material_item_detail();
                    }else {
                        $('.btn-primary').removeAttr('disabled');
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
            data: {'companyFinanceYearID': companyFinanceYearID,'fyDepartmentID':'MI'},
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
            serviceUrl: '<?php echo site_url();?>Inventory/fetch_inv_item_stock_adjustment/?&t=' + type,
            onSelect: function (suggestion) {
                var cont = true;
                $('.itemAutoID').each(function () {
                    if (this.value) {
                        if (this.value == suggestion.itemAutoID) {
                            $('#f_search_' + id).val('');
                            $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                            $('#f_search_' + id).closest('tr').find('.currentWareHouseStock').val('');
                            $('#f_search_' + id).closest('tr').find('.quantityRequested').val('');
                            $('#f_search_' + id).closest('tr').find('.umoDropdown').val('');
                            myAlert('w', 'Selected item is already selected');
                            cont = false;
                        }
                    }
                });
                if (cont) {
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
                    $(this).closest('tr').find('.itemcatergory').val(suggestion.mainCategory);
                    fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                    fetch_warehouse_item(suggestion.itemAutoID, this);
                    $(this).closest('tr').find('.quantityRequested').focus();
                    $(this).closest('tr').css("background-color", 'white');
                    // check_item_not_approved_in_document(suggestion.itemAutoID,id);
                    check_item_not_approved_in_document(suggestion.itemAutoID,id,'');

                }
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');

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

    function initializeitemTypeahead_edit(type) {

        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Inventory/fetch_inv_item_stock_adjustment/?&t=' + type,
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#quantityRequested_edit').val();
                    $('#itemAutoID_edit').val(suggestion.itemAutoID);
                    $('#currentStock_edit').val(suggestion.currentStock);
                    $('#d_uom_edit').val(suggestion.defaultUnitOfMeasure);
                    $('#itemcatergory_Edit').val(suggestion.mainCategory); 
                     $('#quantityRequested_edit').val(0);
                }, 200);
                $(this).closest('tr').find('#quantityRequested_edit').focus();
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                fetch_warehouse_item_edit(suggestion.itemAutoID);
                // check_item_not_approved_in_document(suggestion.itemAutoID,id);
                check_item_not_approved_in_document(suggestion.itemAutoID,id,'MI');



            }
            
        });
        $('#search').off('focus.autocomplete');

    }

    function fetch_warehouse_item(itemAutoID, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemIssueAutoID': itemIssueAutoID, 'itemAutoID': itemAutoID ,'itemIssueDetailID':itemIssueDetailID},
            url: "<?php echo site_url('Inventory/fetch_warehouse_item_new'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
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
            // data: {'itemIssueAutoID': itemIssueAutoID, 'itemAutoID': itemAutoID},
            data: {'itemIssueAutoID': itemIssueAutoID, 'itemAutoID': itemAutoID,'documentcode':'MI','itemIssueDetailID':itemIssueDetailID},
            //url: "<?php //echo site_url('Inventory/fetch_warehouse_item'); ?>//",
            url: "<?php echo site_url('Inventory/fetch_warehouse_item_new'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
              
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
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_material_issue_header() {
        if (itemIssueAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemIssueAutoID': itemIssueAutoID},
                url: "<?php echo site_url('Inventory/load_material_issue_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#issueType').val(data['issueType']).change();
                        materialIssueType = data['issueType'];
                        $('#issueDate').val(data['issueDate']);
                        $('#reserved').prop('checked', data['reservedYN'] == 1);
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        Primary_Segment = data['segmentID'] + '|' + data['segmentCode'];
                        $('#narration').val(data['comment']);
                        $('#location').val(data['wareHouseAutoID']).change();
                        $('#jobID').val(data['jobID']).change();
                        $('#requested_location').val(data['requestedWareHouseAutoID']).change();
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
                        $('#issueRefNo').val(data['issueRefNo']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        fetch_material_item_detail();
                        $('#itemType').val(data['itemType']).change();
                        type = data['itemType'];
                        $('#search').typeahead('destroy');
                        initializeitemTypeahead(type);
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        warehouseID = data['wareHouseAutoID'];
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

                mySelect.append($('<option></option>').val('').html('<?php echo $this->lang->line('common_select_uom');?>'));
                /*Select  UOM*/
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

    function fetch_material_item_detail() {
        var IssueType = $('#issueType').val();
        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
        var advanceCostCapturing = '<?php echo getPolicyValues('ACC', 'All'); ?>';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemIssueAutoID': itemIssueAutoID},
            url: "<?php echo site_url('Inventory/fetch_material_item_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                $('#item_table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#item_table_body').append('<tr class="danger"><td colspan="9" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    <!--No Records Found-->
                    $("#issueType").prop("disabled", false);
                    $("#location").prop("disabled", false);
                    $("#requested_location").prop("disabled", false);
                    $("#itemType").prop("disabled", false);
                    $("#editallbtn").addClass("hidden");
                    if(IssueType == 'Material Request'){
                        $('#activityCode_header').addClass('hide');
                    }else{
                        $('#activityCode_header').removeClass('hide');
                    }
                } else {
                    $("#issueType").prop("disabled", true);
                    $("#location").prop("disabled", true);
                    $("#requested_location").prop("disabled", true);
                    $("#itemType").prop("disabled", true);
                    if(data.issueType == 'Material Request'){
                        $('#activityCode_header').addClass('hide');
                    }else{
                        $('#activityCode_header').removeClass('hide');
                    }
                    currency_decimal = <?php echo json_encode($this->common_data['company_data']['company_default_decimal']); ?>;
                    $.each(data['detail'], function (key, value) {
                        if (value['isSubitemExist'] == 1) {
                            var colour = 'color: #dad835 !important';
                            colour = '';
                            if(itemBatchPolicy==1){
                                if(advanceCostCapturing == 1){
                                    string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber'] +'</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['activityCodeName'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['qtyIssued'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['itemIssueDetailID'] + ',\'MI\', ' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="edit_item(' + value['itemIssueDetailID'] + ',this);"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['itemIssueDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }else{
                                    string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] +'</td><td>' + value['batchNumber'] +'</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['qtyIssued'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['itemIssueDetailID'] + ',\'MI\', ' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="edit_item(' + value['itemIssueDetailID'] + ',this);"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['itemIssueDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }
                            }else{
                                if(advanceCostCapturing == 1){
                                    string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['activityCodeName'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['qtyIssued'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['itemIssueDetailID'] + ',\'MI\', ' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="edit_item(' + value['itemIssueDetailID'] + ',this);"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['itemIssueDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }else{
                                    string = '<tr><td>' + x + '</td><td class="mrBaseDiveTable">' + value['MRCode'] + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['qtyIssued'] + '</td><td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right"><a style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['itemIssueDetailID'] + ',\'MI\', ' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="edit_item(' + value['itemIssueDetailID'] + ',this);"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['itemIssueDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                                }
                            }
                            
                        } else {

                            if(itemBatchPolicy==1){
                                string = '<tr><td>' + x + '</td>' +
                                    '<td class="mrBaseDiveTable">' + value['MRCode'] + '</td>' +
                                    '<td>' + value['itemSystemCode'] + '</td>' +
                                    '<td>' + value['batchNumber'] + '</td>' +
                                    '<td>' + value['itemDescription'] + '</td>';

                                if (advanceCostCapturing == 1){
                                    string += '<td class="text-center">' + value['activityCodeName'] + '</td>';
                                }

                                string += '<td class="text-center">' + value['unitOfMeasure'] + '</td>' +
                                    '<td class="text-right">' + value['qtyIssued'] + '</td>' +
                                    '<td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' +
                                    '<td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td>' +
                                    '<td class="text-right">' +
                                    '<a onclick="edit_glaccount(' + value['itemIssueDetailID'] + ',' + value['PLGLAutoID'] + ',' + value['BLGLAutoID'] + ');">' +
                                    '<span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp; &nbsp;&nbsp;' +
                                    '<a onclick="edit_item(' + value['itemIssueDetailID'] + ',this);">' +
                                    '<span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp;' +
                                    '<a onclick="delete_item(' + value['itemIssueDetailID'] + ');">' +
                                    '<span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a>' +
                                    '</td></tr>';

                            }else{
                                string = '<tr><td>' + x + '</td>' +
                                    '<td class="mrBaseDiveTable">' + value['MRCode'] + '</td>' +
                                    '<td>' + value['itemSystemCode'] + '</td>' +
                                    '<td>' + value['itemDescription'] + '</td>';

                                if (advanceCostCapturing == 1){
                                    string += '<td class="text-center">' + value['activityCodeName'] + '</td>';
                                }

                                string += '<td class="text-center">' + value['unitOfMeasure'] + '</td>' +
                                    '<td class="text-right">' + value['qtyIssued'] + '</td>' +
                                    '<td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(currency_decimal, '.', ',') + '</td>' +
                                    '<td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td>' +
                                    '<td class="text-right">' +
                                    '<a onclick="edit_glaccount(' + value['itemIssueDetailID'] + ',' + value['PLGLAutoID'] + ',' + value['BLGLAutoID'] + ');">' +
                                    '<span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp; ' +
                                    '<a onclick="edit_item(' + value['itemIssueDetailID'] + ',this);">' +
                                    '<span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp; ' +
                                    '<a onclick="delete_item(' + value['itemIssueDetailID'] + ');">' +
                                    '<span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a>' +
                                    '</td></tr>';
                            }

                            

                        }
                        $('#item_table_body').append(string);


                        total += parseFloat(value['totalValue']);
                        x++;
                    });
                    if (materialIssueType == 'Material Request') {
                        $("#segment").prop("disabled", true);
                        $("#editallbtn").addClass("hidden");
                    }else{
                        $("#segment").prop("disabled", false);
                        $("#editallbtn").removeClass("hidden");
                    }

                }
                if(IssueType == 'Material Request'){
                    $('.mrBaseDiveTable').removeClass('hide');
                    $('#table_tfoot').html('<tr> <td class="text-right" colspan="8"><?php echo $this->lang->line('common_total');?></td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>');
                }else{
                    let colspan = 6;
                    if (advanceCostCapturing == 1) {
                        colspan = 7;
                    }
                    $('#table_tfoot').html('<tr> <td class="text-right" colspan="' + colspan + '"><?php echo $this->lang->line('common_total');?></td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>');
                    $('.mrBaseDiveTable').addClass('hide');
                }
                stopLoad();
                /* <!--Total--> */
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
        data.push({name: "masterID", value: itemIssueAutoID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/materialAccountUpdate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                fetch_material_item_detail();
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

    function edit_glaccount(itemIssueDetailID, PLGLAutoID, BLGLAutoID) {
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
                $('#detailID').val(itemIssueDetailID);
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

    function item_detail_modal() {
        if (itemIssueAutoID) {
            if (materialIssueType == 'Material Request') {
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
                $("#item_detail_modal").modal({backdrop: "static"});
            }
        }
    }

    function load_conformation() {
        if (itemIssueAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'itemIssueAutoID': itemIssueAutoID, 'html': true},
                url: "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>/" + itemIssueAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + itemIssueAutoID + '/MI');
                    attachment_modal_MaterialIssue(itemIssueAutoID, "<?php echo $this->lang->line('transaction_material_issue');?>", "MI");
                    /*Material issue*/
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
        if (itemIssueAutoID) {
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
                        data: {'itemIssueAutoID': itemIssueAutoID},
                        url: "<?php echo site_url('Inventory/material_item_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                                if (data['message'] == 'Some Item quantities are not sufficient to confirm this transaction!') {
                                    confirm_all_item_detail_modal(data['itemAutoID']);
                                }
                            }else if(data['error']==2){
                                myAlert('w',data['message']);
                            }
                            else {
                                myAlert('s', data['message']);
                                //refreshNotifications(true);
                                fetchPage('system/inventory/material_issue_management', itemIssueAutoID, 'Material Issue');
                            }
                        }, error: function (xhr, textStatus, errorThrown) {
                            myAlert('e', 'Error (' + textStatus + ') : ' + xhr.responseText);
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (itemIssueAutoID) {
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
                    fetchPage('system/inventory/material_issue_management', itemIssueAutoID, 'Material Issue');
                });
        }
    }

    function delete_item(id) {
        if (itemIssueAutoID) {
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
                        data: {'itemIssueDetailID': id},
                        url: "<?php echo site_url('Inventory/delete_material_item'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_material_item_detail();
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
        if (itemIssueAutoID) {
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
                        data: {'itemIssueDetailID': id},
                        url: "<?php echo site_url('Inventory/load_material_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            if(itemBatchPolicy==1){
                                var textBatchData=data['batchNumber'];
                                var wareHouseAutoIDSelect=$('#location').val();
                                $.ajax({
                                    async: true,
                                    type: 'post',
                                    dataType: 'json',
                                    data: {'itemId': data['itemAutoID'],'wareHouseAutoID': wareHouseAutoIDSelect},
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

                            itemIssueDetailID = data['itemIssueDetailID'];
                            projectID = data['projectID'];
                            projectcategory = data['project_categoryID'];
                            projectsubcat = data['project_subCategoryID'];
                            $('#search').typeahead('destroy');
                            $('#search').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - "+data['seconeryItemCode']);
                            $('#itemAutoID_edit').val(data.itemAutoID);
                            $('#currentStock_edit').val(data.Stock);
                            $('#mrAutoID_detail_updateEdit').val(data.mrAutoID);
                            $('#currentWareHouseStockQty_edit').val(data.Stock);
                            $('#d_uom_edit').text(data.defaultUOM);
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#quantityRequested_edit').val(data['qtyIssued']);
                            $('#segment_edit').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            $('#edit_activityCode').val(data['activityCodeID']).change();
                            $('#comment_edit').val(data['comments']);
                            $('#itemcatergory_Edit').val(data['mainCategory']);
                            initializeitemTypeahead_edit(type);
                            if (materialIssueType == 'Material Request') {
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
                            $('#conversionRateEdit').val(data['conversionRateUOM']);
                            fetch_warehouse_item_edit(data['itemAutoID']);
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
                    $('#Material_issue_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " <?php echo $this->lang->line('common_attachments');?>");
                    /* <!--Attachments--> */
                    $('#Material_issue_attachment').empty();
                    $('#Material_issue_attachment').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_Material_issue_attachement(itemIssueAutoID, DocumentSystemCode, myFileName) {
        if (itemIssueAutoID) {
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
                        data: {'attachmentID': itemIssueAutoID, 'myFileName': myFileName},
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

        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';

        if(itemBatchPolicy==1){
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

    function saveMaterialIssue_addItem() {
        var $form = $('#item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'itemIssueAutoID', 'value': itemIssueAutoID});
        data.push({'name': 'itemIssueDetailID', 'value': itemIssueDetailID});
        $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/save_material_detail_multiple'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    itemIssueDetailID = null;
                    $('#item_detail_form')[0].reset();
                    $('#activityCode').val('').change();
                    setTimeout(function () {
                        fetch_material_item_detail(4);
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
        $('#search').attr('disabled',false);
        $('#UnitOfMeasureID_edit').attr('disabled',false);
        var $form = $('#edit_item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'itemIssueAutoID', 'value': itemIssueAutoID});
        data.push({'name': 'itemIssueDetailID', 'value': itemIssueDetailID});
        data.push({'name': 'materialIssueType', 'value': materialIssueType});
        data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/save_material_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    itemIssueDetailID = null;
                    $('#item_detail_form')[0].reset();
                    $('#edit_activityCode').val('').change();
                    setTimeout(function () {
                        fetch_material_item_detail(4);
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
            myAlert('e', 'Select An Employee');
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

    function checkCurrentStock(det) {
        var currentStock = $(det).closest('tr').find('.currentWareHouseStock').val();
         if (det.value > parseFloat(currentStock)) {
            myAlert('w', 'Issued quantity should be less than or equal to current stock');
            $(det).val(0);
        }
    }

    function checkCurrentStockEdit() {
        var currentStock = $('#currentWareHouseStockQty_edit').val();
        var TransferQty = $('#quantityRequested_edit').val();
        if (parseFloat(TransferQty) > parseFloat(currentStock)) {
            myAlert('w', 'Issued quantity should be less than or equal to current stock');
            $('#quantityRequested_edit').val(0);
        }
    }
    function checkCurrentStockEditunapproveddocument() {
        var currentStock = $('#currentWareHouseStockQty_edit').val();
        var TransferQty = $('#quantityRequested_edit').val();
        var mainCategory = $('#itemcatergory_Edit').val();
        var currentStock_pulled = $('#pulledcurrentstock_edit').val();
        var itemAutoID = $('#itemAutoID_edit').val();
        var wareHouseAutoID = warehouseID;
        var UoM =$('#UnitOfMeasureID_edit option:selected').text().split('|');
        var conversionRate =$('#conversionRateEdit').val();
        if(mainCategory !=='Service'){

            if (parseFloat(TransferQty) > parseFloat(currentStock_pulled)) {
                // document_by_warehouse_qty(itemAutoID,wareHouseAutoID,'MI',itemIssueAutoID,UoM[0],conversionRate,parseFloat(currentStock))
                document_by_warehouse_qty(itemAutoID,wareHouseAutoID,'MI',itemIssueAutoID,UoM[0],conversionRate,parseFloat(currentStock),itemIssueDetailID)
                $('#quantityRequested_edit').val(0);
           
        }


        }

    }

    function edit_all_item_detail_modal() {
        var wareHouseAutoIDSelect =$('#location').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemIssueAutoID': itemIssueAutoID},
            url: "<?php echo site_url('Inventory/fetch_material_item_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                var descm = 2;
                $('#edit_item_table_body').empty();
                var x = 2;
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    <!--No Records Found-->
                    $("#issueType").prop("disabled", false);
                    $("#location").prop("disabled", false);
                    $("#requested_location").prop("disabled", false);
                    $("#itemType").prop("disabled", false);
                    $("#editallbtn").addClass("hidden");
                } else {
                    $("#issueType").prop("disabled", true);
                    $("#location").prop("disabled", true);
                    $("#requested_location").prop("disabled", true);
                    $("#itemType").prop("disabled", true);
                    $.each(data['detail'], function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $unitOfMeasure_arr, '', 'class="form-control select2 umoDropdown"  required')) ?>';
                        var activityCode = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="edit_activityCode_\'+key+\'"'), form_dropdown('activityCode[]', $activityCode_arr, '', 'class="form-control select2 activityCode"  required"')) ?>';

                        ///batch number selection
                        var itemBatchPolicy = '<?php echo getPolicyValues('IB', 'All'); ?>';
                        if(itemBatchPolicy==1){
                            var textBatchData=value['batchNumber'];

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

                        }

                        var Segment = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="seg_\'+key+\'"'), form_dropdown('a_segment[]', $segment_arr, '', 'class="form-control select2 segment"  required onchange="load_segmentBase_projectID_item(this)"')) ?>';
                        
                        if(itemBatchPolicy==1){ 
                            var string = '<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" value="' + value['itemDescription'] + ' - ' + value['itemSystemCodeeditall'] + ' - ' +value['seconeryItemCodeedditall']+'" class="form-control search f_search" name="search[]" placeholder="Item ID,Item Description..." id="f_search_' + x + '"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" value="' + value['itemAutoID'] + '"> <input type="hidden" class="form-control itemIssueDetailID" name="itemIssueDetailID[]" value="' + value['itemIssueDetailID'] + '"> <input type="hidden" class="form-control currentStock" name="currentStock[]"></td> <td>' + UOM + '</td><td>'+batchNumberDropdown+'</td> <td> <div class="input-group"> <input type="text" name="currentWareHouseStockQty[]" class="form-control currentWareHouseStock" value="' + value['stock'] + '" required readonly> </div> </td> <td>' + Segment + '</td> <td>' + activityCode + '</td> <td style="width: 100px"> <input type="text" name="quantityRequested[]" onfocus="this.select();" onkeyup="checkCurrentStock(this)" value="' + value['qtyIssued'] + '" class="form-control number quantityRequested"required> </td><td> <textarea class="form-control" rows="1" name="comment[]" placeholder="Item Comment...">' + value['comments'] + '</textarea> </td><td class="remove-td"><a onclick="delete_item_edit_all(' + value['itemIssueDetailID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                        }else{
                            var string = '<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" value="' + value['itemDescription'] + ' - ' + value['itemSystemCodeeditall'] + ' - ' +value['seconeryItemCodeedditall']+'" class="form-control search f_search" name="search[]" placeholder="Item ID,Item Description..." id="f_search_' + x + '"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" value="' + value['itemAutoID'] + '"> <input type="hidden" class="form-control itemIssueDetailID" name="itemIssueDetailID[]" value="' + value['itemIssueDetailID'] + '"> <input type="hidden" class="form-control currentStock" name="currentStock[]"></td> <td>' + UOM + '</td> <td> <div class="input-group"> <input type="text" name="currentWareHouseStockQty[]" class="form-control currentWareHouseStock" value="' + value['stock'] + '" required readonly> </div> </td> <td>' + Segment + '</td> <td>' + activityCode + '</td> <td style="width: 100px"> <input type="text" name="quantityRequested[]" onfocus="this.select();" onkeyup="checkCurrentStock(this)" value="' + value['qtyIssued'] + '" class="form-control number quantityRequested"required> </td><td> <textarea class="form-control" rows="1" name="comment[]" placeholder="Item Comment...">' + value['comments'] + '</textarea> </td><td class="remove-td"><a onclick="delete_item_edit_all(' + value['itemIssueDetailID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                        }
                        
                        $('#edit_item_table_body').append(string);
                        //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                        $('#seg_' + key).val(value['segmentID'] + '|' + value['segmentCode']).change();
                        $('#edit_activityCode_'+key).val(value['activityCodeID']).change();
                        fetch_related_uom_id(value['defaultUOMID'], value['unitOfMeasureID'], $('#uom_' + key));
                        initializeitemTypeahead(type, x);
                        x++;
                    });
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

    function add_more_edit_material_issue() {
        //$('.search').typeahead('destroy');
        var batch_number_edit_all =search_id-1;
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#MaterialIssue_detail_all_edit_table tbody tr:first').clone();
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
        $('#MaterialIssue_detail_all_edit_table').append(appendData);
        var lenght = $('#MaterialIssue_detail_all_edit_table tbody tr').length - 1;
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        $(".select2").select2();
        initializeitemTypeahead(type, search_id);
        number_validation();
    }


    function updateMaterialIssue_edit_all_item() {
        var $form = $('#edit_all_item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'itemIssueAutoID', 'value': itemIssueAutoID});
        $('#edit_all_item_detail_form select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/save_material_detail_multiple_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#edit_all_item_detail_form')[0].reset();
                    setTimeout(function () {
                        fetch_material_item_detail(4);
                        load_conformation();
                        $('#all_item_edit_detail_modal').modal('hide');
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


    function delete_item_edit_all(id, value, det) {
        if (itemIssueAutoID) {
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
                        data: {'itemIssueDetailID': id},
                        url: "<?php echo site_url('Inventory/delete_material_item'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_material_item_detail();
                            stopLoad();
                            load_conformation();
                            $(det).closest('tr').remove();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function confirm_all_item_detail_modal(itemAutoIdArr) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemIssueAutoID': itemIssueAutoID},
            url: "<?php echo site_url('Inventory/fetch_material_item_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                var descm = 2;
                $('#edit_item_table_body').empty();
                var x = 2;
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                    <!--No Records Found-->
                    $("#issueType").prop("disabled", false);
                    $("#location").prop("disabled", false);
                    $("#requested_location").prop("disabled", false);
                    $("#itemType").prop("disabled", false);
                    $("#editallbtn").addClass("hidden");
                } else {
                    $("#issueType").prop("disabled", true);
                    $("#location").prop("disabled", true);
                    $("#requested_location").prop("disabled", true);
                    $("#itemType").prop("disabled", true);
                    $.each(data['detail'], function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $unitOfMeasure_arr, '', 'class="form-control select2 umoDropdown"  required')) ?>';
                        var Segment = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="seg_\'+key+\'"'), form_dropdown('a_segment[]', $segment_arr, '', 'class="form-control select2 segment"  required onchange="load_segmentBase_projectID_item(this)"')) ?>';
                        var string = '<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" class="form-control search f_search" name="search[]" placeholder="Item ID,Item Description..." id="f_search_' + x + '"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" value="' + value['itemAutoID'] + '"> <input type="hidden" class="form-control itemIssueDetailID" name="itemIssueDetailID[]" value="' + value['itemIssueDetailID'] + '"> <input type="hidden" class="form-control currentStock" name="currentStock[]"></td> <td>' + UOM + '</td> <td> <div class="input-group"> <input type="text" name="currentWareHouseStockQty[]" class="form-control currentWareHouseStock" value="' + value['stock'] + '" required readonly> </div> </td> <td>' + Segment + '</td> <td style="width: 100px"> <input type="text" name="quantityRequested[]" onfocus="this.select();" onkeyup="checkCurrentStock(this)" value="' + value['qtyIssued'] + '" class="form-control number quantityRequested"required> </td><td> <textarea class="form-control" rows="1" name="comment[]" placeholder="Item Comment...">' + value['comments'] + '</textarea> </td><td class="remove-td"><a onclick="delete_item_edit_all(' + value['itemIssueDetailID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                        $('#edit_item_table_body').append(string);
                        //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                        $('#seg_' + key).val(value['segmentID'] + '|' + value['segmentCode']).change();
                        fetch_related_uom_id(value['defaultUOMID'], value['unitOfMeasureID'], $('#uom_' + key));
                        initializeitemTypeahead(type, x);
                        x++;
                    });
                    search_id = x - 1;
                    $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                    $.each(itemAutoIdArr, function (key, valu) {
                        $('.itemAutoID').each(function () {
                            if (this.value == valu['itemAutoID']) {
                                $(this).closest('tr').css("background-color", '#ffb2b2');
                            }
                        });
                    });
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


    function load_MR_codes() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'itemIssueAutoID': itemIssueAutoID},
            url: "<?php echo site_url('Inventory/fetch_MR_code'); ?>",
            success: function (data) {
                $('#mrcode').empty();
                $('#table_body_mr_detail').empty();
                var mySelect = $('#mrcode');
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, value) {
                        var balanceQTY = value['usageQty'];
                        if(balanceQTY>0){
                            var id = 'pull-'+value['mrAutoID'];
                            mySelect.append('<li id="'+id+'" title="MR Date :- ' + value['requestedDate'] + ' Requested By:- ' + value['employeeName'] + '"  rel="tooltip" class="pull-li"><a onclick="fetch_mr_detail_table(' + value['mrAutoID'] + ', '+itemIssueAutoID+')">' + value['MRCode'] + ' <span class="glyphicon glyphicon-chevron-right pull-right" aria-hidden="true"></span></a></li>');
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

    function fetch_mr_detail_table(mrAutoID,itemIssueAutoID) {
        $('.pull-li').removeClass('pulling-based-li');
        $('#pull-'+mrAutoID).addClass('pulling-based-li');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'mrAutoID': mrAutoID, itemIssueAutoID:itemIssueAutoID},
            url: "<?php echo site_url('Inventory/fetch_mr_detail_table'); ?>",
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
                            $('#table_body_mr_detail').append('<tr>' +
                                '<td>' + x + '</td>' +
                                '<td>' + value['itemSystemCode'] + '</td>' +
                                '<td>' + value['itemDescription'] + '</td>' +
                                '<td class="text-center">' + value['unitOfMeasure'] + '</td>' +
                                '<td class="text-right">' + (value['qtyRequested'] ) + '</td>' +
                                '<td class="text-right">' + issuedQTY + '</td>' +
                                '<td class="text-right">' + balanceQTY + ' <i class="fa fa-arrow-circle-right" style="color: #3c8dbc;" onclick="setQty(' + value['mrDetailID'] + ',' + balanceQTY + ',' + masterCurrentStock + ')" aria-hidden="true"></i></td>' +
                                '<td class="text-right">' + masterCurrentStock + ' </td>' +
                                '<td class="text-right">' + issuedQTYs + '</td>' +
                                '</tr>');
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

    function save_mr_base_items() {
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
                data: {'mrDetailID': mrDetailID, 'qty': qty, 'itemIssueAutoID': itemIssueAutoID},
                url: "<?php echo site_url('Inventory/save_mr_base_items'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status']) {
                        $('#activityCode').val('').change();
                        $('#mr_base_modal').modal('hide');
                        setTimeout(function () {
                            fetch_material_item_detail();
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

    $('#issueType').on('change', function() {
        if(this.value == 'Material Request'){
            $('.requestedLocationDiv').removeClass('hidden');
            $('.requestedSegmentDiv').addClass('hide');
        }else{
            $('.requestedLocationDiv').addClass('hidden');
            $('.requestedSegmentDiv').removeClass('hide');
        }
    })
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
                    $('.div_job_number_MI').html(data['dropdown']);
                    $('.select2').select2();
                    $('#jobNumberAdd_MI').removeClass('hidden');
                } else {
                    $('#jobNumberAdd_MI').addClass('hidden');
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
    function check_item_not_approved_in_document(itemAutoID,id,documentcode) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            // data: {itemAutoID: itemAutoID},
            data: {itemAutoID: itemAutoID,'documentcode':documentcode,'itemIssueDetailID':itemIssueDetailID},
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
            data: {itemAutoID: itemAutoID,'documentcode':'MI','itemIssueDetailID':itemIssueDetailID},
            url: "<?php echo site_url('Inventory/check_item_not_approved_in_document_new'); ?>",
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
                    $('#currentStock_edit').val('');
                    $('#d_uom_edit').val('');
                    $('#currentWareHouseStock_edit').val('');
                    $('#UnitOfMeasureID_edit').prop("disabled", true);
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


    function convertPrice(element) {
        var itemAutoID = $(element).closest('tr').find('.itemAutoID').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID,
                'uomID' : element.value,
                'wareHouseAutoID' : warehouseID,
                'id': itemIssueAutoID},
                
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
                    $(element).closest('tr').find('.quantityRequested').val(' ');
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
    function convertPrice_edit(element) {
        var itemAutoID = $(element).closest('tr').find('#itemAutoID_edit').val();
      
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {'itemAutoID': itemAutoID,
                'uomID' : element.value,
                'wareHouseAutoID' : warehouseID,
                'id': itemIssueAutoID,
                'documentcode':'MI',
                'detailID':itemIssueDetailID},
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
                    $(element).closest('tr').find('#quantityRequested_edit').val(' ');
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
    function checkCurrentStock_unapproveddocument(det) {
        var currentStock = $(det).closest('tr').find('.currentWareHouseStock').val();
        var currentStock_pulled = $(det).closest('tr').find('.currentstock_pulleddocument').val();
        var category = $(det).closest('tr').find('.itemcatergory').val();
        var itemAutoID = $(det).closest('tr').find('.itemAutoID').val();
        var wareHouseAutoID = warehouseID;
    
        var UoM =$(det).closest('tr').find('.umoDropdown option:selected').text().split('|');
        var conversionRate =$(det).closest('tr').find('.conversionRate').val();
        if(category !=='Service') {

        if(det.value > parseFloat(currentStock_pulled)){
            document_by_warehouse_qty(itemAutoID,wareHouseAutoID,' ',itemIssueAutoID,UoM[0],conversionRate,parseFloat(currentStock))
                    $(det).val(0);
        }
        }
    }

    function setQty(mrDetailID, balanceQTY, currentStock){
        var ordQtyId = "#qtyIssued_"+mrDetailID;
        $(ordQtyId).val(balanceQTY);
        check_qty_available(mrDetailID, balanceQTY, currentStock);
    }
</script>