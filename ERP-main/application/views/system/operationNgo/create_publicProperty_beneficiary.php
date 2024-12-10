<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('operationngo', $primaryLanguage);
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
            <?php echo $this->lang->line('operationngo_public_property_header'); ?><!--Property Header--></a>
       <a class="btn btn-default btn-wizard" href="#step2" id="tab"
           onclick=""
           data-toggle="tab">Damage Assessment</a>
        <a class="btn btn-default btn-wizard" href="#step6"
           onclick="fetch_beneficiary_imageView()"
           data-toggle="tab">Image Upload</a>
        <a class="btn btn-default btn-wizard" href="#step5" onclick="fetch_ngo_document()" data-toggle="tab">
            <?php echo $this->lang->line('operationngo_benificiary_documents'); ?><!--Documents--></a>
    </div>
    <hr>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="beneficiary_form"'); ?>
            <input type="hidden" name="publicPropertyBeneID" id="edit_beneficiary">
            <input type="hidden" name="systemGeneratedCode" id="systemGeneratedCode">
            <input type="hidden" name="templateType" id="templateType">

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>
                            <?php echo $this->lang->line('operationngo_public_property_template'); ?><!--PUBLIC PROPERTY TEMPLATE--></h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title"><?php echo $this->lang->line('common_project'); ?><!--Project--></label>
                        </div>

                        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php
                    echo form_dropdown('projectID', fetch_project_donor_drop_damage_assestment(), '', 'class="form-control select2" id="projectID" required');

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
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>PROPERTY ASSESSMENT</h2>
                    </header>
                    <?php echo form_open('', 'role="form" id="propertyAssesment_beneficiary_form"'); ?>
                    <input type="hidden" name="publicPropertyBeneID" id="edit_damageAssesment_beneficiary">

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
                            <label class="title">Property Condition</label>
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

            <div class="row" style="display:none;">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>HUMAN INJURY ASSESSMENT</h2>
                    </header>
                </div>
            </div>
            <div class="row" style="display:none;">
                <div class="col-md-12">
                    <button type="button" onclick="add_familyDetail_humanInury_model()" class="btn btn-primary pull-right">
                        <i class="fa fa-plus"></i>&nbsp;Add
                    </button>
                </div>
            </div>
            <div class="row" style="display:none;">
                <div class="col-md-12">
                    <div id="human_injuryAssessment_body"></div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>DAMAGE ASSESSMENT FOR PROPERTY ITEMS</h2>
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
                <button class="btn btn-success submitWizard" onclick="publicProperty_system_code_generator()">
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

    <div class="modal fade" id="beneficiary-manage-country-modal" role="dialog" data-keyboard="false"
         data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <?php echo $this->lang->line('operationngo_benefiviary_new_province'); ?><!--New Province--></h4>
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
                        <?php echo $this->lang->line('operationngo_benefiviary_new_district'); ?><!--New District--></h4>
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
                        <?php echo $this->lang->line('operationngo_benefiviary_new_division'); ?><!--New Division--></h4>
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
                        <input type="hidden" value="" id="humanInjuryAssessment_publicPropertyBeneID" name="publicPropertyBeneID">
                        <input type="hidden" value="" id="humanInjuryAssessment_humanInjuryID" name="humanInjuryID">


                        <div class="row" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Effected Person Name</label>
                                </div>
                                <div class="form-group col-sm-8">
                                 <span class="input-req" title="Required Field">
                                <input type="text" name="effectedPersonName" id="da_id_effectedPersonName"
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
                        <input type="hidden" value="" id="houseItemAssessment_publicPropertyBeneID" name="publicPropertyBeneID">
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
                        <div class="row hide insuranceHouseItem_cls" style="margin-top: 1%;">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-4">
                                    <label class="title">Vehicle Type</label>
                                </div>
                                <div class="form-group col-sm-8">
                                 <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('vehicleAutoID', fetch_vehicleMaster(), '',
                                    'class="form-control select2" id="vehicleAutoID" '); ?>
                                     <span class="input-req-inner"></span></span>
                                </div>
                            </div>
                        </div>
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
                                    <label class="title">Damage Assessment Amount as per the Property</label>
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
                    <button type="button" class="btn btn-primary" onclick="savePD_itemdamage_assitance()">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
    <script type="text/javascript">
        var publicPropertyBeneID;
        var province;
        var district;
        var division;
        var subDivision;
        var jamiyaDivision;
        var gnDivision;
        var subProjectID = '';
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/operationNgo/publicProperty_beneficiary_master', '', 'Public Property Beneficiary');
            });
            $('.select2').select2();

            publicPropertyBeneID = null;
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
                publicPropertyBeneID = p_id;
                load_publicProperty_header();
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
                    projectID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_benefiviary_project_is_required');?>.'}}}, /*Project is required*/
                    registeredDate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_benefiviary_register_date_is_required');?>.'}}}, /*Registered Date is required*/
                    PropertyName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_full_name_is_required');?>.'}}}, /*Full Name is required*/
                    PropertyShortCode: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_benefiviary_name_with_initial_is_required');?>.'}}}, /*Property Short Code is required*/
                    countryCodePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_phone_primary_is_required');?>.'}}}, /*Phone (Primary) is required*/
                    phonePrimary: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_phone_primary_is_required');?>.'}}}, /*Phone (Primary) is required*/
                    address: {validators: {notEmpty: {message: '<?php echo $this->lang->line('operationngo_address_is_required');?>.'}}}, /*Address is required*/
                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $("#projectID").prop("disabled", false);
                $("#projectIDetail").prop("disabled", false);
                $("#propertyType").prop("disabled", false);
                $(".disableHelp").prop("readonly", false);
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
                    url: "<?php echo site_url('OperationNgo/save_publicProperty_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            publicPropertyBeneID = data[2];
                            $("#edit_beneficiary").val(publicPropertyBeneID);

                            fetch_humanInjuryAssessment(publicPropertyBeneID);fetch_houseItemAssessment(publicPropertyBeneID);
                            $("#edit_damageAssesment_beneficiary").val(publicPropertyBeneID);
                            $("#familyDetail_publicPropertyBeneID").val(publicPropertyBeneID);
                            $("#humanInjuryAssessment_publicPropertyBeneID").val(publicPropertyBeneID);
                            $("#houseItemAssessment_publicPropertyBeneID").val(publicPropertyBeneID);
                            $("#propertieItemAssessment_publicPropertyBeneID").val(publicPropertyBeneID);
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
                                $("#propertyType").prop("disabled", true);
                                $("#subPropertyId").prop("disabled", true);
                                $(".disableHelp").prop("readonly", true);
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
            });
        });


        function beneficiary_template(ngoProjectID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'ngoProjectID': ngoProjectID},
                url: "<?php echo site_url('OperationNgo/load_publicPropertyTemplate_view'); ?>",
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
                url: "<?php echo site_url('OperationNgo/load_publicPropertyTemplate_view'); ?>",
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

        function add_familyDetail_humanInury_model() {
            $('#frm_human_damageAssitance')[0].reset();
            $('#family_human_damageAssitance_Modal').modal('show');
            $('#humanInjuryAssessment_humanInjuryID').val('');
            $('.select2').select2();
            $("#familyDetail_publicPropertyBeneID").val(publicPropertyBeneID);
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

        function fetch_ngo_document() {
            var publicPropertyBeneID = $('#edit_beneficiary').val();
            var projectID = $('#projectID').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'publicPropertyBeneID': publicPropertyBeneID, projectID: projectID},
                url: '<?php echo site_url("OperationNgo/load_publicProperty_documents_view"); ?>',
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
            if (publicPropertyBeneID) {
                swal({
                        title: "Are you sure?",
                        text: "You want to save this document!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Save as Draft"
                    },
                    function () {
                        fetchPage('system/operationNgo/publicProperty_beneficiary_master', '', 'Public Property Beneficiary');
                    });
            }
        }

        function confirmation() {
            var projectID = $('#projectID').val();
            var systemGeneratedCode = $('#systemGeneratedCode').val();
            if (publicPropertyBeneID) {
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
                            data: {'publicPropertyBeneID': publicPropertyBeneID, projectID: projectID},
                            url: "<?php echo site_url('OperationNgo/publicProperty_confirmation'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                myAlert(data[0], data[1]);
                                if (data[0] == 's') {
                                    fetchPage('system/operationNgo/publicProperty_beneficiary_master', '', 'Public Property Beneficiary');
                                }
                            }, error: function () {
                                stopLoad();
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    });
            }
        }

        function load_publicProperty_header() {
            if (publicPropertyBeneID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'publicPropertyBeneID': publicPropertyBeneID},
                    url: "<?php echo site_url('OperationNgo/load_publicProperty_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#edit_beneficiary').val(publicPropertyBeneID);
                            $('#edit_damageAssesment_beneficiary').val(publicPropertyBeneID);
                            $('#familyDetail_publicPropertyBeneID').val(publicPropertyBeneID);
                            $('#da_familyDetail_publicPropertyBeneID').val(publicPropertyBeneID);
                            $("#humanInjuryAssessment_publicPropertyBeneID").val(publicPropertyBeneID);
                            $("#houseItemAssessment_publicPropertyBeneID").val(publicPropertyBeneID);
                            $("#propertieItemAssessment_publicPropertyBeneID").val(publicPropertyBeneID);
                            fetch_humanInjuryAssessment(publicPropertyBeneID);fetch_houseItemAssessment(publicPropertyBeneID);
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
                                $('#secondaryCode').val(data['secondaryCode']);
                                $('#PropertyName').val(data['PropertyName']);
                                $('#PropertyShortCode').val(data['PropertyShortCode']);
                                $('#propertyType').val(data['propertyType']).change();
                                $('#registeredDate').val(data['registeredDate']);
                                $('#commencementDate').val(data['commencementDate']);
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
                                $('#totalCost').val(data['totalCost']);
                                $('#familyDetail').val(data['effectedPersonName']);
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
            var publicPropertyBeneID = $('#edit_beneficiary').val();
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
                        fetch_humanInjuryAssessment(publicPropertyBeneID);fetch_houseItemAssessment(publicPropertyBeneID);
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

        function publicProperty_system_code_generator() {
            $('#systemGeneratedCode').val('');
            if (publicPropertyBeneID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'publicPropertyBeneID': publicPropertyBeneID},
                    url: "<?php echo site_url('OperationNgo/publicProperty_system_code_generator'); ?>",
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


        function fetch_humanInjuryAssessment(publicPropertyBeneID) {
           // var publicPropertyBeneID = $('#edit_beneficiary').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'publicPropertyBeneID': publicPropertyBeneID},
                url: '<?php echo site_url("OperationNgo/load_human_injury_pd_view"); ?>',
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

        function fetch_houseItemAssessment(publicPropertyBeneID) {
          //  var publicPropertyBeneID = $('#edit_beneficiary').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'publicPropertyBeneID': publicPropertyBeneID},
                url: '<?php echo site_url("OperationNgo/load_property_damage_pd_view"); ?>',
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


        function savebeneficery_HeaderHouseDamage() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/save_property_header_damageAssesment') ?>",
                data: $("#propertyAssesment_beneficiary_form").serialize(),
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

        function savebeneficery_humanInjury_assesment() {

            var publicPropertyBeneID = $('#edit_beneficiary').val();
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/save_property_header_human_damageAssesment') ?>",
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
                        fetch_humanInjuryAssessment(publicPropertyBeneID);
                        $("#family_human_damageAssitance_Modal").modal('hide');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                }
            });
            return false;
        }

        function savePD_itemdamage_assitance() {

            var publicPropertyBeneID = $('#edit_beneficiary').val();
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('OperationNgo/save_pd_header_itemdamage_assesment') ?>",
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
                        fetch_houseItemAssessment(publicPropertyBeneID);
                        $("#house_items_damageAssitance_Modal").modal('hide');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                }
            });
            return false;
        }

        function fetch_monthly_expenditure_header() {
            var publicPropertyBeneID = $('#edit_beneficiary').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {publicPropertyBeneID: publicPropertyBeneID},
                url: '<?php echo site_url("OperationNgo/load_monthly_expenditure_pd_view"); ?>',
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
            var publicPropertyBeneID = $('#edit_beneficiary').val();
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
                                fetch_humanInjuryAssessment(publicPropertyBeneID);
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
            var publicPropertyBeneID = $('#edit_beneficiary').val();
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
                                fetch_houseItemAssessment(publicPropertyBeneID);
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
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }

        function edit_pd_human_injury_assessment(humanInjuryID) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                data: {humanInjuryID: humanInjuryID},
                url: "<?php echo site_url('OperationNgo/edit_pd_humanInjury_assestment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (!jQuery.isEmptyObject(data)) {
                        $('#humanInjuryAssessment_humanInjuryID').val(data['humanInjuryID']);
                        setTimeout(function () {
                            $('#da_hi_effectedPersonName').val(data['FamilyDetailsID']).change();
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
                            $('#vehicleAutoID').val(data['vehicleAutoID']).change();

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


        function fetch_beneficiary_imageView() {
            var publicPropertyBeneID = $('#edit_beneficiary').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'publicPropertyBeneID': publicPropertyBeneID},
                url: '<?php echo site_url("OperationNgo/load_publicProperty_multiple_img_view"); ?>',
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
