<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);


echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$umo_arr = array('' => 'Select UOM');//all_umo_drop();
$location_arr = all_delivery_location_drop_active();
$location_arr_default = default_delivery_location_drop();
$financeyear_arr = all_financeyear_drop(true);
$segment_arr = fetch_segment();
$stoccounttype = stock_type_dropdown();
$stoccounttype = stock_type_dropdown();
$segment_arr_default = default_segment_drop();
$adjustmentType_arr = array('' => 'Select Type', 'Inventory' => 'Inventory', 'Non Inventory' => 'Non Inventory');
$projectExist = project_is_exist();
$financeyearperiodYN = getPolicyValues('FPC', 'All');
$pID = $this->input->post('page_id');
if($pID != '') {
    
    $Documentid = 'SCNT';
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
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one');?> - <?php echo $this->lang->line('inventory_stock_counting_header');?><!--Step 1 - Stock Counting Header--> </span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" onclick="fetch_detail();" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_two');?> - <?php echo $this->lang->line('inventory_stock_counting_detail');?><!--Step 2 - Stock Counting Detail--> </span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" onclick="delete_is_update_zero();"  data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_three');?> - <?php echo $this->lang->line('inventory_stock_counting_confirmation');?><!--Step 3 - Stock Counting Confirmation--></span>
            </a>
           
        </div>
        
 
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="stock_counting_form"'); ?>
        <input type="hidden" id="useDocProceed" name="useDocProceed" value="1">
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="stockCountingType"><?php echo $this->lang->line('erp_item_category_cat_type');?><!--Category Type--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('stockCountingType', $adjustmentType_arr, '', 'class="form-control select2" id="stockCountingType" required'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="adjustmentType"><?php echo $this->lang->line('common_type');?><!--Type--> <?php required_mark(); ?></label>
                <select name="adjustmentType" id="adjustmentType" class="form-control select2">
                    <option value="0"><?php echo $this->lang->line('transaction_common_stock');?><!--Stock--></option>
                    <option value="1"><?php echo $this->lang->line('transaction_adjustment_wac');?><!--Wac--></option>
                </select>
            </div>
            <div class="form-group col-sm-4">
                
                <label for="segment"><?php echo $this->lang->line('transaction_common_stock_counttype');?><!--stock count type--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('stockcounttypeid', $stoccounttype,'' , 'class="form-control select2" id="stockcounttypeid" required'); ?>

            </div>
           
        </div>
        <div class="row">
        <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('transaction_primary_segment');?><!--Primary Segment--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('segment', $segment_arr, $segment_arr_default , 'class="form-control select2" id="segment" required'); ?>
            </div>

            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_reference_no');?><!--Reference No--></label>
                <input type="text" class="form-control " id="referenceNo" name="referenceNo">
            </div>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_date');?><!--Date--> <?php required_mark(); ?></label>

                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="stockCountingDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="stockCountingDate"
                           class="form-control" required>
                </div>
            </div>

        </div>
        <div class="row">
        <?php if($financeyearperiodYN==1){ ?>
            <div class="form-group col-sm-4">
                <label for="financeyear"><?php echo $this->lang->line('common_financial_year');?><!--Financial Year--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('financeyear', $financeyear_arr, $this->common_data['company_data']['companyFinanceYearID'], 'class="form-control" id="financeyear" required onchange="fetch_finance_year_period(this.value)"'); ?>
            </div>
            <?php } ?>
            <?php if($financeyearperiodYN==1){ ?>
            <div class="form-group col-sm-4">
                <label for="financeyear_period"><?php echo $this->lang->line('transaction_common_financial_period');?><!--Financial Period--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('financeyear_period', array('' => 'Finance Period'), '', 'class="form-control" id="financeyear_period" required'); ?>
            </div>
            <?php } ?>
            <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_Location');?><!--Location-->  <?php required_mark(); ?></label>

                <?php echo form_dropdown('location', $location_arr, $location_arr_default, 'class="form-control select2" id="location" required'); ?>
            </div>

        </div>
        <div class="row">
        <div class="form-group col-sm-4">
                <label><?php echo $this->lang->line('common_narration');?><!--Narration--> </label>
                <textarea class="form-control" rows="3" id="narration" name="narration"></textarea>
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary"
                    type="submit"><?php echo $this->lang->line('common_save_and_next');?><!--Save & Next-->
            </button>
        </div>
        <?php echo form_close(); ?>
        <!--</form>-->
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-8"><h4>&nbsp;&nbsp;&nbsp;<i
                            class="fa fa-hand-o-right"></i><?php echo $this->lang->line('common_add_detail');?><!--Add Detail-->
                </h4>
            </div>
            <div class="col-md-4">
                <button type="button" id="stockadd" onclick="item_detail_modal()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('common_add');?><!--Add-->
                </button>
            </div>
        </div>
        <div class="row">
            <?php echo form_open('', 'role="form" id="frm_filter"'); ?>
            <input type="hidden" id="printID" name="printID">
            <div class="col-sm-12">
                <div class="col-sm-3">
                    <label><?php echo $this->lang->line('erp_item_sub_category');?><!--Sub Category--> </label>
                    <!--Sub Category-->
                    <select name="subcategoryID[]" id="subcategoryID" class="form-control searchbox"
                            onchange="loadSubSubCategory()" multiple="multiple">
                        <!--Select Category-->
                    </select>
                </div>
                <div class="col-sm-3">
                    <label><?php echo $this->lang->line('erp_item_sub_sub_category');?><!--Sub Sub Category--> </label>
                    <!--Sub Sub Category-->
                    <select name="subsubcategoryID[]" id="subsubcategoryID"
                            class="form-control searchbox" onchange="setsubsublocalstorage()" multiple="multiple">
                        <!--Select Category-->
                    </select>
                </div>
                </form>
                <div class="col-sm-2">
                    <br>
                    <button type="button" onclick="fetch_detail()" class="btn btn-primary "><?php echo $this->lang->line('common_load');?><!--Load--> </button>
                    <button type="button" onclick="print_stock_counting_filter()" class="btn btn-default "><i
                                class="fa fa-print" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
        <br>
        <?php echo form_open('', 'role="form" id="stock_counting_detail_form"'); ?>
        <table class="<?php echo table_class(); ?>">
            <thead>
            <tr>
                <th style="min-width: 3%">#</th>
                <th style="min-width: 9%"><?php echo $this->lang->line('transaction_common_item_code');?><!--Item Code--></th>
                <th style="min-width: 20%"><?php echo $this->lang->line('common_item_description');?><!--Item Description--></th>
                <th style="min-width: 10%">Bin Location<!--Bin Location--></th>
                <th style="min-width: 10%"><?php echo $this->lang->line('common_uom');?><!--UOM--></th>
                <th style="min-width: 10%" class="" id="invetstatusid"><?php echo $this->lang->line('transaction_common_invetry_status');?><!--invetory stsus--></th>
                <th style="min-width: 5%" id="adjstmmenttyp"></th>
                <th style="min-width: 3%">&nbsp;</th>
                <th style="min-width: 3%">
                    <input type="checkbox" onclick="checkall()" name="mainchkbox" id="mainchkbox">
                    <button type="button" title="Delete All" class="btn btn-xs btn-danger" onclick="deleteAll()"><i
                                class="fa fa-trash-o" aria-hidden="true"></i></button>
                </th>
            </tr>
            </thead>

            <tbody id="item_table_body">
            <tr class="danger">
                <td colspan="7" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--> </b></td>
            </tr>
            </tbody>
        </table>
        </form>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev"
                    onclick=""><?php echo $this->lang->line('common_previous');?><!--Previous-->
            </button>
            <button class="btn btn-primary next"
                    onclick="delete_is_update_zero()"><?php echo $this->lang->line('common_next');?><!--Next-->
            </button>
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
                <h5 class="modal-title"><?php echo $this->lang->line('inventory_add_counting'); ?> <!--Add Counting--></h5>
                <!---->
            </div>
            <div class="modal-body">
                <form role="form" id="item_detail_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="StockCounting_detail_add_table">
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
                                <th>Project Category</th>
                                <th>Project Subcategory</th>
                            <?php } ?>
                            <th style="width:200px;"
                                class="hidden"><?php echo $this->lang->line('transaction_current_stock'); ?><?php required_mark(); ?></th>
                            <!--Current Stock-->
                            <th style="width:200px;"
                                class="hidden"><?php echo $this->lang->line('transaction_current_wac'); ?><?php required_mark(); ?></th>
                            <!--Current Wac-->
                            <th style="width:100px;"><?php echo $this->lang->line('transaction_current_stock'); ?> <!--Current Stock--> <?php required_mark(); ?></th>
                            <th style="width:200px;"
                                class="hidden"><?php echo $this->lang->line('transaction_adjustment_wac_w'); ?><?php required_mark(); ?></th>
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
                                            <option
                                                    value=""><?php echo $this->lang->line('transaction_goods_received_select_project'); ?></option>
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
                            <td class="hidden">
                                <div class="input-group">
                                    <!--<span class="input-group-addon input-group-addon-mini d_uom">Each</span>-->
                                    <input type="text" name="currentWareHouseStock[]"
                                           class="form-control currentWareHouseStock" readonly required>
                                </div>
                            </td>
                            <td class="hidden">
                                <div class="input-group">
                                    <span
                                            class="input-group-addon input-group-addon-mini"><?php echo $this->common_data['company_data']['company_default_currency']; ?></span>
                                    <input type="text" name="currentWac[]" class="form-control currentWac" readonly
                                           required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <!--<span class="input-group-addon input-group-addon-mini d_uom">Each</span>-->
                                    <input type="text" name="adjustment_Stock[]" onfocus="this.select();"
                                           onkeyup="validatetb_row(this)"
                                           class="form-control number adjustment_Stock" required>
                                </div>
                            </td>
                            <td style="width: 100px" class="hidden">
                                <div class="input-group">
                                    <span
                                            class="input-group-addon input-group-addon-mini"><?php echo $this->common_data['company_data']['company_default_currency']; ?></span>
                                    <input type="text" name="adjustment_wac[]" onfocus="this.select();"
                                           class="form-control number adjustment_wac"
                                           required>
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
                                <th>Project Category</th>
                                <th>Project Subcategory</th>
                            <?php } ?>
                            <th style="width:200px;"
                                class="hidden"><?php echo $this->lang->line('transaction_current_stock'); ?><?php required_mark(); ?></th>
                            <!--Current Stock-->
                            <th style="width:150px;"
                                class="hidden"><?php echo $this->lang->line('transaction_current_wac'); ?><?php required_mark(); ?></th>
                            <!--Current Wac-->
                            <th style="width:100px;"><?php echo $this->lang->line('transaction_adjustment_stock'); ?><?php required_mark(); ?></th>
                            <!--Adjustment Stock-->
                            <th style="width:200px;"
                                class="hidden"><?php echo $this->lang->line('transaction_adjustment_wac_w'); ?><?php required_mark(); ?></th>
                            <!--Adjustment Wac-->
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
                                            <option
                                                    value=""><?php echo $this->lang->line('common_select_project'); ?></option>
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
                            <td class="hidden">
                                <div class="input-group">
                                    <input type="text" name="currentWareHouseStock" id="currentWareHouseStock_edit"
                                           class="form-control" readonly required>
                                </div>
                            </td>
                            <td class="hidden">
                                <div class="input-group">
                                    <span
                                            class="input-group-addon input-group-addon-mini"><?php echo $this->common_data['company_data']['company_default_currency']; ?></span>
                                    <input type="text" name="currentWac" id="currentWac_edit" class="form-control"
                                           readonly
                                           required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" name="adjustment_Stock" onfocus="this.select();"
                                           class="form-control number" id="adjustment_Stock_edit" required>
                                </div>
                            </td>
                            <td style="width: 100px" class="hidden">
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


