<?php
$primaryLanguage = getPrimaryLanguage();
$this->load->helper('boq_helper');
$this->lang->load('project_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('promana_common_project');
echo head_page($title, false);
/*echo head_page('Project', FALSE);*/
$current_date = format_date(date('Y-m-d'));
$customer_arr = all_customer_drop();
$currencyCoversion_arr = all_currency_drop(TRUE, 'ID');
$currency_arr = all_currency_new_drop();
$service_line_arr = fetch_segment(TRUE);
$category_arr = get_category();
$unit_array = load_unit_drop();
$companyname = current_companyName();
$project = get_all_boq_project();
$subcategory_arr = array('' => 'Select  Sub Category');
$date_format_policy = date_format_policy();
$isApprovalExist = fetch_boq_approvals();
?>
<!--<link rel="stylesheet" type="text/css" href="<?php /*echo base_url('plugins/bootstrap/css/tabs.css'); */ ?>">-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<link href=" <?php echo base_url('plugins/jsGantt/jsgantt.css'); ?>" rel="stylesheet" type="text/css"/>
<script src=" <?php echo base_url('plugins/jsGantt/jsgantt.js'); ?>" type="text/javascript"></script>
<style>
    .custometbl .form-control {

        height: 20px;
        vertical-align: middle;
        padding: 0px;
    }

    .custometbl > thead > tr > th,
    .custometbl > tbody > tr > th,
    .custometbl > tfoot > tr > th,
    .custometbl > thead > tr > td,
    .custometbl > tbody > tr > td,
    .custometbl > tfoot > tr > td {
        padding: 0px;
        line-height: 1;
        padding: 5px;

    }

    .gtaskname div,
    .gtaskname {

        font-size: 10px;
        margin: 5px;

    }

    .gtaskcelldiv {
        font-size: 10px;
        margin: 5px;
    }

    td.gmajorheading div {
        margin: 5px;
        font-size: 10px;
    }

    .gresource,
    .gduration,
    .gpccomplete,
    .gstartdate div,
    .gstartdate {

        font-size: 10px;
    }

    .genddate div,
    .genddate {

        font-size: 10px;
    }

    .gpccomplete div {
        font-size: 10px;
    }

    .gduration div {
        font-size: 10px;
    }

    .gresource div {
        font-size: 10px;
    }

    .title {
        float: left;
        width: 170px;
        text-align: left;
        font-size: 13px;
        color: #7b7676;
        padding: 4px 10px 0 0;
    }

    .slider-selection {
        position: absolute;
        background-image: -webkit-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: -o-linear-gradient(top, #6090f5 0, #6090f5 100%);
        background-image: linear-gradient(to bottom, #6090f5 0, #6090f5 100%);
        background-repeat: repeat-x;
    }

    .fontweightcls {
        font-weight: 500;
    }

    .bootBox-btn-margin {
        margin-right: 10px;
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="m-b-md" id="wizardControl">
    <a class="btn btn-primary" href="#step1" data-toggle="tab">
        <?php echo $this->lang->line('promana_common_step_one'); ?>
        <!--Step 1-->
        - Project Initiation</a>
    <a class="btn btn-default btn-wizard" onclick="fetch_project_charter()" href="#step2" data-toggle="tab">
        <?php echo $this->lang->line('promana_common_step_two'); ?>
        <!--Step 2-->
        - Project Planning & Scheduling</a>

    <a class="btn btn-default btn-wizard" onclick="fetch_executionmoni();" href="#step3" data-toggle="tab">
        Step 3 - Execution</a>

    <a class="btn btn-default btn-wizard" onclick="fetch_executionmoni_control();" href="#step4" data-toggle="tab">
        Step 4 - Monitoring and Control</a>

    <a class="btn btn-default btn-wizard" onclick="fetch_projectclosure();" href="#step5" data-toggle="tab">
        Step 5 - Project Closure</a>
</div>
<hr>

<div class="tab-content">
    <div id="step1" class="tab-pane active">

        <div class="row">
            <?php echo form_open('login/loginSubmit', 'role="form" id="boq_create_form"') ?>
            <input type="hidden" name="headerID" id="headerID" value="<?php echo $_POST['page_id'] ?>">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>Project Header</h2>
                </header>

                <div class="row">
                    <input type="hidden" name="headerID" id="headerID" value="<?php echo $_POST['page_id'] ?>">
                    <div class="form-group col-sm-4">
                        <label for="" class=" control-label fontweightcls">
                            <?php echo $this->lang->line('common_project'); ?>
                            <!--Project--><?php required_mark(); ?></label>
                        <?php echo form_dropdown(
                            'projectID',
                            $project,
                            '',
                            'class="form-control searchbox" onchange="get_project(this.value)" id="projectID"  required'
                        ); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="servicelinecode" class="fontweightcls">
                            <?php echo $this->lang->line('common_segment'); ?>
                            <!--Segment--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown(
                            'segement',
                            $service_line_arr,
                            '',
                            'class="form-control searchbox" id="segement" required'
                        ); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="servicelinecode" class="fontweightcls">Project Name<?php required_mark(); ?></label>
                        <input type="text" class="form-control" id="projectname" name="projectname">
                    </div>
                </div>
                <div class="row" id="">
                    <div class="form-group col-sm-4">
                        <label class="fontweightcls"><?php echo $this->lang->line('common_document_date'); ?><?php required_mark(); ?>
                            <!--Document Date--> </label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>

                            <input type="text" name="documentdate" value="" id="documentdate"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="fontweightcls">
                            <?php echo $this->lang->line('promana_pm_cost_project_start_date'); ?> <?php required_mark(); ?>
                            <!--Project Start Date--></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="prjStartDate" value="" id="prjStartDate"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="fontweightcls">
                            <?php echo $this->lang->line('promana_pm_cost_project_end_date'); ?> <?php required_mark(); ?>
                            <!--Project End Date--></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>

                            <input type="text" name="prjEndDate" value="" id="prjEndDate"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="location" class="fontweightcls">
                            <?php echo $this->lang->line('common_customer_name'); ?>
                            <!--Customer Name--> <?php required_mark(); ?></label>
                        <?php echo form_dropdown(
                            'customer',
                            $customer_arr,
                            '',
                            '  class="form-control searchbox" id="customer" required'
                        ); ?>
                    </div>


                    <div class="form-group col-sm-4">
                        <label class="fontweightcls">
                            <?php echo $this->lang->line('common_currency'); ?>
                            <!--Currency--><?php required_mark(); ?></label>
                        <?php echo form_dropdown(
                            'currency',
                            $currency_arr,
                            '',
                            ' class="form-control searchbox" id="currency" required'
                        ); ?>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group ">
                            <label for="comments"
                                   class="fontweightcls"><?php echo $this->lang->line('common_comments'); ?>
                                <!--Comments--></label>
                            <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="location" class="fontweightcls">
                            Customer Type <?php required_mark(); ?></label>
                        <?php echo form_dropdown(
                            'customertype',
                            array('' => 'Select Customer type', '1' => 'Internal', '2' => 'External'),
                            '',
                            '  class="form-control searchbox" id="customertype"'
                        ); ?>
                    </div>
                    <div class="form-group col-sm-3">
                        <label class="fontweightcls">
                            Retention Percentage (%) <?php required_mark(); ?></label>
                        <input autocomplete="off" type="text" onkeypress="return validateFloatKeyPress(this,event)"
                               name="retentionpercentage" class="form-control text-right" id="retentionpercentage">
                    </div>
                    <div class="form-group col-sm-3">
                        <label class="fontweightcls">
                            Advance Percentage (%) <?php required_mark(); ?></label>
                        <input autocomplete="off" type="text" onkeypress="return validateFloatKeyPress(this,event)"
                               name="advancepercentage" class="form-control text-right" id="advancepercentage">
                    </div>
                    <div class="form-group col-sm-3">
                        <label class="fontweightcls">Maintenance/Warranty Period</label>
                        <input autocomplete="off" type="text" value="12" name="warrantyPeriod"
                               class="form-control text-right" id="warrantyPeriod" placeholder="In month">
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-12">
                        <button class="btn btn-primary pull-right" id="save_submit" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <br>
        <div class="row addTableView hide">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>Expression Of Interest</h2>
                </header>
                <?php echo form_open('', 'role="form" id="eoi_tender_add_edit_form"') ?>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="" class="control-label fontweightcls">EOI Status</label>
                        <?php echo form_dropdown('eoistatus', array('' => 'Select Status', '1' => 'Submitted', '2' => 'Not Submitted'), '', 'class="form-control searchbox"  id="eoistatus" '); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="fontweightcls">EOI Submission Date</label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="eoisubdate" value="" id="eoisubdate"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>
                    </div>
                </div>
                </form>
                <br>
                <div class="row ">
                    <div class="col-md-8">
                        <h2 style="margin: 0;font-size: 14px;line-height: 14px;text-transform: uppercase;display: inline-block;padding: 0 8px 0 0;
                   background: #fff;white-space: nowrap;font-weight: bold !important;"><i
                                    class="fa fa-hand-o-right"></i> EOI ATTACHMENTS</h2>
                    </div>
                    <br><br>
                    <?php echo form_open_multipart('', 'id="eoi_attachment_upload" class="form-inline"'); ?>
                    <div class="col-sm-12" style="margin-left: 3%">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" class="form-control" id="eoiattachmentDescription"
                                       name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                                <input type="hidden" class="form-control" id="eoi_documentID" name="documentID"
                                       value="PROEOI">
                                <input type="hidden" class="form-control" id="eoi_document_name" name="document_name"
                                       value="EOI ATTACHMENTS">
                                <input type="hidden" class="form-control" id="eoi_documentSystemCode"
                                       name="documentSystemCode" value="<?php echo $_POST['page_id'] ?>">
                            </div>
                        </div>
                        <div class="col-sm-8" style="margin-top: -8px;">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                     style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                                class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                                class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                            aria-hidden="true"></span></span><span
                                                class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                               aria-hidden="true"></span></span><input
                                                type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id_eoi"
                                       data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                      aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="document_uplode_eoi()"><span
                                        class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                            </button>
                            </form>
                        </div>


                    </div>

                </div>
                <br>
                <div id="eoi_multiple_attachemts"></div>

            </div>
        </div>

        <br>
        <div class="row addTableView hide" style="/* background: #6090f5; */">
            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>Insurance Policies</h2>
                </header>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="control-label fontweightcls"><?= $this->lang->line('promana_policy_description') ?></label>
                        <input type="text" class="form-control" id="policyDescription" name="policyDescription">
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="control-label fontweightcls"><?= $this->lang->line('promana_policy_date_from') ?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="policyDateFrom" value="" id="policyDateFrom"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?= $date_format_policy ?>'" required>
                        </div>
                    </div>

                    <div class="form-group col-sm-4">
                        <label class="control-label fontweightcls"><?= $this->lang->line('promana_policy_date_to') ?></label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="policyDateTo" value="" id="policyDateTo"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?= $date_format_policy ?>'" required>
                        </div>
                    </div>
                </div>

                <div class="row ">
                    <br>
                    <br>
                    <div class="col-md-8">
                        <h2 style="margin: 0;font-size: 14px;line-height: 14px;text-transform: uppercase;display: inline-block;padding: 0 8px 0 0;
                    background: #fff;white-space: nowrap;font-weight: bold !important;">
                            <i class="fa fa-hand-o-right"></i> INSURANCE ATTACHMENTS
                        </h2>
                    </div>
                    <br>
                    <br>
                    <?php echo form_open_multipart('', 'id="insurance_attachment_upload" class="form-inline"'); ?>
                    <div class="col-sm-12" style="margin-left: 3%">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" class="form-control" id="ins_attachmentDescription"
                                       name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                                <input type="hidden" class="form-control" name="documentID" value="PROINS">
                                <input type="hidden" class="form-control" name="document_name"
                                       value="INSURANCE ATTACHMENTS">
                                <input type="hidden" class="form-control" name="documentSystemCode"
                                       value="<?php echo $_POST['page_id'] ?>">
                            </div>
                        </div>
                        <div class="col-sm-8" style="margin-top: -8px;">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                     style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file color fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">
                                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                        </span>
                                        <span class="fileinput-exists">
                                            <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>
                                        </span>
                                        <input type="file" name="document_file" id="document_file">
                                    </span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id_ins"
                                       data-dismiss="fileinput">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                    </a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="document_uplode_insurance()">
                                <span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>
                <br>
                <div id="insurance_multiple_attachemts"></div>
            </div>
        </div>
        <br>


        <br>
        <div class="row addTableView hide">

            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>Tender Information</h2>
                </header>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="" class=" control-label fontweightcls">Tender Reference No</label>
                        <input type="text" class="form-control" id="tenderreferenceno" name="tenderreferenceno">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="" class=" control-label fontweightcls">Tender Value</label>
                        <input type="text" class="form-control" id="tendervalue" name="tendervalue">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="servicelinecode " class="fontweightcls">Tender Status</label>
                        <?php echo form_dropdown('tenderstatus', array('' => 'Select Status', '1' => 'Submitted', '2' => 'Lost', '3' => 'Won', '4' => 'Ongoing', '5' => 'Declined'), '', 'class="form-control searchbox" id="tenderstatus" required'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="fontweightcls">Tender Submission Date</label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="tendersubmissiondate" value="" id="tendersubmissiondate"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="servicelinecode" class="fontweightcls">Type of Contract</label>
                        <?php echo form_dropdown('typeofcontract', typeOfContract(), '', 'class="form-control searchbox" id="typeofcontract" required'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="servicelinecode" class="fontweightcls">Comments</label>
                        <textarea class="form-control" id="commentsstatus" name="commentsstatus" rows="3"></textarea>
                    </div>


                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group ">
                            <label for="comments" class="fontweightcls">Description of the Contract</label>
                            <textarea class="form-control" id="descriptionofthecontract" name="descriptionofthecontract"
                                      rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group ">
                            <label for="comments" class="fontweightcls">Special Conditions</label>
                            <textarea class="form-control" id="specialconditions" name="specialconditions"
                                      rows="3"></textarea>
                        </div>
                    </div>

                </div>
                <br>
                <div class="row ">
                    <div class="col-md-8">
                        <h2 style="margin: 0;font-size: 14px;line-height: 14px;text-transform: uppercase;display: inline-block;padding: 0 8px 0 0;
                   background: #fff;white-space: nowrap;font-weight: bold !important;"><i
                                    class="fa fa-hand-o-right"></i> TENDER ATTACHMENTS</h2>
                    </div>
                    <br><br>
                    <?php echo form_open_multipart('', 'id="tender_attachment_uplode_form" class="form-inline"'); ?>
                    <div class="col-sm-12" style="margin-left: 3%">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" class="form-control" id="tenderattachmentDescription"
                                       name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                                <input type="hidden" class="form-control" id="tender_documentID" name="documentID"
                                       value="PROTENDER">
                                <input type="hidden" class="form-control" id="tender_document_name" name="document_name"
                                       value="TENDER ATTACHMENTS">
                                <input type="hidden" class="form-control" id="tender_documentSystemCode"
                                       name="documentSystemCode" value="<?php echo $_POST['page_id'] ?>">
                            </div>
                        </div>
                        <div class="col-sm-8" style="margin-top: -8px;">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                     style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                                class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                                class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                            aria-hidden="true"></span></span><span
                                                class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                               aria-hidden="true"></span></span><input
                                                type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id_tender"
                                       data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                      aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="document_uplode_tender()"><span
                                        class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                            </button>
                            </form>
                        </div>


                    </div>

                </div>
                <br>
                <div id="tender_multiple_attachemts"></div>
                <br>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="fontweightcls">Bid Submission Date</label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="bidsubmissiondate" value="" id="bidsubmissiondate"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="fontweightcls">Bid Due Date</label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="bidduedate" value="" id="bidduedate"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="fontweightcls">Bid Expiry Date</label>
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="bidexpirydate" value="" id="bidexpirydate"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="" class=" control-label fontweightcls">Bid Validity Period</label>
                        <input type="text" class="form-control" id="bidvalidityperiod" name="bidvalidityperiod">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="" class=" control-label fontweightcls">Bond Value</label>
                        <input type="text" class="form-control" id="bondvalue" name="bondvalue">
                    </div>

                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-2">
                        <label for="" class=" control-label fontweightcls">Company to Supply Bid Bond</label>
                    </div>

                    <div class="form-group col-sm-4">
                        <div class="skin-section extraColumns">
                            <label class="radio-inline">
                                <div class="skin-section extraColumnsgreen">
                                    <label for="checkbox">Yes&nbsp;&nbsp;</label>
                                    <input id="active" type="radio" data-caption="" class="columnSelected cheack_type"
                                           name="active_val">
                                </div>
                            </label>
                            <label class="radio-inline">
                                <div class="skin-section extraColumnsgreen">
                                    <label for="checkbox">No&nbsp;&nbsp;</label>
                                    <input id="inactive" type="radio" data-caption=""
                                           class="columnSelected cheack_type_uncheack" name="active_val">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row ">
                    <div class="col-md-8">
                        <h2 style="margin: 0;font-size: 14px;line-height: 14px;text-transform: uppercase;display: inline-block;padding: 0 8px 0 0;
                   background: #fff;white-space: nowrap;font-weight: bold !important;"><i
                                    class="fa fa-hand-o-right"></i> BID ATTACHMENTS</h2>
                    </div>
                    <br><br>
                    <?php echo form_open_multipart('', 'id="bid_attachment_uplode_form" class="form-inline"'); ?>
                    <div class="col-sm-12" style="margin-left: 3%">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" class="form-control" id="bidattachmentDescription"
                                       name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                                <input type="hidden" class="form-control" id="bid_documentID" name="documentID"
                                       value="PROBID">
                                <input type="hidden" class="form-control" id="bid_document_name" name="document_name"
                                       value="BID ATTACHMENTS">
                                <input type="hidden" class="form-control" id="bid_documentSystemCode"
                                       name="documentSystemCode" value="<?php echo $_POST['page_id'] ?>">
                            </div>
                        </div>
                        <div class="col-sm-8" style="margin-top: -8px;">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                     style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                                class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                                class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                            aria-hidden="true"></span></span><span
                                                class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                               aria-hidden="true"></span></span><input
                                                type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id_bid"
                                       data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                      aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="document_uplode_bid()"><span
                                        class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                            </button>
                            </form>
                        </div>


                    </div>

                </div>
                <br>
                <div id="bid_multiple_attachemts"></div>

            </div>


        </div>

        <div class="row addTableView hide">

            <div class="col-md-12 animated zoomIn">
                <header class="head-title">
                    <h2>Budget Estimation</h2>
                </header>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="" class=" control-label fontweightcls">Consultant</label>
                        <?php echo form_dropdown('consultant', array('' => 'Select Type', '1' => 'Internal', '2' => 'External'), '', 'class="form-control searchbox" id="consultant" required'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="" class=" control-label fontweightcls">Budget Approval Management</label>
                        <?php echo form_dropdown('bapprovalmanagement', array('' => 'Select Type', '1' => 'Approved', '2' => 'Sent For Approval', '3' => 'Pending', '4' => 'Rejected'), '', 'class="form-control searchbox" id="bapprovalmanagement" required'); ?>
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="" class=" control-label fontweightcls">Total Budget Estimation</label>
                        <input type="text" class="form-control" id="budgetestimation" name="budgetestimation">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="" class=" control-label fontweightcls">Budget Approval Internal Client</label>
                        <?php echo form_dropdown('bapprovalinternalclient', array('' => 'Select Type', '1' => 'Approved', '2' => 'Sent For Approval', '3' => 'Pending', '4' => 'Rejected'), '', 'class="form-control searchbox" id="bapprovalinternalclient" required'); ?>
                    </div>


                    <div class="form-group col-sm-4 approvalstatus_cls hide">
                        <label for="" class=" control-label fontweightcls">Status</label>
                        <?php echo form_dropdown('approvalstatus', array('' => 'Select Type', '1' => 'Approved', '2' => 'Rejected'), '', 'class="form-control searchbox" id="approvalstatus" required'); ?>
                    </div>


                </div>


                <br>
                <div class="row ">
                    <div class="col-md-8">
                        <h2 style="margin: 0;font-size: 14px;line-height: 14px;text-transform: uppercase;display: inline-block;padding: 0 8px 0 0;
                   background: #fff;white-space: nowrap;font-weight: bold !important;"><i
                                    class="fa fa-hand-o-right"></i> BUDGET ATTACHMENTS</h2>
                    </div>
                    <br><br>
                    <?php echo form_open_multipart('', 'id="budget_attachment_uplode_form" class="form-inline"'); ?>
                    <div class="col-sm-12" style="margin-left: 3%">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" class="form-control" id="budgetattachmentDescription"
                                       name="attachmentDescription" placeholder="Description..." style="width: 240%;">
                                <input type="hidden" class="form-control" id="budget_documentID" name="documentID"
                                       value="PROBUDGET">
                                <input type="hidden" class="form-control" id="budget_document_name" name="document_name"
                                       value="BUDGET ATTACHMENTS">
                                <input type="hidden" class="form-control" id="budget_documentSystemCode"
                                       name="documentSystemCode" value="<?php echo $_POST['page_id'] ?>">
                            </div>
                        </div>
                        <div class="col-sm-8" style="margin-top: -8px;">
                            <div class="form-group">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                     style="margin-top: 8px;">
                                    <div class="form-control" data-trigger="fileinput"><i
                                                class="glyphicon glyphicon-file color fileinput-exists"></i> <span
                                                class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file"><span
                                                class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                            aria-hidden="true"></span></span><span
                                                class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                               aria-hidden="true"></span></span><input
                                                type="file" name="document_file" id="document_file"></span>
                                    <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id_budget"
                                       data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                      aria-hidden="true"></span></a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-default" onclick="document_uplode_budget()"><span
                                        class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                            </button>
                            </form>
                        </div>


                    </div>

                </div>
                <br>
                <div id="budget_multiple_attachemts"></div>
            </div>
        </div>
        <br>
        <hr>
        <br>
        <div class="row addTableView hide">
            <div class="col-sm-12">
                <div class="text-right m-t-xs">
                    <button class="btn btn-primary " onclick="save_draft()">Save as Draft</button>
                    <button class="btn btn-primary submitWizard" onclick="save_eoi_bid()">Save</button>
                </div>
            </div>
        </div>

    </div>
    <div id="step2" class="tab-pane">
        <div class="row" style="margin: 1%">
            <ul class="nav nav-tabs" id="main-tabs">
                <li class="active"><a href="#projectcharter" onclick="fetch_project_charter()" data-toggle="tab">Project
                        Charter</a></li>
                <li><a href="#projectcost" data-toggle="tab">Cost</a></li>
                <li><a href="#projectcost_review" data-toggle="tab" onclick="show_summary_data()">Cost Review</a></li>
                <li><a href="#quantitysurveying" data-toggle="tab" onclick="show_quantity_surve()"> Quantity
                        Surveying</a></li>
            </ul>
        </div>

        <div class="tab-content">
            <div class="tab-pane active" id="projectcharter">
                <div class="row" style="margin: 0px">
                    <div id="projectcharter_html"></div>
                </div>
            </div>
            <div class="tab-pane" id="projectcost">
                <div class="row" style="margin: 1%">
                    <ul class="nav nav-tabs" id="main-tabs">
                        <li class="active"><a href="#pre-tender" onclick="loadheaderdetails()" data-toggle="tab">Pre-tender</a>
                        </li>
                        <li class="hide" id="post-tender_hide"><a href="#post-tender" data-toggle="tab"
                                                                  onclick="fetch_posttender()">Post-tender</a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div id="pre-tender" class="tab-pane active">
                        <div class="row" style="margin: 0px">
                            <div class="col-sm-12">
                                <h5 style="text-align: center">
                                    <div id="pcode"></div>
                                </h5>
                            </div>
                            <div class="col-sm-12" style="padding: 5px;margin-bottom: 4px;
    background-color: rgba(175, 213, 175, 0.27);">
                                <table width="100%" class="">

                                    <tr>
                                        <td><b><?php echo $this->lang->line('common_project'); ?>
                                                <!--Project-->:</b></td>
                                        <td>
                                            <div id="pcompany"></div>
                                        </td>
                                        <td><b><?php echo $this->lang->line('common_segment'); ?>
                                                <!--Segment-->:</b></td>
                                        <td>
                                            <div id="psegement"></div>
                                        </td>
                                        <td><b><?php echo $this->lang->line('common_customer_name'); ?>
                                                <!--Customer Name-->:</b></td>
                                        <td>
                                            <div id="pcustomer"></div>
                                        </td>
                                    </tr>

                                    <tr>

                                        <td><b>
                                                <?php echo $this->lang->line('promana_pm_cost_project_start_date'); ?>
                                                <!--Project Start Date-->
                                                :</b></td>
                                        <td>
                                            <div id="pstartdate"></div>
                                        </td>
                                        <td><b>
                                                <?php echo $this->lang->line('promana_pm_cost_project_end_date'); ?>
                                                <!--Project End Date-->
                                                :</b></td>
                                        <td>
                                            <div id="penddate"></div>
                                        </td>

                                        <td><b><?php echo $this->lang->line('common_document_date'); ?>
                                                <!--Document Date-->:</b></td>
                                        <td>
                                            <div id="pdocumentdate"></div>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td><b>
                                                <?php echo $this->lang->line('promana_pm_cost_customer_currency'); ?>
                                                <!--Customer Currency-->
                                                :</b></td>
                                        <td>
                                            <div id="pcurrency"></div>
                                        </td>

                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>


                                </table>
                            </div>
                            <div class="row editview">
                                <div class="col-sm-12">
                                    <a onclick="modalheaderdetails(0)" class="btn btn-primary btn-xs pull-right">
                                        <?php echo $this->lang->line('common_create_new'); ?>
                                        <!--Create New--></a>
                                </div>

                            </div>
                            <br>
                            <div class="row" style="margin: 0px;">
                                <div class="">
                                    <div id="loadheaderdetail">
                                        <table id="loadcosttable" class="<?php echo table_class() ?> custometbl">
                                            <thead>
                                            <tr>
                                                <th rowspan="3"><?php echo $this->lang->line('common_category'); ?>
                                                    <!--Category-->
                                                </th>
                                                <th rowspan="3">
                                                    <?php echo $this->lang->line('common_description'); ?>
                                                    <!--Description-->
                                                </th>
                                                <th rowspan="3"><?php echo $this->lang->line('common_unit'); ?>
                                                    <!--Unit-->
                                                </th>

                                                <th rowspan="2" colspan="3">
                                                    <?php echo $this->lang->line('promana_common_selling_price'); ?>
                                                    <!--Selling Price-->
                                                </th>
                                                <th rowspan="3" width="70px">
                                                    <?php echo $this->lang->line('promana_pm_markup'); ?>
                                                    <!--Markup--> %
                                                </th>
                                                <th colspan="4"><?php echo $this->lang->line('common_cost'); ?>
                                                    <!--Cost-->
                                                </th>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <th colspan="2">
                                                    <?php echo $this->lang->line('promana_pm_material_cost'); ?>
                                                    <!--Material Cost-->
                                                </th>
                                                <th rowspan="2">
                                                    <?php echo $this->lang->line('promana_pm_total_labour_cost'); ?>
                                                    <!--Total Labour Cost-->
                                                </th>
                                                <th rowspan="2">
                                                    <?php echo $this->lang->line('promana_pm_total_cost'); ?>
                                                    <!--Total Cost-->
                                                </th>
                                                <th rowspan="2"></th>
                                            </tr>

                                            <tr>
                                                <th><?php echo $this->lang->line('common_qty'); ?>
                                                    <!--Qty-->
                                                </th>
                                                <th><?php echo $this->lang->line('promana_common_unit_rate'); ?>
                                                    <!--Unit Rate-->
                                                </th>
                                                <th><?php echo $this->lang->line('common_total_value'); ?>
                                                    <!--Total Value-->
                                                </th>
                                                <th><?php echo $this->lang->line('common_unit'); ?>
                                                    <!--Unit-->
                                                </th>
                                                <th><?php echo $this->lang->line('common_total'); ?>
                                                    <!--Total-->
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>

                                    </div>
                                    <br>


                                </div>
                            </div>
                        </div>

                    </div>
                    <div id="post-tender" class="tab-pane">
                        <div class="col-md-12">
                            <div id="post-tenderview"></div>
                        </div>

                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <div id="boq_attachments_view"></div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="projectcost_review">
                <div class="row" style="margin: 0px">
                    <div class="col-md-12">
                        <h5 style="text-align: center"><span id="p2code"></span></h5>
                        <div class="pull-right">
                            <button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateReportPdf()">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                <?php echo $this->lang->line('promana_common_pdf_for_client'); ?>
                                <!--PDF for client-->
                            </button>
                            <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="project.xls"
                               onclick="var file = tableToExcel('tablesheet', 'project'); $(this).attr('href', file);">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                <?php echo $this->lang->line('promana_common_pdf_for_excel'); ?>
                                <!-- Excel-->
                            </a>
                        </div>
                    </div>
                    <div id="tablesheet">
                        <div class="col-md-12" style="padding: 5px;margin-bottom: 4px;
    background-color: rgba(175, 213, 175, 0.27);">
                            <table id="" width="100%" class="">

                                <tr>
                                    <td><b><?php echo $this->lang->line('promana_common_project'); ?>
                                            <!--Project-->:</b></td>
                                    <td>
                                        <div id="p2company"></div>
                                    </td>
                                    <td><b><?php echo $this->lang->line('common_segment'); ?>
                                            <!--Segment-->:</b></td>
                                    <td>
                                        <div id="p2segement"></div>
                                    </td>
                                    <td><b><?php echo $this->lang->line('common_customer_name'); ?>
                                            <!--Customer Name-->:</b>
                                    </td>
                                    <td>
                                        <div id="p2customer"></div>
                                    </td>
                                </tr>

                                <tr>

                                    <td><b>
                                            <?php echo $this->lang->line('promana_pm_cost_project_start_date'); ?>
                                            <!--Project Start Date-->
                                            :</b></td>
                                    <td>
                                        <div id="p2startdate"></div>
                                    </td>
                                    <td><b>
                                            <?php echo $this->lang->line('promana_pm_cost_project_end_date'); ?>
                                            <!--Project End Date-->
                                            :</b></td>
                                    <td>
                                        <div id="p2enddate"></div>
                                    </td>

                                    <td><b><?php echo $this->lang->line('common_document_date'); ?>
                                            <!--Document Date-->:</b>
                                    </td>
                                    <td>
                                        <div id="p2documentdate"></div>
                                    </td>

                                </tr>

                                <tr>
                                    <td><b>
                                            <?php echo $this->lang->line('promana_pm_cost_customer_currency'); ?>
                                            <!--Customer Currency-->
                                            :</b></td>
                                    <td>
                                        <div id="p2currency"></div>
                                    </td>

                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>


                            </table>
                        </div>
                        <div class="col-md-12" style="    padding: 0px;">


                            <div id="summaryTable">

                            </div>

                            <hr>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-right m-t-xs">
                            <button class="confirmYNbtn btn btn-success submitWizard" onclick="confirm_boq()">
                                <?php echo $this->lang->line('common_confirmation'); ?>
                                <!--Confirmation-->
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="tab-pane" id="quantitysurveying">
                <div class="row" style="margin: 0px">
                    <div class="col-md-12">
                        <div class="quantitysurveying"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="step3" class="tab-pane">
        <div id="executionmonitoringandcontrol"></div>
    </div>

    <div id="step4" class="tab-pane">
        <div id="monitoringandcontrol"></div>
    </div>

    <div id="step5" class="tab-pane">
        <div id="projectma_closure"></div>
    </div>

</div>
<?php echo footer_page('Right foot', 'Left foot', FALSE); ?>
<div style="z-index: 1000000000" aria-hidden="true" role="dialog" tabindex="-1" id="itemSeachModal" class="modal fade">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 id="" class="modal-title">
                    <?php echo $this->lang->line('promana_common_item_search'); ?>
                    <!--Item Search-->
                </h5>
            </div>

            <div class="modal-body">
                keyword <input type="text" id="searchKeyword" onkeyup="searchByKeyword()"/> <span id="loader_itemSearch"
                                                                                                  style="display: none;"><i
                            class="fa fa-refresh fa-spin"></i></span>
                <br>
                <br>

                <div>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>

                            <th><?php echo $this->lang->line('promana_common_item_code'); ?>
                                <!--Item Code-->
                            </th>
                            <th><?php echo $this->lang->line('common_description'); ?>
                                <!--Description-->
                            </th>
                            <th><?php echo $this->lang->line('common_uom'); ?>
                                <!--UOM-->
                            </th>

                            <th><?php echo $this->lang->line('common_currency'); ?>
                                <!--Currency-->
                            </th>
                            <th><?php echo $this->lang->line('common_cost'); ?>
                                <!--Cost-->
                            </th>

                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="itemSearchResultTblBody">

                        </tbody>
                    </table>
                </div>
            </div>


            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    <?php echo $this->lang->line('common_Close'); ?>
                    <!--Close--></button>

            </div>
        </div>

    </div>

</div>
</div>
<div aria-hidden="true" role="dialog" id="modalheaderdetails" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 id="" class="modal-title">
                    <?php echo $this->lang->line('promana_pm_create_detail_header'); ?>
                    <!--Create Detail header-->
                </h5>
            </div>
            <form role="form" id="boq_create_detail_header" class="form-horizontal">
                <input type="hidden" id="tendertype" name="tendertype">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_category'); ?>
                            <!--Category--> <?php required_mark(); ?></label>
                        <div class="col-sm-8" id="htmlCategory">
                            <!--   <?php /*echo form_dropdown('category', $category_arr, '',
                            'onchange="getSubcategory()" class="form-control searchbox" id="categoryID" required'); */ ?>-->
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('promana_common_sub_cat'); ?>
                            <!--Sub Category--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <?php echo form_dropdown(
                                'subcategory',
                                $subcategory_arr,
                                '',
                                'onchange="subcategorychange()" class="form-control searchbox" id="subcategoryID" required'
                            ); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_description'); ?>
                            <!--Description--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <input type="text" name="description" id="description" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_unit'); ?>
                            <!--Unit--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <!-- --><?php /*echo form_dropdown('unitID', $unit_array, 'disabled', 'class="form-control searchbox" id="unitID" required'); */ ?>
                            <input type="text" name="unitshortcode" id="unitshortcode" readonly class="form-control">
                            <input type="hidden" name="unitID" id="unitID" readonly>

                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?>
                        <!--Close--></button>
                    <button class="btn btn-primary" type="submit">
                        <?php echo $this->lang->line('common_save_change'); ?>
                        <!--Save changes--></button>
                </div>


        </div>
        </form>
    </div>
</div>


<div aria-hidden="true" role="dialog" tabindex="-1" id="modalcostsheet" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 id="" class="modal-title"><?php echo $this->lang->line('common_cost'); ?>
                    <!--Cost-->
                </h5>
            </div>

            <form role="form" id="boq_cost_form_sheet" class="form-horizontal">
                <div class="modal-body" style="overflow: auto;max-height: 400px">

                    <input type="hidden" name="categoryID" id="categoryID1">
                    <input type="hidden" name="subcategoryID" id="subcategoryID1">
                    <input type="hidden" name="customerCurrencyID" id="customerCurrencyID">
                    <input type="hidden" name="detailID" id="detailID">
                    <input type="hidden" name="itemautoidproject" id="itemautoidproject">
                    <input type="hidden" name="tendertype_cost" id="tendertype_cost">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_item'); ?>
                                    <!--Item--></label>
                                <div class="  col-sm-9">
                                    <div class="input-group">

                                        <input readonly type="text" class="form-control col-sm-6 " name="search"
                                               id="search" placeholder="Item ID, Item Description...">
                                        <div onclick="itemSearchModal()" class="input-group-addon"><i
                                                    class="	glyphicon glyphicon-plus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_uom'); ?>
                                    <!--UOM--></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" readonly id="uom" name="uom"
                                           placeholder="UOM">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('promana_pm_total_cost'); ?>
                                    <!--Total Cost--></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" readonly id="totalcost1" name="totalcost"
                                           value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label"></label>
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-xs btn-primary" id="submitcostsheet">
                                        <?php echo $this->lang->line('common_add'); ?>
                                        <!--Add-->
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_qty'); ?>
                                    <!--Qty--></label>
                                <div class="col-sm-4">
                                    <input type="number" step="any" class="form-control" value="0"
                                           onchange="calculate()" id="qty1" name="qty" placeholder="Qty">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_unit_cost'); ?>
                                    <!--Unit Cost--></label>
                                <div class="col-sm-9">
                                    <input type="number" step="any" class="form-control" id="unitcost"
                                           onchange="calculate()" name="unitcost" placeholder="Unit Cost">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_currency'); ?>
                                    <!--Currency--></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" readonly id="currency1" name="currency"
                                           placeholder="Currency">
                                </div>
                            </div>

                        </div>
                    </div>


                    <br>

                    <div id="loadcostsheettable"></div>


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?>
                        <!--Close--></button>

                </div>


        </div>
        </form>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="addMasterTask" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 id="title" class="modal-title">
                    <?php echo $this->lang->line('promana_pm_cost_project_planning'); ?>
                    <!--Project Planning-->
                </h5>
            </div>
            <form role="form" id="formProjectPlanning" class="form-horizontal">
                <input type="hidden" id="projectPlannningID" name="projectPlannningID" value="0">
                <input type="hidden" id="projectphase" name="projectphase" value="0">
                <div class="modal-body">

                    <div class="form-group hide projectphaseshow">
                        <label for="" class="col-sm-3 control-label">
                            Phase <?php required_mark(); ?></label>
                        <div class="col-sm-8" id="">
                            <div id="div_load_projectphase">
                                <select name="projectphase" class="form-control select2" id="projectphase">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_description'); ?>
                            <!--Description--> <?php required_mark(); ?></label>
                        <div class="col-sm-8" id="">
                            <input type="text" name="description" id="planningdescription" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_note'); ?>
                            <!--Note--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <input type="text" name="note" id="planningnote" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for=" " class="col-sm-3 control-label">Project Category <?php required_mark(); ?></label>
                        <div class="col-sm-8" id="load_project_category">
                            <?php // echo form_dropdown('project_category', load_all_project_categories(), '', 'class="form-control select2" id="project_category" '); 
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('promana_pm_assign_employee'); ?>
                            <!--Assign Employee--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <?php
                            $empArr = array();
                            $emp = all_employee_drop_with_non_payroll(FALSE);
                            if (!empty($emp)) {
                                foreach ($emp as $row) {
                                    $empArr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                                }
                            }
                            ?>
                            <?php echo form_dropdown(
                                'assignedEmployee[]',
                                $empArr,
                                '',
                                ' class="form-control " multiple id="assignedEmployee" required'
                            ); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for=" " class="col-sm-3 control-label">Depended Task</label>
                        <div class="col-sm-8">
                            <div id="div_load_project">
                                <select name="relatedprojectID" class="form-control select2" id="relatedprojectID">
                                    <option value="" selected="selected">Select Project</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for=" " class="col-sm-3 control-label">Relationship</label>
                        <div class="col-sm-8">
                            <?php echo form_dropdown('relationship', project_relationship(), '', 'class="form-control select2" id="relationship" '); ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_start_date'); ?>
                            <!--Start Date--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">
                            <div class="input-group datepic_addtask">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>

                                <input type="text" name="startDate" value="" id="planningStartDate"
                                       class="form-control dateFields"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_end_date'); ?>
                            <!--End Date--> <?php required_mark(); ?></label>
                        <div class="col-sm-8">

                            <div class="input-group datepic_addtask">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>

                                <input type="text" name="endDate" value="" id="planningEndDate"
                                       class="form-control dateFields"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'">
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('promana_common_completed'); ?>
                            <!--Completed--> %</label>
                        <div class="col-sm-8">
                            <input type="number" max="100" min="0" name="percentage" id="percentage"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('promana_pm_color'); ?>
                            <!--Color--></label>
                        <div class="col-sm-8">
                            <select id="color" name="color" class="form-control searchbox">
                                <option value="ggroupblack">
                                    <?php echo $this->lang->line('promana_pm_black'); ?>
                                    <!--Black-->
                                </option>
                                <option value="gtaskblue">
                                    <?php echo $this->lang->line('promana_pm_blue'); ?>
                                    <!--Blue-->
                                </option>
                                <option value="gtaskred">
                                    <?php echo $this->lang->line('promana_pm_red'); ?>
                                    <!--Red-->
                                </option>
                                <option value="gtaskpurple">
                                    <?php echo $this->lang->line('promana_pm_purple'); ?>
                                    <!--Purple-->
                                </option>
                                <option value="gtaskgreen">
                                    <?php echo $this->lang->line('promana_pm_green'); ?>
                                    <!--Green-->
                                </option>
                                <option value="gtaskpink">
                                    <?php echo $this->lang->line('promana_pm_pink'); ?>
                                    <!--Pink-->
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="vehicle" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('common_sort_order'); ?>
                            <!--Sort Order--></label>
                        <div class="col-sm-8">
                            <input type="text" readonly name="sortOrder" id="planningSortOrder" class="form-control">
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?>
                        <!--Close--></button>
                    <button class="btn btn-primary" type="submit" id="save_btn_addtask">
                        <?php echo $this->lang->line('common_save_change'); ?>
                        <!--Save changes--></button>
                </div>


        </div>
        </form>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="projectteammodal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="usergroup-title">Add Project Team</h4>
            </div>
            <?php echo form_open('', 'role="form" id="projectteam_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="hederIDproject" name="hederIDproject">
                <input type="hidden" id="teamidproject" name="teamidproject">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Organization</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('organization', array('' => 'Select Organization', '1' => 'Client', '2' => 'Contractor', '3' => 'External'), '', 'class="form-control select2" id="organization"'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
                <div class="row hide empdrophideshow">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Employee </label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('Employee', all_employee_drop(true), '', 'class="form-control select2" id="Employee"'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>

                <div class="row hide empnameshow">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Name</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                            <input type="text" name="empname" id="empname" class="form-control">
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Role</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                            <?php echo form_dropdown('organizationrole', get_all_project_teamrole(), '', 'class="form-control select2" id="organizationrole"'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="save_projectteam()" class="btn btn-sm btn-primary"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?>
                        <!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="activityplanning_modal">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="usergroup-title">Activity Planning</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="headerID" id="headerID">
                <input type="hidden" name="boq_detailID" id="boq_detailID">
                <input type="hidden" name="type" id="type">
                <div class="row" style="margin: 1%">
                    <ul class="nav nav-tabs" id="main-tabs">
                        <li class="active"><a href="#materialplanning" data-toggle="tab"
                                              onclick="materialplanning_fn()">Material Planning</a></li>
                        <li><a href="#hrplanning" data-toggle="tab" onclick="hrplanning_view_fn()">HR Planning</a></li>
                        <li><a href="#equipmentplanning" data-toggle="tab" onclick="equipmentplanning_view_fn()">Equipment
                                Planning</a></li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div class="tab-pane  active" id="materialplanning">

                        <div class="row ">
                            <div class="col-md-12">
                                <div id="materialplanning_view"></div>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane" id="hrplanning">
                        <div id="hrplanning_view"></div>
                    </div>

                    <div class="tab-pane" id="equipmentplanning">
                        <div id="equipmentplanning_view"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<div aria-hidden="true" role="dialog" id="asset_detail_modal" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width: 40%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add Asset</h4>
            </div>
            <form role="form" id="asset_detail_form" class="form-horizontal">
                <div class="modal-body">

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-6">
                            <label class="title" style="width: 80%">Equipment Type</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('equipmenttype', array('' => 'Select Type', '1' => 'Own', '2' => 'Rented'), '', 'class="form-control select2 equipmenttype" id="equipmenttype"'); ?>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row hide assetdrop" style="margin-top: 10px;">
                        <div class="form-group col-sm-6">
                            <label class="title" style="width: 80%">Asset</label>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('asset_drop', get_all_asset_drop(1), '', 'class="form-control select2 asset_drop" id="asset_drop" required'); ?>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row hide assettext" style="margin-top: 10px;">
                        <div class="form-group col-sm-6">
                            <label class="title" style="width: 80%">Asset</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="assettext_field" name="assettext_field">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" title="Clear" rel="tooltip"
                                            onclick="clear_asset_field()" style="height: 29px; padding: 2px 10px;"><i
                                                class="fa fa-repeat"></i></button>
                                    <button class="btn btn-default" type="button" title="Add Vehicle" rel="tooltip"
                                            onclick="asset_link_modal()" style="height: 29px; padding: 2px 10px;"><i
                                                class="fa fa-plus"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row hide assettext" style="margin-top: 10px;">
                        <div class="form-group col-sm-6">
                            <label class="title" style="width: 80%">Supplier</label>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <?php echo form_dropdown('supplierdrop', all_supplier_drop(TRUE, 1), '', 'class="form-control select2 supplierdrop" id="supplierdrop" required'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="row hide assettext" style="margin-top: 10px;">
                        <div class="form-group col-sm-6">
                            <label class="title" style="width: 80%">Equpiment Cost Type</label>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('equpimentcosttype', array('' => 'Select Equipment Type', '1' => 'Hour', '2' => 'Per Day'), '', 'class="form-control select2 equpimentcosttype" id="equpimentcosttype" required'); ?>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row hide assettext" style="margin-top: 10px;">
                        <div class="form-group col-sm-6">
                            <label class="title" style="width: 80%">Rented Periods In Days/In Hours</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <input type="text" class="form-control" id="rentedperiods" name="rentedperiods">
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row hide assettext" style="margin-top: 10px;">
                        <div class="form-group col-sm-6">
                            <label class="title" style="width: 80%">Per Day/Hour Cost</label>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <span class="input-req" title="Required Field">
                                <input type="text" class="form-control" id="perhcost" name="perhcost">
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="row hide assettext" style="margin-top: 10px;">
                        <div class="form-group col-sm-6">
                            <label class="title" style="width: 80%">With Operator</label>
                        </div>
                        <div class="form-group col-sm-6 ">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('withoperator', array('' => 'Select Status', '1' => 'Yes', '2' => 'No'), '', 'class="form-control select2 withoperator" id="withoperator" required'); ?>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button>
                    <!--Close-->
                    <button class="btn btn-primary" type="button"
                            onclick="save_asset_detail()"><?php echo $this->lang->line('common_save_change'); ?>
                    </button>
                    <!--Save changes-->
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="projecttimeline">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="usergroup-title">Add Project Time Line</h4>
            </div>
            <?php echo form_open('', 'role="form" id="projecttimeline_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="hederIDtimeline" name="hederIDtimeline">
                <input type="hidden" id="timelineID" name="timelineID">


                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Phase Description</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" name="phasedescription" id="phasedescription" class="form-control">
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="title">Planned Completion Date</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="plannedsubdate" value="" id="plannedsubdate"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>

                        </span>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" onclick="save_projecttimeline()" class="btn btn-sm btn-primary"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?>
                        <!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="recoveryplantime_modal">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="usergroup-title">Recovery Plan</h4>
            </div>
            <?php echo form_open('', 'role="form" id="recoveryplan_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="hederIDrecoveryplan" name="hederIDrecoveryplan">
                <input type="hidden" id="phaseID" name="phaseID">


                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Recovery Due to</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('recoverydueto', array('' => 'Select Reason', '1' => 'Delay by client', '2' => 'Delay by Contractor', '3' => 'Due to other reason'), '', 'class="form-control select2" id="recoverydueto"'); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Description of the Delay</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <textarea class="form-control" id="descriptionofthedelay" name="descriptionofthedelay"
                                  rows="3"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Recovery Plan Description</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <textarea class="form-control" id="recoveryplandescription" name="recoveryplandescription"
                                  rows="3"></textarea>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <header class="head-title">
                            <h2>Cost Impact</h2>
                        </header>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label class="title">Additional Material Required</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" name="additionalmaterial" id="additionalmaterial" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-3">
                        <label class="title">Additional HR required</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" name="additionalhr" id="additionalhr" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label class="title">Other</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" name="otherreq" id="otherreq" class="form-control">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <header class="head-title">
                            <h2>Attachment</h2>
                        </header>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="attachments_view_recoveryplan"></div>
                    </div>
                </div>
                <br>
                <div class="modal-footer">
                    <button type="button" onclick="save_recoveryplan()" class="btn btn-sm btn-primary"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?>
                        <!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="change_req_modal">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="usergroup-title">CHANGE REQUESTS</h4>
            </div>
            <?php echo form_open('', 'role="form" id="changerequests_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="hederidchangerequests" name="hederidchangerequests">
                <input type="hidden" id="changereqID" name="changereqID">
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">CR#</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" class="form-control" id="cr" name="cr">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Type of CR</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('typeofcr', array('' => 'Select Reason', '1' => 'Enhancement', '2' => 'Defect'), '', 'class="form-control select2" id="typeofcr"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Submitter Name</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" class="form-control" id="submittername" name="submittername">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Breif Description of Request</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <textarea class="form-control" id="breifdescriptionofrequest" name="breifdescriptionofrequest"
                                  rows="3"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Date Submitted</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="datesubmitted" value="" id="datesubmitted"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Date Required</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="daterequired" value="" id="daterequired"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Priority</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('priority', array('' => 'Select Priority', '1' => 'Low', '2' => 'Medium', '3' => 'High', '4' => 'Mandatory'), '', 'class="form-control select2" id="priority"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Reason for change</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <textarea class="form-control" id="reasonofchange" name="reasonofchange" rows="3"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Assumptions And Notes</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <textarea class="form-control" id="assumptionsandnotes" name="assumptionsandnotes"
                                  rows="3"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Comments</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <textarea class="form-control" id="commentschangereq" name="commentschangereq"
                                  rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="save_changerequests()" class="btn btn-sm btn-primary"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?>
                        <!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="initial_req_modal">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"
                                                                                                  onclick="fetch_record_exit()">&times;</span>
                </button>
                <h4 class="modal-title" id="usergroup-title">INITIAL ANALYSIS</h4>
            </div>
            <?php echo form_open('', 'role="form" id="initialanalysis_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="hederidinitial" name="hederidinitial">
                <input type="hidden" id="changereqID_initial" name="changereqID_initial">
                <input type="hidden" id="labourcost_initial" name="labourcost_initial" value="0">
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">CR Code</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div id="div_cr_code">
                            <select name="crcode" class="form-control" id="crcode">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Category </label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div id="div_boqdetail">
                            <select name="boqdetail_changereq" class="form-control" id="boqdetail_changereq">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Sub Category</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div id="div_boqdetail_subcatergory">
                            <select name="boqdetail_changereq_sub" class="form-control" id="boqdetail_changereq_sub">
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Hour Impact</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" class="form-control" id="hourimpact" name="hourimpact"
                               onkeypress="return validateFloatKeyPress(this,event);">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Duration Impact</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" class="form-control" id="durationimpact" name="durationimpact">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Schedule Impact</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" class="form-control" id="scheduleimpact" name="scheduleimpact"
                               onkeypress="return validateFloatKeyPress(this,event);">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Cost Impact</label>
                    </div>
                    <div class="form-group col-sm-5">
                        <input type="text" class="form-control" id="costImpact" name="costImpact"
                               onkeypress="return validateFloatKeyPress(this,event);" readonly>

                    </div>
                    <div class="form-group col-sm-1" style="margin-left: -3%">
                        <button type="button" class="btn btn-primary" onclick="open_cost_impact_model()"><i
                                    class="fa fa-plus"></i></button>
                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Comments</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <textarea class="form-control" id="commentsinitial" name="commentsinitial" rows="3"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Recommendations</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <textarea class="form-control" id="recommendations" name="recommendations" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="save_changerequestsinitial()" class="btn btn-sm btn-primary"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?>
                        <!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="initial_control_modal">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="usergroup-title">CHANGE CONTROL BOARD</h4>
            </div>
            <?php echo form_open('', 'role="form" id="changecontrolboard_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="hederidchangecontrol" name="hederidchangecontrol">
                <input type="hidden" id="changereqID_change" name="changereqID_change">
                <input type="hidden" id="approvalLevelID" name="approvalLevelID">
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">CR Code</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div id="div_cr_code_changecontrol">
                            <select name="crcode_changeboard" class="form-control" id="crcode_changeboard">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row commondecission">
                    <div class="form-group col-sm-3 col-md-offset-1 ">
                        <label class="title">Decision</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('decision', array('' => 'Select Decision', '5' => 'Confirm And Approve', '6' => 'Sent For Approval', '1' => 'Approved', '2' => 'Approved With Conditions', '3' => 'Rejected', '4' => 'More Info'), '', 'class="form-control select2" id="decision"'); ?>
                    </div>
                </div>
                <div class="row hide specialapprovaldecisiion">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Decision</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('decision_spapproval', array('' => 'Select Decision', '1' => 'Approved', '2' => 'Approved With Conditions', '3' => 'Rejected', '4' => 'More Info'), '', 'class="form-control select2" id="decision_spapproval"'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Decision Date</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="input-group datepic">
                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                            <input type="text" name="decisiondate" value="" id="decisiondate"
                                   class="form-control dateFields"
                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Decision Explanation</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <textarea class="form-control" id="decisionexplanation" name="decisionexplanation"
                                  rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="save_approval()"
                            class="btn btn-sm btn-primary hide specialapprovaldecisiion"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?>
                        <!--Save-->
                    </button>

                    <button type="button" onclick="save_changerequestscontrolboard()"
                            class="btn btn-sm btn-primary hide commondecission"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?>
                        <!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="project_closure_template">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="usergroup-title">Add New Template</h4>
            </div>
            <?php echo form_open('', 'role="form" id="project_closure_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="hederidprojectclosure" name="hederidprojectclosure">
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Description</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <input type="text" class="form-control" id="projectclosuredescription"
                               name="projectclosuredescription">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Template</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <?php echo form_dropdown('checklisttemplate', check_list_temp(), '', 'class="form-control select2" id="checklisttemplate"'); ?>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" onclick="save_projectclosuretemp()" class="btn btn-sm btn-primary"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?>
                        <!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="project_closure_template_view">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="usergroup-title"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="project_closure_template"'); ?>
            <div class="modal-body">
                <div id="fetch_projectclosureview">

                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="asset_link_modal" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="exampleModalLabel">Link Asset</h4>
                <!--Link Employee-->
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Asset </label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('asset_text_id', get_all_asset_drop(2), '', 'class="form-control select2" id="asset_text_id" onchange=""  required');
                            ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="fetch_asset_detail()" class="btn btn-primary">Add Asset</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="excelUpload_Modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Equipment upload form</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php echo form_open_multipart('', 'id="equipmentplanningUpload_form" class="form-inline"'); ?>
                    <input type="hidden" name="boqheaderID" id="boqheaderID">
                    <input type="hidden" name="boqdetailID" id="boqdetailID">
                    <div class="col-sm-12" style="margin-left: -10%">
                        <div class="form-group col-sm-4">
                            <label class="title" style="width: 100%">Supplier</label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('supplierupform', all_supplier_drop(TRUE, 1), '', 'class="form-control select2 supplierupform" id="supplierupform"'); ?>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-left: 24%">
                        <div class="form-group">
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                 style="margin-top: 8px;">
                                <div class="form-control" data-trigger="fileinput" style="min-width: 200px; width: 100%;
                                    border-bottom-left-radius: 3px !important; border-top-left-radius: 3px !important; ">
                                    <i class="glyphicon glyphicon-file color fileinput-exists"></i>
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new"><span class="glyphicon glyphicon-plus"
                                                                      aria-hidden="true"></span></span>
                                    <span class="fileinput-exists"><span class="glyphicon glyphicon-repeat"
                                                                         aria-hidden="true"></span></span>
                                    <input type="file" name="excelUpload_file" id="excelUpload_file" accept=".csv">
                                </span>
                                <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id_excelup"
                                   data-dismiss="fileinput">
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                </a>
                            </div>
                        </div>
                        <button type="button" class="btn btn-default" onclick="excel_upload_equipment()">
                            <span class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div class="col-sm-12" style="margin-left: 3%; color: red">
                        <?php echo '- Please make sure the file you are going to upload is in the format of upload template' ?>
                        <br/>
                        <?php echo '- Only CSV file can be uploaded' ?>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?>
                </button>
            </div>
            <form role="form" id="downloadTemplate_form">
            </form>

        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel" id="linkassetmodel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Link Asset</h4>
            </div>
            <?php echo form_open('', 'role="form" id="linkasset_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="activityplanningID" name="activityplanningID">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Link Asset</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                            <?php
                            echo form_dropdown('assetlinkID', get_all_asset_drop(2), '', 'class="form-control select2" id="assetlinkID"  required');
                            ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-3 col-md-offset-1">
                        <label class="title">Supplier</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                            <?php
                            echo form_dropdown('supplierlinkasset', all_supplier_drop(TRUE, 1), '', 'class="form-control select2" id="supplierlinkasset"  required');
                            ?>
                            <span class="input-req-inner"></span>
                        </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="save_linkasset();" class="btn btn-sm btn-primary"><span
                                class="glyphicon glyphicon-floppy-disk"
                                aria-hidden="true"></span> <?php echo $this->lang->line('common_save') ?>
                        <!--Save-->
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div aria-hidden="true" role="dialog" tabindex="-1" id="initalanalysiscost" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 id="" class="modal-title"><?php echo $this->lang->line('common_cost'); ?>
                    <!--Cost-->
                </h5>
            </div>

            <form role="form" id="boq_cost_form_sheet_initialanalysis" class="form-horizontal">
                <div class="modal-body" style="overflow: auto;max-height: 400px">
                    <input type="hidden" name="categoryIDinitialanalysis" id="categoryIDinitialanalysis">
                    <input type="hidden" name="subcategoryIDinitialanalysis" id="subcategoryIDinitialanalysis">
                    <input type="hidden" name="customerCurrencyIDinitialanalysis"
                           id="customerCurrencyIDinitialanalysis">
                    <input type="hidden" name="itemautoidprojectinitialanalysis" id="itemautoidprojectinitialanalysis">
                    <header class="head-title">
                        <h2>Labour Costs</h2>
                    </header>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    Labour Costs</label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="labourcosts" id="labourcosts"
                                               placeholder="Labour Costs">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6" style="margin-left: -20%">
                            <button type="button" class="btn btn-primary" onclick="save_labourcostdetail()">Save
                            </button>
                        </div>

                    </div>
                    <br>
                    <header class="head-title">
                        <h2>Machine Cost</h2>
                    </header>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_item'); ?>
                                    <!--Item--></label>
                                <div class="  col-sm-9">
                                    <div class="input-group">

                                        <input readonly type="text" class="form-control col-sm-6 " name="search"
                                               id="searchintial" placeholder="Item ID, Item Description...">
                                        <div onclick="itemSearchModal_initialanalysis()" class="input-group-addon"><i
                                                    class="	glyphicon glyphicon-plus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_uom'); ?>
                                    <!--UOM--></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" readonly id="uomintial" name="uom"
                                           placeholder="UOM">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('promana_pm_total_cost'); ?>
                                    <!--Total Cost--></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" readonly id="totalcostintial"
                                           name="totalcost" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label"></label>
                                <div class="col-sm-4">
                                    <button type="button" onclick="save_initialanalysis_cost()"
                                            class="btn btn-xs btn-primary" id="submitcostsheetintial">
                                        <?php echo $this->lang->line('common_add'); ?>
                                        <!--Add-->
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_qty'); ?>
                                    <!--Qty--></label>
                                <div class="col-sm-4">
                                    <input type="number" step="any" class="form-control" value="0"
                                           onchange="calculateinitial()" id="qtyintial" name="qty" placeholder="Qty">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vehicle" class="col-sm-3 control-label">
                                    <?php echo $this->lang->line('common_unit_cost'); ?>
                                    <!--Unit Cost--></label>
                                <div class="col-sm-9">
                                    <input type="number" step="any" class="form-control" id="unitcostintial"
                                           onchange="calculateinitial()" name="unitcost" placeholder="Unit Cost">
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div id="loadcostsheettableintial"></div>


                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">
                        <?php echo $this->lang->line('common_Close'); ?>
                        <!--Close--></button>

                </div>


        </div>
        </form>
    </div>
</div>


<div style="z-index: 1000000000" aria-hidden="true" role="dialog" tabindex="-1" id="itemSeachModal_initialanalaysis"
     class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 id="" class="modal-title">
                    <?php echo $this->lang->line('promana_common_item_search'); ?>
                    <!--Item Search-->
                </h5>
            </div>

            <div class="modal-body">
                keyword <input type="text" id="searchKeyword_initialanalysis"
                               onkeyup="searchByKeyword_initializealaysis()"/> <span id="loader_itemSearch"
                                                                                     style="display: none;"><i
                            class="fa fa-refresh fa-spin"></i></span>
                <br>
                <br>

                <div>
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>

                            <th><?php echo $this->lang->line('promana_common_item_code'); ?>
                                <!--Item Code-->
                            </th>
                            <th><?php echo $this->lang->line('common_description'); ?>
                                <!--Description-->
                            </th>
                            <th><?php echo $this->lang->line('common_uom'); ?>
                                <!--UOM-->
                            </th>

                            <th><?php echo $this->lang->line('common_currency'); ?>
                                <!--Currency-->
                            </th>
                            <th><?php echo $this->lang->line('common_cost'); ?>
                                <!--Cost-->
                            </th>

                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody id="itemSearchResultTblBody_initial">

                        </tbody>
                    </table>
                </div>
            </div>


            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    <?php echo $this->lang->line('common_Close'); ?>
                    <!--Close--></button>

            </div>
        </div>

    </div>

</div>


<script type="text/javascript">
    var pID = null;
    var crID = null;
    var boqdetailID_initialan = null;
    var subcatergoryID = null;
    var equipmentID = null;
    var crchcontrolboard = null;
    var activetype = 0;
    $(document).ready(function () {
        $('#warrantyPeriod').numeric({
            decimal: false,
            negative: false
        });

        $('.extraColumnsgreen input').iCheck({
            checkboxClass: 'icheckbox_square_relative-green',
            radioClass: 'iradio_square_relative-green',
            increaseArea: '20%'
        });

        $(".select2").select2();
        $('.headerclose').click(function () {
            fetchPage('system/pm/boq', '', 'Project');
        });

        Inputmask().mask(document.querySelectorAll("input"));
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';

        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#boq_create_form').bootstrapValidator('revalidateField', 'prjStartDate');
            $('#boq_create_form').bootstrapValidator('revalidateField', 'prjEndDate');
            $('#boq_create_form').bootstrapValidator('revalidateField', 'documentdate');
        });
        $('.datepic_addtask').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#formProjectPlanning').bootstrapValidator('revalidateField', 'startDate');
            $('#formProjectPlanning').bootstrapValidator('revalidateField', 'endDate');
        });

        Inputmask().mask(document.querySelectorAll("input"));
        $("#currency").prop('disabled', false);
        $("#segement").prop('disabled', false);
        $("#customer").prop('disabled', false);
        $(".searchbox").select2();

        /*  var from = $('.from').datepicker({ autoclose: true }).on('changeDate', function(e){
         $('.to').datepicker({ autoclose: true}).datepicker('setStartDate', e.date).focus();
         });*/

        // $('#datepicker').datepicker();
        if ($('#headerID').val() != '') {
            $('.btn-wizard').addClass('disabled');
            //  $('#bapprovalmanagement option:not(:selected)').prop('disabled', true);
        } else {
            $('.btn-wizard').addClass('disabled');
        }


        if ($('#headerID').val() != '') {
            $('.addTableView').removeClass('hide');
            load_eoi_attachment();
            load_eoi_attachment('PROINS');
            load_tender_attachment();
            load_bid_attachment();
            load_budget_attachment();
            boq_attachment_view();
            getallsavedvalues($('#headerID').val());

        }


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

        $('#assignedEmployee').multiselect2({
            enableFiltering: true,
            /* filterBehavior: 'value',*/
            includeSelectAllOption: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,

        });

    });
    $(document).on('click', '.remove-tr', function () {
        $(this).closest('tr').remove();
    });

    function generateReportPdf() {
        var form = document.getElementById('boq_create_form');
        form.target = '_blank';
        form.action = '<?php echo site_url('Boq/get_project_pdf'); ?>';
        form.submit();
    }

    function searchByKeyword(initialSearch = null) {


        /*reset Cost form */
        $("#itemSearchResultTblBody").html('');
        var keyword = (initialSearch == null) ? $("#searchKeyword").val() : '-';


        $.ajax({
            async: true,

            data: {
                q: keyword,
                currency: $('#currency').val()
            },
            type: 'post',
            dataType: 'json',
            url: '<?php echo site_url('Boq/item_search'); ?>',
            beforeSend: function () {
                $("#itemSearchResultTblBody").html('');

                //startLoad();
            },
            success: function (data) {

                $("#itemSearchResultTblBody").html('');
                if (data == null || data == '') {

                } else {

                    $.each(data, function (i, v) {
                        ''
                        var tr_data = '<tr><td>' + v.itemSystemCode + '</td> <td>' + v.itemDescription + '</td> <td>' + v.defaultUnitOfMeasure + '</td> <td>' + v.subCurrencyCode + '</td> <td style="text-align: right">' + parseFloat(v.cost).toFixed(2) + '</td><td><button type="button" ' + 'onclick="fetchItemRow(\'' + v.itemSystemCode + '\',\'' + v.itemDescription + '\',\'' + v.defaultUnitOfMeasure + '\',\'' + v.subCurrencyCode + '\',' + parseFloat(v.cost).toFixed(2) + ',' + v.itemAutoID + ')" class="btn btn-xs btn-default"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add'); ?><!--Add--> </button></td></tr>';
                        $("#itemSearchResultTblBody").append(tr_data);
                    });
                }

            },
            error: function () {

                myAlert('e', 'Error while loading')
            }
        });


    }

    function addMasterTask() {
        $('#formProjectPlanning')[0].reset();
        $('#formProjectPlanning').bootstrapValidator('resetForm', true);
        $('#assignedEmployee').multiselect2('deselectAll', false);
        $('#assignedEmployee').multiselect2('updateButtonText');
        $('#percentage').prop('readonly', true);
        $('.projectphaseshow').removeClass('hide');
        $('#load_project_category').html('');
        $('#relatedprojectID').val(null).trigger("change");
        $('#project_category').val(null).trigger("change");
        load_project_category();
        project_planningSortOrder($('#headerID').val(), 1);
        load_project_phases($('#headerID').val());
        get_relatedproject($('#headerID').val());
        $('#projectPlannningID').val(0);
        $('#timelineID').val(0);
        $('#projectphase').val(null).trigger("change");
        $('#title').html('Add Task');
        $('#addMasterTask').modal('show');


        // loadTaskData($('#headerID').val());
    }

    function load_project_category() {
        if (pID) {
            $('#load_project_category').html('');
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    'projectID': pID
                },
                url: "<?php echo site_url('Boq/get_project_category'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#load_project_category').html(data);
                    $('#project_category').select2();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

    }

    function addplanningSub(projectPlannningID, title, timelineID) {

        $('#planningdescription').val('');
        $('#srp_erp_customerreceiptdetail').val('');
        $('#planningStartDate').val('');
        $('#planningEndDate').val('');
        $('#percentage').val('');
        $('#project_category').val(null).trigger('change');
        $('#color').val(null).trigger('change');
        $('#relatedprojectID').val(null).trigger('change');
        $('#relationship').val(null).trigger('change');
        $('#assignedEmployee').multiselect2('deselectAll', false);
        $('#assignedEmployee').multiselect2('updateButtonText');
        $('#percentage').prop('readonly', false);
        $('#projectPlannningID').val(projectPlannningID);
        $('#projectphase').val(timelineID);
        $('#title').html(title);
        get_relatedproject($('#headerID').val());
        load_project_category();
        $('.projectphaseshow').addClass('hide');
        $('#addMasterTask').modal('show');


        project_planningSortOrder(projectPlannningID, 2);
    }

    function get_relatedproject(projectPlannningID) {

        //$('#div_load_project').html('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                projectPlannningID: projectPlannningID
            },
            url: "<?php echo site_url('Boq/get_project_relatedtask'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_project').html(data);
                $('#relatedprojectID').select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function get_project(projectID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {
                projectID: projectID

            },
            url: "<?php echo site_url('Boq/get_project'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#currency').val(data['projectCurrencyID']).change();

                $('#prjEndDate').val(data['projectEndDate']);

                $('#prjStartDate').val(data['projectStartDate']);

                $('#segement').val(data['segmentID']).change();


            },
            error: function () {

            }
        });
    }

    function loadTaskData(headerID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID

            },
            url: "<?php echo site_url('Boq/loadTaskData'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadtaskData').html(data);


            },
            error: function () {

            }
        });

    }

    function project_planningSortOrder(headerID, url) {
        if (url == 1) {
            siteurl = '<?php echo site_url('Boq/project_planningSortOrder'); ?>';
        } else {
            siteurl = '<?php echo site_url('Boq/project_subplanningSortOrder'); ?>';
        }


        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {
                headerID: headerID

            },
            url: siteurl,
            beforeSend: function () {

            },
            success: function (data) {
                $('#planningSortOrder').val(data['sortOrder']);
            },
            error: function () {

            }
        });
    }

    $('#formProjectPlanning').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid'); ?>.',
        /*This value is not valid*/
        /* feedbackIcons: {
         valid: 'glyphicon glyphicon-ok',
         invalid: 'glyphicon glyphicon-remove',
         validating: 'glyphicon glyphicon-refresh'
         },*/
        excluded: [':disabled'],
        fields: {


            description: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('common_description_is_required'); ?>.'
                    }
                }
            },
            /*Description is required*/
            note: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_pm_note_is_required'); ?>.'
                    }
                }
            },
            /*note is required*/
            assignedEmployee: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_pm_assign_employee_is_required'); ?>.'
                    }
                }
            },
            /*Assign Employee is required*/
            startDate: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_pm_start_date_category_is_required'); ?>.'
                    }
                }
            },
            /*Start Date Cateogry is required*/
            endDate: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_pm_end_date_category_is_required'); ?>.'
                    }
                }
            },
            /*End Date Cateogry is required*/

        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({
            'name': 'headerID',
            'value': $('#headerID').val()
        });
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_projectPlanning'); ?>",
            beforeSend: function () {
                HoldOn.open({
                    theme: "sk-bounce",
                    message: "<h4> Please wait until page load! </h4>",
                });
            },
            success: function (data) {

                myAlert(data[0], data[1]);
                $('#save_btn_addtask').prop('disabled', false);
                if (data[0] == 's') {
                    $('#addMasterTask').modal('hide');
                }
                getchart();
                loadTaskData($('#headerID').val());
                HoldOn.close();
                refreshNotifications(true);
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
    });

    function getchart() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID

            },
            url: "<?php echo site_url('Boq/load_gantchart'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#gantchartview').html(data);


            },
            error: function () {

            }
        });

    }


    function fetchItemRow(itemSystemCode, itemDescription, defaultUnitOfMeasure, subCurrencyCode, cost, itemAutoID) {

        $('#search').val(itemDescription + '(' + itemSystemCode + ')');
        $('#uom').val(defaultUnitOfMeasure);
        $('#itemautoidproject').val(itemAutoID);
        $('#unitcost').val(cost);
        $('#searchKeyword').val('');
        $('#searchKeyword').trigger('onkeyup');
        $('#itemSeachModal').modal('hide');

    }


    function itemSearchModal() {
        $('#itemSeachModal').modal('show');
        $('#searchKeyword').val('');
        $('#searchKeyword').trigger('onkeyup');
    }

    function printpage() {
        url = "Boq/printBoqPdf/" + $('#headerID').val();
        window.open(url, '_blank');
    }


    function show_summary_data() {

        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: $('#headerID').val()

            },
            url: "<?php echo site_url('Boq/loadsummaryTable'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#summaryTable').html(data);


            },
            error: function () {

            }
        });
    }


    function modalheaderdetails(type) {
        $('#tendertype').val(type);
        $('#subcategoryID').val(null).trigger("change");
        $('#categoryID').val(null).trigger("change");
        $('#unitshortcode').val('');
        $('#boq_create_detail_header')[0].reset();
        $('#boq_create_detail_header').bootstrapValidator('resetForm', true);
        $("#modalheaderdetails").modal({
            backdrop: "static"
        });

        //form
        /**/
        loadCategory(pID);
    }

    function calculatetotalchangemarkup(id) {
        if ($('#markUp_' + id).val() == '') {
            $('#markUp_' + id).val(0);
        }

        markup = $('#markUp_' + id).val();
        totalcost = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        qty = $('#Qty_' + id).val();
        if ($('#Qty_' + id).val() == 0) {

            unit = $('#unitCostTranCurrency_' + id).val().replace(/,/g, "");
            labour = $('#totalLabourTranCurrency_' + id).val().replace(/,/g, "");
            totalcost = parseFloat(unit) + parseFloat(labour);


            unitrate = ((parseFloat(totalcost)) * (100 + parseFloat(markup))) / 100;
            $('#unitRateTransactionCurrency_' + id).val(unitrate);
        } else {
            unitrate = ((parseFloat(totalcost) / parseFloat(qty)) * (100 + parseFloat(markup))) / 100;
            $('#unitRateTransactionCurrency_' + id).val(unitrate);
        }


        qty = $('#Qty_' + id).val();
        unitrate = $('#unitRateTransactionCurrency_' + id).val().replace(/,/g, "");

        totalvalue = parseFloat(qty) * parseFloat(unitrate);
        $('#totalTransCurrency_' + id).val(totalvalue);

        savecalculatetotal(id, $('#Qty_' + id).val(), $('#unitRateTransactionCurrency_' + id).val().replace(/,/g, ""), $('#totalTransCurrency_' + id).val().replace(/,/g, ""), $('#markUp_' + id).val(),
            $('#totalCostTranCurrency_' + id).val().replace(/,/g, ""), $('#totalLabourTranCurrency_' + id).val().replace(/,/g, ""), $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, ""));


        unitRateTransactionCurrency = $('#unitRateTransactionCurrency_' + id).val().replace(/,/g, "");
        unitRateTransactionCurrency = parseFloat(unitRateTransactionCurrency).toFixed(2);
        $('#unitRateTransactionCurrency_' + id).val(commaSeparateNumber(unitRateTransactionCurrency));

        totalTransCurrency = $('#totalTransCurrency_' + id).val().replace(/,/g, "");
        totalTransCurrency = parseFloat(totalTransCurrency).toFixed(2);
        $('#totalTransCurrency_' + id).val(commaSeparateNumber(totalTransCurrency));

        totalLabourTranCurrency = $('#totalLabourTranCurrency_' + id).val().replace(/,/g, "");
        totalLabourTranCurrency = parseFloat(totalLabourTranCurrency).toFixed(2);
        $('#totalLabourTranCurrency_' + id).val(commaSeparateNumber(totalLabourTranCurrency));

        totalCostAmountTranCurrency = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        totalCostAmountTranCurrency = parseFloat(totalCostAmountTranCurrency).toFixed(2);
        $('#totalCostAmountTranCurrency_' + id).val(commaSeparateNumber(totalCostAmountTranCurrency));


    }


    // $('td').on('change', '.qty-input', function (e) {
    //     // var id=e.target.id.replace("Qty_", "");
    //     // calculateonchangqty(id);
    // });

    function calculateonchangqty(id) {
        if ($('#Qty_' + id).val() == '') {
            $('#Qty_' + id).val(0);
        }


        qty = $('#Qty_' + id).val();
        unitrate = $('#unitRateTransactionCurrency_' + id).val().replace(/,/g, "");
        /*1*/
        totalvalue = parseFloat(qty) * parseFloat(unitrate);
        $('#totalTransCurrency_' + id).val(totalvalue);

        qty = $('#Qty_' + id).val();
        unit = $('#unitCostTranCurrency_' + id).val().replace(/,/g, "");
        /*2*/
        total = parseFloat(qty) * parseFloat(unit);

        $('#totalCostTranCurrency_' + id).val(total);

        /*3*/


        total = $('#totalCostTranCurrency_' + id).val().replace(/,/g, "");
        ;


        totalcost = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        ;

        //This is wrong.
        // labour = parseFloat(totalcost) - parseFloat(total);
        // $('#totalLabourTranCurrency_' + id).val(labour);

        labour = $('#totalLabourTranCurrency_' + id).val().replace(/,/g, "");
        totalCostAmountTranCurrency = parseFloat(total) + parseFloat(labour);
        $('#totalCostAmountTranCurrency_' + id).val(totalCostAmountTranCurrency);


        totalTransCurrency = $('#totalTransCurrency_' + id).val().replace(/,/g, "");
        totalTransCurrency = parseFloat(totalTransCurrency).toFixed(2);
        $('#totalTransCurrency_' + id).val(commaSeparateNumber(totalTransCurrency));

        totalCostTranCurrency = $('#totalCostTranCurrency_' + id).val().replace(/,/g, "");
        totalCostTranCurrency = parseFloat(totalCostTranCurrency).toFixed(2);
        $('#totalCostTranCurrency_' + id).val(commaSeparateNumber(totalCostTranCurrency));

        totalCostAmountTranCurrency = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        totalCostAmountTranCurrency = parseFloat(totalCostAmountTranCurrency).toFixed(2);
        $('#totalCostAmountTranCurrency_' + id).val(commaSeparateNumber(totalCostAmountTranCurrency));

        savecalculatetotal(id, $('#Qty_' + id).val(), $('#unitRateTransactionCurrency_' + id).val(), $('#totalTransCurrency_' + id).val(), $('#markUp_' + id).val(),
            $('#totalCostTranCurrency_' + id).val(), $('#totalLabourTranCurrency_' + id).val(), $('#totalCostAmountTranCurrency_' + id).val());
        calculatetotalchangemarkup(id);


    }

    function calculatelabourcost(id) {
        if ($('#totalLabourTranCurrency_' + id).val() == '') {
            $('#totalLabourTranCurrency_' + id).val(0);
        }


        total = $('#totalCostTranCurrency_' + id).val().replace(/,/g, "");
        labour = $('#totalLabourTranCurrency_' + id).val().replace(/,/g, "");
        totalcost = parseFloat(total) + parseFloat(labour);

        var totalLabourTranCurrency = $('#totalLabourTranCurrency_' + id).val().replace(/,/g, "");
        totalLabourTranCurrency = parseFloat(totalLabourTranCurrency).toFixed(2);
        $('#totalLabourTranCurrency_' + id).val(commaSeparateNumber(totalLabourTranCurrency));

        $('#totalCostAmountTranCurrency_' + id).val(totalcost);
        var totalCostAmountTranCurrency = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        totalCostAmountTranCurrency = parseFloat(totalCostAmountTranCurrency).toFixed(2);
        $('#totalCostAmountTranCurrency_' + id).val(commaSeparateNumber(totalCostAmountTranCurrency));


        calculatetotalchangemarkup(id);


    }

    $('input:text[name=totalCostAmountTranCurrency]').keypress(function (event) {

        if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });

    $('input:text[name=totalLabourTranCurrency]').keypress(function (event) {

        if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
            event.preventDefault();
        }
    });

    function calculatetotalamount(id) {
        if ($('#totalCostAmountTranCurrency_' + id).val() == '') {
            $('#totalCostAmountTranCurrency_' + id).val(0);
        }

        a = $('#totalCostAmountTranCurrency_' + id).val();

        var numericReg = /^\s*?([\d\,]+(\.\d{1,3})?|\.\d{1,3})\s*$/;
        if (numericReg.test(a)) {

        } else {

        }

        totalcost = $('#totalCostAmountTranCurrency_' + id).val().replace(/,/g, "");
        total = $('#totalCostTranCurrency_' + id).val().replace(/,/g, "");
        labour = parseFloat(totalcost) - parseFloat(total);
        $('#totalLabourTranCurrency_' + id).val(labour);

        calculatetotalchangemarkup(id);

    }

    function ccalculatetotallabour(id) {
        if ($('#totalLabourTranCurrency_' + id).val() == '') {
            $('#totalLabourTranCurrency_' + id).val(0);
        }

        if ($('#totalCostAmountTranCurrency_' + id).val() == '') {
            $('#totalCostAmountTranCurrency_' + id).val(0);
        }


        labour = $('#totalLabourTranCurrency_' + id).val();
        tct = $('#totalCostTranCurrency_' + id).val();

        totalcost = parseFloat(tct) + parseFloat(labour);
        $('#totalCostAmountTranCurrency_' + id).val(totalcost);

        savelabourtotalcost(id, $('#totalLabourTranCurrency_' + id).val(), $('#totalCostAmountTranCurrency_' + id).val());


        $('#totalLabourTranCurrency_' + id).val(parseFloat($('#totalLabourTranCurrency_' + id).val()).toFixed(2));
        $('#totalCostAmountTranCurrency_' + id).val(parseFloat($('#totalCostAmountTranCurrency_' + id).val()).toFixed(2));


    }

    function ccalculatetotalamount(id) {
        if ($('#totalLabourTranCurrency_' + id).val() == '') {
            $('#totalLabourTranCurrency_' + id).val(0);
        }

        if ($('#totalCostAmountTranCurrency_' + id).val() == '') {
            $('#totalCostAmountTranCurrency_' + id).val(0);
        }
        tam = $('#totalCostAmountTranCurrency_' + id).val();
        tct = $('#totalCostTranCurrency_' + id).val();

        lc = parseFloat(tam) - parseFloat(tct);
        $('#totalLabourTranCurrency_' + id).val(lc);

        savelabourtotalcost(id, $('#totalLabourTranCurrency_' + id).val(), $('#totalCostAmountTranCurrency_' + id).val());


        $('#totalLabourTranCurrency_' + id).val(parseFloat($('#totalLabourTranCurrency_' + id).val()).toFixed(2));
        $('#totalCostAmountTranCurrency_' + id).val(parseFloat($('#totalCostAmountTranCurrency_' + id).val()).toFixed(2));


    }

    function calculatetotal(id) {
        if ($('#markUp_' + id).val() == '') {
            $('#markUp_' + id).val(0);
        }
        if ($('#Qty_' + id).val() == '') {
            $('#Qty_' + id).val(0);
        }


        if ($('#totalLabourTranCurrency_' + id).val() == '') {
            $('#totalLabourTranCurrency_' + id).val(0);
        }

        if ($('#totalCostAmountTranCurrency_' + id).val() == '') {
            $('#totalCostAmountTranCurrency_' + id).val(0);
        }


        u = $('#unitRateTransactionCurrency_' + id).val();
        q = $('#Qty_' + id).val();
        t = parseFloat(u) * parseFloat(q);


        $('#totalTransCurrency_' + id).val(t);

        q = $('#Qty_' + id).val();
        c = $('#unitCostTranCurrency_' + id).val();
        ct = parseFloat(c) * parseFloat(q);

        $('#totalCostTranCurrency_' + id).val(ct);
        calculatetotallabour(id);
        calculatetotalamount(id);

        m = $('#markUp_' + id).val();
        c = $('#unitCostTranCurrency_' + id).val();
        lb = $('#totalCostAmountTranCurrency_' + id).val();
        q = $('#Qty_' + id).val();

        ur = ((parseFloat(lb) / parseFloat(q)) * (100 + parseFloat(m))) / 100;


        $('#unitRateTransactionCurrency_' + id).val(ur);

        savecalculatetotal(id, q, u, $('#totalTransCurrency_' + id).val(), m, $('#totalCostTranCurrency_' + id).val(), $('#totalLabourTranCurrency_' + id).val(), $('#totalCostAmountTranCurrency_' + id).val());


        $('#unitRateTransactionCurrency_' + id).val(parseFloat($('#unitRateTransactionCurrency_' + id).val()).toFixed(2));
        $('#totalTransCurrency_' + id).val(parseFloat($('#totalTransCurrency_' + id).val()).toFixed(2));
        $('#totalCostTranCurrency_' + id).val(parseFloat($('#totalCostTranCurrency_' + id).val()).toFixed(2));
        $('#totalLabourTranCurrency_' + id).val(parseFloat($('#totalLabourTranCurrency_' + id).val()).toFixed(2));
        $('#totalCostAmountTranCurrency_' + id).val(parseFloat($('#totalCostAmountTranCurrency_' + id).val()).toFixed(2));


    }

    function savecalculatetotal(detailID, Qty, unitRateTransactionCurrency, totalTransCurrency, markUp, totalCostTranCurrency, totalLabourTranCurrency, totalCostAmountTranCurrency) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {
                detailID: detailID,
                Qty: Qty,
                unitRateTransactionCurrency: unitRateTransactionCurrency,
                totalTransCurrency: totalTransCurrency,
                markUp: markUp,
                totalCostTranCurrency: totalCostTranCurrency,
                totalLabourTranCurrency: totalLabourTranCurrency,
                totalCostAmountTranCurrency: totalCostAmountTranCurrency
            },
            url: "<?php echo site_url('Boq/saveboqdetailscalculation'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                setTimeout(function () {
                    loadheaderdetails();
                }, 500);
                fetch_posttender();
                refreshNotifications(true);

            },
            error: function () {
                stopLoad();
            }
        });
    }

    function savelabourtotalcost(detailID, totalLabourTranCurrency, totalCostAmountTranCurrency) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {
                detailID: detailID,
                totalLabourTranCurrency: totalLabourTranCurrency,
                totalCostAmountTranCurrency: totalCostAmountTranCurrency
            },
            url: "<?php echo site_url('Boq/savelabourtotalcost'); ?>",
            beforeSend: function () {

            },
            success: function (data) {

                refreshNotifications(true);

            },
            error: function () {

            }
        });
    }


    /*    $('#prjStartDate').datepicker({
     format: 'yyyy-mm-dd',

     });
     $('#prjEndDate').datepicker({
     format: 'yyyy-mm-dd',

     });

     $('#documentdate').datepicker({
     format: 'yyyy-mm-dd'
     });*/

    /*    $('#prjEndDate,#documentdate,#prjStartDate').datepicker({
     format: 'yyyy-mm-dd'
     }).on('changeDate', function(ev){
     $('#boq_create_form').bootstrapValidator('revalidateField', 'prjEndDate');
     $('#boq_create_form').bootstrapValidator('revalidateField', 'documentdate');
     $('#boq_create_form').bootstrapValidator('revalidateField', 'prjStartDate');
     $("#prjEndDate").datepicker("option","max", ev);
     $("#prjStartDate").datepicker("option","minDate", ev);
     $(this).datepicker('hide');
     });*/


    /*    $('#prjStartDate').on('changeDate', function (ev) {
     $('#boq_create_form').bootstrapValidator('revalidateField', 'prjStartDate');
     $("#prjEndDate").datepicker("option", "max", ev);
     $("#prjStartDate").datepicker('hide');
     });*/

    /* $('#prjStartDate').on('changeDate', function (ev) {
     alert();
     $('#boq_create_form').bootstrapValidator('revalidateField', 'prjStartDate');

     $(this).datepicker('hide');
     });*/

    /*    $('#documentdate').on('changeDate', function (ev) {

     $('#boq_create_form').bootstrapValidator('revalidateField', 'documentdate');

     $(this).datepicker('hide');
     });
     $('#prjEndDate').on('changeDate', function (ev) {
     $('#boq_create_form').bootstrapValidator('revalidateField', 'prjEndDate');
     $("#prjStartDate").datepicker("option", "minDate", ev);
     $(this).datepicker('hide');
     });*/


    function xloadheaderdetails() {
        var Otable = $('#loadheaderdetail').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "sAjaxSource": "<?php echo site_url('Boq/loadheaderdetailstable'); ?>",
            "bJQueryUI": true,
            "iDisplayStart ": 4,
            "sEcho": 1,
            "sAjaxDataProp": "aaData",
            "aaSorting": [
                [4, 'desc']
            ],

            "fnInitComplete": function () {

            },
            "fnDrawCallback": function () {
                $("[rel=tooltip]").tooltip();
            },
            "aoColumns": [

                {
                    "mData": "categoryName"
                },
                {
                    "mData": "subCategoryName"
                },
                {
                    "mData": "itemDescription"
                },
                {
                    "mData": "unitID"
                },
                {
                    "mData": "Qty"
                },
                {
                    "mData": "unitRateTransactionCurrency"
                },
                {
                    "mData": "totalTransCurrency"
                },

                {
                    "mData": "markUp"
                },
                {
                    "mData": "cost"
                },
                {
                    "mData": "totalCostTranCurrency"
                },
                {
                    "mData": "action"
                }

            ],
            //  "columnDefs": [ { "targets": [0,1,5], "orderable": false } ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "headerID",
                    "value": $("#headerID").val()
                });
                //aoData.push({ "name": "subcategory","value": $("#subcategory").val()});
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


    function loadheaderdetails() {

        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: $('#headerID').val(),

            },
            url: "<?php echo site_url('Boq/loadcostheaderdetailstable'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#loadheaderdetail').html(data);


            },
            error: function () {
                stopLoad();
            }
        });
    }

    function loadcostsheettable(detailID) {
        var tendertype = $('#tendertype_cost').val();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                detailID: detailID,
                tendertype: tendertype

            },
            url: "<?php echo site_url('Boq/loadboqcosttable'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#loadcostsheettable').html(data);


            },
            error: function () {

            }
        });

    }


    function calculate() {
        if ($('#unitcost').val() != '') {

            q = $('#qty1').val();
            u = $('#unitcost').val();

            t = u * q;

            x = t.toFixed(2);


            $('#totalcost1').val(x);

        } else {
            $('#totalcost1').val(0);
        }
    }

    function modalcostsheet(categoryID, subcategoryID, customerCurrencyID, detailID, pretenderConfirmedYN, tendertype) {

        $("#modalcostsheet").modal({
            backdrop: "static"
        });
        $('#categoryID1').val(categoryID);
        $('#subcategoryID1').val(subcategoryID);
        $('#customerCurrencyID').val(customerCurrencyID);
        $('#search').val('');
        $('#detailID').val(detailID);
        $('#tendertype_cost').val(tendertype);
        $('#qty1').val('');
        $('#unitcost').val('');
        $('#totalcost1').val('');
        $('#uom').val('');
        loadcostsheettable(detailID);

        if (pretenderConfirmedYN == 1) {
            $('#submitcostsheet').addClass('hide');
        } else {
            $('#submitcostsheet').removeClass('hide');
        }

        $("#currency1").val($("#currency option:selected").text());
    }

    function subcategorychange() {
        //  $('#unitID').select2('val', '');
        $('#unitID').val('');
        $('#unitshortcode').val('');

        var unitshortcode = $('#subcategoryID option:selected').attr('data-title');
        var unitID = $('#subcategoryID option:selected').attr('data-val');
        $('#unitID').val(unitID);
        $('#unitshortcode').val(unitshortcode);
        /*    $('#boq_create_detail_header').bootstrapValidator('revalidateField', 'subcategoryID');*/
        //$('#boq_create_detail_header').bootstrapValidator('revalidateField', 'unitshortcode');

    }

    function getSubcategory() {
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Boq/getSubcategoryDropDown'); ?>",
            data: {
                categoryID: $('#categoryID').val()
            },
            dataType: "json",
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {
                //$('#unitID').select2('val', '');
                $('#unitID').val('');
                $('#unitshortcode').val('');
                $('#subcategoryID').select2('data', null);


                $('#subcategoryID').empty();


                if (!jQuery.isEmptyObject(data)) {

                    var mySelect = $('#subcategoryID');
                    mySelect.append($('<option data-val=" " data-title=" " ></option>').val('').html('Select Subcategory'));
                    $.each(data, function (val, text) {
                        mySelect.append($('<option></option>').val(text['subCategoryID']).attr({
                            'data-val': text['unitID'],
                            'data-title': text['unitID']
                        }).html(text['description']));

                    });

                }
                //  $('#boq_create_detail_header').bootstrapValidator('revalidateField', 'unitshortcode');
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });

        return false;
    }


    function setcurrencycode(code) {

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Boq/getReportingCurrency'); ?>",
            data: {
                customerCode: $('#customer').val(),
                currency: $('#currency').val()
            },
            dataType: "json",
            cache: false,
            beforeSend: function () {

            },
            success: function (data) {

                $('#currency').select2('val', '');
                $('#currency').empty();
                if (!jQuery.isEmptyObject(data)) {

                    var mySelect = $('#currency');
                    // mySelect.append($('<option data-val=" "></option>').val('').html('Select Currency Conversion'));
                    $.each(data, function (val, text) {

                        mySelect.append($('<option></option>').val(text['currencyID']).html(text['CurrencyCode']));
                        $('#currency').select2('val', code);


                    });
                }

                $('#pcurrency').html($('#currency').select2('data')[0].text);
                $('#p2currency').html($('#currency').select2('data')[0].text);
                $('#currency').select2('disable');


            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return false;

    }

    function get_currency_code() {
        // var option = $('option:selected', this).attr('data-val');
        currency = $('#currncyconversion option:selected').attr('data-val');
        $('#currency').html(currency);


    }


    $('#boq_create_detail_header').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        /* feedbackIcons: {
         valid: 'glyphicon glyphicon-ok',
         invalid: 'glyphicon glyphicon-remove',
         validating: 'glyphicon glyphicon-refresh'
         },*/
        excluded: [':disabled'],
        fields: {


            description: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('common_description_is_required'); ?>.'
                    }
                }
            },
            /*Description is required*/
            unitshortcode: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_common_unit_is_required'); ?>.'
                    }
                }
            },
            /*Unit is required*/
            category: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('common_category_is_required'); ?>.'
                    }
                }
            },
            /*Category is required*/
            subcategory: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_common_sub_category_is_required'); ?>.'
                    }
                }
            } /*Sub Cateogry is required*/

        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({
            'name': 'headerID',
            'value': $('#headerID').val()
        });
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_header_details'); ?>",
            beforeSend: function () {
                HoldOn.open({
                    theme: "sk-bounce",
                    message: "<h4> Please wait until page load! </h4>",
                });
            },
            success: function (data) {
                /*$('#modalheaderdetails').modal('hide');*/
                /*   $form.bootstrapValidator('resetForm', true);*/
                $('#description').val('');
                loadheaderdetails();
                fetch_posttender();
                HoldOn.close();
                refreshNotifications(true);
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
    });

    $('#boq_create_form').bootstrapValidator({

        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {


            CompanyID: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_common_company_id_is_required'); ?>.'
                    }
                }
            },
            /*CompanyID is required*/
            segement: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('common_segment_is_required'); ?>.'
                    }
                }
            },
            /*Segment is required*/
            documentdate: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_pm_document_date_line_is_required'); ?>.'
                    }
                }
            },
            /*Document date line is required*/
            customer: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_pm_customer_name_is_required'); ?>.'
                    }
                }
            },
            /*Customer name is required*/
            currncyconversion: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('common_currency_is_required'); ?>.'
                    }
                }
            },
            /*Currency is required*/

            prjStartDate: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_pm_project_start_date_is_required'); ?>.'
                    }
                }
            },
            /*Project start date is required*/
            prjEndDate: {
                validators: {
                    notEmpty: {
                        message: '<?php echo $this->lang->line('promana_pm_project_end_date_is_required'); ?>.'
                    }
                }
            },
            /*Project end date is required*/
            projectname: {
                validators: {
                    notEmpty: {
                        message: 'Project Name is required.'
                    }
                }
            },
            /*Project end date is required*/
            // comments: {validators: {notEmpty: {message: 'Comments is required.'}}}


        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({
            'name': 'customerName',
            'value': $('#customer').select2('data')[0].text
        });
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_header'); ?>",
            beforeSend: function () {
                HoldOn.open({
                    theme: "sk-bounce",
                    message: "<h4> Please wait until page load! </h4>",
                });
            },
            success: function (data) {

                if (data[0] == 's') {

                    $('#save_submit').prop('disabled', false);
                    /*           $('.btn-wizard').removeClass('disabled');*/
                    $('#headerID').val(data[2]);

                    $('#eoi_documentSystemCode').val(data[2]);
                    $('#tender_documentSystemCode').val(data[2]);
                    $('#bid_documentSystemCode').val(data[2]);
                    $('#budget_documentSystemCode').val(data[2]);

                    $('#pcode').html($('#projectID').select2('data')[0].text + ' - ' + data[3]);
                    $('.panel-heading').html($('#projectID').select2('data')[0].text + ' - ' + data[3]);
                    $('#pcompany').html($('#projectID').select2('data')[0].text);
                    $('#psegement').html($('#segement').select2('data')[0].text);
                    $('#pcustomer').html($('#customer').select2('data')[0].text);

                    $('#pdocumentdate').html($('#documentdate').val());
                    $('#penddate').html($('#prjEndDate').val());
                    $('#pstartdate').html($('#prjStartDate').val());

                    $('#p2code').html($('#projectID').select2('data')[0].text + ' - ' + data[3]);
                    $('#p2company').html($('#projectID').select2('data')[0].text);
                    $('#p2segement').html($('#segement').select2('data')[0].text);
                    $('#p2customer').html($('#customer').select2('data')[0].text);

                    $('#p2documentdate').html($('#documentdate').val());
                    $('#p2enddate').html($('#prjEndDate').val());
                    $('#p2startdate').html($('#prjStartDate').val());

                    $('#pcurrency').html($('#currency').select2('data')[0].text);
                    $('#p2currency').html($('#currency').select2('data')[0].text);

                    pID = $('#projectID').val();

                    $('.addTableView').removeClass('hide');

                    load_eoi_attachment();
                    load_tender_attachment();
                    load_bid_attachment();
                    load_budget_attachment();
                    /*      $('[href=#step2]').tab('show');
                    $('.btn-wizard').removeClass('disabled');
                    $('a[data-toggle="tab"]').removeClass('btn-primary');
                    $('a[data-toggle="tab"]').addClass('btn-default');
                    $('[href=#step2]').removeClass('btn-default');
                    $('[href=#step2]').addClass('btn-primary');*/
                }

                myAlert(data[0], data[1]);

                HoldOn.close();
                refreshNotifications(true);
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
    });

    $('#boq_cost_form_sheet').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid'); ?>.',
        /*This value is not valid*/
        /*     feedbackIcons: {
         valid: 'glyphicon glyphicon-ok',
         invalid: 'glyphicon glyphicon-remove',
         validating: 'glyphicon glyphicon-refresh'
         },*/
        excluded: [':disabled'],
        fields: {

            //search: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_item_is_required'); ?>.'}}}, /*Item is required*/
            // uom: {validators: {notEmpty: {message: 'UOM is required.'}}},
            //qty: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_qty_is_required'); ?>.'}}}, /*Qty is required*/
            // unitcost: {validators: {notEmpty: {message: '<?php echo $this->lang->line('promana_pm_unit_cost_is_required'); ?>.'}}}, /*Unit Cost is required*/
            // totalcost: {validators: {notEmpty: {message: 'Currncy conversion is required.'}}},

        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({
            'name': 'headerID',
            'value': $('#headerID').val()
        });


        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_cost_sheet'); ?>",
            beforeSend: function () {
                HoldOn.open({
                    theme: "sk-bounce",
                    message: "<h4> Please wait until page load! </h4>",
                });
            },
            success: function (data) {
                if (data[0] == 's') {
                    loadheaderdetails();
                    fetch_posttender();
                    loadcostsheettable($('#detailID').val());
                    $('#totalcost1').val(0);
                    $('#uom').val('');
                    $form.bootstrapValidator('resetForm', true);
                    // updateunitratebymarkup($('#detailID').val());

                    //$('#headerID').val(data['last_id']);
                }


                HoldOn.close();
                refreshNotifications(true);
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
    });

    function updateunitratebymarkup(detailID) {

        $('#unitRateTransactionCurrency_1').val(1);
        $('#unitCostTranCurrency_1').val(2)


    }

    function loadCategory(projectID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                'projectID': projectID
            },
            url: "<?php echo site_url('Boq/loadCategory'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#htmlCategory').html(data);
                $('#categoryID').select2();


            },
            error: function () {


                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/

            }
        });

    }


    function getallsavedvalues(headerID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/getallsavedvalues'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    pID = data['projectID'];
                    $('#currency').val(data['customerCurrencyID']).change();

                    $('#projectID').val(data['projectID']).change();
                    $('#customertype').val(data['customerType']).change();
                    $("#projectID").prop('disabled', true);
                    $('#documentdate').val(data['projectDocumentDate']);

                    $('#eoistatus').val(data['eoistatus']).change();
                    $('#eoisubdate').val(data['eoisubmissiondate']);

                    $('#policyDescription').val(data['insPolicyDes']);
                    $('#policyDateFrom').val(data['insPolicyDateFrom']);
                    $('#policyDateTo').val(data['insPolicyDateTo']);

                    $('#tenderreferenceno').val(data['tenderreferenceno']);
                    $('#tendervalue').val(data['tendervalue']);
                    $('#tenderstatus').val(data['tenderstatus']).change();


                    if ((data['tenderstatus'] == 3) || ((data['tenderstatus'] == 4))) {
                        $('[href=#step2]').tab('show');
                        $('.btn-wizard').removeClass('disabled');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                        fetch_project_charter()
                        loadheaderdetails();
                    }


                    $('#tendersubmissiondate').val(data['tendersubmissiondate']);
                    $('#typeofcontract').val(data['typeofcontract']).change();
                    $('#commentsstatus').val(data['commentsstatus']);

                    $('#bapprovalinternalclient').val(data['budgetapprovalinternalclient']).change();
                    $('#budgetestimation').val(data['totalbudgetestimation']);

                    $('#descriptionofthecontract').val(data['descriptionofthecontract']);
                    $('#specialconditions').val(data['specialconditions']);

                    $('#bidsubmissiondate').val(data['bidsubmissiondate']);
                    $('#bidduedate').val(data['bidduedate']);
                    $('#bidexpirydate').val(data['bidexpirydate']);
                    $('#bidvalidityperiod').val(data['bidvalidityperiod']);
                    $('#bondvalue').val(data['bondvalue']);
                    if (data['companytosupplybidbond'] == 1) {
                        $('#active').iCheck('check');
                    } else {
                        $('#inactive').iCheck('check');
                    }
                    if (data['budgetapprovalmanagement'] != null) {
                        $("#bapprovalmanagement").val(data['budgetapprovalmanagement']).change();
                        $("#bapprovalmanagement").prop('disabled', true);
                    }
                    <?php if (!empty($isApprovalExist)) { ?>
                    $('.approvalstatus_cls').removeClass('hide');
                    <?php } ?>

                    $('#prjStartDate').val(data['projectDateFrom']);
                    $('#prjEndDate').val(data['projectDateTo']);
                    $('#comments').val(data['comment']);
                    $('#retentionpercentage').val(data['retensionPercentage']);
                    $('#advancepercentage').val(data['advancePercentage']);
                    $('#warrantyPeriod').val(data['warrantyPeriod']);
                    $('#customer').val(data['customerCode']).change();
                    $('#consultant').val(data['consultant']).change();
                    $('#segement').val(data['segementID']).change();
                    /*  $('#projectname').prop('disabled',true);*/
                    $('#projectname').attr("readonly", true);
                    $('#projectname').val(data['projectDescription']);
                    $('#pcode').html($('#projectID').select2('data')[0].text + ' - ' + data['projectCode']);
                    $('.panel-heading').html($('#projectID').select2('data')[0].text + ' - ' + data['projectCode']);
                    $('#pcompany').html($('#projectID').select2('data')[0].text);
                    $('#psegement').html($('#segement').select2('data')[0].text);
                    $('#pcustomer').html($('#customer').select2('data')[0].text);

                    $('#pdocumentdate').html($('#documentdate').val());
                    $('#penddate').html($('#prjEndDate').val());
                    $('#pstartdate').html($('#prjStartDate').val());
                    $("#segement").prop('disabled', true);
                    $("#customer").prop('disabled', true);
                    /* $('#customer').select2('disable');
                     $('#customer').select2('disable');*/

                    $('#p2code').html($('#projectID').select2('data')[0].text + ' - ' + data['projectCode']);
                    $('#p2company').html($('#projectID').select2('data')[0].text);
                    $('#p2segement').html($('#segement').select2('data')[0].text);
                    $('#p2customer').html($('#customer').select2('data')[0].text);

                    $('#p2documentdate').html($('#documentdate').val());
                    $('#p2enddate').html($('#prjEndDate').val());
                    $('#p2startdate').html($('#prjStartDate').val());
                    $('#pcurrency').html($('#currency').select2('data')[0].text);
                    $('#p2currency').html($('#currency').select2('data')[0].text);
                    $("#currency").prop('disabled', true);
                    $('.confirmYNbtn').show();
                    if (data['confirmedYN'] == 1) {
                        $('.confirmYNbtn').hide();
                        $('.saveandnext').hide();
                        $('.editview').hide();
                    }
                    if (data['pretenderConfirmedYN'] != 1) {
                        $('#post-tender_hide').addClass('hide');

                    } else {
                        $('#post-tender_hide').removeClass('hide');
                    }
                }
                HoldOn.close();
                refreshNotifications(true);


            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
    }

    function deleteBoqCost(costingID, detailID) {
        if (costingID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*Are you sure?*/
                    text: "<?php echo $this->lang->line('promana_common_you_will_not_be_able'); ?>",
                    /*Your will not be able to recover this data*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_delete_it'); ?>",
                    /*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'costingID': costingID,
                            'detailID': detailID
                        },
                        url: "<?php echo site_url('Boq/deleteboqcost'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce",
                                message: "<h4> Please wait until page load! </h4>",
                            });
                        },
                        success: function (data) {

                            loadcostsheettable($('#detailID').val());
                            loadheaderdetails();
                            fetch_posttender();
                            HoldOn.close();
                            refreshNotifications(true);

                        },
                        error: function () {

                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                            /*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        }
        ;
    }

    function deleteBoqdetail(detailID) {
        if (detailID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete'); ?>",
                    /*Your want to delete this record*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_delete_it'); ?>",
                    /*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'detailID': detailID
                        },
                        url: "<?php echo site_url('Boq/deleteboqdetail'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce",
                                message: "<h4> Please wait until page load! </h4>",
                            });
                        },
                        success: function (data) {
                            HoldOn.close();
                            myAlert(data[0], data[1]);
                            loadheaderdetails();
                            fetch_posttender();
                        },
                        error: function () {
                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                            /*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        }
        ;
    }


    function confirm_boq() {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "<?php echo $this->lang->line('promana_common_you_will_not_be_able_to_change'); ?>",
                /*Your will not be able to changes in this file!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_confirm_it'); ?>",
                /*Yes, Confirm it!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'headerID': $('#headerID').val()
                    },
                    url: "<?php echo site_url('Boq/confirm_boq'); ?>",
                    success: function (data) {
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetchPage('system/pm/boq', '', 'Project');
                        }
                        ;
                    },
                    error: function () {
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function commaSeparateNumber(val) {
        while (/(\d+)(\d{3})/.test(val.toString())) {
            val = val.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
        }
        return val;
    }

    function varianceamount(amount, id) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: {
                'amount': amount,
                'detailID': id
            },
            url: "<?php echo site_url('Boq/udate_varianceamt'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                if (data[0] == 's') {

                }


                HoldOn.close();
                refreshNotifications(true);
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
    }

    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    function load_eoi_attachment(doc = 'PROEOI') {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID,
                'documentID': doc
            },
            url: "<?php echo site_url('Boq/fetch_eoi_attachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (doc == 'PROINS') {
                    $('#insurance_multiple_attachemts').html(data);
                } else {
                    $('#eoi_multiple_attachemts').html(data);
                }

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function save_eoi_bid() {
        var data = $('#eoi_tender_add_edit_form').serializeArray();

        data.push({
            name: 'policyDescription',
            value: $('#policyDescription').val()
        });
        data.push({
            name: 'insPolicyDateFrom',
            value: $('#policyDateFrom').val()
        });
        data.push({
            name: 'insPolicyDateTo',
            value: $('#policyDateTo').val()
        });

        data.push({
            name: 'tenderreferenceno',
            value: $('#tenderreferenceno').val()
        });
        data.push({
            name: 'tendervalue',
            value: $('#tendervalue').val()
        });
        data.push({
            name: 'tenderstatus',
            value: $('#tenderstatus').val()
        });
        data.push({
            name: 'headerID',
            value: $('#headerID').val()
        });
        data.push({
            name: 'tendersubmissiondate',
            value: $('#tendersubmissiondate').val()
        });
        data.push({
            name: 'typeofcontract',
            value: $('#typeofcontract').val()
        });
        data.push({
            name: 'commentsstatus',
            value: $('#commentsstatus').val()
        });
        data.push({
            name: 'descriptionofthecontract',
            value: $('#descriptionofthecontract').val()
        });
        data.push({
            name: 'specialconditions',
            value: $('#specialconditions').val()
        });

        data.push({
            name: 'bidsubmissiondate',
            value: $('#bidsubmissiondate').val()
        });
        data.push({
            name: 'bidduedate',
            value: $('#bidduedate').val()
        });
        data.push({
            name: 'bidexpirydate',
            value: $('#bidexpirydate').val()
        });
        data.push({
            name: 'bidvalidityperiod',
            value: $('#bidvalidityperiod').val()
        });
        data.push({
            name: 'bondvalue',
            value: $('#bondvalue').val()
        });
        data.push({
            name: 'consultant',
            value: $('#consultant').val()
        });
        data.push({
            name: 'budgetestimation',
            value: $('#budgetestimation').val()
        });
        data.push({
            name: 'bapprovalinternalclient',
            value: $('#bapprovalinternalclient').val()
        });
        data.push({
            name: 'active',
            value: activetype
        });
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_eoi_tender'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                myAlert(data[0], data[1], data[2])
                if (data[0] == 's') {
                    if ((data[2] == 3) || ((data[2] == 4))) {
                        fetch_project_charter();
                        $('[href=#step2]').tab('show');
                        $('.btn-wizard').removeClass('disabled');
                        $('a[data-toggle="tab"]').removeClass('btn-primary');
                        $('a[data-toggle="tab"]').addClass('btn-default');
                        $('[href=#step2]').removeClass('btn-default');
                        $('[href=#step2]').addClass('btn-primary');
                    }
                }

                HoldOn.close();
                refreshNotifications(true);
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
    }

    function document_uplode_eoi() {
        var formData = new FormData($("#eoi_attachment_upload")[0]);
        var masterAutoID = $('#headerID').val();
        $('#eoi_documentSystemCode').val(masterAutoID);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id_eoi').click();
                    $('#eoiattachmentDescription').val('');
                    load_eoi_attachment();

                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;

    }

    function delete_eoi_attachment(id, fileName) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to Delete",
                /*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'attachmentID': id,
                        'myFileName': fileName
                    },
                    url: "<?php echo site_url('Attachment/delete_attachments_AWS_s3'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            load_eoi_attachment();
                            load_eoi_attachment('PROINS');
                            load_tender_attachment();
                            load_bid_attachment();
                            boq_attachment_view();
                            load_budget_attachment();
                            changereq_attachment_view();
                            fetch_projectclosure();
                            fetch_qs_comment_attachment();
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function delete_recovery_attachment(id, fileName, subid) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to Delete",
                /*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'attachmentID': id,
                        'myFileName': fileName
                    },
                    url: "<?php echo site_url('Attachment/delete_attachments_AWS_s3'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            recovery_attachment_view(subid);
                        } else {
                            myAlert('e', 'Deletion Failed');
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function load_tender_attachment() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/fetch_tender_attachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#tender_multiple_attachemts').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function document_uplode_tender() {
        var formData = new FormData($("#tender_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id_tender').click();
                    $('#tenderattachmentDescription').val('');
                    load_tender_attachment();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;

    }

    function document_uplode_bid() {
        var formData = new FormData($("#bid_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id_bid').click();
                    $('#bidattachmentDescription').val('');
                    load_bid_attachment();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }

    function load_bid_attachment() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/fetch_bid_attachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#bid_multiple_attachemts').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    $('input').on('ifChecked', function (event) {
        if ($(this).hasClass('cheack_type')) {
            activetype = 1;
        }
    });
    $('input').on('ifChecked', function (event) {
        if ($(this).hasClass('cheack_type_uncheack')) {
            activetype = 0;
        }
    });

    function document_uplode_budget() {
        var formData = new FormData($("#budget_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id_budget').click();
                    $('#budgetattachmentDescription').val('');
                    load_budget_attachment();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }


    function load_budget_attachment() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/fetch_budget_attachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#budget_multiple_attachemts').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function save_draft() {
        var headerID = $('#headerID').val();
        if (headerID) {
            swal({
                    title: "Are you sure?",
                    text: "You want to save this document!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Save as Draft"
                },
                function () {
                    fetchPage('system/pm/boq', '', ' ');
                });
        }
    }

    $('#bapprovalmanagement').change(function () {
        if ((this.value == 2)) {
            var headerID = $('#headerID').val();
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*Are you sure?*/
                    text: "You want to Sent Approval",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                    /*Yes!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'headerID': headerID
                            },
                            url: "<?php echo site_url('Boq/save_sent_forapproval'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                myAlert(data[0], data[1], data[2])
                                if (data[0] == 's') {
                                    load_eoi_attachment();
                                    load_tender_attachment();
                                    load_bid_attachment();
                                    $("#bapprovalmanagement").prop('disabled', true);
                                    if (data[2] == 1) {
                                        $('.approvalstatus_cls').removeClass('hide');
                                    } else {
                                        $('.approvalstatus_cls').addClass('hide');
                                    }


                                }
                            },
                            error: function () {
                                stopLoad();
                                swal("Cancelled", "Your file is safe :)", "error");
                            }
                        });
                    } else {
                        $('#bapprovalmanagement').val(null).trigger("change");
                    }

                });
        }
    });

    $('#approvalstatus').change(function () {

        var status = this.value;
        var headerID = $('#headerID').val();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to change the status",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'headerID': headerID,
                            'status': status
                        },
                        url: "<?php echo site_url('Boq/save_approvalstatus'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if (data == true) {
                                load_eoi_attachment();
                                load_tender_attachment();
                                load_bid_attachment();
                                $("#bapprovalmanagement").prop('disabled', true);
                                $('.approvalstatus_cls').addClass('hide');
                                fetch_approved_value();
                                //  $("#bapprovalmanagement").val(status).change();
                            }
                        },
                        error: function () {
                            stopLoad();
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                } else {
                    $('#approvalstatus').val(null).trigger("change");
                }

            });

    });

    function fetch_approved_value() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'headerID': headerID
            },
            url: "<?php echo site_url('Boq/fetch_bd_data'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    if (data['bdconfirmedYNmn'] == 2) {
                        $("#bapprovalmanagement").val(4).change();
                    }
                    if ((data['bdconfirmedYNmn'] == 1) && (data['bdapprovedYNmn'] == 1)) {
                        $("#bapprovalmanagement").val(1).change();
                    }
                }
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function fetch_project_charter() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/fetch_project_charter'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#projectcharter_html').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function boq_attachment_view() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/fetch_boq_attachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#boq_attachments_view').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function open_projectteam_add(headerID) {
        $('#projectteam_master_form')[0].reset();
        $('#hederIDproject').val(headerID);

        $('#organization').val(null).trigger("change");
        $('#Employee').val(null).trigger("change");
        $('#organizationrole').val(null).trigger("change");
        $('#empname').val('');
        $('#teamidproject').val('');
        $('.empnameshow').addClass('hide');
        $('.empdrophideshow').addClass('hide');
        $('#projectteammodal').modal('show');
    }

    $('#organization').change(function () {
        if (this.value == 2) {
            $('.empdrophideshow').removeClass('hide');
            $('.empnameshow').addClass('hide');
            $('#empname').val(' ');
        } else {
            $('.empnameshow').removeClass('hide');
            $('.empdrophideshow').addClass('hide');
            $('#Employee').val(null).trigger("change");
        }
    });

    function save_projectteam() {
        var data = $('#projectteam_master_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_project_team'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1])
                if (data[0] == 's') {
                    fetch_project_charter();
                    $('#projectteammodal').modal('hide');
                }
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function edit_team(teamid) {
        $('#teamidproject').val(teamid);
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*/!*Are you sure?*!/*/
                text: "You want to edit this record!",
                /*You want to delete this record!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                /*Delete*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'teamid': teamid
                    },
                    url: "<?php echo site_url('Boq/fetch_project_team'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (!jQuery.isEmptyObject(data)) {
                            if (data['organization'] == 2) {
                                $('.empdrophideshow').removeClass('hide');
                                $('.empnameshow').addClass('hide');
                            } else {
                                $('.empnameshow').removeClass('hide');
                                $('.empdrophideshow').addClass('hide');
                            }
                            $('#empname').val(data['empName']);
                            $('#Employee').val(data['empid']).change();
                            $('#organization').val(data['organizationID']).change();
                            $('#organizationrole').val(data['roleID']).change();
                            $('#projectteammodal').modal('show');
                        }

                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function delete_team(teamid) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to Delete",
                /*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'teamid': teamid
                    },
                    url: "<?php echo site_url('Boq/delete_project_team'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetch_project_charter();
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function fetch_activityplanning(detailID, headerID, type) {
        $('#headerID').val(headerID);
        $('#boq_detailID').val(detailID);
        $('#type').val(type);
        materialplanning_fn();
        hrplanning_view_fn();
        equipmentplanning_view_fn();
        $('.materialplanning').addClass('active');
        $('#activityplanning_modal').modal('show');
    }

    function materialplanning_fn() {
        var headerID = $('#headerID').val();
        var boq_detailID = $('#boq_detailID').val();
        var type = $('#type').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID,
                'boq_detailID': boq_detailID,
                'type': type
            },
            url: "<?php echo site_url('Boq/fetch_boq_materialplanning'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#materialplanning_view').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function hrplanning_view_fn() {
        var headerID = $('#headerID').val();
        var boq_detailID = $('#boq_detailID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID,
                'boq_detailID': boq_detailID
            },
            url: "<?php echo site_url('Boq/fetch_boq_hrplanning'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#hrplanning_view').html(data);
                $(".searchbox").select2();
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function save_material() {
        var headerID = $('#headerID').val();
        var boq_detailID = $('#boq_detailID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'headerID': headerID,
                'boq_detailID': boq_detailID
            },
            url: "<?php echo site_url('Boq/save_material'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    materialplanning_fn();
                }
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function save_hr_planning() {
        var headerID = $('#headerID').val();
        var boq_detailID = $('#boq_detailID').val();
        var DesignationID = $('#DesignationID').val();
        var noofavailableheads = $('#noofavailableheads').val();
        var noofrequiredheads = $('#noofrequiredheads').val();
        var hrplanningtype = $('#hrplanningtype').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'headerID': headerID,
                'boq_detailID': boq_detailID,
                'DesignationID': DesignationID,
                'noofavailableheads': noofavailableheads,
                'noofrequiredheads': noofrequiredheads,
                'hrplanningtype': hrplanningtype
            },
            url: "<?php echo site_url('Boq/save_hrplanning'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    hrplanning_view_fn();
                }
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function delete_hr_plning(activityplanningID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to Delete",
                /*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'activityplanningID': activityplanningID
                    },
                    url: "<?php echo site_url('Boq/delete_hrplanning'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            hrplanning_view_fn();
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function equipmentplanning_view_fn() {
        var headerID = $('#headerID').val();
        var boq_detailID = $('#boq_detailID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID,
                'boq_detailID': boq_detailID
            },
            url: "<?php echo site_url('Boq/equipmentplanning_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#equipmentplanning_view').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function asset_detail_modal() {
        $('#asset_detail_form')[0].reset();
        equipmentID = null;
        $('#equipmenttype').val(null).change();
        $('#asset_drop').val(null).change();
        $("#asset_detail_modal").modal({
            backdrop: "static"
        });
        $('#asset_add_table tbody tr').not(':first').remove();

    }

    function add_more_asset() {
        $('select.select2').select2('destroy');
        var appendData = $('#asset_add_table tbody tr:first').clone();
        appendData.find('.asset_drop ').val(null).change();
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#asset_add_table').append(appendData);
        $(".select2").select2();
    }

    function save_asset_detail() {
        var data = $('#asset_detail_form').serializeArray();
        data.push({
            'name': 'headerID',
            'value': $('#headerID').val()
        });
        data.push({
            'name': 'boq_detailID',
            'value': $('#boq_detailID').val()
        });
        data.push({
            'name': 'asset_text_id',
            'value': $('#asset_text_id').val()
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_asset'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    equipmentplanning_view_fn();
                    $('#asset_detail_modal').modal('hide');
                    $('#asset_detail_form')[0].reset();
                    $('.select2').select2('');
                }


            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_equipment_plning(activityplanningID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to Delete",
                /*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'activityplanningID': activityplanningID
                    },
                    url: "<?php echo site_url('Boq/delete_equipment_plning'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            equipmentplanning_view_fn();
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function fetch_executionmoni() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID,
            },
            url: "<?php echo site_url('Boq/fetch_exe_plan'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#executionmonitoringandcontrol').html(data);
                // $('#monitoringandcontrol').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function fetch_executionmoni_control() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID,
            },
            url: "<?php echo site_url('Boq/fetch_exe_plan_monitoring_con'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#monitoringandcontrol').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function open_project_timeline(headerID) {
        $('#projecttimeline_master_form')[0].reset();


        $('#hederIDtimeline').val(headerID);
        $('#phasedescription').val('');
        $('#plannedsubdate').val('');
        $('#timelineID').val('');
        $('#projecttimeline').modal('show');
    }

    function save_projecttimeline(isValidated = 0) {
        var data = $('#projecttimeline_master_form').serializeArray();
        data.push({
            'name': 'dateValidate',
            'value': isValidated
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_projecttimeline'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data[0] !== 'w') {
                    myAlert(data[0], data[1]);
                }

                if (data[0] == 's') {
                    fetch_project_charter();
                    $('#projecttimeline').modal('hide');
                    $('#projecttimeline_master_form')[0].reset();
                    $('#phasedescription').val('');
                    $('#plannedsubdate').val('');
                    $('#timelineID').val('');
                } else if (data[0] === 'w') {
                    stopLoad();
                    bootbox.confirm({
                        title: '<i class="fa fa-exclamation-triangle "></i> Warning!',
                        message: data[1],
                        buttons: {
                            'cancel': {
                                label: 'Cancel',
                                className: 'btn-default pull-right'
                            },
                            'confirm': {
                                label: 'OK Proceed',
                                className: 'btn-primary pull-right bootBox-btn-margin'
                            }
                        },
                        callback: function (result) {
                            if (result) {
                                save_projecttimeline(1);
                            }
                        }
                    });
                }

            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function edit_phase(timelineID) {

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'timelineID': timelineID
            },
            url: "<?php echo site_url('Boq/fetch_timelinedetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#phasedescription').val(data['phaseDescription']);
                $('#plannedsubdate').val(data['plannedcompletionDate']);
                $('#timelineID').val(timelineID);
                $('#projecttimeline').modal('show');
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function delete_phase(timelineID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to Delete",
                /*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'timelineID': timelineID
                    },
                    url: "<?php echo site_url('Boq/delete_projecttimeline'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            fetch_project_charter();
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function open_recovery_plan(headerID, timelineID) {
        $('#hederIDrecoveryplan').val(headerID);
        $('#phaseID').val(timelineID);
        recovery_attachment_view(timelineID);
        $('#recoveryplantime_modal').modal('show');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'headerID': headerID,
                'timelineID': timelineID
            },
            url: "<?php echo site_url('Boq/fetch_recoveryplan'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#recoverydueto').val(data['recoverydueto']).trigger("change");
                $('#descriptionofthedelay').val(data['descriptionofthedelay']);
                $('#recoveryplandescription').val(data['recoveryplandescription']);
                $('#additionalmaterial').val(data['costimpactmaterial']);
                $('#additionalhr').val(data['costimpacthr']);
                $('#otherreq').val(data['costimpactother']);
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function save_recoveryplan() {
        var data = $('#recoveryplan_master_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_recovery_plan'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#hederIDrecoveryplan').val('');
                    $('#phaseID').val('');
                    $('#recoverydueto').val(null).trigger("change");
                    $('#descriptionofthedelay').val('');
                    $('#recoveryplandescription').val('');
                    $('#additionalmaterial').val('');
                    $('#additionalhr').val('');
                    $('#otherreq').val('');
                    $('#recoveryplantime_modal').modal('hide');
                }
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function recovery_attachment_view(timelineID) {
        var headerID = $('#hederIDrecoveryplan').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                timelineID: timelineID,
                'headerID': headerID
            },
            url: "<?php echo site_url('Boq/fetch_recovery_attachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#attachments_view_recoveryplan').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function changerequests() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/load_change_request'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#changerequests_view').html(data);
                changereq_attachment_view();

            },
            error: function () {

            }
        });

    }

    function inspectiondetail() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/load_inspection_detail'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#inspection_view').html(data);
                changereq_attachment_view();

            },
            error: function () {

            }
        });

    }

    function open_change_req_modal(headerID) {

        $('#cr').val('');
        $('#changereqID').val('');
        $('#submittername').val('');
        $('#breifdescriptionofrequest').val('');
        $('#datesubmitted').val('');
        $('#daterequired').val('');
        $('#reasonofchange').val('');
        $('#assumptionsandnotes').val('');
        $('#commentschangereq').val('');
        $('#typeofcr').val(null).trigger("change");
        $('#priority').val(null).trigger("change");
        $('#boqdetail_changereq').val(null).trigger("change");
        $('#hederidchangerequests').val(headerID);

        $('#change_req_modal').modal('show');

    }

    function save_changerequests() {
        var data = $('#changerequests_master_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_changerequests'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    changerequests();
                    $('#change_req_modal').modal('hide');
                }
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function edit_changereq(requestID) {

        swal({
                title: "Are you sure?",
                text: "You want to edit this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Edit"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'changereqID': requestID
                    },
                    url: "<?php echo site_url('Boq/fetch_changerequest'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        $('#cr').val(data['crcode']);
                        $('#typeofcr').val(data['typeofcr']).change();
                        $('#submittername').val(data['submittername']);
                        $('#breifdescriptionofrequest').val(data['descriptionofrequest']);
                        $('#datesubmitted').val(data['datesubmitted']);
                        $('#daterequired').val(data['daterequired']);
                        $('#priority').val(data['priority']).change();
                        $('#reasonofchange').val(data['reasonforchange']);
                        $('#assumptionsandnotes').val(data['assumptionsandnotes']);
                        $('#commentschangereq').val(data['commentschangereq']);
                        $('#changereqID').val(requestID);
                        $('#change_req_modal').modal('show');
                        stopLoad();
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });


    }

    function delete_change_req(requestID) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "You want to Delete",
                /*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes'); ?>",
                /*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'requestID': requestID
                    },
                    url: "<?php echo site_url('Boq/delete_change_req'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            changerequests();
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }


    function changereq_attachment_view() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID
            },
            url: "<?php echo site_url('Boq/fetch_changereq_attachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#attachments_view_changerequest').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function open_initial_analysis_modal(headerID) {
        crID = null;
        boqdetailID_initialan = null;
        subcatergoryID = null;
        $('#boqdetail_changereq').prop('disabled', false);
        $('#boqdetail_changereq').prop('disabled', false);
        $('#boqdetail_changereq_sub').prop('disabled', false);
        $('#hederidinitial').val(headerID);
        $('#crcode').val(null).trigger("change");
        $('#boqdetail_changereq').val(null).trigger("change");
        $('#boqdetail_changereq_sub').val(null).trigger("change");
        $('#durationimpact').val('');
        $('#hourimpact').val('');
        $('#scheduleimpact').val('');
        $('#changereqID_initial').val('');
        $('#costImpact').val('');
        $('#commentsinitial').val('');
        $('#recommendations').val('');
        $('#labourcosts').val(0);
        $('#labourcost_initial').val(0)
        load_req_dropdown(headerID);
        load_boq_detaildrop(headerID);
        $('#initial_req_modal').modal({
            backdrop: 'static',
            keyboard: false
        })

    }


    function save_changerequestsinitial() {
        $('#crcode').prop('disabled', false);
        $('#boqdetail_changereq').prop('disabled', false);
        $('#boqdetail_changereq').prop('disabled', false);
        $('#boqdetail_changereq_sub').prop('disabled', false);
        var data = $('#initialanalysis_master_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_changerequestsinitial'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    changerequests();
                    $('#initial_req_modal').modal('hide');
                } else {
                    $('#crcode').prop('disabled', true);
                    $('#boqdetail_changereq').prop('disabled', true);
                    $('#boqdetail_changereq').prop('disabled', true);
                    $('#boqdetail_changereq_sub').prop('disabled', true);
                }
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function edit_changereqIinitial(requestID, headerID) {
        swal({
                title: "Are you sure?",
                text: "You want to edit this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Edit"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'changereqID': requestID
                    },
                    url: "<?php echo site_url('Boq/fetch_changerequest_initial'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        crID = data['crID'];
                        subcatergoryID = data['subCategory'];
                        load_boq_detaildrop(headerID);
                        fetch_subcatergory(headerID, subcatergoryID);
                        boqdetailID_initialan = data['category'];
                        load_req_dropdown(headerID);
                        $('#hourimpact').val(data['hourimpact']);
                        $('#durationimpact').val(data['durationimpact']);
                        $('#scheduleimpact').val(data['scheduleimpact']);
                        $('#costImpact').val(data['costimpact']);
                        $('#commentsinitial').val(data['commentsinitial']);
                        $('#recommendations').val(data['recommendations']);
                        $('#changereqID_initial').val(requestID);
                        $('#hederidinitial').val(headerID);
                        $('#initial_req_modal').modal({
                            backdrop: 'static',
                            keyboard: false
                        })
                        stopLoad();
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function load_req_dropdown(headerID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/fetch_cr_code'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_cr_code').html(data);
                if (crID) {
                    $('#crcode').val(crID).change();
                }

                $(".select2").select2();

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function open_decision_modal(headerID) {
        crchcontrolboard = null;
        $('#changereqID_change').val('');
        $('#decision').val(null).trigger("change");
        $('#decisiondate').val('');
        $('#decisionexplanation').val('');
        $('#hederidchangecontrol').val(headerID);
        $('.specialapprovaldecisiion').addClass('hide');
        $('#approvalLevelID').val('');
        $('.commondecission').removeClass('hide');
        $('#initial_control_modal').modal('show');

        load_req_dropdown_changeboard(headerID);
    }

    function save_changerequestscontrolboard() {

        var data = $('#changecontrolboard_master_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_changerequestsinitial_controlboard'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    changerequests();
                    $('#initial_control_modal').modal('hide');
                }
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function edit_changereqcontrolboard(requestID, headerID, isexist, approvalleavel) {
        swal({
                title: "Are you sure?",
                text: "You want to edit this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Edit"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'changereqID': requestID
                    },
                    url: "<?php echo site_url('Boq/fetch_changerequest_controlboard'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        crchcontrolboard = data['crID'];
                        load_req_dropdown_changeboard(headerID);
                        $('#decision').val(data['decision']);
                        $('#decisiondate').val(data['decisiondate']);
                        $('#changereqID_change').val(requestID);
                        $('#decisionexplanation').val(data['decisionexplanation']);
                        $('#hederidchangecontrol').val(headerID);
                        if (isexist == 1) {
                            $('.specialapprovaldecisiion').removeClass('hide');
                            $('#approvalLevelID').val(approvalleavel);
                            $('.commondecission').addClass('hide');
                        } else {
                            $('#approvalLevelID').val('');
                            $('.specialapprovaldecisiion').addClass('hide');
                            $('.commondecission').removeClass('hide');
                        }
                        changerequests();
                        $('#initial_control_modal').modal('show');
                        stopLoad();
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function load_req_dropdown_changeboard(headerID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/fetch_cr_code_changecontrolboard'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_cr_code_changecontrol').html(data);
                if (crchcontrolboard) {
                    $('#crcode_changeboard').val(crchcontrolboard).change();
                }

                $(".select2").select2();

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function load_project_phases(headerID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/fetch_project_phases'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_load_projectphase').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_projectclosure() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID
            },
            url: "<?php echo site_url('Boq/fetch_projectclosure'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#projectma_closure').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function add_template_project(headerID) {
        $('#projectclosuredescription').val('');
        $('#checklisttemplate').val(null).trigger('change');
        $('#hederidprojectclosure').val(headerID);
        $('#project_closure_template').modal('show');
    }

    function save_projectclosuretemp() {
        var data = $('#project_closure_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_projectclosuretemp'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1])
                if (data[0] == 's') {
                    inspectiondetail();
                    $('#project_closure_template').modal('hide');
                }
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }

    function fetch_assign_template(checklisttemplateID, documentchecklistID, documentchecklistmasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'checklisttemplateID': checklisttemplateID,
                'documentchecklistID': documentchecklistID,
                'documentchecklistmasterID': documentchecklistmasterID
            },
            url: "<?php echo site_url('Boq/get_template_checklist_project'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#fetch_projectclosureview').html(data);
                $('#project_closure_template_view').modal('show');
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function update_checkliststatus_template(val, status) {
        var value = val.value;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'checklistID': value,
                'status': status
            },
            url: "<?php echo site_url('Boq/update_checklist_template_detail_checkbox'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                stopLoad();
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }

    function update_textboxvalue(ID, value) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'checklistID': ID,
                'value': value
            },
            url: "<?php echo site_url('Boq/update_checklist_template_detail_text'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                stopLoad();
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }

    function update_masterchecklist(documentchecklistID, colname, val) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'checklistID': documentchecklistID,
                'value': val,
                'colname': colname
            },
            url: "<?php echo site_url('Boq/update_checklist_template_masterdata'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                stopLoad();
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }

    function delete_project_template(documentchecklistID) {
        swal({
                title: "Are you sure?",
                text: "You want to delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Delete"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'documentchecklistID': documentchecklistID
                    },
                    url: "<?php echo site_url('Boq/delete_project_template'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1])
                        if (data[0] == 's') {
                            inspectiondetail();
                        }
                        stopLoad();
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function save_lessonslearnt(headerID) {
        tinymce.triggerSave();
        var data = $('#lessonslearned_form').serializeArray();
        data.push({
            'name': 'headerID',
            'value': headerID
        });
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_project_closure_lessonlearn'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1])
                if (data[0] == 's') {
                    fetch_projectclosure();
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }

    function project_template_confirm(documentchecklistID) {
        swal({
                title: "Are you sure?",
                text: "You want to confirm this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'documentchecklistID': documentchecklistID
                    },
                    url: "<?php echo site_url('Boq/project_template_confirm'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        myAlert(data[0], data[1])
                        if (data[0] == 's') {
                            inspectiondetail();
                        }
                        stopLoad();
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }

    function updateapprovedyn(checklistID, value, status, approvalLevelID) {
        swal({
                title: "Are you sure?",
                text: "You want to " + status + " this Document!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes"
            },
            function (isConfirm) {

                if (isConfirm) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'documentchecklistID': checklistID,
                            'approvalLevelID': approvalLevelID,
                            'value': value
                        },
                        url: "<?php echo site_url('Boq/project_approval_checklist'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            if (data == true) {
                                fetch_projectclosure();
                            } else {
                                $('.approvedcheckYes input').iCheck('uncheck');
                                $('.approvedcheckno input').iCheck('uncheck');
                            }
                            stopLoad();
                        },
                        error: function () {
                            stopLoad();
                            swal("Cancelled", "Try Again ", "error");
                            $('.approvedcheckYes input').iCheck('uncheck');
                            $('.approvedcheckno input').iCheck('uncheck');
                        }
                    });

                } else {
                    $('.approvedcheckYes input').iCheck('uncheck');
                    $('.approvedcheckno input').iCheck('uncheck');
                }


            });
    }

    $(".equipmenttype").change(function () {
        if (this.value == 1) {
            $('.assetdrop').removeClass('hide');
            $('.assettext').addClass('hide');
            $('#asset_text_id').val('').change();
            $('#equpimentcosttype').val(null).trigger("change");
            $('#withoperator').val(null).trigger("change");
            $('#supplierdrop').val(null).trigger("change");
            $('#rentedperiods').val('');
            $('#perhcost').val('');
            $('#assettext_field').val('').trigger('input');
            $('#assettext_field').prop('readonly', false);
            equipmentID = null;

        } else if (this.value == 2) {
            $('.assetdrop').addClass('hide');
            $('.assettext').removeClass('hide');
            $('#asset_drop').val(null).trigger('change');
        } else {
            $('.assetdrop').addClass('hide');
            $('.assettext').addClass('hide');
            $('#rentedperiods').val('');
            $('#asset_text_id').val('').change();
            $('#perhcost').val('');
            $('#equpimentcosttype').val(null).trigger("change");
            $('#supplierdrop').val(null).trigger("change");
            $('#withoperator').val(null).trigger("change");
            $('#assettext_field').val('').trigger('input');
            $('#assettext_field').prop('readonly', false);
            equipmentID = null;
            $('#asset_drop').val(null).trigger('change');


        }
    });

    function clear_asset_field() {
        $('#asset_text_id').val('').change();
        $('#assettext_field').val('').trigger('input');
        $('#assettext_field').prop('readonly', false);
        equipmentID = null;
    }

    function asset_link_modal() {
        $('#asset_link_modal').modal('show');
    }

    function fetch_asset_detail() {
        var equipmentID = $('#asset_text_id').val();
        if (equipmentID == '') {
            //swal("", "Select An Employee", "error");
            myAlert('e', 'Select A Asset');
        } else {
            equipmentID = equipmentID;
            var equipmentname = $("#asset_text_id option:selected").text();
            /*  var empNameSplit = empName.split('|');*/
            $('#assettext_field').val($.trim(equipmentname)).trigger('input');
            $('#assettext_field').prop('readonly', true);
            $('#asset_link_modal').modal('hide');
        }
    }

    function upload_excel_equipmentplanning(boqheaderID, boqdetailID) {
        $('#boqheaderID').val(boqheaderID);
        $('#boqdetailID').val(boqdetailID);
        $('#boqdetailID').val(boqdetailID);
        $('#supplierupform').val(null).trigger("change");
        $('#excelUpload_Modal').modal('show');
    }

    function excel_upload_equipment() {
        var formData = new FormData($("#equipmentplanningUpload_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Boq/equipment_master_excelUpload'); ?>",
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
                    $('#excelUpload_Modal').modal('hide');
                    $('#remove_id_excelup').click();
                    equipmentplanning_view_fn();
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'error');
            }
        });
    }

    function linkasset_equipment(activityplanningID) {
        $('#assetlinkID').val(null).trigger("change");
        $('#activityplanningID').val(activityplanningID);
        $('#linkassetmodel').modal('show');


    }

    function save_linkasset() {
        var data = $('#linkasset_master_form').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/update_linkasset'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1])
                if (data[0] == 's') {
                    equipmentplanning_view_fn();
                    $('#linkassetmodel').modal('hide');
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Try Again ", "error");
            }
        });
    }

    function validateFloatKeyPress(el, evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if (caratPos > dotPos && dotPos > -(currency_decimal - 1) && (number[1] && number[1].length > (currency_decimal - 1))) {
            return false;
        }
        return true;
    }

    function getSelectionStart(o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate()
            r.moveEnd('character', o.value.length)
            if (r.text == '') return o.value.length
            return o.value.lastIndexOf(r.text)
        } else return o.selectionStart
    }

    function load_boq_detaildrop(headerID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/fetch_load_boq_detaildrop'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#div_boqdetail').html(data);
                if (boqdetailID_initialan) {
                    $('#boqdetail_changereq').val(boqdetailID_initialan).change();
                }

                $(".select2").select2();

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_subcatergory(headerID, catergoryID) {
        if (catergoryID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    headerID: headerID,
                    catergoryID: catergoryID
                },
                url: "<?php echo site_url('Boq/fetch_load_subcatboq'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#div_boqdetail_subcatergory').html(data);
                    if (subcatergoryID) {
                        $('#boqdetail_changereq_sub').val(subcatergoryID).change();
                    }
                    $(".select2").select2();
                    stopLoad();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        } else {
            $('#boqdetail_changereq_sub').val(null).trigger("change");
            $('#div_boqdetail_subcatergory').html('<select name="boqdetail_changereq_sub" class="form-control select2" id="boqdetail_changereq_sub"> <option value=" ">Select Sub Category</option> </select>');
            $(".select2").select2();
        }

    }

    function open_cost_impact_model() {
        var category = $('#boqdetail_changereq').val();
        var subcategory = $('#boqdetail_changereq_sub').val();

        $('#categoryIDinitialanalysis').val(category);
        $('#subcategoryIDinitialanalysis').val(subcategory);
        $('#searchintial').val('');
        $('#uomintial').val('');
        $('#totalcostintial').val('');
        $('#qtyintial').val('');
        $('#unitcostintial').val('');
        $('#labourcosts').val($('#labourcost_initial').val());

        $('#boq_cost_form_sheet_initialanalysis').bootstrapValidator('resetForm', true);
        loadcostsheettable_initialize();
        $('#initalanalysiscost').modal('show');
    }

    function itemSearchModal_initialanalysis() {
        $('#itemSeachModal_initialanalaysis').modal('show');
        $('#searchKeyword_initialanalysis').val('');
        $('#searchKeyword_initialanalysis').trigger('onkeyup');
    }

    function searchByKeyword_initializealaysis(initialSearch = null) {


        /*reset Cost form */
        $("#itemSearchResultTblBody_initial").html('');
        var keyword = (initialSearch == null) ? $("#searchKeyword_initialanalysis").val() : '-';


        $.ajax({
            async: true,

            data: {
                q: keyword,
                currency: $('#currency').val()
            },
            type: 'post',
            dataType: 'json',
            url: '<?php echo site_url('Boq/item_search'); ?>',
            beforeSend: function () {
                $("#itemSearchResultTblBody_initial").html('');

                //startLoad();
            },
            success: function (data) {

                $("#itemSearchResultTblBody_initial").html('');
                if (data == null || data == '') {

                } else {

                    $.each(data, function (i, v) {
                        ''
                        var tr_data = '<tr><td>' + v.itemSystemCode + '</td> <td>' + v.itemDescription + '</td> <td>' + v.defaultUnitOfMeasure + '</td> <td>' + v.subCurrencyCode + '</td> <td style="text-align: right">' + parseFloat(v.cost).toFixed(2) + '</td><td><button type="button" ' + 'onclick="fetchItemRowinitial(\'' + v.itemSystemCode + '\',\'' + v.itemDescription + '\',\'' + v.defaultUnitOfMeasure + '\',\'' + v.subCurrencyCode + '\',' + parseFloat(v.cost).toFixed(2) + ',' + v.itemAutoID + ')" class="btn btn-xs btn-default"><i class="fa fa-plus"></i> <?php echo $this->lang->line('common_add'); ?><!--Add--> </button></td></tr>';
                        $("#itemSearchResultTblBody_initial").append(tr_data);
                    });
                }

            },
            error: function () {

                myAlert('e', 'Error while loading')
            }
        });


    }

    function fetchItemRowinitial(itemSystemCode, itemDescription, defaultUnitOfMeasure, subCurrencyCode, cost, itemAutoID) {

        $('#searchintial').val(itemDescription + '(' + itemSystemCode + ')');
        $('#uomintial').val(defaultUnitOfMeasure);
        $('#itemautoidprojectinitialanalysis').val(itemAutoID);
        $('#unitcostintial').val(cost);
        $('#searchKeyword_initialanalysis').val('');
        $('#searchKeyword_initialanalysis').trigger('onkeyup');
        $('#itemSeachModal_initialanalaysis').modal('hide');

    }

    function calculateinitial() {
        if ($('#unitcostintial').val() != '') {

            q = $('#qtyintial').val();
            u = $('#unitcostintial').val();

            t = u * q;

            x = t.toFixed(2);


            $('#totalcostintial').val(x);

        } else {
            $('#totalcostintial').val(0);
        }
    }

    function loadcostsheettable_initialize() {
        var crcode = $('#crcode').val();
        if (crcode) {
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'html',
                data: {
                    crcode: crcode
                },
                url: "<?php echo site_url('Boq/loadboqcosttable_initialanalysis'); ?>",
                beforeSend: function () {
                },
                success: function (data) {
                    $('#loadcostsheettableintial').html(data);
                },
                error: function () {

                }
            });
        }


    }

    function save_initialanalysis_cost() {
        var crcode = $('#crcode').val();
        var data = $('#boq_cost_form_sheet_initialanalysis').serializeArray();
        data.push({
            "name": "crcode",
            "value": crcode
        });
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Boq/save_boq_cost_sheet_initialanalysis'); ?>",
            beforeSend: function () {
                HoldOn.open({
                    theme: "sk-bounce",
                    message: "<h4> Please wait until page load! </h4>",
                });
            },
            success: function (data) {
                if (data[0] == 's') {
                    /*loadheaderdetails();*/
                    loadcostsheettable_initialize(crcode);
                    $('#searchintial').val('');
                    $('#totalcostintial').val(0);
                    $('#uomintial').val('');
                    $('#boq_cost_form_sheet_initialanalysis').bootstrapValidator('resetForm', true);
                    $('#crcode').prop('disabled', true);

                    var totalvaluecost = $('#totalvalue_tbl').val();
                    var materialcost = $('#labourcosts').val();
                    var total = parseFloat(totalvaluecost) + (parseFloat(materialcost));
                    $('#costImpact').val(total);
                    $('#labourcost_initial').val(materialcost);
                    $('#boqdetail_changereq').prop('disabled', true);
                    $('#boqdetail_changereq').prop('disabled', true);
                    $('#boqdetail_changereq_sub').prop('disabled', true);
                }
                HoldOn.close();
                refreshNotifications(true);
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                /*An Error Occurred! Please Try Again*/
                HoldOn.close();
                refreshNotifications(true);
            }
        });
    }

    function fetch_record_exit() {
        var crcode = $('#crcode').val();
        if (crcode) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'crcode': crcode
                },
                url: "<?php echo site_url('Boq/delete_unsaved_costitems'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                },
                error: function () {
                    stopLoad();
                    swal("Cancelled", "Try Again ", "error");
                }
            });
        }
    }

    function deleteBoqCost_initialanalysis(costingID, crcode) {
        if (costingID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*Are you sure?*/
                    text: "<?php echo $this->lang->line('promana_common_you_will_not_be_able'); ?>",
                    /*Your will not be able to recover this data*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('promana_common_yes_delete_it'); ?>",
                    /*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'costingID': costingID,
                            'crcode': crcode
                        },
                        url: "<?php echo site_url('Boq/deleteboqcost_initialanalysis'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce",
                                message: "<h4> Please wait until page load! </h4>",
                            });
                        },
                        success: function (data) {

                            if (data[2] > 0) {
                                $('#crcode').prop('disabled', true);
                                $('#boqdetail_changereq').prop('disabled', true);
                                $('#boqdetail_changereq').prop('disabled', true);
                                $('#boqdetail_changereq_sub').prop('disabled', true);
                            } else {
                                $('#crcode').prop('disabled', false);
                                $('#boqdetail_changereq').prop('disabled', false);
                                $('#boqdetail_changereq').prop('disabled', false);
                                $('#boqdetail_changereq_sub').prop('disabled', false);
                            }
                            loadcostsheettable_initialize(crcode);
                            $('#costImpact').val($('#totalvalue_tbl').val());
                            HoldOn.close();
                            refreshNotifications(true);

                        },
                        error: function () {

                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                            /*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        }
        ;
    }

    function save_approval() {
        var approvalLevelID = $('#approvalLevelID').val();
        var changereqID_change = $('#changereqID_change').val();
        var decision_spapproval = $('#decision_spapproval').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'requestID': changereqID_change,
                'status': decision_spapproval,
                'approvalLevelID': approvalLevelID
            },
            url: "<?php echo site_url('Boq/save_approvalstatus_request'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                if (data == true) {
                    changerequests();
                    $('#initial_control_modal').modal('hide');
                }
            },
            error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function save_labourcostdetail() {
        var totalvaluecost = $('#totalvalue_tbl').val();
        var materialcost = $('#labourcosts').val();
        var total = parseFloat(totalvaluecost) + (parseFloat(materialcost));
        $('#costImpact').val(total);
        $('#labourcost_initial').val(materialcost);
    }

    function sendtorfq(detailID) {
        if (detailID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                    /*Are you sure?*/
                    text: "You Want to sent this for RFQ",
                    /*Your will not be able to recover this data*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    /*Yes, delete it!*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
                },
                function () {
                    $.ajax({
                        async: false,
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'detailID': detailID
                        },
                        url: "<?php echo site_url('Boq/sendtorfq'); ?>",
                        beforeSend: function () {
                            HoldOn.open({
                                theme: "sk-bounce",
                                message: "<h4> Please wait until page load! </h4>",
                            });
                        },
                        success: function (data) {
                            myAlert(data[0], data[1])
                            if (data[0] == 's') {

                            }
                            HoldOn.close();
                            refreshNotifications(true);
                        },
                        error: function () {

                            HoldOn.close();
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                            /*An Error Occurred! Please Try Again*/
                            refreshNotifications(true);
                        }
                    });
                });
        }
    }

    function dailyqulityreport(headerID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID
            },
            url: "<?php echo site_url('Boq/load_daily_qulityreport'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#daliy_qa_qc').html(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function fetch_inspectionreq(headerID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID
            },
            url: "<?php echo site_url('Boq/load_inspectionrequest'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#inspection_request_project').html(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function projectsummary(headerID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/load_projectsummaryview'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#projectsummary_view').html(data);
                fetch_cc_certificate_attachment();
            },
            error: function () {

            }
        });
    }

    function fetch_projecthandover(headerID) {
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/load_projecthandover'); ?>",
            beforeSend: function () {

            },
            success: function (data) {
                $('#projecthandover').html(data);
            },
            error: function () {

            }
        });
    }

    function document_uplode_insurance() {
        var formData = new FormData($("#insurance_attachment_upload")[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id_ins').click();
                    $('#ins_attachmentDescription').val('');
                    load_eoi_attachment('PROINS');

                }
            },
            error: function (data) {
                stopLoad();
                swal("Error", "An unexpected error occurred", "error");
            }
        });
    }

    function pretenderConfirmation() {
        var headerID = $('#headerID').val();
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure'); ?>",
                /*Are you sure?*/
                text: "<?php echo $this->lang->line('common_you_want_to_confirm_this_document'); ?>",
                /*You want to confirm this document !*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm'); ?>",
                /*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel'); ?>"
            },
            function () {
                $.ajax({
                    async: false,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        'headerID': headerID
                    },
                    url: "<?php echo site_url('Boq/confirm_pre_tender'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1])
                        if (data[0] == 's') {
                            loadheaderdetails()
                            // $('#post-tender_hide').removeClass('hide');
                        }

                    },
                    error: function () {
                        stopLoad();
                        alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');
                    }
                });
            });
    }

    function fetch_posttender() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/posttenderview'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#post-tenderview').html(data);
            },
            error: function () {
                stopLoad();
            }
        });


    }

    function show_quantity_surve() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: false,
            type: 'post',
            dataType: 'html',
            data: {
                headerID: headerID
            },
            url: "<?php echo site_url('Boq/fetch_project_quantitysur'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#quantitysurveying').html(data);
                fetch_qs_comment_attachment();
            },
            error: function () {
                stopLoad();
            }
        });

    }

    function fetch_qs_comment_attachment() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID
            },
            url: "<?php echo site_url('Boq/fetch_qs_comment_attachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#qsattachment').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function document_uplode_qs() {
        var formData = new FormData($("#qs_attachment_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id').click();
                    $('#qsattachmentDescription').val('');
                    fetch_qs_comment_attachment();

                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;

    }

    function fetch_cc_certificate_attachment() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID
            },
            url: "<?php echo site_url('Boq/fetch_cc_certificate_attachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ccattachment').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function document_uplode_cc() {
        var formData = new FormData($("#cc_attachment_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id').click();
                    $('#ccattachmentDescription').val('');
                    fetch_cc_certificate_attachment();

                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;

    }

    function fetch_project_maintenancewarranty() {
        var headerID = $('#headerID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                'headerID': headerID
            },
            url: "<?php echo site_url('Boq/fetch_maintenacewarrantyattachment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#maintenancewarrantyattachment').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }


    function document_uplode_mw() {
        var formData = new FormData($("#mw_attachment_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Attachment/do_upload_aws_S3'); ?>",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data['type'], data['message'], 1000);
                if (data['status']) {
                    $('#add_attachemnt_show').addClass('hide');
                    $('#remove_id').click();
                    $('#mwattachmentDescription').val('');
                    fetch_project_maintenancewarranty();

                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;

    }
</script>