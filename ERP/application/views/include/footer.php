</div><!-- /.content-wrapper -->
</div>
<div class="zoom-in-out-box"></div>
<a href="https://support.gopromate.com/login" target="_blank">
    <div id="chat-circle" class="btn btn-raised chat-circle">
            <div id="chat-overlay"></div>
                <i class="fa fa-phone"></i>
    </div>
</a>    

<footer class="main-footer <?php echo isset($notFixed) ? 'hide' : ''; /*navbar-fixed-bottom*/ ?>"
        style="padding:4px;">
    <div class="row">
        <div class="col-sm-12 text-xs-center">
            <div class="col-sm-6">
                <strong> Copyright &copy; 2020 - 2025</strong> All rights reserved.
            </div>
            <div class="col-sm-6 text-right">
                <b><?php echo "Timezone :  " . current_timezoneDescription(); ?></b>
            </div>
        </div>
    </div>
</footer>

<?php if (strtolower(SETTINGS_BAR) == 'on') { ?>
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Home tab content -->
            <div class="tab-pane" id="control-sidebar-home-tab">
                <!-- /.control-sidebar-menu -->
            </div><!-- /.tab-pane -->

            <!-- Settings tab content -->
            <div class="tab-pane" id="control-sidebar-settings-tab">

            </div><!-- /.tab-pane -->
        </div>
    </aside><!-- /.control-sidebar -->
<?php } ?>

<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>

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
                    <dd id="c_document_code_confirmed" class="confirmed hide">...</dd>
                    <dd id="c_document_code_approved" class="approved hide">...</dd>
                    <dt><?php echo $this->lang->line('footer_document_date'); ?><!--Document Date--></dt>
                    <dd id="c_document_date_confirmed" class="confirmed hide">...</dd>
                    <dd id="c_document_date_approved" class="approved hide">...</dd>
                    <dt><?php echo $this->lang->line('footer_confirmed_date'); ?><!--Confirmed Date--></dt>
                    <dd id="c_confirmed_date_confirmed" class="confirmed hide">...</dd>
                    <dd id="c_confirmed_date_approved" class="approved hide">...</dd>
                    <dt><?php echo $this->lang->line('common_confirmed_by'); ?><!--Confirmed By-->&nbsp;&nbsp;</dt>
                    <dd id="c_conformed_by_confirmed" class="confirmed hide">...</dd>
                    <dd id="c_conformed_by_approved" class="approved hide">...</dd>
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
<div class="modal fade" id="documentPageView" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" id="doc-view-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle">Modal title</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-1 col-xs-12">
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
                                <li class="dodelivered_footer" id="tab_dodelivered">
                                    <a href="#dodelivered-v" data-toggle="tab">Delivered</a>
                                </li>

                            </ul>
                        </div>
                        <div class="col-sm-11 col-xs-12" style="padding-left: 0px;padding-right: 0px">
                            <!-- Tab panes MH -->
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

                                <div class="zx-tab-pane" id="dodelivered-v">
                                    <div class="dodelivered_footer">
                                        <h4></h4>
                                        <div id="deliveredTab_footer_div"></div>
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
                <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignBuyerr_view_item_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"style="z-index: 1000000000;">
    <div class="modal-dialog" role="document" style="width:50%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="close_buyers_view()"><span
                        >&times;</span></button>
                <h4 class="modal-title" id="documentPageViewTitle"></h4>
            </div>
            <div class="modal-body">

            <div class="row" style="margin: 6px 0px;">
                    <div class="col-sm-5">
                        <div class="box-tools">
                            <div class="has-feedback">
                                <input name="BuyerSh" type="text" class="form-control input-sm"
                                       placeholder="Search"
                                       id="BuyerSh" onkeyup="startMasterSearchBuyers()">
                                <span class="glyphicon glyphicon-search form-control-feedback"></span>
                            </div>
                        </div>
                    </div>
            </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="assignedbuyer_itemID_view"></div>
                        <input type="hidden" name="masterID" id="masterID">
                        <input type="hidden" name="detailsID" id="detailsID">
                        <input type="hidden" name="type" id="type">
                        <div id="assignBuyer_item_Content_view"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer m-t-xs">
                <div class="col-sm-12 pull-right">
                    <!-- <button type="button" class="btn btn-default" onclick="close_buyers_view()"><?php echo $this->lang->line('common_Close');?></button> -->
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
                    <!-- <span id="buyer_submit" class="hide">
                        <button class="btn btn-primary" onclick="assign_buyers_current_user()">Assign</button>
                    </span> -->
                    
                </div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade pddLess" data-backdrop="static" id="passwordresetModal" data-width="80%"
     role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true"
                          style="color:red;">x</span>
                </button>
                <h5> <?php echo $this->lang->line('footer_change_password'); ?><!--Change Password--></h5>
            </div>
            <form class="form-horizontal" method="post" id="passwordFormLogin" autocomplete="off">
                <div class="modal-body" id="modal_contact">
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

                        </hr>
                        <div class="password_setting">
                            <div class="password_setting_inner">
                                            <h4>Your password must contain:</h4>
                                            <p>* At lease 6 characters</p>
                                            <p>* At least one digit</p>
                                            <p>* At least one upper case character</p>
                                            <p>* At least one lower case character</p>
                                            <p>* At least one special character</p>
                                            <p></p>
                            </div>
                        </div>

                </div>
                <div class="modal-footer" style="background-color: #ffffff">
                    <button type="submit" id="passwordsavebtn" class="btn btn-primary-new size-lg" onclick="">
                        <?php echo $this->lang->line('common_save'); ?><!--Save--></button>
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">
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
                <h4 class="modal-title" style="color: darkblue;font-size: 15px;font-weight: bold;">Third Party
                    Applications</h4>
            </div>

            <div class="modal-body">
                <?php
                $companyID = current_companyID();
                $thirdparty = $this->db->query('SELECT * FROM srp_erp_thirdpartyapplications WHERE companyID =' . $companyID . ' AND isActive=1 ')->result_array();
                ?>
                <div class="row">
                    <div class="col-sm-12">
                        <section class="customer-logos slider">
                            <?php
                            if (!empty($thirdparty)) {
                                $cut = count($thirdparty);
                                if ($cut == 1) {
                                    foreach ($thirdparty as $val) {
                                        ?>
                                        <div class="slide" title="<?php echo $val['Description'] ?>"><a target="_blank"
                                                                                                        href="<?php echo $val['url'] ?>"><img
                                                        src="<?php echo base_url('images/thirdParty/') . $val['logoImage'] ?>">
                                            </a>
                                        </div>
                                        ?>

                                        <?php
                                    }
                                    foreach ($thirdparty as $val) {
                                        ?>
                                        <div class="slide" title="<?php echo $val['Description'] ?>"><a target="_blank"
                                                                                                        href="<?php echo $val['url'] ?>"><img
                                                        src="<?php echo base_url('images/thirdParty/') . $val['logoImage'] ?>">
                                            </a>
                                        </div>
                                        ?>
                                        <?php
                                    }
                                } else {
                                    foreach ($thirdparty as $val) {
                                        ?>
                                        <div class="slide" title="<?php echo $val['Description'] ?>"><a target="_blank"
                                                                                                        href="<?php echo $val['url'] ?>"><img
                                                        src="<?php echo base_url('images/thirdParty/') . $val['logoImage'] ?>"></a>
                                        </div>
                                        ?>
                                        <?php
                                    }
                                }
                            } else {
                                ?>
                                <div class="col-sm-10" style="text-align: center;">No Third Party Applications Found!</div>
                                <?php
                            }
                        ?>
                        </section>
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

<div class="modal fade" id="finance_report_drilldown_modal" tabindex="2" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 95%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><span class="myModalLabel"></span></h4>
            </div>
            <div class="modal-body">
                <div id="reportContentDrilldown"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="access_denied_document_wise" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" style="color: red;" id="title_generate_exceed">You cannot use this Item. This
                    item has been pulled for following docuemnts.</h5>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Document Code</th>
                        <th>Reference No</th>
                        <th>Document Date</th>
                    </tr>
                    </thead>
                    <tbody id="access_denied_document_wise_body">

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


<!-- Model for pulled document warehouse qty Start-->
<div class="modal fade" id="documents_by_warehouse_qty_model" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width:55%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" style="color: red;" id="title_generate_exceed">This item has been pulled for
                    following unapproved documents.</h5>
                <br>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            <label style="font-family:'Times New Roman', Times, serif">UOM :</label>
                        </div>
                        <div class="col-sm-6">
                            <label style="font-family:'Times New Roman', Times, serif" id="unitofmesure">00</label>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            <label style="font-family:'Times New Roman', Times, serif">Current stock :</label>
                        </div>
                        <div class="col-sm-6">
                            <label style="font-family:'Times New Roman', Times, serif" id="requested_qtytot">00</label>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            <label style="font-family:'Times New Roman', Times, serif">Pulled Qty :</label>
                        </div>
                        <div class="col-sm-6">
                            <label style="font-family:'Times New Roman', Times, serif" id="pulled_qtytot">00</label>
                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-2">
                            <label style="font-family:'Times New Roman', Times, serif">Available Qty :</label>
                        </div>
                        <div class="col-sm-6">
                            <label style="font-family:'Times New Roman', Times, serif" id="available_qtytot">00</label>
                        </div>
                    </div>

                </div>


            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Document ID</th>
                        <th>Document Code</th>
                        <th>Reference No</th>
                        <th>Document Date</th>
                        <th>WareHouse</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                    </tr>
                    </thead>
                    <tbody id="documents_by_warehouse_qty_body">

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


<div class="modal fade" id="wac_minus_calculation_validation" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" style="color: red;" id="title_generate_exceed">Below items are with negative wac
                    amount.</h5>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Item System Code</th>
                        <th>Item Name</th>
                        <th>Wac Amout</th>
                    </tr>
                    </thead>
                    <tbody id="wac_minus_calculation_validation_body">

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

<div class="modal fade" role="dialog" aria-labelledby="Employee" style="z-index: 1000000000;"
     id="tax_information_dd">
    <div class="modal-dialog modal-lg" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Tax Calculation Drill Down </h4>
            </div>
            <div class="modal-body">


                <div class="row">
                    <div class="col-sm-12">
                        <div id="tax_calculation_dd_body">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-xs" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close') ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="grv_st_detail_modal_inspection" data-backdrop="static" style="z-index: 1000000000;"
     data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 30%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $this->lang->line('transaction_common_edit_item_detail'); ?> </h4>
                <!--Edit Item Detail-->
            </div>
            <form role="form" id="grv_detail_form_inspection" class="form-horizontal">
                <input type="hidden" name="grvPurchaseOrderID" id="grvPurchaseOrderID">
                <input type="hidden" name="grvPurchaseOrderDetailID" id="grvPurchaseOrderDetailID">
                <input type="hidden" name="grvTaxCalculationFormulaID" id="grvTaxCalculationFormulaID">
                <input type="hidden" name="isGroupBasedTaxEnable" id="isGroupBasedTaxEnable" value="0">
                <input type="hidden" class="form-control text-right" id="bal_qty" value="" readonly>
                <input type="hidden" name="estimatedAmount" onfocus="this.select();"
                                           id="estimatedAmount" value="00"
                                           class="form-control number" readonly>
                                           <input type="hidden" name="receivedTotalAmount" onchange="change_amount_edit(this,2)"
                                       onkeypress="return validateFloatKeyPress(this,event)" onfocus="this.select();"
                                       id="receivedTotalAmount" value="00"
                                       class="form-control number" readonly>

                                       <input type="hidden" name="itemAutoID" id="itemAutoID">
                                       <input type="hidden" name="UnitOfMeasureID" id="UnitOfMeasureID">
                                    
                                       <input type="hidden" name="comment" id="comment">
                                       <input type="hidden" name="grvAutoID_in" id="grvAutoID_in">
                                       <input type="hidden" name="grvDetailsID" id="grvDetailsID">
                                       <input type="hidden" name="uom" id="uom">
                                       <input type="hidden" name="search" id="search">
                                       
                <div class="modal-body">
                    <table class="table table-bordered table-condensed" id="">
                        <thead>
                        <tr>
                            
                            <th style="width: 140px;"><?php echo $this->lang->line('common_qty'); ?><?php required_mark(); ?></th>
                            <th style="width: 140px;">Comment</th>
                           
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            
                            <td>
                                <input type="text" name="quantityRequested" 
                                        id="quantityRequested"
                                       class="form-control number">
                                <input type="hidden" id="qty_unchanged" name="qty_unchanged" value="">
                            </td>

                            <td>
                            <textarea class="form-control" rows="1" name="inspection_comment"
                                          placeholder="<?php echo $this->lang->line('transaction_common_item_remarks'); ?>..."
                                          id="inspection_comment"></textarea>
                            </td>
                         
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    <button class="btn btn-primary" onclick ="save_grv_inspection()"
                            type="button"><?php echo $this->lang->line('common_save_change'); ?> </button><!--Save changes-->
                </div>
            </form>
        </div>
    </div>
</div>

<div aria-hidden="true" role="dialog" id="order_review_statement_body_model" data-backdrop="static" style="z-index: 1000000000;"
     data-keyboard="false" class="modal fade" style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Order </h4>
                <!--Edit Item Detail-->
            </div>
                
                                       
                <div class="modal-body" id="order_review_statement_body">
                    
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default"
                            type="button"><?php echo $this->lang->line('common_Close'); ?> </button><!--Close-->
                    
                </div>
            
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="order_review_modal_review"  role="dialog" aria-labelledby="myModalLabel" style="overflow: scroll;">
    <div class="modal-dialog" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Order Review</h4>
            </div>
            
                <div class="modal-body">
                    <div class="col-sm-1">
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="cn_attachement_approval_Tabview_v_review" class="active"><a href="#Tab-home-v-review" data-toggle="tab" onclick="tabView_review()">
                                    <?php echo $this->lang->line('common_view');?>
                                    <!--View--></a></li>
                        </ul>
                        <ul class="zx-nav zx-nav-tabs zx-tabs-left zx-vertical-text">
                            <li id="cn_attachement_approval_Tabview_vv_review" class=""><a href="#Tab-home-c-review" data-toggle="tab" onclick="tabAttachement_review()">
                                    Statement
                                    <!--View--></a></li>
                        </ul>
                    </div>
                    <div class="col-sm-11">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active_review" id="Tab-home-v-review">
                                <div id="conform_body_review"></div>
                                <hr>
                                
                            </div>

                            <div class="zx-tab-pane hide_review" id="Tab-home-c-review">
                                <div id="conform_body1_review"></div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
           
        </div>
    </div>
</div>

<div class="modal fade"  role="dialog" aria-labelledby="myModalLabel" id="attachment_model_supplier_view" style="z-index: 1000000000;">
        <div class="modal-dialog modal-lg" style="width:80%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="order_review_history"> Supplier Attachments </h4>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
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
                            <tbody id="vendor_attachment" class="no-padding">
                            <tr class="danger">
                                <td colspan="5" class="text-center">
                                    <?php echo $this->lang->line('common_no_attachment_found'); ?><!--No Attachment Found--></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade"  role="dialog" aria-labelledby="myModalLabel" id="vendor_specification_view" style="z-index: 1000000000;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="">Technical Specification </h4>
                </div>
                <div class="modal-body">
                   
                    <div class="row">
                        
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-12  ">
                                    <p id="techText_view"></p>
                                </div>
                            </div>

                        </div>
                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade"  role="dialog" aria-labelledby="myModalLabel" id="vendor_terms_view" style="z-index: 1000000000;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="">Terms & Condition  </h4>
                    <div id="term_ref_supplier">

                    </div>
                </div>
                <div class="modal-body">
                   
                    <div class="row">
                        
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-12  ">
                                    <p id="techText11_view"></p>
                                </div>
                            </div>

                        </div>
                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default-new size-lg" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="progress_bar_model" role="dialog" data-keyboard="false"  style="z-index: 999999">
 <div class="modal-dialog">
   <div class="modal-content">
     <div class="modal-body">
        <div class="text-bold">
            <p id="progress-bar-message">Payroll is Processing Please wait..!</p>
        </div>
       <div class="progress progress-popup">    
            
            <div class="progress-bar" id="progress-bar"  style="width: 0%; background-color:#696cff;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">
                <span class="progress-amount text-bold" id="progress-amount">0%</span>
            </div>
       </div>
     </div>
   </div>
 </div>
</div>

<div class="modal fade bs-example-modal-md" id="document_close_line_wise" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" style="z-index: 1000000000;" >
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Close Item</h4>
            </div>
            <div class="modal-body">
            <?php echo form_open_multipart('', 'id="document_line_close_form" class="form-horizontal"'); ?>
                        
                <input type="hidden" name="documentID_cl" id="documentID_cl">
                <input type="hidden" name="masterID_cl" id="masterID_cl">
                <input type="hidden" name="detailID_cl" id="detailID_cl">
                <input type="hidden" name="tableName_cl" id="tableName_cl">
                <input type="hidden" name="master_col_name_cl" id="master_col_name_cl">

                <div class="row">
                        
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Comment :</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="narration_cl" name="narration_cl" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cancel</button>
                    <button class="btn btn-primary" onclick="close_document_line_wise_item()">Close Item</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-md" id="document_close_line_wise_view" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" style="z-index: 1000000000;" >
    <div class="modal-dialog modal-md" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Close Item</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                            
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Closed By :</label>
                        <div class="col-sm-5" id="cl_by">
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Closed Date :</label>
                        <div class="col-sm-5" id="cl_date">
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Closed Comment :</label>
                        <div class="col-sm-5" id="cl_comment">
                            
                        </div>
                    </div>
                </div>
            
                <span class="hide" id="ac_view_action_all">
                        <div class="row">
                            
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Acknowledge By :</label>
                                <div class="col-sm-5" id="ac_by">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Acknowledge Date :</label>
                                <div class="col-sm-5" id="ac_date">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Acknowledge Comment :</label>
                                <div class="col-sm-5" id="ac_comment">
                                    
                                </div>
                            </div>
                        </div>
                </span>

                <?php echo form_open_multipart('', 'id="document_acknowledge_form" class="form-horizontal"'); ?>
                        
            
                    <input type="hidden" name="masterID_ac" id="masterID_ac">
                    <input type="hidden" name="detailID_ac" id="detailID_ac">

                    <div class="row hide" id="ac_view_all">
                        <div class="col-sm-6">
                            <div class="form-group">
                                    <label class="col-sm-4 control-label">Acknowledge</label>
                                    <div class="col-sm-6" style="top: 5px;">
                                        <input type="checkbox" value="" id="ac_accept" name="ac_accept">
                                        <input type="hidden" value="" id="ac_accept_val" name="ac_accept_val">
                                    </div>
                                
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row hide" id="ac_view">

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Acknowledge Comment <?php required_mark(); ?></label>
                                
                                    <textarea class="form-control" id="ac_comment" name="ac_comment" rows="2"></textarea>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cancel</button>
                    <span id="ac_btn" class="hide">
                    <button class="btn btn-primary" onclick="save_srm_acknowledge()">Save</button></span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="document_item_history" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="exampleModalLabel">
                    Item History</h4>
            </div>
            <div class="modal-body">
              
                <div id="document_item_history_body"></div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="document_cost_allocation_modal" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    Document Cost Allocation</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('', 'id="document_cost_allocation_form" class="form-horizontal"'); ?>
                    <div class="row" id="document_cost_allocation_body"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo $this->lang->line('common_Close'); ?><!--Close--></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade chat-modal-custom" id="chatModalMaxportal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Chat with us</h5>        
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-xs-12 ">
                        <!-- Panel Chat -->
                        <div class="panel" id="chatmax">                    
                            <div class="panel-body bg-white" id="cmax_body">
                            
                            </div>
                            <div class="panel-footer">
                            <form role="form" id="chat_form" class="form-horizontal">
                                <input type="hidden" name="inquiryDetailID_chat" id="inquiryDetailID_chat">
                                <input type="hidden" name="inquiryMasterID_chat" id="inquiryMasterID_chat">
                                <input type="hidden" name="supplierID_chat" id="supplierID_chat">
                                <input type="hidden" name="chatType_chat" id="chatType_chat">
                                <input type="hidden" name="itemAutoID_chat" id="itemAutoID_chat">
                                <input type="hidden" name="documentID_chat" id="documentID_chat">
                                
                                <div class="input-group">
                                <input type="text" class="form-control" placeholder="Say something" id="chat_msg" name="chat_msg"> 
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button" onclick="send_my_message_max_portal()">Send</button>
                                    </span>
                                </div>
                            </form>
                            </div>
                        </div>
                    <!-- End Panel Chat -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>

<!-- END -->


