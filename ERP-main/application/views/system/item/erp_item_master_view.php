<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
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
$ApprovalforItemMaster= getPolicyValues('AIM', 'All');
if($ApprovalforItemMaster==NULL){
    $ApprovalforItemMaster=0;
}
?>
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
                    <div style="margin-right: 15px;" class="form-group col-sm-4">
                        <label>
                            <?php echo $this->lang->line('transaction_main_category'); ?><!--Main Category--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('mainCategoryID', $main_category_arr, 'Each', 'class="form-control" id="mainCategoryID" onchange="load_sub_cat(),validate_itempull(this.value,1);" readonly'); ?>
                    </div>
                    <div style="margin-right: 15px;" class="form-group col-sm-4">
                        <label>
                            <?php echo $this->lang->line('transaction_sub_category'); ?><!--Sub Category--> <?php required_mark(); ?></label>
                        <select name="subcategoryID" id="subcategoryID" class="form-control searchbox"
                                onchange="load_sub_sub_cat(),load_gl_codes(),validate_itempull(this.value,2);" readonly>
                            <option value="">
                                <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                        </select>
                    </div>
                    <div style="margin-right: 15px;" class="form-group col-sm-4">
                        <label>
                            <?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?><!--Sub Sub Category--> </label>
                        <select name="subSubCategoryID" id="subSubCategoryID" class="form-control searchbox" readonly>
                            <option value="">
                                <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div style="margin-right: 15px;" class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('erp_item_master_short_description'); ?><?php required_mark(); ?></label>
                        <!--Short Description-->
                        <input type="text" class="form-control" id="itemName" name="itemName" readonly>
                    </div>
                    <div style="margin-right: 15px;" class="form-group col-sm-8">
                        <label><?php echo $this->lang->line('erp_item_master_long_description'); ?><?php required_mark(); ?></label>
                        <!--Long Description-->
                        <input type="text" class="form-control" id="itemDescription" name="itemDescription" readonly>
                    </div>
                </div>
                <div class="row">
                    <div style="margin-right: 15px;" class="form-group col-sm-4">
                        <label for="">
                            <?php echo $this->lang->line('erp_item_master_secondary_code'); ?><!--Secondary Code--> <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="seconeryItemCode" name="seconeryItemCode" readonly>
                    </div>
                    <div style="margin-right: 15px;" class="form-group col-sm-4">
                        <label for="">
                            <?php echo $this->lang->line('transaction_unit_of_measure'); ?><!--Unit of Measure--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown('defaultUnitOfMeasureID', $uom_arr, 'Each', 'class="form-control" id="defaultUnitOfMeasureID" onchange="validate_itempull(this.value,3);" required readonly'); ?>
                    </div>
                    <?php
                    if ($secondaryUOM == 1) {
                        ?>
                        <div style="margin-right: 15px;" class="form-group col-sm-4">
                            <label for="">Secondary Unit of Measure <?php required_mark(); ?></label>
                            <?php echo form_dropdown('secondaryUOMID', $uom_arr, 'Each', 'class="form-control" id="secondaryUOMID" onchange="validate_itempull(this.value,4);" readonly'); ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div style="margin-right: 15px;" class="form-group col-sm-4">
                        <label for="">
                            Purchasing Price</label>

                        <div class="input-group">
                            <div
                                    class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                            <input type="text" step="any" class="form-control number" id="companyLocalPurchasingPrice"
                                   name="companyLocalPurchasingPrice" value="0" readonly>
                        </div>
                    </div>
                    <div style="margin-right: 15px;" class="form-group col-sm-4">
                        <label for="">
                            <?php echo $this->lang->line('transaction_selling_price'); ?><!--Selling Price--> <?php required_mark(); ?></label>

                        <div class="input-group">
                            <div
                                    class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                            <input type="text" step="any" class="form-control number" id="companyLocalSellingPrice"
                                   name="companyLocalSellingPrice" value="0" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div style="margin-right: 15px;" class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('transaction_barcode'); ?><!--Barcode--></label>
                <input type="text" class="form-control" id="barcode" name="barcode" onchange="validateBarCode(this.value)" readonly>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('transaction_part_no'); ?><!--Part No--> </label>
                <input type="text" class="form-control" id="partno" name="partno" readonly>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-2" id="cls_maximunQty">
                <label for=""><?php echo $this->lang->line('transaction_maximum_qty'); ?><!--Maximum Qty--></label>
                <input type="text" class="form-control number" id="maximunQty" name="maximunQty" readonly>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-2" id="cls_minimumQty">
                <label for=""><?php echo $this->lang->line('transaction_minimum_qty'); ?><!--Minimum Qty--></label>
                <input type="text" class="form-control number" id="minimumQty" name="minimumQty" readonly>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-2" id="cls_reorderPoint">
                <label for="">
                    <?php echo $this->lang->line('erp_item_master_recorder_level'); ?><!--Reorder Level--></label>
                <input type="text" class="form-control number" id="reorderPoint" name="reorderPoint" readonly>
            </div>

        </div>
        <div class="row" id="inventry_row_div">
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="">
                    <?php echo $this->lang->line('erp_item_master_revenue_gl_code'); ?><!--Revenue GL Code --></label>
                <?php echo form_dropdown('revanueGLAutoID', $revenue_gl_arr, '', 'class="form-control select2" id="revanueGLAutoID" onchange="validate_itempull(this.value,5);" readonly'); ?>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4">
                <label for="">
                    <?php echo $this->lang->line('erp_item_master_cost_gl_code'); ?><!--Cost GL Code--></label>
                <?php echo form_dropdown('costGLAutoID', $cost_gl_arr, '', 'class="form-control select2" id="costGLAutoID" onchange="validate_itempull(this.value,6);" readonly'); ?>
            </div>
            <div style="margin-right: 15px;" class="form-group col-sm-4" id="assetGlCode_div">
                <label for="">
                    <?php echo $this->lang->line('erp_item_master_asset_gl_code'); ?><!--Asset GL Code--></label>
                <?php echo form_dropdown('assteGLAutoID', $asset_gl_arr, $this->common_data['controlaccounts']['INVA'], 'class="form-control select2" id="assteGLAutoID" onchange="validate_itempull(this.value,7);" readonly'); ?>
            </div>


        </div>
        <div class="row hide" id="fixed_row_div">
            <div style="margin-right: 15px;" class="col-md-3">
                <div class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('erp_item_master_cost_account'); ?><!--Cost Account--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('COSTGLCODEdes', $fetch_cost_account, '', 'class="form-control form1 select2" id = "COSTGLCODEdes" onchange="validate_itempull(this.value,9);" readonly'); ?>
                </div>
            </div>
            <div style="margin-right: 15px;" class="col-md-3">
                <div class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('erp_item_master_acc_dep_gl_code'); ?><!--Acc Dep GL Code --><?php required_mark(); ?></label>
                    <?php echo form_dropdown('ACCDEPGLCODEdes', $fetch_cost_account, '', 'class="form-control form1 select2" id = "ACCDEPGLCODEdes" readonly'); ?>
                </div>
            </div>
            <div style="margin-right: 15px;" class="col-md-3">
                <div class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('erp_item_master_dep_gl_code'); ?><!--Dep GL Code--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('DEPGLCODEdes', $fetch_dep_gl_code, '', 'class="form-control form1 select2" id = "DEPGLCODEdes" readonly'); ?>
                </div>
            </div>
            <div style="margin-right: 15px;" class="col-md-3">
                <div class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('erp_item_master_disposal_gl_code'); ?><!--Disposal GL Code--> <?php required_mark(); ?></label>
                    <?php echo form_dropdown('DISPOGLCODEdes', $fetch_disposal_gl_code, '', 'class="form-control form1 select2" id = "DISPOGLCODEdes" readonly'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div style="margin-right: 15px;" class="col-md-3">
                <div class="form-group">
                    <div class="form-group" id="stockadjustment">
                        <label for="">Stock Adjustment Control</label>
                        <?php echo form_dropdown('stockadjust', $stock_adjustment, '', 'class="form-control form1 select2" id="stockadjust" onchange="validate_itempull(this.value,8);" readonly'); ?>
                    </div>
                </div>
            </div>
            <div  class="col-md-3">
                <div style="margin-right: 15px;class="form-group">
                    <label for=""><?php echo $this->lang->line('erp_item_master_is_active'); ?><!--isActive--></label>

                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_isActive" type="checkbox"
                                   data-caption="" class="columnSelected" name="isActive" value="1" checked readonly>
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div style="margin-right: 15px;" class="col-md-3">
                <div style="margin-right: 15px; class="form-group">
                    <label for="">
                        <?php echo $this->lang->line('erp_item_master_is_sub_item_applicable'); ?><!--is Sub-item Applicable--> </label>

                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_isSubitemExist" type="checkbox"
                                   data-caption="" class="columnSelected" name="isSubitemExist" value="1" readonly>
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <hr>

        </form>

    </div>


</div>
<script type="text/javascript">
    var itemAutoID;
    var ApprovalforItemMaster = <?php echo $ApprovalforItemMaster ?>;
    $(document).ready(function () {
        itemAutoID = null;
        p_id =<?php echo $this->input->post('itemAutoID'); ?>;
        if (p_id) {
            itemAutoID = p_id;
            //alert(p_id);
            load_item_header();
        }
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
                        $('#companyLocalPurchasingPrice').val(data['companyLocalPurchasingPrice']);
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
</script>
