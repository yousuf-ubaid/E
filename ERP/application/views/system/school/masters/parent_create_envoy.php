<?php
echo head_page('Parent Details', false);

// $Parent_Name = fetch_Parent_Name();
$religion = fetch_religion();
$nationality = fetch_nationality();
$country = fetch_country();
$area = fetch_area();
$parental_status = fetch_parental_status();


$ParentID = $this->input->post('page_id');
$fromHiarachy = $this->input->post('policy_id');
$fromHiarachy = (empty($fromHiarachy)) ? 0 : $fromHiarachy;

// $isAuthenticateNeed = 0;
// if ($fromHiarachy == 0) {
//     $isAuthenticateNeed = Parent_master_authenticate();
//     /** Check company policy on 'Parent Master Edit Approval' **/
//     $fromHiarachy = $isAuthenticateNeed;
//     $isAuthenticateNeed = 1;
// }

// if ($fromHiarachy == 1) {
//     $isPendingDataAvailable = 0;
// }
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

<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary btn-sm navdisabl  btn-wizard" href="#contact_person_tab" data-toggle="tab">
        <?php echo $this->lang->line('Parent_contact_person_detail'); ?> Contact Person
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Father_Details_tab" onclick="fetch_Father_Details_tab()" data-toggle="tab">
        <?php echo $this->lang->line('Parent_Father_Details_detail'); ?>Father Details
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Mother_Details_tab" onclick="fetch_Mother_Details_tab()" data-toggle="tab">
        <?php echo $this->lang->line('Parent_Mother_Details_tab'); ?> Mother Details
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Guardian_Details_tab" onclick="fetch_Guardian_Details()" data-toggle="tab">
        <?php echo $this->lang->line('Parent_Guardian_Details_detail'); ?> Guardian Details
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Relative_Details_tab" onclick="fetch_Relative_Details()" data-toggle="tab">
        <?php echo $this->lang->line('Parent_Relative_Details_detail'); ?> Relative Details
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Employee_as_Parent_tab" onclick="fetch_Employee_as_Parent()" data-toggle="tab">
        <?php echo $this->lang->line('Parent_Employee_as_Parent_detail'); ?> Employee as Parent
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Attachment_tab" onclick="fetch_Attachment()" data-toggle="tab">
        <?php echo $this->lang->line('Parent_Attachment_detail'); ?> Attachment
    </a>
    <a class="btn btn-default btn-sm navdisabl  btn-wizard" href="#Children_Swap_tab" onclick="fetch_Children_Swap()" data-toggle="tab">
        <?php echo $this->lang->line('Parent_Children_Swap_detail'); ?> Children Swap
    </a>
