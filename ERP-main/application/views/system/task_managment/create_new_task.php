<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('crm', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->load->helper('task_helper');
echo head_page($_POST['page_name'], false);
$date_format_policy = date_format_policy();
$newpolicy_date = strtoupper($date_format_policy);
$current_date = current_format_date();
$status_arr = all_task_status();
$categories_arr = load_all_categories();
$types_arr = all_campaign_types();
$employees_arr = fetch_employees_by_company_multiple(false);
$employees_arr_group = fetch_employees(false);
$groupmaster_arr = all_crm_groupMaster();
$isgroupadmin = crm_isGroupAdmin();
$admin = crm_isSuperAdmin();
$assign_task_drop = assign_task_department();
$current_userid = current_userID();
$date_format_policy = date_format_policy();
$current_date = current_format_date();
?>
<div id="filter-panel" class="collapse filter-panel"></div>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/tabs.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/bootstrap/css/build.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/crm_style.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>"/>
<link rel="stylesheet"
      href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/custom_style_web.css'); ?>">
<link rel="stylesheet" href="<?php echo base_url('plugins/css/autocomplete-suggestions.css'); ?>"/>
<style>
    .title {
        float: left;
        width: 170px;
        text-align: right;
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

    .history {
        display: block;
        margin-top: 20px;
        padding: 10px;
        border: 1px solid #ccc;
        background-color: white;
        height: 350px;
    }

    .navigation-buttons a {
        display: inline-block;
        padding: 8px 16px;
        border: 2px;
        border-radius: 4px;
        margin-right: 10px;
        color: black;
        text-decoration: none;
    }

    .navigation-buttons a:hover {
        background-color: whitesmoke;
        color: black;
    }

    .msg_history {
        height: 320px;
        overflow-y: auto;
    }

    .incoming_msg_img {
        display: inline-block;
        width: 6%;
    }

    .chat_img {
        float: left;
        width: 11%;
    }

    img {
        max-width: 100%;
    }


    .time_date {
        color: #747474;
        display: block;
        font-size: 12px;
        margin: 8px 0 0;
    }

    .outgoing_msg_history {
        overflow: hidden;
        margin: 0px 0 0px;
    }

    .sent_msg_task_employee {
        padding-left: 9%;
        width: 100%;

    }


    .sent_msg_task_employee p {
        background: #e7e7e7 none repeat scroll 0 0;
        border-radius: 8px;
        font-size: 16px;
        margin: 10px;
        color: #000000;
        padding: 10px 12px 7px 16px;
        width: 95%;
    }

    .sent_msg_task_employee_detis {
        padding-left: 9%;
        width: 100%;
    }

    .detialsval {
        background: #e7e7e7 none repeat scroll 0 0;
        border-radius: 5px;
        font-size: 16px;
        margin: 10px;
        color: #000000;
        padding: 10px 12px 7px 14px;
        width: 90%;
    }


</style>

<div class="m-b-md" id="wizardControl">

    <div class="steps">
        <a class="step-wiz step--incomplete step--active" href="#step1" data-toggle="tab"
           onclick="load_task_header();">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('crm_step_one'); ?> - <?php echo $this->lang->line('crm_task_header'); ?></span>
        </a>

        <a class="step-wiz step--incomplete step--inactive btn-wizard subtasktabehide " id="subtask" href="#step2"
           onclick="subtaskview();" data-toggle="tab">
            <span class="step__icon"></span>
            <span class="step__label"><?php echo $this->lang->line('crm_step_two'); ?> - <?php echo $this->lang->line('crm_task_subtask'); ?></span>
        </a>

    </div>

</div>
<hr>
<div class="tab-content create-task-outline">
    <div id="step1" class="tab-pane active">
        <div class="row">
            <?php echo form_open('', 'role="form" id="task_header_form"'); ?>
            <div class="row col-md-12">

                <div class="col-md-7 ">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('crm_task_details'); ?></h2><!--Task Details-->
                    </header>
                    <div class="row">
                        <div class="form-group " style="margin-top: 10px;">

                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label class="title"><?php echo $this->lang->line('crm_task_subject'); ?></label>
                            <!--Task Subject-->
                        </div>
                        <div class="form-group col-md-6" style="margin-top: 10px;">
                    <span class="input-req" title="Required Field"><input type="text" name="subject" id="subject"
                                                                          class="form-control" required><span
                                class="input-req-inner"></span></span>
                            <input type="hidden" name="taskID" id="taskID_edit">
                            <input type="hidden" name="projectID" id="projectID">
                            <input type="hidden" name="opportunityID" id="opportunityID">
                            <input type="hidden" name="pipelineStageID" id="pipelineStageID">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group" style="margin-top: 10px;">

                        </div>
                        <div class="form-group ">
                            <label class="title"><?php echo $this->lang->line('common_category'); ?></label>
                            <!--Category-->
                        </div>
                        <div class="form-group col-md-6">
                    <span class="input-req"
                          title="Required Field"><?php echo form_dropdown('categoryID', $categories_arr, '', 'class="form-control" id="categoryID" onchange="loaddays()" required'); ?>
                        <span class="input-req-inner"></span></span>
                        </div>
                    </div>


                    <!-- <div class="row" style="display: none; margin-left:3px; " id="view2" >
                
                        <div id="append_related_data" >
                            <div class="append_data">
                                <div class="row" >
                                    <div class="form-group" >
                                
                                        <label class="title" > Department</label>
                                    </div>
                                    <div class="form-group col-md-6"  >
                                    </*?php echo form_dropdown('departmentid', $assign_task_drop, '', 'class="form-control" id="departmentid"  required'); ?>
                                    </div>
                                    
                                    <div class="form-group remove-td" style="margin-top: 10px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="row">
                        <div class="form-group col-md-12" style="margin-top: 15px;">
                            <span class="input-req" title="Required Field">
                                <textarea class="form-control" rows="8" name="description" id="description"></textarea>
                                <span class="input-req-inner" style="top: 25px; height:50px;"></span>
                            </span>
                        </div>
                    </div>

                    <input type="hidden" class="form-control" name="description" id="description_edit">
                    <div class="row">
                        <div class="text-right m-t-xs">
                            <div class="form-group col-md-12" style="margin-top: 10px;">
                                <button class="btn btn-primary-new size-lg"
                                        type="submit"><?php echo $this->lang->line('common_save'); ?></button>
                                <!--Save-->
                            </div>
                        </div>
                    </div>
                </div>

                <!--ADDITIONAL INFORMATION START-->
                <div class="col-md-5" style="border-left: 2px solid #d3dbd7;">
                    <header class="head-title">
                        <h2><?php echo $this->lang->line('crm_additional_information'); ?></h2>
                        <!--ADDITIONAL INFORMATION-->
                    </header>
                    <div class="row">
                        <div class="form-group col-md-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2">
                            <label class="title"><?php echo $this->lang->line('crm_progress'); ?></label><!--Progress-->
                        </div>
                        <div class="form-group col-md-6 ">
                            <input id="progress" data-slider-id='ex1Slider' type="text" data-slider-min="0"
                                   data-slider-max="100"
                                   data-slider-step="1" data-slider-value="0" name="progress"/>
                        </div>
                    </div>

                    <!--Priority Start-->
                    <div class="row">
                        <div class="form-group col-md-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2">
                            <label class="title"><?php echo $this->lang->line('crm_priority'); ?></label><!--Priority-->
                        </div>
                        <div class="form-group col-md-6">
                            <div class="textbox dropdown">
                                <button type="button" onclick="prioritycheck(3)" id="highPriority"
                                        class="priority-btn high-ptry tipped-top" title="High Priority">!!!
                                </button>
                                <button type="button" onclick="prioritycheck(2)" id="mediumPriority"
                                        class="priority-btn med-ptry tipped-top active" title="Medium Priority">!!
                                </button>
                                <button type="button" onclick="prioritycheck(1)" id="lowPriority"
                                        class="priority-btn low-ptry tipped-top" title="Low Priority">!
                                </button>
                                <input id="taskPriority" name="priority" type="hidden" value="2">
                            </div>
                        </div>
                    </div>
                    <!--Priority End-->

                    <!--Status Start-->
                    <div class="row">
                        <div class="form-group col-md-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2">
                            <label class="title"><?php echo $this->lang->line('common_status'); ?></label><!--Status-->
                        </div>
                        <div class="form-group col-md-6">
                            <span class="input-req" title="Required Field">
                                <?php echo form_dropdown('statusID', $status_arr, '', 'class="form-control" id="statusID" onchange="statuscheack(this.value)" '); ?>

                                <span class="input-req-inner"></span>
                            </span>
                        </div>
                    </div>

                    <!--Status End-->

                    <!--Start Date Start-->

                    <div class="row">
                        <div class="form-group col-md-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2">
                            <label class="title"><?php echo $this->lang->line('common_start_date'); ?></label>
                            <!--Start Date-->
                        </div>
                        <div class="form-group col-md-6">
                            <div class="input-group startdateDatepic">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="startdate" value="<?php echo $current_date; ?>" id="startdate"
                                       class="form-control dateFields frm_input"
                                       style="z-index:1 !important">
                            </div>
                            <span class="input-req-inner" style="z-index: 100;"></span>
                        </div>
                    </div>
                    <!--Start Date End-->

                    <!--Due Date and Is sub task Start-->
                    <div class="row">
                        <div class="form-group col-md-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2">
                            <label class="title"><?php echo $this->lang->line('crm_due_date'); ?></label><!--Due Date-->
                        </div>
                        <div class="form-group col-md-6">
                                    <span class="input-req">
                                    <div class="input-group duedateDatepic">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <input type="text" name="duedate"
                                               data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                               value="<?php echo $current_date; ?>" id="duedate"
                                               class="form-control dateFields frm_input" style="z-index:1 !important">
                                    </div>
                                        <span class="input-req-inner" style="z-index: 100;"></span></span>
                        </div>

                    </div>
                    <!--Due Date and Is sub task End-->
                    <div class="row hide">
                        <div class="form-group col-md-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2" style="padding-right: 0px;">
                            <label class="title">Is SubTask</label>
                        </div>
                        <div class="form-group col-md-4" style="padding-left: 0px;">
                            <div class="col-md-1">
                                <div class="skin skin-square">
                                    <div class="skin-section extraColumns"><input id="issubtask" type="checkbox"
                                                                                  data-caption=""
                                                                                  class="columnSelected issubtaskcls"
                                                                                  name="issubtask" value="1"
                                                                                  checked><label
                                                for="checkbox">&nbsp;</label></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Close Date Start-->
                    <div class="row closedatehideshow hide">
                        <div class="form-group col-md-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2">
                            <label class="title">Close Date</label><!--Close Date-->
                        </div>
                        <div class="form-group col-md-4">
                                    <span class="input-req" title="Required Field">
                                        <div class="input-group dateDatepic">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" name="closedate"
                                                   data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                                   value="<?php echo $current_date; ?>" id="closedate"
                                                   class="form-control" required>
                                        </div>
                                        <span class="input-req-inner" style="z-index: 100;"></span></span>
                        </div>
                    </div>
                    <!--Close Date End-->

                    <!--Assignee Start-->
                    <div class="row">
                        <div class="form-group col-md-1" style="margin-top: 10px;">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2" style="margin-top: 10px;">
                            <label class="title"><?php echo $this->lang->line('crm_assignee'); ?></label><!--Assignee-->
                        </div>
                        <div class="form-group col-md-6"
                             style="margin-top: 5px;"><?php echo form_dropdown('employees[]', $employees_arr_group, '', 'class="form-control select2" id="employeesID"  multiple="" style="z-index: 0;" required'); ?>
                        </div>
                    </div>
                    <!--Assignee End-->

                    <!--Visibility Start-->
                    <div class="row" style="margin-top: 10px;">
                        <div class="form-group col-md-1">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2">
                            <label class="title"><?php echo $this->lang->line('crm_visibility'); ?></label>
                            <!--Visibility-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-1">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2">
                            <label class="title"></label>
                        </div>
                        <div class="form-group col-md-1">
                            <div class="iradio_square-blue">
                                <div class="skin-section extraColumns"><input name="userPermission"
                                                                              id="isPermissionMultiple"
                                                                              type="radio"
                                                                              data-caption="" class="columnSelected"
                                                                              onclick="leadPermission(4)"
                                                                              value="4"><label
                                            for="checkbox">&nbsp;</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4" style="margin-left: -6%;">
                            <label style="font-weight: 400"><?php echo $this->lang->line('crm_select_multiple_pepole'); ?></label>
                            <!--Select Multiple People-->
                        </div>
                    </div>
                    <div class="row hide" id="show_multiplePermission">
                        <div class="form-group col-md-1">
                            &nbsp
                        </div>
                        <div class="form-group col-md-2">
                            <label class="title"></label>
                        </div>
                        <div class="form-group col-md-6" style="margin-left: 2%;">
                            <?php echo form_dropdown('multipleemployees[]', $employees_arr, '', 'class="form-control select2" id="multipleemployeesID"  multiple="" style="z-index: 0;"'); ?>
                        </div>
                    </div>

                    <!--Visibility End-->
                </div>

                <!--ADDITIONAL INFORMATION END-->
                <br>
            </div>

            <div class="row col-md-12" id="attsection" style="display: none;">
                <div class="col-md-6">
                    <div class="row">
                        <div class="form-group navigation-buttons" style="margin-top: 10px;">
                            <a href="#" class="navigation-link" data-section="comment"
                               onclick="chat_box_task_comment()">COMMENT</a>
                            <a href="#" class="navigation-link" data-section="history" onclick="history_detais_load()">HISTORY</a>
                            <a href="#" class="navigation-link" data-section="attachments"
                               onclick="task_multiple_attachemts();">ATTACHMENTS</a>
                        </div>

                        <div id="comment-section" class="section" style="display: none;">

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-12" id="task_chat_comment">
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div id="history-section" class="section history" style="display: none;">
                            <div class="mesgs_history" style="height:350px;">
                                <div class="msg_history">
                                    <div class="outgoing_msg_history">
                                        <div class="sent_msg_task_employee">
                                            <div class="created_info">
                                                <!-- Placeholder for created by and created at -->
                                            </div>

                                        </div>

                                        <div class="sent_msg_task_employee_detis">

                                            <div class="change_details">
                                                <!-- Placeholder for change details -->
                                            </div>
                                        </div>


                                    </div>
                                    <br>
                                    <br>
                                </div>
                            </div>
                        </div>


                        <div id="attachments-section" class="section" style="display: none;">
                            <div class="row">
                                <div class="col-md-8"><h4><i
                                                class="fa fa-hand-o-right"></i> <?php echo $this->lang->line('crm_task_attachments'); ?>
                                    </h4></div><!--Task Attachments-->
                                <div class="col-md-4">
                                    <button type="button" onclick="show_task_button_cmm()"
                                            class="btn btn-primary pull-right"><i
                                                class="fa fa-plus"></i><?php echo $this->lang->line('crm_add_attachment'); ?>
                                    </button><!--Add Attachment-->
                                </div>
                            </div>

                            <div class="row hide" id="add_attachemnt_show">
                                <!-- Attachment form HTML -->
                                <?php echo form_open_multipart('', 'id="task_attachment_uplode_form" class="form-inline"'); ?>
                                <div class="col-md-10" style="margin-left: 3%">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="taskattachmentDescription"
                                                   name="attachmentDescription" placeholder="Description..."
                                                   style="width: 240%;">
                                            <input type="hidden" class="form-control" id="documentID" name="documentID"
                                                   value="2">
                                            <input type="hidden" class="form-control" id="campaign_document_name"
                                                   name="document_name"
                                                   value="Task">
                                            <input type="hidden" class="form-control" id="task_documentAutoID"
                                                   name="documentAutoID">
                                        </div>
                                    </div>

                                    <div class="col-md-8" style="margin-top: -8px;">
                                        <div class="form-group">
                                            <div class="fileinput fileinput-new input-group" data-provides="fileinput"
                                                 style="margin-top: 8px;">
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">
                                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                            </span>
                                            <span class="fileinput-exists">
                                                <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>
                                            </span>
                                            <input type="file" name="document_file" id="document_file"
                                                   onchange="truncateFilename()">
                                        </span>
                                                <div class="form-control" data-trigger="fileinput"
                                                     style="flex: 1; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;">
                                                    <i class="glyphicon glyphicon-file color fileinput-exists"
                                                       style="margin-right: 5px;"></i>
                                                    <span class="fileinput-filename"></span>
                                                </div>
                                                <a class="input-group-addon btn btn-default fileinput-exists"
                                                   id="remove_id" data-dismiss="fileinput">
                                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                </a>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-default" onclick="document_uplode()">
                                            <span class="glyphicon glyphicon-floppy-open color"
                                                  aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="task_multiple_attachemts_cmm"></div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>


    <!-- sub task side view -->

    <div id="step2" class="tab-pane">
        <div class="row">
            <div class="col-md-12 ">
                <header class="head-title">
                    <h2>SUB TASK</h2>
                </header>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary pull-right subtsk"
                                onclick="create_sub_task()" id="beneficiaryassign">
                            <i class="fa fa-plus"></i> Create Sub Task
                        </button>
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12">
                        <div id="subtrskview"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
</form>


<div aria-hidden="true" role="dialog" id="sub_task_add_item_modal" class="modal fade"
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
                    <table class="table table-bordered table-condensed no-color" id="subTask_add_table">
                        <thead>
                        <tr>
                            <th style="width: 150px;">Task Description<?php required_mark(); ?></th>
                            <th style="width: 150px;">Est. Start Date <?php required_mark(); ?></th>
                            <th style="width: 150px;">Est.End Date</th>
                            <!-- <th style="width: 150px;">In Days<?php required_mark(); ?></th> -->
                            <th colspan="2">In Hours<?php required_mark(); ?></th>
                            <th style="width: 150px;">Assignee</th>
                            <th style="width: 40px;">
                                <button type="button" class="btn btn-primary btn-xs" onclick="add_more_subtask()"><i
                                            class="fa fa-plus"></i></button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <textarea class="form-control" rows="1" name="Taskdescription[]"
                                          placeholder="Task Description..."></textarea>
                            </td>
                            <td>
                                <div class="input-group subtaskdateest">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="estsubtaskdate[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" id="estsubtaskdate" class="form-control estsubtaskdate"
                                           required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group subtaskdateestend">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="estsubtaskdateend[]"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="" id="estsubtaskdateend" class="form-control estsubtaskdateend"
                                           required>
                                </div>
                            </td>
                            <!-- <td><input type="text" name="indays[]" onfocus="this.select();" class="form-control indays number" value=" " id="indays" value="0" readonly></td> -->
                            <td><input type="num" style="width: 100%;" class="form-control inhrs number " name="inhrs[]"
                                       placeholder="HH" onkeypress="return validateFloatKeyPress(this,event)"/> <input
                                        type="hidden" name="assign[]" class="assign-cls"></td>
                            <td><input type="text" style="width: 100%;" class="form-control inmns number "
                                       name="inmns[]" placeholder="MM" onkeyup="cheack_minutes_count(this)"
                                       onkeypress="return validateFloatKeyPress(this,event)"/></td>

                            <td class="assigneeapp"><?php echo form_dropdown('employeessubtask[]', $employees_arr, '', 'class="form-control select2 employeessubtask" id="employeessubtask"  multiple="" style="z-index: 0;"'); ?></td>
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
<div class="modal fade bs-example-modal-sm" role="dialog" aria-labelledby="myLargeModalLabel"
     id="subtask_stop_resume_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usergroup-title"></h4>
            </div>
            <?php echo form_open('', 'role="form" id="usergroup_master_form"'); ?>
            <div class="modal-body">
                <input type="hidden" id="userGroupID" name="userGroupID">
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-md-3 col-md-offset-1">
                        <label class="title">Description</label>
                    </div>
                    <div class="form-group col-md-6">
                <span class="input-req" title="Required Field">
                    <input type="text" class="form-control " id="description" name="description" required>
                    <!--<input type="text" name="partNumber" id="partNumber" class="form-control" placeholder="Part No" >-->
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-md-offset-1">
                        <label class="title">Is Active</label>
                    </div>
                    <div class="form-group col-md-6">
                        <div class="skin skin-square item-iCheck">
                            <div class="skin-section extraColumns"><input id="IsActive" type="checkbox"
                                                                          class="IsActive" value="1"><label
                                        for="checkbox">&nbsp;</label></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3 col-md-offset-1">
                        <label class="title">Is Default</label>
                    </div>
                    <div class="form-group col-md-6">
                        <div class="skin skin-square item-iCheck">
                            <div class="skin-section extraColumns"><input id="Isdefault" type="checkbox"
                                                                          class="Isdefault" value="1"><label
                                        for="checkbox">&nbsp;</label></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-floppy-disk"
                                                                               aria-hidden="true"></span> Save
                    </button>
                </div>
                </form>
            </div>
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
                            <div class="col-md-12" id="sub_task_chat">

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
                            <div class="col-md-12" id="sub_task_attachment">

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
                    <div class="form-group col-md-3 col-md-offset-1">
                        <label class="title">Status</label>
                    </div>
                    <div class="form-group col-md-6">
                <span class="input-req" title="Required Field">
                   <?php echo form_dropdown('statussubtask', array('' => 'Select Status', '0' => 'Not Started', '1' => 'On going', '2' => 'Completed'), '', 'class="form-control statusmaintenace select2" id="statussubtask"'); ?>
                    <span class="input-req-inner"></span>
                </span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="save_sub_task_status()" class="btn btn-sm btn-primary"
                        id="save_btn_status"><span
                            class="glyphicon glyphicon-floppy-disk"
                            aria-hidden="true"></span> Save
                </button>
            </div>
            </form>
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
                                <textarea class="form-control" rows="1" name="Taskdescriptionedit"
                                          placeholder="Task Description..." id="edit_taskdescription"></textarea>
                            </td>
                            <td>
                                <div class="input-group subtaskdateest">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="estsubtaskdateedit"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="edit_estsubtaskdate"
                                           class="form-control estsubtaskdate"
                                           required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group subtaskdateestend">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input type="text" name="estsubtaskdateendedit"
                                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'"
                                           value="<?php echo $current_date; ?>" id="edit_estsubtaskdateend"
                                           class="form-control estsubtaskdateend"
                                           required>
                                </div>
                            </td>
                            <td><input type="text" name="indaysedit" onfocus="this.select();"
                                       class="form-control indays number" value="1" id="edit_indays" readonly></td>
                            <td><input type="text" style="width: 100%;" class="form-control inhrs number"
                                       name="inhrsedit" placeholder="HH" id="inhrs_edit"
                                       onkeypress="return validateFloatKeyPress(this,event)"/> <input type="hidden"
                                                                                                      name="assign[]"
                                                                                                      class="assign-cls">
                            </td>
                            <td><input type="text" style="width: 100%;" class="form-control inmns number"
                                       name="inmnsedit" placeholder="MM" id="inmns_edit"
                                       onkeypress="return validateFloatKeyPress(this,event)"
                                       onkeyup="cheack_minutes_count(this)"/></td>

                            <td class="assigneeapp"><?php echo form_dropdown('employeessubtaskedit[]', $employees_arr, '', 'class="form-control select2 employeessubtask" id="edit_employeessubtask"   multiple="" style="z-index: 0;"'); ?></td>
                            <td class="remove-td" style="vertical-align: middle;text-align: center"></td>
                        </tr>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                    <button class="btn btn-primary" type="button" onclick="update_subtask_details()">Update
                        changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo base_url('plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-slider-master/dist/bootstrap-slider.min.js'); ?>"></script>
<script type="text/javascript">

    var search_id = 1;

    $(document).ready(function () {
        $('#progress').slider({}).slider('disable');
        var masterID = '<?php if (isset($_POST['data_arr']) && !empty($_POST['data_arr'])) {
            echo json_encode($_POST['data_arr']);
        } ?>';
        var related_document = '<?php if (isset($_POST['policy_id']) && !empty($_POST['policy_id'])) {
            echo $_POST['policy_id'];
        } ?>';
        var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
        Inputmask().mask(document.querySelectorAll("input"));
        if (masterID != null && masterID.length > 0) {
            var masterIDNew = JSON.parse(masterID);
            if (related_document == 5) {
                load_taskRelated_fromLead(masterIDNew);
                $('.headerclose').click(function () {
                    fetchPage('system/crm/lead_edit_view', masterIDNew, 'View Lead', 'LeadTask');
                });
            } else if (related_document == 4) {
                load_taskRelated_fromOpportunity(masterIDNew);
                $('#opportunityID').val(masterIDNew);
                $('#pipelineStageID').val(masterIDNew);
                if (masterIDNew[1] != '') {
                    $('.headerclose').click(function () {
                        fetchPage('system/crm/opportunities_edit_view', masterIDNew, 'View Opportunity', 'OpportunityTask');
                    });
                }

            } else if (related_document == 44) {
                load_taskRelated_fromOpportunity(masterIDNew[0]);
                $('#opportunityID').val(masterIDNew[0]);
                $('#pipelineStageID').val(masterIDNew[1]);
                if (masterIDNew[1] != '') {
                    $('.headerclose').click(function () {
                        fetchPage('system/crm/opportunities_edit_view', masterIDNew[0], 'View Opportunity', ' ');
                    });
                }
            } else if (related_document == 9) {
                load_taskRelated_fromProject(masterIDNew);
                $('#projectID').val(masterIDNew);
                $('#pipelineStageID').val(masterIDNew[1]);
                if (masterIDNew[1] != '') {
                    $('.headerclose').click(function () {
                        fetchPage('system/crm/project_edit_view', masterIDNew, 'View Project', 'projectTask');
                    });
                }
            } else if (related_document == 2) {
                var responseDate = moment(masterID).format('<?php echo $newpolicy_date ?>');
                $('#startdate').val(responseDate);
                $('.headerclose').click(function () {
                    fetchPage('system/crm/dashboard', '', 'CRM', 'dashboardtask');
                });
            } else if (related_document == 'CRM') {
                $('.headerclose').click(function () {
                    fetchPage('system/task_managment/task_managemnt', '', 'Tasks');
                });
            } else if (related_document == 'dashboardtask') {
                $('.headerclose').click(function () {
                    fetchPage('system/crm/dashboard', '', 'CRM', 'dashboardtask');
                });

            }
        } else {
            $('.headerclose').click(function () {
                fetchPage('system/task_managment/task_management', '', 'Tasks');
            });
        }

        $('.extraColumns input').iCheck({
            checkboxClass: 'icheckbox_square_relative-blue',
            radioClass: 'iradio_square_relative-blue',
            increaseArea: '20%'
        });

        $('.select2').select2();


        search_id = 1;

        initializeTaskTypeahead(1);

        $('#progress').slider({
            formatter: function (value) {
                return 'Current value: ' + value + '%';
            }
        }).slider('enable');

        $('.startdateDatepic').datetimepicker({
            showTodayButton: true,
            format: "<?php echo $newpolicy_date ?>",
            sideBySide: true,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            $('#task_header_form').bootstrapValidator('revalidateField', 'startdate');
        });

        $(document).on('click', '.remove-tr', function () {
            $(this).closest('tr').remove();
        });

        $('.duedateDatepic').datetimepicker({
            showTodayButton: true,
            format: "<?php echo $newpolicy_date ?> ",
            sideBySide: true,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        }).on('dp.change', function (ev) {
            $('#task_header_form').bootstrapValidator('revalidateField', 'duedate');
            //$(this).datetimepicker('hide');
        });
        $('.dateDatepic').datetimepicker({
            showTodayButton: true,
            format: date_format_policy,
            sideBySide: true,
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'top'
            }
        }).on('dp.change', function (ev) {
            $('#task_header_form').bootstrapValidator('revalidateField', 'duedate');
            //$(this).datetimepicker('hide');
        });
        $('.datetimepicker4').datetimepicker({
            useCurrent: false,
            format: 'HH:mm'
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
            // subtaskdatevalidationEnd(this);
        });


        taskID = null;
        subtasksession = null;


        p_id = <?php echo json_encode(trim($this->input->post('page_id'))); ?>;
        if (p_id) {
            taskID = p_id;
            load_task_header();

        } else {
            $('.btn-wizard').addClass('disabled');
            $("#description").wysihtml5();
        }
        <?php $data_arr = $_POST['data_arr']; ?>
        frmlock = '<?php if (isset($data_arr) && !empty($data_arr)) {
            echo (is_array($data_arr)) ? join(",", $data_arr) : $data_arr;
        } ?>';
        if (frmlock == "view") {
            $("input").prop('disabled', true);
            $("select").prop('disabled', true);
            $("textarea").prop('disabled', true);
            $('button').prop('disabled', true);
            $('.headerclose').prop('disabled', false);
        } else {
            $("input").prop('disabled', false);
            $("select").prop('disabled', false);
            $('button').prop('disabled', false);
            $("textarea").prop('disabled', false);
        }
        if ($('#taskID_edit').val()) {
            $('#description_edit').prop('disabled', false);

        } else {
            $('#description_edit').prop('disabled', true);
        }


        $('#task_header_form').bootstrapValidator({
            live: 'enabled',
            message: '<?php echo $this->lang->line('common_this_value_is_not_valid');?>.',/*This value is not valid*/
            excluded: [':disabled'],
            fields: {
                subject: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_subject_is_required');?>.'}}},/*Subject is required*/
                categoryID: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_category_is_required');?>.'}}},/*Category is required*/
                contactName: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_contact_name_is_required');?>.'}}},/*Contact Name is required*/
                priority: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_priority_is_required');?>.'}}},/*Priority is required*/
                startdate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_start_date_id_required');?>.'}}},/*Start Date is required*/
                duedate: {validators: {notEmpty: {message: '<?php echo $this->lang->line('crm_due_date_is_required');?>.'}}},/*Due Date is required*/
                statusID: {validators: {notEmpty: {message: 'Status is required.'}}},
                'employees[]': {validators: {notEmpty: {message: 'Assignee is required.'}}}
            },
        }).on('success.form.bv', function (e) {
            if ($('#taskID_edit').val()) {
                $('#description_edit').prop('disabled', false);
            } else {
                $('#description_edit').prop('disabled', true);
            }
            e.preventDefault();
            $("#relatedTo_1").removeAttr("disabled");
            $("#f_search_1").removeAttr("disabled");
            $("#categoryID").removeAttr("disabled");
            $("#employeesID").prop("disabled", false);
            $("#groupID").prop("disabled", false);
            $("#departmentid").prop("disabled", false);
            $("#multipleemployeesID").prop("disabled", false);
            $('#highPriority').prop("disabled", false);
            $('#mediumPriority').prop("disabled", false);
            $('#lowPriority').prop("disabled", false);
            $('#isPermissionEveryone').iCheck('disabled');
            $('#isPermissionCreator').iCheck('disabled');
            $('#isPermissionGroup').iCheck('disabled');
            $('#isPermissionMultiple').iCheck('Enable');

            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');
            var data = $form.serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Task_management/save_task_header'); ?>",
                beforeSend: function () {

                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    myAlert(data[0], data[1], data[2]);
                    if (data[0] == 's') {

                        taskID = data[2];

                        $('#taskID_edit').val(taskID);
                        $('#task_documentAutoID').val(taskID);
                        $('#task_documentAutoID_ss').val(taskID);
                        $('#Taskid').val(taskID);
                        $('#attsection').show();
                        task_multiple_attachemts();
                        $('.btn-wizard').removeClass('disabled');
                        employeeassigntype(taskID);
                        if ($("#issubtask").is(':checked')) {
                            $('.subtasktabehide').removeClass('hide');
                            $('.subtasktabehide').addClass('disabled');
                            $('#subtask').html('<span class="step__icon"></span><span class="step__label">Step 2 Sub Task</span>');
                            $('#progress').slider({
                                formatter: function (value) {
                                    return 'Current value: ' + value + '%';
                                }
                            }).slider('disable');
                            subtaskview();
                        } else {
                            $('.subtasktabehide').addClass('disabled');
                            $('[href=#step1]').tab('show');
                            $('#subtask').html('<span class="step__icon"></span><span class="step__label">Step 2 Sub Task</span>');
                            $('#progress').slider({
                                formatter: function (value) {
                                    return 'Current value: ' + value + '%';
                                }
                            }).slider('enable');
                            task_multiple_attachemts();

                        }

                        $(document).scrollTop(0);
                        fetchPage('system/task_managment/task_managemnt', '', 'Tasks');
                        history_save();

                    } else {
                        $('.btn-primary').prop('disabled', false);
                    }
                },
                error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
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

        if ((<?php echo $admin['isSuperAdmin'] ?? 0 ?> != 1) &&
        (<?php echo $isgroupadmin['adminYN'] ?? 0 ?> != 1
    ))
        {
            $("#isPermissionMultiple").on("ifChanged", function () {
                $("#show_groupPermission").addClass('hide');
                $("#show_multiplePermission").removeClass('hide');
            });
        }
    else
        {
            $("#isPermissionMultiple").on("ifChanged", function () {
                $("#show_multiplePermission").removeClass('hide');
            });

        }


    });

    function history_save(id, attachemtndescritpom) {
        var $form = $('#task_header_form');
        var bv = $form.data('bootstrapValidator');
        var data = $form.serializeArray();
        data.push({name: 'attachmentID', value: id});
        data.push({name: 'attachemtndescritpom', value: attachemtndescritpom});
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Task_management/history_details_save'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    taskID = data[2];

                    $('#taskID_edit').val(taskID);
                    $('#task_documentAutoID').val(taskID);
                    $('#task_documentAutoID_ss').val(taskID);
                    $('#Taskid').val(taskID);
                }
                $(document).scrollTop(0);
            },
            error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }


    function filteremployee() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'MasterAutoID': taskID},
            url: "<?php echo site_url('Task_management/fetch_tasks_employee_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (!jQuery.isEmptyObject(data)) {
                    var selectedItems = [];
                    $.each(data, function (key, value) {
                        selectedItems.push(value['empID']);
                        $('#employeesID').val(selectedItems).change();
                    });
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');/*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function load_task_header() {
        if (taskID) {
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'taskID': taskID},
                url: "<?php echo site_url('Task_management/load_task_header'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    if (!jQuery.isEmptyObject(data['header'])) {
                        taskID = data['header']['taskID'];
                        priority = data['header']['Priority'];
                        $('#taskID_edit').val(taskID);
                        $('#Taskid').val(taskID);
                        $('#task_documentAutoID').val(taskID);
                        $('#subject').val(data['header']['subject']);
                        $('#categoryID').val(data['header']['categoryID']);
                        $('#departmentid').val(data['header']['departmentID']);
                        $('#view2').show();
                        $('#attsection').show();
                        $('#contactName').val(data['header']['contactName']);
                        $('#description_edit').val(data['header']['description']);
                        $('#priority').val(data['header']['Priority']);
                        $('#visibility').val(data['header']['visibility']);
                        $('#statusID').val(data['header']['status']);
                        $('#statusID').trigger('change');
                        $('#startdate').val(data['header']['starDate']);
                        $('#duedate').val(data['header']['DueDate']);
                        if (!$("#description").hasClass("wysihtml5-initialized")) {
                            $("#description").wysihtml5();
                            $("#description").addClass("wysihtml5-initialized");
                        }

                        $('#description').val(data['header']['description']);
                        $('#relatedTo').val(data['header']['relatedDocumentID']);
                        if (data['header']['isSubTaskEnabled'] == 1) {
                            $('#issubtask').iCheck('check');
                            subtaskview();
                            $('#progress').slider('setValue', data['header']['progress']);
                            $('#subtask').html('<span class="step__icon"></span><span class="step__label">Step 2 Sub Task</span>');
                            // $('#taskttachment').html('<span class="step__icon"></span><span class="step__label">Step 3 Task Attachments</span>');
                            $('.subtasktabehide').addClass('disabled');
                            $('#progress').slider({}).slider('enable');
                        } else {
                            $('.subtasktabehide').addClass('disabled');
                            $('#progress').slider('setValue', data['header']['progress']);
                            // $('#taskttachment').html('<span class="step__icon"></span><span class="step__label">Step 2 Task Attachments</span>');
                            $('#progress').slider({}).slider('enable');
                        }


                        if (data['header']['isClosed'] == 1) {
                            $('.closedatehideshow').removeClass('hide');
                            $('#closedate').val(data['header']['completedDatecovverted']);
                        } else {
                            $('.closedatehideshow').addClass('hide');
                        }
                        if (priority == 3) {
                            $('#highPriority').addClass('active');
                            $('#mediumPriority').removeClass('active');
                            $('#lowPriority').removeClass('active');
                            $('#taskPriority').val('3');
                        } else if (priority == 2) {
                            $('#highPriority').removeClass('active');
                            $('#mediumPriority').addClass('active');
                            $('#lowPriority').removeClass('active');
                            $('#taskPriority').val('2');
                        } else if (priority == 1) {
                            $('#highPriority').removeClass('active');
                            $('#mediumPriority').removeClass('active');
                            $('#lowPriority').addClass('active');
                            $('#taskPriority').val('1');
                        }
                        filteremployee();
                    }
                    if (!jQuery.isEmptyObject(data['detail'])) {
                        $.each(data['detail'], function (key, value) {
                            if (key > 0) {
                                add_more();
                            }
                        });
                    }
                    if (!jQuery.isEmptyObject(data['detail'])) {
                        var id = 1;
                        $.each(data['detail'], function (key, value) {
                            if (value.relatedDocumentID != 0) {
                                $('#relatedTo_' + id).val(value.relatedDocumentID);
                            } else {
                                $('#relatedTo_' + id).val('');
                            }

                            $('#relatedAutoID_' + id).val(value.relatedDocumentMasterID);
                            $('#f_search_' + id).val(value.searchValue);
                            $('#linkedFromOrigin_' + id).val(value.originFrom);
                            if (value.originFrom == 1) {
                                $("#relatedTo_" + id).prop("disabled", "disabled");
                                $("#f_search_" + id).prop("disabled", "disabled");
                                $("#linkmorerelation").addClass("hide");
                            } else {
                                $("#linkmorerelation").removeClass("hide");
                            }
                            id++;
                        });
                    }
                    if (!jQuery.isEmptyObject(data['permission'])) {
                        var selectedItems = [];
                        $.each(data['permission'], function (key, value) {
                            if (value.permissionID == 4) {
                                $('#isPermissionMultiple').iCheck('check');
                                selectedItems.push(value.empID);
                                $('#multipleemployeesID').val(selectedItems).change();
                            }
                        });
                    }
                    if (!jQuery.isEmptyObject(data['assignpermission']['assigpermission'])) {
                        if ((data['assignpermission']['assigpermission'] == 0) && (<?php echo $admin['isSuperAdmin'] ?? 0?> != 1) &&
                        (<?php echo $isgroupadmin['adminYN'] ?? 0 ?> != 1
                    ))
                        {
                            $('#description').attr("disabled", false);
                            $('#categoryID').prop("disabled", false);
                            $('#subject').prop('readonly', false);
                            $('.btn-primary-new').prop("disabled", false);
                            $('.subtsk').prop("disabled", false);
                            $('#startdate').prop('readonly', false);
                            $('#duedate').prop('readonly', false);
                            $('#issubtask').prop("disabled", "disabled");
                            $('#groupID').prop("disabled", "disabled");
                            $("#employeesID").prop("disabled", false);
                            $("#multipleemployeesID").prop("disabled", false);
                            $('#highPriority').prop("disabled", false);
                            $('#mediumPriority').prop("disabled", false);
                            $('#lowPriority').prop("disabled", false);

                            if (!jQuery.isEmptyObject(data['permission'])) {
                                var selectedItems = [];
                                $.each(data['permission'], function (key, value) {
                                    if (value.permissionID == 4) {
                                        $('#isPermissionMultiple').iCheck('check');
                                        selectedItems.push(value.empID);
                                        $('#multipleemployeesID').val(selectedItems).change();
                                        $('#isPermissionEveryone').iCheck('disable');
                                        $('#isPermissionCreator').iCheck('disable');
                                        $('#isPermissionGroup').iCheck('disable');
                                        $('#isPermissionMultiple').iCheck('disable');
                                    }
                                });
                            }

                        }
                        if (data['assignpermission']['assigpermission'] == 1 && data['header']['isClosed'] == 0) {
                            $('#description').attr("disabled", true);
                            $('#categoryID').prop("disabled", true);
                            $('#subject').prop('readonly', true);
                            $('#startdate').prop('readonly', true);
                            $('#duedate').prop('readonly', true);
                            $('#description_edit').val(data['header']['description']);
                            $('#highPriority').prop("disabled", true);
                            $('#mediumPriority').prop("disabled", true);
                            $('#lowPriority').prop("disabled", true);
                            $('.btn-wizard').removeClass('disabled');

                        }

                    }

                    if ((data['header']['createdUserID'] == '<?php echo $current_userid?>' && data['header']['isClosed'] == 0)) {
                        $('#subject').prop('readonly', true);
                        $('#description').prop("disabled", true);
                        $('#categoryID').prop("disabled", true);
                        $('#progress').slider('readonly');
                        $('.btn-primary-new').prop("disabled", false);
                        $('#statusID').prop('readonly', true);
                        $('#statusID').val(data['header']['status']);
                        $('.btn-wizard').removeClass('disabled');
                        $('#startdate').prop('readonly', true);
                        $('.subtasktabehide').addClass('disabled');
                        $('#duedate').prop('readonly', true);
                        $("#multipleemployeesID").prop("disabled", true);
                        $("#employeesID").prop("disabled", true);
                        $('#highPriority').prop("disabled", false);
                        $('#mediumPriority').prop("disabled", false);
                        $('#lowPriority').prop("disabled", false);
                        if (!jQuery.isEmptyObject(data['permission'])) {
                            var selectedItems = [];
                            $.each(data['permission'], function (key, value) {
                                if (value.permissionID == 4) {
                                    $('#isPermissionMultiple').iCheck('check');
                                    selectedItems.push(value.empID);
                                    $('#multipleemployeesID').val(selectedItems).change();
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                }
                            });
                        }


                    }
                    if (data['header']['createdUserID'] == data['assignpermission']['empID'] && data['header']['createdUserID'] == '<?php echo $current_userid?>' && data['header']['isClosed'] == 0) {
                        $('#subject').prop('readonly', true);
                        $('#description').prop("disabled", true);
                        $('#categoryID').prop("disabled", true);
                        $('#progress').slider('readonly');
                        $('#statusID').prop('readonly', false);
                        $('#startdate').prop('readonly', true);
                        $('.btn-wizard').removeClass('disabled');
                        $('#duedate').prop('readonly', true);
                        $("#employeesID").prop("disabled", false);
                        $("#multipleemployeesID").prop("disabled", false);
                        $('#highPriority').prop("disabled", false);
                        $('#mediumPriority').prop("disabled", false);
                        $('#lowPriority').prop("disabled", false);
                        if (!jQuery.isEmptyObject(data['permission'])) {
                            var selectedItems = [];
                            $.each(data['permission'], function (key, value) {
                                if (value.permissionID == 4) {
                                    // $('#isPermissionMultiple').iCheck('uncheck');
                                    selectedItems.push(value.empID);
                                    $('#multipleemployeesID').val(selectedItems).change();
                                    $('#isPermissionMultiple').iCheck('enable');
                                }
                            });
                        }

                    }

                    if ((data['header']['isClosed'] == 1)) {
                        $('#description').attr("disabled", true);
                        $('#categoryID').prop("disabled", true);
                        $('#subject').prop('readonly', true);
                        $('#progress').slider({}).slider('disable');
                        $('#statusID').prop("disabled", "disabled");
                        $('.btn-primary-new').prop("disabled", true);
                        $('.subtsk').prop("disabled", true);
                        // $("#description_edit").prop("disabled", true);
                        $('#startdate').prop('readonly', true);
                        $('#duedate').prop('readonly', true);
                        $('#issubtask').prop("disabled", "disabled");
                        $('#groupID').prop("disabled", "disabled");
                        $('.subtasktabehide').addClass('disabled');
                        $("#employeesID").prop("disabled", true);
                        $("#multipleemployeesID").prop("disabled", true);
                        $('#highPriority').prop("disabled", true);
                        $('#mediumPriority').prop("disabled", true);
                        $('#lowPriority').prop("disabled", true);

                        if (!jQuery.isEmptyObject(data['permission'])) {
                            var selectedItems = [];
                            $.each(data['permission'], function (key, value) {
                                if (value.permissionID == 4) {
                                    $('#isPermissionMultiple').iCheck('check');
                                    selectedItems.push(value.empID);
                                    $('#multipleemployeesID').val(selectedItems).change();
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                }
                            });
                        }
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
    }

    function employeeassigntype(taskID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'taskID': taskID},
            url: "<?php echo site_url('Task_management/load_task_header'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data['assignpermission']['assigpermission'])) {
                    if ((data['assignpermission']['assigpermission'] == 0) && (<?php echo $admin['isSuperAdmin'] ?? 0 ?> != 1) &&
                    (<?php echo $isgroupadmin['adminYN'] ?? 0 ?> != 1
                ) )
                    {
                        $('#subject').prop('readonly', true);
                        $('#categoryID').prop("disabled", "disabled");
                        $('#groupID').prop("disabled", "disabled");
                        $("#employeesID").prop("disabled", true);
                        $("#multipleemployeesID").prop("disabled", true);
                        $('#highPriority').prop("disabled", true);
                        $('#mediumPriority').prop("disabled", true);
                        $('#lowPriority').prop("disabled", true);
                        if (!jQuery.isEmptyObject(data['permission'])) {
                            var selectedItems = [];
                            $.each(data['permission'], function (key, value) {
                                if (value.permissionID == 1) {
                                    $('#isPermissionEveryone').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_multiplePermission").addClass('hide');
                                    $("#show_groupPermission").addClass('hide');
                                } else if (value.permissionID == 2) {
                                    $('#isPermissionCreator').iCheck('check');
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_multiplePermission").addClass('hide');
                                    $("#show_groupPermission").addClass('hide');
                                } else if (value.permissionID == 3) {
                                    $('#isPermissionGroup').iCheck('check');
                                    setTimeout(function () {
                                        $('#groupID').val(value.permissionValue);
                                    }, 600)
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_groupPermission").removeClass('hide');
                                    $("#show_multiplePermission").addClass('hide');
                                    $("#show_groupPermission").removeClass('hide');
                                    $("#show_multiplePermission").addClass('hide');
                                } else if (value.permissionID == 4) {
                                    $('#isPermissionMultiple').iCheck('check');
                                    selectedItems.push(value.empID);
                                    $('#multipleemployeesID').val(selectedItems).change();
                                    $('#isPermissionEveryone').iCheck('disable');
                                    $('#isPermissionCreator').iCheck('disable');
                                    $('#isPermissionGroup').iCheck('disable');
                                    $('#isPermissionMultiple').iCheck('disable');
                                    $("#show_groupPermission").addClass('hide');
                                    $("#show_multiplePermission").removeClass('hide');
                                }
                            });
                        }
                    }
                else
                    {
                        $('#subject').prop('readonly', false);
                        $("#categoryID").removeAttr("disabled");
                        $("#employeesID").prop("disabled", false);
                        $("#multipleemployeesID").prop("disabled", false);
                        if (!jQuery.isEmptyObject(data['permission'])) {
                            var selectedItems = [];
                            $.each(data['permission'], function (key, value) {
                                if (value.permissionID == 1) {
                                    $('#isPermissionEveryone').iCheck('check');
                                } else if (value.permissionID == 2) {
                                    $('#isPermissionCreator').iCheck('check');
                                } else if (value.permissionID == 3) {
                                    $('#isPermissionGroup').iCheck('check');
                                    $('#groupID').val(value.permissionValue);
                                } else if (value.permissionID == 4) {
                                    $('#isPermissionMultiple').iCheck('check');
                                    selectedItems.push(value.empID);
                                    $('#multipleemployeesID').val(selectedItems).change();
                                }
                            });
                        }
                    }

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

    function delete_task_detail(id) {
        if (taskID) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('crm_you_want_to_delete_this_file');?>",/*You want to delete this file!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        async: true,
                        type: 'post',
                        dataType: 'json',
                        data: {'AssingeeID': id},
                        url: "<?php echo site_url('Task_management/delete_task_detail'); ?>",
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            setTimeout(function () {
                                fetch_detail();
                            }, 300);
                            refreshNotifications(true);
                        }, error: function () {
                            swal("Cancelled", "Your file is safe :)", "error");
                        }
                    });
                });
        }
    }

    function initializeTaskTypeahead(id) {
        var relatedType = $('#relatedTo_' + id).val();
        $('#f_search_' + id).autocomplete({
            serviceUrl: '<?php echo site_url();?>Task_management/fetch_document_relate_search/?&t=' + relatedType,
            onSelect: function (suggestion) {
                $('#relatedAutoID_' + id).val(suggestion.DoucumentAutoID);
            }
        });
    }

    function relatedChange(elemant) {
        initializeTaskTypeahead(search_id);
        $('#f_search_' + search_id).val('');
    }

    function prioritycheck(id) {
        if (id == 3) {
            $('#highPriority').addClass('active');
            $('#mediumPriority').removeClass('active');
            $('#lowPriority').removeClass('active');
            $('#taskPriority').val('3');
        } else if (id == 2) {
            $('#highPriority').removeClass('active');
            $('#mediumPriority').addClass('active');
            $('#lowPriority').removeClass('active');
            $('#taskPriority').val('2');
        } else if (id == 1) {
            $('#highPriority').removeClass('active');
            $('#mediumPriority').removeClass('active');
            $('#lowPriority').addClass('active');
            $('#taskPriority').val('1');
        }
    }

    function task_multiple_attachemts() {
        var taskID = $('#task_documentAutoID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {taskID: taskID},
            url: "<?php echo site_url('Task_management/load_task_multiple_attachemts'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#task_multiple_attachemts').html(data);
                $('#task_multiple_attachemts_cmm').html(data);

                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function show_task_button() {
        $('#add_attachemnt_show').removeClass('hide');
    }

    function show_task_button_cmm() {
        $('#add_attachemnt_show').removeClass('hide');
    }

    function document_uplode() {
        var attachmentDescription = $('#taskattachmentDescription').val();
        var documentID = $('#documentID').val();
        var documentAutoID = $('#task_documentAutoID').val();
        var fileInput = document.getElementById('document_file');
        var file = fileInput.files[0];

        if (attachmentDescription.trim() === '' || documentID.trim() === '' || documentAutoID.trim() === '') {
            swal("Cancelled", "Please fill in all required fields. :)", "error");
            return false;
        }
        var formData = new FormData();
        formData.append('attachmentDescription', attachmentDescription);
        formData.append('documentID', documentID);
        formData.append('documentAutoID', documentAutoID);
        formData.append('document_file', file);

        // AJAX request
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: "<?php echo site_url('Task_management/attachement_upload'); ?>",
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
                    $('#taskattachmentDescription').val('');
                    task_multiple_attachemts();
                }
            },
            error: function (data) {
                stopLoad();
                swal("Cancelled", "No File Selected :)", "error");
            }
        });
        return false;
    }


    function delete_crm_attachment(id, fileName, attachemtndescritpom) {
        swal({
                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                text: "<?php echo $this->lang->line('crm_you_want_to_delete_a');?>",/*You want to Delete!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*Yes!*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'attachmentID': id, 'myFileName': fileName, 'attachmentDescription': attachemtndescritpom},
                    url: "<?php echo site_url('Task_management/delete_crm_attachment'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == true) {
                            myAlert('s', 'Deleted Successfully');
                            task_multiple_attachemts();
                            history_save(id, attachemtndescritpom);

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

    function add_more() {
        search_id += 1;
        var appendData = $('.append_data:first').clone();
        appendData.find('input').val('');
        appendData.find('#f_search_' + search_id).val('');
        appendData.find('.relatedTo').attr('id', 'relatedTo_' + search_id);
        appendData.find('.relatedAutoID').attr('id', 'relatedAutoID_' + search_id);
        appendData.find('.linkedFromOrigin').attr('id', 'linkedFromOrigin_' + search_id);
        appendData.find('.f_search').attr('id', 'f_search_' + search_id);
        appendData.find('.remove-td').html('<span class="glyphicon glyphicon-trash remove-tr" style="color:rgb(209, 91, 71);"></span>');
        $('#append_related_data').append(appendData);
        initializeTaskTypeahead(search_id);
    }

    $(document).on('click', '.remove-tr', function () {
        $(this).closest('.append_data').remove();
    });

    function load_taskRelated_fromLead(leadID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {leadID: leadID},
            url: "<?php echo site_url('Task_management/load_taskRelated_fromLead'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#relatedTo_1').val(5);
                    $('#linkedFromOrigin_1').val(1);
                    $("#relatedTo_1").prop("disabled", "disabled");
                    $('#f_search_1').val(data['fullname']);
                    $("#f_search_1").prop("disabled", "disabled");
                    $('#relatedAutoID_1').val(data['leadID']);
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

    function load_taskRelated_fromOpportunity(opportunityID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {opportunityID: opportunityID},
            url: "<?php echo site_url('Task_management/load_taskRelated_fromOpportunity'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#relatedTo_1').val(4);
                    $('#linkedFromOrigin_1').val(1);
                    $("#relatedTo_1").prop("disabled", "disabled");
                    $('#f_search_1').val(data['fullname']);
                    $("#f_search_1").prop("disabled", "disabled");
                    $('#relatedAutoID_1').val(data['opportunityID']);
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

    function load_taskRelated_fromProject(projectID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {projectID: projectID},
            url: "<?php echo site_url('Task_management/load_taskRelated_fromProject'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    $('#relatedTo_1').val(9);
                    $('#linkedFromOrigin_1').val(1);
                    $("#relatedTo_1").prop("disabled", "disabled");
                    $('#f_search_1').val(data['fullname']);
                    $("#f_search_1").prop("disabled", "disabled");
                    $('#relatedAutoID_1').val(data['projectID']);
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

    function load_task_header_enable() {

    }

    function cheack_status() {

        $('#isPermissionEveryone').iCheck('Enable');
        $('#isPermissionCreator').iCheck('Enable');
        $('#isPermissionGroup').iCheck('Enable');
        $('#isPermissionMultiple').iCheck('Enable');
    }

    function subtaskview() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'taskID': taskID},
            url: "<?php echo site_url('Task_management/load_subtask_view'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#subtrskview').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function create_sub_task() {
        $('#sub_task_addform')[0].reset();
        $('#subTask_add_table tbody tr').not(':first').remove();
        $(".employeessubtask").val(null).trigger("change");
        $("#sub_task_add_item_modal").modal({backdrop: "static"});

    }

    function calculatesubtask(element) {


        var startDate1 = $(element).closest('tr').find('.estsubtaskdate').val();
        var endDate1 = $(element).closest('tr').find('.estsubtaskdateend').val();

        var startDate = moment(startDate1, "DD.MM.YYYY");
        var endDate = moment(endDate1, "DD.MM.YYYY");
        var startdatevalid = startDate.isValid()
        var enddatevalid = endDate.isValid()


        if ((startdatevalid != false) && (enddatevalid != false)) {

            var days = endDate.diff(startDate, 'days');
            var formattedDate = days;
            $(element).closest('tr').find('.indays').val(formattedDate);
        } else {
            $(element).closest('tr').find('.indays').val(0);
        }


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
            // subtaskdatevalidationEnd(this);
        });

        $('.datetimepicker4').datetimepicker({
            useCurrent: false,
            format: 'HH:mm',
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

    function save_sub_task_assignee() {
        $(".employeessubtask").each(function () {
            empsubtask = $(this).val();
            $(this).parent().parent().find('.assign-cls').val(empsubtask);

        });
        var data = $('#sub_task_addform').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Task_management/AddSubTaskDetail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1], data[2]);
                if (data[0] == 's') {
                    // $("#description").wysihtml5();

                    subtaskview();
                    $('#progress').slider('setValue', data[2]);
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

    function cheack_hours_count(element) {
        if (element.value > 24) {
            myAlert('w', 'Hours cannot be greater than 24')
            $(element).closest('tr').find('.inhrs').val('');
        }

    }

    function cheack_minutes_count(element) {
        if (element.value > 59) {
            myAlert('w', 'Minutes cannot be greater than 59')
            $(element).closest('tr').find('.inmns').val('');
        }

    }

    function assign_validation_start(subtaskid, taskid, createdUserID, type) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {subtaskid: subtaskid, taskid: taskid},
            url: "<?php echo site_url('Task_management/load_subtask_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data['assignpermission'])) {
                    if (data['assignpermission'] == 1) {
                        start_sub_task(subtaskid, taskid, type);
                    } else {
                        myAlert('w', 'You donot have permission to start this subtask')
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

    function assign_validation_stop(subtaskid, taskid, sessionID, createdUserID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {subtaskid: subtaskid, taskid: taskid},
            url: "<?php echo site_url('Task_management/load_subtask_details'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                if (!jQuery.isEmptyObject(data['assignpermission']['assigpermission'])) {
                    if ((data['assignpermission']['assigpermission'] == 1) || (<?php echo $admin['isSuperAdmin'] ?? 0 ?> == 1) ||
                    (<?php echo $isgroupadmin['adminYN'] ?? 0 ?> == 1
                ) ||
                    (createdUserID == '<?php echo $current_userid?>')
                )
                    {
                        stop_sub_task(subtaskid, taskid, sessionID)
                    }
                else
                    {
                        myAlert('w', 'You donot have permission to stop this subtask')
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

    function start_sub_task(subtaskid, taskid, type) {
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
                    url: "<?php echo site_url('Task_management/start_subtask'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            subtaskview();
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

    function stop_sub_task(subtaskid, taskid, sessionID) {
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
                    data: {subtaskid: subtaskid, taskid: taskid, subtasksession: sessionID},
                    url: "<?php echo site_url('Task_management/stop_subtask'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1]);
                        if (data[0] == 's') {
                            subtaskview();
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

    function load_sub_task_status(subTaskID, taskID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'subTaskID': subTaskID, taskID: taskID},
            url: "<?php echo site_url('Task_management/load_subtsk_status'); ?>",
            success: function (data) {
                if (data) {
                    $('#subtaskID').val(subTaskID);
                    $('#TaskID').val(taskID);
                    $('#statussubtask').val(data['status']).change();
                    $('#sub_task_model').modal('show');
                }
            }
        });

    }

    function save_sub_task_status() {
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
                    url: "<?php echo site_url('Task_management/save_subTask_status'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0], data[1], data[2]);
                        if (data[0] == 's') {
                            subtaskview();
                            $('#progress').slider('setValue', data[2]);
                            $('#progress').slider({}).slider('disable');
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


    function chat_box_subtask(subTaskID, taskID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'subTaskID': subTaskID, 'taskID': taskID},
            url: "<?php echo site_url('Task_management/load_subtask_chats'); ?>",
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

    function chat_box_task_comment(taskID) {
        var taskID = $('#task_documentAutoID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'taskID': taskID},
            url: "<?php echo site_url('Task_management/load_task_chats_comment'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('#task_chat_comment').html(data);
                stopLoad();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function history_detais_load() {
        var taskID = $('#task_documentAutoID').val();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'taskID': taskID},
            url: "<?php echo site_url('Task_management/load_history_task'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();

                if (data.createdUserData) {
                    var createdByLine = data.createdUserData.createdUserName + " created this task at " + " " + data.createdUserData.createdDateTime;
                    var createdHTML = '<div class="incoming_msg_img pull-left">' +
                        '<img src="https://ptetutorials.com/images/user-profile.png" alt="sunil" align="middle">' +
                        '</div>' +
                        '<div class="sent_msg_task_employee">' +
                        '<p>' + createdByLine + '</p>' +
                        '</div><br>';
                    $('#history-section .created_info').html(createdHTML);
                }

                if (data.changeData) {
                    var changeHTML = '';
                    $.each(data.changeData, function (index, change) {
                        var changeLine = change.changedBy + " updated the " + change.fieldName + " at " + change.changedDateTime;
                        changeHTML += '<div class="incoming_msg_img pull-left">' +
                            '<img src="https://ptetutorials.com/images/user-profile.png" alt="sunil" align="middle">' +
                            '</div>' +
                            '<div class="col-md-12 detialsval">' +

                            '<div>' +
                            '<p>' + changeLine + '</p>' +
                            '</div>' +
                            '<div class="col-md-6"> ' + change.oldValue + '</div>' +
                            '<div class="col-md-6"> ' + change.newValue + '</div>' +
                            '</div>';
                    });
                    $('#history-section .change_details').html(changeHTML);
                }

                $('#history-section').show();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', '<br>Message: ' + errorThrown);
            }
        });
    }

    function sub_task_attachment_model(subTaskID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'subTaskID': subTaskID},
            url: "<?php echo site_url('Task_management/attachment_subTask'); ?>",
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

    function subtaskdatevalidation(element) {
        var duedatemainttask = moment($("#duedate").val(), "DD.MM.YYYY");
        var startdatesubtask = moment($(element).closest('tr').find('.estsubtaskdate').val(), "DD.MM.YYYY");

        var startDate = $(element).closest('tr').find('.estsubtaskdate').val();

        var startdatesub = $(element).closest('tr').find('.estsubtaskdate').val();
        var enddatesub = $(element).closest('tr').find('.estsubtaskdateend').val();

        if (enddatesub) {

            if (startdatesub > enddatesub) {
                myAlert('w', 'Est. Start Date cannot be greater than Est. End Date');
                $(element).closest('tr').find('.estsubtaskdate').val('');

            }
        }

        if (startdatesubtask) {
            if (startdatesubtask > duedatemainttask) {
                $(element).closest('tr').find('.estsubtaskdate').val('');
                $(element).closest('tr').find('.indays').val(0);
                myAlert('w', 'Est. Start Date cannot be greater than Task Due Date');
            }
        } else {
            if (startDate) {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'taskID': taskID, 'startDate': startDate},
                    url: "<?php echo site_url('Task_management/start_date_est_date_validation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == 1) {

                        } else {
                            myAlert('w', 'Estimated start Date Cannot be less than Task Start Date');
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
    }


    function subtaskdatevalidationEnd(element) {
        var endstartDate = moment($(element).closest('tr').find('.estsubtaskdateend').val(), 'YYYY-MM-DD').format('YYYY-MM-DD');
        var startdatesub = moment($(element).closest('tr').find('.estsubtaskdate').val(), 'YYYY-MM-DD').format('YYYY-MM-DD');
        var enddatesub = $(element).closest('tr').find('.estsubtaskdateend').val();

        if (startdatesub > endstartDate) {
            myAlert('w', 'Est.End Date cannot be less than Est.Start Date');
            $(element).closest('tr').find('.estsubtaskdateend').val('');
            $(element).closest('tr').find('.indays').val(0);
        } else {

            if (startdatesub) {

                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'taskID': taskID, 'endstartDate': endstartDate},
                    url: "<?php echo site_url('Task_management/end_date_est_date_validation'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        if (data == 1) {


                        } else {
                            myAlert('w', 'Estimated End Date Cannot be greater than Task Due Date');
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
    }


    $('input').on('ifChecked', function (event) {
        if ($(this).hasClass('isClosedcls')) {
            if (taskID) {
                isclosetaskvalidation(taskID);
            }
        }
    });
    $('input').on('ifUnchecked', function (event) {
        if ($(this).hasClass('issubtaskcls')) {

            if (taskID) {
                isexistsubtasks(taskID);
            }
        }

    });

    function isclosetaskvalidation(taskID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'taskID': taskID},
            url: "<?php echo site_url('Task_management/crm_task_close_ischk'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data == 1) {
                    setTimeout(function () {
                        $('#isClosed').iCheck('uncheck');

                    }, 500);
                    myAlert('w', 'There are some unpending subtask are pending you cannot close this task!');

                }

            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function isexistsubtasks() {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'taskID': taskID},
            url: "<?php echo site_url('Task_management/crm_is_subtask_exist'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                if (data == 1) {
                    setTimeout(function () {
                        $('#issubtask').iCheck('check');

                    }, 500);
                    myAlert('w', 'There are some subtask you cannot Untick this subtask');

                }

            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function edit_subtask(subTaskID, taskID) {

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
                    data: {'subTaskID': subTaskID, 'taskID': taskID},
                    url: "<?php echo site_url('Task_management/crm_sub_task_detail_edit'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        hoursNew = Math.floor(data['estimatedHours'] / 60);
                        minutesnew = (data['estimatedHours'] % 60);

                        $('#subtaskAutoid').val(subTaskID);
                        $('#taskautoid').val(taskID);
                        $('#inhrs_edit').val(hoursNew);
                        $('#inmns_edit').val(minutesnew);
                        $('#edit_estsubtaskdate').val(data['startdateSubtask']);
                        $('#edit_estsubtaskdateend').val(data['enddateSubtask']);
                        $('#edit_indays').val(data['estimatedDays']);
                        $('#edit_taskdescription').val(data['taskDescription']);
                        filteremployee_subtask(subTaskID)
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

    function filteremployee_subtask(subTaskID) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {'MasterAutoID': subTaskID},
            url: "<?php echo site_url('Task_management/fetch_tasks_employee_detailsubtask'); ?>",
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

    function update_subtask_details() {
        var data = $('#edit_sub_task_frm').serializeArray();
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: data,
            url: "<?php echo site_url('Task_management/update_subtaask_detail'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                myAlert(data[0], data[1]);
                stopLoad();
                if (data[0] == 's') {
                    subtaskview();
                    $('#edit_task_subtask_details_modal').modal('hide');
                    $('#edit_sub_task_frm')[0].reset();
                    $("#edit_employeessubtask").val(null).trigger("change");
                }
            }, error: function () {
                alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                /*An Error Occurred! Please Try Again*/
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function validateFloatKeyPress(el, evt, currency_decimal = 3) {
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

    function statuscheack(statusid) {
        if(statusid){
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {'statusid': statusid, 'taskID': taskID},
                url: "<?php echo site_url('Task_management/crm_is_subtask_exist'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    if (data['isexist'] == 1) {
                        $('#statusID').val('');
                        myAlert('w', 'There are some  subtask are pending you cannot close this task!');
                        $('.closedatehideshow').addClass('hide');

                    } else if ((data['isexist'] != 1) && (data['closedstatus']['statusType'] == 1) && (data['taskdetail']['isClosed'] == 0)) {
                        swal({
                                title: "Are you sure?",
                                text: "You want to close this task!",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-danger",
                                confirmButtonText: "Yes",
                                cancelButtonText: "No",
                                closeOnConfirm: true,
                                closeOnCancel: true
                            },
                            function (isConfirm) {
                                if (isConfirm) {

                                    $('#progress').slider('setValue', 100);
                                    $('.closedatehideshow').removeClass('hide');
                                } else {
                                    $('#statusID').val('');
                                    $('#progress').slider('setValue', 0);
                                    $('.closedatehideshow').addClass('hide');
                                }
                            });
                    } else {
                        $('.closedatehideshow').addClass('hide');
                    }
                }, error: function () {
                    alert('<?php echo $this->lang->line('common_an_error_occurred_Please_try_again');?>.');
                    /*An Error Occurred! Please Try Again*/
                    stopLoad();
                    refreshNotifications(true);
                }
            });
        }
    }

    function loaddepartment() {
        var categoryID = $('#categoryID').val();
        $.ajax({
            url: 'Task_management/get_department_details',
            type: 'POST',
            dataType: 'json',
            data: {
                categoryID: categoryID
            },
            success: function (response) {
                // Update the employee dropdown with the filtered data
                var options = '<option value="">Select Employee</option>';
                $.each(response, function (index, item) {
                    var isSelected = item.isdefault == 1 ? 'selected' : ''; // Check if isdefault is 1
                    options += '<option value="' + item.EIdNo + '" ' + isSelected + '>' + item.Ename2 + '</option>';
                });
                $('#employeesID').html(options);
                $('#employeessubtask').html(options);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    function loaddays() {
        var categoryID = $('#categoryID').val();
        var startdate = $('#startdate').val();
        loaddepartment();
        $.ajax({
            url: 'Task_management/get_sdays',
            type: 'POST',
            dataType: 'json',
            data: {
                categoryID: categoryID,
                startdate: startdate
            },
            success: function (response) {
                if (response && response.totaldays) {
                    var totaldays = response.totaldays;
                    if (totaldays) {
                        var startDate = $('#startdate').val();
                        var dueDate = calculateDueDate(startDate, totaldays);
                        $('#duedate').val(dueDate);
                        $('#startdate').prop('readonly', true);
                        $('#duedate').prop('readonly', true);
                        $("#description_edit").prop("disabled", true);

                        // Populate the status dropdown
                        var status_arr = <?php echo json_encode($status_arr); ?>;
                        $.each(status_arr, function (key, value) {
                            $('#statusID').append($('<option></option>').attr('value', key).text(value));
                        });
                        $('#statusID').val(Object.keys(status_arr)[0]).trigger('change');
                        $('#statusID').prop('disabled', true);
                        $('#task_header_form').data('bootstrapValidator').resetForm();
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    function calculateDueDate(startDate, totaldays) {
        var totaldaysAdjusted = parseInt(totaldays);
        var startDateObj = moment(startDate, "<?php echo strtoupper($date_format_policy); ?>");
        startDateObj.add(totaldaysAdjusted, 'days');
        var dueDate = startDateObj.format("<?php echo strtoupper($date_format_policy); ?>");

        return dueDate;
    }


    function filteremployee() {
        var taskID = $('#task_documentAutoID').val();
        $.ajax({
            url: 'Task_management/department_hod_filter',
            type: 'POST',
            dataType: 'json',
            data: {
                taskID: taskID
            },
            success: function (response) {
                $.each(response, function (index, employee) {
                    $('#employeesID option[value="' + employee.empID + '"]').prop('selected', true);
                });
                // Trigger change event if using select2
                $('#employeesID').trigger('change');
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    function truncateFilename() {
        var fileInput = document.getElementById('document_file');
        var filenameSpan = document.querySelector('.fileinput-filename');
        if (fileInput.files.length > 0) {
            var filename = fileInput.files[0].name;
            var truncatedFilename = filename.substring(0, 5) + '...'; // Change 10 to the desired length
            filenameSpan.textContent = truncatedFilename;
        }
    }

    $(document).ready(function () {
        $('.navigation-link').click(function (e) {
            e.preventDefault();
            var sectionToShow = $(this).data('section');
            $('.section').hide();
            $('#' + sectionToShow + '-section').show();
        });
    });

</script>