<?php
echo head_page('Student Details', false);

$this->load->helper('sm_school_helper');


$Stu_Name = fetch_Stu_Name();
$category = fetch_category();
$religion = fetch_religion();
$nationality = fetch_nationality();
$bloodgroup = fetch_bloodgroup();
$grade = fetch_grade();
$group = fetch_group();
$admitted_year = fetch_admitted_year();
$contact_person = fetch_contact_person();
$parental_status = fetch_parental_status();
$country = fetch_country();
$area = fetch_area();
$drop_location = fetch_drop_location();
$travel_by = fetch_travel_by();
$journey_type = fetch_journey_type();
$left_year = fetch_left_year();

?>

<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>

<style type="text/css">
    #changeImg {
        width: 235px;
        height: 235px;
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
        background-image: url('<?php echo base_url() . 'images/discharged.png'; ?>');
        background-repeat: no-repeat;
        width: 100%;
        height: 100%;
    }

    #pendingData:hover {
        cursor: pointer;
        color: #292224 !important;
    }

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

<?php
function language_string_conversion2($string)
{
    $outputString = strtolower(str_replace(array('-', ' ', '&', '/'), array('_', '_', '_', '_'), trim($string)));

    return $outputString;
}
?>

<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary btn-sm navdisabl " href="#Personal_tab" data-toggle="tab">
        <?php echo $this->lang->line('Stu_personal_detail'); ?> Personal
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#contact_tab" data-toggle="tab">
        <?php echo $this->lang->line('Stu_contact_detail'); ?> Contact
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#transport_tab" onclick="fetch_transport_tab()" data-toggle="tab">
        <?php echo $this->lang->line('Stu_transport_detail'); ?>Transport
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#machine_input_tab" onclick="fetch_machine_input_tab()" data-toggle="tab">
        <?php echo $this->lang->line('Stu_machine_input_tab'); ?> Machine Input

    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Sponsor_tab" onclick="fetch_Sponsor()" data-toggle="tab">
        <?php echo $this->lang->line('Stu_Sponsor_detail'); ?> Sponsor
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Passport_tab" onclick="fetch_Passport()" data-toggle="tab">
        <?php echo $this->lang->line('Stu_Passport_detail'); ?> Passport
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Caring_tab" onclick="fetch_Caring()" data-toggle="tab">
        <?php echo $this->lang->line('Stu_Caring_detail'); ?> Caring
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Siblings_tab" onclick="fetch_Siblings()" data-toggle="tab">
        <?php echo $this->lang->line('Stu_Siblings_detail'); ?> Siblings
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Attachments_tab" onclick="fetch_Attachements()" data-toggle="tab">
        <?php echo $this->lang->line('Stu_Attachements_detail'); ?> Attachements
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Documents_tab" onclick="fetch_Documents()" data-toggle="tab">
        <?php echo $this->lang->line('Stu_Documents_detail'); ?> Documents
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Status_tab" onclick="fetch_Status()" data-toggle="tab">
        <?php echo $this->lang->line('Stu_Status_detail'); ?> Status
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Summary_tab" onclick="fetch_Summary_detail()" data-toggle="tab">
        <?php echo $this->lang->line('Stu_Summary_detail'); ?> Summary
    </a>
