<?php echo head_page($_POST['page_name'], FALSE);
$this->load->helper('operation_ngo_helper');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$array_status = fetch_ngo_status(6);
$currency_arr = all_currency_new_drop();
$contractor_arr = fetch_ngo_contractor();
$countries_arr = load_all_countrys();
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
<div class="m-b-md titles" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">Header</a>
    <a class="btn btn-default btn-wizard" href="#step2" onclick="fetch_beneficery_details()"
       data-toggle="tab">Beneficiary</a>
    <a class="btn btn-default btn-wizard" href="#step3" onclick="fetch_project_donors_details()"
       data-toggle="tab">Donors</a>
    <a class="btn btn-default btn-wizard" href="#step4" onclick="fetch_project_proposal_image()"
       data-toggle="tab">Images</a>
    <a class="btn btn-default btn-wizard" href="#step5" onclick="fetch_project_proposal_attachments()"
       data-toggle="tab">Attachments</a>
</div>


<br>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="project_proposal_header_form"'); ?>
        <input type="hidden" name="proposalID" id="edit_proposalID">
        <div class="row">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2 id="parentCategory">PROJECT / PROPOSAL HEADER </h2>
                    <!-- <h2>PROJECT / PROPOSAL HEADER</h2>-->
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
                        <label class="title proposalname">Proposal Name<label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                          <input type="text" name="proposalName"
                                 value="" id="proposalName" class="form-control">
                            <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                   <div class="proposaltitlehideshow">


                    <div class="form-group col-sm-2">
                        <label class="title proposaltitle">Proposal Title<label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                          <input type="text" name="proposalTitle" id="proposalTitle" class="form-control">
                            <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                </div>  </div>

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
                   'class="form-control select2" id="transactionCurrencyID"'); ?>
                                <span class="input-req-inner"></span></span>

                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Project Summary</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea id="projectSummary" name="projectSummary" class="form-control"></textarea>
                    </div>
                </div>

                <div class="row projectdetails hide" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Project Contractor</label>
                    </div>
                    <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('contractorIDproject', $contractor_arr, '', 'class="form-control select2" id="contractorIDproject"'); ?>
                               <span class="input-req-inner" style="z-index: 100;left: 93%;"></span></span>

                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Total Project Cost</label>
                    </div>
                    <div class="form-group col-sm-4">
                           <span class="input-req" title="Required Field">
                          <input type="text" name="totalprojectcost" id="totalprojectcost" class="form-control">
                            <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                </div>


                <div class ="projecrproposal_total">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Total Number of Houses</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="number" name="totalNumberofHouses" id="totalNumberofHouses" class="form-control">
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Floor Area</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea class="form-control" id="floorArea" name="floorArea" rows="2"></textarea>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Cost of a House</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea class="form-control" id="costofhouse" name="costofhouse" rows="2"></textarea>
                    </div>
                    <div class="form-group col-sm-2">
                        <label class="title">Additional Cost</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <textarea class="form-control" id="additionalCost" name="additionalCost" rows="2"></textarea>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Estimated Completion Time for a House</label>
                    </div>
                    <div class="form-group col-sm-2">
                         <span class="input-req" title="Required Field">
                        <input type="number" name="EstimatedDays" id="EstimatedDays" class="form-control" onchange="complition_time_for_house()" onkeyup="complition_time_for_house()">
                   <span class="input-req-inner" style="z-index: 100;left: 86%;"></span></span>
                    </div>
                    <div class="form-group col-sm-2">
                       <!-- <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="add-contractor"
                                        style="height: 29px; padding: 2px 10px;">
                                    <i class="fa fa-plus" style="font-size: 11px"></i>
                                </button>
                            </span>-->
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('estimationdays',array('' => 'Select', '1' => 'Days', '2' => 'Months','3' => 'Years'), '', 'class="form-control select2" id="estimationdays"'); ?>
                            <span class="input-req-inner" style="z-index: 100;left: 86%;"></span></span>
                    </div>

                    <!--<div class="form-group col-sm-2">
                        <label class="title">Status</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <span class="input-req" title="Required Field">
                    <?php /*echo form_dropdown('status', $array_status, '',
                        'class="form-control select2" id="status" '); */?>
                            <span class="input-req-inner"></span></span>

                    </div>-->
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Contractor</label>
                    </div>
                    <div class="form-group col-sm-4">
                           <!-- <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="add-contractor"
                                        style="height: 29px; padding: 2px 10px;">
                                    <i class="fa fa-plus" style="font-size: 11px"></i>
                                </button>
                            </span>-->
                            <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('contractorID', $contractor_arr, '', 'class="form-control select2" id="contractorID"'); ?>
                             <span class="input-req-inner" style="z-index: 100;left: 93%;"></span></span>

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
        </div>

        <div class="projectproposaladdress_cls">
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
        </div>
        <br>
        <div class="projectproposaldetail_cls">
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
        </div>
        <br>
        <div class="projectproposalprocessdes_cls">
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
                    <h2>PROJECT PROPOSAL BENEFICIARY DETAIL</h2>
                </header>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="beneficiary_assign_model()" id="beneficiaryassign">
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
    <div id="step3" class="tab-pane">
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
    <div id="step4" class="tab-pane">
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
        <div class="row project_proposal_cls" style="margin-top: 10px;">
            <div class="col-sm-12">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary " onclick="save_draft()" id="saveasdraft">Save as Draft</button>
                    <button class="btn btn-success submitWizard" onclick="confirmation()" id="confirm">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div id="step5" class="tab-pane">
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
<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Beneficiary" id="mfq_user_groupdetail_model">
    <div class="modal-dialog modal-lg" style="width:90%">
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
            <form class="form-horizontal" id="add_beneficiary_form" >
            <div class="modal-body">
                <div id="sysnc">
                    <div class="table-responsive">
                        <table id="employee_sync" class="table table-striped table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 12%">Code</abbr></th>
                                <th style="min-width: 12%">Beneficiary Name</th>
                                <th style="min-width: 12%">Total Sqft</th>
                                <th style="min-width: 12%">Total Cost</th>
                                <th style="min-width: 12%">Estimated Value</th>
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
            </form>
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
<div class="modal fade" id="add-contractor-modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">New Contractor</h3>
            </div>
            <div role="form" id="" class="form-horizontal" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Contractor</label>

                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="contractor_name" name="contractor_name">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="save-btn-contractor">Save</button>
                    <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script>
    $('#save_btn').html('Save');
    $('.addTableView').removeClass('hide');
    var subProjectID = '';
    var proposalID = '';
    var selectedItemsSync = [];
    var totalestimatedvalue = [];
    var selectedDonorsSync = [];
    var amount = [];
    var district;
    var province;
    var division;
    var subDivision;
    var oTable;
    $(document).ready(function () {
        $("[rel-tooltip]").tooltip();
        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/project_proposal', '', 'Project Proposal')
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
                startDate: {validators: {notEmpty: {message: 'Estimated Start Date is required.'}}},
                endDate: {validators: {notEmpty: {message: 'Estimated End Date is required.'}}},
                transactionCurrencyID: {validators: {notEmpty: {message: 'Currency is required.'}}},
                projectID: {validators: {notEmpty: {message: 'Project is required.'}}},

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
                url: "<?php echo site_url('OperationNgo/save_project_proposal_header'); ?>",
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

        $('#add-contractor').click(function () {
            $('#contractor_name').val('');
            $('#add-contractor-modal').modal({backdrop: 'static'});
        });

    });


    function load_project_proposal_header() {
        if (proposalID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'proposalID': proposalID},
                url: "<?php echo site_url('OperationNgo/load_project_proposal_header'); ?>",
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
                        $('#totalNumberofHouses').val(data['totalNumberofHouses']);
                        $('#floorArea').val(data['floorArea']);
                        $('#costofhouse').val(data['costofhouse']);
                        $('#additionalCost').val(data['additionalCost']);
                        $('#EstimatedDays').val(data['EstimatedDays']);
                        $('#contractorID').val(data['contractorID']).change();
                        $('#countryID').val(data['countryID']).change();
                        $('#typepro').val(data['type']).change();
                        district = data['areaID'];
                        province = data['provinceID'];
                        division = data['divisionID'];
                        $('#typepro option:not(:selected)').prop('disabled', true);
                        $('#projectID option:not(:selected)').prop('disabled', true);
                        if (data['confirmedYN'] == 1) {
                            $("#status").prop("disabled", true);
                            $('#documentDate').prop("disabled", true);
                            $('#transactionCurrencyID').prop("disabled", true);
                            $('#proposalName').prop("disabled", true);
                            $('#proposalTitle').prop("disabled", true);
                            $('#projectSummary').prop("disabled", true);
                            $('#startDate').prop("disabled", true);
                            $('#endDate').prop("disabled", true);
                            $('#totalNumberofHouses').prop("disabled", true);
                            $('#floorArea').prop("disabled", true);
                            $('#costofhouse').prop("disabled", true);
                            $('#additionalCost').prop("disabled", true);
                            $('#EstimatedDays').prop("disabled", true);
                            $('#contractorID').prop("disabled", true);
                            $('#bankGLAutoID').prop("disabled", true);
                            $('#saveasdraft').prop("disabled", true);
                            $('#confirm').prop("disabled", true);
                            /*$('#typepro').prop("disabled", true);*/
                            $('#typepro option:not(:selected)').prop('disabled', true);
                            $('#projectID option:not(:selected)').prop('disabled', true);
                        }
                        setTimeout(function () {
                            $('#province option:not(:selected)').prop('disabled', true);
                            $('#countryID option:not(:selected)').prop('disabled', true);
                            $('#district option:not(:selected)').prop('disabled', true);
                            $('#division option:not(:selected)').prop('disabled', true);
                          //  $('#subDivision option:not(:selected)').prop('disabled', true);
                            if (data['confirmedYN'] == 1) {
                                $('#save_btn').prop("disabled", true);
                                $('#donorassign').prop("disabled", true);
                                $('#beneficiaryassign').prop("disabled", true);
                                tinyMCE.get("detailDescription").setMode('readonly');
                                tinyMCE.get("processDescription").setMode('readonly');
                            }

                            tinyMCE.get("detailDescription").setContent(data['detailDescription']);
                            tinyMCE.get("processDescription").setContent(data['processDescription']);
                        }, 300);

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

    $('#save-btn-contractor').click(function (e) {
        e.preventDefault();
        var contractorName = $('#contractor_name').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'contractorName': contractorName},
            url: '<?php echo site_url("OperationNgo/save_ngo_contractor"); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                var contractor_drop = $('#contractorID');
                if (data[0] == 's') {
                    contractor_drop.append('<option value="' + data[2] + '">' + contractorName + '</option>');
                    contractor_drop.val(data[2]);
                    $('#add-contractor-modal').modal('hide');

                }
            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });

    function fetch_beneficery_details() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {proposalID: proposalID},
            url: "<?php echo site_url('OperationNgo/load_beneficery_details_view'); ?>",
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
            url: "<?php echo site_url('OperationNgo/check_project_proposal_details_exist'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $("#subProjectID").prop("disabled", true);
                    /*  $("#projectID").prop("disabled", true);*/
                    $('#projectID option:not(:selected)').prop('disabled', true);
                } else {
                    $("#subProjectID").prop("disabled", false);
                    /*$("#projectID").prop("disabled", false);*/
                }
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

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
                    fetchPage('system/operationNgo/project_proposal', '', 'Project Proposal')
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
                        url: "<?php echo site_url('operationNgo/project_proposal_confirmation'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                fetchPage('system/operationNgo/project_proposal', '', 'Project Proposal');
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

    /*function ItemsSelectedSync(item) {
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
    }*/

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
            "sAjaxSource": "<?php echo site_url('OperationNgo/fetch_project_proposal_beneficiary'); ?>",
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

                $('input').on('ifChanged', function(){
                    changeMandatory(this);
                });



            },

            "aoColumns": [
                {"mData": "benificiaryID"},
                {"mData": "systemCode"},
                {"mData": "name"},
                {"mData": "totalsqftadd"},
                {"mData": "totalcostadd"},
                {"mData": "estimatedvalue"},
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

    function add_beneficiary() {
        var proposalID = $('#edit_proposalID').val();
            var data = $('#add_beneficiary_form').serializeArray();
            data.push({'name': 'proposalid', 'value': proposalID});
            data.push({'name': 'selectedItemsSync', 'value': selectedItemsSync});

            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("OperationNgo/assign_beneficiary_for_project_proposal"); ?>',
                dataType: 'json',
                data: data,
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
                    url: "<?php echo site_url('OperationNgo/delete_project_proposal_detail'); ?>",
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
            url: '<?php echo site_url("OperationNgo/load_project_image_view"); ?>',
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
            url: '<?php echo site_url("OperationNgo/load_project_attachment_view"); ?>',
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
                url: "<?php echo site_url('OperationNgo/fetch_province_based_countryDropdown_project_proposal'); ?>",
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
            url: "<?php echo site_url('OperationNgo/fetch_province_based_districtDropdown_project_proposal'); ?>",
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
            url: "<?php echo site_url('OperationNgo/fetch_division_based_districtDropdown_project_proposal'); ?>",
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
            url: "<?php echo site_url('OperationNgo/fetch_sub_division_based_divisionDropdown_project'); ?>",
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
    function complition_time_for_house() {
        var estimateddays = $('#EstimatedDays').val();
        if(estimateddays < 0)
        {
            myAlert('w', "Estimated completion time for a house couldnot be less than zero");
            $('#EstimatedDays').val('');
        }
    }

    $('input').on('ifChanged', function(){
        changeMandatory(this);
    });

    function changeMandatory(obj) {
       var status = ($(obj).is(':checked')) ? 1 : 0;
       var str = $(obj).attr('data-value');
       var value = $(obj).val();
       $(obj).closest('tr').closest('tr').find('.changestatus-' + str).val(status);
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

</script>
