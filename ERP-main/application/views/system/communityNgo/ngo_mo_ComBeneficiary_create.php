<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('community_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$com_master = fetch_comMaster_lead();
$com_gender = fetch_com_gender();
$all_states_arr = all_statemaster();
$countries_arr = fetch_all_countries();
$countryCode_arr = all_country_codes();
$currency_arr = all_currency_new_drop();
?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/commtNgo_style.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/community_ngo/css/ngo_web_style.css'); ?>">
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: #060606
        }

        span.input-req-inner {
            width: 20px;
            height: 40px;
            position: absolute;
            overflow: hidden;
            display: block;
            right: 4px;
            top: -15px;
            -webkit-transform: rotate(135deg);
            -ms-transform: rotate(135deg);
            transform: rotate(135deg);
        }

        span.input-req-inner:before {
            font-size: 20px;
            content: "*";
            top: 15px;
            right: 1px;
            color: #fff;
            position: absolute;
            z-index: 2;
            cursor: default;
        }

        span.input-req-inner:after {
            content: '';
            width: 35px;
            height: 35px;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
            background: #f45640;
            position: absolute;
            top: 7px;
            right: -29px;
        }

        .search_cancel {
            background-color: #f3f3f3;
            border: solid 1px #dcdcdc;
            vertical-align: middle;
            padding: 3px;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
        }
    </style>
    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary" href="#step1" data-toggle="tab"><?php echo $this->lang->line('CommunityNgo_benificiary_header');?><!--Beneficiary Header--></a>
        <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab"><?php echo $this->lang->line('CommunityNgo_benificiary_family_details');?><!--Beneficiary Family details--></a>
        <a class="btn btn-default btn-wizard" href="#step3" onclick="fetch_ngo_document()" data-toggle="tab"><?php echo $this->lang->line('CommunityNgo_benificiary_documents');?><!--Documents--></a>
    </div>
    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="beneficiary_form"'); ?>
            <input type="hidden" name="benificiaryID" id="edit_beneficiary">
            <input type="hidden" name="systemGeneratedCode" id="systemGeneratedCode">

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('CommunityNgo_benefiviary_template');?><!--BENEFICIARY TEMPLATE--></h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_project');?><!--Project--></label>
                        </div>
                        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('projectID', fetch_project_com_drop(), '',
                        'class="form-control select2" id="projectID" required'); ?>
                    <span class="input-req-inner"></span></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;display: none;">
                        <div class="form-group col-sm-2">
                            <label class="title">Sub Project</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="input-req" title="Required Field">
                                <?php echo form_dropdown('subProjectID', array("" => "Select"), "", 'class="form-control" id="subProjectID"'); ?>
                                <span class="input-req-inner"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>

            <div id="beneficiary_template">
                <div class="col-sm-2">&nbsp;</div>
                <div class="col-sm-8">
                    <div class="alert alert-info">
                        <strong>Info !</strong><span style="font-size: 15px;font-weight: 600;"> Please Select a Project to Load Template.</span>
                    </div>
                </div>
                <div class="col-sm-2">&nbsp;</div>
            </div>
            <div class="row">
                <div class="form-group col-sm-12">
                    <div class="text-right m-t-xs">
                        <button class="btn btn-primary" type="submit"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <div id="step2" class="tab-pane">
            <div class="row">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('CommunityNgo_benefiviary_family_details');?><!--Beneficiary Family Details--> </h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="add_beneficiary_familyDetail()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i> <?php echo $this->lang->line('CommunityNgo_benefiviary_add_family_details');?><!--Add Family Detail-->
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="beneficiary_familyDetail"></div>
                </div>
            </div>
        </div>
        <div id="step3" class="tab-pane">
            <div class="row">
                <div class="col-md-12">
                    <div id="beneficiary_document"></div>
                </div>
            </div>
            <br>

            <div class="text-right m-t-xs">
                <button class="btn btn-default prev"><?php echo $this->lang->line('common_previous');?><!--Previous--></button>
                <button class="btn btn-primary " onclick="save_draft()"><?php echo $this->lang->line('common_save_as_draft');?><!--Save as Draft--></button>
                <button class="btn btn-success submitWizard" onclick="comBeneficiary_systemCode_generator()"><?php echo $this->lang->line('common_confirm');?><!--Confirm--></button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="title-modal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">New Title</h4>
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
                        <button type="button" class="btn btn-primary btn-sm" id="title-btn"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="beneficiary-type-modal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">New Type</h4>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Type</label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="beneficiary-type-description"
                                           name="beneficiary-type-description">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_beneficiary_type()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade pddLess" data-backdrop="static" id="addFamilyDetailModal" data-width="80%"
         role="dialog">
        <div class="modal-dialog" style="width:60%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                    </button>
                    <h5> <?php echo $this->lang->line('CommunityNgo_benefiviary_add_family_details');?><!--Add Family Detail--></h5>
                </div>
                <div class="modal-body" id="modal_contact">
                    <form method="post" name="frm_FamilyContactDetails" id="frm_FamilyContactDetails"
                          class="form-horizontal">
                        <input type="hidden" value="0" id="empfamilydetailsID"
                               name="empfamilydetailsID"/>
                        <input type="hidden" value="" id="familyDetail_benificiaryID" name="benificiaryID">
                        <input type="hidden" value="" id="FamMasterIDs" name="FamMasterIDs">

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="textinput"><?php echo $this->lang->line('communityngo_members');?><!--Community Member--></label>

                            <div class="col-md-7">
                            <span class="input-req" title="Required Field">
                    <select onchange="fatch_beniMemDel();" id="Com_MasterIDs" class="form-control select2"
                            name="Com_MasterIDs">
        <option><?php echo $this->lang->line('CommunityNgo_sel_familyMem'); ?><!--Select Family Member--></option>
            <option></option>

                  </select>

                       <span class="input-req-inner"></span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="textinput"><?php echo $this->lang->line('common_name');?><!--Name--></label>

                            <div class="col-md-7">
                                <input class="form-control input-md" placeholder="<?php echo $this->lang->line('common_name');?>" id="name" name="name"
                                       type="text" value=""><!--Name-->
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="relationshipType"><?php echo $this->lang->line('common_relationship');?><!--Relationship--></label>

                            <div class="col-md-7">
                                <?php echo form_dropdown('relationshipType', hrms_relationship_drop(), '', 'id="relationshipType" class="form-control"'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="country"><?php echo $this->lang->line('common_nationality');?><!--Nationality--></label>

                            <div class="col-md-7">
                                <?php echo form_dropdown('nationality', load_all_nationality_drop(), '', 'id="nationality" class="form-control select2"'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label"><?php echo $this->lang->line('common_date_of_birth');?><!--Date of Birth--></label>

                            <div class="input-group datepic col-md-7" style="padding-left: 15px;">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="DOB" style="width: 94%;"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="DOB" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="textinput"><?php echo $this->lang->line('communityngo_nic');?><!--NIC No--></label>

                            <div class="col-md-7">
                                <input class="form-control input-md" placeholder="<?php echo $this->lang->line('communityngo_nic_brief');?>" id="idNO" name="idNO"
                                       type="text" value=""><!--NIC No-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="textinput"><?php echo $this->lang->line('common_gender');?><!--Gender--></label>

                            <div class="col-md-7">


                                <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('gender', $com_gender, '',
                                    'class="form-control select2" id="gender" '); ?>
                                    <span class="input-req-inner"></span></span>
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

    <div class="modal fade" id="beneficiary-manage-country-modal" role="dialog" data-keyboard="false"
         data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('CommunityNgo_benefiviary_new_province');?><!--New Province--></h4>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="country_description"
                                           name="country_description">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_province()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="beneficiary-district-modal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('CommunityNgo_benefiviary_new_district');?><!--New District--></h4>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="description_district"
                                           name="description_district">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_district()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="beneficiary-division-modal" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('CommunityNgo_benefiviary_new_division');?><!--New Division--></h4>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $this->lang->line('common_description');?><!--Description--></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="description_division"
                                           name="description_division">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_division()"><?php echo $this->lang->line('common_save');?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade " data-backdrop="static" id="modaluploadimages" data-width="60%" tabindex="-1"
         role="dialog">
        <div class="modal-dialog">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h5><?php echo $this->lang->line('common_attachments'); ?> </h5><!--Attachments-->
            </div>
            <div class="modal-body" id="modal_contact" style="min-height:100px;background-color: #F5F5F5">
                <?php echo form_open_multipart('', 'id="family_image_uplode_form" class="form-horizontal"'); ?>
                <fieldset>
                    <!-- Text input-->

                    <input type="hidden" class="form-control" value="" id="empfamilydetailzID"
                           name="empfamilydetailsID">


                    <!-- File Button -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="filebutton"><?php echo $this->lang->line('common_attachment');?><!--Attachment--> </label><!--Attachment-->

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

                <button type="button" class="btn btn-xs btn-default"
                        data-dismiss="modal"><?php echo $this->lang->line('common_cancel'); ?> </button><!--Cancel-->
            </div>
        </div>
    </div>

    <script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
    <script type="text/javascript">
        var benificiaryID;
        var province;
        var district;
        var division;
        var subDivision;
        var Com_MasterID;
        var FamMasterID;
        var subProjectID ='';

        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/communityNgo/ngo_mo_communityBeneficiary', '', 'Beneficiary');
            });
            $('.select2').select2();

            benificiaryID = null;
            province = null;
            district = null;
            division = null;
            subDivision = null;
            FamMasterID = null;
            Com_MasterID = null;

            Inputmask().mask(document.querySelectorAll("input"));

            $('.extraColumns input').iCheck({
                checkboxClass: 'icheckbox_square_relative-blue',
                radioClass: 'iradio_square_relative-blue',
                increaseArea: '20%'
            });

            p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
            if (p_id) {
                benificiaryID = p_id;
                load_comBeneficiary_header();
                $('.btn-wizard').removeClass('disabled');
            } else {
                //beneficiary_template(8);
                $('.btn-wizard').addClass('disabled');
            }

            $('#beneficiary_form').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    projectID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_benefiviary_project_is_required');?>.'}}},/*Project is required*/
                    registeredDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_benefiviary_register_date_is_required');?>.'}}},/*Registered Date is required*/
                    emp_title: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_benefiviary_title_is_required');?>.'}}},/*Title is required*/
                    fullName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_full_name_is_required');?>.'}}},/*Full Name is required*/
                    nameWithInitials: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_benefiviary_name_with_initial_is_required');?>.'}}},/*Name with Initials is required*/
                    countryCodePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_phone_primary_is_required');?>.'}}},/*Phone (Primary) is required*/
                    phonePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_phone_primary_is_required');?>.'}}},/*Phone (Primary) is required*/
                    address: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_address_is_required');?>.'}}},/*Address is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $("#projectID").prop("disabled", false);
                $("#benificiaryType").prop("disabled", false);
                $(".disableHelp").prop("readonly", false);
                $("#emp_title").prop("disabled", false);
                $("#countryID").prop("disabled", false);
                $("#province").prop("disabled", false);
                $("#district").prop("disabled", false);
                $("#division").prop("disabled", false);
                $("#subDivision").prop("disabled", false);
                $("#FamMasterID").prop("disabled", false);
              //  $("#EconStateID").prop("disabled", false);
                $("#Com_MasterID").prop("disabled", false);
                $("#secondaryCode").prop("readonly", false);
                $("#countryCodePrimary").prop("disabled", false);
                tinymce.triggerSave();
                var contactID = $('#contactID').val();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('CommunityNgo/save_comBeneficiary'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            benificiaryID = data[2];
                            beneficiary_family_detail(benificiaryID);
                            $("#edit_beneficiary").val(benificiaryID);
                            $("#familyDetail_benificiaryID").val(benificiaryID);
                            $("#FamMasterIDs").val(FamMasterID);

                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                            $('.btn-wizard').removeClass('disabled');
                            $("#projectID").prop("disabled", true);
                            $('.btn-primary').prop('disabled', false);
                        } else {
                            $('.btn-primary').prop('disabled', false);
                            if(contactID != ''){
                                $("#projectID").prop("disabled", true);
                                $("#benificiaryType").prop("disabled", true);
                                $(".disableHelp").prop("readonly", true);
                                $("#emp_title").prop("disabled", true);
                                $("#countryID").prop("disabled", true);
                                $("#province").prop("disabled", true);
                                $("#district").prop("disabled", true);
                                $("#division").prop("disabled", true);
                                $("#subDivision").prop("disabled", true);
                                $("#FamMasterID").prop("disabled", true);
                               // $("#EconStateID").prop("disabled", true);
                                $("#Com_MasterID").prop("disabled", true);
                                $("#secondaryCode").prop("readonly", true);
                                $("#countryCodePrimary").prop("disabled", true);
                            }
                        }

                    },
                    error: function () {
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

            $("#projectID").change(function () {
                get_sub_projects($(this).val());
                beneficiary_template($(this).val());
            });


        });


        function fatch_beniMemDel() {

            var Com_MasterID = document.getElementById('Com_MasterIDs').value;

            if (Com_MasterID == "" || Com_MasterID == null) {
            } else {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "CommunityNgo/searchCommunityBeniFem",
                    data: {Com_MasterID: Com_MasterID},
                    success: function (datum) {

                        $('#name').val( datum.name );
                        $('#DOB').val(datum.CDOB);
                        $('#FamMasterID').val(datum.FamMasterID);
                        $('#idNO').val(datum.idNO);
                        $('#relationshipType').val(datum.relationshipType).change();
                        $('#gender').val(datum.gender).change();


                    }
                });

            }
        }

        function beneficiary_template(ngoProjectID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'ngoProjectID': ngoProjectID},
                url: "<?php echo site_url('CommunityNgo/load_comBeneficiaryTemplate_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#beneficiary_template').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function add_beneficiary_familyDetail() {
            $('#frm_FamilyContactDetails')[0].reset();
            $('#addFamilyDetailModal').modal('show');

            $('#empfamilydetailsID').val('0');
            $('.select2').select2();
            $("#familyDetail_benificiaryID").val(benificiaryID);
            $("#FamMasterIDs").val(FamMasterID);

            catch_femily_mem();
        }

        function catch_femily_mem() {

         var benificiaryIDs = document.getElementById('familyDetail_benificiaryID').value;

            if (benificiaryIDs == "" || benificiaryIDs == null) {
            } else {
                $.ajax({
                    type: "POST",
                    url: "CommunityNgo/get_FamMemCatch",
                    data: {benificiaryIDs: benificiaryIDs},
                    success: function (data) {

                        $('#Com_MasterIDs').html(data);
                    }
                });
            }
        }

        function saveFamilyDetails() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('CommunityNgo/save_comBeneficiary_familyDel') ?>",
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
                        beneficiary_family_detail(benificiaryID);
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

        function beneficiary_family_detail(beneficiaryID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'beneficiaryID': beneficiaryID},
                url: "<?php echo site_url('CommunityNgo/fetch_comBeneficiary_familyDel'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#beneficiary_familyDetail').html(data);
                    if (!jQuery.isEmptyObject(data)) {
                        $("#Com_MasterID").attr('disabled', 'disabled');
                    } else {
                        $("#Com_MasterID").removeAttr('disabled');
                    }
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_comBeneficiary_familyDel(id) {
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
                        url: "<?php echo site_url('CommunityNgo/delete_comBeneficiary_familyDel'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                beneficiary_family_detail(benificiaryID);
                            }
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                            // swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }

        function fetch_ngo_document() {
            var benificiaryID = $('#edit_beneficiary').val();
            var projectID = $('#projectID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'benificiaryID': benificiaryID, projectID: projectID},
                url: '<?php echo site_url("CommunityNgo/load_comBeneficiary_documents_view"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#beneficiary_document').html(data);
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');

                    stopLoad();
                }
            });
        }

        function save_draft() {
            if (benificiaryID) {
                swal({
                        title: "Are you sure?",
                        text: "You want to save this document!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Save as Draft"
                    },
                    function () {
                        fetchPage('system/communityNgo/ngo_mo_communityBeneficiary', '', 'Beneficiary');
                    });
            }
        }

        function confirmation() {
            var projectID = $('#projectID').val();
            var systemGeneratedCode = $('#systemGeneratedCode').val();
            if (benificiaryID) {
                swal({
                        title: "Are you sure?",
                        text: "You want confirm this Beneficiary "+systemGeneratedCode,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Confirm"
                    },
                    function () {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {'benificiaryID': benificiaryID, projectID: projectID},
                            url: "<?php echo site_url('CommunityNgo/comBeneficiary_confirmed'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                myAlert(data[0], data[1]);
                                if (data[0] == 's') {
                                    fetchPage('system/communityNgo/ngo_mo_communityBeneficiary', '', 'Beneficiary');
                                }
                            }, error: function () {
                                stopLoad();
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
        }

        function load_comBeneficiary_header() {
            if (benificiaryID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'benificiaryID': benificiaryID},
                    url: "<?php echo site_url('CommunityNgo/load_comBeneficiary_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#edit_beneficiary').val(benificiaryID);
                            $('#familyDetail_benificiaryID').val(benificiaryID);
                            $("#FamMasterIDs").val(FamMasterID);
                            $('#projectID').val(data['projectID']).change();
                            subProjectID = data['subProjectID'];
                            setTimeout(function () {
                                province = data['province'];
                                district = data['district'];
                                division = data['division'];
                                subDivision = data['subDivision'];
                                //Com_MasterID = data['Com_MasterID'];
                                $('#systemCode').val(data['systemCode']);
                                $('#Com_MasterID').val(data['Com_MasterID']).change();
                               // $('#EconStateID').val(data['EconStateID']).change();
                                $('#emp_title').val(data['titleID']).change();
                                $('#secondaryCode').val(data['secondaryCode']);
                                $('#fullName').val(data['fullName']);
                                $('#nameWithInitials').val(data['nameWithInitials']);
                                $('#benificiaryType').val(data['benificiaryType']).change();
                                $('#registeredDate').val(data['registeredDate']);
                                $('#dateOfBirth').val(data['dateOfBirth']);
                                $('#email').val(data['email']);
                                $('#countryCodePrimary').val(data['phoneCountryCodePrimary']);
                                $('#phoneAreaCodePrimary').val(data['phoneAreaCodePrimary']);
                                $('#phonePrimary').val(data['phonePrimary']);
                                $('#countryCodeSecondary').val(data['phoneCountryCodeSecondary']);
                                $('#phoneAreaCodeSecondary').val(data['phoneAreaCodeSecondary']);
                                $('#phoneSecondary').val(data['phoneSecondary']);
                                $('#postalcode').val(data['postalCode']);
                                $('#countryID').val(data['countryID']).change();
                                $('#address').val(data['address']);
                                $('#nationalIdentityCardNo').val(data['NIC']);
                                if (data['ownLandAvailable'] == 1) {
                                    $('#ownLandAvailableYes').iCheck('check');
                                }else if(data['ownLandAvailable'] == 2){
                                    $('#ownLandAvailableNo').iCheck('check');
                                }
                                $('#ownLandAvailableComments').val(data['ownLandAvailableComments']);
                                $('#totalSqFt').val(data['totalSqFt']);
                                $('#totalCost').val(data['totalCost']);
                                $('#familyDetail').val(data['familyMembersDetail']);
                                if(data['familyDescription'] != ''){
                                    tinymce.get('familyDescription').setContent(data['familyDescription']);
                                }else{
                                    tinymce.get('familyDescription').setContent(data['reasoninBrief']);
                                }
                                beneficiary_family_detail(benificiaryID);
                                $("#contact_text").addClass('hide');
                                $("#linkcontact_text").addClass('hide');
                            }, 1000);
                            $("#projectID").prop("disabled", true);
                        }
                        stopLoad();
                        refreshNotifications(true);
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        }

        function modaluploadimages(empfamilydetailsID) {
            $('#empfamilydetailzID').val(empfamilydetailsID);
            $('#modaluploadimages').modal('show');
        }

        function familyimage_uplode() {
            var benificiaryID = $('#edit_beneficiary').val();
            var formData = new FormData($("#family_image_uplode_form")[0]);
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "<?php echo site_url('CommunityNgo/comBeneficiary_familyImg_upload'); ?>",
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
                        beneficiary_family_detail(benificiaryID);
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

        function comBeneficiary_systemCode_generator() {
            $('#systemGeneratedCode').val('');
            if (benificiaryID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'benificiaryID': benificiaryID},
                    url: "<?php echo site_url('CommunityNgo/comBeneficiary_systemCode_generator'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#systemGeneratedCode').val(data);
                            confirmation();
                        }else{
                            myAlert('e', 'Please Fill the Area Details');
                        }
                        stopLoad();
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            }
        }

        function get_sub_projects(ngoProjectID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    ngoProjectID: ngoProjectID
                },
                url: "<?php echo site_url('CommunityNgo/fetch_ngoSub_projectsForCom'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#subProjectID').empty();
                    var mySelect = $('#subProjectID');
                    mySelect.append($('<option></option>').val("").html("Select"));
                    if (!$.isEmptyObject(data)) {
                        $.each(data, function (k, text) {
                            mySelect.append($('<option></option>').val(text['ngoProjectID']).html(text['description']));
                        });
                    }
                    if(subProjectID){
                        mySelect.val(subProjectID);
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    </script>

<?php
