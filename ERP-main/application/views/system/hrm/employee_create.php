<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('religion', $primaryLanguage);
$this->lang->load('country', $primaryLanguage);

echo head_page($this->lang->line('emp_add_employee'), false);/*'Add Employee'*/
$emp_title = fetch_emp_title();
$religion = fetch_emp_religion();
$Nationality = fetch_emp_nationality();
$MaritialStatus = fetch_emp_maritialStatus();
$BloodGroup = fetch_emp_blood_type();
$counties = fetch_emp_countries();
$empCode = '';//empCodeGenerate();
$current_date = '';
$date_format_policy = date_format_policy();
$empID = $this->input->post('page_id');
$fromHiarachy = $this->input->post('policy_id');
$fromHiarachy = (empty($fromHiarachy))?0:$fromHiarachy;

$isPendingDataAvailable = 0;
$isNeedApproval = getPolicyValues('EPD', 'All'); /** Check company policy on 'Approval for Employee Personal Detail Update' **/
if($isNeedApproval == 1 && !empty($empID)){
    $isPendingDataAvailable = ( !empty(get_pendingEmpApprovalData($empID)) ) ? 1 : 0;

    if($isPendingDataAvailable == 0){
        $isPendingDataAvailable = ( !empty(get_pendingFamilyApprovalData($empID, 'Y')) ) ? 1 : 0;
    }
}

$isAuthenticateNeed = 0;
if($fromHiarachy == 0){
    $isAuthenticateNeed = emp_master_authenticate(); /** Check company policy on 'Employee Master Edit Approval' **/
    $fromHiarachy = $isAuthenticateNeed;
    $isAuthenticateNeed = 1;
}

if($fromHiarachy == 1){ $isPendingDataAvailable = 0; }
?>

<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<script type="text/javascript"
        src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<style type="text/css">
    #changeImg {
        width: 235px;
        height: 125px;
    }

    #changeImg:hover {
        cursor: pointer;
    }

    #changeSignatureImg {
        width: 354px;
        height: 80px;
    }

    #changeSignatureImg:hover {
        cursor: pointer;
    }

    fieldset {
        border: 1px solid silver;
        border-radius: 5px;
        padding: 1%;
        padding-bottom: 15px;
        margin: auto;
    }

    legend {
        width: auto;
        border-bottom: none;
        margin: 0px 10px;
        font-size: 20px;
        font-weight: 500
    }

    .discharged {
        background-image: url('<?php echo base_url() . 'images/discharged.png';?>');
        background-repeat: no-repeat;
        width: 100%;
        height: 100%;
    }

    #pendingData:hover{
        cursor: pointer;
        color : #292224 !important;
    }
</style>
<?php

function language_string_conversion2($string)
{
    $outputString = strtolower(str_replace(array('-', ' ', '&', '/'), array('_', '_', '_', '_'), trim($string)));

    return $outputString;


}

/**/

