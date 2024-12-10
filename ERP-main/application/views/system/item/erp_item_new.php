<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$main_category_arr = all_main_category_drop();
$revenue_gl_arr = all_revenue_gl_drop();
$cost_gl_arr = all_cost_gl_drop();
$asset_gl_arr = all_asset_gl_drop();
$empCodeTemp = empCodeGenerateTemp();
$uom_arr = all_umo_new_drop();
$stock_adjustment = stock_adjustment_control_drop();
$fetch_cost_account = fetch_cost_account();
$fetch_dep_gl_code = fetch_gl_code(array('masterCategory' => 'PL', 'subCategory' => 'PLE'));
$fetch_disposal_gl_code = fetch_gl_code(array('masterCategory' => 'PL'));
$ware_house_binlocations = companyWarehouseBinLocations();
$companyBinLocations = companyBinLocations();
$secondaryUOM = getPolicyValues('SUOM', 'All');
$ApprovalforItemMaster= getPolicyValues('AIM', 'All');
$hidesellingpricePolicy= getPolicyValues('HIMSP', 'All');
$supplierList = get_suppliermaster_list();
$partNovalue=getPolicyValues('LNG', 'All');
$companyLanguage = getPolicyValues('LNG', 'All');

if($ApprovalforItemMaster==NULL){
    $ApprovalforItemMaster=0;
}
$showPurchasePrice = getPolicyValues('SPP', 'All');
if($showPurchasePrice==' ' || $showPurchasePrice== null || empty($showPurchasePrice)){
    $showPurchasePrice = 0;
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

    /* #approvebtn,
    #rejectbtn {
    display: none;
    } */

    

</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

    <div class="steps">
    <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
        <span class="step__icon"></span>
        <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_one'); ?>
            - <?php echo $this->lang->line('erp_item_master_item_header'); ?></span><!--Step 1--><!--Item Header-->
    </a>
    <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" data-toggle="tab">
        <span class="step__icon"></span>
        <span class="step__label"><?php echo $this->lang->line('transaction_goods_received_voucher_step_two'); ?>
            - <?php echo $this->lang->line('erp_item_master_item_attachments'); ?></span><!--Step 2--><!--Item Attachments-->
    </a>
    <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" data-toggle="tab">
        <span class="step__icon"></span>
        <span class="step__label">Step 3 - Bin Locations</span>
    </a>
    </div>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="itemmaster_form"'); ?>
        <div class="row modal-body" style="padding-bottom: 0px;">
            <div class="col-sm-3" align="" style="padding-left: 0px;">
                <div class="fileinput-new thumbnail" style="margin-bottom: 4px;width: 200px; height: 150px;margin:auto">
                    <img src="<?php echo base_url('images/item/no-image.png'); ?>" id="changeImg">
                    <input type="file" name="itemImage" id="itemImage" style="display: none;"
                           onchange="loadImage(this)"/>
                </div>
                <div class="form-group col-sm-12 no-padding">
                    <div id="barcodeDiv"></div>
                </div>
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
                            <?php echo $this->lang->line('erp_item_master_sub_sub_category'); ?><!--Sub Sub Sub Category--> </label>
                        <select name="subSubCategoryID" id="subSubCategoryID" class="form-control searchbox" onchange="load_sub_sub_sub_cat()">
                            <option value="">
                                <?php echo $this->lang->line('transaction_select_category'); ?><!--Select Category--></option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label> Sub Sub Sub Category </label>
                        <select name="subSubSubCategoryID" id="subSubSubCategoryID" class="form-control searchbox" onchange="load_se_code_temp()">
                            <option value="">Select Category</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('erp_item_master_short_description'); ?><?php required_mark(); ?></label>
                        <!--Short Description-->
                        <input type="text" class="form-control" id="itemName" name="itemName">
                    </div>
                    <div class="form-group col-sm-4">
                        <label><?php echo $this->lang->line('erp_item_master_long_description'); ?><?php required_mark(); ?></label>
                        <!--Long Description-->
                        <input type="text" class="form-control" id="itemDescription" name="itemDescription" onkeyup="set_short_description(this.value)">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="">
                            <?php echo $this->lang->line('erp_item_master_secondary_code'); ?><!--Secondary Code--></label>
                        <input type="text" class="form-control" id="seconeryItemCode" name="seconeryItemCode" >
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
                    <?php
                    if ($showPurchasePrice == 1) { ?>
                    <div class="form-group col-sm-4">
                        <label for="">
                            Purchasing Price</label>
                        <div class="input-group">
                            <div
                                    class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                            <input type="text" step="any" class="form-control number" id="companyLocalPurchasingPrice"
                                   name="companyLocalPurchasingPrice" value="0">
                        </div>
                    </div>
                        <?php
                    }
                    ?>

                    <?php if($hidesellingpricePolicy==1){ ?>
                        <input type="hidden" step="any" class="form-control number" id="companyLocalSellingPrice"
                                   name="companyLocalSellingPrice" value="0">
                    <?php }else{ ?>
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
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('transaction_barcode'); ?><!--Barcode--></label>
                <input type="text" class="form-control" id="barcode" name="barcode" onchange="validateBarCode(this.value)">
            </div>
            <div class="form-group col-sm-3">
                <?php if ($partNovalue == 'Default'): ?><!--Language Changes Based on policy--> 
                    <label for=""><?php echo $this->lang->line('transaction_part_no');?></label>
                <?php else: ?>
                    <label for=""><?php echo $this->lang->line('transaction_oem_part_no');?></label>
                <?php endif; ?>

                <button type="button" onclick="open_part_number_modal();" class="btn btn-primary pull-right" ><i class="fa fa-plus"></i> </button>
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
                        <div class="skin-section isSubItemExist" id="extraColumns">
                            <input id="checkbox_isSubitemExist"  type="checkbox"
                                   data-caption="" class="columnSelected" name="isSubitemExist" value="1">
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Allow this item to sell</label>
                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_sell_this"  type="checkbox"
                                   data-caption="" class="columnSelected" name="sell_this" value="1" checked>
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div> 

            <div class="col-md-4">
                <div class="form-group">
                    <label>Allow this item to Buy</label>
                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="checkbox_buy_this"  type="checkbox"
                                   data-caption="" class="columnSelected" name="buy_this" value="1" checked>
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div> 
            
            <div class="col-md-4">
                <div class="form-group">
                    <?php if($companyLanguage == 'FlowServe') { ?>
                        <label>Operation/Service Item</label>
                    <?php } else { ?>
                        <label>Manufacturing Item</label>
                    <?php } ?>
                    <div class="skin skin-square">
                        <div class="skin-section" id="extraColumns">
                            <input id="isMfqItem"  type="checkbox"
                                   data-caption="" class="columnSelected" name="isMfqItem" value="1" >
                            <label for="checkbox">
                                &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row hide subitemapplicableon">
             <div class="col-md-4">
                <div class="form-group">
                    <div class="form-group" id="stockadjustment">
                        <label for="">Sub Item Applicable On</label>
                        <?php echo form_dropdown('subItem', array("1"=>'Primary unit of measure',"2"=>'Secondary unit of measure'), '', 'class="form-control form1 select2" id="subItem"'); ?>
                    </div>
                </div>
             </div>  
        </div>
        <hr>
        <div id="">
            <button class="btn btn-primary-new size-lg pull-right" type="submit" id="submitbtn">
                <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
        </div>
        </form>
        <?php if($ApprovalforItemMaster==1) {  ?>
            <div id="confirmbtn">
                <button class="btn btn-success size-lg btn-wizard pull-right" style="margin-right: 5px;" onclick="confirmation()">
                    <?php echo $this->lang->line('common_confirm');?></button>
            </div>
            <div id="approvebtn"></div>
            <div id="rejectbtn"></div>
        <?php } ?>
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
<div class="modal fade" id="rejectCommentModal" tabindex="-1" role="dialog" aria-labelledby="rejectCommentModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rejectCommentModal">Add Comment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="comment">Your Comment:</label>
            <textarea class="form-control" id="comment" rows="3"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary size-lg  btn-wizard pull-right" data-dismiss="modal">Close</button>
        <button type="button" id="rejectSaveBtn" class="btn btn-primary size-lg btn-wizard pull-right">Add Comment</button>
    
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



<div aria-hidden="true" role="dialog" tabindex="-1" id="item_part_number_model" class="modal fade" style="display: none;">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?php if ($partNovalue == 'Default'){ ?>
                <h3 class="modal-title" id="itemPartNumberModelHeader">Part Number</h3>
                <?php }else{ ?>
                    <h3 class="modal-title" id="itemPartNumberModelHeader">OEM Part Number</h3>
                <?php } ?>
            </div>
            
                <div class="modal-body">
                   <form role="form" id="item_partnumber_form" class="form-horizontal">
                    <div class="row">
                        <input type="hidden" class="form-control" id="itemPartNumberedit" name="itemPartNumberedit">
                        
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Supplier <?php required_mark(); ?></label>
                            <div class="col-sm-5">
                                <?php  echo form_dropdown('supplier', $supplierList,'','class="form-control select2" id="supplier"'); ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <?php if ($partNovalue == 'Default'){ ?>
                                <label class="col-sm-4 control-label">Part Number<?php required_mark(); ?></label>
                            <?php }else{ ?>
                                <label class="col-sm-4 control-label">OEM Part Number<?php required_mark(); ?></label>
                            <?php } ?>
                            
                            <div class="col-sm-6">
                               <input type="text" class="form-control" id="partNumber" name="partNumber"  placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Active</label>
                            <div class="col-sm-6">
                            <input id="isactive" type="checkbox"
                                            data-caption="" class="columnSelected" name="isactive" value="1">
                                        <label for="checkbox">
                                            &nbsp;
                                        </label>
                            </div>
                        </div>

                        
                    </div>
                    </form>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button"><?php echo $this->lang->line('common_close')?><!--Close--></button>
                        <button onclick="save_partNumber_details()" class="btn btn-primary">Save</button>
                    </div>
            
        </div>

        <div class="table-responsive">
            <table id="partNumber_table" class="<?php echo table_class(); ?>">
                <thead>
                    <tr>
                        <th style="min-width: 5%">#</th>
                        <th style="min-width: 10%">Supplier</th>
                        <th style="min-width: 20%">Part Number</th>
                        <th style="min-width: 11%">Active</th>
                        <th style="min-width: 8%"><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                </thead>
            </table>
        </div>
        
    </div>
</div>
<script type="text/javascript">
    var itemAutoID;
    var ApprovalforItemMaster = <?php echo $ApprovalforItemMaster ?>;
    var longDesToShortPolicy = '<?php echo getPolicyValues('IMSDFLD', 'All'); ?>'?'<?php echo getPolicyValues('IMSDFLD', 'All'); ?>':0;
    var SecondaryCodePolicy = '<?php echo getPolicyValues('IMSCOA', 'All'); ?>'?'<?php echo getPolicyValues('IMSCOA', 'All'); ?>':0;
    var subsubCategoryBaseNewSequencePolicy ='<?php echo getPolicyValues('IMSSNS', 'All'); ?>'?'<?php echo getPolicyValues('IMSSNS', 'All'); ?>':0;
    $(document).ready(function () {
        $('.headerclose').click(function () {
            fetchPage('system/item/erp_item_master', '', 'Item Master')
        });
        $('.select2').select2();
        itemAutoID = null;
        number_validation();
       // load_sub_cat();

        $('#extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });
        $('.isSubItemExist input').on('ifChecked', function (event) {
            $('.subitemapplicableon').removeClass('hide');
        });

        $('.isSubItemExist input').on('ifUnchecked', function (event) {
            $('.subitemapplicableon').addClass('hide');
        });


      
       // $('#approvebtn').empty();
        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            itemAutoID = p_id;
            if(ApprovalforItemMaster){
                load_aprovebtn(itemAutoID);
               
            }
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
            $('#rejectbtn').prop('disabled', false);
            $('#mainCategoryID').prop("disabled", false);
            $('#subcategoryID').prop("disabled", false);
            $('#subSubCategoryID').prop("disabled", false);
            $('#subSubSubCategoryID').prop("disabled", false);
            $('#defaultUnitOfMeasureID').prop("disabled", false);
            $('#secondaryUOMID').prop("disabled", false);
            $('#revanueGLAutoID').prop("disabled", false);
            $('#costGLAutoID').prop("disabled", false);
            $('#assteGLAutoID').prop("disabled", false);
            $('#COSTGLCODEdes').prop("disabled", false);
            $('#ACCDEPGLCODEdes').prop("disabled", false);
            $('#DEPGLCODEdes').prop("disabled", false);
            $('#DISPOGLCODEdes').prop("disabled", false);
            $('#stockadjust').prop("disabled", false);

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
                url: "<?php echo site_url('ItemMaster/save_itemmaster'); ?>",
                beforeSend: function () {
                    startLoad();
                   // $('#submitbtn').prop('disabled', true);
                    $('#mainCategoryID').prop("disabled", true);
                    $('#subcategoryID').prop("disabled", true);

                    if(subsubCategoryBaseNewSequencePolicy==1){
                        $('#submitbtn').prop('disabled', false);
                        $('#subSubCategoryID').prop("disabled", false);
                        $('#subSubSubCategoryID').prop("disabled", false);
                    }else{
                        $('#submitbtn').prop('disabled', true);
                        $('#subSubCategoryID').prop("disabled", true);
                        $('#subSubSubCategoryID').prop("disabled", true);
                    }
                  //  $('#subSubCategoryID').prop("disabled", true);
                    //$('#subSubSubCategoryID').prop("disabled", true);
                    $('#revanueGLAutoID').prop("disabled", true);
                    $('#costGLAutoID').prop("disabled", true);
                    $('#assteGLAutoID').prop("disabled", true);

                    $('#COSTGLCODEdes').prop("disabled", true);
                    $('#ACCDEPGLCODEdes').prop("disabled", true);
                    $('#DEPGLCODEdes').prop("disabled", true);
                    $('#DISPOGLCODEdes').prop("disabled", true);
                    $('#stockadjust').prop("disabled", true);


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

    function open_part_number_modal(){
        $('#item_part_number_model').modal('show');
        $('#itemPartNumberedit').val('');
        $('#supplier').val('').change();
        fetch_partNumbers();

    }

    function fetch_partNumbers() {
        var p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        var Otable = $('#partNumber_table').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('ItemMaster/load_item_part_number_data'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "partNumberAutoID"},
                {"mData": "supplierSystemCode"},
                {"mData": "partNumber"},
                {"mData": "confirmed"},
                {"mData": "action"}
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                //aoData.push({ "name": "filter","value": $(".pr_Filter:checked").val()});
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
                aoData.push({"name": "itemAutoID", "value": p_id});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                    });
                }
            });
    }



    function save_partNumber_details() {
        var data = $('#item_partnumber_form').serializeArray();

        var itemAutoID = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;

        if (itemAutoID) {
            data.push({'name': 'itemAutoID', 'value': itemAutoID});

            $.ajax(
                {
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('ItemMaster/save_part_number_detail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);

                        if (data[0] == 's') {
                            setTimeout(function () {
                                tab_active(tabID);
                            }, 300);
                            refreshNotifications(true);
                            $('#item_partnumber_form')[0].reset();
                            $('#itemPartNumberedit').val('');
                            $('#supplier').val('').change();
                            fetch_partNumbers();
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

    function editItemPartNumber(id){
       
       $.ajax({
           type: 'post',
           dataType: 'json',
           data: {id:id},
           url: "<?php echo site_url('ItemMaster/edit_item_part_number'); ?>",
           success: function (data) {
               $('#itemPartNumberedit').val('');
               $('#supplier').val('').change();
              // $('#itemPartNumberModelHeader').html('Edit Part Number');
               $('#itemPartNumberedit').val(id);
               $('#supplier').val(data['supplierSystemCode']).change();
               $('#partNumber').val(data['partNumber']);
              
               if(data['isActive']==1){
                   $( "#isactive" ).prop( "checked", true );

               }else{
                   $( "#isactive" ).prop( "checked", false );
               }
           }, 
           error: function () {
               alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again')?>.');
           }
       });
   }

   function deleteItemPartNumber(id){
        swal({   title: "<?php echo $this->lang->line('common_are_you_sure');?>",/* Are you sure? */
            text: "<?php echo $this->lang->line('procuement_you_want_to_delete_this_file');?>",/* You want to delete this file ! */
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/* Delete */
            cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>",
            closeOnConfirm: true },
            function(){
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {id:id},
                    url: "<?php echo site_url('ItemMaster/delete_item_part_number'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if(data){
                            fetch_partNumbers();
                        }
                    }, 
                    error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.'); /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
    }

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

                        if (data['isMfqItem'] == 1) {
                            $('#isMfqItem').iCheck('check');
                        } else {
                            $('#isMfqItem').iCheck('uncheck');
                        }

                        $('#partno').val(data['partNo']);
                        $('#defaultUnitOfMeasureID').val(data['defaultUnitOfMeasureID']);
                        $('#secondaryUOMID').val(data['secondaryUOMID']);
                        $('#companyLocalSellingPrice').val(data['companyLocalSellingPrice']);
                        $('#companyLocalPurchasingPrice').val(data['companyLocalPurchasingPrice']);
                        $('#seconeryItemCode').val(data['seconeryItemCode']);
                        $('#barcode').val(data['barcode']);
                        load_sub_cat(data['subcategoryID']);
                        $('#subcategoryID').val(data['subcategoryID']);
                        load_sub_sub_cat(data['subSubCategoryID']);
                        $('#subSubCategoryID').val(data['subSubCategoryID']);
                        load_sub_sub_sub_cat(data['subSubSubCategoryID']);
                        $('#subSubSubCategoryID').val(data['subSubSubCategoryID']);
                        $("#barcode").val(data['barcode']);
                        $("#maximunQty").val(data['maximunQty']);
                        $("#minimumQty").val(data['minimumQty']);
                        $("#reorderPoint").val(data['reorderPoint']);
                        $("#subItem").val(data['subItemapplicableon']).change();
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
                      
                        if (data['allowedtoSellYN'] == 1) {
                            $('#checkbox_sell_this').iCheck('check');
                        } else {
                            $('#checkbox_sell_this').iCheck('uncheck');
                        }

                        if (data['allowedtoBuyYN'] == 1) {
                            $('#checkbox_buy_this').iCheck('check');
                        } else {
                            $('#checkbox_buy_this').iCheck('uncheck');
                        }

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
        $('#subSubSubCategoryID').val("");
        $('#subSubSubCategoryID option').remove();
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
        load_se_code_temp();
        $('#subcategoryID').val("");
        $('#subcategoryID option').remove();
        $('#subSubCategoryID').val("");
        $('#subSubCategoryID option').remove();
        $('#subSubSubCategoryID').val("");
        $('#subSubSubCategoryID option').remove();
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

    function load_se_code_temp() {
       // changeFormCode();
        if(SecondaryCodePolicy==1){
            //$('#subcategoryID').val("");
            var subid = $('#mainCategoryID').val();
            var subcategoryID = $('#subcategoryID').val();
            var subSubCategoryID = $('#subSubCategoryID').val();
            var subSubSubCategoryID = $('#subSubSubCategoryID').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("ItemMaster/load_se_code_temp"); ?>',
                dataType: 'json',
                data: {'subid': subid,'subcategoryID':subcategoryID,'subSubCategoryID':subSubCategoryID,'subSubSubCategoryID':subSubSubCategoryID},
                async: false,
                success: function (data) {

                    if(data[0]=='s'){

                        if(subsubCategoryBaseNewSequencePolicy==1){

                            if(subSubCategoryID && subSubSubCategoryID){
                                $('#seconeryItemCode').val(data[1]);
                            }
                        }else{
                            $('#seconeryItemCode').val(data[1]);
                        }

                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }
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

    /* Function added */
    function confirmation() {
        if (itemAutoID && ApprovalforItemMaster) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document');?>",/*You want to confirm this document*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#dd6b55",
                    confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'itemAutoID': itemAutoID},
                        url: "<?php echo site_url('itemMaster/im_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0],data[1]);
                            // load_aprovebtn(itemAutoID);
                            $('#rejectbtn').prop('disabled', false);
                            /*if(data[0]=='s'){
                                load_aprovebtn(itemAutoID);
                            }*/
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
        ;
    }
    function reject_btn_itemmaster() {
    if (itemAutoID) {
        // Triggering the reject modal
        $('#rejectCommentModal').modal('show');
        $('#rejectCommentModal').on('click', '#rejectSaveBtn', function() {
            var comment = $('#comment').val(); 
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemAutoID': itemAutoID, 'comment': comment},
                url: "<?php echo site_url('ItemMaster/reject_itemmaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);
                    if(data[2]=='2'){
                    $('#approve_btn').prop("disabled", true).val();
                        alert('Item rejected successfully.');
                    }
                    if(data[0]=='s'){

                    }
                    $('#rejectCommentModal').modal('hide'); // Close the modal after submission
           
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
               
            });
        });
    }
}



    function load_aprovebtn(itemAutoID){
        if(itemAutoID && ApprovalforItemMaster){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemAutoID': itemAutoID},
                url: "<?php echo site_url('ItemMaster/check_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        //$('#approvebtn').empty();
                        if(data[2]=='2'){
                            $('#approvebtn').html('<button id="approve_btn" class="btn btn-success size-lg btn-wizard pull-right" style="margin-right: 5px;" type="submit" onclick="approve_itemmaster(itemAutoID)">Approve</button>');
                            $('#approve_btn').prop("disabled", true).val();
                          
                           
                           
                        }else{
                            $('#approvebtn').html('<button id="approve_btn" class="btn btn-warning size-lg btn-wizard pull-right" style="margin-right: 5px;" type="submit" onclick="approve_itemmaster(itemAutoID)">Approve</button>');
                            $('#rejectbtn').html('<button id="reject_btn" class="btn btn-success size-lg btn-wizard pull-right" style="margin-right: 5px;" type="submit" onclick=" reject_btn_itemmaster()">Reject</button>');
                        }
                        $('#confirmbtn').empty();
                        //$('#submitbtn').prop("disabled", true).val();
                        $('#mainCategoryID').prop("disabled", true).val();
                        $('#subcategoryID').prop("disabled", true).val();
                        $('#subSubCategoryID').prop("disabled", true).val();

                        $('#itemName').prop("readonly", true).val();
                        $('#itemDescription').prop("readonly", true).val();
                        $('#seconeryItemCode').prop("readonly", true).val();
                        $('#defaultUnitOfMeasureID').prop("disabled", true).val();
                        $('#secondaryUOMID').prop("disabled", true).val();
                        $('#companyLocalSellingPrice').prop("readonly", true).val();
                        $('#companyLocalPurchasingPrice').prop("readonly", true).val();
                        $('#barcode').prop("readonly", true).val();
                        $('#partno').prop("readonly", true).val();
                        $('#maximunQty').prop("readonly", true).val();
                        $('#minimumQty').prop("readonly", true).val();
                        $('#reorderPoint').prop("readonly", true).val();
                        $('#revanueGLAutoID').prop("disabled", true).val();
                        $('#costGLAutoID').prop("disabled", true).val();
                        $('#assteGLAutoID').prop("disabled", true).val();

                        $('#COSTGLCODEdes').prop("disabled", true).val();
                        $('#ACCDEPGLCODEdes').prop("disabled", true).val();
                        $('#DEPGLCODEdes').prop("disabled", true).val();
                        $('#DISPOGLCODEdes').prop("disabled", true).val();

                        $('#stockadjust').prop("disabled", true).val();

                        //$('#checkbox_isActive').prop("disabled", true).val();
                        $('#checkbox_isSubitemExist').prop("disabled", true).val();
                        $('#isMfqItem').prop("disabled", true).val();
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

    function approve_itemmaster(itemAutoID) {

        if(ApprovalforItemMaster && itemAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'itemAutoID': itemAutoID},
                url: "<?php echo site_url('ItemMaster/approve_itemmaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0],data[1]);
                    if(data[2]=='2'){
                        $('#approve_btn').prop("disabled", true).val();
                     
                    }
                    if(data[0]=='s'){

                    }

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }

    }

    function set_short_description(val){
        var result_for_short = val.substring(0,15);

        if(longDesToShortPolicy==1){
            $('#itemName').val(result_for_short);
        }
        
    }

    function load_sub_sub_sub_cat() {
        $('#subSubSubCategoryID option').remove();
        $('#subSubSubCategoryID').val("");
        let subsubid = $('#subSubCategoryID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("ItemMaster/load_subsubcat"); ?>',
            dataType: 'json',
            data: {'subsubid': subsubid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#subSubSubCategoryID').empty();
                    let mySelect = $('#subSubSubCategoryID');
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
</script>