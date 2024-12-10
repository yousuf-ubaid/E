<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
/*$title = $this->lang->line('transaction_add_new_material_issue');
echo head_page($title, false);*/

echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$customer_arr = all_employee_drop();
$location_arr = all_delivery_location_drop_active();
$location_arr_default = default_delivery_location_drop();
$segment_arr = fetch_segment();
$financeyear_arr = all_financeyear_drop(true);
$unitOfMeasure_arr = array('' => 'Select UOM');
$projectExist = project_is_exist();
$project=get_all_boq_project();
$pID = $this->input->post('page_id');
if($pID != '') {
    
    $Documentid = 'MR';
    $warehouseidcurrentdoc = all_warehouse_drop_isactive_inactive($pID,$Documentid);
    if(!empty($warehouseidcurrentdoc) && $warehouseidcurrentdoc['isActive'] == 0)
    {
        $location_arr[trim($warehouseidcurrentdoc['wareHouseAutoID'] ?? '')] = trim($warehouseidcurrentdoc['wareHouseCode'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseLocation'] ?? '') . ' | ' . trim($warehouseidcurrentdoc['wareHouseDescription'] ?? '');
    }
}
?>
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

    <div class="steps">
        <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one');?> - <?php echo $this->lang->line('common_request_header');?><!--Step 1 - Request Header--></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_material_request_detail()" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_two');?> - <?php echo $this->lang->line('common_request_detail');?><!--Step 2 - Request Detail--></span>
        </a>
        <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="load_conformation();" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_three');?> - <?php echo $this->lang->line('common_request_confirmation');?> <!--Step 3 - Request Confirmation--></span>
        </a>
    </div>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="material_request_form"'); ?>
        <div class="row">
<!--            <div class="form-group col-sm-4">
                <label for="segment">Primary Segment <?php /*required_mark(); */?></label>
                <?php /*echo form_dropdown('segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment" required'); */?>
            </div>-->
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_reference_no');?><!--Reference No--> </label>
                <input type="text" class="form-control " id="referenceNo" name="referenceNo">
            </div>
            <div class="form-group col-sm-4">
                <label
                    for="SalesPersonName"><?php echo $this->lang->line('transaction_material_requested_by');?><!--Requested By--> <?php required_mark(); ?></label>
                <div class="input-group">
                    <input type="text" class="form-control" id="employeeName" name="employeeName" required>
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
                    for=""><?php echo $this->lang->line('common_requested_date');?><!--Requested Date--><?php required_mark(); ?></label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="requestedDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="requestedDate"
                           class="form-control" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('transaction_common_warehouse_location');?><!--Warehouse Location--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('location', $location_arr, $location_arr_default, 'class="form-control select2" id="location" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label
                    for="itemType"><?php echo $this->lang->line('transaction_item_type');?><!--Item Type--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('itemType', array('' => $this->lang->line('common_select_type')/*'Select Type'*/, 'Inventory' => $this->lang->line('transaction_inventory')/* 'Inventory'*/, 'Non Inventory' => $this->lang->line('transaction_non_inventory')/*'Non Inventory'*/), 'Inventory', 'class="form-control select2" id="itemType" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for=""><?php echo $this->lang->line('common_narration');?><!--Narration-->  </label>
                <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next--> </button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('transaction_common_add_item_detail');?><!--Add Item Detail--></h4></div>
            <div class="col-md-4">
                <button type="button" onclick="item_detail_modal()" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add_item');?><!--Add Item--></button>
                <button type="button" onclick="edit_all_item_detail_modal()" style="margin-right:8px;" id="editallbtn" class="btn btn-default hidden pull-right"><span class="glyphicon glyphicon-pencil"></span><?php echo $this->lang->line('common_document_edit_all');?><!--Edit All--> </button>
            </div>
        </div>
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 5%">#</th>
                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_common_item_code');?><!--Item Code --></th>
                <th style="min-width: 30%"><?php echo $this->lang->line('common_item_description');?></th><!--Item Description-->
                <th style="min-width: 10%"><?php echo $this->lang->line('common_uom');?> </th><!--UOM-->
                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_current_qty');?> </th><!--Current Qty-->
                <th style="min-width: 10%"><?php echo $this->lang->line('transaction_requested_qty');?></th><!--Requested Qty-->
<!--                <th style="min-width: 10%">WAC </th>
                <th style="min-width: 15%">Value(<?php /*echo $this->common_data['company_data']['company_default_currency']; */?>)</th>-->
                <th style="min-width: 10%">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="item_table_body">
            <tr class="danger">
                <td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--> </b>
                </td>
            </tr>
            </tbody>
            <tfoot id="table_tfoot">

            </tfoot>
        </table>
        <hr>
    </div>
    <div id="step3" class="tab-pane">
        <div id="conform_body"></div>
        <hr>
        <div id="conform_body_attachement">
            <h4 class="modal-title"
                id="Material_issue_attachment_label"><?php echo $this->lang->line('transaction_goods_received_voucher_modal_title');?><!--Modal title--></h4>
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
                <h5 class="modal-title"><?php echo $this->lang->line('transaction_common_add_item_detail'); ?> <!--Add Item Detail--> </h5>
            </div>
            <div class="modal-body">
                <form role="form" id="item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="MaterialIssue_detail_add_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;"><?php echo $this->lang->line('transaction_common_item_code'); ?> <!--Item Code--> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom'); ?> <!--UOM--></th>
                            <th style="width:200px;"><?php echo $this->lang->line('transaction_current_stock'); ?> <!--Current Stock--></th>
                            <!--<th style="width: 150px;">Segment <?php /*required_mark(); */?></th>-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project'); ?><!-- Project --></th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_requested_qty'); ?> <!--Requested Qty--> <?php required_mark(); ?></th>
                            <th style="width: 200px;"><?php echo $this->lang->line('common_comment'); ?> <!--Comment--> </th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs"
                                        onclick="add_more_material_request()"><i
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
                                       placeholder="Item ID,Item Description..."
                                       id="f_search_1">
                                <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]">
                                <input type="hidden" class="form-control currentStock" name="currentStock">
                            </td>
                            <td>
                                <?php echo form_dropdown('UnitOfMeasureID[]', $unitOfMeasure_arr, '', 'class="form-control umoDropdown"  required'); ?>
                            </td>
                            <td>
                                <div class="input-group" style="width: 100%">
                                    <input type="text" name="currentWareHouseStockQty[]" class="form-control currentWareHouseStock" required readonly>
                                </div>
                            </td>
<!--                            <td>
                                <?php /*echo form_dropdown('a_segment[]', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2 segment" required onchange="load_segmentBase_projectID_item(this)"'); */?>
                            </td>-->
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div class="div_projectID_income"> 
                                        <select name="projectID" class="form-control select2">
                                            <option
                                                value=""><?php echo $this->lang->line('common_select_project'); ?> <!--Select Project--> </option>
                                        </select>
                                    <?php  //echo form_dropdown('projectID[]', $project, '', 'class="form-control select2" id="projectID"'); ?>
                                    </div>
                                </td>
                            <?php } ?>
                            <td style="width: 100px">
                                <input type="text" name="quantityRequested[]"
                                       onfocus="this.select();"
                                       onkeyup="validatetb_row(this)"
                                       class="form-control number quantityRequested" required>
                            </td>
                            <td>
                                <textarea class="form-control" rows="1" name="comment[]"
                                          placeholder="Item Comment..."></textarea>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_close'); ?> <!--Close--> </button>
                <button class="btn btn-primary" type="button"
                        onclick="saveMaterialIssue_addItem()"><?php echo $this->lang->line('common_save_change'); ?> <!--Save changes-->
                </button>
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
                <h4 class="modal-title"><?php echo $this->lang->line('transaction_common_edit_item_detail'); ?> <!--Edit Item Detail--> </h4>
            </div>
            <div class="modal-body">
                <form role="form" id="edit_item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="MaterialIssue_detail_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;"><?php echo $this->lang->line('transaction_common_item_code'); ?> <!--Item Code--> <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom'); ?> <!--UOM --><?php required_mark(); ?></th>
                            <th style="width:200px;"><?php echo $this->lang->line('transaction_current_stock'); ?> <!--Current Stock--> <?php required_mark(); ?></th>
                            <!--<th style="width: 150px;">Segment <?php /*required_mark(); */?></th>-->
                            <?php if ($projectExist == 1) { ?>
                            <th><?php echo $this->lang->line('common_project'); ?><!-- Project --> </th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_requested_qty'); ?> <!--Requested Qty--> <?php required_mark(); ?></th>
                            <th style="width: 200px;"><?php echo $this->lang->line('common_comment'); ?> <!--Comment--> </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="text" onkeyup="clearitemAutoIDEdit(event,this)" class="form-control"
                                       id="search"
                                       name="search"
                                       placeholder="Item ID,Item Description...">
                                <input type="hidden" class="form-control" id="itemAutoID_edit" name="itemAutoID">
                                <input type="hidden" class="form-control" id="currentStock_edit" name="currentStock">
                            </td>
                            <td>
                                <?php echo form_dropdown('unitOfMeasureID', $unitOfMeasure_arr, '', 'class="form-control" id="UnitOfMeasureID_edit"  required'); ?>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="currentWareHouseStockQty"
                                           class="form-control" id="currentWareHouseStockQty_edit" required readonly>
                                </div>
                            </td>
<!--                            <td>
                                <?php /*echo form_dropdown('a_segment', $segment_arr, $this->common_data['company_data']['default_segment'], 'class="form-control select2" id="segment_edit" onchange="load_segmentBase_projectID_itemEdit(this)"'); */?>
                            </td>-->
                            <?php if ($projectExist == 1) { ?>
                                <td>
                                    <div id="edit_div_projectID_item"> 
                                        <select name="projectID" id="projectID" class="form-control select2">
                                            <option
                                                value=""><?php echo $this->lang->line('common_select_project'); ?> <!--Select Project--> </option>
                                        </select>
                                    <?php  //echo form_dropdown('projectID', $project, '', 'class="form-control select2" id="projectID_edit"'); ?>
                                   </div>
                                </td>
                            <?php } ?>
                            <td style="width: 100px">
                                <input type="text" name="quantityRequested" onfocus="this.select();"
                                       class="form-control number" id="quantityRequested_edit" required>
                            </td>
                            <td>
                                <textarea class="form-control" rows="1" name="comment"
                                          placeholder="Item Comment..."
                                          id="comment_edit"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_close'); ?> <!--Close-->  </button>
                <button class="btn btn-primary" type="button"
                        onclick="updateMaterialIssue_addItem()"><?php echo $this->lang->line('common_update_changes'); ?> <!--Update changes-->
                </button>
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
                               class="col-sm-3 control-label"><?php echo $this->lang->line('common_employee'); ?> <!--Employee--> </label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('employee_id', $customer_arr, '', 'class="form-control select2" id="employee_id" required'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('common_close'); ?> <!--Close--> </button>
                <button type="button" class="btn btn-primary"
                        onclick="fetch_employee_detail()"><?php echo $this->lang->line('common_add_employee'); ?> <!--Add employee-->  </button>
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
                <h5 class="modal-title"><?php echo $this->lang->line('transaction_gl_account'); ?> <!--GL Account-->  </h5>
            </div>
            <div class="modal-body" id="divglAccount">
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default"
                        type="button"><?php echo $this->lang->line('common_close'); ?> <!--Close-->  </button>
                <button class="btn btn-primary" type="button"
                        onclick="materialAccountUpdate(1)"><?php echo $this->lang->line('transaction_apply_to_all'); ?> <!--Apply to All<?php echo $this->lang->line('common_close'); ?> <!--Close--> -->
                </button>
                <button class="btn btn-primary" type="button"
                        onclick="materialAccountUpdate(0)"><?php echo $this->lang->line('common_save_change'); ?> <!--Save changes-->
                </button>

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
                <h4 class="modal-title"><?php echo $this->lang->line('transaction_common_edit_item_detail'); ?> <!--Edit Item Detail--> </h4>
            </div>
            <div class="modal-body">
                <form role="form" id="edit_all_item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="MaterialIssue_detail_all_edit_table">
                        <thead>
                        <tr>
                            <th style="width: 250px;"><?php echo $this->lang->line('transaction_common_item_code'); ?> <!--Item Code-->  <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_uom'); ?> <!--UOM --> <?php required_mark(); ?></th>
                            <th style="width:200px;"><?php echo $this->lang->line('transaction_current_stock'); ?> <!-- Current Stock--> <?php required_mark(); ?></th>
                            <!--<th style="width: 150px;">Segment <?php /*required_mark(); */?></th>-->
                            <?php if ($projectExist == 1) { ?>
                                <th><?php echo $this->lang->line('common_project'); ?><!-- Project -->  </th>
                            <?php } ?>
                            <th style="width: 150px;"><?php echo $this->lang->line('transaction_requested_qty'); ?> <!--Requested Qty-->  <?php required_mark(); ?></th>
                            <th style="width: 150px;"><?php echo $this->lang->line('common_comment'); ?> <!--Comment--> </th>
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
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_close'); ?> <!--Close-->  </button>
                <button class="btn btn-primary" type="button" onclick="save_material_request_detail_multiple_edit()"><?php echo $this->lang->line('common_update_changes'); ?> <!--Update changes-->
                </button>
            </div>

        </div>
    </div>
</div>
<?php
$data['documentID'] = 'MR';
$this->load->view('system/item/itemmastersub/inc-item-master-sub-config-edit', $data);
?>
<script type="text/javascript">
    var search_id = 1;
    var type;
    var mrAutoID;
    var mrDetailID;
    var EIdNo;
    var ECode;
    var projectID;
    $(document).ready(function () {
        $("[rel=tooltip]").tooltip();
        $('.headerclose').click(function () {
            fetchPage('system/inventory/material_request_management','Test','Material Request');
        });
        $('.select2').select2();
        type = 'Inventory';
        EIdNo = null;
        projectID = null;
        mrAutoID = null;
        mrDetailID = null;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#material_request_form').bootstrapValidator('revalidateField', 'requestedDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
        currency_validation_modal(CurrencyID, 'MR', '', '');
        if (p_id) {
            mrAutoID = p_id;
            load_material_request_header();
            $("#a_link").attr("href", "<?php echo site_url('Inventory/load_material_request_conformation'); ?>/" + mrAutoID);
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

        $('#material_request_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                //segment: {validators: {notEmpty: {message: 'Segment is required.'}}},
                employeeName: {validators: {notEmpty: {message: 'Employee is required.'}}},
                requestedDate: {validators: {notEmpty: {message: 'Requested Date is required.'}}},
                location: {validators: {notEmpty: {message: 'Warehouse Location is required.'}}},
                itemType: {validators: {notEmpty: {message: 'Item Type is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $("#location").prop("disabled", false);
            $("#itemType").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'employeeID', 'value': EIdNo});
            data.push({'name': 'mrAutoID', 'value': mrAutoID});
            data.push({'name': 'requested', 'value': $('#employee_id option:selected').text()});
            data.push({'name': 'location_dec', 'value': $('#location option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Inventory/save_material_request_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    if (data['status']) {
                        $('.btn-wizard').removeClass('disabled');
                        mrAutoID = data['last_id'];
                        $("#a_link").attr("href", "<?php echo site_url('Inventory/load_material_request_conformation'); ?>/" + mrAutoID);
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
            data: {'companyFinanceYearID': companyFinanceYearID},
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
        /** var inv_item = new Bloodhound({
            datumTokenizer: function (d) {
                return Bloodhound.tokenizers.whitespace(d.Match);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: "<?php echo site_url();?>Inventory/fetch_inv_item/?q=%QUERY&t=" + type
        });

         inv_item.initialize();
         $('.search').typeahead(null, {
            minLength: 1,
            highlight: true,
            displayKey: 'Match',
            source: inv_item.ttAdapter()
        }).on('typeahead:selected', function (object, datum) {
            $(this).closest('tr').find('.itemAutoID').val(datum.itemAutoID);
            $(this).closest('tr').find('.currentStock').val(datum.currentStock);
            $(this).closest('tr').find('.d_uom').val(datum.defaultUnitOfMeasure);
            fetch_related_uom_id(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID, this);
            fetch_warehouse_item(datum.itemAutoID, this);
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
                    $(this).closest('tr').find('.currentStock').val(suggestion.currentStock);
                    $(this).closest('tr').find('.d_uom').text(suggestion.defaultUnitOfMeasure);
                    fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                    fetch_warehouse_item(suggestion.itemAutoID, this);
                    $(this).closest('tr').find('.quantityRequested').focus();
                    $(this).closest('tr').css("background-color", 'white');
                }
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');

    }

    function initializeitemTypeahead_edit(type) {
        /**var inv_item = new Bloodhound({
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
            $('#quantityRequested_edit').val();
            $('#itemAutoID_edit').val(datum.itemAutoID);
            $('#currentStock_edit').val(datum.currentStock);
            $('#d_uom_edit').val(datum.defaultUnitOfMeasure);
            fetch_related_uom_id_edit(datum.defaultUnitOfMeasureID, datum.defaultUnitOfMeasureID);
            fetch_warehouse_item_edit(datum.itemAutoID);
        });*/


        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Inventory/fetch_inv_item_stock_adjustment/?&t=' + type,
            onSelect: function (suggestion) {
                setTimeout(function () {
                    $('#quantityRequested_edit').val();
                    $('#itemAutoID_edit').val(suggestion.itemAutoID);
                }, 200);
                //$('#currentWareHouseStockQty_edit').val(suggestion.currentStock);
                $('#d_uom_edit').val(suggestion.defaultUnitOfMeasure);
                $(this).closest('tr').find('#quantityRequested_edit').focus();
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                fetch_warehouse_item_edit(suggestion.itemAutoID);
            }
        });
        $('#search').off('focus.autocomplete');

    }

    function fetch_warehouse_item(itemAutoID, element) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'mrAutoID': mrAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Inventory/fetch_warehouse_item_material_request'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $(element).closest('tr').find('.currentWareHouseStock').val(data['currentStock']);
                } else {
                    //$(element).typeahead('val', '');
                    //$(element).closest('tr').find('.itemAutoID').val('');
                    $(element).closest('tr').find('.currentWareHouseStock').val(0);
                }
                //refreshNotifications(true);
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
            data: {'mrAutoID': mrAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('Inventory/fetch_warehouse_item_material_request'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $('#currentWareHouseStockQty_edit').val(data['currentStock']);
                } else {
                    //$('#search').typeahead('val', '');
                    //$('#itemAutoID_edit').val('');
                    $(element).closest('tr').find('.currentWareHouseStock').val(0);
                }
                //refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_material_request_header() {
        if (mrAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'mrAutoID': mrAutoID},
                url: "<?php echo site_url('Inventory/load_material_request_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#requestedDate').val(data['requestedDate']);
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#narration').val(data['comment']);
                        $('#location').val(data['wareHouseAutoID']).change();
                        if (data["employeeID"] > 0) {
                            $('#employeeName').prop('readonly', true);
                            $('#employeeName').val(data['employeeCode'] + ' | ' + data['employeeName']);
                        } else {
                            $('#employeeName').val(data['employeeName']);
                        }
                        $('#employee_id').val(data['employeeID']).change();
                        EIdNo = data['employeeID'];
                        ECode = data['employeeCode'];
                        $('#referenceNo').val(data['referenceNo']);
                        fetch_material_request_detail();
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

    function fetch_material_request_detail() {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'mrAutoID': mrAutoID},
            url: "<?php echo site_url('Inventory/fetch_material_request_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                $('#item_table_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['detail'])) {
                    $('#item_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');
                   
                    $("#location").prop("disabled", false);
                    $("#itemType").prop("disabled", false);
                    $("#editallbtn").addClass("hidden");
                } else {
                    $("#location").prop("disabled", true);
                    $("#itemType").prop("disabled", true);
                    currency_decimal = <?php echo json_encode($this->common_data['company_data']['company_default_decimal']); ?>;
                    $.each(data['detail'], function (key, value) {
                        var comment ='';
                        if(value['comments']){
                            var comment = ' - ' + value['comments'];
                        }
                        if (value['isSubitemExist'] == 1) {
                            var colour = 'color: #dad835 !important';
                            colour = '';


                            string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + comment + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['CurrentStockAddTime'] + '</td><td class="text-right">' + value['qtyRequested'] + '</td><td class="text-right"> &nbsp;&nbsp;<a onclick="edit_item(' + value['mrDetailID'] + ',this);"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['mrDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                        } else {

                            string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + comment  + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-right">' + value['CurrentStockAddTime'] + '</td><td class="text-right">' + value['qtyRequested'] + '</td><td class="text-right"> &nbsp;&nbsp; <a onclick="edit_item(' + value['mrDetailID'] + ',this);"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp; |&nbsp;&nbsp; <a onclick="delete_item(' + value['mrDetailID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';

                        }
                        /*<td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td>*/
                        /*<td class="text-right">' + parseFloat(value['currentlWacAmount']).formatMoney(currency_decimal, '.', ',') + '</td><td class="text-right">' + parseFloat(value['totalValue']).formatMoney(currency_decimal, '.', ',') + '</td>*/

                        $('#item_table_body').append(string);


                        total += parseFloat(value['totalValue']);
                        x++;
                    });
                    $("#editallbtn").removeClass("hidden");
                }
/*                $('#table_tfoot').html('<tr> <td class="text-right" colspan="4"><?php echo $this->lang->line('common_total');?></td><td class="text-right total">' + total.formatMoney(currency_decimal, '.', ',') + '</td><td></td></tr>');*/
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
        data.push({name: "masterID", value: mrAutoID});
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
                fetch_material_request_detail();
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

    function item_detail_modal() {
        if (mrAutoID) {
            $('.search').typeahead('destroy');
            $('#item_detail_form')[0].reset();
            $('#MaterialIssue_detail_add_table tbody tr').not(':first').remove();
            $('.search').typeahead('val', '');
            $('.itemAutoID').val('');
            initializeitemTypeahead(type, 1);
            $('#a_segment').val($('#segment').val());
            $('.f_search').closest('tr').css("background-color",'white');
            $('.quantityRequested').closest('tr').css("background-color",'white');
            $("#item_detail_modal").modal({backdrop: "static"});
        
            load_segmentBase_projectID_item('');

        }
    }

    function load_conformation() {
        if (mrAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'mrAutoID': mrAutoID, 'html': true},
                url: "<?php echo site_url('Inventory/load_material_request_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body').html(data);
                    $("#a_link").attr("href", "<?php echo site_url('Inventory/load_material_request_conformation'); ?>/" + mrAutoID);
                    attachment_modal_MaterialIssue(mrAutoID, "<?php echo $this->lang->line('transaction_material_issue');?>", "MR");
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
        if (mrAutoID) {
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
                        data: {'mrAutoID': mrAutoID},
                        url: "<?php echo site_url('Inventory/material_request_item_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data) {
                                fetchPage('system/inventory/material_request_management','Test','Material Request');
                            }

                        }, error: function (xhr, textStatus, errorThrown) {
                            myAlert('e', 'Error (' + textStatus + ') : ' + xhr.responseText);
                        }
                    });
                });
        }
    }

    function save_draft() {
        if (mrAutoID) {
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
                    fetchPage('system/inventory/material_request_management', mrAutoID, 'Material Request');
                });
        }
    }

    function delete_item(id) {
        if (mrAutoID) {
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
                        data: {'mrDetailID': id},
                        url: "<?php echo site_url('Inventory/delete_material_request_item'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_material_request_detail();
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
        if (mrAutoID) {
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
                    load_segmentBase_projectID_item('', true);
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'mrDetailID': id},
                        url: "<?php echo site_url('Inventory/load_material_request_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            mrDetailID = data['mrDetailID'];
                            if(data['projectID'] == 0){
                                projectID = '';
                            }else{
                                projectID = data['projectID'];
                            }
                            //projectID = data['projectID'];
                            $('#search').typeahead('destroy');
                            $('#search').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - "+data['seconeryItemCode']);
                            $('#itemAutoID_edit').val(data.itemAutoID);
                            $('#currentStock_edit').val(data.Stock);
                            $('#currentWareHouseStockQty_edit').val(data.Stock);
                            $('#d_uom_edit').text(data.defaultUOM);
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['unitOfMeasureID']);
                            $('#quantityRequested_edit').val(data['qtyRequested']);
                            $('#projectID').val(projectID).change();
                            $('#segment_edit').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            //$('#projectID_edit').val(data['projectID']).change();
                            $('#comment_edit').val(data['comments']);
                            initializeitemTypeahead_edit(type);
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
                    
                    $('#Material_issue_attachment').empty();
                    $('#Material_issue_attachment').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_Material_issue_attachement(mrAutoID, DocumentSystemCode, myFileName) {
        if (mrAutoID) {
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
                        data: {'attachmentID': mrAutoID, 'myFileName': myFileName},
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

    function add_more_material_request() {
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#MaterialIssue_detail_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#MaterialIssue_detail_add_table').append(appendData);
        var lenght = $('#MaterialIssue_detail_add_table tbody tr').length - 1;
        $('#f_search_'+ search_id).closest('tr').css("background-color",'white');
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
        data.push({'name': 'mrAutoID', 'value': mrAutoID});
        data.push({'name': 'mrDetailID', 'value': mrDetailID});
        $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });

        $('.quantityRequested').each(function () {
            if (this.value == '' ||this.value == 0 ) {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/save_material_request_detail_multiple'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    mrDetailID = null;
                    $('#item_detail_form')[0].reset();
                    setTimeout(function () {
                        fetch_material_request_detail();
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
        var $form = $('#edit_item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'mrAutoID', 'value': mrAutoID});
        data.push({'name': 'mrDetailID', 'value': mrDetailID});
        data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Inventory/save_material_request_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    mrDetailID = null;
                    $('#item_detail_form')[0].reset();
                    setTimeout(function () {
                        fetch_material_request_detail();
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

    function load_segmentBase_projectID_item(segment, isEdit=false) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Procurement/load_project_segmentBase_multiple"); ?>',
            dataType: 'html',
            data: {segment: segment.value,'post_doc': 'MR'},
            async: true,
            beforeSend: function () {
                startLoad();
                $('#projectID').remove();
            },
            success: function (data) {
                if(isEdit){
                    $('#edit_div_projectID_item').html(data);
                    $('#edit_div_projectID_item').find('#projectID').attr('name', 'projectID');
                }
                else{
                    //$(segment).closest('tr').find('.div_projectID_income').html(data);
                    $('.div_projectID_income').html(data);
                }
                //$(segment).closest('tr').find('.div_projectID_income').html(data);
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

    /*function checkCurrentStock(det) {
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
    }*/

    function edit_all_item_detail_modal(){
        load_segmentBase_projectID_item('', true);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'mrAutoID': mrAutoID},
            url: "<?php echo site_url('Inventory/fetch_material_request_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                var total = 0;
                var descm = 2;
                $('#edit_item_table_body').empty();
                var x = 2;
                if (jQuery.isEmptyObject(data)) {
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                    $("#location").prop("disabled", false);
                    $("#itemType").prop("disabled", false);
                    $("#editallbtn").addClass("hidden");
                } else {
                    $("#location").prop("disabled", true);
                    $("#itemType").prop("disabled", true);
                    let project_html = $('#projectID').html();
                    $.each(data['detail'], function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $unitOfMeasure_arr, '', 'class="form-control select2 umoDropdown"  required')) ?>';
                        //var project = '<?php //echo str_replace(array("\n", '<select'), array('', '<select id="proj_\'+key+\'"'), form_dropdown('projectID[]', $project, '', 'class="form-control select2 project"  required ')) ?>';
                        var project = '<td style="width: 100px"><select name="projectID[]" class="form-control select2 projectID" id="edit_prID_'+key+'">'+project_html+'</td>';

                        //var Segment = '<?php //echo str_replace(array("\n", '<select'), array('', '<select id="seg_\'+key+\'"'), form_dropdown('a_segment[]', $segment_arr, '', 'class="form-control select2 segment"  required onchange="load_segmentBase_projectID_item(this)"')) ?>';
                        var string = '<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" value="' + value['itemDescription'] + ' - ' + value['itemSystemCodeeditall'] + ' -'+ value['seconeryItemCodeedditall']+'" class="form-control search f_search" name="search[]" placeholder="Item ID,Item Description..." id="f_search_'+ x +'"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" value="' + value['itemAutoID'] + '"> <input type="hidden" class="form-control mrDetailID" name="mrDetailID[]" value="' + value['mrDetailID'] + '"> <input type="hidden" class="form-control currentStock" name="currentStock[]"></td> <td>'+ UOM +'</td> <td> <div class="input-group"> <input type="text" name="currentWareHouseStockQty[]" class="form-control currentWareHouseStock" value="' + value['stock'] + '" required readonly> </div> </td>'+project+'<td style="width: 100px"> <input type="text" name="quantityRequested[]" onfocus="this.select();" value="' + value['qtyRequested'] + '" class="form-control number quantityRequested"required> </td><td> <textarea class="form-control" rows="1" name="comment[]" placeholder="Item Comment...">' + value['comments'] + '</textarea> </td><td class="remove-td"><a onclick="delete_item_edit_all(' + value['mrDetailID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                        $('#edit_item_table_body').append(string);
                        //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                        //$('#seg_'+key).val(value['segmentID'] + '|' + value['segmentCode']).change();
                        //$('#proj_'+key).val(value['projectID']).change();
                        if(value['projectID']==0){
                            projectID='';
                        }else{
                            projectID=value['projectID'];
                        }
                        $('#edit_prID_'+key).val(projectID);

                        fetch_related_uom_id( value['defaultUOMID'],value['unitOfMeasureID'],$('#uom_'+key));
                        initializeitemTypeahead(type, x);
                        x++;
                    });
                    $('.select2').select2();
                    search_id=x-1;
                    $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                }
                stopLoad();

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function add_more_edit_material_issue() {
        //$('.search').typeahead('destroy');
        search_id += 1;
        $('select.select2').select2('destroy');
        var appendData = $('#MaterialIssue_detail_all_edit_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#MaterialIssue_detail_all_edit_table').append(appendData);
        var lenght = $('#MaterialIssue_detail_all_edit_table tbody tr').length - 1;
        $('#f_search_'+ search_id).closest('tr').css("background-color",'white');
        $(".select2").select2();
        initializeitemTypeahead(type, search_id);
        number_validation();
    }


    function save_material_request_detail_multiple_edit() {
        var $form = $('#edit_all_item_detail_form');
        var data = $form.serializeArray();
        data.push({'name': 'mrAutoID', 'value': mrAutoID});
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
            url: "<?php echo site_url('Inventory/save_material_request_detail_multiple_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#edit_all_item_detail_form')[0].reset();
                    setTimeout(function () {
                        fetch_material_request_detail();
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


    function delete_item_edit_all(id, value,det) {
        if (mrAutoID) {
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
                        data: {'mrDetailID': id},
                        url: "<?php echo site_url('Inventory/delete_material_item'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            fetch_material_request_detail();
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

    function confirm_all_item_detail_modal(itemAutoIdArr){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'mrAutoID': mrAutoID},
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
                    $('#edit_item_table_body').append('<tr class="danger"><td colspan="8" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                    $("#location").prop("disabled", false);
                    $("#itemType").prop("disabled", false);
                    $("#editallbtn").addClass("hidden");
                } else {
                    $("#location").prop("disabled", true);
                    $("#itemType").prop("disabled", true);
                    $.each(data['detail'], function (key, value) {
                        var UOM = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="uom_\'+key+\'"'), form_dropdown('UnitOfMeasureID[]', $unitOfMeasure_arr, '', 'class="form-control select2 umoDropdown"  required')) ?>';
                        var Segment = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="seg_\'+key+\'"'), form_dropdown('a_segment[]', $segment_arr, '', 'class="form-control select2 segment"  required onchange="load_segmentBase_projectID_item(this)"')) ?>';
                        var string = '<tr> <td> <input type="text" onkeyup="clearitemAutoID(event,this)" value="' + value['itemDescription'] + ' (' + value['itemSystemCode'] + ')" class="form-control search f_search" name="search[]" placeholder="Item ID,Item Description..." id="f_search_'+ x +'"> <input type="hidden" class="form-control itemAutoID" name="itemAutoID[]" value="' + value['itemAutoID'] + '"> <input type="hidden" class="form-control mrDetailID" name="mrDetailID[]" value="' + value['mrDetailID'] + '"> <input type="hidden" class="form-control currentStock" name="currentStock[]"></td> <td>'+ UOM +'</td> <td> <div class="input-group"> <input type="text" name="currentWareHouseStockQty[]" class="form-control currentWareHouseStock" value="' + value['stock'] + '" required readonly> </div> </td> <td>'+ Segment +'</td> <td style="width: 100px"> <input type="text" name="quantityRequested[]" onfocus="this.select();" value="' + value['qtyIssued'] + '" class="form-control number quantityRequested"required> </td><td> <textarea class="form-control" rows="1" name="comment[]" placeholder="Item Comment...">' + value['comments'] + '</textarea> </td><td class="remove-td"><a onclick="delete_item_edit_all(' + value['mrDetailID'] + ',\'' + value['itemDescription'] + '\',this);"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td></tr>';
                        $('#edit_item_table_body').append(string);
                        //$('#uom_'+key).val(value['unitOfMeasureID']).change();
                        $('#seg_'+key).val(value['segmentID'] + '|' + value['segmentCode']).change();
                        fetch_related_uom_id( value['defaultUOMID'],value['unitOfMeasureID'],$('#uom_'+key));
                        initializeitemTypeahead(type, x);
                        x++;
                    });
                    search_id=x-1;
                    $("#all_item_edit_detail_modal").modal({backdrop: "static"});
                    $.each(itemAutoIdArr, function (key, valu) {
                        $('.itemAutoID').each(function () {
                            if(this.value == valu['itemAutoID']){
                                $(this).closest('tr').css("background-color",'#ffb2b2');
                            }
                        });
                    });
                }
                stopLoad();

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

</script>