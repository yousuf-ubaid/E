<?php  ?>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$title = $this->lang->line('manufacturing_job');
echo head_page($title, true);
$date_format_policy = date_format_policy();
$segment = fetch_mfq_segment(true, false);
$att_documents = fetch_mfq_documentSetup();
$usageQtyUpdatePolicy = getPolicyValues('JUQ', 'All');
$LanguagePolicy = getPolicyValues('LNG', 'All');
$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<style>
    #tbl_mfq_job th{
        text-transform: uppercase;
    }
    td.details-control {
        background: url('http://www.pskreporter.de/public/images/details_open.png') no-repeat center center;
        cursor: pointer;
    }

    tr.shown td.details-control {
        background: url('http://www.pskreporter.de/public/images/details_close.png') no-repeat center center;
    }

    .hiddenRow {
        padding: 0 !important;
    }
</style>

<div id="filter-panel" class="collapse filter-panel">
    <form role="form" id="mfq_job_filter" class="" autocomplete="off">
        <input type="hidden" name="<?=$token_details['name'];?>" value="<?=$token_details['hash'];?>" />
        <div class="row" style="padding-left: 2%">
            <div class="form-group col-sm-2">
                <label for="filter_dateFrom">Date From :</label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="filter_dateFrom"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" id="filter_dateFrom" class="form-control"  value="">
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="filter_dateTo">&nbsp&nbspTo :&nbsp&nbsp</label>
                <div class="input-group datepic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" name="filter_dateTo"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"  id="filter_dateTo"  class="form-control" value="" >
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="filter_dateTo">Department :</label>
                <div>
                    <?php echo form_dropdown('filter_DepartmentID[]', $segment,'', 'class="form-control" id="filter_DepartmentID" multiple="multiple" onchange="oTable.draw()"'); ?>
                </div>
            </div>
            <div class="form-group col-sm-2">
                <label for="filter_customerID">Customer :</label>
                <?php echo form_dropdown('filter_customerID[]', all_mfq_customer_drop(false), '', 'class="form-control" multiple="multiple" id="filter_customerID" onchange="oTable.draw()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="filter_mainJobStatus">Main Job Status :</label>
                <?php echo form_dropdown('filter_mainJobStatus', array(''=> 'Select Status', 1=>'Pending', 2=>'Delivered', 3=>'Invoiced'), '', 'class="form-control select2" id="filter_mainJobStatus" onchange="oTable.draw()"'); ?>
            </div>
            <div class="form-group col-sm-2">
                <label for="filter_subJobStatus">Sub Job Status :</label>
                <?php echo form_dropdown('filter_subJobStatus', array(''=> 'Select Status', 1=>'Open', 2=>'Invoiced', 3=>'Delivered', 4=>'Overdue', 5=>'Closed'), '', 'class="form-control select2" id="filter_subJobStatus" onchange="oTable.draw()"'); ?>
            </div>
        </div>
    </form>
</div>
<div class="row">
    <div class="col-md-12">
        <div class=" pull-right">
            <button type="button" data-text="excel" id="btnAdd"
                    onclick="mfq_job_excel()"
                    class="btn btn-sm btn-success">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> <?php echo $this->lang->line('common_excel') ?><!--Excel-->
            </button>
            <button type="button" data-text="Add" id="btnAdd" class="btn btn-sm btn-primary"
                onclick="fetchPage('system/mfq/mfq_job_create_process_based',null,'Add Job','MFQ');">
                <i class="fa fa-plus" aria-hidden="true"></i> <?php echo $this->lang->line('common_add') ?> Job<!--Add-->
            </button>
        </div>
    </div>
</div>

<?php echo form_open('login/loginSubmit', ' class="form-horizontal" id="job_order_pdf" role="form"'); ?>
    <input class="hidden" id="estimateMasterID_pdf" name="estimateMasterID">
    <input class="hidden" id="workProcessID_pdf" name="workProcessID">
</form>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table id="tbl_mfq_job" class="table table-condensed" width="100%">
                <thead>
                <tr>
                    <th style="min-width: 3%">&nbsp;</th>
                    <th style="min-width: 3%">&nbsp;</th>
                    <th style="min-width: 12%"><?php echo $this->lang->line('manufacturing_job_no');?></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('manufacturing_date');?></th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('manufacturing_customer');?></th>
                    <th style="min-width: 12%">Workflow Template</th>
                    <th style="min-width: 15%"><?php echo $this->lang->line('manufacturing_item');?></th>
                    <th style="min-width: 10%"><?php echo $this->lang->line('manufacturing_description');?></th>
                    <th style="min-width: 3%"><?php echo $this->lang->line('manufacturing_job_status');?></th>
                    <th style="min-width: 3%"><?php echo $this->lang->line('manufacturing_status');?></th>
                    <th style="min-width: 3%"><?php echo $this->lang->line('manufacturing_percentage');?></th>
                    <th style="min-width: 6%">&nbsp;</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>

<div class="modal fade" id="pulled_documents_processBased_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title drilldown-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div id="pulled_documents"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" aria-labelledby="Work Flow Modal" data-backdrop="static"
     data-keyboard="false"
     id="workflowTemplateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times text-red"></i></span></button>
                <h4 class="modal-title" id="modal_title_category">Work Flow Template </h4>
            </div>
            <form id="frm_mfq_template">
                <div class="modal-body">
                    <input type="hidden" value="0" id="workFlowTemplateID" name="workFlowTemplateID">

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Industry</label>
                        </div>
                        <div class="form-group col-sm-6">
                        <span class="input-req"
                              title="Required Field">
                            <?php echo form_dropdown('industryID', get_all_mfq_industry(), '', 'class="form-control" id="industryID"  required'); ?>
                            <span class="input-req-inner"></span>
                        </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                            <label class="title">Description </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <span class="input-req" title="Required Field">
                                <input type="text" name="description" id="description"
                                       class="form-control" required>
                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Save
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="job_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 55%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="jobHeader"><?php echo $this->lang->line('manufacturing_job');?><!--Job--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <div class="table-responsive">
                                <table id="mfq_material_consumption" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 12%">Item</th>
                                        <th style="min-width: 12%">Estimated Qty</th>
                                        <th style="min-width: 12%">Job Qty</th>
                                        <th style="min-width: 12%">Balance Qty</th>
                                        <th style="min-width: 12%">Qty</th>
                                    </tr>
                                    </thead>
                                    <tbody id="sub_job_generate_table"></tbody>
                                </table>
                            </div>
                        <div id="jobContent"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="generateJob()"><?php echo $this->lang->line('manufacturing_generate_job');?><!--Generate Job--></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="route_card_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="routeHeader">Route Card</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo form_open(" ", 'class="form-horizontal" id="route_card"'); ?>
                        <div class="form-group">
                            <label for="usergroup" class="col-sm-2 control-label">Work Process Flow</label>
                            <div class="col-sm-4">

                                <?php
                                echo form_dropdown('workProcessFlowID', "", ' ', 'class="form-control" id="workProcessFlowID" onchange="loadRouteCard(this.value)"'); ?>
                                <input type="hidden" name="jobID" id="jobID" value="">
                            </div>
                        </div>
                        <table id="mfq_route_card" class="table table-condensed">
                            <thead>
                            <tr>
                                <th style="min-width: 12%">Process</th>
                                <th style="min-width: 12%">Instructions</th>
                                <th style="min-width: 12%">Acceptance Criteria</th>
                                <th style="min-width: 12%">Production</th>
                                <th style="min-width: 12%">QA/QC</th>
                                <th style="min-width: 5%">
                                    <div class=" pull-right">
                                        <button type="button" data-text="Add"
                                                onclick="add_more_route()" id="addRoute"
                                                class="button button-square button-tiny button-royal button-raised">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="route_body">

                            </tbody>
                        </table>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveRouteCard()">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="usage_qty_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="">Update Usage Qty</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input class="hidden" id="jobID_qtyUpdate" name="jobID_qtyUpdate">
                    <div class="form-group col-sm-4">
                        <label for="financeyear">Job Card :</label>
                        <?php echo form_dropdown('jobCard_qtyUpdate', array('' => 'Select Job Card'), '', 'class="form-control" id="jobCard_qtyUpdate" onchange="load_jobCard_updateQty(this.value)"'); ?>
                    </div>
                </div>
                <div class="row updateTablesView">
                    <div class="col-md-12">
                        <?php echo form_open(" ", 'class="form-horizontal" id="usage_qty"'); ?>
                        <?php if($usageQtyUpdatePolicy == 1) { ?>
                            <header class="head-title">
                                <h2>Material Consumption</h2>
                            </header>
                            <table id="mfq_usage_qty" class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 40%">Item</th>
                                    <th style="width: 20%">Current Stock</th>
                                    <th style="width: 20%">Estimated Qty</th>
                                    <th style="width: 10%">Qty Used</th>
                                    <th style="width: 20%">Update Qty</th>
                                </tr>
                                </thead>
                                <tbody id="usage_qty_body">

                                </tbody>
                            </table>
                        <?php } ?>
                        <header class="head-title" style="margin-top: 10px">
                            <h2>LABOUR TASKS</h2>
                        </header>
                        <?php echo form_open(" ", 'class="form-horizontal" id="usage_qty"'); ?>
                        <table id="mfq_labour_task_table" class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 40%">Description</th>
                                <th style="width: 20%">Estimated Hours</th>
                                <th style="width: 20%">Hours Spent</th>
                                <th style="width: 20%">Update Hours</th>
                            </tr>
                            </thead>
                            <tbody id="labour_task_body">

                            </tbody>
                        </table>
                        <header class="head-title" style="margin-top: 10px">
                            <h2>OVERHEAD COST</h2>
                        </header>
                        <?php echo form_open(" ", 'class="form-horizontal" id="usage_qty"'); ?>
                        <table id="mfq_overhead_table" class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 40%">Description</th>
                                <th style="width: 20%">Estimated Hours</th>
                                <th style="width: 20%">Hours Spent</th>
                                <th style="width: 20%">Update Hours</th>
                            </tr>
                            </thead>
                            <tbody id="overhead_body">

                            </tbody>
                        </table>
                        
                        <?php if($LanguagePolicy != 'FlowServe') { ?>
                        <header class="head-title" style="margin-top: 10px">
                            <h2>THIRD PARTY SERVICE</h2>
                        </header>
                        <?php echo form_open(" ", 'class="form-horizontal" id="usage_qty"'); ?>
                        <table id="mfq_overhead_table" class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 40%">Description</th>
                                <th style="width: 20%">Estimated Hours</th>
                                <th style="width: 20%" <?php echo ($LanguagePolicy == 'FlowServe' ) ? 'class="hide"' : '' ?>>Hours Spent</th>
                                <th style="width: 20%">Update Hours</th>
                            </tr>
                            </thead>
                            <tbody id="thirdparty_body">

                            </tbody>
                        </table>

                        <header class="head-title" style="margin-top: 10px">
                            <h2>MACHINE</h2>
                        </header>
                        <?php echo form_open(" ", 'class="form-horizontal" id="usage_qty"'); ?>
                        <table id="mfq_machine_table" class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 40%">Description</th>
                                <th style="width: 20%">Estimated Hours</th>
                                <th style="width: 20%">Hours Spent</th>
                                <th style="width: 20%">Update Hours</th>
                            </tr>
                            </thead>
                            <tbody id="machine_body">

                            </tbody>
                        </table>

                        <?php } ?>
                        <?php echo form_close(); ?>
                    </div>
                </div>
                <div class="row selectJobCardView hide">
                <div class="col-md-12 xxcol-md-offset-2">
                    <div class="alert alert-warning" role="alert">
                        Select Job Card To Update Quantity
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveUsageQty()">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="job_view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('manufacturing_job_order');?><!--Job Order--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" style="margin-top: 10px">
                        <div id="job_print">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="material_request_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close hide" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Material Request</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" style="margin-top: 10px">
                        <div id="material_request_body">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="saveMaterialRequest()">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="insufficient_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="50%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Raw Material Not Configured</h4>
            </div>
            <div class="modal-body">
                <div class="row hidden" id="itemValidate">
                    <div class="col-sm-4 pull-left">
                        <label>&nbsp;&nbsp;Item</label>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="" class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">Item Code</th>
                                    <th style="min-width: 12%">Item Description</th>
                                </tr>
                                </thead>
                                <tbody id="table_body_insufficient">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="job_attachment_modal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="attachment_modal_label">Job Attachments</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-11">
                        <span class="pull-right">
                            <?php echo form_open_multipart('', 'id="job_attachment_upload_form" class="form-inline"'); ?>
                            <div class="form-group">
                                <?php echo form_dropdown('att_docType', $att_documents,'', 'class="form-control" id="att_docType"'); ?>
                                <!-- <label for="attachmentDescription">Description</label> -->
                                <input type="text" class="form-control" id="attachmentDescription"
                                       name="attachmentDescription"
                                       placeholder="<?php echo $this->lang->line('common_description'); ?>...">
                                <!--Description-->
                                <input type="hidden" class="form-control" id="documentSystemCode"
                                       name="documentSystemCode">
                                <input type="hidden" class="form-control" id="documentID" name="documentID">
                                <input type="hidden" class="form-control" id="document_name" name="document_name">
                                <input type="hidden" class="form-control" id="confirmYNadd" name="confirmYNadd">
                            </div>
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
                                  <a class="input-group-addon btn btn-default fileinput-exists" id="remove_id"
                                     data-dismiss="fileinput"><span class="glyphicon glyphicon-remove"
                                                                    aria-hidden="true"></span></a>
                              </div>
                          </div>
                          <button type="button" class="btn btn-default" onclick="document_uplode_job()"><span
                                      class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form>
                        </span>
                    </div>
                </div>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                        <th>Attachment Type</th>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="attachment_modal_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    var oTable;
    var jobID;
    var jobAutoID;
    var currency_decimal = 3;
    
    $(document).ready(function () {

        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_job', 'Job', 'Job');
        });

        template();
        init_route_card();

        $('#filter_DepartmentID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        $("#filter_DepartmentID").multiselect2('selectAll', false);
        $("#filter_DepartmentID").multiselect2('updateButtonText');

       
        $('#filter_customerID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            numberDisplayed: 1,
            buttonWidth: '180px',
            maxHeight: '30px'
        });
        


        $('.select2').select2();
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            oTable.draw();
        });

    });
    
    $(document).on('click', '.remove-tr2', function () {
        $(this).closest('tr').remove();
    });

    function createJob(estimateMasterID, jobID) {
        if (estimateMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'estimateMasterID': estimateMasterID, jobID: jobID},
                url: "<?php echo site_url('MFQ_Estimate/load_mfq_estimate_detail_subJobGenerate'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('#jobContent').html('');
                    $('#sub_job_generate_table').html("");
                },
                success: function (data) {
                    stopLoad();
                    $('#job_modal').modal();
                    if (!$.isEmptyObject(data)) {
                        $.each(data, function (key, value) {
                            $('#sub_job_generate_table').append('<tr>'+
                                 '<td valign="middle"><div class="">'+
                                '<div class="skin skin-square item-iCheck">'+
                                ' <div class="skin-section extraColumns">'+
                                '<input id="linkItem" name="linkItem" type="radio" data-estimatemasterid= "' + value.estimateMasterID + '" data-bommasterid= "' + value.bomMasterID + '" data-estimatedetailid= "' + value.estimateDetailID + '" data-mfqcustomerautoid = "' + value.mfqCustomerAutoID + '" data-description = "' + value.description + '" data-mfqitemid = "' + value.mfqItemID + '" data-unitdes = "' + value.UnitDes + '" data-itemdescription="' + value.itemDescription + '" data-expectedqty = "' + value.sumedQty + '" value="" data-worprocessid = ' + jobID + ' class="radioChk"></div></div></div></td>'+
                                '<td>' + value.itemDescription + ' (' + value.itemSystemCode + ')</td>'+
                                '<td>' + value.expectedQty + '</td>'+
                                '<td>' + value.jobQty + '</td>'+
                                '<td>' + (value.balanceQty) + '</td>'+
                                '<td><input class="qtyGenerate" id="qtyGenerate" name="qtyGenerate" onkeyup="validateBalanceQty(this, '+ value.balanceQty +')"></td>'+
                            '</tr>');


                            // if (value.itemType == 2 || value.itemType == 3) {
                            //     $('#jobContent').append('<div class="">'+
                            //     '<div class="skin skin-square item-iCheck">'+
                            //     ' <div class="skin-section extraColumns">'+
                            //     '<input id="linkItem" name="linkItem" type="radio" data-estimatemasterid= "' + value.estimateMasterID + '" data-bommasterid= "' + value.bomMasterID + '" data-estimatedetailid= "' + value.estimateDetailID + '" data-mfqcustomerautoid = "' + value.mfqCustomerAutoID + '" data-description = "' + value.description + '" data-mfqitemid = "' + value.mfqItemID + '" data-unitdes = "' + value.UnitDes + '" data-itemdescription="' + value.itemDescription + '" data-expectedqty = "' + value.sumedQty + '" value="" data-worprocessid = ' + jobID + ' class="radioChk">&nbsp;&nbsp;&nbsp;&nbsp;<label for="checkbox">' + value.itemDescription + ' (' + value.itemSystemCode + ')</label> </div></div></div><br>');
                            // }
                        });
                    }
                    $('.radioChk').iCheck('uncheck');
                    $('.extraColumns input').iCheck({
                        checkboxClass: 'icheckbox_square_relative-purple',
                        radioClass: 'iradio_square_relative-purple',
                        increaseArea: '20%'
                    });
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            })
        }
    }

    function getWorkFlowStatus(workProcessID) {
        if (workProcessID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {workProcessID: workProcessID},
                url: "<?php echo site_url('MFQ_Job/get_workflow_status'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#route_card_modal').modal();
                    $("#addRoute").hide();
                    $('#route_body').html("<tr><td colspan='6'><div class='callout callout-warning'>Please select a work process flow</td></td></tr>");
                    if (!$.isEmptyObject(data)) {
                        $('#workProcessFlowID').empty();
                        $('#workProcessFlowID')
                            .append($("<option></option>")
                                .attr("value", "")
                                .text("Select"));
                        $.each(data, function (key, value) {
                            $('#workProcessFlowID')
                                .append($("<option></option>")
                                    .attr("value", value["workProcessFlowID"])
                                    .text(value["description"]));
                        });
                        jobID = workProcessID;
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            })
        }
    }

    function loadRouteCard(workFlowID) {
        if (workFlowID) {
            $("#addRoute").show();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {jobID: jobID, workFlowID: workFlowID},
                url: "<?php echo site_url('MFQ_Job/load_route_card'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#route_card_modal').modal();
                    if (!$.isEmptyObject(data)) {
                        $('#route_body').empty();
                        $.each(data, function (key, value) {
                            var checked = "";
                            var checked2 = "";
                            if (value["production"] == 1) {
                                checked = "checked";
                            }
                            if (value["QAQC"] == 1) {
                                checked2 = "checked";
                            }
                            $('#route_body').append('<tr id="rowRC_' + value["routeCardDetailID"] + '"> <td> <textarea class="form-control" name="process[]" placeholder="Process">' + value["process"] + ' </textarea><input type="hidden" class="form-control routeCardDetailID" name="routeCardDetailID[]" value="' + value["routeCardDetailID"] + '">  </td><td> <textarea class="form-control" name="Instructions[]" placeholder="Instructions">' + value["Instructions"] + '</textarea></td> <td><textarea class="form-control" name="acceptanceCriteria[]" placeholder="Acceptance Criteria">' + value["acceptanceCriteria"] + '</textarea> </td> <td class="text-center"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input  name="production[]" type="checkbox" class="checkboxChk production" ' + checked + '>&nbsp;&nbsp;&nbsp;&nbsp;<label for="checkbox"></label> </div></div></td> <td class="text-center"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input name="QAQC[]" type="checkbox" class="checkboxChk QAQC" ' + checked2 + '>&nbsp;&nbsp;&nbsp;&nbsp;<label for="checkbox"></label> </div></div></td> <td class="remove-td" style="vertical-align: middle;text-align: center"><span onclick="delete_routecard(' + value["routeCardDetailID"] + ')" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></td> </tr>');
                        });
                        //$('.checkboxChk').iCheck('uncheck');
                        $('.item-iCheck  input').iCheck({
                            checkboxClass: 'icheckbox_square_relative-purple',
                            radioClass: 'iradio_square_relative-purple',
                            increaseArea: '20%'
                        });
                    } else {
                        $('#route_body').empty();
                        init_route_card();
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            })
        } else {
            $("#addRoute").hide();
            $('#route_body').html("<tr><td colspan='6'><div class='callout callout-warning'>Please select a work process flow</td></td></tr>");

        }
    }

    function updateUsageQty_process_based(jobID) {
        if (jobID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {jobID: jobID},
                url: "<?php echo site_url('MFQ_Job/load_open_jobCard_dropdown'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#jobCard_qtyUpdate').empty();
                    var mySelect = $('#jobCard_qtyUpdate');
                    mySelect.append($('<option></option>').val('').html('Select Job Card'));
                    if (!jQuery.isEmptyObject(data)) {
                        $.each(data, function (val, text) {
                            mySelect.append($('<option></option>').val(text['templateDetailID']).html(text['description']));
                        });
                    }
                    $('#jobID_qtyUpdate').val(jobID);
                    $('.updateTablesView').addClass('hide');
                    $('.selectJobCardView').removeClass('hide');
                    $('#usage_qty_modal').modal();
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            })
        } else {
            $("#addRoute").hide();
            $('#route_body').html("<tr><td colspan='6'><div class='callout callout-warning' style='margin-bottom: 0px'>No items found</td></td></tr>");

        }
    }

    function load_jobCard_updateQty(templateDetailID) {
        if(templateDetailID) {
            $('.updateTablesView').removeClass('hide');
            $('.selectJobCardView').addClass('hide');
            var jobID = $('#jobID_qtyUpdate').val();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {jobID: jobID, templateDetailID:templateDetailID},
                url: "<?php echo site_url('MFQ_Job/load_material_consumption_qty_process_based'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#usage_qty_modal').modal();
                    if (!$.isEmptyObject(data["material"])) {
                        $('#usage_qty_body').empty();
                        $.each(data["material"], function (key, value) {
                            $('#usage_qty_body').append('<tr> <td> <input type="hidden" class="form-control jobID" name="jobID[]" value="' + value["workProcessID"] + '"> <input type="hidden" class="form-control jcMaterialConsumptionID" name="jcMaterialConsumptionID[]" value="' + value["jcMaterialConsumptionID"] + '"><input type="hidden" class="form-control jobCardID" name="jobCardID[]" value="' + value["jobCardID"] + '"> ' + value["itemDescription"] + ' </td><td class="text-right"> ' + value["currentStock"] + '</td><td class="text-right"> ' + value["qtyUsed"] + '</td> <td class="text-right"> ' + value["usageQty"] + '</td> <td><input type="text" class="form-control number qtyUsage" onchange="validate_usage_int(this,'+ (parseFloat(value['currentStock'])+parseFloat(value["usageQty"])) +',\'qtyUsage\',\''+ value['mainCategory'] +'\','+value["currentStock"]+','+value["usageQty"]+')"  onfocus="this.select();" name="qtyUsage[]" placeholder="Qty" value="0"> </td> </tr>');
                        });
                    } else {
                        $('#usage_qty_body').html("<tr><td colspan='7'><div class='callout callout-warning' style='margin-bottom: 0px'>No items found</td></td></tr>");
                    }

                    if (!$.isEmptyObject(data["labour"])) {
                        $('#labour_task_body').empty();
                        $.each(data["labour"], function (key, value) {
                            $('#labour_task_body').append('<tr> <td> <input type="hidden" class="form-control jobID" name="ljobID[]" value="' + value["workProcessID"] + '"> <input type="hidden" class="form-control jcLabourTaskID" name="jcLabourTaskID[]" value="' + value["jcLabourTaskID"] + '"> <input type="hidden" class="form-control jobCardID" name="jobCardID[]" value="' + value["jobCardID"] + '">' + value["description"] + ' </td><td class="text-right"> ' + value["totalHours"] + '</td>  <td class="text-right"> ' + value["usageHours"] + '</td> <td><input type="text" class="form-control number ltotalHours" onkeypress="return validateFloatKeyPress(this,event)" onchange="validate_usage(this,'+value["usageHours"] +',\'ltotalHours\')" onfocus="this.select();" name="ltotalHours[]" placeholder="Hours" value="0"> </td> </tr>');
                        });
                    } else {
                        $('#labour_task_body').html("<tr><td colspan='7'><div class='callout callout-warning' style='margin-bottom: 0px'>No items found</td></td></tr>");
                    }

                    if (!$.isEmptyObject(data["overhead"])) {
                        $('#overhead_body').empty();
                        $.each(data["overhead"], function (key, value) {
                            $('#overhead_body').append('<tr> <td> <input type="hidden" class="form-control jobID" name="ojobID[]" value="' + value["workProcessID"] + '"> <input type="hidden" class="form-control jcOverHeadID" name="jcOverHeadID[]" value="' + value["jcOverHeadID"] + '"> <input type="hidden" class="form-control jobCardID" name="jobCardID[]" value="' + value["jobCardID"] + '">' + value["description"] + ' </td><td class="text-right"> ' + value["totalHours"] + '</td> <td class="text-right"> ' + value["usageHours"] + '</td> <td><input type="text" class="form-control number ototalHours" onkeypress="return validateFloatKeyPress(this,event)" onchange="validate_usage(this,'+value["usageHours"] +',\'ototalHours\')"  onfocus="this.select();" name="ototalHours[]" placeholder="Hours" value="0"> </td> </tr>');
                        });
                    } else {
                        $('#overhead_body').html("<tr><td colspan='7'><div class='callout callout-warning' style='margin-bottom: 0px'>No items found</td></td></tr>");
                    }

                    if (!$.isEmptyObject(data["thirdparty"])) {
                        $('#thirdparty_body').empty();
                        $.each(data["thirdparty"], function (key, value) {
                            //<td class="text-right"> ' + value["usageHours"] + '</td>
                            $('#thirdparty_body').append('<tr> <td> <input type="hidden" class="form-control jobID" name="tpsjobID[]" value="' + value["workProcessID"] + '"> <input type="hidden" class="form-control tpsOverHeadID" name="tpsOverHeadID[]" value="' + value["jcOverHeadID"] + '"> <input type="hidden" class="form-control jobCardID" name="jobCardID[]" value="' + value["jobCardID"] + '">' + value["description"] + ' </td><td class="text-right"> ' + value["totalHours"] + '</td>  <td><input type="text" class="form-control number tpstotalHours" onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();" name="tpstotalHours[]" onchange="validate_usage(this,'+value["usageHours"] +',\'tpstotalHours\')" placeholder="Hours" value="0"> </td> </tr>');
                        });
                    } else {
                        $('#thirdparty_body').html("<tr><td colspan='7'><div class='callout callout-warning' style='margin-bottom: 0px'>No items found</td></td></tr>");
                    }

                    if (!$.isEmptyObject(data["machine"])) {
                        $('#machine_body').empty();
                        $.each(data["machine"], function (key, value) {
                            $('#machine_body').append('<tr> <td> <input type="hidden" class="form-control jobID" name="mjobID[]" value="' + value["workProcessID"] + '"> <input type="hidden" class="form-control jcMachineID" name="jcMachineID[]" value="' + value["jcMachineID"] + '"> ' + value["description"] + ' </td><td class="text-right"> ' + value["totalHours"] + '</td> <td class="text-right"> ' + value["usageHours"] + '</td> <td><input type="text" class="form-control number mtotalHours" onkeypress="return validateFloatKeyPress(this,event)" onchange="validate_usage(this,'+value["usageHours"] +',\'mtotalHours\')"  onfocus="this.select();" name="mtotalHours[]" placeholder="Hours" value="0"> </td> </tr>');
                        });
                    } else {
                        $('#machine_body').html("<tr><td colspan='7'><div class='callout callout-warning' style='margin-bottom: 0px'>No items found</td></td></tr>");
                    }
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            })
        } else {
            $('.updateTablesView').addClass('hide');
            $('.selectJobCardView').removeClass('hide');
        }
    }

    function saveRouteCard() {
        var data = $("#route_card").serializeArray();
        $('.production').each(function () {
            if ($(this).is(':checked')) {
                data.push({'name': 'productionO[]', 'value': 1})
            } else {
                data.push({'name': 'productionO[]', 'value': 0})
            }

        });
        $('.QAQC').each(function () {
            if ($(this).is(':checked')) {
                data.push({'name': 'QAQCO[]', 'value': 1})
            } else {
                data.push({'name': 'QAQCO[]', 'value': 0})
            }
        });
        data.push({'name': 'jobID', 'value': jobID});

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Job/save_route_card'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    loadRouteCard($("#workProcessFlowID").val());
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        })
    }

    function saveUsageQty() {
        var data = $("#usage_qty").serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Job/save_usage_qty'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#usage_qty_modal').modal("hide");
                }
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        })
    }

    function init_route_card() {
        $('#route_body').append('<tr> <td> <textarea class="form-control" name="process[]" placeholder="Process"></textarea> <input type="hidden" class="form-control routeCardDetailID" name="routeCardDetailID[]">  </td><td> <textarea class="form-control" name="Instructions[]" placeholder="Instructions"></textarea></td> <td><textarea class="form-control" name="acceptanceCriteria[]" placeholder="Acceptance Criteria"></textarea> </td> <td class="text-center"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input  name="production[]" type="checkbox" class="checkboxChk production">&nbsp;&nbsp;&nbsp;&nbsp;<label for="checkbox"></label> </div></div></td> <td class="text-center"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input name="QAQC[]" type="checkbox" class="checkboxChk QAQC">&nbsp;&nbsp;&nbsp;&nbsp;<label for="checkbox"></label> </div></div></td> <td class="remove-td" style="vertical-align: middle;text-align: center"></td> </tr>');
        $('.item-iCheck  input').iCheck({
            checkboxClass: 'icheckbox_square_relative-purple',
            radioClass: 'iradio_square_relative-purple',
            increaseArea: '20%'
        });
    }

    function add_more_route() {
        var appendData = $('#mfq_route_card tbody tr:first').clone();
        appendData.find('input').val('');
        appendData.find('textarea').val('');
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr2" style="color:rgb(209, 91, 71);"></span>');
        appendData.find('td:eq(3)').html('<div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input  name="production[]" type="checkbox" class="checkboxChk production">&nbsp;&nbsp;&nbsp;&nbsp;<label for="checkbox"></label> </div></div>');
        appendData.find('td:eq(4)').html('<div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input  name="QAQC[]" type="checkbox" class="checkboxChk QAQC">&nbsp;&nbsp;&nbsp;&nbsp;<label for="checkbox"></label> </div></div>');
        $('#mfq_route_card').append(appendData);

        $('.item-iCheck').find('input').iCheck({
            checkboxClass: 'icheckbox_square_relative-purple',
            radioClass: 'iradio_square_relative-purple',
            increaseArea: '20%'
        });
    }

    function generateJob() {
        if ($('input[name=linkItem]:checked').length != 0) {
            param = [];
            var estimateMasterID = $('input[name=linkItem]:checked').data('estimatemasterid');
            var estimateDetailID = $('input[name=linkItem]:checked').data('estimatedetailid');
            var bomMasterID = $('input[name=linkItem]:checked').data('bommasterid');
            var mfqCustomerAutoID = $('input[name=linkItem]:checked').data('mfqcustomerautoid');
            var description = $('input[name=linkItem]:checked').data('description');
            var mfqItemID = $('input[name=linkItem]:checked').data('mfqitemid');
            var unitDes = $('input[name=linkItem]:checked').data('unitdes');
            var itemDescription = $('input[name=linkItem]:checked').data('itemdescription');
            var expectedQty = $('input[name=linkItem]:checked').data('expectedqty');
            var workProcessID = $('input[name=linkItem]:checked').data('worprocessid');
            var createQty =  $('input[name=linkItem]:checked').closest('tr').find('#qtyGenerate').val();
            param.push(
                {name: 'estimateDetailID', value: estimateDetailID},
                {name: 'estimateMasterID', value: estimateMasterID},
                {name: 'bomMasterID', value: bomMasterID},
                {name: 'mfqCustomerAutoID', value: mfqCustomerAutoID},
                {name: 'description', value: description},
                {name: 'mfqItemID', value: mfqItemID},
                {name: 'unitDes', value: unitDes},
                {name: 'type', value: 2},
                {name: 'itemDescription', value: itemDescription},
                {name: 'expectedQty', value: expectedQty},
                {name: 'createQty', value: createQty},
                {name: 'workProcessID', value: workProcessID});

            if(createQty > 0) {
                $.ajax({
                    async: true,
                    type: 'POST',
                    dataType: 'json',
                    data: param,
                    url: '<?php echo site_url('MFQ_Job/save_sub_job'); ?>',
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == "s") {
                            $('#job_modal').modal('hide');
                            if (data[3]) {
                                $.ajax({
                                    async: true,
                                    type: 'POST',
                                    dataType: 'html',
                                    data: {mrAutoID: data[3]},
                                    url: '<?php echo site_url('MFQ_Job/get_material_request'); ?>',
                                    beforeSend: function () {
                                        startLoad();
                                    },
                                    success: function (dataHtml) {
                                        stopLoad();
                                        jobAutoID = data[2];
                                        $('#material_request_modal').modal();
                                        $('#material_request_body').html(dataHtml);
                                    }, error: function () {
                                        alert('An Error Occurred! Please Try Again.');
                                        stopLoad();
                                    }
                                });
                            } else {
                                setTimeout(function () {
                                    fetchPage('system/mfq/mfq_job_create', data[2], 'Add Job', 'EST');
                                }, 500);
                            }
                        }
                        if (data[2]) {
                            if (!jQuery.isEmptyObject(data[2])) {
                                $('#itemValidate').removeClass('hidden');
                                $("#table_body_insufficient").html("");
                                $.each(data[2], function (k, v) {
                                    $("#table_body_insufficient").append("<tr><td>" + v.itemSystemCode + "</td><td>" + v.itemDescription + "</td></tr>");                            });
                            }
                            $("#insufficient_modal").modal('show');
                        }
                    }, error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                    }
                });
            } else {
                myAlert('e', 'Item Quantity Should be Greater Than 0')
            }
        } else {
            myAlert('w', 'Please select an item')
        }
    }

    function addWorkFlowTemplate() {
        $('#frm_mfq_workflow_template')[0].reset();
        $('#frm_mfq_workflow_template').bootstrapValidator('resetForm', true);
        $('#workFlowTemplateID').val('');
        $('#workflowTemplateModal').modal();
    }

    function job_drillDown_table(d) {
        var workProcessID = d.workProcessID;
        return '<div id="drilldown_' + workProcessID + '"></div>';
    }

    function job_drillDown_table_test(d) {
        var status = $('#filter_subJobStatus').val();
        var workProcessID = d.workProcessID;
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {workProcessID: workProcessID, status: status},
            url: '<?php echo site_url('MFQ_Job/get_mfq_job_drilldown'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#drilldown_" + workProcessID).html(data);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function template() {
        oTable = $('#tbl_mfq_job').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": false,
            "sAjaxSource": "<?php echo site_url('MFQ_Job/fetch_job_process_based'); ?>",
            "aaSorting": [[0, 'desc']],
            language: {
                paginate: {
                    previous: '‹‹',
                    next: '››'
                }
            },
            "aoColumns": [
                {"mData": "workProcessID"},
                {
                    "className": 'details-control',
                    "orderable": false,
                    "data": '',
                    "defaultContent": ''
                },
                {"mData": "documentCode"},
                {"mData": "dates"},
                {"mData": "CustomerName"},
                {"mData": "TemplateDesc"},
                {"mData": "poNumber"},
                {"mData": "description"},
                {"mData": "jobStatus"},
                {"mData": "status"},
                {"mData": "percentage"},
                {"mData": "edit"}
            ],
            "columnDefs": [
                {"targets": [10], "orderable": false},
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                },
            ],
            "fnDrawCallback": function () {
                $("[rel=tooltip]").tooltip();
                $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({"name": "filter_dateTo", "value": $("#filter_dateTo").val()});
                aoData.push({"name": "filter_dateFrom", "value": $("#filter_dateFrom").val()});
                aoData.push({"name": "filter_customerID", "value": $("#filter_customerID").val()});
                aoData.push({"name": "filter_DepartmentID", "value": $("#filter_DepartmentID").val()});
                aoData.push({"name": "filter_mainJobStatus", "value": $("#filter_mainJobStatus").val()});
                aoData.push({"name": "filter_subJobStatus", "value": $("#filter_subJobStatus").val()});
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            }
        });

        // Add event listener for opening and closing details
        $('#tbl_mfq_job tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = oTable.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                row.child(job_drillDown_table(row.data())).show();
                job_drillDown_table_test(row.data());
                tr.addClass('shown');
            }
        });
    }

    function referbackJob(workProcessID) {
        swal({
                title: "Are you sure?",
                text: "You want to refer back!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes!"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'workProcessID': workProcessID},
                    url: "<?php echo site_url('MFQ_Job/referback_job_processBased'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            oTable.draw();
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function job_drillDown2_table(workProcessID) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {workProcessID: workProcessID},
            url: '<?php echo site_url('MFQ_Job/get_mfq_job_drilldown2'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#" + workProcessID).html(data);
                //return data;
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });

    }

    function delete_routecard(routeCardDetailID) {
        swal({
                title: "Are you sure?",
                text: "You want to Delete this record!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "delete",
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: "<?php echo site_url('MFQ_Job/delete_routecard'); ?>",
                    type: 'post',
                    data: {routeCardDetailID: routeCardDetailID},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data['error'] == 1) {
                            swal("Error!", data['message'], "error");
                        }
                        else if (data['error'] == 0) {
                            $("#rowRC_" + routeCardDetailID).remove();
                            if ($('#route_body tr').length == 0) {
                                init_route_card();
                            }
                            swal("Deleted!", data['message'], "success");
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        stopLoad();
                        myAlert('e', xhr.responseText);
                    }
                });
            });
    }

    function validateFloatKeyPress(el, evt) {
        //alert(currency_decimal);
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var number = el.value.split('.');

        if (charCode != 45 && charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        //just one dot
        if (number.length > 1 && charCode == 46) {
            return false;
        }
        //get the carat position
        var caratPos = getSelectionStart(el);
        var dotPos = el.value.indexOf(".");
        if ((caratPos > dotPos) && (dotPos > -(currency_decimal - 1)) && (number[1] && number[1].length > (currency_decimal - 1))) {
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

    function load_jobOrder_view(estimateMasterID, workProcessID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                estimateMasterID: estimateMasterID,
                workProcessID: workProcessID,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/fetch_job_order_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#job_print").html(data);
                $("#job_view_modal").modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function generateReportPdf_job(estimateMasterID, workProcessID) {
        $('#estimateMasterID_pdf').val(estimateMasterID);
        $('#workProcessID_pdf').val(workProcessID);

        var form = document.getElementById('job_order_pdf');
        form.target = '_blank';
        form.action = '<?php echo site_url('MFQ_Estimate/fetch_job_order_view_pdf'); ?>';
        form.submit();
    }

    function saveMaterialRequest() {
        var data = $('#frm_material_request').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Job/save_material_request'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#material_request_modal').modal('hide');
                    setTimeout(function () {
                        fetchPage('system/mfq/mfq_job_create', jobAutoID, 'Add Job', 'EST');
                    }, 500);
                }
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();

            }
        })
    }

    function delete_job(id)
    {
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
                    data: {'workProcessID': id},
                    url: "<?php echo site_url('MFQ_Job/delete_job'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        oTable.draw();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function delete_sub_job(id)
    {
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
                    data: {'workProcessID': id},
                    url: "<?php echo site_url('MFQ_Job/delete_sub_job'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        oTable.draw();
                        refreshNotifications(true);
                    }, error: function () {
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function pulled_documents_process_based(workProcessID, documentCode) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {workProcessID: workProcessID},
            url: '<?php echo site_url('MFQ_Job/get_job_pulled_documents'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#pulled_documents").html(data);
                $('.drilldown-title').html("Job - " + documentCode);
                $("#pulled_documents_processBased_modal").modal({backdrop: "static"});
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    function validate_usage(element,usageQty,colname) {
  
        // if($(element).val() < 0)
        // {
        //     if($(element).val() < (usageQty*-1))
        //     {
        //         $(element).closest('tr').find('.'+colname).val(0)
        //         myAlert('w','usage cannot be greater than ' +usageQty*-1)
        //     }

        // }

        // if($(element).val() > (usageQty))
        // {
        //     $(element).val(0)
        //     myAlert('w','usage cannot be greater than current stock');
        // }
    }

    function validate_usage_int(element,usageQty,colname,type,estimate=null,usage=null){
       
        if($(element).val() > (usageQty))
        {
            if(type == 'Inventory'){
                $(element).val(0)
                myAlert('w','usage cannot be greater than current stock');
            }
        }

        if(type != 'Service'){
         
            if((parseFloat($(element).val()) + parseFloat(usage)) > parseFloat(estimate)){
                $(element).val(0)
                myAlert('w','usage quantity become greater than estimated quantity');
            }
        }
       
    }

    function mfq_job_excel()
    {
        var form = document.getElementById('mfq_job_filter');
        form.target = '_blank';
        form.method = 'post';
        form.action = '<?php echo site_url('MFQ_Job/fetch_job_excel'); ?>';
        form.submit();
    }

    function job_attachments(documentSystemCode, document_name, documentID, confirmedYN)
    {
        $('#attachmentDescription').val('');
        $('#documentSystemCode').val(documentSystemCode);
        $('#document_name').val(document_name);
        $('#documentID').val(documentID);
        $('#confirmYNadd').val(confirmedYN);
        $('#remove_id').click();
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("MFQ_Job/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': confirmedYN},
                success: function (data) {
                    $('#attachment_modal_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "");
                    $('#attachment_modal_body').empty();
                    $('#job_attachment_upload_form')[0].reset();
                    $('#attachment_modal_body').append('' + data + '');
                    $("#job_attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function document_uplode_job()
    {
        var formData = new FormData($("#job_attachment_upload_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('MFQ_Job/do_upload_aws_S3_job'); ?>",
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
                    job_attachments($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val(), $('#confirmYNadd').val());
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
    }

    function validateBalanceQty (element, balanceQty) {
        if(balanceQty < element.value) {
            myAlert('w', 'Qty cannot be greater than Balance Qty!');
            $(element).closest('tr').find('.qtyGenerate').val('');
        }

    }
</script>