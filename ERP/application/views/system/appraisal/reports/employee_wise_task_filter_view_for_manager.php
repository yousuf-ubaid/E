<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_employee_wise_performance');
$rating_data = getAppraisalRatingData();
?>
<style>
    @media print {
        #print_view {
            display: block
        }
    }

    .btn-result {
        margin-top: 25px;
    }

    .fa-ban {
        color: red;
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

    .manager_comment_label {
        vertical-align: top;
    }

    .manager_comment_text {
        width: 100%;
    }


    .manager_comment_label2 {
        vertical-align: top;
    }

    .manager_comment_text2 {
        width: 100%;
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

    table, td, th {
        border: 1px solid darkgrey !important;
    }


    /* The customcheck */
    .customcheck {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 22px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default checkbox */
    .customcheck input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: -5px;
        left: 58px;
        height: 25px;
        width: 25px;
        background-color: #eee;
        border-radius: 5px;
    }

    /* On mouse-over, add a grey background color */
    .customcheck:hover input ~ .checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .customcheck input:checked ~ .checkmark {
        background-color: #02cf32;
        border-radius: 5px;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .customcheck input:checked ~ .checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .customcheck .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .manager_comment_label {
        vertical-align: top;
    }

    .manager_comment_text {
        width: 100%;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">

                </div>
            </div>
            <link rel="stylesheet"
                  href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>


            <div class="row" style="margin: 1%">
                <ul class="nav nav-tabs mainpanel">
                    <li class="active">
                        <a class="" data-id="0" href="#step3" data-toggle="tab" aria-expanded="true">
                                <span>
                                    <i class="fa fa-cog tachometerColor" aria-hidden="true"
                                       style="color: #50749f;font-size: 16px;"></i>&nbsp;&nbsp;Employee Wise Performance (Objectives)
                                </span>
                        </a>
                    </li>
                    <li class="">
                        <a class="" data-id="0" href="#step4" data-toggle="tab" aria-expanded="true">
                                <span>
                                    <i class="fa fa-list tachometerColor" aria-hidden="true"
                                       style="color: #50749f;font-size: 16px;"></i>Employee Wise Performance (Soft-skills)
                                </span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div id="step3" class="tab-pane active">
                    <div class="row" style="margin: 20px;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <div id="normal_view">
                                        <div class="row">
                                            <div class="form-group col-sm-3">
                                                <label for="department_objective">
                                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal'); ?>
                                                </label>
                                                <select class="form-control" id="corporate_goals_dropdown">
                                                </select>
                                                <div id="corporate_goals_dropdown_error" class="error-message"></div>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="department_objective">
                                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department'); ?>
                                                </label>
                                                <select class="form-control" id="departments_dropdown"
                                                        onchange="department_dropdown_onchange()">
                                                </select>
                                                <div id="departments_dropdown_error" class="error-message"></div>
                                            </div>
                                            <div class="form-group col-sm-3">
                                                <label for="department_objective">
                                                    <?php echo $this->lang->line('appraisal_activity_department_employee'); ?>
                                                </label>
                                                <select class="form-control" id="employees_dropdown">
                                                </select>
                                                <div id="employees_dropdown_error" class="error-message"></div>
                                            </div>
                                            <div class="form-group col-sm-3">


                                                <button class="btn btn-success btn-result"
                                                        onclick="get_employee_wise_report()">
                                                    <?php echo $this->lang->line("appraisal_fetch"); ?><!--Fetch-->


                                                </button>
                                                <button class="btn btn-primary btn-result" onclick="show_print_view()">
                                                    <?php echo $this->lang->line("appraisal_print_view"); ?><!--Print View-->
                                                </button>

                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table id="employee_wise_performance_table"
                                                       class="<?php echo table_class(); ?>">
                                                    <thead>
                                                    <tr>
                                                        <th style="min-width: 15%;border-radius: 3px 0 0 0;">
                                                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_task_description"); ?>
                                                        </th>
                                                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                                                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_weight"); ?>
                                                        </th>
                                                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                                                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_assigned_employee"); ?>
                                                        </th>
                                                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                                                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_date_to_complete"); ?>
                                                        </th>
                                                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                                                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_is_approved_by_manager"); ?>
                                                        </th>
                                                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                                                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_completion"); ?>
                                                        </th>
                                                        <th style="min-width: 15%;border-radius: 0 0 0 0;">
                                                            <?php echo $this->lang->line("appraisal_activity_department_appraisal_corporate_goal_department_objective_manager_review"); ?>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                            <div class="col-md-12">
                                                <?php echo $this->lang->line("appraisal_employee_wise_performance_task_completion_percentage"); ?>
                                                :<span id="task_completion_percentage"></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label class="manager_comment_label2">
                                                    <?php echo $this->lang->line('appraisal_activity_department_manager_comment'); ?>
                                                    :
                                                </label>
                                                <textarea id="manager_comment" class="manager_comment_text2"></textarea>
                                                <div id="manager_comment_error" class="error-message"></div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="manager_comment_label2">
                                                        <?php echo $this->lang->line('appraisal_suggested_reward'); ?>
                                                        &nbsp;<!--Suggested reward-->:
                                                    </label>
                                                    <textarea id="suggested_reward_input"
                                                              class="manager_comment_text2"></textarea>
                                                    <div id="suggested_reward_input_error" class="error-message"></div>
                                                </div>
                                            </div>


                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="manager_comment_label2">
                                                        <?php echo $this->lang->line('appraisal_identified_training_needs'); ?>
                                                        <!--Identified training needs -->:
                                                    </label>
                                                    <textarea id="identified_training_needs"
                                                              class="manager_comment_text2"></textarea>
                                                    <div id="identified_training_needs_error"
                                                         class="error-message"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="manager_comment_label2">
                                                        <?php echo $this->lang->line('appraisal_special_remarks_from_hod'); ?>
                                                        &nbsp;<!--Special remarks from HOD--> :
                                                    </label>
                                                    <textarea id="special_remarks_from_hod"
                                                              class="manager_comment_text2"></textarea>
                                                    <div id="special_remarks_from_hod_error"
                                                         class="error-message"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="manager_comment_label2">
                                                        <?php echo $this->lang->line('appraisal_employee_comment'); ?>
                                                        <!--Employee comment--> :
                                                    </label>
                                                    <textarea disabled="true" id="employee_comment"
                                                              class="manager_comment_text2" disabled></textarea>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="department_objective">
                                                    <?php echo $this->lang->line('appraisal_activity_department_approved_by'); ?>
                                                    :
                                                </label>
                                                <span id="approved_by"></span>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label for="department_objective">
                                                    <?php echo $this->lang->line('appraisal_activity_department_approved_date'); ?>
                                                    :
                                                </label>
                                                <span id="approved_date"></span>
                                            </div>
                                            <div class="col-md-3">

                                            </div>
                                            <div class="form-group col-md-3">
                                                <button id="btn_employee_performance_save_as_draft"
                                                        class="btn btn-primary pull-left"
                                                        style="float: right;"
                                                        onclick="performance_report_save_as_draft()">
                                                    <?php echo $this->lang->line('common_save_as_draft'); ?>
                                                    <!--Save as Draft-->
                                                </button>
                                                <button id="btn_approve_employee_performance" class="btn btn-success"
                                                        style="float: right;"
                                                        onclick="approve_employee_performance_report()">
                                                    <?php echo $this->lang->line('common_save_and_confirm'); ?>
                                                    <!--Save & Confirm-->
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="print_view_buttons" style="display: none; margin: 5px;">
                                        <div class="col-md-10">
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary act-btn-margin"
                                                    onclick="print_view_back_button()"><?php echo $this->lang->line("appraisal_employee_wise_performance_back_button"); ?>
                                            </button>
                                            <button class="btn btn-success act-btn-margin"
                                                    onclick="print_report()"><?php echo $this->lang->line("common_print"); ?></button>
                                        </div>
                                    </div>
                                    <div id="print_view" class="row" style="display: none;padding-bottom: 20px;">
                                        <div class="col-md-12">
                                            <div style="text-align: center;margin-bottom: 60px;">
                                                <h4><?php echo $this->lang->line('appraisal_master_employee_wise_performance'); ?></h4>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_epf_number'); ?>
                                                        :</label>
                                                    <span id="epf_number"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_emp_name'); ?>
                                                        :</label>
                                                    <span id="name_of_the_employee"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_designation'); ?>
                                                        :</label>
                                                    <span id="designation"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_department'); ?>
                                                        :</label>
                                                    <span id="department"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_period_of_review'); ?>
                                                        :</label>
                                                    <span id="period_of_review"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_name_of_hod'); ?>
                                                        :</label>
                                                    <span id="name_of_department_head"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_today'); ?>
                                                        :</label>
                                                    <span id="date_today"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <table id="employee_wise_performance_table_for_print_view"
                                                   class="<?php echo table_class(); ?>">
                                                <thead>
                                                <tr>
                                                    <th style="min-width: 15%;border-radius: 3px 0 0 0;">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_key_result_area'); ?>
                                                    </th>
                                                    <th style="min-width: 15%;border-radius: 0 0 0 0;">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_kpi'); ?>
                                                    </th>
                                                    <th style="min-width: 15%;border-radius: 0 0 0 0;">
                                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_weight'); ?>
                                                    </th>
                                                    <th style="min-width: 15%;border-radius: 0 0 0 0;">
                                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_completion'); ?>
                                                    </th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_total'); ?>
                                                        :</label>
                                                    <span id="print_view_total"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <div>
                                                        <label for="">
                                                            <?php echo $this->lang->line('appraisal_master_employee_wise_performance_special_remarks'); ?>
                                                            :</label>
                                                        <span id="print_view_special_remarks"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="manager_comment_label">
                                                            <?php echo $this->lang->line('appraisal_suggested_reward'); ?>
                                                            &nbsp;<!--Suggested reward--> :
                                                        </label>
                                                        <span id="print_view_suggested_reward_input"
                                                              class="manager_comment_text"></span>
                                                    </div>
                                                </div>


                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="manager_comment_label">
                                                            <?php echo $this->lang->line('appraisal_identified_training_needs'); ?>
                                                            <!--Identified training needs --> :
                                                        </label>
                                                        <span id="print_view_identified_training_needs"
                                                              class="manager_comment_text"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="manager_comment_label">
                                                            &nbsp;<?php echo $this->lang->line('appraisal_special_remarks_from_hod'); ?>
                                                            &nbsp;<!--Special remarks from HOD--> :
                                                        </label>
                                                        <span id="print_view_special_remarks_from_hod"
                                                              class="manager_comment_text"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group" style="margin-bottom: 158px;">
                                                        <label class="manager_comment_label">
                                                            &nbsp;<?php echo $this->lang->line('appraisal_employee_comment'); ?>
                                                            <!--Employee comment-->  :
                                                        </label>
                                                        <span disabled="true" id="print_view_employee_comment"
                                                              class="manager_comment_text"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="width: 100%;">
                                                <div style="width: 50%;float: left;padding-left: 25px">
                                                    <div>......................................................</div>
                                                    <div><?php echo $this->lang->line('appraisal_master_employee_wise_performance_signature_of_hod'); ?></div>
                                                </div>
                                                <div style="width: 50%;float: left;padding-left: 25px">
                                                    <div>......................................................</div>
                                                    <div><?php echo $this->lang->line('appraisal_master_employee_wise_performance_signature_of_emp'); ?></div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="step4" class="tab-pane">
                    <div class="row" style="margin: 20px;">
                        <div class="row">
                            <div class="col-md-12" id="sub-container">
                                <div class="box">
                                    <div class="box-header with-border" id="box-header-with-border">
                                        <h3 class="box-title" id="box-header-title"><?php echo $title; ?></span>
                                        </h3>
                                        <div class="box-tools pull-right">

                                        </div>
                                    </div>
                                    <div class="box-body">

                                        <div class="row">
                                            <div class="form-group col-md-12">

                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="col-md-12">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="department_objective">
                                                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal'); ?>
                                                                </label>
                                                                <select class="form-control"
                                                                        id="corporate_goals_dropdown2">
                                                                </select>
                                                                <div id="corporate_goals_dropdown2_error
                                                                     class=" error-message
                                                                ">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>
                                                                <?php echo $this->lang->line('appraisal_activity_department_employee'); ?>
                                                            </label>
                                                            <select id="employee_list_dropdown" class="form-control"
                                                                    onchange="employee_dropdown_onchange()">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group">
                                                            <label>
                                                                &nbsp;
                                                            </label>
                                                            <button class="btn btn-primary form-control"
                                                                    onclick="fetch_employee_skills_performance_appraisal.call(this)"> <?php echo $this->lang->line('appraisal_fetch'); ?>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>
                                                                &nbsp;
                                                            </label>
                                                            <div id="closed_label_div" style="display: none;">
                                                                <label for="closed_label">Status:</label>
                                                                <span id="closed_label"
                                                                      class="label label-text-size"
                                                                      style="background-color: red; margin-right: 5px;">Closed</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-12">
                                                            <div style="margin-top: 15px;"><span class="lbl1"
                                                                                                 style="font-weight: 600;display:none;">Marked By You</span>
                                                            </div>
                                                            <div id="softskills_template">
                                                            </div>
                                                        </div>
                                                        <div id="rating_error" class="error-message"></div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-2 total_div" style="margin-top: 7px;">
                                        <span class="label label-text-size"
                                              style="background-color: #02cf32;margin-right: 5px;"> <?php echo $this->lang->line('appraisal_total_marks'); ?>: </span>
                                                            <span
                                                                    id="total"></span>
                                                        </div>
                                                        <div class="col-md-4 last_update_mgr_div"
                                                             style="margin-top: 7px;">Last updated:
                                                            <span id="last_update_mgr"></span></div>
                                                    </div>
                                                    <div class="col-md-12" style="<?php
                                                    $isHideMarkedByEmpShow = hide_marks_marked_by_employee();
                                                    if($isHideMarkedByEmpShow){
                                                        echo 'display:none';
                                                    }
                                                    ?>">
                                                        <div class="col-md-12">
                                                            <div style="margin-top: 15px;"><span class="lbl1"
                                                                                                 style="font-weight: 600;display:none;">Marked By Employee</span>
                                                            </div>
                                                            <div id="softskills_template_emp_self">
                                                            </div>
                                                        </div>
                                                        <div id="rating_error" class="error-message"></div>
                                                    </div>
                                                    <div class="col-md-12" style="<?php
                                                    if($isHideMarkedByEmpShow){
                                                        echo 'display:none';
                                                    }
                                                    ?>">
                                                        <div class="col-md-2 total_div" style="margin-top: 7px;">
                                        <span class="label label-text-size"
                                              style="background-color: #02cf32;margin-right: 5px;"> <?php echo $this->lang->line('appraisal_total_marks'); ?>: </span>
                                                            <span
                                                                    id="total_emp"></span>
                                                        </div>
                                                        <div class="col-md-4 last_update_emp_div"
                                                             style="margin-top: 7px;">Last updated:
                                                            <span id="last_update_emp"></span></div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="manager_comment_label">
                                                                    Rating
                                                                </label>
                                                                <select id="manager_rating_dropdown"
                                                                        class="form-control" onchange="">
                                                                    <?php
                                                                    foreach ($rating_data as $ratingItem) {
                                                                        echo '<option value="0" >Select Rating Value</option>';
                                                                        echo '<option value="' . $ratingItem['appraisalRatingID'] . '">' . $ratingItem['ratedValue'] . ' - ' . $ratingItem['rating'] . ' (' . $ratingItem['description'] . ')</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <div id="manager_rating_error"
                                                                     class="error-message"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="manager_comment_label">
                                                                    Manager comment
                                                                </label>
                                                                <textarea id="manager_comment2"
                                                                          class="manager_comment_text"></textarea>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="manager_comment_label">
                                                                    &nbsp;Suggested reward
                                                                </label>
                                                                <textarea id="suggested_reward_input2"
                                                                          class="manager_comment_text"></textarea>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="manager_comment_label">
                                                                    Identified training needs
                                                                </label>
                                                                <textarea id="identified_training_needs2"
                                                                          class="manager_comment_text"></textarea>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="manager_comment_label">
                                                                    &nbsp;Special remarks from HOD
                                                                </label>
                                                                <textarea id="special_remarks_from_hod2"
                                                                          class="manager_comment_text"></textarea>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label class="manager_comment_label">
                                                                    &nbsp;Employee comment
                                                                </label>
                                                                <textarea disabled="true" id="employee_comment2"
                                                                          class="manager_comment_text"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="col-md-6">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <button id="btn_save_manager_comments_as_draft"
                                                                    class="btn btn-primary form-control pull-right"
                                                                    onclick="btn_save_manager_comments_as_draft.call(this)" style="display: none;">
                                                                Save as Draft
                                                            </button>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <button id="btn_save_manager_comments"
                                                                    class="btn btn-success form-control pull-right"
                                                                    onclick="btn_save_manager_comments.call(this)" style="display: none;">
                                                                Save & Confirm
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.box-body -->
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <?php echo footer_page('Right foot', 'Left foot', false); ?>
            <script>


                app = {};
                app.company_id = <?php echo current_companyID(); ?>;
                app.current_user_id = <?php echo current_userID(); ?>;
                app.employee_wise_performance_table = $('#employee_wise_performance_table').DataTable({
                    "language": {
                        "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                    },
                });
                app.employee_wise_performance_table_for_print_view = $('#employee_wise_performance_table_for_print_view').DataTable({
                    "language": {
                        "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                    },
                    searching: false,
                    paging: false,
                    info: false
                });
                app.goal_id = null;
                app.department_id = null;
                app.emp_id = null;
                app.appraisal_header_id = null;


                $(document).ready(function () {
                    $('.date-picker').datepicker();

                    load_corporate_goal_dropdown(app.company_id);
                    load_departments_dropdown(app.current_user_id);
                    var selected_department_id = $("#departments_dropdown").val();
                    load_department_employees_dropdown(selected_department_id);
                });

                function show_manager_comments_boxes() {
                    $(".manager_comment_label").show();
                    $(".manager_comment_text").show();
                    //$("#btn_save_manager_comments").show();

                }

                function hide_manager_comment_boxes() {
                    $(".manager_comment_label").hide();
                    $(".manager_comment_text").hide();
                    $("#btn_save_manager_comments").hide();
                }

                function performance_report_save_as_draft() {
                    var manager_comment = $("#manager_comment").val();
                    var suggested_reward = $("#suggested_reward_input").val();
                    var identified_training_needs = $("#identified_training_needs").val();
                    var special_remarks_from_hod = $("#special_remarks_from_hod").val();
                    if (app.goal_id != null) {
                        if (validation()) {
                            $.ajax({
                                async: false,
                                dataType: "json",
                                type: "POST",
                                url: "<?php echo site_url('Appraisal/approve_employee_performance_report'); ?>",
                                data: {
                                    goal_id: app.goal_id,
                                    department_id: app.department_id,
                                    employee_id: app.emp_id,
                                    manager_comment: manager_comment,
                                    suggested_reward: suggested_reward,
                                    identified_training_needs: identified_training_needs,
                                    special_remarks_from_hod: special_remarks_from_hod,
                                    status: 0
                                },
                                success: function (data) {
                                    app.employee_performance_header_details = data[0];
                                    myAlert('i', '<?php echo $this->lang->line('common_saved_as_draft'); ?>');/*Saved as draft*/
                                    //disable_comment_boxes(1);
                                }
                            });
                        }
                    } else {
                        myAlert('i', '<?php echo $this->lang->line('appraisal_please_fetch_data_before_save'); ?>');/*Please fetch data before save*/
                    }
                }

                function approve_employee_performance_report() {
                    var manager_comment = $("#manager_comment").val();
                    var suggested_reward = $("#suggested_reward_input").val();
                    var identified_training_needs = $("#identified_training_needs").val();
                    var special_remarks_from_hod = $("#special_remarks_from_hod").val();

                    if (app.goal_id != null) {
                        if (validation()) {
                            if (validation()) {
                                $.ajax({
                                    async: false,
                                    dataType: "json",
                                    type: "POST",
                                    url: "<?php echo site_url('Appraisal/approve_employee_performance_report'); ?>",
                                    data: {
                                        goal_id: app.goal_id,
                                        department_id: app.department_id,
                                        employee_id: app.emp_id,
                                        manager_comment: manager_comment,
                                        suggested_reward: suggested_reward,
                                        identified_training_needs: identified_training_needs,
                                        special_remarks_from_hod: special_remarks_from_hod,
                                        status: 1
                                    },
                                    success: function (data) {
                                        app.employee_performance_header_details = data[0];
                                        myAlert('s', '<?php echo $this->lang->line('appraisal_successfully_saved_confirmed'); ?>');/*Successfully saved & confirmed*/
                                        disable_comment_boxes(1);
                                        $("#btn_employee_performance_save_as_draft").attr("disabled", "disabled");
                                    }
                                });
                            }
                        }
                    } else {
                        myAlert('i', '<?php echo $this->lang->line('appraisal_please_fetch_data_before_save'); ?>');/*Please fetch data before save*/
                    }

                }

                function validation() {
                    var manager_comment = $("#manager_comment").val();
                    var suggested_reward = $("#suggested_reward_input").val();
                    var identified_training_needs = $("#identified_training_needs").val();
                    var special_remarks_from_hod = $("#special_remarks_from_hod").val();
                    var is_valid = true;
                    if (manager_comment == "") {
                        is_valid = false;
                        show_error('manager_comment_error', '<?php echo $this->lang->line('appraisal_required_field'); ?>'); /*Required filed*/
                    } else {
                        hide_error('manager_comment_error');
                    }
                    if (suggested_reward == "") {
                        is_valid = false;
                        show_error('suggested_reward_input_error', '<?php echo $this->lang->line('appraisal_required_field'); ?>');
                    } else {
                        hide_error('suggested_reward_input_error');
                    }

                    if (identified_training_needs == "") {
                        is_valid = false;
                        show_error('identified_training_needs_error', '<?php echo $this->lang->line('appraisal_required_field'); ?>');
                    } else {
                        hide_error('identified_training_needs_error');
                    }

                    if (special_remarks_from_hod == "") {
                        is_valid = false;
                        show_error('special_remarks_from_hod_error', '<?php echo $this->lang->line('appraisal_required_field'); ?>');
                    } else {
                        hide_error('special_remarks_from_hod_error');
                    }
                    return is_valid;
                }

                function print_report() {
                    $.print("#print_view");
                }

                function show_print_view() {
                    $("#normal_view").hide();
                    $("#print_view").show();
                    $("#print_view_buttons").show();

                }

                function print_view_back_button() {
                    $("#normal_view").show();
                    $("#print_view").hide();
                    $("#print_view_buttons").hide();
                }

                function department_dropdown_onchange() {
                    var selected_department_id = $("#departments_dropdown").val();
                    load_department_employees_dropdown(selected_department_id);
                }

                function format_for_two_digits(num) {
                    if (num < 10) {
                        return '0' + num;
                    } else {
                        return num;
                    }
                }

                function get_employee_wise_report() {
                    startLoad();
                    var goal_id = $("#corporate_goals_dropdown").val();
                    var department_id = $("#departments_dropdown").val();
                    var employee_id = $("#employees_dropdown").val();

                    app.goal_id = goal_id;
                    app.department_id = department_id;
                    app.emp_id = employee_id;

                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_employee_tasks_for_employee_wise_performance_report'); ?>",
                        data: {department_id: department_id, goal_id: goal_id, employee_id: employee_id},
                        success: function (data) {
                            app.employee_wise_performance_table.clear().draw();
                            app.employee_wise_performance_table_for_print_view.clear().draw();
                            var number_of_tasks = 0;
                            var number_of_completed_tasks = 0;
                            if (data == "failed") {
                                $("#name_of_the_employee").text("");
                                $("#department").text("");
                                $("#date_today").text("");
                                $("#period_of_review").text("");
                                $("#designation").text("");
                                $("#epf_number").text("");
                                $("#name_of_department_head").text("");
                                $("#task_completion_percentage").text("");
                                $("#approved_by").text("");
                                $("#approved_date").text("");
                                stopLoad();
                            } else {
                                data.forEach(function (item, index) {
                                    //preparing columns for datatable
                                    var short_description = item.description.substring(0, 18) + '...';
                                    var description_title = item.task_description + ' (' + item.description + ')';
                                    var task_description = '<span title="' + description_title + '">' + item.task_description + ' (' + short_description + '</span>';
                                    var weight = '<div style="text-align: center">' + item.weight + '%</div>';
                                    var emp_name = item.Ename1;

                                    var d = new Date(item.date_to_complete);
                                    var month = format_for_two_digits((d.getMonth() + 1));
                                    var date = format_for_two_digits(d.getDate());
                                    var date_to_complete = d.getFullYear() + '-' + month + '-' + date;

                                    var is_approved_by_manager = item.is_approved_by_manager;
                                    var manager_approved_checkbox_status = "";
                                    if (is_approved_by_manager == 1) {
                                        manager_approved_checkbox_status = "checked";
                                    }
                                    var manager_approve_checkbox = '<input disabled data-task_id="' + item.id + '" type="checkbox" ' + manager_approved_checkbox_status + '/>';

                                    var progress_bar_text_color = 'black';
                                    if (item.completion >= 60) {
                                        progress_bar_text_color = 'white';
                                    }
                                    var completion = '<div style="text-align: center">' + item.completion + '%</div>';

                                    var manager_review = item.manager_review;
                                    if (item.manager_review == 'pending') {
                                        manager_review = '<?php echo $this->lang->line('common_pending'); ?>';/*Pending*/
                                    } else if (item.manager_review == 'rejected') {
                                        manager_review = '<?php echo $this->lang->line('common_rejected'); ?>';/*Rejected*/
                                    } else if (item.manager_review == 'approved') {
                                        manager_review = '<?php echo $this->lang->line('common_approved'); ?>';/*Approved*/
                                    } else if ('refer_back' == item.manager_review) {
                                        manager_review = '<?php echo $this->lang->line('common_referred_back'); ?>';/*Referred back*/
                                    }

                                    //append row into datatable
                                    app.employee_wise_performance_table.row.add([task_description, weight, emp_name, date_to_complete, manager_approve_checkbox, completion, manager_review]).draw(false);
                                    app.employee_wise_performance_table_for_print_view.row.add([item.task_description, item.description, weight, completion]).draw(false);

                                    //calculating total
                                    number_of_tasks++;
                                    if (is_approved_by_manager == 1 && item.completion == 100) {
                                        number_of_completed_tasks++;
                                    }

                                });

                                var d = new Date(data[0].from);
                                var month = format_for_two_digits((d.getMonth() + 1));
                                var date = format_for_two_digits(d.getDate());
                                var goal_start_date = d.getFullYear() + '-' + month + '-' + date;

                                var d = new Date(data[0].to);
                                var month = format_for_two_digits((d.getMonth() + 1));
                                var date = format_for_two_digits(d.getDate());
                                var goal_end_date = d.getFullYear() + '-' + month + '-' + date;


                                var employee_details = get_employee_details(employee_id);
                                if (data[0].hod_id != "") {
                                    var hod_details = get_employee_details(data[0].hod_id);
                                }
                                var epf_number = employee_details.ssoNo;
                                var name_of_the_employee = $("#employees_dropdown option:selected").text();
                                var designation = employee_details.DesDescription;
                                var department = $("#departments_dropdown option:selected").text();
                                var period_of_review = goal_start_date + " to " + goal_end_date;
                                if (data[0].hod_id != "") {
                                    var name_of_department_head = hod_details.Ename1;
                                } else {
                                    var name_of_department_head = "";
                                }
                                var date_today = "<?php echo date('Y-m-d') ?>";

                                $("#name_of_the_employee").text(name_of_the_employee);
                                $("#department").text(department);
                                $("#date_today").text(date_today);
                                $("#period_of_review").text(period_of_review);
                                $("#designation").text(designation);
                                $("#epf_number").text(epf_number);
                                $("#name_of_department_head").text(name_of_department_head);

                                var employee_task_completion_percentage = ((number_of_completed_tasks / number_of_tasks) * 100).toFixed(1);
                                $("#task_completion_percentage").text(employee_task_completion_percentage + "%");
                                $("#print_view_total").text(employee_task_completion_percentage + "%");
                                stopLoad();
                            }

                            //checking employee performance header details for maintain approve button status.
                            var emp_performance_details = get_emp_performance_header_details(app.goal_id, app.department_id, app.emp_id);
                            if(emp_performance_details){
                                $("#print_view_special_remarks").text(emp_performance_details.manager_comment);
                                $("#print_view_suggested_reward_input").text(emp_performance_details.suggested_reward);
                                $("#print_view_identified_training_needs").text(emp_performance_details.identified_training_needs);
                                $("#print_view_employee_comment").text(emp_performance_details.special_remarks_from_emp);
                                $("#print_view_special_remarks_from_hod").text(emp_performance_details.special_remarks_from_hod);

                                if (emp_performance_details.is_approved == "0") {
                                    if (emp_performance_details.approved_datetime != null) {
                                        var d = new Date(emp_performance_details.approved_datetime);
                                        if (isNaN(d.getTime())) {
                                            $("#approved_date").text("");
                                        } else {
                                            var month = format_for_two_digits((d.getMonth() + 1));
                                            var date = format_for_two_digits(d.getDate());
                                            var approved_date = d.getFullYear() + '-' + month + '-' + date;
                                            $("#approved_date").text(approved_date);
                                        }
                                    } else {
                                        $("#approved_date").text("");
                                    }

                                    $("#manager_comment").val(emp_performance_details.manager_comment);
                                    $("#manager_comment").removeAttr("disabled");

                                    $("#suggested_reward_input").val(emp_performance_details.suggested_reward);
                                    $("#suggested_reward_input").removeAttr("disabled");

                                    $("#identified_training_needs").val(emp_performance_details.identified_training_needs);
                                    $("#identified_training_needs").removeAttr("disabled");

                                    $("#special_remarks_from_hod").val(emp_performance_details.special_remarks_from_hod);
                                    $("#special_remarks_from_hod").removeAttr("disabled");

                                    $("#employee_comment").val("");
                                    // $("#employee_comment").removeAttr("disabled");

                                    $("#btn_approve_employee_performance").removeAttr("disabled");
                                    $("#btn_approve_employee_performance").removeAttr("title");

                                    $("#btn_employee_performance_save_as_draft").removeAttr("disabled");
                                    $("#btn_employee_performance_save_as_draft").removeAttr("title");


                                } else {
                                    //approved by
                                    var approved_by = get_employee_details(emp_performance_details.approved_by);
                                    if (approved_by != null) {
                                        $("#approved_by").text(approved_by.Ename1);
                                    }

                                    //approved date
                                    var d = new Date(emp_performance_details.approved_datetime);
                                    if (isNaN(d.getTime())) {
                                        $("#approved_date").text("");
                                    } else {
                                        var month = format_for_two_digits((d.getMonth() + 1));
                                        var date = format_for_two_digits(d.getDate());
                                        var approved_date = d.getFullYear() + '-' + month + '-' + date;
                                        $("#approved_date").text(approved_date);
                                    }

                                    $("#manager_comment").val(emp_performance_details.manager_comment);
                                    $("#manager_comment").attr("disabled", "disabled");
                                    $("#btn_approve_employee_performance").attr("disabled", "disabled");
                                    $("#btn_approve_employee_performance").attr("title", "Already approved this for employee performance.");

                                    $("#btn_employee_performance_save_as_draft").attr("disabled", "disabled");
                                    $("#btn_employee_performance_save_as_draft").attr("title", "Already approved this for employee performance.");

                                    $("#suggested_reward_input").val(emp_performance_details.suggested_reward);
                                    $("#suggested_reward_input").attr("disabled", "disabled");

                                    $("#identified_training_needs").val(emp_performance_details.identified_training_needs);
                                    $("#identified_training_needs").attr("disabled", "disabled");


                                    $("#employee_comment").val(emp_performance_details.special_remarks_from_emp);
                                    $("#employee_comment").attr("disabled", "disabled");

                                    $("#special_remarks_from_hod").val(emp_performance_details.special_remarks_from_hod);
                                    $("#special_remarks_from_hod").attr("disabled", "disabled");
                                }
                            }
                        }
                    });

                }

                function disable_comment_boxes(status) {
                    if (status == 1) {
                        $("#manager_comment").attr("disabled", "disabled");
                        $("#btn_approve_employee_performance").attr("disabled", "disabled");
                        $("#btn_approve_employee_performance").attr("title", "Already approved this for employee performance.");
                        $("#suggested_reward_input").attr("disabled", "disabled");
                        $("#identified_training_needs").attr("disabled", "disabled");
                        $("#special_remarks_from_hod").attr("disabled", "disabled");
                    } else {
                        $("#manager_comment").removeAttr("disabled");
                        $("#suggested_reward_input").removeAttr("disabled");
                        $("#identified_training_needs").removeAttr("disabled");
                        $("#special_remarks_from_hod").removeAttr("disabled");
                        $("#btn_approve_employee_performance").removeAttr("disabled");
                        $("#btn_approve_employee_performance").removeAttr("title");
                    }
                }

                function get_emp_performance_header_details(goal_id, department_id, emp_id) {
                    app.employee_performance_header_details = null;
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_employee_performance_header_details'); ?>",
                        data: {goal_id: goal_id, department_id: department_id, emp_id: emp_id},
                        success: function (data) {
                            app.employee_performance_header_details = data[0];
                        }
                    });
                    return app.employee_performance_header_details;
                }

                function get_employee_details(employee_id) {
                    app.employee_details = null;
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_employee_details'); ?>",
                        data: {employee_id: employee_id},
                        success: function (data) {
                            app.employee_details = data[0];
                        }
                    });
                    return app.employee_details;
                }

                function load_corporate_goal_dropdown(company_id) {
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_corporate_goals_for_dashboard'); ?>",
                        data: {company_id: company_id},
                        success: function (data) {
                            var options = "";
                            data.forEach(function (item, index) {
                                //var narration = item.narration;
                                //item.id
                                options += '<option value="' + item.id + '">' + item.narration + '</option>';
                            })
                            $("#corporate_goals_dropdown").html(options);
                            $("#corporate_goals_dropdown").select2({
                                placeholder: '<?php echo $this->lang->line('appraisal_select_an_option'); ?>',
                                tags: true
                            });
                        }
                    });
                }

                function load_departments_dropdown(current_user_id) {
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_employee_departments_data'); ?>",
                        data: {employee_id: current_user_id},
                        success: function (data) {
                            var options = "";
                            data.forEach(function (item, index) {
                                options += '<option value="' + item.DepartmentMasterID + '">' + item.DepartmentDes + '</option>';
                            })
                            $("#departments_dropdown").html(options);
                            $("#departments_dropdown").select2({
                                placeholder: '<?php echo $this->lang->line('appraisal_select_an_option'); ?>',
                                tags: true
                            });
                        }
                    });
                }

                function load_department_employees_dropdown(department_id) {

                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_department_employees'); ?>",
                        data: {department_id: department_id},
                        success: function (data) {
                            var options = "";
                            data.forEach(function (item, index) {
                                options += '<option value="' + item.EmpID + '">' + item.Ename1 + ' - ' + item.ECode + '</option>';
                            })
                            $("#employees_dropdown").html(options);
                            $("#employees_dropdown").select2({
                                placeholder: '<?php echo $this->lang->line('appraisal_select_an_option'); ?>',/*Select an option*/
                                tags: true
                            });
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

                $(document).ready(function () {


                    // $("#appraisal_name").text(localStorage.getItem('appraisal_name'));

                    load_corporate_goal_dropdown();
                    load_department_employees_dropdown();
                    $(".total_div").hide();
                    $(".last_update_mgr_div").hide();
                    $(".last_update_emp_div").hide();
                    hide_manager_comment_boxes();
                });

                function load_corporate_goal_dropdown() {
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_performance_based_appraisals_by_department'); ?>",
                        data: {},
                        success: function (data) {
                            var options = "";
                            data.forEach(function (item, index) {
                                //var narration = item.narration;
                                //item.id
                                options += '<option value="' + item.goal_id + '">' + item.narration + ' - ' + item.document_id + '</option>';
                            })
                            $("#corporate_goals_dropdown2").html(options);
                            $("#corporate_goals_dropdown2").select2({
                                placeholder: 'Select an option',
                                tags: true
                            });
                        }
                    });
                }

                function btn_save_manager_comments_as_draft() {
                    var suggested_reward_input = $("#suggested_reward_input2").val();
                    var identified_training_needs = $("#identified_training_needs2").val();
                    var special_remarks_from_hod = $("#special_remarks_from_hod2").val();
                    var manager_comment = $("#manager_comment2").val();
                    var rating = $("#manager_rating_dropdown").val();
                    if (validation_before_confirm()) {
                        $.ajax({
                            async: false,
                            dataType: "json",
                            type: "POST",
                            url: "<?php echo site_url('Appraisal/update_manager_comments'); ?>",
                            data: {
                                suggested_reward_input: suggested_reward_input,
                                identified_training_needs: identified_training_needs,
                                special_remarks_from_hod: special_remarks_from_hod,
                                template_mapping_id: app.template_mapping_id,
                                manager_comment: manager_comment,
                                confirmed: 0,
                                rating: rating
                            },
                            success: function (data) {
                                myAlert('i', 'Successfully saved.');
                                //comment_box_enable_disable(1);
                                //disable_radio_button(1);
                            }
                        });
                    }
                }

                function btn_save_manager_comments() {
                    var suggested_reward_input = $("#suggested_reward_input2").val();
                    var identified_training_needs = $("#identified_training_needs2").val();
                    var special_remarks_from_hod = $("#special_remarks_from_hod2").val();
                    var manager_comment = $("#manager_comment2").val();
                    var rating = $("#manager_rating_dropdown").val();

                    if (validation_before_confirm()) {
                        swal({
                                title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                                text: "Do you want to confirm this records? This record cannot refer-back once you confirmed.",
                                type: "warning",/*warning*/
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Confirm",
                                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                            },
                            function () {
                                $.ajax({
                                    async: true,
                                    type: 'post',
                                    dataType: 'json',
                                    data: {
                                        suggested_reward_input: suggested_reward_input,
                                        identified_training_needs: identified_training_needs,
                                        special_remarks_from_hod: special_remarks_from_hod,
                                        template_mapping_id: app.template_mapping_id,
                                        manager_comment: manager_comment,
                                        confirmed: 1,
                                        rating: rating
                                    },
                                    url: "<?php echo site_url('Appraisal/update_manager_comments'); ?>",
                                    success: function (data) {
                                        myAlert('i', 'Successfully saved.');
                                        comment_box_enable_disable(1);
                                        disable_radio_button(1);
                                    }
                                });
                            });
                    }
                }

                function validation_before_confirm() {
                    var is_valid = true;
                    var suggested_reward_input = $("#suggested_reward_input2").val();
                    var identified_training_needs = $("#identified_training_needs2").val();
                    var special_remarks_from_hod = $("#special_remarks_from_hod2").val();
                    var manager_comment = $("#manager_comment2").val();
                    var rating = $("#manager_rating_dropdown").val();


                    if (rating == 0) {
                        is_valid = false;
                        show_error('manager_rating_error', 'Manager rating field is required.');
                    } else {
                        hide_error('manager_rating_error');
                    }

                    if (manager_comment == "") {
                        is_valid = false;
                        show_error('manager_comment_error', 'Manager comment field is required.');
                    } else {
                        hide_error('manager_comment_error');
                    }

                    if (suggested_reward_input == "") {
                        is_valid = false;
                        show_error('suggested_reward_error', 'Suggested rewards field is required.');
                    } else {
                        hide_error('suggested_reward_error');
                    }

                    if (identified_training_needs == "") {
                        is_valid = false;
                        show_error('identified_training_needs_error', 'Identifed training needs field is required.');
                    } else {
                        hide_error('identified_training_needs_error');
                    }

                    if (special_remarks_from_hod == "") {
                        is_valid = false;
                        show_error('special_remarks_from_hod_error', 'Special remarks from HOD field is required.');
                    } else {
                        hide_error('special_remarks_from_hod_error');
                    }

                    var rating_input = extract_values_from_rating_input();
                    var rating_validation = validate_rating_input(rating_input);

                    if (rating_validation == false) {
                        is_valid = false;
                        show_error('rating_error', 'All items are required in the rating satisfaction.');
                        myAlert('e', 'All items are required in the rating satisfaction.');
                    } else {
                        hide_error('rating_error');
                    }

                    return is_valid;
                }

                function validate_rating_input(rating_input) {
                    var is_valid = true;
                    rating_input.forEach(function (item, index) {
                        var is_valid2 = false;
                        item.forEach(function (item2, index2) {
                            if (item2.checked == true) {
                                is_valid2 = true;
                            }
                        });
                        if (is_valid2 == false) {
                            is_valid = false;
                        }
                    });
                    return is_valid;
                }

                function extract_values_from_rating_input() {
                    var number_of_checkboxes = $("td input[class='radio-1']").length;
                    var checkbox_group = [];
                    for (var i = 0; i < number_of_checkboxes; i++) {
                        var element = $("td input[class='radio-1']")[i];
                        var checkbox_set = {name: element.name, checked: element.checked};
                        checkbox_group.push(checkbox_set);
                    }
                    var checkbox_gr_array = [];
                    var j = 0;
                    checkbox_gr_array[j] = [];
                    checkbox_group.forEach(function (item, index) {
                        if (index == 0) {
                            checkbox_gr_array[j].push({name: item.name, checked: item.checked});//

                        } else {

                            if (checkbox_gr_array[j][0].name == item.name) {
                                checkbox_gr_array[j].push({name: item.name, checked: item.checked})
                            } else {
                                j++;
                                checkbox_gr_array[j] = [];
                                checkbox_gr_array[j].push({name: item.name, checked: item.checked})
                            }
                        }
                    });
                    return checkbox_gr_array;
                }

                function disable_radio_button(status) {
                    if (status == 1) {
                        $("#softskills_template input[type=radio]").attr("disabled", true);
                    } else {
                        $("#softskills_template input[type=radio]").attr("disabled", false);
                    }
                }

                function show_manager_comments_boxes() {
                    $(".manager_comment_label").show();
                    $(".manager_comment_text").show();
                    //$("#btn_save_manager_comments").show();
                    //$("#btn_save_manager_comments_as_draft").show();
                    $("#manager_rating_dropdown").show();
                }

                function hide_manager_comment_boxes() {
                    $(".manager_comment_label").hide();
                    $(".manager_comment_text").hide();
                    $("#btn_save_manager_comments").hide();
                    $("#btn_save_manager_comments_as_draft").hide();
                    $("#manager_rating_dropdown").hide();

                }

                function employee_dropdown_onchange() {
                    $("#softskills_template").html('');
                    $("#softskills_template_emp_self").html('');
                    $(".total_div").hide();
                    $(".last_update_mgr_div").hide();
                    $(".last_update_emp_div").hide();
                    $(".lbl1").hide();
                    hide_manager_comment_boxes();
                    hide_all_error_messages();
                }

                function hide_all_error_messages() {
                    hide_error('manager_comment_error');
                    hide_error('suggested_reward_error');
                    hide_error('identified_training_needs_error');
                    hide_error('special_remarks_from_hod_error');
                    hide_error('rating_error');
                }

                function fetch_employee_skills_performance_appraisal() {
                    $(".lbl1").show();
                    var emp_id = $('#employee_list_dropdown').val();
                    var config_goal_id = $('#corporate_goals_dropdown2').val();
                    app.config_goal_id = config_goal_id;
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/fetch_employee_skills_performance_appraisal'); ?>",
                        data: {config_goal_id: config_goal_id, emp_id: emp_id},
                        success: function (data) {
                            $(".last_update_mgr_div").show();
                            $("#last_update_mgr").text(data.last_update_time);

                            var template_body = '<table class="table table-striped table-bordered"><thead><tr>';
                            template_body += '<th><?php echo $this->lang->line('appraisal_performance_area'); ?></th>';
                            template_body += '<th><?php echo $this->lang->line('sub_performance_area'); ?></th>';
                            data.skills_grades_list.forEach(function (item, index) {
                                if (item.grade == "Not Applicable") {
                                    template_body += '<th>' + item.grade + ' </th>';
                                } else {
                                    template_body += '<th>' + item.grade + ' (' + item.marks + ' Marks)</th>';
                                }
                            });
                            //template_body += '<th>Not Applicable</th>';

                            template_body += '</tr></thead>' +
                                '<tbody id="table_body_read_only"></tbody></table>';
                            $("#softskills_template").html(template_body);

                            var table_body = "";
                            var total = 0;
                            data.performance_areas.forEach(function (item, index) {

                                if (item.sub != null) {
                                    let rowspan = item.sub.length + 1;
                                    table_body += '<tr>' +
                                        '<td rowspan="' + rowspan + '">' + item.description + '</td>';

                                    item.sub.forEach(function (item, index) {
                                        table_body += '<tr>' +
                                            '<td>' + item.description + '</td>';
                                        var radio_group_name = "performance" + item.performance_area_id;
                                        var currently_selected_grade_id = item.grade_id;
                                        var performance_area_id = item.performance_area_id;
                                        data.skills_grades_list.forEach(function (item, index) {

                                            var is_checked = '';
                                            if (currently_selected_grade_id == item.id) {
                                                is_checked = 'checked';
                                                total += parseInt(item.marks);
                                            }
                                            table_body += '<td><label class="customcheck"><input class="radio-1" ' + is_checked + ' type="radio" name="' + radio_group_name + '" data-performance_id="' + performance_area_id + '" data-grade_id="' + item.id + '" data-emp_id="' + emp_id + '" onclick="performance_radio_click.call(this)"/><span class="checkmark"></span></label></td>';
                                        });
                                        //table_body += '<td><input ' + is_checked + ' type="radio" name="' + radio_group_name + '" data-performance_id="' + performance_area_id + '" data-grade_id="" data-emp_id="' + emp_id + '" onclick="performance_radio_click.call(this)"/></td>';

                                        table_body += '</tr>';
                                    });

                                } else {
                                    table_body += '<tr>' +
                                        '<td>' + item.description + '</td><td></td>';
                                    var radio_group_name = "performance" + item.performance_area_id;
                                    var currently_selected_grade_id = item.grade_id;
                                    var performance_area_id = item.performance_area_id;
                                    data.skills_grades_list.forEach(function (item, index) {

                                        var is_checked = '';
                                        if (currently_selected_grade_id == item.id) {
                                            is_checked = 'checked';
                                            total += parseInt(item.marks);
                                        }
                                        table_body += '<td><label class="customcheck"><input class="radio-1" ' + is_checked + ' type="radio" name="' + radio_group_name + '" data-performance_id="' + performance_area_id + '" data-grade_id="' + item.id + '" data-emp_id="' + emp_id + '" onclick="performance_radio_click.call(this)"/><span class="checkmark"></span></label></td>';
                                    });
                                    //table_body += '<td><input ' + is_checked + ' type="radio" name="' + radio_group_name + '" data-performance_id="' + performance_area_id + '" data-grade_id="" data-emp_id="' + emp_id + '" onclick="performance_radio_click.call(this)"/></td>';

                                    table_body += '</tr>';
                                }


                            });
                            $("#table_body_read_only").html(table_body);
                            $("#total").text(total);
                            $(".total_div").show();
                            $("#suggested_reward_input2").text(data.suggested_reward);
                            $("#identified_training_needs2").text(data.identified_training_needs);
                            $("#special_remarks_from_hod2").text(data.special_remarks_from_hod);
                            $("#manager_comment2").text(data.manager_comment);
                            $("#employee_comment2").text(data.special_remarks_from_emp);
                            $("#manager_rating_dropdown").val(data.ratingID);
                            app.template_mapping_id = data.template_mapping_id;
                            show_manager_comments_boxes();
                            comment_box_enable_disable(1);//this form is not allowed to modify in this screen
                            disable_radio_button(1);//this form is not allowed to modify in this screen
                            $(".customcheck").attr("disabled", true);


                            let is_goal_closed = data.is_goal_closed;
                            if (is_goal_closed == "1" || data.is_approved == "1") {
                                $("input").prop('disabled', true);
                                $("textarea").prop('disabled', true);
                                $("#btn_save_manager_comments_as_draft").prop('disabled', true);
                                $("#btn_save_manager_comments").prop('disabled', true);
                                $("#closed_label_div").show();
                                $("#manager_rating_dropdown").prop('disabled', true);
                            } else {
                                $("input").prop('disabled', true);
                                $("textarea").prop('disabled', true);
                                $("#btn_save_manager_comments_as_draft").prop('disabled', true);
                                $("#btn_save_manager_comments").prop('disabled', true);
                                $("#closed_label_div").hide();
                                $("#manager_rating_dropdown").prop('disabled', true);
                            }
                            $("#employee_comment2").prop('disabled', true);//always desabled for this window.
                        }
                    });

                    //Employee Self Evaluation
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/fetch_employee_skills_performance_appraisal_self_eval'); ?>",
                        data: {config_goal_id: config_goal_id, emp_id: emp_id},
                        success: function (data) {
                            $(".last_update_emp_div").show();
                            $("#last_update_emp").text(data.last_update_time);
                            var template_body = '<table class="table table-striped table-bordered"><thead><tr>';
                            template_body += '<th><?php echo $this->lang->line('appraisal_performance_area'); ?></th>';
                            template_body += '<th><?php echo $this->lang->line('sub_performance_area'); ?></th>';
                            data.skills_grades_list.forEach(function (item, index) {
                                if (item.grade == "Not Applicable") {
                                    template_body += '<th>' + item.grade + ' </th>';
                                } else {
                                    template_body += '<th>' + item.grade + ' (' + item.marks + ' Marks)</th>';
                                }
                            });
                            //template_body += '<th>Not Applicable</th>';

                            template_body += '</tr></thead>' +
                                '<tbody id="template_tablebody_self_eval"></tbody></table>';
                            $("#softskills_template_emp_self").html(template_body);

                            var table_body = "";
                            var total = 0;
                            data.performance_areas.forEach(function (item, index) {
                                if (item.sub != null) {
                                    let rowspan = item.sub.length + 1;
                                    table_body += '<tr>' +
                                        '<td rowspan="' + rowspan + '">' + item.description + '</td>';

                                    item.sub.forEach(function (item, index) {
                                        table_body += '<tr>' +
                                            '<td>' + item.description + '</td>';
                                        var radio_group_name = "performancex" + item.performance_area_id;
                                        var currently_selected_grade_id = item.grade_id;
                                        var performance_area_id = item.performance_area_id;
                                        data.skills_grades_list.forEach(function (item, index) {

                                            var is_checked = '';
                                            if (currently_selected_grade_id == item.id) {
                                                is_checked = 'checked';
                                                total += parseInt(item.marks);
                                            }

                                            if (data.is_confirmed_by_employee != 1) {//data marked by employee will not show to manager until employee confirms.
                                                is_checked = ''
                                            }
                                            table_body += '<td><label class="customcheck" style="cursor: not-allowed;"><input ' + is_checked + ' type="radio" name="' + radio_group_name + '" data-performance_id="' + performance_area_id + '" data-grade_id="' + item.id + '" data-emp_id="' + emp_id + '" /><span class="checkmark"></span></label></td>';
                                        });
                                        table_body += '</tr>';
                                    });
                                } else {
                                    table_body += '<tr>' +
                                        '<td>' + item.description + '</td><td></td>';
                                    var radio_group_name = "performancex" + item.performance_area_id;
                                    var currently_selected_grade_id = item.grade_id;
                                    var performance_area_id = item.performance_area_id;
                                    data.skills_grades_list.forEach(function (item, index) {

                                        var is_checked = '';
                                        if (currently_selected_grade_id == item.id) {
                                            is_checked = 'checked';
                                            total += parseInt(item.marks);
                                        }

                                        if (data.is_confirmed_by_employee != 1) {//data marked by employee will not show to manager until employee confirms.
                                            is_checked = ''
                                        }
                                        table_body += '<td><label class="customcheck" style="cursor: not-allowed;"><input ' + is_checked + ' type="radio" name="' + radio_group_name + '" data-performance_id="' + performance_area_id + '" data-grade_id="' + item.id + '" data-emp_id="' + emp_id + '" /><span class="checkmark"></span></label></td>';
                                    });
                                    table_body += '</tr>';
                                }


                            });
                            $("#total_emp").text(total);
                            $("#template_tablebody_self_eval").html(table_body);
                            $("#softskills_template_emp_self input[type=radio]").attr("disabled", true);

                        }
                    });
                }

                function comment_box_enable_disable(status) {
                    if (status == 1) {
                        $("#suggested_reward_input2").attr("disabled", true);
                        $("#identified_training_needs2").attr("disabled", true);
                        $("#special_remarks_from_hod2").attr("disabled", true);
                        $("#manager_comment2").attr("disabled", true);
                        $("#btn_save_manager_comments").attr("disabled", true);
                        $("#btn_save_manager_comments_as_draft").attr("disabled", true);
                    } else {
                        $("#suggested_reward_input2").attr("disabled", false);
                        $("#identified_training_needs2").attr("disabled", false);
                        $("#special_remarks_from_hod2").attr("disabled", false);
                        $("#manager_comment2").attr("disabled", false);
                        $("#btn_save_manager_comments").attr("disabled", false);
                        $("#btn_save_manager_comments_as_draft").attr("disabled", false);
                    }
                }

                function performance_radio_click() {
                    var grade_id = $(this).data('grade_id');
                    var performance_id = $(this).data('performance_id');
                    var emp_id = $(this).data('emp_id');
                    var goal_id = app.config_goal_id;
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/save_emp_softskills_grade'); ?>",
                        data: {
                            performance_id: performance_id,
                            emp_id: emp_id,
                            goal_id: goal_id,
                            grade_id: grade_id
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                myAlert('i', data.message);
                                $("#total").text(data.total);
                            } else {
                                myAlert('e', data.message);

                            }
                        }
                    });
                }

                function load_department_employees_dropdown() {
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_employees_for_performance_apr'); ?>",
                        data: {},
                        success: function (data) {
                            var options = "";
                            data.forEach(function (item, index) {
                                options += '<option value="' + item.EmpID + '">' + item.Ename1 + ' - ' + item.ECode + '</option>';
                            })
                            $("#employee_list_dropdown").html(options);
                            $("#employee_list_dropdown").select2({
                                placeholder: 'Select an option',
                                tags: true
                            });
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


