<?php
$primaryLanguage = getPrimaryLanguage();/*Language*/
$this->lang->load('assetmanagementnew', $primaryLanguage);/*Language*/
$this->lang->load('common', $primaryLanguage);/*Language*/

$current_date = format_date(date('Y-m-d'));
$currency_arr = all_currency_new_drop();//array('' => 'Select Currency');
$supplier_arr = all_supplier_drop();
$financeyear_arr = all_financeyear_drop();
$segment_arr = fetch_segment();
$fetch_all_gl_codes = fetch_all_gl_codes();
$fetch_cost_account = fetch_cost_account();
$fetch_dep_gl_code = fetch_gl_code(array('masterCategory' => 'PL', 'subCategory' => 'PLE'));
$fetch_disposal_gl_code = fetch_gl_code(array('masterCategory' => 'PL'));
$fetch_master_cat = fa_asset_category(3);
$fetch_all_location = fetch_all_location();
$fetch_all_custodian = fetch_all_custodian();
$supplier_arr = all_supplier_drop();
$currncy_arr = all_currency_new_drop();
$fetch_post_to_gl = fetch_post_to_gl();
$local_decimal_place = $this->common_data['company_data']['company_default_decimal'];

$editDatas = $this->db->query("SELECT CONCAT(segmentID, '|', segmentCode) segment ,srp_erp_fa_asset_master.* FROM `srp_erp_fa_asset_master` WHERE `faID` = '$faID'")->row_array();

if ($editDatas === null) {
    $fetch_sub_cat = [];
    $fetch_sub_sub_cat = [];
} else {
    $fetch_sub_cat = (isset($editDatas['faCatID']) && $editDatas['faCatID'] !== 'NULL' && $editDatas['faCatID'] !== '')
        ? fa_asset_category_sub($editDatas['faCatID'])
        : [];

    $fetch_sub_sub_cat = (isset($editDatas['faSubCatID']) && $editDatas['faSubCatID'] !== 'NULL' && $editDatas['faSubCatID'] !== '')
        ? fa_asset_category_sub($editDatas['faSubCatID'])
        : [];
}

$groupTO = isset($editDatas['faCatID']) ? group_to($editDatas['faCatID']) : [];
$companyid = current_companyID();

$costs = $this->db->query("SELECT companyLocalAmount,dateAQ,faCode,salvageAmount FROM srp_erp_fa_asset_master WHERE faID='{$faID}'")->result_array();
$documentcodedrilldown = [];
$documentdrilldownasset = $this->db->query("SELECT docOriginSystemCode,docOrigin FROM srp_erp_fa_asset_master WHERE companyID = '{$companyid}' AND faID='{$faID}' ")->row_array();
if($documentdrilldownasset){
    if($documentdrilldownasset['docOrigin'] == 'GRV')
    {
        $documentcodedrilldown = $this->db->query("SELECT grvPrimaryCode as systemcode FROM `srp_erp_grvmaster` where companyID = '{$companyid}' AND grvAutoID = '{$documentdrilldownasset['docOriginSystemCode']}'")->row_array();
    }else if($documentdrilldownasset['docOrigin'] == 'PV')
    {
        $documentcodedrilldown = $this->db->query("SELECT PVcode as systemcode FROM `srp_erp_paymentvouchermaster` where companyID = '{$companyid}' AND payVoucherAutoId = '{$documentdrilldownasset['docOriginSystemCode']}'")->row_array();
    }else if($documentdrilldownasset['docOrigin'] == 'BSI')
    {
        $documentcodedrilldown = $this->db->query("SELECT bookingInvCode as systemcode FROM `srp_erp_paysupplierinvoicemaster` where companyID = '{$companyid}' And InvoiceAutoID = '{$documentdrilldownasset['docOriginSystemCode']}' ")->row_array();
    }
}

$deps = $this->db->query("SELECT srp_erp_fa_depmaster.depCode,srp_erp_fa_depmaster.approvedYN, srp_erp_fa_assetdepreciationperiods.companyLocalAmount, srp_erp_fa_depmaster.depDate, srp_erp_fa_assetdepreciationperiods.faCode,srp_erp_fa_depmaster.depType,srp_erp_fa_depmaster.depMonthYear FROM srp_erp_fa_depmaster INNER JOIN srp_erp_fa_assetdepreciationperiods ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID WHERE srp_erp_fa_assetdepreciationperiods.faID = '{$faID}' AND srp_erp_fa_depmaster.approvedYN = 1")->result_array();

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">

        <div class="steps mb25">
            <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('assetmanagement_asset'); ?><!--Asset--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step2" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"><?php echo $this->lang->line('assetmanagement_asset_categorization'); ?><!--Asset Categorization--></span>
            </a>
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step3" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"> <?php echo $this->lang->line('assetmanagement_asset_valuation'); ?><!--Asset Valuation--></span>
            </a>        
            <a class="step-wiz step--incomplete step--inactive btn-wizard" href="#step4" data-toggle="tab">
                <span class="step__icon"></span>
                <span class="step__label"> <?php echo $this->lang->line('assetmanagement_asset_attachment'); ?><!--Asset Attachment--></span>
            </a>       
        </div>