</div>
<hr>
<div class="tab-content">
    <div id="Personal_tab" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="student_form" '); ?> <!-- autocomplete="off"-->
        <div class="row">
            <div class="col-sm-3">
                <div class="fileinput-new thumbnail" id="Stu-image-container" style="width: 236px; height: 250px;">
                    <img src="<?php echo base_url('images/users/student.png'); ?>" id="changeImg">
                    <input type="file" name="StuImage" id="StuImage" style="display: none;" onchange="loadImage(this)" />
                </div>
            </div>
            <div class="col-sm-9">
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="otherName"><?php echo $this->lang->line('Stu_serialNo'); ?>Serial No:</label>
                        <input type="text" class="form-control" id="SerialNo" name="SerialNo" value="">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="otherName"><?php echo $this->lang->line('Stu_Code'); ?>Student Code:</label>
                        <input type="text" class="form-control" id="StuCode" name="StuCode">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="Stu_Name"><?php echo $this->lang->line('Stu_Name'); ?>Name: <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="Stu_Name" name="Stu_Name" value="">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="otherName">
                            <?php echo $this->lang->line('Stu_other_name'); ?>Name (Other):</label>
                        <input type="text" class="form-control" id="otherName" name="otherName">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="fullName">
                            <?php echo $this->lang->line('Stu_surname'); ?>Surname:
                        </label>
                        <input type="text" class="form-control" id="Ename3" name="Ename3">
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="fullName">
                            <?php echo $this->lang->line('Stu_name_with_initials'); ?>Name with initials: </label>

                        <div class="input-group" style="width: 100%; ">
                            <input type="text" class="form-control input-sm" value="" id="initial" name="initial" placeholder="Initial" style="width: 50px" />
                            <span class="input-group-btn" style="width:0px;"></span>
                            <input type="text" class="form-control input-sm" value="" id="Ename4" name="Ename4" placeholder="Name" />
                        </div>
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="StuDob"><?php echo $this->lang->line('Stu_date_of_birth'); ?>Date of Birth: <?php required_mark(); ?></label>
                        <!-- <div class="input-group datepic">
                            <div class="input-group-addon"> -->
                        <i class="fa fa-calender" aria-hidden="true"></i>
                        <!-- </div> -->
                        <input type='date' class="form-control" id="StuDob" name="StuDob">
                        <!-- </div> -->
                    </div>
                    <div class="form-group col-sm-3">
                        <label for="Age">
                            <?php echo $this->lang->line('Stu_age'); ?>Age: <?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="age" name="age">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="Stu_gender"><?php echo $this->lang->line('Stu_gender'); ?>Gender: <?php required_mark(); ?></label>

                        <div class="form-control">
                            <label class="radio-inline">
                                <input type="radio" name="Stu_gender" value="1" id="male" class="gender" checked="checked">Male
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="Stu_gender" value="2" id="feMale" class="gender">Female
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="category"><?php echo $this->lang->line('Stu_category'); ?>Category:</label><br>
                        <?php echo form_dropdown('category[]', $category, '', 'class="form-control" id="category" onchange="load_category()"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="religion"><?php echo $this->lang->line('Stu_religion'); ?>Religion:</label><br>
                        <?php echo form_dropdown('religion[]', $religion, '', 'class="form-control" id="religion" onchange="load_religion()"'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Nationality"><?php echo $this->lang->line('Stu_nationality'); ?>Nationality</label><br>
                <?php echo form_dropdown('nationality[]', $nationality, '', 'class="form-control" id="nationality" onchange="load_nationality()"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="StuMachineID"><?php echo $this->lang->line('Stu_nic_no'); ?>NIC/Civil No/RC:</label>
                <input type="text" class="form-control" id="NIC" name="NIC">
            </div>
            <div class="form-group col-sm-3">
                <label for="bloodgroup"><?php echo $this->lang->line('Stu_blood_group'); ?>Blood group:</label><br>
                <?php echo form_dropdown('bloodgroup[]', $bloodgroup, '', 'class="form-control" id="bloodgroup" onchange="load_bloodgroup()"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="Stu_email"><?php echo $this->lang->line('Stu_primary_e-mail'); ?>Email:</label>

                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                    <input type="email" class="form-control " id="Stu_email" name="Stu_email">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="LastSchoolAttended">Last School Attended:
                    <?php echo $this->lang->line('Stu_last_school'); ?></label>
                <input type="text" class="form-control" id="LastSchoolAttended" name="LastSchoolAttended">
            </div>
            <div class="form-group col-sm-3">
                <label for="AdmittedGrade">Admitted Grade:
                    <?php echo $this->lang->line('Stu_admitted_grade'); ?></label>
                <input type="text" class="form-control" id="AdmittedGrade" name="AdmittedGrade">
            </div>
            <div class="form-group col-sm-3">
                <label for="admissionDate">Date of Admission: <?php required_mark(); ?></label>
                    <i class="fa fa-calender" aria-hidden="true"></i>
                    <input type="date" class="form-control" id="admissionDate" name="admissionDate">
            </div>
            <div class="form-group col-sm-3">
                <label for="AdmittedYear">Admitted Academic Year:
                    <?php echo $this->lang->line('Stu_admitted_year'); ?><?php required_mark(); ?></label>
                <?php echo form_dropdown('admitted_year[]', $admitted_year, '', 'class="form-control" id="admitted_year" onchange="load_year()"'); ?>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="PresentClass">Present class:
                    <?php echo $this->lang->line('Stu_present_class'); ?><?php required_mark(); ?></label>
                <div class="input-group" style="width: 100%; ">
                    <?php echo form_dropdown('grade[]', $grade, '', 'class="form-control" id="grade" onchange="load_grade()"'); ?>
                    <span class="input-group-btn" style="width:0px;"></span>
                    <?php echo form_dropdown('group[]', $group, '', 'class="form-control" id="group" onchange="load_group()"'); ?>
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="HomeAddress">Home Address:
                    <?php echo $this->lang->line('Stu_home_address'); ?></label>
                <input type="text" class="form-control" id="HomeAddress" name="HomeAddress">
            </div>
            <div class="form-group col-sm-3">
                <label for="BirthPlace">Place of Birth:
                    <?php echo $this->lang->line('Stu_birth_place'); ?></label>
                <input type="text" class="form-control" id="BirthPlace" name="BirthPlace">
            </div>
            <div class="form-group col-sm-3">
                <label for="Remarks">Remarks:
                    <?php echo $this->lang->line('Stu_remarks'); ?></label>
                <input type="text" class="form-control" id="Remarks" name="Remarks">
            </div>
        </div>

        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Stu_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('student_save'); ?>Save
            </button>

        </div>
    </div>
    <div id="contact_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="student_form" '); ?>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="contact">Contact Person:
                    <?php echo $this->lang->line('Stu_contact_person'); ?></label>
                    <?php echo form_dropdown('contact_person[]', $contact_person, '', 'class="form-control" id="contact_person" onchange="load_contact_person()"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="status">Parental Status:
                    <?php echo $this->lang->line('Stu_parental_status'); ?></label>
                    <?php echo form_dropdown('parental_status[]', $parental_status, '', 'class="form-control" id="parental_status" onchange="load_status()"'); ?>
            </div>
        </div>

        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Stu_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('student_save'); ?>Save
            </button>

        </div>
    </div>
    <div id="transport_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="student_form" '); ?>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="transport">Country:
                    <?php echo $this->lang->line('Stu_Country'); ?></label>
                    <?php echo form_dropdown('country[]', $country, '', 'class="form-control" id="country" onchange="load_country()"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="transport">Area:
                    <?php echo $this->lang->line('Stu_Area'); ?></label>
                    <?php echo form_dropdown('area[]', $area, '', 'class="form-control" id="area" onchange="load_area()"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="transport">Journey Type:
                    <?php echo $this->lang->line('Stu_Journey_Type'); ?></label>
                    <?php echo form_dropdown('journey_type[]', $journey_type, '', 'class="form-control" id="journey_type" onchange="load_journey_type()"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="transport">Drop Location:
                    <?php echo $this->lang->line('Stu_Drop_Location'); ?></label>
                    <?php echo form_dropdown('drop_location[]', $drop_location, '', 'class="form-control" id="drop_location" onchange="load_drop_location()"'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="transport">Travelled By:
                    <?php echo $this->lang->line('Stu_Travelled_By'); ?></label>
                    <?php echo form_dropdown('travel_by[]', $travel_by, '', 'class="form-control" id="travel_by" onchange="load_travel_by()"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="transport">Vehicle Number:
                    <?php echo $this->lang->line('Stu_Vehicle_No'); ?></label>
                <input type="text" class="form-control" id="Vehicle_No" name="Vehicle_No">
            </div>
        </div>

        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Stu_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('student_save'); ?>Save
            </button>

        </div>
    </div>
    <div id="machine_input_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="student_form" '); ?>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="machine">Finger Print Device ID:
                    <?php echo $this->lang->line('Stu_Finger_Print_Device_ID'); ?></label>
                <input type="number" class="form-control" id="Finger_Print_Device_ID" name="Finger_Print_Device_ID">
            </div>
            <div class="form-group col-sm-3">
                <label for="machine">Machine User ID:
                    <?php echo $this->lang->line('Stu_Machine_User_ID'); ?></label>
                <input type="number" class="form-control" id="Machine_User_ID" name="Machine_User_ID">
            </div>
        </div>

        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Stu_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('student_save'); ?>Save
            </button>

        </div>
    </div>
    <div id="Caring_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="student_form" '); ?>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="caring">Speech Status:
                    <?php echo $this->lang->line('Stu_Speech_Status'); ?></label>
                <input type="number" class="form-control" id="Speech_Status" name="Speech_Status">
            </div>
            <div class="form-group col-sm-3">
                <label for="caring">Special Care:
                    <?php echo $this->lang->line('Stu_Special_Care'); ?></label>
                <input type="number" class="form-control" id="Special_Care" name="Special_Care">
            </div>
            <div class="form-group col-sm-3">
                <label for="caring">Physical Disability (if any):
                    <?php echo $this->lang->line('Stu_Physical-Disability'); ?></label>
                <input type="number" class="form-control" id="Physical-Disability" name="Physical-Disability">
            </div>
        </div>

        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Stu_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('student_save'); ?>Save
            </button>

        </div>
    </div>
    <div id="Siblings_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="student_form" '); ?>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="siblings">Number of Siblings:
                    <?php echo $this->lang->line('Stu_Siblings_Number'); ?></label>
                <div class="input-group" style="width: 100%; ">
                    <label for="Siblings_Number">Boy:
                        <span class="input-group-btn" style="width:0px;"></span>
                        <input type="number" class="form-control input-sm" value="" id="Boy" name="Boy" style="width: 50px" />
                    </label>
                    <span class="input-group-btn" style="width:0px;"></span>
                    <label for="Siblings_Number">Girl:
                        <span class="input-group-btn" style="width:0px;"></span>
                        <input type="number" class="form-control input-sm" value="" id="Girl" name="Girl" style="width: 50px" />
                    </label>
                </div>
            </div>

            <div class="form-group col-sm-3">
                <label for="siblings">Order between siblings:
                    <?php echo $this->lang->line('Stu_Siblings_Order'); ?></label>
                <input type="number" class="form-control" id="Siblings_Order" name="Siblings_Order">
            </div>
        </div>

        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Stu_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('student_save'); ?>Save
            </button>

        </div>
    </div>
    <div id="Attachement_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="student_form" '); ?>
        <div class="row">
            <label for="Attachement_Description">Attachement Description:
                <?php echo $this->lang->line('Student_Attachement_Description'); ?></label><br>
            <input type="text" class="form-control" id="Attachement_Description" name="Attachement_Description">
        </div>
        <div class="row">
            <label for="Attachement">Attachement:
                <?php echo $this->lang->line('Student_Attachement'); ?></label><br>
            <input type="text" class="form-control" id="Attachement" name="Attachement">
        </div>
        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Student_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('student_save'); ?>Save
            </button>
        </div>
        <hr>
        <div>
            <div class="table-responsive">
                <table id="Student_Attachment" class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            <th style="width: 5%">SN</th>
                            <th style="width: 20%">Description</th>
                            <th style="width: 50%;">File</th>
                            <th style="width: 25%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="Documents_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="student_form" '); ?>
        <div class="col-sm-6">
            <label for="">Upload</label>
            <div class="row">
                <label for="Documents_Description">Documents Description:
                    <?php echo $this->lang->line('Student_Documents_Description'); ?></label><br>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="Documents_Description" name="Documents_Description">
                    </div>
            </div>
            <div class="row">
                <label for="Documents">Documents:
                    <?php echo $this->lang->line('Student_Documents'); ?></label><br>
                    <div class="col-sm-11">
                        <input type="text" class="form-control" id="Documents" name="Documents">
                    </div>
                    <div class="col-sm-1">
                        <i class="fa fa-upload btn btn-info" aria-hidden="true"></i>
                    </div>
            </div>
            <div class="row">
                <label for="Documents">Expires on:
                    <?php echo $this->lang->line('Student_expires_on'); ?></label><br>
                    <div class="col-sm-12">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <input type="text" class="form-control" id="expires_on" name="expires_on">
                    </div>
            </div>
            <div class="row">
                <label for="Documents">Remind bBefore Expires (In Days):
                    <?php echo $this->lang->line('Student_remind_before'); ?></label><br>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="remind_before" name="remind_before">
                    </div>
            </div>
        </div>
        <div class="col-sm-6">
            <label for="">Documents</label>
        </div>
        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Student_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('student_save'); ?>Save
            </button>
        </div>
    </div>
    <div id="Status_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="student_form" '); ?>
        <label>Current Status : Active </label>
        <div class="row" style="padding-left: 2%">
            <ul class="nav nav-tabs" id="main-tabs">
                <li class="form-group col-sm-6"><a href="#Dormant" data-toggle="tab"><?php echo $this->lang->line('Stu_Dormant_detail'); ?>Dormant</a></li>
                <li class="form-group col-sm-6 active btn btn-primary"><a href="#left" data-toggle="tab"><?php echo $this->lang->line('Stu_Left_detail'); ?>Left</a></li>
            </ul>
        </div>
        <div id="Left_Sub_tab" class="tab-pane active">
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="Stu_status"><?php echo $this->lang->line('Stu_status_left'); ?>Is Student Left?</label>

                    <div class="form-control">
                        <label class="radio-inline">
                            <input type="radio" name="Stu_status" value="1" id="No" class="status" checked="checked">No
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="Stu_status" value="2" id="Yes" class="status">Yes
                        </label>
                    </div>
                </div>

                <div class="form-group col-sm-6">
                    <label for="Stu_status"><?php echo $this->lang->line('Stu_status_confirmed'); ?>Is Confirmed?</label>

                    <div class="form-control">
                        <label class="radio-inline">
                            <input type="radio" name="Stu_status" value="1" id="No" class="status" checked="checked">No
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="Stu_status" value="2" id="Yes" class="status">Yes
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="otherName"><?php echo $this->lang->line('Stu_Left_Date'); ?>Left Date:</label>
                    <input type="date" class="form-control" id="Left_Date" name="Left_Date">
                </div>
                <div class="form-group col-sm-3">
                    <label for="otherName"><?php echo $this->lang->line('Stu_Left_Year'); ?>Left Academic Year:</label>
                    <?php echo form_dropdown('left_year[]', $left_year, '', 'class="form-control" id="left_year" onchange="load_()"'); ?>
                </div>
                <div class="form-group col-sm-3">
                    <label for="otherName"><?php echo $this->lang->line('Stu_Confirmed_By'); ?>Confirmed By:</label>
                    <input type="text" class="form-control" id="Confirmed_By" name="Confirmed_By">
                </div>
                <div class="form-group col-sm-3">
                    <label for="otherName"><?php echo $this->lang->line('Stu_Confirmed_Date'); ?>Confirmed Date:</label>
                    <input type="date" class="form-control" id="Confirmed_Date" name="Confirmed_Date">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="otherName"><?php echo $this->lang->line('Stu_Left_Remark'); ?>Left Remark:</label>
                    <input type="text" class="form-control" id="Left_Remark" name="Left_Remark">
                </div>
                <div class="form-group col-sm-6">
                    <label for="otherName"><?php echo $this->lang->line('Stu_Confirmed_Remark'); ?>Confirmed Remark:</label>
                    <input type="text" class="form-control" id="Confirmed_Remark" name="Confirmed_Remark">
                </div>
            </div>

            <hr>
            <div class="text-right m-t-xs">

                <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                    <?php echo $this->lang->line('Stu_edit'); ?> Edit
                </button> <!--Edit -->
                <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                    <?php echo $this->lang->line('student_save'); ?>Save
                </button>

            </div>
        </div>
    </div>
    <div id="Summary_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="student_form" '); ?>
        <div class="col-sm-3">
            <div class="table-responsive">
                <table id="studentTB" class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            <th style="width: auto" text="centre">11 Years<br>AGE</th>
                            <th style="width: auto" text="centre">Grade 9A<br>CLASS</th>
                            <th style="width: auto" text="centre">Male<br>GENDER</th>
                        </tr>
                    </thead>
                </table>
                <br>
                <hr>
                <table>
                    <tbody>
                        <tr><label align="left">Nationality</label>
                            <p></p>
                        </tr>
                        <tr><label align="left">Religion</label>
                            <p>Christianity</p>
                        </tr>
                        <tr><label align="left">Homeroom Teacher</label>
                            <p>Not Specified</p>
                        </tr>
                        <tr><label align="left">Parent Code</label>
                            <p>PR0002</p>
                        </tr>
                        <tr><label align="left">Contact Person</label>
                            <p>Steven Mills Test</p>
                        </tr>
                        <tr><label align="left">Contact Number/s</label>
                            <p>99346461|99346462|99346460</p>
                        </tr>
                        <tr><label align="left">Email</label>
                            <p>gowthami@gears-int.com</p>
                        </tr>
                        <tr><label align="left">Address</label>
                            <p>No 15, 1st Lane, Colombo 03</p>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <br>
            <div>
                <label><u>Siblings</u></label><br>
                <p> Angella Eyman <br>Grade 1</p>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="table-responsive">
                <label><u>Exam [2020/2021]</u></label>
                <table id="Exam_Table_1" class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            <th style="width: auto" text="centre">Term</th>
                            <th style="width: auto" text="centre">Position</th>
                            <th style="width: auto" text="centre">Average</th>
                            <th style="width: auto" text="centre">Highest</th>
                            <th style="width: auto" text="centre">Class Average</th>
                            <th style="width: auto" text="centre">Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr></tr>
                    </tbody>
                </table>
                <br>
                <br>
                <hr>
                <label><u>Exam [2019/2020]</u></label>
                <table id="Exam_Table_2" class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            <th style="width: auto" text="centre">Term</th>
                            <th style="width: auto" text="centre">Position</th>
                            <th style="width: auto" text="centre">Average</th>
                            <th style="width: auto" text="centre">Highest</th>
                            <th style="width: auto" text="centre">Class Average</th>
                            <th style="width: auto" text="centre">Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr></tr>
                    </tbody>
                </table>
                <br>
                <br>
                <hr>
                <label><u>Public Exams</u></label>
                <table id="Exam_Table_3" class="<?php echo table_class(); ?>">
                    <thead>
                        <tr>
                            <th style="width: auto" text="centre">Exam</th>
                            <th style="width: auto" text="centre">Academic year</th>
                            <th style="width: auto" text="centre">Class</th>
                            <th style="width: auto" text="centre">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>G Exam</td>
                            <td>2020/2021</td>
                            <td>Grade 8A</td>
                            <td>G View</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
        <div class="col-sm-3 pull-right">
            <a href="#" type="button" class="btn btn-success-new size-sm pull-right" onclick="excelDownload()">
                <i class="fa fa-file-excel-o"></i> Document
            </a>

        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>

<script type="text/javascript">

    var studentAutoID;
    $(document).ready(function() {
        $('.headerclose').click(function() {
            fetchPage('system/school/masters/student_masters_envoy', 'Test', 'HRMS');
        });

        $('#category').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '250px',
            maxHeight: '30px'
        });
        $('#religion').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '250px',
            maxHeight: '30px'
        });
        $('#nationality').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '200px',
            maxHeight: '30px'
        });
        $('#bloodgroup').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '200px',
            maxHeight: '30px'
        });
        $('#admitted_year').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '250px',
            maxHeight: '30px'
        });
        $('#grade').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '200px',
            maxHeight: '30px'
        });
        $('#group').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '80px',
            maxHeight: '30px'
        });
        $('#contact_person').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '200px',
            maxHeight: '30px'
        });
        $('#parental_status').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '200px',
            maxHeight: '30px'
        });
        $('#left_year').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '200px',
            maxHeight: '30px'
        });
    });

    $('#student_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        
        excluded: [':disabled'],
        fields: {


            Stu_Name: {validators: {notEmpty: {message: '<?php echo $this->lang->line('Stu_Name_is_required');?>.'}}},
            StuDob: {validators: {notEmpty: {message: '<?php echo $this->lang->line('Stu_date_of_birth_is_required');?>.'}}},
            age: {validators: {notEmpty: {message: '<?php echo $this->lang->line('Stu_age_is_required');?>.'}}},
            Stu_gender: {validators: {notEmpty: {message: '<?php echo $this->lang->line('Stu_gender_is_required');?>.'}}}
            admissionDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('admissionDate_is_required');?>.'}}},
            admitted_year: {validators: {notEmpty: {message: '<?php echo $this->lang->line('Stu_admitted_year_is_required');?>.'}}},

        },
    }).on('success.form.bv', function (e) {

        $('#student_save').prop('disabled', false);
        $('#category').prop("disabled", false);
        $('#religion').prop("disabled", false);
        $('#bloodgroup').prop("disabled", false);
        $('#nationality').prop("disabled", false);
        $('#admitted_year').prop("disabled", false);
        $('#grade').prop("disabled", false);
        $('#group').prop("disabled", false);

        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'stuID', 'value': stuID});

        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('school/School/save_student'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('#student_save').prop('disabled', false);
                    $('#category').prop("disabled", false);
                    $('#religion').prop("disabled", false);
                    $('#bloodgroup').prop("disabled", false);
                    $('#nationality').prop("disabled", false);
                    $('#admitted_year').prop("disabled", false);
                    $('#grade').prop("disabled", false);
                    $('#group').prop("disabled", false);

                    HoldOn.open({
                        theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                    });
                },
                success: function (data) {
                    stopLoad();
                    $('#student_form').modal('hide');
                    $form.bootstrapValidator('resetForm', true);
                    myAlert(data[0], data[1]);
                    fetch_student();
                    HoldOn.close();

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
    });

    function load_year(select_val) {
        $('#admitted_year').val("");
        $('#admitted_year option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_year"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#admitted_year').empty();
                    var mySelect = $('#admitted_year');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_contact_person(select_val) {
        $('#contact_person').val("");
        $('#contact_person option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_contact_person"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#contact_person').empty();
                    var mySelect = $('#contact_person');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_grade(select_val) {
        $('#grade').val("");
        $('#grade option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_grade"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#grade').empty();
                    var mySelect = $('#grade');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_group(select_val) {
        $('#group').val("");
        $('#group option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_group"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#group').empty();
                    var mySelect = $('#group');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_category(select_val) {
        $('#category').val("");
        $('#category option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_category"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#category').empty();
                    var mySelect = $('#category');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_religion(select_val) {
        $('#religion').val("");
        $('#religion option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_religion"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#religion').empty();
                    var mySelect = $('#religion');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_status(select_val) {
        $('#status').val("");
        $('#status option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_status"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#status').empty();
                    var mySelect = $('#status');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_nationality(select_val) {
        $('#nationality').val("");
        $('#nationality option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_nationality"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#nationality').empty();
                    var mySelect = $('#nationality');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function load_bloodgroup(select_val) {
        $('#bloodgroup').val("");
        $('#bloodgroup option').remove();
        var stuid = $('#StuID').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("school/School/load_bloodgroup"); ?>',
            dataType: 'json',
            data: {'stuid': stuid},
            async: false,
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#bloodgroup').empty();
                    var mySelect = $('#bloodgroup');
                    // mySelect.append($('<option></option>').val('').html('Select Option'));
                    // $.each(data, function (val, text) {
                    //     mySelect.append($('<option></option>').val(text['itemCategoryID']).html(text['description']));
                    // });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {

            }
        });
    }
    function delete_student(stuID) {
        if (stuID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('student_common_you_will_not_be_able');?>",/*Your will not be able to recover this data*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('student_common_yes_delete_it');?>",/*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'stuID': stuID},
                        url: "<?php echo site_url('school/School/delete_student'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                            });
                        },
                        success: function (data) {

                            myAlert(data[0], data[1]);


                            fetch_student();
                            HoldOn.close();
                            refreshNotifications(true);

                        }, error: function () {

                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        }
        ;
    }

    function edit_student(stuID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'stuID': stuID},
            url: "<?php echo site_url('school/School/edit_student'); ?>",
            beforeSend: function () {
                HoldOn.open({
                    theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                });
            },
            success: function (data) {

                $('#student_form').form('show');
                $('#Stu_email').val(data['Stu_email']).change();

                HoldOn.close();
                refreshNotifications(true);

            }, error: function () {

                HoldOn.close();
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                refreshNotifications(true);
            }
        });
    }
</script>