<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('communityngo', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
echo head_page($_POST['page_name'], false);
$this->load->helper('operation_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$all_states_arr = all_states();
$countries_arr = load_all_countrys();
$countryCode_arr = all_country_codes();
$currency_arr = all_currency_new_drop();
$school_arr = fetch_ngo_schoolMakthab(1);
$makthab_arr = fetch_ngo_schoolMakthab(2);
$occupation_arr = fetch_ngo_occupationMaster();
$pID = $this->input->post('page_id');

?>
    <div id="filter-panel" class="collapse filter-panel"></div>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
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
        <a class="btn btn-primary" href="#step1" data-toggle="tab">
            <?php echo $this->lang->line('CommunityNgo_benificiary_header'); ?><!--Beneficiary Header--></a>
        <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab">
            <?php echo $this->lang->line('CommunityNgo_benificiary_family_details'); ?><!--Beneficiary Family details--></a>
        <a class="btn btn-default btn-wizard hide" href="#step3" id="tab_ha"
           onclick="fetch_humanInjuryAssessment(),fetch_houseItemAssessment()"
           data-toggle="tab">Damage Assessment</a>
        <a class="btn btn-default btn-wizard hide" href="#step4" id="tab_ha_bp"
           onclick="fetch_properties_damageAssessment()"
           data-toggle="tab">Damage Assessment for Business Properties</a>
        <a class="btn btn-default btn-wizard" href="#step6"
           onclick="fetch_beneficiary_imageView()"
           data-toggle="tab">Image Upload</a>
        <a class="btn btn-default btn-wizard" href="#step5" onclick="fetch_ngo_document()" data-toggle="tab">
            <?php echo $this->lang->line('CommunityNgo_benificiary_doc'); ?><!--Documents--></a>
    </div>
    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="beneficiary_form"'); ?>
            <input type="hidden" name="benificiaryID" id="edit_beneficiary">
            <input type="hidden" name="systemGeneratedCode" id="systemGeneratedCode">
            <input type="hidden" name="templateType" id="templateType">

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>
                            <?php echo $this->lang->line('CommunityNgo_benefiviary_template'); ?><!--BENEFICIARY TEMPLATE--></h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_project'); ?><!--Project--></label>
                        </div>

                        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php
                    if ($pID == '')
                        echo form_dropdown('projectID', fetch_project_donor_drop(), '', 'class="form-control select2" id="projectID" required');
                    else {
                        echo form_dropdown('projectID', fetch_project_donor_drop(), '', 'class="form-control select2" id="projectID" required');
                    }
                    ?>
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
                        <button class="btn btn-primary" type="submit">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <div id="step2" class="tab-pane">
            <div class="row">
                <div class="col-md-8"><h4><i class="fa fa-hand-o-right"></i>
                        <?php echo $this->lang->line('CommunityNgo_benificiary_family_details'); ?><!--Beneficiary Family Details-->
                    </h4></div>
                <div class="col-md-4">
                    <button type="button" onclick="add_beneficiary_familyDetail()" class="btn btn-primary pull-right"><i
                            class="fa fa-plus"></i>
                        <?php echo $this->lang->line('CommunityNgo_benefiviary_add_familyDetails'); ?><!--Add Family Detail-->
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
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>HOUSE ASSESSMENT</h2>
                    </header>
                    <?php echo form_open('', 'role="form" id="damageAssesment_beneficiary_form"'); ?>
                    <input type="hidden" name="benificiaryID" id="edit_damageAssesment_beneficiary">

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Type of Damage</label>
                        </div>
                        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('da_typeOfhouseDamage', fetch_ngo_damageTypeMaster('1', 'HTD'), '',
                        'class="form-control select2 valueHelp disableHelp" id="da_typeOfhouseDamage" '); ?>
                    <span class="input-req-inner"></span></span>
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">House Type</label>
                        </div>
                        <div class="form-group col-sm-4">
                  <span class="input-req" title="Required Field">
                      <?php echo form_dropdown('da_houseCategory', fetch_ngo_buildingtypemaster('1'), '',
                          'class="form-control select2" id="da_houseCategory" '); ?>
                      <span class="input-req-inner"></span></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Housing Condition</label>
                        </div>
                        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('da_housingCondition', fetch_ngo_houseconditionmaster(), '', 'class="form-control select2 relatedTo" id="da_housingCondition"'); ?>
                    <span class="input-req-inner"></span></span>
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">Building Damages</label>
                        </div>
                        <div class="form-group col-sm-4">
                  <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('da_buildingDamages', fetch_ngo_damageTypeMaster('1', 'HBD'), '',
                        'class="form-control select2 valueHelp disableHelp" id="da_buildingDamages" '); ?>
                      <span class="input-req-inner"></span></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Estimated Cost for Repair</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <input type="text" name="da_estimatedRepairingCost" id="da_estimatedRepairingCost"
                                   onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                   class="form-control number">
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">Do you need assistance to repair?</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <div class="skin-section extraColumns">
                                <label class="radio-inline">
                                    <div class="skin-section extraColumnsgreen">
                                        <label for="checkbox">Yes&nbsp;&nbsp;</label>
                                        <input id="da_needAssistancetoRepairYes" type="radio" data-caption=""
                                               class="columnSelected"
                                               name="da_needAssistancetoRepairYN" value="1">
                                    </div>
                                </label>
                                <label class="radio-inline">
                                    <div class="skin-section extraColumnsgreen">
                                        <label for="checkbox">No&nbsp;&nbsp;</label>
                                        <input id="da_needAssistancetoRepairNo" type="radio" data-caption=""
                                               class="columnSelected"
                                               name="da_needAssistancetoRepairYN" value="2">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Total Paid Amount</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <input type="text" name="da_totalpaidamt" id="da_totalpaidamt"
                                   onkeypress="return validateFloatKeyPress(this,event)" value=""
                                   class="form-control number">
                        </div>
                    </div>

                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 ">
                    <button type="button" class="btn btn-primary pull-right" onclick="savebeneficery_HeaderHouseDamage()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>HUMAN INJURY ASSESSMENT</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="button" onclick="add_familyDetail_humanInury_model()" class="btn btn-primary pull-right">
                        <i class="fa fa-plus"></i>&nbsp;Add
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="human_injuryAssessment_body"></div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>DAMAGE ASSESSMENT FOR HOUSE ITEMS</h2>
                    </header>
                </div>
            </div>
            <!--        <div class="row">
                        <div class="col-md-12">
                            <button type="button" onclick="add_familyDetail_houseItem_model()" class="btn btn-primary pull-right">
                                <i class="fa fa-plus"></i>&nbsp;Add
                            </button>
                        </div>
                    </div>-->
            <div class="row">
                <div class="col-md-12">
                    <div id="house_items_injuryAssessment_body"></div>
                </div>
            </div>
        </div>
        <div id="step4" class="tab-pane">
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>DAMAGE ASSESSMENT FOR BUSINESS PROPERTIES</h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="button" onclick="add_familyDetail_businessProperties_model()"
                            class="btn btn-primary pull-right">
                        <i class="fa fa-plus"></i>&nbsp;Add
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="properties_assessment_body"></div>
                </div>
            </div>
        </div>
        <div id="step6" class="tab-pane">
            <div class="row">
                <div class="col-md-12"><h4><i class="fa fa-hand-o-right"></i> Image Upload </h4></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="beneficiary_image_view"></div>
                </div>
            </div>
        </div>
        <div id="step5" class="tab-pane">
            <div class="row">
                <div class="col-md-12">
                    <div id="beneficiary_document"></div>
                </div>
            </div>
            <div class="text-right m-t-xs">
                <button class="btn btn-default prev">
                    <?php echo $this->lang->line('common_previous'); ?><!--Previous--></button>
                <button class="btn btn-primary " onclick="save_draft()">
                    <?php echo $this->lang->line('common_save_as_draft'); ?><!--Save as Draft--></button>
                <button class="btn btn-success submitWizard" onclick="beneficiary_system_code_generator()">
                    <?php echo $this->lang->line('common_confirm'); ?><!--Confirm--></button>
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
                        <button type="button" class="btn btn-primary btn-sm" id="title-btn">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
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
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_beneficiary_type()">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
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
                    <h5>
                        <?php echo $this->lang->line('CommunityNgo_benefiviary_add_familyDetails'); ?><!--Add Family Detail--></h5>
                </div>
                <div class="modal-body" id="modal_contact">
                    <form method="post" name="frm_FamilyContactDetails" id="frm_FamilyContactDetails"
                          class="form-horizontal">
                        <input type="hidden" value="0" id="empfamilydetailsID"
                               name="empfamilydetailsID"/>
                        <input type="hidden" value="" id="familyDetail_benificiaryID" name="benificiaryID">

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="textinput">
                                <?php echo $this->lang->line('common_name'); ?><!--Name--></label>

                            <div class="col-md-7">
                                <input class="form-control input-md"
                                       placeholder="<?php echo $this->lang->line('common_name'); ?>" id="name" name="name"
                                       type="text" value=""><!--Name-->
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="relationshipType">
                                <?php echo $this->lang->line('common_relationship'); ?><!--Relationship--></label>

                            <div class="col-md-7">
                                <?php echo form_dropdown('relationshipType', hrms_relationship_drop(), '', 'id="relationshipType" class="form-control"'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="country">
                                <?php echo $this->lang->line('common_nationality'); ?><!--Nationality--></label>

                            <div class="col-md-7">
                                <?php echo form_dropdown('nationality', load_all_nationality_drop(), '', 'id="nationality" class="form-control select2"'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <?php echo $this->lang->line('common_date_of_birth'); ?><!--Date of Birth--></label>

                            <div class="input-group datepic col-md-7" style="padding-left: 15px;">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="DOB" style="width: 94%;"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="DOB" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="gender">
                                <?php echo $this->lang->line('common_gender'); ?><!--Gender--></label>

                            <div class="col-md-7">
                                <select name="gender" class="form-control empMasterTxt" id="gender">
                                    <option value="1"> <?php echo $this->lang->line('common_male'); ?><!--Male--></option>
                                    <option value="2">
                                        <?php echo $this->lang->line('common_female'); ?><!--Female--></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label" for="textinput">
                                <?php echo $this->lang->line('communityngo_nic'); ?><!--NIC No--></label>

                            <div class="col-md-7">
                                <input class="form-control input-md"
                                       placeholder="<?php echo $this->lang->line('communityngo_nic_brief'); ?>"
                                       id="idNO" name="idNO"
                                       type="text" value=""><!--National ID Number-->
                            </div>
                        </div>
                    </form>
                    <div id="familyDetail_msg"></div>
                </div>
                <div class="modal-footer" style="background-color: #ffffff">
                    <button type="button" class="btn btn-primary" onclick="saveFamilyDetails()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade pddLess" data-backdrop="static" id="addFamilyDetail_damageAssitance_Modal" data-width="80%"
         role="dialog">
        <div class="modal-dialog" style="width: 60%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                    </button>
                    <h5>Damage Assessment Family Detail</h5>
                </div>
                <div class="modal-body" id="modal_contact">
                    <form method="post" name="frm_FamilyContactDetails_damageAssessment"
                          id="frm_FamilyContactDetails_damageAssessment"
                          class="form-horizontal">
                        <input type="hidden" value="0" id="da_empfamilydetailsID"
                               name="empfamilydetailsID"/>
                        <input type="hidden" value="" id="da_familyDetail_benificiaryID" name="benificiaryID">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="textinput">
                                        <?php echo $this->lang->line('common_name'); ?><!--Name--></label>

                                    <div class="col-md-8">
                                     <span class="input-req" title="Required Field">
                                    <input class="form-control input-md"
                                           placeholder="<?php echo $this->lang->line('common_name'); ?>"
                                           id="name_family_detail_da"
                                           name="name"
                                           type="text" value=""><!--Name-->
                                          <span class="input-req-inner"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="relationshipType">Type</label>

                                    <div class="col-md-8">
                                <span class="input-req" title="Required Field">
                                    <?php echo form_dropdown('familyType', fetch_ngo_beneficiary_familydetail_types(), '', 'class="form-control relatedTo" id="familyType"'); ?>
                                    <span class="input-req-inner"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="relationshipType">
                                        <?php echo $this->lang->line('common_relationship'); ?><!--Relationship--></label>

                                    <div class="col-md-8">
                                    <span class="input-req" title="Required Field">
                                    <?php echo form_dropdown('relationshipType', hrms_relationship_drop(), '', 'id="da_relationship" class="form-control"'); ?>
                                        <span class="input-req-inner"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="country">
                                        <?php echo $this->lang->line('common_nationality'); ?><!--Nationality--></label>

                                    <div class="col-md-8">
                                    <span class="input-req" title="Required Field">
                                    <?php echo form_dropdown('nationality', load_all_nationality_drop(), '', 'id="nationality_family_detail_da" class="form-control select2"'); ?>
                                        <span class="input-req-inner"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">
                                        <?php echo $this->lang->line('common_date_of_birth'); ?><!--Date of Birth--></label>

                                    <div class="input-group datepic col-md-8" style="padding-left: 15px;">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="DOB" style="width: 94%;"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="" id="DOB_family_detail_da"
                                               class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="gender">
                                        <?php echo $this->lang->line('common_gender'); ?><!--Gender--></label>

                                    <div class="col-md-8">
                                    <span class="input-req" title="Required Field">
                                    <?php echo form_dropdown('gender', array('' => 'Select', '1' => 'Male', '2' => 'Female'), '', 'class="form-control relatedTo" id="gender_family_detail_da"'); ?>
                                        <span class="input-req-inner"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="textinput">
                                        <?php echo $this->lang->line('communityngo_nic'); ?><!--NIC No--></label>

                                    <div class="col-md-8">
                                        <input class="form-control input-md"
                                               placeholder="<?php echo $this->lang->line('communityngo_nic_brief'); ?>"
                                               id="idNO_family_detail_da" name="idNO"
                                               type="text" value=""><!--National ID Number-->
                                    </div>
                                </div>
                            </div>
                            <!--                    <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="relationshipType">Rel. to HH Head</label>

                                <div class="col-md-8">
                                    <?php /*echo form_dropdown('RelatedHHHead', array('' => 'Select Type', 'S' => 'S', 'D' => 'D', 'P' => 'P', 'R' => 'R'), '', 'class="form-control relatedTo" id="RelatedHHHead"'); */ ?>
                                </div>
                            </div>
                        </div>-->
                        </div>
                        <div class="row hide damageassestmenttype_cls" style="margin-top: 1%;">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="relationshipType">School</label>

                                    <div class="col-md-8">
                                        <?php echo form_dropdown('schoolID', $school_arr, '', 'class="form-control relatedTo" id="schoolID"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="country">Grade</label>

                                    <div class="col-md-8">
                                        <input class="form-control input-md" placeholder="Grade" id="schoolGrade"
                                               name="schoolGrade"
                                               type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row hide damageassestmenttype_cls" style="margin-top: 1%;">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="country">Class Rank</label>

                                    <div class="col-md-8">
                                        <input class="form-control input-md" placeholder="Class Rank" id="schoolRank"
                                               name="schoolRank" type="text">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row hide damageassestmenttype_cls" style="margin-top: 1%;">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="relationshipType">Makthab</label>

                                    <div class="col-md-8">
                                        <?php echo form_dropdown('makthabID', $makthab_arr, '', 'class="form-control relatedTo" id="da_makthabID"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="country">Makthab Grade</label>

                                    <div class="col-md-8">
                                        <input class="form-control input-md" placeholder="Makthab Grade" id="makthabGrade"
                                               name="makthabGrade"
                                               type="text">
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="relationshipType">Disability</label>

                                    <div class="col-md-8">
                                        <?php echo form_dropdown('Disability', array('' => 'Select', 'Yes' => 'Yes', 'No' => 'No'), '', 'class="form-control relatedTo" id="da_Disability"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="relationshipType">Occupation</label>

                                    <div class="col-md-8">
                                        <?php echo form_dropdown('occupationID', fetch_ngo_jobcategories(), '', 'class="form-control relatedTo" id="occupationID"'); ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="relationshipType">Remarks</label>

                                    <div class="col-md-8">
                                    <textarea class="form-control relatedTo" name="familyremarks"
                                              id="familyremarks"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="familyDetail_msg"></div>
                </div>
                <div class="modal-footer" style="background-color: #ffffff">
                    <button type="button" class="btn btn-primary" onclick="saveFamilyDetails_damageAssessment()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
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
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('CommunityNgo_benefiviary_new_province'); ?><!--New Province--></h4>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('common_description'); ?><!--Description--></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="country_description"
                                           name="country_description">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_province()">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
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
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('CommunityNgo_benefiviary_new_district'); ?><!--New District--></h4>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('common_description'); ?><!--Description--></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="description_district"
                                           name="description_district">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_district()">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
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
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('CommunityNgo_benefiviary_new_division'); ?><!--New Division--></h4>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">
                                    <?php echo $this->lang->line('common_description'); ?><!--Description--></label>

                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="description_division"
                                           name="description_division">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="save_division()">
                            <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
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
                    <input type="hidden" class="form-control" value="" id="empfamilydetailzID"
                           name="empfamilydetailsID">
                    <!-- File Button -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="filebutton">
                            <?php echo $this->lang->line('common_attachment'); ?><!--Attachment--> </label><!--Attachment-->

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

    <div class="modal fade pddLess" data-backdrop="static" id="family_human_damageAssitance_Modal" data-width="80%"
         role="dialog">
        <div class="modal-dialog" style="width: 40%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                    </button>
                    <h5>Human Injury Assessment</h5>
                </div>
                <div class="modal-body" id="modal_contact">
                    <form method="post" name="frm_human_damageAssitance" id="frm_human_damageAssitance"
                          class="form-horizontal">
                        <input type="hidden" value="" id="humanInjuryAssessment_benificiaryID" name="benificiaryID">
                        <input type="hidden" value="" id="humanInjuryAssessment_humanInjuryID" name="humanInjuryID">


                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Family Member</label>
                                </div>
                                <div class="form-group col-sm-8">
                                <span class="input-req" title="Required Field">
                                <div id="div_familyMembers">
                                    <select name="groupEmployeeID[]" id="groupEmployeeID"
                                            class="form-control select2">
                                    </select>
                                </div>
                                    <span class="input-req-inner"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Type of Damage</label>
                                </div>
                                <div class="form-group col-sm-8">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('damageTypeID', fetch_ngo_damageTypeMaster('2', 'HI'), '',
                        'class="form-control select2 valueHelp disableHelp" id="da_hi_damageTypeID"'); ?>
                    <?php /*echo form_dropdown('damageTypeID[]', fetch_ngo_damageTypeMaster_multiple('2', 'HI'), '',
                        'class="form-control select2 valueHelp disableHelp" id="damageTypeID" multiple="" '); */ ?>
                    <span class="input-req-inner"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Estimated Amount</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input type="text" name="estimatedAmount" id="da_hi_estimatedRepairingCost"
                                           onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                           class="form-control number">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Total Paid Amount</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input type="text" name="totalpaidamt" id="totalpaidamt"
                                           onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                           class="form-control number">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Remarks</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <textarea class="form-control relatedTo" name="remarks"
                                              id="da_hi_familyremarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="background-color: #ffffff">
                    <button type="button" class="btn btn-primary" onclick="savebeneficery_humanInjury_assesment()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade pddLess" data-backdrop="static" id="house_items_damageAssitance_Modal" data-width="80%"
         role="dialog">
        <div class="modal-dialog" style="width: 40%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                    </button>
                    <h5>Damage Assessment For Items</h5>
                </div>
                <div class="modal-body" id="modal_contact">
                    <form method="post" name="frm_items_damageAssitance" id="frm_items_damageAssitance"
                          class="form-horizontal">
                        <input type="hidden" value="" id="houseItemAssessment_benificiaryID" name="benificiaryID">
                        <input type="hidden" value="" id="da_id_damageItemCategoryID" name="damageItemCategoryID">
                        <input type="hidden" value="" id="da_id_itemDamagedID" name="itemDamagedID">
                        <!--                    <div class="row" style="margin-top: 1%;">
                        <div class="col-sm-12">
                            <div class="form-group col-sm-4">
                                <label class="title">Category</label>
                            </div>
                            <div class="form-group col-sm-8">
                                <span class="input-req" title="Required Field">
                                <?php /*echo form_dropdown('damageItemCategoryID', fetch_ngo_damageItemCategories(), '', 'id="da_id_damageItemCategoryID" class="form-control"'); */ ?>
                                    <span class="input-req-inner"></span></span>
                            </div>
                        </div>
                    </div>-->
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Description</label>
                                </div>
                                <div class="form-group col-sm-8">
                                 <span class="input-req" title="Required Field">
                                <input type="text" name="itemDescription" id="da_id_itemDescription"
                                       class="form-control">
                                     <span class="input-req-inner"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Type of Damage</label>
                                </div>
                                <div class="form-group col-sm-8">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('damageTypeID', fetch_ngo_damageTypeMaster('3', 'ID'), '',
                        'class="form-control select2 valueHelp disableHelp" id="da_id_damageTypeID" '); ?>
                    <span class="input-req-inner"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Item Condition</label>
                                </div>
                                <div class="form-group col-sm-8">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('damageConditionID', fetch_ngo_damageTypeMaster('3', 'IDC'), '',
                        'class="form-control select2 valueHelp disableHelp" id="da_damageConditionID" '); ?>
                    <span class="input-req-inner"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Damage Assessment Amount as per the client</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input type="text" name="damagedAmountClient" id="da_id_damagedAmountClient"
                                           onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                           class="form-control number">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Total Paid Amount</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input type="text" name="totalpaidamtitem" id="totalpaidamtitem"
                                           onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                           class="form-control number">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Brand</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input type="text" name="Brand" id="da_id_Brand"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row hide insuranceHouseItem_cls" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Insurance</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <?php echo form_dropdown('isInsuranceYN', array('' => 'Select', '1' => 'Yes', '0' => 'No'), '', 'class="form-control relatedTo" id="da_id_isInsuranceYN"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row hide insuranceHouseItem_cls insuraneHouseItemYnhide_Cls" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Insurance Type</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <?php echo form_dropdown('insuranceTypeID', fetch_ngo_insurancetypemaster('1'), '',
                                        'class="form-control select2 " id="da_id_insuranceTypeID" '); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row hide insuranceHouseItem_cls insuraneHouseItemYnhide_Cls" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Insurance Remarks</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <textarea class="form-control relatedTo" name="insuranceRemarks"
                                              id="da_id_remarks"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Assessed Value</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input type="text" name="assessedValue" id="da_id_assessedValue"
                                           onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                           class="form-control number">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="background-color: #ffffff">
                    <button type="button" class="btn btn-primary" onclick="savebeneficery_itemdamage_assitance()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade pddLess" data-backdrop="static" id="businessProperties_assessment_Modal" data-width="80%"
         role="dialog">
        <div class="modal-dialog" style="width: 40%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                    </button>
                    <h5>Damage Assessment for Business Properties</h5>
                </div>
                <div class="modal-body" id="modal_contact">
                    <form method="post" name="frm_propertie_damageAssitance" id="frm_propertie_damageAssitance"
                          class="form-horizontal">
                        <input type="hidden" value="" id="propertieItemAssessment_benificiaryID" name="benificiaryID">
                        <input type="hidden" value="" id="da_bp_businessDamagedID" name="businessDamagedID">

                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Business Activity</label>
                                </div>
                                <div class="form-group col-sm-8">
                                <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('busineesActivityID', fetch_ngo_business_activity(), '', 'id="da_bp_busineesActivityID" class="form-control"'); ?>
                                    <span class="input-req-inner"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Business Type</label>
                                </div>
                                <div class="form-group col-sm-8">
                                <span class="input-req" title="Required Field">
                      <?php echo form_dropdown('buildingTypeID', fetch_ngo_buildingtypemaster('2'), '',
                          'class="form-control select2" id="da_bp_buildingTypeID" '); ?>
                                    <span class="input-req-inner"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Type of Damage</label>
                                </div>
                                <div class="form-group col-sm-8">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('damageTypeID', fetch_ngo_damageTypeMaster('4', 'BPD'), '',
                        'class="form-control select2 valueHelp disableHelp" id="da_bp_damageTypeID" '); ?>
                    <span class="input-req-inner"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Property Condition</label>
                                </div>
                                <div class="form-group col-sm-8">
                <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('damageConditionID', fetch_ngo_damageTypeMaster('4', 'BPC'), '',
                        'class="form-control select2 valueHelp disableHelp" id="da_bp_damageConditionID" '); ?>
                    <span class="input-req-inner"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Income Source</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <?php echo form_dropdown('incomeSourceType', fetch_ngo_incomesourcemaster(), '', 'class="form-control relatedTo" id="da_bp_incomeSourceType"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Value of the Property</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input type="text" name="propertyValue" id="da_bp_propertyValue"
                                           onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                           class="form-control number">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Total Paid Amount</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input type="text" name="totalpaidamtbsp" id="totalpaidamtbsp"
                                           onkeypress="return validateFloatKeyPress(this,event)" value="00"
                                           class="form-control number">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Condition of Existing item</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <?php echo form_dropdown('existingItemCondition', fetch_ngo_damageTypeMaster('4', 'BPIC'), '',
                                        'class="form-control select2 valueHelp disableHelp" id="da_bp_existingItemCondition" '); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Insurance</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <?php echo form_dropdown('isInsuranceYN', array('' => 'Select', '1' => 'Yes', '0' => 'No'), '', 'class="form-control relatedTo" id="da_bp_isInsuranceYN"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row  hide insurance_damage_assetment_cls" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Insurance Type</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <?php echo form_dropdown('insuranceTypeID', fetch_ngo_insurancetypemaster('1'), '',
                                        'class="form-control select2 " id="da_bp_insuranceTypeID" '); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row hide insurance_damage_assetment_cls" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Insurance Remarks</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <textarea class="form-control relatedTo" name="insuranceRemarks"
                                              id="da_bp_insuranceRemarks"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Expectations</label>
                                </div>
                                <div class="form-group col-sm-8">
                                       <textarea class="form-control relatedTo" name="expectations"
                                                 id="da_bp_expectations"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="background-color: #ffffff">
                    <button type="button" class="btn btn-primary" onclick="savebeneficery_businessProperties_assitance()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
                </div>
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
        var jamiyaDivision;
        var gnDivision;
        var subProjectID = '';
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
            jamiyaDivision = null;
            gnDivision = null;

            Inputmask().mask(document.querySelectorAll("input"));

            $('.extraColumns input').iCheck({
                checkboxClass: 'icheckbox_square_relative-blue',
                radioClass: 'iradio_square_relative-blue',
                increaseArea: '20%'
            });

            p_id = <?php echo json_encode($pID); ?>;
            if (p_id) {
                benificiaryID = p_id;
                load_beneficiary_header();
                $('.btn-wizard').removeClass('disabled');
            } else {
                //beneficiary_template(8);
                $('.btn-wizard').addClass('disabled');
            }

            $('#beneficiary_form').bootstrapValidator({
                live: 'enabled',
                message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
                excluded: [':disabled'],
                fields: {
                    projectID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_benefiviary_project_is_required');?>.'}}}, /*Project is required*/
                    registeredDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_benefiviary_register_date_is_required');?>.'}}}, /*Registered Date is required*/
                    emp_title: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_benefiviary_title_is_required');?>.'}}}, /*Title is required*/
                    fullName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_full_name_is_required');?>.'}}}, /*Full Name is required*/
                    nameWithInitials: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_benefiviary_name_with_initial_is_required');?>.'}}}, /*Name with Initials is required*/
                    countryCodePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_phone_primary_is_required');?>.'}}}, /*Phone (Primary) is required*/
                    phonePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_phone_primary_is_required');?>.'}}}, /*Phone (Primary) is required*/
                    address: {validators: {notEmpty: {message: '<?php echo $this->lang->line('CommunityNgo_address_is_required');?>.'}}}, /*Address is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $("#projectID").prop("disabled", false);
                $("#projectIDetail").prop("disabled", false);
                $("#benificiaryType").prop("disabled", false);
                $(".disableHelp").prop("readonly", false);
                $("#emp_title").prop("disabled", false);
                $("#countryID").prop("disabled", false);
                $("#province").prop("disabled", false);
                $("#district").prop("disabled", false);
                $("#division").prop("disabled", false);
                $("#subDivision").prop("disabled", false);
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
                    url: "<?php echo site_url('OperationNgo/save_beneficiary_header'); ?>",
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
                            $("#edit_damageAssesment_beneficiary").val(benificiaryID);
                            $("#familyDetail_benificiaryID").val(benificiaryID);
                            $("#humanInjuryAssessment_benificiaryID").val(benificiaryID);
                            $("#houseItemAssessment_benificiaryID").val(benificiaryID);
                            $("#propertieItemAssessment_benificiaryID").val(benificiaryID);
                            $('[href=#step2]').tab('show');
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('[href=#step2]').removeClass('btn-default');
                            $('[href=#step2]').addClass('btn-primary');
                            $('.btn-wizard').removeClass('disabled');
                            $("#projectID").prop("disabled", true);
                            $("#projectIDetail").prop("disabled", true);
                            $('.btn-primary').prop('disabled', false);
                        } else {
                            $('.btn-primary').prop('disabled', false);
                            if (contactID != '') {
                                $("#projectID").prop("disabled", true);
                                $("#projectIDetail").prop("disabled", true);
                                $("#benificiaryType").prop("disabled", true);
                                $(".disableHelp").prop("readonly", true);
                                $("#emp_title").prop("disabled", true);
                                $("#countryID").prop("disabled", true);
                                $("#province").prop("disabled", true);
                                $("#district").prop("disabled", true);
                                $("#division").prop("disabled", true);
                                $("#subDivision").prop("disabled", true);
                                $("#secondaryCode").prop("readonly", true);
                                $("#countryCodePrimary").prop("disabled", true);
                            }
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

            $("#projectID").change(function () {
                //get_sub_projects($(this).val());
                check_project_shortCode(this.value);
                beneficiary_template(this.value);
                //load_template_according_to_user_credintiol();
                ;
            });
        });


        function beneficiary_template(ngoProjectID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'ngoProjectID': ngoProjectID},
                url: "<?php echo site_url('OperationNgo/load_beneficiaryTemplate_view'); ?>",
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

        function beneficiary_template2(ngoProjectID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'ngoProjectID': ngoProjectID},
                url: "<?php echo site_url('OperationNgo/load_beneficiaryTemplate_view'); ?>",
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
            var templateType = $('#templateType').val();
            $('#da_empfamilydetailsID').val('');
            if (templateType == 'DA') {
                $('#frm_FamilyContactDetails_damageAssessment')[0].reset();
                $('#addFamilyDetail_damageAssitance_Modal').modal('show');
                $('#empfamilydetailsID').val('0');
                $('.select2').select2();
                $("#familyDetail_benificiaryID").val(benificiaryID);

            } else {
                $('#frm_FamilyContactDetails')[0].reset();
                $('#addFamilyDetailModal').modal('show');
                $('#empfamilydetailsID').val('0');
                $('.select2').select2();
                $("#familyDetail_benificiaryID").val(benificiaryID);
            }
        }

        $("#familyType").change(function () {
            if (this.value == 2 || this.value == 3 || this.value == 4) {
                $('.damageassestmenttype_cls').removeClass('hide');
            } else {
                $('.damageassestmenttype_cls').addClass('hide');
            }
        });

        function add_familyDetail_humanInury_model() {
            $('#frm_human_damageAssitance')[0].reset();
            $('#family_human_damageAssitance_Modal').modal('show');
            $('#humanInjuryAssessment_humanInjuryID').val('');
            $('.select2').select2();
            load_human_injuryAssessment_members();
            $("#familyDetail_benificiaryID").val(benificiaryID);
        }

        function add_familyDetail_houseItem_model(id) {
            $('#frm_items_damageAssitance')[0].reset();
            $('#house_items_damageAssitance_Modal').modal('show');
            $('#da_id_itemDamagedID').val('');
            $('.select2').select2();
            $('#da_id_damageItemCategoryID').val(id);
            if (id == 3) {
                $('.insuranceHouseItem_cls').removeClass('hide');
                $('.insuraneHouseItemYnhide_Cls').addClass('hide');
            } else {
                $('.insuranceHouseItem_cls').addClass('hide');
            }
        }

        function add_familyDetail_businessProperties_model() {
            $('#frm_propertie_damageAssitance')[0].reset();
            $('#da_bp_businessDamagedID').val('');
            $('#businessProperties_assessment_Modal').modal('show');
            $('.select2').select2();
        }

        function saveFamilyDetails() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/save_beneficiary_familyDetails') ?>",
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

        function saveFamilyDetails_damageAssessment() {
            $('#da_familyDetail_benificiaryID').val(benificiaryID);
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/save_beneficiary_familyDetails_damageAssessment') ?>",
                data: $("#frm_FamilyContactDetails_damageAssessment").serialize(),
                dataType: "json",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data['0'], data['1']);
                    if (data['0'] == 's') {
                        beneficiary_family_detail(benificiaryID);
                        $("#addFamilyDetail_damageAssitance_Modal").modal('hide');
                    }
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
                url: "<?php echo site_url('OperationNgo/fetch_beneficiary_family_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#beneficiary_familyDetail').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_beneficiary_familydetail(id) {
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
                        url: "<?php echo site_url('OperationNgo/delete_beneficiary_familydetail'); ?>",
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
                url: '<?php echo site_url("OperationNgo/load_beneficiary_documents_view"); ?>',
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
                        text: "You want confirm this Beneficiary " + systemGeneratedCode,
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
                            url: "<?php echo site_url('OperationNgo/beneficiary_confirmation'); ?>",
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

        function load_beneficiary_header() {
            if (benificiaryID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'benificiaryID': benificiaryID},
                    url: "<?php echo site_url('OperationNgo/load_beneficiary_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#edit_beneficiary').val(benificiaryID);
                            $('#edit_damageAssesment_beneficiary').val(benificiaryID);
                            $('#familyDetail_benificiaryID').val(benificiaryID);
                            $('#da_familyDetail_benificiaryID').val(benificiaryID);
                            $("#humanInjuryAssessment_benificiaryID").val(benificiaryID);
                            $("#houseItemAssessment_benificiaryID").val(benificiaryID);
                            $("#propertieItemAssessment_benificiaryID").val(benificiaryID);
                            beneficiary_family_detail(benificiaryID);
                            fetch_monthly_expenditure_header();
                            $('#projectID').val(data['projectID']).change();
                            //$('#projectIDetail').val(data['projectID']).change();
                            check_project_shortCode(data['projectID']);
                            subProjectID = data['subProjectID'];
                            setTimeout(function () {
                                province = data['province'];
                                district = data['district'];
                                division = data['division'];
                                subDivision = data['subDivision'];
                                jamiyaDivision = data['da_jammiyahDivision'];
                                gnDivision = data['da_GnDivision'];
                                $('#systemCode').val(data['systemCode']);
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
                                $('#da_ethnicityID').val(data['ethnicityID']);
                                $("#da_GnDivision").val(data['da_GnDivision']);
                                $("#da_GsDivision").val(data['da_GsDivision']);
                                $("#da_mosque").val(data['da_mosque']);
                                $("#da_typeOfhouseDamage").val(data['da_typeOfhouseDamage']);
                                $("#da_housingCondition").val(data['da_housingCondition']);
                                $("#da_houseCategory").val(data['da_houseCategory']);
                                $("#da_buildingDamages").val(data['da_buildingDamages']);
                                $("#da_estimatedRepairingCost").val(data['da_estimatedRepairingCost']);
                                $("#da_totalpaidamt").val(data['da_paidAmount']);
                                $("#da_occupationID").val(data['da_occupationID']).change();
                                $("#da_economicStatus").val(data['da_economicStatus']);
                                $("#recommendedBy").val(data['recommendedBy']);
                                $("#recommendedDate").val(data['recommendedDate']);
                                $("#totalcostforahouse").val(data['totalEstimatedValue']);
                                if (data['ownLandAvailable'] == 1) {
                                    $('#ownLandAvailableYes').iCheck('check');
                                }else if(data['ownLandAvailable'] == 2){
                                    $('#ownLandAvailableNo').iCheck('check');
                                }
                                if (data['da_needAssistancetoRepairYN'] == 1) {
                                    $('#da_needAssistancetoRepairYes').iCheck('check');
                                } else if (data['da_needAssistancetoRepairYN'] == 2) {
                                    $('#da_needAssistancetoRepairNo').iCheck('check');
                                }
                                if (data['eligibleForZakathYN'] == 1) {
                                    $('#da_eligbleForZakathYes').iCheck('check');
                                }else if(data['eligibleForZakathYN'] == 2){
                                    $('#da_eligbleForZakathNo').iCheck('check');
                                }
                                $('#totalCost').val(data['totalCost']);
                                $('#familyDetail').val(data['familyMembersDetail']);
                                if (data['familyDescription'] != '') {
                                    tinymce.get('familyDescription').setContent(data['familyDescription']);
                                } else {
                                    tinymce.get('familyDescription').setContent(data['reasoninBrief']);
                                }
                                $("#contact_text").addClass('hide');
                                $("#linkcontact_text").addClass('hide');
                            }, 2000);
                            $("#projectID").prop("disabled", true);
                            $("#projectIDetail").prop("disabled", true);
                            stopLoad();
                        }

                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
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
                url: "<?php echo site_url('OperationNgo/beneficiary_familyimage_upload'); ?>",
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

        function beneficiary_system_code_generator() {
            $('#systemGeneratedCode').val('');
            if (benificiaryID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'benificiaryID': benificiaryID},
                    url: "<?php echo site_url('OperationNgo/beneficiary_system_code_generator'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#systemGeneratedCode').val(data);
                            confirmation();
                        } else {
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
                url: "<?php echo site_url('OperationNgo/fetch_ngo_sub_projects'); ?>",
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
                    if (subProjectID) {
                        mySelect.val(subProjectID);
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }


        function fetch_humanInjuryAssessment() {
            var benificiaryID = $('#edit_beneficiary').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'benificiaryID': benificiaryID},
                url: '<?php echo site_url("OperationNgo/load_human_injury_assessment_view"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#human_injuryAssessment_body').html(data);
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');

                    stopLoad();
                }
            });
        }

        function fetch_houseItemAssessment() {
            var benificiaryID = $('#edit_beneficiary').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'benificiaryID': benificiaryID},
                url: '<?php echo site_url("OperationNgo/load_house_items_assessment_view"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#house_items_injuryAssessment_body').html(data);
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function fetch_properties_damageAssessment() {
            var benificiaryID = $('#edit_beneficiary').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'benificiaryID': benificiaryID},
                url: '<?php echo site_url("OperationNgo/load_damage_property_assessment_view"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#properties_assessment_body').html(data);
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function savebeneficery_HeaderHouseDamage() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/save_beneficiary_header_house_damageAssesment') ?>",
                data: $("#damageAssesment_beneficiary_form").serialize(),
                dataType: "json",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                }
            });
            return false;
        }

        function load_human_injuryAssessment_members() {
            var benificiaryID = $('#edit_beneficiary').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("OperationNgo/load_human_InjuryAssessment_members"); ?>',
                dataType: 'html',
                data: {benificiaryID: benificiaryID},
                async: true,
                beforeSend: function () {
                    $('#div_familyMembers').html('');
                    startLoad();
                },
                success: function (data) {
                    $('#div_familyMembers').html(data);
                    stopLoad();
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function savebeneficery_humanInjury_assesment() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/save_beneficiary_header_human_damageAssesment') ?>",
                data: $("#frm_human_damageAssitance").serialize(),
                dataType: "json",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetch_humanInjuryAssessment();
                        $("#family_human_damageAssitance_Modal").modal('hide');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                }
            });
            return false;
        }

        function savebeneficery_itemdamage_assitance() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/save_beneficiary_header_itemdamage_assesment') ?>",
                data: $("#frm_items_damageAssitance").serialize(),
                dataType: "json",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetch_houseItemAssessment();
                        $("#house_items_damageAssitance_Modal").modal('hide');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                }
            });
            return false;
        }

        function savebeneficery_businessProperties_assitance() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/save_beneficiary_header_businessProperties_assesment') ?>",
                data: $("#frm_propertie_damageAssitance").serialize(),
                dataType: "json",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetch_properties_damageAssessment();
                        $("#businessProperties_assessment_Modal").modal('hide');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                }
            });
            return false;
        }

        function fetch_monthly_expenditure_header() {
            var benificiaryID = $('#edit_beneficiary').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {benificiaryID: benificiaryID},
                url: '<?php echo site_url("OperationNgo/load_monthly_expenditure_header_view"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#monthly_expenditure_body').html(data);
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function delete_human_injury_assessment(id) {
            var benificiaryID = $('#edit_beneficiary').val();
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
                        data: {'humanInjuryID': id},
                        url: "<?php echo site_url('OperationNgo/delete_human_injury_assessment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_humanInjuryAssessment();
                            }
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                            // swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }


        function delete_house_items_assessment(id) {
            var benificiaryID = $('#edit_beneficiary').val();
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
                        data: {'itemDamagedID': id},
                        url: "<?php echo site_url('OperationNgo/delete_house_items_assessment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_houseItemAssessment();
                            }
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        }
                    });
                });
        }

        function delete_business_properties_assessment(id) {
            var benificiaryID = $('#edit_beneficiary').val();
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
                        data: {'businessDamagedID': id},
                        url: "<?php echo site_url('OperationNgo/delete_business_properties_assessment'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetch_properties_damageAssessment();
                            }
                        }, error: function (XMLHttpRequest, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', "Status: " + textStatus + "Error: " + errorThrown);
                        }
                    });
                });
        }

        function check_project_shortCode(projectID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'ngoProjectID': projectID},
                url: "<?php echo site_url('OperationNgo/check_project_shortCode'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        if (data['projectShortCode'] == 'DA') {
                            $('#tab_ha').removeClass('hide');
                            $('#tab_ha_bp').removeClass('hide');
                        } else {
                            $('#tab_ha').addClass('hide');
                            $('#tab_ha_bp').addClass('hide');
                        }
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

        function check_project_shortCode2(projectID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'ngoProjectID': projectID},
                url: "<?php echo site_url('OperationNgo/check_project_shortCode'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        if (data['projectShortCode'] == 'DA') {
                            $('#tab_ha').removeClass('hide');
                            $('#tab_ha_bp').removeClass('hide');
                        } else {
                            $('#tab_ha').addClass('hide');
                            $('#tab_ha_bp').addClass('hide');
                        }
                        $('#templateType').val(data['projectShortCode']);

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

        function edit_beneficiary_familydetail(empfamilyid) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {empfamilyid: empfamilyid},
                url: "<?php echo site_url('OperationNgo/edit_benificiary_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
                        $('#da_empfamilydetailsID').val(data['empfamilydetailsID']);
                        $('#name_family_detail_da').val(data['name']);
                        $('#da_relationship').val(data['relationship']);
                        $('#DOB_family_detail_da').val(data['DOB']);
                        $('#idNO_family_detail_da').val(data['idNO']);
                        $('#schoolID').val(data['schoolID']);
                        $('#schoolRank').val(data['classRank']);
                        $('#da_makthabID').val(data['makthabID']);
                        $('#da_Disability').val(data['Disability']);
                        $('#familyremarks').val(data['remarks']);
                        $('#familyType').val(data['type']);
                        $('#nationality_family_detail_da').val(data['nationality']).change();
                        $('#gender_family_detail_da').val(data['gender']);
                        $('#schoolGrade').val(data['schoolGrade']);
                        $('#makthabGrade').val(data['makthabGrade']);
                        $('#occupationID').val(data['occupationID']);
                    }

                    $('#addFamilyDetail_damageAssitance_Modal').modal('show');

                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function edit_human_injury_assessment(humanInjuryID) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {humanInjuryID: humanInjuryID},
                url: "<?php echo site_url('OperationNgo/edithumaninjury_assestment'); ?>",
                beforeSend: function () {
                    startLoad();
                    load_human_injuryAssessment_members();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
                        $('#humanInjuryAssessment_humanInjuryID').val(data['humanInjuryID']);
                        setTimeout(function () {
                            $('#da_hi_familyMembers').val(data['FamilyDetailsID']).change();
                        }, 50);
                        $('#humanInjuryAssessment_familydetailsid').val(data['FamilyDetailsID']);
                        $('#da_hi_damageTypeID').val(data['damageTypeID']).change();
                        $('#da_hi_estimatedRepairingCost').val(data['estimatedAmount']);
                        $('#totalpaidamt').val(data['paidAmount']);
                        $('#da_hi_familyremarks').val(data['remarks']);
                    }
                    $('#family_human_damageAssitance_Modal').modal('show');
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function load_house_items_assessment(itemDamagedID) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {itemDamagedID: itemDamagedID},
                url: "<?php echo site_url('OperationNgo/load_item_damage'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    load_human_injuryAssessment_members();
                    if (!jQuery.isEmptyObject(data)) {
                        $('#da_id_damageItemCategoryID').val(data['damageItemCategoryID']);
                        $('#da_id_itemDamagedID').val(data['itemDamagedID']);

                        if (data['isInsuranceYN'] == 1) {
                            $('.insuraneHouseItemYnhide_Cls').removeClass('hide');
                        } else {
                            $('.insuraneHouseItemYnhide_Cls').addClass('hide');
                        }

                        if (data['damageItemCategoryID'] == 3) {
                            $('.insuranceHouseItem_cls').removeClass('hide');
                            $('#da_id_itemDescription').val(data['itemDescription']);
                            $('#da_id_damageTypeID').val(data['damageTypeID']).change();
                            $('#da_damageConditionID').val(data['damageConditionID']).change();
                            $('#da_id_damagedAmountClient').val(data['damagedAmountClient']);
                            $('#totalpaidamtitem').val(data['paidAmount']);
                            $('#da_id_Brand').val(data['Brand']);
                            $('#da_id_assessedValue').val(data['assessedValue']);
                            $('#da_id_isInsuranceYN').val(data['isInsuranceYN']).change();
                            $('#da_id_remarks').val(data['insuranceRemarks']);
                            $('#da_id_insuranceTypeID').val(data['insuranceTypeID']).change();

                        } else {
                            $('.insuranceHouseItem_cls').addClass('hide');
                            $('#da_id_itemDescription').val(data['itemDescription']);
                            $('#da_id_damageTypeID').val(data['damageTypeID']).change();
                            $('#da_damageConditionID').val(data['damageConditionID']).change();
                            $('#da_id_damagedAmountClient').val(data['damagedAmountClient']);
                            $('#totalpaidamtitem').val(data['paidAmount']);
                            $('#da_id_Brand').val(data['Brand']);
                            $('#da_id_assessedValue').val(data['assessedValue']);

                        }

                    }

                    $('#house_items_damageAssitance_Modal').modal('show');
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function load_business_properties_assessment(businessDamagedID) {

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {businessDamagedID: businessDamagedID},
                url: "<?php echo site_url('OperationNgo/load_item_damage_bussiness_properties'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
                        $('#da_bp_businessDamagedID').val(data['businessDamagedID']);
                        $('#da_bp_busineesActivityID').val(data['busineesActivityID']).change();
                        $('#da_bp_damageTypeID').val(data['damageTypeID']).change();
                        $('#damage_assessment_damageConditionID').val(data['damageConditionID']).change();
                        $('#da_bp_incomeSourceType').val(data['incomeSourceType']).change();
                        $('#da_bp_propertyValue').val(data['propertyValue']);
                        $('#totalpaidamtbsp').val(data['paidAmount']);
                        $('#da_bp_existingItemCondition').val(data['existingItemCondition']).change();
                        $('#da_bp_isInsuranceYN').val(data['isInsuranceYN']);
                        $('#da_bp_insuranceRemarks').val(data['insuranceRemarks']);
                        $('#da_bp_expectations').val(data['expectations']);
                        $('#da_bp_buildingTypeID').val(data['buildingTypeID']).change();
                        $('#da_bp_damageConditionID').val(data['damageConditionID']).change();
                        $('#da_bp_insuranceTypeID').val(data['insuranceTypeID']).change();
                        if (data['isInsuranceYN'] == 1) {
                            $('.insurance_damage_assetment_cls').removeClass('hide');
                        } else {
                            $('.insurance_damage_assetment_cls').addClass('hide');
                        }
                    }
                    $('#businessProperties_assessment_Modal').modal('show');
                },

                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });


        }

        function fetch_beneficiary_imageView() {
            var benificiaryID = $('#edit_beneficiary').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'benificiaryID': benificiaryID},
                url: '<?php echo site_url("OperationNgo/load_beneficiary_multiple_image_view"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#beneficiary_image_view').html(data);
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                }
            });
        }

        $("#da_bp_isInsuranceYN").change(function () {
            if (this.value == 1) {
                $('.insurance_damage_assetment_cls').removeClass('hide');
            } else {
                $('.insurance_damage_assetment_cls').addClass('hide');
            }
        });
        $("#da_id_isInsuranceYN").change(function () {
            if (this.value == 1) {
                $('.insuraneHouseItemYnhide_Cls').removeClass('hide');
            } else {
                $('.insuraneHouseItemYnhide_Cls').addClass('hide');
            }
        });


    </script>
<?php