</div>
<div class="tab-content">
    <div id="step1" class="tab-pane active" style="box-shadow: none;">
        <?php echo form_open('', 'role="form" id="add_new_asset_form"'); ?>
        <div class="row">
            <div class="col-sm-3">
                <div style="text-align: center;">
                    <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;margin: 0 auto;;">
                        <img class="img-responsive" style="height: 141px !important;"
                             src="<?php echo !empty($editDatas) && isset($editDatas['image']) && !empty($editDatas['image']) ? $this->s3->createPresignedRequest('uploads/assets/' . $editDatas['image'], '1 hour') : $this->s3->createPresignedRequest('images/item/no-image.png', '1 hour'); ?>"
                             id="asset_img" alt="...">
                        <input type="file" name="assetImage" id="assetImage" style="display: none;"
                               onchange="loadImage(this)"/>
                    </div>
                    <a href="#" style="margin-top: 5px;" type="button" onclick="$('#assetImage').click();">
                        <?php echo $this->lang->line('assetmanagement_select_a_image'); ?><!--Select a Image--> </a>
                </div>

                <div class="form-group col-sm-12 no-padding">
                    <label for=""><?php echo $this->lang->line('assetmanagement_fa_code'); ?><!--FA Code--></label>
                    <h4 class="" id="faCode"
                        style="margin-top: 2px;color: #48bbce;"><?php echo $editDatas['faCode'] ?? null ?></h4>
                </div>

                <div class="form-group col-sm-12 no-padding" style="margin-top:1px;">
                    <label for="">
                        <?php echo $this->lang->line('common_description'); ?><!--Description--> <?php required_mark(); ?></label>
                    <textarea tabindex="1" style="color: #48bbce;" class="form-control" rows="2" id="assetDescription"
                              name="assetDescription"><?php echo $editDatas['assetDescription'] ?? null ?></textarea>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for=""><?php echo $this->lang->line('assetmanagement_serial'); ?><!--Serial--> #</label>
                        <input tabindex="2" type="text" class="form-control " id="faUnitSerialNo"
                               value="<?php echo $editDatas['faUnitSerialNo'] ?? null ?>" name="faUnitSerialNo">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for=""><?php echo $this->lang->line('assetmanagement_bar_code'); ?><!--Barcode--></label>
                        <input tabindex="3" type="text" class="form-control "
                               value="<?php echo $editDatas['barcode'] ?? null ?>"
                               id="barcode" name="barcode"></div>

                    <div class="form-group col-sm-3">
                        <label for=""><?php echo $this->lang->line('assetmanagement_rfid_code'); ?></label>
                        <input tabindex="12" type="text" class="form-control" id="rfidCode" name="rfidCode"
                               value="<?php echo $editDatas['rfidCode'] ?? null ?>">
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="">
                            <?php echo $this->lang->line('assetmanagement_manufacturer'); ?><!--Manufacturer--> <?php required_mark(); ?></label>
                        <input tabindex="4" type="text" class="form-control " id="MANUFACTURE" name="MANUFACTURE"
                               value="<?php echo $editDatas['manufacture'] ?? null ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="">
                            <?php echo $this->lang->line('assetmanagement_date_acquired'); ?><!--Date Acquired--> <?php required_mark(); ?></label>

                        <div class=" input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input tabindex="5" type="text" name="dateAQ" readonly
                                   value="<?php echo format_date($editDatas['dateAQ'] ?? null); ?>"
                                   id="dateAQ"
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="">
                            <?php echo $this->lang->line('assetmanagement_asset_capitalized_date'); ?><!--Asset Capitalized Date--></label>

                        <div class=" input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="postDate" readonly
                                   value="<?php echo format_date($editDatas['postDate'] ?? null); ?>"
                                   id="postDate"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="">
                            <?php echo $this->lang->line('assetmanagement_depreciaton_date'); ?><!--Depreciating Date--> <?php required_mark(); ?></label>

                        <div class=" input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input tabindex="6" type="text" name="dateDEP" readonly
                                   value="<?php echo format_date($editDatas['dateDEP'] ?? null); ?>"
                                   id="dateDEP"
                                   class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group col-sm-3">
                        <label for="">
                            <?php echo $this->lang->line('assetmanagement_life_time_in_month'); ?><!--Life time in Month--> <?php required_mark(); ?></label>
                        <input tabindex="7" onchange="DepPerUpdate()" type="text" class="form-control number" id="depMonth"
                               name="depMonth" value="<?php echo $editDatas['depMonth'] ?? null ?>" onkeypress="return validateFloatKeyPress(this,event)">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for=""><?php echo $this->lang->line('assetmanagement_dep'); ?><!--DEP--> %</label>
                        <input tabindex="8" type="text" readonly class="form-control " id="DEPpercentage"
                               name="DEPpercentage"
                               value="<?php echo $editDatas['DEPpercentage'] ?? null ?>">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="customerCurrency">
                            <?php echo $this->lang->line('common_currency'); ?><!--Currency--> <?php required_mark(); ?></label>
                        <?php
                        $transactionCurrency = $editDatas['transactionCurrency'] ?? null;
                        $transactionCurrencyID = $editDatas['transactionCurrencyID'] ?? null;
                        if (empty($transactionCurrencyID)) {
                            $cur = $this->common_data['company_data']['company_default_currencyID'];
                        } else {
                            $cur = $transactionCurrencyID;
                        }

                        $docOrigin = $documentdrilldownasset['docOrigin'] ?? null;
                        if(!empty($docOrigin !='')) {
                            echo form_dropdown('transactionCurrency', $currncy_arr, $cur, 'class="form-control select2" tabindex="9" onchange = "" id = "transactionCurrency" disabled required');
                        }else{
                            echo form_dropdown('transactionCurrency', $currncy_arr, $cur, 'class="form-control select2" tabindex="9" onchange = "" id = "transactionCurrency" required');
                        }

                        ?>
                    </div>
                    <?php if (isset($documentdrilldownasset['docOrigin']) && !empty($documentdrilldownasset['docOrigin'])) { ?>
                    <div class="form-group col-sm-3">
                        <label for=""><?php echo $this->lang->line('assetmanagement_unit_price'); ?><!--Unit Price-->
                            <?php required_mark(); ?></label>
                        <input tabindex="10" type="text" class="form-control text-right number" id="COSTUNIT" name="COSTUNIT"
                               value="<?php echo $editDatas['transactionAmount'] ?? null ?>" onkeypress="return validateFloatKeyPress(this,event)" readonly>
                    </div>
                    <?php } else {?>
                        <div class="form-group col-sm-3">
                            <label for=""><?php echo $this->lang->line('assetmanagement_unit_price'); ?><!--Unit Price-->
                                <?php required_mark(); ?></label>
                            <input tabindex="10" type="text" class="form-control text-right number" id="COSTUNIT" name="COSTUNIT"
                                   value="<?php echo $editDatas['transactionAmount'] ?? null ?>" onkeyup="selvageCheck()" onkeypress="return validateFloatKeyPress(this,event)">
                        </div>
                    <?php }?>
                    <div class="form-group col-sm-3">
                        <label for="segment">
                            <?php echo $this->lang->line('assetmanagement_asset_location'); ?><!--Asset Location--> <?php echo required_mark() ?></label>
                        <?php echo form_dropdown('currentLocation', $fetch_all_location, array($editDatas['currentLocation'] ?? null), 'class="form-control" tabindex="12" id = "currentLocation"'); ?>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for=""><?php echo $this->lang->line('assetmanagement_acc_dep_amount'); ?><!--Acc Dep Amount--> </label>
                        <input tabindex="11" type="text" class="form-control text-right number" id="accDepAmount" name="accDepAmount"
                               value="<?php echo $editDatas['accDepAmount'] ?? null ?>" onkeyup="validateunitprice()" onkeypress="return validateFloatKeyPress(this,event)" disabled>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for=""><?php echo $this->lang->line('assetmanagement_acc_dep_date'); ?><!--Acc Dep Date--> </label>
                        <div class=" input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input tabindex="6" type="text" name="accDepDate" readonly
                                   value="<?php echo format_date($editDatas['accDepDate'] ?? null); ?>"
                                   id="accDepDate"
                                   class="form-control">
                        </div>
                    </div>
                        
                    
                    <?php if(!empty($documentdrilldownasset['docOrigin'])!=''){?>
                        <div class="form-group col-sm-3">
                        <label for="">Residual/Salvage</label>
                        <input tabindex="12" type="text" class="form-control text-right number" id="salvageValue" name="salvageValue"
                        value="<?php echo $editDatas['salvageAmount'] ?? null ?>" onkeyup="validateSalvage()" >
                        <small id="salvage_error" class="text-danger"></small>
                    </div>

                    <div class="form-group col-sm-3 no-padding">
                        <label for=""><?php echo $this->lang->line('assetmanagement_origin_document'); ?><!--Origin Document--></label>
                        <h4 class="" id="documentorigindrill" style="margin-top: 2px;color: #48bbce;font-size: 100%" onclick="documentPageView_modal('<?php echo $documentdrilldownasset['docOrigin']?>',<?php echo $documentdrilldownasset['docOriginSystemCode'] ?>)"><?php echo $documentcodedrilldown['systemcode'] ?></h4>
                    </div>
                    <?php }?>
                    <div class="form-group col-sm-3">
                        <label for="">Residual/Salvage</label>
                        <input tabindex="12" type="text" class="form-control text-right number" id="salvageValue" name="salvageValue"
                        value="<?php echo $editDatas['salvageAmount'] ?? null ?>" onkeyup="validateSalvage()" disabled>
                        <small id="salvage_error" class="text-danger"></small>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="isOperationalAsset" class="" style="padding-right:20px;"><?php echo 'Is Operational Asset' ?><!--Comments--></label>
                        <input type="checkbox" name="isOperationalAsset" id="isOperationalAsset" class="checkbox" value="1" <?php echo ($editDatas['isOperationalAsset'] ?? null == 1) ? 'checked' : '' ?> />
                    </div>
                    
                   
                </div>
                
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for=""><?php echo $this->lang->line('common_comments'); ?><!--Comments--></label>
                        <textarea tabindex="13" class="form-control" rows="2" id="comments"
                                  name="comments"><?php echo $editDatas['comments'] ?? null ?></textarea>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="custodian">
                            <?php echo $this->lang->line('assetmanagement_custodian_type'); ?><!--Custodian Type--></label>
                        <?php echo form_dropdown('custodianID', $fetch_all_custodian, array($editDatas['custodianID'] ?? null), 'class="form-control" tabindex="12" id = "custodianID"'); ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary" type="submit" id="assetNext">
                        <?php echo $this->lang->line('common_next'); ?><!--Next--></button>
                </div>
            </div>
        </div>
        </form>
        <!-- --><?php /*form_close() */ ?>
    </div>
    <!--step2-->
    <div id="step2" class="tab-pane" style="box-shadow: none;">
        <?php echo form_open('', 'role="form" id="add_new_asset_form_2"'); ?>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="segment">
                    <?php echo $this->lang->line('assetmanagement_asset_type'); ?><!--Asset Type--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('assetType', array('1' => $this->lang->line('assetmanagement_own_asset')/*'Own Asset'*/, '2' => $this->lang->line('assetmanagement_thirdparty')/*'Third Party'*/), array($editDatas['assetType'] ?? null), 'class="form-control" id="assetType" required onchange="enableSubplier(this)"'); ?>
            </div>

            <div class="form-group col-sm-4">
                <label for="segment"><?php echo $this->lang->line('common_supplier'); ?><!--Supplier--></label>
                <?php echo form_dropdown('supplier', $supplier_arr, array($editDatas['supplierID'] ?? null), 'class="form-control select2" id="supplier"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="segment">
                    <?php echo $this->lang->line('common_segment'); ?><!--Segment--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('segment', $segment_arr, array($editDatas['segment'] ?? null), 'class="form-control select2" id="segment" required'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-4">
                <label for="faCatID">
                    <?php echo $this->lang->line('assetmanagement_main_category'); ?><!--Main Category--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('faCatID', $fetch_master_cat, array($editDatas['faCatID'] ?? null), "class='form-control select2' id='faCatID' required onchange='getSubCategory(this);fetchGLCode(this)'"); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="faSubCatID">
                    <?php echo $this->lang->line('assetmanagement_sub_category'); ?><!--Sub Category--> <?php required_mark(); ?></label>
                <?php echo form_dropdown('faSubCatID', $fetch_sub_cat, array($editDatas['faSubCatID'] ?? null), 'class="form-control" id="faSubCatID" required onchange="getSubCategory(this)"'); ?>
            </div>
            <div class="form-group col-sm-4">
                <label for="faSubCatID2">
                    <?php echo $this->lang->line('assetmanagement_sub_sub_category'); ?><!--Sub Sub Category--></label>
                <?php echo form_dropdown('faSubCatID2', $fetch_sub_sub_cat, array($editDatas['faSubCatID2'] ?? null), 'class="form-control" id="faSubCatID2"'); ?>
            </div>
        </div>
        <div class="row" style="display: block" id="financeGrouping">
            <div class="col-md-12" style="margin-bottom: 10px;">

                <fieldset class="scheduler-border">
                    <legend class="scheduler-border">
                        <?php echo $this->lang->line('assetmanagement_finance_grouping'); ?><!--Finance Grouping--></legend>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">
                                <?php echo $this->lang->line('assetmanagement_cost_account'); ?><!--Cost Account--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('COSTGLCODEdes', $fetch_cost_account, array($editDatas['costGLAutoID'] ?? null), 'class="form-control select2" id = "COSTGLCODEdes" disabled'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">
                                <?php echo $this->lang->line('assetmanagement_acc_dep_gl_code'); ?><!--Acc Dep GL Code--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('ACCDEPGLCODEdes', $fetch_cost_account, array($editDatas['ACCDEPGLAutoID'] ?? null), 'class="form-control select2" id = "ACCDEPGLCODEdes" disabled'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">
                                <?php echo $this->lang->line('assetmanagement_dep_gl_code'); ?><!--Dep GL Code--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('DEPGLCODEdes', $fetch_dep_gl_code, array($editDatas['DEPGLAutoID'] ?? null), 'class="form-control select2" id = "DEPGLCODEdes" disabled'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">
                                <?php echo $this->lang->line('assetmanagement_disposal_gl'); ?><!--Disposal GL Code--> <?php required_mark(); ?></label>
                            <?php echo form_dropdown('DISPOGLCODEdes', $fetch_disposal_gl_code, array($editDatas['DISPOGLAutoID'] ?? null), 'class="form-control select2" id = "DISPOGLCODEdes" disabled'); ?>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-5" id="postGLAutoID_divType">
                <label for=""><?php echo $this->lang->line('assetmanagement_post_to_gl'); ?><!--Post to GL--> </label>

                <div class="input-group">
                        <span class="input-group-addon">
                          <input type="checkbox" onclick="posttoGlCheck(this)" id="isPostToGL"
                                 name="isPostToGL" <?php echo $editDatas['isFromGRV'] ?? '' == 1 ? 'disabled' : '' ?>>
                        </span>
                    <?php
                    echo form_dropdown('postGLAutoID', $fetch_post_to_gl, array($editDatas['postGLAutoID'] ?? null), 'class="form-control select2" disabled id = "postGLAutoID"'); ?>
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for=""><?php echo $this->lang->line('assetmanagement_group_to'); ?><!--Group To--></label>

                <div class="input-group">
                        <span class="input-group-addon">
                          <input type="checkbox" onclick="GroupToCheck(this)" id="groupTOCheck" name="groupTOCheck">
                        </span>
                    <?php echo form_dropdown('groupTO', $groupTO, array($editDatas['groupTO'] ?? null), 'class="form-control" disabled id="groupTO"'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            
            <div class="form-group col-sm-3">
                <label for="">Replacement Assets</label>

                <div class="input-group">
                        
                    <?php echo form_dropdown('replacementID', $groupTO, array($editDatas['replacementAssetsID'] ?? null), 'class="form-control" id="replacementID"'); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="text-right m-t-xs">
                    <input type="hidden" value="<?php echo $editDatas['isFromGRV'] ?? null ?>" name="isFromGRV">
                    <button class="btn btn-primary" type="submit" id="assetSave" data-type="save">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    &nbsp;
                    <a href="#" class="btn btn-success pull-right" onclick="assetConfirm(this)"
                       data-type="confirm"
                       style="display: none;" id="assetConfirm">
                        <?php echo $this->lang->line('common_confirm'); ?><!--Confirm-->
                    </a>
                </div>
            </div>
        </div>
        </form>
    </div>
    <!--step3-->
    <div id="step3" class="tab-pane" style="box-shadow: none;">
        <div class="row">
            <div class="col-md-12">
                <span class="no-print pull-right">
                <a class="btn btn-default btn-sm" id="de_link" target="_blank"
                   href="<?php echo site_url("/Double_entry/fetch_double_entry_asset_master/{$faID}/FA") ?>"><span
                        class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;
                    <?php echo $this->lang->line('assetmanagement_account_review_entries'); ?><!--&nbsp;Account Review entries-->
                </a>
                </span>
            </div>
        </div>
        <div class="row">
            <?php
            $costs;
            $deps;
            if ($costs >= $deps) {
                $rowCount = count($costs);
            } elseif ($costs < $deps) {
                $rowCount = count($deps);
            }
            ?>
            <div class="col-md-4" style="padding-right: 0;">
                <table class="table table-bordered table-striped table-condesed" style="">
                    <thead>
                    <tr>
                        <th colspan="3"><?php echo $this->lang->line('common_cost'); ?><!--Cost-->
                            (<?php echo $this->common_data['company_data']['company_default_currency']; ?>)
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                        <th><?php echo $this->lang->line('assetmanagement_doc_ref'); ?><!--Doc Ref--></th>
                        <th class="text-right"><?php echo $this->lang->line('common_value'); ?><!--Value--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($costs as $cost) { ?>
                        <tr>
                            <td><?php echo format_date($cost['dateAQ']) ?></td>
                            <td><?php echo $cost['faCode'] ?></td>
                            <td class="text-right"><?php echo number_format($cost['companyLocalAmount'], $local_decimal_place) ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                    </tbody>
                    <tfoot>

                    <tr>
                        <th colspan="2"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                        <th class="text-right total"><?php echo isset($cost['companyLocalAmount']) ? number_format($cost['companyLocalAmount'], $local_decimal_place) : '' ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-4" style="padding-left: 0;">
                <table class="table table-bordered table-striped table-condesed" style="">
                    <thead>
                    <tr>
                        <th colspan="3"><?php echo $this->lang->line('common_depreciation'); ?><!--Depreciation-->
                            (<?php echo $this->common_data['company_data']['company_default_currency']; ?>)
                        </th>
                    </tr>
                    <tr>
                        <th><?php echo $this->lang->line('common_date'); ?><!--Date--></th>
                        <th><?php echo $this->lang->line('assetmanagement_doc_ref'); ?><!--Doc Ref--></th>
                        <th><?php echo $this->lang->line('common_value'); ?><!--Value--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $depTotal = 0;
                    foreach ($deps as $dep) {
                        $depTotal += $dep['companyLocalAmount'];
                        ?>
                        <tr>
                            <td><?php
                                if ($dep['depType'] == 0) {
                                    echo format_date($dep['depDate']);
                                } else {
                                    echo $dep['depMonthYear'];
                                }
                                ?>
                            </td>
                            <td><?php echo $dep['depCode'] ?></td>
                            <td class="text-right"><?php echo number_format($dep['companyLocalAmount'], $local_decimal_place) ?></td>
                        </tr>
                    <?php }
                    /**/
                    if (!$deps) {
                        ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php
                    } ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="2"><?php echo $this->lang->line('common_total'); ?><!--Total--></th>
                        <th class="text-right total"><?php echo number_format($depTotal, $local_decimal_place) ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-4">

            </div>
        </div>


        <div class="row">
            <div class="col-md-2">
                  
                    <h5 style="font-weight: bold;text-align: right">
                    Residual/Salvage Value 
                    (<?php echo $this->common_data['company_data']['company_default_currency']; ?>): 
                    <?php 
                        $salvalue = isset($cost['salvageAmount']) ? (float)$cost['salvageAmount'] : 0;
                        echo number_format($salvalue,$local_decimal_place)
                    ?>
                    </h5>
            </div>
            <div class="col-md-6"> 
                <h4 style="font-weight: bold;text-align: right">
                    <?php echo $this->lang->line('assetmanagement_net_book_value'); ?><!--Net Book Value-->
                    (<?php echo $this->common_data['company_data']['company_default_currency']; ?>) : <?php
                    $aqCost = isset($cost['companyLocalAmount']) ? (float)$cost['companyLocalAmount'] : 0;
                    $salAmount = isset($cost['salvageAmount']) ? (float)$cost['salvageAmount'] : 0;
                    //$totalCost = $aqCost-$salAmount;
                    echo number_format(($aqCost- $depTotal), $local_decimal_place);
                    ?></h4>
            </div>
            <div class="col-md-4"></div>
        </div>

    </div>
    <!--step4-->
    <div id="step4" class="tab-pane" style="box-shadow: none;">
        <div class="row">
            <button class="btn btn-primary btn-flat pull-right" onclick="addAttachment()">
                <?php echo $this->lang->line('assetmanagement_add_attachment'); ?><!--Add Attachment--></button>
        </div>
        <hr>
        <div class="row">
            <table class="table table-bordered table-striped table-condesed" id="attachmentTable">
                <thead>
                <tr>
                    <th style="width: 27px;">#</th>
                    <th style="width: 80px;">
                        <?php echo $this->lang->line('assetmanagement_date_of_issue'); ?><!--Date of Issue--></th>
                    <th style="width: 84px;">
                        <?php echo $this->lang->line('assetmanagement_date_of_expiry'); ?><!--Date of Expiry--></th>
                    <!--<th>
                        <?php /*echo $this->lang->line('assetmanagement_document_description'); */?><!--Document Description--></th>
                    <th style="width: 100px;">
                        <?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></th>
                    <th style="width: 25px;"></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!--Modal-->
<div aria-hidden="true" role="dialog" tabindex="-1" id="attachment_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('assetmanagement_asset_attachment'); ?><!--Asset Attachment--></h4>
            </div>
            <form role="form" id="attachment_modal_form" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group" style="">
                        <label for="enterfueltype" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_description'); ?><!--Description--> <span
                                title="required field"
                                style="color:red; font-weight: 600; font-size: 12px;">*</span></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="document_description"
                                   name="document_description">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('assetmanagement_date_of_issue'); ?><!--Date of issue--> </label>

                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control" id="dateissued" name="dateissued">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('assetmanagement_date_of_expiry'); ?><!--Date of expiry--></label>

                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control" id="dateexpired" name="dateexpired">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_attachment'); ?><!--Attachment--> <span
                                title="required field"
                                style="color:red; font-weight: 600; font-size: 12px;">*</span></label>

                        <div class="col-sm-8">
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                <div class="form-control" data-trigger="fileinput"><i
                                        class="glyphicon glyphicon-file fileinput-exists"></i> <span
                                        class="fileinput-filename"></span></div>
                                <span class="input-group-addon btn btn-default btn-file"><span
                                        class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                    aria-hidden="true"></span></span><span
                                        class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                       aria-hidden="true"></span></span><input
                                        type="file" name="document_file" id="document_file"></span>
                                <a href="#" class="input-group-addon btn btn-default fileinput-exists"
                                   data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                  aria-hidden="true"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="progress" style="display: none;" id="uploadProgressDiv">
                        <div class="progress-bar" role="progressbar" id="uploadProgress" aria-valuenow="0"
                             aria-valuemin="0" aria-valuemax="100" style="width:0%">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="submit">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" tabindex="-1" id="attachment_edit_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo $this->lang->line('assetmanagement_asset_attachment_edit'); ?><!--Asset Attachment Edit--></h4>
            </div>
            <form role="form" id="attachment_edit_modal_form" class="form-horizontal">
                <input type="hidden" class="form-control" id="attachmentIDhn"
                       name="attachmentIDhn">

                <div class="modal-body">
                    <div class="form-group" style="">
                        <label for="enterfueltype" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_description'); ?><!--Description--> <span
                                title="required field"
                                style="color:red; font-weight: 600; font-size: 12px;">*</span></label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="document_descriptionedit"
                                   name="document_descriptionedit">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('assetmanagement_date_of_issue'); ?><!--Date of issue--> </label>

                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control" id="dateissuededit" name="dateissuededit">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('assetmanagement_date_of_expiry'); ?><!--Date of expiry--></label>

                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" class="form-control" id="dateexpirededit" name="dateexpirededit">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-sm btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-sm btn-primary" type="button" onclick="updateAttachment()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--//Modal-->


<script type="text/javascript">
    var faID = null;
    var confirmedYN = '<?php echo $editDatas['confirmedYN'] ?? '' ?>';
    var approvedYN = '<?php echo $editDatas['approvedYN'] ?? ''  ?>';
    var currency_decimal;
    $(document).ready(function () {

        $('.select2').select2();
        $('#dateAQ').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $('#add_new_asset_form').bootstrapValidator('revalidateField', 'dateAQ');
            var postDate = $('#postDate').val();
            if (!postDate) {
                $('#postDate').val($(this).val())
            }
            $(this).datepicker('hide');
        });

        $('#dateDEP').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $('#add_new_asset_form').bootstrapValidator('revalidateField', 'dateDEP');
            $(this).datepicker('hide');
        });

        $('#accDepDate').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $('#add_new_asset_form').bootstrapValidator('revalidateField', 'accDepDate');
            $(this).datepicker('hide');
        });

        $('#dateexpired,#dateissued,#postDate,#dateexpirededit,#dateissuededit').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $(this).datepicker('hide');
        });

        currency_decimal = 2;
        number_validation();


        faID = '<?php echo $faID; ?>';
        if (faID) {
            $('.btn-wizard').removeClass('disabled');

            $('#assetSave').text('Update');
            if (confirmedYN == 1) {
                $('#assetConfirm').addClass('disabled').show();
                $('#add_new_asset_form input,#add_new_asset_form select,#add_new_asset_form textarea,#add_new_asset_form button').not('#assetImage').attr('disabled', true);
                $('#add_new_asset_form_2 input,#add_new_asset_form_2 select,#add_new_asset_form_2 textarea,#add_new_asset_form_2 button').attr('disabled', true);
            } else {
                $('#assetConfirm').show();
            }

            var assetType = '<?php echo $editDatas['assetType'] ?? ''  ?>';
            if (assetType == 2) {
                $('#financeGrouping').hide();
                $('#postGLAutoID_divType').addClass('hide');
            }

            var isPostToGL = '<?php echo $editDatas['isPostToGL'] ?? ''  ?>'
            var isFromGRV = '<?php echo $editDatas['isFromGRV'] ?? ''  ?>'
            if (isPostToGL) {
                $('#isPostToGL').attr('checked', true);
                if (isFromGRV != '1') {
                    $('#postGLAutoID').attr('disabled', false);
                } else {
                    $('#isPostToGL').attr('disabled', true);
                }
            }

            var groupTO = '<?php echo $editDatas['groupTO'] ?? ''  ?>'
            if (groupTO) {
                $('#groupTOCheck').attr('checked', true);
                $('#groupTO').attr('disabled', false);
            }

            $('#assetImage').on('change', function () {
                /*Image upload*/

                var imgageVal = new FormData();
                imgageVal.append('faID', faID);

                var files = $("#assetImage")[0].files[0];
                imgageVal.append('files', files);

                if (files == undefined) {
                    return false;
                }
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    data: imgageVal,
                    contentType: false,
                    cache: false,
                    processData: false,
                    url: "<?php echo site_url('AssetManagement/asset_image_upload'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                        /*An Error Occurred! Please Try Again*/
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            })

        } else {
            $('.btn-wizard').addClass('disabled');
        }

        $('#assetConfirm').click(function (e) {

        });

        $('#add_new_asset_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                assetDescription: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}, /*Description is required*/
                MANUFACTURE: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_manufacturer_is_required');?>.'}}}, /*Manufacturer is required*/
                dateAQ: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_date_acquired_is_required');?>.'}}}, /*Date Acquired is required*/
                dateDEP: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_depreciation_date_is_required');?>.'}}}, /*Depreciation Date Start is required*/
                depMonth: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_life_time_in_month_is_required');?>.'}}}, /*Life time in Month is required*/
                COSTUNIT: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_unit_price_local_is_required');?>.'}}}, /*Unit Price (Local) required*/
                costUnitRpt: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_unit_price_rpt_required');?>.'}}}, /*Unit Price (Rpt) required*/
                currentLocation: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_asset_location_is_required');?>.'}}}, /*Asset Location is required*/
                salvageValue: {validators: {notEmpty: {message: 'Salvage value should be minimum of Zero'}}},
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $('[href=#step2]').tab('show');
            $('#assetNext').attr('disabled', false);
            $("#transactionCurrency").prop("disabled", false);
        }).on('error.form.bv', function (e) {

        });

        $('#add_new_asset_form_2').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                segment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}}, /*Segment is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            $('#COSTGLCODEdes').prop("disabled", false);
            $('#ACCDEPGLCODEdes').prop("disabled", false);
            $('#DEPGLCODEdes').prop("disabled", false);
            $('#DISPOGLCODEdes').prop("disabled", false);
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            $("#transactionCurrency").prop("disabled", false);
            var data = $form.serializeArray();
            var form1 = $('#add_new_asset_form').serializeArray();

            $.merge(data, form1);

            data.push({'name': 'faID', 'value': faID});
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                data: data,
                cache: false,
                url: "<?php echo site_url('AssetManagement/save_asset'); ?>",
                beforeSend: function () {
                    $('#COSTGLCODEdes').prop("disabled", true);
                    $('#ACCDEPGLCODEdes').prop("disabled", true);
                    $('#DEPGLCODEdes').prop("disabled", true);
                    $('#DISPOGLCODEdes').prop("disabled", true);
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data['status'] == 'success') {
                        feedAssetMaster();
                        faID = data['last_id'];
                        $('#assetSave').text('Update').attr('disabled', false);
                        $('#assetConfirm').show();
                        $("#transactionCurrency").prop("disabled", true);
                        $('.btn-wizard').removeClass('disabled');
                        $('#faCode').html(data.faCode);
                        $('[href=#step2]').tab('show');

                        /*Image upload*/

                        var imgageVal = new FormData();
                        imgageVal.append('faID', faID);

                        var files = $("#assetImage")[0].files[0];
                        imgageVal.append('files', files);

                        if (files == undefined) {
                            return false;
                        }
                        $.ajax({
                            type: 'POST',
                            dataType: 'JSON',
                            data: imgageVal,
                            contentType: false,
                            cache: false,
                            processData: false,
                            url: "<?php echo site_url('AssetManagement/asset_image_upload'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
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
                    else {
                        refreshNotifications(true);
                        $('#assetSave').attr('disabled', false);
                    }
                    <?php if (isset($documentdrilldownasset['docOrigin']) && !empty($documentdrilldownasset['docOrigin'])) { ?>
                    $("#transactionCurrency").prop("disabled", true);
                    <?php } ?>
                    stopLoad();
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }).on('error.form.bv', function (e) {

        });

        /*Attachment Modal*/
        $('#attachment_modal_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                document_description: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_description_is_required');?>.'}}}, /*Description is required*/