</div>
<hr>
<div class="tab-content">
    <div id="contact_person_tab" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="Contact_Person_form" '); ?>
        <div class="row">
            <label>Same as:</label>
            <br>
            <input type="checkbox" id="Father" name="Father" value="Father">
            <label> Father Details <span> </span></label>
            <input type="checkbox" id="Mother" name="Mother" value="Mother">
            <label> Mother Details <span> </span></label>
            <input type="checkbox" id="Guardian" name="Guardian" value="Guardian">
            <label> Guardian Details <span> </span></label>
        </div>
        <hr><br>
        <div class="row">
            <div class="col-sm-3">
                <div class="fileinput-new thumbnail" id="Parent-image-container" style="width: 236px; height: 230px;">
                    <img src="<?php echo base_url('images/users/parent.jpg'); ?>" id="changeImg">
                    <input type="file" name="ParentImage" id="ParentImage" style="display: none;" onchange="loadImage(this)" />
                </div>
            </div>
            <div class="col-sm-9">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="fullName">
                            <?php echo $this->lang->line('Contact_Person_surname'); ?>Contact Person/Surname: <?php required_mark(); ?>
                        </label>
                        <input type="text" class="form-control" id="Contact_Name" name="Contact_Name">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="Contact_Person_First_Name"><?php echo $this->lang->line('Contact_Person_First_Name'); ?>First Name: </label>
                        <input type="text" class="form-control" id="Contact_Person_First_Name" name="Contact_Person_First_Name" value="">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="fullName">
                            <?php echo $this->lang->line('Contact_Person_name_with_initials'); ?>Name with initials: </label>

                        <div class="input-group" style="width: 100%; ">
                            <input type="text" class="form-control input-sm" value="" id="initial" name="initial" placeholder="Initial" style="width: 50px" />
                            <span class="input-group-btn" style="width:0px;"></span>
                            <input type="text" class="form-control input-sm" value="" id="Ename4" name="Ename4" placeholder="Name" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="otherName">
                            <?php echo $this->lang->line('Contact_Person_other_name'); ?>Contact Person (Other):</label>
                        <input type="text" class="form-control" id="otherName" name="otherName">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="Contact_PersonMachineID"><?php echo $this->lang->line('Contact_Person_nic_no'); ?>Resident/Labour/NIC/Civil No:</label><?php required_mark(); ?>
                        <input type="text" class="form-control" id="NIC" name="NIC">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="Contact_Person_gender"><?php echo $this->lang->line('Contact_Person_gender'); ?>Gender: </label>

                        <div class="form-control">
                            <label class="radio-inline">
                                <input type="radio" name="Contact_Person_gender" value="1" id="male" class="gender" checked="checked">Male
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="Contact_Person_gender" value="2" id="feMale" class="gender">Female
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-2">
                        <label for="Marital_Status">Marital Status :
                            <?php echo $this->lang->line('Contact_Person_Marital_Status'); ?></label>
                        <?php echo form_dropdown('parental_status[]', $parental_status, '', 'class="form-control" id="parental_status"'); ?>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="Nationality"><?php echo $this->lang->line('Contact_Person_nationality'); ?>Nationality</label>
                        <?php echo form_dropdown('nationality[]', $nationality, '', 'class="form-control" id="nationality"'); ?>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="Country">Country:
                            <?php echo $this->lang->line('Contact_Person_Country'); ?></label>
                        <?php echo form_dropdown('country[]', $country, '', 'class="form-control" id="country"'); ?>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="Area">Area :
                            <?php echo $this->lang->line('Contact_Person_Area'); ?></label><br>
                        <?php echo form_dropdown('area[]', $area, '', 'class="form-control" id="area"'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="Address"> Address:
                            <?php echo $this->lang->line('Contact_Person_address'); ?></label>
                        <input type="text" class="form-control" id="Address" name="Address">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <div class="form-group col-sm-6">
                    <label for="PO_Box">PO Box:
                        <?php echo $this->lang->line('Contact_Person_PO_Box'); ?></label>
                    <input type="text" class="form-control" id="PO_Box" name="PO_Box">
                </div>
                <div class="form-group col-sm-6">
                    <label for="Postal_Code">Postal_Code:
                        <?php echo $this->lang->line('Contact_Person_Postal_Code'); ?></label>
                    <input type="text" class="form-control" id="Postal_Code" name="Postal_Code">
                </div>
            </div>

            <div class="form-group col-sm-3">
                <label for="Contact_Person_email"><?php echo $this->lang->line('Contact_Person_primary_e-mail'); ?>Email:</label>

                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                    <input type="email" class="form-control " id="Contact_Person_email" name="Contact_Person_email">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="Designation">Designation:
                    <?php echo $this->lang->line('Contact_Person_Designation'); ?></label>
                <input type="text" class="form-control" id="Designation" name="Designation">
            </div>
            <div class="form-group col-sm-3">
                <label for="Working_Place_Address">Working Place Address:
                    <?php echo $this->lang->line('Contact_Person_Working_Place_Address'); ?></label>
                <input type="text" class="form-control" id="Working_Place_Address" name="Working_Place_Address">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Telephone1">Telephone (Mobile):
                    <?php echo $this->lang->line('Contact_Person_Telephone1'); ?></label>
                <input type="number" class="form-control" id="Telephone1" name="Telephone1">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone2">Telephone (Home):
                    <?php echo $this->lang->line('Contact_Person_Telephone2'); ?></label>
                <input type="number" class="form-control" id="Telephone2" name="Telephone2">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone3">Telephone (Office):
                    <?php echo $this->lang->line('Contact_Person_Telephone3'); ?></label>
                <input type="number" class="form-control" id="Telephone3" name="Telephone3">
            </div>
            <div class="form-group col-sm-3">
                <label for="Passport_No">Passport Number:
                    <?php echo $this->lang->line('Contact_Person_Passport_No'); ?></label>
                <input type="number" class="form-control" id="Passport_No" name="Passport_No">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Qualification">Qualification/s:
                    <?php echo $this->lang->line('Contact_Person_Qualification'); ?></label>
                <input type="text" class="form-control" id="Qualification" name="Qualification">
            </div>
            <div class="form-group col-sm-3">
                <label for="Remarks">Remarks:
                    <?php echo $this->lang->line('Contact_Person_remarks'); ?></label>
                <input type="text" class="form-control" id="Remarks" name="Remarks">
            </div>
        </div>

        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Contact_Person_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('parent_save'); ?>Save
            </button>

        </div>
    </div>
    <div id="Father_Details_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="Contact_Person_form" '); ?>
        <div class="row">
            <label>Same as:</label>
            <br>
            <input type="checkbox" id="Mother" name="Mother" value="Mother">
            <label> Mother Details <span> </span></label>
            <input type="checkbox" id="Guardian" name="Guardian" value="Guardian">
            <label> Guardian Details <span> </span></label>
            <input type="checkbox" id="Contact_Person" name="Contact_Person" value="Contact_Person">
            <label> Contact Person Details <span> </span></label>
        </div>
        <hr><br>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="fullName">
                    <?php echo $this->lang->line('Father_surname'); ?>Father Name/Surname:
                </label>
                <input type="text" class="form-control" id="Contact_Name" name="Contact_Name">
            </div>
            <div class="form-group col-sm-3">
                <label for="Father_First_Name"><?php echo $this->lang->line('Father_First_Name'); ?>First Name: </label>
                <input type="text" class="form-control" id="Father_First_Name" name="Father_First_Name" value="">
            </div>
            <div class="form-group col-sm-3">
                <label for="fullName">
                    <?php echo $this->lang->line('Father_name_with_initials'); ?>Name with initials: </label>

                <div class="input-group" style="width: 100%; ">
                    <input type="text" class="form-control input-sm" value="" id="initial" name="initial" placeholder="Initial" style="width: 50px" />
                    <span class="input-group-btn" style="width:0px;"></span>
                    <input type="text" class="form-control input-sm" value="" id="Ename4" name="Ename4" placeholder="Name" />
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="otherName">
                    <?php echo $this->lang->line('Father_other_name'); ?>Father Name (Other):</label>
                <input type="text" class="form-control" id="otherName" name="otherName">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="FatherMachineID"><?php echo $this->lang->line('Father_nic_no'); ?>Resident/Labour/NIC/Civil No:</label>
                <input type="text" class="form-control" id="NIC" name="NIC">
            </div>
            <div class="form-group col-sm-3">
                <label for="Address"> Address:
                    <?php echo $this->lang->line('Father_address'); ?></label>
                <input type="text" class="form-control" id="Address" name="Address">
            </div>
            <div class="form-group col-sm-3">
                <label for="Nationality"><?php echo $this->lang->line('Father_nationality'); ?>Nationality</label><br>
                <?php echo form_dropdown('nationality[]', $nationality, '', 'class="form-control" id="nationality"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="Country">Country:
                    <?php echo $this->lang->line('Father_Country'); ?></label><br>
                <?php echo form_dropdown('country[]', $country, '', 'class="form-control" id="country"'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Area">Area :
                    <?php echo $this->lang->line('Father_Area'); ?></label><br>
                <?php echo form_dropdown('area[]', $area, '', 'class="form-control" id="area"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <div class="form-group col-sm-6">
                    <label for="PO_Box">PO Box:
                        <?php echo $this->lang->line('Father_PO_Box'); ?></label>
                    <input type="text" class="form-control" id="PO_Box" name="PO_Box">
                </div>
                <div class="form-group col-sm-6">
                    <label for="Postal_Code">Postal_Code:
                        <?php echo $this->lang->line('Father_Postal_Code'); ?></label>
                    <input type="text" class="form-control" id="Postal_Code" name="Postal_Code">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="Father_email"><?php echo $this->lang->line('Father_primary_e-mail'); ?>Email:</label>

                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                    <input type="email" class="form-control " id="Father_email" name="Father_email">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="Designation">Designation:
                    <?php echo $this->lang->line('Father_Designation'); ?></label>
                <input type="text" class="form-control" id="Designation" name="Designation">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Working_Place_Address">Working Place Address:
                    <?php echo $this->lang->line('Father_Working_Place_Address'); ?></label>
                <input type="text" class="form-control" id="Working_Place_Address" name="Working_Place_Address">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone1">Telephone (Mobile):
                    <?php echo $this->lang->line('Father_Telephone1'); ?></label>
                <input type="number" class="form-control" id="Telephone1" name="Telephone1">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone2">Telephone (Home):
                    <?php echo $this->lang->line('Father_Telephone2'); ?></label>
                <input type="number" class="form-control" id="Telephone2" name="Telephone2">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone3">Telephone (Office):
                    <?php echo $this->lang->line('Father_Telephone3'); ?></label>
                <input type="number" class="form-control" id="Telephone3" name="Telephone3">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Passport_No">Passport Number:
                    <?php echo $this->lang->line('Father_Passport_No'); ?></label>
                <input type="number" class="form-control" id="Passport_No" name="Passport_No">
            </div>
            <div class="form-group col-sm-3">
                <label for="Qualification">Qualification/s:
                    <?php echo $this->lang->line('Father_Qualification'); ?></label>
                <input type="text" class="form-control" id="Qualification" name="Qualification">
            </div>
            <div class="form-group col-sm-3">
                <label for="Remarks">Remarks:
                    <?php echo $this->lang->line('Father_remarks'); ?></label>
                <input type="text" class="form-control" id="Remarks" name="Remarks">
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Father_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('parent_save'); ?>Save
            </button>
        </div>
    </div>
    <div id="Mother_Details_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="Contact_Person_form" '); ?>
        <div class="row">
            <label>Same as:</label>
            <br>
            <input type="checkbox" id="Father" name="Father" value="Father">
            <label> Father Details <span> </span></label>
            <input type="checkbox" id="Guardian" name="Guardian" value="Guardian">
            <label> Guardian Details <span> </span></label>
            <input type="checkbox" id="Contact_Person" name="Contact_Person" value="Contact_Person">
            <label> Contact Person Details <span> </span></label>
        </div>
        <hr><br>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="fullName">
                    <?php echo $this->lang->line('Mother_surname'); ?>Mother Name/Surname:
                </label>
                <input type="text" class="form-control" id="Contact_Name" name="Contact_Name">
            </div>
            <div class="form-group col-sm-3">
                <label for="Mother_First_Name"><?php echo $this->lang->line('Mother_First_Name'); ?>First Name: </label>
                <input type="text" class="form-control" id="Mother_First_Name" name="Mother_First_Name" value="">
            </div>
            <div class="form-group col-sm-3">
                <label for="fullName">
                    <?php echo $this->lang->line('Mother_name_with_initials'); ?>Name with initials: </label>

                <div class="input-group" style="width: 100%; ">
                    <input type="text" class="form-control input-sm" value="" id="initial" name="initial" placeholder="Initial" style="width: 50px" />
                    <span class="input-group-btn" style="width:0px;"></span>
                    <input type="text" class="form-control input-sm" value="" id="Ename4" name="Ename4" placeholder="Name" />
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="otherName">
                    <?php echo $this->lang->line('Mother_other_name'); ?>Mother Name (Other):</label>
                <input type="text" class="form-control" id="otherName" name="otherName">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="MotherMachineID"><?php echo $this->lang->line('Mother_nic_no'); ?>Resident/Labour/NIC/Civil No:</label>
                <input type="text" class="form-control" id="NIC" name="NIC">
            </div>
            <div class="form-group col-sm-3">
                <label for="Address"> Address:
                    <?php echo $this->lang->line('Mother_address'); ?></label>
                <input type="text" class="form-control" id="Address" name="Address">
            </div>
            <div class="form-group col-sm-3">
                <label for="Nationality"><?php echo $this->lang->line('Mother_nationality'); ?>Nationality</label><br>
                <?php echo form_dropdown('nationality[]', $nationality, '', 'class="form-control" id="nationality"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="Country">Country:
                    <?php echo $this->lang->line('Mother_Country'); ?></label><br>
                <?php echo form_dropdown('country[]', $country, '', 'class="form-control" id="country"'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Area">Area :
                    <?php echo $this->lang->line('Mother_Area'); ?></label><br>
                <?php echo form_dropdown('area[]', $area, '', 'class="form-control" id="area"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <div class="form-group col-sm-6">
                    <label for="PO_Box">PO Box:
                        <?php echo $this->lang->line('Mother_PO_Box'); ?></label>
                    <input type="text" class="form-control" id="PO_Box" name="PO_Box">
                </div>
                <div class="form-group col-sm-6">
                    <label for="Postal_Code">Postal_Code:
                        <?php echo $this->lang->line('Mother_Postal_Code'); ?></label>
                    <input type="text" class="form-control" id="Postal_Code" name="Postal_Code">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="Mother_email"><?php echo $this->lang->line('Mother_primary_e-mail'); ?>Email:</label>

                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                    <input type="email" class="form-control " id="Mother_email" name="Mother_email">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="Designation">Designation:
                    <?php echo $this->lang->line('Mother_Designation'); ?></label>
                <input type="text" class="form-control" id="Designation" name="Designation">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Working_Place_Address">Working Place Address:
                    <?php echo $this->lang->line('Mother_Working_Place_Address'); ?></label>
                <input type="text" class="form-control" id="Working_Place_Address" name="Working_Place_Address">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone1">Telephone (Mobile):
                    <?php echo $this->lang->line('Mother_Telephone1'); ?></label>
                <input type="number" class="form-control" id="Telephone1" name="Telephone1">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone2">Telephone (Home):
                    <?php echo $this->lang->line('Mother_Telephone2'); ?></label>
                <input type="number" class="form-control" id="Telephone2" name="Telephone2">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone3">Telephone (Office):
                    <?php echo $this->lang->line('Mother_Telephone3'); ?></label>
                <input type="number" class="form-control" id="Telephone3" name="Telephone3">
            </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Passport_No">Passport Number:
                    <?php echo $this->lang->line('Mother_Passport_No'); ?></label>
                <input type="number" class="form-control" id="Passport_No" name="Passport_No">
            </div>
            <div class="form-group col-sm-3">
                <label for="Qualification">Qualification/s:
                    <?php echo $this->lang->line('Mother_Qualification'); ?></label>
                <input type="text" class="form-control" id="Qualification" name="Qualification">
            </div>
            <div class="form-group col-sm-3">
                <label for="Remarks">Remarks:
                    <?php echo $this->lang->line('Mother_remarks'); ?></label>
                <input type="text" class="form-control" id="Remarks" name="Remarks">
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Mother_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('parent_save'); ?>Save
            </button>
        </div>
    </div>
    <div id="Guardian_Details_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="Contact_Person_form" '); ?>
        <div class="row">
            <label>Same as:</label>
            <br>
            <input type="checkbox" id="Father" name="Father" value="Father">
            <label> Father Details <span> </span></label>
            <input type="checkbox" id="Mother" name="Mother" value="Mother">
            <label> Mother Details <span> </span></label>
            <input type="checkbox" id="Contact_Person" name="Contact_Person" value="Contact_Person">
            <label> Contact Person Details <span> </span></label>
        </div>
        <hr><br>

        <div class="row">
            <div class="form-group col-sm-3">
                <label for="fullName">
                    <?php echo $this->lang->line('Guardian_surname'); ?>Guardian Name/Surname:
                </label>
                <input type="text" class="form-control" id="Contact_Name" name="Contact_Name">
            </div>
            <div class="form-group col-sm-3">
                <label for="Guardian_First_Name"><?php echo $this->lang->line('Guardian_First_Name'); ?>First Name: </label>
                <input type="text" class="form-control" id="Guardian_First_Name" name="Guardian_First_Name" value="">
            </div>
            <div class="form-group col-sm-3">
                <label for="fullName">
                    <?php echo $this->lang->line('Guardian_name_with_initials'); ?>Name with initials: </label>

                <div class="input-group" style="width: 100%; ">
                    <input type="text" class="form-control input-sm" value="" id="initial" name="initial" placeholder="Initial" style="width: 50px" />
                    <span class="input-group-btn" style="width:0px;"></span>
                    <input type="text" class="form-control input-sm" value="" id="Ename4" name="Ename4" placeholder="Name" />
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="otherName">
                    <?php echo $this->lang->line('Guardian_other_name'); ?>Guardian Name (Other):</label>
                <input type="text" class="form-control" id="otherName" name="otherName">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="GuardianMachineID"><?php echo $this->lang->line('Guardian_nic_no'); ?>Resident/Labour/NIC/Civil No:</label>
                <input type="text" class="form-control" id="NIC" name="NIC">
            </div>
            <div class="form-group col-sm-3">
                <label for="Address"> Address:
                    <?php echo $this->lang->line('Guardian_address'); ?></label>
                <input type="text" class="form-control" id="Address" name="Address">
            </div>
            <div class="form-group col-sm-3">
                <label for="Nationality"><?php echo $this->lang->line('Guardian_nationality'); ?>Nationality</label><br>
                <?php echo form_dropdown('nationality[]', $nationality, '', 'class="form-control" id="nationality"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <label for="Country">Country:
                    <?php echo $this->lang->line('Guardian_Country'); ?></label><br>
                <?php echo form_dropdown('country[]', $country, '', 'class="form-control" id="country"'); ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Area">Area :
                    <?php echo $this->lang->line('Guardian_Area'); ?></label><br>
                <?php echo form_dropdown('area[]', $area, '', 'class="form-control" id="area"'); ?>
            </div>
            <div class="form-group col-sm-3">
                <div class="form-group col-sm-6">
                    <label for="PO_Box">PO Box:
                        <?php echo $this->lang->line('Guardian_PO_Box'); ?></label>
                    <input type="text" class="form-control" id="PO_Box" name="PO_Box">
                </div>
                <div class="form-group col-sm-6">
                    <label for="Postal_Code">Postal_Code:
                        <?php echo $this->lang->line('Guardian_Postal_Code'); ?></label>
                    <input type="text" class="form-control" id="Postal_Code" name="Postal_Code">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="Guardian_email"><?php echo $this->lang->line('Guardian_primary_e-mail'); ?>Email:</label>

                <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-envelope" aria-hidden="true"></i></div>
                    <input type="email" class="form-control " id="Guardian_email" name="Guardian_email">
                </div>
            </div>
            <div class="form-group col-sm-3">
                <label for="Designation">Designation:
                    <?php echo $this->lang->line('Guardian_Designation'); ?></label>
                <input type="text" class="form-control" id="Designation" name="Designation">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Working_Place_Address">Working Place Address:
                    <?php echo $this->lang->line('Guardian_Working_Place_Address'); ?></label>
                <input type="text" class="form-control" id="Working_Place_Address" name="Working_Place_Address">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone1">Telephone (Mobile):
                    <?php echo $this->lang->line('Guardian_Telephone1'); ?></label>
                <input type="number" class="form-control" id="Telephone1" name="Telephone1">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone2">Telephone (Home):
                    <?php echo $this->lang->line('Guardian_Telephone2'); ?></label>
                <input type="number" class="form-control" id="Telephone2" name="Telephone2">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone3">Telephone (Office):
                    <?php echo $this->lang->line('Guardian_Telephone3'); ?></label>
                <input type="number" class="form-control" id="Telephone3" name="Telephone3">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Passport_No">Passport Number:
                    <?php echo $this->lang->line('Guardian_Passport_No'); ?></label>
                <input type="number" class="form-control" id="Passport_No" name="Passport_No">
            </div>
            <div class="form-group col-sm-3">
                <label for="Qualification">Qualification/s:
                    <?php echo $this->lang->line('Guardian_Qualification'); ?></label>
                <input type="text" class="form-control" id="Qualification" name="Qualification">
            </div>
            <div class="form-group col-sm-3">
                <label for="Remarks">Remarks:
                    <?php echo $this->lang->line('Guardian_remarks'); ?></label>
                <input type="text" class="form-control" id="Remarks" name="Remarks">
            </div>
        </div>
        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Guardian_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('parent_save'); ?>Save
            </button>
        </div>
    </div>
    <div id="Relative_Details_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="Contact_Person_form" '); ?>
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="Relative_Name">Relative Name:
                    <?php echo $this->lang->line('Parent_Relative_Name'); ?></label>
                <input type="text" class="form-control" id="Relative_Name" name="Relative_Name">
            </div>
            <div class="form-group col-sm-3">
                <label for="Relation">Relation:
                    <?php echo $this->lang->line('Parent_Relation'); ?></label>
                <input type="text" class="form-control" id="Relation" name="Relation">
            </div>
            <div class="form-group col-sm-3">
                <label for="Telephone">Telephone (Mobile):
                    <?php echo $this->lang->line('Parent_Telephone'); ?></label>
                <input type="number" class="form-control" id="Telephone" name="Telephone">
            </div>
            <div class="form-group col-sm-3">
                <label for="Address">Address:
                    <?php echo $this->lang->line('Parent_Address'); ?></label>
                <input type="text" class="form-control" id="Address" name="Address">
            </div>
        </div>

        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Parent_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('parent_save'); ?>Save
            </button>

        </div>
    </div>
    <div id="Attachment_tab" class="tab-pane">
        <?php echo form_open('', 'role="form" id="Contact_Person_form" '); ?>
        <div class="row">
            <label for="Attachement_Description">Attachement Description:
                <?php echo $this->lang->line('Parent_Attachement_Description'); ?></label><br>
            <input type="text" class="form-control" id="Attachement_Description" name="Attachement_Description">
        </div>
        <div class="row">
            <label for="Attachement">Attachement:
                <?php echo $this->lang->line('Parent_Attachement'); ?></label><br>
            <input type="text" class="form-control" id="Attachement" name="Attachement">
        </div>
        <hr>
        <div class="text-right m-t-xs">

            <button class="btn btn-default-new size-lg" type="button" id="editBtn">
                <?php echo $this->lang->line('Parent_edit'); ?> Edit
            </button> <!--Edit -->
            <button class="btn btn-primary-new size-lg submitBtn" id="saveBtn" type="submit">
                <?php echo $this->lang->line('parent_save'); ?>Save
            </button>
        </div>
        <hr>
        <div>
            <div class="table-responsive">
                <table id="Parent_Attachment" class="<?php echo table_class(); ?>">
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
</div>
<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.headerclose').click(function() {
            fetchPage('system/school/masters/parent_masters_envoy', 'Test', 'HRMS');
        });

        $('#parental_status').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '100px',
            maxHeight: '30px'
        });
        $('#nationality').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '100px',
            maxHeight: '30px'
        });
        $('#country').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '100px',
            maxHeight: '30px'
        });
        $('#area').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '100px',
            maxHeight: '30px'
        });
    });

    $('#Contact_Person_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        
        excluded: [':disabled'],
        fields: {


            Contact_Name: {validators: {notEmpty: {message: '<?php echo $this->lang->line('Contact_Person_surname_is_required');?>.'}}},
            NIC: {validators: {notEmpty: {message: '<?php echo $this->lang->line('Contact_Person_nic_no_is_required');?>.'}}},
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({'name': 'parentID', 'value': parentrID});

        $.ajax(
            {
                async: false,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('school/School/save_parent'); ?>",
                beforeSend: function () {
                    HoldOn.open({
                        theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                    });
                },
                success: function (data) {
                    $('#Contact_Person_form').modal('hide');
                    $form.bootstrapValidator('resetForm', true);
                    myAlert(data[0], data[1]);
                    loadtable();
                    HoldOn.close();

                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    HoldOn.close();
                    refreshNotifications(true);
                }
            });
    });
    function delete_parent(parentID) {
        if (parentID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('parent_common_you_will_not_be_able');?>",/*Your will not be able to recover this data*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('parent_common_yes_delete_it');?>",/*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {'parentID': parentID},
                        url: "<?php echo site_url('school/School/delete_parent'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                            });
                        },
                        success: function (data) {

                            myAlert(data[0], data[1]);


                            fetch_parent();
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

    function edit_parent(parentID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {'parentID': parentID},
            url: "<?php echo site_url('school/School/edit_parent'); ?>",
            beforeSend: function () {
                HoldOn.open({
                    theme: "sk-bounce", message: "<h4> Please wait until page load! </h4>",
                });
            },
            success: function (data) {

                $('#Contact_Person_form').form('show');
                $('#Contact_Person_email').val(data['Contact_Person_email']).change();

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