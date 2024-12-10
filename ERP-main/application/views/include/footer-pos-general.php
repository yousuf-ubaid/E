</div><!-- /.content-wrapper -->
</div>
</div><!-- ./wrapper -->
<div class="modal fade" id="ap_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ap_user_label">Modal title</h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt><?php echo $this->lang->line('footer_document_code'); ?><!--Document code--></dt>
                    <dd id="c_document_code">...</dd>
                    <dt><?php echo $this->lang->line('footer_document_date'); ?><!--Document Date--></dt>
                    <dd id="c_document_date">...</dd>
                    <dt><?php echo $this->lang->line('footer_confirmed_date'); ?><!--Confirmed Date--></dt>
                    <dd id="c_confirmed_date">...</dd>
                    <dt><?php echo $this->lang->line('common_confirmed_by'); ?><!--Confirmed By-->&nbsp;&nbsp;</dt>
                    <dd id="c_conformed_by">...</dd>
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th><?php echo $this->lang->line('footer_approved_date'); ?><!--Approved Date--></th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                        <th><?php echo $this->lang->line('common_comment'); ?><!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="ap_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('footer_document_not_approved_yet'); ?><!--Document not approved yet--></td>
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

<!--model for reject approval -->
<div class="modal fade" id="reject_drill_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span>
                    <?php echo $this->lang->line('footer_approval_rejected_history'); ?><!--Approval Rejected History-->
                </h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt><?php echo $this->lang->line('common_document_code'); ?><!--Document code--></dt>
                    <dd id="c_document_code_rejected">...</dd>
                    <!--<dt>Referback Date</dt><dd id="c_document_date_referback">...</dd>-->
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th><?php echo $this->lang->line('footer_rejected_date'); ?><!--Rejected Date--></th>
                        <th><?php echo $this->lang->line('common_status'); ?><!--Status--></th>
                        <th><?php echo $this->lang->line('common_comments'); ?><!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="reject_ap_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_document_not_approved_yet'); ?><!--Document not approved yet--></td>
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

<!--model for referback comments view -->
<div class="modal fade" id="referback_drill_user_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ap_user_label_referback">Modal title</h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt><?php echo $this->lang->line('common_document_code'); ?><!--Document code--></dt>
                    <dd id="c_document_code_reject">...</dd>
                    <!--<dt>Referback Date</dt><dd id="c_document_date_referback">...</dd>-->
                </dl>
                <table class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo $this->lang->line('common_name'); ?><!--Name--></th>
                        <th><?php echo $this->lang->line('common_level'); ?><!--Level--></th>
                        <th><?php echo $this->lang->line('footer_referred_back_date'); ?><!--Referred-back Date--></th>
                        <th><?php echo $this->lang->line('common_comments'); ?><!--Comments--></th>
                    </tr>
                    </thead>
                    <tbody id="referback_ap_user_body" class="no-padding">
                    <tr class="danger">
                        <td colspan="5" class="text-center">
                            <?php echo $this->lang->line('common_document_not_approved_yet'); ?><!--Document not approved yet--></td>
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