?>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary btn-sm navdisabl" href="#step1" data-toggle="tab">
        <?php echo $this->lang->line('emp_personal_detail'); ?>
    </a>
    <a class="btn btn-default btn-sm navdisabl btn-wizard" href="#step2" data-toggle="tab">
        <?php echo $this->lang->line('emp_contact_detail'); ?>
    </a>
    <a class="btn btn-default btn-sm navdisabl btn-wizard" href="#familydetails_tab" onclick="fetch_family_details()"
       data-toggle="tab"><?php echo $this->lang->line('emp_family_details');?><!--Family Details--></a>

    <a class="btn btn-default btn-sm navdisabl btn-wizard" href="#employment_tab" onclick="fetch_employment()"
       data-toggle="tab"><?php echo $this->lang->line('emp_employment'); ?></a>
    <!--<a class="btn btn-default btn-sm navdisabl btn-wizard" href="#department_tab" onclick="fetch_departments()"
       data-toggle="tab">Departments</a>-->
    <!--<a class="btn btn-default btn-sm navdisabl btn-wizard" href="#contract_tab" data-toggle="tab">Contract Details</a>-->
    <a class="btn btn-default btn-sm navdisabl btn-wizard" href="#document_tab" onclick="fetch_document()"
       data-toggle="tab"><?php echo $this->lang->line('emp_document'); ?></a>
    <a class="btn btn-default btn-sm navdisabl btn-wizard" href="#salary_tab" onclick="fetch_salaryDet()"
       data-toggle="tab"><?php echo $this->lang->line('emp_salary'); ?></a>
    <a class="btn btn-default btn-sm navdisabl btn-wizard" href="#accounts_tab" onclick="fetch_accounts()"
       data-toggle="tab"><?php echo $this->lang->line('emp_bank'); ?></a>
    <a class="btn btn-default btn-sm navdisabl btn-wizard" href="#qualification_tab" onclick="fetch_qualification()"
       data-toggle="tab"><?php echo $this->lang->line('emp_qualification'); ?></a>
    <a class="btn btn-default btn-sm navdisabl btn-wizard" href="#attendance_tab" onclick="fetch_attendance()"
       data-toggle="tab"><?php echo $this->lang->line('emp_attendance'); ?></a>
    <a class="btn btn-default btn-sm navdisabl btn-wizard" href="#si_tab" onclick="fetch_socail_insurance()"
       data-toggle="tab"><?php echo $this->lang->line('emp_social_insurance'); ?> </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#assets_tab" onclick="fetch_employee_assets()"
       data-toggle="tab"><?php echo $this->lang->line('emp_master_assets'); ?> </a>
    <a class="btn btn-default btn-sm navdisabl btn-wizard" href="#discharged_tab" onclick="fetch_discharged_detail()"
       data-toggle="tab"><?php echo $this->lang->line('emp_discharged'); ?></a>
</div>
<hr>

