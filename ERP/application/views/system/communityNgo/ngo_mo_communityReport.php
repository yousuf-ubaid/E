<?php
echo head_page('Community Member Reports', false);
$this->load->helper('community_ngo_helper');
$date_format_policy = date_format_policy();

$memOccutn = fetch_memOccupation();
$occupation = load_Jobcategories();
$ngoSchools = load_ngoSchools();
$grades = load_grades();
$language = load_language();
$com_area = report_load_region();
$comDivision = load_divisionForUploads();
$countries_arr = load_countries_compare();
$helpCategory = load_help_category();
$bothHelpMem= load_both_help_member();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

$title = $this->lang->line('communityngo_Occupation');
$titleTab2 = $this->lang->line('communityngo_Qualificatn');
$titleTab4 = $this->lang->line('communityngo_division');
$titleTab3 = $this->lang->line('communityngo_otherRprt');
$titleTab5 = $this->lang->line('communityNgo_helping');

?>
    <style>
        .bgc {
            background-color: #e1f1e1;
        }

        .infoComm-box {
            display: block;
            min-height: 12px;
            background: #fff;
            width: 160px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            margin-bottom: 15px;
        }
        .infoComm-box-icon {
            border-top-left-radius: 2px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 2px;
            display: block;
            float: left;
            height: 45px;
            width: 50px;
            text-align: center;
            font-size: 45px;
            line-height: 50px;
            background: rgba(0, 0, 0, 0.2);
        }
        .infoComm-box-content {
            padding: 5px 10px;
            margin-left: 50px;
        }

        .infoComm-box-text {
            display: block;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .infoComm-box-number {
            display: block;
            font-weight: bold;
            font-size: 14px;
        }
    </style>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
    <div id="filter-panel" class="collapse filter-panel">
    </div>

    <div class="nav-tabs-custom" style="margin-bottom: 0px; box-shadow: none;">
        <ul class="nav nav-tabs" style="border: 1px solid rgba(112, 107, 107, 0.21);">
            <li class="active">
                <a href="#approvelTab" data-toggle="tab" aria-expanded="true" onclick="switchComMemOccRprt();"><?php echo $title;?> </a>
            </li>
            <li class="">
                <a href="#qualificationAppTap" data-toggle="tab" aria-expanded="false" onclick="switchComMemQualRprt();"><?php echo $titleTab2;?></a>
            </li>
            <li class="">
                <a href="#divisionAppTap" data-toggle="tab" aria-expanded="false" onclick="switchComMemDiviRprt();"><?php echo $titleTab4;?></a>
            </li>
            <li class="">
                <a href="#helpRequirementsTap" data-toggle="tab" aria-expanded="false" onclick="switchComMemHelpRqRprt();"><?php echo $titleTab5;?></a>
            </li>
            <li class="">
                <a href="#otherAppTap" data-toggle="tab" aria-expanded="false" onclick="switchComMemOthrRprt();"><?php echo $titleTab3;?></a>
            </li>
        </ul>
        <div class="tab-content" style="border: 1px solid rgba(112, 107, 107, 0.21)">

            <input type="text" name="switchControlId" id="switchControlId" value="1" style="display:none;">
            <input type="text" name="switchHelpId" id="switchHelpId" value="" style="display:none;">


            <div class="tab-pane active disabled" id="approvelTab">

                <div class="table-responsive">

                    <?php echo form_open('login/loginSubmit', ' name="form_rpt_commMem" id="form_rpt_commMem" class="form-horizontal" role="form"'); ?>
                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <div class="col-md-12" id="occuMemTypeDiv">

                            <div class="form-group col-sm-3">
                                <label for="memberType" class="control-label">Occupation Type</label>
                                <select id="memberType" class="form-control select2" onchange="get_commMemDetails();"
                                        name="memberType" data-placeholder="Select Member Type" style="height:30px;width:180px;font-size: 13px;">
                                    <option value="">Select Member Type</option>
                                    <option value="-1">Select All</option>
                                    <option value="8">None</option>
                                    <?php
                                    $com_mem = fetch_ngo_memberType_drop();
                                    if (!empty($com_mem)) {
                                        foreach ($com_mem as $val) {
                                            ?>
                                            <option value="<?php echo $val['OccTypeID'] ?>"><?php echo $val['Description'] ?></option>
                                            <?php

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-3" id="jobDiv" style="display:none;">
                                <label for="JobCatId" class="control-label">Job Category</label>
                                <select class="form-control select2" id="JobCatId" name="JobCatId"
                                        style="height:30px;width: 180px;font-size: 13px;" data-placeholder="Select Job Category">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($occupation)) {
                                        foreach ($occupation as $val) {
                                            ?>
                                            <option
                                                value="<?php echo $val['JobCategoryID'] ?>"><?php echo $val['JobCatDescription'] ?></option>
                                            <?php

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-3" id="schlDiv" style="display:none;">
                                <label for="schlId" class="control-label">School</label>
                                <select class="form-control select2" id="schlId" name="schlId"
                                        style="height:30px;width:180px;font-size: 13px;" data-placeholder="Select School">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($ngoSchools)) {
                                        foreach ($ngoSchools as $val) {
                                            ?>
                                            <option
                                                value="<?php echo $val['schoolComID'] ?>"><?php echo $val['schoolComDes'] ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-2" id="MediumDiv" style="display:none;">

                                <label for="MedumId" class="control-label">Medium</label>
                                <select class="form-control select2" id="MedumId" name="MedumId"
                                        style="height:30px;width:120px;font-size: 13px;" data-placeholder="Select Medium">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($language)) {
                                        foreach ($language as $val) {
                                            ?>
                                            <option
                                                value="<?php echo $val['languageID'] ?>"><?php echo $val['description'] ?></option>
                                            <?php

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-2" id="classDiv" style="display:none;">

                                <label for="classId" class="control-label">Class</label>
                                <select class="form-control select2" id="classId" name="classId"
                                        style="height:30px;width:120px;font-size: 13px;" data-placeholder="Select Grade">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($grades)) {
                                        foreach ($grades as $val) {
                                            ?>
                                            <option
                                                value="<?php echo $val['gradeComID'] ?>"><?php echo $val['gradeComDes'] ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-3" id="placeDiv">
                                <label for="PlaceDes" class="control-label">Search</label>
                                <input name="PlaceDes" type="text" class="form-control input-sm" style="height:30px;width:200px;font-size: 13px;"
                                       placeholder="Search by all"
                                       id="PlaceDes"><!--Search by all-->
                            </div>
                            <div class="form-group col-sm-2">
                                <button type="button" class="btn btn-primary pull-right" onclick="generateReport()" name="filtersubmit" id="filtersubmit"><i class="fa fa-plus"></i> Generate
                                </button>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>

            <div class="tab-pane" id="qualificationAppTap">

                <div class="table-responsive">
                    <form method="post" name="form_rpt_memQual" id="form_rpt_memQual" class="form-horizontal">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <div class="col-md-12" id="qualMemTypeDiv">

                            <div class="form-group col-sm-3" id="qualMemDiv">
                                <label for="qualMemType" class="control-label">Qualification</label>
                                <select id="qualMemType" class="form-control select2" onchange="get_commMemQualDetails();"
                                        name="qualMemType" data-placeholder="Select Qualification" style="height:30px;width:180px;font-size: 13px;">
                                    <option value="-4" selected> All</option>
                                    <option value="-5"> None</option>
                                    <?php
                                    $com_qual = load_degree();
                                    if (!empty($com_qual)) {
                                        foreach ($com_qual as $val) {
                                            ?>
                                            <option value="<?php echo $val['DegreeID'] ?>"><?php echo $val['DegreeDescription'] ?></option>
                                            <?php

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-3" id="InstituteDiv">
                                <label for="InstituteId" class="control-label">Institute</label>
                                <select class="form-control select2" id="InstituteId" name="InstituteId" onchange="get_commMemQualDetails();"
                                        style="height:30px;width: 180px;font-size: 13px;" data-placeholder="Select Institute">
                                    <option value=""></option>
                                    <option value="">All</option>
                                    <?php
                                    $instituteQr = load_university();
                                    if (!empty($instituteQr)) {
                                        foreach ($instituteQr as $val) {
                                            ?>
                                            <option
                                                value="<?php echo $val['UniversityID'] ?>"><?php echo $val['UniversityDescription'] ?></option>
                                            <?php

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-3" id="qualSerchDiv">
                                <label for="qualSerch" class="control-label">Search</label>
                                <input name="qualSerch" type="text" class="form-control input-sm" style="height:30px;width:200px;font-size: 13px;"
                                       placeholder="Search by all"
                                       id="qualSerch"><!--Search by all-->
                            </div>
                            <div class="form-group col-sm-2">
                                <button type="button" class="btn btn-primary pull-right" onclick="generateQualReport()" name="filterQualsubmit" id="filterQualsubmit"><i class="fa fa-plus"></i> Generate
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <div class="tab-pane" id="divisionAppTap">

                <div class="table-responsive">
                    <form method="post" name="form_rpt_memDivi" id="form_rpt_memDivi" class="form-horizontal">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <div class="col-md-12" id="diviMemTypeDiv">
                            <div class="form-group col-sm-3" id="contrMemDiv">
                                <label for="countryId" class="control-label"><?php echo $this->lang->line('communityngo_Country'); ?><!--Country--></label>
                                <span class="input-req" title="Required Field">
                    <select id="countyID" name="countyID" class="form-control select2" onchange="loadRcountry_Province();" data-placeholder="Select Country">
                                    <option value=""></option>
                        <?php foreach ($countries_arr as $Val) { ?>
                            <option value="<?php echo $Val['countryID']; ?>" selected><?php echo $Val['CountryDes']; ?></option>
                        <?php } ?>
                                </select><span
                                        class="input-req-inner"></span></span>
                            </div>
                            <div class="form-group col-sm-3" id="proMemDiv">
                                <label for="provinceID" class="control-label"><?php echo $this->lang->line('communityngo_Province'); ?><!--Province--></label>
                                <select name="provinceID" class="form-control select2" id="provinceID"
                                        onchange="loadRcountry_District();">
                                    <option value="" selected="selected">Select a Province</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-3" id="disMemDiv">
                                <label for="districtID" class="control-label"><?php echo $this->lang->line('communityngo_District'); ?><!--District Division--></label>
                                <select name="districtID" class="form-control select2" id="districtID"
                                        onchange="loadRcountry_districtDivision();">
                                    <option value="" selected="selected">Select a District</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-3" id="disDivMemDiv">
                                <label for="districtDivisionID" class="control-label"><?php echo $this->lang->line('communityngo_DistrictDivision'); ?><!--District Division--></label>
                                <select name="districtDivisionID" class="form-control select2" id="districtDivisionID"
                                        onchange="loadRcountry_GSDivision(this.value);loadRcountry_Division_Area(this.value)">
                                    <option value="" selected="selected">Select a District Division</option>
                                </select>
                            </div>
                            </div>
                            <div class="col-md-12" id="divi2MemTypeDiv">
                            <div class="form-group col-sm-3" id="areaMemDiv">
                                <label for="areaMemId" class="control-label"><?php echo $this->lang->line('communityngo_region_temp2'); ?><!--Area--></label>

                                <select name="areaMemId" class="form-control select2" id="areaMemId">
                                    <option value="" selected="selected">Select a Area</option>
                                    <?php
                                    if (!empty($com_area)) {
                                        foreach ($com_area as $val) {
                                            ?>
                                            <option
                                                value="<?php echo $val['stateID'] ?>"><?php echo $val['Description'] ?></option>
                                            <?php

                                        }
                                    }
                                    ?>
                                </select>

                            </div>
                            <div class="form-group col-sm-3" id="gsDivitnDiv">
                                <label for="gsDivitnId" class="control-label"><?php echo $this->lang->line('communityngo_GS_Division'); ?><!--GS Division--></label>

                                <select name="gsDivitnId" class="form-control select2" id="gsDivitnId">
                                    <option value="" selected="selected">Select a GS Division</option>
                                    <?php
                                    if (!empty($comDivision)) {
                                        foreach ($comDivision as $val) {
                                            ?>
                                            <option
                                                value="<?php echo $val['stateID'] ?>"><?php echo $val['Description'] ?></option>
                                            <?php

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-1">

                            </div>
                            <div class="form-group col-sm-2">
                                <button type="button" class="btn btn-primary pull-centre" onclick="generateDiviReport()" name="filterDivisubmit" id="filterDivisubmit"><i class="fa fa-plus"></i> Generate
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div class="tab-pane" id="helpRequirementsTap">

                <div class="table-responsive">
                    <div class="row-fluid" id="applyDiv">
                        <div class="form-group contactDiv" style="border-color:#a6a6a6;">
                            <label class="checkbox-inline" style="font-weight: bold;color: #4c75a5;"> <input class="icheckbox_minimal-blue helpingDelCls" type="checkbox"
                                                                                                             name="stateHr" id="stateHr" value="Hr"><?php echo $this->lang->line('communityngo_memHelp_require') ;?></label>


                            <label class="checkbox-inline" style="font-weight: bold;color: #4c75a5;"> <input class="icheckbox_minimal-blue helpingDelCls" type="checkbox"
                                                                                                             name="stateWh" id="stateWh" value="Wh"><?php echo $this->lang->line('communityNgo_willing_to_help') ;?></label>

                            <label class="checkbox-inline" style="font-weight: bold;color: #4c75a5;"> <input class="icheckbox_minimal-blue helpingDelCls" type="checkbox"
                                                                                                             name="stateHW" id="stateHW" value="HW"><?php echo $this->lang->line('communityNgo_both') ;?></label>
                        </div>
                    </div>
                    <div style="display: none" id="memHelpReqDiv">
                    <form method="post" name="form_rpt_memHelpRq" id="form_rpt_memHelpRq" class="form-horizontal">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <div class="col-md-12" id="memHelpRqTypeDiv">

                            <div class="form-group col-sm-3" id="helpNeedMemDiv">
                                <label for="helpRqType" class="control-label"><?php echo $this->lang->line('communityngo_memHelp_type'); ?></label>
                                <select id="helpRqType" class="form-control select2"
                                        name="helpRqType" onchange="getMem_helpRqType_del();get_comMemHelpRqDetails();"
                                        data-placeholder="<?php echo $this->lang->line('communityngo_memHelp_type'); ?>" style="height:30px;width:180px;font-size: 13px;">
                                    <option value="-3" selected> All</option>
                                    <option value="1">Government Help</option>
                                    <option value="2">Private Help</option>
                                    <option value="3">Consultancy</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-3" id="InstituteDiv">
                                <label for="helpDelIds" class="control-label"><?php echo $this->lang->line('communityngo_memHelp_details'); ?></label>
                                <select class="form-control select2" id="helpDelIds" name="helpDelIds" onchange="get_comMemHelpRqDetails();"
                                        style="height:30px;width: 180px;font-size: 13px;" data-placeholder="Help In Detail">
                                    <option value="" selected="selected">Select a Help Details</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-3" id="helpRqSerchDiv">
                                <label for="helpRqSerch" class="control-label">Search</label>
                                <input name="helpRqSerch" type="text" class="form-control input-sm" style="height:30px;width:200px;font-size: 13px;"
                                       placeholder="Search by all"
                                       id="helpRqSerch"><!--Search by all-->
                            </div>
                            <div class="form-group col-sm-2">
                                <button type="button" class="btn btn-primary pull-right" onclick="generateHelpRqReport()" name="filterHlpRqSubmit" id="filterHlpRqSubmit"><i class="fa fa-plus"></i> Generate
                                </button>
                            </div>
                        </div>
                    </form>
                    </div>
                    <div style="display: none" id="memWillingToHelpDiv">
                        <form method="post" name="form_rpt_memHelpWilling" id="form_rpt_memHelpWilling" class="form-horizontal">
                            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                            <div class="col-md-12" id="memhelpCategoryDiv">

                                <div class="form-group col-sm-3" id="willingMemDiv">
                                    <label for="helpCategoryID" class="control-label"><?php echo $this->lang->line('communityNgo_willing_to_help_category'); ?></label>
                                    <select id="helpCategoryID" class="form-control select2"
                                            name="helpCategoryID" onchange="get_comMemWillingHpDetails();"
                                            data-placeholder="<?php echo $this->lang->line('communityNgo_willing_to_help_category'); ?>" style="height:30px;width:180px;font-size: 13px;">
                                        <option value="-2" selected> All</option>
                                        <?php
                                        if (!empty($helpCategory)) {
                                            foreach ($helpCategory as $val) {
                                                ?>
                                                <option
                                                    value="<?php echo $val['helpCategoryID'] ?>"><?php echo $val['helpCategoryDes'] ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3" id="helpRqSerchDiv">
                                    <label for="helpWillSerch" class="control-label">Search</label>
                                    <input name="helpWillSerch" type="text" class="form-control input-sm" style="height:30px;width:200px;font-size: 13px;"
                                           placeholder="Search by all"
                                           id="helpWillSerch"><!--Search by all-->
                                </div>
                                <div class="form-group col-sm-2">
                                    <button type="button" class="btn btn-primary pull-right" onclick="generateHelpWillReport()" name="filterHlpWillSubmit" id="filterHlpWillSubmit"><i class="fa fa-plus"></i> Generate
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div style="display: none" id="memHelpAndWillingDiv">
                        <form method="post" name="form_rpt_memHelpBoth" id="form_rpt_memHelpBoth" class="form-horizontal">
                            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                            <div class="col-md-12" id="memHelpRqWillingDiv">
                                <div class="form-group col-sm-3" id="bothHlpMemDiv">
                                    <label for="bothHelpRqID" class="control-label"><?php echo $this->lang->line('communityngo_name_of_members'); ?></label>
                                    <select id="bothHelpRqID" class="form-control select2"
                                            name="bothHelpRqID" onchange="get_comMemWillingHpDetails();"
                                            data-placeholder="<?php echo $this->lang->line('communityngo_select_member'); ?>" style="height:30px;width:180px;font-size: 13px;">
                                        <option value="-1" selected> All</option>
                                        <?php
                                        if (!empty($bothHelpMem)) {
                                            foreach ($bothHelpMem as $val) {
                                                ?>
                                                <option
                                                    value="<?php echo $val['Com_MasterID'] ?>"><?php echo $val['CName_with_initials'] ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-sm-3" id="bothHelpSerchDiv">
                                    <label for="bothHelpSerch" class="control-label">Search</label>
                                    <input name="bothHelpSerch" type="text" class="form-control input-sm" style="height:30px;width:200px;font-size: 13px;"
                                           placeholder="Search by all"
                                           id="bothHelpSerch"><!--Search by all-->
                                </div>
                                <div class="form-group col-sm-2">
                                    <button type="button" class="btn btn-primary pull-right" onclick="generateBothHelpReport()" name="filterHlpBothSubmit" id="filterHlpBothSubmit"><i class="fa fa-plus"></i> Generate
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="otherAppTap">
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>

                    <form method="post" name="form_rpt_memOtr" id="form_rpt_memOtr" class="form-horizontal">
                        <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
                        <div class="col-md-12" id="otrMemTypeDiv">

                            <div class="form-group col-sm-3" id="otrMemDiv">
                                <label for="genType" class="control-label">Gender</label>
                                <select id="genType" class="form-control select2" onchange="get_commMemOtrDetails();"
                                        name="genType" data-placeholder="Select Gender">
                                    <option value="-9" selected> All</option>
                                    <?php
                                    $com_gen = drop_gender();
                                    if (!empty($com_gen)) {
                                        foreach ($com_gen as $val) {
                                            ?>
                                            <option value="<?php echo $val['genderID'] ?>"><?php echo $val['name'] ?></option>
                                            <?php

                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-sm-2" id="abroadDiv">
                                <label for="IsAbroad" class="control-label" style="margin-left:20px;">Is Abroad</label>
                                <select class="form-control select2" id="IsAbroad" name="IsAbroad" onchange="get_commMemOtrDetails();"
                                        style="margin-left:20px;height:30px;width: 100px;font-size: 13px;" data-placeholder="Select Abroad">
                                    <option value="-7" selected>All</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>

                                </select>
                            </div>
                            <div class="form-group col-sm-2" id="convertDiv">
                                <label for="isConvertId" class="control-label" style="margin-left:20px;">Is converted</label>
                                <select class="form-control select2" id="isConvertId" name="isConvertId" onchange="get_commMemOtrDetails();"
                                        style="margin-left:20px;height:30px;width: 100px;font-size: 13px;" data-placeholder="Select Abroad">
                                    <option value="-8" selected>All</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>

                                </select>
                            </div>
                            <div class="form-group col-sm-2" id="bloodDiv">
                                <label for="bloodGrpID" class="control-label" style=""><?php echo $this->lang->line('communityngo_bloodGroup'); ?><!--Blood Group--></label>

                                <?php echo form_dropdown('bloodGrpID[]', fetch_BloodGrpsDes(false), '', 'multiple  class="form-control" id="bloodGrpID" required'); ?>

                            </div>
                            <div class="form-group col-sm-3" id="stateDiv">
                                <label for="stateId" class="control-label" style="margin-left:30px;">Marital Status</label>
                                <select class="form-control select2" id="stateId" name="stateId" style="margin-left:30px;" data-placeholder="Select Status">
                                    <option value="-6" selected>All</option>
                                    <?php
                                    $com_status = drop_maritalstatus();
                                    if (!empty($com_status)) {
                                        foreach ($com_status as $val) {
                                            ?>
                                            <option value="<?php echo $val['maritalstatusID'] ?>"><?php echo $val['maritalstatus'] ?></option>
                                            <?php

                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-12" id="otrMemTypeDiv2">
                            <div class="form-group col-sm-3" id="sickDiv">
                                <label for="sicknessID" class="control-label"><?php echo $this->lang->line('communityngo_CPermanent_sickness'); ?></label>
                                <?php echo form_dropdown('sicknessID[]', fetch_permanentSicknes(false), '', 'multiple  class="form-control" id="sicknessID" required'); ?>

                            </div>
                            <div class="form-group col-sm-3" id="ageOpDiv">
                                <label for="ageOpDiv" class="control-label">Age</label>
                                <select class="form-control select2" id="ageOps" name="ageOps" onchange="get_commMemOtrDetails();"
                                        style="height:30px;" data-placeholder="Select Operator">
                                    <option value="1" selected> = - Equal to</option>
                                    <option value="2"> < -Less than</option>
                                    <option value="3"> > -Greater than</option>
                                    <option value="4"> <= -Less than or equal to</option>
                                    <option value="5"> >= -Greater than or equal to</option>
                                    <option value="6"> Between </option>

                                </select>
                            </div>
                            <div class="form-group col-sm-2" id="ageFrmDiv">
                                <label for="ageFrmDiv" class="control-label">Age From</label>
                                <input name="ageFrm" type="number" class="form-control input-sm" style="font-size: 13px;"
                                       placeholder="Age"
                                       id="ageFrm"><!--Search by ageFrm-->
                            </div>
                            <div class="form-group col-sm-2" id="ageToDiv" style="display:none;">
                                <label for="ageToDiv" class="control-label">Age To</label>
                                <input name="ageTo" type="number" class="form-control input-sm" style="font-size: 13px;"
                                       placeholder="Age"
                                       id="ageTo"><!--Search by ageTo-->
                            </div>
                            <div class="form-group col-sm-2" style="float: right;">
                                <button type="button" style="float: right;" class="btn btn-primary pull-right" onclick="generateOtrReport()" name="filterOtrSubmit" id="filterOtrSubmit"><i class="fa fa-plus"></i> Generate
                                </button>
                            </div>
                        </div>
                    </form>
                </fieldset>
            </div>

            <hr style="margin: 0px;">
            <div id="div_comm_memDel">
            </div>
        </div>
    </div>



<?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script>

        var helpDelIDs;

        Inputmask().mask(document.querySelectorAll("input"));

        $('#bloodGrpID').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#bloodGrpID").multiselect2('selectAll', false);
        $("#bloodGrpID").multiselect2('updateButtonText');
       // $('.select2').select2();

        $('#sicknessID').multiselect2({
            includeSelectAllOption: true,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 200,
            numberDisplayed: 1
        });
        $("#sicknessID").multiselect2('selectAll', false);
        $("#sicknessID").multiselect2('updateButtonText');

        $('.headerclose').click(function () {
            fetchPage('system/communityNgo/ngo_mo_communityReport','','Community Member Report')
        });
        $(document).ready(function (e) {
          //  generateReport();
           //$('.select2').select2();

        });


        function get_commMemDetails() {

            var memberTypeID= document.getElementById('memberType').value;

            $("#PlaceDes").val("");
            $("#qualSerch").val("");
            $("#ageFrm").val("");
            $("#ageTo").val("");
            $("#helpRqSerch").val("");
            $("#helpWillSerch").val("");
            $("#bothHelpSerch").val("");
           // $("#divitnSerch").val("");

            if(memberTypeID =='-1' || memberTypeID =='8'){

               // $('#JobCatId').select2("val", "");
               // $('#classId').select2("val", "");

                document.getElementById('jobDiv').style.display = 'none';
                document.getElementById('schlDiv').style.display = 'none';
                document.getElementById('MediumDiv').style.display = 'none';
                document.getElementById('classDiv').style.display = 'none';
                document.getElementById('placeDiv').style.display = 'block';
            }
            else if(memberTypeID =='1'){

               // $('#JobCatId').select2("val", "");
                document.getElementById('jobDiv').style.display = 'none';
                document.getElementById('placeDiv').style.display = 'none';
                document.getElementById('schlDiv').style.display = 'block';
                document.getElementById('MediumDiv').style.display = 'block';
                document.getElementById('classDiv').style.display = 'block';
            }
            else{

                // $('#classId').select2("val", "");
                document.getElementById('schlDiv').style.display = 'none';
                document.getElementById('MediumDiv').style.display = 'none';
                document.getElementById('classDiv').style.display = 'none';
                document.getElementById('placeDiv').style.display = 'block';
                document.getElementById('jobDiv').style.display = 'block';
            }

        }

        function switchComMemOccRprt() {

            $("#div_comm_memDel").html('');
            $('#form_rpt_commMem')[0].reset();
            $('#form_rpt_memQual')[0].reset();
            $('#form_rpt_memOtr')[0].reset();
            $('#form_rpt_memHelpRq')[0].reset();
            $('#form_rpt_memHelpWilling')[0].reset();
            $('#form_rpt_memHelpBoth')[0].reset();
            $('#bloodGrpID').val('').change();
            $('#sicknessID').val('').change();
            $('#form_rpt_memDivi')[0].reset();

            document.getElementById('switchControlId').value = 1;
            document.getElementById('qualMemTypeDiv').style.display = 'none';
            document.getElementById('otrMemTypeDiv').style.display = 'none';
            document.getElementById('otrMemTypeDiv2').style.display = 'none';
            document.getElementById('diviMemTypeDiv').style.display = 'none';
            document.getElementById('divi2MemTypeDiv').style.display = 'none';
            document.getElementById('occuMemTypeDiv').style.display = 'block';
            document.getElementById('jobDiv').style.display = 'none';
            document.getElementById('schlDiv').style.display = 'none';
            document.getElementById('MediumDiv').style.display = 'none';
            document.getElementById('classDiv').style.display = 'none';
            document.getElementById('memWillingToHelpDiv').style.display = 'none';
            document.getElementById('memHelpAndWillingDiv').style.display = 'none';
            document.getElementById('memHelpReqDiv').style.display = 'none';
            document.getElementById('memhelpCategoryDiv').style.display = 'none';

            $("#PlaceDes").val("");
        }
        function switchComMemQualRprt() {

            $("#div_comm_memDel").html('');
            $('#form_rpt_commMem')[0].reset();
            $('#form_rpt_memQual')[0].reset();
            $('#form_rpt_memOtr')[0].reset();
            $('#form_rpt_memHelpRq')[0].reset();
            $('#form_rpt_memHelpWilling')[0].reset();
            $('#form_rpt_memHelpBoth')[0].reset();
            $('#bloodGrpID').val('').change();
            $('#sicknessID').val('').change();
            $('#form_rpt_memDivi')[0].reset();

            document.getElementById('switchControlId').value = 2;
            document.getElementById('occuMemTypeDiv').style.display = 'none';
            document.getElementById('otrMemTypeDiv').style.display = 'none';
            document.getElementById('otrMemTypeDiv2').style.display = 'none';
            document.getElementById('diviMemTypeDiv').style.display = 'none';
            document.getElementById('divi2MemTypeDiv').style.display = 'none';
            document.getElementById('qualMemTypeDiv').style.display = 'block';
            document.getElementById('jobDiv').style.display = 'none';
            document.getElementById('schlDiv').style.display = 'none';
            document.getElementById('MediumDiv').style.display = 'none';
            document.getElementById('classDiv').style.display = 'none';
            document.getElementById('memWillingToHelpDiv').style.display = 'none';
            document.getElementById('memHelpAndWillingDiv').style.display = 'none';
            document.getElementById('memHelpReqDiv').style.display = 'none';
            document.getElementById('memhelpCategoryDiv').style.display = 'none';

            $("#qualSerch").val("");

        }

        function switchComMemDiviRprt() {

            $("#div_comm_memDel").html('');
            $('#form_rpt_commMem')[0].reset();
            $('#form_rpt_memQual')[0].reset();
            $('#form_rpt_memOtr')[0].reset();
            $('#form_rpt_memHelpRq')[0].reset();
            $('#form_rpt_memHelpWilling')[0].reset();
            $('#form_rpt_memHelpBoth')[0].reset();
            $('#bloodGrpID').val('').change();
            $('#sicknessID').val('').change();
            $('#form_rpt_memDivi')[0].reset();

            document.getElementById('switchControlId').value = 4;
            document.getElementById('occuMemTypeDiv').style.display = 'none';
            document.getElementById('otrMemTypeDiv').style.display = 'none';
            document.getElementById('otrMemTypeDiv2').style.display = 'none';
            document.getElementById('qualMemTypeDiv').style.display = 'none';
            document.getElementById('diviMemTypeDiv').style.display = 'block';
            document.getElementById('divi2MemTypeDiv').style.display = 'block';
            document.getElementById('jobDiv').style.display = 'none';
            document.getElementById('schlDiv').style.display = 'none';
            document.getElementById('MediumDiv').style.display = 'none';
            document.getElementById('classDiv').style.display = 'none';
            document.getElementById('memWillingToHelpDiv').style.display = 'none';
            document.getElementById('memHelpAndWillingDiv').style.display = 'none';
            document.getElementById('memHelpReqDiv').style.display = 'none';
            document.getElementById('memhelpCategoryDiv').style.display = 'none';

            loadRcountry_Province();
          //  $("#divitnSerch").val("");

        }

        function switchComMemOthrRprt() {

            $("#div_comm_memDel").html('');
            $('#form_rpt_commMem')[0].reset();
            $('#form_rpt_memQual')[0].reset();
            $('#form_rpt_memOtr')[0].reset();
            $('#form_rpt_memHelpRq')[0].reset();
            $('#form_rpt_memHelpWilling')[0].reset();
            $('#form_rpt_memHelpBoth')[0].reset();
            $('#bloodGrpID').val('').change();
            $('#sicknessID').val('').change();
            $('#form_rpt_memDivi')[0].reset();


            document.getElementById('switchControlId').value = 3;
            document.getElementById('occuMemTypeDiv').style.display = 'none';
            document.getElementById('qualMemTypeDiv').style.display = 'none';
            document.getElementById('diviMemTypeDiv').style.display = 'none';
            document.getElementById('divi2MemTypeDiv').style.display = 'none';
            document.getElementById('otrMemTypeDiv').style.display = 'block';
            document.getElementById('otrMemTypeDiv2').style.display = 'block';
            document.getElementById('jobDiv').style.display = 'none';
            document.getElementById('schlDiv').style.display = 'none';
            document.getElementById('MediumDiv').style.display = 'none';
            document.getElementById('classDiv').style.display = 'none';
            document.getElementById('memWillingToHelpDiv').style.display = 'none';
            document.getElementById('memHelpAndWillingDiv').style.display = 'none';
            document.getElementById('memHelpReqDiv').style.display = 'none';
            document.getElementById('memhelpCategoryDiv').style.display = 'none';

            $("#ageFrm").val("");
            $("#ageTo").val("");
        }

        function switchComMemHelpRqRprt() {

            $("#div_comm_memDel").html('');
            $('#form_rpt_commMem')[0].reset();
            $('#form_rpt_memQual')[0].reset();
            $('#form_rpt_memOtr')[0].reset();
            $('#form_rpt_memHelpRq')[0].reset();
            $('#form_rpt_memHelpWilling')[0].reset();
            $('#form_rpt_memHelpBoth')[0].reset();
            $('#bloodGrpID').val('').change();
            $('#sicknessID').val('').change();
            $('#form_rpt_memDivi')[0].reset();

            document.getElementById('memWillingToHelpDiv').style.display = 'none';
            document.getElementById('memHelpAndWillingDiv').style.display = 'none';
            document.getElementById('memHelpReqDiv').style.display = 'none';
            $('input.helpingDelCls').prop('checked', false);

            document.getElementById('switchControlId').value = 5;
            document.getElementById('occuMemTypeDiv').style.display = 'none';
            document.getElementById('otrMemTypeDiv').style.display = 'none';
            document.getElementById('otrMemTypeDiv2').style.display = 'none';
            document.getElementById('diviMemTypeDiv').style.display = 'none';
            document.getElementById('divi2MemTypeDiv').style.display = 'none';
            document.getElementById('qualMemTypeDiv').style.display = 'none';
            document.getElementById('jobDiv').style.display = 'none';
            document.getElementById('schlDiv').style.display = 'none';
            document.getElementById('MediumDiv').style.display = 'none';
            document.getElementById('classDiv').style.display = 'none';

            $("#helpRqSerch").val("");
            $("#helpWillSerch").val("");
            $("#bothHelpSerch").val("");

        }

        $('input.helpingDelCls').on('change', function() {
            $('input.helpingDelCls').not(this).prop('checked', false);

            var stateHr=document.getElementById('stateHr').id;
            var stateWh=document.getElementById('stateWh').id;
            var stateHW=document.getElementById('stateHW').id;

            $("#div_comm_memDel").html('');
            $('#form_rpt_commMem')[0].reset();
            $('#form_rpt_memQual')[0].reset();
            $('#form_rpt_memOtr')[0].reset();
            $('#form_rpt_memHelpRq')[0].reset();
            $('#form_rpt_memHelpWilling')[0].reset();
            $('#form_rpt_memHelpBoth')[0].reset();
            $('#bloodGrpID').val('').change();
            $('#sicknessID').val('').change();
            $('#form_rpt_memDivi')[0].reset();

            if (document.getElementById(stateHr).checked == true) {

                document.getElementById('switchHelpId').value = 9;

                document.getElementById('memWillingToHelpDiv').style.display = 'none';
                document.getElementById('memHelpAndWillingDiv').style.display = 'none';
                document.getElementById('memHelpReqDiv').style.display = 'block';


            }
            else if (document.getElementById(stateWh).checked == true) {

                document.getElementById('switchHelpId').value = 8;

                document.getElementById('memHelpAndWillingDiv').style.display = 'none';
                document.getElementById('memHelpReqDiv').style.display = 'none';
                document.getElementById('memWillingToHelpDiv').style.display = 'block';


            }

            else  if (document.getElementById(stateHW).checked == true) {

                document.getElementById('switchHelpId').value = 7;

                document.getElementById('memWillingToHelpDiv').style.display = 'none';
                document.getElementById('memHelpReqDiv').style.display = 'none';
                document.getElementById('memHelpAndWillingDiv').style.display = 'block';

            }
            else {

                document.getElementById('switchHelpId').value = '';

                document.getElementById('memWillingToHelpDiv').style.display = 'none';
                document.getElementById('memHelpAndWillingDiv').style.display = 'none';
                document.getElementById('memHelpReqDiv').style.display = 'none';
            }

        });


        function generateReport() {

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_communityMem_status_report') ?>",
                data: $("#form_rpt_commMem").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_comm_memDel").html(data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        //member Qualification
        function get_commMemQualDetails() {

            var qualMemTypeID= document.getElementById('qualMemType').value;
            var InstituteId= document.getElementById('InstituteId').value;

            $("#PlaceDes").val("");
            $("#qualSerch").val("");
            $("#ageFrm").val("");
            $("#ageTo").val("");
            $("#helpRqSerch").val("");
            $("#helpWillSerch").val("");
            $("#bothHelpSerch").val("");
           // $("#divitnSerch").val("");

        }

        function generateQualReport() {

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_communityMem_qual_report') ?>",
                data: $("#form_rpt_memQual").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_comm_memDel").html(data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function get_commMemOtrDetails() {

            var ageOpsID= document.getElementById('ageOps').value;

            $("#PlaceDes").val("");
            $("#qualSerch").val("");
            $("#ageFrm").val("");
            $("#ageTo").val("");
            $("#helpRqSerch").val("");
            $("#helpWillSerch").val("");
            $("#bothHelpSerch").val("");
          //  $("#divitnSerch").val("");

        if(ageOpsID =='6'){
                document.getElementById('ageToDiv').style.display = 'block';
        }
        else {
            document.getElementById('ageToDiv').style.display = 'none';

        }

        }

        function generateOtrReport() {

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_communityMem_otrReport') ?>",
                data: $("#form_rpt_memOtr").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_comm_memDel").html(data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function get_comMemHelpRqDetails() {

            var helpRqType= document.getElementById('helpRqType').value;
            var helpDelIds= document.getElementById('helpDelIds').value;

            $("#PlaceDes").val("");
            $("#qualSerch").val("");
            $("#ageFrm").val("");
            $("#ageTo").val("");
            $("#helpRqSerch").val("");
            $("#helpWillSerch").val("");
            $("#bothHelpSerch").val("");
            // $("#divitnSerch").val("");
        }

        function get_comMemWillingHpDetails() {

            $("#PlaceDes").val("");
            $("#qualSerch").val("");
            $("#ageFrm").val("");
            $("#ageTo").val("");
            $("#helpRqSerch").val("");
            $("#helpWillSerch").val("");
            $("#bothHelpSerch").val("");
        }

        function get_commMemDiviDetails() {

            // $("#divitnSerch").val("");
            $("#PlaceDes").val("");
            $("#qualSerch").val("");
            $("#ageFrm").val("");
            $("#ageTo").val("");
            $("#helpRqSerch").val("");
            $("#helpWillSerch").val("");
            $("#bothHelpSerch").val("");

        }

        function generateDiviReport() {

            var countyID= document.getElementById('countyID').value;
            var provinceID= document.getElementById('provinceID').value;
            var districtID= document.getElementById('districtID').value;
            var districtDivisionID= document.getElementById('districtDivisionID').value;
            var areaMemId= document.getElementById('areaMemId').value;
            var gsDivitnId= document.getElementById('gsDivitnId').value;

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_communityMem_diviReport') ?>",
                data: $("#form_rpt_memDivi").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_comm_memDel").html(data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });

        }

        function generateReportPdf() {

            var switchControlId= document.getElementById('switchControlId').value;
            var switchHelpId= document.getElementById('switchHelpId').value;

            if(switchControlId == 1){

                var form = document.getElementById('form_rpt_commMem');
                form.target = '_blank';
                form.action = '<?php echo site_url('CommunityNgo/get_communityMem_status_report_pdf'); ?>';
                form.submit();
            }
            else if(switchControlId == 2){

                var form = document.getElementById('form_rpt_memQual');
                form.target = '_blank';
                form.action = '<?php echo site_url('CommunityNgo/get_communityMem_qual_report_pdf'); ?>';
                form.submit();
            }
            else if(switchControlId == 3){

                var form = document.getElementById('form_rpt_memOtr');
                form.target = '_blank';
                form.action = '<?php echo site_url('CommunityNgo/get_communityMem_otrReport_pdf'); ?>';
                form.submit();
            }
            else if(switchControlId == 4){

                var form = document.getElementById('form_rpt_memDivi');
                form.target = '_blank';
                form.action = '<?php echo site_url('CommunityNgo/get_communityMem_diviReport_pdf'); ?>';
                form.submit();
            }
            else if(switchControlId == 5 && switchHelpId=='9'){

                var form = document.getElementById('form_rpt_memHelpRq');
                form.target = '_blank';
                form.action = '<?php echo site_url('CommunityNgo/get_communityMem_helpRq_report_pdf'); ?>';
                form.submit();
            }
            else if(switchControlId == 5 && switchHelpId=='8'){

                var form = document.getElementById('form_rpt_memHelpWilling');
                form.target = '_blank';
                form.action = '<?php echo site_url('CommunityNgo/get_comMem_willingToHelp_report_pdf'); ?>';
                form.submit();
            }
            else if(switchControlId == 5 && switchHelpId=='7'){

                var form = document.getElementById('form_rpt_memHelpBoth');
                form.target = '_blank';
                form.action = '<?php echo site_url('CommunityNgo/get_comMem_bothHelp_report_pdf'); ?>';
                form.submit();
            }


        }

        function getMem_helpRqType_del() {

            var helpRqType = document.getElementById('helpRqType').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {memHelpType: helpRqType},
                url: "<?php echo site_url('CommunityNgo/fetch_helpType_delDropdown'); ?>",
                success: function (data) {
                    $('#helpDelIds').html(data);
                    $('#helpDelIds').val(helpDelIDs).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateHelpRqReport() {

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_communityMem_helpRq_report') ?>",
                data: $("#form_rpt_memHelpRq").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_comm_memDel").html(data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateHelpWillReport() {

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_comMem_willingToHelp_report') ?>",
                data: $("#form_rpt_memHelpWilling").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_comm_memDel").html(data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

        function generateBothHelpReport(){

            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/get_comMem_bothHelp_report') ?>",
                data: $("#form_rpt_memHelpBoth").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_comm_memDel").html(data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }

//area setup
        function loadRcountry_Province() {

            var countyID= document.getElementById('countyID').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {countyID: countyID},
                url: "<?php echo site_url('CommunityNgo/fetch_province_based_countryDropdown'); ?>",
                success: function (data) {
                    $('#provinceID').html(data);
                   // $('#provinceID').val(province).change();
                    loadRcountry_District();
                    $('#form_rpt_memDivi').data('bootstrapValidator').resetField($('#provinceID'));

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadRcountry_District() {

            var provinceID= document.getElementById('provinceID').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: provinceID},
                url: "<?php echo site_url('CommunityNgo/fetch_province_based_districtDropdown'); ?>",
                success: function (data) {
                    $('#districtID').html(data);
                  //  $('#districtID').val(district).change();
                    loadRcountry_districtDivision();

                    $('#form_rpt_memDivi').data('bootstrapValidator').resetField($('#districtID'));
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadRcountry_districtDivision() {

            var districtID= document.getElementById('districtID').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: districtID},
                url: "<?php echo site_url('CommunityNgo/fetch_district_based_districtDivisionDropdown'); ?>",
                success: function (data) {
                    $('#districtDivisionID').html(data);
                   // $('#districtDivisionID').val(district_division).change();
                    $('#form_rpt_memDivi').data('bootstrapValidator').resetField($('#districtDivisionID'));
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadRcountry_GSDivision(masterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('CommunityNgo/fetch_division_based_GS_divisionDropdown'); ?>",
                success: function (data) {
                    $('#gsDivitnId').html(data);
                   // $('#gsDivitnId').val(gs_division).change();
                    $('#form_rpt_memDivi').data('bootstrapValidator').resetField($('#gsDivitnId'));
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadRcountry_Division_Area(masterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('CommunityNgo/fetch_division_based_division_Area_Dropdown'); ?>",

                success: function (data) {
                    $('#areaMemId').html(data);
                   // $('#areaMemId').val(area).change();
                    $('#form_rpt_memDivi').data('bootstrapValidator').resetField($('#areaMemId'));
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
    </script>

<?php