//                dateissued: {validators: {notEmpty: {message: 'Date of Issue is required.'}}},
//                dateexpired: {validators: {notEmpty: {message: 'Date of Expiry is required.'}}},
                document_file: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_file_is_required');?>.'}}}, /*File is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = new FormData($('#attachment_modal_form')[0]);

            data.append('documentSystemCode', faID);
            data.append('documentID', 'AST');
            $.ajax({
                type: 'post',
                data: data,
                mimeType: "multipart/form-data",
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                url: "<?php echo site_url('AssetManagement/save_attachment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    var percentVal = percentComplete + '%';
                    bar.width(percentVal);
                    $('#uploadProgressDiv').show();
                    $('#uploadProgress').css('width', percentVal)
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('#attachment_modal').modal('hide');
                        documentAttachment();
                        $('#attachment_modal_form').bootstrapValidator('resetForm', true);
                        $('#uploadProgressDiv').hide();
                        $('#uploadProgress').css('width', '0%')
                    }

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }).on('error.form.bv', function (e) {

        });

        /*Wizad*/
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
        /*Wizad*/

        $('#asset_img').click(function () {
            $('#assetImage').click();
        });

    });


    function assetConfirm(item) {
        $("#transactionCurrency").prop("disabled", false);
        $("#COSTGLCODEdes").prop("disabled", false);
        $("#ACCDEPGLCODEdes").prop("disabled", false);
        $("#DEPGLCODEdes").prop("disabled", false);
        $("#DISPOGLCODEdes").prop("disabled", false);
        var data = $('#add_new_asset_form_2').serializeArray();
        var form1 = $('#add_new_asset_form').serializeArray();

        $.merge(data, form1);

        data.push({'name': 'faID', 'value': faID});
        var dateAQ = $('#dateAQ').val();
        var postDate = $('#postDate').val();
        bootbox.confirm("This Asset will be posted to the document date " + postDate + ". Are you sure want to confirm?", function (confirmed) {
            if (confirmed) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('AssetManagement/assetConfirm'); ?>",
                    data: data,
                    dataType: "json",
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if (data.status == true) {
                            $(item).addClass('disabled');
                            $("#transactionCurrency").prop("disabled", true);
                            $('#assetConfirm').addClass('disabled').show();
                            $('#add_new_asset_form input,#add_new_asset_form select,#add_new_asset_form textarea,#add_new_asset_form button').attr('disabled', true);
                            $('#add_new_asset_form_2 input,#add_new_asset_form_2 select,#add_new_asset_form_2 textarea,#add_new_asset_form_2 button').attr('disabled', true);
                            $("#COSTGLCODEdes").prop("disabled", true);
                            $("#ACCDEPGLCODEdes").prop("disabled", true);
                             $("#DEPGLCODEdes").prop("disabled", true);
                            $("#DISPOGLCODEdes").prop("disabled", true);
                        }else { 
                            $("#COSTGLCODEdes").prop("disabled", true);
                            $("#ACCDEPGLCODEdes").prop("disabled", true);
                             $("#DEPGLCODEdes").prop("disabled", true);
                            $("#DISPOGLCODEdes").prop("disabled", true);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                    }
                });

            }else{
                $("#transactionCurrency").prop("disabled", true);
            }
        });

    }

    function getSubCategory(item) {
        var masterCategory = item.value;
        var thisName = item.name;
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('AssetManagement/getSubCategory'); ?>",
            data: {masterCategory: masterCategory,status:false},
            dataType: "html",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                if (thisName == 'faCatID') {
                    $('#faSubCatID').html(data);
                } else if (thisName == 'faSubCatID') {
                    $('#faSubCatID2').html(data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }

    function DepPerUpdate() {
        var month = $('#depMonth').val();
        if (month < 1){
            $('#DEPpercentage').val(0);
        } else {
            var DEPpercentage = 100 / ($('#depMonth').val() / 12);
            var per = parseFloat(DEPpercentage).formatMoney(2, '.', ',');
            $('#DEPpercentage').val(per);
        }
    }

    function posttoGlCheck(item) {
        if (item.checked) {
            $('#postGLAutoID').attr('disabled', false);
        } else {
            $('#postGLAutoID').attr('disabled', true);
        }
    }

    function GroupToCheck(item) {
        if (item.checked) {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('AssetManagement/groupToAsset'); ?>",
                data: {faCatID: $('#faCatID').val()},
                dataType: "html",
                cache: false,
                beforeSend: function () {
                },
                success: function (data) {
                    $('#groupTO').html(data);
                    $('#groupTO').attr('disabled', false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        } else {
            $('#groupTO').attr('disabled', true);
        }
    }

    function fetchGLCode(item) {
        var masterCategory = $('#faCatID').val();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('AssetManagement/fetchGLCode'); ?>",
            data: {faCatID: $('#faCatID').val()},
            dataType: "json",
            cache: false,
            beforeSend: function () {
                $('#COSTGLCODEdes').prop("disabled", false);
                $('#ACCDEPGLCODEdes').prop("disabled", false);
                $('#DEPGLCODEdes').prop("disabled", false);
                $('#DISPOGLCODEdes').prop("disabled", false);
            },
            success: function (data) {
                $('#COSTGLCODEdes').val(data[0].faCostGLAutoID).change();
                $('#ACCDEPGLCODEdes').val(data[0].faACCDEPGLAutoID).change();
                $('#DEPGLCODEdes').val(data[0].faDEPGLAutoID).change();
                $('#DISPOGLCODEdes').val(data[0].faDISPOGLAutoID).change();
                $('#COSTGLCODEdes').prop("disabled", true);
                $('#ACCDEPGLCODEdes').prop("disabled", true);
                $('#DEPGLCODEdes').prop("disabled", true);
                $('#DISPOGLCODEdes').prop("disabled", true);
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }


    function enableSubplier(item) {
        if (item.value == '2') {
            /*$('#supplier').attr('disabled', false);*/
            $('#financeGrouping').hide();
        } else {
            $('#financeGrouping').show();
        }
    }

    documentAttachment();
    function documentAttachment() {
        var Otable = $('#attachmentTable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('AssetManagement/load_attachment'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }
            },
            "aoColumns": [
                {"mData": "attachmentID"},
                {"mData": "dateofIssued"},
                {"mData": "docExpiryDate"},
                //{"mData": "attachmentDescription"},
                {"mData": "myFileName"},
                {"mData": "delete"}
            ],
            "columnDefs": [{
                "targets": [0, 1, 2, 3],
                "orderable": false
            }],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push(
                    {name: "documentID", value: 'AST'},
                    {name: "documentSystemCode", value: faID}
                );
                $.ajax
                ({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });
    }

    function addAttachment(id) {
        $("#attachment_modal").modal({backdrop: "static"});
    }

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#asset_img').attr('src', e.target.result);
            };

            reader.readAsDataURL(obj.files[0]);
        }
    }

    function delete_attachment(index) {
        if (approvedYN == 1) {
            notification('Asset was Approved. You Can not delete Attachment');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('AssetManagement/delete_attachment'); ?>",
            data: {index: index, faID: faID},
            dataType: "json",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                refreshNotifications(true);
                documentAttachment();
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }

    function edit_attachment(id) {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('AssetManagement/edit_attachment'); ?>",
            data: {attachmentID: id},
            dataType: "json",
            cache: false,
            beforeSend: function () {
            },
            success: function (data) {
                $('#document_descriptionedit').val(data['attachmentDescription']);
                $('#dateexpirededit').val(data['docExpiryDate']);
                $('#dateissuededit').val(data['dateofIssued']);
                $('#attachmentIDhn').val(id);
                $("#attachment_edit_modal").modal({backdrop: "static"});
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }

    function updateAttachment() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('AssetManagement/updateAttachment') ?>",
            data: $("#attachment_edit_modal_form").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $("#attachment_edit_modal").modal('hide');
                    documentAttachment();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);

            }
        });
        return false;
    }

    $('#assetType').on('change', function () {
        if (this.value == 2) {
            $('#postGLAutoID_divType').addClass('hide')
        } else {
            $('#postGLAutoID_divType').removeClass('hide');
        }
    })

    function validateFloatKeyPress(el, evt) {
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

    function validateunitprice(){
        var unit = $('#COSTUNIT').val();
        var accDepAmount = $('#accDepAmount').val();
        var salvage = $('#salvageValue').val();
        var depSum = accDepAmount + salvage;
        var currency = $('#transactionCurrency option:selected').text();

        if(parseFloat(accDepAmount)>parseFloat(unit)){
            myAlert('w','Unit Price should be greater than Acc Dep Amount');
            $('#accDepAmount').val('');
        }
        if(depSum > unit) {
            $('#salvage_error').html('Salvage amount only can between'+' '+currency.slice(0,3)+'.'+((unit-accDepAmount) <= 0 ? unit: (unit-accDepAmount)) +' or '+ currency.slice(0,3)+ '.0' );
            $('#salvageValue').parent('div').addClass('has-error');
            $('#salvageValue').val('');
        } else {
            $('#salvageValue').parent('div').removeClass('has-error');
            $('#salvage_error').html('');
            $('#salvageValue').val('0');
        }
    }

    function selvageCheck() {
        var unit = $('#COSTUNIT').val();

        if(unit == '') {
            $('#salvageValue').prop("disabled", true);
        } else {
            $('#salvageValue').prop("disabled", false);
            $('#accDepAmount').prop("disabled", false);
        }
    }

    function validateSalvage(){
        var unit = $('#COSTUNIT').val();
        var salvage = $('#salvageValue').val();
        var accDep = $('#accDepAmount').val();
        var currency = $('#transactionCurrency option:selected').text();
        var depSum = '';  

        if(parseFloat(salvage) > parseFloat(unit)) {
            $('#salvage_error').html('Salvage amount cannot exceed Unit price'+' '+currency.slice(0,3)+'.'+unit);
            $('#salvageValue').parent('div').addClass('has-error');
            $('#salvageValue').val('');
  
        } else if(salvage == '') {
            $('#salvageValue').parent('div').addClass('has-error'); 
        } else {
            $('#salvageValue').parent('div').removeClass('has-error');
            $('#salvageValue').parent('div').addClass('has-success');
            $('#salvage_error').html('');
        }

        // if(salvage > 0 && accDep == '') {
        //     $('#accDepAmount').prop("disabled", true);                                            
        // } else {
        //     $('#accDepAmount').prop("disabled", false); 
        // }
       
        if(accDep != '') {
            depSum = unit - accDep;
            if(salvage > depSum){
                $('#salvage_error').html('Salvage amount cannot exceed Netbook value'+' '+currency.slice(0,3)+'.'+depSum);
                $('#salvageValue').parent('div').addClass('has-error');
                $('#salvageValue').val('');
            } else {
                $('#salvageValue').parent('div').removeClass('has-error');
                $('#salvageValue').parent('div').addClass('has-success');
                $('#salvage_error').html(''); 
            }
        }
    }

</script>   