<?php
$passwordComplexityExist = get_password_complexity_exist();
$passwordComplexity = get_password_complexity();
?>
<?php $this->load->view('include/inc-footer-modals'); ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/select2/css/select2.min.css'); ?>"/>
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/validation/css/bootstrapValidator.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/sweetalert/sweet-alert.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datepicker/datepicker3.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/offline/offline.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/offline/offline-language-english.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/holdon/HoldOn.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/datatables/dataTables.bootstrap.css'); ?>"/>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/jasny-bootstrap.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/toastr/toastr.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/style.css'); ?>">
<link rel="stylesheet" type="text/css"
      href="<?php echo base_url('plugins/multiSelectCheckbox/dist/css/bootstrap-multiselect.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/xeditable/css/bootstrap-editable.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker.css'); ?>">

<link rel="stylesheet" href="<?php echo base_url('plugins/dist/css/skin_one.css'); ?>">
<?php
loadTopNavigationCss()
?>
<script type="text/javascript" src="<?php echo base_url('plugins/select2/js/select2.full.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/validation/js/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/timepicker2/bootstrap-timepicker2.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/bootstrap-datepicker.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('plugins/toastr/toastr.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/fastclick/fastclick.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/app.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sparkline/jquery.sparkline.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>

        <!-- Include flatpickr JavaScript -->
<script src="<?php echo base_url('plugins/dist/js/flatpickr.js'); ?>"></script>

<?php
$end_record_num = "";
$end_record_numk = $_SERVER['PHP_SELF'];
$link_array = explode('/', $end_record_numk);
$end_record_num = end($link_array);

$ci =& get_instance();

if ($end_record_num != "m-pos") {
    $ci->session->set_userdata('currentpanel_id', 'm-pos');
    ?>
    <script type="text/javascript" src="<?php echo base_url('plugins/slimScroll/jquery.slimscroll.js'); ?>"></script>
    <?php
} else {
    $ci->session->set_userdata('currentpanel_id', $end_record_num);
}
?>

<script type="text/javascript" src="<?php echo base_url('plugins/chartjs/Chart.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/demo.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/sweetalert/sweet-alert.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/holdon/HoldOn.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/dist/js/typeahead.bundle.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootstrap/js/jasny-bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/handlebars/handlebars-v4.0.5.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/tableHeadFixer/tableHeadFixer.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/multiselect/dist/js/multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/iCheck/icheck.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/multiSelectCheckbox/dist/js/bootstrap-multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/daterangepicker/moment.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/xeditable/js/bootstrap-editable.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/bootbox-alert/bootbox.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/jQuery/jquery.maskedinput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/highchart/highcharts.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/highchart/modules/exporting.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/highchart/modules/no-data-to-display.js'); ?>"></script>

<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/jquery.inputmask.bundle.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/inputmask/inputmask.date.extensions.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/input-mask/dist/inputmask/jquery.inputmask.js'); ?>"></script>
<!-- value as well in textbox-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/numeric/jquery.numeric.min.js'); ?>"></script>

<script type="text/javascript"
        src="<?php echo base_url('plugins/tapmodo-Jcrop-1902fbc/js/jquery.Jcrop.min.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('plugins/combodate/combodate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/combodate/moment.js'); ?>"></script>
<script type="text/javascript"
        src="<?php echo base_url('plugins/datetimepicker/src/js/bootstrap-datetimepicker.js'); ?>"></script>

<!--jquery auto complete-->
<script type="text/javascript"
        src="<?php echo base_url('plugins/jQuery-Autocomplete-master/dist/jquery.autocomplete.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.2.2/js/dataTables.fixedColumns.min.js"></script>