<div class="modal fade" id="attachment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="attachment_modal_label">Modal title</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-10"><span class="pull-right">
                      <?php echo form_open_multipart('', 'id="attachment_uplode_form" class="form-inline"'); ?>
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
                          <button type="button" class="btn btn-default" onclick="document_uplode()"><span
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
<div class="modal fade" id="documentPageView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     style="z-index: 1000000000;">
    <div class="modal-dialog" role="document" style="width:90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">Modal title</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-1">
                            <!-- Nav tabs -->
                            <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                                <li id="TabViewActivation_view" class="active"><a href="#home-v"
                                                                                  data-toggle="tab">
                                        <?php echo $this->lang->line('common_view'); ?><!--View--></a></li>
                                <li id="TabViewActivation_attachment">
                                    <a href="#profile-v" data-toggle="tab">
                                        <?php echo $this->lang->line('common_attachment'); ?><!--Attachment--></a>
                                </li>
                                <li class="itemMasterSubTab_footer" id="tab_itemMasterTabF">
                                    <a href="#subItemMaster-v" data-toggle="tab">
                                        <?php echo $this->lang->line('footer_item_master_sub'); ?><!--Item&nbsp;Master&nbsp;Sub--></a>
                                </li>

                            </ul>
                        </div>
                        <div class="col-sm-11" style="padding-left: 0px;margin-left: -2%;">
                            <!-- Tab panes -->
                            <div class="zx-tab-content">
                                <div class="zx-tab-pane active" id="home-v">
                                    <div id="loaddocumentPageView" class="col-md-12"></div>
                                </div>
                                <div class="zx-tab-pane" id="profile-v">
                                    <div id="loadPageViewAttachment" class="col-md-8">
                                        <div class="table-responsive">
                                            <span aria-hidden="true"
                                                  class="glyphicon glyphicon-hand-right color"></span>
                                            &nbsp <strong>
                                                <?php echo $this->lang->line('common_attachments'); ?><!--Attachments--></strong>
                                            <br><br>
                                            <table class="table table-striped table-condensed table-hover">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_file_name'); ?><!--File Name--></th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                                                    <th><?php echo $this->lang->line('common_type'); ?><!--Type--></th>
                                                    <th>
                                                        <?php echo $this->lang->line('common_action'); ?><!--Action--></th>
                                                </tr>
                                                </thead>
                                                <tbody id="View_attachment_modal_body" class="no-padding">
                                                <tr class="danger">
                                                    <td colspan="5" class="text-center">
                                                        <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="zx-tab-pane" id="subItemMaster-v">
                                    <div class="itemMasterSubTab_footer">
                                        <h4>
                                            <?php echo $this->lang->line('footer_item_master_sub'); ?><!--Item Master Sub--></h4>
                                        <div id="itemMasterSubTab_footer_div"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade pddLess" data-backdrop="static" id="passwordresetModal" data-width="80%"
     role="dialog">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                </button>
                <h5> <?php echo $this->lang->line('footer_change_password'); ?><!--Change Password--></h5>
            </div>
            <div class="modal-body" id="modal_contact">
                <form class="form-horizontal" method="post" id="passwordFormLogin" autocomplete="off">
                    <div class="form-group">
                        <label for="currentPassword" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('footer_current_password'); ?><!--Current Password--></label>
                        <div class="col-sm-6">
                            <input type="password"
                                   placeholder="<?php echo $this->lang->line('footer_current_password'); ?>"
                                   class="form-control"
                                   name="currentPassword" id="currentPassword"/>

                        </div>

                    </div>
                    <div class="form-group">
                        <label for="newPassword" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('profile_new_password'); ?><?php echo $this->lang->line('footer_new_password'); ?><!--New Password--></label>
                        <div class="col-sm-6">
                            <input type="password" onkeyup="validatepwsStrengthfotr()" class="form-control"
                                   id="newPassword"
                                   name="newPassword"
                                   placeholder="<?php echo $this->lang->line('footer_new_password'); ?>">
                            <div class="progressbr" id="progrssbarlogin">

                            </div>
                        </div>
                        <div class="col-sm-3" id="messagelogin"></div>

                    </div>
                    <div class="form-group">
                        <label for="confirmPassword" class="col-sm-3 control-label">
                            <?php echo $this->lang->line('footer_confirm_password'); ?><!--Confirm Password--></label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" id="confirmPassword"
                                   name="confirmPassword"
                                   placeholder="<?php echo $this->lang->line('footer_confirm_password'); ?>">
                        </div>
                    </div>

            </div>
            <div class="modal-footer" style="background-color: #ffffff">
                <button type="submit" id="passwordsavebtn" class="btn btn-primary" onclick="">
                    <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_cancel'); ?><!--Cancel--></button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="insufficient_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Insufficient Items</h4>
            </div>

            <form class="form-horizontal" id="insufficient_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right"><a href="" class="btn btn-excel btn-xs" id="btn-excel"
                                                       download="Insufficient Items List.xls"
                                                       onclick="var file = tableToExcel('insufficient_item', 'Insufficient Items List'); $(this).attr('href', file);">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="insufficient_item">
                        <table class="table table-condensed table-bordered">
                            <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Description</th>
                                <th>Current Stock</th>
                            </tr>
                            </thead>
                            <tbody id="insufficient_item_body">

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?> </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="document_status_more_details-model" class="modal fade" role="dialog" data-backdrop="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="document_status_more_details-title"></h4>
            </div>

            <div class="modal-body">
                <div id="document_status_view" style="min-height: 70px;"></div>

                <div class="modal-footer" style="padding: 10px 5px 2px;">
                    <button type="button" class="btn btn-default btn-sm"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="third_party_model" class="modal fade" role="dialog" data-backdrop="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="color: darkblue;font-size: 15px;font-weight: bold;">Third Party Applications</h4>
            </div>

            <div class="modal-body">
                <?php
                $companyID=current_companyID();
                $thirdparty=$this->db->query('SELECT * FROM srp_erp_thirdpartyapplications WHERE companyID =' . $companyID . ' AND isActive=1 ')->result_array();
                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <section class="customer-logos slider">
                            <?php
                            if(!empty($thirdparty)) {
                                $cut = count($thirdparty);
                                if ($cut == 1) {
                                    foreach ($thirdparty as $val) {
                                        ?>
                                        <div class="slide" title="<?php echo $val['Description'] ?>"><a target="_blank"
                                                                                                        href="<?php echo $val['url'] ?>"><img
                                                    src="<?php echo base_url('images/thirdParty/') . $val['logoImage'] ?>">
                                            </a>
                                        </div><!--<span style="color: #00BCD5;"><?php //echo $val['Description']
                                        ?></span>-->

                                        <?php
                                    }
                                    foreach ($thirdparty as $val) {
                                        ?>
                                        <div class="slide" title="<?php echo $val['Description'] ?>"><a target="_blank"
                                                                                                        href="<?php echo $val['url'] ?>"><img
                                                    src="<?php echo base_url('images/thirdParty/') . $val['logoImage'] ?>">
                                            </a>
                                        </div><!--<span style="color: #00BCD5;"><?php //echo $val['Description']
                                        ?></span>-->
                                        <?php
                                    }
                                } else {
                                    foreach ($thirdparty as $val) {
                                        ?>
                                        <div class="slide" title="<?php echo $val['Description'] ?>"><a target="_blank"
                                                                                                        href="<?php echo $val['url'] ?>"><img
                                                    src="<?php echo base_url('images/thirdParty/') . $val['logoImage'] ?>"></a>
                                        </div><!--<span style="color: #00BCD5;"><?php //echo $val['Description']
                                        ?></span>-->
                                        <?php
                                    }
                                }
                                ?>
                        </section>
                                <?php
                            }else{
                                ?>
                               <div class="col-sm-10" style="text-align: center;">No Third Party Applications Found!</div>
                            <?php
                            }
                            ?>

                    </div>
                </div>
            </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm"
                            data-dismiss="modal"><?php echo $this->lang->line('common_Close'); ?></button>
                </div>
            </div>
        </div>
