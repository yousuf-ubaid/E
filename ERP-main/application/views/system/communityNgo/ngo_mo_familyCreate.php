<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], FALSE);
$this->load->helper('community_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$getAll_title = load_titles();
$gl_code_arr = fetch_all_gl_codes();
$fam_econSt = fetch_fam_econStatus();
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/commtNgo_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/ngo_web_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>

    .title {
        float: left;
        width: 170px;
        text-align: right;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }

    .ui-datepicker {
        z-index: 99999;
    !important;
    }
</style>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">
        <?php echo $this->lang->line('CommunityNgo_step_one'); ?><!--Step 1 -->-
        <?php echo $this->lang->line('CommunityNgo_ngo_family_header'); ?><!--NGO Family Header--></a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="load_ngoHouseHeader();" data-toggle="tab">
        <?php echo $this->lang->line('CommunityNgo_step_two'); ?><!--Step 2--> -
        <?php echo $this->lang->line('CommunityNgo_family_house_details'); ?><!--Family House Details--></a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="load_confirmation();" data-toggle="tab">
        <?php echo $this->lang->line('CommunityNgo_step_three'); ?><!--Step 3--> -
        <?php echo $this->lang->line('CommunityNgo_family_Master_cofirmation'); ?><!--Family Master Confirmation--></a>
</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="dispatchNote_header_form"'); ?>

        <input type="text" name="FamMasterID" id="FamMasterID" value="" style="display:none ;">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>
                        <?php echo $this->lang->line('CommunityNgo_family_header'); ?><!--NGO Family Master--></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('CommunityNgo_famAddedDate'); ?><!--Family Added Date--></label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input onchange="$('#FamilyAddedDate').val(this.value);" type="text" name="FamilyAddedDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="FamilyAddedDate" class="form-control" required>
                </div>
                <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('CommunityNgo_ref_no'); ?><!--Reference No--><label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                          <input type="text" name="LedgerNo" value="" id="LedgerNo" onchange="chkDuplicate();" class="form-control">
                        <span class="input-req-inner"></span></span>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">

                    <div class="form-group col-sm-2">
                        <label class="title"><?php echo $this->lang->line('CommunityNgo_leader'); ?><!--Leader--></label>
                    </div>
                    <div class="form-group col-sm-4">

    <span class="input-req" title="Required Field">
    <select onchange="get_comMaserDel();" id="LeaderID" class="form-control select2"
            name="LeaderID">
        <option data-currency=""
                value=""><?php echo $this->lang->line('CommunityNgo_select_family'); ?><!--Select leader--></option>
        <?php

        $com_master = fetch_comMaster_lead();
        if (!empty($com_master)) {
            foreach ($com_master as $val) {
                $query = $this->db->query("SELECT LeaderID FROM srp_erp_ngo_com_familymaster WHERE companyID='" . $val['companyID'] . "'");
                $datFam= $query->row();
           //  if($datFam->LeaderID != $val['Com_MasterID']){
                ?>
                <option value="<?php echo $val['Com_MasterID'] ?>"><?php echo $val['CName_with_initials'] ?></option>
                <?php
             // }
            }
        }
        ?>
    </select>

    <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('CommunityNgo_familyName'); ?><!--Family Name--></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                          <input type="text" name="FamilyName" value="" id="FamilyName" class="form-control">
                        <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_gender'); ?><!--Gender--><label>
                    </div>
                    <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
    <select onchange="" id="FamHgender" class="form-control select2"
            name="FamHgender" disabled>
        <option><?php echo $this->lang->line('CommunityNgo_fam_SelGender'); ?><!--Select Gender--></option>
            <option></option>

    </select>

    <span class="input-req-inner"></span></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_contactAddress'); ?><!--Contact Address--><label>
                    </div>
                    <div class="form-group col-sm-4">
                        <select onchange="" id="FamHaddress" class="form-control select2"
                                name="FamHaddress" disabled>
                            <option data-currency=""
                                    value=""><?php echo $this->lang->line('CommunityNgo_fam_SelAddress'); ?><!--Select Address--></option>
                            <option></option>
                        </select>

                        <span class="input-req-inner"></span>

                    </div>

                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_region'); ?><!--Area--><label>
                    </div>
                    <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
    <select onchange="" id="FamArea" class="form-control select2"
            name="FamArea" disabled>
          <option value="">Select Area</option>
            <option></option>

    </select>

    <span class="input-req-inner"></span></span>
                    </div>

                    <div class="form-group col-sm-2">

                        <label class="title">
                            <?php echo $this->lang->line('communityngo_houseNo'); ?><!--House No--><label>
                    </div>
                    <div class="form-group col-sm-4">
                                  <span class="input-req" title="Required Field">
    <select onchange="" id="FamHouseNo" class="form-control select2"
            name="FamHouseNo" disabled>
          <option value="">Select House No</option>
            <option></option>

    </select>

    <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('CommunityNgo_fam_ancestryState'); ?><!--Ancestory Status--><label>
                    </div>
                    <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
    <select onchange="get_AncesCategory();" id="FamAncestory" class="form-control select2"
            name="FamAncestory">
        <option><?php echo $this->lang->line('CommunityNgo_fam_SelAncestryState'); ?><!--Select Ancestory Status--></option>

                <option value='0'>Local</option>
                <option value='1'>Outside</option>

    </select>
    <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-2" id="AncesDesDiv" style="display:none;">
                        <label class="title">
                            <?php echo $this->lang->line('CommunityNgo_fam_ancestry'); ?><!--Ancestory--><label>
                    </div>
                    <div class="form-group col-sm-4" id="AncesSelDiv" style="display:none;">
                     <span class="input-req" title="Required Field">
                            <select onchange="" id="AncestryCatID" class="form-control select2"
                                    name="AncestryCatID">
                                <option data-currency=""
                                        value=""><?php echo $this->lang->line('CommunityNgo_fam_SelAncestry'); ?><!--Select Ancestry--></option>
                                <?php

                                $fam_ances = fetch_family_ancestry();
                                if (!empty($fam_ances)) {
                                    foreach ($fam_ances as $val) {
                                        ?>
                                        <option value="<?php echo $val['AncestryCatID'] ?>"><?php echo $val['AncestryDes'] ?></option>
                                        <?php

                                    }
                                }
                                ?>
                            </select>
                       <span class="input-req-inner"></span></span>
                    </div>
                </div>
          </div>
       </div>
        <br>
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>
                        <?php echo $this->lang->line('CommunityNgo_fam_ecoStatus'); ?><!--Family Economic Status--></h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('CommunityNgo_fam_econState'); ?><!--Economic Status --><label>
                    </div>
                    <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
                                <select id="ComEconSteID" class="form-control select2"
                                        name="ComEconSteID">
                                <option value=""><?php echo $this->lang->line('CommunityNgo_fam_selEconState'); ?><!--Select Economic Status--></option>
                                    <?php

                                    if (!empty($fam_econSt)) {
                                        foreach ($fam_econSt as $val) {
                                            ?>
                                            <option value="<?php echo $val['EconStateID'] ?>"><?php echo $val['EconStateDes'] ?></option>
                                            <?php

                                        }
                                    }
                                    ?>
                            </select>

    <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('CommunityNgo_fam_expenses'); ?><!-- monthly expenses--><label>
                    </div>
                    <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
                                  <div class="input-group">
                          <div class="input-group-addon"><?php echo $this->common_data['company_data']['company_default_currency']; ?></div>
                          <input type="text" name="monthlyExpenses" value="" id="monthlyExpenses" class="form-control">
                                      </div>
                        <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_Remarks'); ?><!--Remark --><label>
                    </div>
                    <div class="form-group col-sm-10">
                               <input type="text" name="femExpensesRemark" value="" id="femExpensesRemark" class="form-control">
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('CommunityNgo_famHelpNeed'); ?><!--Any Urgent Help Need--><label>
                    </div>
                    <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
                                <select onchange="get_econStatus();" id="femHelpNeedId" class="form-control select2"
                                        name="femHelpNeedId">
                                <option value=""><?php echo $this->lang->line('CommunityNgo_famSelHelpNeed'); ?><!--Select Help Need Status--></option>

                <option value='0'>No</option>
                <option value='1'>Yes</option>
                            </select>

    <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-2" id="fmHelpDNeed" style="display:none;">
                        <label class="title">
                            <?php echo $this->lang->line('CommunityNgo_fam_neededHelp'); ?><!--Needed Help Details--><label>
                    </div>
                    <div class="form-group col-sm-4" id="fmHelpNeed" style="display:none;">
                            <textarea rows="2" cols="2" name="femNeededHelp" id="femNeededHelp" class="form-control"></textarea>

                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-12">
                        <button id="save_btn" class="btn btn-primary pull-right" type="submit">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <br>
        <div class="row addTableView">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2><?php echo $this->lang->line('CommunityNgo_famMem_del'); ?><!--Fam Mem DETAILS--></h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="gt_femMemAdding_modal()">
                            <i class="fa fa-plus"></i>
                            <?php echo $this->lang->line('CommunityNgo_add_members'); ?><!--Add Members-->
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-11">
                        <div id="dispatchDetial_addonCost"></div>
                    </div>
                    <div class="col-sm-1">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>

    </div>
    <div id="step2" class="tab-pane">

        <?php echo form_open('', 'role="form" id="dispatchFam_house_form"'); ?>
        <input type="text" name="hEnrollingID" id="hEnrollingID" value="" style="display:none ;">
        <input type="text" name="FamMasterID2" id="FamMasterID2" value="" style="display:none ;">
        <input type="text" name="LeaderID2" id="LeaderID2" value="" style="display:none ;">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>
                        <?php echo $this->lang->line('CommunityNgo_family_house'); ?><!--Family House Master--></h2>
                </header>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">

                        <label class="title">
                            <?php echo $this->lang->line('communityngo_famHouseCount'); ?><!--House Count--><label>
                    </div>
                    <div class="form-group col-sm-4">
                          <span class="input-req" title="Required Field">
                        <select id="FamHouseCn" class="form-control select2" name="FamHouseCn" onchange="checkHome_state()">
                            <option><?php echo $this->lang->line('CommunityNgo_status'); ?><!--Select Status--></option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>

                        <span class="input-req-inner"></span></span>

                    </div>
                </div>
                <div id="viewDivCls1" class="row" style="margin-top: 10px;display: none;">
                    <div class="form-group col-sm-2">

                        <label class="title">
                            <?php echo $this->lang->line('communityngo_famExistHouse'); ?><!--House Count--><label>
                    </div>
                    <div class="form-group col-sm-4">
                         <span class="input-req" title="Required Field">
                        <select id="FamHouseSt" class="form-control select2" name="FamHouseSt" onchange="checkHome_inExist()">
                            <option><?php echo $this->lang->line('CommunityNgo_status'); ?><!--Select Status--></option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>

                        <span class="input-req-inner"></span></span>

                    </div>
                    <div class="form-group col-sm-2" id="houseExitHd" style="display:none;">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_famHouseExisting'); ?><!--Existing Family--><label>
                    </div>
                    <div class="form-group col-sm-4" id="houseExitDiv" style="display: none;">
                        <span class="input-req" title="Required Field">
                        <select id="housesExitId" class="form-control select2"
                                name="housesExitId" onchange="getDel_inExist();">
                            <option data-currency=""
                                    value=""><?php echo $this->lang->line('communityngo_selFamHouseExist'); ?><!--Select Existing Family --></option>
                            <?php
                            $house_ex = fetch_house_exitInEnroll();
                            if (!empty($house_ex)) {
                                foreach ($house_ex as $val) {
                                    ?>
                                    <option value="<?php echo $val['hEnrollingID'] ?>"><?php echo $val['FamilyName'] ?></option>
                                    <?php

                                }
                            }
                            ?>
                        </select>
                        <span class="input-req-inner"></span></span>
                    </div>

                </div>

                <div id="viewDivCls2" class="row" style="margin-top: 10px;display: none;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_JammiyaDivision'); ?><!--Jammiyah Division--><label>
                    </div>
                    <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
    <select onchange="" id="hEjammiyahDivisionID" class="form-control select2"
            name="hEjammiyahDivisionID" disabled>
        <option>Select Jammiyah Division</option>
            <option></option>

    </select>

    <span class="input-req-inner"></span></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_GS_Division'); ?><!--GS Division--><label>
                    </div>
                    <div class="form-group col-sm-2">
                           <span class="input-req" title="Required Field">
    <select onchange="" id="hEGS_Division" class="form-control select2"
            name="hEGS_Division" disabled>
        <option>Select a GS Division</option>
            <option></option>

    </select>

    <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                           <span class="input-req" title="Required Field">
    <select onchange="" id="hEGS_No" class="form-control select2"
            name="hEGS_No" disabled>
        <option>GS No</option>
            <option></option>

    </select>

    <span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div id="viewDivCls3" class="row" style="margin-top: 10px;display: none;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_region'); ?><!--Area / Mahalla--><label>
                    </div>
                    <div class="form-group col-sm-2">
                           <span class="input-req" title="Required Field">
    <select onchange="" id="hERegionID" class="form-control select2"
            name="hERegionID" disabled>
        <option>Select a Area / Mahalla</option>
            <option></option>

    </select>

    <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                           <span class="input-req" title="Required Field">
    <select onchange="" id="hEHouseNo" class="form-control select2"
            name="hEHouseNo" disabled>
        <option><?php echo $this->lang->line('communityngo_houseNo'); ?></option>
            <option></option>

    </select>

    <span class="input-req-inner"></span></span>
                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_contactAddress'); ?><!--Contact Address--><label>
                    </div>
                    <div class="form-group col-sm-4">
                        <select onchange="" id="hEC_Address" class="form-control select2"
                                name="hEC_Address" disabled>
                            <option data-currency=""
                                    value=""><?php echo $this->lang->line('CommunityNgo_fam_SelAddress'); ?><!--Select Address--></option>
                            <option></option>
                        </select>

                        <span class="input-req-inner"></span>

                    </div>

                </div>

                <div id="viewDivCls4" class="row" style="margin-top: 10px;display: none;">
                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_famOwnType'); ?><label>
                    </div>
                    <div class="form-group col-sm-4">
                        <select id="ownershipAutoID" class="form-control select2"
                                name="ownershipAutoID">
                            <option data-currency=""
                                    value=""><?php echo $this->lang->line('communityngo_selfamOwnType'); ?></option>
                            <?php
                            $house_own = fetch_house_house_ownership();
                            if (!empty($house_own)) {
                                foreach ($house_own as $val) {
                                    ?>
                                    <option value="<?php echo $val['ownershipAutoID'] ?>"><?php echo $val['ownershipDescription'] ?></option>
                                    <?php

                                }
                            }
                            ?>
                        </select>

                    </div>

                    <div class="form-group col-sm-2">
                        <label class="title">
                            <?php echo $this->lang->line('communityngo_famHouseType'); ?><!--House Type--><label>
                    </div>
                    <div class="form-group col-sm-2">
                        <select id="hTypeAutoID" class="form-control select2"
                                name="hTypeAutoID">
                            <option data-currency=""
                                    value=""><?php echo $this->lang->line('communityngo_selfamHouseType'); ?><!--Select House Type --></option>
                            <?php
                            $house_ty = fetch_house_type_master();
                            if (!empty($house_ty)) {
                                foreach ($house_ty as $val) {
                                    ?>
                                    <option value="<?php echo $val['hTypeAutoID'] ?>"><?php echo $val['hTypeDescription'] ?></option>
                                    <?php

                                }
                            }
                            ?>
                        </select>

                    </div>
                    <div class="form-group col-sm-2">
                          <input type="text" name="hESizeInPerches" value="" id="hESizeInPerches" class="form-control" placeholder=" <?php echo $this->lang->line('communityngo_famHouseSizeInPrch'); ?>">
                    </div>

                </div>
                <fieldset id="viewDivCls5" class="scheduler-border" style="margin-top: 10px;display: none;">
                    <legend class="scheduler-border"><?php echo $this->lang->line('communityngo_famHouseFacilities');?><!--Home Facilities--></legend>
                    <div class="col-sm-8" style="margin-bottom: 0px;margin-top:10px;">
                        <table class="<?php echo table_class(); ?>" id="facilityColumns">
                            <tr>
                                <td style="vertical-align: middle">Electricity</td>
                                <td>
                                            <input type="checkbox" id="isHmElectric1"  name="isHmElectric1" onchange="isElectricChk();">

                                            <input class="form-control" type="number" name="isHmElectric" id="isHmElectric" value="0" style="display: none ;">
                                </td>
                                <td style="vertical-align: middle">Water Supply</td>
                                <td>

                                    <input type="checkbox" id="isHmWaterSup1"  name="isHmWaterSup1" onchange="isHmWaterSupChk();">

                                    <input class="form-control" type="number" name="isHmWaterSup" id="isHmWaterSup" value="0" style="display: none ;">

                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle">Toilet</td>
                                <td>
                                    <input type="checkbox" id="isHmToilet1"  name="isHmToilet1" onchange="isHmToiletChk();">

                                    <input class="form-control" type="number" name="isHmToilet" id="isHmToilet" value="0" style="display: none ;">

                                </td>
                                <td style="vertical-align: middle">Bathroom</td>
                                <td>
                                    <input type="checkbox" id="isHmBathroom1"  name="isHmBathroom1" onchange="isHmBathroomChk();">

                                    <input class="form-control" type="number" name="isHmBathroom" id="isHmBathroom" value="0" style="display: none ;">

                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle">Telephone</td>
                                <td>

                                    <input type="checkbox" id="isHmTelephone1"  name="isHmTelephone1" onchange="isHmTelephoneChk();">

                                    <input class="form-control" type="number" name="isHmTelephone" id="isHmTelephone" value="0" style="display: none ;">

                                </td>
                                <td style="vertical-align: middle">Kitchen</td>
                                <td>

                                    <input type="checkbox" id="isHmKitchen1"  name="isHmKitchen1" onchange="isHmKitchenChk();">

                                    <input class="form-control" type="number" name="isHmKitchen" id="isHmKitchen" value="0" style="display: none ;">

                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4" style="margin-bottom: 0px;margin-top:10px">
                        <?php echo $this->lang->line('communityngo_famCheckFacility');?>  <!--Un-check the facility, If It doesn't exist.-->
                    </div>
                </fieldset>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-12">
                        <button id="save2_btn" class="btn btn-primary pull-right"  type="submit">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <br>
        <br>

    </div>
    <div id="step3" class="tab-pane">
        <div id="confirm_body"></div>
        <hr>
        <div class="text-right m-t-xs">
            <button class="btn btn-default prev">
                <?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
            <button class="btn btn-primary " onclick="save_draft()">
                <?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
            <button class="btn btn-success submitWizard" onclick="confirmation()">
                <?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="comMem_detail_modal" class="modal fade" style="display: none;z-index:1100;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('communityngo_add_new_member'); ?><!--Add New Member--></h5>
            </div>

            <form role="form" id="comMem_detail_form" class="form-horizontal">
                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="comMem_add_table">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('communityngo_title'); ?><!--Title--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('communityngo_name_with_initial'); ?><!--Name with Initial--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('communityngo_name'); ?><!--Full Name--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('communityngo_gender'); ?><!--Gender--></th>
                            <th><?php echo $this->lang->line('communityngo_dob'); ?><!--Date of Birth--></th>
                            <th><?php echo $this->lang->line('communityngo_relationship'); ?><!--Relationship--></th>

                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_communityMem()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>  <?php echo form_dropdown('TitleID[]', $getAll_title, '',
                                    'class="form-control"  required'); ?>
                            </td>
                            <td>
                                <input type="text" name="nameWithIni[]" placeholder="Name With Initial"
                                       class="form-control " required>
                            </td>
                            <td>
                                <input type="text" name="nameWithFull[]" placeholder="Full Name"
                                       class="form-control " required>
                            </td>
                            <td>  <?php echo form_dropdown('genderID[]', fetch_com_gender(), '',
                                    'class="form-control"  required'); ?>
                            </td>
                            <td>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="newMemDOB[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           class="form-control" required>
                                </div>
                            </td>
                            <td>
                                    <?php echo form_dropdown('NewRelatnID[]', fetch_family_relationship(), '',
                                        'class="form-control" required'); ?>
                            </td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="saveCommunityMem()">
                        <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="femMemAdding_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="clsRel" onclick="dis_clsModal();"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title"><?php echo $this->lang->line('CommunityNgo_add_member'); ?><!--Add Family Member--></h5>
            </div>
            <form role="form" id="femMemAdding_form" class="form-horizontal">
                <div class="modal-body">
                    <div id="PD_modalMainFm" style="display: block;">
                    <button type="button" class="btn btn-success pull-right"
                            onclick="comMem_detail_modal()">
                        <i class="fa fa-plus"></i>
                        <?php echo $this->lang->line('communityngo_add_new'); ?><!--New Members-->
                    </button>

                    <div class="form-group col-sm-3">
                        <label class="title">
                            <?php echo $this->lang->line('CommunityNgo_famMem_AddedDate'); ?><!--Member Added Date--><label>
                    </div>
                    <div class="form-group col-sm-4 input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="FamMemAddedDate"
                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                               value="" class="form-control">
                    </div>

                        <div class="col-sm-12" style="margin-top:15px;margin-bottom:10px;">
                            <div class="col-sm-5">
                                <select name="Com_MasterIDFrom[]" id="search" class="form-control" style="font-size: 13px;" size="8"
                                        multiple="multiple">
                                    <?php
                                    $fmCode = fetch_familyMemAct_drop();
                                    if (!empty($fmCode)) {
                                        foreach ($fmCode as $key => $val) {
                                            echo '<option value="' . $key . '">' . $val . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <!--<button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>-->
                                <button type="button" id="search_rightAll" class="btn btn-block btn-sm"
                                ><i class="fa fa-forward"></i></button>
                                <button type="button" id="search_rightSelected" class="btn btn-block btn-sm"><i
                                        class="fa fa-chevron-right"></i></button>
                                <button type="button" id="search_leftSelected" class="btn btn-block btn-sm"><i
                                        class="fa fa-chevron-left"></i></button>
                                <button type="button" id="search_leftAll" class="btn btn-block btn-sm"><i
                                        class="fa fa-backward"></i></button>
                                <!--<button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button>-->
                            </div>
                            <div class="col-sm-5">
                                <select name="Com_MasterID[]" id="search_to" class="form-control" style="font-size: 13px;" size="8"
                                        multiple="multiple">
                                </select>
                            </div>
                        </div>
                  </div>
                    <div id="PD_modalRelatnFrm" style="display: none;">
                        <div id="femilyMem_reltnView"></div>
                    </div>

                </div>
                <div class="modal-footer">
                    <table border="0" style="float: right;"><tr><td style="padding:2px;">
                            <button data-dismiss="modal" class="btn btn-default comRltn1" type="button" id="clsRel" onclick="dis_clsModal();"><i class="fa fa-times"></i>
                                <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>

                                <button class="btn btn-primary comRltn2" type="button" id="footerNxt_DT" onclick="display_relatn_form()"><i class="fa fa-share"></i>
                                    <?php echo $this->lang->line('common_next'); ?><!--Next--></button>

                                <button class="btn btn-danger comRltn3" type="button" style="display: none" id="PD_ModalBack2Btn" onclick="display_backHm()"><i class="fa fa-reply"></i>
                                    <?php echo $this->lang->line('communityngo_back'); ?><!--Back--> </button>

                                <button class="btn btn-primary comRltn4" type="button" style="display: none" id="footerSumt_DT" onclick="saveFemMemDetails()">
                                    <?php echo $this->lang->line('common_save_change'); ?><!--Save changes-->
                                </button>
                            </td>
                        </tr></table>


                </div>
            </form>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" id="edit_femilyMem_model" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('CommunityNgo_edit_famMember'); ?><!--Edit Member--></h4>
            </div>
            <form role="form" id="edit_rv_income_detail_form" class="form-horizontal">
                <!--hidden feild to capture edit id-->
                <input type="number" name="edit_femMm" id="edit_femMm" value="" style="display: none;">

                <div class="modal-body">
                    <table class="table table-bordered table-condensed no-color" id="income_edit_table">
                        <thead>
                        <tr>
                            <th><?php echo $this->lang->line('communityngo_name_of_member'); ?><!--Family Member--> <?php required_mark(); ?></th>
                            <th><?php echo $this->lang->line('communityngo_relationship'); ?><!--Relationship--></th>

                            <th><?php echo $this->lang->line('CommunityNgo_famMem_AddedDate'); ?><!--Member Added Date--></th>
                            <th><?php echo $this->lang->line('CommunityNgo_famMem_isDef'); ?><!--Is Default--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <?php echo form_dropdown('Com_MasterID', fetch_familyMems_drop(), '',
                                    'class="form-control" id="edit2_Com_MasterID" required'); ?>
                            </td>
                            <td>
                                <div class="input-group">
                                <?php echo form_dropdown('relationshipID', fetch_family_relationship(), '',
                                    'class="form-control" id="edit2_relationshipID" required'); ?>
                                <span class="input-group-btn">
                             <button class="btn btn-default" type="button" id="add-Relatn" style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus" style="font-size: 11px"></i></button>
                             </span>

                             </div>
                            </td>
                            <td style="">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="FamMemAddedDate" id="edit2_FamMemAddedDate"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" class="form-control">
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <input type="checkbox" id="isMoveSt2b"  name="isMoveSt" onchange="isItMove();">
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <input class="form-control" type="number" name="isMoveId" id="moveSt2" value="0"
                                           style="display: none ;">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <button class="btn btn-primary" type="button" onclick="updateDirectRvDetails()">
                        <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="Relatn-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('communityngo_relationship');?> </h4><!--New Relationship Title-->
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_title');?> </label><!--Title-->

                            <div class="col-sm-6">
                                    <span class="input-req" title="Required Field">
                                     <input type="text" class="form-control" id="add-mem-Relatn" name="add-mem-Relatn" required><span
                                            class="input-req-inner"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="Relatn-btn"><?php echo $this->lang->line('common_save');?> </button><!--Save-->
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#save_btn').html('Save');
    $('#save2_btn').html('Save');
    $('.addTableView').removeClass('hide');
    var documentCurrency;
    var FamMasterID;
    var hEnrollingID;
    var search_id = 1;
    var currency_decimal = 1;
    var batchMasterID;
    var dispatchDetailsID;
    var FamDel_ID;
    $(document).ready(function () {

        var masterID = '<?php if (isset($_POST['data_arr']) && !empty($_POST['data_arr'])) {
            echo json_encode($_POST['data_arr']);
        } ?>';

        if (masterID != null && masterID.length > 0) {
            var masterIDNew = JSON.parse(masterID);
            $('.headerclose').click(function () {

                fetchPage('system/communityNgo/ngo_mo_familyLink_view', masterIDNew[0], 'Family Relationship', 'NGO');

            });
        }
        else {
            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_mo_familyMaster', '', 'Family Master');
            });
        }

        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });

        $('.select2').select2();

        $('#search').multiselect({
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />',<!--Search-->
                right: '<input type="text" name="q" class="form-control" placeholder="<?php echo $this->lang->line('common_search');?>..." />',<!--Search-->
            },
            afterMoveToLeft: function ($left, $right, $options) {
                $("#search_to option").prop("selected", "selected");

            }
        });

        Inputmask().mask(document.querySelectorAll("input"));

        FamDel_ID = null;
        documentCurrency = null;
        FamMasterID = null;
        hEnrollingID = null;
        dispatchDetailsID = null;

        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            FamMasterID = p_id;
            load_ngoFamilyHeader();
            getDispatchDetailAddonCost_tableView(FamMasterID);
            load_confirmation();
            familyMaster_exist();

            $('.btn-wizard').removeClass('disabled');
        } else {
            $('.btn-wizard').addClass('disabled');
            $('.addTableView').addClass('hide');

        }

        number_validation();
        currency_decimal = 2;

        $('#dispatchNote_header_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                FamilyAddedDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_famAddeddate_is_required');?>.'}}}, /*Family Added Date is required*/
                LeaderID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_leader_is_required');?>.'}}}, /*Head Of The Family is required*/
                FamAncestory: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_AncestryState_required');?>.'}}}/*Ancestry State is required*/
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            data.push({'name': 'FamMasterID', 'value': FamMasterID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/save_familyMaster'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        $('.addTableView').removeClass('hide');

                        getDispatchDetailAddonCost_tableView(data[2]);
                        FamMasterID = data[2];
                        familyMaster_exist();
                        $('#save_btn').html('Update');
                        $('#save_btn').html('Update');
                        $('.btn-wizard').removeClass('disabled');
                        /*  $('[href=#step3]').tab('show');*/
                        $('#save_btn').removeAttr('disabled');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        });


        $('#dispatchFam_house_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                FamHouseCn: {validators: {notEmpty: {message: 'Enroll To House Count status is required.'}}}, /*Enroll To House Count is required*/
                FamHouseSt: {validators: {notEmpty: {message: 'Enroll To Existing House Count status is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            data.push({'name': 'hEnrollingID', 'value': hEnrollingID});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/save_familyHouseEnroll'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {

                        hEnrollingID = data[2];
                        $('#save2_btn').html('Update');
                        $('#save2_btn').html('Update');
                        $('.btn-wizard').removeClass('disabled');
                        /*  $('[href=#step3]').tab('show');*/
                        $('#save2_btn').removeAttr('disabled');
                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
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
    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });


    function chkDuplicate(){

        var ledger_num =document.getElementById('LedgerNo').value;
        var editFamMasID=document.getElementById('FamMasterID').value;

        if(ledger_num == "" || ledger_num == null) {
        }else{
            $.ajax({
                async: true,
                url: "<?php echo site_url('CommunityNgo/nicDup_Check'); ?>",
                type: 'post',
                dataType: 'json',
                data: {'ledger_num': ledger_num,'editFamMasID':editFamMasID},
                beforeSend: function () {
                 //   startLoad();
                },
                success: function (data) {

                   // stopLoad();
                    if (data['error'] == 1) {

                        $('#LedgerNo').val("");

                        myAlert('e', data['message']);

                    }

                }, error: function () {
                    stopLoad();
                    myAlert('e', 'error');

                }
            });
        }
    }

    function get_comMaserDel() {
        var LeaderID = document.getElementById('LeaderID').value;
        var LeaderName = $("#LeaderID :selected").text();

        $('#FamAncestory').select2('val'," ");

        if (LeaderID == "" || LeaderID == null) {
        } else {
            //$('#FamilyName').html(LeaderID);
            $('#FamilyName').val(LeaderName+' '+'Family').change();
            $.ajax({
                type: "POST",
                url: "CommunityNgo/get_FamHgender",
                data: {'LeaderID': LeaderID},
                success: function (data) {

                    $('#FamHgender').html(data);
                }
            });
            $.ajax({
                type: "POST",
                url: "CommunityNgo/get_FamHaddress",
                data: {'LeaderID': LeaderID},
                success: function (data) {

                    $('#FamHaddress').html(data);
                }
            });
            $.ajax({
                type: "POST",
                url: "CommunityNgo/get_FamArea",
                data: {'LeaderID': LeaderID},
                success: function (data) {

                    $('#FamArea').html(data);
                }
            });
            $.ajax({
                type: "POST",
                url: "CommunityNgo/get_FamHouseNo",
                data: {'LeaderID': LeaderID},
                success: function (data) {

                    $('#FamHouseNo').html(data);
                }
            });
        }
    }


    function get_AncesCategory() {

        var FamAncestory = document.getElementById('FamAncestory').value;

        if (FamAncestory == '1') { //if Outside

            document.getElementById('AncesDesDiv').style.display="block";
            document.getElementById('AncesSelDiv').style.display="block";

        }
        else {

            document.getElementById('AncesDesDiv').style.display="none";
            document.getElementById('AncesSelDiv').style.display="none";
        }
    }


    function checkHome_state() {

        var editFamMasID=document.getElementById('FamMasterID2').value;
        var LeaderID =document.getElementById('LeaderID2').value;
        var FamHouseCn = document.getElementById('FamHouseCn').value;
        var FamHouseSt = document.getElementById('FamHouseSt').value;
        var housesExitId = document.getElementById('housesExitId').value;

        $('#housesExitId').val('').change();

        if(FamHouseCn == '1'){
            document.getElementById('viewDivCls1').style.display="block";
            document.getElementById('viewDivCls2').style.display="block";
            document.getElementById('viewDivCls3').style.display="block";
            document.getElementById('viewDivCls4').style.display="block";
            document.getElementById('viewDivCls5').style.display="block";
        }
        else{

            $('#FamHouseSt').val('0').change();

            document.getElementById('viewDivCls1').style.display="none";
            document.getElementById('viewDivCls2').style.display="none";
            document.getElementById('viewDivCls3').style.display="none";
            document.getElementById('viewDivCls4').style.display="none";
            document.getElementById('viewDivCls5').style.display="none";

        }


        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hEjammiyahDivisionID",
            data: {'FamHouseSt':FamHouseSt,'housesExitId':housesExitId,'LeaderID': LeaderID},
            success: function (data) {

                $('#hEjammiyahDivisionID').html(data);
            }
        });
        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hEGS_Division",
            data: {'FamHouseSt':FamHouseSt,'housesExitId':housesExitId,'LeaderID': LeaderID},
            success: function (data) {

                $('#hEGS_Division').html(data);
            }
        });
        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hEGS_No",
            data: {'FamHouseSt':FamHouseSt,'housesExitId':housesExitId,'LeaderID': LeaderID},
            success: function (data) {

                $('#hEGS_No').html(data);
            }
        });
        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hERegionID",
            data: {'FamHouseSt':FamHouseSt,'housesExitId':housesExitId,'LeaderID': LeaderID},
            success: function (data) {

                $('#hERegionID').html(data);
            }
        });
        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hEHouseNo",
            data: {'FamHouseSt':FamHouseSt,'housesExitId':housesExitId,'LeaderID': LeaderID},
            success: function (data) {

                $('#hEHouseNo').html(data);
            }
        });
        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hEC_Address",
            data: {'FamHouseSt':FamHouseSt,'housesExitId':housesExitId,'LeaderID': LeaderID},
            success: function (data) {

                $('#hEC_Address').html(data);
            }
        });


    }

    function get_econStatus() {

        var femHelpNeedID = document.getElementById('femHelpNeedId').value;

        if (femHelpNeedID == '1') { //if Outside

            document.getElementById('fmHelpDNeed').style.display="block";
            document.getElementById('fmHelpNeed').style.display="block";

        }
        else {

            document.getElementById('fmHelpDNeed').style.display="none";
            document.getElementById('fmHelpNeed').style.display="none";
        }
    }

    function checkHome_inExist() {

        var editFamMasID=document.getElementById('FamMasterID2').value;
        var LeaderID =document.getElementById('LeaderID2').value;
        var FamHouseSt = document.getElementById('FamHouseSt').value;

        $('#housesExitId').val('').change();

        if(FamHouseSt == '1'){
            document.getElementById('houseExitHd').style.display="block";
            document.getElementById('houseExitDiv').style.display="block";

        }
        else{
            document.getElementById('houseExitHd').style.display="none";
            document.getElementById('houseExitDiv').style.display="none";

        }

    }

    function getDel_inExist() {

        var editFamMasID = document.getElementById('FamMasterID2').value;
        var LeaderID = document.getElementById('LeaderID2').value;
        var FamHouseSt = document.getElementById('FamHouseSt').value;
        var housesExitId = document.getElementById('housesExitId').value;

        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hEjammiyahDivisionID",
            data: {'FamHouseSt': FamHouseSt, 'housesExitId': housesExitId, 'LeaderID': LeaderID},
            success: function (data) {

                $('#hEjammiyahDivisionID').html(data);
            }
        });
        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hEGS_Division",
            data: {'FamHouseSt': FamHouseSt, 'housesExitId': housesExitId, 'LeaderID': LeaderID},
            success: function (data) {

                $('#hEGS_Division').html(data);
            }
        });
        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hEGS_No",
            data: {'FamHouseSt': FamHouseSt, 'housesExitId': housesExitId, 'LeaderID': LeaderID},
            success: function (data) {

                $('#hEGS_No').html(data);
            }
        });
        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hERegionID",
            data: {'FamHouseSt': FamHouseSt, 'housesExitId': housesExitId, 'LeaderID': LeaderID},
            success: function (data) {

                $('#hERegionID').html(data);
            }
        });
        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hEHouseNo",
            data: {'FamHouseSt': FamHouseSt, 'housesExitId': housesExitId, 'LeaderID': LeaderID},
            success: function (data) {

                $('#hEHouseNo').html(data);
            }
        });
        $.ajax({
            type: "POST",
            url: "CommunityNgo/get_hEC_Address",
            data: {'FamHouseSt': FamHouseSt, 'housesExitId': housesExitId, 'LeaderID': LeaderID},
            success: function (data) {

                $('#hEC_Address').html(data);
            }
        });

        if (housesExitId){

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'FamHouseSt': FamHouseSt, 'housesExitId': housesExitId, 'LeaderID': LeaderID},
                url: "<?php echo site_url('CommunityNgo/load_ngoHouseExitDel'); ?>",
                beforeSend: function () {
                 //   startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {

                        $('#hESizeInPerches').val(data['hESizeInPerches']);
                        $("#ownershipAutoID").val(data['ownershipAutoID']).change();

                        $('#hTypeAutoID').val(data['hTypeAutoID']).change();

                        if(data['isHmElectric']==1){
                            document.getElementById('isHmElectric1').checked=true;
                            document.getElementById('isHmElectric').value = 1;
                        }
                        else{
                            document.getElementById('isHmElectric1').checked=false;
                            document.getElementById('isHmElectric').value = 0;
                        }
                        if(data['isHmWaterSup']==1){
                            document.getElementById('isHmWaterSup1').checked=true;
                            document.getElementById('isHmWaterSup').value = 1;
                        }
                        else{
                            document.getElementById('isHmWaterSup1').checked=false;
                            document.getElementById('isHmWaterSup').value = 0;
                        }
                        if(data['isHmToilet']==1){
                            document.getElementById('isHmToilet1').checked=true;
                            document.getElementById('isHmToilet').value = 1;
                        }
                        else{
                            document.getElementById('isHmToilet1').checked=false;
                            document.getElementById('isHmToilet').value = 0;
                        }
                        if(data['isHmBathroom']==1){
                            document.getElementById('isHmBathroom1').checked=true;
                            document.getElementById('isHmBathroom').value = 1;
                        }
                        else{
                            document.getElementById('isHmBathroom1').checked=false;
                            document.getElementById('isHmBathroom').value = 0;
                        }
                        if(data['isHmTelephone']==1){
                            document.getElementById('isHmTelephone1').checked=true;
                            document.getElementById('isHmTelephone').value = 1;
                        }
                        else{
                            document.getElementById('isHmTelephone1').checked=false;
                            document.getElementById('isHmTelephone').value = 0;
                        }
                        if(data['isHmKitchen']==1){
                            document.getElementById('isHmKitchen1').checked=true;
                            document.getElementById('isHmKitchen').value = 1;
                        }
                        else{
                            document.getElementById('isHmKitchen1').checked=false;
                            document.getElementById('isHmKitchen').value = 0;
                        }

                    }
                }
            });
        }

    }

    function isElectricChk(){

        var f=document.getElementById('isHmElectric1').checked;

        if(f==true) {
            document.getElementById('isHmElectric').value = 1;
        }if(f==false){
            document.getElementById('isHmElectric').value = 0;
        }
    }

    function isHmWaterSupChk(){

        var f=document.getElementById('isHmWaterSup1').checked;

        if(f==true) {
            document.getElementById('isHmWaterSup').value = 1;
        }if(f==false){
            document.getElementById('isHmWaterSup').value = 0;
        }
    }

    function isHmToiletChk(){

        var f=document.getElementById('isHmToilet1').checked;

        if(f==true) {
            document.getElementById('isHmToilet').value = 1;
        }if(f==false){
            document.getElementById('isHmToilet').value = 0;
        }
    }
    function isHmBathroomChk(){

        var f=document.getElementById('isHmBathroom1').checked;

        if(f==true) {
            document.getElementById('isHmBathroom').value = 1;
        }if(f==false){
            document.getElementById('isHmBathroom').value = 0;
        }
    }
    function isHmTelephoneChk(){

        var f=document.getElementById('isHmTelephone1').checked;

        if(f==true) {
            document.getElementById('isHmTelephone').value = 1;
        }if(f==false){
            document.getElementById('isHmTelephone').value = 0;
        }
    }
    function isHmKitchenChk(){

        var f=document.getElementById('isHmKitchen1').checked;

        if(f==true) {
            document.getElementById('isHmKitchen').value = 1;
        }if(f==false){
            document.getElementById('isHmKitchen').value = 0;
        }
    }

    function load_ngoHouseHeader(){
        if (FamMasterID) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'FamMasterID': FamMasterID},
                url: "<?php echo site_url('CommunityNgo/load_ngoHouseHeader'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        hEnrollingID = data['hEnrollingID'];
                        FamMasterID = data['FamMasterID'];

                        document.getElementById('hEnrollingID').value = hEnrollingID;
                        document.getElementById('FamMasterID2').value = FamMasterID;
                        document.getElementById('LeaderID2').value = data['LeaderID'];

                        $('#hESizeInPerches').val(data['hESizeInPerches']);

                        $("#FamHouseCn").val('1').change();
                        $("#FamHouseSt").val(data['FamHouseSt']).change();
                        $("#housesExitId").val(data['Link_hEnrollingID']).change();
                        $("#ownershipAutoID").val(data['ownershipAutoID']).change();

                        $('#hTypeAutoID').val(data['hTypeAutoID']).change();

                        if(data['isHmElectric']==1){
                            document.getElementById('isHmElectric1').checked=true;
                            document.getElementById('isHmElectric').value = 1;
                        }
                        else{
                            document.getElementById('isHmElectric1').checked=false;
                            document.getElementById('isHmElectric').value = 0;
                        }
                        if(data['isHmWaterSup']==1){
                            document.getElementById('isHmWaterSup1').checked=true;
                            document.getElementById('isHmWaterSup').value = 1;
                        }
                        else{
                            document.getElementById('isHmWaterSup1').checked=false;
                            document.getElementById('isHmWaterSup').value = 0;
                        }
                        if(data['isHmToilet']==1){
                            document.getElementById('isHmToilet1').checked=true;
                            document.getElementById('isHmToilet').value = 1;
                        }
                        else{
                            document.getElementById('isHmToilet1').checked=false;
                            document.getElementById('isHmToilet').value = 0;
                        }
                        if(data['isHmBathroom']==1){
                            document.getElementById('isHmBathroom1').checked=true;
                            document.getElementById('isHmBathroom').value = 1;
                        }
                        else{
                            document.getElementById('isHmBathroom1').checked=false;
                            document.getElementById('isHmBathroom').value = 0;
                        }
                        if(data['isHmTelephone']==1){
                            document.getElementById('isHmTelephone1').checked=true;
                            document.getElementById('isHmTelephone').value = 1;
                        }
                        else{
                            document.getElementById('isHmTelephone1').checked=false;
                            document.getElementById('isHmTelephone').value = 0;
                        }
                        if(data['isHmKitchen']==1){
                            document.getElementById('isHmKitchen1').checked=true;
                            document.getElementById('isHmKitchen').value = 1;
                        }
                        else{
                            document.getElementById('isHmKitchen1').checked=false;
                            document.getElementById('isHmKitchen').value = 0;
                        }

                        $('[href=#step2]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        $('#save2_btn').html('<?php echo $this->lang->line('common_update');?>');
                        /*Update*/
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


    function display_relatn_form() {

        document.getElementById('PD_modalMainFm').style.display = 'none';
        document.getElementById('PD_modalRelatnFrm').style.display = 'block';

        $('.comRltn2').hide(); // hides
       // var data = $('#femMemAdding_form').serializeArray();

        $.ajax({

            type: "POST",
            url: "<?php echo site_url('CommunityNgo/getAdded_members'); ?>",
            dataType: 'html',
            data: $('#femMemAdding_form').serializeArray(),
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                refreshNotifications(true);
                stopLoad();
                $('#femilyMem_reltnView').html(data);
                $('.comRltn3').show(); // shows
                $('.comRltn4').show(); // shows
                $('.comRltn1').show(); // shows

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                refreshNotifications(true);
            }
        });


    }

    function display_backHm(){

        document.getElementById('PD_modalRelatnFrm').style.display = 'none';
        document.getElementById('PD_modalMainFm').style.display = 'block';

        $('.comRltn2').show(); // show
        $('.comRltn3').hide(); // hides
        $('.comRltn4').hide(); // hides
        $('.comRltn1').show(); // shows
    }

    function dis_clsModal(){


        document.getElementById('PD_modalRelatnFrm').style.display = 'none';
        document.getElementById('PD_modalMainFm').style.display = 'block';

        $('.comRltn3').hide(); // hides
        $('.comRltn4').hide(); // hides
        $('.comRltn2').show(); // shows
        $('.comRltn1').show(); // shows

        var obj = document.getElementById('search_to');
        for (var i=0; i < obj.options.length; i++) {
            obj.options[i] = null;
        }

        //document.getElementById('femMemAdding_form').reset();
        $('#femMemAdding_form')[0].reset();

        fetch_familyMemAct_drop();

      //  $("#search_to option").prop("val", "");
      //  $('#search_to').select2("val", "");

    }


    function updateDirectRvDetails() {
        var data = $('#edit_rv_income_detail_form').serializeArray();
        data.push({'name': 'FamMasterID', 'value': FamMasterID});
        data.push({'name': 'FamDel_ID', 'value': FamDel_ID});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('CommunityNgo/update_comFem_member_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    FamDel_ID = null;
                    getDispatchDetailAddonCost_tableView(FamMasterID);
                    $('#edit_femilyMem_model').modal('hide');
                    $('#edit_rv_income_detail_form')[0].reset();
                    $('.select2').select2('')

                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function familyMaster_exist() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'FamMasterID': FamMasterID},
            url: "<?php echo site_url('CommunityNgo/familyMaster_exist'); ?>",
            beforeSend: function () {

            },
            success: function (data) {

                if (!jQuery.isEmptyObject(data)) {
                    $("#LeaderID").attr('disabled', 'disabled');
                  //  $("#FamAncestory").attr('disabled', 'disabled');
                } else {
                    $("#LeaderID").removeAttr('disabled');
                  //  $("#FamAncestory").removeAttr('disabled');
                }
                stopLoad();
                //refreshNotifications(true);
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }


    function edit_familyMember(id) {

        document.getElementById('edit_femMm').value = id;

        if (FamMasterID) {
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
                        data: {'FamDel_ID': id},
                        url: "<?php echo site_url('CommunityNgo/fetch_item_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            FamDel_ID = data['FamDel_ID'];

                            $('#edit2_Com_MasterID').val(data['Com_MasterID']).change();
                            $('#edit2_relationshipID').val(data['relationshipID']).change();
                            $('#edit2_FamMemAddedDate').val(data['FamMemAddedDate']);

                            if((data['isMove']) == '1'){
                                document.getElementById('isMoveSt2b').checked=false;
                                document.getElementById('moveSt2').value='1';
                            }else{

                                document.getElementById('isMoveSt2b').checked=true;
                                document.getElementById('moveSt2').value='0';
                            }

                            $("#edit_femilyMem_model").modal({backdrop: "static"});
                            stopLoad();
                            //refreshNotifications(true);
                        }, error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                        }
                    });
                });


            var editFamMasID = document.getElementById('FamMasterID').value;
            var edit_femMm = document.getElementById('edit_femMm').value;

            $.ajax({

                async : true,
                type: 'POST',
                url :"<?php echo site_url('CommunityNgo/get_memMoveState'); ?>",
                data: {'editFamMasID': FamMasterID,'edit_femMm':id},
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,

                success: function (data) {

                    if(data.checkIsLead){

                        document.getElementById("isMoveSt2b").disabled = true;

                    }
                    else{
                        if(data.checkIsIn){

                            document.getElementById("isMoveSt2b").disabled = true;

                        }
                        else{

                            if(data.checkIsOnce){
                                document.getElementById("isMoveSt2b").disabled = false;

                            }
                            else {
                                document.getElementById("isMoveSt2b").disabled = true;
                            }
                        }
                    }

                }
            });

        }
    }

    function isItMove(){

        var f=document.getElementById('isMoveSt2b').checked;

        if(f==true) {
            document.getElementById('moveSt2').value = 0;
        }if(f==false){
            document.getElementById('moveSt2').value = 1;
        }

    }

    //add new community
    function comMem_detail_modal() {
        if (FamMasterID) {
            $("#gl_code").val(null).trigger("change");
            $('#comMem_detail_form')[0].reset();
            $("#comMem_detail_modal").modal({backdrop: "static"});
            $('#comMem_add_table tbody tr').not(':first').remove();
            $('.f_search').closest('tr').css("background-color", 'white');
        }
    }

    function add_more_communityMem() {
        $('select.select2').select2('destroy');
        var appendData = $('#comMem_add_table tbody tr:first').clone();

        appendData.find('input,select,textarea').val('');

        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $("#comMem_add_table").append(appendData);
        $(".select2").select2();
        number_validation();
        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
        });
    }

    /*new Relatn category*/
    $('#add-Relatn').click(function () {
        $('#add-mem-Relatn').val('');
        $('#Relatn-modal').modal({backdrop: 'static'});
    });

    $('#Relatn-btn').click(function (e) {

        e.preventDefault();
        var Relatn = $.trim($('#add-mem-Relatn').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'Relatn': Relatn},
            url: '<?php echo site_url("CommunityNgo/new_RelationCat"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                var mem_Relatn = $('#edit2_relationshipID');
                if (data[0] == 's') {
                    mem_Relatn.append('<option value="' + data[2] + '">' + Relatn + '</option>');
                    mem_Relatn.val(data[2]);
                    $('#Relatn-modal').modal('hide');
                }


            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    });

    function saveCommunityMem() {

        var data = $('#comMem_detail_form').serializeArray();

      data.push({'name': 'FamMasterID', 'value': FamMasterID});

      data.push({'name': 'FamilyAddedDate', 'value': FamilyAddedDate});
        /*    data.push({'name': 'receiptVoucherDetailAutoID', 'value': receiptVoucherDetailAutoID});*/

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('CommunityNgo/save_communityMem_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                /*receiptVoucherDetailAutoID = null;*/
                refreshNotifications(true);
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    setTimeout(function () {
                        getDispatchDetailAddonCost_tableView(FamMasterID);
                        familyMaster_exist();
                    }, 300);
                    $('#comMem_detail_modal').modal('hide');
                    $('#comMem_detail_form')[0].reset();
                    $('.select2').select2('')
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    //FEMILY MEM ADDING
    function gt_femMemAdding_modal() {
        if (FamMasterID) {
            $("#gl_code").val(null).trigger("change");
            $('#femMemAdding_form')[0].reset();
            $("#femMemAdding_modal").modal({backdrop: "static"});
            $('.f_search').closest('tr').css("background-color", 'white');
           $("input[name='FamMemAddedDate']").val($('#commitmentFamMemAddedDate').val());
        }
    }

    function saveFemMemDetails() {
        var data = $('#femMemAdding_form').serializeArray();

        data.push({'name': 'FamMasterID', 'value': FamMasterID});

        data.push({'name': 'FamilyAddedDate', 'value': FamilyAddedDate});

        /*    data.push({'name': 'receiptVoucherDetailAutoID', 'value': receiptVoucherDetailAutoID});*/

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('CommunityNgo/save_famMembers_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                /*receiptVoucherDetailAutoID = null;*/
                refreshNotifications(true);
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {

                    var mem_search = $('#search');
                    if (data[0] == 's') {
                        setTimeout(function () {
                            getDispatchDetailAddonCost_tableView(FamMasterID);
                          //  familyMaster_exist();
                        }, 300);
                        mem_search.append('<option value="' + data[2] + '"></option>');
                        mem_search.val(data[2]);

                        document.getElementById('PD_modalRelatnFrm').style.display = 'none';
                        document.getElementById('PD_modalMainFm').style.display = 'block';

                        $('.comRltn3').hide(); // hides
                        $('.comRltn4').hide(); // hides
                        $('.comRltn2').show(); // shows
                        $('.comRltn1').show(); // shows

                        var obj = document.getElementById('search_to');
                        for (var i=0; i < obj.options.length; i++) {
                            obj.options[i] = null;
                        }

                        $('#femMemAdding_modal').modal('hide');
                        $('#femMemAdding_form')[0].reset();

                        fetch_familyMemAct_drop();

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


    function load_ngoFamilyHeader() {
        if (FamMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'FamMasterID': FamMasterID},
                url: "<?php echo site_url('CommunityNgo/load_ngoFamilyHeader'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        FamMasterID = data['FamMasterID'];

                        document.getElementById('FamMasterID').value = FamMasterID;
                        document.getElementById('FamMasterID2').value = FamMasterID;
                        document.getElementById('LeaderID2').value = data['LeaderID'];

                        $('#FamilyAddedDate').val(data['FamilyAddedDate']);
                        $('#commitmentFamMemAddedDate').val(data['commitmentFamMemAddedDate']);
                        $('#FamilyName').val(data['FamilyName']);
                        $("#LeaderID").val(data['LeaderID']).change();
                        $('#FamAncestory').val(data['FamAncestory']).change();

                        $('#LedgerNo').val(data['LedgerNo']);

                        $("#AncestryCatID").val(data['AncestryCatID']).change();
                        $("#ComEconSteID").val(data['ComEconSteID']).change();
                        $('#monthlyExpenses').val(data['monthlyExpenses']);
                        $('#femExpensesRemark').val(data['femExpensesRemark']);
                        $("#femHelpNeedId").val(data['femHelpNeedId']).change();
                        $('#femNeededHelp').val(data['femNeededHelp']);
                       /* $('#FamHgender').val(data['FamHgender']).change();
                        $('#FamHaddress').val(data['FamHaddress']).change();*/

                        /* getDispatchDetailItem_tableView(data['FamMasterID']);
                         getDispatchDetailAddonCost_tableView(data['FamMasterID']);
                         load_confirmation();*/
                        $('[href=#step3]').tab('show');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step3]').removeClass('btn-default');
                        $('[href=#step3]').addClass('btn-primary');
                        $('#save_btn').html('<?php echo $this->lang->line('common_update');?>');
                        /*Update*/
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

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }


    function delete_familyMemDetails(id, type) {
        if (FamMasterID) {
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
                        data: {'FamDel_ID': id},
                        url: "<?php echo site_url('CommunityNgo/delete_familyMemDetail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (type == 1) {

                            } else {
                                getDispatchDetailAddonCost_tableView(FamMasterID);

                            }
                            familyMaster_exist();


                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function getDispatchDetailAddonCost_tableView(FamMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {FamMasterID: FamMasterID},
            url: "<?php echo site_url('CommunityNgo/load_famMembers_details_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#dispatchDetial_addonCost').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function save_draft() {
        if (FamMasterID) {
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
                    fetchPage('system/communityNgo/ngo_mo_familyMaster', '', 'Community Family');
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

    function load_confirmation() {
        if (FamMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'FamMasterID': FamMasterID, 'html': true},
                url: "<?php echo site_url('CommunityNgo/load_community_family_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#confirm_body').html(data);
                    /*attachment_modal_dispatchNote(dispatchAutoID, "Dispatch Note", "DPN");*/
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function confirmation() {
        if (FamMasterID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "<?php echo $this->lang->line('CommunityNgo_you_want_to_confirm_this_sub');?>", /*You want confirm this document!*/
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
                        data: {'FamMasterID': FamMasterID},
                        url: "<?php echo site_url('CommunityNgo/familyCreate_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {

                            stopLoad();
                            if (data[0] == 'e') {
                                myAlert(data[0], data[1]);
                            }
                            else if (data[0] == 'w') {
                                myAlert(data[0], data[1]);
                            }else {
                                myAlert(data[0], data[1]);
                                fetchPage('system/communityNgo/ngo_mo_familyMaster', '', 'Family Master');
                                refreshNotifications(true);
                            }

                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function validatetb_row(det) {
        if (det.value > 0) {
            $(det).closest('tr').css("background-color", 'white');
        }
    }
</script>