<script type="text/javascript">

    var popup = 0;
    var CSRFHash = '<?php echo $this->security->get_csrf_hash() ?>';
    var numberOfAttempt = 0;
    var templateKeyWord;
    var current_user = '<?php echo $this->common_data['loginusername']; ?>';
    var productID = '<?php echo PRODUCT_ID ?>';
    var supportToken = '<?php echo current_token(); ?>';
    var assignBuyersViewSync =[];

    /*Remove employee master filter values */
    window.localStorage.removeItem("isDischarged");
    window.localStorage.removeItem("employeeCode");
    window.localStorage.removeItem("segment");
    window.localStorage.removeItem('emp-master-alpha-search');
    window.localStorage.removeItem('emp-master-searchKeyword');
    window.localStorage.removeItem('emp-master-designation-list');
    window.localStorage.removeItem('emp-master-segment-list');
    window.localStorage.removeItem('emp-master-status-list');
    window.localStorage.removeItem('emp-master-pagination');

    const channel = new BroadcastChannel('company_switch_channel');

    if (!localStorage.getItem('tab_count')) {
        localStorage.setItem('tab_count', '0');
    }

    channel.onmessage = (event) => {
        let tabCount = parseInt(localStorage.getItem('tab_count'));
        if (event.data.action === 'tab_open') {
            tabCount += 1;
            localStorage.setItem('tab_count', tabCount.toString());
        } else if (event.data.action === 'tab_close') {
            tabCount -= 1;
            localStorage.setItem('tab_count', tabCount.toString());
        }
    };

    channel.postMessage({ action: 'tab_open' });

    window.addEventListener('beforeunload', () => {
        channel.postMessage({ action: 'tab_close' });
    });

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

    function fetchPage(page_url, page_id, page_name, policy_id, data_arr, master_page_url = null) {
        $(window).unbind('scroll');

        if (page_name == 'Operation') {
            getRedirectionToken(page_url);
            return true;
        }

        if (page_name == 'Subscription') {
            var sub_url = '<?php echo site_url("subscription"); ?>';
            window.open(sub_url, '_blank');
            return true;
        }

        if (page_name == 'QHSE') {
            get_QHSE_LoginDetails();
            return true;
        }

        if (page_name == 'Real Max') {
            get_realmax_LoginDetails();
            return true;
        }

        if (page_url == 'errors/html/coming_soon') {
            let sub_url = '<?php echo site_url("dashboard/comingSoon"); ?>';
            window.open(sub_url, '_self');
            return true;
        }
        /***************************************************************
         * date : 2017-09-15
         * Load the employee master filters
         ***************************************************************/
        var s_AlphaSearch = window.localStorage.getItem('emp-master-alpha-search');
        var s_SearchKeyword = window.localStorage.getItem('emp-master-searchKeyword');
        var s_Designation = window.localStorage.getItem('emp-master-designation-list');
        var s_Segment = window.localStorage.getItem('emp-master-segment-list');
        var s_Pagination = window.localStorage.getItem('emp-master-pagination');
        var s_Status = window.localStorage.getItem('emp-master-status-list');

        var filterPost = {
            alphaSearch: s_AlphaSearch,
            searchKeyword: s_SearchKeyword,
            designation: s_Designation,
            segment: s_Segment,
            empStatus: s_Status,
            pagination: s_Pagination
        };

        $.ajax({
            async: true,
            type: 'POST',
            url: '<?php echo site_url("dashboard/fetchPage"); ?>',
            dataType: 'html',
            data: {
                'page_id': page_id,
                'page_url': page_url,
                'page_name': page_name,
                'policy_id': policy_id,
                'data_arr': data_arr,
                'master_page_url': master_page_url,
                'filterPost': filterPost
            },
            beforeSend: function () {
                startLoad();
                check_session_status();
            },
            success: function (page_html) {
                stopLoad();
                /***************************************************************
                 * date : 2017-09-06
                 * to avoid Jquery UI library functions and other styles on
                 * \views\system\hrm\employee_master_new.php
                 ***************************************************************/

                $('.employee_master_styles').attr("disabled", "disabled");

                $('#ajax_body_container').html(page_html);
                $("html, body").animate({scrollTop: "0px"}, 10);
                numberOfAttempt = 0;

            },
            error: function (jqXHR, status, errorThrown) {
                stopLoad();
                $("html, body").animate({scrollTop: "0px"}, 10);
                $('#ajax_body_container').html(jqXHR.responseText + '<br/>Error Message: ' + errorThrown);
                check_session_status();
            }
        });
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

    function document_uplode() {
        var formData = new FormData($("#attachment_uplode_form")[0]);
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
                    attachment_modal($('#documentSystemCode').val(), $('#document_name').val(), $('#documentID').val(), $('#confirmYNadd').val());
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

    function fetch_approval_user_modal(documentID, documentSystemCode) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
            url: '<?php echo site_url('Approvel_user/fetch_approval_user_modal'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                let modal_title = '<?=$this->lang->line('common_approval_user');?>';
                if (documentID = 'HDR') {
                    modal_title = '<?=$this->lang->line('common_approval_user');?>';
                }

                $('#ap_user_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp;' + modal_title);
                <!--Approval user-->
                $('#ap_user_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['approved'])) {
                    $('#ap_user_body').append('<tr class="danger"><td colspan="6" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                    <!--No Records Found-->
                } else {
                    $.each(data['approved'], function (key, value) {
                        comment = ' - ';
                        if (value['approvedComments'] !== null) {
                            comment = value['approvedComments'];
                        }
                        bePlanVar = (value['approvedYN'] == true) ? '<span class="label label-success">&nbsp;</span>' : '<span class="label label-danger">&nbsp;</span>';
                        $('#ap_user_body').append('<tr><td>' + x + '</td><td>' + value['Ename2'] + '</td><td class="text-center"> Level ' + value['approvalLevelID'] + '</td><td class="text-center">  ' + value['approveDate'] + '</td><td class="text-center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                        x++;
                    });
                }
                $("#ap_user_modal").modal({backdrop: "static", keyboard: true});
                $('.confirmed').addClass('hide');
                $('.approved').removeClass('hide');
                $("#c_document_code_approved").html(data['document_code']);
                $("#c_document_date_approved").html(data['document_date']);
                $("#c_confirmed_date_approved").html(data['confirmed_date']);
                $("#c_conformed_by_approved").html(data['conformed_by']);
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_all_approval_users_modal(documentID, documentSystemCode) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
            url: '<?php echo site_url('Approvel_user/fetch_all_approval_users_modal'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ap_user_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; <?php echo $this->lang->line('footer_approval_user'); ?>');
                <!--Approval user-->
                $('#ap_user_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['approved'])) {
                    $('#ap_user_body').append('<tr class="danger"><td colspan="3" class="text-center"><b><?php echo $this->lang->line('common_no_records_found'); ?></b></td></tr>');
                    <!--No Records Found-->
                } else {
                    $.each(data['approved'], function (key, value) {
                        comment = ' - ';
                        if (value['approvedComments'] !== null) {
                            comment = value['approvedComments'];
                        }
                        approvalDate = ' - ';
                        if (value['approveDate'] !== null) {
                            approvalDate = value['approveDate'];
                        }
                        bePlanVar = (value['approvedYN'] == true) ? '<span class="label label-success">&nbsp;</span>' : '<span class="label label-danger">&nbsp;</span>';
                        $('#ap_user_body').append('<tr><td>' + x + '</td><td>' + value['Ename2'] + '</td><td class="text-center"> Level ' + value['approvalLevelID'] + '</td><td class="text-center">' + approvalDate + '</td><td class="text-center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                        x++;
                    });
                }
                $("#ap_user_modal").modal({backdrop: "static", keyboard: true});
                $('.confirmed').removeClass('hide');
                $('.approved').addClass('hide');
                $("#c_document_code_confirmed").html(data['document_code']);
                $("#c_document_date_confirmed").html(data['document_date']);
                $("#c_confirmed_date_confirmed").html(data['confirmed_date']);
                $("#c_conformed_by_confirmed").html(data['conformed_by']);

                //if (documentID == 'LA' && (data.requestForCancelYN !== undefined)) {
                if (documentID == 'LA' && (data.requestForCancelYN == 1)) {
                    $('#ap_user_label').append(' - Cancellation');
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_approval_reject_user_modal(documentID, documentSystemCode) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
            url: '<?php echo site_url('Approvel_user/fetch_reject_user_modal'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                /*                $('#ap_user_label_referback').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; Referred-back History');*/
                $('#reject_ap_user_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['rejected'])) {
                    $('#reject_ap_user_body').append('<tr class="danger"><td colspan="6" class="text-center"><b>No Records Found</b></td></tr>');
                } else {
                    $.each(data['rejected'], function (key, value) {
                        comment = ' - ';
                        if (value['comment'] !== null) {
                            comment = value['comment'];
                        }
                        bePlanVar = '<span class="label label-danger">&nbsp;</span>';
                        $('#reject_ap_user_body').append('<tr><td>' + x + '</td><td>' + value['ECode'] + ' - ' + value['Ename2'] + '</td><td class="text-center"> Level ' + value['rejectedLevel'] + '</td><td style="text-align: center">' + value['referbackDate'] + '</td><td style="text-align: center">' + bePlanVar + '</td><td>' + comment + '</td></tr>');
                        x++;
                    });
                    $("#c_document_code_rejected").html(data['document_code']);
                }
                $("#reject_drill_user_modal").modal({backdrop: "static", keyboard: true});
                //$("#c_document_date_referback").html(data['referback_date']);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function approval_refer_back_user_modal(documentID, documentSystemCode) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID, 'documentSystemCode': documentSystemCode},
            url: '<?php echo site_url('Approvel_user/fetch_approval_referbackuser_user_modal'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#ap_user_label_referback').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right"></span> &nbsp; <?php echo $this->lang->line('footer_referred_back_history');?>');
                <!--Referred-back History-->
                $('#referback_ap_user_body').empty();
                x = 1;
                if (jQuery.isEmptyObject(data['rejected'])) {
                    $('#referback_ap_user_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                    <!--No Records Found-->
                } else {
                    $.each(data['rejected'], function (key, value) {
                        comment = ' - ';
                        if (value['comment'] !== null) {
                            comment = value['comment'];
                        }
                        $('#referback_ap_user_body').append('<tr><td>' + x + '</td><td>' + value['ECode'] + ' - ' + value['Ename2'] + '</td><td class="text-center"> Level ' + value['rejectedLevel'] + '</td><td style="text-align: center">' + value['referbackDate'] + '</td><td>' + comment + '</td></tr>');
                        x++;
                    });
                }
                $("#referback_drill_user_modal").modal({backdrop: "static", keyboard: true});
                $("#c_document_code_referback").html(data['document_code']);
                //$("#c_document_date_referback").html(data['referback_date']);
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
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

    function attachment_modal(documentSystemCode, document_name, documentID, confirmedYN) {
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
                // beforeSend: function () {
                //     check_session_status();
                //     //startLoad();
                // },
                success: function (data) {
                    $('#attachment_modal_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "");
                    $('#attachment_modal_body').empty();
                    $('#attachment_modal_body').append('' + data + '');
                    $("#attachment_modal").modal({backdrop: "static", keyboard: true});
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    function myAlert(type, message, duration = null) {
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

    function initAlertSetup(duration = null) {
        duration = (duration == null) ? '1000' : duration;
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
            theme: "custom",//If not given or inexistent theme throws default theme , sk-bounce , sk-cube-grid
            message: "<div style='font-size: 16px; color:#ffffff; text-shadow: 0px 0px 4px black, 0 0 7px #000000, 0 0 3px #000000;'> Loading, Please wait </div><div id='loaderDivContent'></div>",
            content: '<img src="<?php echo base_url("images/quantum_small.png")?>" alt="Q" width="50px" height="70px">', // If theme is set to "custom", this property is available
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

        isGroupCompany(company_id);

        $('#passwordFormLogin').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                currentPassword: {validators: {notEmpty: {message: '<?php echo $this->lang->line('footer_current_password_is_required');?>.'}}}, /*Current Password is required*/
                newPassword: {validators: {notEmpty: {message: '<?php echo $this->lang->line('footer_new_password_is_required');?>.'}}}, /*New Password is required*/
                confirmPassword: {
                    validators: {
                        identical: {
                            field: 'newPassword',
                            message: '<?php echo $this->lang->line('footer_new_password_and_confirm_password_are_not_matching');?>'/*New Password and Confirm Password are not matching*/
                        },
                        notEmpty: {message: '<?php echo $this->lang->line('footer_confirm_password_are_required');?>.'}/*Confirm Password are required*/
                    }
                }
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
                url: "<?php echo site_url('Profile/change_password'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1]);
                    $('#passwordFormLogin')[0].reset();
                    $("#passwordFormLogin").data('bootstrapValidator').resetForm();
                    if (data[0] == 's') {
                        $('#passwordresetModal').modal('hide');
                    }
                    $('#messagelogin').html('');
                    $('#progrssbarlogin').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 0" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                }, error: function (data) {
                    stopLoad();
                    var msg = JSON.parse(data.responseText);
                    myAlert('w', msg[1])
                }
            });
        });
        /*third_party_model*/
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
        widgetHelpDesk();
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

    function set_sidebar_theme_mode_cookie() {
        var classVal = $('body').attr('class');
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Dashboard/set_sidebar_theme_mode_cookie'); ?>",
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

    function fetch_template_View(documentID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'documentID': documentID},
            url: "<?php echo site_url('Access_menu/fetch_template_keyword'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data) {
                    templateKeyWord = data;
                } else {
                    templateKeyWord = '';
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

    function documentPageView_modal(documentID, para1, para2, approval = 0) {
        // added for show attachemnt in same view page
        $("#profile-v").removeClass("active");
        $("#home-v").addClass("active");
        $("#TabViewActivation_attachment").removeClass("active");
        $("#tab_itemMasterTabF").removeClass("active");
        $("#tab_dodelivered").removeClass("active");
        $("#TabViewActivation_view").addClass("active");

        //fetch parameter to assign template function
        fetch_template_View(documentID);
        attachment_View_modal(documentID, para1);
 
        $('#loaddocumentPageView').html('');
        var siteUrl;
        var paramData = new Array();
        var title = '';
        var a_link;
        var de_link;

        /*set modal width*/
        $('#doc-view-modal-dialog').css('width', '90%');

        //timeout funtion to set time for assigning template parameter
        setTimeout(function () {
            switch (documentID) {

                case "GRV":
                case "PV":
                    $("#deliveredTab_footer_div").html('');
                    $(".dodelivered_footer").hide();
                    $("#itemMasterSubTab_footer_div").html('');
                    $(".itemMasterSubTab_footer").show();
                    break;
                case "DO":
                    $("#deliveredTab_footer_div").html('');
                    $(".dodelivered_footer").show();
                    $("#itemMasterSubTab_footer_div").html('');
                    $(".itemMasterSubTab_footer").hide();
                    break;

                default:
                    $("#deliveredTab_footer_div").html('');
                    $(".dodelivered_footer").hide();
                    $("#itemMasterSubTab_footer_div").html('');
                    $(".itemMasterSubTab_footer").hide();

            }

            switch (documentID) {
                case "MAC":
                    siteUrl = "<?php echo site_url('Employee/monthly_allowance_print'); ?>";
                    paramData.push({name: 'id', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Monthly Allowance Claim";
                    a_link = "<?php echo site_url('Employee/monthly_allowance_print'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_monthly_allowance'); ?>/" + para1 + '/MAC';
                    break;
                case "WFH":
                    siteUrl = "<?php echo site_url('Employee/load_WFH_request_conformation'); ?>";
                    paramData.push({name: 'wfhId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Work From Home";
                    /*Work From Home Request*/
                    a_link = "<?php echo site_url('Employee/load_WFH_request_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_wfh_request'); ?>/" + para1 + '/WFH';
                    break;
                case "PAA":
                    if(para2){
                        siteUrl = "<?php echo site_url('Employee/load_personal_action_conformation_mse'); ?>";
                        paramData.push({name: 'id', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "Personal Action";
                        a_link = "<?php echo site_url('Employee/load_personal_action_conformation_mse'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_personal_action_mse'); ?>/" + para1 + '/PAA';
                    }else{
                        siteUrl = "<?php echo site_url('Employee/load_personal_action_conformation'); ?>";
                        paramData.push({name: 'id', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "Personal Action";
                        a_link = "<?php echo site_url('Employee/load_personal_action_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_personal_action'); ?>/" + para1 + '/PAA';
                    }
                    break;
                case "BT":  // Bank Transfer -
                    siteUrl = "<?php echo site_url('Bank_rec/bank_transfer_view'); ?>";
                    paramData.push({name: 'bankTransferAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_bank_transfer');?>";
                    /*Bank Transfer*/
                    break;
                case "PO": // Purchase Order -

                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Procurement/load_purchase_order_conformation_buyback'); ?>";
                        paramData.push({name: 'purchaseOrderID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('common_purchase_order');?>";
                        /*Purchase Order*/
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>";
                        paramData.push({name: 'purchaseOrderID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('common_purchase_order');?>";
                        /*Purchase Order*/
                        break;
                    }


                case "EC": // Expense Claim 
                    siteUrl = "<?php echo site_url('ExpenseClaim/load_expense_claim_conformation'); ?>";
                    paramData.push({name: 'expenseClaimMasterAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('common_expense_claim');?>";
                    /*Expense Claim*/
                    break;
                case "GRV": // Good Receipt Voucher -
                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Grv/load_grv_conformation_buyback'); ?>";
                        paramData.push({name: 'grvAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_goods_received_voucher');?>";
                        /*Goods Received Voucher*/
                        a_link = "<?php echo site_url('Grv/load_grv_conformation_buyback'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + para1 + '/GRV';
                        load_itemMasterSub('GRV', para1); // item master sub - Sahfri
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Grv/load_grv_conformation'); ?>";
                        paramData.push({name: 'grvAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_goods_received_voucher');?>";
                        /*Goods Received Voucher*/
                        a_link = "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + para1 + '/GRV';
                        load_itemMasterSub('GRV', para1); // item master sub - Sahfri
                        break;
                    }


                case "SR": // Purchase Return -
                    siteUrl = "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>";
                    paramData.push({name: 'stockReturnAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_purchase_return');?>";
                    /*Purchase Return*/
                    a_link = "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_stock_return'); ?>/" + para1 + '/SR';
                    break;
                case "MI": // Material Issue -
                    if (para2 == 'mc' || templateKeyWord == 'mc') {
                        siteUrl = "<?php echo site_url('Inventory/load_material_issue_conformation_mc'); ?>";
                        paramData.push({name: 'itemIssueAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_material_issue');?>";
                        /*Material Issue*/
                        a_link = "<?php echo site_url('Inventory/load_material_issue_conformation_mc'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + para1 + '/MI';
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>";
                        paramData.push({name: 'itemIssueAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_material_issue');?>";
                        /*Material Issue*/
                        a_link = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + para1 + '/MI';
                        break;
                    }

                case "ST": // Stock Transfer -

                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Inventory/load_stock_transfer_conformation_buyback'); ?>";
                        paramData.push({name: 'stockTransferAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_stock_transfer');?>";
                        /*Stock Transfer*/
                        a_link = "<?php echo site_url('Inventory/load_stock_transfer_conformation_buyback'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + para1 + '/ST';
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>";
                        paramData.push({name: 'stockTransferAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_stock_transfer');?>";
                        /*Stock Transfer*/
                        a_link = "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + para1 + '/ST';
                        break;
                    }

                case "SA": // Stock Adjustment -Document ID is not set .
                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>";
                        paramData.push({name: 'stockAdjustmentAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_stock_adjustment');?>";
                        /*Stock Adjustment*/
                        a_link = "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + para1 + '/SA';
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>";
                        paramData.push({name: 'stockAdjustmentAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_stock_adjustment');?>";
                        /*Stock Adjustment*/
                        a_link = "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + para1 + '/SA';
                        break;
                    }

                case "BSI": // Supplier Invoices -
                    siteUrl = "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>";
                    paramData.push({name: 'InvoiceAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_supplier_invoices');?>";
                    /*Supplier Invoices*/
                    a_link = "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_supplier_invoices'); ?>/" + para1 + '/BSI';
                    break;
                case "DN": // Debit Note -
                    siteUrl = "<?php echo site_url('Payable/load_dn_conformation'); ?>";
                    paramData.push({name: 'debitNoteMasterAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_debit_note');?>";
                    /*Debit Note*/
                    a_link = "<?php echo site_url('Payable/load_dn_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_debit_note'); ?>/" + para1 + '/DN';
                    break;
                case "PV": // Payment Voucher -
                    if (para2 == 'SUOM' || templateKeyWord == 'SUOM') {
                        siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation_suom'); ?>";
                    } else if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation_buyback'); ?>";
                    } else {
                        siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>";
                    }
                    paramData.push({name: 'payVoucherAutoId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                    /*Payment Voucher*/
                    if (para2 == 'SUOM' || templateKeyWord == 'SUOM') {
                        a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation_suom'); ?>/" + para1;
                    } else if (para2 == 'buy' || templateKeyWord == 'buy') {
                        a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation_buyback'); ?>/" + para1;
                    } else {
                        a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + para1;
                    }
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                    load_itemMasterSub('PV', para1); // item master sub
                    break;
                case "PVM": // Payment Match -
                    siteUrl = "<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>";
                    paramData.push({name: 'matchID', value: para1});
                    title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                    /*Payment Voucher*/
                    a_link = "<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                    break;
                case "CINV": // Invoice -
                    if (para2 == 'insurance' || templateKeyWord == 'insurance') {
                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_invoicetype'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation_invoicetype'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;
                    } else if (para2 == 'margin' || templateKeyWord == 'margin') {
                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_margin'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation_margin'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;
                    } else if (para2 == 'SUOM' || templateKeyWord == 'SUOM') {
                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_suom'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation_suom'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;
                    } else if (para2 == 'DS') {

                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_ds'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation_ds'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;

                    } else if (para2 == 'Commission') {

                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_cs'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation_cs'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;

                    } else {
                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;
                    }


                case "HCINV": // Invoice
                    siteUrl = "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>";
                    paramData.push({name: 'invoiceAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_invoice');?>";
                    /*Invoice*/
                    a_link = "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice_buyback'); ?>/" + para1 + '/HCINV';
                    break;
                case "CN": // Credit Note
                    siteUrl = "<?php echo site_url('Receivable/load_cn_conformation'); ?>";
                    paramData.push({name: 'creditNoteMasterAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_credit_note');?>";
                    /*Credit Note*/
                    a_link = "<?php echo site_url('Receivable/load_cn_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + para1 + '/CN';
                    break;
                case "RV": // Receipt Voucher
                    if (para2 == 'suom' || templateKeyWord == 'suom') {
                        siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_conformation_suom'); ?>";
                        paramData.push({name: 'receiptVoucherAutoId', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_receipt_voucher');?>";
                        /*Receipt Voucher*/
                        a_link = "<?php echo site_url('Receipt_voucher/load_rv_conformation_suom'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher_suom'); ?>/" + para1 + '/RV';
                        break;
                    } else if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_conformation_buyback'); ?>";
                        paramData.push({name: 'receiptVoucherAutoId', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_receipt_voucher');?>";
                        /*Receipt Voucher*/
                        a_link = "<?php echo site_url('Receipt_voucher/load_rv_conformation_buyback'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + para1 + '/RV';
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>";
                        paramData.push({name: 'receiptVoucherAutoId', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_receipt_voucher');?>";
                        /*Receipt Voucher*/
                        a_link = "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + para1 + '/RV';
                        break;
                    }
                case "RVM": // Receipt Matching
                    siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_match_conformation'); ?>";
                    paramData.push({name: 'matchID', value: para1});
                    title = "<?php echo $this->lang->line('footer_receipt_matching');?>";
                    /*Receipt Matching*/
                    break;
                case "JV": // Journal Voucher 
                    siteUrl = "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>";
                    paramData.push({name: 'JVMasterAutoId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_journal_entry');?>";
                    /*Journal Entry*/
                    a_link = "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_journal_entry'); ?>/" + para1 + '/JV';
                    break;
                case "BR": // Bank Rec
                    siteUrl = "<?php echo site_url('Bank_rec/bank_rec_book_balance'); ?>";
                    paramData.push({name: 'bankRecAutoID', value: para1});
                    paramData.push({name: 'GLAutoID', value: para2});
                    title = "<?php echo $this->lang->line('footer_bank_reconciliation');?>";
                    /*Bank Reconciliation*/
                    break;
                case "FA": // Fixed Asset
                    siteUrl = "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>";
                    paramData.push({name: 'faID', value: para1});
                    title = "<?php echo $this->lang->line('footer_fixed_asset');?>";
                    /*Fixed Asset*/
                    a_link = "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>/" + para1;
                    //de_link="<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + para1 + '/FA';
                    break;
                case "FAD": // Fixed Asset Depriciation
                    if (para2 == 'month' || templateKeyWord == 'month') {
                        siteUrl = "<?php echo site_url('AssetManagement/load_asset_dipriciation_view'); ?>";
                        paramData.push({name: 'depMasterAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_asset_monthly_depreciation');?>";
                        /*Asset Monthly Depreciation*/
                    } else {
                        siteUrl = "<?php echo site_url('AssetManagement/load_asset_dipriciation_adhoc_view'); ?>";
                        paramData.push({name: 'depMasterAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_asset_ad_hoc_depreciation');?>";
                        /*Asset Ad hoc Depreciation*/
                    }
                    break;
                case "ADSP": // Fixed Asset Disposal
                    siteUrl = "<?php echo site_url('AssetManagement/load_asset_disposal_view'); ?>";
                    paramData.push({name: 'assetdisposalMasterAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_asset_disposal');?>";
                    /*Asset Disposal*/
                    break;
                case "SD": // Salary Declaration - ( Only in salary declaration approval)
                    siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation'); ?>";
                    paramData.push({name: 'declarationMasterID', value: para1}, {name: 'isFromApproval', value: 'Y'});
                    title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                    /*Salary Declaration*/
                    break;
                case "SD-C": // Salary Declaration - (in Salary declaration master)
                    siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation'); ?>";
                    paramData.push({name: 'declarationMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                    /*Salary Declaration*/
                    break;
                case "SD-C2": // Salary Declaration - (in Salary declaration master)
                    siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation_view'); ?>";
                    paramData.push({name: 'declarationMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                    /*Salary Declaration*/
                    break;
                case "SVD-2": // Salary Declaration - (in Salary declaration master)
                    siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation_view_variable'); ?>";
                    paramData.push({name: 'declarationMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                    /*Salary Declaration*/
                    break;
                case "VD": // Variable Pay Declaration - 
                    siteUrl = "<?php echo site_url('Employee/variable_pay_approval_confirmation_view'); ?>";
                    paramData.push({name: 'masterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_variable_pay_declaration');?>";
                    break;
                case "CNT": // Contract

                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_contract');?>";
                    } else if (para2 == 'NH') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_nh'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_order');?>";
                    } else {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_contract');?>";
                    }


                    /*Contract*/
                    break;
                case "QUT": // Quotation

                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_quotation');?>";
                        /*Asset Monthly Depreciation*/
                    } else if (para2 == 'NH') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_nh'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_order');?>";
                    } else {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_quotation');?>";
                        /*Asset Ad hoc Depreciation*/
                    }

                    /*Quotation*/
                    break;
                case "SO": // Quotation


                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_order');?>";
                        /*Asset Monthly Depreciation*/
                    } else if (para2 == 'NH') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_nh'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_order');?>";
                    } else {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_order');?>";
                        /*Asset Ad hoc Depreciation*/
                    }

                    /*Sales Order*/
                    break;
                case "SC": // Sales Commission
                    siteUrl = "<?php echo site_url('Sales/load_sc_conformation'); ?>";
                    paramData.push({name: 'salesCommisionID', value: para1});
                    title = "<?php echo $this->lang->line('footer_sales_commission');?>";
                    /*Sales Commission*/
                    a_link = "<?php echo site_url('Sales/load_sc_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_SC'); ?>/" + para1 + '/SC';
                    break;
                case "FED": // Fixed Element Declaration
                    siteUrl = "<?php echo site_url('Employee/load_fixed_elementDeclaration_approval_confirmation'); ?>";
                    paramData.push({name: 'feDeclarationMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_fixed_element_declaration');?>";
                    /*Fixed Element Declaration*/
                    break;
                case "SLR": // Sales Return

                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>";
                        paramData.push({name: 'salesReturnAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_return');?>";
                        a_link = "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return_buyback'); ?>/" + para1 + '/SLR';
                    } else {
                        siteUrl = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>";
                        paramData.push({name: 'salesReturnAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_return');?>";
                        a_link = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return'); ?>/" + para1 + '/SLR';
                    }

                    /*Sales Return*/
                    break;
                case "PRQ": // Purchase Order -
                    siteUrl = "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>";
                    paramData.push({name: 'purchaseRequestID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_purchase_request');?>";
                    /*Purchase Request*/
                    break;
                /*** SME-2341 => Remove from document drill down function ***/
                /*case "SPN": // Salary Processing (Non-payroll)
                    siteUrl = "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>";
                    paramData.push({'name': 'hidden_payrollID', 'value': para1});
                    paramData.push({'name': 'isNonPayroll', 'value': 'Y'});
                    paramData.push({'name': 'from_approval', 'value': 'Y'});
                    paramData.push({'name': 'isForReverse', 'value': 'Y'});
                    title = "<?php echo $this->lang->line('footer_monthly_allowance');?>";
                    /!*Monthly Allowance*!/
                    break;
                case "SP": // Salary Processing
                    siteUrl = "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>";
                    paramData.push({'name': 'hidden_payrollID', 'value': para1});
                    paramData.push({'name': 'isNonPayroll', 'value': 'N'});
                    paramData.push({'name': 'from_approval', 'value': 'Y'});
                    paramData.push({'name': 'isForReverse', 'value': 'Y'});
                    title = "<?php echo $this->lang->line('footer_salary_processing');?>";
                    /!*Salary Processing*!/
                    break;*/
                case "MRN": // Purchase Order
                    siteUrl = "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>";
                    paramData.push({name: 'mrnAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_material_receipt_note');?>";
                    /*Material Receipt Note*/
                    break;
                case "CMT": // Commitment
                    siteUrl = "<?php echo site_url('OperationNgo/load_donor_commitment_confirmation'); ?>";
                    paramData.push({name: 'commitmentAutoId', value: para1});
                    a_link = "<?php echo site_url('OperationNgo/load_donor_commitment_confirmation'); ?>/" + para1;
                    title = "<?php echo $this->lang->line('footer_donor_commitment');?>";
                    /*Donor Commitment*/
                    break;
                case "DC": // collection
                    siteUrl = "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>";
                    paramData.push({name: 'collectionAutoId', value: para1});
                    a_link = "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_donor_collection'); ?>/" + para1 + '/DC';
                    title = "<?php echo $this->lang->line('footer_donor_collection');?>";
                    /*Donor Collection*/
                    break;
                case "BBM": // Buy Back Mortality 
                    siteUrl = "<?php echo site_url('buyback/load_mortality_confirmation'); ?>";
                    paramData.push({name: 'mortalityAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_mortality');?>";
                    /*Mortality*/
                    break;
                case "BBDPN": // Buy Back Dispatch Note 
                    siteUrl = "<?php echo site_url('buyback/load_dispatchNote_confirmation'); ?>";
                    paramData.push({name: 'dispatchAutoID', value: para1});
                    paramData.push({'name': 'batchid', value: para2});
                    title = "<?php echo $this->lang->line('footer_dispatch_note');?>";
                    /*Dispatch Note*/
                    break;
                case "BBCR": // Buy Back Collection 
                    siteUrl = "<?php echo site_url('Buyback/load_buyback_collection_confirmation'); ?>";
                    paramData.push({name: 'collectionautoid', value: para1});
                    title = "Buyback Collection";
                    break;
                case "BBGRN": // Buy Back Good Receipt Note 
                    siteUrl = "<?php echo site_url('buyback/load_goodReceiptNote_confirmation'); ?>";
                    paramData.push({name: 'grnAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_good_receipt_note');?>";
                    /*Good Receipt Note*/
                    break;
                case "EST": // Estimate 
                    siteUrl = "<?php echo site_url('MFQ_Estimate/fetch_estimate_print'); ?>";
                    paramData.push({name: 'estimateMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_estimate');?>";
                    /*Estimate*/
                    break;
                case "BBPV": // Buy Back Payment Voucher
                    siteUrl = "<?php echo site_url('buyback/load_paymentVoucher_confirmation'); ?>";
                    paramData.push({name: 'pvMasterAutoID', value: para1});
                    title = "";
                    /*Payment Voucher*/
                    break;
                case "PRVR": // Payment Reversal
                    siteUrl = "<?php echo site_url('PaymentReversal/load_payment_reversal_conformation'); ?>";
                    paramData.push({name: 'paymentReversalAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_payment_reversal');?>";
                    /*Payment Reversal*/
                    break;
                case "BBBC": // Buy Back Batch Closing
                    siteUrl = "<?php echo site_url('buyback/load_production_report_confirmation'); ?>";
                    paramData.push({name: 'batchMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_production_statement');?>";
                    /*Production Statement*/
                    break;
                case "RJV": //Recurring Journal Voucher
                    siteUrl = "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>";
                    paramData.push({name: 'RJVMasterAutoId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Recurring Journal Entry";
                    /*Journal Entry*/
                    a_link = "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>/" + para1;
                    break;
                case "BBFVR": // Buy Back Farm Visit Report
                    siteUrl = "<?php echo site_url('buyback/load_farmVisitReport_confirmation'); ?>";
                    paramData.push({name: 'farmerVisitID', value: para1});
                    title = "Farm Visit Report";
                    break;
                case "MR": // Material Request
                    siteUrl = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>";
                    paramData.push({name: 'mrAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Material Request";
                    /**/
                    a_link = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>/" + para1;
                    break;
                case "MIC": // inventory Catalogue
                    siteUrl = "<?php echo site_url('Inventory/load_inventory_catalogue_conformation'); ?>";
                    paramData.push({name: 'mrAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Inventory Catalogue";
                    /**/
                    a_link = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>/" + para1;
                    break;
                case "CI": // Customer inquiry 
                    siteUrl = "<?php echo site_url('MFQ_CustomerInquiry/fetch_customer_inquiry_print'); ?>";
                    paramData.push({name: 'ciMasterID', value: para1});
                    title = "Customer Inquiry";
                    break;
                case "YPRP": // Yield Preparation 
                    siteUrl = "<?php echo site_url('POS_yield_preparation/yield_preparation_print'); ?>";
                    paramData.push({name: 'yieldPreparationID', value: para1});
                    title = "Yield Preparation";
                    /****/
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_yield_preparation'); ?>/" + para1 + '/YPRP';
                    break;
                case "PRP": // collection 
                    siteUrl = "<?php echo site_url('OperationNgo/load_project_proposal_confirmation'); ?>";
                    paramData.push({name: 'proposalID', value: para1});
                    title = "Donor Collection";
                    /*Prpoposal Approval*/
                    a_link = "<?php echo site_url('OperationNgo/load_project_proposal_print_pdf_approval'); ?>/" + para1;
                    break;
                case "SCNT": // Stock Counting 
                    if (para2 == 'SCNTsuom' || templateKeyWord == 'SCNTsuom') {
                        siteUrl = "<?php echo site_url('StockCounting/load_stock_counting_conformation_suom'); ?>";
                    } else {
                        siteUrl = "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>";
                    }
                    paramData.push({name: 'stockCountingAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Stock Counting";
                    /**/
                    if (para2 == 'SCNTsuom' || templateKeyWord == 'SCNTsuom') {
                        a_link = "<?php echo site_url('StockCounting/load_stock_counting_conformation_suom'); ?>/" + para1;
                    } else {
                        a_link = "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>/" + para1;
                    }
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_scnt'); ?>/" + para1 + '/SCNT';
                    break;
                case "BBDR":
                    siteUrl = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>";
                    paramData.push({name: 'returnAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Return";
                    a_link = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>/" + para1;

                    break;
                case "IOU":
                    siteUrl = "<?php echo site_url('Iou/load_iou_voucher_confirmation'); ?>";
                    paramData.push({name: 'voucherAutoID', value: para1});
                    title = "IOU Voucher";

                    break;

                case "IOUE": // IOUB Voucher
                    $(".itemMasterSubTab_footer").hide();
                    $("#TabViewActivation_view").hide();
                    siteUrl = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>";
                    paramData.push({name: 'IOUbookingmasterid', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "IOU Booking";
                    /**/
                    a_link = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Iou/fetch_double_entry_iou_booking'); ?>/" + para1 + '/IOUB';
                    break;

                case "FU": // Fuel Usage
                    siteUrl = "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>";
                    paramData.push({name: 'fuelusageID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Fuel Usage";
                    /**/
                    a_link = "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>/" + para1;
                    break;
                case "JP": 
                    siteUrl = "<?php echo site_url('Journeyplan/load_jp_view'); ?>";
                    paramData.push({name: 'journeyPlanMasterID', value: para1});
                    title = "Journey Plan";

                    a_link = "<?php echo site_url('Journeyplan/load_jp_view'); ?>/" + para1;
                    break;
                case "BDT": // Budget Transfer
                    siteUrl = "<?php echo site_url('Budget_transfer/load_budget_transfer_view'); ?>";
                    paramData.push({name: 'budgetTransferAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Budget Transfer";
                    /*Budget Transfer*/
                    break;
                case "STJOB":
                    siteUrl = "<?php echo site_url('MFQ_Job_standard/load_standardjobcard_print'); ?>";
                    paramData.push({name: 'jobAutoID', value: para1});
                    title = "Standard Job Card";

                    a_link = "<?php echo site_url('MFQ_Job_standard/load_standardjobcard_print'); ?>/" + para1;
                    de_link = "<?php echo site_url('MFQ_Job_standard/fetch_double_entry_standardjobcard'); ?>/" + para1 + '/STJOB';
                    break;

                case "DO": 
                    siteUrl = "<?php echo site_url('Delivery_order/load_order_confirmation_view'); ?>";
                    paramData.push({name: 'orderAutoID', value: para1});
                    title = "Delivery Order";
                    load_Delivered_view('DO', para1); // Delivered view
                    break;

                case "SAR": 
                    siteUrl = "<?php echo site_url('Employee/load_salary_advance_request_view/view'); ?>";
                    paramData.push({name: 'masterID', value: para1});
                    title = "Salary Advance Request";
                    break;

                case "LEC": 
                    siteUrl = "<?php echo site_url('Employee/leave_encashment_and_salary_view/view/Y'); ?>";
                    paramData.push({name: 'masterID', value: para1});
                    title = (para2 == 1) ? "Leave Encashment" : "Leave Salary";
                    break;

                case "HDR": 
                    siteUrl = "<?php echo site_url('Employee/view_hr_letter_request/'); ?>";
                    paramData.push({name: 'masterID', value: para1});
                    title = "Document Request";
                    $('#doc-view-modal-dialog').removeAttr('style');
                    break;
                case "OPCNT": 
                    siteUrl = "<?php echo site_url('Operation/load_contract_master_view'); ?>";
                    paramData.push({name: 'contractUID', value: para1});
                    title = "Contract Master";
                    break;
                case "OPJOB": 
                    siteUrl = "<?php echo site_url('Operation/load_ticket_master_view'); ?>";
                    paramData.push({name: 'ticketidAtuto', value: para1});
                    title = "Ticket Master";
                    break;
                case "ORD-RVW": // Order Review 
                    siteUrl = "<?php echo site_url('Srm_master/load_ordereview_conformation'); ?>";
                    paramData.push({name: 'orderreviewID', value: para1});
                    title = "Order Review";
                    break;
                case "SS": // Split Salary 
                    siteUrl = "<?php echo site_url('Employee/load_splitSalary_conformation'); ?>";
                    paramData.push({name: 'splitSalaryMasterID', value: para1});
                    title = "Split Salary";
                    break;

                case "STB": // Stock Bulk Transfer 
                    siteUrl = "<?php echo site_url('Inventory/load_bulk_transfer_conformation'); ?>";
                    paramData.push({name: 'stockTransferAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_stock_transfer');?>";
                    /*Stock Transfer*/
                    a_link = "<?php echo site_url('Inventory/load_bulk_transfer_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_bulk_transfer'); ?>/" + para1 + '/STB';
                    break;
                case "RRVR": // Receipt reversal
                    siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>";
                    paramData.push({name: 'payVoucherAutoId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    paramData.push({name: 'doc_type', value: 'RRVR'});
                    title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                    /*Payment Voucher*/
                    a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                    load_itemMasterSub('PV', para1); // item master sub
                    break;

                case "MDN": // Manufacturing Delivery Note
                    siteUrl = "<?php echo site_url('MFQ_DeliveryNote/load_deliveryNote_confirmation'); ?>";
                    paramData.push({name: 'deliverNoteID', value: para1});
                    title = "<?php echo $this->lang->line('footer_');?>";
                    break;

                case "MCINV": // Manufacturing Customer Invoice
                    siteUrl = "<?php echo site_url('MFQ_CustomerInvoice/fetch_customer_invoice_print'); ?>";
                    paramData.push({name: 'invoiceAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_customer_invoice');?>";
                    break;
                
                case "JOB": // Manufacturing Job View
                    siteUrl = "<?php echo site_url('MFQ_Estimate/fetch_job_order_view'); ?>";
                    paramData.push({name: 'estimateMasterID', value: para1});
                    paramData.push({name: 'workProcessID', value: 2});
                    paramData.push({name: 'html', value: true});
                    title = "<?php echo $this->lang->line('footer_customer_invoice');?>";
                    break;


                default:
                    notification('<?php echo $this->lang->line('footer_document_id_is_not_set');?> .', 'w');
                    /*Document ID is not set*/
                    return false;
            }
            paramData.push({name: 'html', value: true});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: paramData,
                url: siteUrl,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    $('#documentPageViewTitle').html(title);
                    $('#loaddocumentPageView').html(data);
                    $('#documentPageView').modal('show');
                    $("#a_link").attr("href", a_link);
                    $("#de_link").attr("href", de_link);
                    $('.review').removeClass('hide');
                    stopLoad();
                    if (documentID = 'SP') {
                        $('#paysheet-tb').tableHeadFixer({
                            head: true,
                            foot: true,
                            left: 0,
                            right: 0,
                            'z-index': 0
                        });
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }, 500);

    }

    function documentPageView_modal_version(documentID, para1, para2, approval = 0) {
        // added for show attachemnt in same view page
        $("#profile-v").removeClass("active");
        $("#home-v").addClass("active");
        $("#TabViewActivation_attachment").removeClass("active");
        $("#tab_itemMasterTabF").removeClass("active");
        $("#tab_dodelivered").removeClass("active");
        $("#TabViewActivation_view").addClass("active");

        //fetch parameter to assign template function
        fetch_template_View(documentID);
        attachment_View_modal(documentID, para1);
 
        $('#loaddocumentPageView').html('');
        var siteUrl;
        var paramData = new Array();
        var title = '';
        var a_link;
        var de_link;

        /*set modal width*/
        $('#doc-view-modal-dialog').css('width', '90%');

        //timeout funtion to set time for assigning template parameter
        setTimeout(function () {
            switch (documentID) {

                case "GRV":
                case "PV":
                    $("#deliveredTab_footer_div").html('');
                    $(".dodelivered_footer").hide();
                    $("#itemMasterSubTab_footer_div").html('');
                    $(".itemMasterSubTab_footer").show();
                    break;
                case "DO":
                    $("#deliveredTab_footer_div").html('');
                    $(".dodelivered_footer").show();
                    $("#itemMasterSubTab_footer_div").html('');
                    $(".itemMasterSubTab_footer").hide();
                    break;

                default:
                    $("#deliveredTab_footer_div").html('');
                    $(".dodelivered_footer").hide();
                    $("#itemMasterSubTab_footer_div").html('');
                    $(".itemMasterSubTab_footer").hide();

            }

            switch (documentID) {
                
                case "WFH": /**SMSD - Work From Home */
                    siteUrl = "<?php echo site_url('Employee/load_WFH_request_conformation'); ?>";
                    paramData.push({name: 'wfhId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Work From Home";
                    /*Work From Home Request*/
                    a_link = "<?php echo site_url('Employee/load_WFH_request_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_wfh_request'); ?>/" + para1 + '/WFH';
                    break;
                case "BT":  // Bank Transfer -
                    siteUrl = "<?php echo site_url('Bank_rec/bank_transfer_view'); ?>";
                    paramData.push({name: 'bankTransferAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_bank_transfer');?>";
                    /*Bank Transfer*/
                    break;
                case "PO": // Purchase Order -

                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Procurement/load_purchase_order_conformation_buyback'); ?>";
                        paramData.push({name: 'purchaseOrderID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('common_purchase_order');?>";
                        /*Purchase Order*/
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Procurement/load_purchase_order_conformation_version'); ?>";
                        paramData.push({name: 'purchaseOrderID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('common_purchase_order');?>";
                        /*Purchase Order*/
                        break;
                    }


                case "EC": // Expense Claim 
                    siteUrl = "<?php echo site_url('ExpenseClaim/load_expense_claim_conformation'); ?>";
                    paramData.push({name: 'expenseClaimMasterAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('common_expense_claim');?>";
                    /*Expense Claim*/
                    break;
                case "GRV": // Good Receipt Voucher -
                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Grv/load_grv_conformation_buyback'); ?>";
                        paramData.push({name: 'grvAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_goods_received_voucher');?>";
                        /*Goods Received Voucher*/
                        a_link = "<?php echo site_url('Grv/load_grv_conformation_buyback'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + para1 + '/GRV';
                        load_itemMasterSub('GRV', para1); // item master sub - Sahfri
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Grv/load_grv_conformation'); ?>";
                        paramData.push({name: 'grvAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_goods_received_voucher');?>";
                        /*Goods Received Voucher*/
                        a_link = "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + para1 + '/GRV';
                        load_itemMasterSub('GRV', para1); // item master sub - Sahfri
                        break;
                    }


                case "SR": // Purchase Return -
                    siteUrl = "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>";
                    paramData.push({name: 'stockReturnAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_purchase_return');?>";
                    /*Purchase Return*/
                    a_link = "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_stock_return'); ?>/" + para1 + '/SR';
                    break;
                case "MI": // Material Issue -
                    if (para2 == 'mc' || templateKeyWord == 'mc') {
                        siteUrl = "<?php echo site_url('Inventory/load_material_issue_conformation_mc'); ?>";
                        paramData.push({name: 'itemIssueAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_material_issue');?>";
                        /*Material Issue*/
                        a_link = "<?php echo site_url('Inventory/load_material_issue_conformation_mc'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + para1 + '/MI';
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>";
                        paramData.push({name: 'itemIssueAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_material_issue');?>";
                        /*Material Issue*/
                        a_link = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + para1 + '/MI';
                        break;
                    }

                case "ST": // Stock Transfer -

                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Inventory/load_stock_transfer_conformation_buyback'); ?>";
                        paramData.push({name: 'stockTransferAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_stock_transfer');?>";
                        /*Stock Transfer*/
                        a_link = "<?php echo site_url('Inventory/load_stock_transfer_conformation_buyback'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + para1 + '/ST';
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>";
                        paramData.push({name: 'stockTransferAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_stock_transfer');?>";
                        /*Stock Transfer*/
                        a_link = "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + para1 + '/ST';
                        break;
                    }

                case "SA": // Stock Adjustment -Document ID is not set .
                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>";
                        paramData.push({name: 'stockAdjustmentAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_stock_adjustment');?>";
                        /*Stock Adjustment*/
                        a_link = "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + para1 + '/SA';
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>";
                        paramData.push({name: 'stockAdjustmentAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_stock_adjustment');?>";
                        /*Stock Adjustment*/
                        a_link = "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + para1 + '/SA';
                        break;
                    }

                case "BSI": // Supplier Invoices -
                    siteUrl = "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>";
                    paramData.push({name: 'InvoiceAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_supplier_invoices');?>";
                    /*Supplier Invoices*/
                    a_link = "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_supplier_invoices'); ?>/" + para1 + '/BSI';
                    break;
                case "DN": // Debit Note -
                    siteUrl = "<?php echo site_url('Payable/load_dn_conformation'); ?>";
                    paramData.push({name: 'debitNoteMasterAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_debit_note');?>";
                    /*Debit Note*/
                    a_link = "<?php echo site_url('Payable/load_dn_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_debit_note'); ?>/" + para1 + '/DN';
                    break;
                case "PV": // Payment Voucher -
                    if (para2 == 'SUOM' || templateKeyWord == 'SUOM') {
                        siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation_suom'); ?>";
                    } else if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation_buyback'); ?>";
                    } else {
                        siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>";
                    }
                    paramData.push({name: 'payVoucherAutoId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                    /*Payment Voucher*/
                    if (para2 == 'SUOM' || templateKeyWord == 'SUOM') {
                        a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation_suom'); ?>/" + para1;
                    } else if (para2 == 'buy' || templateKeyWord == 'buy') {
                        a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation_buyback'); ?>/" + para1;
                    } else {
                        a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + para1;
                    }
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                    load_itemMasterSub('PV', para1); // item master sub
                    break;
                case "PVM": // Payment Match -
                    siteUrl = "<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>";
                    paramData.push({name: 'matchID', value: para1});
                    title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                    /*Payment Voucher*/
                    a_link = "<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                    break;
                case "CINV": // Invoice -
                    if (para2 == 'insurance' || templateKeyWord == 'insurance') {
                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_invoicetype'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation_invoicetype'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;
                    } else if (para2 == 'margin' || templateKeyWord == 'margin') {
                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_margin'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation_margin'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;
                    } else if (para2 == 'SUOM' || templateKeyWord == 'SUOM') {
                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_suom'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation_suom'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;
                    } else if (para2 == 'DS') {

                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_ds'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation_ds'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;

                    } else if (para2 == 'Commission') {

                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_cs'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation_cs'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;

                    } else {
                        siteUrl = "<?php echo site_url('invoices/load_invoices_conformation'); ?>";
                        paramData.push({name: 'invoiceAutoID', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_invoice');?>";
                        /*Invoice*/
                        a_link = "<?php echo site_url('invoices/load_invoices_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                        break;
                    }


                case "HCINV": // Invoice
                    siteUrl = "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>";
                    paramData.push({name: 'invoiceAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_invoice');?>";
                    /*Invoice*/
                    a_link = "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice_buyback'); ?>/" + para1 + '/HCINV';
                    break;
                case "CN": // Credit Note
                    siteUrl = "<?php echo site_url('Receivable/load_cn_conformation'); ?>";
                    paramData.push({name: 'creditNoteMasterAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_credit_note');?>";
                    /*Credit Note*/
                    a_link = "<?php echo site_url('Receivable/load_cn_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + para1 + '/CN';
                    break;
                case "RV": // Receipt Voucher
                    if (para2 == 'suom' || templateKeyWord == 'suom') {
                        siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_conformation_suom'); ?>";
                        paramData.push({name: 'receiptVoucherAutoId', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_receipt_voucher');?>";
                        /*Receipt Voucher*/
                        a_link = "<?php echo site_url('Receipt_voucher/load_rv_conformation_suom'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher_suom'); ?>/" + para1 + '/RV';
                        break;
                    } else if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_conformation_buyback'); ?>";
                        paramData.push({name: 'receiptVoucherAutoId', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_receipt_voucher');?>";
                        /*Receipt Voucher*/
                        a_link = "<?php echo site_url('Receipt_voucher/load_rv_conformation_buyback'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + para1 + '/RV';
                        break;
                    } else {
                        siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>";
                        paramData.push({name: 'receiptVoucherAutoId', value: para1});
                        paramData.push({name: 'approval', value: approval});
                        title = "<?php echo $this->lang->line('footer_receipt_voucher');?>";
                        /*Receipt Voucher*/
                        a_link = "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + para1 + '/RV';
                        break;
                    }
                case "RVM": // Receipt Matching
                    siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_match_conformation'); ?>";
                    paramData.push({name: 'matchID', value: para1});
                    title = "<?php echo $this->lang->line('footer_receipt_matching');?>";
                    /*Receipt Matching*/
                    break;
                case "JV": // Journal Voucher 
                    siteUrl = "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>";
                    paramData.push({name: 'JVMasterAutoId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_journal_entry');?>";
                    /*Journal Entry*/
                    a_link = "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_journal_entry'); ?>/" + para1 + '/JV';
                    break;
                case "BR": // Bank Rec
                    siteUrl = "<?php echo site_url('Bank_rec/bank_rec_book_balance'); ?>";
                    paramData.push({name: 'bankRecAutoID', value: para1});
                    paramData.push({name: 'GLAutoID', value: para2});
                    title = "<?php echo $this->lang->line('footer_bank_reconciliation');?>";
                    /*Bank Reconciliation*/
                    break;
                case "FA": // Fixed Asset
                    siteUrl = "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>";
                    paramData.push({name: 'faID', value: para1});
                    title = "<?php echo $this->lang->line('footer_fixed_asset');?>";
                    /*Fixed Asset*/
                    a_link = "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>/" + para1;
                    //de_link="<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + para1 + '/FA';
                    break;
                case "FAD": // Fixed Asset Depriciation
                    if (para2 == 'month' || templateKeyWord == 'month') {
                        siteUrl = "<?php echo site_url('AssetManagement/load_asset_dipriciation_view'); ?>";
                        paramData.push({name: 'depMasterAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_asset_monthly_depreciation');?>";
                        /*Asset Monthly Depreciation*/
                    } else {
                        siteUrl = "<?php echo site_url('AssetManagement/load_asset_dipriciation_adhoc_view'); ?>";
                        paramData.push({name: 'depMasterAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_asset_ad_hoc_depreciation');?>";
                        /*Asset Ad hoc Depreciation*/
                    }
                    break;
                case "ADSP": // Fixed Asset Disposal
                    siteUrl = "<?php echo site_url('AssetManagement/load_asset_disposal_view'); ?>";
                    paramData.push({name: 'assetdisposalMasterAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_asset_disposal');?>";
                    /*Asset Disposal*/
                    break;
                case "SD": // Salary Declaration - ( Only in salary declaration approval)
                    siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation'); ?>";
                    paramData.push({name: 'declarationMasterID', value: para1}, {name: 'isFromApproval', value: 'Y'});
                    title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                    /*Salary Declaration*/
                    break;
                case "SD-C": // Salary Declaration - (in Salary declaration master)
                    siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation'); ?>";
                    paramData.push({name: 'declarationMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                    /*Salary Declaration*/
                    break;
                case "SD-C2": // Salary Declaration - (in Salary declaration master)
                    siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation_view'); ?>";
                    paramData.push({name: 'declarationMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                    /*Salary Declaration*/
                    break;
                case "SVD-2": // Salary Declaration - (in Salary declaration master)
                    siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation_view_variable'); ?>";
                    paramData.push({name: 'declarationMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                    /*Salary Declaration*/
                    break;
                case "VD": // Variable Pay Declaration - 
                    siteUrl = "<?php echo site_url('Employee/variable_pay_approval_confirmation_view'); ?>";
                    paramData.push({name: 'masterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_variable_pay_declaration');?>";
                    break;
                case "CNT": // Contract

                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_contract');?>";
                    } else if (para2 == 'NH') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_nh'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_order');?>";
                    } else {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_contract');?>";
                    }


                    /*Contract*/
                    break;
                case "QUT": // Quotation

                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_quotation');?>";
                        /*Asset Monthly Depreciation*/
                    } else if (para2 == 'NH') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_nh'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_order');?>";
                    } else {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_quotation');?>";
                        /*Asset Ad hoc Depreciation*/
                    }

                    /*Quotation*/
                    break;
                case "SO": // Quotation


                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_order');?>";
                        /*Asset Monthly Depreciation*/
                    } else if (para2 == 'NH') {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_nh'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_order');?>";
                    } else {
                        siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                        paramData.push({name: 'contractAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_order');?>";
                        /*Asset Ad hoc Depreciation*/
                    }

                    /*Sales Order*/
                    break;
                case "SC": // Sales Commission
                    siteUrl = "<?php echo site_url('Sales/load_sc_conformation'); ?>";
                    paramData.push({name: 'salesCommisionID', value: para1});
                    title = "<?php echo $this->lang->line('footer_sales_commission');?>";
                    /*Sales Commission*/
                    a_link = "<?php echo site_url('Sales/load_sc_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_SC'); ?>/" + para1 + '/SC';
                    break;
                case "FED": // Fixed Element Declaration
                    siteUrl = "<?php echo site_url('Employee/load_fixed_elementDeclaration_approval_confirmation'); ?>";
                    paramData.push({name: 'feDeclarationMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_fixed_element_declaration');?>";
                    /*Fixed Element Declaration*/
                    break;
                case "SLR": // Sales Return

                    if (para2 == 'buy' || templateKeyWord == 'buy') {
                        siteUrl = "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>";
                        paramData.push({name: 'salesReturnAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_return');?>";
                        a_link = "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return_buyback'); ?>/" + para1 + '/SLR';
                    } else {
                        siteUrl = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>";
                        paramData.push({name: 'salesReturnAutoID', value: para1});
                        title = "<?php echo $this->lang->line('footer_sales_return');?>";
                        a_link = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>/" + para1;
                        de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return'); ?>/" + para1 + '/SLR';
                    }

                    /*Sales Return*/
                    break;
                case "PRQ": // Purchase Order -
                    siteUrl = "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>";
                    paramData.push({name: 'purchaseRequestID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_purchase_request');?>";
                    /*Purchase Request*/
                    break;
                /*** SME-2341 => Remove from document drill down function ***/
                /*case "SPN": // Salary Processing (Non-payroll)
                    siteUrl = "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>";
                    paramData.push({'name': 'hidden_payrollID', 'value': para1});
                    paramData.push({'name': 'isNonPayroll', 'value': 'Y'});
                    paramData.push({'name': 'from_approval', 'value': 'Y'});
                    paramData.push({'name': 'isForReverse', 'value': 'Y'});
                    title = "<?php echo $this->lang->line('footer_monthly_allowance');?>";
                    /!*Monthly Allowance*!/
                    break;
                case "SP": // Salary Processing
                    siteUrl = "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>";
                    paramData.push({'name': 'hidden_payrollID', 'value': para1});
                    paramData.push({'name': 'isNonPayroll', 'value': 'N'});
                    paramData.push({'name': 'from_approval', 'value': 'Y'});
                    paramData.push({'name': 'isForReverse', 'value': 'Y'});
                    title = "<?php echo $this->lang->line('footer_salary_processing');?>";
                    /!*Salary Processing*!/
                    break;*/
                case "MRN": // Purchase Order
                    siteUrl = "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>";
                    paramData.push({name: 'mrnAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_material_receipt_note');?>";
                    /*Material Receipt Note*/
                    break;
                case "CMT": // Commitment
                    siteUrl = "<?php echo site_url('OperationNgo/load_donor_commitment_confirmation'); ?>";
                    paramData.push({name: 'commitmentAutoId', value: para1});
                    a_link = "<?php echo site_url('OperationNgo/load_donor_commitment_confirmation'); ?>/" + para1;
                    title = "<?php echo $this->lang->line('footer_donor_commitment');?>";
                    /*Donor Commitment*/
                    break;
                case "DC": // collection
                    siteUrl = "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>";
                    paramData.push({name: 'collectionAutoId', value: para1});
                    a_link = "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_donor_collection'); ?>/" + para1 + '/DC';
                    title = "<?php echo $this->lang->line('footer_donor_collection');?>";
                    /*Donor Collection*/
                    break;
                case "BBM": // Buy Back Mortality 
                    siteUrl = "<?php echo site_url('buyback/load_mortality_confirmation'); ?>";
                    paramData.push({name: 'mortalityAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_mortality');?>";
                    /*Mortality*/
                    break;
                case "BBDPN": // Buy Back Dispatch Note 
                    siteUrl = "<?php echo site_url('buyback/load_dispatchNote_confirmation'); ?>";
                    paramData.push({name: 'dispatchAutoID', value: para1});
                    paramData.push({'name': 'batchid', value: para2});
                    title = "<?php echo $this->lang->line('footer_dispatch_note');?>";
                    /*Dispatch Note*/
                    break;
                case "BBCR": // Buy Back Collection 
                    siteUrl = "<?php echo site_url('Buyback/load_buyback_collection_confirmation'); ?>";
                    paramData.push({name: 'collectionautoid', value: para1});
                    title = "Buyback Collection";
                    break;
                case "BBGRN": // Buy Back Good Receipt Note 
                    siteUrl = "<?php echo site_url('buyback/load_goodReceiptNote_confirmation'); ?>";
                    paramData.push({name: 'grnAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_good_receipt_note');?>";
                    /*Good Receipt Note*/
                    break;
                case "EST": // Estimate 
                    siteUrl = "<?php echo site_url('MFQ_Estimate/fetch_estimate_print'); ?>";
                    paramData.push({name: 'estimateMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_estimate');?>";
                    /*Estimate*/
                    break;
                case "BBPV": // Buy Back Payment Voucher
                    siteUrl = "<?php echo site_url('buyback/load_paymentVoucher_confirmation'); ?>";
                    paramData.push({name: 'pvMasterAutoID', value: para1});
                    title = "";
                    /*Payment Voucher*/
                    break;
                case "PRVR": // Payment Reversal
                    siteUrl = "<?php echo site_url('PaymentReversal/load_payment_reversal_conformation'); ?>";
                    paramData.push({name: 'paymentReversalAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_payment_reversal');?>";
                    /*Payment Reversal*/
                    break;
                case "BBBC": // Buy Back Batch Closing
                    siteUrl = "<?php echo site_url('buyback/load_production_report_confirmation'); ?>";
                    paramData.push({name: 'batchMasterID', value: para1});
                    title = "<?php echo $this->lang->line('footer_production_statement');?>";
                    /*Production Statement*/
                    break;
                case "RJV": //Recurring Journal Voucher
                    siteUrl = "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>";
                    paramData.push({name: 'RJVMasterAutoId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Recurring Journal Entry";
                    /*Journal Entry*/
                    a_link = "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>/" + para1;
                    break;
                case "BBFVR": // Buy Back Farm Visit Report
                    siteUrl = "<?php echo site_url('buyback/load_farmVisitReport_confirmation'); ?>";
                    paramData.push({name: 'farmerVisitID', value: para1});
                    title = "Farm Visit Report";
                    break;
                case "MR": // Material Request
                    siteUrl = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>";
                    paramData.push({name: 'mrAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Material Request";
                    /**/
                    a_link = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>/" + para1;
                    break;
                case "CI": // Customer inquiry 
                    siteUrl = "<?php echo site_url('MFQ_CustomerInquiry/fetch_customer_inquiry_print'); ?>";
                    paramData.push({name: 'ciMasterID', value: para1});
                    title = "Customer Inquiry";
                    break;
                case "YPRP": // Yield Preparation 
                    siteUrl = "<?php echo site_url('POS_yield_preparation/yield_preparation_print'); ?>";
                    paramData.push({name: 'yieldPreparationID', value: para1});
                    title = "Yield Preparation";
                    /****/
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_yield_preparation'); ?>/" + para1 + '/YPRP';
                    break;
                case "PRP": // collection 
                    siteUrl = "<?php echo site_url('OperationNgo/load_project_proposal_confirmation'); ?>";
                    paramData.push({name: 'proposalID', value: para1});
                    title = "Donor Collection";
                    /*Prpoposal Approval*/
                    a_link = "<?php echo site_url('OperationNgo/load_project_proposal_print_pdf_approval'); ?>/" + para1;
                    break;
                case "SCNT": // Stock Counting 
                    if (para2 == 'SCNTsuom' || templateKeyWord == 'SCNTsuom') {
                        siteUrl = "<?php echo site_url('StockCounting/load_stock_counting_conformation_suom'); ?>";
                    } else {
                        siteUrl = "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>";
                    }
                    paramData.push({name: 'stockCountingAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Stock Counting";
                    /**/
                    if (para2 == 'SCNTsuom' || templateKeyWord == 'SCNTsuom') {
                        a_link = "<?php echo site_url('StockCounting/load_stock_counting_conformation_suom'); ?>/" + para1;
                    } else {
                        a_link = "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>/" + para1;
                    }
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_scnt'); ?>/" + para1 + '/SCNT';
                    break;
                case "BBDR":
                    siteUrl = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>";
                    paramData.push({name: 'returnAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Return";
                    a_link = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>/" + para1;

                    break;
                case "IOU":
                    siteUrl = "<?php echo site_url('Iou/load_iou_voucher_confirmation'); ?>";
                    paramData.push({name: 'voucherAutoID', value: para1});
                    title = "IOU Voucher";

                    break;

                case "IOUE": // IOUB Voucher
                    $(".itemMasterSubTab_footer").hide();
                    $("#TabViewActivation_view").hide();
                    siteUrl = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>";
                    paramData.push({name: 'IOUbookingmasterid', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "IOU Booking";
                    /**/
                    a_link = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Iou/fetch_double_entry_iou_booking'); ?>/" + para1 + '/IOUB';
                    break;

                case "FU": // Fuel Usage
                    siteUrl = "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>";
                    paramData.push({name: 'fuelusageID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Fuel Usage";
                    /**/
                    a_link = "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>/" + para1;
                    break;
                case "JP": 
                    siteUrl = "<?php echo site_url('Journeyplan/load_jp_view'); ?>";
                    paramData.push({name: 'journeyPlanMasterID', value: para1});
                    title = "Journey Plan";

                    a_link = "<?php echo site_url('Journeyplan/load_jp_view'); ?>/" + para1;
                    break;
                case "BDT": // Budget Transfer
                    siteUrl = "<?php echo site_url('Budget_transfer/load_budget_transfer_view'); ?>";
                    paramData.push({name: 'budgetTransferAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "Budget Transfer";
                    /*Budget Transfer*/
                    break;
                case "STJOB":
                    siteUrl = "<?php echo site_url('MFQ_Job_standard/load_standardjobcard_print'); ?>";
                    paramData.push({name: 'jobAutoID', value: para1});
                    title = "Standard Job Card";

                    a_link = "<?php echo site_url('MFQ_Job_standard/load_standardjobcard_print'); ?>/" + para1;
                    de_link = "<?php echo site_url('MFQ_Job_standard/fetch_double_entry_standardjobcard'); ?>/" + para1 + '/STJOB';
                    break;

                case "DO": 
                    siteUrl = "<?php echo site_url('Delivery_order/load_order_confirmation_view'); ?>";
                    paramData.push({name: 'orderAutoID', value: para1});
                    title = "Delivery Order";
                    load_Delivered_view('DO', para1); // Delivered view
                    break;

                case "SAR": 
                    siteUrl = "<?php echo site_url('Employee/load_salary_advance_request_view/view'); ?>";
                    paramData.push({name: 'masterID', value: para1});
                    title = "Salary Advance Request";
                    break;

                case "LEC": 
                    siteUrl = "<?php echo site_url('Employee/leave_encashment_and_salary_view/view/Y'); ?>";
                    paramData.push({name: 'masterID', value: para1});
                    title = (para2 == 1) ? "Leave Encashment" : "Leave Salary";
                    break;

                case "HDR": 
                    siteUrl = "<?php echo site_url('Employee/view_hr_letter_request/'); ?>";
                    paramData.push({name: 'masterID', value: para1});
                    title = "Document Request";
                    $('#doc-view-modal-dialog').removeAttr('style');
                    break;
                case "OPCNT": 
                    siteUrl = "<?php echo site_url('Operation/load_contract_master_view'); ?>";
                    paramData.push({name: 'contractUID', value: para1});
                    title = "Contract Master";
                    break;
                case "OPJOB": 
                    siteUrl = "<?php echo site_url('Operation/load_ticket_master_view'); ?>";
                    paramData.push({name: 'ticketidAtuto', value: para1});
                    title = "Ticket Master";
                    break;
                case "ORD-RVW": // Order Review 
                    siteUrl = "<?php echo site_url('Srm_master/load_ordereview_conformation'); ?>";
                    paramData.push({name: 'orderreviewID', value: para1});
                    title = "Order Review";
                    break;
                case "SS": // Split Salary 
                    siteUrl = "<?php echo site_url('Employee/load_splitSalary_conformation'); ?>";
                    paramData.push({name: 'splitSalaryMasterID', value: para1});
                    title = "Split Salary";
                    break;

                case "STB": // Stock Bulk Transfer 
                    siteUrl = "<?php echo site_url('Inventory/load_bulk_transfer_conformation'); ?>";
                    paramData.push({name: 'stockTransferAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_stock_transfer');?>";
                    /*Stock Transfer*/
                    a_link = "<?php echo site_url('Inventory/load_bulk_transfer_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_bulk_transfer'); ?>/" + para1 + '/STB';
                    break;
                case "RRVR": // Receipt reversal
                    siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>";
                    paramData.push({name: 'payVoucherAutoId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    paramData.push({name: 'doc_type', value: 'RRVR'});
                    title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                    /*Payment Voucher*/
                    a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                    load_itemMasterSub('PV', para1); // item master sub
                    break;

                case "MDN": // Manufacturing Delivery Note
                    siteUrl = "<?php echo site_url('MFQ_DeliveryNote/load_deliveryNote_confirmation'); ?>";
                    paramData.push({name: 'deliverNoteID', value: para1});
                    title = "<?php echo $this->lang->line('footer_');?>";
                    break;

                case "MCINV": // Manufacturing Customer Invoice
                    siteUrl = "<?php echo site_url('MFQ_CustomerInvoice/fetch_customer_invoice_print'); ?>";
                    paramData.push({name: 'invoiceAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_customer_invoice');?>";
                    break;
                
                case "JOB": // Manufacturing Job View
                    siteUrl = "<?php echo site_url('MFQ_Estimate/fetch_job_order_view'); ?>";
                    paramData.push({name: 'estimateMasterID', value: para1});
                    paramData.push({name: 'workProcessID', value: 2});
                    paramData.push({name: 'html', value: true});
                    title = "<?php echo $this->lang->line('footer_customer_invoice');?>";
                    break;


                default:
                    notification('<?php echo $this->lang->line('footer_document_id_is_not_set');?> .', 'w');
                    /*Document ID is not set*/
                    return false;
            }
            paramData.push({name: 'html', value: true});
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: paramData,
                url: siteUrl,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    $('#documentPageViewTitle').html(title);
                    $('#loaddocumentPageView').html(data);
                    $('#documentPageView').modal('show');
                    $("#a_link").attr("href", a_link);
                    $("#de_link").attr("href", de_link);
                    $('.review').removeClass('hide');
                    stopLoad();
                    if (documentID = 'SP') {
                        $('#paysheet-tb').tableHeadFixer({
                            head: true,
                            foot: true,
                            left: 0,
                            right: 0,
                            'z-index': 0
                        });
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }, 500);

    }

    function load_version_confirmation_po(po){
        //$('#documentPageView').modal('hide');
        documentPageView_modal_version('PO',po.value);
    }

    function requestPageView_model(documentID, para1, para2, approval = 0) {


        var siteUrl;
        var paramData = new Array();
        var title = '';
        var a_link;
        var de_link;//timeout funtion to set time for assigning template parameter

        switch (documentID) {

            case "GRV":
            case "PV":
                $("#deliveredTab_footer_div").html('');
                $(".dodelivered_footer").hide();
                $("#itemMasterSubTab_footer_div").html('');
                $(".itemMasterSubTab_footer").show();
                break;
            case "DO":
                $("#deliveredTab_footer_div").html('');
                $(".dodelivered_footer").show();
                $("#itemMasterSubTab_footer_div").html('');
                $(".itemMasterSubTab_footer").hide();


                break;

            default:
                $("#deliveredTab_footer_div").html('');
                $(".dodelivered_footer").hide();
                $("#itemMasterSubTab_footer_div").html('');
                $(".itemMasterSubTab_footer").hide();

        }

        switch (documentID) {
            case "BT":  // Bank Transfer -
                siteUrl = "<?php echo site_url('Bank_rec/bank_transfer_view'); ?>";
                paramData.push({name: 'bankTransferAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_bank_transfer');?>";
                /*Bank Transfer*/
                break;
            case "PO": // Purchase Order -

                if (para2 == 'buy' || templateKeyWord == 'buy') {
                    siteUrl = "<?php echo site_url('Procurement/load_purchase_order_conformation_buyback'); ?>";
                    paramData.push({name: 'purchaseOrderID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('common_purchase_order');?>";
                    /*Purchase Order*/
                    break;
                } else {
                    siteUrl = "<?php echo site_url('Procurement/load_purchase_order_conformation'); ?>";
                    paramData.push({name: 'purchaseOrderID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('common_purchase_order');?>";
                    /*Purchase Order*/
                    break;
                }


            case "EC": // Expense Claim 
                siteUrl = "<?php echo site_url('ExpenseClaim/load_expense_claim_conformation'); ?>";
                paramData.push({name: 'expenseClaimMasterAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('common_expense_claim');?>";
                /*Expense Claim*/
                break;
            case "GRV": // Good Receipt Voucher -
                if (para2 == 'buy' || templateKeyWord == 'buy') {
                    siteUrl = "<?php echo site_url('Grv/load_grv_conformation_buyback'); ?>";
                    paramData.push({name: 'grvAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_goods_received_voucher');?>";
                    /*Goods Received Voucher*/
                    a_link = "<?php echo site_url('Grv/load_grv_conformation_buyback'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + para1 + '/GRV';
                    load_itemMasterSub('GRV', para1); // item master sub - Sahfri
                    break;
                } else {
                    siteUrl = "<?php echo site_url('Grv/load_grv_conformation'); ?>";
                    paramData.push({name: 'grvAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_goods_received_voucher');?>";
                    /*Goods Received Voucher*/
                    a_link = "<?php echo site_url('Grv/load_grv_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_grv'); ?>/" + para1 + '/GRV';
                    load_itemMasterSub('GRV', para1); // item master sub - Sahfri
                    break;
                }


            case "SR": // Purchase Return -
                siteUrl = "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>";
                paramData.push({name: 'stockReturnAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_purchase_return');?>";
                /*Purchase Return*/
                a_link = "<?php echo site_url('Inventory/load_stock_return_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_stock_return'); ?>/" + para1 + '/SR';
                break;
            case "MI": // Material Issue -
                if (para2 == 'mc' || templateKeyWord == 'mc') {
                    siteUrl = "<?php echo site_url('Inventory/load_material_issue_conformation_mc'); ?>";
                    paramData.push({name: 'itemIssueAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_material_issue');?>";
                    /*Material Issue*/
                    a_link = "<?php echo site_url('Inventory/load_material_issue_conformation_mc'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + para1 + '/MI';
                    break;
                } else {
                    siteUrl = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>";
                    paramData.push({name: 'itemIssueAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_material_issue');?>";
                    /*Material Issue*/
                    a_link = "<?php echo site_url('Inventory/load_material_issue_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_material_issue'); ?>/" + para1 + '/MI';
                    break;
                }

            case "ST": // Stock Transfer -

                if (para2 == 'buy' || templateKeyWord == 'buy') {
                    siteUrl = "<?php echo site_url('Inventory/load_stock_transfer_conformation_buyback'); ?>";
                    paramData.push({name: 'stockTransferAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_stock_transfer');?>";
                    /*Stock Transfer*/
                    a_link = "<?php echo site_url('Inventory/load_stock_transfer_conformation_buyback'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + para1 + '/ST';
                    break;
                } else {
                    siteUrl = "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>";
                    paramData.push({name: 'stockTransferAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_stock_transfer');?>";
                    /*Stock Transfer*/
                    a_link = "<?php echo site_url('Inventory/load_stock_transfer_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_stock_transfer'); ?>/" + para1 + '/ST';
                    break;
                }

            case "SA": // Stock Adjustment -Document ID is not set .
                if (para2 == 'buy' || templateKeyWord == 'buy') {
                    siteUrl = "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>";
                    paramData.push({name: 'stockAdjustmentAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_stock_adjustment');?>";
                    /*Stock Adjustment*/
                    a_link = "<?php echo site_url('Inventory/load_stock_adjustment_conformation_buyback'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + para1 + '/SA';
                    break;
                } else {
                    siteUrl = "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>";
                    paramData.push({name: 'stockAdjustmentAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_stock_adjustment');?>";
                    /*Stock Adjustment*/
                    a_link = "<?php echo site_url('Inventory/load_stock_adjustment_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sa'); ?>/" + para1 + '/SA';
                    break;
                }

            case "BSI": // Supplier Invoices -
                siteUrl = "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>";
                paramData.push({name: 'InvoiceAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_supplier_invoices');?>";
                /*Supplier Invoices*/
                a_link = "<?php echo site_url('Payable/load_supplier_invoice_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_supplier_invoices'); ?>/" + para1 + '/BSI';
                break;
            case "DN": // Debit Note -
                siteUrl = "<?php echo site_url('Payable/load_dn_conformation'); ?>";
                paramData.push({name: 'debitNoteMasterAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_debit_note');?>";
                /*Debit Note*/
                a_link = "<?php echo site_url('Payable/load_dn_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_debit_note'); ?>/" + para1 + '/DN';
                break;
            case "PV": // Payment Voucher -
                if (para2 == 'SUOM' || templateKeyWord == 'SUOM') {
                    siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation_suom'); ?>";
                } else if (para2 == 'buy' || templateKeyWord == 'buy') {
                    siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation_buyback'); ?>";
                } else {
                    siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>";
                }
                paramData.push({name: 'payVoucherAutoId', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                /*Payment Voucher*/
                if (para2 == 'SUOM' || templateKeyWord == 'SUOM') {
                    a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation_suom'); ?>/" + para1;
                } else if (para2 == 'buy' || templateKeyWord == 'buy') {
                    siteUrl = "<?php echo site_url('Payment_voucher/load_pv_conformation_buyback'); ?>";
                } else {
                    a_link = "<?php echo site_url('Payment_voucher/load_pv_conformation'); ?>/" + para1;
                }
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                load_itemMasterSub('PV', para1); // item master sub - Sahfri
                break;
            case "PVM": // Payment Match -
                siteUrl = "<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>";
                paramData.push({name: 'matchID', value: para1});
                title = "<?php echo $this->lang->line('footer_payment_voucher');?>";
                /*Payment Voucher*/
                a_link = "<?php echo site_url('Payment_voucher/load_pv_match_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_payment_voucher'); ?>/" + para1 + '/PV';
                break;
            case "CINV": // Invoice -
                if (para2 == 'insurance' || templateKeyWord == 'insurance') {
                    siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_invoicetype'); ?>";
                    paramData.push({name: 'invoiceAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_invoice');?>";
                    /*Invoice*/
                    a_link = "<?php echo site_url('invoices/load_invoices_conformation_invoicetype'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                    break;
                } else if (para2 == 'margin' || templateKeyWord == 'margin') {
                    siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_margin'); ?>";
                    paramData.push({name: 'invoiceAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_invoice');?>";
                    /*Invoice*/
                    a_link = "<?php echo site_url('invoices/load_invoices_conformation_margin'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                    break;
                } else if (para2 == 'SUOM' || templateKeyWord == 'SUOM') {
                    siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_suom'); ?>";
                    paramData.push({name: 'invoiceAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_invoice');?>";
                    /*Invoice*/
                    a_link = "<?php echo site_url('invoices/load_invoices_conformation_suom'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                    break;
                } else if (para2 == 'Commission') {
                    siteUrl = "<?php echo site_url('invoices/load_invoices_conformation_cs'); ?>";
                    paramData.push({name: 'invoiceAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_invoice');?>";
                    /*Invoice*/
                    a_link = "<?php echo site_url('invoices/load_invoices_conformation_cs'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                    break;
                } else {
                    siteUrl = "<?php echo site_url('invoices/load_invoices_conformation'); ?>";
                    paramData.push({name: 'invoiceAutoID', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_invoice');?>";
                    /*Invoice*/
                    a_link = "<?php echo site_url('invoices/load_invoices_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice'); ?>/" + para1 + '/CINV';
                    break;
                }


            case "HCINV": // Invoice - 
                siteUrl = "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>";
                paramData.push({name: 'invoiceAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_invoice');?>";
                /*Invoice*/
                a_link = "<?php echo site_url('InvoicesPercentage/load_invoices_conformation_buyback'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_customer_invoice_buyback'); ?>/" + para1 + '/HCINV';
                break;
            case "CN": // Credit Note - 
                siteUrl = "<?php echo site_url('Receivable/load_cn_conformation'); ?>";
                paramData.push({name: 'creditNoteMasterAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_credit_note');?>";
                /*Credit Note*/
                a_link = "<?php echo site_url('Receivable/load_cn_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + para1 + '/CN';
                break;
            case "RV": // Receipt Voucher - 
                if (para2 == 'suom' || templateKeyWord == 'suom') {
                    siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_conformation_suom'); ?>";
                    paramData.push({name: 'receiptVoucherAutoId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_receipt_voucher');?>";
                    /*Receipt Voucher*/
                    a_link = "<?php echo site_url('Receipt_voucher/load_rv_conformation_suom'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher_suom'); ?>/" + para1 + '/RV';
                    break;
                } else {
                    siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>";
                    paramData.push({name: 'receiptVoucherAutoId', value: para1});
                    paramData.push({name: 'approval', value: approval});
                    title = "<?php echo $this->lang->line('footer_receipt_voucher');?>";
                    /*Receipt Voucher*/
                    a_link = "<?php echo site_url('Receipt_voucher/load_rv_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_receipt_voucher'); ?>/" + para1 + '/RV';
                    break;
                }
            case "RVM": // Receipt Matching
                siteUrl = "<?php echo site_url('Receipt_voucher/load_rv_match_conformation'); ?>";
                paramData.push({name: 'matchID', value: para1});
                title = "<?php echo $this->lang->line('footer_receipt_matching');?>";
                /*Receipt Matching*/
                break;
            case "JV": // Journal Voucher - 
                siteUrl = "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>";
                paramData.push({name: 'JVMasterAutoId', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_journal_entry');?>";
                /*Journal Entry*/
                a_link = "<?php echo site_url('Journal_entry/journal_entry_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_journal_entry'); ?>/" + para1 + '/JV';
                break;
            case "BR": // Bank Rec - 
                siteUrl = "<?php echo site_url('Bank_rec/bank_rec_book_balance'); ?>";
                paramData.push({name: 'bankRecAutoID', value: para1});
                paramData.push({name: 'GLAutoID', value: para2});
                title = "<?php echo $this->lang->line('footer_bank_reconciliation');?>";
                /*Bank Reconciliation*/
                break;
            case "FA": // Fixed Asset - 
                siteUrl = "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>";
                paramData.push({name: 'faID', value: para1});
                title = "<?php echo $this->lang->line('footer_fixed_asset');?>";
                /*Fixed Asset*/
                a_link = "<?php echo site_url('AssetManagement/load_asset_conformation'); ?>/" + para1;
                //de_link="<?php echo site_url('Double_entry/fetch_double_entry_credit_note'); ?>/" + para1 + '/FA';
                break;
            case "FAD": // Fixed Asset Depriciation- 
                if (para2 == 'month' || templateKeyWord == 'month') {
                    siteUrl = "<?php echo site_url('AssetManagement/load_asset_dipriciation_view'); ?>";
                    paramData.push({name: 'depMasterAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_asset_monthly_depreciation');?>";
                    /*Asset Monthly Depreciation*/
                } else {
                    siteUrl = "<?php echo site_url('AssetManagement/load_asset_dipriciation_adhoc_view'); ?>";
                    paramData.push({name: 'depMasterAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_asset_ad_hoc_depreciation');?>";
                    /*Asset Ad hoc Depreciation*/
                }
                break;
            case "ADSP": // Fixed Asset Disposal- 
                siteUrl = "<?php echo site_url('AssetManagement/load_asset_disposal_view'); ?>";
                paramData.push({name: 'assetdisposalMasterAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_asset_disposal');?>";
                /*Asset Disposal*/
                break;
            case "SD": // Salary Declaration - ( Only in salary declaration approval)
                siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation'); ?>";
                paramData.push({name: 'declarationMasterID', value: para1}, {name: 'isFromApproval', value: 'Y'});
                title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                /*Salary Declaration*/
                break;
            case "SD-C": // Salary Declaration - (in Salary declaration master)
                siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation'); ?>";
                paramData.push({name: 'declarationMasterID', value: para1});
                title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                /*Salary Declaration*/
                break;
            case "SD-C2": // Salary Declaration - (in Salary declaration master)
                siteUrl = "<?php echo site_url('Employee/load_salary_approval_confirmation_view'); ?>";
                paramData.push({name: 'declarationMasterID', value: para1});
                title = "<?php echo $this->lang->line('footer_salary_declaration');?>";
                /*Salary Declaration*/
                break;
            case "VD": // Variable Pay Declaration
                siteUrl = "<?php echo site_url('Employee/variable_pay_approval_confirmation_view'); ?>";
                paramData.push({name: 'masterID', value: para1});
                title = "<?php echo $this->lang->line('footer_variable_pay_declaration');?>";
                break;
            case "CNT": // Contract

                if (para2 == 'buy' || templateKeyWord == 'buy') {
                    siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>";
                    paramData.push({name: 'contractAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_contract');?>";
                } else {
                    siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                    paramData.push({name: 'contractAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_contract');?>";
                }


                /*Contract*/
                break;
            case "QUT": // Quotation

                if (para2 == 'buy' || templateKeyWord == 'buy') {
                    siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>";
                    paramData.push({name: 'contractAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_quotation');?>";
                    /*Asset Monthly Depreciation*/
                } else {
                    siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                    paramData.push({name: 'contractAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_quotation');?>";
                    /*Asset Ad hoc Depreciation*/
                }

                /*Quotation*/
                break;
            case "SO": // Quotation


                if (para2 == 'buy' || templateKeyWord == 'buy') {
                    siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation_buyback'); ?>";
                    paramData.push({name: 'contractAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_sales_order');?>";
                    /*Asset Monthly Depreciation*/
                } else {
                    siteUrl = "<?php echo site_url('Quotation_contract/load_contract_conformation'); ?>";
                    paramData.push({name: 'contractAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_sales_order');?>";
                    /*Asset Ad hoc Depreciation*/
                }

                /*Sales Order*/
                break;
            case "SC": // Sales Commission
                siteUrl = "<?php echo site_url('Sales/load_sc_conformation'); ?>";
                paramData.push({name: 'salesCommisionID', value: para1});
                title = "<?php echo $this->lang->line('footer_sales_commission');?>";
                /*Sales Commission*/
                a_link = "<?php echo site_url('Sales/load_sc_conformation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_SC'); ?>/" + para1 + '/SC';
                break;
            case "FED": // Fixed Element Declaration 
                siteUrl = "<?php echo site_url('Employee/load_fixed_elementDeclaration_approval_confirmation'); ?>";
                paramData.push({name: 'feDeclarationMasterID', value: para1});
                title = "<?php echo $this->lang->line('footer_fixed_element_declaration');?>";
                /*Fixed Element Declaration*/
                break;
            case "SLR": // Sales Return 

                if (para2 == 'buy' || templateKeyWord == 'buy') {
                    siteUrl = "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>";
                    paramData.push({name: 'salesReturnAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_sales_return');?>";
                    a_link = "<?php echo site_url('Inventory/load_sales_return_conformation_buyback'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return_buyback'); ?>/" + para1 + '/SLR';
                } else {
                    siteUrl = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>";
                    paramData.push({name: 'salesReturnAutoID', value: para1});
                    title = "<?php echo $this->lang->line('footer_sales_return');?>";
                    a_link = "<?php echo site_url('Inventory/load_sales_return_conformation'); ?>/" + para1;
                    de_link = "<?php echo site_url('Double_entry/fetch_double_entry_sales_return'); ?>/" + para1 + '/SLR';
                }

                /*Sales Return*/
                break;
            case "PRQ": // Purchase Order 
                siteUrl = "<?php echo site_url('PurchaseRequest/load_purchase_request_conformation'); ?>";
                paramData.push({name: 'purchaseRequestID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_purchase_request');?>";
                /*Purchase Request*/
                break;
            /*** SME-2341 => Remove from document drill down function ***/
            /*case "SPN": // Salary Processing (Non-payroll)
                siteUrl = "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>";
                paramData.push({'name': 'hidden_payrollID', 'value': para1});
                paramData.push({'name': 'isNonPayroll', 'value': 'Y'});
                paramData.push({'name': 'from_approval', 'value': 'Y'});
                paramData.push({'name': 'isForReverse', 'value': 'Y'});
                title = "<?php echo $this->lang->line('footer_monthly_allowance');?>";
                /!*Monthly Allowance*!/
                break;
            case "SP": // Salary Processing 
                siteUrl = "<?php echo site_url('Template_paysheet/templateDetails_view'); ?>";
                paramData.push({'name': 'hidden_payrollID', 'value': para1});
                paramData.push({'name': 'isNonPayroll', 'value': 'N'});
                paramData.push({'name': 'from_approval', 'value': 'Y'});
                paramData.push({'name': 'isForReverse', 'value': 'Y'});
                title = "<?php echo $this->lang->line('footer_salary_processing');?>";
                /!*Salary Processing*!/
                break;*/
            case "MRN": // Purchase Order 
                siteUrl = "<?php echo site_url('MaterialReceiptNote/load_material_receipt_conformation'); ?>";
                paramData.push({name: 'mrnAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_material_receipt_note');?>";
                /*Material Receipt Note*/
                break;
            case "CMT": // Commitment 
                siteUrl = "<?php echo site_url('OperationNgo/load_donor_commitment_confirmation'); ?>";
                paramData.push({name: 'commitmentAutoId', value: para1});
                a_link = "<?php echo site_url('OperationNgo/load_donor_commitment_confirmation'); ?>/" + para1;
                title = "<?php echo $this->lang->line('footer_donor_commitment');?>";
                /*Donor Commitment*/
                break;
            case "DC": // collection 
                siteUrl = "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>";
                paramData.push({name: 'collectionAutoId', value: para1});
                a_link = "<?php echo site_url('OperationNgo/load_donor_collection_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_donor_collection'); ?>/" + para1 + '/DC';
                title = "<?php echo $this->lang->line('footer_donor_collection');?>";
                /*Donor Collection*/
                break;
            case "BBM": // Buy Back Mortality 
                siteUrl = "<?php echo site_url('buyback/load_mortality_confirmation'); ?>";
                paramData.push({name: 'mortalityAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_mortality');?>";
                /*Mortality*/
                break;
            case "BBDPN": // Buy Back Dispatch Note 
                siteUrl = "<?php echo site_url('buyback/load_dispatchNote_confirmation'); ?>";
                paramData.push({name: 'dispatchAutoID', value: para1});
                paramData.push({'name': 'batchid', value: para2});
                title = "<?php echo $this->lang->line('footer_dispatch_note');?>";
                /*Dispatch Note*/
                break;
            case "BBCR": // Buy Back Collection  
                siteUrl = "<?php echo site_url('Buyback/load_buyback_collection_confirmation'); ?>";
                paramData.push({name: 'collectionautoid', value: para1});
                title = "Buyback Collection";
                break;
            case "BBGRN": // Buy Back Good Receipt Note 
                siteUrl = "<?php echo site_url('buyback/load_goodReceiptNote_confirmation'); ?>";
                paramData.push({name: 'grnAutoID', value: para1});
                title = "<?php echo $this->lang->line('footer_good_receipt_note');?>";
                /*Good Receipt Note*/
                break;
            case "EST": // Estimate 
                siteUrl = "<?php echo site_url('MFQ_Estimate/fetch_estimate_print'); ?>";
                paramData.push({name: 'estimateMasterID', value: para1});
                title = "<?php echo $this->lang->line('footer_estimate');?>";
                /*Estimate*/
                break;
            case "BBPV": // Buy Back Payment Voucher 
                siteUrl = "<?php echo site_url('buyback/load_paymentVoucher_confirmation'); ?>";
                paramData.push({name: 'pvMasterAutoID', value: para1});
                title = "";
                /*Payment Voucher*/
                break;
            case "PRVR": // Payment Reversal 
                siteUrl = "<?php echo site_url('PaymentReversal/load_payment_reversal_conformation'); ?>";
                paramData.push({name: 'paymentReversalAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "<?php echo $this->lang->line('footer_payment_reversal');?>";
                /*Payment Reversal*/
                break;
            case "BBBC": // Buy Back Batch Closing 
                siteUrl = "<?php echo site_url('buyback/load_production_report_confirmation'); ?>";
                paramData.push({name: 'batchMasterID', value: para1});
                title = "<?php echo $this->lang->line('footer_production_statement');?>";
                /*Production Statement*/
                break;
            case "RJV": //Recurring Journal Voucher 
                siteUrl = "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>";
                paramData.push({name: 'RJVMasterAutoId', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Recurring Journal Entry";
                /*Journal Entry*/
                a_link = "<?php echo site_url('Recurring_je/recurring_journal_entry_conformation'); ?>/" + para1;
                break;
            case "BBFVR": // Buy Back Farm Visit Report 
                siteUrl = "<?php echo site_url('buyback/load_farmVisitReport_confirmation'); ?>";
                paramData.push({name: 'farmerVisitID', value: para1});
                title = "Farm Visit Report";
                break;
            case "MR": // Material Request 
                siteUrl = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>";
                paramData.push({name: 'mrAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Material Request";
                /**/
                a_link = "<?php echo site_url('Inventory/load_material_request_conformation'); ?>/" + para1;
                break;
            case "CI": // Customer inquiry 
                siteUrl = "<?php echo site_url('MFQ_CustomerInquiry/fetch_customer_inquiry_print'); ?>";
                paramData.push({name: 'ciMasterID', value: para1});
                title = "Customer Inquiry";
                break;
            case "YPRP": // Yield Preparation
                siteUrl = "<?php echo site_url('POS_yield_preparation/yield_preparation_print'); ?>";
                paramData.push({name: 'yieldPreparationID', value: para1});
                title = "Yield Preparation";
                /****/
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_yield_preparation'); ?>/" + para1 + '/YPRP';
                break;
            case "PRP": // collection 
                siteUrl = "<?php echo site_url('OperationNgo/load_project_proposal_confirmation'); ?>";
                paramData.push({name: 'proposalID', value: para1});
                title = "Donor Collection";
                /*Prpoposal Approval*/
                a_link = "<?php echo site_url('OperationNgo/load_project_proposal_print_pdf_approval'); ?>/" + para1;
                break;
            case "SCNT": // Stock Counting 
                if (para2 == 'SCNTsuom' || templateKeyWord == 'SCNTsuom') {
                    siteUrl = "<?php echo site_url('StockCounting/load_stock_counting_conformation_suom'); ?>";
                } else {
                    siteUrl = "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>";
                }
                paramData.push({name: 'stockCountingAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Stock Counting";
                /**/
                if (para2 == 'SCNTsuom' || templateKeyWord == 'SCNTsuom') {
                    a_link = "<?php echo site_url('StockCounting/load_stock_counting_conformation_suom'); ?>/" + para1;
                } else {
                    a_link = "<?php echo site_url('StockCounting/load_stock_counting_conformation'); ?>/" + para1;
                }
                de_link = "<?php echo site_url('Double_entry/fetch_double_entry_scnt'); ?>/" + para1 + '/SCNT';
                break;
            case "BBDR": 
                siteUrl = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>";
                paramData.push({name: 'returnAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Return";
                a_link = "<?php echo site_url('Buyback/load_buyback_return_conformation'); ?>/" + para1;

                break;
            case "IOU": 
                siteUrl = "<?php echo site_url('Iou/load_iou_voucher_confirmation'); ?>";
                paramData.push({name: 'voucherAutoID', value: para1});
                title = "IOU Voucher";

                break;

            case "IOUE": // IOUB Voucher
                $(".itemMasterSubTab_footer").hide();
                $("#TabViewActivation_view").hide();
                siteUrl = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>";
                paramData.push({name: 'IOUbookingmasterid', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "IOU Booking";
                /**/
                a_link = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Iou/fetch_double_entry_iou_booking'); ?>/" + para1 + '/IOUB';
                break;

            case "FU": // Fuel Usage
                siteUrl = "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>";
                paramData.push({name: 'fuelusageID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Fuel Usage";
                /**/
                a_link = "<?php echo site_url('Fleet/load_fleet_fuel_comfirmation'); ?>/" + para1;
                break;
            case "JP": 
                siteUrl = "<?php echo site_url('Journeyplan/load_jp_view'); ?>";
                paramData.push({name: 'journeyPlanMasterID', value: para1});
                title = "Journey Plan";

                a_link = "<?php echo site_url('Journeyplan/load_jp_view'); ?>/" + para1;
                break;
            case "BDT": // Budget Transfer
                siteUrl = "<?php echo site_url('Budget_transfer/load_budget_transfer_view'); ?>";
                paramData.push({name: 'budgetTransferAutoID', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "Budget Transfer";
                /*Budget Transfer*/
                break;
            case "STJOB": 
                siteUrl = "<?php echo site_url('MFQ_Job_standard/load_standardjobcard_print'); ?>";
                paramData.push({name: 'jobAutoID', value: para1});
                title = "Standard Job Card";

                a_link = "<?php echo site_url('MFQ_Job_standard/load_standardjobcard_print'); ?>/" + para1;
                de_link = "<?php echo site_url('MFQ_Job_standard/fetch_double_entry_standardjobcard'); ?>/" + para1 + '/STJOB';
                break;

            case "DO": 
                siteUrl = "<?php echo site_url('Delivery_order/load_order_confirmation_view'); ?>";
                paramData.push({name: 'orderAutoID', value: para1});
                title = "Delivery Order";
                load_Delivered_view('DO', para1); // Delivered view
                break;

            case "SAR": 
                siteUrl = "<?php echo site_url('Employee/load_salary_advance_request_view/view'); ?>";
                paramData.push({name: 'masterID', value: para1});
                title = "Salary Advance Request";
                break;

            case "LEC": 
                siteUrl = "<?php echo site_url('Employee/leave_encashment_and_salary_view/view/Y'); ?>";
                paramData.push({name: 'masterID', value: para1});
                title = (para2 == 1) ? "Leave Encashment" : "Leave Salary";
                break;

            case "HDR": 
                siteUrl = "<?php echo site_url('Employee/view_hr_letter_request/'); ?>";
                paramData.push({name: 'masterID', value: para1});
                title = "Document Request";
                $('#doc-view-modal-dialog').removeAttr('style');
                break;
            case "OPCNT": 
                siteUrl = "<?php echo site_url('Operation/load_contract_master_view'); ?>";
                paramData.push({name: 'contractUID', value: para1});
                title = "Contract Master";
                break;

            default:
                notification('<?php echo $this->lang->line('footer_document_id_is_not_set');?> .', 'w');
                /*Document ID is not set*/
                return false;
        }

        paramData = JSON.stringify(paramData);
        window.localStorage.setItem('drill-down-document-id', documentID);
        window.localStorage.setItem('drill-down-site-url', siteUrl);
        window.localStorage.setItem('drill-down-parameter-data', paramData);
        window.localStorage.setItem('drill-down-page-title', title);
        window.localStorage.setItem('drill-down-master-id', para1);


        window.open("<?php echo site_url('Access_menu/fetch_document_drill_down') ?>" + '/');
    }

    function load_itemMasterSub(receivedDocumentID, grvAutoID) {

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
                $("#itemMasterSubTab_footer_div").html('<div  class="text-center" style="margin: 10px 0px;"><i class="fa fa-refresh fa-spin"></i></div>');
            },
            success: function (data) {
                $("#itemMasterSubTab_footer_div").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#itemMasterSubTab_footer_div").html('<br>Message:<br/> ' + errorThrown);
            }
        });
    }

    function load_Delivered_view(DocumentID, DOAutoID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {
                DocumentID: DocumentID,
                DOAutoID: DOAutoID
            },
            url: "<?php echo site_url('Delivery_order/load_order_confirmation_view_delivered'); ?>",
            beforeSend: function () {
                $("#deliveredTab_footer_div").html('<div  class="text-center" style="margin: 10px 0px;"><i class="fa fa-refresh fa-spin"></i></div>');
            },
            success: function (data) {
                $("#deliveredTab_footer_div").html(data);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("#deliveredTab_footer_div").html('<br>Message:<br/> ' + errorThrown);
            }
        });
    }

    function change_fetchcompany(companyID, reload) {
        if (localStorage.getItem('tab_count') !== '0') {
            swal({
                title: "Warning",
                text: "You cannot switch companies while multiple tabs are open. To work with multiple companies simultaneously, please use incognito mode for each company",
                type: "warning",
                showCancelButton: false,
                showConfirmButton: false,
                closeOnConfirm: false,
                closeOnCancel: false,
                showCloseButton: true,
            });
            return;
        }
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>", /*Are you sure?*/
                text: "<?php echo $this->lang->line('footer_you_want_to_load_this_company');?> ! ", /*You want to load this company*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>!", /*Yes*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>",
                closeOnCancel: false

            },
            function (isConfirm) {
                if (isConfirm) {
                    var result = companyID.split('-');
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {companyType: result[1], companyID: result[0], isGroupUser: result[2], eid: result[3]},
                        url: "<?php echo site_url('Access_menu/load_navigation'); ?>",
                        success: function (data) {
                            location.reload();
                            stopLoad();
                        }, error: function (err_data) {
                            alert(err_data.responseText);
                            location.reload();
                        }
                    });
                } else {
                    location.reload();
                    swal("Canceled", "", "error");
                    e.preventDefault();
                }
            });
    }

    function fetchcompany(companyID, reload) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {companyID: companyID},
            url: "<?php echo site_url('Access_menu/load_navigation_html'); ?>",
            beforeSend: function () {
                /*startLoad();*/
                $('#ajax_body_container').html('');
            },
            success: function (data) {
                $('.main-sidebar').html(data);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function load_version_confirmation_PR(ev){

        var id =  $(ev).val();
        
        if(id != ''){
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('PurchaseRequest/load_purchase_request_version'); ?>",
                data: {'id': $(ev).val()},

                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#versionSection').empty().html(data);
                },
                error: function (data) {
                    stopLoad();
                    swal("Cancelled", "No File Selected :)", "error");
                }
            });
        }

    }

    function attachment_View_modal(documentID, documentSystemCode) {
        $('#loadPageViewAttachment').attr('class', 'col-sm-8');
        if (documentSystemCode) {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url("Attachment/fetch_attachments"); ?>',
                dataType: 'json',
                data: {'documentID': documentID, 'documentSystemCode': documentSystemCode, 'view_modal': 1},
                success: function (data) {

                    if (documentID == 'HDR') {
                        setTimeout(function () {
                            $('#loadPageViewAttachment').attr('class', 'col-sm-12');
                        }, 300);
                    }

                    $('#attachment_modal_label').html('<span aria-hidden="true" class="glyphicon glyphicon-hand-right color"></span> &nbsp;' + document_name + "'s Attachments");
                    $('#View_attachment_modal_body').empty();
                    $('#View_attachment_modal_body').append('' + data + '');
                    //$("#View_attachment_modal_body").modal({backdrop: "static", keyboard: true});
                    if (documentID == 'IOUE') {
                        $('#TabViewActivation_attachment').attr('class', 'hide');
                        $('#View_attachment_modal_body').empty();
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#ajax_nav_container').html(xhr.responseText);
                }
            });
        }
    }

    var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><style>thead th \{font-size: 12px !important;text-align: center;\}thead tr \{ background: #dedede; /* Old browsers */ \} </style></head><body>{headerDiv}<table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name, headerDiv) {
            if (!table.nodeType) table = document.getElementById(table)
            headerDiv = (headerDiv != undefined) ? headerDiv : ''; 
            var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML, headerDiv: headerDiv}
            var blob = new Blob([format(template, ctx)]);
            var blobURL = window.URL.createObjectURL(blob);
            return blobURL;
        }
    })();

    function delete_attachments(id, fileName) {

        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>?", /*Are you sure*/
                text: "<?php echo $this->lang->line('common_you_want_to_delete');?>!", /*You want to Delete*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>!"/*Yes*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'attachmentID': id, 'myFileName': fileName},
                    url: "<?php echo site_url('Attachment/delete_attachments_AWS_s3'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', '<?php echo $this->lang->line('common_deleted_successfully');?>');
                            /*Deleted Successfully*/
                            $('#' + id).hide();
                        } else {
                            myAlert('e', '<?php echo $this->lang->line('footer_deletion_failed');?>');
                            /*Deletion Failed*/
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }

    function openChangePassowrdModel() {
        $('#passwordresetModal').modal('show');
    }

    function validatepwsStrengthfotr() {
        var passwordComplexityExist = '<?php echo $passwordComplexityExist; ?>';
        let passwordComplexity = '<?php echo json_encode($passwordComplexity); ?>';

        if (passwordComplexityExist == 1) {

            var word = $('#newPassword').val();
            var Score = 0;
            var conditions = 0;
            var iscapital = 0;
            var isspecial = 0;
            var lengt = word.length;
            var Capital = word.match(/[A-Z]/);
            var format = /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
            if (format.test(word) == true) {
                isspecial = 1
            } else {
                isspecial = 0
            }
            if (jQuery.isEmptyObject(Capital)) {
                iscapital = 0
            } else {
                iscapital = 1
            }
            $('#messagelogin').html('<label class="label label-danger">Weak</label>');
            $('#progrssbarlogin').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
            $('#passwordsavebtn').attr('disabled', true);
            var minimumLength = passwordComplexity?.minimumLength || 6;
            if (minimumLength <= lengt) {
                conditions = conditions + 1;
                Score = Score + 1;
                $('#messagelogin').html(' ');
                var isCapitalLettersMandatory = passwordComplexity?.isCapitalLettersMandatory || 0;
                var isSpecialCharactersMandatory = passwordComplexity?.isSpecialCharactersMandatory || 0;

                if (isCapitalLettersMandatory == 1 && isSpecialCharactersMandatory == 1) {
                    conditions = conditions + 2;
                    if (iscapital == 1) {

                        Score = Score + 1;
                    }
                    if (isspecial == 1) {
                        Score = Score + 1;
                    }

                } else if (isCapitalLettersMandatory == 1 && isSpecialCharactersMandatory == 0) {
                    conditions = conditions + 1;
                    if (iscapital == 1) {
                        Score = Score + 1;
                    }

                } else if (isCapitalLettersMandatory == 0 && isSpecialCharactersMandatory == 1) {
                    conditions = conditions + 1;
                    if (isspecial == 1) {
                        Score = Score + 1;
                    }

                } else if (isCapitalLettersMandatory == 0 && isSpecialCharactersMandatory == 0) {

                }
                if (conditions == Score) {
                    $('#passwordsavebtn').attr('disabled', false);
                    $('#progrssbarlogin').html('<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>');
                    $('#messagelogin').html('<label class="label label-success">Strong</label>');
                } else if ((conditions % Score) > 0) {
                    $('#passwordsavebtn').attr('disabled', true);
                    $('#progrssbarlogin').html('<div class="progress-bar progress-bar-warning" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>');
                    $('#messagelogin').html('<label class="label label-warning">Medium</label>');
                } else {
                    $('#passwordsavebtn').attr('disabled', true);
                    $('#progrssbarlogin').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                    $('#messagelogin').html('<label class="label label-danger">Weak</label>');
                }
            }
        } else {
            var word = $('#newPassword').val();
            var lengt = word.length;

            if (lengt < 6) {
                $('#passwordsavebtn').attr('disabled', true);
                $('#progrssbarlogin').html('<div class="progress-bar progress-bar-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>');
                $('#messagelogin').html('<label class="label label-danger">Weak</label>');
            } else {
                $('#passwordsavebtn').attr('disabled', false);
                $('#progrssbarlogin').html('<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>');
                $('#messagelogin').html('<label class="label label-success">Strong</label>');
            }

        }
    }

    function msg_popup(btnClass = null) {
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

        // $('body').append('<div class="modal fade-scale" id="access_denied_alertDiv" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document" style="width: 35%"><div class="modal-content" style=" border:3px solid #cc0000;"><div class="modal-body" style="text-align: center;"><div class="row-fluid"><div class="span2" style="text-align: center;"><i class="fa fa-times-circle fa-4x" style="color:#cc0000; margin-top: 10%;"></i></div><div class="span10" style="text-align: left;"><span style="text-align: left"><b>Access Denied!</b></span><br><span>You do not have sufficient privileges to access this feature. Please contact the admin for the access.</span></div></div><br><button type="button" class="btn btn-danger btn-flat btn-sm pull-right" data-dismiss="modal">Okay</button></div></div></div></div>');

        $('body').append('<div class="modal fade-scale" id="access_denied_alertDiv" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog" role="document" style="width: 35%"><div class="modal-content" style=" border:3px solid #cc0000;"><div class="modal-body" style="text-align: center;"><div class="row-fluid"><div class="span12" style="text-align: center;"><span><i class="fa fa-warning" style="font-size:24px;color:#cc0000;margin-left: 10px;float: left;"></i><b style="text-align: center;"> Access Denied!</b></span><br><br><span>You do not have sufficient privileges to access this feature. Please contact the admin for the access.</span></div></div><button type="button" class="btn btn-danger btn-flat btn-sm pull-right" data-dismiss="modal">Okay</button></div></div></div></div>');

        $('#access_denied_alertDiv').modal('show');

    }

    function control_staff_access(page_id, page_url, status, pageTitle) {

    }

    //end of User control function

    function openlanguagemodel() {
        $('#language_select_modal').modal('show');
    }


    function change_emp_language(languageid, reload) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {languageid: languageid},
            url: "<?php echo site_url('Access_menu/update_emp_language'); ?>",
            success: function (data) {
                location.reload();

                stopLoad();

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });


    }


    function openthirdpartymodal() {
        $('#third_party_model').modal('show');

    }

    function employee_location() {
        $('#employee_location_select_modal').modal('show');

    }

    function change_emp_location(locationID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {locationID: locationID},
            url: "<?php echo site_url('Access_menu/update_emp_location'); ?>",
            success: function (data) {
                location.reload();
                stopLoad();

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function isGroupCompany(companyid) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {companyid: companyid},
            url: "<?php echo site_url('Access_menu/getusergroupcomapny'); ?>",
            success: function (data) {
                if (data == 2) {
                    totaldocumentcount();
                }

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }


    function totaldocumentcount()//approvaldocuments
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('documentallapproval/total_document_count'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                $('#totalapprovalcount').html(data);
                stopLoad();

            }, error: function () {
                myAlert('e', 'An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function getRedirectionToken(page_url) {


        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('Access_menu/getRedirectionToken'); ?>",
            success: function (data) {
                if (data['token']) {
                    var token = data['token'];
                    window.open(page_url + token, '_blank');
                } else {
                    swal("error", "You are not authorized to access", "error");
                }

                stopLoad();

            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });

    }

    function open_user_manual() {
        $('#usermaualmodal').modal('show');
    }

    function open_training_videos() {
        $('#training_videos').modal('show');
    }

    function hide_subscription_alert() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('Company/hide_subscription_alert'); ?>",
        });
    }

    function get_QHSE_LoginDetails() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('Company/QHSE_user_authentication'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data['status'] == 'e') {
                    swal("", data['message'], "error");
                    return false;
                }

                window.open(data['url'] + '#/pages/login?token=' + data['token'] + '&username=' + data['loginID'] + '', '_blank');

                stopLoad();
            }, error: function () {
                myAlert('e', 'Some thing went wrong,Please contact system support.')
            }
        });
    }

    function get_realmax_LoginDetails() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            url: "<?php echo site_url('Company/relamax_user_authentication'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (data['status'] == 'e') {
                    window.open(data['url'], '_blank');
                }
                window.open(data['url'] + '#/pages/login?token=' + data['token'] + '', '_blank');
                stopLoad();
            }, error: function () {
                myAlert('e', 'Some thing went wrong,Please contact system support.')
            }
        });
    }



    function widgetHelpDesk() {
        ((d, tenant, token, email, productIid, js) => {
            const script = d.createElement("script");
            script.src = js;
            document.getElementsByTagName("body")[0].appendChild(script);
            const element = d.createElement("hds-iframe");
            element.setAttribute("tenant", tenant);
            element.setAttribute("email", email);
            element.setAttribute("token", token);
            element.setAttribute("product_id", productIid);
            document.getElementsByTagName("body")[0].appendChild(element);
        })(
            document,
            "pbs",
            supportToken,
            current_user,
            productID,
            "https://int.com/iframe/app.js",
        );
    }

    function removeIFrame(){
        document.getElementsByTagName('hds-iframe')[0].remove();
    }

</script>

<?php if (!isset($noChart)) { ?>
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">

        /**window.onbeforeunload = closingCode;
         function closingCode(){
            window.open("<?php echo site_url('Login/logout') ?>");
        }*/
        //document email history
        function save_document_email_history(toemail, docCode, docId) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'toemail': toemail, 'documentCode': docCode, 'documentID': docId},
                url: "<?php echo site_url('Procurement/save_document_email_history'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

        function check_item_not_approved_document_wise(itemAutoID, typ, docID, id) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {itemAutoID: itemAutoID},
                url: "<?php echo site_url('Inventory/check_item_not_approved_document_wise'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#access_denied_document_wise_body').empty();
                    x = 1;
                    if (jQuery.isEmptyObject(data['usedDocs'])) {
                        $('#access_denied_document_wise_body').append('<tr class="danger"><td colspan="4" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                    } else {
                        $.each(data['usedDocs'], function (key, value) {
                            $('#access_denied_document_wise_body').append('<tr><td>' + x + '</td><td><a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'' + value['documentID'] + '\',' + value['documentAutoID'] + ')">' + value['documentCode'] + '</a></td><td>' + value['referenceNo'] + '</td><td>' + value['documentDate'] + '</td></tr>');
                            x++;
                        });

                        if (docID == 'MI') {
                            if (typ == 'add') {
                                $('#f_search_' + id).val('');
                                $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                                $('#f_search_' + id).closest('tr').find('.currentWareHouseStock').val('');
                                $('#f_search_' + id).closest('tr').find('.quantityRequested').val('');
                                $('#f_search_' + id).closest('tr').find('.umoDropdown').val('');
                            } else {
                                $('#quantityRequested_edit').val('');
                                $('#itemAutoID_edit').val('');
                                $('#currentStock_edit').val('');
                                $('#d_uom_edit').val('');
                                $('#search').val('');
                            }
                        } else if (docID == 'RV') {
                            if (typ == 'add') {
                                $('#f_search_' + id).val('');
                                $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                                $('#f_search_' + id).closest('tr').find('.currentstock').val('');
                                $('#f_search_' + id).closest('tr').find('.quantityRequested').val('');
                                $('#f_search_' + id).closest('tr').find('.estimatedAmount').val('');
                                $('#f_search_' + id).closest('tr').find('.netAmount').val('');
                            } else {
                                $('#currentstock_edit').val('');
                                $('#edit_itemAutoID').val('');
                                $('#currentStock_edit').val('');
                                $('#edit_estimatedAmount').val('');
                                $('#editNetAmount').val('');
                                $('#search').val('');
                            }
                        } else if (docID == 'CINV') {
                            if (typ == 'add') {
                                $('#f_search_' + id).val('');
                                $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                                $('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val('');
                                $('#f_search_' + id).closest('tr').find('.currentstock').val('');
                                $('#f_search_' + id).closest('tr').find('.quantityRequested').val('');
                                $('#f_search_' + id).closest('tr').find('.estimatedAmount').val('');
                            } else {
                                $('#edit_itemAutoID').val('');
                                $('#edit_wareHouseAutoID').val('');
                                $('#currentstock_edit').val('');
                                $('#edit_quantityRequested').val('');
                                $('#edit_estimatedAmount').val('');
                                $('#search').val('');
                            }
                        } else if (docID == 'DO') {
                            if (typ == 'add') {
                                $('#f_search_' + id).val('');
                                $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                                $('#f_search_' + id).closest('tr').find('.wareHouseAutoID').val('');
                                $('#f_search_' + id).closest('tr').find('.currentstock').val('');
                                $('#f_search_' + id).closest('tr').find('.quantityRequested').val('');
                                $('#f_search_' + id).closest('tr').find('.estimatedAmount').val('');
                            } else {
                                $('#edit_itemAutoID').val('');
                                $('#edit_wareHouseAutoID').val('');
                                $('#currentstock_edit').val('');
                                $('#edit_quantityRequested').val('');
                                $('#edit_estimatedAmount').val('');
                                $('#search').val('');
                            }
                        } else if (docID == 'ST') {
                            if (typ == 'add') {
                                $('#f_search_' + id).val('');
                                $('#f_search_' + id).closest('tr').find('.itemAutoID').val('');
                                $('#f_search_' + id).closest('tr').find('.currentWareHouseStock').val('');
                                $('#f_search_' + id).closest('tr').find('.currentWac').val('');
                                $('#f_search_' + id).closest('tr').find('.adjustment_wac').val('');
                                $('#f_search_' + id).closest('tr').find('.umoDropdown').val('');
                            } else {
                                $('#itemAutoID_edit').val('');
                                $('#currentStock_edit').val('');
                                $('#edit_wareHouseAutoID').val('');
                                $('#transferqty_edit').val('');
                                $('#search').val('');
                            }
                        }
                        $('#access_denied_document_wise').modal('show');
                    }


                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function insert_system_audit_log_nav(navigationMenuID, transactionType, documentID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'navigationMenuID': navigationMenuID,
                    'transactionType': transactionType,
                    'documentID': documentID
                },
                url: "<?php echo site_url('Company/insert_system_audit_log_nav'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

        function open_support_contact_info() {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                url: "<?php echo site_url('Company/fetch_support_contact_info '); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#support_contact_information_body').html('');
                    if (!$.isEmptyObject(data)) {
                        $.each(data, function (k, v) {
                            $('#support_contact_information_body').append('<tr><td>' + v.contactPerson + '</td><td>' + v.contactEmail + '</td><td>' + v.contactTelephone + '</td></tr>');
                        });
                    } else {
                        $('#support_contact_information_body').append('<tr class="danger"><td colspan="2" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?></b></td></tr>');
                    }
                    $('#support_contact_info_modal').modal('show');

                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });
            //$('#support_contact_info_modal').modal('show');
        }

        function document_by_warehouse_qty(itemAutoID, warehouseAutoID, documentcode = null, DocumentAutoID = null, uom, uomRate, TransferQty,DocumentDetAutoID = null) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    'itemAutoID': itemAutoID,
                    'warehouseAutoID': warehouseAutoID,
                    'documentcode': documentcode,
                    'DocumentAutoID': DocumentAutoID,
                    'DocumentDetAutoID': DocumentDetAutoID
                },
                url: "<?php echo site_url('Inventory/check_item_not_approved_in_document_bywarehouse'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();

                    $('#documents_by_warehouse_qty_body').empty();
                    x = 1;


                    if (jQuery.isEmptyObject(data['usedDocs'])) {
                        $('#documents_by_warehouse_qty_body').append('<tr class="danger"><td colspan="5" class="text-center"><b><?php echo $this->lang->line('common_no_records_found');?><!--No Records Found--></b></td></tr>');
                    } else {
                        var stock = 0;
                        var stock_tot = 0;
                        $.each(data['usedDocs'], function (key, value) {
                            if (uomRate != '') {
                                stock = parseFloat((value['stock'] * uomRate)).toFixed(4);
                            } else {
                                stock = parseFloat((value['stock'])).toFixed(4);
                            }


                            stock_tot += parseFloat(stock);


                            $('#documents_by_warehouse_qty_body').append('<tr><td>' + x + '</td><td>' + value['documentID'] + '</td><td><a target="_blank" style="cursor: pointer;" onclick="documentPageView_modal(\'' + value['documentID'] + '\',' + value['documentAutoID'] + ')">' + value['documentCode'] + '</a></td><td>' + value['referenceNo'] + '</td><td>' + value['documentDate'] + '</td><td>' + value['warehouse'] + '</td><td>' + uom + '</td><td style="text-align:right">' + stock + '</td></tr>');
                            x++;


                        });
                        //alert(stock_tot);
                        $('#pulled_qtytot').html(parseFloat(stock_tot).toFixed(4));
                        $('#requested_qtytot').html(parseFloat(TransferQty).toFixed(4));
                        $('#available_qtytot').html((parseFloat(TransferQty).toFixed(4) - parseFloat(stock_tot).toFixed(4)).toFixed(4));
                        $('#unitofmesure').html(uom);

                        $('#documents_by_warehouse_qty_model').modal('show');
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

        function open_tax_dd(taxDetailAutoID = null, documentMasterAutoID, documentID, currency_decimal, documentDetailID = null, detailTBL, detailColName, reloadYN = 0,isFromView = 0) {

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {
                    'taxDetailAutoID': taxDetailAutoID,
                    'documentMasterAutoID': documentMasterAutoID,
                    'documentID': documentID,
                    'documentDetailID': documentDetailID,
                    'detailTBL': detailTBL,
                    'detailColName': detailColName,
                    'currency_decimal': currency_decimal,
                    'isFromView': isFromView
                },
                url: "<?php echo site_url('TaxCalculationGroup/fetch_tax_group_detail'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#tax_calculation_dd_body').html(data);
                    $('#tax_information_dd').modal('show');
                    if (reloadYN == 1) {
                        reloadFunName(documentID);
                    }
                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

        function calculateTax(element, taxLedgerAutoID, taxFormulaDetailID, documentMasterAutoID, documentDetailID, detailTBL, detailColName, documentID, FieldToUpdate, taxDetailAutoID, currency_decimal) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',

                data: {
                    'taxLedgerAutoID': taxLedgerAutoID,
                    'taxFormulaDetailID': taxFormulaDetailID,
                    'documentMasterAutoID': documentMasterAutoID,
                    'documentDetailID': documentDetailID,
                    'value': element.value,
                    'detailTBL': detailTBL,
                    'detailColName': detailColName,
                    'documentID': documentID,
                    'FieldToUpdate': FieldToUpdate
                },

                url: "<?php echo site_url('TaxCalculationGroup/update_tax_calculation_DD'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
                        open_tax_dd(taxDetailAutoID, documentMasterAutoID, documentID, currency_decimal, documentDetailID, detailTBL, detailColName, 1);
                    }
                }, error: function () {
                    stopLoad();
                    //swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }

        function reloadFunName(documentID) {

            switch (documentID) {
                case "GRV-ADD":
                case "SRN-ADD":
                    fetch_addon_cost();
                break;
                case "PO":
                case "PO-PRQ":
                    fetch_po_detail_table();
                    break;
                case "GRV":
                case "SRN":
                    fetch_details();
                    break;
                case "BSI":
                    fetch_details();
                    break;
                case "RV":
                    fetch_rv_details();
                    break;
                case "DO":
                    fetch_details();
                    break;
                case "CNT":
                    fetch_item_detail_table();
                    break;
                case "CINV":
                    fetch_invoice_direct_details();
                    break;
                case "CN":
                    fetch_cn_detail_table();
                    break;
                case "PV":
                    fetch_pv_direct_details();
                    break;
                case "PR":
                    fetch_return_direct_details();
                    break;



            }
        }

        function close_progress_bar(){
         
            setTimeout(() => {
                $('#progress_bar_model').modal('hide');

                $('#progress-bar').css('width','0%');
                // $('#progress-bar').css('background-color','#8f6782');
                $('#progress-amount').html('0%');
                $('#progress-bar-message').text('Payroll is Processing Please wait..!');
               
            }, 2500);

        }


        $(window).keyup(function(event){
            if(event.which==27){
                $('.modal').modal('hide');
            }
        })

        ////////////////Start pr Buyer assign /////////////////////////////////////////

    function view_buyersViewAssignModel(purchaseRequestID,purchaseRequestDetailsID,search,type,isSerch) {

        assignBuyersViewSync =[];
       var search_index = '';
        if(isSerch == 1){ 
             $('#masterID').val('');
             $('#detailsID').val('');
             $('#type').val('');
        }else { 
            search_index  = search;
        }
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {purchaseRequestID: purchaseRequestID,purchaseRequestDetailsID:purchaseRequestDetailsID,Search:search_index,type:type},
            url: "<?php echo site_url('PurchaseRequest/assignItem_pr_buyer_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                //$('#documentPageViewTitle').html(title);
                $('#masterID').val(purchaseRequestID);
                $('#detailsID').val(purchaseRequestDetailsID);
                $('#type').val(type);
                $('#assignBuyer_item_Content_view').html(data);

                
                if(type == 2 && $('#current_user_access').val()==1){
                    
                    $("#buyer_submit").removeClass('hide');
                }else{
                    $("#buyer_submit").addClass('hide');
                }

                if(isSerch==1){
                    $("#assignBuyerr_view_item_model").modal({backdrop: "static"});
                }
            
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function addAllRows_buyers_on_document() {

        var postData = $('#category_buyers_form_document').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: postData,
            url: "<?php echo site_url('PurchaseRequest/add_buyers_to_document_item'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                
                    view_buyersViewAssignModel(data[2],data[3],'',2,0);
                }
            },
            error: function () {
                myAlert('e', '<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
            }
        });

    }

    function assign_buyers_selected_check(buyer) {
        var value = $(buyer).val();
        if ($(buyer).is(':checked')) {
            var inArray = $.inArray(value, assignBuyersViewSync);
            if (inArray == -1) {
                assignBuyersViewSync.push(value);
            }
        }
        else {
            var i = assignBuyersViewSync.indexOf(value);
            if (i != -1) {
                assignBuyersViewSync.splice(i, 1);
            }
        }
    }

    function assign_buyers_current_user() {
        
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                'assignBuyersSync': assignBuyersViewSync,
            },
            url: "<?php echo site_url('PurchaseRequest/assign_buyers_pr_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                refreshNotifications(true);
                
                if (data[0] == 's') {
                    myAlert(data[0], data[1]);
                    view_buyersViewAssignModel($('#masterID').val(),$('#detailsID').val(),'',$('#type').val(),1);
                   
                } else {

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function close_buyers_view(){
        $("#assignBuyerr_view_item_model").modal('hide');
    }

    function startMasterSearchBuyers(){ 
        var purchaseRequestID= $('#masterID').val();
        var purchaseRequestDetailsID= $('#detailsID').val();
        var type= $('#type').val();
        var search = $('#BuyerSh').val();
        view_buyersViewAssignModel(purchaseRequestID,purchaseRequestDetailsID,search,type,0);
    }

    function remove_assign_buyers_pr(id){ 
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id:id},
            url: "<?php echo site_url('PurchaseRequest/remove_assign_buyers_pr'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    view_buyersViewAssignModel($('#masterID').val(),$('#detailsID').val(),'',$('#type').val(),1);
                } else {

                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function remove_assign_buyers_pr_item(id,masterID,detID){ 
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {id:id},
            url: "<?php echo site_url('PurchaseRequest/remove_assign_buyers_pr_item'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    view_buyersViewAssignModel(masterID,detID,'',2,0);
                } else {

                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }
 ////////////////end pr Buyer assign /////////////////////////////////////////

    function openItemHistoryModel(id,documentCode){

        //$('#').modal('show');

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'itemAutoID': id,'documentCode': documentCode,'html': true},
            url: "<?php echo site_url('Procurement/fetch_item_history_on_document'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#document_item_history_body').html("");
                $('#document_item_history_body').html(data);
                
                $("#document_item_history").modal({backdrop: "static"});
                stopLoad();
             
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
        
    }

    function load_srm_order_review_statement(inquiryMasterID,reviewMasterID){

        var template = '';
        $('#conform_body1').html('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryID: inquiryMasterID,orderreviewID: reviewMasterID,template:template},
            url: "<?php echo site_url('srm_master/order_review_detail_view_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#order_review_statement_body').html(data);
                $('.re_check').attr('disabled', true);

                
                $("#order_review_statement_body_model").modal({backdrop: "static"});
                stopLoad();
             
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function fetch_approval_view_review(orderreviewID) {
        $("#Tab-home-c-review").addClass("hide");
        $("#Tab-home-v-review").removeClass("hide");

        $("#Tab-home-c-review").removeClass("active");
        $("#Tab-home-v-review").addClass("active");

        $("#cn_attachement_approval_Tabview_vv_review").removeClass("active");
        $("#cn_attachement_approval_Tabview_v_review").addClass("active");
        $('#conform_body_review').html('');
        if (orderreviewID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'html',
                data: {'orderreviewID': orderreviewID, 'html': true},
                url: "<?php echo site_url('Srm_master/load_ordereview_conformation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                   $("#order_review_modal_review").modal({backdrop: "static"});
                    $('#conform_body_review').html(data);
                   

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


    function tabAttachement_review(){
        $("#Tab-home-c-review").removeClass("hide");
        $("#Tab-home-v-review").addClass("hide");

        $("#Tab-home-c-review").addClass("active");
        $("#Tab-home-v-review").removeClass("active");

        $("#cn_attachement_approval_Tabview_v_review").removeClass("active");
        $("#cn_attachement_approval_Tabview_vv_review").addClass("active");

        var inquiryMasterID = $('#inquiryMasterID').val();
        var reviewMasterID = $('#reviewMasterID').val();

        var template = '';
        $('#conform_body1_review').html('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryID: inquiryMasterID,orderreviewID: reviewMasterID,template:template},
            url: "<?php echo site_url('srm_master/order_review_detail_view_approval'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#conform_body1_review').html(data);
                $('.re_check').attr('disabled', true);
                stopLoad();
                // $('#pending-li').removeClass('active');
                // $('#or_ongoing').removeClass('active');
                // $('#statement-li').addClass('active');
                // $('#statement').addClass('active');
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function tabView_review(){
        $("#Tab-home-c-review").addClass("hide");
        $("#Tab-home-v-review").removeClass("hide");

        $("#Tab-home-c-review").removeClass("active");
        $("#Tab-home-v-review").addClass("active");

        $("#cn_attachement_approval_Tabview_vv_review").removeClass("active");
        $("#cn_attachement_approval_Tabview_v_review").addClass("active");
    }

 function edit_item_grv_inspection(id, value) {
    
    if (id) {
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
                    data: {'grvDetailsID': id,'purchaseOrderID': value},
                    url: "<?php echo site_url('Grv/fetch_grv_detail'); ?>",
                    beforeSend: function () {
                        $("#grv_st_detail_modal_inspection").modal('show');
                        startLoad();
                    },
                    success: function (data) {
                        // grvDetailsID = data['grvDetailsID'];
                        // projectID = data['projectID'];
                        // projectcategory = data['project_categoryID'];
                        // projectsubcat = data['project_subCategoryID'];
                        

                        $('#UnitOfMeasureID').val(data['unitOfMeasureID']);
                        $('#grvAutoID_in').val(data['grvAutoID']);
                        $('#grvDetailsID').val(data['grvDetailsID']);

                        $('#search').val(data['itemDescription'] + " - " + data['itemSystemCode'] + " - "+data['seconeryItemCode']);
                        //fetch_related_uom_id(data['defaultUOMID'], data['unitOfMeasureID']);
                        $('#quantityRequested').val(data['receivedQty']);
                        $('#qty_unchanged').val(data['receivedQty']);
                        $('#bal_qty').val(data['qtybalance']);
                        $('#estimatedAmount').val(data['receivedAmount']);
                        $('#receivedTotalAmount').val(data['receivedTotalAmount']);
                        $('#search_id').val(data['itemSystemCode']);
                        $('#itemSystemCode').val(data['itemSystemCode']);
                        $('#itemAutoID').val(data['itemAutoID']);
                        $('#itemDescription').val(data['itemDescription']);
                        $('#comment').val(data['comment']); 
                        $('#batchNumber').val(data['batchNumber']);
                        $('#expireDate').val(data['batchExpireDate']);
                        $('#remarks').val(data['remarks']);

                        if(data['isFoc'] == 1){
                            $('#isFocEdit').prop('checked',true);
                        }else{
                            $('#isFocEdit').prop('checked',false);
                        }

                        $('#grvPurchaseOrderID').val(data['purchaseOrderMastertID']);
                        $('#grvPurchaseOrderDetailID').val(data['purchaseOrderDetailsID']);
                        $('#grvTaxCalculationFormulaID').val(data['taxCalculationformulaID']);

                        if (value != 0) {
                            $('.hide_po').hide();
                        }
                      
                        // if(isGroupByTaxEnable == 1){ 
                        //     $('#linetaxDescription').html(data['Description']);
                        //     $('#tax_type').val(data['taxtype']);
                        //     $('#linetaxamnt_edit').html( parseFloat( data['taxAmountLedger']).toFixed(currency_decimal));
                        //     $('#receivedTotalAmount').val( ( parseFloat(data['receivedTotalAmount']) + parseFloat(data['taxAmountLedger'])));
                        // }
                     
                        /*$("#grv_st_detail_modal").modal({backdrop: "static"});*/
                        //load_segmentBase_projectID_itemEdit();
                        //initializeitemTypeahead_edit();
                        stopLoad();
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }
}

    function save_grv_inspection(){
        var data = $('#grv_detail_form_inspection').serializeArray();

        var bal_qty = parseFloat($("#bal_qty").val());
        var quantityRequested = parseFloat($("#quantityRequested").val());

        if(bal_qty<=quantityRequested){
            myAlert('w', 'Qty cannot be greater than  balance Qty!')
           // swal("Warning", "Qty cannot be greater than  balance Qty!", "warning");
        }else{

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Grv/save_grv_inspection_qty'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    //myAlert(data[0], data[1]);
                    if (data['status'] == true) {
                        $("#grv_st_detail_modal_inspection").modal('hide');
                        $("#grv_modal").modal('hide');
                        fetchPage('system/grv/grv_approval', 'Test', 'GRV Approval');
                        //documentPageView_modal(data[2],data[3]);
                    } else {

                    }
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });
        }
    }

    function allocateCost(detailId, masterId, documentId, activityCodeID)
    {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('DocumentCostAllocation/getCostAllocation'); ?>",
            data: {
                documentId: documentId, 
                masterId: masterId,
                activityCodeID: activityCodeID,
                detailId: detailId,
            },
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#document_cost_allocation_body").html(data);
                $("#document_cost_allocation_modal").modal({backdrop: "static"});

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
        return false;
        
    }

    function close_Document_details_line_wise(documentID,MasterID,DetailsID,tbName,master_col_name_cl){
        $('#documentID_cl').val('');
        $('#masterID_cl').val('');
        $('#detailID_cl').val('');
        $('#tableName_cl').val('');
        $('#master_col_name_cl').val('');
      

        $('#documentID_cl').val(documentID);
        $('#masterID_cl').val(MasterID);
        $('#detailID_cl').val(DetailsID);
        $('#tableName_cl').val(tbName);
        $('#master_col_name_cl').val(master_col_name_cl);
      
        $("#document_close_line_wise").modal({backdrop: "static"});
    }

    function close_Document_details_view_line_wise(documentID,MasterID,DetailsID,tbName,master_col_name_cl,issrm){
       
        $('#masterID_ac').val('');
        $('#detailID_ac').val('');

        $('#ac_by').text('');
        $('#ac_date').text('');
        $('#ac_comment').text('');

        $('#cl_by').text('');
        $('#cl_date').text('');
        $('#cl_comment').text('');



        $('#masterID_ac').val(MasterID);
        $('#detailID_ac').val(DetailsID);

        if(issrm ==1){
            $('#ac_view_all').removeClass('hide');
            $('#ac_btn').removeClass('hide');

            $('#ac_comment').text('');
            $('#ac_accept_val').val(0);
            $('#ac_accept').prop('checked',false);
            $('#ac_view').addClass('hide');
        }else{
            $('#ac_view_all').addClass('hide');
            $('#ac_btn').addClass('hide');
        }

        $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {"documentID":documentID,"MasterID":MasterID,"DetailsID":DetailsID,"tbName":tbName,"master_col_name_cl":master_col_name_cl},
                url: "<?php echo site_url('PurchaseRequest/fetch_close_document_details'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);

                    $('#cl_by').text(data['Ename1']);
                    $('#cl_date').text(data['isClosedDate']);
                    $('#cl_comment').text(data['isClosedComment']);

                    if(data['isAcknowledgeYN']==1){
                        $('#ac_view_all').addClass('hide');
                        $('#ac_btn').addClass('hide');
                        $('#ac_view_action_all').removeClass('hide');

                        $('#ac_by').text(data['isAcknowledgeByName']);
                        $('#ac_date').text(data['isAcknowledgeDate']);
                        $('#ac_comment').text(data['isAcknowledgeComment']);
                    }else{
                        $('#ac_view_action_all').addClass('hide');
                    }
                     $("#document_close_line_wise_view").modal({backdrop: "static"});
                    
                }, error: function () {
                    swal("Cancelled", "Your file is safe :)", "error");
                }
        });
      
        
    }

    $('#ac_accept').on('change', function(){
        if($('#ac_accept').is(":checked")){
            $('#ac_view').removeClass('hide');
            $('#ac_accept_val').val(1);
        }else {
            $('#ac_view').addClass('hide');
            $('#ac_accept_val').val(0);
        }

    });

    function close_document_line_wise_item(){

        var data = $('#document_line_close_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('PurchaseRequest/remove_assign_items_line_wise'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $("#document_close_line_wise").modal('hide');
                    documentPageView_modal(data[2],data[3]);
                } else {

                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function save_srm_acknowledge(){

        var data1 = $('#document_acknowledge_form').serializeArray();

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data1,
            url: "<?php echo site_url('PurchaseRequest/save_srm_acknowledge'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                  
                    close_Document_details_view_line_wise('PRQ',data[2],data[3],'srp_erp_purchaserequestdetails','purchaseRequestDetailsID',0);
                } else {

                }
            }, error: function () {
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function open_chat_model_max_portal(inquiryDetailID,inquiryMasterID,supplierID,masterDocID,chatType,documentID,type=0){
        $('#inquiryDetailID_chat').val('');
        $('#inquiryMasterID_chat').val('');
        $('#supplierID_chat').val('');
        $('#chatType_chat').val('');
        $('#itemAutoID_chat').val('');
        $('#documentID_chat').val('');
        $('#cmax_body').html('');
        $('#inquiryDetailID_chat').val(inquiryDetailID);
        $('#inquiryMasterID_chat').val(inquiryMasterID);
        $('#supplierID_chat').val(supplierID);
        $('#chatType_chat').val(chatType);
        $('#itemAutoID_chat').val(masterDocID);
        $('#documentID_chat').val(documentID);

       // var html = 'html';
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {inquiryMasterID: inquiryMasterID,inquiryDetailID:inquiryDetailID, supplierID: supplierID,itemAutoID:masterDocID,chatType:chatType,documentID:documentID, html: true},
            url: "<?php echo site_url('srm_master/load_my_chat_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data1) {
                //$('#documentPageViewTitle').html(title);
                $('#cmax_body').html(data1);
                if(type==0){
                    $('#chatModalMaxportal').modal('show');
                }
                
                stopLoad();
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }

    function send_my_message_max_portal(){
        var msg = $('#chat_msg').val();

        if(msg !=null){

            var $form = $('#chat_form');
            var data = $form.serializeArray();

            var inquiryDetailID_chat =$('#inquiryDetailID_chat').val();
            var inquiryMasterID_chat=$('#inquiryMasterID_chat').val();
            var supplierID_chat=$('#supplierID_chat').val();
            var chatType_chat =$('#chatType_chat').val();
            var itemAutoID_chat =$('#itemAutoID_chat').val();
            var documentID_chat =$('#documentID_chat').val();

            data.push({'name': 'inquiryDetailID', 'value': inquiryDetailID_chat});
            data.push({'name': 'inquiryMasterID', 'value': inquiryMasterID_chat});
            data.push({'name': 'supplierID', 'value': supplierID_chat});
            data.push({'name': 'chatType', 'value': chatType_chat});
            data.push({'name': 'itemAutoID', 'value': itemAutoID_chat});
            data.push({'name': 'documentID', 'value': documentID_chat});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Srm_master/save_my_chat'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data1) {
                    stopLoad();
                    $('#chat_msg').val('');
                    
                   if(data1[0]=='s'){
                    $('#chatModalMaxportal').modal('hide');
                    myAlert('s', 'Message Send Successfully');
                   }else{
                    myAlert('s', 'Try again!');
                   }
                    
                },
                error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }

    }

    </script>

<?php } ?>
<!--End of Tawk.to Script-->

<script>
(function() {
  // Multi-Level Accordion Menu
  var accordionsMenu = document.getElementsByClassName('cd-accordion--animated');

	if( accordionsMenu.length > 0 && window.requestAnimationFrame) {
		for(var i = 0; i < accordionsMenu.length; i++) {(function(i){
			accordionsMenu[i].addEventListener('change', function(event){
				animateAccordion(event.target);
			});
		})(i);}

		function animateAccordion(input) {
			var bool = input.checked,
				dropdown =  input.parentNode.getElementsByClassName('cd-accordion__sub')[0];
			
			Util.addClass(dropdown, 'cd-accordion__sub--is-visible'); // make sure subnav is visible while animating height

			var initHeight = !bool ? dropdown.offsetHeight: 0,
				finalHeight = !bool ? 0 : dropdown.offsetHeight;

			Util.setHeight(initHeight, finalHeight, dropdown, 200, function(){
				Util.removeClass(dropdown, 'cd-accordion__sub--is-visible');
				dropdown.removeAttribute('style');
			});
		}
	}
}());
</script>

<script>
const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
const currentTheme = localStorage.getItem('theme');

if (currentTheme) {
    document.documentElement.setAttribute('data-theme', currentTheme);
  
    if (currentTheme === 'dark-mode') {
        toggleSwitch.checked = true;
    }
}

function switchTheme(e) {
    if (e.target.checked) {
        document.documentElement.setAttribute('data-theme', 'dark-mode');
        localStorage.setItem('theme', 'dark-mode');
    }
    else {        document.documentElement.setAttribute('data-theme', 'light-mode');
          localStorage.setItem('theme', 'light-mode');
    }    
}

function onClickNavigation(masterID) {
    $.ajax({
        async: true,
        type: 'post',
        dataType: 'json',
        data: {masterID: masterID},
        url: "<?php echo site_url('Access_menu/addNavigationMaster'); ?>",
        success: function (data) {
            location.reload();
            stopLoad();
        }, error: function (err_data) {
            alert(err_data.responseText);
        }
    });
}
</script>

</body>
</html>
