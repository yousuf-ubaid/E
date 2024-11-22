<?php
$primaryLanguage = getPrimaryLanguage();
$this->load->helper('employee');
$this->lang->load('hrms_approvals', $primaryLanguage);
$this->lang->load('hrms_payroll_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('hrms_payroll_leave_approval');
$titleTab2 = $this->lang->line('hrms_payroll_general_cancellation_approval');
echo head_page('Document Approval',false);
$address=load_addresstype_drop();
$document_code_arr  = all_document_code_drop(false);
$documentapptemp = fetch_approval_tem_by_link('CINV');
$appDrop = [
    ''=>$this->lang->line('common_please_select'),
    '1'=>$this->lang->line('common_approved'),
    '2'=>$this->lang->line('common_refer_back')
];
$status_arr = [
    '0' => $this->lang->line('common_pending'),
    '1' => $this->lang->line('common_approved')
];
$leaveTypes = leaveTypes_drop();
?>
<style>
    .person-circle {
        background-color: #d7d9da;
        border-radius: 50% !important;
        color: rgba(255, 255, 255, .87);
        font-size: 14px;
        font-weight: bold;
        height: 500px;
        line-height: 42px;
        text-align: center;
        width: 28px;
        position: relative;
    }

    .panel-body {
        margin-bottom: 20px;
        background-color: #ffffff;
        border: 1px solid #dddddd;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
    }
</style>

<div id="filter-panel" class="collapse filter-panel"></div>

<div class="row">
    <div class="form-group col-sm-4">
        <label for="Document">Document</label>
        <div class="d-flex align-items-center">
            <?php echo form_dropdown('Document[]', $document_code_arr, '', 'class="form-control select2" id="Document" multiple="" style="z-index: 0; width: auto;"'); ?>
            <button type="button" class="btn btn-primary ml-2" onclick="Otable.draw()" style="margin-left: 10px;">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</div>

<br>
<div class="table-responsive">
    <table id="address_table" class="<?php echo table_class(); ?>">
        <thead>
            <tr>
                <th style="min-width: 2%">#</th>
                <th style="min-width: 2%">Doc ID</th>
                <th style="min-width: 10%;">Document Code</th>
                <th style="min-width: 15%">Narration</th>
                <th style="min-width: 2%;width: 35px;">Segment</th>
                <th style="min-width: 4%">Party Name</th>
                <th style="min-width: 2%">Amount</th>
                <th style="min-width: 1%">Level</th>
                <th style="min-width: 2%">Action</th>
            </tr>
        </thead>
    </table>
</div>
<?php echo footer_page('Right foot','Left foot',false); ?>

<div class="modal fade" id="Quotation_contract_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" id="quotation_contract_modal_container" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('common_approval');?><!--Approval--></h4>
            </div>
            <form class="form-horizontal" id="quotation_contract_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="cn_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v" data-toggle="tab" onclick="tabView()">
                                    <?php echo $this->lang->line('common_view');?>
                                    <!--View--></a></li>
                            <li id="cn_attachement_approval_Tabview_a"><a href="#Tab-profile-v" data-toggle="tab" onclick="tabAttachement()">
                                    <?php echo $this->lang->line('common_attachment');?>
                                    <!--Attachment--></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                <div id="conform_body"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">
                                        <?php echo $this->lang->line('common_status');?>
                                        <!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/,

                                            '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="status" required'); ?>

                                        <input type="hidden" name="code" id="code">
                                        <input type="hidden" name="Level" id="Level">
                                        <input type="hidden" name=" " id="autoid">
                                        <input type="hidden" name="documentApprovedID" id="documentApprovedID">
                                        <input type="hidden" name=" " id="commonids">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">  <?php echo $this->lang->line('common_comment');?><!--Comments--></label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments" id="comments"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"> <?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                                </div>
                            </div>
                            <div class="tab-pane hide" id="Tab-profile-v">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp <strong><?php echo $this->lang->line('common_attachments');?></strong><!--Credit Note Attachments-->
                                    <br><br>
                                    <table class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('common_file_name');?></th><!--File Name-->
                                            <th><?php echo $this->lang->line('common_description');?></th><!--Description-->
                                            <th><?php echo $this->lang->line('common_type');?></th><!--Type-->
                                            <th><?php echo $this->lang->line('common_action');?></th><!--Action-->
                                        </tr>
                                        </thead>
                                        <tbody id="cn_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?></td><!--No Attachment Found-->
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="grv_modal" tabindex="-1" role="dialog" aria-labelledby="grv_modal_lbl">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">GRV Approval</h4><!--GRV Approval-->
            </div>
            <div class="modal-body">
                <form id="grv_approval_form">
                    <div class="row">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#tabView" aria-controls="home" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_view');?>
                                    </a><!--   View-->
                                </li>
                                <li role="tab_attachment">
                                    <a href="#tab_attachment" aria-controls="profile" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_attachment');?>
                                    </a><!-- Attachment-->
                                </li>
                                <li role="tab_">
                                    <a href="#tab_subItemMaster" aria-controls="messages" role="tab" data-toggle="tab">
                                        Item Master Sub
                                    </a><!-- Item Master Sub-->
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content col-sm-11">
                            <div role="tabpanel" class="tab-pane active" id="tabView">

                                <div id="conform_body_grv"></div>
                                <hr>
                                <div class="form-horizontal">
                                    <div class="form-group ">
                                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?></label><!--Status-->

                                        <div class="col-sm-4">
                                            <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => 'Referred-back'), '', 'class="form-control" id="status_grv" required'); ?>
                                            <input type="hidden" name="Level" id="Levelgrv">
                                            <input type="hidden" name="grvAutoID" id="grvAutoID">
                                            <input type="hidden" name="documentApprovedID" id="documentApprovedIDgrv">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?> </label><!--Comments-->

                                        <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments_grv"></textarea>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-primary btn-sm"><?php echo $this->lang->line('common_submit');?> </button><!--Submit-->
                                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?></button><!--Close-->
                                    </div>
                                </div>

                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_attachment">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp; <strong>GRV Attachments </strong><!--GRV Attachments-->
                                    <br><br>
                                    <table class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('common_file_name');?> </th><!--File Name-->
                                            <th><?php echo $this->lang->line('common_description');?> </th><!--Description-->
                                            <th><?php echo $this->lang->line('common_type');?> </th><!--Type-->
                                            <th><?php echo $this->lang->line('common_action');?> </th><!--Action-->
                                        </tr>
                                        </thead>
                                        <tbody id="grv_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?> </td><!--No Attachment Found-->
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_subItemMaster">
                                <h4>Sub Item Configuration</h4><!--Sub Item Configuration-->
                                <div id="itemMasterSubDiv">

                                </div>

                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="pv_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Payment Voucher Approval<!--Payment Voucher Approval--></h4>
            </div>
            <form class="form-horizontal" id="pv_approval_form">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_view');?>  <!--View-->
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                                        <?php echo $this->lang->line('common_attachment');?>  <!--Attachment-->
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">
                                        Sub Item <!--Sub Item-->
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab panes -->
                        <div class="col-sm-11">
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="home">

                                    <div id="conform_body_PV"></div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                                        <div class="col-sm-4">
                                            <?php echo form_dropdown('status', array('' =>$this->lang->line('common_please_select')/*'Please Select'*/,'1' =>$this->lang->line('common_approved') /*'Approved'*/, '2' =>$this->lang->line('common_referred_back') /*'Referred-back'*/), '', 'class="form-control" id="status_pv" required'); ?>
                                            <input type="hidden" name="Level" id="Level_pv">
                                            <input type="hidden" name="payVoucherAutoId" id="payVoucherAutoId">
                                            <input type="hidden" name="documentApprovedID" id="documentApprovedID_pv">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>

                                        <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments"
                                                  id="comments_pv"></textarea>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                                        <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>

                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="profile">

                                    <div class="table-responsive">
                                        <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                        &nbsp <strong>Payment Voucher Attachments</strong><!--Payment Voucher Attachments-->
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
                                            <tbody id="pv_attachment_body" class="no-padding">
                                            <tr class="danger">
                                                <td colspan="5" class="text-center"><?php echo $this->lang->line('common_no_attachment_found');?><!--No Attachment Found--></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <div role="tabpanel" class="tab-pane" id="messages">
                                    <h4>Sub Item Configuration<!--Sub Item Configuration--></h4>
                                    <div id="itemMasterSubDiv_PV">

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="fa_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Asset Approval</h4>
            </div>
            <form class="form-horizontal" id="fa_approval_form">
                <div class="modal-body">
                    <div id="conform_body_fa"></div>
                    <hr>
                    <div class="ifApproved">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>

                            <div class="col-sm-4">
                                <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_referred_back')/*'Referred-back'*/), '', 'class="form-control" id="status_fa" required'); ?>
                                <input type="hidden" name="Level" id="Level_fa">
                                <input type="hidden" name="faID" id="faID">
                                <input type="hidden" name="documentApprovedID" id="documentApprovedID_fa">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>

                            <div class="col-sm-8">
                                <textarea class="form-control" rows="3" name="comments" id="comments_fa"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer ifApproved">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="fad_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Depreciation Approval<!--Depreciation Approval--></h4>
            </div>
            <form class="form-horizontal" id="fad_approval_form">
                <div class="modal-body">
                    <div id="conform_body_fad"></div>
                    <hr>
                    <div class="ifApprovedfad">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>
                            <div class="col-sm-4">
                                <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_referred_back')/*'Referred-back'*/), '', 'class="form-control" id="status_fad" required'); ?>
                                <input type="hidden" name="Level" id="Level_fad">
                                <input type="hidden" name="depMasterAutoID" id="depMasterAutoID">
                                <input type="hidden" name="documentApprovedID" id="documentApprovedID_fad">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>
                            <div class="col-sm-8">
                                <textarea class="form-control" rows="3" name="comments" id="comments_fad"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer ifApprovedfad">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="adsp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Disposal Approval<!--Disposal Approval--></h4>
            </div>
            <form class="form-horizontal" id="adsp_approval_form">
                <div class="modal-body">
                    <div id="conform_body_adsp"></div>
                    <hr>
                    <div class="ifApproved_adsp">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>
                            <div class="col-sm-4">
                                <?php echo form_dropdown('status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_referred_back')/*'Referred-back'*/), '', 'class="form-control" id="status_adsp" required'); ?>
                                <input type="hidden" name="Level" id="Level_adsp">
                                <input type="hidden" name="assetdisposalMasterAutoID" id="assetdisposalMasterAutoID">
                                <input type="hidden" name="documentApprovedID" id="documentApprovedID_adsp">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comments');?><!--Comments--></label>
                            <div class="col-sm-8">
                                <textarea class="form-control" rows="3" name="comments" id="comments_adsp"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer ifApproved_adsp">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="paysheetApprove_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Payroll Approval<!--Payroll Approval--> <span id="pay-sheet-month"> </span></h4>
            </div>
            <form class="form-horizontal" id="payroll_approval_form">
                <div class="modal-body">

                    <div class="pull-right">
                        <label><span id="payrollHeaderDet" style="display: none;"> </span> </label>
                            <span class="no-print pull-right">
                            <a class="btn btn-default btn-sm" id="payrollAccountReview" target="_blank" href="" style="">
                                <span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review Entries<!--Account Review Entries--> </a>
                        </span>
                    </div>

                    <div class="panel-body" id="load-paysheet">

                    </div>
                    <hr>
                    <div class="form-group form_items">
                        <label for="status" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status'); ?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', array(''=>$this->lang->line('common_please_select'),'1'=>$this->lang->line('common_approved'),'2'=>$this->lang->line('common_refer_back')/*'Referred-back'*/), '','class="form-control controlCls" id="status" required'); ?><!-- /*Please Select*/--><!--'/*Approved*/'-->
                            <input type="hidden" name="level" id="level_paysheet">
                            <input type="hidden" name="doccode" id="doccode">
                            <input type="hidden" name="hiddenPaysheetID" id="hiddenPaysheetID">
                            <input type="hidden" name="hiddenPaysheetCode" id="hiddenPaysheetCode">
                        </div>
                    </div>
                    <div class="form-group form_items">
                        <label for="comments" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comment'); ?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm form_items"><?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="loanApprove_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Loan Approval</h4>
            </div>
            <form class="form-horizontal" id="loan_approval_form">
                <div class="modal-body">
                    <div id="conform_body">
                        <div class="box-body">
                            <div class="col-md-1">
                                <div class="">
                                    <a href="#" class="thumbnail"> <img src="<?php echo base_url(); ?>images/default.gif" id="empImg" alt=""> </a>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <table border="0px">
                                    <tr>
                                        <td class="empDisTbTR">Employee Name</td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="empNameDis"></td>
                                    </tr>
                                    <tr>
                                        <td class="empDisTbTR">Employee Code</td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="empCodeDis"></td>
                                    </tr>
                                    <tr>
                                        <td class="empDisTbTR"><?php echo $this->lang->line('common_designation');?><!--Designation--></td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="empDisgnationDis"></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-5">
                                <table border="0px">
                                    <tr>
                                        <td class="empDisTbTR">Loan Code</td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="disLoanCode">-</td>
                                    </tr>
                                    <tr>
                                        <td class="empDisTbTR">Approved By</td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="empCodeDis">-</td>
                                    </tr>
                                    <tr>
                                        <td class="empDisTbTR"><?php echo $this->lang->line('common_status');?><!--Status--></td>
                                        <td class="" width="10px" align="center"> :</td>
                                        <td class="empDetailDisplay" id="loanStatus">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <table style="width: 100%">
                            <tbody>
                            <tr>
                                <td><b>Loan Type</b></td>
                                <td>:</td>
                                <td id="con_loanType"> </td>
                                <td><b>Percentage</b></td>
                                <td>:</td>
                                <td id="con_intPer"> </td>
                            </tr>

                            <tr>
                                <td><b>Loan Date</b></td>
                                <td>:</td>
                                <td id="con_loanDate"> </td>
                                <td><b>Loan Amount</b></td>
                                <td>:</td>
                                <td id="con_amount"> </td>
                            </tr>

                            <tr>
                                <td><b>No. of Installment</b></td>
                                <td>:</td>
                                <td id="con_noOfIns"> </td>
                                <td><b>Deduction Start Date</b></td>
                                <td>:</td>
                                <td id="con_dedStartDate"> </td>
                            </tr>

                            <tr>
                                <td><b>Loan Description</b></td>
                                <td>:</td>
                                <td colspan="4" id="con_loanDes"> </td>
                            </tr>
                            <tr><td colspan="6">&nbsp;</td></tr>
                            </tbody>
                        </table>

                        <hr>
                        <div style="margin:1%">&nbsp;</div>

                        <table id="" class="<?php echo table_class(); ?> loanScheduleTB">
                            <thead>
                            <tr>
                                <th style="min-width: 5%">#</th>
                                <th style="min-width: 25%"><?php echo $this->lang->line('common_date');?><!--Deduction Date--> </th>
                                <th style="min-width: 15%"><?php echo $this->lang->line('common_amount');?><!--Amount--> </th>
                                <th style="min-width: 5%"><?php echo $this->lang->line('common_status');?><!--Status--></th>
                            </tr>
                            </thead>
                        </table>

                    </div><hr>
                    <div class="form-group  form_items_loan">
                        <label for="status" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', array(''=> $this->lang->line('common_please_select'),'1'=>$this->lang->line('common_approved')/*'Approved'*/,'2'=>$this->lang->line('common_refer_back')/*'Referred-back'*/), '','class="form-control controlCls" id="status_lo" required'); ?>
                            <input type="hidden" name="level" id="level_loan">
                            <input type="hidden" name="hiddenLoanID" id="hiddenLoanID">
                            <input type="hidden" name="documentApprovedID" id="documentApprovedID_loan">
                        </div>
                    </div>
                    <div class="form-group form_items_loan">
                        <label for="comments" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comment');?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments_lo"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                    <button type="submit" class="btn btn-primary btn-sm controlCls form_items_loan"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="finalSettlementApprove_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Final Settlement Approvals</span></h4>
            </div>
            <form class="form-horizontal" id="fs_approval_form">
                <div class="modal-body">



                    <div class="panel-body" id="ajax-container">

                    </div>
                    <hr>
                    <div class="form-group form_items_final_settl">
                        <label for="status" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status'); ?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', $appDrop, '','class="form-control controlCls" id="status_fs" required'); ?>
                            <input type="hidden" name="level" id="level_finalsettlement">
                            <input type="hidden" name="masterID" id="fs_masterID">
                        </div>
                    </div>
                    <div class="form-group form_items_final_settl">
                        <label for="comments" class="col-sm-2 control-label"><?php echo $this->lang->line('common_comment'); ?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments_fs"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm form_items_final_settl"><?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="leaveApprove_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span id="approval-title"></span> <span id="levelText"></span>
                </h4>
            </div>
            <form class="form-horizontal" id="leave_approval_form">
                <div class="modal-body">
                    <div id="app-chk">
                        <div class="panel-body" style="padding: 0px;padding-left: 15px;"><h4 ><?php echo $this->lang->line('hrms_payroll_decu_code');?><!--Document Code--> - <span id="leaveCode"></span> </h4></div>
                        <div class="panel-body">
                            <div class="row" style="margin-bottom: 3px">
                                <div class="col-xs-4 col-sm-2"><label ><?php echo $this->lang->line('hrms_payroll_employee_name');?><!--Employee Name--></label></div>
                                <div class="col-xs-7 col-sm-4">: <span id="empNameSpan" class="frm_input"></span></div>



                                <div class="col-xs-4 col-sm-3"><label ><?php echo $this->lang->line('hrms_payroll_emp_code');?><!--Employee Code--></label></div>
                                <div class="col-xs-7 col-sm-3">: <span id="empCodeSpan" class="frm_input"></span></div>
                            </div>

                            <div class="row" style="">
                                <div class="col-xs-4 col-sm-2"><label><?php echo $this->lang->line('hrms_payroll_designation');?><!--Designation--></label></div>
                                <div class="col-xs-7 col-sm-4">: <span id="designationSpan" class="frm_input"></span></div>



                                <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('common_date');?><!--Date--></label></div>
                                <div class="col-xs-7 col-sm-3">: <span id="dateSpan" class="frm_input"></span>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 3px">
                                <div class="col-xs-4 col-sm-2"><label ><?php echo $this->lang->line('common_department');?><!--Department--><!--Employee Name--></label></div>
                                <div class="col-xs-7 col-sm-4">: <span id="department" class="frm_input"></span></div>



                                <div class="col-xs-4 col-sm-3"><label ><?php echo $this->lang->line('hrms_payroll_reporting_manager');?><!--Reporting Manager--></label></div>
                                <div class="col-xs-7 col-sm-3">: <span id="reportingManager" class="frm_input"></span></div>
                            </div>

                            <div class="row" style="">
                                <div class="col-xs-4 col-sm-2"> <label ><label><?php echo $this->lang->line('hrms_payroll_leave_type');?><!--Leave Type--></label></div>
                                <div class="col-xs-7 col-sm-4">
                                    : <span id="leaveTypeSpan" class="frm_input"></span>
                                    <select name="leaveType" class="form-control frm_input" id="leaveType" style="display: none;">
                                        <option value="">Select a Type</option>
                                        <?php
                                        foreach($leaveTypes as $leave){
                                            echo '<option value="'.$leave['leaveTypeID'].'" data-value="'.$leave['isPaidLeave'].'">'.$leave['description'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('hrms_payroll_leave_policy');?><!--Leave Policy--></label></div>
                                <div class="col-xs-7 col-sm-3">: <span id="policySpan" class="frm_input"></span></div>
                            </div>

                            <div class="row" style="">
                                <div class="col-xs-4 col-sm-2"> <label ><?php echo $this->lang->line('hrms_payroll_leave_entitled');?><!--Leave Entitled--></label></div>
                                <div class="col-xs-7 col-sm-4">: <span id="entitleSpan" class="frm_input"></span></div>



                                <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('hrms_payroll_taken');?><!--Leave Taken--></label></div>
                                <div class="col-xs-7 col-sm-3">: <span id="takenSpan" class="frm_input"></span></div>
                            </div>

                            <div class="row" style="">
                                <div class="col-xs-4 col-sm-2"> <label ><?php echo $this->lang->line('hrms_payroll_balance');?><!--Leave Balance--></label></div>
                                <div class="col-xs-7 col-sm-4">: <span id="balanceSpan" class="frm_input"></span></div>



                                <div class="col-xs-4 col-sm-3"> <label ><?php echo $this->lang->line('hrms_payroll_leave_no_of_days');?><!--No. of Days--></label></div>
                                <div class="col-xs-7 col-sm-3">: <span id="days" class="frm_input"></span></div>
                            </div>

                            <div class="row" style="">
                                <div class="col-xs-4 col-sm-2"> <label ><?php echo $this->lang->line('hrms_payroll_leave_starting_date');?><!--Start Date--></label></div>
                                <div class="col-xs-7 col-sm-4">: <span id="startDateSpan" class="frm_input"></span></div>



                                <div class="col-xs-4 col-sm-3"><label><?php echo $this->lang->line('hrms_payroll_leave_ending_date');?><!--End Date--></label></div>
                                <div class="col-xs-7 col-sm-3">: <span id="endDateSpan" class="frm_input"></span></div>
                            </div>

                            <div class="row" style="">
                                <div class="col-xs-4 col-sm-2"> <label ><?php echo $this->lang->line('hrms_payroll_leave_comment');?><!--Comment--></label></div>
                                <div class="col-xs-7 col-sm-4">: <span id="commentSpan" class="frm_input"></span></div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="form-group approved">
                        <label for="status" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status');?><!--Status--></label>
                        <div class="col-sm-4">
                            <?php echo form_dropdown('status', array(''=>$this->lang->line('common_please_select')/*'Please Select'*/,'1'=>$this->lang->line('common_approved')/*'Approved'*/,'2'=>$this->lang->line('common_refer_back')/*'Referred-back'*/), '','class="form-control controlCls" id="status_leaveapp" required'); ?>
                            <input type="hidden" name="level" id="level_leave_emp">
                            <input type="hidden" name="hiddenLeaveID" id="hiddenLeaveID">
                            <input type="hidden" name="isFromCancelYN" id="isFromCancelYN">
                        </div>
                    </div>
                    <div class="form-group approved">
                        <label for="comments" class="col-sm-2 control-label"><?php echo $this->lang->line('hrms_payroll_leave_comment');?><!--Comments--></label>
                        <div class="col-sm-8">
                            <textarea class="form-control controlCls" rows="3" name="comments" id="comments"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm approved"><?php echo $this->lang->line('common_submit');?><!--Submit--></button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade" id="expense_claim_Approval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Expense Claim Approval</h4>
            </div>
            <form class="form-horizontal" id="ec_approval_form">
                <div class="modal-body">
                    <div class="col-sm-1">
                        <!-- Nav tabs -->
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="po_attachement_approval_Tabview_v" class="active"><a href="#Tab-home-v_ec" data-toggle="tab" onclick="tabView_ec()">
                                    <?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                            <li id="po_attachement_approval_Tabview_a"><a href="#Tab-profile-v_ec" data-toggle="tab" onclick="tabAttachement_ec()">
                                    <?php echo $this->lang->line('common_attachments'); ?><!--Attachment--></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v_ec">
                                <div id="conform_body_ec"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label"><?php echo $this->lang->line('common_status'); ?><!--Status--></label>

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('ec_status', array('' => $this->lang->line('common_please_select')/*'Please Select'*/,'1' => $this->lang->line('common_approved')/*'Approved'*/, '2' => $this->lang->line('common_refer_back')/*'Referred-back'*/), '', 'class="form-control" id="ec_status" required'); ?>
                                        <input type="hidden" name="Level" id="Level_ec">
                                        <input type="hidden" name="expenseClaimMasterAutoID" id="expenseClaimMasterAutoID">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-2 control-label">
                                        <?php echo $this->lang->line('common_comment'); ?><!--Comments--></label>

                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" name="comments" id="comments_ec"></textarea>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo $this->lang->line('common_submit'); ?><!--Submit--></button>
                                </div>
                            </div>

                            <div class="tab-pane hide" id="Tab-profile-v_ec">
                                <div class="table-responsive">
                                    <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span>
                                    &nbsp; <strong><?php echo $this->lang->line('common_expense_claim_attachments'); ?><!--Expense Claim Attachments--></strong>
                                    <br><br>
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
                                        <tbody id="ec_attachment_body" class="no-padding">
                                        <tr class="danger">
                                            <td colspan="5" class="text-center">
                                                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Attachment Found--></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">&nbsp;
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Documemt Attachmet View Modal -->

<div class="modal fade" id="documentAttachmentView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document" id="doc-view-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentAttachmentViewTitle">Modal title</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        
        
                        <div id="loadPageViewAttachment">
                            <div class="table-responsive">
                                <span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp; 
                                <strong> <?php echo $this->lang->line('common_attachments'); ?><!--Attachments--></strong>
                                            
                                <br><br>
                                            
                                <table class="table table-striped table-condensed table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>
                                                <?php echo $this->lang->line('common_file_name'); ?><!--File Name-->
                                            </th>
                                            <th>
                                                <?php echo $this->lang->line('common_description'); ?><!--Description-->
                                            </th>
                                            <th>
                                                <?php echo $this->lang->line('common_type'); ?><!--Type-->
                                            </th>
                                            <th>
                                                <?php echo $this->lang->line('common_action'); ?><!--Action-->
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="document_attachment_modal_body" class="no-padding">

                                        <tr class="danger">
                                            <td colspan="5" class="text-center">
                                                <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found-->
                                            </td>
                                        </tr>
                                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal End -->


<script type="text/javascript">
    var Otable
    var entitleSpan = $('#entitleSpan');
    var code = $('#code').val();
    var takenSpan = $('#takenSpan');
    var balanceSpan = $('#balanceSpan');
    var policySpan = $('#policySpan');
    $(document).ready(function () {

        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/erp_dashboard',' ','Document Approval');
        });
        fetch_address();

        $('#quotation_contract_approval_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                status: {validators: {notEmpty: {message: 'Status is required.'}}},/*Status is required*/

                Level: {validators: {notEmpty: {message: 'Level Order Status is required.'}}},/*Level Order Status is required*/

                documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('sales_markating_level_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
            },

        }).on('success.form.bv', function (e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var url;
            var code = $('#code').val();
            switch (code) {
                case "SO":
                    url = "<?php echo site_url('Quotation_contract/save_quotation_contract_approval'); ?>";
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: url,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }


                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "QUT":
                    url = "<?php echo site_url('Quotation_contract/save_quotation_contract_approval'); ?>";
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: url,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }


                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "CNT":
                    url = "<?php echo site_url('Quotation_contract/save_quotation_contract_approval'); ?>";
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: url,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }


                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "CINV":
                    url = "<?php echo site_url('Quotation_contract/save_quotation_contract_approval'); ?>";
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Invoices/save_invoice_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                               $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            } else if ($.isArray(data[2])) {
                                $('#insufficient_item_body').html('');
                                $.each(data[2], function (item, value) {
                                    $('#insufficient_item_body').append('<tr><td>' + value['itemSystemCode'] + '</td> <td>' + value['itemDescription'] + '</td> <td>' + value['availableStock'] + '</td></tr>')
                                });
                                $("#insufficient_item_modal").modal({backdrop: "static"});
                            }

                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "SLR":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Inventory/save_sales_return_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data['error'] == 1) {
                                myAlert('e', data['message']);
                            } else if (data['error'] == 0) {
                                myAlert('s', data['message']);
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        }
                    });
                    break;
                case "SC":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('sales/save_sc_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }

                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "PRQ":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('PurchaseRequest/save_purchase_request_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }

                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "PO":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Procurement/save_purchase_order_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }

                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "SR":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Inventory/save_stock_return_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('An Error Occurred! Please Try Again.');
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "MI":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Inventory/save_material_issue_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            } else if ($.isArray(data[2])) {
                                $('#insufficient_item_body').html('');
                                $.each(data[2], function (item, value) {
                                    $('#insufficient_item_body').append('<tr><td>' + value['itemSystemCode'] + '</td> <td>' + value['itemDescription'] + '</td> <td>' + value['availableStock'] + '</td></tr>')
                                });
                                $("#insufficient_item_modal").modal({backdrop: "static"});
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "MR":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Inventory/save_material_request_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "MRN":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('MaterialReceiptNote/save_material_receipt_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                case "ST":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Inventory/save_stock_transfer_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            } else if ($.isArray(data[2])) {
                                $('#insufficient_item_body').html('');
                                $.each(data[2], function (item, value) {
                                    $('#insufficient_item_body').append('<tr><td>' + value['itemSystemCode'] + '</td> <td>' + value['itemDescription'] + '</td> <td>' + value['availableStock'] + '</td></tr>')
                                });
                                $("#insufficient_item_modal").modal({backdrop: "static"});
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                            /*An Error Occurred! Please Try Again*/
                            stopLoad();
                            //refreshNotifications(true);
                        }
                    });
                    break;
                case "SA":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Inventory/save_stock_adjustment_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "SCNT":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('StockCounting/save_stock_counting_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "BSI":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Payable/save_supplier_invoice_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "DN":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Payable/save_dn_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            //$("#grv_modal").modal('hide');
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "CN":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Receivable/save_cn_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            //$("#grv_modal").modal('hide');
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "RV":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Receipt_voucher/save_rv_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            //refreshNotifications(true);
                            myAlert(data[0], data[1]);
                            if (data[0] == 's') {
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address()
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            } else if ($.isArray(data[2])) {
                                $('#insufficient_item_body').html('');
                                $.each(data[2], function (item, value) {
                                    $('#insufficient_item_body').append('<tr><td>' + value['itemSystemCode'] + '</td> <td>' + value['itemDescription'] + '</td> <td>' + value['availableStock'] + '</td></tr>')
                                });
                                $("#insufficient_item_modal").modal({backdrop: "static"});
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "JV":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Journal_entry/save_jv_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address()
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "RJV":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Recurring_je/save_rjv_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address()
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "BT":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Bank_rec/save_bank_transfer_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address()
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }

                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "IOU":
                    var data = $form.serializeArray();
                    var status = $('#status').val();
                    if(status == 1)
                    {
                        swal({
                                title: '',
                                text: "This Transaction Will Generate A Payment Voucher," +
                                "Are you sure you want to continue ?",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes"
                            },
                            function () {
                                $.ajax({
                                    async: true,
                                    type: 'post',
                                    dataType: 'json',
                                    data: data,
                                    url: "<?php echo site_url('Iou/save_iou_voucher_approval'); ?>",
                                    beforeSend: function () {
                                        startLoad();
                                    },
                                    success: function (data) {
                                        stopLoad();
                                        refreshNotifications(true);
                                        if (data == true) {
                                            $("#Quotation_contract_modal").modal('hide');
                                            fetch_address()
                                            $form.bootstrapValidator('disableSubmitButtons', false);
                                        }
                                    }, error: function () {
                                        stopLoad();
                                        myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                                    }
                                });
                            });
                    }  else
                    {
                        $.ajax({
                            async: true,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            url: "<?php echo site_url('Iou/save_iou_voucher_approval'); ?>",
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                refreshNotifications(true);
                                if (data == true) {
                                    $("#Quotation_contract_modal").modal('hide');
                                    fetch_address()
                                    $form.bootstrapValidator('disableSubmitButtons', false);
                                }
                            }, error: function () {
                                stopLoad();
                                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            }
                        });
                    }
                    break;
                case "IOUE":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Iou/save_iou_booking_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if (data == true) {
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address()
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        }
                    });
                    break;
                case "BRC":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Bank_rec/save_bank_rec_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "BDT":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Budget_transfer/save_budget_transfer_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            refreshNotifications(true);
                            stopLoad();
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }

                        }, error: function () {
                            alert('An Error Occurred! Please Try Again.');
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "SD":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Employee/save_salary_declaration_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]) ;

                            if( data[0] == 's' ){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }

                        }, error: function () {
                            myAlert('An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });
                    break;
                case "VD":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Employee/approval_variable_pay_declaration'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]) ;

                            if( data[0] == 's' ){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }

                        }, error: function () {
                            myAlert('An Error Occurred! Please Try Again.');
                            stopLoad();
                        }
                    });
                    break;
                case "FU":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Fleet/save_fuel_usage_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "JP":
                    var data = $form.serializeArray();
                    var status = $('#status').val();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Journeyplan/save_jpapproval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if (data == true) {
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            stopLoad();
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                        }
                    });
                    break;
                case "DC":
                    var data = $form.serializeArray();
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('OperationNgo/save_donor_collection_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            refreshNotifications(true);
                            if(data == true){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                            refreshNotifications(true);
                        }
                    });
                    break;
                case "LEC":
                    var data = $form.serializeArray();
                    data.push({'name':'level', 'value': $('#Level').val()});
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Employee/leave_encashment_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if(data[0] == 's'){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                        }
                    });
                    break;
                case "SAR":
                    var data = $form.serializeArray();
                    data.push({'name':'level', 'value': $('#Level').val()});
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Employee/salary_advance_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if(data[0] == 's'){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                        }
                    });
                    break;
                case "HCINV":
                    var data = $form.serializeArray();
                    data.push({'name':'level', 'value': $('#Level').val()});
                    data.push({'name':'status', 'value': $('#status').val()});
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('InvoicesPercentage/save_invoice_approval'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if(data[0] == 's'){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                        }
                    });
                    break;
                case "DO":
                    var data = $form.serializeArray();
                    data.push({'name':'level', 'value': $('#Level').val()});
                    data.push({'name':'status', 'value': $('#status').val()});
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        url: "<?php echo site_url('Delivery_order/approve_delivery_order'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            myAlert(data[0], data[1]);
                            if(data[0] == 's'){
                                $("#Quotation_contract_modal").modal('hide');
                                fetch_address();
                                $form.bootstrapValidator('disableSubmitButtons', false);
                            }
                        }, error: function () {
                            myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                            stopLoad();
                        }
                    });
                    break;
                default:
                    notification('Document ID Not Set', 'w');
                    return false;
            }


        });
    });
    $('#grv_approval_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
            Level: {validators: {notEmpty: {message: 'Level Order Status is required.'}}},/*Level Order Status is required*/
            //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
            grvAutoID: {validators: {notEmpty: {message: 'GRV ID is required.'}}},/*GRV ID is required*/
            documentApprovedID: {validators: {notEmpty: {message: 'Document Approved ID is required.'}}}/*Document Approved ID is required*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Grv/save_grv_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                if (data == true) {
                    $("#grv_modal").modal('hide');
                    fetch_address();
                    $form.bootstrapValidator('disableSubmitButtons', false);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    $('#fa_approval_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_asset_status_is_required');?>.'}}},/*Asset Status is required*/
            Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
            //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
            faID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_asset_id_is_required');?>.'}}},/*Asset ID is required*/
            documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('AssetManagement/save_asset_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                if(data != false){
                    $("#fa_modal").modal('hide');

                    if(data['month'] != ''){
                        //depreciationUserResponse(data['month'], data['faID']);
                    }
                    if(data['accDep']== 1) {
                        myAlert('i','Depreciation created for the following document')
                    }
                    fetch_address();
                    $form.bootstrapValidator('disableSubmitButtons', false);
                }

            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    $('#pv_approval_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
            Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_pv_level_order_is_required');?>.'}}},/*Level Order Status is required*/
            //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
            payVoucherAutoId: {validators: {notEmpty: {message: '<?php echo $this->lang->line('accounts_payable_pv_payment_voucher_id_is_required');?>.'}}},/*Payment Voucher ID is required*/
            documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Payment_voucher/save_pv_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                if (data == true) {
                    $("#pv_modal").modal('hide');
                    fetch_address();
                    $form.bootstrapValidator('disableSubmitButtons', false);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    $('#fad_approval_form').bootstrapValidator({
        live: 'enabled',
        message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
        excluded: [':disabled'],
        fields: {
            status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
            Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
            //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
            faID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_asset_id_is_required');?>.'}}},/*Asset ID is required*/
            documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('AssetManagement/save_depreciation_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                if(data == true){
                    $("#fad_modal").modal('hide');
                    fetch_address();
                    $form.bootstrapValidator('disableSubmitButtons', false);
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });
    $('#adsp_approval_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            status: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_status_is_required');?>.'}}},/*Status is required*/
            Level: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_level_order_status_is_required');?>.'}}},/*Level Order Status is required*/
            //comments: {validators: {notEmpty: {message: 'Comments are required.'}}},
            assetdisposalMasterAutoID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('assetmanagement_disposal_id_is_required');?>.'}}},/*Diposal ID is required*/
            documentApprovedID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('common_document_approved_id_is_required');?>.'}}}/*Document Approved ID is required*/
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('AssetManagement/save_disposal_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                if(data == true){
                    $("#adsp_modal").modal('hide');
                    fetch_address();
                    $form.bootstrapValidator('disableSubmitButtons', false);
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    });
    $('#loan_approval_form').bootstrapValidator({
        live            : 'enabled',
        message         : 'This value is not valid.',
        excluded        : [':disabled'],
        fields          : {
            status     			    : {validators : {notEmpty:{message:'Loan Status is required.'}}},
            Level                   : {validators : {notEmpty:{message:'Level Order Status is required.'}}},
            //comments                : {validators : {notEmpty:{message:'Comments are required.'}}},
            hiddenLoanID    		: {validators : {notEmpty:{message:'Loan ID is required.'}}},
            documentApprovedID      : {validators : {notEmpty:{message:'Document Approved ID is required.'}}}
        },
    }).on('success.form.bv', function(e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : data,
            url :"<?php echo site_url('loan/loanApproval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                /* refreshNotifications(true);
                 $("#loanApprove_modal").modal('hide');
                 loan_table_approval();*/
                myAlert(data[0], data[1]);

                if( data[0] == 's') {
                    $("#loanApprove_modal").modal('hide');
                    fetch_address();
                    $form.bootstrapValidator('disableSubmitButtons', false);
                }

            },error : function(){
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });
    $('#fs_approval_form').bootstrapValidator({
        live            : 'enabled',
        message         : 'This value is not valid.',
        excluded        : [':disabled'],
        fields          : {
            status     			    : {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_status_is_required'); ?>.'}}},
            Level                   : {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_level_order_status_is_required'); ?>.'}}},
            hiddenPaysheetID    	: {validators : {notEmpty:{message:'<?php echo $this->lang->line('common_document_approved_id_is_required'); ?>.'}}}
        }
    }).
    on('success.form.bv', function(e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : data,
            url :"<?php echo site_url('Employee/final_settlement_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's') {
                    $("#finalSettlementApprove_modal").modal('hide');
                    fetch_address();
                    $form.bootstrapValidator('disableSubmitButtons', false);
                }

            },error : function(){
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });
    $('#ec_approval_form').bootstrapValidator({
        live: 'enabled',
        message: 'This value is not valid.',
        excluded: [':disabled'],
        fields: {
            ec_status: {validators: {notEmpty: {message: 'Expense Claim Status is required.'}}},
            expenseClaimMasterAutoID: {validators: {notEmpty: {message: 'Expense Claim ID is required.'}}}
        },
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('ExpenseClaim/save_expense_Claim_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                refreshNotifications(true);
                stopLoad();
                if(data == true){
                    $("#expense_claim_Approval_modal").modal('hide');
                    fetch_address();
                    $form.bootstrapValidator('disableSubmitButtons', false);
                }

            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    });

    function fetch_address() {
        //totaldocumentcount();
        var al = $(".Document").val();
        Otable = $('#address_table').DataTable({"language": {
            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
        },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "bStateSave": true,
            "sAjaxSource": "<?php echo site_url('Documentallapproval/fetch_approvaldocuments'); ?>",
            "aaSorting": [[0, 'desc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                $("[rel=tooltip]").tooltip();
                var tmp_i   = oSettings._iDisplayStart;
                var iLen    = oSettings.aiDisplay.length;
                var x = 0;
                for (var i = tmp_i; (iLen + tmp_i) > i; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[x]].nTr).html(i + 1);
                    x++;
                }
            },
            "aoColumns": [
                {"mData": "DocumentAutoID"},
                {"mData": "docid"},
                {"mData": "DocumentCode"},
                {"mData": "Narration"},
                {"mData": "segmentcodedes"},
                {"mData": "suppliercustomer"},
                {"mData": "total_value"},
                {"mData": "Level"},
                {"mData": "edit"}
              /*  {"mData": "action"}*/
                //{"mData": "edit"},
            ],
            //"columnDefs": [{"targets": [2], "orderable": false}],
            "fnServerData": function (sSource, aoData, fnCallback) {
                aoData.push({ "name": "Document","value":$("#Document").val()});
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



    function fetch_approval(docAutoID, documentApprovedID, Level,code,bankGLAutoID) {
        $('#quotation_contract_modal_container').css({'width': '900px'});

        var data =  new Array();
        var url;
        var idname;
        var status;
        var commonid;
        var commoncol;
        var Document;


        switch (code) {
            case "SO":
               data.push({name: 'contractAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                idname = 'contractAutoID';
                status = 'status';
                url = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;

            case "QUT":
                data.push({name: 'contractAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                idname = 'contractAutoID';
                status = 'status';
                url = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "CNT":
                data.push({name: 'contractAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                idname = 'contractAutoID';
                status = 'status';
                url = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "CINV":
                data.push({name: 'invoiceAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'invoiceAutoID';
                status = 'status';
                <?php if($documentapptemp == 'system/invoices/invoice_approval_insurance') {?>
                    url =  "<?php echo site_url('Invoices/load_invoices_conformation_invoicetype'); ?>";
                <?php } else if ($documentapptemp == 'system/invoices/invoices_approval_margin') {?>
                    url =  "<?php echo site_url('Invoices/load_invoices_conformation_margin'); ?>";
                <?php } else {?>
                    url =  "<?php echo site_url('Invoices/load_invoices_conformation'); ?>";
                <?php }?>
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "SLR":
                data.push({name: 'salesReturnAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'salesReturnAutoID';
                status = 'status';
                url =  "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "SC":
                data.push({name: 'salesCommisionID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'salesCommisionID';
                status = 'status';
                url =  "<?php echo site_url('sales/load_sc_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "PRQ":
                data.push({name: 'purchaseRequestID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'purchaseRequestID';
                status = 'po_status';
                url =  "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "PO":
                data.push({name: 'purchaseOrderID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'purchaseOrderID';
                status = 'po_status';
                url =  "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "SR":
                data.push({name: 'stockReturnAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'stockReturnAutoID';
                status = 'status';
                url =  "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "MI":
                data.push({name: 'itemIssueAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'itemIssueAutoID';
                status = 'status';
                url =  "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "MR":
                data.push({name: 'mrAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'mrAutoID';
                status = 'status';
                url =  "<?php echo site_url('Inventory/load_material_request_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "MRN":
                data.push({name: 'mrnAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'mrnAutoID';
                status = 'status';
                url =  "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "ST":
                data.push({name: 'stockTransferAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'stockTransferAutoID';
                status = 'status';
                url =  "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "SA":
                data.push({name: 'stockAdjustmentAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'stockAdjustmentAutoID';
                status = 'status';
                url =  "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "SCNT":
                data.push({name: 'stockCountingAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'stockCountingAutoID';
                status = 'status';
                url =  "<?php echo site_url('StockCounting/load_stock_counting_approval_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "BSI":
                data.push({name: 'InvoiceAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'InvoiceAutoID';
                status = 'status';
                url =  "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "DN":
                data.push({name: 'debitNoteMasterAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'debitNoteMasterAutoID';
                status = 'status';
                url =  "<?php echo site_url('Payable/load_dn_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "CN":
                data.push({name: 'creditNoteMasterAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'creditNoteMasterAutoID';
                status = 'status';
                url =  "<?php echo site_url('Receivable/load_cn_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "RV":
                data.push({name: 'receiptVoucherAutoId', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'receiptVoucherAutoId';
                status = 'status';
                url =  "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "JV":
                data.push({name: 'JVMasterAutoId', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'JVMasterAutoId';
                status = 'status';
                url =  "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "RJV":
                data.push({name: 'RJVMasterAutoId', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'RJVMasterAutoId';
                status = 'status';
                url =  "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "BT":
                data.push({name: 'bankTransferAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                idname = 'bankTransferAutoID';
                status = 'status';
                url =  "<?php echo site_url('Bank_rec/bank_transfer_view'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "IOU":
                data.push({name: 'voucherAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'iouvoucherid';
                status = 'status';
                url =  "<?php echo site_url('Iou/load_iou_voucher_confirmation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "IOUE":
                data.push({name: 'IOUbookingmasterid', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'ioubookingid';
                status = 'status';
                url =  "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "BRC":
                data.push({name: 'bankRecAutoID', value: docAutoID});
                data.push({name: 'GLAutoID', value: bankGLAutoID});
                data.push({name: 'html', value: true});
                idname = 'bankRecAutoID';
                status = 'status';
                commonid = 'GLAutoID';
                commoncol = bankGLAutoID;
                url =  "<?php echo site_url('Bank_rec/bank_rec_book_balance'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "BDT":
                data.push({name: 'budgetTransferAutoID', value: docAutoID});
                data.push({name: 'approval', value: 1});
                data.push({name: 'html', value: true});
                idname = 'budgetTransferAutoID';
                status = 'status';
                url =  "<?php echo site_url('Budget_transfer/load_budget_transfer_view'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);
                break;
            case "SD":
                data.push({name: 'declarationMasterID', value: docAutoID});
                data.push({name: 'isFromApproval', value: 'Y'});
                data.push({name: 'html', value: true});
                idname = 'salaryOrderID';
                status = 'approval_status';
                url =  "<?php echo site_url('Employee/load_salary_approval_confirmation_view'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);

                break;
            case "VD":
                data.push({name: 'masterID', value: docAutoID});
                data.push({name: 'isFromApproval', value: 'Y'});
                data.push({name: 'html', value: true});
                idname = 'masterID';
                status = 'approval_status';
                url =  "<?php echo site_url('Employee/variable_pay_approval_confirmation_view'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);

                break;
            case "FU":
                data.push({name: 'fuelusageID', value: docAutoID});
                data.push({name: 'html', value: true});
                idname = 'fuelusageID';
                status = 'po_status';
                url =  "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);

                break;
            case "JP":
                data.push({name: 'journeyPlanMasterID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'jurneyplanid';
                status = 'status';
                url =  "<?php echo site_url('Journeyplan/load_jp_view'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);

                break;
            case "DC":
                data.push({name: 'collectionAutoId', value: docAutoID});
                data.push({name: 'html', value: true});
                idname = 'collectionAutoId';
                status = 'po_status';
                url =  "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);

                break;
            case "EC":
                data.push({name: 'expenseClaimMasterAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'expenseClaimMasterAutoID';
                status = 'ec_status';
                url =  "<?php echo site_url('ExpenseClaim/load_expense_claim_conformation'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);

                break;
            case "HCINV":
                data.push({name: 'invoiceAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'invoiceAutoID';
                status = 'hcinv_status';
                url =  "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);

                break;
            case "DO":
                data.push({name: 'orderAutoID', value: docAutoID});
                data.push({name: 'html', value: true});
                data.push({name: 'approval', value: 1});
                idname = 'orderAutoID';
                status = 'do_status';
                url =  "<?php echo site_url('Delivery_order/load_order_confirmation_view'); ?>";
                creditNote_attachment_View_modal(code,docAutoID);

                break;
            default:

                notification('Document ID Not Set', 'w');
                return false;
        }
        if (docAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: data,
                url: url,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#documentApprovedID').val(documentApprovedID);
                    $('#code').val(code);
                    $('#Level').val(Level);
                    $('#autoid').attr('name',idname).val(docAutoID);
                    $('#status').attr('name',status).val('');
                    $('#commonids').attr('name',commonid).val(commoncol);
                    $('#conform_body').html(data);
                    $('#comments').val('');
                    $("#Quotation_contract_modal").modal({backdrop: "static"});
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    refreshNotifications(true);
                }
            });
        }
    }

    function openaddressmodel(id){
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {id:id},
            url: "<?php echo site_url('Address/edit_address'); ?>",
            success: function (data) {
                open_address_model();
                $('#purchasingAddressHead').html('Edit Purchasing Address');
                $('#addressedit').val(id);
                $('#addresstypeid').val(data['addressTypeID']);
                $('#addressdescription').val(data['addressDescription']);
                $('#contactpersonid').val(data['contactPerson']);
                $('#contactpersontelephone').val(data['contactPersonTelephone']);
                $('#contactpersonfaxno').val(data['contactPersonFaxNo']);
                $('#contactpersonemail').val(data['contactPersonEmail']);
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
            }
        });
    }

    function deleteaddress(id){
        swal({   title: "Are you sure?",
            text: "You want to delete this file !",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Delete",
            closeOnConfirm: true },
            function(){
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: {id:id},
                    url: "<?php echo site_url('Address/delete_address'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        refreshNotifications(true);
                        if(data){
                            fetch_address();
                            //fetchPage('system/srp_address_view','Test','Address');
                        }
                    },
                    error: function () {
                        alert('An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
                });
            });
    }

    function fetch_approval_grv(grvAutoID, documentApprovedID, Level) {
        if (grvAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'grvAutoID': grvAutoID, 'html': true, 'approval': 1},
                url: "<?php echo site_url('Grv/load_grv_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $("#Tab-profile-v").addClass("hide");
                    $('#grvAutoID').val(grvAutoID);
                    $('#documentApprovedIDgrv').val(documentApprovedID);
                    $('#Levelgrv').val(Level);
                    $("#grv_modal").modal({backdrop: "static"});
                    $('#conform_body_grv').html(data);
                    $('#comments_grv').val('');
                    $('#status_grv').val('');
                    grv_attachment_View_modal('GRV', grvAutoID);
                    load_itemMasterSub_approval('GRV', grvAutoID);
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }
    function grv_attachment_View_modal(documentID, documentSystemCode) {
        $("#Tab-profile-v").removeClass("active");
        $("#Tab-home-v").addClass("active");
        $("#grv_attachement_approval_Tabview_a").removeClass("active");
        $("#grv_attachement_approval_Tabview_v").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                success: function (data) {
                    $('#grv_attachment_body').empty();
                    $('#grv_attachment_body').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function fetch_approval_paymentvoucher(payVoucherAutoId, documentApprovedID, Level) {
        if (payVoucherAutoId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'payVoucherAutoId': payVoucherAutoId, 'html': true, 'approval': 1},
                url: "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#payVoucherAutoId').val(payVoucherAutoId);
                    $('#documentApprovedID_pv').val(documentApprovedID);
                    $('#Level_pv').val(Level);
                    $("#pv_modal").modal({backdrop: "static"});
                    $('#conform_body_PV').html(data);
                    $('#status_pv').val('');
                    $('#comments_pv').val('');
                    creditNote_attachment_View_modal('PV', payVoucherAutoId);
                    stopLoad();
                    load_itemMasterSub_approval_pv('PV', payVoucherAutoId);
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }

    function creditNote_attachment_View_modal(documentID, documentSystemCode) {
        $("#Tab-profile-v").removeClass("active");
        $("#Tab-home-v").addClass("active");
        $("#cn_attachement_approval_Tabview_a").removeClass("active");
        $("#cn_attachement_approval_Tabview_v").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                success: function (data) {
                    if(documentID == 'PV') {
                        $('#pv_attachment_body').empty();
                        $('#pv_attachment_body').append('' +data+ '');
                    } else {
                        $('#cn_attachment_body').empty();
                        $('#cn_attachment_body').append('' +data+ '');
                    }
                    <!--No Attachment Found-->
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }
    function load_itemMasterSub_approval(receivedDocumentID, grvAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                receivedDocumentID: receivedDocumentID,
                grvAutoID: grvAutoID
            },
            url: "<?php echo site_url('Grv/load_itemMasterSub_approval'); ?>",
            beforeSend: function () {
                $("#itemMasterSubDiv").html('<div  class="text-center" style="margin: 10px 0px;"><i class="fa fa-refresh fa-spin"></i></div>');
            },
            success: function (data) {
                $("#itemMasterSubDiv").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#itemMasterSubDiv").html('<br>Message:<br/> ' + errorThrown);
            }
        });
    }
    function load_itemMasterSub_approval_pv(receivedDocumentID, grvAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                receivedDocumentID: receivedDocumentID,
                grvAutoID: grvAutoID
            },
            url: "<?php echo site_url('Grv/load_itemMasterSub_approval'); ?>",
            beforeSend: function () {
                $("#itemMasterSubDiv_PV").html('<div  class="text-center" style="margin: 10px 0px;"><i class="fa fa-refresh fa-spin"></i></div>');
            },
            success: function (data) {
                $("#itemMasterSubDiv_PV").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#itemMasterSubDiv_PV").html('<br>Message:<br/> ' + errorThrown);
            }
        });
    }
    function fetch_approval_fa(faID, documentApprovedID, Level,documentid,approvedYN) {
        if (approvedYN == 1) {
            $('.ifApproved').hide();
        } else {
            $('.ifApproved').show()
        }
        if (faID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'faID': faID, 'html': true},
                url: "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#faID').val(faID);
                    $('#documentApprovedID_fa').val(documentApprovedID);
                    $('#Level_fa').val(Level);
                    $("#fa_modal").modal({backdrop: "static"});
                    $('#conform_body_fa').html(data);
                    $('#comments_fa').val('');
                    $('#status_fa').val('');
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }
    function fetch_approval_fad(depMasterAutoID, documentApprovedID, Level,$document,approvedYN) {
        if (approvedYN == 1) {
            $('.ifApprovedfad').hide();
        } else {
            $('.ifApprovedfad').show()
        }

        if (depMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'depMasterAutoID': depMasterAutoID, 'html': true},
                url: "<?php echo site_url('AssetManagement/load_dep_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#depMasterAutoID').val(depMasterAutoID);
                    $('#documentApprovedID_fad').val(documentApprovedID);
                    $('#Level_fad').val(Level);
                    $("#fad_modal").modal({backdrop: "static"});
                    $('#conform_body_fad').html(data);
                    $('#comments_fad').val('');
                    $('#status_fad').val('');
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }
    function fetch_approval_adsp(assetdisposalMasterAutoID, documentApprovedID, Level,Documentid,approvedYN) {
        if (approvedYN == 1) {
            $('.ifApproved_adsp').hide();
        } else {
            $('.ifApproved_adsp').show()
        }
        if (assetdisposalMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'assetdisposalMasterAutoID': assetdisposalMasterAutoID, 'html': true},
                url: "<?php echo site_url('AssetManagement/load_disposal_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#assetdisposalMasterAutoID').val(assetdisposalMasterAutoID);
                    $('#documentApprovedID_adsp').val(documentApprovedID);
                    $('#Level_adsp').val(Level);
                    $("#adsp_modal").modal({backdrop: "static"});
                    $('#conform_body_adsp').html(data);
                    $('#comments_adsp').val('');
                    $('#status_adsp').val('');
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }


    function load_paysheetApproval(payrollID ,approvedid,approvalLevel, payMonth, payrollCode,appYN,code){
        $('.form_items').show();
        $('#payroll_approval_form')[0].reset();
        $('#payroll_approval_form').bootstrapValidator('resetForm', true);

        $('#hiddenPaysheetID').val(payrollID);
        $('#hiddenPaysheetCode').val(payrollCode);
        $('#level_paysheet').val(approvalLevel);
        $('#doccode').val(code);
        $('#status').val(1);


        var data =  new Array();
        var url;
        var idname;
        var status;
        switch (code) {
            case "SP":
                data.push({name: 'hidden_payrollID', value: payrollID});
                data.push({name: 'from_approval', value: 'Y'});
                url =  "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>";
                break;

            case "SPN":
                data.push({name: 'hidden_payrollID', value: payrollID});
                data.push({name: 'from_approval', value: 'Y'});
                data.push({name: 'isNonPayroll', value: 'Y'});
                url =  "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>";
                break;
            default:
                notification('Document ID Not Set', 'w');
                return false;
        }



        /*url: ' echo site_url('template_paySheet/paysheetDetail') ?>',*/

        $.ajax({
            type: 'post',
            url: url,
            data:data,
            dataType: 'html',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(appYN==1){
                    $('.form_items').hide();
                }
                else{$('.form_items').show();}
                //$('#pay-sheet-month').html(' &nbsp;&nbsp; '+payMonth);
                $("#paysheetApprove_modal").modal({backdrop: "static"});
                $('#load-paysheet').html(data);

                var printUrl = '<?php echo site_url('Template_paysheet/payrollAccountReview'); ?>';
                $('#payrollAccountReview').attr('href', printUrl+'/' + payrollID + '/N/' + payrollCode + '-' + payMonth);

                $('#paysheet-tb').tableHeadFixer({
                    head: true,
                    foot: true,
                    left: 0,
                    right: 0,
                    'z-index': 0
                });
            },
            error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again'); ?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }
    $('#payroll_approval_form').bootstrapValidator({
        live            : 'enabled',
        message         : 'This value is not valid.',
        excluded        : [':disabled'],
        fields          : {
            status     			    : {validators : {notEmpty:{message:'<?php echo $this->lang->line('hrms_payroll_status_is_required'); ?>.'}}},/*Status is required*/
            Level                   : {validators : {notEmpty:{message:'<?php echo $this->lang->line('hrms_payroll_level_order_status_is_required'); ?>.'}}},/*Level Order Status is required*/
            //comments                : {validators : {notEmpty:{message:'Comments are required.'}}},
            hiddenPaysheetID    	: {validators : {notEmpty:{message:'<?php echo $this->lang->line('hrms_payroll_payroll_id_is_required'); ?>.'}}}/*Payroll ID is required*/
        },
    }).
    on('success.form.bv', function(e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
         var code = $('#doccode').val();
        var data = $form.serializeArray();
        switch (code) {
            case "SP":
                data.push({name: 'isNonPayroll', value: 'N'});
                break;

            case "SPN":
                data.push({name: 'isNonPayroll', value: 'Y'});
                break;
            default:
                notification('Document ID Not Set', 'w');
                return false;
        }

         $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            data : data,
            url :"<?php echo site_url('Template_paysheet/paysheetApproval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){
                stopLoad();
                myAlert(data[0], data[1]);

                if( data[0] == 's') {
                    $("#paysheetApprove_modal").modal('hide');
                    fetch_address();
                    $form.bootstrapValidator('disableSubmitButtons', false);
                }

            },error : function(){
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    });


    $('#leave_approval_form').bootstrapValidator({
        live            : 'enabled',
        message         : 'This value is not valid.',
        excluded        : [':disabled'],
        fields          : {
            //status     			    : {validators : {notEmpty:{message:'Status is required.'}}},
            //Level                   : {validators : {notEmpty:{message:'Level Order Status is required.'}}},
            //comments                : {validators : {notEmpty:{message:'Comments are required.'}}},
            //hiddenLeaveID    		: {validators : {notEmpty:{message:'Leave ID is required.'}}}
        },
    })
        .on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();

            $.ajax({
                async : true,
                type : 'post',
                dataType : 'json',
                data : data,
                url :"<?php echo site_url('Employee/leaveApproval'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success : function(data){
                    stopLoad();
                    myAlert(data[0], data[1]);

                    if( data[0] == 's') {
                        $("#leaveApprove_modal").modal('hide');

                        if( $('#isFromCancelYN').val() == 1){
                            fetch_address();
                        }
                        else{
                            fetch_address();
                        }

                        $('#comments').val('');
                        $form.bootstrapValidator('disableSubmitButtons', false);
                    }

                },error : function(){
                    myAlert('e', 'An Error Occurred! Please Try Again.');
                    stopLoad();
                }
            });
        });

    function load_emp_loanDet(loanID , documentID, approvalLevel,appYN){
        $('.form_items_loan').show();
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('loan/load_emp_loanDet') ?>',
            data: {'loanID': loanID},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if(appYN==1){
                    $('.form_items_loan').hide();
                }
                else{$('.form_items_loan').show();}
                stopLoad();
                loadLoanSchedule(loanID);

                $("#loanApprove_modal").modal({backdrop: "static"});
                var intPer = ( data['interestPer'] == 0 ) ? '' : data['interestPer'];

                $('#hiddenLoanID').val(data['ID']);
                $('#documentApprovedID_loan').val(documentID);
                $('#level_loan').val(approvalLevel);
                $('#hiddenLoanCode').val(data['loanCode']);
                $('#empID').val(data['EIdNo']);
                $('#empName').val(data['Employee']);
                $('#empNameDis').text(data['Employee']);
                $('#empCodeDis').text(data['ECode']);
                $('#empDisgnationDis').text(data['DesDescription']);
                $('#disLoanCode').text(data['loanCode']);
                $('#status_lo').val('');
                $('#comments_lo').val('');


                //values for conformation tab
                intPer = ( intPer == '' )? '-' : intPer;
                $('#con_loanType').text( data['description'] );
                $('#con_intPer').text(intPer);
                $('#con_loanDate').text(data['loanDate']);
                $('#con_amount').text(commaSeparateNumber(data['amount']));
                $('#con_noOfIns').text(data['numberOfInstallment']);
                $('#con_dedStartDate').text(data['deductionStartingDate']);
                $('#con_loanDes').text(data['loanDescription']);

                $('.controlCls').prop('disabled', false);
                if( approvalLevel > 1 ){
                    isPreviousLevelsApproved(loanID , documentID, approvalLevel);
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }
    function loadLoanSchedule(loanID){
        var Otable = $('.loanScheduleTB').DataTable({
            "language": {
                "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
            },
            "bProcessing": true,
            "bServerSide": true,
            "bDestroy": true,
            "StateSave": true,
            "sAjaxSource": "<?php echo site_url('Loan/load_empLoanSchedule?loanID='); ?>"+loanID,
            "aaSorting": [[2, 'asc']],
            "fnInitComplete": function () {

            },
            "fnDrawCallback": function (oSettings) {
                if (oSettings.bSorted || oSettings.bFiltered) {
                    for (var i = 0, iLen = oSettings.aiDisplay.length; i < iLen; i++) {
                        $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
                    }
                }

            },
            "aoColumns": [
                {"mData": "scheduleID"},
                {"mData": "scheduleDate1"},
                {"mData": "amount"},
                {"mData": "status"}
            ],
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
    function totaldocumentcount()
    {
        $.ajax({
            async : true,
            type : 'post',
            dataType : 'json',
            url :"<?php echo site_url('Documentallapproval/total_document_count'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success : function(data){

                $('#totalapprovalcount').html(data);
                stopLoad();

            },error : function(){
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }
    function load_approvalView(id, level, isApproved){
            $('#comments_fs').val('');
            $('#status_fs').val('');
        $("#finalSettlementApprove_modal").modal({backdrop: "static"});
        $('#fs_masterID').val(id);
        $('#level_finalsettlement').val(level);

        $('.form_items_final_settl').show();

        if(isApproved == 1){
            $('.form_items_final_settl').hide();
        }

        $.ajax({
            async: true,
            type: 'POST',
            url: '<?php echo site_url("dashboard/fetchPage"); ?>',
            dataType: 'html',
            data: {
                'page_id': id,
                'page_url': 'system/hrm/ajax/final-settlement-approval-view',
                'page_name': 'HRMS',
                'isFromApproval': 1
            },
            beforeSend: function () {
                startLoad();
            },
            success: function (page_html) {
                stopLoad();
                $('#ajax-container').html(page_html);
            },
            error: function (jqXHR, status, errorThrown) {
                stopLoad();
                myAlert('e', jqXHR.responseText + '<br/>Error Message: ' + errorThrown);
            }
        });
    }

    function load_emp_leaveDet_new(leaveID , approval, level, isFromCancel){
        $('#leave_approval_form').bootstrapValidator('resetForm', true);
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/leave_approval_view') ?>',
            data: {'masterID': leaveID},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if(data[0] == 'e'){
                    myAlert(data[0], data[1]);
                    return false;
                }

                $("#leaveApprove_modal").modal({backdrop: "static"});
                $('#app-chk').html(data['view']);

                $('#hiddenLeaveID').val(leaveID);
                $('#level_leave_emp').val(level);
                $('#level').val(level);
                $('#status').val(1);
                $('#comments').val('');

                var approvalTitle = (isFromCancel == 1)? 'Cancellation Approval' : '<?php echo $this->lang->line('hrms_payroll_leave_approval');?>';
                $('#approval-title').html(approvalTitle);
                $('#levelText').html('&nbsp;&nbsp;&nbsp; - Level '+level);
                $('#isFromCancelYN').val(isFromCancel);

                if(approval == 1){
                    $('.approved').addClass("hidden");
                }else{
                    $('.approved').removeClass("hidden");
                }

                attachment_View_modal('LA', leaveID);

                var current_userID = '<?php echo current_userID() ?>';
                if(current_userID == data['coveringEmpID']){
                    $('#entitleSpan, #balanceSpan').text('-');
                }
            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function load_emp_leaveDet(leaveID , approval, level, isFromCancel){
        $('#leave_approval_form').bootstrapValidator('resetForm', true);
        $.ajax({
            type: 'post',
            url: '<?php echo site_url('Employee/employeeLeave_detailsOnApproval') ?>',
            data: {'masterID': leaveID},
            dataType: 'json',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#leaveApprove_modal").modal({backdrop: "static"});
                $('#comments').val('');
                var empDet = data['empDet'];
                var leaveDet = data['leaveDet'];
                var entitleDet = data['entitleDet'];

                if($.isEmptyObject(entitleDet)){
                    entitleDet = {balance : 0 };
                }

                $('#leaveCode').text(leaveDet['documentCode']);
                $('#empID').val(empDet['EIdNo']);
                $('#empNameSpan').text( empDet['ECode']+" | "+empDet['employee'] );
                $('#empCodeSpan').text(empDet['EmpSecondaryCode']);
                $('#designationSpan').text(empDet['DesDescription']);
                $('#department').text(empDet['department']);
                $('#reportingManager').text(empDet['manager']);

                var leaveType = $('#leaveType');
                leaveType.val(leaveDet['leaveTypeID']);
                $('#leaveTypeSpan').text(leaveDet['description']);
                if(leaveDet['approvedYN']==1){  /*if approved set leaveavailable column leave master*/
                    entitleDet['balance']=leaveDet['leaveAvailable'];
                }
                if(leaveDet['policyMasterID']==2){
                    var l_taken = entitleDet['leaveTaken'];
                    var l_entitle = entitleDet['balance'];
                    entitleSpan.text(display(l_entitle));
                    takenSpan.text(display(leaveDet['hours']));
                    bal=   entitleDet['balance']-leaveDet['hours'];
                    balanceSpan.text( display(bal) );
                    policySpan.text(entitleDet['policyDescription']);
                }
                else{



                    /*   if( isPaidLeave == 0 ){
                           entitleSpan.text(' None ');
                           takenSpan.text(' None ');
                           balanceSpan.text(' None ');
                           policySpan.text(' None ');
                       }
                       else{*/
                    var l_taken = entitleDet['leaveTaken'];
                    var l_entitle = entitleDet['balance'];
                    entitleSpan.text(l_entitle);
                    takenSpan.text(l_taken);
                    bal =   entitleDet['balance']-leaveDet['days'];
                    if (bal != parseInt(bal)){
                        bal = bal.toFixed(1);
                    }
                    balanceSpan.text( bal );
                    policySpan.text(entitleDet['policyDescription']);
                    /*  }*/
                }
                $('#startDateSpan').text(leaveDet['startDate']);
                $('#endDateSpan').text(leaveDet['endDate']);
                $('#commentSpan').text(leaveDet['comments']);
                $('#dateSpan').text(leaveDet['entryDate']);
                $('#entryDate').text(leaveDet['entryDate']);
                $('#days').text(leaveDet['days']);


                $('#hiddenLeaveID').val(leaveID);
                $('#level_leave_emp').val(level);
                $('#status_leaveapp').val(1);


                var approvalTitle = (isFromCancel == 1)? 'Cancellation Approval' : '<?php echo $this->lang->line('hrms_payroll_leave_approval');?>';
                $('#approval-title').html(approvalTitle);
                $('#levelText').html('&nbsp;&nbsp;&nbsp; - Level '+level);
                $('#isFromCancelYN').val(isFromCancel);

                if(approval==1){
                    $('.approved').addClass("hidden");
                }else{
                    $('.approved').removeClass("hidden");
                }

                var current_userID = '<?php echo current_userID() ?>';
                if(current_userID == leaveDet['coveringEmpID']){
                    $('#entitleSpan, #balanceSpan').text('-');
                }

            },
            error: function () {
                stopLoad();
                myAlert('e', 'An Error Occurred! Please Try Again.');
            }
        });
    }

    function fetch_approval_ec(expenseClaimMasterAutoID,$document) {
        if (expenseClaimMasterAutoID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'expenseClaimMasterAutoID': expenseClaimMasterAutoID, 'html': true,'approval':1},
                url: "<?php echo site_url('ExpenseClaim/load_expense_claim_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    $('#expenseClaimMasterAutoID').val(expenseClaimMasterAutoID);
                    $("#expense_claim_Approval_modal").modal({backdrop: "static"});
                    $('#conform_body_ec').html(data);
                    $('#comments_ec').val('');
                    $('#ec_status').val('').change();
                    expenseClaim_attachment_View_modal('EC',expenseClaimMasterAutoID);
                    stopLoad();
                    refreshNotifications(true);
                }, error: function () {
                    stopLoad();
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    refreshNotifications(true);
                }
            });
        }
    }
    function expenseClaim_attachment_View_modal(documentID, documentSystemCode) {
        $("#Tab-profile-v_ec").removeClass("active");
        $("#Tab-home-v_ec").addClass("active");
        $("#po_attachement_approval_Tabview_a").removeClass("active");
        $("#po_attachement_approval_Tabview_v").addClass("active");
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode,'confirmedYN': 0},
                success: function (data) {
                    $('#ec_attachment_body').empty();
                    $('#ec_attachment_body').append('' +data+ '');

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function fetch_approval_lec(id, document_type, level, approvID){ /*Leave encashment/salary*/
        $('#quotation_contract_modal_container').css({'width': '80%'});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'masterID': id},
            url: "<?php echo site_url('Employee/leave_encashment_and_salary_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#documentApprovedID').val(approvID);
                $('#code').val('LEC');
                $('#Level').val(level);
                $('#autoid').attr('name', 'masterID').val(id);
                $('#status').attr('name', 'status').val('');
                $('#commonids').attr('name', 'comments').val('');
                $('#conform_body').html(data['view']);
                $('#comments').val('');
                $("#Quotation_contract_modal").modal({backdrop: "static"});
                stopLoad();

            }, error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function fetch_approval_sar(id, level, approvID){ /*Salary Advance Request*/
        $('#quotation_contract_modal_container').css({'width': '80%'});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'masterID': id},
            url: "<?php echo site_url('Employee/load_salary_advance_request_view/view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#documentApprovedID').val(approvID);
                $('#code').val('SAR');
                $('#Level').val(level);
                $('#autoid').attr('name', 'masterID').val(id);
                $('#status').attr('name', 'status').val('');
                $('#commonids').attr('name', 'comments').val('');
                $('#conform_body').html(data);
                $('#comments').val('');
                $("#Quotation_contract_modal").modal({backdrop: "static"});
                stopLoad();

            }, error: function () {
                stopLoad();
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
            }
        });
    }

    function tabAttachement_ec()
    {
        $("#Tab-profile-v_ec").removeClass("hide");
    }
    function tabView_ec(){
        $("#Tab-profile-v_ec").addClass("hide");
    }


    function tabAttachement(){
        $("#Tab-profile-v").removeClass("hide");
    }
    function tabView(){
        $("#Tab-profile-v").addClass("hide");
    }
    function document_attachment_view(documentSystemCode,documentID){

        var confirmedYN = '1';

        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentSystemCode': documentSystemCode, 'documentID': documentID, 'confirmedYN': confirmedYN},
                success: function (data) {
                    $('#documentAttachmentViewTitle').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;[' + documentID + '] Attachments' +"");
                    $('#document_attachment_modal_body').empty();
                    $('#document_attachment_modal_body').append('' + data + '');
                    $("#documentAttachmentView").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }

    }
    
</script>