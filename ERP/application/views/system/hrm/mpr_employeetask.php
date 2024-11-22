<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);

$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('emp_master_mrp_action_tracker');
echo head_page($title, false);
//echo head_page('MPR - Action Tracker', false);
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$employeedrop = all_employee_drop();

$companyType = $this->session->userdata("companyType");
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/crm_style.css'); ?>">
<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .label {
        display: inline;
        padding: .2em .8em .3em;
    }

    .actionicon {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #89e68d;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 2px 5px 2px 5px;
        line-height: 14px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .headrowtitle {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0 25px;
        font-weight: bold;
        text-align: left;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
        background-color: white;
        border-top: 1px solid #ffffff;
    }

    .task-cat-upcoming {
        border-bottom: solid 1px #f76f01;
    }

    .task-cat-upcoming-label {
        display: inline;
        float: left;
        color: #f76f01;
        font-weight: bold;
        margin-top: 5px;
        font-size: 15px;
    }

    .taskcount {
        display: inline-block;
        font-weight: normal;
        font-size: 12px;
        background-color: #eee;
        -moz-border-radius: 2px;
        -khtml-border-radius: 2px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        padding: 1px 3px 0 3px;
        line-height: 14px;
        margin-left: 8px;
        margin-top: 9px;
        vertical-align: text-bottom;
        box-shadow: inset 0 -1px 0 #ccc;
        color: #888;
    }

    .numberColoring {
        font-size: 13px;
        font-weight: 600;
        color: saddlebrown;
    }

    .nav-tabs > li > a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs > li > a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }
</style>


<ul class="nav nav-tabs" id="main-tabs">
    <li class="active"><a href="#assignedtask" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('emp_master_assigned_task') ?><!--Assigned Task--></a></li>
    <li><a href="#createdtask" onclick="getcreatedtask();" data-toggle="tab"><i class="fa fa-television"></i><?php echo $this->lang->line('emp_master_created_task') ?><!--Created Task--></a></li>
</ul>
<br>
<div class="tab-content">
    <div class="tab-pane active" id="assignedtask">
        <div class="row">
            <div class="col-md-5">
            </div>
            <div class="col-md-4 text-center">
                &nbsp;
            </div>
            <div class="col-md-3 text-right">

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive mailbox-messages" id="assignedtask_view">
                    <!-- /.table -->
                </div>

            </div>
        </div>
    </div>


    <div class="tab-pane" id="createdtask">

        <div class="row" style="margin-top: 2%;">
            <div class="col-sm-4" style="margin-left: 2%;">

                <div class="col-sm-12">
                    <div class="box-tools">
                        <div class="has-feedback">
                            <input name="searchTaskexpences" type="text" class="form-control input-sm"
                                   placeholder="Enter Your Text Here"
                                   id="searchTaskexpences" onkeypress="startMasterSearch()">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-1">
                <div class="col-sm-1 hide" id="search_cancel">
                    <span class="tipped-top"><a id="cancelSearch" href="#" onclick="clearSearchFilter()"><img
                                    src="<?php echo base_url("images/crm/cancel-search.gif") ?>"></a></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive mailbox-messages" id="createdtask_view">
                    <!-- /.table -->
                </div>

            </div>
        </div>
    </div>