<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="employee_form" '); ?> <!-- autocomplete="off"-->
        <div class="row">
            <div class="col-sm-3">
                <div class="fileinput-new thumbnail" id="emp-image-container" style="width: 136px; height: 135px;">
                    <img src="<?php echo base_url('images/users/default.gif'); ?>" id="changeImg">
                    <input type="file" name="empImage" id="empImage" style="display: none;"
                           onchange="loadImage(this)"/>
                </div>
            </div>
            <div class="col-sm-9">
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
                    <div class="form-group col-sm-3">
                        <label for="shortName">
                            <?php echo $this->lang->line('emp_calling_name'); ?><?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="shortName" name="shortName">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="fullName">
                            <?php echo $this->lang->line('emp_name_with_initials'); ?><?php required_mark(); ?></label>

                        <div class="input-group" style="width: 100%; ">
                            <input type="text" class="form-control input-sm" value="" id="initial" name="initial"
                                   placeholder="Initial" style="width: 50px"/>
                            <span class="input-group-btn" style="width:0px;"></span>
                            <input type="text" class="form-control input-sm" value="" id="Ename4" name="Ename4"
                                   placeholder="Name"/>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="fullName">
                            <?php echo $this->lang->line('emp_full_name'); ?><?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="fullName" name="fullName">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="fullName">
                            <?php echo $this->lang->line('emp_surname'); ?>
                        </label>
                        <input type="text" class="form-control" id="Ename3" name="Ename3">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="emp_gender"><?php echo $this->lang->line('emp_gender'); ?></label>

                <div class="form-control">
                    <label class="radio-inline">
                        <input type="radio" name="emp_gender" value="1" id="male" class="gender"
                               checked="checked">Male
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="emp_gender" value="2" id="feMale" class="gender">Female
                    </label>
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
            <div class="form-group col-sm-3">
                <label for="MaritialStatus"><?php echo $this->lang->line('emp_marital_status'); ?></label>
                <?php echo form_dropdown('MaritialStatus', $MaritialStatus, '', 'class="form-control" id="MaritialStatus" '); ?>
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
                    <!--<input type="text" name="empDob" value="<?php /*echo $current_date; */ ?>" id="empDob"
                           class="form-control dateFields">-->
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="religion"><?php echo $this->lang->line('emp_blood_group'); ?></label>
                <?php echo form_dropdown('BloodGroup', $BloodGroup, '', 'class="form-control" id="BloodGroup" '); ?>
            </div>
            <div class="form-group col-sm-3">
                <label
                    for="emp_email"><?php echo $this->lang->line('emp_primary_e-mail'); ?><?php required_mark(); ?></label>

                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                    <input type="email" class="form-control " id="emp_email" name="emp_email">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="empMachineID"><?php echo $this->lang->line('emp_nic_no'); ?></label>
                <input type="text" class="form-control" id="NIC" name="NIC">
            </div>
        </div>


        <div class="row">
            <div class="col-sm-3" align="">
                <label for=""><?php echo $this->lang->line('emp_signature'); ?></label>

                <div class="fileinput-new thumbnail" style="width: 226px;height: 90px;">
                    <img src="<?php echo base_url('images/No_Image.png'); ?>" id="changeSignatureImg">
                    <input type="file" name="empSignatureImage" id="empSignatureImage" style="display: none;"
                           onchange="loadSigImage(this)"/>
                </div>
            </div>
        </div>

        <hr>
        <div class="text-right m-t-xs">
            <input type="hidden" id="requestLink">
            <input type="hidden" id="updateID" name="updateID">
            <input type="hidden" id="isConfirmed" name="isConfirmed">
            <button class="btn btn-info" type="button" data-type="disabled" style="display: none;" id="editBtn">
                <?php echo $this->lang->line('emp_edit'); ?>
            </button> <!--Edit -->
            <button class="btn btn-primary btn-sm submitBtn" id="saveBtn" type="submit">Save</button>
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
    <div id="step2" class="tab-pane">
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
                <button class="btn btn-xs btn-primary pull-right" id="save_itm_btn" type="button" onclick="copy_permanent_details();">Copy Detail</button>
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
                        <label for="emp_mobile"><?php echo $this->lang->line('emp_mobile_no'); ?><!--Mobile--></label>

                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></div>
                            <input type="tel" class="form-control " id="emp_mobile" name="emp_mobile">
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
    </div>
    <!--<div id="contract_tab" class="tab-pane">
        <?php /*echo form_open('', 'role="form" id="employeeVisa_form" '); */ ?>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group col-sm-3 col-xs-6">
                    <label for="contractStartDate">Contract Start Date</label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="contractStartDate" value="" id="contractStartDate" class="form-control"
                               data-inputmask="'alias': '<?php /*echo $date_format_policy */ ?>'">
                    </div>
                </div>

                <div class="form-group col-sm-3 col-xs-6">
                    <label for="contractEndDate">Contract End Date</label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="contractEndDate" value="" id="contractEndDate" class="form-control"
                               data-inputmask="'alias': '<?php /*echo $date_format_policy */ ?>'">
                    </div>
                </div>

                <div class="form-group col-sm-3 col-xs-6">
                    <label for="airport_destination">Contract Ref No.</label>
                    <input type="text" name="contractRefNo" id="contractRefNo" class="form-control"
                           placeholder="">
                </div>

            </div>

            <div class="col-md-12">
                <div class="form-group col-sm-3 col-xs-6">
                    <label for="pass_portNo">Passport No</label>
                    <input type="text" class="form-control" id="pass_portNo" name="pass_portNo"
                           placeholder="Passport No" style="width:100%;">
                </div>

                <div class="form-group col-sm-3 col-xs-6">
                    <label for="passPort_expiryDate">Passport Expiry Date</label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="passPort_expiryDate" value="" id="passPort_expiryDate" class="form-control"
                               data-inputmask="'alias': '<?php /*echo $date_format_policy */ ?>'">
                    </div>
                </div>

                <div class="form-group col-sm-3 col-xs-6">
                    <label for="passPort_expiryDate">Visa Expiry Date</label>
                    <div class="input-group datepic">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        <input type="text" name="visa_expiryDate" value="" id="visa_expiryDate" class="form-control"
                               data-inputmask="'alias': '<?php /*echo $date_format_policy */ ?>'">
                    </div>
                </div>

                <div class="form-group col-sm-3 col-xs-6">
                    <label for="airport_destination">Airport Destination</label>
                    <input type="text" name="airport_destination" id="airport_destination" class="form-control"
                           placeholder="Airport Destination">
                </div>
            </div>
        </div>
        <hr>

        <div class="row" style="margin-bottom: 2%">
            <div class="col-md-12">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary btn-sm" id="visa-update" type="button">Save Changes</button>
                </div>
            </div>
        </div>
        <?php /*echo form_close(); */ ?>
    </div>-->
    <div id="designation_tab" class="tab-pane"></div>
    <div id="familydetails_tab" class="tab-pane"></div>
    <div id="employment_tab" class="tab-pane"></div>
    <div id="qualification_tab" class="tab-pane"></div>
    <div id="document_tab" class="tab-pane"></div>
    <div id="salary_tab" class="tab-pane"></div>
    <div id="department_tab" class="tab-pane"></div>
    <div id="accounts_tab" class="tab-pane"></div>
    <div id="attendance_tab" class="tab-pane"> </div>
    <div id="si_tab" class="tab-pane"></div>
    <div id="assets_tab" class="tab-pane"></div>

    <div id="discharged_tab" class="tab-pane">

        <form action="#" id="dischargedForm" class="dischargedForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="checkbox">
                        <label class="checkbox"><input type="checkbox" id="isDischarged" name="isDischarged"
                                                       value="1">
                            <?php echo $this->lang->line('emp_is_discharged'); ?><!--Is Discharged--></label>
                    </div>
                    <div class="form-group ">
                        <label for="dischargedDate">
                            <?php echo $this->lang->line('emp_discharged_date'); ?><!--Discharged Date--></label>

                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="dischargedDate" value="<?php echo $current_date; ?>"
                                   id="dischargedDate"
                                   class="form-control dateFields">
                        </div>

                    </div>
                    <div class="form-group ">
                        <label for="dischargedDate">
                            <?php echo $this->lang->line('emp_lastworking_date'); ?><!--Last Working Date--></label>

                        <div class="input-group">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="lastWorkingDate" value="<?php echo $current_date; ?>"
                                   id="lastWorkingDate"
                                   class="form-control dateFields">
                        </div>

                    </div>
                    <div class="form-group ">
                        <label for="zip_code">
                            <?php echo $this->lang->line('emp_discharged_comment'); ?><!--Discharged Comment--></label>
                        <textarea name="dischargedComment" id="dischargedComment" class="form-control"
                                  rows="1"></textarea>
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
        </form>

    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>

