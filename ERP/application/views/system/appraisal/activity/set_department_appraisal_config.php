<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_deparment_appraisal_config_title');


?>
<style>
    .fa-ban {
        color: red;
    }

    .tab-content-div {
        padding: 25px;
    }

    .sub-dep-task thead tr {
        background-color: white;
    }

    .error-message {
        color: red;
    }

    .objectives-table th {
        text-align: left;
    }

    .act-btn-margin {
        margin: 0 2px;
    }

    .text-content-color {
        color: #555555;
    }

    .label-color {
        color: rgb(23, 43, 77);
    }

    #department_objectives_table th {
        background-color: #5fb0b7;
        color: white;
    }

    .label-text-size {
        font-size: 12px;
    }

    .section-padding {
        padding: 30px;
    }

    .header-label-color {
        background-color: #f1f2f6;
        color: #000000;
    }

    .sub-header-label-color {
        background-color: #f1f2f6;
        color: #000000;
    }

    .table-striped > tbody > tr:nth-child(n+1) > td, .table-striped > tbody > tr:nth-child(n+1) > th {
        background-color: rgb(234 228 233 / 70%);
    }

    .progress {
        position: relative;
    }

    .progress span {
        position: absolute;
        display: block;
        width: 100%;
        color: black;
        text-align: center;
    }

    .speech-bubble {
        position: relative;
        background: #00aabb;
        border-radius: .4em;
        width: auto;
        float: right;
        padding: 10px;
        color: white;
        margin: 3px 0;
        max-width: 60%;
        min-width: 215px;
    }

    .speech-bubble:after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        width: 0;
        height: 0;
        border: 0.438em solid transparent;
        border-left-color: #00aabb;
        border-right: 0;
        border-bottom: 0;
        margin-top: -0.219em;
        margin-right: -0.437em;
    }

    .speech-bubble2 {
        position: relative;
        background: #efefef;
        border-radius: .4em;
        width: auto;
        float: left;
        padding: 10px;
        color: black;
        margin: 3px 0;
        max-width: 60%;
        min-width: 215px;
    }

    .speech-bubble2:after {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 0;
        height: 0;
        border: 0.438em solid transparent;
        border-right-color: #efefef;
        border-left: 0;
        border-top: 0;
        margin-top: -0.219em;
        margin-left: -0.437em;
    }

    .tab-title {
        cursor: pointer;
    }

    ul.tabs li a {
        font-size: 14px !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
        font-weight: 600;
        min-width: 100px;
        margin-left: 2px;
    }

    ul.tabs li {
        width: auto !important;
    }
    .progress-bar{
        background-color: #337ab7;
    }
    .progress-bar-green, .progress-bar-success {
        background-color: #06d6a0;
    }
    .glyphicon-pencil{
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: antiquewhite;
        text-align: center;
        line-height: 24px;
        color: #116f5e;
        background-color: #ffffff;
        outline: 0!important;
        font-size: 12px;
        margin: 1px 0;
    }
    .glyphicon-trash{
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: antiquewhite;
        text-align: center;
        line-height: 24px;
        color: #f75964;
        background-color: #ffffff;
        outline: 0!important;
        font-size: 12px;
        margin: 1px 0;
    }
    .glyphicon-flag{
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: antiquewhite;
        text-align: center;
        line-height: 24px;
        color: #607d8b;
        background-color: #ffffff;
        outline: 0!important;
        font-size: 12px;
        margin: 1px 0;
    }
</style>
<style>
    body {
        overflow-y: scroll;

    }

    .wrap {
        marin: 0 auto;
        margin-top: -10px;
        margin-bottom: 20px;
    }

    ul.tabs {
        width: 100%;
        height: 80px;
        margin: 0 auto;
        list-style: none;
        overflow: hidden;
        padding: 0;
    }

    ul.tabs li {
        float: left;
        width: 130px;


    }

    ul.tabs li a {
        position: relative;
        display: block;
        height: 50px;
        margin-top: 40px;
        padding: 10px 0 0 0;
        font-size: 18px;
        text-align: center;
        text-decoration: none;
        color: #ffffff;
        background: #f9f9f9;       
        border: 0px solid #000000;
        -webkit-transition: padding 0.2s ease, margin 0.2s ease;
        -moz-transition: padding 0.2s ease, margin 0.2s ease;
        -o-transition: padding 0.2s ease, margin 0.2s ease;
        -ms-transition: padding 0.2s ease, margin 0.2s ease;
        transition: padding 0.2s ease, margin 0.2s ease;
    }

    .tabs li:first-child a {
        z-index: 3;
        -webkit-border-top-left-radius: 8px;
        -moz-border-radius-topleft: 8px;
        border-top-left-radius: 8px;
    }

    .tabs li:nth-child(2) a {
        z-index: 2;
    }

    .tabs li:last-child a {
        z-index: 1;
        -webkit-border-top-right-radius: 8px;
        -moz-border-radius-topright: 8px;
        border-top-right-radius: 8px;
    }

    ul.tabs li a:hover {
        margin: 35px 0 0 0;
        padding: 10px 0 5px 0;
    }

    ul.tabs li a.active {
        margin: 40px 0 0 0;
        padding: 10px 0 10px 0;
        background: #696CFF;
        color: #f9f9f9;
        /*color: #ff6831;*/
        z-index: 4;
        outline: none;
    }

    ul.tabs li a {

        color: #555555;

    }

    .group:before,
    .group:after {
        content: " "; /* 1 */
        display: table; /* 2 */
    }

    .group:after {
        clear: both;
    }

    #content {
        width: 100%;
        height: auto;
        margin: 0 auto;
        background: #ffffff;
    }

    .table-responsive {
        padding: 0 0 0 0 !important;
    }