</div>
<div class="modal fade" id="documentPageView_iou" role="dialog" aria-labelledby="myModalLabel">
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

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="action_tracker_view_model_edit">
    <div class="modal-dialog" style="width: 45%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Action Tracker</h4>
            </div>
            <?php echo form_open('', 'role="form" id="edit_action_tracker"'); ?>
            <input type="hidden" id="assignedID" name="assignedID">
            <div class="modal-body">
                <div class="span6 well well-small" style="min-height: 274px;">
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Description</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="actiondescription" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Company</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="companyid" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Segment</label>
                        </div>
                        <div class="form-group col-sm-8">
                           <p id="department" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Month</label>
                        </div>
                        <div class="form-group col-sm-8">
                           <p id="month">......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Person Responsible</label>
                        </div>
                        <div class="form-group col-sm-8">
                           <p id="personresponsible" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Created Date</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="createddate" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Target Date</label>
                        </div>
                        <div class="form-group col-sm-8">
                           <p id="targetdate" >......</p>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Status</label>
                        </div>
                        <div class="form-group col-sm-5">
                            <?php echo form_dropdown('status', array(''=>'Select Status','1'=>'In Progress','2'=>'Completed'), '', 'class="form-control select2 status" id="status" '); ?>
                        </div>
                    </div>

                    <div class="row completioncomment hide">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Completion Date</label>
                        </div>
                        <div class="form-group col-sm-5">
                            <div class="input-group datepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="completiondate"
                                       data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                       value="<?php echo $current_date; ?>" id="completiondate"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row completioncomment hide">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Comment</label>
                        </div>
                        <div class="form-group col-sm-5">
                           <textarea class="form-control" rows="3" name="comment" id="comment"
                                     placeholder="Comment..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary next" type="button" onclick="update_action_tracker_status();">
                    Save
                </button>
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button><!--Close-->
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="action_tracker_view_model_status">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Assigned Task</h4>
            </div>

            <div class="modal-body">
                <div class="span6 well well-small" style="min-height: 274px;">
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Action</label>
                        </div>
                        <div class="form-group col-sm-8">
                           <p id="actiondescription_status">......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Company</label>
                        </div>
                        <div class="form-group col-sm-8">
                          <p id="CompanyID_status" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Segment </label>
                        </div>
                        <div class="form-group col-sm-8">
                           <p id="Segment_status" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Month</label>
                        </div>
                        <div class="form-group col-sm-8">
                           <p id="month_status" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Person Responsible </label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="personresponsible_status" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Created Date </label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="createddate_status" >......</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Target Date </label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="targetDate_status" >......</p>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Status</label>
                        </div>
                        <div class="form-group col-sm-5">
                            <span class="label hide closedstatus" style="background-color:#89de27; color: #FFFFFF; font-size: 11px;">Closed</span>
                            <span class="label hide openstatus" style="background-color:#00c0ef; color: #FFFFFF; font-size: 11px;">Open</span>
                            <span class="label hide inprogressstatus" style="background-color:#f39c12; color: #FFFFFF; font-size: 11px;">In Progress</span>
                            <span class="label hide completedstatus" style="background-color:#00a65a; color: #FFFFFF; font-size: 11px;">Completed</span>

                        </div>
                    </div>

                    <div class="row hide statuscomcls">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Date</label>
                        </div>
                        <div class="form-group col-sm-8">
                            <p id="completiondate_status" >......</p>
                        </div>
                    </div>

                    <div class="row hide statuscomclscomment">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title pull-right">Comment</label>
                        </div>
                        <div class="form-group col-sm-8">
                           <p id="Comment_status">......</p>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button><!--Close-->
            </div>
        </div>
    </div>
</div>

