
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page($_POST['page_name'], false);
$this->load->helper('crm_helper');
$isgroupadmin = crm_isGroupAdmin();
$admin = crm_isSuperAdmin();
$current_userid = current_userID();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
$employees_arr = fetch_employees_by_company_multiple(false);
$current_date = current_format_date();
?>

<style>
    .search-no-results {
        text-align: center;
        background-color: #f6f6f6;
        border: solid 1px #ddd;
        margin-top: 10px;
        padding: 1px;
    }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #828282;
        font-weight: bold;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .tddata {
        color: #333;
        padding: 4px 10px 0 0;
        font-size: 13px;
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
<?php
if (!empty($header)) {
    if ($header['isClosed'] != 1) {
        ?>
        <div class="row">
            <div class="col-md-5">
                &nbsp;
            </div>
            <div class="col-md-4 text-center">
                &nbsp;
            </div>
            <div class="col-md-3 text-right projecteditbtn">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="check_edit_approval()">
                    <span title="" rel="tooltip" class="glyphicon glyphicon-pencil" data-original-title="Edit"></span>
                    Edit
                </button>
            </div>
        </div>
        <br>
        <?php
    } else { ?>
        <div class="row">
            <div class="col-md-5">
                &nbsp;
            </div>
            <div class="col-md-4 text-center">
                &nbsp;
            </div>
            <div class="col-md-3 text-right projecteditbtn">
                <button type="button" class="btn btn-primary pull-right"
                        onclick="fetchPage('system/crm/create_new_task',<?php echo $header['taskID'] ?>,'View Task','CRM','view');">
                    View
                </button>
            </div>
        </div>
        <br>
        <?php
    }
    ?>
    <ul class="nav nav-tabs" id="main-tabs">
        <li class="active"><a href="#about" data-toggle="tab"><i class="fa fa-television"></i>About</a></li>
        <li><a href="#files" onclick="task_multiple_attachemts()" data-toggle="tab"><i class="fa fa-television"></i>Files</a></li>
        <?php if($header['isSubTaskEnabled']  == 1)
        { ?>
            <li><a href="#subtask" onclick="sub_task_view_assignee()" data-toggle="tab"><i class="fa fa-television"></i>Subtask</a></li>
        <?php }?>


    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="about">
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>Task Details</h2>
                    </header>
                </div>
            </div>
            <table class="property-table">
                <tbody>
                <tr>
                    <td class="ralign"><span class="title">Document Code</span></td>
                    <td><span class="tddata"><?php echo $header['documentSystemCodetask'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Task Subject</span></td>
                    <td><span class="tddata"><?php echo $header['subject'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Category</span></td>

                    <td><span class="label"
                              style="background-color:<?php echo $header['backGroundColor'] ?>; color: <?php echo $header['textColor'] ?>; font-size: 11px;"><?php echo $header['categoryDescription'] ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Progress</span></td>
                    <td><span class="tddata"><?php echo $header['progress'] ?> %</span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Priority</span></td>
                    <td>
                        <?php
                        if ($header['Priority'] == 3) { ?>
                            <button type="button"
                                    class="priority-btn high-ptry tipped-top active" title="High Priority">!!!
                            </button><span class="tddata">&nbsp High Priority</span>
                            <?php
                        } else if ($header['Priority'] == 2) { ?>
                            <button type="button"
                                    class="priority-btn med-ptry tipped-top active" title="Medium Priority">!!
                            </button> <span class="tddata">&nbsp Medium Priority</span>
                            <?php
                        } else if ($header['Priority'] == 1) { ?>
                            <button type="button"
                                    class="priority-btn low-ptry tipped-top" title="Low Priority">!
                            </button><span class="tddata">&nbsp Low Priority</span>

                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Status</span></td>
                    <td><span class="tddata"><?php echo $header['statusDescription'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign" ><span class="title">Start Date</span></td>
                    <td><span class="tddata" id="mainstartdate"><?php echo $header['starDate'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Due Date</span></td>
                    <td><span class="tddata" id="DueDate"><?php echo $header['DueDate'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Assigned To</span></td>
                    <td><span class="tddata"><?php
                            if (!empty($taskAssignee)) {
                                foreach ($taskAssignee as $row) {
                                    echo $row['Ename2'] . " ,";
                                }
                            }
                            ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Source</span></td>
                    <td>
                        <span class="tddata"><?php
                            if (!empty($header['opportunityID'])) { ?>
                                Opportunity :
                                <a class="link-person noselect " href="#"
                                   onclick="fetchPage('system/crm/opportunities_edit_view',<?php echo $header['opportunityID']; ?>,'View Opportunity','CRM')"></a>
                            <?php } else if (!empty($header['projectID'])) { ?>
                               Project :
                                <a class="link-person noselect " href="#"
                                   onclick="fetchPage('system/crm/project_edit_view',<?php echo $header['projectID']; ?>,'View Project','CRM')"><?php echo $header['projectName'] ?></a>
                                <?php
                            }
                            ?>
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>TASK DESCRIPTION</h2>
                    </header>
                </div>
            </div>
            <table class="property-table">
                <tbody>
                <tr>
                    <td style="padding-left: 5%;"><span class="tddata"><?php echo $header['taskDescription'] ?></span>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>RECORD DETAILS</h2>
                    </header>
                </div>
            </div>
            <table class="property-table">
                <tbody>
                <tr>
                    <td class="ralign"><span class="title">Date Completed</span></td>
                    <td><span class="tddata"><?php echo $header['completedDate'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Completed By</span></td>
                    <td><span class="tddata"><?php echo $header['completedBy'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Last Updated</span></td>
                    <td><span class="tddata"><?php echo $header['updateDate'] ?></span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Task Visibility</span></td>
                    <td><span class="tddata">
                    <?php if ($header['visibility'] == 1) {
                        echo 'Public Task';
                    }  if ($header['visibility'] == 2) {
                        echo 'Private Task';
                    }if ($header['visibility'] == 3){
                        echo 'Group Task';
                    }else if ($header['visibility'] == 4){
                        echo 'Selected Views Only';
                    }
                    ?>
                </span></td>
                </tr>
                <tr>
                    <td class="ralign"><span class="title">Task Created By</span></td>
                    <td><span class="tddata"><?php echo $header['createdbY'] ?></span></td>
                </tr>
                </tbody>
            </table>
            <br>

            <div class="row">
                <div class="col-md-12 animated zoomIn">
                    <header class="head-title">
                        <h2>TASK COMMENTS</h2>
                    </header>
                </div>
            </div>
            <?php echo form_open('', 'role="form" id="task_view_comment_form"'); ?>
            <div class="row">
                <div class="col-md-12">
                    <textarea class="form-control" rows="4" name="taskcomment"
                              id="taskcomment"><?php echo $header['comment']; ?></textarea>
                    <input type="hidden" name="taskID" value="<?php echo $header['taskID']; ?>">
                </div>
            </div>
            <br>

            <div class="row projecteditbtn">
                <div class="form-group col-sm-6">
                    <button class="btn btn-primary" type="submit">Add</button>
                    <button class="btn btn-danger" type="button" onclick="task_edit_view_close()">Close</button>
                </div>
                <div class="form-group col-sm-6" style="margin-top: 10px;">
                    &nbsp
                </div>
            </div>
            </form>
        </div>
        <div class="tab-pane" id="files">
            <br>

            <div id="task_multiple_attachemts"></div>
        </div>

        <div class="tab-pane" id="subtask">
            <br>
            <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right"
                                onclick="create_sub_task()" id="beneficiaryassign">
                            <i class="fa fa-plus"></i> Create SubTask
                        </button>
                    </div>
                </div>

            <div id="sub_task_view_assignee">

            
            </div>
            
        </div>
    </div>

    <div class="modal fade" id="sub_task_chat_model" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" style="width: 50%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title" style="line-height: 0.428571;">Chat</h3>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-sm-12" id="sub_task_chat">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="sub_task_attachment_model" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" style="width: 70%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Sub Task Attachment</h4>
                </div>
                <div role="form" id="" class="form-horizontal" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="">
                                <div class="col-sm-12" id="sub_task_attachment">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div aria-hidden="true" role="dialog" tabindex="-1" id="sub_task_add_item_modal" class="modal fade"
     style="display: none;">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">Add Sub Task Detail</h5>
            </div>
            <div class="modal-body">
                <!--t-->
                <?php echo form_dropdown('employeessubtask[]', $employees_arr, '', 'class="form-control" id="tempAssign"   multiple="" style="z-index: 0; display:none"'); ?>
                <form role="form" id="sub_task_addform" class="form-horizontal">
                    <input type="hidden" name="Taskid" id="Taskid">
                    <input type="hidden" id="DueDate" name=DueDate value="<?php echo $header['DueDate'] ?>">
                    <table class="table table-bordered table-condensed no-color" id="subTask_add_table">
                        <thead>
                        <tr>
                            <th style="width: 280px;">Task Description<?php required_mark(); ?></th>
                            <th style="width: 200px;">Est. Start Date <?php required_mark(); ?></th>
                            <th style="width: 200px;">Est.End Date</th>
                            <th style="width: 150px;">In Days<?php required_mark(); ?></th>
                            <th colspan="2">In Hours<?php required_mark(); ?></th>
                            <th style="width: 200px;">Assignee</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_subtask()"><i
                                        class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                            <textarea class="form-control" rows="1" name="Taskdescription[]" placeholder="Task Description..."></textarea>
                            </td>
                            <td>
                                <div class="input-group subtaskdateest">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="estsubtaskdate[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value=" " id="estsubtaskdate" class="form-control estsubtaskdate"
                                           required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group subtaskdateestend">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="estsubtaskdateend[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value=" " id="estsubtaskdateend" class="form-control estsubtaskdateend"
                                           required>
                                </div>
                            </td>
                            <td><input type="text" name="indays[]" onfocus="this.select();" class="form-control indays number" value=" " id="indays" value="0" readonly></td>
                            <td><input type="num" style="width: 100%;" class="form-control inhrs number " name="inhrs[]" placeholder="HH" onkeypress="return validateFloatKeyPress(this,event)" /> <input type="hidden" name="assign[]"  class="assign-cls"></td>
                            <td><input type="text" style="width: 100%;" class="form-control inmns number " name="inmns[]" placeholder="MM" onkeyup="cheack_minutes_count(this)" onkeypress="return validateFloatKeyPress(this,event)" /></td>

                            <td class="assigneeapp"><?php echo form_dropdown('employeessubtask[]', $employees_arr, '', 'class="form-control select2 employeessubtask"   multiple="" style="z-index: 0;"'); ?></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-primary" type="button" onclick="save_sub_task_assignee()">Save changes
                </button>
            </div>
        </div>
    </div>
</div>




    <div aria-hidden="true" role="dialog" id="edit_task_subtask_details_modal" class="modal fade"
         style="display: none;">
        <div class="modal-dialog modal-lg" style="width: 80%">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Sub Task</h4>
                </div>
                <form role="form" id="edit_sub_task_frm" class="form-horizontal">
                    <div class="modal-body">
                        <input type="hidden" name="subtaskAutoid" id="subtaskAutoid">
                        <input type="hidden" name="taskautoid" id="taskautoid">

                        <table class="table table-bordered table-condensed no-color" id="income_edit_table">
                            <thead>
                            <tr>
                                <th style="width: 280px;">Task Description<?php required_mark(); ?></th>
                                <th style="width: 200px;">Est. Start Date <?php required_mark(); ?></th>
                                <th style="width: 200px;">Est.End Date</th>
                                <th style="width: 150px;">In Days<?php required_mark(); ?></th>
                                <th colspan="2" style="width: 18%;">In Hours<?php required_mark(); ?></th>
                                <th style="width: 200px;">Assignee</th>
                                <th style="width: 40px;">
                                    <!--  <button type="button" class="btn btn-primary btn-xs" onclick="add_more_subtask()"><i
                                              class="fa fa-plus"></i></button>-->
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                            <tr>
                                <td>
                                    <textarea class="form-control" rows="1" name="Taskdescriptionedit" placeholder="Task Description..." id="edit_taskdescription"></textarea>
                                </td>
                                <td>
                                    <div class="input-group subtaskdateest">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="estsubtaskdateedit"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="edit_estsubtaskdate" class="form-control editestsubtaskdate"
                                               required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group subtaskdateestend">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="estsubtaskdateendedit"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="edit_estsubtaskdateend" class="form-control editestsubtaskdateend"
                                               required>
                                    </div>
                                </td>
                                <td><input type="text" name="indaysedit" onfocus="this.select();" class="form-control indays number" value="1" id="edit_indays" readonly></td>
                                <td><input type="text" style="width: 100%;" class="form-control inhrs" name="inhrsedit" placeholder="HH" id="inhrs_edit" /> <input type="hidden" name="assign[]"  class="assign-cls"></td>
                                <td><input type="text" style="width: 100%;" class="form-control inmns" name="inmnsedit" placeholder="MM" id="inmns_edit" onkeyup="cheack_minutes_count(this)" /></td>

                                <td class="assigneeapp"><?php echo form_dropdown('employeessubtaskedit[]', $employees_arr, '', 'class="form-control select2 employeessubtask" id="edit_employeessubtask"   multiple="" style="z-index: 0;"'); ?></td>
                                <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                            </tr>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                        <button class="btn btn-primary" type="button" onclick="update_subtask_details_edit()">Update
                            changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="sub_task_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="ap_closed_user_label">Sub Task Status</h4>
                </div>
                <?php echo form_open('', 'role="form" id="sub_task_status_frm"'); ?>
                <div class="modal-body">
                    <input type="hidden" id="subtaskID" name="subtaskID">
                    <input type="hidden" id="TaskID" name="TaskID">

                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-sm-3 col-md-offset-1">
                            <label class="title">Status</label>
                        </div>
                        <div class="form-group col-sm-6">
                <span class="input-req" title="Required Field">
                   <?php echo form_dropdown('statussubtask', array('' => 'Select Status', '0' => 'Not Started', '1' => 'On going', '2' => 'Completed'), '', 'class="form-control statusmaintenace select2" id="statussubtask"'); ?>
                    <span class="input-req-inner"></span>
                </span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="save_sub_task_status_edit()" class="btn btn-sm btn-primary" id="save_btn_status"><span
                            class="glyphicon glyphicon-floppy-disk"
                            aria-hidden="true"></span> Save
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}
?>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-slider-master/dist/bootstrap-slider.min.js'); ?>"></script>

<script type="text/javascript">
    var Otable;
    var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
    $(document).ready(function () {
       

        $("#taskcomment").wysihtml5();
        $('.select2').select2();
        $('#task_view_comment_form').bootstrapValidator({
            live: 'enabled',
            message: 'This value is not valid.',
            excluded: [':disabled'],
            fields: {
                //campaign_name: {validators: {notEmpty: {message: 'Campaign Name is required.'}}},
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
                url: "<?php echo site_url('Crm/update_task_edit_view_comment'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {
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

    });

    function task_multiple_attachemts() {
        var taskID = <?php echo $header['taskID'] ?>;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {taskID: taskID},
            url: "<?php echo site_url('crm/load_task_multiple_attachemts'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#task_multiple_attachemts').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function sub_task_view_assignee() {
        var taskID = <?php echo $header['taskID'] ?>;
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {taskID: taskID},
            url: "<?php echo site_url('crm/load_subtask_detail_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#sub_task_view_assignee').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }


    function assign_validation_start_edit(subtaskid,taskid,createdUserID,type) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {subtaskid:subtaskid,taskid:taskid},
            url: "<?php echo site_url('Crm/load_subtask_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data['assignpermission'])) {
                    if ((data['assignpermission'] == 1) || ('<?php echo $admin['isSuperAdmin']?>' == 1) || ('<?php echo $isgroupadmin['adminYN']?>' == 1) || (createdUserID == '<?php echo $current_userid?>')) {
                        start_sub_task_edit(subtaskid,taskid,type)
                    }else
                    {
                        myAlert('w','You donot have permission to start this subtask')
                    }
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }
    function start_sub_task_edit(subtaskid,taskid,type) {



        swal({
                title: "Are you sure?",
                text: "You want to start this subtask",
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
                    data: {subtaskid: subtaskid, taskid: taskid},
                    url: "<?php echo site_url('crm/start_subtask'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1],data[2]);
                        if (data[0]=='s') {
                            sub_task_view_assignee();
                            start_resume(subtaskid);

                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

    }
    function assign_validation_stop_edit(subtaskid,taskid,sessionID,createdUserID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {subtaskid:subtaskid,taskid:taskid},
            url: "<?php echo site_url('Crm/load_subtask_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data['assignpermission'])) {
                    if ((data['assignpermission'] == 1) || ('<?php echo $admin['isSuperAdmin']?>' == 1) || ('<?php echo $isgroupadmin['adminYN']?>' == 1) || (createdUserID == '<?php echo $current_userid?>')) {
                        stop_sub_task_edit(subtaskid,taskid,sessionID);
                    }else
                    {
                        myAlert('w','You donot have permission to stop this subtask')
                    }
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }
    function stop_sub_task_edit(subtaskid,taskid,sessionID)
    {
        swal({
                title: "Are you sure?",
                text: "You want to stop this subtask",
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
                    data: {subtaskid: subtaskid, taskid: taskid,subtasksession:sessionID},
                    url: "<?php echo site_url('crm/stop_subtask'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        if (data[0]=='s') {
                            sub_task_view_assignee();
                            stop_stopwatch();
                        }
                    },
                    error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });
    }
    function chat_box_subtask_edit(subTaskID,taskID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'subTaskID':subTaskID,'taskID':taskID},
            url: "<?php echo site_url('Crm/load_subtask_chats_edit'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#sub_task_chat').html(data);
                $("#sub_task_chat_model").modal({backdrop: "static"});
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }
    function sub_task_attachment_model_edit(subTaskID)
    {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'subTaskID': subTaskID},
            url: "<?php echo site_url('Crm/attachment_subTask'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#sub_task_attachment').html(data);
                $("#sub_task_attachment_model").modal({backdrop: "static"});
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function edit_subtask_edit(subTaskID,taskID)
    {

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
                    data: {'subTaskID': subTaskID,'taskID':taskID},
                    url: "<?php echo site_url('Crm/crm_sub_task_detail_edit'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        hoursNew =  Math.floor(data['estimatedHours'] / 60);
                        minutesnew = (data['estimatedHours'] % 60);

                        $('#subtaskAutoid').val(subTaskID);
                        $('#taskautoid').val(taskID);
                        $('#inhrs_edit').val(hoursNew);
                        $('#inmns_edit').val(minutesnew);
                        $('#edit_estsubtaskdate').val(data['startdateSubtask']);
                        $('#edit_estsubtaskdateend').val(data['enddateSubtask']);
                        $('#edit_indays').val(data['estimatedDays']);
                        $('#edit_taskdescription').val(data['taskDescription']);
                        fetch_assignees_subtask(subTaskID)
                        $("#edit_task_subtask_details_modal").modal({backdrop: "static"});
                        stopLoad();

                        //refreshNotifications(true);
                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Try Again ", "error");
                    }
                });
            });
    }
    function fetch_assignees_subtask(subTaskID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'MasterAutoID': subTaskID},
            url: "<?php echo site_url('Crm/fetch_tasks_employee_detailsubtask'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    var selectedItems = [];
                    $.each(data, function (key, value) {
                        selectedItems.push(value['empID']);
                        $('#edit_employeessubtask').val(selectedItems).change();
                    });
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function update_subtask_details_edit() {
        var data = $('#edit_sub_task_frm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Crm/update_subtaask_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    sub_task_view_assignee();
                    $('#edit_task_subtask_details_modal').modal('hide');
                    $('#edit_sub_task_frm')[0].reset();
                    $("#edit_employeessubtask").val(null).trigger("change");
                    /*commitmentDetailAutoID = null;
                     getDispatchDetailAddonCost_tableView(commitmentAutoId);
                     $('#edit_rv_income_detail_modal').modal('hide');
                     $('#edit_rv_income_detail_form')[0].reset();
                     $('.select2').select2('')*/

                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function load_sub_task_status_edit(subTaskID,taskID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'subTaskID': subTaskID,taskID:taskID},
            url: "<?php echo site_url('Crm/load_subtsk_status'); ?>",
            success: function (data) {
                if (data) {
                    $('#subtaskID').val(subTaskID);
                    $('#TaskID').val(taskID);
                    $('#statussubtask').val(data['status']).change();
                    $('#sub_task_model').modal('show');
                };
            }
        });

    }
    function save_sub_task_status_edit() {
        var data = $("#sub_task_status_frm").serializeArray();
        swal({
                title: "Are you sure?",
                text: "You want to Change the Status!",
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
                    data: data,
                    url: "<?php echo site_url('Crm/save_subTask_status'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            sub_task_view_assignee();
                            load_task_header();
                            $('#sub_task_model').modal('hide');
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
    }



    function check_edit_approval(){

      <?php if($masterID == 'dashboardtask'){?>
        if(<?php echo $this->common_data['current_userID']?>==<?php if(!empty($superadmn)){ echo $superadmn['isadmin'];}else{echo 000;}  ?>){
            fetchPage('system/crm/create_new_task',<?php echo $header['taskID'] ?>,'Edit Task','dashboardtask','dashboardtask');
        }else if(<?php echo $this->common_data['current_userID']?>==<?php echo $header['crtduser'] ?>){
            fetchPage('system/crm/create_new_task',<?php echo $header['taskID'] ?>,'Edit Task','dashboardtask','dashboardtask');
        }else if(<?php echo $tskass ?>==1){
            fetchPage('system/crm/create_new_task',<?php echo $header['taskID'] ?>,'Edit Task','dashboardtask','dashboardtask');
        }else{
            myAlert('w','You do not have permission to edit this task')
        }
        <?php } else {?>
        if(<?php echo $this->common_data['current_userID']?>==<?php if(!empty($superadmn)){ echo $superadmn['isadmin'];}else{echo 000;}  ?>){
            fetchPage('system/crm/create_new_task',<?php echo $header['taskID'] ?>,'Edit Task','CRM','CRM');
        }else if(<?php echo $this->common_data['current_userID']?>==<?php echo $header['crtduser'] ?>){
            fetchPage('system/crm/create_new_task',<?php echo $header['taskID'] ?>,'Edit Task','CRM','CRM');
        }else if(<?php echo $tskass ?>==1){
            fetchPage('system/crm/create_new_task',<?php echo $header['taskID'] ?>,'Edit Task','CRM','CRM');
        }else{
            myAlert('w','You do not have permission to edit this task')
        }
    <?php }?>

    }

    function create_sub_task()
    {
        $('#sub_task_addform')[0].reset();
        $('#subTask_add_table tbody tr').not(':first').remove();
        $(".employeessubtask").val(null).trigger("change");
        $("#sub_task_add_item_modal").modal({backdrop: "static"});

    }

    function calculatesubtask(element) {
        var startDate = moment($(element).closest('tr').find('.estsubtaskdate').val(), "DD.MM.YYYY");
        var endDate = moment($(element).closest('tr').find('.estsubtaskdateend').val(), "DD.MM.YYYY");

        var days = endDate.diff(startDate, 'days');
        var formattedDate = days + 0;
        if(startDate!='' && endDate!='')
        {
            $(element).closest('tr').find('.indays').val(formattedDate)
        }else
        {
            $(element).closest('tr').find('.indays').val(0)
        }
    }
    $('.datetimepicker4').datetimepicker({
            useCurrent: false,
            format : 'HH:mm'
         /*   widgetPositioning: {
                vertical: 'top'
            }*/
        });
        $('.subtaskdateest').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {

            calculatesubtask(this);
            subtaskdatevalidation(this);
        });

        $('.subtaskdateestend').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            calculatesubtask(this);
            subtaskdatevalidationEnd(this);
        });


        function save_sub_task_assignee()
    {
        $(".employeessubtask").each(function(){
            empsubtask = $(this).val();
            $(this).parent().parent().find('.assign-cls').val(empsubtask);

        });
        var data = $('#sub_task_addform').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data ,
            url: "<?php echo site_url('Crm/AddSubTaskDetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1],data[2]);
                if (data[0] == 's') {
                  // $("#description").wysihtml5();
                  
                    subtaskview();
                    $('#progress').slider('setValue',data[2]);
                    $('#progress').slider({}).slider('disable');
                    $('#sub_task_add_item_modal').modal('hide');
                }
                stopLoad();
                refreshNotifications(true);
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }
    function cheack_hours_count(element)
    {
        if(element.value > 24)
        {
           myAlert('w','Hours cannot be greater than 24')
           $(element).closest('tr').find('.inhrs').val('');
        }

    }
    function cheack_minutes_count(element)
    {
        if(element.value > 59)
        {
            myAlert('w','Minutes cannot be greater than 59')
            $(element).closest('tr').find('.inmns').val('');
        }

    }
    function validateFloatKeyPress(el, evt,currency_decimal=3) {
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

    

    function add_more_subtask() {
        var appendData = $('#subTask_add_table tbody tr:first').clone();
        //$('select.select2').select2('destroy');
        appendData.find('input').val('');
        appendData.find('textarea').val('');

        var str = '<select name="employeessubtask[]" class="form-control select2 employeessubtask" multiple="" style="z-index: 0;">';
        str += $('#tempAssign').html();
        str += '</select>';
        appendData.find('.assigneeapp').html(str);
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#subTask_add_table').append(appendData);
        var lenght = $('#subTask_add_table tbody tr').length - 1;
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        $('.subtaskdateest').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            calculatesubtask(this);
            subtaskdatevalidation(this);


        });

        $('.subtaskdateestend').datetimepicker({
            useCurrent: false,
            format: date_format_policy,
        }).on('dp.change', function (ev) {
            calculatesubtask(this);
            subtaskdatevalidationEnd(this);
        });

        $('.datetimepicker4').datetimepicker({
            useCurrent: false,
            format : date_format_policy,
           /* widgetPositioning: {
                vertical: 'top'
            }*/
        }).on('dp.change', function (ev) {
            //  $('#jp_detail_add_table').bootstrapValidator('revalidateField', 'departtimecls');
        });

        number_validation();
        Inputmask().mask(document.querySelectorAll("input"));
        $(".select2").select2();
    }

    function subtaskdatevalidation(element)
    {
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        var startdatemainttask = moment($("#mainstartdate").val(), "DD.MM.YYYY");
        var duedatemainttask = moment($("#DueDate").val(), "DD.MM.YYYY");
        var startdatesubtask  = moment($(element).closest('tr').find('.estsubtaskdate').val(), "DD.MM.YYYY");

        var startDate = $(element).closest('tr').find('.estsubtaskdate').val();

        var startdatesub = $(element).closest('tr').find('.estsubtaskdate').val();
        var enddatesub = $(element).closest('tr').find('.estsubtaskdateend').val();
        if(enddatesub = null)
        {

            if (startdatesub > enddatesub) {
                myAlert('w','Est. Start Date cannot be greater than Est. End Date');
                $(element).closest('tr').find('.estsubtaskdate').val('');

            }
        }

        if(startdatesubtask)
            {
                if(startdatesubtask > duedatemainttask)
                {
                    $(element).closest('tr').find('.estsubtaskdate').val('');
                    $(element).closest('tr').find('.indays').val(0);
                    myAlert('w','Est. Start Date cannot be greater than Task Due Date');


                  /*  $('.subtaskdateest').datetimepicker('remove');*/
                  /*  $('.subtaskdateest').datetimepicker('setStartDate', null);*/
                }
            }
       else
            {
        if(startDate < startdatemainttask)
        {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taskID':taskID,'startDate':startDate},
                url: "<?php echo site_url('Crm/start_date_est_date_validation'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data == 1) {

                    } else {
                        myAlert('w','Estimated start Date Cannot be less than Task Start Date');
                        $(element).closest('tr').find('.estsubtaskdate').val('');
                        $(element).closest('tr').find('.indays').val(0);
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
    // alert(taskID);
    }

        function subtaskdatevalidationEnd(element)
    {
        var endstartDate = moment($(element).closest('tr').find('.estsubtaskdateend').val(), "DD.MM.YYYY");
        var startdatesub = moment($(element).closest('tr').find('.estsubtaskdate').val(), "DD.MM.YYYY");
        var enddatesub = $(element).closest('tr').find('.estsubtaskdateend').val();

        if (startdatesub > endstartDate) {
            myAlert('w','Est.End Date cannot be less than Est.Start Date');
            $(element).closest('tr').find('.estsubtaskdateend').val('');
            $(element).closest('tr').find('.indays').val(0);
        }else
        {
            if(startdatesub)
            {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'taskID':taskID,'endstartDate':endstartDate},
                    url: "<?php echo site_url('Crm/end_date_est_date_validation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == 1) {

                        } else {
                            myAlert('w','Estimated End Date Cannot be greater than Task Due Date');
                            $(element).closest('tr').find('.estsubtaskdateend').val('');
                            $(element).closest('tr').find('.indays').val(0);
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



        // alert(taskID);
    }


    function assign_validation_start(subtaskid,taskid,createdUserID,type) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {subtaskid:subtaskid,taskid:taskid},
            url: "<?php echo site_url('Crm/load_subtask_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data['assignpermission'])) {
                    if ((data['assignpermission'] == 1) || ('<?php echo $admin['isSuperAdmin']?>' == 1) || ('<?php echo $isgroupadmin['adminYN']?>' == 1) || (createdUserID == '<?php echo $current_userid?>')) {
                        start_sub_task(subtaskid,taskid,type)
                    }else
                    {
                        myAlert('w','You donot have permission to start this subtask')
                    }
                }
                stopLoad();
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });

    }





</script>