</style>
<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">

                    <button id="" class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button id="" class="btn btn-box-tool headerclose navdisabl"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <link rel="stylesheet"
                  href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>
            <div class="row">
                <div class="col-md-12">
                    <div style="padding: 10px;">
                        <div class="row">
                            <div class="col-md-2"><span
                                        class="label header-label-color label-text-size"><?php echo $this->lang->line('appraisal_date_created'); ?><!--Date Created--></span>
                                <span
                                        id="date_created" class="text-content-color"></span></div>
                            <div class="col-md-4"><span
                                        class="label header-label-color label-text-size"><?php echo $this->lang->line('appraisal_department_appraisal_document_id'); ?><!--Department Appraisal Document ID--></span>
                                <span id="department_appraisal_doucment_id" class="text-content-color"></span></div>
                        </div>
                    </div>
                </div>
            </div>


            <hr style="margin-top:5px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <label class="label sub-header-label-color label-text-size">
                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_name'); ?>
                                </label>
                                &nbsp;<span id="goal_department_name" class="text-content-color"></span>
                            </div>

                            <div class="col-md-4">
                                <label class="label sub-header-label-color label-text-size">
                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_document_id'); ?>
                                </label>
                                &nbsp;<span id="goal_doucment_id" class="text-content-color"></span>
                            </div>

                            <div class="col-md-2">
                                <label class="label sub-header-label-color label-text-size">
                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_period'); ?>
                                </label>
                                &nbsp;<span id="period" class="text-content-color"></span>
                            </div>


                            <div class="col-md-2">
                                <div>
                                    <label class="label sub-header-label-color label-text-size"><?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_comment'); ?></label>
                                    &nbsp;<span id="comment" class="text-content-color"></span>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div style="text-align: center;margin-top: 30px">
                                <label class="label-color"><?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objectives'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="department_objectives_table" class="<?php echo table_class(); ?>">
                                    <thead>
                                    <tr>
                                        <th style="width: 40%;border-radius: 3px 0 0 0;">
                                            <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_description'); ?>
                                        </th>
                                        <th style="width: 20%;border-radius: 0 3px 0 0;">
                                            <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_weight'); ?>
                                        </th>
                                        <th style="width: 40%;border-radius: 0 3px 0 0;">
                                            <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_assigned_in_tasks'); ?>
                                            <span style="background-color: #337ab7;width: 20px;height: 20px;margin-right: 10px;margin-left: 5px;display: inline-block;">&nbsp</span>
                                            <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_completed_percentage'); ?>
                                            <span style="background-color: #06d6a0;width: 20px;height: 20px;display: inline-block;margin-right: 10px;margin-left: 5px;">&nbsp</span>
                                        </th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <div>
                                    <label class="label sub-header-label-color label-text-size">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_total_weight'); ?>
                                    </label>
                                    &nbsp;<span id="total_weight" class="text-content-color"></span>%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">.
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <div class="text-right">
                                    <button class="btn btn-primary-new size-sm" id="regenerate_sub_departments" data-id=""
                                            onclick="regenerate_sub_departments.call(this)">
                                            <i class="fa fa-rotate-right mr-1"></i> <?php echo $this->lang->line('appraisal_regenerate_with_newly_added_sub_departments'); ?><!--Regenerate with newly added sub
                                        departments-->
                                    </button>
                                </div>    
                                <div class="wrap">
                                    <ul id="tab_header" class="tabs group">
                                    </ul>
                                    <div id="content">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="department_appraisal_task_modal" role="dialog"
                 aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="CommonEdit_Title">
                                <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_total_department_task'); ?>
                            </h4>
                        </div>

                        <div class="modal-body" style="overflow-y: scroll;height: 280px;">

                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="task_description">

                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_task_description'); ?>
                                    </label>
                                    <textarea type="text" id="task_description" class="form-control"></textarea>
                                    <div id="task_description_error" class="error-message"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <label for="task_weight">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_weight'); ?>

                                    </label>
                                    <input type="text" id="task_weight" class="form-control"/>
                                    <div id="task_weight_error" class="error-message"></div>
                                </div>
                                <div class="form-group col-sm-5">
                                    <label for="department_objective">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective'); ?>
                                    </label>
                                    <select class="form-control" id="department_objective"
                                            onchange="change_objective_used_percentage.call(this)">
                                    </select>
                                    <div id="department_objective_error" class="error-message"></div>
                                </div>
                                <div class="form-group">
                                    <label for="department_objective">

                                    </label>
                                    <div class="progress" style="height: 20px;    width: 100px;">
                                        <div id="add_task_form_progress_bar" class="progress-bar" role="progressbar"
                                             style="" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"></div>
                                        <span id="add_task_form_progress_bar_text" style=""></span></div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label for="assigned_employee">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_assigned_employee'); ?>  </label>
                                    <select class="form-control" id="assigned_employee">
                                    </select>
                                    <div id="assigned_employee_error" class="error-message"></div>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label for="date_to_complete">

                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_date_to_complete'); ?>
                                    </label>
                                    <input id="date_to_complete" class="form-control date-picker" autocomplete="off"/>
                                    <div id="date_to_complete_error" class="error-message"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="text-right m-t-xs">
                                <button class="btn btn-primary" id="add_deparment_task"
                                        onclick="add_department_task.call(this);" type="button">
                                    <?php echo $this->lang->line('common_add'); ?><!--Save & Next--></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="manager_review_modal" role="dialog"
                 aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="CommonEdit_Title">
                                <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_manager_review'); ?>
                            </h4>
                        </div>

                        <div class="modal-body" style="overflow-y: scroll;height: 280px;">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="manager_review_task_description">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_task_description'); ?>
                                    </label>
                                    <div id="manager_review_task_description"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label for="manager_review_assigned_employee">

                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_assigned_employee'); ?>
                                    </label>
                                    <div id="manager_review_assigned_employee"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label for="manager_review_completion">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_completion'); ?>
                                    </label>
                                    <div class="progress">
                                        <div id="manager_review_completion" class="progress-bar" role="progressbar"
                                             style="width: 0%" aria-valuenow="25" aria-valuemin="0"
                                             aria-valuemax="100"></div>
                                        <span id="manager_review_progressbar_text"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label for="manager_review_completion"><?php echo $this->lang->line('common_status'); ?>
                                        <!--Status--></label>
                                    <select class="form-control" id="manager_review_select">
                                        <option value="approved">
                                            <?php echo $this->lang->line('appraisal_master_corporate_goal_approved_column'); ?><!--Approved--></option>
                                        <option value="refer_back">
                                            <?php echo $this->lang->line('common_refer_back'); ?><!--Refer back--></option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <div class="text-right m-t-xs">
                                <button class="btn btn-primary" id=""
                                        onclick="manager_review_save.call(this)" type="button">
                                    <?php echo $this->lang->line('common_save'); ?><!--Save & Next--></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="task_discussion_modal" role="dialog"
                 aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-lg" style="width:54%">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="modal-title"
                                        id="CommonEdit_Title"><?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_messages'); ?></h4>
                                </div>
                                <div class="col-md-5">
                                    <label><?php echo $this->lang->line('common_reference'); ?><!--Ref--></label>
                                    <input type="text" id="msg_ref_search_text"/>
                                    <button id="msg_search_btn">
                                        <?php echo $this->lang->line('common_search'); ?><!--Search--></button>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal-body" id="chat-body" style="overflow-y: scroll;height: 280px;">
                            <div id="chat-messages"></div>
                        </div>
                        <div class="modal-footer">
                            <div class="row">
                                <div class="form-group col-sm-10">
                                    <input id="discussion_message" class="col-md-12"/>
                                </div>
                                <div class="form-group col-sm-2">
                                    <button class="btn btn-primary col-md-12" id="btn_send_message"
                                            onclick="send_message.call(this);" type="button">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_send'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php echo footer_page('Right foot', 'Left foot', false); ?>
            <script type="text/javascript">
                app = {};
                app.company_id = <?php echo current_companyID(); ?>;
                app.current_user_id = <?php echo current_userID(); ?>;
                app.config_department_id = localStorage.getItem('config_department_id');
                app.config_goal_id = localStorage.getItem('config_goal_id');
                app.department_objectives_table = $('#department_objectives_table').DataTable({
                    searching: false,
                    paging: false,
                    info: false
                });
                app.department_objectives = [];
                app.department_appraisal_header_id = null;
                app.is_closed = null; //this variable holds the close status of the corporate goal.

                $(document).ready(function () {
                    $('.date-picker').datepicker({format: 'yyyy-mm-dd'});
                    generate_document_for_department_appraisal();
                    $("#regenerate_sub_departments").attr("data-id", app.department_appraisal_header_id);
                    load_department_appraisal_details();
                    load_department_employees_dropdown();
                    load_department_objectives_dropdown();

                    $("#assigned_employee").select2();
                    $("#department_objective").select2();

                });

                $("#msg_search_btn").click(function () {
                    var key = $("#msg_ref_search_text").val();
                    scrollToBubble(key);
                });

                function regenerate_sub_departments() {
                    var department_appraisal_id = $(this).data('id');
                    startLoad();
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/regenerate_department_appraisal_with_newly_added_subdepartments'); ?>",
                        data: {
                            department_id: app.config_department_id,
                            department_appraisal_id: department_appraisal_id
                        },
                        success: function (data) {
                            generate_sub_departments();
                        }
                    });

                }

                function change_objective_used_percentage() {
                    var objective_id = $(this).val();
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_percentage_details'); ?>",
                        data: {
                            department_id: app.config_department_id,
                            goal_id: app.config_goal_id,
                            objective_id: objective_id,
                            department_appraisal_header_id: app.department_appraisal_header_id
                        },
                        success: function (data) {
                            set_progress_bar_with_text('add_task_form_progress_bar', data.used_percentage);
                        }
                    });
                }

                function _change_objective_used_percentage() {
                    var e = document.getElementById("department_objective");
                    var objective_id = e.options[e.selectedIndex].value;
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_percentage_details'); ?>",
                        data: {
                            department_id: app.config_department_id,
                            goal_id: app.config_goal_id,
                            objective_id: objective_id,
                            department_appraisal_header_id: app.department_appraisal_header_id
                        },
                        success: function (data) {
                            set_progress_bar_with_text('add_task_form_progress_bar', data.used_percentage);
                        }
                    });
                }

                $("#discussion_message").keyup(function (event) {
                    if (event.which == 13) {
                        send_message();
                    }
                });

                function load_chat_messages() {
                    $('#discussion_message').val("");
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/load_appraisal_task_discussion'); ?>",
                        data: {task_id: app.discussion_task_id},
                        success: function (data) {
                            var chat = '';
                            data.forEach(function (item, index) {
                                var uniqid = "";
                                var datetime = "";
                                if (item.uniqid != null) {
                                    uniqid = item.uniqid;
                                }
                                if (item.datetime != null) {
                                    datetime = item.datetime;
                                }
                                var bubble_content = '<div id="' + item.uniqid + '" style="margin-bottom: 10px;">' + item.message + '</div><div><span style="font-size: 11px;  margin-left: 10px;   left: 0px;   bottom: 4px;   position: absolute;">Ref: ' + uniqid + '</span> <span style="font-size: 11px;    margin-left: 10px;    right: 6px;    bottom: 4px;    position: absolute;">' + datetime + '</span></div>';
                                if (item.user_id == app.current_user_id) {
                                    chat += '<div class="speech-bubble">' + bubble_content + '</div>';
                                } else {
                                    chat += '<div class="speech-bubble2">' + bubble_content + '</div>';
                                }
                                chat += '<div style="clear: both;"></div>';
                            });
                            chat += '<div id="chat-end">&nbsp</div>'
                            $('#chat-messages').html(chat);
                        }
                    });
                }

                function send_message() {
                    var discussion_message = $('#discussion_message').val();
                    if (discussion_message != "") {
                        $.ajax({
                            dataType: "json",
                            type: "POST",
                            url: "<?php echo site_url('Appraisal/send_message'); ?>",
                            data: {task_id: app.discussion_task_id, message: discussion_message},
                            success: function (data) {
                                load_chat_messages();
                                load_sub_department_tasks();
                            }
                        });
                    }
                }

                function manager_review_save() {
                    var status = $("#manager_review_select").val();
                    var task_id = app.manager_review_task_id;
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/manager_review_save'); ?>",
                        data: {status: status, task_id: task_id},
                        success: function (data) {
                            if (data.status == 'success') {
                                load_sub_department_tasks();
                                $('#manager_review_modal').modal('hide');
                                myAlert('s', '<?php echo $this->lang->line('appraisal_successfully_saved'); ?>');/*Successfully saved*/
                            } else if (data.status == 'not_approved_by_manager') {
                                myAlert('e', '<?php echo $this->lang->line('appraisal_this_task_has_not_approved_by_the_manager'); ?>');/*This task has not approved by the manager*/
                            } else if (data.status == 'db_update_error') {
                                myAlert('e', '.');/*Database error*/
                            }

                        }
                    });
                }

                function department_task_form_edit_validation() {

                    var task_description = $("#task_description").val();
                    var task_weight = $("#task_weight").val();
                    var department_objective_id = $("#department_objective").val();
                    var date_to_complete = $("#date_to_complete").val();
                    var emp_id = $("#assigned_employee").val();
                    app.is_valid = true;

                    var is_emp_already_approved_for_performance = app.employee_performance_status_by_emp_id[emp_id];

                    if(app.appraisal_sub_department_id==""){
                        myAlert('e', 'Internal Error: appraisal_sub_department_id not set.');
                        app.is_valid = false;
                    }

                    if (task_description == "") {
                        app.is_valid = false;
                        show_error('task_description_error', '<?php echo $this->lang->line('appraisal_task_description_is_required'); ?>');/*Task description is required*/
                    } else {
                        hide_error('task_description_error');
                    }

                    if (department_objective_id == "") {
                        app.is_valid = false;
                        show_error('department_objective_error', '<?php echo $this->lang->line('appraisal_objective_is_required'); ?>');/*Objective is required*/
                    } else {
                        hide_error('department_objective_error');
                    }


                    if (emp_id == "") {
                        app.is_valid = false;
                        show_error('assigned_employee_error', '<?php echo $this->lang->line('common_employee_is_required'); ?>');/*Employee is required*/
                    } else {
                        if (is_emp_already_approved_for_performance == 1) {
                            app.is_valid = false;
                            show_error('assigned_employee_error', '<?php echo $this->lang->line('appraisal_this_employee_already_approved_in_this_appraisal'); ?>');/*This employee already approved in this appraisal*/
                        } else {
                            hide_error('assigned_employee_error');
                        }
                    }

                    if (task_weight == "") {
                        app.is_valid = false;
                        show_error('task_weight_error', '<?php echo $this->lang->line('appraisal_weight_is_required'); ?>');/*Weight is required*/
                    } else {
                        //hide_error('task_weight_error');
                        $.ajax({
                            async: false,
                            dataType: "json",
                            type: "POST",
                            url: "<?php echo site_url('Appraisal/get_percentage_details'); ?>",
                            data: {
                                department_id: app.config_department_id,
                                goal_id: app.config_goal_id,
                                objective_id: department_objective_id,
                                department_appraisal_header_id: app.department_appraisal_header_id
                            },
                            success: percentage_validation
                        });
                        app.is_valid = app.is_valid;
                    }

                    if (date_to_complete == "") {
                        app.is_valid = false;
                        show_error('date_to_complete_error', '<?php echo $this->lang->line('common_date_is_required'); ?>');/*Date is required*/
                    } else {
                        if (app.current_date_value != date_to_complete) {
                            var d = new Date(date_to_complete);
                            var from = new Date(app.goal_from);
                            var to = new Date(app.goal_to);

                            var month = format_for_two_digits((from.getMonth() + 1));
                            var date = format_for_two_digits(from.getDate());
                            var from_display_format = from.getFullYear() + '-' + month + '-' + date;

                            var month = format_for_two_digits((to.getMonth() + 1));
                            var date = format_for_two_digits(to.getDate());
                            var to_display_format = to.getFullYear() + '-' + month + '-' + date;


                            if (from <= d && d <= to) {
                                hide_error('date_to_complete_error');
                            } else {
                                app.is_valid = false;
                                let message = '<?php echo $this->lang->line('appraisal_completion_date_should_be_in_goal_period'); ?> (' + from_display_format + ' - ' + to_display_format + ')';/*Completion date should be in goal period*/
                                show_error('date_to_complete_error', message);
                            }
                        } else {
                            hide_error('date_to_complete_error');
                        }
                    }
                    return app.is_valid;
                }

                function department_task_form_validation() {

                    var task_description = $("#task_description").val();
                    var task_weight = $("#task_weight").val();
                    var department_objective_id = $("#department_objective").val();
                    var date_to_complete = $("#date_to_complete").val();
                    var emp_id = $("#assigned_employee").val();
                    var used_percentage = $("#add_task_form_progress_bar").css("width");
                    used_percentage = used_percentage.replace('px', '');


                    app.is_valid = true;


                    var is_emp_already_approved_for_performance = app.employee_performance_status_by_emp_id[emp_id];


                    if(app.appraisal_sub_department_id==""){
                        myAlert('e', 'Internal Error: appraisal_sub_department_id not set.');
                        app.is_valid = false;
                    }

                    if (department_objective_id == "") {
                        app.is_valid = false;
                        show_error('department_objective_error', '<?php echo $this->lang->line('appraisal_objective_is_required'); ?>');/*Objective is required*/
                    } else {
                        hide_error('department_objective_error');
                    }


                    if (emp_id == "") {
                        app.is_valid = false;
                        show_error('assigned_employee_error', '<?php echo $this->lang->line('common_employee_is_required'); ?>');/*Employee is required*/
                    } else {
                        if (is_emp_already_approved_for_performance == 1) {
                            app.is_valid = false;
                            show_error('assigned_employee_error', '<?php echo $this->lang->line('appraisal_this_employee_already_approved_in_this_appraisal'); ?>');/*This employee already approved in this appraisal*/
                        } else {
                            hide_error('assigned_employee_error');
                        }
                    }


                    if (used_percentage == 100) {
                        // app.is_valid = false;
                    }


                    if (task_description == "") {
                        app.is_valid = false;
                        show_error('task_description_error', '<?php echo $this->lang->line('appraisal_task_description_is_required'); ?>');/*Task description is required*/
                    } else {
                        hide_error('task_description_error');
                    }

                    if (task_weight == "") {
                        app.is_valid = false;
                        show_error('task_weight_error', '<?php echo $this->lang->line('appraisal_weight_is_required'); ?>');/*Weight is required*/
                    } else {
                        //hide_error('task_weight_error');
                        $.ajax({
                            async: false,
                            dataType: "json",
                            type: "POST",
                            url: "<?php echo site_url('Appraisal/get_percentage_details'); ?>",
                            data: {
                                department_id: app.config_department_id,
                                goal_id: app.config_goal_id,
                                objective_id: department_objective_id,
                                department_appraisal_header_id: app.department_appraisal_header_id
                            },
                            success: percentage_validation
                        });
                        app.is_valid = app.is_valid;
                    }

                    if (date_to_complete == "") {
                        app.is_valid = false;
                        show_error('date_to_complete_error', '<?php echo $this->lang->line('common_date_is_required'); ?>');/*Date is required*/
                    } else {
                        var from = new Date(app.goal_from);
                        var to = new Date(app.goal_to);

                        var month = format_for_two_digits((from.getMonth() + 1));
                        var date = format_for_two_digits(from.getDate());
                        var from_display_format = from.getFullYear() + '-' + month + '-' + date;

                        var month = format_for_two_digits((to.getMonth() + 1));
                        var date = format_for_two_digits(to.getDate());
                        var to_display_format = to.getFullYear() + '-' + month + '-' + date;

                        if (from_display_format <= date_to_complete && date_to_complete <= to_display_format) {
                            hide_error('date_to_complete_error');
                        } else {
                            app.is_valid = false;
                            let message = '<?php echo $this->lang->line('appraisal_completion_date_should_be_in_goal_period'); ?> (' + from_display_format + ' - ' + to_display_format + ')';/*Completion date should be in goal period*/
                            show_error('date_to_complete_error', message);
                        }

                    }

                    return app.is_valid;
                }

                function percentage_validation(data) {
                    if (app.form_status == 'save') {
                        var task_weight = $("#task_weight").val();
                        if (data.remaining_percentage < task_weight) {
                            app.is_valid = false;
                            show_error('task_weight_error', '<?php echo $this->lang->line('appraisal_remaining_quota_is'); ?> ' + data.remaining_percentage + '%');/*Remaining Quota is*/
                        } else {
                            hide_error('task_weight_error');
                        }
                    } else if (app.form_status == 'edit') {

                        var task_weight = $("#task_weight").val();
                        var remaining_percentage = parseFloat(data.remaining_percentage) + parseFloat(app.current_task_weight);
                        if (remaining_percentage < task_weight) {
                            app.is_valid = false;
                            show_error('task_weight_error', '<?php echo $this->lang->line('appraisal_remaining_quota_is'); ?> ' + remaining_percentage + '%');/*Remaining Quota is*/
                        } else {
                            hide_error('task_weight_error');
                        }
                    }

                }


                function add_department_task() {
                    if (app.form_status == 'save') {
                        if (department_task_form_validation()) {
                            startLoad();
                            var task_description = $("#task_description").val();
                            var task_weight = $("#task_weight").val();
                            var department_objective_id = $("#department_objective").val();
                            var assigned_employee_id = $("#assigned_employee").val();
                            var date_to_complete = $("#date_to_complete").val();
                            $.ajax({
                                dataType: "json",
                                type: "POST",
                                url: "<?php echo site_url('Appraisal/insert_department_task'); ?>",
                                data: {
                                    appraisal_sub_department_id: app.appraisal_sub_department_id,
                                    task_description: task_description,
                                    task_weight: task_weight,
                                    department_objective_id: department_objective_id,
                                    assigned_employee_id: assigned_employee_id,
                                    date_to_complete: date_to_complete,
                                    department_appraisal_header_id: app.department_appraisal_header_id,
                                    task_created_user_type: 'manager'
                                },
                                success: function (data) {
                                    stopLoad();
                                    load_sub_department_tasks();
                                    load_department_appraisal_details();
                                    $("#department_appraisal_task_modal").modal('hide');
                                }
                            });
                        }
                    } else if (app.form_status == 'edit') {
                        if (department_task_form_edit_validation()) {
                            startLoad();
                            var task_description = $("#task_description").val();
                            var task_weight = $("#task_weight").val();
                            var department_objective_id = $("#department_objective").val();
                            var assigned_employee_id = $("#assigned_employee").val();
                            var date_to_complete = $("#date_to_complete").val();
                            //////////////////
                            $.ajax({
                                dataType: "json",
                                type: "POST",
                                url: "<?php echo site_url('Appraisal/edit_department_task'); ?>",
                                data: {
                                    task_description: task_description,
                                    task_weight: task_weight,
                                    department_objective_id: department_objective_id,
                                    assigned_employee_id: assigned_employee_id,
                                    date_to_complete: date_to_complete,
                                    task_id: app.task_id
                                },
                                success: function (data) {
                                    stopLoad();
                                    load_sub_department_tasks();
                                    load_department_appraisal_details();
                                    $("#department_appraisal_task_modal").modal('hide');
                                }
                            });
                        }
                    }

                }

                function load_department_employees_dropdown(selected_value) {
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_department_employees'); ?>",
                        data: {department_id: app.config_department_id},
                        success: function (data) {
                            var employees = "";
                            employees = '<option value="">Select an option</option>';
                            data.forEach(function (item, index) {
                                if (selected_value == item.EIdNo) {
                                    select_status = "selected";
                                } else {
                                    select_status = "";
                                }
                                employees += '<option ' + select_status + ' value="' + item.EIdNo + '">' + item.Ename1 + ' - ' + item.DepartmentDes + '</option>';
                            });
                            //app.department_employees_drop_down_list_html = employees;
                            $('#assigned_employee').html(employees);
                        }
                    });
                }

                function load_department_objectives_dropdown(selected_value) {
                    var objectives = "";
                    objectives = '<option value="">Select an option</option>';
                    app.department_objectives.forEach(function (item, index) {
                        if (selected_value == item.corporate_objective_id) {
                            select_status = "selected";
                        } else {
                            select_status = "";
                        }

                        objectives += '<option ' + select_status + ' value="' + item.corporate_objective_id + '">' + item.objective_description + '</option>';
                    });

                    if (objectives == "") {
                        app.task_button_disabled = "disabled";
                    } else {
                        app.task_button_disabled = "";
                        $('#department_objective').html("");
                        $('#department_objective').html(objectives);

                        if (selected_value == null) {
                            selected_value = $('#department_objective').val();//app.department_objectives[0].corporate_objective_id;
                        }

                        $.ajax({
                            async: false,
                            dataType: "json",
                            type: "POST",
                            url: "<?php echo site_url('Appraisal/get_percentage_details'); ?>",
                            data: {
                                department_id: app.config_department_id,
                                goal_id: app.config_goal_id,
                                objective_id: selected_value,
                                department_appraisal_header_id: app.department_appraisal_header_id
                            },
                            success: function (data) {
                                set_progress_bar_with_text('add_task_form_progress_bar', data.used_percentage);
                            }
                        });
                    }

                }


                function generate_document_for_department_appraisal() {
                    startLoad();
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/generate_document_for_department_appraisal'); ?>",
                        data: {department_id: app.config_department_id, goal_id: app.config_goal_id},
                        success: function (data) {

                            var d = new Date(data.created_at);
                            var month = format_for_two_digits((d.getMonth() + 1));
                            var date = format_for_two_digits(d.getDate());
                            var created_date = d.getFullYear() + '-' + month + '-' + date;
                            $("#date_created").text(created_date);
                            app.department_appraisal_header_id = data.id;
                            $("#department_appraisal_doucment_id").text(data.document_id);

                            generate_sub_departments();

                        }
                    });
                }

                function generate_sub_departments() {

                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_sub_departments_by_department_id'); ?>",
                        data: {department_appraisal_header_id: app.department_appraisal_header_id},
                        success: function (data) {
                            app.sub_department_list = data;
                            if (app.sub_department_list.length == 0) {
                                let message = '<div style="margin-top:20px;color: red;"><?php echo $this->lang->line('appraisal_no_sub_departments'); ?></div>';/*No sub departments*/
                                $('#tab_header').html(message);
                                stopLoad();
                            } else {
                                var tab_header = "";
                                data.forEach(function (item, index) {
                                    if (index == 0) {
                                        tab_header += '<li><a onclick="tab_click.call(this)" class="active tab-title" data-tab_id="#tab' + item.id + '">' + item.description + '</a></li>';
                                    } else {
                                        tab_header += '<li><a onclick="tab_click.call(this)" class="tab-title" data-tab_id="#tab' + item.id + '">' + item.description + '</a></li>';
                                    }
                                });
                                $('#tab_header').html(tab_header);

                                var tab_body = "";
                                var table_ids_array = [];
                                data.forEach(function (item, index) {
                                    var style = null;
                                    if (index == 0) {
                                        style = "";
                                    } else {
                                        style = "display: none;";
                                    }
                                    tab_body += '<div class="tab-content-div" style="' + style + '" id="tab' + item.id + '">';
                                    tab_body += '<table id="table' + item.id + '" class="<?php echo table_class(); ?> sub-dep-task">' +
                                        '                                        <thead>' +
                                        '                                        <tr>' +
                                        '                                            <th style="min-width: 15%;border-radius: 3px 0 0 0;">' +
                                        '                                                <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_task_description"); ?>' +
                                        '                                            </th>' +
                                        '                                            <th style="min-width: 15%;border-radius: 0 0 0 0;">' +
                                        '                                                 <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_weight"); ?>' +
                                        '                                            </th>' +
                                        '                                            <th style="min-width: 15%;border-radius: 0 0 0 0;">' +
                                        '                                                <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_assigned_employee"); ?>' +
                                        '                                            </th>' +
                                        '                                            <th style="min-width: 15%;border-radius: 0 0 0 0;">' +
                                        '                                                <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_date_to_complete"); ?>' +
                                        '                                            </th>' +
                                        '                                            <th style="min-width: 15%;border-radius: 0 0 0 0;">' +
                                        '                                                <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_is_approved_by_manager"); ?>' +
                                        '                                            </th>' +
                                        '                                            <th style="min-width: 15%;border-radius: 0 0 0 0;">' +
                                        '                                                <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_completion"); ?>' +
                                        '                                            </th>' +
                                        '                                            <th style="min-width: 15%;border-radius: 0 0 0 0;">' +
                                        '                                                <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_manager_review"); ?>' +
                                        '                                            </th>' +
                                        '                                            <th style="min-width: 15%;border-radius: 0 0 0 0;">' +
                                        '                                                <?php echo $this->lang->line("appraisal_master_subdepartment_actions_column"); ?>' +
                                        '                                            </th>' +
                                        '                                            </th>' +
                                        '                                            <th style="min-width: 15%;border-radius: 0 0 0 0;">' +
                                        '                                                <?php echo $this->lang->line("appraisal_master_subdepartment_discussion_column"); ?>' +
                                        '                                            </th>' +
                                        '                                        </tr>' +
                                        '                                        </thead>' +
                                        '                                    </table>';

                                    tab_body += '<button ' + app.task_button_disabled + ' class="btn btn-default btn-task-popup" onclick="sub_department_task_add_btn_click.call(this);" data-appraisal_sub_department_id="' + item.id + '"><i class="fa fa-plus"></i></button>' +
                                        '</div>';
                                    var table_id = 'table' + item.id;
                                    table_ids_array.push(table_id);
                                });
                                $('#content').html(tab_body);

                                //disabling task button if all the objectives 100% assigned.
                                if (app.task_button_disabled == "disabled") {
                                    $(".btn-task-popup").attr('disabled', true);
                                    $(".btn-task-popup").attr('title', "Objectives have been 100% assigned.");
                                }

                                //initializing data table for all generated tables.
                                app.sub_department_tables_array = []
                                table_ids_array.forEach(function (item, index) {
                                    var table_id_selector = '#' + item;
                                    app.sub_department_tables_array[item] = $(table_id_selector).DataTable({
                                        "language": {
                                            "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                                        },
                                        searching: false,
                                        paging: false,
                                        info: false
                                    });
                                });
                                load_sub_department_tasks();

                            }

                        }
                    });
                }

                function load_sub_department_tasks() {
                    app.employee_performance_status_by_emp_id = new Array();
                    app.sub_department_list.forEach(function (item, index) {
                        $.ajax({
                            async: false,
                            dataType: "json",
                            type: "POST",
                            url: "<?php echo site_url('Appraisal/get_sub_department_tasks'); ?>",
                            data: {
                                sub_department_id: item.id,
                                department_appraisal_id: app.department_appraisal_header_id
                            },
                            success: function (data) {
                                var array_index = 'table' + item.id;
                                app.sub_department_tables_array[array_index].clear().draw();
                                data.forEach(function (element, index) {

                                    var short_description = element.objective_description.substring(0, 18) + '...';

                                    var description_title = element.task_description + ' (' + element.objective_description + ')';
                                    var task_description = '<span title="' + description_title + '">' + element.task_description + ' (' + short_description + '</span>';
                                    var weight = element.weight;
                                    var Ename1 = element.Ename1;


                                    d = new Date(element.date_to_complete);
                                    var month = format_for_two_digits((d.getMonth() + 1));
                                    var date = format_for_two_digits(d.getDate());
                                    var date_to_complete = d.getFullYear() + '-' + month + '-' + date;


                                    var progress_bar_text_color = 'black';
                                    if (element.completion >= 60) {
                                        progress_bar_text_color = 'white';
                                    }
                                    var completion = '<div class="progress" style="height: 20px;">' +
                                        '  <div class="progress-bar" role="progressbar" style="width: ' + element.completion + '%;" aria-valuenow="' + element.completion + '" aria-valuemin="0" aria-valuemax="100"></div>' +
                                        '<span style="color: ' + progress_bar_text_color + ';">' + element.completion + '%</span>' +
                                        '</div>';
                                    var manager_review = '';
                                    if (element.manager_review == 'pending') {
                                        manager_review = '<?php echo $this->lang->line('common_pending') ?>';/*Pending*/
                                    } else if (element.manager_review == 'rejected') {
                                        manager_review = '<?php echo $this->lang->line('common_rejected') ?>';/*Rejected*/
                                    } else if (element.manager_review == 'approved') {
                                        manager_review = '<?php echo $this->lang->line('common_approved') ?>';/*Approved*/
                                    } else if (element.manager_review == 'refer_back') {
                                        manager_review = '<?php echo $this->lang->line('common_referred_back') ?>';/*Referred Back*/
                                    }

                                    if (element.message == null) {
                                        element.message = '<?php echo $this->lang->line('appraisal_start_a_discussion') ?>';/*Start a discussion*/
                                    }
                                    var discussion = '<div data-task_id="' + element.task_id + '" onclick="show_discussion_dialog.call(this)" style="text-decoration: underline;color: #2185d0;float: right;cursor: pointer;text-align: right;">' + element.message + '</div>';
                                    var action = '';
                                    action += '<i onclick="sub_department_task_edit_popup.call(this)" title="<?php echo $this->lang->line('common_edit') ?>" data-task_id="' + element.task_id + '" class="glyphicon glyphicon-pencil corporate-goal-edit act-btn-margin" style="color: #3c8dbc;"></i>';
                                    action += '<i onclick="sub_department_task_delete.call(this)" title="<?php echo $this->lang->line('common_delete') ?>" data-task_id="' + element.task_id + '" class="glyphicon glyphicon-trash act-btn-margin btn-task-delete" style="color: #ff3f3a;"></i>';
                                    action += '<i onclick="sub_department_manager_review.call(this)" title="<?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_manager_review') ?>" data-current_status="' + element.manager_review + '" data-completion="' + element.completion + '" data-assigned_employee="' + Ename1 + '" data-task_description="' + element.task_description + '" data-task_id="' + element.task_id + '" class="glyphicon glyphicon-flag act-btn-margin btn-task-flag" style="color: #3c8dbc;"></i>';


                                    var is_approved_for_emp_performance = element.employee_performance_approved;
                                    app.employee_performance_status_by_emp_id[element.employee_id] = is_approved_for_emp_performance;

                                    var manager_approved_checkbox_disable_status = "";
                                    var manager_approved_checkbox_title = "";
                                    if (is_approved_for_emp_performance == 1) {
                                        manager_approved_checkbox_disable_status = "disabled";
                                        manager_approved_checkbox_title = "<?php echo $this->lang->line('appraisal_already_approved_this_task_for_employee_performance') ?>";/*Already approved this task for employee performance.*/
                                    }

                                    var is_approved_by_manager = element.is_approved_by_manager;
                                    var manager_approved_checkbox_status = "";
                                    if (is_approved_by_manager == 1) {
                                        manager_approved_checkbox_status = "checked";
                                    }
                                    if (element.is_closed == 1) {
                                        manager_approved_checkbox_disable_status = "disabled";
                                    }
                                    var manager_approve_checkbox = '<input title="' + manager_approved_checkbox_title + '" ' + manager_approved_checkbox_disable_status + ' data-task_id="' + element.task_id + '" type="checkbox" ' + manager_approved_checkbox_status + ' onchange="task_approve_checkbox_change_event.call(this)"/>';

                                    if (is_approved_for_emp_performance == 1) {
                                        action = '<i title="<?php echo $this->lang->line('appraisal_already_approved_this_task_for_employee_performance') ?>" class="fa fa-ban"></i>';
                                    }
                                    if (element.is_closed == 1) {
                                        action = '<i title="<?php echo $this->lang->line('appraisal_goal_is_closed') ?>" class="fa fa-ban"></i>';
                                    }
                                    app.sub_department_tables_array[array_index].row.add([task_description, weight, Ename1, date_to_complete, manager_approve_checkbox, completion, manager_review, action, discussion]).draw(false);
                                });
                                disable_user_conrolls_if_goal_is_closed();
                                stopLoad();
                            }
                        });
                    });
                }

                function task_approve_checkbox_change_event() {

                    var task_id = $(this).data('task_id');
                    var status = this.checked;
                    if (status == true) {
                        status = 1;
                    } else {
                        status = 0;
                    }
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/change_task_approval_status'); ?>",
                        data: {task_id: task_id, status: status},
                        success: function (data) {

                        }
                    });
                }

                function disable_user_conrolls_if_goal_is_closed() {


                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_goal_closed_status'); ?>",
                        data: {goal_id: app.config_goal_id},
                        success: function (data) {
                            if (data.is_closed == 1) {
                                $("#regenerate_sub_departments").attr('disabled', true);
                                $(".corporate-goal-edit").css('pointer-events', 'none');
                                $(".btn-task-delete").css('pointer-events', 'none');
                                $(".btn-task-flag").css('pointer-events', 'none');
                                $("#add_deparment_task").attr('disabled', true);
                                $("#btn_send_message").attr('disabled', true);
                                $(".btn-task-popup").attr('disabled', true);
                            }
                        }
                    });
                }

                function show_discussion_dialog() {
                    $('#discussion_message').val("");
                    app.discussion_task_id = $(this).data('task_id');
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/load_appraisal_task_discussion'); ?>",
                        data: {task_id: app.discussion_task_id},
                        success: function (data) {
                            var chat = '';
                            data.forEach(function (item, index) {

                                var uniqid = "";
                                var datetime = "";
                                if (item.uniqid != null) {
                                    uniqid = item.uniqid;
                                }
                                if (item.datetime != null) {
                                    datetime = item.datetime;
                                }
                                var bubble_content = '<div id="' + item.uniqid + '" style="margin-bottom: 10px;">' + item.message + '</div><div><span style="font-size: 11px;  margin-left: 10px;   left: 0px;   bottom: 4px;   position: absolute;">Ref: ' + uniqid + '</span> <span style="font-size: 11px;    margin-left: 10px;    right: 6px;    bottom: 4px;    position: absolute;">' + datetime + '</span></div>';
                                if (item.user_id == app.current_user_id) {
                                    chat += '<div class="speech-bubble">' + bubble_content + '</div>';
                                } else {
                                    chat += '<div class="speech-bubble2">' + bubble_content + '</div>';
                                }
                                chat += '<div style="clear: both;"></div>';
                            });
                            chat += '<div id="chat-end">&nbsp</div>'
                            $('#chat-messages').html(chat);
                            $('#task_discussion_modal').modal('show');

                        }
                    });
                }

                function scrollToBubble(key) {
                    var key_id = '#' + key;

                    $(key_id).css('background-color', 'yellow');
                    $(key_id).css('color', 'black');
                    let $container = $("#chat-body"), $scrollTo = $(key_id);
                    if ($container.length > 0) {

                        $container.animate({
                            scrollTop: $scrollTo.offset().top - $container.offset().top + $container.scrollTop() - 200,
                            scrollLeft: 0
                        }, 1000);
                    }

                }


                function sub_department_manager_review() {
                    var completion = $(this).data('completion');
                    var assigned_employee = $(this).data('assigned_employee');
                    var task_description = $(this).data('task_description');
                    var current_status = $(this).data('current_status');
                    app.manager_review_task_id = $(this).data('task_id');//saving id for next function

                    $('#manager_review_task_description').text(task_description);
                    $('#manager_review_assigned_employee').text(assigned_employee);
                    $('#manager_review_progressbar_text').text(completion + '%');
                    set_progress_bar('manager_review_completion', completion);
                    var progress_bar_text_color = 'black';
                    if (completion >= 60) {
                        progress_bar_text_color = 'white';
                    }
                    $('#manager_review_progressbar_text').css('color', progress_bar_text_color);
                    $("#manager_review_select").val(current_status);

                    $('#manager_review_modal').modal('show');
                }

                function sub_department_task_delete() {
                    var task_id = $(this).data('task_id');
                    bootbox.confirm({
                        message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_delete_this_task') ?>",/*Are you sure you want to delete this task?*/
                        buttons: {
                            confirm: {
                                label: '<?php echo $this->lang->line('common_yes'); ?>',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: '<?php echo $this->lang->line('common_yes'); ?>',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (user_confirmation) {
                            if (user_confirmation) {
                                startLoad();
                                $.ajax({
                                    dataType: "json",
                                    type: "POST",
                                    url: "<?php echo site_url('Appraisal/delete_sub_department_task'); ?>",
                                    data: {task_id: task_id},
                                    success: function (data) {
                                        stopLoad();
                                        load_sub_department_tasks();
                                        load_department_appraisal_details();
                                    }
                                });
                            }
                        }
                    });
                }

                function set_progress_bar(id, value) {
                    var width = value + '%';
                    $('#' + id).css('width', width);
                }

                function set_progress_bar_with_text(id, value) {
                    var width = value + '%';
                    $('#' + id).css('width', width);
                    var progress_bar_text = value + '%';

                    var text_span_id = '#' + id + '_text';
                    $(text_span_id).html(progress_bar_text);

                    if (parseFloat(value) > 60) {
                        $(text_span_id).css('color', 'white');
                    } else {
                        $(text_span_id).css('color', 'black');
                    }

                }

                function format_for_two_digits(num) {
                    if (num < 10) {
                        return '0' + num;
                    } else {
                        return num;
                    }
                }

                function sub_department_task_edit_popup() {
                    hide_form_errors();
                    var task_id = $(this).data('task_id');
                    app.task_id = task_id;
                    app.form_status = 'edit';
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_sub_department_tasks_by_id'); ?>",
                        data: {sub_department_task_id: task_id},
                        success: function (data) {
                            $("#task_description").val(data[0].task_description);
                            $("#task_weight").val(data[0].weight);
                            app.current_task_weight = data[0].weight;
                            load_department_objectives_dropdown(data[0].department_objective_id);
                            d = new Date(data[0].date_to_complete);
                            var month = format_for_two_digits((d.getMonth() + 1));
                            var date = format_for_two_digits(d.getDate());
                            var to = d.getFullYear() + '-' + month + '-' + date;
                            $('#date_to_complete').val(to);
                            app.current_date_value = to;
                            load_department_employees_dropdown(data[0].employee_id);
                            $("#department_appraisal_task_modal").modal('show');
                        }
                    });
                }

                function sub_department_task_add_btn_click() {
                    app.form_status = 'save';
                    app.appraisal_sub_department_id = $(this).data('appraisal_sub_department_id');
                    hide_form_errors();
                    clear_values_in_task_form();
                    _change_objective_used_percentage();
                    $("#department_appraisal_task_modal").modal('show');
                }

                function clear_values_in_task_form() {
                    $("#task_description").val("");
                    $("#task_weight").val("");
                    $("#date_to_complete").val("");
                    $("#department_objective").val("").trigger('change');
                    $("#assigned_employee").val("").trigger('change');
                    set_progress_bar_with_text('add_task_form_progress_bar', 0);
                }

                function hide_form_errors() {
                    hide_error('task_description_error');
                    hide_error('task_weight_error');
                    hide_error('date_to_complete_error');
                    hide_error('department_objective_error');
                    hide_error('assigned_employee_error');
                }

                function load_department_appraisal_details() {
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/department_appraisal_details'); ?>",
                        data: {
                            department_id: app.config_department_id,
                            goal_id: app.config_goal_id,
                            department_appraisal_header_id: app.department_appraisal_header_id
                        },
                        success: function (data) {

                            var d = new Date(data[0].from);
                            var period = d.getFullYear();
                            var document_id = data[0].document_id;
                            var narration = data[0].narration;
                            app.goal_from = data[0].from;
                            app.goal_to = data[0].to;
                            var department_name = data[0].DepartmentDes;

                            $("#goal_doucment_id").text(document_id);
                            $("#period").text(period);
                            $("#comment").text(narration);
                            $("#goal_department_name").text(department_name);
                            var total_weight = 0;
                            app.department_objectives_table.clear().draw();
                            app.department_objectives = new Array();
                            data.forEach(function (item, index) {
                                total_weight += parseFloat(item.weight);
                                var weight = '<div style="text-align:center;">' + item.weight + '</div>';
                                var progress_bar_text_color = 'black';
                                if (item.used_percentage >= 60) {
                                    progress_bar_text_color = 'white';
                                }
                                $('#manager_review_progressbar_text').css('color', progress_bar_text_color);
                                var task_assigned_percentage = '<div class="progress" style="height: 20px;width: 40%;float: left;margin-right: 5px;">' +
                                    '  <div class="progress-bar" role="progressbar" style="width: ' + item.used_percentage + '%;" aria-valuenow="' + item.used_percentage + '" aria-valuemin="0" aria-valuemax="100"></div>' +
                                    '<span style="color: ' + progress_bar_text_color + ';">' + parseFloat(item.used_percentage).toFixed(1) + '%</span>' +
                                    '</div>';

                                var progress_bar_text_color = 'black';
                                if (item.completion_percentage >= 60) {
                                    progress_bar_text_color = 'white';
                                }
                                var task_completion_percentage = '<div class="progress" style="height: 20px;width: 40%;float: left;margin-right: 5px;">' +
                                    '  <div class="progress-bar progress-bar-success" role="progressbar" style="width: ' + item.completion_percentage + '%;" aria-valuenow="' + item.completion_percentage + '" aria-valuemin="0" aria-valuemax="100"></div>' +
                                    '<span style="color: ' + progress_bar_text_color + ';">' + item.completion_percentage.toFixed(1) + '%</span>' +
                                    '</div>';
                                var percentage_column = task_assigned_percentage + task_completion_percentage;
                                app.department_objectives_table.row.add([item.objective_description, weight, percentage_column]).draw(false);
                                app.department_objectives.push(item);
                            });
                            $("#total_weight").text(total_weight);
                        }
                    });
                }

                function show_error(errorDivId, errorMessage) {
                    var divSelector = "#" + errorDivId;
                    $(divSelector).html(errorMessage);
                }

                function hide_error(errorDivId) {
                    var divSelector = "#" + errorDivId;
                    $(divSelector).html("");
                }
            </script>

            <script>
                function tab_click() {
                    var tabs = $(".tabs li a");
                    var content = $(this).data('tab_id');
                    tabs.removeClass("active");
                    $(this).addClass("active");
                    $("#content").find('.tab-content-div').hide();
                    $(content).fadeIn(200);
                }
            </script>