<div class="modal fade" id="title-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">New Employee Title</h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Title</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="add-emp-title" name="add-emp-title">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="title-btn">Save</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="addFamilyDetailModal" data-width="80%"
     role="dialog">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                </button>
                <h5> Add Family Detail</h5>
            </div>
            <div class="modal-body" id="modal_contact">
                <form method="post" name="frm_FamilyContactDetails" id="frm_FamilyContactDetails"
                      class="form-horizontal">
                    <input type="hidden" value="0" id="empfamilydetailsID"
                           name="empfamilydetailsID"/>
                    <input type="hidden" value="" id="empID_familyDetail" name="employeeID"/>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="textinput">Name</label>

                        <div class="col-md-7">
                            <input class="form-control input-md" placeholder="Name" id="name" name="name"
                                   type="text" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="relationshipType">Relationship</label>

                        <div class="col-md-7">
                            <?php echo form_dropdown('relationshipType', hrms_relationship_drop(), '', 'id="relationshipType" class="form-control"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="country">Nationality</label>

                        <div class="col-md-7">
                            <?php echo form_dropdown('nationality', load_all_nationality_drop(), '', 'id="nationality" class="form-control select2"'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Date of Birth</label>

                        <div class="input-group datepic col-md-7" style="padding-left: 15px;">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="DOB" style="width: 94%;"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="DOB" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label" for="gender">Gender</label>

                        <div class="col-md-7">
                            <select name="gender" class="form-control empMasterTxt" id="gender">
                                <option value="1"> Male</option>
                                <option value="2"> Female</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div id="familyDetail_msg"></div>
            </div>
            <div class="modal-footer" style="background-color: #ffffff">
                <button type="button" class="btn btn-primary" onclick="saveFamilyDetails()">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " data-backdrop="static" id="modaluploadimages" data-width="60%" tabindex="-1"
     role="dialog">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h5>Attachments</h5>
        </div>
        <div class="modal-body" id="modal_contact" style="min-height:100px;background-color: #F5F5F5">
            <?php echo form_open_multipart('', 'id="family_image_uplode_form" class="form-horizontal"'); ?>
            <fieldset>
                <!-- Text input-->

                <input type="hidden" class="form-control" value="" id="empfamilydetailzID"
                       name="empfamilydetailsID">


                <!-- File Button -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton">Attachment</label>

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

            <button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Cancel</button>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="modaluploadattachment" data-width="60%" tabindex="-1"
     role="dialog">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h5>Attachments</h5>
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
                    <label class="col-md-4 control-label" for="filebutton">Attachment</label>

                    <div class="col-md-8">
                        <input type="file" name="document_file" class=" input-md" id="image_file">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton">Description</label>

                    <div class="col-md-8">
                        <input type="text" name="attachmentDescription" id="attachmentDescription">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="filebutton">Document</label>

                    <div class="col-md-8">
                        <select name="documentID" id="documentID" class="form-control">
                            <option value="1">Passport</option>
                            <option value="2">Visa</option>
                            <option value="3">Insurance</option>
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
                        <th style="min-width: 15%">Description</th>
                        <th style="min-width: 20%">Document</th>
                        <th style="min-width: 5%">Action</th>
                    </tr>
                    </thead>
                </table>
            </div>

        </div>
        <div class="modal-footer" style="background-color: #ffffff">

            <button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Cancel</button>
        </div>
    </div>
</div>

<div class="modal fade" id="rejoin-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" id="reJoin-form" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Rejoin Employee</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-8 col-xs-12">
                            <div class="form-group">
                                <label class="col-sm-4 col-xs-2 control-label" style="text-align: left">Rejoin Date</label>
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
                                <legend>Select which details you want to copy</legend>
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="rejoinEmp()">Proceed</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="pending-approval-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form role="form" id="pending-approval-form" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">Employee Personal Data Pending Approval</h3>
                </div>
                <div class="modal-body" id="pending-response">

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="empID" value="<?php echo trim($this->input->post('page_id'));?>">
                    <button type="button" class="btn btn-primary btn-sm" onclick="approve_personalData()">Proceed</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    var fromHiarachy = <?php echo $fromHiarachy ?>;
    var isAuthenticateNeed = '<?php echo $isAuthenticateNeed ?>';
    var eCodePolicy = <?php echo json_encode(getPolicyValues('ECG', 'All'))?>;
    var setSecondaryCodeDisable = false;

    if(fromHiarachy == 1){
        $('.btn ').addClass('hidden');
        $('.navdisabl ').removeClass('hidden');
        $('.form-control:not([type="search"], #parentCompanyID)').attr('disabled', true);
    }

    Inputmask().mask(document.querySelectorAll("input"));
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';


    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy
    });

    var employee_form = $("#employee_form");
    $(document).ready(function () {

        var empID = <?php echo json_encode(trim($empID)); ?>;
        var isPendingDataAvailable = '<?php echo $isPendingDataAvailable; ?>';

        if (empID) {
            employee_details(empID);
            $('#saveBtn').hide();
            $('#updateBtn').show();
            $('#editBtn').show();
            $('.btn-wizard').removeClass('disabled');

            $('#box-header-title').text('Edit Employee ');/*'Edit Employee '*/
            $('#box-header-title').append('| <div class="pull-right" id="box-content"><span id="employeeName" style="font-weight: bold;font-size: 14px; margin-top: 3px;">' +
                '  </span></div> ');

            if( isPendingDataAvailable == 1){
                setTimeout(function(){
                    $('#employeeName').after('<span class="label label-warning pull-right" onclick="load_pendingData()" id="pendingData">Pending Personal Data Update</span>');
                }, 300);
            }


            $('#step1 input,#step1 textarea,#step1 select').attr('disabled', true);
            $('#updateBtn, #confirmBtn').attr('disabled', true);
        } else {
            $('.btn-wizard').addClass('disabled');
            $('#empCurrency').val('<?php echo $this->common_data['company_data']['company_default_currency']?>');
            $('#isPayrollEmployee').prop('checked', true);
        }

        $('.headerclose').click(function () {
            if(isAuthenticateNeed == 1){
                fetchPage('<?php echo $master_page_url; ?>', empID, 'HRMS');
            }
            else if(fromHiarachy == 1){
                fetchPage('system/profile/profile_information', '', 'Profile',1);
            }else{
                fetchPage('<?php echo $master_page_url; ?>', empID, 'HRMS');
            }

        });

        $('.dateFields').datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            $(this).datepicker('hide');
            var filedName = $(this).attr('name');
            if (filedName == 'empDoj') {
                $('#DateAssumed').val($(this).val());
            }
            employee_form.bootstrapValidator('revalidateField', $(this).attr('id'));
        });

        $('#editBtn').click(function () {
            var type = $(this).data('type')
            if ($(this).hasClass('disabled')) {
                return false;
            }
            if (type == 'disabled') {
                $(this).data('type', 'enabled')
                $('#step1 input,#step1 textarea,#step1 select').attr('disabled', false);
                if(setSecondaryCodeDisable == true){
                    $('#EmpSecondaryCode').attr('disabled', true);
                }
                $('#updateBtn, #confirmBtn').attr('disabled', false);
            } else {
                $(this).data('type', 'disabled')
                $('#step1 input,#step1 textarea,#step1 select').attr('disabled', true);
                $('#updateBtn, #confirmBtn').attr('disabled', true);
            }
        })

        employee_form.bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                emp_title: {validators: {notEmpty: {message: 'Title is required.'}}},
                fullName: {validators: {notEmpty: {message: 'Full Name is required.'}}},
                emp_gender: {validators: {notEmpty: {message: 'Gender is required.'}}},
                /*empDob: {validators: {notEmpty: {message: 'Date of birth is required.'}}},
                 empDoj: {validators: {notEmpty: {message: 'Date of joined is required.'}}},
                 religion: {validators: {notEmpty: {message: 'Religion is required.'}}},*/
                empDoj: {validators: {notEmpty: {message: 'Date of joined is required.'}}},
                DateAssumed: {validators: {notEmpty: {message: 'Date Assumed is required.'}}},
                Ename4: {validators: {notEmpty: {message: 'Name is required.'}}},
                intial: {validators: {notEmpty: {message: 'Initials is required.'}}},
                emp_email: {validators: {notEmpty: {message: 'E-Mail required.'}}},
                designation: {validators: {notEmpty: {message: 'Designation required.'}}},
                //telNo1: {validators: {notEmpty: {message: 'Telephone No is required.'}}},
                empCurrency: {validators: {notEmpty: {message: 'Currency is required.'}}},
                empSegment: {validators: {notEmpty: {message: 'Segment is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            $('.submitBtn').prop('disabled', false);
            e.preventDefault();
            var requestUrl = $('#requestLink').val();
            save_update(requestUrl);
        });

        $('#employeeContact_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
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

        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });
    });

    $('.submitBtn').click(function () {
        if ($(this).hasClass('updateBtn')) {
            $('#requestLink').val('<?php echo site_url('Employee/update_employee'); ?>');
        } else {
            $('#requestLink').val('<?php echo site_url('Employee/new_employee'); ?>');
        }

        var isConfirmed = ( $(this).attr('data-value') == 1 ) ? '1' : '0';
        $('#isConfirmed').val(isConfirmed);
    });

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

    function employee_details(empID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'empID': empID},
            url: '<?php echo site_url("Employee/new_employee_details"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('.btn-wizard').removeClass('disabled');
                $('#updateID').val(empID);

                $('#emp_title').val(data['EmpTitleId']);
                $('#fullName').val(data['Ename1']);
                $('#employeeName').html('<i class="fa fa-fw fa-user"></i> ' + data['Ename2'] + ' - ' + data['ECode']);
                $('#shortName').val(data['EmpShortCode']);
                $('#EmpSecondaryCode').val(data['EmpSecondaryCode']);
                $('#empDob').val(data['EDOB']);
                $('#empDoj').val(data['EDOJ']);
                $('#religion').val(data['rid']);
                $('#telNo1').val(data['EpTelephone']);
                $('#telNo2').val(data['EcTel']);
                $('#emp_mobile').val(data['EcMobile']);
                $('#emp_email').val(data['EEmail']);
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

                /*Discharged*/
                if (data['isDischarged'] == 1) {
                    $('#isDischarged').prop('checked', true);
                    $('#dischargedComment').val(data['dischargedComment']);
                    $('#dischargedDate').val(data['dischargedDate']);
                    $('#editBtn').addClass('disabled')
                    $('#isDischarged,#dischargedComment,#dischargedDate,#dischargeUpdate,#lastWorkingDate').prop('disabled', true);
                    $('#reJoinBtn').show();
                    $('#editBtn, #updateBtn, #confirmBtn').hide();

                    $('#emp-image-container').addClass('discharged');
                    $('#changeImg').css('opacity', '0.4');
                }


                var empImageUrl = data['EmpImage'];
                var empImageSignatureUrl = data['empSignature'];


                var empImage = $('#changeImg');
                var empSignatureImage = $('#changeSignatureImg');
                empImage.attr('src', empImageUrl);
                empSignatureImage.attr('src', empImageSignatureUrl);


                $('.gender').prop('checked', false);
                if (data['Gender'] == 1) {
                    $('#male').prop('checked', true);
                } else {
                    $('#feMale').prop('checked', true);
                }

                if(empID == '<?=current_userID()?>'){ // current user image may be updated
                    $('.current-user-img').attr('src', data['EmpImage']);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
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
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });

    $('#changeImg').click(function () {
        $('#empImage').click();
    });

    $('#changeSignatureImg').click(function () {
        $('#empSignatureImage').click();
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

    function loadSigImage(obj) {
        if (obj.files && obj.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#changeSignatureImg').attr('src', e.target.result);
            };

            reader.readAsDataURL(obj.files[0]);
        }
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

    function fetch_employment() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID},
            url: '<?php echo site_url("Employee/load_employmentView"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#employment_tab').html(data);


            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
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
                myAlert('e', 'An Error Occurred! Please Try Again.');
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
                myAlert('e', 'An Error Occurred! Please Try Again.');
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
                if(fromHiarachy == 1){
                    $('.btn ').addClass('hidden');
                    $('.navdisabl ').removeClass('hidden');
                    $('.form-control:not([type="search"], #parentCompanyID)').attr('disabled', true);
                }

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
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
                myAlert('e', 'An Error Occurred! Please Try Again.');
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
                alert('An Error Occurred! Please Try Again.');
            }

        });
    }

    function fetch_salaryDet2() {
        var empID = $('#updateID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': empID},
            url: '<?php echo site_url("Employee/load_empSalary"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                $('#salary_tab').html(data);

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
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
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function fetch_socail_insurance() {
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
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }


    $('.number').keypress(function (event) {

        if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });


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
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function fetch_discharged_detail() {
    }

    function dischargedUpdate() {

        swal({
                title: "Are you sure?",
                text: "You want to discharge this employee!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Confirmed",
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
                            $('#isDischarged,#dischargedComment,#dischargedDate,#dischargeUpdate').prop('disabled', true);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        stopLoad();
                        myAlert('e', errorThrown);
                    }
                });
            });
    }

    function confirm_employee() {
        swal({
                title: "Are you sure?",
                text: "You want to confirm this!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                employee_form.submit();
            }
        );
    }


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
                $("#familydetails_tab").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#familydetails_tab").html('<div class="alert alert-danger">An Error Occurred! Please Try Again.<br/><strong>Error Message: </strong>' + errorThrown + '</div>');
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
                $("#familyDetail_msg").html('<div class="alert alert-danger"><strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown + '</div>');
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
                myAlert('e', 'Please contact support Team');
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
                myAlert('e', 'Please contact support Team');
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

    function copy_permanent_details(){
        $('#ec_address1').val($('#ep_address1').val());
        $('#ec_address2').val($('#ep_address2').val());
        $('#ec_address3').val($('#ep_address3').val());
        $('#ec_address4').val($('#ep_address4').val());
        $('#ec_pc').val($('#zip_code').val());
        $('#ec_fax').val($('#ep_fax').val());
    }

    function load_pendingData(){
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'empID': '<?php echo trim($this->input->post('page_id'));?>'},
            url: "<?php echo site_url('Profile/fetch_pendingEmpDataApproval'); ?>",
            beforeSend: function () {
                startLoad();
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