<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
        $('.select2').select2();
        $('.headerclose').click(function () {
            fetchPage('system/hrm/mpr_employeetask', '', 'MPR - Action Tracker');
        });
        getassignedtask();
        getcreatedtask();
        Inputmask().mask(document.querySelectorAll("input"));
    });

    $('#searchTaskexpences').bind('input', function () {
        startMasterSearch();
    });


    $('#searchTask').bind('input', function () {
        startMasterSearchiou();
    });
    $('.datepic').datetimepicker({
        useCurrent: false,
        format: date_format_policy,
    }).on('dp.change', function (ev) {

    });





    function startMasterSearch() {
        $('#search_cancel').removeClass('hide');
        getcreatedtask();
    }


    function clearSearchFilter() {
        $('#search_cancel').addClass('hide');
        $('#searchTaskexpences').val('');
        $('#sorting_1').addClass('selected');
        getcreatedtask();
    }
    function documentPageView_modal_ioue(documentID, para1, para2, approval = 0) {
        $("#profile-v").removeClass("active");
        $("#home-v").addClass("active");
        $("#TabViewActivation_attachment").removeClass("active");
        $("#tab_itemMasterTabF").removeClass("active");
        $("#TabViewActivation_view").addClass("active");
        attachment_View_modal(documentID, para1);
        $('#loaddocumentPageView').html('');
        var siteUrl;
        var paramData = new Array();
        var title = '';
        var a_link;
        var de_link;


        switch (documentID) {
            case "IOUE":
                siteUrl = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>";
                paramData.push({name: 'IOUbookingmasterid', value: para1});
                paramData.push({name: 'approval', value: approval});
                title = "IOU Booking";
                /**/
                a_link = "<?php echo site_url('Iou/load_iou_voucher_booking_confirmation'); ?>/" + para1;
                de_link = "<?php echo site_url('Iou/fetch_double_entry_iou_booking'); ?>/" + para1 + '/IOUB';
                break;
            case "IOU": 
                siteUrl = "<?php echo site_url('Iou/load_iou_voucher_confirmation'); ?>";
                paramData.push({name: 'voucherAutoID', value: para1});
                title = "IOU Voucher";

                break;

            default:
                notification('Document ID is not set.', 'w');
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
                $('#documentPageView_iou').modal('show');
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

    }

    function getassignedtask() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/assignedtask_myprofile_mpr'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#assignedtask_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function edit_action_tracker_status(ID) {
        $('#status').val('').trigger('change');
        $('#comment').val('');
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : {actionID:ID},
            url: "<?php echo site_url('Finance_dashboard/fetch_company_action_tracker_view_model'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#actiondescription').html(data['description']);
                $('#companyid').html(data['companycode']);
                $('#department').html(data['segment']);
                $('#personresponsible').html(data['AssignedEmp']);
                $('#createddate').html(data['createddate']);
                $('#targetdate').html(data['targetDate']);
                $('#assignedID').val(ID);
                $('#status').val(data['status']).change();

                $('#month').html(data['monthname']);
                $("#action_tracker_view_model_edit").modal({backdrop: "static"});

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });



    }

    $( ".status" ).change(function() {

      if (this.value == 2) {
            $('.completioncomment').removeClass('hide');
        } else {
          $('.completioncomment').addClass('hide');
        }

    });
    function update_action_tracker_status(ID) {
        var data = $('#edit_action_tracker').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : data,
            url: "<?php echo site_url('Finance_dashboard/update_mpr_task_status'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    getassignedtask();
                    $("#action_tracker_view_model_edit").modal('hide');
                }
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function view_mpr_view_myprofile(ID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data : {actionID:ID},
            url: "<?php echo site_url('Finance_dashboard/fetch_company_action_tracker_view_model'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#actiondescription_status').html(data['description']);
                $('#CompanyID_status').html(data['companycode']);
                $('#Segment_status').html(data['segment']);
                $('#month_status').html(data['monthname']);
                $('#personresponsible_status').html(data['AssignedEmp']);
                $('#createddate_status').html(data['createddate']);
                $('#targetDate_status').html(data['targetDate']);

                if (data['status'] == 0) {
                    $('.inprogressstatus').addClass('hide');
                    $('.closedstatus').addClass('hide');
                    $('.openstatus').removeClass('hide');
                    $('.completedstatus').addClass('hide');
                    $('.statuscomcls').addClass('hide');
                    $('.statuscomclscomment').addClass('hide');

                } else if (data['status'] == 1) {
                    $('.inprogressstatus').removeClass('hide');
                    $('.closedstatus').addClass('hide');
                    $('.openstatus').addClass('hide');
                    $('.completedstatus').addClass('hide');
                    $('.statuscomcls').addClass('hide');
                    $('.statuscomclscomment').addClass('hide');
                } else if (data['status'] == 2) {
                    $('.inprogressstatus').addClass('hide');
                    $('.closedstatus').addClass('hide');
                    $('.openstatus').addClass('hide');
                    $('.completedstatus').removeClass('hide');
                    $('.statuscomcls').removeClass('hide');
                    $('.statuscomclscomment').removeClass('hide');

                    $('#completiondate_status').html(data['completedDate']);
                    $('#Comment_status').html(data['completoncomment']);


                } else if (data['status'] == 3)
                {
                    $('.inprogressstatus').addClass('hide');
                    $('.closedstatus').removeClass('hide');
                    $('.openstatus').addClass('hide');
                    $('.completedstatus').addClass('hide');
                    $('.statuscomcls').removeClass('hide');
                    $('.statuscomclscomment').removeClass('hide');
                    $('#completiondate_status').html(data['approvedDate']);
                    $('#Comment_status').html(data['approvecom']);

                }
                $("#action_tracker_view_model_status").modal({backdrop: "static"});

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });

    }

    function getcreatedtask() {
        var searchTask = $('#searchTaskexpences').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'q': searchTask},
            url: "<?php echo site_url('Finance_dashboard/createdtask_myprofile_mpr'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#createdtask_view').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


</script>