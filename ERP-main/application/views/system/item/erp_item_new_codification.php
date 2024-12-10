<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$main_category_arr = all_main_category_drop();
$revenue_gl_arr = all_revenue_gl_drop();
$cost_gl_arr = all_cost_gl_drop();
$asset_gl_arr = all_asset_gl_drop();
$uom_arr = all_umo_new_drop();
$stock_adjustment = stock_adjustment_control_drop();
$fetch_cost_account = fetch_cost_account();
$fetch_dep_gl_code = fetch_gl_code(array('masterCategory' => 'PL', 'subCategory' => 'PLE'));
$fetch_disposal_gl_code = fetch_gl_code(array('masterCategory' => 'PL'));
$ware_house_binlocations = companyWarehouseBinLocations();
$companyBinLocations = companyBinLocations();
$secondaryUOM = getPolicyValues('SUOM', 'All');
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<style>
    @font-face {
        font-family: barCodeFont;
        src: url(<?php echo base_url('font/fre3of9x.ttf') ?>);
    }

    .barcodeDiv {
        width: 200px;
        height: 42px;
        margin-top: 10px;

    }

</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1"
       data-toggle="tab"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one'); ?>
        - <?php echo $this->lang->line('erp_item_master_item_header'); ?></a><!--Step 1--><!--Item Header-->
    <a class="btn btn-default btn-wizard" href="#step2"
       data-toggle="tab"><?php echo $this->lang->line('transaction_goods_received_voucher_step_two'); ?>
        - <?php echo $this->lang->line('erp_item_master_item_attachments'); ?></a><!--Step 2--><!--Item Attachments-->
    <a class="btn btn-default btn-wizard" href="#step3" data-toggle="tab">Step 3 - Bin Locations</a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="itemmaster_form"'); ?>
        <div class="row modal-body" style="padding-bottom: 0px;">
            <div class="col-sm-3" align="" style="padding-left: 0px;">
                <div class="fileinput-new thumbnail" style="margin-bottom: 4px;width: 200px; height: 150px;">
                    <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg">
                    <input type="file" name="itemImage" id="itemImage" style="display: none;"
                           onchange="loadImage(this)"/>
                </div>
                <div class="form-group col-sm-12 no-padding">
                    <!--<label for="">System Code</label>-->
                    <!--<h4 id="edit_systemCode" style="margin-top: 2px;color: #48bbce;"></h4>-->
                    <!--<div id="barCode" style="font-family: barCodeFont;">&nbsp</div>-->
                    <div id="barcodeDiv"></div>
                </div>
                <!--<div class="form-group col-sm-12 no-padding">
                    <label for="">Short Description</label>
                    <h4 id="edit_shortDescription" style="margin-bottom:0px;margin-top: 2px;color: #48bbce;"></h4>
                </div>-->
            </div>
            <div class="col-md-9" style="padding-left: 0px;">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label>
                            <?php echo $this->lang->line('transaction_main_category'); ?><!--Main Category--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="load_sub_cat(),validate_itempull(this.value,1);"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>
                            <?php echo $this->lang->line('transaction_sub_category'); ?><!--Sub Category--> <?php required_mark(); ?></label>
                        <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                                onchange="load_sub_sub_cat(),load_gl_codes(),validate_itempull(this.value,2);">
                            <option value="">
                                <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label>
                            <?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?><!--Sub Sub Category--> </label>
                        <select name="subSubCategoryID" id="subSubCategoryID" class="form-control searchbox">
                            <option value="">
                                <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('erp_item_master_short_description'); ?><?php required_mark(); ?></label>
                        <!--Short Description-->
                        <input type="text" class="form-control" id="itemName" name="itemName">
                    </div>
                    <div class="form-group col-sm-8">
                        <label><?php echo $this->lang->line('erp_item_master_long_description'); ?><?php required_mark(); ?></label>
                        <!--Long Description-->
                        <input type="text" class="form-control" id="itemDescription" name="itemDescription">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="">
                            <?php echo $this->lang->line('erp_item_master_secondary_code'); ?><!--Secondary Code--> <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="seconeryItemCode" name="seconeryItemCode">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">
                            <?php echo $this->lang->line('transaction_unit_of_measure'); ?><!--Unit of Measure--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('defaultUnitOfMeasureID', $uom_arr, 'Each', 'class="form-control" id="defaultUnitOfMeasureID" onchange="validate_itempull(this.value,3);" required'); ?>
                    </div>
                    <?php
                    if ($secondaryUOM == 1) {
                        ?>
                        <div class="form-group col-sm-4">
                            <label for="">Secondary Unit of Measure <?php required_mark(); ?></label>
                            <?php echo form_dropdown('secondaryUOMID', $uom_arr, 'Each', 'class="form-control" id="secondaryUOMID" onchange="validate_itempull(this.value,4);"'); ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group col-sm-4">
                        <label for="">
                            <?php echo $this->lang->line('transaction_selling_price'); ?><!--Selling Price--> <?php required_mark(); ?></label>

                        <div class="input-group">
                            <div
                                    class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                            <input type="text" step="any" class="form-control number" id="companyLocalSellingPrice"
                                   name="companyLocalSellingPrice" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('transaction_barcode'); ?><!--Barcode--></label>
                <input type="text" class="form-control" id="barcode" name="barcode" onchange="validateBarCode(this.value)">
            </div>
            <div class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('transaction_part_no'); ?><!--Part No--> </label>
                <input type="text" class="form-control" id="partno" name="partno">
            </div>
            <div class="form-group col-sm-2" id="cls_maximunQty">
                <label for=""><?php echo $this->lang->line('transaction_maximum_qty'); ?><!--Maximum Qty--></label>
                <input type="text" class="form-control number" id="maximunQty" name="maximunQty">
            </div>
            <div class="form-group col-sm-2" id="cls_minimumQty">
                <label for=""><?php echo $this->lang->line('transaction_minimum_qty'); ?><!--Minimum Qty--></label>
                <input type="text" class="form-control number" id="minimumQty" name="minimumQty">
            </div>
            <div class="form-group col-sm-2" id="cls_reorderPoint">
                <label for="">
                    <?php echo $this->lang->line('erp_item_master_recorder_level'); ?><!--Reorder Level--></label>
                <input type="text" class="form-control number" id="reorderPoint" name="reorderPoint">
            </div>

        </div>
        <div class="row" id="inventry_row_div">
            <div class="form-group col-sm-4">
                <label for="">
                    <?php echo $this->lang->line('erp_item_master_revenue_gl_code'); ?><!--Revenue GL Code --></label>
                <?php echo form_dropdown('revanueGLAutoID', $revenue_gl_arr, '', 'class="form-control select2" id="revanueGLAutoID" onchange="validate_itempull(this.value,5);" '); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="">
                    <?php echo $this->lang->line('erp_item_master_cost_gl_code'); ?><!--Cost GL Code--></label>
                <?php echo form_dropdown('costGLAutoID', $cost_gl_arr, '', 'class="form-control select2" id="costGLAutoID" onchange="validate_itempull(this.value,6);" '); ?>
            </div>
            <div class="form-group col-sm-4" id="assetGlCode_div">
                <label for="">
                    <?php echo $this->lang->line('erp_item_master_asset_gl_code'); ?><!--Asset GL Code--></label>
                <?php echo form_dropdown('assteGLAutoID', $asset_gl_arr, $this->common_data['controlaccounts']['INVA'], 'class="form-control select2" id="assteGLAutoID" onchange="validate_itempull(this.value,7);"'); ?>
            </div>


        </div>
        <div class="row hide" id="fixed_row_div">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('erp_item_master_cost_account'); ?><!--Cost Account--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('COSTGLCODEdes', $fetch_cost_account, '', 'class="form-control form1 select2" id = "COSTGLCODEdes" onchange="validate_itempull(this.value,9);"'); ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('erp_item_master_acc_dep_gl_code'); ?><!--Acc Dep GL Code --><?php required_mark(); ?></label>
                    <?php echo form_dropdown('ACCDEPGLCODEdes', $fetch_cost_account, '', 'class="form-control form1 select2" id = "ACCDEPGLCODEdes"'); ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('erp_item_master_dep_gl_code'); ?><!--Dep GL Code--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('DEPGLCODEdes', $fetch_dep_gl_code, '', 'class="form-control form1 select2" id = "DEPGLCODEdes" '); ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('erp_item_master_disposal_gl_code'); ?><!--Disposal GL Code--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('DISPOGLCODEdes', $fetch_disposal_gl_code, '', 'class="form-control form1 select2" id = "DISPOGLCODEdes"'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <div class="form-group" id="stockadjustment">
                        <label for="">Stock Adjustment Control</label>
                        <?php echo form_dropdown('stockadjust', $stock_adjustment, '', 'class="form-control form1 select2" id="stockadjust" onchange="validate_itempull(this.value,8);"'); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for=""><?php echo $this->lang->line('erp_item_master_is_active'); ?><!--isActive--></label>

                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_isActive" type="checkbox"
                                   data-caption="" class="columnSelected" name="isActive" value="1" checked>
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('erp_item_master_is_sub_item_applicable'); ?><!--is Sub-item Applicable--> </label>

                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_isSubitemExist" type="checkbox"
                                   data-caption="" class="columnSelected" name="isSubitemExist" value="1">
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<br>
<br>
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">Codification Setup</legend>
            <div id="codsetup">

            </div>
        </fieldset>

        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-primary" type="submit" id="submitbtn">
                <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
        </div>
        </form>
    </div>
    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-sm-8">
                <h4 class="modal-title" id="purchaseOrder_attachment_label">
                    <?php echo $this->lang->line('erp_item_master_modal_title'); ?><!--Modal title--></h4>
                <br>
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="itemMaster_attachment_uplode_form" class="form-inline"'); ?>
                            <div class="form-group">
                                <!-- <label for="attachmentDescription">Description</label> -->
                                <input type="text" class="form-control" id="attachmentDescription"
                                       name="attachmentDescription" placeholder="Description...">
                                <input type="hidden" class="form-control" id="itm_documentSystemCode"
                                       name="documentSystemCode">
                                <input type="hidden" class="form-control" id="itm_documentID" name="documentID"
                                       value="ITM">
                                <input type="hidden" class="form-control" id="itm_document_name" name="document_name"
                                       value="Item Master">
                            </div>
                          <div class="form-group">
                              <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                   style="margin-top: 8px;">
                                  <div class="form-control" data-trigger="fileinput"><i
                                              class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                              class="fileinput-filename"></span></div>
                                  <span class="input-group-addon btn btn-default btn-file"><span
                                              class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                          aria-hidden="true"></span></span><span
                                              class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                             aria-hidden="true"></span></span><input
                                              type="file" name="document_file" id="document_file"></span>
                                  <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                     data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                              </div>
                          </div>
                          <button type="button" class="btn btn-default" onclick="itemMaster_document_uplode()"><span
                                      class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form></span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                            <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                            <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                            <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                        </tr>
                        </thead>
                        <tbody id="purchaseOrder_attachment" class="no-padding">
                        <tr class="danger">
                            <td colspan="5" class="text-center">
                                <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="step3" class="tab-pane">
        <div class="row">
            <div class="col-sm-12">
                <table class="<?php echo table_class(); ?>">
                    <thead>
                    <tr>
                        <th>Ware House</th>
                        <th>Bin Location</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($ware_house_binlocations as $val) {
                        ?>
                        <tr>
                            <td><?php echo $val['wareHouseDescription'] ?></td>
                            <?php
                            $binlocations = array();
                            foreach ($companyBinLocations as $bins) {
                                if ($bins['warehouseAutoID'] == $val['wareHouseAutoID']) {
                                    $binlocations[$bins['binLocationID']] = $bins['Description'];

                                } else {
                                    //$binlocations =array();
                                }
                            }
                            if (!empty($binlocations)) {

                                ?>
                                <td>
                                    <input type="hidden" id="itemBinlocationID_<?php echo $val['wareHouseAutoID'] ?>"
                                           name="itemBinlocationID[]">
                                    <select class="form-control" name="binloc[]"
                                            id="binloc_<?php echo $val['wareHouseAutoID'] ?>">
                                        <option value="">Select Bin Location</option>
                                        <?php
                                        foreach ($binlocations as $key => $valu) {
                                            ?>
                                            <option value="<?php echo $key ?>"><?php echo $valu ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" type="button"
                                            onclick="save_item_bin_location(<?php echo $val['wareHouseAutoID'] ?>)">Save
                                    </button>
                                </td>
                                <?php
                            } else {
                                ?>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="access_denied" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="title_generate_exceed"></h4>
            </div>
            <div class="modal-body">
                <h6 class="modal-title" id="myModalLabel" style="color: red;font-size: 13px;">You cannot change this values. Because this item has been pulled for following docuemnts.</h6>
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Document Code</th>
                        <th>Document Type</th>
                        <th>Reference No</th>
                    </tr>
                    </thead>
                    <tbody id="access_denied_body">

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
<div class="modal fade" id="barcode_validate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="title_generate_exceed"></h4>
            </div>
            <div class="modal-body">
                <h6 class="modal-title" id="myModalLabel" style="color: red;font-size: 13px;">You cannot assign this value. Because this bar code is already assigned for</h6>
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Description</th>
                        <th>Barcode</th>
                    </tr>
                    </thead>
                    <tbody id="barcode_validate_body">

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
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var itemAutoID;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/item/erp_item_master_codification', '', 'Item Master')
        });
        $('.select2').select2();
        itemAutoID = null;
        number_validation();
        load_sub_cat();

        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            itemAutoID = p_id;
            load_item_header();
            load_item_bin_location();
            changeFormCode();
            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
        }
        $('#itemmaster_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                /*revanueGLAutoID: {validators: {notEmpty: {message: 'Revanue GL Code is required.'}}},
                 costGLAutoID: {validators: {notEmpty: {message: 'Cost GL Code is required.'}}},
                 assteGLAutoID: {validators: {notEmpty: {message: 'Asste GL Code is required.'}}},*/
                seconeryItemCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_item_code_is_required');?>.'}}},/*Item Code is required*/
                itemName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_item_name_is_required');?>.'}}},/*Item Name is required*/
                itemDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_item_description_is_required');?>.'}}},/*Item Description is required*/
                mainCategoryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_main_category_is_required');?>.'}}},/*Main category is required*/
                subcategoryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_sub_category_is_required');?>.'}}},/*Sub category is required*/
                defaultUnitOfMeasureID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('erp_item_master_unit_of_measure_is_required');?>.'}}},/*Unit of measure is required*/
                /*                    maximunQty              : {validators: {notEmpty: {message: 'Maximun Qty is required.'}}},
                 minimumQty              : {validators: {notEmpty: {message: 'Minimum Qty is required.'}}},
                 reorderPoint            : {validators: {notEmpty: {message: 'Reorder Point is required.'}}},*/
            },
        }).on('success.form.bv', function (e) {
            $('#submitbtn').prop('disabled', false);
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            data.push({'name': 'itemAutoID', 'value': itemAutoID});
            data.push({'name': 'revanue', 'value': $('#revanueGLAutoID option:selected').text()});
            data.push({'name': 'cost', 'value': $('#costGLAutoID option:selected').text()});
            data.push({'name': 'asste', 'value': $('#assteGLAutoID option:selected').text()});
            data.push({'name': 'mainCategory', 'value': $('#mainCategoryID option:selected').text()});
            data.push({'name': 'uom', 'value': $('#defaultUnitOfMeasureID option:selected').text()});
            data.push({'name': 'stockadjustment', 'value': $('#stockadjust option:selected').text()});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Codification/save_itemmaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    //refreshNotifications(true);
                    myAlert(data[0], data[1], data[2], data[3]);
                    if (data[0] == 's') {
                        if (data[3]) {
                            $('#barcode').val(data[3]);
                        }
                        itemAutoID = data[2];
                        //$("#mainCategoryID").readonly();
                        //$('#mainCategoryID').prop('readonly', true);
                        //$("#mainCategoryID").prop("disabled", false);
                        $("#defaultUnitOfMeasureID").prop("disabled", false);
                        $("#secondaryUOMID").prop("disabled", false);
                        faID = data[2];

                        var imgageVal = new FormData();
                        imgageVal.append('faID', faID);

                        var files = $("#itemImage")[0].files[0];
                        imgageVal.append('files', files);

                        if (files == undefined) {
                            //$('#itemmaster_form')[0].reset();
                            $('.btn-wizard').removeClass('disabled');
                            //$('#itemmaster_form').bootstrapValidator('resetForm', true);
                            $("#itm_documentSystemCode").val(faID);
                            attachment_modal_itemMaster(faID, "Item Master", "ITM");
                            $('[href=#step2]').tab('show');
                            return false;
                        }

                        $.ajax({
                            type: 'POST',
                            dataType: 'JSON',
                            data: imgageVal,
                            contentType: false,
                            cache: false,
                            processData: false,
                            url: "<?php echo site_url('ItemMaster/item_image_upload'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                refreshNotifications(true);
                                //$('#itemmaster_form')[0].reset();
                                $('.btn-wizard').removeClass('disabled');
                                $("#itm_documentSystemCode").val(faID);
                                attachment_modal_itemMaster(faID, "Item Master", "ITM");
                                //$('#itemmaster_form').bootstrapValidator('resetForm', true);
                                $('[href=#step2]').tab('show');
                            }, error: function () {
                                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                                stopLoad();
                                refreshNotifications(true);
                            }
                        });
                    } else {
                        $('.btn-primary').attr('disabled', false);
                    }
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

    function load_item_header() {
        var path = '<?php echo site_url('Barcode/generateBarcode/'); ?>';
        if (itemAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemAutoID': itemAutoID},
                url: "<?php echo site_url('ItemMaster/load_item_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {

                        $('#itemName').val(data['itemName']);
                        $('#edit_systemCode').text(data['itemSystemCode']);
                        var tmpSystemCode = data['itemSystemCode'];
                        var replaced = tmpSystemCode.replace("/", "-");
                        $('#barcodeDiv').html('<img class="barcodeDiv" src="' + path + '/' + replaced + '" alt="barcodeImage"/>');
                        //$('#edit_shortDescription').text(data['itemName']);
                        $('#itemDescription').val(data['itemDescription']);
                        $('#mainCategoryID').val(data['mainCategoryID']);
                        $('#mainCategoryID option:not(:selected)').prop('disabled', true);





                        // $('#stockadjust option:not(:selected)').prop('disabled', true);
                    /*    if (data['revanueGLAutoID'] == 0) {
                            $('#revanueGLAutoID option:not(:selected)').prop('disabled', false);
                        } else {
                            $('#revanueGLAutoID option:not(:selected)').prop('disabled', true);
                        }*/
                        $('#partno').val(data['partNo']);
                        $('#defaultUnitOfMeasureID').val(data['defaultUnitOfMeasureID']);
                       /* $('#defaultUnitOfMeasureID option:not(:selected)').prop('disabled', true);*/
                        $('#secondaryUOMID').val(data['secondaryUOMID']);
                    /*    $('#secondaryUOMID option:not(:selected)').prop('disabled', true);*/
                        $('#companyLocalSellingPrice').val(data['companyLocalSellingPrice']);
                        $('#seconeryItemCode').val(data['seconeryItemCode']);
                        $('#barcode').val(data['barcode']);
                        load_sub_cat(data['subcategoryID']);
                        $('#subcategoryID').val(data['subcategoryID']);
                        load_sub_sub_cat(data['subSubCategoryID']);
                        $('#subSubCategoryID').val(data['subSubCategoryID']);
                        $("#barcode").val(data['barcode']);
                        $("#maximunQty").val(data['maximunQty']);
                        $("#minimumQty").val(data['minimumQty']);
                        $("#reorderPoint").val(data['reorderPoint']);
                        if(data['mainCategory'] == 'Fixed Assets')
                        {
                            $('#COSTGLCODEdes').val(data['faCostGLAutoID']).change();
                            $('#ACCDEPGLCODEdes').val(data['faACCDEPGLAutoID']).change();
                            /*$('#ACCDEPGLCODEdes option:not(:selected)').prop('disabled', true);*/
                            $('#DEPGLCODEdes').val(data['faDEPGLAutoID']).change();
                            /*  $('#DEPGLCODEdes option:not(:selected)').prop('disabled', true);*/
                            $('#DISPOGLCODEdes').val(data['faDISPOGLAutoID']).change();
                            $('#DISPOGLCODEdes option:not(:selected)').prop('disabled', true);
                        }
                        if(data['mainCategory'] == 'Inventory')
                        {
                            $('#revanueGLAutoID').val(data['revanueGLAutoID']).change();
                            $('#costGLAutoID').val(data['costGLAutoID']).change();
                            $('#assteGLAutoID').val(data['assteGLAutoID']).change();
                            $('#stockadjust').val(data['stockAdjustmentGLAutoID']).change();
                        }
                        if(data['mainCategory'] == 'Service')
                        {
                            $('#revanueGLAutoID').val(data['revanueGLAutoID']).change();
                            $('#costGLAutoID').val(data['costGLAutoID']).change();

                        }
                        if(data['mainCategory'] == 'Non Inventory')
                        {
                            $('#revanueGLAutoID').val(data['revanueGLAutoID']).change();
                            $('#costGLAutoID').val(data['costGLAutoID']).change();
                        }

                  /*      $('#COSTGLCODEdes option:not(:selected)').prop('disabled', true);*/

                        // $('#subcategoryID option:not(:selected)').prop('disabled', true);
                        if (data['isActive'] == 1) {
                            $('#checkbox_isActive').iCheck('check');
                        } else {
                            $('#checkbox_isActive').iCheck('uncheck');
                        }
                        if (data['isSubitemExist'] == 1) {
                            $('#checkbox_isSubitemExist').iCheck('check');
                        } else {
                            $('#checkbox_isSubitemExist').iCheck('uncheck');
                        }
                        if (data['itemImage'] == 'no-image.png') {
                            $("#changeImg").attr("src", data['item_no_image']);
                        } else {
                            //$("#changeImg").attr("src", "<?php echo base_url('uploads/itemMaster/'); ?>" + '/' + data['itemImage']);
                            $("#changeImg").attr("src", data['emp']);
                        }
                        $("#itm_documentSystemCode").val(itemAutoID);
                        attachment_modal_itemMaster(itemAutoID, "Item Master", "ITM");

                        // $('[href=#step2]').tab('show');
                        // $('a[data-toggle="tab"]').removeClass('btn-primary');
                        // $('a[data-toggle="tab"]').addClass('btn-default');
                        // $('[href=#step2]').removeClass('btn-default');
                        // $('[href=#step2]').addClass('btn-primary');
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


    function load_sub_sub_cat() {
        $('#subSubCategoryID option').remove();
        $('#subSubCategoryID').val("");
        var subsubid = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subSubCategoryID').empty();
                    var mySelect = $('#subSubCategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
                load_codification_tmplat()
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function load_gl_codes() {
        $('#revanueGLAutoID').val("");
        $('#costGLAutoID').val("");
        $('#stockadjust').val("");
        $('#assteGLAutoID').val("");
        $('#COSTGLCODEdes').val("");
        $('#ACCDEPGLCODEdes').val("");
        $('#DEPGLCODEdes').val("");
        $('#DISPOGLCODEdes').val("");
        itemCategoryID = $('#subcategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_gl_codes"); ?>',
            dataType: 'json',
            data: {'itemCategoryID': itemCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#revanueGLAutoID").val(data['revenueGL']).change();
                    $("#costGLAutoID").val(data['costGL']).change();
                    $("#assteGLAutoID").val(data['assetGL']).change();
                    $("#COSTGLCODEdes").val(data['faCostGLAutoID']).change();
                    $("#ACCDEPGLCODEdes").val(data['faACCDEPGLAutoID']).change();
                    $("#DEPGLCODEdes").val(data['faDEPGLAutoID']).change();
                    $("#DISPOGLCODEdes").val(data['faDISPOGLAutoID']).change();
                    $("#stockadjust").val(data['stockAdjustmentGL']).change();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }




    $('#changeImg').click(function () {
        $('#itemImage').click();
    });

    /*$('#empImage').change(function(){

     });*/

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };

            reader.readAsDataURL(obj.files[0]);
        }
    }

    function attachment_modal_itemMaster(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': 0},
                success: function (data) {
                    $('#purchaseOrder_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + " Attachments");
                    $('#purchaseOrder_attachment').empty();

                    $('#purchaseOrder_attachment').append('' + data + '');

                    //$("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function delete_itemMaster_attachment(attachmentID, myFileName, DocumentSystemCode) {
        if (itemAutoID) {
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
                        data: {'attachmentID': attachmentID, 'myFileName': myFileName},
                        url: "<?php echo site_url('Procurement/delete_purchaseOrder_attachement'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            attachment_modal_itemMaster(DocumentSystemCode, "Item Master", "ITM");
                            stopLoad();
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function itemMaster_document_uplode() {
        var formData = new FormData($("#itemMaster_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    attachment_modal_itemMaster($('#itm_documentSystemCode').val(), 'Item Master', 'ITM');
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function save_item_bin_location(wareHouseAutoID) {
        var binLocationID = $('#binloc_' + wareHouseAutoID).val();
        var itemBinlocationID = $('#itemBinlocationID_' + wareHouseAutoID).val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: ({
                binLocationID: binLocationID,
                itemBinlocationID: itemBinlocationID,
                itemAutoID: itemAutoID,
                wareHouseAutoID: wareHouseAutoID
            }),
            url: "<?php echo site_url('ItemMaster/save_item_bin_location'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#itemBinlocationID_' + wareHouseAutoID).val(data[2]);
                }

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function load_item_bin_location() {
        var path = '<?php echo site_url('Barcode/generateBarcode/'); ?>';
        if (itemAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemAutoID': itemAutoID},
                url: "<?php echo site_url('ItemMaster/load_item_bin_location'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            $('#binloc_' + text['warehouseAutoID']).val(text['binLocationID']);
                            $('#itemBinlocationID_' + text['warehouseAutoID']).val(text['itemBinlocationID']);
                        });
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

    function validate_itempull(id, type) {
        if (itemAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'typevalue': id, 'Type': type, 'itemAutoID': itemAutoID},
                url: "<?php echo site_url('ItemMaster/item_type_pull'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    if (data['typechange'] == 1) {
                        if (!jQuery.isEmptyObject(data['item'])) {
                            changeFormCode();
                        switch (data['cattype']) {
                            case "Main":
                                $('#mainCategoryID').val(data['typevalue']);
                                changeFormCode();
                                load_sub_cat(data['typevaluesub']);
                                $('#subcategoryID').val(data['typevaluesub']);
                                load_sub_sub_cat(data['typevaluesubsub']);
                                $('#subSubCategoryID').val(data['typevaluesubsub']);
                                break;
                            case "Sub":
                                load_sub_cat(data['typevalue']);
                                $('#subcategoryID').val(data['typevalue']);
                                load_sub_sub_cat(data['typevaluesubsub']);
                                $('#subSubCategoryID').val(data['typevaluesubsub']);
                                break;
                            case "UomDe":
                                $('#defaultUnitOfMeasureID').val(data['typevalue']);
                                break;
                            case "SecUom":
                                $('#secondaryUOMID').val(data['typevalue']);
                                break;
                            case "revenueGL":
                                $('#revanueGLAutoID').val(data['typevalue']).change();
                                break;
                            case "costGL":
                                $('#costGLAutoID').val(data['typevalue']).change();
                                break;
                            case "assetGL":
                                $('#assteGLAutoID').val(data['typevalue']).change();
                                break;
                            case "stockAdjustment":
                                $('#stockadjust').val(data['typevalue']).change();
                                break;
                            /*case "faCostGL":
                                $('#stockadjust').val(data['faCostGLAutoID']).change();
                                break;
                            default:*/

                        }
                        $('#access_denied_body').empty();
                        x = 1;
                        if (jQuery.isEmptyObject(data['item'])) {
                            $('#access_denied_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                        } else {
                            $.each(data['item'], function (key, value) {
                                $('#access_denied_body').append('<tr><td>' + x + '</td><td>' + value['documentcode'] + '</td><td>' + value['documentType'] + '</td><td>' + value['referanceNo'] + '</td></tr>');
                                x++;
                            });
                        }
                        $('#access_denied').modal('show');

                    }
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

    function load_sub_cat(select_val) {
        changeFormCode();
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subSubCategoryID').val("");
        $('#subSubCategoryID option').remove();
        var subid = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subcat"); ?>',
            dataType: 'json',
            data: {'subid': subid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subcategoryID').empty();
                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option></option>').val('').html('Select Option'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function changeFormCode() {
        itemCategoryID = $('#mainCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_category_type_id"); ?>',
            dataType: 'json',
            data: {'itemCategoryID': itemCategoryID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    if ((data['categoryTypeID'] == 2) || (data['categoryTypeID'] == 4)) {
                        $("#assetGlCode_div").addClass("hide");
                        $("#cls_maximunQty").addClass("hide");
                        $("#cls_minimumQty").addClass("hide");
                        $("#cls_reorderPoint").addClass("hide");
                        $("#stockadjustment").addClass("hide");

                    } else {
                        $("#assetGlCode_div").removeClass("hide");
                        $("#cls_maximunQty").removeClass("hide");
                        $("#cls_minimumQty").removeClass("hide");
                        $("#cls_reorderPoint").removeClass("hide");
                        $("#stockadjustment").removeClass("hide");


                    }
                    if (data['categoryTypeID'] == 3) {
                        $("#inventry_row_div").addClass("hide");
                        $("#fixed_row_div").removeClass("hide");
                        $("#stockadjustment").addClass("hide");

                    } else {
                        $("#inventry_row_div").removeClass("hide");
                        $("#fixed_row_div").addClass("hide");


                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }

    function validateBarCode(code) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'barCode': code, 'itemAutoID': itemAutoID},
            url: "<?php echo site_url('ItemMaster/item_barcode_validate'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#barcode_validate_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data)) {
                        $('#barcode_validate_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                    } else {
                        var barcodeVal = $('#barcode').val();
                        $.each(data, function (key, value) {
                            $('#barcode_validate_body').append('<tr><td>' + x + '</td><td>' + value['documentcode'] + '</td><td>' + value['item'] + '</td><td>' +  barcodeVal + '</td></tr>');
                            x++;
                        });
                        $('#barcode').val('');
                    }
                    $('#barcode_validate').modal('show');
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

    function load_codification_tmplat(setupDetailID=0) {
        var subid = $('#subcategoryID').val();
        var attributeDetailID=0;
        if(setupDetailID>0){
             attributeDetailID = $('#attributeDetailID_'+setupDetailID).val();
        }
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Codification/load_codification_tmplat"); ?>',
            dataType: 'html',
            data: {'subid': subid,'attributeDetailID': attributeDetailID,'itemAutoID': itemAutoID},
            async: false,
            success: function (data) {
                $("#codsetup").html('');
                if (jQuery.isEmptyObject(data)) {
                    myAlert('e','Code setup not assign to the selected sub category');
                    $('#subcategoryID').val('');
                }else{
                    $("#codsetup").html(data);
                }

                if (!jQuery.isEmptyObject(itemAutoID)){
                    load_codification_edit_drp()
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                myAlert('e','Code setup not assign to the selected sub category');
                $('#subcategoryID').val('');
            }
        });
    }

    var n;
    function load_sub_codes(setupDetailID,attributeID,ds) {
       var attributeDetailID = $('#attributeDetailID_'+setupDetailID).val();
        var subid = $('#subcategoryID').val();

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Codification/load_sub_codes"); ?>',
            dataType: 'json',
            data: {'subid': subid,'setupDetailID': setupDetailID,'attributeDetailID': attributeDetailID,'attributeID': attributeID},
            async: false,
            success: function (data) {
                if (data[0]=='s') {
                    $.each(data[2], function (key, value) {
                        $('#attributeDetailID_'+key).html('<option value="">Select Value</option>');
                        $.each(value, function (ky, dtl) {
                            $('#attributeDetailID_'+key).append('<option value="' + dtl['attributeDetailID'] + '">' + dtl['detailDescription'] + '</option>');
                        });
                    });
                }else{
                    $.each(data[2], function (key, value) {
                        $('#attributeDetailID_'+key).html('<option value="">Select Value</option>');
                        $.each(value, function (ky, dtl) {
                            $('#attributeDetailID_'+key).append('<option value="' + dtl['attributeDetailID'] + '">' + dtl['detailDescription'] + '</option>');
                        });
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                myAlert('e','Code setup not assign to the selected sub category');
                //$('#subcategoryID').val('');
            }
        });

    }



    function load_codification_edit_drp() {
        var subid = $('#subcategoryID').val();

        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Codification/load_codification_edit_drp"); ?>',
            dataType: 'json',
            data: {'subid': subid,'itemAutoID': itemAutoID},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $.each(data, function (key, dtl) {
                        var setupDetailID=dtl['setupDetailID'];
                        var strng = $('#attributeDetailID_'+setupDetailID).val();
                        
                        $('#attributeDetailID_'+setupDetailID).data("selval");
                        $('#attributeDetailID_'+setupDetailID).attr("data-selval", strng);
                    });
                    $.each(data, function (key, dtl) {
                        var setupDetailID=dtl['setupDetailID'];
                        var selval = $('#attributeDetailID_'+setupDetailID).attr("data-selval");
                        $('#attributeDetailID_'+setupDetailID).val(selval).change();
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //myAlert('e','Code setup not assign to the selected sub category');
            }
        });
    }
</script>