</div>


<?php
$passwordComplexityExist = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();
?>
<?php $this->load->view('include/inc-footer-modals'); ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/select2/css/select2.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/sweetalert/sweet-alert.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datepicker/datepicker3.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/holdon/HoldOn.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/dataTables.bootstrap.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/jasny-bootstrap.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/toastr/toastr.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/xeditable/css/bootstrap-editable.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('plugins/select2/js/select2.full.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker2.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/bootstrap-datepicker.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/toastr/toastr.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/fastclick/fastclick.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/app.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sparkline/jquery.sparkline.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/slimScroll/jquery.slimscroll.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/demo.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sweetalert/sweet-alert.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/offline/offline.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/holdon/HoldOn.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/typeahead.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap/js/jasny-bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/handlebars/handlebars-v4.0.5.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/tableHeadFixer/tableHeadFixer.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/multiselect/dist/js/multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/iCheck/icheck.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/daterangepicker/moment.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/xeditable/js/bootstrap-editable.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jQuery/jquery.maskedinput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/jquery.inputmask.bundle.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.date.extensions.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/inputmask/jquery.inputmask.js'); ?>"></script>
<!-- allow - value as well in textbox-->
<script type="text/javascript" src="<?php echo base_url('plugins/numeric/jquery.numeric.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/tapmodo-Jcrop-1902fbc/js/jquery.Jcrop.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/combodate/combodate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/combodate/moment.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datetimepicker/src/js/bootstrap-datetimepicker.js'); ?>"></script>

