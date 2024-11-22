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
$gl_code = fetch_all_gl_codes();
$pID = $this->input->post('page_id');
$segment_arr = fetch_segment();
?>
<div id="filter-panel" class="collapse filter-panel" xmlns="http://www.w3.org/1999/html"></div>
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

    <a class="btn btn-primary" href="#step1" data-toggle="tab">Project Details</a>
    <a class="btn btn-default btn-wizard" href="#step2" data-toggle="tab" onclick="beneficiary_details()">Beneficiary
        Details</a>
    <a class="btn btn-default btn-wizard" href="#step3" data-toggle="tab" onclick="donor_details()">Donor Details</a>
    <a class="btn btn-default btn-wizard" href="#step4" data-toggle="tab" onclick="project_process()">Project
        Process</a>

</div>
<hr>
<div class="tab-content">
    <div id="step1" class="tab-pane active">
        <?php echo form_open('', 'role="form" id="proposal_convertion_to_project"'); ?>

        <div class="row proposalcovertion">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>
                        CREATE PROJECT </h2>
                </header>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label class="title">Project Proposals</label>
                    </div>

                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                    <?php
                    if ($pID == '') {
                        echo form_dropdown('proposalID', coverted_project_proposal_drop(true), '', 'class="form-control select2" id="proposalID" required');
                    } else {
                        echo form_dropdown('proposalID', coverted_project_proposal_drop(), '', 'class="form-control select2" id="proposalID" required');
                    } ?>
                    <span class="input-req-inner"></span></span>
                    </div>
                    <div class="form-group col-sm-4 hide" id="viewpropodetail">
                        <button class="btn btn-success" type="button" onclick="viewdetails()">
                            View Proposal Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div id="project_proposal_template">
            <div class="col-sm-2">&nbsp;</div>
            <div class="col-sm-8">
                <div class="alert alert-info proposalconversitionheader">
                    <strong>Info !</strong><span style="font-size: 15px;font-weight: 600;"> Please Select a Proposal To Convert To The Project.</span>
                </div>
            </div>
            <div class="col-sm-2">&nbsp;</div>
            <div class="row">
                <div class="form-group col-sm-12">
                    <div class="text-right m-t-xs">
                        <button class="btn btn-primary" type="submit">
                            Convert
                        </button>
                    </div>
                </div>
            </div>
        </div>

        </form>
    </div>

    <div id="step2" class="tab-pane">
        <div id="qualified_beneficiary_view">
        </div>

    </div>

    <div id="step3" class="tab-pane">
        <div id="donor_detail_view">
        </div>
    </div>


    <div id="step4" class="tab-pane">
        <button type="button" onclick="project_steps()" class="btn btn-primary pull-right addprojectbtn"
                id="projectsteps">
            <i class="fa fa-plus"></i>Add Project Steps
        </button>

        <br>
        <div id="project_steps_detail_view">
        </div>


    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="modal_project_claim" data-width="80%"
     role="dialog">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                </button>
                <h5>Project Stages Description</h5>
            </div>
            <br>
                <div class="col-sm-12">
                    <strong>
                        PROJECT : <label id="project_details"> </label>
                    </strong>
            </div>

            <div class="modal-body" id="modal_contact">
                <form method="post" name="frm_prj_claim" id="frm_prj_claim"
                      class="form-horizontal">
                    <div class="modal-body">
                        <input type="hidden" id="stage_id" name="stage_id">
                        <table class="table table-bordered table-condensed no-color" id="project_claim_table">
                            <thead>
                            <tr>
                                <th>Description</th>
                                <th>GL Code</th>
                                <th>Amount</th>
                                <th style="width: 40px;">
                                    <button type="button" class="btn btn-primary addmorebtn btn-xs"
                                            onclick="add_more_income()"><i
                                                class="fa fa-plus"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td style=""><input type="text" name="description[]" placeholder="Description"
                                                    class="form-control"></td>
                                <td><?php echo form_dropdown('glcode[]', $gl_code, '', 'class="form-control glcode select2" required'); ?></td>
                                <td style=""><input type="text" name="amount[]" placeholder="Amount"
                                                    class="form-control"></td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--<div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title">Description</label>
                        </div>
                        <div class="form-group col-sm-6">
                      <span class="input-req" title="Required Field">
                      <input type="text" class="form-control " id="description" name="description" required>
                    <span class="input-req-inner"></span>
                  </span>
                        </div>
                    </div>-->
                    <!--<div class="row" style="margin-top: 10px;">
                            <div class="form-group col-sm-3 col-md-offset-1">
                                <label class="title">GL Code</label>
                            </div>
                            <div class="form-group col-sm-6">
                          <span class="input-req" title="Required Field">
                               <?php /*echo form_dropdown('glcode', $gl_code, '', 'class="form-control select2" id="glcode" required "'); */ ?>
                              <span class="input-req-inner"></span>
                      </span>
                            </div>
                        </div>-->
                    <!-- <div class="row" style="margin-top: 10px;">
                         <div class="form-group col-sm-3 col-md-offset-1">
                             <label class="title">Amount</label>
                         </div>
                         <div class="form-group col-sm-6">
                       <span class="input-req" title="Required Field">
                             <input type="text" class="form-control " id="amount" name="amount" required>
                           <span class="input-req-inner"></span>
                   </span>
                         </div>
                     </div>-->
                </form>
            </div>
            <div class="modal-footer" style="background-color: #ffffff">
                <button type="button" class="btn btn-primary step_savebtn" onclick="crate_a_claim()">
                    Save
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Close
                    </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="modal_project_process_steps" data-width="100%"
     role="dialog">
    <div class="modal-dialog" style="width: 55%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                </button>
                <h5>Project Stages</h5>
            </div>
            <div class="modal-body" id="modal_contact">
                <form method="post" name="frm_project_process" id="frm_project_process"
                      class="form-horizontal">
                    <input type="hidden" name="project_id" id="project_id">
                    <input type="hidden" name="projectvalue" id="projectvalue">
                    <div class="row" style="margin-top: 1%;">
                        <div class="row">
                            <div class="row" id="linkmorerelation">
                                <div class="form-group col-sm-11">
                                    <button type="button" class="btn btn-primary btn-xs pull-right addmorebtn"
                                            onclick="add_more()"><i
                                                class="fa fa-plus"></i></button>
                                </div>
                                <div class="form-group col-sm-1">

                                </div>
                            </div>
                            <div class="row">
                                <div id="append_related_data">
                                    <div class="append_data">
                                        <div class="row">
                                            <div class="form-group col-sm-2" style="margin-top: 10px;">
                                                <label class="title"></label>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label><strong>Project Stage As</strong></label>
                                                <?php echo form_dropdown('projectstages[]', project_stages_drop(), '', 'class="form-control projectstages" id="projectstages" onchange="project_steps_des(this,this.value)"'); ?>
                                            </div>
                                            <div class="form-group col-sm-3" style="margin-left:0% ">
                                                <label><strong>Project Stage Description</strong> </label>
                                                <input type="text" class="form-control stagedescription"
                                                       name="stagedescription[]"
                                                       placeholder="Project Stage Description">
                                            </div>
                                            <div class="form-group col-sm-3 ">
                                                <label style="margin-left: 6%"><strong>Percentage</strong></label>
                                                <input type="text" class="form-control percentage" name="percentage[]"
                                                       placeholder="percentage"
                                                       onkeyup="caltotalamt(this,this.value)"
                                                       style="margin-left: 6%">
                                            </div>
                                            <div class="form-group col-sm-3 ">
                                                <label style="margin-left: 62%"><strong>Amount</strong></label>
                                                <input type="text" class="form-control Amount" name="Amount[]"
                                                       placeholder="Amount"
                                                       onkeyup="cal_per(this,this.value)"
                                                       style="margin-left: 62%">
                                            </div>
                                            <div class="form-group col-sm-2 remove-td" style="margin-top: 35px;">
                                            </div>
                                        </div>
                                        <hr width="90%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer" style="background-color: #ffffff">
                <button type="button" class="btn btn-primary step_savebtn" onclick="save_ngo_project_stages()">
                    Save
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Close
                    </button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" aria-labelledby="proposal"
     id="proposal_detail_model">
    <div class="modal-dialog modal-lg" style="width:50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Proposal Details </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Proposal System Code :</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <label id="proposalsyscode"> <strong> - <strong> </label>
                        <!--   <input type="text" name="proposalsyscode" id="proposalsyscode" class="form-control" readonly>-->
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Proposal Name :</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <label id="proposalname"> <strong> - <strong> </label>
                        <!--<input type="text" name="proposalname" id="proposalname" class="form-control" readonly>-->
                    </div>
                </div>


                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Proposal Title :</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <label id="proposaltitle"> <strong> - <strong> </label>
                        <!-- <input type="text" name="proposaltitle" id="proposaltitleid" class="form-control" readonly>-->
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">No of Beneficiaries :</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <label id="benficiariestot"> <strong> - <strong> </label>
                        <!--  <input type="text" name="benficiariestot" id="benficiariestot" class="form-control" readonly>-->
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Total Estimated Cost :</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <label id="totalproposalestimatedvalue"><strong> - <strong></label>
                        <!--<input type="text" name="estimatedcost" id="estimatedcost" class="form-control" readonly>-->
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade pddLess" data-backdrop="static" id="modal_update_project_stages" data-width="30%"
     role="dialog">
    <div class="modal-dialog" style="width: 55%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                </button>
                <h5>Update Project Stages( <label id="projectdescriptionstatus"> </label>)</h5>
            </div>
            <?php echo form_open('', 'role="form" id="frm_up_project_stages"'); ?>
            <input type="hidden" name="projectidstages" id="projectidstages">
            <input type="hidden" name="project_stage_id" id="project_stage_id">
            <div class="modal-body">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label style="padding-left: 30%">Project Stage As</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                     <input type="text" name="projectstagesupdate" id="projectstagesupdate"

                            class="form-control" disabled>

                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label style="padding-left: 30%">Description</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                     <input type="text" name="stagedescriptionupdate" id="stagedescriptionupdate"
                            class="form-control">
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label style="padding-left: 30%">Percentage</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                     <input type="text" name="percentageupdate" id="percentageupdate"
                            class="form-control" onkeyup="cal_amount_as_percentage(this.value)">
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label style="padding-left: 30%">Amount</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                     <input type="text" name="amountupdate" id="amountupdate"
                            class="form-control" onkeyup="cal_percentage_as_amt(this.value)">
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary savebrnstages" onclick="update_stage()"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> Update
                    </button>
                </div>
                </form>

            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" tabindex="-1" id="modal_project_stages_descrpion" data-backdrop="static"
     data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 70%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Project Stages Details</h4>
            </div>
            <form method="post" name="frm_up_project_stages_details" id="frm_up_project_stages_details"
                  class="form-horizontal">
                <div class="modal-body">
                    <!--/<input type="hidden" id="stage_id" name="stage_id">-->
                    <input type="hidden" id="projectStageDetailID" name="projectStageDetailID">
                    <input type="hidden" id="projectstageid" name="projectstageid">
                    <table class="table table-bordered table-condensed" id=" ">
                        <thead>
                        <tr>
                            <th>Description</th>
                            <th>GL Code</th>
                            <th>Amount</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary addmorebtn btn-xs"
                                        onclick="add_more_income()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style=""><input type="text" name="descriptionedit" placeholder="Description"
                                                class="form-control" id="description"></td>
                            <td><?php echo form_dropdown('glcodeedit', $gl_code, '', 'class="form-control select2 glcode" id="glcode"'); ?></td>
                            <td style=""><input type="text" name="amountedit" placeholder="Amount"
                                                class="form-control amount" id="amount"></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default " type="button">Close</button>
                <button class="btn btn-primary savebrnstages" type="button" onclick="project_stage_det()">Update Changes
                </button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="claim_select_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Claims</h4>
            </div>
            <br>
            <div class="col-sm-12">
                <strong>
                    PROJECT : <label id="project_details_claim"> </label>
                </strong>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped table-condesed ">
                            <thead>

                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 15%">Stage Name</th>
                                <th style="width: 15%">GL Code</th>
                                <th style="width: 15%">Claimed Amount</th>
                                <th style="width: 5%">Is Claimed</th>
                                <th style="width: 1%"></th>
                            </tr>
                            </thead>
                            <tbody id="stage_description_table_body">
                            <tr class="danger">
                                <td colspan="6" class="text-center"><b>No Records Founnd. </b></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary savebrnstages" onclick="docdate_narration()">Generate
                    Invoice
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade pddLess" data-backdrop="static" id="modal_project_stage_doc_narration" data-width="30%"
     role="dialog">
    <div class="modal-dialog" style="width: 55%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                </button>
                <h5>Claim</h5>
            </div>
            <?php echo form_open('', 'role="form" id="frm_project_stage_doc_narration"'); ?>
            <input type="hidden" id="projectid_stage" name="projectid_stage">
            <div class="modal-body">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label style="padding-left: 30%">Document Date</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="documentDate"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                           value="<?php echo $current_date; ?>" id="documentDate" class="form-control" required>
                </div>
                <span class="input-req-inner" style="z-index: 100"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label style="padding-left: 30%">Narration</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                     <input type="text" name="narration" id="narration"
                            class="form-control">
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label style="padding-left: 30%">Segment</label>
                    </div>
                    <div class="form-group col-sm-4">
                <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('segment', $segment_arr, '', 'class="form-control select2" id="segment" required'); ?>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary savebrnstages"
                            onclick="save_doc_date_narration()"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="view_invoice_details" tabindex="2" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 80%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Invoices Claimed<span class="myModalLabel"></span>
                </h4>
            </div>
            <div class="col-sm-12">
                <strong>
                    Project : <label id="project_details_view_c"> </label>
                </strong>
            </div>
            <div class="modal-body">
                <div id="invoice_claim_total_body">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="supplier_pament_voucher" tabindex="2" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 80%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Supplier Invoice<span class="myModalLabel"></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="confirm_body_supplier"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="load_payment_voucher" tabindex="2" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 80%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Payment Voucher Details<span class="myModalLabel"></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="conform_body_payment_voucher"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="load_payment_voucher_invoices" tabindex="2" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 80%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Payment Voucher<span class="myModalLabel"></span>
                </h4>
            </div>
            <div class="modal-body">
                <div id="conform_body_payment_voucher_invoices"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script type="text/javascript">
    var search_id = 1;

    $(document).ready(function () {
        proposalid = null;
        projectid = null;
        invoiceAutoID = null;
        proposaltype = null;


        $('.btn-wizard').addClass('disabled');
        $('.select2').select2();

        $('.headerclose').click(function () {
            fetchPage('system/operationNgo/projectproposal_project', '', 'Project');
        });
        $('.select2').select2();
        p_id = <?php echo json_encode($pID); ?>;
        if (p_id) {
            proposalID = p_id;
            load_project_header();
        }
        else {
            $('.btn-wizard').addClass('disabled');
            $('.addTableView').addClass('hide');
        }

        $('#proposalID').change(function () {
            if ($(this).val() == '') {
                $('#viewpropodetail').addClass('hide');
            } else {
                $('#viewpropodetail').removeClass('hide');
            }
        });

        search_id = 1;

    });
    $('#proposal_convertion_to_project').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.', /*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            proposalID: {validators: {notEmpty: {message: 'Proposal is Required.'}}},
        },

    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        swal({
                title: "Are You Sure",
                text: "You want to convert this proposal",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('OperationNgo/proposal_cconvertion_to_project'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2], data[3]);
                        if (data[0] == 's') {
                            proposalid = data[2];
                            projectid = data[3];
                            $('.btn-wizard').removeClass('disabled');
                            $('#proposalID').prop('disabled', true);
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('.btn-wizard').removeClass('disabled');
                            $('.btn-primary').prop('disabled', true);
                            $('.addprojectbtn').prop('disabled', false);
                            $('.step_savebtn').prop('disabled', false);
                            $('.addmorebtn').prop('disabled', false);
                            $('.savebrnstages').prop('disabled', false);
                            ProposalDetails(proposalid);
                            beneficiary_details();
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

    });

    function add_claim_project_stage(projectStageID,projectid) {
        load_project_details(projectid);
        $('#frm_prj_claim')[0].reset();
        $('#project_id').val(projectid);
        $('#project_claim_table tbody tr').not(':first').remove();
        $("#modal_project_claim").modal({backdrop: "static"});
        $('#modal_project_claim').modal('show');
        $('#stage_id').val(projectStageID);
        $('.select2').select2();
    }


    function load_claim_project_staus(projectStageDetailID, projectStageID) {
        load_claim_project_details(projectStageDetailID);
        $('#projectStageDetailID').val(projectStageDetailID);
        $('#projectstageid').val(projectStageID);
        // $('#modal_project_stages_descrpion').modal('show');
    }

    function project_steps() {

        load_project_step_details(projectid);
        $('#frm_project_process')[0].reset();
        $('#project_id').val(projectid);
        $('.select2').select2();
        $('#modal_project_process_steps').modal('show');
    }

    function ProposalDetails(proposalid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'proposalid': proposalid},
            url: "<?php echo site_url('OperationNgo/fetch_project_proposal_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#project_proposal_template').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function beneficiary_details(proposalid) {
        var proposalid = $('#proposalID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'proposalID': proposalid,'proposaltype':proposaltype},
            url: "<?php echo site_url('OperationNgo/fetch_beneficiarydetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#qualified_beneficiary_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function donor_details(proposalid) {
        var proposalid = $('#proposalID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'proposalid': proposalid,'proposaltype':proposaltype},
            url: "<?php echo site_url('OperationNgo/fetch_donordetails'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#donor_detail_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function viewdetails() {
        var proposalid = $('#proposalID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'proposalid': proposalid},
            url: "<?php echo site_url('OperationNgo/fetch_proposal_details_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#proposalsyscode').html(data['documentSystemCode']);
                $('#proposalname').html(data['proposalName']);
                $('#proposaltitle').html(data['proposalTitle']);
                $('#benficiariestot').html(data['beneficiarycount']);
                $('#totalproposalestimatedvalue').html(data['totalEstimatedValue']);
                $('#proposal_detail_model').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function load_project_header() {
        if (proposalID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'proposalID': proposalID},
                url: "<?php echo site_url('OperationNgo/load_project_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    proposaltype = data['type'];

                    if(proposaltype == 1)
                    {
                        if (!jQuery.isEmptyObject(data)) {
                            $('#proposalID').val(data['proposalID']).change();
                            $('.btn-wizard').removeClass('disabled');
                            $('#proposalID').prop('disabled', true);
                            $('a[data-toggle="tab"]').removeClass('btn-primary');
                            $('a[data-toggle="tab"]').addClass('btn-default');
                            $('.btn-wizard').removeClass('disabled');
                            $('.btn-primary').prop('disabled', true);
                            $('.addprojectbtn').prop('disabled', false);
                            $('.step_savebtn').prop('disabled', false);
                            $('.addmorebtn').prop('disabled', false);
                            $('.savebrnstages').prop('disabled', false);
                            ProposalDetails(proposalID);
                            beneficiary_details(proposalID);
                            donor_details(proposalID);
                            projectid = data['projectSubID'];
                            load_project_step_details(projectid);

                    }
                    }else
                    {
                        $('#proposalID').val(proposalID);
                        ProposalDetails(proposalID);
                        beneficiary_details(proposalID);
                        donor_details(proposalID);
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('.btn-wizard').removeClass('disabled');
                        $('.btn-primary').prop('disabled', true);
                        $('.addprojectbtn').prop('disabled', false);
                        $('.step_savebtn').prop('disabled', false);
                        $('.addmorebtn').prop('disabled', false);
                        $('.savebrnstages').prop('disabled', false);
                        projectid = data['projectSubID'];
                        load_project_step_details(projectid);
                        $('.proposalcovertion').addClass('hide');
                        $('.proposalconversitionheader').addClass('hide');
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

    function load_project_step_details(projectid) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {projectid: projectid},
            url: "<?php echo site_url('OperationNgo/project_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#projectvalue').val(data['totalProjectValue']);
                    $('#reamt').val(data['totalProjectValue']);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function caltotalamt(th, percentage) {
        if (percentage < 0 || percentage == '') {
            $(th).parent().parent().find('.Amount').val('');
            $(th).parent().parent().find('.percentage').val('');
        }
        if (percentage > 100) {
            swal("Cancelled", "Percentage should between 0 - 100", "error");
            $(th).parent().parent().find('.Amount').val('');
            $(th).parent().parent().find('.percentage').val('');
        } else {
            var totalprojectcost = $('#projectvalue').val();
            if (percentage) {
                var totalamt = (totalprojectcost * percentage) / 100;
                var amountremaing = (totalprojectcost - totalamt)
                $(th).parent().parent().find('.Amount').val(totalamt);
            }
        }

    }

    function cal_per(th, amount) {

        var totalprojectcost = $('#projectvalue').val();
        var percentage = $(th).parent().parent().find('.percentage').val();

        if (amount < 0 || amount == '') {
            $(th).parent().parent().find('.percentage').val('');
        }

        if (amount) {

            var percentage = ((parseFloat(amount) / totalprojectcost) * 100).toFixed(2);
            $(th).parent().parent().find('.percentage').val(percentage);
        }

    }

    function add_more() {
        var appendData = $('.append_data:first').clone();
        appendData.find('input').val('');
        appendData.find('input').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71); margin-left: 105%"></span>');
        $('#append_related_data').append(appendData);
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('.append_data').remove();
    });

    function project_steps_des(th, defaultStageID) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {defaultStageID: defaultStageID},
            url: "<?php echo site_url('OperationNgo/fetch_project_stages'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $(th).parent().parent().find('.percentage').val(data['percentage']);
                    // $(th).parent().parent().find('.stagedescription').val(data['description']);

                    setTimeout(function () {
                        $(th).parent().parent().find('.percentage').keyup();
                    }, 100)

                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function project_steps_update(defaultStageID) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {defaultStageID: defaultStageID},
            url: "<?php echo site_url('OperationNgo/fetch_project_stages'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    // $('#percentageupdate').val(data['percentage']);
                    setTimeout(function () {
                        $('#percentageupdate').keyup();
                    }, 100)
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function save_ngo_project_stages() {
        var data = $('#frm_project_process').serialize()
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/save_project_stages'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.uom_disabled').prop('disabled', true);
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    project_process();

                    $('#modal_project_process_steps').modal('hide');
                    setTimeout(function () {

                    }, 300);
                }
            }, error: function () {
                $('.uom_disabled').prop('disabled', true);
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function project_process() {
        var proposalid = $('#proposalID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'projectid': projectid, 'proposalid': proposalid},
            url: "<?php echo site_url('OperationNgo/project_steps'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#project_steps_detail_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function update_stage_details(projectStageID,projectid) {
        load_project_details(projectid);
        load_project_stage_details(projectStageID);
        $('#frm_up_project_stages')[0].reset();
        $('#project_stage_id').val(projectStageID);
        $('#projectidstages').val(projectid);
        $('#modal_update_project_stages').modal('show');
        $('.select2').select2();
    }

    function save_doc_date_narration() {

        var data = $('#frm_project_stage_doc_narration').serializeArray();
        data.push({'name': 'projectid', 'value': projectid});
        $.ajax({
            url: "<?php echo site_url('OperationNgo/save_project_claim_docdate_narration'); ?>",
            type: 'post',
            data: data,
            dataType: 'json',
            cache: false,

            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                invoiceAutoID = (data[2]);
                if (data[0] == 's') {
                    save_claim(invoiceAutoID)
                    confirmation(invoiceAutoID)
                    project_process();


                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });


    }

    function delete_project_step(projectStageID) {
        swal({
                title: "Are You Sure",
                text: "You want to delete this Stage",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "cancel"
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('OperationNgo/delete_stages_project'); ?>",
                    type: 'post',
                    data: {'projectStageID': projectStageID},
                    dataType: 'json',
                    cache: false,

                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            project_process();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });

    }

    function load_project_stage_details(projectStageID) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {projectStageID: projectStageID},
            url: "<?php echo site_url('OperationNgo/project_stage_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    $('#projectstagesupdate').val(data['projectstages'])
                    $('#stagedescriptionupdate').val(data['description'])
                    $('#percentageupdate').val(data['percentage'])
                    $('#amountupdate').val(data['stageAmount'])
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function cal_amount_as_percentage(percentage) {


        var totalprojectcost = $('#projectvalue').val();
        if (percentage < 0 || percentage == '') {
            $('#percentageupdate').val('');
            $('#amountupdate').val('')
        }
        if (percentage > 100) {
            swal("Cancelled", "Percentage should between 0 - 100", "error");
            $('#percentageupdate').val('');
            $('#amountupdate').val('')
        } else {

            if (percentage) {
                var totalamt = (totalprojectcost * percentage) / 100;
                var amountremaing = (totalprojectcost - totalamt);
                $('#amountupdate').val(totalamt);
            }
        }
    }

    function cal_percentage_as_amt(amount) {

        var totalprojectcost = $('#projectvalue').val();

        if (amount < 0 || amount == '') {
            $('#percentageupdate').val('');
            $('#amountupdate').val('')
        }

        if (amount) {
            var percentage = ((parseFloat(amount) / totalprojectcost) * 100).toFixed(2);
            $('#percentageupdate').val(percentage);
        }
    }

    function update_stage() {
        var data = $('#frm_up_project_stages').serialize()
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/project_stage_update'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    project_process();
                    $('#modal_update_project_stages').modal('hide');

                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });
    }

    function crate_a_claim() {
        var data = $('#frm_prj_claim').serialize();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/save_project_claims'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);

                if (data[0] == 's') {
                    project_process();
                    $('#modal_project_claim').modal('hide');

                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                stopLoad();
                myAlert('e', xhr.responseText);
            }
        });

    }

    function load_claim_project_details(projectStageDetailID) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {projectStageDetailID: projectStageDetailID},
            url: "<?php echo site_url('OperationNgo/fetch_project_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {

                    $('#description').val(data['description'])
                    setTimeout(function () {
                        $('#glcode').val(data['glcode']).change();
                    }, 100)
                    $('#amount').val(data['amount']);
                    $('#modal_project_stages_descrpion').modal('show');
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_claim_project(projectStageDetailID) {
        swal({
                title: "Are You Sure",
                text: "You want to delete this record",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete",
                cancelButtonText: "cancel"
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('OperationNgo/delete_project_stage_steps'); ?>",
                    type: 'post',
                    data: {'projectStageDetailID': projectStageDetailID},
                    dataType: 'json',
                    cache: false,

                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            project_process();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }

    function add_more_income() {

        $('select.select2').select2('destroy');
        var appendData = $('#project_claim_table tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#project_claim_table').append(appendData);
        var lenght = $('#project_claim_table tbody tr').length - 1;
        $(".select2").select2();
        number_validation();

        Inputmask().mask(document.querySelectorAll("input"));
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function project_stage_det() {
        var data = $("#frm_up_project_stages_details").serialize();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('OperationNgo/update_project_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $("#modal_project_stages_descrpion").modal('hide');
                    project_process();
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function add_a_claim(projectStageID,projectid) {
         load_project_details(projectid);
        fetch_project_description_details(projectStageID);
        $('#claim_select_modal').modal('show');
    }

    function fetch_project_description_details(projectStageID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'projectStageID': projectStageID},
            url: "<?php echo site_url('OperationNgo/fetch_project_description'); ?>",
            success: function (data) {
                $('#stage_description_table_body').empty();
                if (!jQuery.isEmptyObject(data)) {
                    var x = 1;
                    $.each(data, function (val, text) {
                        $('#stage_description_table_body').append('<tr><td>' + x + '</td><td>' + text['description'] + '</td><td><input type="text" value="' + text['glcode'] + '" class="form-control glcode" id="glcode_' + text['projectStageDetailID'] + '" readonly></td><td class="text-center"><input type="text" value="' + text['amount'] + '" class="number amt" size="15" id="amt_' + text['projectStageDetailID'] + '" readonly></td><td class="text-center"><input class="checkbox" id="check_' + text['projectStageDetailID'] + '" type="checkbox" value="' + text['projectStageDetailID'] + '"></td><td class="text-right" style="display: none;"></td><td><input type="hidden" value="' + text['glid'] + '" id="glid_' + text['projectStageDetailID'] + '"></td></tr>');
                        x++;
                    });
                } else {
                    $('#stage_description_table_body').append('<tr class="danger"><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>');
                }

            }, error: function () {

            }
        });
    }

    function docdate_narration() {
        $('#frm_project_stage_doc_narration')[0].reset();
        var selected = [];
        var amt = [];
        $('#stage_description_table_body input:checked').each(function () {
            selected.push($(this).val());
            amt.push($('#amt_' + $(this).val()).val());
        });
        if (jQuery.isEmptyObject(selected)) {
            myAlert('w', 'Please select a stage you need to claimed', 100);
        } else {
            swal({
                    title: "Are you sure",
                    text: "You want to Generate this invoice",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No"
                },
                function () {
                    $('#modal_project_stage_doc_narration').modal('show');
                });
        }
    }

    function save_claim(invoiceAutoID) {
        if (invoiceAutoID) {
            var selected = [];
            var amt = [];
            var glcode = [];
            var glid = [];
            $('#stage_description_table_body input:checked').each(function () {
                selected.push($(this).val());
                amt.push($('#amt_' + $(this).val()).val());
                glcode.push($('#glcode_' + $(this).val()).val());
                glid.push($('#glid_' + $(this).val()).val());
            });

            if (!jQuery.isEmptyObject(selected)) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'projectStageDetailID': selected,
                        'amt': amt,
                        'invoiceAutoID': invoiceAutoID,
                        'glcode': glcode,
                        'glid': glid,
                        'projectid': projectid
                    },
                    url: "<?php echo site_url('OperationNgo/save_project_step_details'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#claim_select_modal').modal('hide');
                        $('#modal_project_stage_doc_narration').modal('hide');
                        $('#segment').val(null).trigger("change");
                        refreshNotifications(true);
                        project_process();
                    }, error: function () {
                        stopLoad();
                        myAlert('e', 'An error has occurred!, Please contact your system support team.')
                    }
                });
            }
        }


    }

    function confirmation(invoiceAutoID) {
        if (invoiceAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'InvoiceAutoID': invoiceAutoID},
                url: "<?php echo site_url('OperationNgo/supplier_invoice_confirmation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data) {
                        project_process();
                    }


                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });

        }
    }

    function load_invoices_claimed(projectStageID,projectid) {
        if (projectStageID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'projectStageID': projectStageID},
                url: "<?php echo site_url('OperationNgo/load_invoice_claimed'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    load_project_details(projectid);
                    $('#invoice_claim_total_body').html(data);
                    $('#view_invoice_details').modal("show");
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_calime_invoices(InvoiceAutoID) {
        if (InvoiceAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'InvoiceAutoID': InvoiceAutoID, 'html': true, 'approval': 0},
                url: "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#confirm_body_supplier').html(data);
                    $('#supplier_pament_voucher').modal("show");
                    $("#a_link").attr("href", "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>/" + InvoiceAutoID);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_supplier_invoices'); ?>/" + InvoiceAutoID + '/BSI');
                    attachment_modal_supplierInvoice(InvoiceAutoID, "Supplier Invoice", "BSI");
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function attachment_modal_supplierInvoice(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': 0},
                success: function (data) {
                    $('#supplierInvoice_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "<?php echo $this->lang->line('common_attachments');?>");
                    <!--Attachments-->
                    $('#supplierInvoice_attachment').empty();
                    $('#supplierInvoice_attachment').append('' + data + '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function load__paymentvoucher_drildown(claimedInvoiceAutoID) {
        if (claimedInvoiceAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'claimedInvoiceAutoID': claimedInvoiceAutoID},
                url: "<?php echo site_url('OperationNgo/load_payment_voucher_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body_payment_voucher').html(data);
                    $('#load_payment_voucher').modal("show");
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('An Error Occurred! Please Try Again.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function load_paymentvoucher_invoices(payVoucherAutoId) {
        if (payVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'payVoucherAutoId': payVoucherAutoId, 'html': true, 'approval': 0},
                url: "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#conform_body_payment_voucher_invoices').html(data);
                    $('#load_payment_voucher_invoices').modal("show");
                    $("#a_link").attr("href", "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + payVoucherAutoId);
                    $("#de_link").attr("href", "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + payVoucherAutoId + '/PV');
                    attachment_modal_paymentVoucher(payVoucherAutoId, "<?php echo $this->lang->line('accounts_payable_tr_payment_voucher');?> ", "PV");
                    /*Payment Voucher*/
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function attachment_modal_paymentVoucher(documentSystemCode, document_name, documentID) {
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': 0},
                success: function (data) {
                    $('#paymentVoucher_attachment_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "<?php echo $this->lang->line('common_attachment');?>");
                    <!--Attachments-->
                    $('#paymentVoucher_attachment').empty();
                    $('#paymentVoucher_attachment').append('' + data + '');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }
    function load_project_details(projectid) {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'projectid': projectid},
            url: "<?php echo site_url('OperationNgo/project_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
               if(!jQuery.isEmptyObject(data))
               {
                   $('#project_details').html(data['description'])
                   $('#project_details_claim').html(data['description'])
                   $('#projectdescriptionstatus').html(data['description'])
                   $('#project_details_view_c').html(data['description'])
               }


            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }


</script>