<div class="modal fade" id="access_denied_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" style="color: red;" id="title_generate_exceed">You cannot use this Item. This item has been pulled in following docuemnts.</h5>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Description</th>
                        <th>Document Code</th>
                    </tr>
                    </thead>
                    <tbody id="access_denied_item_body">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="usedDocSkip()" class="btn btn-default" data-dismiss="modal">Skip and Proceed</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="access_denied_itemwise" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" style="color: red;" id="title_generate_exceed">You cannot use this Item. This item has been pulled in following docuemnts.</h5>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Document Code</th>
                    </tr>
                    </thead>
                    <tbody id="access_denied_itemwise_body">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="usedDocSkip()" class="btn btn-default" data-dismiss="modal">Skip and Proceed</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="update_stock_validated_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Update Stock Counting</h4>
            </div>
            <div class="modal-body">
                <form role="form" id="update_stock_validated_form" class="form-horizontal">
                    <table class="table table-bordered table-condensed no-color" id="update_stock_validated_table">
                        <thead>
                        <tr>
                            <th colspan="3">Item</th>
                            <th colspan="3">Document Details</th>
                            <th colspan="2">Current Details</th>
                        </tr>
                        <tr>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th>Item UOM</th>

                            <th>Previous Stock</th>
                            <th>Previous Current Stock</th>
                            <th>Previous Adjusted Stock</th>

                            <th>Current Stock</th>
                            <th>Actual Stock</th>
                        </tr>
                        </thead>
                        <tbody id="update_stock_validated_table_body" style="background: #ffb2b2">

                        </tbody>
                        <tfoot id="update_stock_validated_table_tfoot">

                        </tfoot>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                <button class="btn btn-primary" type="button" onclick="update_stock_validated_counting()"><?php echo $this->lang->line('common_update_changes');?>
                </button><!--Update changes-->
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
    var stockCountingAutoID;
    var stockCountingDetailsAutoID;
    var projectID;
    var stockupdatetype;
    var projectcategory;
    var projectsubcat;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/inventory/stock_counting_management', stockCountingAutoID, 'Stock Counting');
        });
        $('.select2').select2();
        number_validation();
        type = 'Inventory';
        adjustmentType = 0;
        projectID = null;
        stockCountingAutoID = null;
        stockCountingDetailsAutoID = null;
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#stock_counting_form').bootstrapValidator('revalidateField', 'stockCountingDate');
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        CurrencyID = <?php echo json_encode($this->common_data['company_data']['company_default_currencyID']); ?>;
        currency_validation_modal(CurrencyID, 'SA', '', '');
        if (p_id) {
            stockCountingAutoID = p_id;
            laad_stock_counting_header();
            $("#a_link").attr("href", "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>/" + stockCountingAutoID);
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

        $('#stock_counting_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                stockCountingDate: {validators: {notEmpty: {message: 'Date is required.'}}},
                //location: {validators: {notEmpty: {message: 'Location is required.'}}},
                stockCountingType: {validators: {notEmpty: {message: 'Category Type is required.'}}},
                adjustmentType: {validators: {notEmpty: {message: 'Type is required.'}}},
                segment: {validators: {notEmpty: {message: 'Primary Segment is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $('#location').attr('disabled', false);
            $("#stockCountingType").prop("disabled", false);
            $("#adjustmentType").prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'stockCountingAutoID', 'value': stockCountingAutoID});
            data.push({'name': 'companyFinanceYear', 'value': $('#financeyear option:selected').text()});
            data.push({'name': 'location_dec', 'value': $('#location option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('StockCounting/save_stock_counting_header'); ?>",
                beforeSend: function () {
                    startLoad();

                },
                success: function (data) {
                    type = $('#stockCountingType').val();
                    adjustmentType = $('#adjustmentType').val();
                    stopLoad();

                    if (data['status']) {
                        refreshNotifications(true);
                        $('.btn-wizard').removeClass('disabled');
                        $('#location').attr('disabled', true);
                        stockCountingAutoID = data['last_id'];
                        fetch_detail();
                        $("#a_link").attr("href", "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>/" + stockCountingAutoID);
                        if ($('#stockCountingType').val() != 'Non Inventory') {
                            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_scnt'); ?>/" + stockCountingAutoID + '/SCNT');
                        } else {
                            $("#de_link").hide();
                        }
                        loadSubCategory();


                        $('[href=#step2]').tab('show');
                    }else{
                        if(data['wrd']=='w'){
                            $('#access_denied_item_body').empty();
                            x = 1;
                            if (jQuery.isEmptyObject(data['docs']['usedDocs'])) {
                                $('#access_denied_item_body').append('<tr class="danger"><td colspan="3" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                            } else {
                                $.each(data['docs']['usedDocs'], function (key, value) {
                                    $('#access_denied_item_body').append('<tr><td>' + x + '</td><td>' + value['itemDescription'] + '</td><td><a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'' + value['documentID'] + '\',' + value['documentAutoID'] + ')">' + value['documentCode'] + '</a></td></tr>');
                                    x++;
                                });
                                $('#access_denied_item').modal('show');
                            }
                        }else{
                            refreshNotifications(true);
                        }
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

        $("#subcategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });

        $("#subsubcategoryID").multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
    });

    function edit_glaccount(stockCountingDetailsAutoID, PLGLAutoID, BLGLAutoID) {
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
                $('#detailID').val(stockCountingDetailsAutoID);
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
        data.push({name: "masterID", value: stockCountingAutoID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('StockCounting/stockadjustmentAccountUpdate'); ?>",
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

    function laad_stock_counting_header() {
        if (stockCountingAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'stockCountingAutoID': stockCountingAutoID},
                url: "<?php echo site_url('StockCounting/laad_stock_counting_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        stockCountingAutoID = data['stockCountingAutoID'];
                        if (type != 'Non Inventory') {
                            $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_scnt'); ?>/" + stockCountingAutoID + '/SCNT');
                        } else {
                            $("#de_link").hide();
                        }
                        $('#stockCountingType').val(data['stockCountingType']).change();
                        $('#adjustmentType').val(data['adjustmentType']).change();
                        $('#segment').val(data['segmentID'] + '|' + data['segmentCode']).change();
                        $('#stockCountingDate').val(data['stockCountingDate']);
                        $('#financeyear').val(data['companyFinanceYearID']);
                        fetch_finance_year_period(data['companyFinanceYearID'], data['companyFinancePeriodID']);
                        $("#location").val(data['wareHouseAutoID']).change();
                        $('#location').attr('disabled', true);
                        $('#narration').val(data['comment']);
                        $('#referenceNo').val(data['referenceNo']);
                        $('#stockcounttypeid').val(data['stockcountingtypeID']).change();
                        fetch_detail();
                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        type = data['stockCountingType'];
                        adjustmentType = data['adjustmentType'];
                        $('#search').typeahead('destroy');
                        initializeitemTypeahead(type);
                        loadSubCategory();
                        var subsubcategorycounting = window.localStorage.getItem('subsubcategorycounting');
                        var subcategorycounting = window.localStorage.getItem('subcategorycounting');
                        if (!jQuery.isEmptyObject(subcategorycounting)) {
                            if (subcategorycounting != null || subcategorycounting != undefined) {
                                var subcat = subcategorycounting.split(',');
                                $('#subcategoryID').val(subcat).multiselect2("refresh");
                                loadSubSubCategory()
                                fetch_detail()
                            }
                        }
                        if (!jQuery.isEmptyObject(subsubcategorycounting)) {
                            if (subsubcategorycounting != null || subsubcategorycounting != undefined) {
                                var subsubcat = subsubcategorycounting.split(',');
                                $('#subsubcategoryID').val(subsubcat).multiselect2("refresh");
                                fetch_detail()
                            }
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
    }

    function item_detail_modal() {
        if (stockCountingAutoID) {
            $('.f_search').typeahead('destroy');
            $('#item_detail_form')[0].reset();
            $('#StockCounting_detail_add_table tbody tr').not(':first').remove();
            $('.f_search').closest('tr').css("background-color", 'white');
            $('.adjustment_Stock').closest('tr').css("background-color", 'white');
            $('.f_search').typeahead('val', '');
            $('.itemAutoID').val('');
            //initializeitemTypeahead(type,1);
            $("#item_detail_modal").modal({backdrop: "static"});
            initializeitemTypeahead(type, 1);
            //$('#a_segment').val($('#segment').val());
        }
    }

    function fetch_detail() {
        if (stockCountingAutoID) {
            var stocktypeid = $('#stockcounttypeid').val();
            var subcategoryID = $('#subcategoryID').val();
            var subsubcategoryID = $('#subsubcategoryID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'stockCountingAutoID': stockCountingAutoID,
                    'subcategoryID': subcategoryID,
                    'subsubcategoryID': subsubcategoryID,
                    'stocktypeid' : stocktypeid
                },
                url: "<?php echo site_url('StockCounting/fetch_stock_counting_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                 
                    $('#item_table_body').empty();
                    x = 1;
                    tot = 0;
                    currency_decimal = 2;
                    if (jQuery.isEmptyObject(data)) {
                        $("#stockCountingType").prop("disabled", false);
                        $("#adjustmentType").prop("disabled", false);
                        $('#item_table_body').append('<tr class="danger"><td colspan="7" class="text-center"><b>No Records Found</b></td></tr>');
                        if(adjustmentType==0){
                            $('#adjstmmenttyp').html('Stock');
                            $('#stockadd').removeClass('hidden');
                        }else{
                            $('#adjstmmenttyp').html('Wac');
                            $('#stockadd').addClass('hidden');
                        }
                    } else {
                        $("#stockCountingType").prop("disabled", true);
                        $("#adjustmentType").prop("disabled", true);
                        tot_amount = 0;
                        if(adjustmentType==0){
                            $('#adjstmmenttyp').html('Stock');
                            $('#stockadd').removeClass('hidden');
                        }else{
                            $('#adjstmmenttyp').html('Wac');
                            $('#stockadd').addClass('hidden');
                        }
                        $.each(data, function (key, value) {


                            currency_decimal = value['companyLocalCurrencyDecimalPlaces'];

                            var previousStock = value['previousStock'];
                            var currentStock = value['currentStock'];
                            var stocktypestatus =  value['stockcountingtypeID'];

                            if (value['isSubitemExist'] == 1 && previousStock != currentStock && value['isUpdated'] == 1) {

                                var colour = '';
                                var jsSet = '';
                                var stockTypeOptions='';

                                if (previousStock < currentStock) {
                                    /** Like GRV */
                                    colour = 'color: #09b50f  !important';
                                    jsSet = '<a rel="tooltip" title="Sub Item Master - Add Items"  style="' + colour + '" onclick="load_itemMasterSub_modal(' + value['stockCountingDetailsAutoID'] + ',\'SCNT\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; ';

                                }
                                else if (previousStock > currentStock) {
                                    colour = 'color: #b72922 !important';
                                    jsSet = '<a rel="tooltip" title="Sub Item Master - Deduct Items" style="' + colour + '" onclick="load_itemMasterSub_config_modal(' + value['stockCountingDetailsAutoID'] + ',\'SCNT\',' + value['wareHouseAutoID'] + ')"><i class="fa fa-list"></i></a> &nbsp;&nbsp; | &nbsp;&nbsp; ';

                                }


                                if(adjustmentType==0){
                                    var adjeststk = '<input type="text" tabindex="' + x + '" name="adjestmentStock" id="adjestmentStock_' + value['stockCountingDetailsAutoID'] + '" value="' + value['currentWareHouseStock'] + '" class="form-control number">';
                                }else{
                                    var adjeststk = '<input type="text" tabindex="' + x + '" name="adjustmentWac" id="adjustmentWac_' + value['stockCountingDetailsAutoID'] + '" value="' + value['currentWac'] + '" class="form-control number">';
                                }

                                if(stocktypestatus == 2) {
                                    $('#invetstatusid').removeClass('hidden');
                                    stockTypeOptions = '<input type="text" name="stoctypestatus" tabindex=" " id="stoctypestatus_' + value['stockCountingDetailsAutoID'] + '" value="' + value['stocktypestatusID'] + '" class="form-control number">';
                                    getinvetrystatusdropdown(value['stockCountingDetailsAutoID'], value['stocktypestatusID']);

                                }

                                var string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['binlocation'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + stockTypeOptions + '</td><td class="text-center">' + adjeststk + '</td><td class="text-right">' + jsSet + '<a onclick="edit_item(' + value['stockCountingDetailsAutoID'] + ',\'' + value['itemDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_item(' + value['stockCountingDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td><td style="text-align: center;"><input type="checkbox" class="deletechk" name="deletechk[]" id="deletechk_' + value['stockCountingDetailsAutoID'] + '" value="' + value['stockCountingDetailsAutoID'] + '"></td></tr>';

                                if(stocktypestatus == 2) {
                                    $('#invetstatusid').removeClass('hidden');
                                    var stockTypeOptions = '<input type="text" name="stoctypestatus" tabindex=" " id="stoctypestatus_' + value['stockCountingDetailsAutoID'] + '" value="' + value['stocktypestatusID'] + '" class="form-control number">';
                                    getinvetrystatusdropdown(value['stockCountingDetailsAutoID'], value['stocktypestatusID']);

                                }

                                var string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + stockTypeOptions + '</td><td class="text-center">' + adjeststk + '</td><td class="text-right">' + jsSet + '<a onclick="edit_item(' + value['stockCountingDetailsAutoID'] + ',\'' + value['itemDescription'] + '\');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_item(' + value['stockCountingDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td><td style="text-align: center;"><input type="checkbox" class="deletechk" name="deletechk[]" id="deletechk_' + value['stockCountingDetailsAutoID'] + '" value="' + value['stockCountingDetailsAutoID'] + '"></td></tr>';

                            } else {
                                stockTypeOptions = '';
                                if (value['isUpdated'] == 1) {
                                    if(adjustmentType==0) {
                                        var adjeststk = '<input type="text" name="adjestmentStock" tabindex="' + x + '" id="adjestmentStock_' + value['stockCountingDetailsAutoID'] + '" value="' + value['currentWareHouseStock'] + '" onchange="updateCountingStockSingle(' + value['itemAutoID'] + ',' + value['stockCountingDetailsAutoID'] + ',' + value['isUpdated'] + ',' + value['previousStock'] + ',' + value['previousWareHouseStock'] + ')" class="form-control number">';
                                    }else{
                                        var adjeststk = '<input type="text" name="adjustmentWac" tabindex="' + x + '" id="adjustmentWac_' + value['stockCountingDetailsAutoID'] + '" value="' + value['currentWac'] + '" onchange="updateCountingStockSingle(' + value['itemAutoID'] + ',' + value['stockCountingDetailsAutoID'] + ',' + value['isUpdated'] + ',' + value['previousWac'] + ',' + value['previousWareHouseStock'] + ')" class="form-control number">';
                                    }
                                } else {
                                    if(adjustmentType==0) {
                                        var adjeststk = '<input type="text" name="adjestmentStock" tabindex="' + x + '" id="adjestmentStock_' + value['stockCountingDetailsAutoID'] + '" onchange="updateCountingStockSingle(' + value['itemAutoID'] + ',' + value['stockCountingDetailsAutoID'] + ',' + value['isUpdated'] + ',' + value['previousStock'] + ',' + value['previousWareHouseStock'] + ')"  class="form-control number">';
                                    }else{
                                        var adjeststk = '<input type="text" name="adjustmentWac" tabindex="' + x + '" id="adjustmentWac_' + value['stockCountingDetailsAutoID'] + '" onchange="updateCountingStockSingle(' + value['itemAutoID'] + ',' + value['stockCountingDetailsAutoID'] + ',' + value['isUpdated'] + ',' + value['previousWac'] + ',' + value['previousWareHouseStock'] + ')"  class="form-control number">';
                                    }
                                }
                                if(stocktypestatus== 2){
                                    $('#invetstatusid').removeClass('hidden');
                                    stockTypeOptions = '<input type="text" name="stoctypestatus" tabindex=" " id="stoctypestatus_' + value['stockCountingDetailsAutoID'] + '" value="' + value['stocktypestatusID'] + '" class="form-control number">';
                                    getinvetrystatusdropdown(value['stockCountingDetailsAutoID'], value['stocktypestatusID']);
                                }

                                var string = '<tr><td>' + x + '</td><td>' + value['itemSystemCode'] + '</td><td>' + value['itemDescription'] + '</td><td>' + value['binlocation'] + '</td><td class="text-center">' + value['unitOfMeasure'] + '</td><td class="text-center">' + stockTypeOptions + '</td><td class="text-center">' + adjeststk + '</td><td class="text-right"><a onclick="edit_glaccount(' + value['stockCountingDetailsAutoID'] + ',\'' + value['PLGLAutoID'] + '\',\'' + value['BLGLAutoID'] + '\');"><span class="glyphicon glyphicon-transfer"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="edit_item(' + value['stockCountingDetailsAutoID'] + ');"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_item(' + value['stockCountingDetailsAutoID'] + ');"><span style="color:rgb(209, 91, 71);" class="glyphicon glyphicon-trash"></span></a></td><td style="text-align: center;"><input type="checkbox" class="deletechk" name="deletechk[]" id="deletechk_' + value['stockCountingDetailsAutoID'] + '" value="' + value['stockCountingDetailsAutoID'] + '"></td></tr>';  }

                            $('#item_table_body').append(string);
                            x++;
                            tot += parseFloat(value['totalValue']);

                        });
                        number_validation();
                    }
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    stopLoad();
                }
            });
        }
        ;
    }

    function getinvetrystatusdropdown(stockCountingDetailsAutoID, stocktypestatusID) {
    var stocktypeid = $('#stockcounttypeid').val();
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {'stocktypeid': stocktypeid },
        url: "<?php echo site_url('StockCounting/get_stock_type_status'); ?>",
        success: function (data) {
            var dropdown = $('<select></select>')
                .attr('id', 'statusDropdown_' + stockCountingDetailsAutoID)
                .attr('stoctypestatus', 'stoctypestatus' + stocktypestatusID) 
                 // Unique ID for each dropdown
                .attr('name', 'statusDropdown')
                .attr('style', 'border-top-color: white; border-right-color: white; border-left-color: white; width: 117px;');

            dropdown.append($('<option></option>').attr('value', '').text('Select Status'));

            $.each(data, function (key, value) {
                dropdown.append($('<option></option>').attr('value', key).text(value));
            });

            // Ensure the value is set after the dropdown is fully populated
            dropdown.val(stocktypestatusID);

            dropdown.on('change', function () {
                var selectedStatus = $(this).val();
                var autoId = stockCountingDetailsAutoID;
                stockstatusupdate(autoId, selectedStatus);
            });

            $('#stoctypestatus_' + stockCountingDetailsAutoID).replaceWith(dropdown);
        },
        error: function () {
            swal("Cancelled", "Your request could not be completed", "error");
        }
    });
}

function stockstatusupdate(stockCountingDetailsAutoID, selectedStatus) {
    $.ajax({
        type: 'POST',
        url: '<?php echo site_url("StockCounting/updateinvetorystatustype"); ?>',
        dataType: 'json',
        data: {
            stockCountingDetailsAutoID: stockCountingDetailsAutoID,
            selectedStatus: selectedStatus
        },
        async: true,
        beforeSend: function () {
            startLoad();
        },
        success: function (data) {
            stopLoad();
            myAlert(data[0],data[1]);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            stopLoad();
        }
    });
}



        function getinvetrystatusdropdown(stockCountingDetailsAutoID, stocktypestatusID) {
        var stocktypeid = $('#stockcounttypeid').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'stocktypeid': stocktypeid },
            url: "<?php echo site_url('StockCounting/get_stock_type_status'); ?>",
            success: function (data) {
                var dropdown = $('<select></select>')
                    .attr('id', 'statusDropdown_' + stockCountingDetailsAutoID)
                    .attr('stoctypestatus', 'stoctypestatus' + stocktypestatusID) 
                     // Unique ID for each dropdown
                    .attr('name', 'statusDropdown')
                    .attr('style', 'border-top-color: white; border-right-color: white; border-left-color: white; width: 117px;');

                dropdown.append($('<option></option>').attr('value', '').text('Select Status'));

                $.each(data, function (key, value) {
                    dropdown.append($('<option></option>').attr('value', key).text(value));
                });

                // Ensure the value is set after the dropdown is fully populated
                dropdown.val(stocktypestatusID);

                dropdown.on('change', function () {
                    var selectedStatus = $(this).val();
                    var autoId = stockCountingDetailsAutoID;
                    stockstatusupdate(autoId, selectedStatus);
                });

                $('#stoctypestatus_' + stockCountingDetailsAutoID).replaceWith(dropdown);
            },
            error: function () {
                swal("Cancelled", "Your request could not be completed", "error");
            }
        });
    }

    function stockstatusupdate(stockCountingDetailsAutoID, selectedStatus) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("StockCounting/updateinvetorystatustype"); ?>',
            dataType: 'json',
            data: {
                stockCountingDetailsAutoID: stockCountingDetailsAutoID,
                selectedStatus: selectedStatus
            },
            async: true,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0],data[1]);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
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
                if (cont) {
                    setTimeout(function () {
                        $('#f_search_' + id).closest('tr').find('.itemAutoID').val(suggestion.itemAutoID);
                    }, 200);
                    $(this).closest('tr').find('.currentStock').val(suggestion.currentStock);
                    $(this).closest('tr').find('.d_uom').val(suggestion.defaultUnitOfMeasure);
                    fetch_related_uom_id(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID, this);
                    $(this).closest('tr').find('.umoDropdown').prop("disabled", true);
                    fetch_warehouse_item(suggestion.itemAutoID, this);
                    $(this).closest('tr').find('.adjustment_Stock').focus();
                    $(this).closest('tr').css("background-color", 'white');
                    check_item_not_approved_in_document(suggestion.itemAutoID,id,stockCountingAutoID);
                    return false;
                }
            }
        });
        $('#f_search_' + id).off('focus.autocomplete');
    }


    function initializeitemTypeahead_edit(type) {

        $('#search').autocomplete({
            serviceUrl: '<?php echo site_url();?>Inventory/fetch_inv_item_stock_adjustment/?&t=' + type,
            onSelect: function (suggestion) {

                setTimeout(function () {
                    $('#itemAutoID_edit').val(suggestion.itemAutoID);
                    $('#currentStock_edit').val(suggestion.currentStock);
                    $('#d_uom_edit').val(suggestion.defaultUnitOfMeasure);
                }, 200);
                fetch_related_uom_id_edit(suggestion.defaultUnitOfMeasureID, suggestion.defaultUnitOfMeasureID);
                $('#UnitOfMeasureID_edit').prop("disabled", true);
                $('#adjustment_Stock_edit').focus();
                fetch_warehouse_item_edit(suggestion.itemAutoID);
                check_item_not_approved_in_document_edit(suggestion.itemAutoID,stockCountingAutoID);
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
            data: {'stockCountingAutoID': stockCountingAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('StockCounting/fetch_warehouse_item_adjustment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $(element).closest('tr').find('.currentWareHouseStock').val(data['currentStock']);
                    if(jQuery.isEmptyObject(data['currentWac'])){
                        $(element).closest('tr').find('.currentWac').val(0);
                    }else{
                        $(element).closest('tr').find('.currentWac').val(data['currentWac']);
                    }

                    if(jQuery.isEmptyObject(data['adjustment_wac'])){
                        $(element).closest('tr').find('.adjustment_wac').val(0);
                    }else{
                        $(element).closest('tr').find('.adjustment_wac').val(data['currentWac']);
                    }

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
            data: {'stockCountingAutoID': stockCountingAutoID, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('StockCounting/fetch_warehouse_item_adjustment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status']) {
                    $('#currentWac_edit').val(data['currentWac']);
                    $('#adjustment_wac_edit').val(data['currentWac']);
                    $('#currentWareHouseStock_edit').val(data['currentStock']);
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
    function delete_is_update_zero()
      {
          $.ajax({
          async: true,
          type: 'post',
          dataType: 'json',
          data: {'stockCountingAutoID': stockCountingAutoID},
          url: "<?php echo site_url('StockCounting/chk_delete_stock_counting_up_items'); ?>",
          beforeSend: function () {
              startLoad();
          },
          success: function (data) {
              stopLoad();
              if (data['value'] == 1) {

                  delete_blank_fields();
              }
              else
              {
                  load_conformation();
              }
          }
          , error: function () {
              alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
              /*An Error Occurred! Please Try Again*/
              stopLoad();
              refreshNotifications(true);
          }
      });

      }


    function delete_blank_fields() {
        swal({
                title: " ",
                text: "Do you want to delete all the blank fields",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "Cancel"
            },
            function (data) {
                if (data) {
                    delete_stock_counting_up_items();
                }
                else
                {
                    load_conformation();
                }

            });

    }

    function load_conformation() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'stockCountingAutoID': stockCountingAutoID, 'html': true},
            url: "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#conform_body').html(data);
                $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_scnt'); ?>/" + stockCountingAutoID + '/SCNT');
                $("#a_link").attr("href", "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>/" + stockCountingAutoID);
                attachment_modal_stockAdjustment(stockCountingAutoID, "Stock Counting", "SCNT");
                /*Stock Adjustment*/
                stopLoad();
            }, error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
            }
        });

    }

    function confirmation() {
        if (stockCountingAutoID) {
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
                        data: {'stockCountingAutoID': stockCountingAutoID},
                        url: "<?php echo site_url('StockCounting/stock_counting_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                                if (data['stock']) {
                                    $('#update_stock_validated_table_body').empty();
                                    if (jQuery.isEmptyObject(data)) {
                                        $('#update_stock_validated_table_body').append('<tr class="danger"><td colspan="12" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?> </b></td></tr>');<!--No Records Found-->
                                    } else {
                                        $.each(data['stock'], function (key, value) {
                                            var string = '<tr><td><input class="hidden" id="stockCountingDetailsAutoID" name="stockCountingDetailsAutoID[]" value="'+ value['stockCountingDetailsAutoID'] + '">' + value['itemSystemCode'] + '</td>';
                                            string += '<td>' + value['itemDescription'] + '</td>';
                                            string += '<td>' + value['unitOfMeasure'] + '</td>';

                                            string += '<td>' + value['previousWareHouseStock'] + '</td>';
                                            string += '<td>' + value['currentWareHouseStock'] + '</td>';
                                            string += '<td>'+ value['adjustmentStock'] +'</td>';

                                            string += '<td>' + value['ledgerItems'] + '</td>';
                                            string += '<td><input class="text-right stock number" name="stock[]" id="stock" onchange="calculateBalanceQty_Counting(this)">';
                                            string += '<input class="hidden text-right" id="currentWarehouseItem" name="currentWarehouseItem[]" value="'+ value['ledgerItems'] +'"></td>';
                                            string += '</tr>';

                                            $('#update_stock_validated_table_body').append(string);
                                        });
                                        $("#update_stock_validated_modal").modal({backdrop: "static"});
                                    }
                                }
                            } else if(data['error']== 2)
                            {
                                myAlert('w', data['message']);
                            }
                            else {
                                myAlert('s', data['message']);
                                //refreshNotifications(true);
                                fetchPage('system/inventory/stock_counting_management', stockCountingAutoID, 'Stock Counting');
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function calculateBalanceQty_Counting(element){
        var adjustmentStock = parseFloat(element.value);
        if(adjustmentStock < 0) {
            $(element).closest('tr').find('.stock').val('');
            myAlert('w', 'Balance Qty cannot be less than 0');
        }
    }

    function save_draft() {
        if (stockCountingAutoID) {
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
                    fetchPage('system/inventory/stock_counting_management', stockCountingAutoID, 'Stock Counting');
                });
        }
    }

    function fetch_finance_year_period(companyFinanceYearID, select_value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'companyFinanceYearID': companyFinanceYearID,'fyDepartmentID':'SCNT'},
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
        if (stockCountingAutoID) {
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
                        data: {'stockCountingDetailsAutoID': id},
                        url: "<?php echo site_url('StockCounting/delete_counting_item'); ?>",
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
        if (stockCountingAutoID) {
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
                        data: {'stockCountingDetailsAutoID': id, location: location},
                        url: "<?php echo site_url('StockCounting/load_counting_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            $('#search').typeahead('destroy');
                            stockCountingDetailsAutoID = data['stockCountingDetailsAutoID'];
                            projectID = data['projectID'];
                            projectcategory = data['project_categoryID'];
                            projectsubcat = data['project_subCategoryID'];
                            $('#search').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - "+data['seconeryItemCode']);
                            $('#itemAutoID_edit').val(data.itemAutoID);
                            $('#currentStock_edit').val(data.currentStock);
                            fetch_related_uom_id_edit(data['defaultUOMID'], data['defaultUOMID']);
                            $('#segment_edit').val(data['segmentID'] + '|' + data['segmentCode']).change();
                            $('#UnitOfMeasureID_edit').prop("disabled", true);
                            $('#currentWareHouseStock_edit').val(data['wareHouseStock']);
                            $('#currentWac_edit').val(data['LocalWacAmount']);
                            $('#adjustment_Stock_edit').val(data['currentStock']);
                            $('#adjustment_wac_edit').val(data['LocalWacAmount']);
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

    function delete_stockAdjustment_attachement(stockCountingAutoID, DocumentSystemCode, myFileName) {
        if (stockCountingAutoID) {
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
                        data: {'attachmentID': stockCountingAutoID, 'myFileName': myFileName},
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
        var appendData = $('#StockCounting_detail_add_table tbody tr:first').clone();
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.f_search').attr('onkeyup', 'clearitemAutoID(event,this)');
        appendData.find('input').val('');
        appendData.find('textarea').val('');


        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#StockCounting_detail_add_table').append(appendData);
        var lenght = $('#StockCounting_detail_add_table tbody tr').length - 1;
        $('#f_search_' + search_id).closest('tr').css("background-color", 'white');
        $(".select2").select2();
        initializeitemTypeahead(type, search_id);
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
        data.push({'name': 'stockCountingAutoID', 'value': stockCountingAutoID});
        data.push({'name': 'stockCountingDetailsAutoID', 'value': stockCountingDetailsAutoID});
        $('select[name="UnitOfMeasureID[]"] option:selected').each(function () {
            data.push({'name': 'uom[]', 'value': $(this).text()})
        });
        $('.itemAutoID').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $('.adjustment_Stock').each(function () {
            if (this.value == '') {
                $(this).closest('tr').css("background-color", '#ffb2b2');
            }
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('StockCounting/save_stock_counting_detail_multiple'); ?>",
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
                        stockCountingDetailsAutoID = null;
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
        data.push({'name': 'stockCountingAutoID', 'value': stockCountingAutoID});
        data.push({'name': 'stockCountingDetailsAutoID', 'value': stockCountingDetailsAutoID});
        data.push({'name': 'uom', 'value': $('#UnitOfMeasureID_edit option:selected').text()});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('StockCounting/save_stock_counting_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    stockCountingDetailsAutoID = null;
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


    function updateCountingStockSingle(itemAutoID, stockCountingDetailsAutoID, isUpdated, previousStock, previousWareHouseStock) {
        if(adjustmentType==0){
            var stock = $('#adjestmentStock_' + stockCountingDetailsAutoID).val();
            if (stock == '') {
                //myAlert('w','stock canot be empty');
            } else {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("StockCounting/updateCountingStockSingle"); ?>',
                    dataType: 'json',
                    data: {
                        itemAutoID: itemAutoID,
                        stockCountingDetailsAutoID: stockCountingDetailsAutoID,
                        isUpdated: isUpdated,
                        stock: stock,
                        previousStock: previousStock,
                        previousWareHouseStock: previousWareHouseStock,
                        stockCountingAutoID: stockCountingAutoID
                    },
                    async: true,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        //myAlert(data[0],data[1]);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                    }
                });
            }
        }else{
            var wac = $('#adjustmentWac_' + stockCountingDetailsAutoID).val();
            if (wac == '') {
                //myAlert('w','stock canot be empty');
            } else {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo site_url("StockCounting/updateCountingWacSingle"); ?>',
                    dataType: 'json',
                    data: {
                        itemAutoID: itemAutoID,
                        stockCountingDetailsAutoID: stockCountingDetailsAutoID,
                        isUpdated: isUpdated,
                        wac: wac,
                        previousWac: previousStock,
                        previousWareHouseStock: previousWareHouseStock,
                        stockCountingAutoID: stockCountingAutoID
                    },
                    async: true,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        //myAlert(data[0],data[1]);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                    }
                });
            }
        }

    }

    function loadSubCategory() {
        $('#subcategoryID option').remove();
        var mainCategoryID = $('#stockCountingType').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("StockCounting/load_subcat"); ?>',
            dataType: 'json',
            data: {'mainCategory': mainCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
                $('#subcategoryID').multiselect2('rebuild');
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function loadSubSubCategory() {
        $('#subsubcategoryID option').remove();
        var subCategoryID = $('#subcategoryID').val();
        window.localStorage.setItem('subcategorycounting', subCategoryID);
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("StockCounting/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subCategoryID': subCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subsubcategoryID').empty();
                    var mySelect = $('#subsubcategoryID');
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
                $('#subsubcategoryID').multiselect2('rebuild');
                /*$("#subsubcategoryID").multiselect2('selectAll', false);
                 $("#subsubcategoryID").multiselect2('updateButtonText');*/
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function setsubsublocalstorage() {
        var subsubcategoryID = $('#subsubcategoryID').val();
        window.localStorage.setItem('subsubcategorycounting', subsubcategoryID);
    }

    function deleteAll() {
        var $form = $('#stock_counting_detail_form');
        var data = $form.serializeArray();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "You want to delete the selected items",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('StockCounting/delete_all_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetch_detail();
                        }
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function checkall() {
        if ($('#mainchkbox').is(':checked')) {
            $('.deletechk').prop("checked", true);
        } else {
            $('.deletechk').prop("checked", false);
        }
    }

    function print_stock_counting_filter() {
        $('#printID').val(stockCountingAutoID);
        var form = document.getElementById('frm_filter');
        form.target = '_blank';
        form.action = '<?php echo site_url('StockCounting/print_stock_counting_filter'); ?>';
        form.submit();
    }

    function delete_stock_counting_up_items() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'stockCountingAutoID': stockCountingAutoID},
            url: "<?php echo site_url("StockCounting/delete_stock_counting_up_items"); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    load_conformation();
                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function usedDocSkip() {
        $('#useDocProceed').val(0);
        $('#stock_counting_form').submit()
    }

    function check_item_not_approved_in_document(itemAutoID,id,stockCountingAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {itemAutoID: itemAutoID,'stockCountingAutoID':stockCountingAutoID},
            url: "<?php echo site_url('Inventory/check_item_not_approved_in_document'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#access_denied_itemwise_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['usedDocs'])) {
                    $('#access_denied_itemwise_body').append('<tr class="danger"><td colspan="2" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                } else {
                    $.each(data['usedDocs'], function (key, value) {
                        $('#access_denied_itemwise_body').append('<tr><td>' + x + '</td><td><a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'' + value['documentID'] + '\',' + value['documentAutoID'] + ')">' + value['documentCode'] + '</a></td></tr>');
                        x++;
                    });

                    $('#f_search_' + id).val('');
                    $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                    $('#f_search_' + id).closest('tr').find('.currentWareHouseStock').val('');
                    $('#f_search_' + id).closest('tr').find('.currentWac').val('');
                    $('#f_search_' + id).closest('tr').find('.adjustment_wac').val('');
                    $('#f_search_' + id).closest('tr').find('.umoDropdown').val('');
                    $('#f_search_' + id).closest('tr').find('.currentStock').val('');
                    $('#access_denied_itemwise').modal('show');
                }


            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');

                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function check_item_not_approved_in_document_edit(itemAutoID,stockCountingAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {itemAutoID: itemAutoID,'stockCountingAutoID':stockCountingAutoID},
            url: "<?php echo site_url('Inventory/check_item_not_approved_in_document'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#access_denied_itemwise_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['usedDocs'])) {
                    $('#access_denied_itemwise_body').append('<tr class="danger"><td colspan="2" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                } else {
                    $.each(data['usedDocs'], function (key, value) {
                        $('#access_denied_itemwise_body').append('<tr><td>' + x + '</td><td><a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'' + value['documentID'] + '\',' + value['documentAutoID'] + ')">' + value['documentCode'] + '</a></td></tr>');
                        x++;
                    });

                    $('#itemAutoID_edit').val('');
                    $('#currentStock_edit').val('');
                    $('#d_uom_edit').val('');
                    $('#access_denied_itemwise').modal('show');
                }


            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
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
        var projectID = $(element).parent().closest('tr').find('.projectID');

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

    function update_stock_validated_counting() {
        var data = $('#update_stock_validated_form').serializeArray();
        $.ajax(
            {
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('StockCounting/update_stock_minus_qty'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#update_stock_validated_modal').modal('hide');
                    load_conformation();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
    }

</script>