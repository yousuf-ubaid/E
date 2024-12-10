<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page_employee();
$empID = trim($this->input->post('page_id'));

$emp_title = fetch_emp_title();
$religion = fetch_emp_religion();
$Nationality = fetch_emp_nationality();
$maritalStatus = fetch_emp_maritialStatus();
$BloodGroup = fetch_emp_blood_type();
$counties = fetch_emp_countries();
$empCode = '';//empCodeGenerate();
$current_date = '';
$date_format_policy = date_format_policy();

$discharge = get_discharge_reasons();
$emiratesLang=getPolicyValues('LNG', 'All');



$isPendingDataAvailable = 0;
$isNeedApproval = getPolicyValues('EPD', 'All'); /** Check company policy on 'Approval for Employee Personal Detail Update' **/
if($isNeedApproval == 1 && !empty($empID)){
    $isPendingDataAvailable = ( !empty(get_pendingEmpApprovalData($empID)) ) ? 1 : 0;

    if($isPendingDataAvailable == 0){
        $isPendingDataAvailable = ( !empty(get_pendingFamilyApprovalData($empID, 'Y')) ) ? 1 : 0;
    }
}

$fromHiarachy = $this->input->post('policy_id');
$fromHiarachy = (empty($fromHiarachy))?0:$fromHiarachy;

$isAuthenticateNeed = 0;
if($fromHiarachy == 0){
    $isAuthenticateNeed = emp_master_authenticate(); /** Check company policy on 'Employee Master Edit Approval' **/
    $fromHiarachy = $isAuthenticateNeed;
    $isAuthenticateNeed = 1;
}

if($fromHiarachy == 1){ $isPendingDataAvailable = 0; }

$styleNotificationPending = 'style="color: #fff; ';
$styleNotificationPending .= ($isPendingDataAvailable == 1)? '"' : 'display:none"';



function language_string_conversion2($string){
    $outputString = strtolower(str_replace(array('-', ' ', '&', '/'), array('_', '_', '_', '_'), trim($string)));
    return $outputString;
}

$hide_Name_with_Initials = getPolicyValues('HNWI', 'All');

if($hide_Name_with_Initials == 1){
    $hideNameWithInitials = true;
}else{
    $hideNameWithInitials = false;
}
$rayLanguagePolicy = getPolicyValues('LNG', 'All');
$hrms_flow = getPolicyValues('HRFW', 'All');
if($hrms_flow == 'ASAAS'){
    $hrmsflow = true;
}else{
    $hrmsflow = false;
}

$advancedCostCapturing = getPolicyValues('ACC', 'All');
?>
<style>
    .scheduler-border legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 16px;
        font-weight: 500
    }

    fieldset.scheduler-border {
        border: 1px solid #ddd !important;
        padding: 10px 0px;
        -webkit-box-shadow: 0px 0px 0px 0px #000;
        box-shadow: 0px 0px 0px 0px #000;
        margin: 10px;
    }
</style>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/HR-plugins/styles.css'); ?>" class="employee_master_styles">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<?php $this->load->view('system/hrm/scroll-emp-master-styles-js'); ?>