<!--jquery auto complete-->
<script type="text/javascript" src="<?php echo base_url('plugins/jQuery-Autocomplete-master/dist/jquery.autocomplete.js'); ?>"></script>

<script type="text/javascript">
    //fetchcompany(<?php //echo current_companyID() ?>, false);
    var popup = 0;
    var CSRFHash = '<?php echo $this->security->get_csrf_hash() ?>';
    var numberOfAttempt = 0;



    function check_session_status() {
        $.ajax({
            async: true,
            type: 'get',
            dataType: 'json',
            data: {'': ''},
            url: '<?php echo site_url("login/session_status"); ?>',
            success: function (data) {
                if (data['status'] == 0) {
                    session_logout_page();
                } else {
                    CSRFHash = data.csrf;
                }
                stopLoad();
            },
            error: function () {
                stopLoad();

            }
        });
    }

    function refresh_session_status() {
        $.ajax({
            async: true,
            type: 'get',
            dataType: 'json',
            data: {'': ''},
            url: '<?php echo site_url("login/session_status"); ?>',
            success: function (data) {
                if (data['status'] == 0) {
                    session_logout_page();
                } else {
                    CSRFHash = data.csrf;
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function session_logout_page() {
        swal({
            title: "Session Destroyed!",
            text: "You will be redirect to login page in 2 seconds.",
            timer: 2000,
            showConfirmButton: false
        });
        setTimeout(function () {
            window.location = '<?php echo site_url('/Login/logout'); ?>';
        }, 2000);
    }



    function refreshNotifications() {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url("Dashboard/fetch_notifications"); ?>',
            dataType: 'json',
            async: true,
            success: function (data) {
                check_session_status();
                if (!jQuery.isEmptyObject(data)) {
                    toastr.options = {
                        "closeButton": true,
                        "debug": true,
                        "newestOnTop": true,
                        "progressBar": true,
                        "positionClass": "toast-bottom-right animated-panel fadeInRight",
                        "preventDuplicates": true,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    }
                    $.each(data, function (i, v) {
                        toastr[v.t](v.m, v.h);
                    });
                }
                stopLoad();
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function notification(message, status) {
        toastr.options = {
            "positionClass": "toast-bottom-right",
        }

        if (status == undefined) {
            toastr.error(message)
        } else if (status == 's') {
            toastr.success(message);
        } else if (status == 'w') {
            toastr.warning(message);
        } else if (status == 'i') {
            toastr.info(message);
        } else {
            toastr.error(message);
        }
    }

    Number.prototype.formatMoney = function (c, d, t) {
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };

    function number_validation() {
        $(".number").attr('autocomplete', 'off');
        $(".number").on("onkeyup keyup blur", function (event) {
            $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                if (event.keyCode != 8) {
                    event.preventDefault();
                }
            }
        });
        $(".m_number").attr('autocomplete', 'off');
        $(".m_number").on("onkeyup keyup blur", function (event) {
            $(this).val($(this).val().replace(/[^-0-9\.]/g, ''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                if (event.keyCode != 8) {
                    event.preventDefault();
                }
                ;
            }
        });
    }

    function commaSeparateNumber(val, dPlace = 2) {
        var toFloat = parseFloat(val);
        var a = toFloat.toFixed(dPlace);
        while (/(\d+)(\d{3})/.test(a.toString())) {
            a = a.toString().replace(/(\d+)(\d{3})/, '$1' + ',' + '$2');
        }
        return a;
    }

    function date_format_change(userdate, policydate) {
        var date_string = moment(userdate, "YYYY-MM-DD").format(policydate);
        return date_string;
    }

    function removeCommaSeparateNumber(val) {
        return parseFloat(val.replace(/,/g, ""));
    }



    function currency_validation_modal(CurrencyID, documentID, partyAutoID, partyType) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'CurrencyID': CurrencyID, 'partyAutoID': partyAutoID, 'partyType': partyType},
            url: '<?php echo site_url('Company/currency_validation'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data['status'] == true) {
                    var message = 'local currency ( ' + data['data']['default']['masterCurrencyCode'] + ' - ' + data['data']['default']['subCurrencyCode'] + ' ) ' + data['data']['default']['conversion'];
                    message += '<br><?php echo $this->lang->line('footer_reporting_currency');?> ( ' + data['data']['reporting']['masterCurrencyCode'] + ' - ' + data['data']['reporting']['subCurrencyCode'] + ' ) ' + data['data']['reporting']['conversion'];
                    <!--Reporting currency-->
                    if (partyAutoID) {
                        message += '<br><?php echo $this->lang->line('footer_party_currency');?> ( ' + data['data']['party']['masterCurrencyCode'] + ' - ' + data['data']['party']['subCurrencyCode'] + ' ) ' + data['data']['party']['conversion'];
                        <!--Party currency-->
                    }
                    myAlert('i', message, 1000);
                } else {
                    var message = 'local currency ( ' + data['data']['currency'] + ' - ' + data['data']['def'] + ' ) ';
                    message += 'Reporting currency ( ' + data['data']['currency'] + ' - ' + data['data']['rpt'] + ' ) ';
                    if (partyAutoID) {
                        message += 'Party currency ( ' + data['data']['currency'] + ' - ' + data['data']['par'] + ' ) ';
                    }
                    swal({
                            title: "Exchange rates !",
                            text: "<?php echo $this->lang->line('footer_exchange_rates_are_not_set_for_the_selected_currency');?>." + message, /*Exchange rates are not set for the selected currency*/
                            showCancelButton: false,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "<?php echo $this->lang->line('common_ok');?>", /*Ok*/
                            closeOnConfirm: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                fetchPage('system/erp_dashboard', '', 'Dashboard');
                            }
                        });
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }



    function myAlert(type, message, duration=null) {
        toastr.clear();
        initAlertSetup(duration);
        if (type == 'e' || type == 'd') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>'/*'Error!'*/);
            check_session_status();
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>'/*'Success!'*/);
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>'/*'Warning!'*/);
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_information');?>'/*'Information'*/);
        } else {
            check_session_status();
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function initAlertSetup(duration=null) {
        duration = ( duration == null ) ? '1000' : duration;
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right animated-panel fadeInTop",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": duration,
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }

    function alerMessage(type, message) {
        // message+='<br /><br /><button type="button" class="btn clear">Yes</button>';

        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-center",
            "preventDuplicates": true,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": 0,
            "extendedTimeOut": 0,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "tapToDismiss": false
        };
        toastr.clear();
        if (type == 'e') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>!');
            /*Error*/
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>');
            /*Success!*/
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Warning!*/
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_information');?>');
            /*Information*/
        } else {
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function myAlert_topPosition(type, message) {
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-center animated-panel fadeInTop",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        toastr.clear();
        if (type == 'e') {
            toastr.error(message, '<?php echo $this->lang->line('common_error');?>!');
            /*Error*/
        } else if (type == 's') {
            toastr.success(message, '<?php echo $this->lang->line('common_success');?>');
            /*Success!*/
        } else if (type == 'w') {
            toastr.warning(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Warning!*/
        } else if (type == 'i') {
            toastr.info(message, '<?php echo $this->lang->line('common_warning');?>');
            /*Information*/
        } else {
            toastr.error('wrong input type! type only allowed for "e","s","w","i"', 'Error!');
        }
    }

    function startLoad() {
        HoldOn.open({
            theme: "sk-rect",//If not given or inexistent theme throws default theme , sk-bounce , sk-cube-grid
            message: "<div style='font-size: 16px; color:#ffffff; margin-top:20px;     text-shadow: 0px 0px 4px black, 0 0 7px #000000, 0 0 3px #000000;'> Loading, Please wait </div><div id='loaderDivContent'></div>",
            content: 'test', // If theme is set to "custom", this property is available
            textColor: "#000000" // Change the font color of the message
        });
    }

    function startLoadPos() {
        $("#posPreLoader").show();
    }

    function stopLoad() {
        HoldOn.close();
        $("#posPreLoader").hide();
    }

    function modalFix() {
        setTimeout(function () {
            if ($('.modal').hasClass('in')) {
                $('body').addClass('modal-open');
            }
        }, 500);
    }

    function csrf_init() {
        $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
            if (originalOptions.type == 'POST' || originalOptions.type == 'post' || options.type == 'POST' || options.type == 'post') {
                if (options.processData) { /*options.contentType === 'application/x-www-form-urlencoded; charset=UTF-8'*/
                    options.data = (options.data ? options.data + '&' : '') + $.param({'<?php echo $this->security->get_csrf_token_name(); ?>': CSRFHash});
                } else {
                    options.data.append('<?php echo $this->security->get_csrf_token_name(); ?>', CSRFHash);
                }
            } else {
                if (options.processData) {
                } else {
                }
            }
        });

    }

    $(document).ready(function () {
        $('.modal').on('hidden.bs.modal', function () {
            modalFix()
        });

        $(".select2").select2();

        csrf_init();

        setInterval(function () {
            refresh_session_status();
        }, 3601000);


        var company_id = '<?php echo json_encode($this->common_data['company_data']['company_id']); ?>';
        var company_code = '<?php echo json_encode($this->common_data['company_data']['company_code']); ?>';
        if (company_code == 'null') {
            fetchPage('system/company/erp_company_configuration_new', company_id, 'Add Company', 'COM');
        }



        <?php
        if(!empty($thirdparty)){
            ?>
        $('.customer-logos').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 1500,
            arrows: false,
            dots: false,
            pauseOnHover: true,
            responsive: [{
                breakpoint: 768,
                settings: {
                    slidesToShow: 1
                }
            }, {
                breakpoint: 520,
                settings: {
                    slidesToShow: 1
                }
            }]
        });
        <?php
        }
        ?>

    });

    function getMonthName(monthNumber) {
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return months[monthNumber - 1];
    }

    function set_navbar_cookie() {
        var classVal = $('body').attr('class');
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Dashboard/set_navbar_cookie'); ?>",
            data: {'className': classVal},
            dataType: "json",
            cache: true
        });
    }


    function makeTdAlign(name, side, rowNo) {
        $('#' + name + ' tbody tr').each(function () {
            var thisRow = this;
            $.each(rowNo, function (i, v) {
                $(thisRow).find('td:eq(' + v + ')').css('text-align', side);
            });
        });
    }



    function msg_popup(btnClass=null) {
        setTimeout(function () {
            swal({
                html: true,
                title: '',
                text: 'This document contains some employees, That you do not have permission to view their information'
            });
            if (btnClass != null) {
                $('.' + btnClass).hide();
            }
        }, 300);
    }

    function isDateInputMaskNotComplete(dateStr) {
        return (/[dmy]/.test(dateStr))
    }

    //User control function

    function open_access_denied_alertMod() {

        $("#access_denied_alertDiv").remove();



        $('body').append('<div class="modal fade-scale" id="access_denied_alertDiv" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document" style="width: 35%"><div class="modal-content" style=" border:3px solid #cc0000;"><div class="modal-body" style="text-align: center;"><div class="row-fluid"><div class="span12" style="text-align: center;"><span><i class="fa fa-warning" style="font-size:24px;color:#cc0000;margin-left: 10px;float: left;"></i><b style="text-align: center;"> Access Denied!</b></span><br><br><span>You do not have sufficient privileges to access this feature. Please contact the admin for the access.</span></div></div><button type="button" class="btn btn-danger btn-flat btn-sm pull-right" data-dismiss="modal">Okay</button></div></div></div></div>');

        $('#access_denied_alertDiv').modal('show');

    }


</script>

