<?php
$primaryLanguage = getPrimaryLanguage();

$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], FALSE);
$this->load->helper('community_ngo_helper');

$date_format_policy = date_format_policy();
$current_date = current_format_date();
$getAll_title = load_titles();
$gender_drop = fetch_com_gender();
$countryCode_arr = all_country_codes();
$countries_arr = load_all_countries();
$default_data = load_default_data();
$marital_status = drop_maritalstatus();
$schools = load_ngoSchools();
echo selectOnTab();

?>

<?php echo other_attachments(); ?>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">

    <style>

        .title {
            float: left;
            width: 170px;
            text-align: right;
            font-size: 13px;
            color: #7b7676;
            padding: 4px 10px 0 0;
        }

    </style>
    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary" href="#step1" data-toggle="tab">
            <?php echo $this->lang->line('CommunityNgo_step_one'); ?><!--Step 1 -->-
            <?php echo $this->lang->line('communityngo_com_member_header'); ?><!-- Header--></a>

        <a class="btn btn-default btn-wizard" href="#step2" onclick="load_memberOtherDetails();" data-toggle="tab">
            <?php echo $this->lang->line('CommunityNgo_step_two'); ?><!--Step 2--> -
            <?php echo $this->lang->line('communityngo_com_member_header_Other'); ?><!--Other--></a>

        <a class="btn btn-default btn-wizard" href="#step3"
           onclick="load_memberStatusDetails();load_memberStatus_attachments();" data-toggle="tab">
            <?php echo $this->lang->line('CommunityNgo_step_three'); ?><!--Step 3--> -
            <?php echo $this->lang->line('communityngo_com_member_header_Status'); ?><!--Status--></a>
    </div>

    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="CommunityMaster_Form"'); ?>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>
                            <?php echo $this->lang->line('communityngo_com_member_header_Profile'); ?><!--MEMBER DETAIL HEADER--></h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_title'); ?><!--Title--></label>
                        </div>

                        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                                <?php echo form_dropdown('TitleID', $getAll_title, '',
                                                    'class="form-control select2" id="TitleID" '); ?>
                                <span class="input-req-inner"></span></span>
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_name'); ?><!--Name--></label>
                        </div>

                        <div class="form-group col-sm-4">
                                    <span class="input-req" title="Required Field"><input type="text" name="CFullName"
                                                                                          id="CFullName"
                                                                                          class="form-control"
                                                                                          placeholder="<?php echo $this->lang->line('communityngo_name'); ?>"
                                                                                          required><span
                                            class="input-req-inner"></span></span><!--Name-->
                            <input type="hidden" name="Com_MasterID" id="Com_MasterID_edit">
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_name_with_initial'); ?><!--Name_with_initial--></label>

                        </div>
                        <div class="form-group col-sm-4">
                                        <span class="input-req" title="Required Field"><input type="text"
                                                                                              name="CName_with_initials"
                                                                                              id="CName_with_initials"
                                                                                              class="form-control"
                                                                                              placeholder="<?php echo $this->lang->line('communityngo_name_with_initial'); ?>"
                                                                                              required><span
                                                class="input-req-inner"></span></span><!--Name_with_initial-->
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_memberOtherName'); ?><!--Other Name--></label>

                        </div>
                        <div class="form-group col-sm-4"><input type="text" name="OtherName" id="OtherName"
                                                                class="form-control"
                                                                placeholder="<?php echo $this->lang->line('communityngo_memberOtherName'); ?>">
                        </div>

                    </div>

                    <div class="row" style="margin-top: 10px;">

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_nic'); ?><!--NIC NO--><label>
                        </div>
                        <div class="form-group col-sm-4">
                            <input type="text" name="CNIC_No" onblur="get_DOB(this);check_isNIC_available(this);"
                                   maxlength="12"
                                   placeholder="<?php echo $this->lang->line('communityngo_nic'); ?>"
                                   value="" id="CNIC_No" class="form-control">
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_gender'); ?><!--Gender--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('GenderID', $gender_drop, '',
                                    'class="form-control select2" id="GenderID" '); ?>
                                <span class="input-req-inner"></span></span>
                        </div>

                    </div>
                    <div class="row" style="margin-top: 10px;">

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_dob'); ?><!--Date of Birth--></label>
                        </div>
                        <div class="form-group col-sm-2">
                            <span class="input-req" title="Required Field">
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="CDOB" onblur="calculateAge();"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="" id="CDOB" class="form-control" required>
                                </div>
                            <span class="input-req-inner" style="z-index: 100"></span></span>
                        </div>
                        <div class="form-group col-sm-2" style="padding-left: 0px;">
                            <input type="text" name="Age"
                                   value="" id="Age" class="form-control" disabled>
                            <input type="text" name="Age_hidden"
                                   value="" id="Age_hidden" class="form-control" style="display: none">
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_bloodGroup'); ?><!--Blood Group--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <select id="BloodGroupID" class="form-control select2"
                                    data-placeholder="<?php echo $this->lang->line('communityngo_bloodGroup'); ?>"
                                    name="BloodGroupID">
                                <option value=""></option>
                                <?php

                                $bg_drop = load_bloodGroup();
                                if (!empty($bg_drop)) {
                                    foreach ($bg_drop as $val) {
                                        ?>
                                        <option
                                            value="<?php echo $val['BloodTypeID'] ?>"><?php echo $val['BloodDescription'] ?></option>
                                        <?php

                                    }
                                }
                                ?>
                            </select>
                        </div>

                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_status'); ?><!--Marital Status :--></label>
                        </div>

                        <div class="form-group col-sm-4">
                            <select id="CurrentStatus" class="form-control select2"
                                    name="CurrentStatus"
                                    data-placeholder="<?php echo $this->lang->line('communityngo_status'); ?>">
                                <option value=""></option>
                                <?php
                                if (!empty($marital_status)) {
                                    foreach ($marital_status as $val) {
                                        ?>
                                        <option
                                            value="<?php echo $val['maritalstatusID'] ?>"><?php echo $val['maritalstatus'] ?></option>
                                        <?php

                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_email'); ?><!--EMAIL--><label>
                        </div>
                        <div class="form-group col-sm-4">
                            <input type="email" name="EmailID"
                                   placeholder="<?php echo $this->lang->line('communityngo_email'); ?>"
                                   value="" id="EmailID" class="form-control">
                        </div>

                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_TP_MobileNo'); ?><!--MOBILE NO--><label>
                        </div>

                        <div class="form-group col-sm-2">
                                     <span class="input-req" title="Required Field">
                                <?php

                                $company_id = current_companyID();
                                $where = "WHERE company_id = $company_id";
                                $data = $this->db->query("select * FROM srp_erp_company $where ")->row_array();

                                $countryid = $data['countryID'];
                                $filter = "WHERE countryID = $countryid ";
                                $Countrys = $this->db->query("SELECT * FROM srp_erp_countrymaster $filter")->row_array();

                                $countryid = $Countrys['countryCode'];

                                echo form_dropdown('CountryCodePrimary', $countryCode_arr, $countryid, 'class="form-control" id="CountryCodePrimary"'); ?>
                                         <span class="input-req-inner"></span></span>
                        </div>

                        <div class="form-group col-sm-2" style=" padding-left: 0px;">
                            <span class="input-req" title="Required Field">
                        <input type="text" name="TP_Mobile"
                               data-inputmask="'alias': '999-999 9999'"
                               id="TP_Mobile" class="form-control" required><span
                                    class="input-req-inner"></span></span>

                            <input type="hidden" name="AreaCodePrimary" id="AreaCodePrimary"
                                   class="form-control"
                                   placeholder="<?php echo $this->lang->line('communityngo_AreaCode'); ?>">
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_TP_Home'); ?><!--HOME TP NO--><label>
                        </div>

                        <div class="form-group col-sm-2">
                            <?php

                            $company_id = current_companyID();
                            $where = "WHERE company_id = $company_id";
                            $data = $this->db->query("select * FROM srp_erp_company $where ")->row_array();

                            $countryid = $data['countryID'];
                            $filter = "WHERE countryID = $countryid ";
                            $Countrys = $this->db->query("SELECT * FROM srp_erp_countrymaster $filter")->row_array();

                            $countryid = $Countrys['countryCode'];

                            echo form_dropdown('CountryCodeSecondary', $countryCode_arr, $countryid, 'class="form-control" id="CountryCodeSecondary"'); ?>
                        </div>

                        <div class="form-group col-sm-2" style="padding-left: 0px;">
                            <input type="text" name="TP_home"
                                   placeholder="<?php echo $this->lang->line('communityngo_TP_No'); ?>"
                                   id="TP_home" data-inputmask="'alias': '999-999 9999'" class="form-control">
                            <input type="hidden" name="AreaCodeSecondary" id="AreaCodeSecondary"
                                   class="form-control"
                                   placeholder="<?php echo $this->lang->line('communityngo_AreaCode'); ?>">
                        </div>
                    </div>


                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>
                            <?php echo $this->lang->line('communityngo_com_member_header_Address'); ?><!--CONTACT DETAIL HEADER--></h2>
                    </header>

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_Country'); ?><!--Country--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('countyID', $countries_arr, '', 'class="form-control select2 valueHelp disableHelp" onchange="loadcountry_Province(this.value)" id="countyID"'); ?>
                                <span class="input-req-inner"></span></span>
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_Province'); ?><!--Province--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <div id="div_load_province">
                                <select name="provinceID" class="form-control select2" id="provinceID"
                                        onchange="loadcountry_District(this.value)">
                                    <option value="" selected="selected">Select a Province</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_District'); ?><!--District --></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <div id="div_load_district">
                                <select name="districtID" class="form-control select2" id="districtID"
                                        onchange="loadcountry_districtDivision(this.value),loadcountry_jamiya_Division(this.value)">
                                    <option value="" selected="selected">Select a District</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_DistrictDivision'); ?><!--District Division--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <div id="div_load_districtDivision">
                                <select name="districtDivisionID" class="form-control select2" id="districtDivisionID"
                                        onchange="loadcountry_GSDivision(this.value),loadcountry_Division_Area(this.value)">
                                    <option value="" selected="selected">Select a District Division</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_JammiyaDivision'); ?><!--Jammiyah Division--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <!--<span class="input-req" title="Required Field">-->
                            <div id="div_load_jammiyahDivision">
                                <select name="jammiyahDivisionID" class="form-control select2" id="jammiyahDivisionID">
                                    <option value="" selected="selected">Select a Jammiyah Division</option>
                                </select>
                            </div>
                            <!--<span class="input-req-inner"></span></span>-->
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_GS_Division'); ?><!--GS Division--><label>
                        </div>
                        <div class="form-group col-sm-3">
                             <span class="input-req" title="Required Field">
                                 <div id="div_load_GS_Division">
                                <select name="GS_Division" class="form-control select2" id="GS_Division"
                                        onchange="fetchdivisioNo(this)">
                                    <option value="" selected="selected">Select a GS Division</option>
                                </select>
                            </div>
                                <span class="input-req-inner"></span></span>
                        </div>
                        <div class="form-group col-sm-1" style="padding-left: 0px;">
                        <span class="input-req" title="Required Field">
                        <input type="text" name="GS_No"
                               placeholder="<?php echo $this->lang->line('communityngo_GS_No'); ?>"
                               value="" id="GS_No" class="form-control" readonly><span
                                class="input-req-inner"></span></span>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_region'); ?><!--Area--><label>
                        </div>
                        <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                            <div id="div_load_area">
                                <select name="RegionID" class="form-control select2" id="RegionID">
                                    <option value="" selected="selected">Select a Area / Mahalla</option>
                                </select>
                            </div>

                                <span class="input-req-inner"></span></span>
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_houseNo'); ?><!--House No--><label>
                        </div>

                        <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                        <input type="text" name="HouseNo"
                               placeholder="<?php echo $this->lang->line('communityngo_houseNo'); ?>"
                               value="" id="HouseNo" class="form-control" required><span
                                class="input-req-inner"></span></span>
                        </div>

                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_contactAddress'); ?><!--Address--></label>
                        </div>

                        <div class="form-group col-sm-4">
                                    <span class="input-req" title="Required Field"><textarea class="form-control"
                                                                                             id="C_Address"
                                                                                             name="C_Address" rows="2"
                                                                                             required></textarea>
                                        <span class="input-req-inner"></span></span>
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_perAddress'); ?><!--Address--></label>
                        </div>

                        <div class="form-group col-sm-4"><textarea class="form-control" id="P_Address"
                                                                   name="P_Address" rows="2"></textarea>
                        </div>
                    </div>

                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>
                            <?php echo $this->lang->line('communityngo_com_member_header_Other'); ?><!--OTHER DETAIL HEADER--></h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_isAbroad'); ?><!--is abroad--></label>
                        </div>
                        <div class="form-group col-sm-1">
                            <select id="IsAbroad" class="form-control select2"
                                    name="IsAbroad" onchange="removeDisable();"
                                    data-placeholder="">
                                <option value=""></option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-3" style="padding-left: 0px;">
                            <select id="CountryOfResidentID" class="form-control select2"
                                    name="CountryOfResidentID"
                                    data-placeholder="<?php echo $this->lang->line('common_Country'); ?>" disabled>
                                <option value=""></option>
                                <?php
                                $country_drop = load_country();
                                if (!empty($country_drop)) {
                                    foreach ($country_drop as $val) {
                                        ?>
                                        <option
                                            value="<?php echo $val['countryID'] ?>"><?php echo $val['CountryDes'] ?></option>
                                        <?php

                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_IsSchoolCompleted'); ?><!--Is School Completed--></label>
                        </div>

                        <div class="form-group col-sm-1">
                            <select id="IsSchoolCompleted" class="form-control select2"
                                    name="IsSchoolCompleted" onchange="remove_school_disable()"
                                    data-placeholder="">
                                <option value=""></option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-1">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_School'); ?><!--School--></label>
                        </div>
                        <div class="form-group col-sm-2" style="padding-left: 0px;">
                            <input type="hidden" name="CompletedYear"
                                   placeholder="<?php echo $this->lang->line('communityngo_CompletedYear'); ?>"
                                   value="" id="CompletedYear" class="form-control" disabled>

                            <select id="SchoolAttended" class="form-control select2"
                                    data-placeholder="<?php echo $this->lang->line('communityngo_School'); ?>"
                                    name="SchoolAttended">
                                <option value=""></option>
                                <?php
                                if (!empty($schools)) {
                                    foreach ($schools as $val) {
                                        ?>
                                        <option
                                            value="<?php echo $val['schoolComID'] ?>"><?php echo $val['schoolComDes'] ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>


                    </div>

                    <div class="row" style="margin-top: 10px;">


                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_isConverted'); ?><!--IS CONVERTED--></label>
                        </div>
                        <div class="form-group col-sm-1">
                            <select id="isConverted" class="form-control select2"
                                    name="isConverted" onchange="removeDisableYear();"
                                    data-placeholder="">
                                <option value=""></option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-1">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_Year'); ?><!--Year--></label>
                        </div>
                        <div class="form-group col-sm-2" style="padding-left: 0px;">
                            <input type="number" name="ConvertedYear"
                                   placeholder="<?php echo $this->lang->line('communityngo_ConvertedYear'); ?>"
                                   value="" id="ConvertedYear" class="form-control" disabled>
                        </div>
                        <div class="form-group col-sm-2" style="padding-left: 0px;">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_converted_place'); ?><!-- CONVERTED PLACE--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <input type="text" name="ConvertedPlace"
                                   placeholder="<?php echo $this->lang->line('communityngo_ConvertedPlace'); ?>"
                                   value="" id="ConvertedPlace" class="form-control" disabled>
                        </div>

                    </div>


                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">
                                <?php echo $this->lang->line('communityngo_isVoter'); ?><!--IS VOTER--></label>
                        </div>
                        <div class="form-group col-sm-4">
                            <select id="isVoter" class="form-control select2"
                                    name="isVoter"
                                    data-placeholder="">
                                <option value=""></option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>
                    </div>


                    <div class="row" style="margin-top: 11px;">
                        <div class="form-group col-sm-12">
                            <button class="btn btn-default pull-right next" style="margin-left: 1%; display: none;"
                                    id="nxtBtn">
                                <?php echo $this->lang->line('common_next'); ?><!--Next--></button>
                            <button id="save_btn" class="btn btn-primary pull-right" type="submit">
                                <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                        </div>

                    </div>
                </div>
            </div>
            </form>

        </div>

        <div id="step2" class="tab-pane">
            <div id="OtherDetails"></div>

            <div class="text-right m-t-xs">
                <button class="btn btn-default prev">
                    <?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
                <button class="btn btn-default pull-right next" style="margin-left: 1%;"
                        id="nxtBtn">
                    <?php echo $this->lang->line('common_next'); ?><!--Next--></button>
            </div>
        </div>

        <div id="step3" class="tab-pane">
            <?php echo form_open('', 'role="form" id="MemberStatus_Form"'); ?>
            <div id="StatusDetails">

                <div class="row">
                    <div class="form-group col-sm-3 col-xs-6">
                        <label for="isActive">&nbsp;</label>
                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <input type="checkbox" name="isActive" id="isActive" onchange="isActiveMove();" value="0"/>
                                            </span>
                            <input type="text" class="form-control" disabled=""
                                   value="<?php echo $this->lang->line('communityngo_isActive'); ?>"> <!--Is Active-->
                        </div>
                        <input class="form-control" type="number" name="isComActiveId" id="activeSt2" value="0"
                               style="display: none ;">
                    </div>
                </div>

                <div class="row">

                    <div class="col-sm-3">
                        <label for="communityngo_deactivatedFor">
                            <?php echo $this->lang->line('communityngo_deactivatedFor'); ?><!--Reason--></label>
                        <div class="form-group">
                            <select id="DeactivatedFor" class="form-control select2"
                                    name="DeactivatedFor" onchange="removeBtnDisable();"
                                    data-placeholder="" required>
                                <option value=""></option>
                                <option value="1">Death</option>
                                <option value="2">Migrate</option>
                            </select>
                        </div>

                    </div>
                    <div class="col-sm-3">
                        <div class="form-group ">
                            <label for="deactivatedDate" class="form-group">
                                <?php echo $this->lang->line('communityngo_deactivatedDate'); ?><!--Deactivated Date--></label>

                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="deactivatedDate" value="<?php echo $current_date; ?>"
                                       id="deactivatedDate" class="form-control" required onblur="removeBtnDisable();"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group">
                            <label for="dischargedComment">
                                <?php echo $this->lang->line('communityngo_deactivatedComment'); ?><!--Discharged Comment--></label>
                            <input type="text" name="deactivatedComment" id="deactivatedComment" class="form-control">
                        </div>
                    </div>
                </div>

            </div>
            </form>
            <br>

            <div class="row">
                <div class="zx-tab-pane" id="profile-v">
                    <div id="loadPageViewAttachment" class="col-md-8">
                        <div class="table-responsive">
                                            <span aria-hidden="true"
                                                  class="glyphicon glyphicon-hand-right color"></span>
                            &nbsp <strong>
                                <?php echo $this->lang->line('common_attachments'); ?><!--Attachments--></strong>
                            <br><br>

                            <div class="row">
                                <div class="col-md-2">&nbsp;</div>
                                <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="status_attachment_uplode_form" class="form-inline"'); ?>
                                        <div class="form-group">
                                <input type="text" class="form-control" id="status_attachmentDescription"
                                       name="status_attachmentDescription"
                                       placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                                            <!--Description-->
                                <input type="hidden" class="form-control" id="status_documentSystemCode"
                                       name="status_documentSystemCode">
                                <input type="hidden" class="form-control" id="status_documentID"
                                       name="status_documentID">
                                <input type="hidden" class="form-control" id="status_document_name"
                                       name="status_document_name">
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
                                  <a class="input-group-addon btn btn-default fileinput-exists" id="status_remove_id"
                                     data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                              </div>
                          </div>
                          <button type="button" class="btn btn-default" onclick="status_attachment_upload()"><span
                                  class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                                        </form></span>
                                </div>
                            </div>
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
                                <tbody id="status_attachment_modal_body" class="no-padding">
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

            <div class="text-right m-t-xs">
                <button class="btn btn-default prev" style="margin-right: 1%;">
                    <?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
                <button id="save_btn_status" class="btn btn-primary pull-right " type="submit"
                        onclick="save_memberStatus()" disabled>
                    <?php echo $this->lang->line('common_update'); ?><!--Save--></button>
            </div>
        </div>
    </div>


    <script>
        $('#save_btn').html('Save');
        var Com_MasterID;
        var country;
        var province;
        var district;
        var jammiyah_division;
        var district_division;
        var gs_division;
        var area;

        $(document).ready(function () {

            country = '<?php echo $default_data['country'] ?>';
            if (country) {
                $('#countyID').val(country).change();
            }

            province = '<?php echo $default_data['province'] ?>';
            if (province) {
                $('#provinceID').val(province).change();
            }

            district = '<?php echo $default_data['district'] ?>';
            if (district) {
                $('#districtID').val(district).change();
            }

            district_division = '<?php echo $default_data['DD'] ?>';
            if (district_division) {
                $('#districtDivisionID').val(district_division).change();
            }

            jammiyah_division = null;
            gs_division = null;
            area = null;
            Com_MasterID = null;

            var masterID = '<?php if (isset($_POST['data_arr']) && !empty($_POST['data_arr'])) {
                echo json_encode($_POST['data_arr']);
            } ?>';

            if (masterID != null && masterID.length > 0) {
                var masterIDNew = JSON.parse(masterID);
                $('.headerclose').click(function () {

                    fetchPage('system/communityNgo/ngo_mo_familyCreate', masterIDNew[0], 'Edit Family', 'NGO');

                });
            }
            else {
                $('.headerclose').click(function () {
                    fetchPage('system/communityNgo/ngo_hi_communityMaster', '', 'Community Members');
                });
            }

            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
                $('#CommunityMaster_Form').bootstrapValidator('revalidateField', 'CDOB');
                calculateAge();
            });


            $('.select2').select2();
            Inputmask().mask(document.querySelectorAll("input"));

            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                Com_MasterID = p_id;
                load_member();
                load_memberOtherDetails();
                load_memberStatusDetails();
                $('.btn-wizard').removeClass('disabled');
            } else {
                $('.btn-wizard').addClass('disabled');

            }

            $('#CommunityMaster_Form').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
                excluded: [':disabled'],
                fields: {

                    CDOB: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_dob_required');?>.'}}},
                    RegionID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_region_required');?>.'}}},
                    GenderID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_gender_required');?>.'}}},
                    TitleID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_title_required');?>.'}}},
                    HouseNo: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_HouseNo_required');?>.'}}},
                    GS_Division: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_GS_Division_required');?>.'}}},
                    countyID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_country_required');?>.'}}},
                    CountryCodePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('communityngo_CountryCodePrimary_required');?>.'}}},

                    TP_Mobile: {
                        validators: {
                            notEmpty: {
                                message: 'Phone No is required.'
                            },
                            regexp: {
                                message: 'The phone number can only contain the digits, spaces and -',
                                regexp: /^[0-9\s\-()+\.]+$/
                            }
                        }
                    },
                    TP_home: {
                        validators: {
                            regexp: {
                                message: 'The phone number can only contain the digits, spaces and -',
                                regexp: /^[0-9\s\-()+\.]+$/
                            }
                        }
                    }
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();

                data.push({'name': 'Com_MasterID', 'value': Com_MasterID});
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('CommunityNgo/save_communityMember'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            Com_MasterID = data[2];
                            $('#save_btn').html('Update');
                            $('#save_btn').html('Update');
                            $('.btn-wizard').removeClass('disabled');
                            $('[href=#step2]').tab('show');
                            load_memberOtherDetails();
                        } else {
                            $('.btn-primary').prop('disabled', false);
                        }

                        document.getElementById('nxtBtn').style.display = 'block';
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

        function removeDisable() {

            $("#CountryOfResidentID").val('').change();
            var dropdownvalue = $("select#IsAbroad option").filter(":selected").val();

            switch (dropdownvalue) {
                case '1':
                    $('#CountryOfResidentID').prop('disabled', false);
                    break;

                default:
                    $('#CountryOfResidentID').prop('disabled', true);
            }
        }

        function removeDisableYear() {

            $('#ConvertedYear').val('');
            $('#ConvertedPlace').val('');

            var dropdownvalue = $("select#isConverted option").filter(":selected").val();

            switch (dropdownvalue) {
                case '1':
                    $('#ConvertedYear').prop('disabled', false);
                    $('#ConvertedPlace').prop('disabled', false);
                    break;

                default:
                    $('#ConvertedYear').prop('disabled', true);
                    $('#ConvertedPlace').prop('disabled', true);
            }
        }
        function remove_school_disable() {

            $("#SchoolAttended").val('').change();

            var dropdownvalue = $("select#IsSchoolCompleted option").filter(":selected").val();

            switch (dropdownvalue) {
                case '1':
                    $("#SchoolAttended").attr( "disabled", "disabled" );

                    break;

                default:
                    $("#SchoolAttended").removeAttr( "disabled", "disabled" );
            }
        }

        function isActiveMove(){

            var f=document.getElementById('isActive').checked;

            if(f==true) {
                document.getElementById('activeSt2').value = 0;
            }if(f==false){
                document.getElementById('activeSt2').value = 1;
            }

        }

        function load_member() {
            if (Com_MasterID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'Com_MasterID': Com_MasterID},
                    url: "<?php echo site_url('CommunityNgo/load_member'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            Com_MasterID = data['Com_MasterID'];

                            $("#TitleID").val(data['TitleID']).change();
                            $('#CFullName').val(data['CFullName']);
                            $('#CName_with_initials').val(data['CName_with_initials']);
                            $('#OtherName').val(data['OtherName']);
                            $('#CNIC_No').val(data['CNIC_No']);
                            $('#CDOB').val(data['CDOB']);
                            $('#Age').val(data['Age']);
                            $('#Age_hidden').val(data['Age']);
                            $("#GenderID").val(data['GenderID']).change();
                            $("#BloodGroupID").val(data['BloodGroupID']).change();
                            $('#P_Address').val(data['P_Address']);
                            $('#C_Address').val(data['C_Address']);
                            $('#TP_Mobile').val(data['TP_Mobile']);
                            $('#AreaCodePrimary').val(data['AreaCodePrimary']);
                            $("#CountryCodePrimary").val(data['CountryCodePrimary']).change();
                            $('#TP_home').val(data['TP_home']);
                            $('#AreaCodeSecondary').val(data['AreaCodeSecondary']);
                            $("#CountryCodeSecondary").val(data['CountryCodeSecondary']).change();
                            $('#EmailID').val(data['EmailID']);
                            $('#GS_No').val(data['GS_No']);
                            $("#countyID").val(data['countyID']).change();

                            country = data['countyID'];
                            province = data['provinceID'];
                            district = data['districtID'];
                            jammiyah_division = data['jammiyahDivisionID'];
                            district_division = data['districtDivisionID'];
                            gs_division = data['GS_Division'];
                            area = data['RegionID'];

                            $('#HouseNo').val(data['HouseNo']);

                            $("#IsAbroad").val(data['IsAbroad']).change();
                            $("#CountryOfResidentID").val(data['CountryOfResidentID']).change();
                            $('#CurrentStatus').val(data['CurrentStatus']).change();
                            $("#IsSchoolCompleted").val(data['IsSchoolCompleted']).change();
                            $('#CompletedYear').val(data['CompletedYear']);
                            $('#SchoolAttended').val(data['SchoolAttended']).change();
                            $("#isVoter").val(data['isVoter']).change();
                            $("#isConverted").val(data['isConverted']).change();
                            $('#ConvertedYear').val(data['ConvertedYear']);
                            $('#ConvertedPlace').val(data['ConvertedPlace']);

                            $('#save_btn').html('<?php echo $this->lang->line('common_update');?>');
                            document.getElementById('nxtBtn').style.display = 'block';

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

        function load_memberOtherDetails() {

            if (Com_MasterID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {'Com_MasterID': Com_MasterID},
                    url: '<?php echo site_url("CommunityNgo/load_memberOtherDetails_View"); ?>',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();

                        $('#OtherDetails').html(data);
                        $('#lanTab').tab('show');
                        $('[href=#languageTab]').addClass('active');
                        $('.language').addClass('active');

                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        }

        function load_memberStatusDetails() {
            if (Com_MasterID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'Com_MasterID': Com_MasterID},
                    url: "<?php echo site_url('CommunityNgo/load_member'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {

                            if((data['isActive']) == '1'){
                                document.getElementById('activeSt2').value='1';
                            }else{
                                document.getElementById('activeSt2').value='0';
                            }
                            $('#isActive').val(data['isActive']);
                            var isActive = data['isActive'];
                            $('#isActive').prop('checked', (isActive == 0));

                            $('#deactivatedDate').val(data['deactivatedDate']);
                            $('#deactivatedComment').val(data['deactivatedComment']);
                            $("#DeactivatedFor").val(data['DeactivatedFor']).change();

                            document.getElementById('nxtBtn').style.display = 'block';

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

        function calculateAge() {

            var date = document.getElementById('CDOB').value; //dd-mm-yyyy

            if (date == null || date == '') {
                document.getElementById('Age').value = '';
                document.getElementById('Age_hidden').value = '';
            } else {

                var date_format_policy = '<?php echo($date_format_policy) ?>';

                switch (date_format_policy) {
                    case 'yyyy-mm-dd':
                        var p = date.split(/\D/g);
                        var D_Date = [p[2], p[1], p[0]].join("-");
                        break;

                    case 'dd-mm-yyyy':
                        D_Date = date;
                        break;

                    default:
                        D_Date = date;
                }

                var DOB = D_Date.split("-").reverse().join("-");

                var dd = new Date(DOB);
                var y = dd.getFullYear();
                var m = dd.getMonth() + 1;
                var day = dd.getDate();

                var d1 = new Date();
                var y1 = d1.getFullYear();
                var m1 = d1.getMonth() + 1; //note:0=jan ,1=feb....
                var day1 = d1.getDate();

                if (m1 == '04' || m1 == '06' || m1 == '09' || m1 == '11') {
                    var xm = 30;
                } else if (m1 == '01' || m1 == '03' || m1 == '05' || m1 == '07' || m1 == '08' || m1 == '10' || m1 == '12') {
                    var xm = 31;
                } else if (m1 == '02') {
                    var xm = 28;
                }

                if (day1 < day) {
                    if (m1 < m || m1 == m) {
                        mm1 = (m1 + 12) - 1;
                        yy1 = y1 - 1;
                        dayy1 = day1 + xm;
                    } else {
                        mm1 = m1 - 1;
                        yy1 = y1;
                        dayy1 = day1 + xm;
                    }
                } else if (day1 > day || day1 == day) {
                    if (m1 < m) {
                        mm1 = (m1 + 12);
                        yy1 = y1 - 1;
                        dayy1 = day1;
                    } else {
                        mm1 = m1;
                        yy1 = y1;
                        dayy1 = day1;
                    }
                }

                var yrs = yy1 - y;
                var mon = mm1 - m;
                var dy = dayy1 - day;
                if (yrs < 0) {
                    document.getElementById('Age').value = 'Incorrect Date Of Birth';
                } else {
                    // document.getElementById('Age').value = yrs + 'yrs,' + ' ' + mon + 'mths' + ' ' + '&' + ' ' + dy + 'days';
                    // document.getElementById('Age_hidden').value = yrs + 'yrs,' + ' ' + mon + 'mths' + ' ' + '&' + ' ' + dy + 'days';

                    document.getElementById('Age').value = yrs + 'yrs';
                    document.getElementById('Age_hidden').value = yrs + 'yrs';
                }
            }
        }

        function save_memberStatus() {

            swal(
                {
                    title: "Are you sure?",
                    text: "You want to in-active this member!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "In-active"
                },
                function () {

                    var postData = $('#MemberStatus_Form').serializeArray();
                    postData.push({'name': 'Com_MasterID', 'value': Com_MasterID});

                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: postData,
                        url: "<?php echo site_url('CommunityNgo/save_communityMemberStatus'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                Com_MasterID = data[2];
                                load_memberStatusDetails();
                            } else {
                            }
                        },
                        error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                }
            );
        }

        $("#isActive").click(function () {
            if (this.checked) {
                $('#isActive').val('0');

                document.getElementById('activeSt2').value = 0;

                var reason = document.getElementById('DeactivatedFor').value;
                var date = document.getElementById('deactivatedDate').value;

                if (reason == null || reason == '' || date == null || date == '') {
                    $('#save_btn_status').prop('disabled', true);
                } else {
                    $('#save_btn_status').prop('disabled', false);
                }

            } else {
                $('#isActive').val('1');
                document.getElementById('activeSt2').value = 1;
                $('#save_btn_status').prop('disabled', true);

            }
        });

        function removeBtnDisable() {

            var isActive = document.getElementById('isActive').value;
            var reason = document.getElementById('DeactivatedFor').value;
            var date = document.getElementById('deactivatedDate').value;

            if (isActive == null || isActive == 1 || reason == null || reason == '' || date == null || date == '') {
                $('#save_btn_status').prop('disabled', true);
            } else {
                $('#save_btn_status').prop('disabled', false);
            }

        }

        function fetchdivisioNo(division) {
            var GS_Div = $('#GS_Division').val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'division': GS_Div},
                url: "<?php echo site_url('CommunityNgo/get_gs_division_no'); ?>",
                success: function (data) {
                    $('#GS_No').val(data);
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }

        function get_DOB(NIC) {

            var NIC_No = NIC.value;
            var count = NIC_No.length;

            if (NIC_No) {
                if (count == 10) {
                    var myYear = NIC_No.substring(0, 2);
                    var Year = '19' + myYear.trim();
                    var MidThree = NIC_No.substring(2, 5);
                } else if (count == 12) {
                    myYear = NIC_No.substring(0, 4);
                    Year = myYear.trim();
                    MidThree = NIC_No.substring(4, 7);
                }

                if (MidThree) {
                    if (MidThree > 500) {
                        var Day = MidThree - 500;
                        var GenderID = 2; //Female
                    } else {
                        Day = MidThree;
                        GenderID = 1; //Male
                    }

                    if (Day > 0 && Day < 367) //Validating The Days
                    {
                        if (Day > 335) {
                            Day = Day - 335;
                            var Month = "12"; //December
                        }
                        else if (Day > 305) {
                            Day = Day - 305;
                            Month = "11"; //November
                        }
                        else if (Day > 274) {
                            Day = Day - 274;
                            Month = "10"; //October
                        }
                        else if (Day > 244) {
                            Day = Day - 244;
                            Month = "09"; //September
                        }
                        else if (Day > 213) {
                            Day = Day - 213;
                            Month = "08"; //Auguest
                        }
                        else if (Day > 182) {
                            Day = Day - 182;
                            Month = "07"; //July
                        }
                        else if (Day > 152) {
                            Day = Day - 152;
                            Month = "06"; //June
                        }
                        else if (Day > 121) {
                            Day = Day - 121;
                            Month = "05"; //May
                        }
                        else if (Day > 91) {
                            Day = Day - 91;
                            Month = "04"; //April
                        }
                        else if (Day > 60) {
                            Day = Day - 60;
                            Month = "03"; //March
                        }
                        else if (Day < 32) {
                            Month = "01"; //January
                        }
                        else if (Day > 31) {
                            Day = Day - 31;
                            Month = "02"; //Febuary
                        }
                    }

                    //onchange gender
                    $("#GenderID").val(GenderID).change();

                    var date_format_policy = '<?php echo($date_format_policy) ?>';

                    switch (date_format_policy) {
                        case 'yyyy-mm-dd':
                            var DOB = Year + '-' + Month + '-' + Day;
                            break;

                        case 'dd-mm-yyyy':
                            DOB = Day + '-' + Month + '-' + Year;
                            break;

                        default:
                            DOB = Year + '-' + Month + '-' + Day;
                    }

                    /*   var currentTime = new Date().toJSON().slice(0, 10);
                     var DOBNew = '19' + Year + '-' + Month + '-' + Day;

                     var date1 = new Date(currentTime);
                     var date2 = new Date(DOBNew);
                     var timeDiff = Math.abs(date2.getTime() - date1.getTime());
                     var diffDays = (Math.ceil(timeDiff / (1000 * 3600 * 24))) / 365;
                     alert(diffDays.toFixed(0));

                     var Age = ((currentTime - new Date(DOB)) / 365);*/

                    // change DOB
                    $('#CDOB').val(DOB);
                    $('#CommunityMaster_Form').bootstrapValidator('revalidateField', 'CDOB');
                    calculateAge();
                }
            } else {
                $("#GenderID").val('').change();
                $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#GenderID'));

                $('#CDOB').val('');
                $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#CDOB'));
                $('#Age').val('');
                $('#Age_hidden').val('');
            }
        }

        function check_isNIC_available(NIC) {
            var NIC_No = NIC.value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'NIC': NIC_No, 'Com_MasterID': Com_MasterID},
                url: "<?php echo site_url('CommunityNgo/check_isNIC_available'); ?>",

                success: function (data) {

                    if (data[0] == 'e') {
                        myAlert(data[0], data[1]);

                        $('#CNIC_No').val('');
                        $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#CNIC_No'));
                        $("#GenderID").val('').change();
                        $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#GenderID'));
                        $('#CDOB').val('');
                        $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#CDOB'));
                        $('#Age').val('');
                        $('#Age_hidden').val('');
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }


        function loadcountry_Province(countyID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {countyID: countyID},
                url: "<?php echo site_url('CommunityNgo/fetch_province_based_countryDropdown'); ?>",
                success: function (data) {
                    $('#provinceID').html(data);
                    $('#provinceID').val(province).change();
                    $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#provinceID'));

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_District(masterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('CommunityNgo/fetch_province_based_districtDropdown'); ?>",
                success: function (data) {
                    $('#districtID').html(data);
                    $('#districtID').val(district).change();
                    $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#districtID'));
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_districtDivision(masterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('CommunityNgo/fetch_district_based_districtDivisionDropdown'); ?>",
                success: function (data) {
                    $('#districtDivisionID').html(data);
                    $('#districtDivisionID').val(district_division).change();
                    $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#districtDivisionID'));
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_jamiya_Division(masterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('CommunityNgo/fetch_district_based_jammiyaDropdown'); ?>",
                success: function (data) {
                    $('#jammiyahDivisionID').html(data);
                    $('#jammiyahDivisionID').val(jammiyah_division).change();
                    $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#jammiyahDivisionID'));
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_GSDivision(masterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('CommunityNgo/fetch_division_based_GS_divisionDropdown'); ?>",
                success: function (data) {
                    $('#GS_Division').html(data);
                    $('#GS_Division').val(gs_division).change();
                    $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#GS_Division'));
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }


        function loadcountry_Division_Area(masterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('CommunityNgo/fetch_division_based_division_Area_Dropdown'); ?>",

                success: function (data) {
                    $('#RegionID').html(data);
                    $('#RegionID').val(area).change();
                    $('#CommunityMaster_Form').data('bootstrapValidator').resetField($('#RegionID'));
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        //attachments -Status
        function load_memberStatus_attachments() {

            $('#status_attachmentDescription').val('');
            $('#status_documentSystemCode').val(Com_MasterID);
            $('#status_document_name').val('Member Status Attachments');
            $('#status_documentID').val('8');
            $('#status_remove_id').click();

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {'Com_MasterID': Com_MasterID},
                url: "<?php echo site_url('CommunityNgo/load_memberStatus_attachments'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#status_attachment_modal_body').empty();
                    $('#status_attachment_modal_body').append('' + data + '');

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function status_attachment_upload() {
            var formData = new FormData($("#status_attachment_uplode_form")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('CommunityNgo/status_attachment_upload'); ?>",
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
                        load_memberStatus_attachments();
                        $('#status_remove_id').click();
                        $('#status_attachmentDescription').val('');
                    }
                },
                error: function (data) {
                    stopLoad();
                    swal("Cancelled", "No File Selected :)", "error");
                }
            });
            return false;
        }

        function delete_member_attachment(id, fileName) {
            swal({
                    title: "Are you sure?",
                    text: "You want to Delete!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes!"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'attachmentID': id, 'myFileName': fileName},
                        url: "<?php echo site_url('CommunityNgo/delete_member_attachment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                load_memberStatus_attachments();
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        },
                        error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        }
    </script>

<?php
/**
 * Created by PhpStorm.
 * User: Hishama
 * Date: 1/22/2018
 * Time: 2:07 PM
 */