<div class="row" id="master-div">
    <div class="col-sm-12">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 page-sidebar visible-lg">&nbsp;</div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 remove-margin employee-create-header">
            <div id="empFullName">
                <span id="empNameWithInitial"></span>
                <div class="box-tools pull-right" style="left: 35px;position: relative;">
                    <button class="btn btn-box-tool" <?php echo $styleNotificationPending; ?> id="notificationPending"
                            data-placement="bottom" title="Pending Personal Data Update" onclick="load_pendingData()">
                        <i class="fa fa-bell" aria-hidden="true"></i>
                    </button>
                    <button  id="" class="btn btn-box-tool headerclose navdisabl" style="color: #fff;"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div id="empDesignation"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12 page-sidebar remove-margin">
        <aside>
            <div class="white-container mb0">
                <div class="widget sidebar-widget jobs-search-widget" style="margin-top: -55px;">
                    <div class="visible-sm visible-xs" style="height: 30px;">&nbsp;</div>
                    <div class="widget-content">
                        <div id="employeePhoto" class="employeePhoto">
                            <img src="" width="170px" height="170px" id="changeImg" alt="" class="empPhoto">
                            <a title="Edit" id="edit-emp-img">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="widget sidebar-widget jobs-filter-widget" style="margin-top: 35px">
                    <div class="js-EmployeeInfoColumn">
                        <ul>
                            <li class="social">
                                <a href="mailto:" class="emailOnly email-display" title=""  style="color: #504d4d;">
                                    <i class="fa fa-envelope" aria-hidden="true"></i> .....
                                </a>
                            </li>
                        </ul>
                        <h4 class="baseColor divider" style="margin-bottom: 5px;"><?php echo $this->lang->line('common_joined_date');?><!--Joined Date--></h4>
                        <ul class="hireInfo">
                            <li>
                                <span id="joinDate-display" style="font-weight: 600; margin: 0px; color:#504d4d">.....</span>
                                <div id="period-display" style="font-size: 13px; margin: 0px; color:#504d4d">.....</div>
                            </li>
                        </ul>
                        <ul class="divider" style="color:#504d4d">
                            <i class="fa fa-gavel" aria-hidden="true"></i> <span id="employmentTypeDisplay">.....</span></li>
                        </ul>

                        <h4 class="baseColor divider" style="margin-bottom: 5px;">DOB<!--Date of Birth--></h4>
                        <ul class="dobInfo">
                            <li>
                                <span id="birthDay-display" style="font-weight: 600; margin: 0px; color:#504d4d">.....</span>
                                <div id="age-display" style="font-size: 13px; margin: 0px; color:#504d4d">.....</div>
                            </li>
                        </ul>

                        <h4 class="baseColor divider"><?php echo $this->lang->line('common_manager');?><!--Manager--></h4>
                        <ul class="manager">
                            <li class="notTruncate">
                                <img class="Avatar" width="26" height="26" id="managerImg" >
                                <div class="truncate">
                                    <a href="#" id="managerName">.....</a>
                                </div>
                                <div class="truncate" style="color:#504d4d" id="managerDesignation">.....</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12 remove-margin">
        <div class="row">
            <div class="col-md-12">
                <div class="" style="width: 100%">
                    <!--<div class="scroller">
                        <div class="scroller scroller-left"><i class="glyphicon glyphicon-chevron-left"></i></div>
                    </div>
                    <div class="scroller">
                        <div class="scroller scroller-right"><i class="glyphicon glyphicon-chevron-right"></i></div>
                    </div>-->
                    <div class="scroller scroller-left"><i class="glyphicon glyphicon-chevron-left"></i></div>
                    <div class="scroller scroller-right"><i class="glyphicon glyphicon-chevron-right"></i></div>
                    <div class="wrapper-scrolller card" id="employee-details">
                        <ul class="nav nav-tabs list" role="tablist">
                            <li role="presentation" class="active master-nav-li">
                                <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?php echo $this->lang->line('emp_personal_detail'); ?></a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#contact" aria-controls="contact" role="tab" data-toggle="tab"><?php echo $this->lang->line('emp_contact_detail'); ?></a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#employment_tab" aria-controls="employment_tab" role="tab" data-toggle="tab" onclick="fetch_employment()">
                                    <?php echo $this->lang->line('emp_employment'); ?>
                                </a>
                            </li>
                            <?php if($advancedCostCapturing == 1){ ?>
                            <li role="presentation" class="master-nav-li">
                                <a href="#reportingStructure_tab" aria-controls="reportingStructure_tab" role="tab" data-toggle="tab" onclick="fetch_reporting_structure()">
                                    <?php echo $this->lang->line('emp_reporting_stucture'); ?>
                                </a>
                            </li>
                            <?php } ?>
                            <li role="presentation" class="master-nav-li">
                                <a href="#family_tab" aria-controls="family_tab" role="tab" data-toggle="tab" onclick="fetch_family_details()">
                                    <?php echo $this->lang->line('emp_family_details'); ?><!--Family Details-->
                                </a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#medicine_tab" aria-controls="medicine_tab" role="tab" data-toggle="tab" onclick="fetch_medical_details()">
                                    Medical
                                </a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#salary_tab" aria-controls="salary_tab" role="tab" data-toggle="tab" onclick="fetch_salaryDet()">
                                    <?php echo $this->lang->line('emp_salary'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#accounts_tab" aria-controls="accounts_tab" role="tab" data-toggle="tab" onclick="fetch_accounts()">
                                    <?php echo $this->lang->line('emp_bank'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#qualification_tab" aria-controls="qualification_tab" role="tab" data-toggle="tab" onclick="fetch_qualification()">
                                    <?php echo $this->lang->line('emp_qualification'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#attendance_tab" aria-controls="attendance_tab" role="tab" data-toggle="tab" onclick="fetch_attendance()">
                                    <?php echo $this->lang->line('emp_attendance'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#si_tab" aria-controls="si_tab" role="tab" data-toggle="tab" onclick="fetch_social_insurance()">
                                    <?php echo $this->lang->line('emp_social_insurance'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#document_tab" aria-controls="document_tab" role="tab" data-toggle="tab" onclick="fetch_document()">
                                    <?php echo $this->lang->line('emp_document'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#assets_tab" aria-controls="assets_tab" role="tab" data-toggle="tab" onclick="fetch_employee_assets()">
                                    <?php echo $this->lang->line('emp_master_assets'); ?>
                                </a>
                            </li>
                            <li role="presentation" class="master-nav-li">
                                <a href="#discharged_tab" aria-controls="discharged_tab" role="tab" data-toggle="tab">
                                    <?php echo $this->lang->line('emp_discharged'); ?>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="profile">
                                <?php echo form_open('', 'role="form" id="employee_form" '); ?> <!-- autocomplete="off"-->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="form-group col-sm-3">
                                                <label for="shortName"><?php echo $this->lang->line('emp_employee_code'); ?></label>
                                                <input type="text" class="form-control" id="empCode" name="empCode"
                                                       value="<?php echo $empCode; ?>" readonly>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="shortName"><?php echo $this->lang->line('emp_secondary_code'); ?></label>
                                                <input type="text" class="form-control" id="EmpSecondaryCode" name="EmpSecondaryCode"
                                                       value="">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label
                                                    for="emp_title"><?php echo $this->lang->line('emp_title'); ?><?php required_mark(); ?></label>

                                                <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" id="add-title"
                                                            style="height: 29px; padding: 2px 10px;">
                                                        <i class="fa fa-plus" style="font-size: 11px"></i>
                                                    </button>
                                                </span>
                                                    <?php echo form_dropdown('emp_title', $emp_title, '', 'class="form-control" id="emp_title"'); ?>
                                                </div>
                                            </div>
                                            <?php if($hrms_flow != 'ASAAS'){ ?>
                                                <div class="form-group col-sm-3">
                                                    <label for="shortName">
                                                        <?php echo $this->lang->line('emp_calling_name'); ?><?php required_mark(); ?></label>
                                                    <input type="text" class="form-control" id="shortName" name="shortName">
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-sm-3">

                                                <?php if($hide_Name_with_Initials == 1 || $hrms_flow == 'ASAAS'){ ?>
                                                    <label for="fullName">First Name</label>
                                                    <div class="input-group" style="width: 100%; ">
                                                    <span class="input-group-btn" style="width:0px;"></span>
                                                    <input type="text" class="form-control input-sm" value="" id="Ename4" name="Ename4"
                                                           placeholder="<?php echo $this->lang->line('common_name'); ?>"/><!--Name-->
                                                    </div>
                                                <?php } 
                                                else{?>
                                                    <label for="fullName">
                                                    <?php echo $this->lang->line('emp_name_with_initials'); ?><?php required_mark(); ?></label>
                                                    <div class="input-group" style="width: 100%; ">
                                                    <input type="text" class="form-control input-sm" value="" id="initial" name="initial"
                                                           placeholder="<?php echo $this->lang->line('common_initial'); ?>" style="width: 50px"/><!--Initial-->
                                                    <span class="input-group-btn" style="width:0px;"></span>
                                                    <input type="text" class="form-control input-sm" value="" id="Ename4" name="Ename4"
                                                           placeholder="<?php echo $this->lang->line('common_name'); ?>"/><!--Name-->
                                                    </div>
                                                <?php } ?>

                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="fullName">
                                                    <?php echo $this->lang->line('emp_full_name'); ?><?php required_mark(); ?></label>
                                                <input type="text" class="form-control" id="fullName" name="fullName">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="fullName">
                                                    <?php echo $this->lang->line('emp_surname'); ?>
                                                </label>
                                                <input type="text" class="form-control" id="Ename3" name="Ename3">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="emp_gender"><?php echo $this->lang->line('emp_gender'); ?></label>

                                                <div class="form-control">
                                                    <label class="radio-inline">
                                                        <input type="radio" name="emp_gender" value="1" id="male" class="gender"
                                                               checked="checked"><?php echo $this->lang->line('common_male'); ?><!--Male-->
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="emp_gender" value="2" id="feMale" class="gender"><?php echo $this->lang->line('common_female'); ?><!--Female-->
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-sm-3">
                                        <label for="empDob"><?php echo $this->lang->line('emp_date_of_birth'); ?> </label>

                                        <div class="input-group datepic">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type='text' class="form-control" id="empDob" name="empDob"
                                                   value="<?php echo $current_date; ?>"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label for="Nationality"><?php echo $this->lang->line('emp_nationality'); ?></label>
                                        <?php echo form_dropdown('Nationality', $Nationality, '', 'class="form-control" id="Nationality" '); ?>
                                    </div>

                                    <div class="form-group col-sm-3">
                                        <label for="religion"><?php echo $this->lang->line('emp_religion'); ?></label>
                                        <?php

                                        //echo form_dropdown('religion', $religion, '', 'class="form-control" id="religion" ');
                                        echo '<select name="religion" class="form-control" id="religion">';
                                        if (!empty($religion)) {
                                            foreach ($religion as $key => $val) {

                                                $translation = $this->lang->line('religion_' . strtolower($val));
                                                // $translation = $this->lang->line('religion_select_the_religion' . strtolower($val));
                                                $output = language_string_conversion2('religion_' . $val);
                                                $translation = $this->lang->line($output);


                                                //*/$translation = $this->lang->line('religion_buddhism' );

                                                if (!empty(trim($translation))) {
                                                    $showDescription = $translation;
                                                } else {
                                                    $showDescription = $val;
                                                }
                                                // echo '<option value="' . $key . '">' . $showDescription . '</option>';
                                                echo '<option value="' . $key . '">' . $showDescription . '</option>';


                                            }
                                        }
                                        echo ' </select>';
                                        ?>


                                    </div>

                                   
                                </div>

                                <div class="row">

                                    <div class="form-group col-sm-3">
                                        <label for="MaritialStatus"><?php echo $this->lang->line('emp_marital_status'); ?></label>
                                        <?php echo form_dropdown('MaritialStatus', $maritalStatus, '', 'class="form-control" id="MaritialStatus" '); ?>
                                    </div>

                                    <?php if($hrms_flow != 'ASAAS'){ ?>
                                        <div class="form-group col-sm-3">
                                            <label for="religion"><?php echo $this->lang->line('emp_blood_group'); ?></label>
                                            <?php echo form_dropdown('BloodGroup', $BloodGroup, '', 'class="form-control" id="BloodGroup" '); ?>
                                        </div>
                                    <?php } ?>

                                    <div class="form-group col-sm-3">
                                        <?php if($emiratesLang == 'Default'){?>
                                            <label
                                            for="emp_email"><?php echo $this->lang->line('emp_primary_e-mail'); ?><?php required_mark(); ?></label>
                                        <?php }else{ ?>
                                            <label
                                            for="emp_email">Company Email<?php required_mark(); ?></label>
                                        <?php } ?>

                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                                            <input type="email" class="form-control " id="emp_email" name="emp_email">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <?php if($rayLanguagePolicy=='Ray'){ ?>
                                        <label for="empMachineID">Civil ID / ID No</label>
                                        <?php }else{ ?>
                                            <label for="empMachineID"><?php echo $this->lang->line('emp_national_id_no'); ?></label>
                                            <?php } ?>
                                        <input type="text" class="form-control" id="NIC" name="NIC">
                                    </div>


                                       <?php if($hrms_flow != 'ASAAS'){ ?>
                                        <div class="form-group col-sm-3" align="">
                                            <label for=""><?php echo $this->lang->line('emp_signature'); ?></label>

                                            <div class="fileinput-new thumbnail" style="width: 100%;height: 90px;">
                                                <img src="<?php echo base_url('images/No_Image.png'); ?>" id="changeSignatureImg">
                                                <input type="file" name="empSignatureImage" id="empSignatureImage" style="display: none;"
                                                    onchange="loadSigImage(this)"/>
                                            </div>

                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="row">

                                </div>

                                <hr>
                                <div class="text-right m-t-xs">
                                    <input type="file" name="empImage" id="empImage" style="display: none;" onchange="loadImage(this)"/>
                                    <input type="hidden" id="updateID" name="updateID">
                                    <input type="hidden" id="isConfirmed" name="isConfirmed">
                                    <button class="btn btn-info" type="button" data-type="disabled" style="display: none;" id="editBtn">
                                        <?php echo $this->lang->line('emp_edit'); ?>
                                    </button> <!--Edit -->
                                    <button class="btn btn-primary btn-sm submitBtn" id="saveBtn" type="submit"><?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                                    <button class="btn btn-primary submitBtn updateBtn" id="updateBtn" type="submit" style="display:none">
                                        <?php echo $this->lang->line('emp_update'); ?>
                                    </button>
                                    <button type="button"  class="btn btn-primary btn-sm" id="reJoinBtn" style="display: none;" data-toggle="modal" data-target="#rejoin-modal">
                                        Rejoin
                                    </button>
                                    <button class="btn btn-success submitBtn updateBtn" id="confirmBtn" type="button" style="display:none"
                                            onclick="confirm_employee()" data-value="1">
                                        <?php echo $this->lang->line('emp_confirm'); ?>
                                    </button><!--Confirm-->
                                </div>
                                <?php echo form_close(); ?>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="contact">
                                <?php echo form_open('', 'role="form" id="employeeContact_form" '); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <fieldset>
                                            <legend><?php echo $this->lang->line('emp_permanent_details'); ?><!--Permanent Details--></legend>
                                            <div class="form-group col-sm-3">
                                                <label for="ep_address1">
                                                    <?php echo $this->lang->line('emp_address_line1'); ?><!--Address Line1--></label>
                                                <input type="text" class="form-control" name="ep_address1" id="ep_address1"
                                                       style="width:100%;" placeholder="<?php echo $this->lang->line('emp_number'); ?>">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="ep_address2">
                                                    <?php echo $this->lang->line('emp_address_line2'); ?><!--Address Line2--></label>
                                                <input type="text" class="form-control" name="ep_address2" id="ep_address2"
                                                       style="width:100%;" placeholder="<?php echo $this->lang->line('emp_street'); ?>">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="ep_address3">
                                                    <?php echo $this->lang->line('emp_address_line3'); ?><!--Address Line3--></label>
                                                <input type="text" class="form-control" name="ep_address3" id="ep_address3"
                                                       style="width:100%;" placeholder="<?php echo $this->lang->line('emp_city'); ?>">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="ep_address4"
                                                       class="control-label"><?php echo $this->lang->line('emp_country'); ?></label>
                                                <?php

                                                /*echo form_dropdown('ep_address4', $counties, '', 'class="form-control" id="ep_address4" ');*/

                                                echo '<select name="ep_address4" class="form-control" id="ep_address4">';
                                                if (!empty($counties)) {
                                                    foreach ($counties as $key => $val) {

                                                        /*$translation = $this->lang->line('country_' . strtolower($val));*/
                                                        // $translation = $this->lang->line('religion_select_the_religion' . strtolower($val));
                                                        $output = language_string_conversion2('country_' . $val);
                                                        $translation = $this->lang->line($output);


                                                        //*/$translation = $this->lang->line('religion_buddhism' );

                                                        if (!empty(trim($translation))) {
                                                            $showDescription = $translation;
                                                        } else {
                                                            $showDescription = $val;
                                                        }
                                                        // echo '<option value="' . $key . '">' . $showDescription . '</option>';
                                                        echo '<option value="' . $key . '">' . $showDescription . '</option>';


                                                    }
                                                }
                                                echo ' </select>';


                                                ?>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="zip_code"><?php echo $this->lang->line('emp_zip_code'); ?><!--Zip Code--></label>
                                                <input type="text" class="form-control" name="zip_code" id="zip_code" style="width:100%;"
                                                       placeholder="<?php echo $this->lang->line('emp_zip_code'); ?>">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="ep_fax"><?php echo $this->lang->line('emp_fax_number'); ?><!--Fax No--></label>
                                                <input type="text" class="form-control" name="ep_fax" id="ep_fax" style="width:100%;"
                                                       placeholder="<?php echo $this->lang->line('emp_fax_number'); ?>" data-bv-field="ep_fax">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for=""><?php echo $this->lang->line('emp_employee_personal_email'); ?><!--Personal Email--></label>
                                                <input type="text" class="form-control" name="personalEmail" id="personalEmail" style="width:100%;"
                                                       placeholder="<?php echo $this->lang->line('emp_employee_personal_email'); ?>" data-bv-field="">
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 10px">
                                    <div class="col-md-12">
                                        <button class="btn btn-xs btn-primary pull-right" id="save_itm_btn" type="button"
                                                onclick="copy_permanent_details();"><?php echo $this->lang->line('emp_copy_detail'); ?><!--Copy Detail-->
                                        </button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <fieldset>
                                            <legend><?php echo $this->lang->line('emp_contact_details'); ?><!--Contact Details--></legend>
                                            <div class="form-group col-sm-3">
                                                <label for="ec_address1">
                                                    <?php echo $this->lang->line('emp_address_line1'); ?><!--Address Line1--></label>
                                                <input type="text" class="form-control" name="ec_address1" id="ec_address1"
                                                       style="width:100%;" placeholder="<?php echo $this->lang->line('emp_number'); ?>">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="ec_address2">
                                                    <?php echo $this->lang->line('emp_address_line2'); ?><!--Address Line2--></label>
                                                <input type="text" class="form-control" name="ec_address2" id="ec_address2"
                                                       style="width:100%;" placeholder="<?php echo $this->lang->line('emp_street'); ?>">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="ec_address3">
                                                    <?php echo $this->lang->line('emp_address_line3'); ?><!--Address Line3--></label>
                                                <input type="text" class="form-control" name="ec_address3" id="ec_address3"
                                                       style="width:100%;" placeholder="<?php echo $this->lang->line('emp_city'); ?>">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="ec_address4" class="control-label">
                                                    <?php echo $this->lang->line('emp_country'); ?><!--Country--></label>
                                                <?php
                                                /*echo form_dropdown('ec_address4', $counties, '', 'class="form-control" id="ec_address4" ');*/

                                                echo '<select name="ec_address4" class="form-control" id="ec_address4">';
                                                if (!empty($counties)) {
                                                    foreach ($counties as $key => $val) {

                                                        /*$translation = $this->lang->line('country_' . strtolower($val));*/
                                                        // $translation = $this->lang->line('religion_select_the_religion' . strtolower($val));
                                                        $output = language_string_conversion2('country_' . $val);
                                                        $translation = $this->lang->line($output);


                                                        //*/$translation = $this->lang->line('religion_buddhism' );

                                                        if (!empty(trim($translation))) {
                                                            $showDescription = $translation;
                                                        } else {
                                                            $showDescription = $val;
                                                        }
                                                        // echo '<option value="' . $key . '">' . $showDescription . '</option>';
                                                        echo '<option value="' . $key . '">' . $showDescription . '</option>';


                                                    }
                                                }
                                                echo ' </select>';

                                                ?>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="ec_po_box"> <?php echo $this->lang->line('emp_p_o_box'); ?></label>
                                                <input type="text" name="ec_po_box" id="ec_po_box" class="form-control"
                                                       placeholder="<?php echo $this->lang->line('emp_p_o_box'); ?>" style="width:100%;">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="ec_pc"><?php echo $this->lang->line('emp_zip_code'); ?><!--Zip Code--></label>
                                                <input type="text" name="ec_pc" id="ec_pc" class="form-control"
                                                       placeholder="<?php echo $this->lang->line('emp_zip_code'); ?>"
                                                       style="width:100%;">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="ec_fax"><?php echo $this->lang->line('emp_fax_number'); ?><!--Fax No--></label>
                                                <input type="text" name="ec_fax" id="ec_fax" class="form-control"
                                                       placeholder="<?php echo $this->lang->line('emp_fax_number'); ?>"
                                                       style="width:100%;" data-bv-field="ec_fax">
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="telNo1">
                                                    <?php echo $this->lang->line('emp_telephone_no_1'); ?><!--Telephone No1--></label>

                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                                    <input type="tel" class="form-control " id="telNo1" name="telNo1">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="telNo2">
                                                    <?php echo $this->lang->line('emp_telephone_no_2'); ?><!--Telephone No2--></label>

                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                                    <input type="tel" class="form-control " id="telNo2" name="telNo2">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <?php if($emiratesLang == 'Default'){?>
                                                    <label for="emp_mobile"><?php echo $this->lang->line('emp_mobile_no'); ?><!--Mobile--></label>
                                                <?php }else{ ?>
                                                    <label for="emp_mobile">Personal Mobile<!--Personal Mobile--></label>
                                                <?php } ?>

                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                                    <input type="tel" class="form-control " id="emp_mobile" name="emp_mobile">
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="company_mobile">Company Mobile<!--Company Mobile--></label>
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                                                    <input type="tel" class="form-control " id="company_mobile" name="company_mobile">
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                <hr>

                                <div class="row" style="margin-bottom: 2%">
                                    <div class="col-md-12">
                                        <div class="text-right m-t-xs">
                                            <button class="btn btn-primary btn-sm" id="contact-update"
                                                    type="submit"><?php echo $this->lang->line('emp_save'); ?></button><!--Save Changes-->
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>

                                <div class="row">
                                    <fieldset class="scheduler-border">
                                        <legend><?php echo $this->lang->line('common_emergency_contact_details'); ?></legend>

                                        <div class="col-sm-12" style="margin-bottom: 10px">
                                            <button class="btn btn-primary btn-sm pull-right" onclick="add_emergencyContact()">
                                                <?php echo $this->lang->line('common_add_emergency_contact'); ?>
                                            </button>
                                        </div>

                                        <div class="table-responsive-div">
                                            <table id="emergency_contact_tbl" class="<?php echo table_class(); ?>">
                                                <thead>
                                                <tr>
                                                    <th style="width: auto"><?php echo $this->lang->line('common_contact_person'); ?></th>
                                                    <th style="width: auto"><?php echo $this->lang->line('common_telephone'); ?></th>
                                                    <th style="width: auto"><?php echo $this->lang->line('common_mobile'); ?></th>
                                                    <th style="width: auto"><?php echo $this->lang->line('common_address'); ?></th>
                                                    <th style="width: auto"><?php echo $this->lang->line('common_relationship'); ?></th>
                                                    <th style="width: auto"><?php echo $this->lang->line('common_Country'); ?></th>
                                                    <th style="width: auto"><?php echo $this->lang->line('common_default'); ?></th>
                                                    <th style="width: 40px"></th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="family_tab"> <?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->

                            <div role="tabpanel" class="tab-pane" id="employment_tab"><?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->

                            <?php if($advancedCostCapturing == 1){ ?>
                            <div role="tabpanel" class="tab-pane" id="reportingStructure_tab"><?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->
                            <?php } ?>

                            <div role="tabpanel" class="tab-pane" id="medicine_tab"><?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->

                            <div role="tabpanel" class="tab-pane" id="salary_tab"><?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->

                            <div role="tabpanel" class="tab-pane" id="accounts_tab"><?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->

                            <div role="tabpanel" class="tab-pane" id="qualification_tab"><?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->

                            <div role="tabpanel" class="tab-pane" id="attendance_tab"> <?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->

                            <div role="tabpanel" class="tab-pane" id="si_tab"> <?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->

                            <div role="tabpanel" class="tab-pane" id="document_tab"> <?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->

                            <div role="tabpanel" class="tab-pane" id="assets_tab"> <?php echo $this->lang->line('emp_loading'); ?> .... </div><!--Loading-->

                            <div role="tabpanel" class="tab-pane" id="discharged_tab" style="height: 430px">
                            <form action="#" id="dischargedForm" class="dischargedForm">
                                    <div class="row">
                                        <div class="form-group col-sm-3 col-xs-6">
                                            <label for="isPayrollEmployee">&nbsp;</label>
                                            <div class="input-group">
                                            <span class="input-group-addon">
                                                <input type="checkbox" name="isDischarged" id="isDischarged" value="1" />
                                            </span>
                                                <input type="text" class="form-control" disabled=""
                                                       value="<?php echo $this->lang->line('emp_is_discharged'); ?>"> <!--Is Discharged-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="form-group ">
                                                <label for="dischargedDate">
                                                    <?php echo $this->lang->line('emp_discharged_date'); ?><!--Discharged Date--></label>

                                                <div class="input-group datepic">
                                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                    <input type="text" name="dischargedDate" value="<?php echo $current_date; ?>"
                                                           id="dischargedDate" class="form-control" data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-xs-6">
                                            <div class="form-group">
                                                <label for="lastWorkingDate"> <?php echo $this->lang->line('emp_lastworking_date'); ?><!--Last Working Date--></label>

                                                <div class="input-group datepic">
                                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                    <input type="text" name="lastWorkingDate" value="<?php echo $current_date; ?>" id="lastWorkingDate" class="form-control"
                                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                        <div class="form-group">
                                        <label class="col-md-3 control-label" for="dischargereason">
                                        <?php echo $this->lang->line('emp_discharged_reason'); ?><!--Discharged Reason--></label>
                                                                            
                                            <?php
                                            // Generate the dropdown menu
                                            echo form_dropdown('dischargereason', $discharge, '', 'id="dischargereason" class="form-control select2"');
                                            ?>
                                        </div>
                                        </div>
                                    </div>
                                   
                                    <div class="row" style="pt-20">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="dischargedComment">
                                                    <?php echo $this->lang->line('emp_discharged_comment'); ?><!--Discharged Comment--></label>
                                                <textarea name="dischargedComment" id="dischargedComment" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="">
                                        <div class="col-md-12">
                                            <div class="text-right m-t-xs">
                                                <button class="btn btn-primary btn-sm" id="dischargeUpdate" onclick="dischargedUpdate()"
                                                        type="button"><?php echo $this->lang->line('emp_save'); ?><!--Save Changes-->
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                   
                                </div>
                                </form>

    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="pending-approval-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" id="pending-approval-form" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title"><?php echo $this->lang->line('emp_personal_data_pending_approval');?> </h3><!--Employee Personal Data Pending Approval-->
                </div>
                <div class="modal-body" id="pending-response">

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="empID" value="<?php echo $empID;?>">
                    <button type="button" class="btn btn-primary btn-sm" onclick="approve_personalData()"><?php echo $this->lang->line('emp_proceed');?> </button><!--Proceed-->
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rejoin-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" id="reJoin-form" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title"><?php echo $this->lang->line('emp_personal_rejoin_employee');?> </h3><!--Rejoin Employee-->
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-8 col-xs-12">
                            <div class="form-group">
                                <label class="col-sm-4 col-xs-2 control-label" style="text-align: left"><?php echo $this->lang->line('emp_personal_rejoin_date');?> </label><!--Rejoin Date-->
                                <div class="input-group datepic col-md-4 col-xs-3">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="rejoinDate" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="rejoinDate" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12" style="margin-top: 2%">
                            <fieldset>
                                <legend><?php echo $this->lang->line('emp_select_which_details_you_want_to_copy');?> </legend><!--Select which details you want to copy-->
                                <div class="row"><div class="col-sm-6 col-xs-6" style="margin-bottom: 10px;"></div></div>
                                <div class="col-sm-4 col-xs-4" style="margin-bottom: 2%">
                                    <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="personalDetails" value="Y" checked disabled>
                                </span>
                                        <input type="text" class="form-control" disabled="" value="Personal Details">
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xs-4" style="margin-bottom: 2%">
                                    <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="contactDetails" value="Y" checked>
                                </span>
                                        <input type="text" class="form-control" disabled="" value="Contact Details">
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xs-4" style="margin-bottom: 2%">
                                    <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="familyDetails" value="Y" checked>
                                </span>
                                        <input type="text" class="form-control" disabled="" value="Family Details">
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xs-4" style="margin-bottom: 2%">
                                    <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="documentDetails" value="Y" checked>
                                </span>
                                        <input type="text" class="form-control" disabled="" value="Document Details">
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xs-4" style="margin-bottom: 2%">
                                    <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="bankDetails" value="Y" checked>
                                </span>
                                        <input type="text" class="form-control" disabled="" value="Bank Details">
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xs-4" style="margin-bottom: 2%">
                                    <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="qualificationDetails" value="Y" checked>
                                </span>
                                        <input type="text" class="form-control" disabled="" value="Qualification Details">
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xs-4" style="margin-bottom: 2%">
                                    <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="ssoDetails" value="Y" checked>
                                </span>
                                        <input type="text" class="form-control" disabled="" value="SSO Details">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="rejoinEmpID" value="<?php echo trim($this->input->post('page_id'));?>">
                    <button type="button" class="btn btn-primary btn-sm" onclick="rejoinEmp()"><?php echo $this->lang->line('emp_proceed');?> </button><!--Proceed-->
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('emp_Close');?> </button><!--Close-->
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="addFamilyDetailModal" role="dialog">
    <div class="modal-dialog modal-lg" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true" style="color:red;">x</span>
                </button>
                <h5> <?php echo $this->lang->line('emp_add_Family_detail');?><!--Add Family Detail--></h5>
            </div>
            <div class="modal-body" id="modal_contact">
                <form method="post" id="frm_FamilyContactDetails" class="" autocomplete="off">
                    <input type="hidden" value="0" id="empfamilydetailsID" name="empfamilydetailsID"/>
                    <input type="hidden" value="" id="empID_familyDetail" name="employeeID"/>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group col-sm-4">
                                <label class="control-label" for="textinput"><?php echo $this->lang->line('common_name');?><!--Name--></label>
                                <input class="form-control input-md" placeholder="<?php echo $this->lang->line('common_name');?>"
                                       id="name" name="name" type="text" value=""><!--Name-->
                            </div>

                            <div class="form-group col-sm-4">
                                <label class="control-label" for="relationshipType"><?php echo $this->lang->line('common_relationship');?><!--Relationship--></label>
                                <?php echo form_dropdown('relationshipType', hrms_relationship_drop(), '', 'id="relationshipType" class="form-control select2"'); ?>
                            </div>

                            <div class="form-group col-sm-4">
                                <label class="control-label" for="country"><?php echo $this->lang->line('common_nationality');?><!--Nationality--></label>
                                <?php echo form_dropdown('nationality', load_all_nationality_drop(), '', 'id="nationality" class="form-control select2"'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group col-md-3">
                                <label class="control-label"><?php echo $this->lang->line('common_date_of_birth');?><!--Date of Birth--></label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="DOB" style="width: 94%;" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="DOB" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group col-md-3">
                                <label class="control-label" for="gender"><?php echo $this->lang->line('common_gender');?><!--Gender--></label>
                                <select name="gender" class="form-control empMasterTxt" id="gender">
                                    <option value="1"> <?php echo $this->lang->line('common_male');?><!--Male--></option>
                                    <option value="2"> <?php echo $this->lang->line('common_female');?><!--Female--></option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label class="control-label" for="fam_national_no"><?php echo $this->lang->line('common_national_no');?></label>
                                <input type="text" name="fam_national_no" id="fam_national_no" class="form-control input-md" >
                            </div>

                            <div class="form-group col-md-3">
                                <label class="control-label" for="fam_id_no">
                                <?php
                                    if (in_array($emiratesLang, ['MSE', 'SOP', 'GCC', 'ASAAS', 'Flowserve'])) {
                                        echo $this->lang->line('common_emirates_no');
                                    } else {
                                        echo $this->lang->line('common_id_no');
                                    }
                                ?>
                                </label>
                                <input type="text" name="fam_id_no" id="fam_id_no" class="form-control input-md" >
                            </div>

                            <div class="form-group col-sm-6">
                                <label class="control-label" for="fam_id_expiry">
                                    <?php
                                        if (in_array($emiratesLang, ['MSE', 'SOP', 'GCC', 'ASAAS', 'Flowserve'])) {
                                            echo $this->lang->line('emp_emirate_expiry_date');
                                        } else {
                                            echo $this->lang->line('emp_id_expiry_date');
                                        } ?>    
                                </label>
                                <div class="input-group datepic">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="fam_id_expiry" style="width: 94%;" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                            value="<?php echo $current_date; ?>" id="fam_id_expiry" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <fieldset class="scheduler-border">
                                <legend> <?php echo $this->lang->line('common_passport_details');?> </legend>

                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="fam_passport_no"><?php echo $this->lang->line('common_passport_number_no');?></label>
                                    <input type="text" name="fam_passport_no" id="fam_passport_no" class="form-control input-md" >
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="fam_pass_expiry"><?php echo $this->lang->line('emp_passport_expiry_date');?></label>
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="fam_pass_expiry" style="width: 94%;" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="fam_pass_expiry" class="form-control" required>
                                    </div>
                                </div>
                            </fieldset>
                        </div>


                        <div class="col-sm-6">
                            <fieldset class="scheduler-border">
                                <legend> <?php echo $this->lang->line('common_visa_details');?> </legend>

                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="fam_visa_no"><?php echo $this->lang->line('emp_visa_no');?></label>
                                    <input type="text" name="fam_visa_no" id="fam_visa_no" class="form-control input-md" >
                                </div>

                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="fam_visa_expiry"><?php echo $this->lang->line('emp_visa_expiry_date');?></label>
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="fam_visa_expiry" style="width: 94%;" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="fam_visa_expiry" class="form-control" required>
                                    </div>
                                </div>
                            </fieldset>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <fieldset class="scheduler-border">
                                <legend> <?php echo $this->lang->line('common_insurance_details');?> </legend>

                                <div class="form-group col-md-4">
                                    <label class="control-label" for="fam_ins_category"><?php echo $this->lang->line('emp_insurance_category');?></label>
                                    <?php echo form_dropdown('fam_ins_category', get_hrms_insuranceCategory(1), '', 'id="fam_ins_category" class="form-control select2"'); ?>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="control-label" for="fam_insurance_no"><?php echo $this->lang->line('common_insurance_code');?></label>
                                    <input type="text" name="fam_insurance_no" id="fam_insurance_no" class="form-control input-md" >
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="control-label" for="fam_cover_from"><?php echo $this->lang->line('emp_cover_form');?></label>
                                    <div class="input-group datepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="fam_cover_from" style="width: 94%;" data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="fam_cover_from" class="form-control" required>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </form>
                <div id="familyDetail_msg"></div>
            </div>
            <div class="modal-footer" style="background-color: #ffffff">
                <button type="button" class="btn btn-primary" onclick="saveFamilyDetails()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_cancel');?><!--Cancel--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " data-backdrop="static" id="modaluploadimages" data-width="60%" tabindex="-1"
     role="dialog">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h5><?php echo $this->lang->line('common_attachments');?> </h5><!--Attachments-->
        </div>
        <div class="modal-body" id="modal_contact" style="min-height:100px;background-color: #F5F5F5">
            <?php echo form_open_multipart('', 'id="family_image_uplode_form" class="form-horizontal"'); ?>
            <fieldset>
                <!-- Text input-->

                <input type="hidden" class="form-control" value="" id="empfamilydetailzID"
                       name="empfamilydetailsID">


                <!-- File Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton"><?php echo $this->lang->line('common_attachment');?> </label><!--Attachment-->

                    <div class="col-md-8">
                        <input type="file" name="document_file" class=" input-md" id="image_file">
                    </div>
                </div>

                <!-- Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="singlebutton"></label>

                    <div class="col-md-8">

                        <button type="button" class="btn btn-xs btn-primary" onclick="familyimage_uplode()"><span
                                class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span></button>
                    </div>
                </div>
            </fieldset>
            </form>
        </div>
        <div class="modal-footer" style="background-color: #ffffff">

            <button type="button" class="btn btn-xs btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_cancel');?> </button><!--Cancel-->
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="modaluploadattachment" data-width="60%" tabindex="-1"
     role="dialog">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h5><?php echo $this->lang->line('common_attachments');?> </h5><!--Attachments-->
        </div>
        <div class="modal-body" id="modal_contact" style="min-height:100px;background-color: white">
            <?php echo form_open_multipart('', 'id="family_attachment_uplode_form" class="form-horizontal"'); ?>
            <fieldset>
                <!-- Text input-->

                <input type="hidden" class="form-control" value="" id="empfamilydetailsAttachID"
                       name="empfamilydetailsAttachID">
                <input type="hidden" class="form-control" value="" id="empIDFamilyAttach"
                       name="empIDFamilyAttach">


                <!-- File Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton"><?php echo $this->lang->line('common_attachment');?> </label><!--Attachment-->

                    <div class="col-md-8">
                        <input type="file" name="document_file" class=" input-md" id="image_file">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton"><?php echo $this->lang->line('common_description');?> </label><!--Description-->

                    <div class="col-md-8">
                        <input type="text" name="attachmentDescription" id="attachmentDescription">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton"><?php echo $this->lang->line('common_document');?> </label><!--Document-->

                    <div class="col-md-8">
                        <select name="documentID" id="documentID" class="form-control">
                            <option value="1"><?php echo $this->lang->line('common_passport');?> </option><!--Passport-->
                            <option value="2"><?php echo $this->lang->line('common_visa');?> </option><!--Visa-->
                            <option value="3"><?php echo $this->lang->line('common_insurance');?>  </option><!--Insurance-->
                        </select>
                    </div>
                </div>

                <!-- Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="singlebutton"></label>

                    <div class="col-md-8">

                        <button type="button" class="btn btn-xs btn-primary" onclick="familyattachment_uplode()"><span
                                class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span></button>
                    </div>
                </div>


            </fieldset>
            </form>

            <hr>
            <div class="table-responsive">
                <table id="family_attachment_table" class="<?php echo table_class() ?>">
                    <thead>
                    <tr>
                        <th style="min-width: 4%">#</th>
                        <th style="min-width: 15%"><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                        <th style="min-width: 20%"><?php echo $this->lang->line('common_document');?> </th><!--Document-->
                        <th style="min-width: 5%"><?php echo $this->lang->line('common_action');?> </th><!--Action-->
                    </tr>
                    </thead>
                </table>
            </div>

        </div>
        <div class="modal-footer" style="background-color: #ffffff">

            <button type="button" class="btn btn-xs btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_cancel');?> </button><!--Cancel-->
        </div>
    </div>
</div>

<div class="modal fade" id="title-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('emp_new_employee_title');?> </h3><!--New Employee Title-->
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_title');?> </label><!--Title-->

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="add-emp-title" name="add-emp-title">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="title-btn"><?php echo $this->lang->line('common_save');?> </button><!--Save-->
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="emergencyContact_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title"><?php echo $this->lang->line('common_emergency_contact_details');?> <span id="emergency_contact_modal_title"></span></h3>
            </div>
            <form role="form" id="emergencyContact_frm" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="updateID" id="emergency_autoID" value="0">
                        <fieldset class="scheduler-border">
                            <legend><?php echo $this->lang->line('common_primary');?></legend>

                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_contact_person');?> </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="contactName" name="contactName">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_telephone');?> </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="telNo" name="telNo">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_mobile');?> </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="mobile" name="mobile">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_address');?> </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="address" name="address">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_relationship');?> </label>
                                <div class="col-sm-6">
                                    <?php echo form_dropdown('relationshipType', hrms_relationship_drop(), '', 'id="em_cont_relationshipType" class="form-control contact-frm-select select2"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_Country');?> </label>
                                <div class="col-sm-6">
                                    <select class="form-control contact-frm-select select2" id="country" name="country">
                                        <?php
                                        if (!empty($counties)) {
                                            foreach ($counties as $key => $val) {
                                                $output = language_string_conversion2('country_' . $val);
                                                $translation = $this->lang->line($output);
                                                $showDescription = (!empty(trim($translation))) ? $translation: $val;

                                                echo '<option value="' . $key . '">' . $showDescription . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend><?php echo $this->lang->line('common_other');?></legend>

                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_contact_person');?> </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="contactName_other" name="contactName_other">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_address');?> </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="address_other" name="address_other">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="update_emergencyContactDetails()"><?php echo $this->lang->line('common_save');?> </button><!--Save-->
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?> </button><!--Close-->
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    var fromHiarachy = '<?php echo $fromHiarachy ?>';
    var isAuthenticateNeed = '<?php echo $isAuthenticateNeed ?>';
    var eCodePolicy = <?php echo json_encode(getPolicyValues('ECG', 'All'))?>;
    var setSecondaryCodeDisable = false;

    if(fromHiarachy == 1){
        $('.btn ').addClass('hidden');
        $('.navdisabl ').removeClass('hidden');
        $('.form-control:not([type="search"], #parentCompanyID)').attr('disabled', true);
    }


    /** Toggle the navigation to small**/
    $('body').addClass('sidebar-collapse');

    $("#notificationPending").tooltip();


    var employee_form = $("#employee_form");
    var empID = '<?php echo $empID; ?>';
    var isPendingDataAvailable = '<?php echo $isPendingDataAvailable; ?>';


    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
        widgetPositioning: {
            vertical: 'bottom'
        }
    });

    $('.contact-frm-select').select2();

    $(document).ready(function () {
        $('.headerclose').click(function () {
            if(isAuthenticateNeed == 1){
                fetchPage('system/hrm/employee_master_new', 'Test', 'HRMS');
            }
            else if(fromHiarachy == 1){
                fetchPage('system/profile/profile_information', '', 'Profile',1);
            }else{
                fetchPage('system/hrm/employee_master_new', 'Test', 'HRMS');
            }
        });

        employee_form.bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                emp_title: {validators: {notEmpty: {message: '<?php echo $this->lang->line('emp_title_is_required');?>.'}}},/*Title is required*/
                fullName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_full_name_is_required');?>.'}}},/*Full Name is required*/
                emp_gender: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_gender_is_required');?>.'}}},/*Gender is required*/
                /*empDob: {validators: {notEmpty: {message: 'Date of birth is required.'}}},
                 empDoj: {validators: {notEmpty: {message: 'Date of joined is required.'}}},
                 religion: {validators: {notEmpty: {message: 'Religion is required.'}}},*/
                empDoj: {validators: {notEmpty: {message: '<?php echo $this->lang->line('emp_date_of_joined_required');?>.'}}},/*Date of joined is required*/
                DateAssumed: {validators: {notEmpty: {message: '<?php echo $this->lang->line('emp_date_assumed_is_required');?>.'}}},/*Date Assumed is required*/
                Ename4: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_name_is_required');?>.'}}},/*Name is required*/
                intial: {validators: {notEmpty: {message: '<?php echo $this->lang->line('emp_initials_is_required');?>.'}}},/*Initials is required*/
                emp_email: {validators: {notEmpty: {message: '<?php echo $this->lang->line('emp_email_is_required');?>.'}}},/*E-Mail required*/
                designation: {validators: {notEmpty: {message: '<?php echo $this->lang->line('emp_designation_is_required');?>.'}}},/*Designation required*/
                //telNo1: {validators: {notEmpty: {message: 'Telephone No is required.'}}},
                empCurrency: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_currency_is_required');?>.'}}},/*Currency is required*/
                empSegment: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_segment_is_required');?>.'}}}/*Segment is required*/
            },
        }).on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var requestUrl = '<?php echo site_url('Employee/update_employee'); ?>';
            save_update(requestUrl);
        });

        $('#employeeContact_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':enabled'],
            fields: {
//                    ep_address4: {validators: {notEmpty: {message: 'Permanent Country is required.'}}},
//                    ec_address4: {validators: {notEmpty: {message: 'Current County is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            $('#contact-update').prop('disabled', false);
            e.preventDefault();
            var $form = $(e.target);
            var data = $form.serializeArray();
            var requestUrl = '<?php echo site_url('Employee/contactDetails_update'); ?>';
            update_details(data, requestUrl);
        });

        fetch_emergency_contact_details();
    });

    if(empID){ employee_details(empID); }


    $('#editBtn').click(function () {
        var type = $(this).data('type');
        if ($(this).hasClass('disabled')) {
            return false;
        }
        if (type == 'disabled') {
            $(this).data('type', 'enabled');
            $('#profile input, #profile textarea, #profile select').attr('disabled', false);
            $('#edit-emp-img').show();
            $('#updateBtn, #confirmBtn').attr('disabled', false);
            if(setSecondaryCodeDisable == true){
                $('#EmpSecondaryCode').attr('readonly', true);
            }
        } else {
            $('#edit-emp-img').hide();
            $(this).data('type', 'disabled');
            $('#profile input, #profile textarea, #profile select').attr('disabled', true);
            $('#updateBtn, #confirmBtn').attr('disabled', true);
        }
    });

    function employee_details(empID){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID': empID},
            url: '<?php echo site_url("Employee/new_employee_details"); ?>',
            beforeSend: function () {
                $('.master-nav-li').hide();
                startLoad();
            },
            success: function (data) {

                if( 'thisEmpID' in data ){

                    if(<?php echo $hideNameWithInitials ? 'true' : 'false'; ?> || <?php echo $hrmsflow ? 'true' : 'false'; ?>){
                        $('#empNameWithInitial').text(data['Ename4'] +' - '+ data['EmpSecondaryCode']);
                    }
                    else{
                        $('#empNameWithInitial').text(data['Ename2'] +' - '+ data['ECode']);
                    }
                    

                    $('#empDesignation').text(data['DesDescription']);

                    $('.btn-wizard').removeClass('disabled');
                    $('#updateID').val(empID);

                    $('#emp_title').val(data['EmpTitleId']);
                    $('#fullName').val(data['Ename1']);
                    $('#shortName').val(data['EmpShortCode']);
                    $('#EmpSecondaryCode').val(data['EmpSecondaryCode']);
                    $('#empDob').val(data['EDOB']);
                    $('#empDoj').val(data['EDOJ']);
                    $('#religion').val(data['rid']);
                    $('#telNo1').val(data['EpTelephone']);
                    $('#telNo2').val(data['EcTel']);
                    $('#emp_mobile').val(data['EpMobile']);
                    $('#company_mobile').val(data['EcMobile']);

                    var empEmail = data['EEmail'];
                    $('#emp_email').val(empEmail);
                    $('.email-display').html('<i class="fa fa-envelope" aria-hidden="true"></i> '+empEmail).attr('title', empEmail).attr('href', 'mailto:'+empEmail);
                    $('#joinDate-display').html(data['joinDate-display']);
                    $('#period-display').html(data['period-display']);

                    $('#birthDay-display').html(data['dob']);
                    $('#age-display').html(data['age']);
                    
                    $('#employmentTypeDisplay').html(data['employmentTypeDisplay']);
                    if(data['managerId'] != null){
                        $('#managerName').html(data['managerName']).attr('onclick', 'edit_empDet('+data['managerId']+')');
                    }
                    else{
                        $('#managerName').html('Not set').removeAttr('onclick');
                    }
                    $('#managerDesignation').html(data['managerDesignation']);
                    $('#managerImg').attr('src',data['managerImg']);
                    $('#designation').val(data['EmpDesignationId']);
                    $('#empSegment').val(data['segmentID']);
                    $('#empCode').val(data['ECode']);
                    $('#empCurrency').val(data['payCurrency']);
                    $('#empMachineID').val(data['empMachineID']);
                    $('#floorID').val(data['floorID']);
                    $('#overTimeGroup').val(data['overTimeGroup']);
                    $('#EmployeeConType').val(data['EmployeeConType']);

                    $('#ep_address1').val(data['EpAddress1']);
                    $('#ep_address2').val(data['EpAddress2']);
                    $('#ep_address3').val(data['EpAddress3']);
                    $('#ep_address4').val(data['EpAddress4']);
                    $('#zip_code').val(data['ZipCode']);
                    $('#ep_fax').val(data['EpFax']);
                    $('#personalEmail').val(data['personalEmail']);

                    $('#ec_address1').val(data['EcAddress1']);
                    $('#ec_address2').val(data['EcAddress2']);
                    $('#ec_address3').val(data['EcAddress3']);
                    $('#ec_address4').val(data['EcAddress4']);
                    $('#ec_po_box').val(data['EcPOBox']);
                    $('#ec_pc').val(data['EcPC']);
                    $('#ec_fax').val(data['EcFax']);


                    $('#pass_portNo').val(data['EPassportNO']);
                    $('#passPort_expiryDate').val(data['EPassportExpiryDate']);
                    $('#visa_expiryDate').val(data['EVisaExpiryDate']);
                    $('#airport_destination').val(data['AirportDestination']);
                    $('#BloodGroup').val(data['BloodGroup']);
                    $('#DateAssumed').val(data['DateAssumed']);
                    $('#Nationality').val(data['Nid']);
                    $('#MaritialStatus').val(data['MaritialStatus']);
                    $('#Ename4').val(data['Ename4']);
                    $('#initial').val(data['initial']);
                    $('#Ename3').val(data['Ename3']);
                    $('#NIC').val(data['NIC']);
                    $('#managerID').val(data['managerId']);
                    $('#reportingManager').val(data['Match']);
                    $('#leaveGroupID').val(data['leaveGroupID']);
                    $('#probationPeriod').val(data['probationPeriodMonth']);
                    $('#contractStartDate').val(data['contractStartDate']);
                    $('#contractEndDate').val(data['contractEndDate']);
                    $('#contractRefNo').val(data['contractRefNo']);

                    $('.gender').prop('checked', false);
                    if (data['Gender'] == 1) {
                        $('#male').prop('checked', true);
                    } else {
                        $('#feMale').prop('checked', true);
                    }


                    $('#changeImg').attr('src', data['EmpImage']);
                    $('#changeSignatureImg').attr('src', data['empSignature']);

                    if(empID == '<?=current_userID()?>'){ // current user image may be updated
                        $('.current-user-img').attr('src', data['EmpImage']);
                    }

                }
                else{
                    load_undefined_employee();
                }


                if (data['isCheckin'] == 1) {
                    $('#isCheckin').prop('checked', true);
                }

                if (data['isPayrollEmployee'] == 1) {
                    $('#isPayrollEmployee').prop('checked', true);
                }

                if (data['empConfirmedYN'] != 1) {
                    $('#confirmBtn').show();
                }else{
                    if(eCodePolicy == 0){
                        setSecondaryCodeDisable = true;
                    }
                }

                /*** Discharged ***/
                if (data['isDischarged'] == 1) {
                    $('#isDischarged').prop('checked', true);
                    $('#dischargedComment').val(data['dischargedComment']);
                    $('#dischargedDate').val(data['dischargedDate']);
                    $('#lastWorkingDate').val(data['lastWorkingDate']);
                    $('#editBtn').addClass('disabled');
                    $('#isDischarged,#dischargedComment,#dischargedDate,#dischargeUpdate,#lastWorkingDate').prop('disabled', true);
                    $('#reJoinBtn').show();
                    $('#editBtn, #updateBtn, #confirmBtn').hide();

                    $('#emp-image-container').addClass('discharged');
                    $('#changeImg').css('opacity', '0.4');
                }


                /*** If system admin ***/
                if (data['isSystemAdmin'] == 1) {
                    $('#isDischarged').prop('checked', true);
                    $('#dischargedComment').val(data['dischargedComment']);
                    $('#dischargedDate').val(data['dischargedDate']);
                    $('#editBtn').addClass('disabled');
                    $('#isDischarged,#dischargedComment,#dischargedDate,#dischargeUpdate,#lastWorkingDate').prop('disabled', true);

                    $('#editBtn, #updateBtn, #confirmBtn').hide();
                }

                stopLoad();

                setTimeout(function(){
                    $('.master-nav-li').show();
                }, 300);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

        $('#saveBtn').hide();
        $('#updateBtn').show();
        $('#editBtn').show();

        if( isPendingDataAvailable == 1){
            setTimeout(function(){
                $('#employeeName').append('<span class="label label-warning pull-right" onclick="load_pendingData()" id="pendingData"><?php echo $this->lang->line('emp_pending_personal_data_update');?> </span>');<!--Pending Personal Data Update-->
            }, 300);
        }

        $('#profile input, #profile textarea, #profile select').attr('disabled', true);
        $('#updateBtn, #confirmBtn').attr('disabled', true);
    }

    function save_update(requestUrl) {

        var formData = new FormData($("#employee_form")[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: formData,
            url: requestUrl,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    edit_empDet(data[2]);
                    /*$('.btn-wizard').removeClass('disabled');
                     $('#employeeName').text( $('#fullName').val() );
                     $('#updateID').val(data[2]);*/
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function update_details(data, requestUrl) {
        data.push({'name': 'updateID', 'value': $('#updateID').val()});
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: data,
            url: requestUrl,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', errorThrown);
            }
        });
    }

    $('#edit-emp-img').click(function () {
        $('#empImage').click();
    });

    function loadImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeImg').attr('src', e.target.result);
            };

            reader.readAsDataURL(obj.files[0]);
        }
    }

    $('#changeSignatureImg').click(function () {
        $('#empSignatureImage').click();
    });

    function loadSigImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeSignatureImg').attr('src', e.target.result);
            };

            reader.readAsDataURL(obj.files[0]);
        }
    }

    $('.submitBtn').click(function () {
        if ($(this).hasClass('updateBtn')) {
            $('#requestLink').val('<?php echo site_url('Employee/update_employee'); ?>');
        } else {
            $('#requestLink').val('<?php echo site_url('Employee/new_employee'); ?>');
        }

        var isConfirmed = ( $(this).attr('data-value') == 1 ) ? '1' : '0';
        $('#isConfirmed').val(isConfirmed);
    });

    function confirm_employee() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('emp_you_want_to_confirm_this');?>",/*You want to confirm this!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                employee_form.submit();
            }
        );
    }

    function load_pendingData(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': '<?php echo $empID;?>'},
            url: "<?php echo site_url('Profile/fetch_pendingEmpDataApproval'); ?>",
            beforeSend: function () {
                startLoad();
                $('#pending-response').html("");
            },
            success: function (data) {
                stopLoad();
                $('#pending-response').html(data);
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
            }
        });

        $('#pending-approval-modal').modal('show');
    }

    function approve_personalData(){
        var postData = $('#pending-approval-form').serializeArray();

        $('.approveChk-family:checked').each(function(){
            var colName = $(this).val();
            var colValue = $(this).attr('data-value');
            var colID = $(this).attr('data-id');

            postData.push({name:'familyData['+colID+']['+colName+']', value: colValue});
        });

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Profile/approve_pendingEmpData'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0] == 's'){
                    $('#pending-approval-modal').modal('hide');

                    setTimeout(function(){
                        edit_empDet( '<?php echo trim($this->input->post('page_id'));?>' );
                    }, 400);
                }
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
            }
        });
    }

    function rejoinEmp(){
        var postData = $('#reJoin-form').serializeArray();

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: postData,
            url: '<?php echo site_url("Employee/employee_rejoin"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#rejoin-modal').modal('hide');
                    setTimeout(function (){
                        edit_empDet(data[2]);
                    }, 300);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function dischargedUpdate() {

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('emp_you_want_to_discharge_this_employee');?>",/*You want to discharge this employee!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>",/*Confirm*/
                closeOnConfirm: true
            },
            function () {
                var data = $('#dischargedForm').serializeArray();
                data.push({'name': 'updateID', 'value': $('#updateID').val()});
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                    url: '<?php echo site_url('Employee/discharge_update'); ?>',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            $('#editBtn').addClass('disabled');
                            $('#isDischarged,#dischargedComment,#dischargedDate,#dischargeUpdate,#lastWorkingDate').prop('disabled', true);
                        }else{
                            $('#dischargedForm')[0].reset();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });
            });
    }

    function copy_permanent_details() {
        $('#ec_address1').val($('#ep_address1').val());
        $('#ec_address2').val($('#ep_address2').val());
        $('#ec_address3').val($('#ep_address3').val());
        $('#ec_address4').val($('#ep_address4').val());
        $('#ec_pc').val($('#zip_code').val());
        $('#ec_fax').val($('#ep_fax').val());
    }


    function fetch_employment() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID, 'template': 'envoy_'},
            url: '<?php echo site_url("Employee/load_employmentView"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#employment_tab').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }
    
    function fetch_reporting_structure() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID, 'template': 'envoy_'},
            url: '<?php echo site_url("Employee/load_reporting_structure"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#reportingStructure_tab').html(data);
                $('.rep_Strc_Save').addClass('disabled');

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function fetch_medical_details(){
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID, 'template': 'envoy_'},
            url: '<?php echo site_url("Employee/load_medical_details"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#medicine_tab').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function fetch_designation() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID},
            url: '<?php echo site_url("Employee/load_empDesignationView"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#designation-container').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function fetch_departments() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID},
            url: '<?php echo site_url("Employee/load_empDepartmentsView"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#department-container').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function fetch_salaryDet() {
        var empID = $('#updateID').val();
        var empCurrency = $('#empCurrency').val();

        $.ajax({
            url: '<?php echo site_url('Employee/empSalaryDetailsView'); ?>',
            method: 'post',
            data: {'empID': empID, 'empCurrency': empCurrency},
            dataType: 'html',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#salary_tab').html(data);
            },
            error: function () {
                stopLoad();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }

        });
    }

    function fetch_accounts() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID},
            url: '<?php echo site_url("Employee/load_empAccountsView"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#accounts_tab').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function fetch_qualification(subTab='') {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID, subTab:subTab},
            url: '<?php echo site_url("Employee/load_empQualificationView"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#qualification_tab').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function fetch_attendance() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID},
            url: '<?php echo site_url("Employee/load_empShiftView"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#attendance_tab').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function fetch_social_insurance() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID},
            url: '<?php echo site_url("Employee/load_social_incusrance"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#si_tab').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function fetch_document() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID},
            url: '<?php echo site_url("Employee/load_empDocumentView"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#document_tab').html(data);

            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    }

    function load_undefined_employee() {
        bootbox.confirm({
            message: "<?php echo $this->lang->line('emp_some_thing_went_wrong');?> , <br/> <?php echo $this->lang->line('emp_some_thing_went_wrong');?>.",<!--Some thing went wrong-->/*Please click ok to reload the page*/
            size: 'small',
            closeButton: false,
            buttons: {
                confirm: {
                    label: 'Ok',
                    className: 'btn-primary'
                },
                cancel: {
                    label: '',
                    className: 'btn-danger hide'
                }
            },
            callback: function () {
                window.location.href = '<?php echo site_url("dashboard"); ?>';
            }
        });


    }

    /**** Family Details****/
    function fetch_family_details() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {empID: empID},
            url: "<?php echo site_url('Employee/fetch_family_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#family_tab").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#family_tab").html('<div class="alert alert-danger"><?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.<br/><strong><?php echo $this->lang->line('emp_error_message');?> : </strong>' + errorThrown + '</div>');<!--An Error Occurred! Please Try Again-->/*Error Message*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function addfamilydetails() {
        $('#addFamilyDetailModal').modal('show');
        $('#frm_FamilyContactDetails')[0].reset();
        $('#empfamilydetailsID').val('0');
        $('.select2').select2();
    }

    function saveFamilyDetails() {
        var empID = $('#updateID').val();
        $('#empID_familyDetail').val(empID);
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Employee/saveFamilyDetails') ?>", /*ajax/ajax-add-profile-contact-detail.php*/
            data: $("#frm_FamilyContactDetails").serialize(),
            dataType: "json",
            cache: false,
            beforeSend: function () {
                $("#familyDetail_msg").html('');
                $("#familyDetail_msg").show();
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data.error == 0) {
                    $("#familyDetail_msg").html('<div class="alert alert-success"><strong> Success </strong><br>' + data['message'] + '</div>');
                    $("#addFamilyDetailModal").modal('hide');
                    fetch_family_details(data['empID']);
                    myAlert('s', data['message']);
                } else if (data.error == 1) {
                    $("#familyDetail_msg").html('<div class="alert alert-danger"><strong> Error </strong><br>' + data['message'] + '</div>');
                }
                setTimeout(function () {
                    $("#familyDetail_msg").hide();
                }, 5000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                setTimeout(function () {
                    $("#familyDetail_msg").hide();
                }, 5000);
                $("#familyDetail_msg").html('<div class="alert alert-danger"><strong><?php echo $this->lang->line('common_error');?> </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown + '</div>');<!-- Error-->
            }
        });
        return false;
    }

    function delete_familydetail(id) {
        var empID = $('#updateID').val();
        swal({
                title: "Are you sure", /*Are you sure?*/
                text: "You want to delete this record", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete" /*Delete*/,
                cancelButtonText: "cancel" /*cancel */
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'empfamilydetailsID': id},
                    url: "<?php echo site_url('Employee/delete_familydetail'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetch_family_details(empID);
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function modaluploadimages(empfamilydetailsID) {
        $('#empfamilydetailzID').val(empfamilydetailsID);
        $('#modaluploadimages').modal('show');

    }

    function familyimage_uplode() {
        var empID = $('#updateID').val();
        var formData = new FormData($("#family_image_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Employee/familyimage_upload'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetch_family_details(empID);
                    $('#modaluploadimages').modal('hide');
                }
                $('#family_image_uplode_form')[0].reset();


            },
            error: function (data) {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_please_contac_support_team');?>');/*Please contact support Team*/
            }
        });
        return false;
    }

    function attach_familydetail(empfamilydetailsID) {
        var empID = $('#updateID').val();
        $('#empfamilydetailsAttachID').val(empfamilydetailsID);
        $('#empIDFamilyAttach').val(empID);
        $('#modaluploadattachment').modal('show');
        fetch_family_attachment_details(empfamilydetailsID);
    }

    function familyattachment_uplode() {
        var empfamilydetailsID = $('#empfamilydetailsAttachID').val();
        var formData = new FormData($("#family_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Employee/familyattachment_uplode'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    fetch_family_attachment_details(empfamilydetailsID);
                    //$('#modaluploadattachment').modal('hide');
                }
                $('#family_attachment_uplode_form')[0].reset();


            },
            error: function (data) {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_please_contac_support_team');?>');/*Please contact support Team*/
            }
        });
        return false;
    }

    function fetch_family_attachment_details(empfamilydetailsID, selectedID=null) {
        Otable = $('#family_attachment_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_family_attachment_details'); ?>",
            "aaSorting": [[0, 'desc']],
            "searching": false,
            "bLengthChange": false,
            "columnDefs": [
                {}
            ],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var selectedRowID = (selectedID == null) ? parseInt('<?php echo $this->input->post('page_id'); ?>') : parseInt(selectedID);
                var tmp_i = oSettings._iDisplayStart;
                var iLen = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    if (parseInt(oSettings.aoData[x]._aData['expenseClaimCategoriesAutoID']) == selectedRowID) {
                        var thisRow = oSettings.aoData[oSettings.aiDisplay[x]].nTr;
                        $(thisRow).addClass('dataTable_selectedTr');
                    }
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "attachmentID"},
                {"mData": "desc"},
                {"mData": "document"},
                {"mData": "edit"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "empFamilyDetailsID", "value": empfamilydetailsID});
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

    function delete_family_attachment(id, empFamilyDetailsID) {
        swal({
                title: "Are you sure", /*Are you sure?*/
                text: "You want to delete this record", /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete" /*Delete*/,
                cancelButtonText: "cancel" /*cancel */
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'attachmentID': id},
                    url: "<?php echo site_url('Employee/delete_family_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetch_family_attachment_details(empFamilyDetailsID);
                        }
                    }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        // swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    $('#add-title').click(function () {
        $('#add-emp-title').val('');
        $('#title-modal').modal({backdrop: 'static'});
    });

    $('#title-btn').click(function (e) {
        e.preventDefault();
        var title = $.trim($('#add-emp-title').val());
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'title': title},
            url: '<?php echo site_url("Employee/new_empTitle"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                var emp_title = $('#emp_title');
                if (data[0] == 's') {
                    emp_title.append('<option value="' + data[2] + '">' + title + '</option>');
                    emp_title.val(data[2]);
                    $('#title-modal').modal('hide');
                }


            }, error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });
    });

    function fetch_emergency_contact_details(){
        $('#emergency_contact_tbl').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Employee/fetch_emergency_contact_details'); ?>",
            "aaSorting": [[0, 'desc']],
            "searching": false,
            "bLengthChange": false,
            "columnDefs": [ {
                "targets": [0,1,2,3,4,5,6,7],
                "orderable": false
            } ],
            "fnInitComplete": function () {
            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                $(".switch-chk").bootstrapSwitch();
                if(fromHiarachy == 1){
                    //Otable.column( 6 ).visible( false );
                    $(".switch-chk").bootstrapSwitch("disabled",true);
                }
            },
            "aoColumns": [
                {"mData": "contactName"},
                {"mData": "personToContactTelephone"},
                {"mData": "personToContactMobile"},
                {"mData": "address"},
                {"mData": "relationship"},
                {"mData": "CountryDes"},
                {"mData": "isMajorAction"},
                {"mData": "action"}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "empID", "value": empID});
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

    function add_emergencyContact(){
        $('#emergencyContact_frm')[0].reset();
        $('.contact-frm-select').change();
        $('#emergency_contact_modal_title').html(' - <?php echo $this->lang->line('common_add');?>');
        $('#emergencyContact_modal').modal('show');
    }

    function update_emergencyContactDetails(){
        var postData = $('#emergencyContact_frm').serializeArray();
        postData.push({name:'empID', value: empID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('Employee/update_emergency_contact_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if(data[0] == 's'){
                    $('#emergencyContact_modal').modal('hide');

                    setTimeout(function(){
                        fetch_emergency_contact_details();
                    }, 400);
                }
            }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
            }
        });
    }

    function edit_emergencyContact(obj){
        var table = $('#emergency_contact_tbl').DataTable();
        var thisRow = $(obj);
        var details = table.row(  thisRow.parents('tr') ).data();

        $('#emergencyContact_frm')[0].reset();

        $('#emergency_autoID').val( details.autoID );
        $('#contactName').val( details.personToContactName );
        $('#telNo').val( details.personToContactTelephone );
        $('#mobile').val( details.personToContactMobile );
        $('#address').val( details.personToContactAddress );
        $('#em_cont_relationshipType').val( details.relationshipType );
        $('#country').val( details.country );

        $('#contactName_other').val( details.personToContactName_O );
        $('#address_other').val( details.personToContactAddress_O );
        $('.contact-frm-select').change();

        $('#emergency_contact_modal_title').html(' - <?php echo $this->lang->line('common_edit');?>');
        $('#emergencyContact_modal').modal('show');
    }

    function delete_emergencyContact(obj){
        var table = $('#emergency_contact_tbl').DataTable();
        var thisRow = $(obj);

        var details = table.row( thisRow.parents('tr') ).data();
        var autoID = details.autoID;

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55 ",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: { 'delID': autoID },
                    url: "<?php echo site_url('Employee/delete_emergencyContact'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if(data[0] == 's'){
                            fetch_emergency_contact_details();
                        }
                    }, error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            }
        );
    }

    function change_emergency_contact_status(obj, id){
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('common_are_you_sure_you_want_to_make_this_as_default');?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        async: true,
                        url: "<?php echo site_url('Employee/changeEmergencyContactDetails'); ?>",
                        type: 'post',
                        dataType: 'json',
                        data: {'hidden-id': id, 'empID': empID},
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                setTimeout(function () {
                                    fetch_emergency_contact_details();
                                }, 400);
                            }
                            else{
                                reverseDefaultEmContactDetails(obj, id);
                            }
                        }, error: function () {
                            reverseDefaultEmContactDetails(obj, id);
                            stopLoad();
                            myAlert('e', 'error');
                        }
                    });
                }
                else {
                    reverseDefaultEmContactDetails(obj, id);
                }
            }
        );
    }

    function reverseDefaultEmContactDetails(obj, id){
        var changeStatus = ( $(obj).prop('checked') ) ? false : true;
        $('#emergency_contact_status' + id).prop('checked', changeStatus).change();
    }

    function fetch_employee_assets() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {empID: empID},
            url: "<?php echo site_url('Employee/fetch_employee_assets'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#assets_tab").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#assets_tab").html('<div class="alert alert-danger"><?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.<br/><strong><?php echo $this->lang->line('emp_error_message');?> : </strong>' + errorThrown + '</div>');<!--An Error Occurred! Please Try Again-->/*Error Message*/
                stopLoad();
            }
        });
    }
</script>
<?php
