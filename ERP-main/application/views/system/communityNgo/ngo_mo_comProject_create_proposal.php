<?php echo head_page($_POST['page_name'], FALSE);
$primaryLanguage = getPrimaryLanguage();
$this->load->helper('community_ngo_helper');
$this->lang->load('communityNgo', $primaryLanguage);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$array_status = fetch_ngo_status(6);
$currency_arr = all_currency_new_drop();
$countries_arr = load_all_countries();
$fam_econSt = fetch_fam_econStatus();

$data_arr = array();
$pID = $this->input->post('page_id');
?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
    <link rel="stylesheet"
          href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
    <style>
        .slider-selection {
            position: absolute;
            background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
            background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
            background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
            background-repeat: repeat-x;
        }

        .bootstrap-datetimepicker-widget {
            z-index: 100000000;
        !important;

        }
    </style>
    <div class="m-b-md" id="wizardControl">
        <a class="btn btn-primary" href="#step1" data-toggle="tab">Header</a>
        <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_zaqath_contribution()" data-toggle="tab">Zakat Contribution</a>
        <a class="btn btn-default btn-wizard" href="#step3" onclick="fetch_beneficery_details()" data-toggle="tab">Beneficiary</a>
        <a class="btn btn-default btn-wizard" href="#step4" onclick="fetch_project_donors_details()" data-toggle="tab">Donors</a>
        <a class="btn btn-default btn-wizard" href="#step5" onclick="fetch_project_proposal_image()" data-toggle="tab">Images</a>
        <!--<a class="btn btn-default btn-wizard" href="#step6" onclick="fetch_project_proposal_attachments()"
           data-toggle="tab">Attachments</a>-->
    </div>
    <br>
    <div class="tab-content">
        <div id="step1" class="tab-pane active">
            <?php echo form_open('', 'role="form" id="project_proposal_header_form"'); ?>
            <input type="hidden" name="proposalID" id="edit_proposalID">
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>PROJECT PROPOSAL HEADER</h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Create As </label>
                        </div>
                        <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('typepro', array('' => 'Select Type', '1' => 'Project Proposal', '2' => 'Project'), '', 'class="form-control select2" id="typepro"'); ?>
                            <span class="input-req-inner"></span></span>

                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">Proposal Name<label>
                        </div>
                        <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                          <input type="text" name="proposalName"
                                 value="" id="proposalName" class="form-control">
                            <span class="input-req-inner" style="z-index: 100"></span></span>
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">Proposal Title<label>
                        </div>
                        <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                          <input type="text" name="proposalTitle" id="proposalTitle" class="form-control">
                            <span class="input-req-inner" style="z-index: 100"></span></span>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Project Category</label>
                        </div>
                        <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                <?php
                if ($pID == '')
                    echo form_dropdown('projectID', fetch_project_donor_drop(true), '', 'class="form-control select2" id="projectID" required');
                else {
                    echo form_dropdown('projectID', fetch_project_donor_drop(), '', 'class="form-control select2" id="projectID"');
                } ?>
                            <span class="input-req-inner"></span></span>

                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">Document Date</label>

                        </div>
                        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input onchange="" type="text" name="documentDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
                </div>
                <span class="input-req-inner" style="z-index: 100"></span></span>
                        </div>
                        <!--            <div class="form-group col-sm-2">
                <label class="title">Sub Project</label>
            </div>
            <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
                            <?php /*echo form_dropdown('subProjectID', array("" => "Select"), "", 'class="form-control" id="subProjectID"'); */ ?>
                                <span class="input-req-inner"></span></span>
            </div>-->
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Estimated Start Date</label>
                        </div>
                        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                                            <div class="input-group datepic">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                                <input type="text" name="startDate"
                                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                       value="<?php echo $current_date; ?>" id="startDate"
                                                       class="form-control" required>
                                            </div>
                                  <span class="input-req-inner" style="z-index: 10;"></span></span>

                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">Estimated End Date</label>
                        </div>
                        <div class="form-group col-sm-4">
                                     <span class="input-req" title="Required Field">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="endDate"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                   value="<?php echo $current_date; ?>" id="endDate" class="form-control" required>
                        </div>
                                         <span class="input-req-inner" style="z-index: 10;"></span></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Currency</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <span class="input-req" title="Required Field">
               <?php echo form_dropdown('transactionCurrencyID', $currency_arr, '',
                   'class="form-control select2" id="transactionCurrencyID"  required'); ?>
                                <span class="input-req-inner"></span></span>

                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">Project Summary</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <textarea id="projectSummary" name="projectSummary" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Status</label>
                        </div>
                        <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                    <?php echo form_dropdown('status', $array_status, '',
                        'class="form-control select2" id="status" required'); ?>
                            <span class="input-req-inner"></span></span>

                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">Bank</label>
                        </div>
                        <div class="form-group col-sm-4">
                            <?php echo form_dropdown('bankGLAutoID', company_bank_account_drop(), '', 'class="form-control select2" id="bankGLAutoID"'); ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>ADDRESS</h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Country</label>
                        </div>
                        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <?php echo form_dropdown('countryID', $countries_arr, '', 'class="form-control select2 valueHelp disableHelp" onchange="loadcountry_Province(this.value)" id="countryID"'); ?>
                    <span class="input-req-inner"></span></span>
                        </div>
                        <div class="form-group col-sm-2">
                            <label class="title">Province / State</label>
                        </div>
                        <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                <div id="div_load_province">
                    <select name="province" class="form-control select2" id="province">
                        <option value="" selected="selected">Select a Province</option>
                    </select>
                </div>
                    <span class="input-req-inner"></span></span>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-2">
                            <label class="title">Area / District</label>
                        </div>
                        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div id="div_load_district">
                    <select name="district" class="form-control select2" id="district">
                        <option value="" selected="selected">Select a District</option>
                    </select>
                </div>
                    <span class="input-req-inner"></span></span>
                        </div>

                        <div class="form-group col-sm-2">
                            <label class="title">Division</label>
                        </div>
                        <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div id="div_load_division">
                    <select name="division" class="form-control select2" id="division">
                        <option value="" selected="selected">Select a Division</option>
                    </select>
                </div>
                     <span class="input-req-inner"></span></span>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>DETAIL DESCRIPTION </h2>
                    </header>
                    <div class="row">
                        <div class="form-group col-sm-12" style="margin-top: 5px;">
                        <textarea class="form-control customerTypeDescription" rows="5" name="detailDescription"
                                  id="detailDescription"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>PROCESS DESCRIPTION </h2>
                    </header>
                    <div class="row">
                        <div class="form-group col-sm-12" style="margin-top: 5px;">
                        <textarea class="form-control customerTypeDescription" rows="5" name="processDescription"
                                  id="processDescription"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="form-group col-sm-12">
                    <button id="save_btn" class="btn btn-primary pull-right" type="submit">Save</button>
                </div>
            </div>


            </form>
        </div>
        <div id="step2" class="tab-pane">
            <div class="row addTableView">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>PROJECT ZAKAT CONTRIBUTION DETAIL</h2>
                    </header>
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary pull-right"
                                    onclick="zaqath_assign_model()">
                                <i class="fa fa-plus"></i> Assign Zakat Contribution
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-sm-11">
                            <div id="projectProposal_zaqath_body"></div>
                        </div>
                        <div class="col-sm-1">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="step3" class="tab-pane">
            <div class="row addTableView">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>PROJECT PROPOSAL BENEFICIARY DETAIL</h2>
                    </header>
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary pull-right"
                                    onclick="beneficiary_assign_model()">
                                <i class="fa fa-plus"></i> Assign Beneficiary
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-sm-11">
                            <div id="projectProposal_beneficery_body"></div>
                        </div>
                        <div class="col-sm-1">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="step4" class="tab-pane">
            <div class="row addTableView">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>PROJECT PROPOSAL DONORS DETAIL</h2>
                    </header>
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary pull-right"
                                    onclick="donors_assign_model()" id="donorassign">
                                <i class="fa fa-plus"></i> Assign Donors
                            </button>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-sm-11">
                            <div id="projectProposal_donor_body"></div>
                        </div>
                        <div class="col-sm-1">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="step5" class="tab-pane">
            <div class="row addTableView">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>PROJECT PROPOSAL IMAGES</h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-sm-12">
                            <div id="projectProposal_images"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 10px;">
                <div class="col-sm-12">
                    <div class="text-right m-t-xs">
                        <button class="btn btn-primary " onclick="save_draft()" id="saveasdraft">Save as Draft</button>
                        <button class="btn btn-success submitWizard" onclick="confirmation()" id="confirm">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="step6" class="tab-pane">
            <div class="row addTableView">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>PROJECT PROPOSAL Attachments</h2>
                    </header>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-sm-12">
                            <div id="projectProposal_attachment"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Beneficiary" id="ngo_zaqath_contributn_model">
        <div class="modal-dialog modal-lg" style="width:60%">
            <div class="modal-content">
                <form role="form" id="zakatSet_detail_form" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="zakat_close_assign();"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Assign Zakat Contribution </h4>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-2">
                        <label class="title" style="font-weight: bold;"><?php echo $this->lang->line('CommunityNgo_fam_selEconState'); ?></label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div>
                            <select id="EconStateID" class="form-control select2"
                                    name="EconStateID" onchange="get_zaqathData();">
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
                        </div>
                    </div>
                </div>
                <br>
                <div class="modal-body">
                    <div id="div_load_zakatDiv">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal" onclick="zakat_close_assign();">Close</button>
                </div>
                    </form>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Beneficiary" id="mfq_user_groupdetail_model">
        <div class="modal-dialog modal-lg" style="width:60%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Assign Beneficiary </h4>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-2">
                        <label class="title" style="font-weight: bold;">Mahalla</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <div id="div_load_sub_division">
                            <select name="subDivision" class="form-control" id="subDivision">
                                <option value="" selected="selected">Select a Mahalla</option>
                            </select>
                        </div>
                    </div>
                </div>
                <br>
                <div class="modal-body">
                    <div id="sysnc">
                        <div class="table-responsive">
                            <table id="employee_sync" class="table table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 12%">Code</abbr></th>
                                    <th style="min-width: 12%">Beneficiary Name</th>
                                    <th style="min-width: 12%">Economic Status</th>
                                    <th style="min-width: 5%">&nbsp;
                                        <button type="button" data-text="Add" onclick="add_beneficiary()"
                                                class="btn btn-xs btn-primary">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Assign Beneficiary
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" aria-labelledby="Beneficiary" id="ngo_load_donors_model">
        <div class="modal-dialog modal-lg" style="width:60%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Assign Donors </h4>
                </div>
                <div class="modal-body">
                    <div id="sysnc">
                        <div class="table-responsive">
                            <table id="donors_sync" class="table table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">#</th>
                                    <th style="min-width: 12%">Donor Name</th>
                                    <th style="min-width: 5%">&nbsp;
                                        <button type="button" data-text="Add" onclick="add_donors()"
                                                class="btn btn-xs btn-primary">
                                            <i class="fa fa-plus" aria-hidden="true"></i> Assign Donors
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reversing_approval_document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width:90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="docProject_title">Family Details</h4>
                </div>
                <form class="form-horizontal" id="reversing_approval_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-1">
                                    <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                        <li id="TabViewActivation_view" class="active"><a href="#home-v" data-toggle="tab"><?php echo $this->lang->line('common_view');?><!--View--></a></li>
                                        <li id="TabViewActivation_attachment"><a href="#profile-v" data-toggle="tab"><?php echo $this->lang->line('common_attachment');?><!--Attachment--></a></li>
                                    </ul>
                                </div>
                                <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                                    <div class="zx-tab-content">
                                        <div class="zx-tab-pane active" id="home-v">
                                            <div id="load_approved_document" class="col-md-12"></div>
                                        </div>
                                        <div class="zx-tab-pane" id="profile-v">
                                            <div id="loadPageViewAttachment" class="col-md-8">
                                                <div class="table-responsive">
                                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>&nbsp; <strong><?php echo $this->lang->line('common_attachments');?><!--Attachments--></strong>
                                                    <br><br>
                                                    <table class="table table-striped table-condensed table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th><?php echo $this->lang->line('common_file_name');?><!--File Name--></th>
                                                            <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
                                                            <th><?php echo $this->lang->line('common_type');?><!--Type--></th>
                                                            <th><?php echo $this->lang->line('common_action');?><!--Action--></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody id="View_attachment_modal_body" class="no-padding">
                                                        <tr class="danger">
                                                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" role="dialog" id="edit_zakatSet_model" class="modal fade"
         style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Zakat Setup</h4>
                </div>
                <form role="form" id="edit_rv_income_detail_form" class="form-horizontal">
                    <!--hidden feild to capture edit id-->
                    <input type="number" name="edit_proposeId" id="edit_proposeId" value="" style="display: none;">

                    <div class="modal-body">
                        <table class="table table-bordered table-condensed no-color" id="income_edit_table">
                            <thead>
                            <tr>
                                <th>Status</th>
                                <th><?php echo $this->lang->line('communityngo_zakat_ageGrp'); ?></th>
                                <th><?php echo $this->lang->line('communityngo_zakat_ageLimit'); ?></th>
                                <th><?php echo $this->lang->line('communityngo_zakat_points'); ?></th>
                                <th><?php echo $this->lang->line('communityngo_zakat_perAmount'); ?></th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="edit_EconStateID" id="edit_EconStateID" value="" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="edit_AgeGroupID" id="edit_AgeGroupID" value="" readonly>
                                    </div>

                                </td>
                                <td style="text-align: center;">
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="edit_AgeLimit" id="edit_AgeLimit" value="" readonly>
                                    </div>
                                </td>
                                <td style="">
                                    <div class="input-group">
                                        <input class="form-control edit_GrpPoints" type="number" name="edit_GrpPoints" id="edit_GrpPoints" onkeyup="cal_totalEditZaqath(this);" onfocus="this.select();" value="">

                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="input-group">
                                        <input class="form-control edit_ZakAmount" type="number" name="edit_ZakAmount" id="edit_ZakAmount" onkeyup="cal_totalEditZaqath(this);" onfocus="this.select();" value="0">
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="input-group">
                                        <input class="form-control edit_tZAmount" type="number" name="edit_tZAmount" id="edit_tZAmount" value="0" readonly>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="update_zakatSetup_edit()">
                            <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" role="dialog" id="edit_beneficiarySet_model" class="modal fade"
         style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Public Property Beneficiary Setup</h4>
                </div>
                <form role="form" id="edit_zk_beneficiary_form" class="form-horizontal">
                    <!--hidden feild to capture edit id-->
                    <input type="number" name="edit_BeneficiaryID" id="edit_BeneficiaryID" value="" style="display: none;">
                    <input type="number" name="proBenificiaryID" id="proBenificiaryID" value="" style="display: none;">

                    <div class="modal-body">
                        <table class="table table-bordered table-condensed no-color" id="bene_edit_table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Public Property Code</th>
                                <th>Name</th>
                                <th><?php echo $this->lang->line('CommunityNgo_fam_econState'); ?></th>
                                <th><?php echo $this->lang->line('communityngo_zakat_amount'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <div class="input-group">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="edit_beneCode" id="edit_beneCode" value="" readonly>
                                    </div>

                                </td>
                                <td style="text-align: center;">
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="edit_beneName" id="edit_beneName" value="" readonly>
                                    </div>
                                </td>
                                <td style="">
                                    <select id="edit_beneEcState" class="form-control select2"
                                            name="edit_beneEcState" onchange="get_beneEdit_zakat();">
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

                                </td>
                                <td style="text-align: center;">
                                    <div class="input-group">
                                        <input class="form-control" type="number" name="edit_beneTotZakAmnt" id="edit_beneTotZakAmnt" value="" readonly>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">
                            <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                        <button class="btn btn-primary" type="button" onclick="update_beneficiarySet_edit()">
                            <?php echo $this->lang->line('common_update_changes'); ?><!--Update changes-->
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
    <script>
        $('#save_btn').html('Save');
        $('.addTableView').removeClass('hide');
        var subProjectID = '';
        var proposalID = '';
       // var selectedEconSync = [];
        var selectedItemsSync = [];
        var selectedDonorsSync = [];
        var district;
        var province;
        var division;
        var subDivision;
        var oTable;
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/OperationNgo/project_proposal', '', 'Community Project Proposal')
            });
            district = null;
            province = null;
            division = null;
            subDivision = null;
            Inputmask().mask(document.querySelectorAll("input"));
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

            $('.datepic').datetimepicker({
                useCurrent: false,
                format: date_format_policy,
            }).on('dp.change', function (ev) {
            });

            $('.select2').select2();

            p_id = <?php echo json_encode($pID); ?>;
            if (p_id) {
                proposalID = p_id;
                load_project_proposal_header();
                fetch_beneficery_details(proposalID);
                check_project_proposal_details_exist(proposalID);
                //load_confirmation();
            } else {
                $('.btn-wizard').addClass('disabled');
                $('.addTableView').addClass('hide');

            }

            $('#project_proposal_header_form').bootstrapValidator({
                live: 'enabled',
                message: 'This value is not valid.',
                excluded: [':disabled'],
                fields: {

                    typepro: {validators: {notEmpty: {message: 'Create As is required.'}}},
                    documentDate: {validators: {notEmpty: {message: 'Document Date is required.'}}},
                    proposalName: {validators: {notEmpty: {message: 'Proposal Name is required.'}}},
                    proposalTitle: {validators: {notEmpty: {message: 'Proposal Title is required.'}}},
                    projectID: {validators: {notEmpty: {message: 'Project is required.'}}},
                    transactionCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                    startDate: {validators: {notEmpty: {message: 'Estimated Start Date is required.'}}},
                    endDate: {validators: {notEmpty: {message: 'Estimated End Date is required.'}}},
                    status: {validators: {notEmpty: {message: 'Status is required.'}}},

                },
            }).on('success.form.bv', function (e) {
                e.preventDefault();
                $("#projectID").prop("disabled", false);
                tinymce.triggerSave();
                var $form = $(e.target);
                var bv = $form.data('bootstrapValidator');
                var data = $form.serializeArray();
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('CommunityNgo/save_project_proposal_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            proposalID = data[2];
                            $('.addTableView').removeClass('hide');
                            $('#edit_proposalID').val(data[2]);
                            fetch_zaqath_contribution();
                            fetch_beneficery_details();
                            fetch_project_donors_details();
                            fetch_project_proposal_image();
                            check_project_proposal_details_exist(data[2]);
                            $('#save_btn').html('Update');

                            setTimeout(function () {
                                $('#projectID option:not(:selected)').prop('disabled', true);
                                $('#typepro option:not(:selected)').prop('disabled', true);
                                $('#projectID option:not(:selected)').prop('disabled', true);
                                $('#countryID option:not(:selected)').prop('disabled', true);
                                $('#province option:not(:selected)').prop('disabled', true);
                                $('#district option:not(:selected)').prop('disabled', true);
                                $('#division option:not(:selected)').prop('disabled', true);
                            }, 500);
                            var type = $('#typepro').val();
                            if (type == 1) {
                                $('[href=#step2]').tab('show');
                                $(document).scrollTop(0);
                            }

                            $('.btn-wizard').removeClass('disabled');
                            $(document).scrollTop(0);
                            $('.btn-primary').prop('disabled', false);
                        } else {
                            $('.btn-primary').prop('disabled', false);
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });

            $("#projectID").change(function () {
                get_sub_projects($(this).val());
            });

            tinymce.init({
                selector: ".customerTypeDescription",
                height: 200,
                browser_spellcheck: true,
                plugins: [
                    "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern"
                ],
                toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
                toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
                toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft code",

                menubar: false,
                toolbar_items_size: 'small',

                style_formats: [{
                    title: 'Bold text',
                    inline: 'b'
                }, {
                    title: 'Red text',
                    inline: 'span',
                    styles: {
                        color: '#ff0000'
                    }
                }, {
                    title: 'Red header',
                    block: 'h1',
                    styles: {
                        color: '#ff0000'
                    }
                }, {
                    title: 'Example 1',
                    inline: 'span',
                    classes: 'example1'
                }, {
                    title: 'Example 2',
                    inline: 'span',
                    classes: 'example2'
                }, {
                    title: 'Table styles'
                }, {
                    title: 'Table row 1',
                    selector: 'tr',
                    classes: 'tablerow1'
                }],

                templates: [{
                    title: 'Test template 1',
                    content: 'Test 1'
                }, {
                    title: 'Test template 2',
                    content: 'Test 2'
                }]
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


        function load_project_proposal_header() {
            if (proposalID) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'proposalID': proposalID},
                    url: "<?php echo site_url('CommunityNgo/load_project_proposal_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        if (!jQuery.isEmptyObject(data)) {
                            proposalID = data['proposalID'];
                            $('#edit_proposalID').val(data['proposalID']);
                            $("#projectID").val(data['projectID']).change();
                            $("#transactionCurrencyID").val(data['transactionCurrencyID']).change();
                            $("#bankGLAutoID").val(data['bankGLAutoID']).change();
                            $("#status").val(data['status']).change();
                            $('#documentDate').val(data['DocumentDate']);
                            $('#proposalName').val(data['proposalName']);
                            $('#proposalTitle').val(data['proposalTitle']);
                            $('#projectSummary').val(data['projectSummary']);
                            $('#startDate').val(data['startDate']);
                            $('#endDate').val(data['endDate']);
                            $('#countryID').val(data['countryID']).change();
                            $('#typepro').val(data['type']).change();
                            district = data['areaID'];
                            province = data['provinceID'];
                            division = data['divisionID'];

                            $('#typepro option:not(:selected)').prop('disabled', true);
                            $('#projectID option:not(:selected)').prop('disabled', true);

                            if (data['confirmedYN'] == 1 ) {
                                $("#status").prop("disabled", true);
                                $('#documentDate').prop("disabled", true);
                                $('#transactionCurrencyID').prop("disabled", true);
                                $('#proposalName').prop("disabled", true);
                                $('#proposalTitle').prop("disabled", true);
                                $('#projectSummary').prop("disabled", true);
                                $('#startDate').prop("disabled", true);
                                $('#endDate').prop("disabled", true);
                                $('#bankGLAutoID').prop("disabled", true);
                                $('#saveasdraft').prop("disabled", true);
                                $('#confirm').prop("disabled", true);
                                $('#typepro option:not(:selected)').prop('disabled', true);
                                $('#projectID option:not(:selected)').prop('disabled', true);

                            }
                            setTimeout(function () {
                                $('#province option:not(:selected)').prop('disabled', true);
                                $('#countryID option:not(:selected)').prop('disabled', true);
                                $('#district option:not(:selected)').prop('disabled', true);
                                $('#division option:not(:selected)').prop('disabled', true);

                                if (data['confirmedYN'] == 1) {
                                    $('#countryID').prop("disabled", true);
                                    $("#province").prop("disabled", true);
                                    $("#district").prop("disabled", true);
                                    $("#division").prop("disabled", true);
                                    $('#save_btn').prop("disabled", true);
                                    $('#donorassign').prop("disabled", true);
                                    tinyMCE.get("detailDescription").setMode('readonly');
                                    tinyMCE.get("processDescription").setMode('readonly');
                                }

                                tinyMCE.get("detailDescription").setContent(data['detailDescription']);
                                tinyMCE.get("processDescription").setContent(data['processDescription']);
                            }, 1000);

                            $('#save_btn').html('Update');
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

        function fetch_zaqath_contribution() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {proposalID: proposalID},
                url: "<?php echo site_url('CommunityNgo/load_zaqath_contribution'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#projectProposal_zaqath_body').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function fetch_beneficery_details() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {proposalID: proposalID},
                url: "<?php echo site_url('CommunityNgo/load_beneficery_details_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#projectProposal_beneficery_body').html(data);
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function fetch_project_donors_details() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {proposalID: proposalID},
                url: "<?php echo site_url('OperationNgo/load_project_proposal_donor_details_view'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#projectProposal_donor_body').html(data);


                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function check_project_proposal_details_exist(proposalID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'proposalID': proposalID},
                url: "<?php echo site_url('CommunityNgo/check_project_proposal_details_exist'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $("#projectID").prop("disabled", true);
                        $("#subProjectID").prop("disabled", true);
                    } else {
                        $("#projectID").prop("disabled", false);
                        $("#subProjectID").prop("disabled", false);
                    }
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        $("#typepro").change(function () {

            if (this.value == 1) {
                $('.project_proposal_cls').removeClass('hide');
                $('.projectproposaladdress_cls').removeClass('hide');
                $('.projectproposaldetail_cls').removeClass('hide');
                $('.projecrproposal_total').removeClass('hide');
                $('.titles').removeClass('hide');
                $('.projectproposalprocessdes_cls').removeClass('hide');
                $("#parentCategory").html('PROPOSAL HEADER');
                $(".proposalname").html('Proposal Name');
                $(".proposaltitle").html('Proposal Title');
            } else if (this.value == 2) {
                $('.project_proposal_cls').addClass('hide');
                $('.projectproposaladdress_cls').addClass('hide');
                $('.projectproposaldetail_cls').removeClass('hide');
                $('.projecrproposal_total').addClass('hide');
                $('.titles').addClass('hide');
                $('.projectproposalprocessdes_cls').addClass('hide');
                $("#parentCategory").html('PROJECT HEADER');
                $(".proposalname").html('Project Name');
                $(".proposaltitlehideshow").addClass('hide');


                $(".projectdetails").removeClass('hide');
            }
        });

        function save_draft() {
            if (proposalID) {
                swal({
                        title: "Are you sure?",
                        text: "You want to save this document!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Save as Draft"
                    },
                    function () {
                        fetchPage('system/OperationNgo/project_proposal', '', 'Community Project Proposal')
                    });
            }
        }


        function confirmation() {
            if (proposalID) {
                swal({
                        title: "Are you sure?",
                        text: "You want confirm this document!",
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
                            data: {'proposalID': proposalID},
                            url: "<?php echo site_url('CommunityNgo/project_proposal_confirmation'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                myAlert(data[0], data[1]);
                                if (data[0] == 's') {
                                    fetchPage('system/OperationNgo/project_proposal', '', 'Community Project Proposal');
                                }
                            }, error: function () {
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
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
                url: "<?php echo site_url('CommunityNgo/fetch_ngo_sub_projects'); ?>",
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

        function ItemsSelectedSync(item) {
            var value = $(item).val();
            if ($(item).is(':checked')) {
                var inArray = $.inArray(value, selectedItemsSync);
                if (inArray == -1) {
                    selectedItemsSync.push(value);
                }
            }
            else {
                var i = selectedItemsSync.indexOf(value);
                if (i != -1) {
                    selectedItemsSync.splice(i, 1);
                }
            }
        }

        function DonorsSelectedSync(item) {
            var value = $(item).val();
            if ($(item).is(':checked')) {
                var inArray = $.inArray(value, selectedDonorsSync);
                if (inArray == -1) {
                    selectedDonorsSync.push(value);
                }
            }
            else {
                var i = selectedDonorsSync.indexOf(value);
                if (i != -1) {
                    selectedDonorsSync.splice(i, 1);
                }
            }
        }

        function zaqath_assign_model() {
            if (proposalID) {
               // selectedEconSync = [];
                $('#ngo_zaqath_contributn_model').modal('show');
            }
        }

        function zakat_close_assign() {

            $('#div_load_zakatDiv').html('');
            $('#ngo_zaqath_contributn_model').modal('hide');
            $('#zakatSet_detail_form')[0].reset();
            $('.select2').select2('');

            get_zaqathData();
        }

        function beneficiary_assign_model() {
            if (proposalID) {
                selectedItemsSync = [];
                template_userGroupDetail(proposalID);
                $('#mfq_user_groupdetail_model').modal('show');
            }
        }

        function donors_assign_model() {
            if (proposalID) {
                selectedDonorsSync = [];
                fetch_ngo_projectProposal_donors(proposalID);
                $('#ngo_load_donors_model').modal('show');
            }
        }

        function fetch_sub_divisions() {
            if (proposalID) {
                template_userGroupDetail(proposalID)
            }
        }

        function template_userGroupDetail(proposalID) {
            oTable = $('#employee_sync').DataTable({
                "ordering": false,
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": false,
                "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
                "sAjaxSource": "<?php echo site_url('CommunityNgo/fetch_project_proposal_beneficiary'); ?>",
                language: {
                    paginate: {
                        previous: '‹‹',
                        next: '››'
                    }
                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                    $('.item-iCheck').iCheck('uncheck');
                    if (selectedItemsSync.length > 0) {
                        $.each(selectedItemsSync, function (index, value) {
                            $("#selectItem_" + value).iCheck('check');
                        });
                    }
                    $('.extraColumns input').iCheck({
                        checkboxClass: 'icheckbox_square_relative-purple',
                        radioClass: 'iradio_square_relative-purple',
                        increaseArea: '20%'
                    });
                    $('input').on('ifChecked', function (event) {
                        ItemsSelectedSync(this);
                    });
                    $('input').on('ifUnchecked', function (event) {
                        ItemsSelectedSync(this);
                    });
                },

                "aoColumns": [
                    {"mData": "benificiaryID"},
                    {"mData": "systemCode"},
                    {"mData": "name"},
                    {"mData": "econState"},
                    {"mData": "edit"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "projectID", "value": $('#projectID').val()});
                    aoData.push({"name": "proposalID", "value": proposalID});
                    aoData.push({"name": "countryID", "value": $('#countryID').val()});
                    aoData.push({"name": "division", "value": $('#division').val()});
                    aoData.push({"name": "subDivision", "value": $('#subDivision').val()});

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

        function fetch_ngo_projectProposal_donors(proposalID) {
            oTable = $('#donors_sync').DataTable({
                "ordering": false,
                "bProcessing": true,
                "bServerSide": true,
                "bDestroy": true,
                "bStateSave": false,
                "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
                "sAjaxSource": "<?php echo site_url('OperationNgo/fetch_project_proposal_donors'); ?>",
                language: {
                    paginate: {
                        previous: '‹‹',
                        next: '››'
                    }
                },
                "fnDrawCallback": function (oSettings) {
                    $("[rel=tooltip]").tooltip();
                    var tmp_i = oSettings._iDisplayStart;
                    var iLen = oSettings.aiDisplay.length;
                    var x = 0;
                    for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                        x++;
                    }
                    $('.item-iCheck').iCheck('uncheck');
                    if (selectedDonorsSync.length > 0) {
                        $.each(selectedDonorsSync, function (index, value) {
                            $("#selectDonors_" + value).iCheck('check');
                        });
                    }
                    $('.extraColumns input').iCheck({
                        checkboxClass: 'icheckbox_square_relative-purple',
                        radioClass: 'iradio_square_relative-purple',
                        increaseArea: '20%'
                    });
                    $('input').on('ifChecked', function (event) {
                        DonorsSelectedSync(this);
                    });
                    $('input').on('ifUnchecked', function (event) {
                        DonorsSelectedSync(this);
                    });
                },

                "aoColumns": [
                    {"mData": "contactID"},
                    {"mData": "donorName"},
                    {"mData": "edit"}
                ],
                "fnServerData": function (sSource, aoData, fnCallback) {
                    aoData.push({"name": "proposalID", "value": proposalID});
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

        function add_zaqthContribution() {
            var data = $('#zakatSet_detail_form').serializeArray();
            var proposalID = $('#edit_proposalID').val();
            var EconStateID = document.getElementById('EconStateID').value;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/assign_zaqath_for_project_proposal'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    /*receiptVoucherDetailAutoID = null;*/
                    refreshNotifications(true);
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if (data[0] == 's') {
                        fetch_zaqath_contribution(proposalID);
                        check_project_proposal_details_exist(proposalID);

                        $('#div_load_zakatDiv').html('');
                        $("#ngo_zaqath_contributn_model").modal('hide');
                        $('#zakatSet_detail_form')[0].reset();
                        $('.select2').select2('');

                        get_zaqathData();

                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function add_beneficiary() {
            var proposalID = $('#edit_proposalID').val();
            var EconStateIDs = document.getElementById('EconStateIDs').value;

            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("CommunityNgo/assign_beneficiary_for_project_proposal"); ?>',
                dataType: 'json',
                data: {'EconStateIDs':EconStateIDs,'selectedItemsSync': selectedItemsSync, 'proposalID': proposalID},
                async: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetch_beneficery_details(proposalID);
                        check_project_proposal_details_exist(proposalID);
                        $("#mfq_user_groupdetail_model").modal('hide');
                        $('.extraColumns input').iCheck('uncheck');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function add_donors() {
            var proposalID = $('#edit_proposalID').val();
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("OperationNgo/assign_donors_for_project_proposal"); ?>',
                dataType: 'json',
                data: {'selectedDonorsSync': selectedDonorsSync, 'proposalID': proposalID},
                async: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    if (data[0] == 's') {
                        fetch_project_donors_details(proposalID);
                        check_project_proposal_details_exist(proposalID);
                        $("#ngo_load_donors_model").modal('hide');
                        $('.extraColumns input').iCheck('uncheck');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            });
        }

        function fetch_zakatDistributedDel(proposalBeneficiaryID,proposalID,FamMasterID,proposalTitle,name) {

            $("#profile-v").removeClass("active");
            $("#home-v").addClass("active");
            $("#TabViewActivation_attachment").removeClass("active");
            $("#TabViewActivation_view").addClass("active");
           // attachment_View_modal(documentID, para1);
            $('#load_approved_document').html('');
            var title = proposalTitle +' ( '+ name +' Family )';
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {proposalBeneficiaryID: proposalBeneficiaryID,proposalID:proposalID,FamMasterID:FamMasterID},
                url: "<?php echo site_url('CommunityNgo/load_proposal_family_del'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#reversing_approval_form')[0].reset();
                    $('#reversing_approval_form').bootstrapValidator('resetForm', true);

                    $('#load_approved_document').html(data);
                    $('#docProject_title').html(title);
                    $('#reversing_approval_document').modal('show');

                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }

        function edit_beneficiarySetup(proposalBeneficiaryID,beneficiaryID,proposalTitle,name)  {

            document.getElementById('edit_BeneficiaryID').value = proposalBeneficiaryID;
            document.getElementById('proBenificiaryID').value = beneficiaryID;

            var edit_BeneficiaryID = document.getElementById('edit_BeneficiaryID').value;
            if (proposalBeneficiaryID) {
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
                            data: {'proposalBeneficiaryID': edit_BeneficiaryID},
                            url: "<?php echo site_url('CommunityNgo/fetch_beneficiarySet_edit'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {

                                $('#edit_beneCode').val(data['benCode']).change();
                             //   $('#edit_beneCode').val(data['benCode']);
                                $('#edit_beneName').val(data['name']);
                                $('#edit_beneEcState').val(data['EconStateID']).change();
                                $('#edit_beneTotZakAmnt').val(data['totalEstimatedValue']);

                                $("#edit_beneficiarySet_model").modal({backdrop: "static"});
                                stopLoad();
                                //refreshNotifications(true);
                            }, error: function () {
                                stopLoad();
                                swal("Cancelled", "Try Again ", "error");
                            }
                        });
                    });

            }
        }

        function get_beneEdit_zakat() {
            var edit_BeneficiaryID = document.getElementById('edit_BeneficiaryID').value;
            var edit_beneEcState = document.getElementById('edit_beneEcState').value;

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'proposalBeneficiaryID': edit_BeneficiaryID,'edit_beneEcState':edit_beneEcState},
                url: "<?php echo site_url('CommunityNgo/get_beneEdit_zakat'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {

                    $('#edit_beneTotZakAmnt').val(data);
                    stopLoad();
                    //refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }

        function update_beneficiarySet_edit() {
            var data = $('#edit_zk_beneficiary_form').serializeArray();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/update_beneficiary_edit'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    stopLoad();
                    if (data[0] == 's') {
                      //  proposalBeneficiaryID = null;
                        fetch_beneficery_details(proposalID);
                        check_project_proposal_details_exist(proposalID);
                        $('#edit_beneficiarySet_model').modal('hide');
                        $('#edit_zk_beneficiary_form')[0].reset();
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

        function delete_beneficiary(proposalBeneficiaryID) {
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
                        data: {'proposalBeneficiaryID': proposalBeneficiaryID},
                        url: "<?php echo site_url('CommunityNgo/delete_project_proposal_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Deleted Successfully');
                                fetch_beneficery_details(proposalID);
                                check_project_proposal_details_exist(proposalID);
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        }

        function in_active_zaqath(proposalZaqathSetID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "You want to inactive this record !", /*You want to inactive this record !*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_in_active');?>", /*Inactive*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'proposalZaqathSetID': proposalZaqathSetID},
                        url: "<?php echo site_url('CommunityNgo/delete_project_zakat_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Inactive Successfully');
                                fetch_zaqath_contribution(proposalID);
                                check_project_proposal_details_exist(proposalID);
                            } else {
                                myAlert('e', 'Inactive Failed');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        }

        function edit_zakatSetup(proposalZaqathSetID) {

            document.getElementById('edit_proposeId').value = proposalZaqathSetID;
            var edit_proposeId = document.getElementById('edit_proposeId').value;
            if (proposalZaqathSetID) {
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
                            data: {'proposalZaqathSetID': edit_proposeId},
                            url: "<?php echo site_url('CommunityNgo/fetch_zakatSet_edit'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {

                                $('#edit_EconStateID').val(data['EconStateDes']).change();
                                $('#edit_AgeGroupID').val(data['AgeGroup']).change();
                                $('#edit_AgeLimit').val(data['AgeLimit']);
                                $('#edit_GrpPoints').val(data['GrpPoints']);
                                $('#edit_ZakAmount').val(data['ZakatAmount']);
                                $('#edit_tZAmount').val(data['TotalPerZakat']);

                               // $('#edit_zakatSet_model').modal('show');
                                $("#edit_zakatSet_model").modal({backdrop: "static"});
                                stopLoad();
                                //refreshNotifications(true);
                            }, error: function () {
                                stopLoad();
                                swal("Cancelled", "Try Again ", "error");
                            }
                        });
                    });

            }
        }

        function cal_totalEditZaqath(element) {

            var GrpPoints = parseFloat($(element).closest('tr').find('.edit_GrpPoints').val());
            var TotalAmount = parseFloat($(element).closest('tr').find('.edit_ZakAmount').val());

            if (GrpPoints) {
                $(element).closest('tr').find('.edit_tZAmount').val(GrpPoints * TotalAmount)
            }

        }
        
        function update_zakatSetup_edit() {
            var data = $('#edit_rv_income_detail_form').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('CommunityNgo/update_zakatSet_edit'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    stopLoad();
                    if (data[0] == 's') {
                        proposalZaqathSetID = null;
                        fetch_zaqath_contribution(proposalID);
                        check_project_proposal_details_exist(proposalID);
                        $('#edit_zakatSet_model').modal('hide');
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

        function active_zaqath(proposalZaqathSetID) {

            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                    text: "You want to active this record !", /*You want to active this record !*/
                    type: "success",
                    showCancelButton: true,
                    confirmButtonColor: "#5cb85c",
                    confirmButtonText: "<?php echo $this->lang->line('common_active');?>", /*Active*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'proposalZaqathSetID': proposalZaqathSetID},
                        url: "<?php echo site_url('CommunityNgo/active_project_zakat_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data == true) {
                                myAlert('s', 'Active Successfully');
                                fetch_zaqath_contribution(proposalID);
                                check_project_proposal_details_exist(proposalID);
                            } else {
                                myAlert('e', 'Active Failed');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        }

        function delete_donor(proposalDonourID, proposalID, donorID) {
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
                        data: {'proposalDonourID': proposalDonourID, 'proposalID': proposalID, 'donorID': donorID},
                        url: "<?php echo site_url('OperationNgo/delete_project_proposal_donors_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['status'] == 1) {
                                myAlert('e', data['message']);
                            } else if (data['status'] == 0) {
                                myAlert('s', data['message']);
                                fetch_project_donors_details(proposalID);
                                check_project_proposal_details_exist(proposalID);
                            } else {
                                myAlert('e', 'Deletion Failed');
                            }
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });

        }

        function fetch_project_proposal_image() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {proposalID: proposalID},
                url: '<?php echo site_url("CommunityNgo/load_project_image_view"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#projectProposal_images').html(data);
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function fetch_project_proposal_attachments() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {proposalID: proposalID},
                url: '<?php echo site_url("CommunityNgo/load_project_attachment_view"); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#projectProposal_attachment').html(data);
                }, error: function () {
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        }

        function loadcountry_Province(countyID) {
            if (countyID) {
                $('#div_load_division').html('');
                $('#div_load_sub_division').html('');
                $('#div_load_province').html('');
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'html',
                    data: {countyID: countyID},
                    url: "<?php echo site_url('CommunityNgo/fetch_province_based_countryDropdown_project_proposal'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#div_load_province').html(data);
                        $('.select2').select2();
                        $('#province').val(province).change();
                        stopLoad();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        myAlert('e', '<br>Message: ' + errorThrown);
                    }
                });
            }

        }

        function loadcountry_District(masterID) {
            $('#div_load_district').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('CommunityNgo/fetch_province_based_districtDropdown_project_proposal'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_district').html(data);
                    $('.select2').select2();
                    $('#district').val(district).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_Division(masterID) {
            $('#div_load_division').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('CommunityNgo/fetch_division_based_districtDropdown_project_proposal'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_division').html(data);
                    $('.select2').select2();
                    $('#division').val(division).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function loadcountry_sub_Divisions(masterID) {
            $('#div_load_sub_division').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {masterID: masterID},
                url: "<?php echo site_url('CommunityNgo/fetch_sub_division_based_divisionDropdown_project'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_sub_division').html(data);
                    $('.select2').select2();
                    $('#subDivision').val(subDivision).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }
        
        function get_zaqathData() {

            $('#div_load_zakatDiv').html('');
            var proposalID = $('#edit_proposalID').val();
            var EconStateID = document.getElementById('EconStateID').value;
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'proposalID': proposalID,'EconStateID':EconStateID},
                url: "<?php echo site_url('CommunityNgo/fetch_project_proposal_zaqath'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_load_zakatDiv').html(data);
                  //  $('.select2').select2();
                   // $('#subDivision').val(subDivision).change();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
            
        }


    </script>

<?php
