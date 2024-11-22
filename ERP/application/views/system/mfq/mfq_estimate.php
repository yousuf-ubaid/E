<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('mfq', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$isGCC= getPolicyValues('MANFL', 'All');
if($isGCC=='GCC'){
    $title = $this->lang->line('manufacturing_quotation');
}
else{
    $title = $this->lang->line('manufacturing_estimate');
}
echo head_page($title, false);
//$showJobOrderForm = getPolicyValues('','All');
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$token_details = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];
?>
<link href="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/datatables/customer-style-datatable.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/mfq/custom-mfq.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('plugins/buttons/button.css'); ?>" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url('plugins/multipleattachment/fileinput.css'); ?>">
<script src="<?php echo base_url('plugins/bootstrap-switch/bootstrap-switch.min.js'); ?>"></script>
<style>
    .sidebar-mini {
        padding-right: 0 !important;
    }

    .btn-filemultiup {
        position: relative;
        overflow: hidden;
    }

    .multipleattachmentbtn {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
    .job_policy_new_modal{
        width: 90%;
    }
</style>
<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <form role="form" id="estimate_filter" class="" autocomplete="off"></form>
    <div class="col-md-5">

    </div>
    <div class="col-md-4 text-center">
        &nbsp;
    </div>
    <div class="col-md-3 text-right">
        <!-- <button type="button" style="margin-right: 17px;" class="btn btn-primary pull-right"
                onclick="fetchPage('system/mfq/mfq_add_new_estimate',null,'<?php echo $this->lang->line('manufacturing_add_estimate') ?>','EST');"><i
                    class="fa fa-plus"></i><?php echo $this->lang->line('manufacturing_new_estimate') ?>New Estimate
        </button> -->
        <button type="button" data-text="Add" id="btnAdd"
                onclick="estimate_excel()"
                class="btn btn-sm btn-success">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> <?php echo $this->lang->line('common_excel') ?><!--Excel-->
        </button>
    </div>
</div>
<div id="">
    <div class="table-responsive" style="margin-top: 10px">
        <table id="estimate_table" class="table table-striped table-condensed" width="100%">
            <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('manufacturing_estimate_code') ?><!--ESTIMATE CODE--></th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('manufacturing_estimate_date') ?><!--ESTIMATE DATE--></th>
                <th class="text-uppercase" style="min-width: 6%"><?php echo $this->lang->line('common_segment') ?><!--SEGMENT--></th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('common_customer') ?><!--CUSTOMER--></th>
                <th class="text-uppercase" style="min-width: 12%"><?php echo $this->lang->line('common_description') ?><!--DESCRIPTION--></th>
                <th class="text-uppercase" style="min-width: 12%">Amount</th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_approval_status') ?><!--APPROVAL STATUS--></th>
                <th class="text-uppercase" style="min-width: 12%;text-align: center"><?php echo $this->lang->line('manufacturing_estimate_status') ?><!--ESTIMATE STATUS--></th>
                <th style="min-width: 8%">&nbsp;</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="estimate_print_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-capitalize" id="myModalLabel"><?php echo $this->lang->line('manufacturing_estimate') ?><!--Estimate--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group col-sm-3 md-offset-2">
                            <label class="title"><?php echo $this->lang->line('manufacturing_revisions') ?><!--Revisions--> : </label>
                        </div>
                        <div class="form-group col-sm-8">
                            <select onchange="changeVersion(this.value)" class="form-control"
                                    id="est-versionLevel"></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group col-sm-4 md-offset-2">
                            <label class="title">Discount : </label>
                        </div>
                        <div class="form-group col-sm-6">
                            <?php echo form_dropdown('discountView', array('1'=> 'View Discount', '0'=>'Hide Discount'), '0', ' onchange="viewDiscount()" class="form-control" id="est-discountView"'); ?>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group" id="hide_total_row">
                                <span class="input-group-addon">
                                    <input type="checkbox" name="hideMargin" id="hideMargin" value="0" onclick="viewDiscount()">
                                </span>
                            <input type="text" class="form-control" disabled="" value="View Discount And Margin/Markup">
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="input-group">
                        <a class="btn btn-default-new size-sm no-print pull-right" id="print_contract_link" id="a_link" target="_blank" href=""> 
                            <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="print">

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

<div class="modal fade" id="job_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 35%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="jobHeader"><?php echo $this->lang->line('manufacturing_job') ?><!--Job--></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="jobContent"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="generateJob()"><?php echo $this->lang->line('manufacturing_generate_job') ?><!--Generate Job--></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="Email_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <?php echo form_open_multipart('', 'role="form" id="Send_Email_form"'); ?>
    <div class="modal-dialog modal-lg" style="width: 35%">
        <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <input type="hidden" name="estimateMasterID" id="estimateMasterID" value="">
                    <h4 class="modal-title" id="EmailHeader"><?php echo $this->lang->line('common_email') ?><!--Emails--></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="emailContent">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                        </div>
                    </div>
                    <div class="append_data_nw">
                        <div class="row removable-div-nw" id="mr_1" style="margin-top: 10px;">
                            <div class="col-sm-1">
                            </div>
                            <div class="col-sm-8">
                                <input type="email" name="emailNW[]" id="emailNW" class="form-control emailNW"
                                       placeholder="@<?php echo $this->lang->line('common_email') ?>" style="margin-left: -10px">
                            </div>
                            <div class="col-sm-1 remov-btn">
                                <button type="button" class="btn btn-primary btn-xs pull-right" id="btn_add_emailNW"
                                        onclick="add_more_nw_mail()"><i class="fa fa-plus"></i></button>
                            </div>

                            <div class="col-sm-1 remove-tdnw">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12 files" id="files3" style="margin-top: 1%;">
                        <span class="btn btn-default btn-filemultiup">
                               <?php echo $this->lang->line('common_attachment') ?><!--Attachment-->  <input type="file" id="files3" name="files3" class="multipleattachmentbtn" multiple/>
                             </span>
                            <br/>
                            <ul class="fileList"></ul>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <div id="status"></div>
                        <div id="photos" class="row"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="loadEmailView()"><?php echo $this->lang->line('common_view') ?><!--View--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('manufacturing_send_email') ?><!--Send Email--></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="additional_order_modal" role="dialog" aria-labelledby="myModalLabel"
     data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 80%">
        <form id="frm_additionalformdetail" class="frm_additionalformdetail" method="post">
            <input type="hidden" name="estimateMasterID" id="estimateMasterID2" value="">
            <input type="hidden" name="mfqCustomerAutoID" id="est-mfqCustomerAutoID2" value="">

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id=""><?php echo $this->lang->line('manufacturing_additional_order_detail') ?><!--Additional Order Detail--> </h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6">

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title">Design Code </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <input type="text" name="designCode" id="designCode"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title">Design Edition </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <input type="text" name="designEditor" id="designEditor"
                                                   class="form-control">
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title">QA/QC documentation </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <?php echo form_dropdown('qcqtDocumentation', array('' => 'Select', '1' => 'Yes', '2' => 'No'), '', 'class="form-control" id="qcqtDocumentation"'); ?>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title"> Material Certification </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <?php echo form_dropdown('materialCertificateID[]', fetch_materialCertificate(), '', 'class="form-control" id="materialCertificateID" multiple="multiple"');
                                                ?>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title"> Material Certification Comment </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                    <textarea class="form-control" id="materialCertificationComment"
                                                              name="materialCertificationComment" rows="3"></textarea>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title">Department</label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <span class="input-req" title="Required Field">
                                            <?php echo form_dropdown('mfqSegmentID', fetch_mfq_segment(), '', 'class="form-control select2" id="mfqSegmentID"');
                                            ?><span class="input-req-inner"></span></span>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title">PO Number</label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <input type="text" name="poNumber" id="poNumber"
                                                   class="form-control">

                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title"> Expected Delivery Date </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-group datepic input-req" title="Required Field">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                <input type='text' class="form-control"
                                                       name="expectedDeliveryDate"
                                                       id="expectedDeliveryDate"
                                                       value=""
                                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title"> Submission of Engineering Drawings </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                <?php echo form_dropdown('engineeringDrawings', array('' => 'Select', '1' => 'Yes', '2' => 'No'), '', 'class="form-control" id="engineeringDrawings"'); ?>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title"> Submission of Engineering Drawings Comment </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                    <textarea class="form-control" id="engineeringDrawingsComment"
                                                              name="engineeringDrawingsComment" rows="3"></textarea>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title"> Submission of ITP </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                <?php echo form_dropdown('submissionOfITP', array('' => 'Select', '1' => 'Yes', '2' => 'No'), '', 'class="form-control" id="submissionOfITP"'); ?>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title"> Submission of ITP Comment </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                    <textarea class="form-control" id="itpComment"
                                                              name="itpComment" rows="3"></textarea>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title">Warehouse</label>
                                        </div>
                                        <div class="form-group col-sm-6">
                         <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('mfqWarehouseAutoID', all_mfq_warehouse_drop(), '', 'class="form-control select2" id="mfqWarehouseAutoID"'); ?>
                             <span class="input-req-inner"></span></span>

                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title"> Order Status </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-req" title="Required Field">
                                                <!--<div class="input-group-addon"><i class="fa fa-calendar"></i></div>-->
                                                <?php echo form_dropdown('orderStatus', array('' => 'Select','1'=> 'Pending','2'=> 'Confirmed & Received'), '2', 'class="form-control" id="orderStatus"'); ?>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-4">
                                            <label class="title"> Awarded Date </label>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <div class="input-group datepic input-req" title="Required Field">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                <input type='text' class="form-control"
                                                       name="awardedDate"
                                                       id="awardedDate"
                                                       value="<?php echo $current_date; ?>"
                                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                                            </div>
                                            <span class="input-req-inner"></span>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-2 md-offset-2">
                                            <label class="title"> Exclusions </label>
                                        </div>
                                        <div class="form-group col-sm-9">
                                            <div class="input-req" title="Required Field">
                                                    <textarea class="form-control richtext" id="exclusions"
                                                              name="exclusions" rows="3"></textarea>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="form-group col-sm-2 md-offset-2">
                                            <label class="title"> Scope of Work </label>
                                        </div>
                                        <div class="form-group col-sm-9">
                                            <div class="input-req" title="Required Field">
                                                    <textarea class="form-control richtext" id="scopeOfWork"
                                                              name="scopeOfWork" rows="3"></textarea>
                                                <span class="input-req-inner"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="jobOrder_print_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Job Order</h4>
            </div>
            <div class="modal-body">
                <form id="frm_jobOrder_print">
                    <input type="hidden" name="estimateMasterID" id="estimateMasterID_jobOrderEdit">
                    <input type="hidden" name="workProcessID" id="workProcessID_jobOrderEdit">

                    <div class="row">
                        <div class="col-md-6">
                            <div id="">
                                <div class="form-group">
                                    <label for="usergroup" class="col-sm-3 control-label">User Group</label>
                                    <div class="col-sm-9">
                                        <?php echo form_dropdown('usergroup[]', all_mfq_usergroup_drop(false), '', 'class="form-control" id="usergroup" multiple="multiple"'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 10px">
                            <div id="jobOrder_print">

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="load_jobOrder_save()">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quotation_print_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Job Order</h4>
            </div>
            <div class="modal-body">
                <form id="">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="quotation_print">

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="job_view_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Job View</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="job_print">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">&nbsp;
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="job_policy_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document" >
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Additional Order Details</h4>            
            </div>
            <form id="job_policy_form">
            <div class="modal-body">

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">Department</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('segmentID', fetch_mfq_segment(), '', 'class="form-control select2" id="segmentID"'); ?><span class="input-req-inner"></span></span>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">Warehouse</label>
                    </div>
                    <div class="form-group col-sm-6">
                         <span class="input-req" title="Required Field">
                        <?php echo form_dropdown('warehouseID', all_mfq_warehouse_drop(), '', 'class="form-control select2" id="warehouseID"'); ?><span class="input-req-inner"></span></span>
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">PO Number</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <span class="input-req" title="Required Field">
                        <input type="text" name="poNumber" id="poNumber">
                    </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-sm-4">
                        <label class="title">PO Date</label>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="input-group datepic input-req" title="Required Field">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            <input type='text' class="form-control"
                                    name="poDate"
                                    id="poDate"
                                    value="<?php echo $current_date; ?>"
                                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'"/>
                        </div>
                    </div>
                </div>
                <!--<input type="text" name="segmentID" id="segmentID">
                <input type="text" name="warehouseID" id="warehouseID"> -->
            </div>
            </form>
            <div class="modal-footer">&nbsp;
                <button type="button" class="btn btn-primary" onclick="save_job_policybased()">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<!-- create job in est with new policy -->
<div class="modal fade" id="job_policy_new_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog job_policy_new_modal" role="document" >
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Additional Order Details</h4>            
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <div class="table-responsive">
                                <table id="mfq_estimate_MConsumption" class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 5%">#</th>
                                        <th style="min-width: 10%">Item</th>
                                        <th style="min-width: 12%">Estimated Qty</th>
                                        <th style="min-width: 12%">Job Qty</th>
                                        <th style="min-width: 12%">Balance Qty</th>
                                        <th style="min-width: 6%">Qty</th>
                                        <th style="min-width: 12%">Department</th>
                                        <th style="min-width: 12%">Warehouse</th>
                                        <th style="min-width: 22%">Workflow Template</th>
                                    </tr>
                                    </thead>
                                    <tbody id="esti_job_generate_table"></tbody>
                                </table>
                            </div>
                        <div id="esti_jobContent"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">&nbsp;
                <button type="button" class="btn btn-primary" onclick="generateEstJob();">Generate Job</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- END -->

<?php echo form_open('login/loginSubmit', ' class="form-horizontal" id="job_order_pdf" role="form"'); ?>
<input class="hidden" id="estimateMasterID_pdf" name="estimateMasterID">
<input class="hidden" id="workProcessID_pdf" name="workProcessID">
</form>

<div class="modal fade" id="BOM_modal" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-capitalize" id="myModalLabel"><?php echo $this->lang->line('manufacturing_bom') ?><!--BOM--></h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <div id="bom_print">

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

<div class="modal fade" id="insufficient_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="50%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Item/Third Party Service Not Configured</h4>
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
                <br>
                <div class="row hidden" id="ServiceValidate">
                    <div class="col-sm-4 pull-left">
                        <label>&nbsp;&nbsp;Third Party Service</label>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="" class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">Third Party Service</th>
                                    <th style="min-width: 12%">Description</th>
                                </tr>
                                </thead>
                                <tbody id="table_body_insufficientService">

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
<div class="modal fade" id="erp_not_linked_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="50%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Item Not Linked to ERP</h4>
            </div>
            <div class="modal-body">
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="" class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 5%">Item System Code</th>
                                    <th style="min-width: 5%">Item Secondary Code</th>
                                    <th style="min-width: 12%">Item Name</th>
                                    <th style="min-width: 12%">Description</th>
                                </tr>
                                </thead>
                                <tbody id="table_body_itemNotLinked">

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

<div class="modal fade" id="attachment_modal_EST" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">ESTIMATE</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="EST_attachment_uplode_form" class="form-inline"'); ?>
                            <div class="form-group">
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
                          <button type="button" class="btn btn-default" onclick="document_uplode_EST()"><span
                                      class="glyphicon glyphicon-floppy-open color" aria-hidden="true"></span></button>
                            </form></span>
                    </div>
                </div>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                        <th><?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                    </tr>
                    </thead>
                    <tbody id="EST_attachment_modal_body" class="no-padding">
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

<div class="modal fade" id="estimate_proposal_review_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     data-width="95%" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-capitalize" id="myModalLabel">Proposal Review<!--Estimate--></h4>
            </div>
            <div class="modal-body">

            <div class="row">
                <div class="col-md-12" style="margin-bottom: 10px;margin-right: 10px;">
                        <button class="btn btn-pdf btn-xs pull-right" id="btn-pdf" type="button">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                        </button>
                </div>
            </div>
                
                <div class="row">
                    <div class="col-md-12">
                     
                        <div>
                            <table style="font-size: 12px" width="100%" cellspacing="0" cellpadding="4" border="1">
                                <thead>
                                <tr>
                                    <th style="width: 15%" ><img width="200px" src="<?php echo $this->common_data['company_data']['company_logo'] ?>" style="width:200px;"></th>
                                    <th style="width: 70%" >CONTRACT / PURCHASE ORDER REVIEW</th>
                                    <th style="width: 15%" >
                                        <table class="tbl-p-1" align="left" cellspacing="0" cellpadding="0">
                                            <tr><td>Form : </td><td> &nbsp;</td></tr>
                                            <tr><td>Issue : </td><td> &nbsp;</td></tr>
                                            <tr><td>Revision : </td><td> &nbsp;</td></tr>
                                        </table>
                                    </th>
                                    
                                </tr>
                                </thead>
                            </table>

                            <br><br>

                            <table class="estimate_tbl1" style="font-size: 12px" width="100%" cellspacing="0" cellpadding="4" border="1">
                                <thead>
                                <tr>
                                    <th bgcolor="#c1e1e8" style="background:#c1e1e8 !important; width: 100%" colspan="2">INTERNAL USE ONLY</th>
                                    
                                </tr>
                                </thead>

                        
                                <tbody id="internal_use">          
                                    
                                </tbody>
                            </table>

                            <br><br>

                            <table class="estimate_tbl1" style="font-size: 12px" width="100%" cellspacing="0" cellpadding="4" border="1">
                                <thead>
                                <tr>
                                    <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 40%" ></th>
                                    <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 30%" >QUOTED PRICE</th>
                                    <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 30%" >GROSS MARGIN</th>
                                </tr>
                                </thead>

                        
                                <tbody id="quoted_price">
                                                      
                                </tbody>
                            </table>

                            <br><br>
                            
                            <table class="estimate_tbl1" style="font-size: 12px" width="100%" cellspacing="0" cellpadding="4" border="1">
                                <thead>
                                    <tr>
                                        <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 10%" rowspan="2"></th>
                                        <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 20%" rowspan="2">Prepared By</th>
                                        <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 40%" colspan="2">Reviewed By</th>
                                        <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 15%" rowspan="2">Acknowledged By</th>
                                        <th bgcolor="#c1e1e8" style="background:#c1e1e8!important; width: 15%" rowspan="2">Approved By</th>
                                        
                                    </tr>

                                    <tr>
                                        
                                        <th style="background:#c1e1e8!important; width: 20%" >Chief Engineer</th>
                                        <th style="background:#c1e1e8!important; width: 20%" >Asst. Eng Manager</th>

                                        
                                        
                                    </tr>
                                </thead>

                        
                                <tbody id="estimate_confirmation">
                                 
                                </tbody>
                            </table>

                            <br><br>
                            <input type="hidden" name="estimateID" id="estimateID">
                            <table class="estimate_tbl1" style="font-size: 12px" width="100%" cellspacing="0" cellpadding="4" border="1">
                                <thead>
                                <tr>
                                    <th style="background:#c1e1e8!important; width: 20%">DEPARTMENT</th>
                                    <th style="background:#c1e1e8!important; width: 40%">COMMENTS</th>
                                    <th style="background:#c1e1e8!important; width: 20%">DATE</th>
                                    <th style="background:#c1e1e8!important; width: 20%">NAME & SIGNATURE</th>
                                    
                                </tr>
                                </thead>

                        
                                <tbody>
                                    <tr>
                                        <td style="font-size: 13px !important;font-weight: 600; width: 20%">SALES</td>
                                        <td style="font-size: 13px !important; width: 40%"><input type="text" onChange="addDepartmentComment(5)" class="form-control" value="" id="comment_5" name="comment_5"></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_5" name="nameSig_5" readonly></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSigName_5" name="nameSigName_5" readonly></td>

                                    </tr>

                                    <tr>
                                        <td style="font-size: 13px !important;font-weight: 600; width: 20%">ESTIMATION</td>
                                        <td style="font-size: 13px !important; width: 40%"><input type="text" onChange="addDepartmentComment(6)" class="form-control" value="" id="comment_6" name="comment_6"></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_6" name="nameSig_6" readonly></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSigName_6" name="nameSigName_6" readonly></td>

                                    </tr>

                                    <tr>
                                        <td style="font-size: 13px !important;font-weight: 600; width: 20%">ENGINEERING</td>
                                        <td style="font-size: 13px !important; width: 40%"><input type="text" onChange="addDepartmentComment(1)" class="form-control" value="" id="comment_1" name="comment_1"></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_1" name="nameSig_1" readonly></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSigName_1" name="nameSigName_1" readonly></td>

                                    </tr>

                                    <tr>
                                        <td style="font-size: 13px !important;font-weight: 600; width: 20%">TECHNICAL / COMMERCIAL</td>
                                        <td style="font-size: 13px !important; width: 40%"><input type="text" onChange="addDepartmentComment(7)" class="form-control" value="" id="comment_7" name="comment_7"></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_7" name="nameSig_7" readonly></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSigName_7" name="nameSigName_7" readonly></td>

                                    </tr>

                                    <tr>
                                        <td style="font-size: 13px !important;font-weight: 600; width: 20%">PRODUCTION</td>
                                        <td style="font-size: 13px !important; 12px; width: 40%"><input type="text" onChange="addDepartmentComment(3)" class="form-control" value="" id="comment_3" name="comment_3"></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_3" name="nameSig_3" readonly></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSigName_3" name="nameSigName_3" readonly></td>
                                    </tr>

                                    <tr>
                                        <td style="font-size: 13px !important;font-weight: 600; width: 20%">QUALITY</td>
                                        <td style="font-size: 13px !important; width: 40%"><input type="text" onChange="addDepartmentComment(4)" class="form-control" value="" id="comment_4" name="comment_4"></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_4" name="nameSig_4" readonly></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSigName_4" name="nameSigName_4" readonly></td>

                                    </tr>

                                    <tr>
                                        <td style="font-size: 13px !important;font-weight: 600; width: 20%">ACCOUNTS</td>
                                        <td style="font-size: 13px !important; width: 40%"><input type="text" onChange="addDepartmentComment(8)" class="form-control" value="" id="comment_8" name="comment_8"></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_8" name="nameSig_8" readonly></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSigName_8" name="nameSigName_8" readonly></td>

                                    </tr>

                                    <tr>
                                        <td style="font-size: 13px !important;font-weight: 600; width: 20%">PROCUREMENT</td>
                                        <td style="font-size: 13px !important; width: 40%"><input type="text" onChange="addDepartmentComment(9)" class="form-control" value="" id="comment_9" name="comment_9"></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSig_9" name="nameSig_9" readonly></td>
                                        <td style="font-size: 13px !important; width: 20%"><input type="text" onChange="" class="form-control" value="" id="nameSigName_9" name="nameSigName_9" readonly></td>

                                    </tr>

                                            
                                    
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
            <hr>
            <div class="col-md-12 approved hide" id="approvedYNMessage">
                <p class="text-bold">Document Approved By : <span id="approvedName"> <span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default-new size-lg" id="btnConfirmProposal" data-dismiss="modal" onclick="confirm_proposal_review()"><?php echo $this->lang->line('common_confirm') ?><!--Close--></button>
                <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal"><?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/multipleattachment/fileinput.min.js'); ?>"></script>
<script type="text/javascript">
    var oTable;
    var param = [];
    var isShowCreateJob = '<?php echo getPolicyValues('SOF', 'All'); ?>';
    var isEstimatePolicy = '<?php echo (getPolicyValues('PBM', 'All')==1?getPolicyValues('PBM', 'All'):0); ?>';
    
    $(document).ready(function () {
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.datepic').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            $('#frm_additionalformdetail').bootstrapValidator('revalidateField', 'expectedDeliveryDate');
            $('#frm_additionalformdetail').bootstrapValidator('revalidateField', 'awardedDate');
        });
        $(".select2").select2();
        $('#materialCertificateID').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            allSelectedText: 'All Selected'
        });

        $('#usergroup').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: true,
            allSelectedText: 'All Selected'
        });

        $('.headerclose').click(function () {
            fetchPage('system/mfq/mfq_estimate', 'Test', 'Estimate');
        });
        estimate_table();

        $('.modal').on('hidden.bs.modal', function (e) {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });

        $('.modal').on('shown.bs.modal', function () {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        });

        $('#frm_additionalformdetail').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
               // exclusions: {validators: {notEmpty: {message: 'Exclusions is required.'}}},
                engineeringDrawings: {validators: {notEmpty: {message: 'Submission of Engineering Drawings is required.'}}},
                engineeringDrawingsComment: {validators: {notEmpty: {message: 'Submission of Engineering Drawings comment is required.'}}},
                submissionOfITP: {validators: {notEmpty: {message: 'Submission of ITP  is required.'}}},
                itpComment: {validators: {notEmpty: {message: 'Submission of ITP Comment is required.'}}},
                qcqtDocumentation: {validators: {notEmpty: {message: 'QA/QC documentation is required.'}}},
                materialCertificateID: {validators: {notEmpty: {message: 'Material certification is required.'}}},
                expectedDeliveryDate: {validators: {notEmpty: {message: 'Expected Delivery Date is required.'}}},
                //scopeOfWork: {validators: {notEmpty: {message: 'Scope of work is required.'}}},
                mfqSegmentID: {validators: {notEmpty: {message: 'Segment is required.'}}},
                mfqWarehouseAutoID: {validators: {notEmpty: {message: 'Warehouse is required.'}}},
                orderStatus: {validators: {notEmpty: {message: 'Order status is required.'}}},
                awardedDate: {validators: {notEmpty: {message: 'Awarded Date is required.'}}},
                materialCertificationComment: {validators: {notEmpty: {message: 'Material Certification Comment is required.'}}},
                /*poNumber: {validators: {notEmpty: {message: 'PO Number is required.'},callback: {
                            callback: function(value, validator, $field) {
                                if (value === '') {
                                    return true;
                                }
                                // Check po valistion
                                if ($("#orderStatus").val() != 2 && value != '') {
                                    return {
                                        valid: false,
                                        message: 'PO Number is required'
                                    };
                                }
                                return true;
                            }}
                    }
                }*/
            }
        }).on('success.form.bv', function (e) {
            e.preventDefault();
            tinymce.triggerSave();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            /* param[0]["mfqSegmentID"] = $("#mfqSegmentID").val();
             param[0]["mfqWarehouseAutoID"] = $("#mfqWarehouseAutoID").val();*/
            //data2 = data.concat(param);
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MFQ_Estimate/save_additional_order_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data[0] == 's') {
                        $('#additional_order_modal').modal('hide');
                        oTable.draw();
                        /*                        setTimeout(function () {
                         fetchPage('system/mfq/mfq_job_create', data[2], 'Add Job', 'EST');
                         }, 500);*/
                        swal("New Job Created!", data[1], "success");
                        load_jobOrder(data[2], data[3]);
                    } else {
                        myAlert(data[0], data[1]);
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

        tinymce.init({
            selector: ".richtext",
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
        })

    });

    function estimate_table() {
        oTable = $('#estimate_table').DataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            /*"bStateSave": true,*/
            "sAjaxSource": "<?php echo site_url('MFQ_Estimate/fetch_estimate'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

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
                 $('.deleted').css('text-decoration', 'line-through');
                $('.deleted div').css('text-decoration', 'line-through');
            },
            "aoColumns": [
                {"mData": "estimateMasterID"},
                {"mData": "estimateCode"},
                {"mData": "documentDate"},
                {"mData": "depcode"},
                {"mData": "CustomerName"},
                {"mData": "description"},
                {"mData": "estAmount"},
                {"mData": "submissionStatus"},
                {"mData": "estimateStatus"},
                {"mData": "edit"}
            ],
            "columnDefs": [{"targets": [7], "orderable": false}, {"targets": [0], "searchable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
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

    function viewDocument(estimateMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                estimateMasterID: estimateMasterID
            },
            url: "<?php echo site_url('MFQ_Estimate/load_mfq_estimate_version'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                /*$('#est-versionLevel').append($("<option></option>").attr("value", " ").text('Select Version'));*/
                $('#est-versionLevel').empty();
                $.each(data, function (key, value) {
                    $('#est-versionLevel').append($("<option></option>").attr("value", value.estimateMasterID).text('[Revision ' + value.versionLevel + '] ' + value.estimateCode));
                });
                $('#est-versionLevel').val(estimateMasterID).change();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function viewProposal(estimateMasterID) {

        $("#estimate_proposal_review_modal").modal();
        $('#estimateID').val('');
        $('#internal_use').empty();
        $('#quoted_price').empty();
        $('#estimate_confirmation').empty();

        for (let i = 1; i < 10; i++) {
            $('#comment_'+i).val('');
            $('#nameSig_'+i).val('');
        }

        $('#estimateID').val(estimateMasterID);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                estimateMasterID: estimateMasterID
            },
            url: "<?php echo site_url('MFQ_Estimate/load_mfq_estimate_proposal_review'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                var totPrice=(data.estimate.totalCost)-(data.estimate.totDiscount);

                $('#internal_use').append('<tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 50%;background: #f9f9f9;">CLIENT NAME</td><td style="font-size: 13px !important; width: 50%">'+data.estimate.CustomerName+'</td></tr><tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight:600; width: 50%;background: #f9f9f9;">CLIENT REFERENCE</td><td style="font-size: 13px !important; width: 50%">'+data.estimate.referenceNo+'</td></tr><tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 50%;background: #f9f9f9;">PROJECT NAME</td><td style="font-size: 13px !important; width: 50%">'+data.estimate.description+'</td></tr><tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 50%;background: #f9f9f9;">MICODA TENDER REFERENCE</td><td style="font-size: 13px !important; width: 50%">'+data.estimate.inqNumber+'</td></tr><tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 50%;background: #f9f9f9;">MICODA QUOTATION</td><td style="font-size: 13px !important; width: 50%">'+data.estimate.estimateCode+'</td></tr><tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 50%;background: #f9f9f9;">CLIENT PURCHASE ORDER</td><td style="font-size: 13px !important; width: 50%"><div class="col-sm-3"><div class="input-group"><input type="text" class="form-control" value="'+data.estimate.poNumber+'" id="ponumber" onChange="addPoNumber('+estimateMasterID+')" name="ponumber" readonly></div></div></td></tr>');
                $('#quoted_price').append('<tr><td bgcolor="#f9f9f9" style="font-size: 13px !important;font-weight: 600; width: 40%;background: #f9f9f9;">QUOTED PRICE AFTER SCOPE FINALIZATION</td><td style="font-size: 13px !important; width: 30%">'+data.estimate.totDiscount+'</td><td style="font-size: 13px !important; width: 30%">'+totPrice+'</td></tr><tr><td style="font-size: 13px !important;font-weight: 600; width: 40%;background: #f9f9f9;">AWARDED PRICE</td><td style="font-size: 13px !important; width: 30%">'+data.estimate.totDiscount+'</td><td style="font-size: 13px !important; width: 30%">-</td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 40%">COMMENTS</td><td style="font-size: 13px !important; width: 60%" colspan="2"><div class="col-sm-6"><div class="input-group"><input type="text" class="form-control" onChange="addQuotedComment('+estimateMasterID+')" value="'+data.estimate.quotedComment+'" id="quotedComment" name="quotedComment"></div></div></td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 40%">NAME OF ESTIMATOR IN-CHARGE:</td><td style="font-size: 13px !important; width: 60%" colspan="2">'+data.estimate.confirmedByName+'</td></tr>');
                $('#estimate_confirmation').append('<tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 10%">NAME</td><td style="font-size: 13px !important; width: 20%">'+data.estimate.confirmedByName+'</td><td style="font-size: 13px !important; width: 20%">---</td><td style="font-size: 13px !important; width: 20%">-</td><td style="font-size: 13px !important; width: 15%">---</td><td style="font-size: 13px !important; width: 15%">'+data.estimate.approvedbyEmpName+'</td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 10%">Date</td><td style="font-size: 13px !important; width: 20%">'+data.estimate.confirmedDate+'</td><td style="font-size: 13px !important; width: 20%">---</td><td style="font-size: 13px !important; width: 20%">-</td><td style="font-size: 13px !important; width: 15%">---</td><td style="font-size: 13px !important; width: 15%">'+data.estimate.approvedDate+'</td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 10%">Signature</td><td style="font-size: 13px !important; width: 20%">---</td><td style="font-size: 13px !important; width: 20%">---</td><td style="font-size: 13px !important; width: 20%">-</td><td style="font-size: 13px !important; width: 15%">---</td><td style="font-size: 13px !important; width: 15%">---</td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 10%">Comments</td><td style="font-size: 13px !important; width: 20%">'+data.estimate.description+'</td><td style="font-size: 13px !important; width: 20%">---</td><td style="font-size: 13px !important; width: 20%">-</td><td style="font-size: 13px !important; width: 15%">---</td><td style="font-size: 13px !important; width: 15%">'+data.estimate.description+'</td></tr><tr><td style="font-size: 13px !important;font-weight: 600;background: #f9f9f9; width: 80%" colspan="5">Upon approval of this contract review form by GM, acknowledgement to be sent to client to inform about MPSI decision to proceed or not by (*) : Name / Date </td><td style="font-size: 13px !important; width: 20%">---</td></tr>');
            
                $.each(data.dpt_data, function (key, value) {

                    $('#comment_'+value.departmentMasterID).val(value.dptComment);
                    $('#nameSig_'+value.departmentMasterID).val(value.modifiedDateTime);
                    $('#nameSigName_'+value.departmentMasterID).val(value.modifiedUserName);
                    
                });

                if(data.estimate.confirmedYN == 1){
                    $('#btnConfirmProposal').addClass('hide');
                    if(data.estimate.approvedYN == 1){
                        $('#approvedYNMessage').removeClass('hide');
                        $('#approvedName').html(data.estimate.approvedbyEmpName);
                    }else{
                        $('#approvedYNMessage').addClass('hide');
                        
                    }
                }else{
                    $('#btnConfirmProposal').removeClass('hide');
                }

            
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function addPoNumber(astimate) {     
         
         var ponum = $('#ponumber').val(); 
         //var mfq_job_id = $('#mfq_job_id').val(); 
 
             $.ajax({
                 type: 'post',
                 dataType: 'json',
                 data: {'estimateMasterID': astimate,'poNumber': ponum},
                 url: "<?php echo site_url('MFQ_Estimate/update_mfq_estimate_po_number'); ?>",
                 beforeSend: function () {
                     startLoad();
                 },
                 success: function (data) {
                     stopLoad();
                     
                     if (data['0']== 's') {
                             myAlert('s', 'Updated Successfully');
                         } else {
                             myAlert('e', 'Please try again later');
                     }
                 },
                 error: function (jqXHR, textStatus, errorThrown) {
                     stopLoad();
                     swal("Cancelled", "Your file is safe :)", "error");
                 }
             });   
 
     }

     function addDepartmentComment(id) {     
         
         var estimateID = $('#estimateID').val(); 
         var comment = $('#comment_'+id).val(); 
         $.ajax({
                 type: 'post',
                 dataType: 'json',
                 data: {'estimateMasterID': estimateID,'dptMasterID': id,'comment':comment},
                 url: "<?php echo site_url('MFQ_Estimate/update_inquiry_department_comment'); ?>",
                 beforeSend: function () {
                     startLoad();
                 },
                 success: function (data) {
                     stopLoad();
                     
                     if (data['0']== 's') {
                             myAlert('s', 'Updated Successfully');
                         } else {
                             myAlert('e', 'Please try again later');
                     }
                 },
                 error: function (jqXHR, textStatus, errorThrown) {
                     stopLoad();
                     swal("Cancelled", "Your file is safe :)", "error");
                 }
             });   
 
     }

     function addQuotedComment(astimate) {     
         
         var quotedComment = $('#quotedComment').val();
 
             $.ajax({
                 type: 'post',
                 dataType: 'json',
                 data: {'estimateMasterID': astimate,'quotedComment': quotedComment},
                 url: "<?php echo site_url('MFQ_Estimate/update_mfq_estimate_quotedComment'); ?>",
                 beforeSend: function () {
                     startLoad();
                 },
                 success: function (data) {
                     stopLoad();
                     
                     if (data['0']== 's') {
                             myAlert('s', 'Updated Successfully');
                         } else {
                             myAlert('e', 'Please try again later');
                     }
                 },
                 error: function (jqXHR, textStatus, errorThrown) {
                     stopLoad();
                     swal("Cancelled", "Your file is safe :)", "error");
                 }
             });   
 
     }

    function changeVersion(estimateMasterID) {

        //print view init
        var print_link = '/index.php/MFQ_Estimate/fetch_estimate_print/'+estimateMasterID+'/0';
        $('#print_contract_link').attr('href',print_link);

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                estimateMasterID: estimateMasterID,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/fetch_estimate_print'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $('#hideMargin').prop('checked', false);
                $("#print").html(data);
                $("#estimate_print_modal").modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_jobOrder(estimateMasterID, workProcessID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                estimateMasterID: estimateMasterID,
                workProcessID: workProcessID,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/fetch_job_order_view_for_save'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#estimateMasterID_jobOrderEdit").val(estimateMasterID);
                $("#workProcessID_jobOrderEdit").val(workProcessID);
                $("#jobOrder_print").html(data);
                $("#jobOrder_print_modal").modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function load_jobOrder_save() {
        var data = $('#frm_jobOrder_print').serialize();
        $.ajax({
            async: true,
            type: 'post',
            data: data,
            dataType: 'json',
            url: "<?php echo site_url('MFQ_Estimate/fetch_job_order_save'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                $('#jobOrder_print_modal').modal('hide');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    // function createJob(estimateMasterID) {
    //     if (estimateMasterID) {
    //         $('#estimateMasterID2').val(estimateMasterID);
    //         $('#frm_additionalformdetail').bootstrapValidator('resetForm', true);
    //         $('#materialCertificateID').multiselect2("clearSelection");
    //         getAdditionalOrderDetail(estimateMasterID);
    //         $('#additional_order_modal').modal('show');

    //         $.ajax({
    //             async: true,
    //             type: 'post',
    //             dataType: 'json',
    //             data: {'estimateMasterID': estimateMasterID},
    //             url: "<?php echo site_url('MFQ_Estimate/load_mfq_estimate_detail'); ?>",
    //             beforeSend: function () {
    //                 startLoad();
    //                 $('#jobContent').html('');
    //             },
    //             success: function (data) {
    //                 stopLoad();
    //                 $('#job_modal').modal();
    //                 if (!$.isEmptyObject(data)) {
    //                     $.each(data, function (key, value) {
    //                         if (value.itemType == 2 || value.itemType == 3) {
    //                             $('#jobContent').append('<div class=""><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="linkItem" name="linkItem" type="radio" data-estimatemasterid= "' + value.estimateMasterID + '" data-bommasterid= "' + value.bomMasterID + '" data-estimatedetailid= "' + value.estimateDetailID + '" data-mfqcustomerautoid = "' + value.mfqCustomerAutoID + '" data-description = "' + value.description + '" data-mfqitemid = "' + value.mfqItemID + '" data-unitdes = "' + value.UnitDes + '" data-itemdescription="' + value.itemDescription + '" data-expectedqty = "' + value.expectedQty + '" value="" class="radioChk">&nbsp;&nbsp;&nbsp;&nbsp;<label for="checkbox">' + value.itemDescription + ' (' + value.itemSystemCode + ')</label> </div></div></div><br>');
    //                         }
    //                     });
    //                 }
    //                 $('.radioChk').iCheck('uncheck');
    //                 $('.extraColumns input').iCheck({
    //                     checkboxClass: 'icheckbox_square_relative-purple',
    //                     radioClass: 'iradio_square_relative-purple',
    //                     increaseArea: '20%'
    //                 });
    //             }, error: function () {
    //                 alert('An Error Occurred! Please Try Again.');
    //                 stopLoad();
    //             }
    //         }) 
    //     }
    // }

/*function create job with policy*/
 function createJob(estimateMasterID) {
    if (estimateMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'estimateMasterID': estimateMasterID},
            url: "<?php echo site_url('MFQ_Estimate/validate_item'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if(data['status'] === 1) {
                    $('#itemValidate').addClass('hidden');
                    $('#ServiceValidate').addClass('hidden');
                    if (!jQuery.isEmptyObject(data['item'])) {
                        $('#itemValidate').removeClass('hidden');
                        myAlert('w', 'Please update missing details for these items!')
                        $("#table_body_insufficient").html("");
                        $.each(data['item'], function (k, v) {
                            $("#table_body_insufficient").append("<tr><td>" + v.itemSystemCode + "</td><td>" + v.itemDescription + "</td></tr>");
                        });
                    }
                    if (!jQuery.isEmptyObject(data['thirdPartyService'])) {
                        $('#ServiceValidate').removeClass('hidden');
                        myAlert('w', 'Please update missing details for these Third Party Service!')
                        $("#table_body_insufficientService").html("");
                        $.each(data['thirdPartyService'], function (k, c) {
                            $("#table_body_insufficientService").append("<tr><td>" + c.overHeadCode + "</td><td>" + c.overHeadDescription + "</td></tr>");
                        });
                    }
                    $("#insufficient_modal").modal({backdrop: 'static'});
                } else if(data['status'] === 2) {
                    if (!jQuery.isEmptyObject(data['notLinkedItem'])) {
                        myAlert('w', 'Please Link these Items to ERP to proceed!')
                        $("#table_body_itemNotLinked").html("");
                        $.each(data['notLinkedItem'], function (k, c) {
                            $("#table_body_itemNotLinked").append("<tr><td>" + c.itemSystemCode + "</td><td>" + c.secondaryItemCode + "</td><td>" + c.itemName + "</td><td>" + c.itemDescription + "</td></tr>");
                        });
                    }
                    $("#erp_not_linked_modal").modal({backdrop: 'static'});
                } else {
                    getAdditionalOrderDetail(estimateMasterID);
                    $('#estimateMasterID2').val(estimateMasterID);

                    if(isEstimatePolicy == 1){
                        load_estimate_detail_items(estimateMasterID);
                    }else{
                        if(isShowCreateJob == 1){
                            $('#frm_additionalformdetail').bootstrapValidator('resetForm', true);
                            $('#materialCertificateID').multiselect2("clearSelection");
                            $('#orderStatus').val(2);
                            $('#additional_order_modal').modal('show');
                        }else{
                            $('#job_policy_modal').bootstrapValidator('resetForm', true);
                            $('#orderStatus').val(2);
                            $('#job_policy_modal').modal('show');
                        }
                    }
                }
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        })
    }
}

    function createEstimateVersion(estimateMasterID) {
        swal({
                title: "Are you sure?",
                text: "You want to create version of this estimate",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: true
            },
            function () {
                if (estimateMasterID) {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'estimateMasterID': estimateMasterID},
                        url: "<?php echo site_url('MFQ_Estimate/save_estimate_version'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data[0] == 's') {
                                fetchPage('system/mfq/mfq_add_new_estimate', data[2], 'Edit Estimate', 'EST');
                            }

                        }, error: function () {
                            alert('An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    })
                }
            });
    }

    /*function generateJob() {
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
            $("#estimateMasterID2").val(estimateMasterID);
            param.push(
                {name: 'estimateDetailID', value: estimateDetailID},
                {name: 'bomMasterID', value: bomMasterID},
                {name: 'mfqCustomerAutoID', value: mfqCustomerAutoID},
                {name: 'description', value: description},
                {name: 'mfqItemID', value: mfqItemID},
                {name: 'unitDes', value: unitDes},
                {name: 'type', value: 2},
                {name: 'itemDescription', value: itemDescription},
                {name: 'expectedQty', value: expectedQty});

            $('#job_modal').modal('hide');
            setTimeout(function () {
                $('#frm_additionalformdetail').bootstrapValidator('resetForm', true);
                $('#materialCertificateID').multiselect2("clearSelection");
                getAdditionalOrderDetail(estimateMasterID);
                $('#additional_order_modal').modal('show');
            }, 500);
        } else {
            myAlert('w', 'Please select an item')
        }
    }*/

    function sendemail(estimateMasterID) {

        $('#estimateMasterID').val(estimateMasterID);
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {estimateMasterID:estimateMasterID},
            url: "<?php echo site_url('MFQ_Estimate/load_emails'); ?>",
            beforeSend: function () {
                startLoad();
                $('#emailContent').html('');
                $('#emailNW').val('');
            },
            success: function (data) {
                stopLoad();
                $("#Email_modal").modal();


                // $(".append_data_nw").remove();

                if (data['customer'].length > 0) {
                    //$('#emailContent').append('<form method="post" id="Send_Email_form"><div class=""><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><ul class="list-group"style="margin-bottom: 5px; > <li class="list-group-item">' + value.email + '</li><li class="list-group-item hidden" id="EmailCusmID">' + value.mfqCustomerAutoID + '</li> <input type="checkbox" name="checkmail[]" value="' + value.customerEmailAutoID + '" ' + checked + ' style="margin-left: 15px;"  id="checkmail"></ul></div></div></div></form><br>');
                    var str = '';
                    $.each(data['customer'], function (key, value) {

                        var checked = '';
                        if (value.isDefault == 1) {
                            checked = 'checked';
                        }

                        str += '<div class="">';
                        str += '<ul class="list-group"style="margin-bottom: 5px; >';
                        str += '<li class="list-group-item">' + value.email + '' +
                            '<div class="col-md-2"><input type="checkbox" name="checkmailid[]" value="' + value.customerEmailAutoID + '" ' + checked + ' style="margin-left: 15px;" ><div>' +
                            '</li> ';

                        str += '</ul>';
                        str += '</div></div></div>';

                    });

                    str += '</ul></div></div></div></form>';
                    $('#emailContent').append(str);
                    if (data['customer']['email'] == '') {
                        var srtnon = '';
                        srtnon += '<div class="text-center alert alert-warning">';
                        srtnon += '<b>No Email Address Found';
                        srtnon += '</b>';
                        srtnon += '</div>';
                        $('#emailContent').append(srtnon);
                    }
                } else {
                    var srtnon = '';
                    srtnon += '<div class="text-center alert alert-warning">';
                    srtnon += '<b>No Email Address Found';
                    srtnon += '</b>';
                    srtnon += '</div>';
                    $('#emailContent').append(srtnon);
                }
                /* $('.emailNW').val('aflal.abdeen@gmail.com');*/


            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

  /*  function SendEstimateMail() {
        var filesToUpload = [];
        var files3Uploader = $("#files3").fileUploader(filesToUpload, "files3");
        var formdata = new FormData($("#Send_Email_form")[0]);
        for (var i = 0, len = filesToUpload.length; i < len; i++) {
            formdata.append("upload[]", filesToUpload[i].file);
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: formdata,
            url: "<?php echo site_url('MFQ_Estimate/send_emails'); ?>",
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data[0] == 's') {
                    oTable.draw();
                    $("#Email_modal").modal('hide');
                }
                myAlert(data[0], data[1]);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }*/

    function add_more_nw_mail() {
        var appendData = $('#mr_1').clone();
        appendData.find('input').val('');
        appendData.find('.remove-tdnw').html('<span class="glyphicon glyphicon-trash remove-trnw" onclick="remove_app_div_nw(this)" style="color:rgb(209, 91, 71);"></span>');
        appendData.find('.remov-btn').remove();
        $('.append_data_nw').append(appendData);

    }

    function remove_app_div_nw(obj) {
        $(obj).closest('.removable-div-nw').remove()
    }

    function referbackEstimate(estimateMasterID) {
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
                    data: {'estimateMasterID': estimateMasterID},
                    url: "<?php echo site_url('MFQ_Estimate/referback_estimate'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            estimate_table()
                        }
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }

    function getAdditionalOrderDetail(estimateMasterID) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("MFQ_Estimate/load_mfq_estimate"); ?>',
            dataType: 'json',
            data: {estimateMasterID: estimateMasterID},
            async: true,
            success: function (data) {
                $("#designCode").val(data['designCode']);
                $("#designEditor").val(data['designEditor']);
                $("#engineeringDrawings").val(data['engineeringDrawings']);
                $("#engineeringDrawingsComment").val(data['engineeringDrawingsComment']);
                $("#submissionOfITP").val(data['submissionOfITP']);
                $("#itpComment").val(data['itpComment']);
                $("#qcqtDocumentation").val(data['qcqtDocumentation']);
                //$("#scopeOfWork").val(data['scopeOfWork']);
                $("#mfqSegmentID").val(data['mfqSegmentID']).change();
                $("#mfqWarehouseAutoID").val(data['mfqWarehouseAutoID']).change();
                $("#est-mfqCustomerAutoID2").val(data['mfqCustomerAutoID']);
                $("#orderStatus").val(data['orderStatus']);
                $("#poNumber").val(data['poNumber']);
                $("#materialCertificationComment").val(data['materialCertificationComment']);
                setTimeout(function () {
                    tinyMCE.get("exclusions").setContent(data['exclusions']);
                }, 1000);
                setTimeout(function () {
                    tinyMCE.get("scopeOfWork").setContent(data['scopeOfWork']);
                }, 1000);

                var valArr = data['materialcertificate'];
                i = 0, size = valArr.length;
                for (i; i < size; i++) {
                    $("#materialCertificateID").multiselect2().find(":checkbox[value='" + valArr[i] + "']").prop("checked", true);
                    $("#materialCertificateID option[value='" + valArr[i] + "']").prop("selected", true);
                    $("#materialCertificateID").multiselect2("refresh");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                myAlert('e', xhr.responseText);
            }
        });
    }

    function loadEmailView() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                estimateMasterID: $("#estimateMasterID").val(),
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/fetch_quotation_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#quotation_print").html(data);
                $("#quotation_print_modal").modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
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
    $.fn.fileUploader = function (filesToUpload, sectionIdentifier) {
        var fileIdCounter = 0;

        this.closest(".files").change(function (evt) {
            var output = [];

            for (var i = 0; i < evt.target.files.length; i++) {
                fileIdCounter++;
                var file = evt.target.files[i];
                var fileId = sectionIdentifier + fileIdCounter;

                filesToUpload.push({
                    id: fileId,
                    file: file
                });

                var removeLink = "<a class=\"removeFile\" href=\"#\" data-fileid=\"" + fileId + "\">Remove</a>";

                output.push("<li><strong>", escape(file.name), "</strong> - ", file.size, " bytes. &nbsp; &nbsp; ", removeLink, "</li> ");
            };

            $(this).children(".fileList")
                .append(output.join(""));

            //reset the input to null - nice little chrome bug!
            evt.target.value = null;
        });

        $(this).on("click", ".removeFile", function (e) {
            e.preventDefault();

            var fileId = $(this).parent().children("a").data("fileid");

            // loop through the files array and check if the name of that file matches FileName
            // and get the index of the match
            for (var i = 0; i < filesToUpload.length; ++i) {
                if (filesToUpload[i].id === fileId)
                    filesToUpload.splice(i, 1);
            }

            $(this).parent().remove();
        });

        this.clear = function () {
            for (var i = 0; i < filesToUpload.length; ++i) {
                if (filesToUpload[i].id.indexOf(sectionIdentifier) >= 0)
                    filesToUpload.splice(i, 1);
            }

            $(this).children(".fileList").empty();
        }

        return this;
    };


    var filesToUpload = [];
    var files3Uploader = $("#files3").fileUploader(filesToUpload, "files3");
    $('#Send_Email_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            to: {validators: {notEmpty: {message: 'To is required'}}},/*To is required*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        tinymce.triggerSave();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = new FormData($("#Send_Email_form")[0]);
        for (var i = 0, len = filesToUpload.length; i < len; i++) {
            data.append("upload[]", filesToUpload[i].file);
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('MFQ_Estimate/send_emails'); ?>",
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
                    $('.btn-primary').prop('disabled', false);
                    oTable.draw();
                    $("#Email_modal").modal('hide');
                    for (var i = 0, len = filesToUpload.length; i < len; i++) {
                        files3Uploader.clear();
                    }

                } else {
                    $('.btn-primary').prop('disabled', true);
                }
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });
    function save_job_policybased()
    { 
          var data = $('#job_policy_form').serializeArray();
          data.push({name: 'estimateMasterID', value: $('#estimateMasterID2').val()});
          data.push({name: 'mfqCustomerAutoID', value: $('#est-mfqCustomerAutoID2').val()});
          
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('MFQ_Estimate/generate_mfq_job'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data[0] == 's') {
                        swal("New Job Created!", data[1], "success");
                        estimate_table();
                    } else {
                        myAlert(data[0], data[1]);
                    }  
                      
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            })


    }

    function delete_estimate(id) {
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
                    data: {'estimateMasterID': id},
                    url: "<?php echo site_url('MFQ_Estimate/delete_estimate'); ?>",
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

    function generateReportPdf_job(estimateMasterID, workProcessID) {
        $('#estimateMasterID_pdf').val(estimateMasterID);
        $('#workProcessID_pdf').val(workProcessID);

        var form = document.getElementById('job_order_pdf');
        form.target = '_blank';
        form.action = '<?php echo site_url('MFQ_Estimate/fetch_job_order_view_pdf'); ?>';
        form.submit();
    }

    function viewItemBOM(bomMasterID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                bomMasterID: bomMasterID,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/fetch_item_bom'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#bom_print").html(data);
                $("#BOM_modal").modal();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function viewDiscount(){
        var estimateMasterID = $('#est-versionLevel').val();
        var discountView = $('#est-discountView').val();
        var hideMargin = ($('#hideMargin').prop('checked'))? '1' : '0';

        var print_link = '/index.php/MFQ_Estimate/fetch_estimate_print/'+estimateMasterID+'/'+hideMargin;
        $('#print_contract_link').attr('href',print_link);

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                discountView: discountView,
                estimateMasterID: estimateMasterID,
                hideMargin: hideMargin,
                html: true
            },
            url: "<?php echo site_url('MFQ_Estimate/change_discount_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if(data) {
                    $("#print").html(data);
                    $("#estimate_print_modal").modal();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function estimate_excel()
    {
        var form = document.getElementById('estimate_filter');
        form.target = '_blank';
        // form.method = 'post';
        form.action = '<?php echo site_url('MFQ_Estimate/fetch_estimate_excel'); ?>';
        form.submit();
    }

    function attachment_modal_EST(documentSystemCode, document_name, documentID, confirmedYN) {
        $('#attachmentDescription').val('');
        $('#documentSystemCode').val(documentSystemCode);
        $('#document_name').val(document_name);
        $('#documentID').val(documentID);
        $('#confirmYNadd').val(confirmedYN);
        $('#remove_id').click();
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': confirmedYN},
                success: function (data) {
                    $('#EST_attachment_modal_body').empty();
                    $('#EST_attachment_modal_body').append('' + data + '');
                    $("#attachment_modal_EST").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function document_uplode_EST() {
        var formData = new FormData($("#EST_attachment_uplode_form")[0]);
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('MFQ_Estimate/upload_attachment_for_estimate'); ?>",
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
                    attachment_modal_EST($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val(), $('#confirmYNadd').val());
                    $('#remove_id').click();
                    $('#attachmentDescription').val('');
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }
    function load_estimate_detail_items(estimateMasterID){
        if (estimateMasterID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'estimateMasterID': estimateMasterID},
                url: "<?php echo site_url('MFQ_Estimate/load_estimate_detail_items'); ?>",
                beforeSend: function () {
                    startLoad();
                    $('#esti_jobContent').html('');
                    $('#esti_job_generate_table').html("");
                },
                success: function (data) {
                    stopLoad();
                    $('#job_policy_new_modal').modal('show');
                    if (!$.isEmptyObject(data)) {
                        var esti_segmentID = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="est_segmentID"'), form_dropdown('est_segmentID[]', fetch_mfq_segment(), 'Each', 'class="form-control select2 "  required')) ?>';
                        var esti_warehouse = '<?php echo str_replace(array("\n", '<select'), array('', '<select id="est_warehouse"'), form_dropdown('esti_warehouse[]', all_mfq_warehouse_drop(), 'Each', 'class="form-control select2 "  required')) ?>';
                        $.each(data, function (key, value) {
                            let _item_tempalte_drop = '<select class="form-control select2" id="est_templateID" name="est_templateID[]" required>';
                            _item_tempalte_drop += '<option value="">Select a template</option>'
                            if(!$.isEmptyObject(value._item_template)){
                                $.each(value._item_template, function (tkey, tm_value) {
                                    
                                    _item_tempalte_drop += '<option value="'+tm_value.templateMasterID +'">'+tm_value.templateDescription +'</option>';
                                });
                            }
                             _item_tempalte_drop += '</select>';
                            $('#esti_job_generate_table').append('<tr>'+
                                 '<td valign="middle"><div class="">'+
                                '<div class="skin skin-square item-iCheck">'+
                                ' <div class="skin-section extraColumns">'+
                                '<input id="linkedItem" name="linkedItem" type="radio" data-estimatemasterid= "' + value.estimateMasterID + '" data-bommasterid= "' + value.bomMasterID + '" data-estimatedetailid= "' + value.estimateDetailID + '" data-mfqcustomerautoid = "' + value.mfqCustomerAutoID + '" data-description = "' + value.description + '" data-mfqitemid = "' + value.mfqItemID + '" data-unitdes = "' + value.UnitDes + '" data-itemdescription="' + value.itemDescription + '" data-expectedqty = "' + value.sumedQty + '" class="radioChk"></div></div></div></td>'+
                                '<td>' + value.itemDescription + ' (' + value.itemSystemCode + ')</td>'+
                                '<td>' + value.expectedQty + '</td>'+
                                '<td>' + value.jobQty + '</td>'+
                                '<td>' + (value.balanceQty) + '</td>'+
                                '<td><input class="qtyGenerate" id="qtyGenerate" name="qtyGenerate" onkeyup="validateBalanceQty(this, '+ value.balanceQty +')"></td>'+
                                '<td>'+ esti_segmentID +'</td>' +
                                '<td>'+ esti_warehouse +'</td>' +
                                '<td>'+ _item_tempalte_drop +'</td>' +
                            '</tr>');

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
    function validateBalanceQty (element, balanceQty) {
        if(balanceQty < element.value) {
            myAlert('w', 'Qty cannot be greater than Balance Qty!');
            $(element).closest('tr').find('.qtyGenerate').val('');
        }
    }
    function generateEstJob(){
        if ($('input[name=linkedItem]:checked').length != 0) {
            data = [];
            var estimateMasterID = $('input[name=linkedItem]:checked').data('estimatemasterid');
            var estimateDetailID = $('input[name=linkedItem]:checked').data('estimatedetailid');
            var bomMasterID = $('input[name=linkedItem]:checked').data('bommasterid');
            var mfqCustomerAutoID = $('input[name=linkedItem]:checked').data('mfqcustomerautoid');
            var description = $('input[name=linkedItem]:checked').data('description');
            var mfqItemID = $('input[name=linkedItem]:checked').data('mfqitemid');
            var unitDes = $('input[name=linkedItem]:checked').data('unitdes');
            var itemDescription = $('input[name=linkedItem]:checked').data('itemdescription');
            var expectedQty = $('input[name=linkedItem]:checked').data('expectedqty');
            var createQty =  $('input[name=linkedItem]:checked').closest('tr').find('#qtyGenerate').val();
            var est_segmentID =  $('input[name=linkedItem]:checked').closest('tr').find('#est_segmentID').val();
            var est_warehouse =  $('input[name=linkedItem]:checked').closest('tr').find('#est_warehouse').val();
            var est_templateID =  $('input[name=linkedItem]:checked').closest('tr').find('#est_templateID').val();

            data.push(
                {name: 'estimateDetailID', value: estimateDetailID},
                {name: 'estimateMasterID', value: estimateMasterID},
                {name: 'bomMasterID', value: bomMasterID},
                {name: 'mfqCustomerAutoID', value: mfqCustomerAutoID},
                {name: 'description', value: description},
                {name: 'estMfqItemID', value: mfqItemID},
                {name: 'itemUoM', value: unitDes},
                {name: 'type', value: 2},
                {name: 'itemDescription', value: itemDescription},
                {name: 'qty', value: createQty},
                {name: 'mfqSegmentID', value: est_segmentID},
                {name: 'mfqWarehouseAutoID', value: est_warehouse},
                {name: 'workFlowTemplateID', value: est_templateID},
                {name: 'isProcessBased', value: 1},
                {name: 'fromType', value: 'EST'},
                {name: 'workProcessID', value: ''},
            );

            if(createQty >= 0 && est_segmentID != '' && est_warehouse != '' && est_templateID != ''){
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    url: "<?php echo site_url('MFQ_Job/save_job_header'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        $('#job_policy_new_modal').modal('hide');
                        myAlert(data[0], data[1],data[2]);
                    
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();

                    }
                });
            }else if(createQty >= 0 && est_segmentID == ''){
                myAlert('e', 'Please select a Segment')
            }else if(createQty >= 0 && est_segmentID != '' && est_warehouse == ''){
                myAlert('e', 'Please select a Warehouse')
            }
            else if(createQty > 0 && est_segmentID != '' && est_warehouse != '' && est_templateID == ''){
                myAlert('e', 'Please select a Workflow Template')
            }else if(createQty < 0){
                myAlert('e', 'Item Quantity Should be Greater Than 0')
            }
            //alert(estimateMasterID +' | '+ estimateDetailID +' | '+bomMasterID +' | '+mfqCustomerAutoID+' | '+description+' | '+mfqItemID+' | '+unitDes+' | '+itemDescription+' | '+expectedQty+' | '+createQty+' | '+est_segmentID+' | '+est_warehouse+' | '+est_templateID );

        }else{
            myAlert('w', 'Please select an item')
        }
    }

    function confirm_proposal_review(){

        var estimateID = $('#estimateID').val();

        swal({
            title: "Are you sure?",
            text: "You want to confirm !",
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
                data: {'estimateID': estimateID},
                url: "<?php echo site_url('MFQ_Estimate/confirm_proposal_review'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    // myAlert(data[0], data[1]);
                    // if (data[0] == 's') {
                    //     estimate_table()
                    // }
                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        });

    }
</script>