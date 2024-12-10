<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_my_appraisal_title');


?>
<style>
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

    .pt-100 {
        margin-top: 100px;
    }

    .closed-label {
        display: inline-block;
        position: absolute;
        right: 87px;
    }
</style>
<style>
    .accordion {
        background-color: #eee;
        color: #444;
        cursor: pointer;
        padding: 18px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
        border: 1px solid #ccc;
        transition: 0.4s;
        border-radius: 5px;
        margin-bottom: 5px;
    }

    button.accordion:after {
        content: '\002B';
        color: #777;
        font-weight: bold;
        float: right;
        margin-left: 5px;
    }

    button.accordion.active:after {
        content: "\2212";
    }

    .accordion:hover {
        background-color: #ffffff;
    }

    .panel {
        padding: 0 18px;
        display: none;
        background-color: white;
        overflow: hidden;
    }

    .cus-panel {
        background-color: #ffffff;
        padding: 15px;
    }

    .manager_comment_label {
        vertical-align: top;
    }

    .manager_comment_text {
        width: 100%;
    }
</style>
<style>


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
        position: absolute;
        top: -5px;

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

    .performance-table{
        border: 1px solid darkgrey !important;
    }
    .performance-table td{
        border: 1px solid darkgrey !important;
    }
    .performance-table th{
        border: 1px solid darkgrey !important;
    }
</style>
<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool headerclose navdisabl"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <link rel="stylesheet"
                  href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>


            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">

                        <div id="table_list" style="min-height: 500px;">


                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="task_progress_update_modal" role="dialog"
                 aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-lg" style="width: 43%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title"
                                id="CommonEdit_Title"><?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_task'); ?></h4>
                        </div>

                        <div class="modal-body" style="overflow-y: scroll;height: 157px;">
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="task_description">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_task_description'); ?>
                                    </label>
                                    <div id="task_description"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label for="formControlRange">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_task_progress'); ?>
                                    </label>
                                    <input oninput="task_progress_input_onchange.call(this)" type="range"
                                           class="form-control-range" id="task_progress_input" min="0" max="100">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <div id="task_progress_in_number" style="text-align: center"></div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <div class="text-right m-t-xs">
                                <button class="btn btn-primary" id="add_task"
                                        onclick="save_progress.call(this);" type="button">
                                    <?php echo $this->lang->line('common_save'); ?><!--Save & Next--></button>
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
                                    <textarea type="text" id="task_description2" class="form-control"></textarea>
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
                                        &nbsp;
                                    </label>
                                    <div class="progress" style="height: 20px;    width: 100px;">
                                        <div id="add_task_form_progress_bar" class="progress-bar" role="progressbar"
                                             style="" aria-valuenow="" aria-valuemin="0" aria-valuemax="100"></div>
                                        <span id="add_task_form_progress_bar_text" style=""></span></div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label for="sub_departments_dropdown">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_sub_departments'); ?>
                                    </label>
                                    <select class="form-control" id="sub_departments_dropdown"
                                            onchange="change_objective_used_percentage.call(this)">
                                    </select>
                                    <div id="department_objective_error" class="error-message"></div>
                                </div>
                                <!--                                <div class="form-group col-sm-4">-->
                                <!--                                    <label for="assigned_employee">-->
                                <!--                                        -->
                                <?php //echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_assigned_employee'); ?><!--  </label>-->
                                <!--                                    <select class="form-control" id="assigned_employee">-->
                                <!--                                    </select>-->
                                <!--                                    <div id="assigned_employee_error" class="error-message"></div>-->
                                <!--                                </div>-->

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label for="date_to_complete">

                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_date_to_complete'); ?>
                                    </label>
                                    <input id="date_to_complete" class="form-control date-picker"/>
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
                                    <label>Ref</label>
                                    <input type="text" id="msg_ref_search_text"/>
                                    <button id="msg_search_btn"><?php echo $this->lang->line('common_search'); ?><!--Search--></button>
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
            <script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
            <script type="text/javascript">
                app = {};
                app.company_id = <?php echo current_companyID(); ?>;
                app.current_user_id = <?php echo current_userID(); ?>;
                //app.employee_task_table = $('#employee_task_table').DataTable();
                app.employee_task_table_list = "";
                app.employee_task_table_ids = [];
                app.goal_close_status = [];
                app.appraisal_header_id = null;
                app.appraisal_sub_department_id = null;
                app.goal_id = null;
                app.department_id = null;
                app.department_objectives = new Array();
                app.form_status = null;
                app.goal_from = null;
                app.goal_to = null;
                app.is_valid = null;
                app.employee_task_table_array = new Array();

                app.marking_type = 0;
                app.template_id = 0;

                $(document).ready(function () {

                    $('.headerclose').click(function () {
                        fetchPage('system/appraisal/activity/employee_appraisal', '0', 'My Appraisal');
                    });

                    $('.date-picker').datepicker({format: 'yyyy-mm-dd'});
                    load_employee_tasks();
                    //load_department_objectives_dropdown();

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
                    });

                    // // Disable the TinyMCE editor
                    // tinymce.get('richtext_careerAndAction').setMode('readonly');
                    // tinymce.get('richtext_managerComment').setMode('readonly');
                    // $("#richtext_managerComment").attr("disabled", true);
                    // $("#richtext_careerAndAction").attr("disabled", true);

                });

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

                $("#msg_search_btn").click(function () {
                    var key = $("#msg_ref_search_text").val();
                    scrollToBubble(key);
                });

                $("#discussion_message").keyup(function (event) {
                    if (event.which == 13) {
                        send_message();
                    }
                });

                function department_task_form_validation() {

                    var task_description = $("#task_description2").val();
                    var task_weight = $("#task_weight").val();
                    var department_objective_id = $("#department_objective").val();
                    var date_to_complete = $("#date_to_complete").val();

                    app.is_valid = true;

                    if (task_description == "") {
                        app.is_valid = false;
                        show_error('task_description_error', '<?php echo $this->lang->line('appraisal_task_description_is_required') ?>');/*Task description is required*/
                    } else {
                        hide_error('task_description_error');
                    }

                    if (task_weight == "") {
                        app.is_valid = false;
                        show_error('task_weight_error', '<?php echo $this->lang->line('appraisal_weight_is_required') ?>');/*Weight is required*/
                    } else {
                        //hide_error('task_weight_error');
                        $.ajax({
                            async: false,
                            dataType: "json",
                            type: "POST",
                            url: "<?php echo site_url('Appraisal/get_percentage_details'); ?>",
                            data: {
                                department_id: app.department_id,
                                goal_id: app.goal_id,
                                objective_id: department_objective_id,
                                department_appraisal_header_id: app.appraisal_header_id
                            },
                            success: percentage_validation
                        });
                        app.is_valid = app.is_valid;
                    }

                    if (date_to_complete == "") {
                        app.is_valid = false;
                        show_error('date_to_complete_error', '<?php echo $this->lang->line('common_date_is_required') ?>');/*Date is required*/
                    } else {
                        var d = new Date(date_to_complete);
                        var from = new Date(app.goal_from);
                        var to = new Date(app.goal_to);

                        if (from <= d && d <= to) {
                            hide_error('date_to_complete_error');
                        } else {
                            app.is_valid = false;
                            show_error('date_to_complete_error', '<?php echo $this->lang->line('appraisal_completion_date_should_be_in_goal_period') ?>');/*Completion date should be in goal period*/
                        }

                    }

                    return app.is_valid;
                }

                function percentage_validation(data) {
                    if (app.form_status == 'save') {
                        var task_weight = $("#task_weight").val();
                        if (data.remaining_percentage < task_weight) {
                            app.is_valid = false;
                            show_error('task_weight_error', '<?php echo $this->lang->line('appraisal_remaining_quota_is') ?> ' + data.remaining_percentage + '%');/**/
                        } else {
                            hide_error('task_weight_error');
                        }
                    } else if (app.form_status == 'edit') {

                        var task_weight = $("#task_weight").val();
                        var remaining_percentage = parseFloat(data.remaining_percentage) + parseFloat(app.current_task_weight);
                        if (remaining_percentage < task_weight) {
                            app.is_valid = false;
                            show_error('task_weight_error', '<?php echo $this->lang->line('appraisal_remaining_quota_is') ?> ' + remaining_percentage + '%');/*Remaining Quota is*/
                        } else {
                            hide_error('task_weight_error');
                        }
                    }

                }

                function department_task_form_edit_validation() {

                    var task_description = $("#task_description2").val();
                    var task_weight = $("#task_weight").val();
                    var department_objective_id = $("#department_objective").val();
                    var date_to_complete = $("#date_to_complete").val();
                    app.is_valid = true;

                    if (task_description == "") {
                        app.is_valid = false;
                        show_error('task_description_error', '<?php echo $this->lang->line('appraisal_task_description_is_required') ?>');/*Task description is required*/
                    } else {
                        hide_error('task_description_error');
                    }

                    if (task_weight == "") {
                        app.is_valid = false;
                        show_error('task_weight_error', '<?php echo $this->lang->line('appraisal_weight_is_required') ?>');/*Weight is required*/
                    } else {
                        //hide_error('task_weight_error');

                        $.ajax({
                            async: false,
                            dataType: "json",
                            type: "POST",
                            url: "<?php echo site_url('Appraisal/get_percentage_details'); ?>",
                            data: {
                                department_id: app.department_id,
                                goal_id: app.goal_id,
                                objective_id: department_objective_id,
                                department_appraisal_header_id: app.appraisal_header_id
                            },
                            success: percentage_validation
                        });
                        app.is_valid = app.is_valid;
                    }

                    if (date_to_complete == "") {
                        app.is_valid = false;
                        show_error('date_to_complete_error', '<?php echo $this->lang->line('common_date_is_required') ?>');/*Date is required*/
                    } else {
                        if (app.current_date_value != date_to_complete) {
                            var d = new Date(date_to_complete);
                            var from = new Date(app.goal_from);
                            var to = new Date(app.goal_to);

                            if (from <= d && d <= to) {
                                hide_error('date_to_complete_error');
                            } else {
                                app.is_valid = false;
                                show_error('date_to_complete_error', '<?php echo $this->lang->line('appraisal_completion_date_should_be_in_goal_period') ?>');/*Completion date should be in goal period*/
                            }
                        } else {
                            hide_error('date_to_complete_error');
                        }
                    }
                    return app.is_valid;
                }


                //this function will save the data.
                function add_department_task() {
                    if (app.form_status == 'save') {
                        if (department_task_form_validation()) {
                            startLoad();
                            var task_description = $("#task_description2").val();
                            var task_weight = $("#task_weight").val();
                            var department_objective_id = $("#department_objective").val();
                            var assigned_employee_id = app.current_user_id;
                            var date_to_complete = $("#date_to_complete").val();
                            var sub_department_id = $("#sub_departments_dropdown").val();

                            //storing last selected sub department id for future use.
                            var name = "last_selected_department_id" + app.goal_id;
                            localStorage.setItem(name, sub_department_id);

                            $.ajax({
                                dataType: "json",
                                type: "POST",
                                url: "<?php echo site_url('Appraisal/insert_department_task'); ?>",
                                data: {
                                    appraisal_sub_department_id: sub_department_id,
                                    task_description: task_description,
                                    task_weight: task_weight,
                                    department_objective_id: department_objective_id,
                                    assigned_employee_id: assigned_employee_id,
                                    date_to_complete: date_to_complete,
                                    department_appraisal_header_id: app.appraisal_header_id,
                                    task_created_user_type: 'employee'
                                },
                                success: function (data) {
                                    stopLoad();
                                    //load_sub_department_tasks();
                                    //load_department_appraisal_details();
                                    load_employee_tasks();
                                    addListener();
                                    $("#department_appraisal_task_modal").modal('hide');
                                }
                            });
                        }
                    } else if (app.form_status == 'edit') {
                        if (department_task_form_edit_validation()) {
                            startLoad();
                            var task_description = $("#task_description2").val();
                            var task_weight = $("#task_weight").val();
                            var department_objective_id = $("#department_objective").val();
                            var assigned_employee_id = app.current_user_id;
                            var date_to_complete = $("#date_to_complete").val();
                            var sub_department_id = $("#sub_departments_dropdown").val();

                            //storing last selected sub department id for future use.
                            var name = "last_selected_department_id" + app.goal_id;
                            localStorage.setItem(name, sub_department_id);

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
                                    task_id: app.task_id,
                                    appraisal_sub_department_id: sub_department_id
                                },
                                success: function (data) {
                                    stopLoad();
                                    load_employee_tasks();
                                    addListener();
                                    //load_sub_department_tasks();
                                    //load_department_appraisal_details();
                                    var accordian_id = "#acc" + app.appraisal_header_id;
                                    $(accordian_id).trigger("click");
                                    $("#department_appraisal_task_modal").modal('hide');
                                }
                            });
                        }
                    }

                }

                function load_sub_departments_dropdown(selected_value) {
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_sub_departments_by_department_id'); ?>",
                        data: {
                            department_appraisal_header_id: app.appraisal_header_id
                        },
                        success: function (data) {

                            var options = "";
                            data.forEach(function (item, index) {
                                if (selected_value == item.id) {
                                    select_status = "selected";
                                } else {
                                    select_status = "";
                                }
                                if (item.used_percentage != 100) {
                                    options += '<option ' + select_status + ' value="' + item.id + '">' + item.description + '</option>';
                                }
                            });
                            $('#sub_departments_dropdown').html("");
                            $('#sub_departments_dropdown').html(options);

                            if (selected_value == null) {
                                selected_value = $('#sub_departments_dropdown').val();//app.department_objectives[0].corporate_objective_id;
                            }
                        }
                    });


                }

                //function load_department_employees_dropdown(selected_value) {
                //    $.ajax({
                //        dataType: "json",
                //        type: "POST",
                //        url: "<?php //echo site_url('Appraisal/get_department_employees'); ?>//",
                //        data: {department_id: app.department_id},
                //        success: function (data) {
                //            var employees = "";
                //            data.forEach(function (item, index) {
                //                if (selected_value == item.EIdNo) {
                //                    select_status = "selected";
                //                } else {
                //                    select_status = "";
                //                }
                //                employees += '<option ' + select_status + ' value="' + item.EIdNo + '">' + item.Ename1 + '</option>';
                //            });
                //            //app.department_employees_drop_down_list_html = employees;
                //            $('#assigned_employee').html(employees);
                //        }
                //    });
                //}

                function load_department_objectives_dropdown(selected_value) {
                    var objectives = "";
                    app.department_objectives.forEach(function (item, index) {
                        if (selected_value == item.corporate_objective_id) {
                            select_status = "selected";
                        } else {
                            select_status = "";
                        }

                        if (item.used_percentage != 100) {
                            objectives += '<option ' + select_status + ' value="' + item.corporate_objective_id + '">' + item.objective_description + '</option>';
                        }
                    });
                    $('#department_objective').html("");
                    $('#department_objective').html(objectives);

                    if (selected_value == null) {
                        selected_value = $('#department_objective').val();//app.department_objectives[0].corporate_objective_id;
                    }
                    //$.ajax({
                    //    dataType: "json",
                    //    type: "POST",
                    //    url: "<?php //echo site_url('Appraisal/get_percentage_details'); ?>//",
                    //    data: {
                    //        department_id: app.department_id,
                    //        goal_id: app.goal_id,
                    //        objective_id: selected_value,
                    //        department_appraisal_header_id: app.appraisal_header_id
                    //    },
                    //    success: function (data) {
                    //        set_progress_bar_with_text('add_task_form_progress_bar', data.used_percentage);
                    //    }
                    //});
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

                function change_objective_used_percentage() {
                    var objective_id = $(this).val();
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_percentage_details'); ?>",
                        data: {
                            department_id: app.department_id,
                            goal_id: app.goal_id,
                            objective_id: objective_id,
                            department_appraisal_header_id: app.appraisal_header_id
                        },
                        success: function (data) {
                            set_progress_bar_with_text('add_task_form_progress_bar', data.used_percentage);
                        }
                    });
                }

                function task_progress_input_onchange() {
                    $('#task_progress_in_number').text($(this).val() + '%');
                }

                function change_objective_used_percentage() {
                    var objective_id = $(this).val();
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_percentage_details'); ?>",
                        data: {
                            department_id: app.department_id,
                            goal_id: app.goal_id,
                            objective_id: objective_id,
                            department_appraisal_header_id: app.appraisal_header_id
                        },
                        success: function (data) {
                            set_progress_bar_with_text('add_task_form_progress_bar', data.used_percentage);
                        }
                    });
                }

                function add_employee_task() {
                    app.form_status = 'save';
                    app.department_id = $(this).data('department_id');
                    app.goal_id = $(this).data('goal_id');
                    app.appraisal_header_id = $(this).data('appraisal_header_id');
                    app.goal_from = $(this).data('goal_from');
                    app.goal_to = $(this).data('goal_to');

                    var local_storage_id = "last_selected_department_id" + app.goal_id;
                    var last_selected_subdepartment = localStorage.getItem(local_storage_id);


                    app.department_objectives = new Array();
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_department_objectives'); ?>",
                        data: {
                            assigned_department_id: app.department_id,
                            corporate_goal_id: app.goal_id,
                            appraisal_header_id: app.appraisal_header_id
                        },
                        success: function (data) {

                            data.forEach(function (item, index) {
                                var department_objective = new Array();
                                department_objective['objective_description'] = item.description;
                                department_objective['corporate_objective_id'] = item.objective_id;
                                department_objective['used_percentage'] = item.used_percentage;
                                app.department_objectives.push(department_objective);
                            });
                            load_department_objectives_dropdown();
                            load_sub_departments_dropdown();

                            $("#sub_departments_dropdown").val(last_selected_subdepartment);
                            //load_department_employees_dropdown();
                            $("#department_appraisal_task_modal").modal('show');
                        }
                    });

                }

                function format_for_two_digits(num) {
                    if (num < 10) {
                        return '0' + num;
                    } else {
                        return num;
                    }
                }

                
                function load_employee_tasks(table_init = true) {

                    app.employee_task_table_array.forEach(function (item, index) {
                        item.destroy();
                    });

                    //clearing old stuff before append
                    $("#table_list").html("<p>&nbsp;</p>");
                    var emp_id = $('#employee_list_dropdown').val();//....................................

                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_appraisal_wise_employee_tasks'); ?>",
                        data: {},
                        success: function (data) {
                            data.sort(function(a,b){
                                // Turn your strings into dates, and then subtract them
                                // to get a value that is either negative, positive, or zero.
                                return new Date(b.approvedDate) - new Date(a.approvedDate);
                            });



                            if (data.length == 0) {
                                $("#table_list").html('<div style="color:red;margin-top: 10px;"><?php echo $this->lang->line('common_no_records_found') ?></div>');/*No Records*/
                            }
                            data.forEach(function (item_level_1, index) {


                                //accessing first element to get the goal narration and the goal id. all elements have common narration and a goal id.
                                var title = item_level_1.name + ' &nbsp;&nbsp;|&nbsp;&nbsp; ' + item_level_1.from_date + ' - ' + item_level_1.to_date + ' &nbsp;&nbsp;|&nbsp;&nbsp; ' + item_level_1.document_id;
                                var goal_id = item_level_1.goal_id;
                                var appraisal_header_id = item_level_1.appraisal_header_id;
                                var appraisal_sub_department_id = item_level_1.appraisal_sub_department_id;
                                var department_id = item_level_1.department_id;
                                var is_closed = item_level_1.is_closed;
                                var is_approved_for_emp_performance = item_level_1.employee_performance_approved;
                                var appraisal_type = item_level_1.appraisal_type;
//manager feedbacks
                                var manager_comment = item_level_1.manager_comment;
                                var suggested_reward = item_level_1.suggested_reward;
                                var identified_training_needs = item_level_1.identified_training_needs;
                                var special_remarks_from_emp = item_level_1.special_remarks_from_emp;
                                var special_remarks_from_hod = item_level_1.special_remarks_from_hod;

                                var is_confirmed_by_employee = item_level_1.is_confirmed_by_employee;

                                //table header
                                var add_task_button_visibility = "";
                                var closed_label_visibility = ";visibility : hidden;";
                                if (item_level_1.is_closed == 1 || is_approved_for_emp_performance == 1) {
                                    add_task_button_visibility = ";visibility : hidden;";
                                    closed_label_visibility = "";
                                } else {
                                    //hidden this for now as per request.
                                    add_task_button_visibility = ";visibility : hidden;";
                                }

                                var closed_goal_disable = "";
                                var closed_text = "";
                                if (item_level_1.is_closed == 1) {
                                    closed_goal_disable = "disabled";
                                    closed_text = '<div class="closed-label"><span class="label label-text-size" style="background-color: red;margin-right: 5px;"><?php echo $this->lang->line('common_closed'); ?></span></div>';/*Closed*/
                                }

                                var closed_by_name = item_level_1.closed_by;
                                var closed_at = item_level_1.closed_at;

                                var softskills_performance = null;

                                var softskills_performance_manager_marked = null;

                                if (appraisal_type == 'performance_based' || appraisal_type == 'both') {
                                    // app.goal_id = goal_id;
                                    softskills_performance = get_softskills_performance_appraisal(goal_id);
                                    softskills_performance_manager_marked = get_softskills_performance_appraisal_manager_marked(goal_id);
                                }



                                var table = '<button id="acc' + appraisal_header_id + '" class="accordion" style="' +
                                    '    background: rgba(0, 166, 90, 0.23137254901960785);' +
                                    '">' + title + ' ' + closed_text + '</button>' +
                                    '<div class="panel cus-panel">' +
                                    '<ul class="nav nav-tabs">' +
                                    '    <li class="active"><a data-toggle="tab" id="tab_objective' + goal_id + '" href="#home' + goal_id + '"><?php echo $this->lang->line('appraisal_objective_based_performance'); ?></a></li>' +
                                    '    <li><a data-toggle="tab" id="tab_performance' + goal_id + '" href="#menu1' + goal_id + '" ><?php echo $this->lang->line('appraisal_soft_skills_based_performance'); ?></a></li>' +
                                    '  </ul>' +

                                    '<div class="tab-content" style="min-height: 9em;">' +
                                    '<div id="home' + goal_id + '" class="tab-pane fade in active">' +
                                    '<button style="' + add_task_button_visibility + '" data-goal_from="' + item_level_1.from_date + '" data-goal_to="' + item_level_1.to_date + '" onclick="add_employee_task.call(this)" data-department_id="' + department_id + '" data-goal_id="' + goal_id + '" data-appraisal_header_id="' + appraisal_header_id + '" data-appraisal_sub_department_id="' + appraisal_sub_department_id + '"><?php //echo $this->lang->line('common_add_task') ?>Add Task</button>' +
                                    '<table id="employee_task_table' + goal_id + '" class="<?php echo table_class(); ?>">' +
                                    '                            <thead>' +
                                    '                            <tr>' +
                                    '                                <th style="min-width: 15%">#</th>' +
                                    '                                <th style="min-width: 15%">' +
                                    '                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_task_description'); ?>' +
                                    '                                </th>' +
                                    '                                <th style="min-width: 15%">' +
                                    '                                   <?php echo $this->lang->line('common_department'); ?>' +
                                    '                                </th>' +
                                    '                                <th style="min-width: 15%">' +
                                    '                                    <?php echo $this->lang->line('appraisal_master_subdepartment_column'); ?>' +
                                    '                                </th>' +
                                    '                                <th style="min-width: 15%">' +
                                    '                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_weight'); ?>' +
                                    '                                </th>' +
                                    '                                <th style="min-width: 15%">' +
                                    '                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_assigned_employee'); ?>' +
                                    '                                </th>' +
                                    '                                <th style="min-width: 15%">' +
                                    '                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_date_to_complete'); ?>' +
                                    '                                </th>' +
                                    '                                <th style="width: 87px">' +
                                    '                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_is_approved_by_manager'); ?>' +
                                    '                                </th>' +
                                    '                                <th style="min-width: 15%">' +
                                    '                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_completion'); ?>' +
                                    '                                </th>' +
                                    '                                <th style="min-width: 15%">' +
                                    '                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_objective_manager_review'); ?>' +
                                    '                                </th>' +
                                    '                                <th style="min-width: 15%">' +
                                    '                                    <?php echo $this->lang->line('common_action'); ?>' +
                                    '                                </th>' +
                                    '                                <th style="min-width: 15%">' +
                                    '                                    <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department_task_discussion'); ?>' +
                                    '                                </th>' +
                                    '                            </tr>' +
                                    '                            </thead>' +
                                    '                           <tbody>' +
                                    '</table>' +
                                    '<div class="col-md-12">\n' +
                                    '                                    <div class="form-group">\n' +
                                    '                                        <label class="manager_comment_label">\n' +
                                    '                                            &nbsp;<?php echo $this->lang->line('appraisal_activity_department_manager_comment'); ?>\n' +
                                    '                                        </label>\n' +
                                    '                                        <textarea disabled="true" class="manager_comment_text">' + manager_comment + '</textarea>\n' +
                                    '                                    </div>\n' +
                                    '                                </div>' +
                                    '<div class="col-md-12">\n' +
                                    '                                    <div class="form-group">\n' +
                                    '                                        <label class="manager_comment_label">\n' +
                                    '                                            &nbsp;<?php echo $this->lang->line('appraisal_suggested_rewards'); ?>\n' +
                                    '                                        </label>\n' +
                                    '                                        <textarea disabled="true" class="manager_comment_text">' + suggested_reward + '</textarea>\n' +
                                    '                                    </div>\n' +
                                    '                                </div>' +
                                    '<div class="col-md-12">\n' +
                                    '                                    <div class="form-group">\n' +
                                    '                                        <label class="manager_comment_label">\n' +
                                    '                                            &nbsp;<?php echo $this->lang->line('appraisal_identified_training_needs'); ?>\n' +
                                    '                                        </label>\n' +
                                    '                                        <textarea disabled="true" class="manager_comment_text">' + identified_training_needs + '</textarea>\n' +
                                    '                                    </div>\n' +
                                    '                                </div>' +
                                    '<div class="col-md-12">\n' +
                                    '                                    <div class="form-group">\n' +
                                    '                                        <label class="manager_comment_label">\n' +
                                    '                                            &nbsp;<?php echo $this->lang->line('appraisal_special_remarks_from_hod'); ?>\n' +
                                    '                                        </label>\n' +
                                    '                                        <textarea disabled="true" class="manager_comment_text">' + special_remarks_from_hod + '</textarea>\n' +
                                    '                                    </div>\n' +
                                    '                                </div>' +
                                    '<div class="col-md-12">\n' +
                                    '                                    <div class="form-group">\n' +
                                    '                                        <label class="manager_comment_label">\n' +
                                    '                                            &nbsp;<?php echo $this->lang->line('appraisal_employee_comment'); ?>\n' +
                                    '                                        </label>\n' +
                                    '                                        <textarea ' + closed_goal_disable + ' id="emp_comment_' + goal_id + '" class="manager_comment_text">' + special_remarks_from_emp + '</textarea>\n' +
                                    '                                    </div>\n' +
                                    '                                </div>' +
                                    '<div class="form-group col-md-12">\n' +
                                    '<div style="font-weight: bold;' + closed_label_visibility + '"><span><?php echo $this->lang->line('common_closed_by'); ?>:</span>&nbsp;<span>' + closed_by_name + '</span></div>' +
                                    '<div style="font-weight: bold;' + closed_label_visibility + '"><span><?php echo $this->lang->line('common_closed_date'); ?>:</span>&nbsp;<span></span>' + closed_at + '</div>' +
                                    '                                    <button ' + closed_goal_disable + ' id="btn_approve_employee_performance' + goal_id + '" class="btn btn-success"\n' +
                                    '                                            style="float: right;" data-id="#emp_comment_' + goal_id + '" data-goal_id="' + goal_id + '" data-department_id="' + department_id + '" data-emp_id="' + app.current_user_id + '" onclick="save_employee_comment.call(this)">\n' +
                                    '                                        <?php echo $this->lang->line('common_submit'); ?> \n' +
                                    '                                    </button>\n' +
                                    '                                </div>' +
                                    '</div>' +

                                    '    <div id="menu1' + goal_id + '" class="tab-pane fade">';
                                if (softskills_performance != null) {

                                    marking_type = softskills_performance.markingType;
                                    template_id = softskills_performance.template_id; 
                                        
                                        if(softskills_performance.markingType == 2){    //................................................................MPO base
                                            
                                            /**marked by you */
                                            table += '<div style="margin-top: 15px;"><span style="font-weight: 600;"><?php echo $this->lang->line('appraisal_marked_by_you'); ?></span></div>';

                                            var grand_total_measure = 0;
                                            var grand_total_employee = 0;
                                            var grand_total_manager = 0;
                                            
                                            softskills_performance.performance_areas.forEach(function (hd_item, index) {
                                                var performance_area = hd_item.description;
                                                var order = hd_item.order;

                                                // Check if hd_item.sub is defined and is an array
                                                if (Array.isArray(hd_item.sub)) {
                                                    table += ' <div class="table-responsive">';
                                                    table +=' <table id="soft_skill_MPO_table_mngr" class="<?php echo table_class(); ?>">';
                                                        table +=' <thead>';
                                                                table +=' <tr>';
                                                                    table +=' <th style="min-width: 5%"><b>'+order+'</b></th>';
                                                                        table +=' <th class="text-left" style="min-width: 50%"><u><b>'+performance_area+'</b></u></th>';
                                                                        table +=' <th style="min-width: 15%"><b>Measured Points</b></th>';
                                                                        table +=' <th style="min-width: 15%"><b>Employee Points</b></th>';
                                                                        table +=' <th style="min-width: 15%"><b>Manager Pointss</b></th>';
                                                                        table +=' </tr>';
                                                                    table +=' </thead>';
                                                                table +=' <tbody>';

                                                                var mpoint = 0;
                                                                var emp_point = 0;
                                                                var mngr_point = 0;
                                                                var total_measure = 0;
                                                                var total_employee = 0;
                                                                var total_manager = 0;
                                                                hd_item.sub.forEach(function(sub_item, index){

                                                                    mpoint = sub_item.measuredPoints ? parseFloat(sub_item.measuredPoints) : 0; // Convert to number
                                                                    emp_point = sub_item.employeePoints ? parseFloat(sub_item.employeePoints) : 0; // Convert to number
                                                                    mngr_point = sub_item.managerPoints ? parseFloat(sub_item.managerPoints) : 0; // Convert to number

                                                                    table +=' <tr>';
                                                                        table +=' <td class="text-center">&nbsp;</td>';
                                                                        table +=' <td>* '+sub_item.description+'</td>';
                                                                        table +=' <td><input type="text" name="measuredPoint" style="width:100%" id="measuredPoint_'+ sub_item.performance_area_id +'" placeholder="0" value="'+ mpoint +'" class="measuredPoint1 number" onkeyup="calculate_total_measurePoints(this, '+ hd_item.performance_area_id  +')" ';
                                                                        table +=' onchange="save_measurepoint('+ sub_item.performance_area_id +', this.value)" readonly></td> ';

                                                                        table +=' <td><input type="text" name="empmeasuredPoint" style="width:100%" id="empmeasuredPoint_'+ sub_item.performance_area_id +'" placeholder="0" value="'+ emp_point+'" class="empmeasuredPoint1 number" onkeyup="calculate_total_measurePoints(this, '+ hd_item.performance_area_id  +')" ';
                                                                        table +=' onchange="save_emp_measurepoint('+ sub_item.performance_area_id +', this.value, '+ app.current_user_id +', '+ goal_id +')" ></td> ';
                                                                        table +=' <td><input type="text" name="mngrmeasuredPoint" style="width:100%" id="mngrmeasuredPoint_'+ sub_item.performance_area_id +'" placeholder="0" value="'+ mngr_point +'" class="mngrmeasuredPoint1 number" onkeyup="calculate_total_measurePoints(this, '+ hd_item.performance_area_id  +')" ';
                                                                        table +=' onchange="save_measurepoint('+ sub_item.performance_area_id +', this.value)" readonly></td> ';
                                                                    table +=' </tr>';

                                                                    total_measure += mpoint;
                                                                    total_employee += emp_point;
                                                                    total_manager += mngr_point;
                                                                });

                                                                table +=' <tr style="background-color:rgb(221,210,0,0.6);">';
                                                                    table +=' <td class="text-center">-</td>';
                                                                        table +=' <td class="text-center">Total</td>';
                                                                        table +=' <td><input type="text" value="'+ total_measure +'" style="width:100%;background-color:rgb(221,210,0,0.6);" id="total_'+ hd_item.performance_area_id +'" placeholder="0" class="number" readonly></td>';
                                                                        table +=' <td><input type="text" value="'+ total_employee +'" style="width:100%;background-color:rgb(221,210,0,0.6);" id="emptotal_'+ hd_item.performance_area_id +'" placeholder="0" class="number" readonly></td>';
                                                                        table +=' <td><input type="text" value="'+ total_manager +'" style="width:100%;background-color:rgb(221,210,0,0.6);" id="mngrtotal_'+ hd_item.performance_area_id +'" placeholder="0" class="number" readonly></td>';
                                                                        table +=' </tr>';

                                                                    table +=' </tbody>';
                                                                table +=' </table>';
                                                        table +=' </div>';

                                                        grand_total_measure += total_measure;
                                                        grand_total_employee += total_employee;
                                                        grand_total_manager += total_manager;
                                                }
                                            });

                                            var tottbl = ""; //for grand total & percentages
                                            var percentage_measure = 100 + '%';
                                            var percentage_employee_a = ( (100/grand_total_measure) * grand_total_employee ).toFixed(2) + '%';
                                            var percentage_manager_a = ( (100/grand_total_measure) * grand_total_manager ).toFixed(2) + '%';

                                            tottbl += ' <div class="table-responsive">';
                                                tottbl +=' <table id="soft_skill_MPO_grandTotal_table" class="<?php echo table_class(); ?>">';
                                                    tottbl +=' <tbody>';
                                                        tottbl +=' <tr style="background-color:rgba(255,163,34,0.67);">';
                                                            tottbl +=' <td class="text-center" style="min-width: 5%">--</td>';
                                                            tottbl +=' <td class="text-center" style="min-width: 50%">Grand Total</td>';
                                                            tottbl +=' <td style="min-width: 15%"><input type="text" value="'+grand_total_measure+'" style="width:100%;background-color:rgba(255,163,34,0.67);" id="" placeholder="0" class="number" readonly></td>';
                                                            tottbl +=' <td style="min-width: 15%"><input type="text" value="'+grand_total_employee+'" style="width:100%;background-color:rgba(255,163,34,0.67);" id="" placeholder="0" class="number" readonly></td>';
                                                            tottbl +=' <td style="min-width: 15%"><input type="text" value="'+grand_total_manager+'" style="width:100%;background-color:rgba(255,163,34,0.67);" id="" placeholder="0" class="number" readonly></td>';
                                                        tottbl +=' </tr>';
                                                        tottbl +=' <tr style="background-color:rgba(255,163,34,0.67);">';
                                                            tottbl +=' <td class="text-center" style="min-width: 5%">%</td>';
                                                            tottbl +=' <td class="text-center" style="min-width: 50%">Percentage</td>';
                                                            tottbl +=' <td style="min-width: 15%"><input type="text" value="'+percentage_measure+'" style="width:100%;background-color:rgba(255,163,34,0.67);" id="" placeholder="0" class="number" readonly></td>';
                                                            tottbl +=' <td style="min-width: 15%"><input type="text" value="'+percentage_employee_a+'" style="width:100%;background-color:rgba(255,163,34,0.67);" id="" placeholder="0" class="number" readonly></td>';
                                                            tottbl +=' <td style="min-width: 15%"><input type="text" value="'+percentage_manager_a+'" style="width:100%;background-color:rgba(255,163,34,0.67);" id="" placeholder="0" class="number" readonly></td>';
                                                        tottbl +=' </tr>';
                                                    tottbl +=' </tbody>';
                                                tottbl +=' </table>';
                                            tottbl +=' </div>';

                                            table += tottbl;

                                        }
                                        else{   //............................................................................grade base

                                                //marked by you 
                                                table += '<div style="margin-top: 15px;"><span style="font-weight: 600;"><?php echo $this->lang->line('appraisal_marked_by_you'); ?></span></div>' +
                                                    '<table class="table table-striped table-bordered performance-table"><thead><tr>';
                                                        table += '<th><?php echo $this->lang->line('appraisal_performance_area'); ?></th>';
                                                        table += '<th><?php echo $this->lang->line('sub_performance_area'); ?></th>';

                                                softskills_performance.skills_grades_list.forEach(function (item, index) {
                                                    table += '<th>' + item.grade + ' (' + item.marks + ' Marks)</th>';
                                                });
                                                table += '</tr></thead>' +
                                                    '<tbody id="table_body_read_only">';


                                                var total = 0;
                                                softskills_performance.performance_areas.forEach(function (item, index) {
                                                    if (item.sub != null) {
                                                        let rowspan = item.sub.length+1;
                                                        table += '<tr>' +
                                                            '<td rowspan="'+rowspan+'">' + item.description + '</td>';
                                                        item.sub.forEach(function (item, index) {
                                                            table += '<tr>' +
                                                                '<td>' + item.description + '</td>';
                                                            var currently_selected_grade_id = item.grade_id;
                                                            var performance_area_id = item.performance_area_id;
                                                            var radio_group_name = "performance_" + goal_id + "_" + item.performance_area_id;
                                                            softskills_performance.skills_grades_list.forEach(function (item, index) {
                                                                var is_checked = '';
                                                                if (currently_selected_grade_id == item.id) {
                                                                    is_checked = 'checked';
                                                                    total += parseInt(item.marks);
                                                                }
                                                                table += '<td><label class="customcheck"><input ' + closed_goal_disable + ' class="input_' + goal_id + ' radio-' + goal_id + '" ' + is_checked + ' type="radio" name="' + radio_group_name + '" data-goal_id="' + goal_id + '" data-performance_id="' + performance_area_id + '" data-grade_id="' + item.id + '" data-emp_id="' + app.current_user_id + '" onclick="performance_radio_click.call(this)"/><span class="checkmark"></span></label></td>';
                                                            });
                                                            table += '</tr>';
                                                        });

                                                    }else{
                                                        table += '<tr>' +
                                                            '<td>' + item.description + '</td><td></td>';
                                                        var currently_selected_grade_id = item.grade_id;
                                                        var performance_area_id = item.performance_area_id;
                                                        var radio_group_name = "performance_" + goal_id + "_" + item.performance_area_id;
                                                        softskills_performance.skills_grades_list.forEach(function (item, index) {
                                                            var is_checked = '';
                                                            if (currently_selected_grade_id == item.id) {
                                                                is_checked = 'checked';
                                                                total += parseInt(item.marks);
                                                            }
                                                            table += '<td><label class="customcheck"><input ' + closed_goal_disable + ' class="input_' + goal_id + ' radio-' + goal_id + '" ' + is_checked + ' type="radio" name="' + radio_group_name + '" data-goal_id="' + goal_id + '" data-performance_id="' + performance_area_id + '" data-grade_id="' + item.id + '" data-emp_id="' + app.current_user_id + '" onclick="performance_radio_click.call(this)"/><span class="checkmark"></span></label></td>';
                                                        });
                                                        table += '</tr>';
                                                    }

                                                });
                                                table += ' </tbody></table>' +
                                                    '<div style="margin-top: 7px;"><span class="label label-text-size" style="background-color: #02cf32;margin-right: 5px;">Total Marks:</span> <span id="total_' + goal_id + '">' + total + '</span> <span style="margin-left: 106px;">Last updated: ' + softskills_performance.last_update_time + '</span></div>';


                                                //Manager Filled Data.................................................
                                                table += '<div style="margin-top: 15px;"><span style="font-weight: 600;"><?php echo $this->lang->line('appraisal_marked_by_manager'); ?></span></div>' +
                                                    '<table class="table table-striped table-bordered performance-table"><thead><tr>';
                                                    table += '<th><?php echo $this->lang->line('appraisal_performance_area'); ?></th>';
                                                    table += '<th><?php echo $this->lang->line('sub_performance_area'); ?></th>';

                                                softskills_performance_manager_marked.skills_grades_list.forEach(function (item, index) {
                                                    table += '<th>' + item.grade + ' (' + item.marks + ' Marks)</th>';
                                                });

                                                table += '</tr></thead>' +
                                                    '<tbody id="table_body_read_only">';

                                                var total = 0;
                                                softskills_performance_manager_marked.performance_areas.forEach(function (item, index) {
                                                    if (item.sub != null) {
                                                        let rowspan = item.sub.length+1;
                                                        table += '<tr>' +
                                                            '<td rowspan="'+rowspan+'">' + item.description + '</td>';
                                                            
                                                        item.sub.forEach(function (item, index) {
                                                            table += '<tr>' +
                                                                '<td>' + item.description + '</td>';
                                                            var currently_selected_grade_id = item.grade_id;
                                                            var performance_area_id = item.performance_area_id;
                                                            var radio_group_name = "performance_" + goal_id + "_" + item.performance_area_id;
                                                            softskills_performance_manager_marked.skills_grades_list.forEach(function (item, index) {
                                                                var is_checked = '';
                                                                if (currently_selected_grade_id == item.id) {
                                                                    is_checked = 'checked';
                                                                    total += parseInt(item.marks);
                                                                }
                                                                table += '<td><label class="customcheck" style="cursor: not-allowed;"><input disabled ' + is_checked + ' type="radio" name="" data-goal_id="' + goal_id + '" data-performance_id="' + performance_area_id + '" data-grade_id="' + item.id + '" data-emp_id="' + app.current_user_id + '" /><span class="checkmark"></span></label></td>';
                                                            });
                                                            table += '</tr>';
                                                        });

                                                    }else{
                                                        table += '<tr>' +
                                                            '<td>' + item.description + '</td><td></td>';
                                                        var currently_selected_grade_id = item.grade_id;
                                                        var performance_area_id = item.performance_area_id;
                                                        var radio_group_name = "performance_" + goal_id + "_" + item.performance_area_id;
                                                        softskills_performance_manager_marked.skills_grades_list.forEach(function (item, index) {
                                                            var is_checked = '';
                                                            if (currently_selected_grade_id == item.id) {
                                                                is_checked = 'checked';
                                                                total += parseInt(item.marks);
                                                            }
                                                            table += '<td><label class="customcheck" style="cursor: not-allowed;"><input disabled ' + is_checked + ' type="radio" name="" data-goal_id="' + goal_id + '" data-performance_id="' + performance_area_id + '" data-grade_id="' + item.id + '" data-emp_id="' + app.current_user_id + '" /><span class="checkmark"></span></label></td>';
                                                        });
                                                        table += '</tr>';
                                                    }

                                                });

                                                table += ' </tbody></table>' +
                                                '<div style="margin-top: 7px;"><span class="label label-text-size" style="background-color: #02cf32;margin-right: 5px;">Total Marks:</span> <span>' + total + '</span> <span style="margin-left: 106px;">Last updated: ' + softskills_performance_manager_marked.last_update_time + '</span></div>';

                                            }

                                            //...............................................................................text areas

                                            var begin_with_the_end_in_mind = softskills_performance.begin_with_the_end_in_mind;
                                            var miscellaneous_worth_mentioning = softskills_performance.miscellaneous_worth_mentioning;
                                            var benchmark_objective_assessment = softskills_performance.benchmark_objective_assessment;

                                            var career_and_training_action_plan = softskills_performance.career_and_training_action_plan;
                                            var manager_assessment_undertaking = softskills_performance.manager_assessment_undertaking;

                                            var manager_comment = softskills_performance.manager_comment;
                                            var suggested_reward = softskills_performance.suggested_reward;
                                            var identified_training_needs = softskills_performance.identified_training_needs;
                                            var special_remarks_from_hod = softskills_performance.special_remarks_from_hod;
                                            var special_remarks_from_emp = softskills_performance.special_remarks_from_emp;

                                            var template_mapping_id = softskills_performance.template_mapping_id;
                                            var emp_skills_confirmed = softskills_performance.is_confirmed_by_employee


                                            if(softskills_performance.markingType == 2) {       /**..................................................mpo based text areas */

                                            table += 
                                                
                                                '<br><div class="col-md-12 mpo_based">\n' + 
                                                        '        <div class="row">\n' +
                                                        '            <div class="form-group col-sm-2 md-offset-2">\n' +
                                                        '                <label class="title">Begin with the end in mind</label>\n' +
                                                        '            </div>\n' +
                                                        '            <div class="form-group col-sm-10">\n' +
                                                        '                <textarea class="form-control richtext" id="begin_with_the_end_in_mind_' + goal_id + '"\n' +
                                                        '                          name="technicalDetail"\n' +
                                                        '                          rows="2">' + begin_with_the_end_in_mind + '</textarea>\n' +
                                                        '            </div>\n' +
                                                        '        </div>\n' +
                                                        '        <hr>\n' +
                                                '</div>' +
                                                '<div class="col-md-12 mpo_based">\n' +
                                                        '        <div class="row">\n' +
                                                        '            <div class="form-group col-sm-2 md-offset-2">\n' +
                                                        '                <label class="title">Miscellaneous worth mentioning</label>\n' +
                                                        '            </div>\n' +
                                                        '            <div class="form-group col-sm-10">\n' +
                                                        '                <textarea class="form-control richtext" id="miscellaneous_worth_mentioning_' + goal_id + '"\n' +
                                                        '                          name="technicalDetail"\n' +
                                                        '                          rows="2">' + miscellaneous_worth_mentioning + '</textarea>\n' +
                                                        '            </div>\n' +
                                                        '        </div>\n' +
                                                        '        <hr>\n' +
                                                '</div>' +
                                                '<div class="col-md-12 mpo_based">\n' +
                                                        '        <div class="row">\n' +
                                                        '            <div class="form-group col-sm-2 md-offset-2">\n' +
                                                        '                <label class="title">Benchmark objective assessment</label>\n' +
                                                        '            </div>\n' +
                                                        '            <div class="form-group col-sm-10">\n' +
                                                        '                <textarea class="form-control richtext" id="benchmark_objective_assessment_' + goal_id + '"\n' +
                                                        '                          name="technicalDetail"\n' +
                                                        '                          rows="2">' + benchmark_objective_assessment + '</textarea>\n' +
                                                        '            </div>\n' +
                                                        '        </div>\n' +
                                                        '        <hr>\n' +
                                                '</div>' +
                                                '<div class="col-md-12 mpo_based">\n' +
                                                        '        <div class="row">\n' +
                                                        '            <div class="form-group col-sm-2 md-offset-2">\n' +
                                                        '                <label class="title">Manager Comments on Assessment</label>\n' +
                                                        '            </div>\n' +
                                                        '            <div class="form-group col-sm-10">\n' +
                                                        '                <textarea class="form-control richtext richtext_managerComment" id="richtext_managerComment_' + goal_id + '"\n' +
                                                        '                          name="technicalDetail"\n' +
                                                        '                          rows="2">' + manager_comment + '</textarea>\n' +
                                                        '            </div>\n' +
                                                        '        </div>\n' +
                                                        '        <hr>\n' +
                                                '</div>' +
                                                '<div class="col-md-12 mpo_based">\n' +
                                                        '        <div class="row">\n' +
                                                        '            <div class="form-group col-sm-2 md-offset-2">\n' +
                                                        '                <label class="title">Career and training action plan</label>\n' +
                                                        '            </div>\n' +
                                                        '            <div class="form-group col-sm-10">\n' +
                                                        '                <textarea class="form-control richtext richtext_careerAndAction" id="career_and_training_action_plan_' + goal_id + '"\n' +
                                                        '                          name="technicalDetail"\n' +
                                                        '                          rows="2">' + career_and_training_action_plan + '</textarea>\n' +
                                                        '            </div>\n' +
                                                        '        </div>\n' +
                                                        '        <hr>\n' +
                                                '</div>' +
                                                '<div class="col-md-12 mpo_based">\n' +
                                                        '        <div class="row">\n' +
                                                        '            <div class="form-group col-sm-2 md-offset-2">\n' +
                                                        '                <label class="title">Manager assessment undertaking</label>\n' +
                                                        '            </div>\n' +
                                                        '            <div class="form-group col-sm-10">\n' +
                                                        '                <textarea class="form-control richtext richtext_mangerAssessmntUndertakung" id="manager_assessment_undertaking_' + goal_id + '"\n' +
                                                        '                          name="technicalDetail"\n' +
                                                        '                          rows="2">' + manager_assessment_undertaking + '</textarea>\n' +
                                                        '            </div>\n' +
                                                        '        </div>\n' +
                                                        '        <hr>\n' +
                                                '</div>' +
                                                // '<div class="col-md-12 mpo_based">\n' +
                                                // '                                    <div class="form-group">\n' +
                                                // '                                        <label class="manager_comment_label">\n' +
                                                // '                                            &nbsp;Manager assessment undertaking\n' +
                                                // '                                        </label>\n' +
                                                // '                                        <textarea disabled="true" class="manager_comment_text">' + manager_assessment_undertaking + '</textarea>\n' +
                                                // '                                    </div>\n' +
                                                // '                                </div>' +
                                               
                                                /*..............common text areas*/
                                                '<div class="col-md-12">\n' +
                                                '                                    <div class="form-group">\n' +
                                                '                                        <label class="manager_comment_label">\n' +
                                                '                                            &nbsp;Manager comment\n' +
                                                '                                        </label>\n' +
                                                '                                        <textarea disabled="true" class="manager_comment_text">' + manager_comment + '</textarea>\n' +
                                                '                                    </div>\n' +
                                                '                                </div>' +
                                                '<div class="col-md-12">\n' +
                                                '                                    <div class="form-group">\n' +
                                                '                                        <label class="manager_comment_label">\n' +
                                                '                                            &nbsp;<?php echo $this->lang->line('appraisal_suggested_rewards'); ?>\n' +
                                                '                                        </label>\n' +
                                                '                                        <textarea disabled="true" class="manager_comment_text">' + suggested_reward + '</textarea>\n' +
                                                '                                    </div>\n' +
                                                '                                </div>' +
                                                '<div class="col-md-12">\n' +
                                                '                                    <div class="form-group">\n' +
                                                '                                        <label class="manager_comment_label">\n' +
                                                '                                            &nbsp;<?php echo $this->lang->line('appraisal_identified_training_needs'); ?>\n' +
                                                '                                        </label>\n' +
                                                '                                        <textarea disabled="true" class="manager_comment_text">' + identified_training_needs + '</textarea>\n' +
                                                '                                    </div>\n' +
                                                '                                </div>' +
                                                '<div class="col-md-12">\n' +
                                                '                                    <div class="form-group">\n' +
                                                '                                        <label class="manager_comment_label">\n' +
                                                '                                            &nbsp;<?php echo $this->lang->line('appraisal_special_remarks_from_hod'); ?>\n' +
                                                '                                        </label>\n' +
                                                '                                        <textarea disabled="true" class="manager_comment_text">' + special_remarks_from_hod + '</textarea>\n' +
                                                '                                    </div>\n' +
                                                '                                </div>' +
                                                '<div class="col-md-12">\n' +
                                                '                                    <div class="form-group">\n' +
                                                '                                        <label class="manager_comment_label">\n' +
                                                '                                            &nbsp;<?php echo $this->lang->line('appraisal_employee_comment'); ?>\n' +
                                                '                                        </label>\n' +
                                                '                                        <textarea ' + closed_goal_disable + ' id="emp_skills_comment_mpo_' + goal_id + '" class="manager_comment_text">' + special_remarks_from_emp + '</textarea>\n' +
                                                '                                    </div>\n' +
                                                '                                </div>' +
                                                '<div class="form-group col-md-12">\n' +
                                                '<div class="col-md-2">' +
                                                '<div style="font-weight: bold;' + closed_label_visibility + '"><span>Closed By:</span>&nbsp;<span>' + closed_by_name + '</span></div>' +
                                                '<div style="font-weight: bold;' + closed_label_visibility + '"><span>Closed Date:</span>&nbsp;<span>' + closed_at + '</span></div>' +
                                                '</div>' +
                                                '<div class="col-md-8"></div><div class="col-md-2"><button ' + closed_goal_disable + ' id="btn_approve_employee_skills' + goal_id + '" class="btn btn-success"\n' +
                                                '                                            style="float: right;" data-id="#emp_skills_comment_mpo_' + goal_id + '" data-goal_id="' + goal_id + '" data-template_mapping_id="' + template_mapping_id + '" onclick="save_employee_skills_comment.call(this)">\n' +
                                                '                                        Submit \n' +
                                                '                                    </button></div>\n' +
                                                '                                </div>' +
                                                '';

                                            }else{     /*................................................................................grade base*/

                                                table += 
                                                /*.................common text areas*/
                                                '<div class="col-md-12">\n' +
                                                '                                    <div class="form-group">\n' +
                                                '                                        <label class="manager_comment_label">\n' +
                                                '                                            &nbsp;Manager comment\n' +
                                                '                                        </label>\n' +
                                                '                                        <textarea disabled="true" class="manager_comment_text">' + manager_comment + '</textarea>\n' +
                                                '                                    </div>\n' +
                                                '                                </div>' +
                                                '<div class="col-md-12">\n' +
                                                '                                    <div class="form-group">\n' +
                                                '                                        <label class="manager_comment_label">\n' +
                                                '                                            &nbsp;<?php echo $this->lang->line('appraisal_suggested_rewards'); ?>\n' +
                                                '                                        </label>\n' +
                                                '                                        <textarea disabled="true" class="manager_comment_text">' + suggested_reward + '</textarea>\n' +
                                                '                                    </div>\n' +
                                                '                                </div>' +
                                                '<div class="col-md-12">\n' +
                                                '                                    <div class="form-group">\n' +
                                                '                                        <label class="manager_comment_label">\n' +
                                                '                                            &nbsp;<?php echo $this->lang->line('appraisal_identified_training_needs'); ?>\n' +
                                                '                                        </label>\n' +
                                                '                                        <textarea disabled="true" class="manager_comment_text">' + identified_training_needs + '</textarea>\n' +
                                                '                                    </div>\n' +
                                                '                                </div>' +
                                                '<div class="col-md-12">\n' +
                                                '                                    <div class="form-group">\n' +
                                                '                                        <label class="manager_comment_label">\n' +
                                                '                                            &nbsp;<?php echo $this->lang->line('appraisal_special_remarks_from_hod'); ?>\n' +
                                                '                                        </label>\n' +
                                                '                                        <textarea disabled="true" class="manager_comment_text">' + special_remarks_from_hod + '</textarea>\n' +
                                                '                                    </div>\n' +
                                                '                                </div>' +
                                                '<div class="col-md-12">\n' +
                                                '                                    <div class="form-group">\n' +
                                                '                                        <label class="manager_comment_label">\n' +
                                                '                                            &nbsp;<?php echo $this->lang->line('appraisal_employee_comment'); ?>\n' +
                                                '                                        </label>\n' +
                                                '                                        <textarea ' + closed_goal_disable + ' id="emp_skills_comment_' + goal_id + '" class="manager_comment_text">' + special_remarks_from_emp + '</textarea>\n' +
                                                '                                    </div>\n' +
                                                '                                </div>' +
                                                '<div class="form-group col-md-12">\n' +
                                                '<div class="col-md-2">' +
                                                '<div style="font-weight: bold;' + closed_label_visibility + '"><span>Closed By:</span>&nbsp;<span>' + closed_by_name + '</span></div>' +
                                                '<div style="font-weight: bold;' + closed_label_visibility + '"><span>Closed Date:</span>&nbsp;<span>' + closed_at + '</span></div>' +
                                                '</div>' +
                                                '<div class="col-md-8"></div><div class="col-md-2"><button ' + closed_goal_disable + ' id="btn_approve_employee_skills' + goal_id + '" class="btn btn-success"\n' +
                                                '                                            style="float: right;" data-id="#emp_skills_comment_' + goal_id + '" data-goal_id="' + goal_id + '" data-template_mapping_id="' + template_mapping_id + '" onclick="save_employee_skills_comment.call(this)">\n' +
                                                '                                        Submit \n' +
                                                '                                    </button></div>\n' +
                                                '                                </div>' +
                                                '';
                                            }
                                

                                } else {
                                    table += '<div class="text-center pt-100" style="color: red;"><?php echo $this->lang->line('appraisal_soft_skills_based_performance_has_not_configured_for_this_appraisal') ?></div>';/*Soft skills based performance has not configured for this appraisal*/
                                }

                                    table += '   </div>' +
                                        '  </div>' +
                                        '</div>';
                                

                                
                                $("#table_list").append(table);    ///////////////////////////////////////


                                //removing default content then replace with a message if 'performance based' appraisal.
                                if (appraisal_type == 'performance_based') {
                                    $("#home" + goal_id).html('<div class="text-center pt-100" style="color: red;">Template not configured.</div>');
                                    $("#tab_objective" + goal_id).hide();
                                    $("#tab_performance" + goal_id).trigger('click');

                                }

                                if (appraisal_type == 'objective_based') {
                                    $("#menu1" + goal_id).html('<div class="text-center pt-100" style="color: red;">Objective based template not configured.</div>');
                                    $("#tab_performance" + goal_id).hide();
                                    $("#tab_objective" + goal_id).trigger('click');

                                }


                                //
                                if (emp_skills_confirmed == 1) {
                                    var selector = '#emp_skills_comment_' + goal_id;
                                    $(selector).attr('disabled', true);
                                    var input_selector = ".input_" + goal_id;
                                    $(input_selector).attr('disabled', true);
                                    $("#btn_approve_employee_skills" + goal_id).attr('disabled', true);
                                }

                                if (is_confirmed_by_employee == 1) {
                                    var selector = '#emp_comment_' + goal_id;
                                    $(selector).attr('disabled', true);
                                    $("#btn_approve_employee_performance" + goal_id).attr('disabled', true);
                                }

                                var table_selector = '#employee_task_table' + goal_id;

                                app.employee_task_table_array[table_selector] = $(table_selector).DataTable({
                                    "language": {
                                        "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                                    },
                                });

                                var sequence = 1;
                                //this loop will append the table rows

                                if(item_level_1.data!=null){
                                    item_level_1.data.forEach(function (item_level_2, index) {

                                        var task_description = item_level_2.task_description;
                                        var weight = item_level_2.weight;
                                        var Ename1 = item_level_2.Ename1;

                                        d = new Date(item_level_2.date_to_complete);
                                        var month = format_for_two_digits((d.getMonth() + 1));
                                        var date = format_for_two_digits(d.getDate());
                                        var date_to_complete = d.getFullYear() + '-' + month + '-' + date;

                                        if (item_level_2.message == null) {
                                            item_level_2.message = '<?php echo $this->lang->line('appraisal_start_a_discussion') ?>';/*Start a discussion*/
                                        } else {
                                            item_level_2.message = item_level_2.message.substring(0, 18) + '...';
                                        }

                                        var discussion = '<div data-goal_id="' + goal_id + '" data-task_id="' + item_level_2.id + '" onclick="show_discussion_dialog.call(this)" style="text-decoration: underline;color:blue;float: right;cursor: pointer;">' + item_level_2.message + '</div>';

                                        var progress_bar_text_color = 'black';
                                        if (item_level_2.completion >= 60) {
                                            progress_bar_text_color = 'white';
                                        }

                                        var progerss_title = ""
                                        if (item_level_2.is_closed != 1 && item_level_2.is_approved_by_manager == true && is_approved_for_emp_performance == 0) {
                                            //blank.
                                        } else {
                                            progerss_title = "<?php echo $this->lang->line('appraisal_this_progress_allowed_to_update_only_after_manager_approved_the_task') ?>";/*This progress allowed to update only after manager approved the task*/
                                        }

                                        var completion = '<div title="' + progerss_title + '" class="progress" style="height: 20px;">' +
                                            '  <div class="progress-bar" role="progressbar" style="width: ' + item_level_2.completion + '%;" aria-valuenow="' + item_level_2.completion + '" aria-valuemin="0" aria-valuemax="100"></div>' +
                                            '<span style="color: ' + progress_bar_text_color + ';">' + item_level_2.completion + '%</span>' +
                                            '</div>';

                                        if (item_level_2.is_closed != 1 && item_level_2.is_approved_by_manager == true && is_approved_for_emp_performance == 0) {
                                            completion += '<span data-appraisal_header_id="' + appraisal_header_id + '" onclick="update_progress.call(this)" data-task_progress="' + item_level_2.completion + '" data-task_description="' + task_description + '" data-task_id="' + item_level_2.id + '" style="text-decoration: underline;color:blue;float: right;cursor: pointer;"><?php echo $this->lang->line('common_update') ?></span>';

                                        }
                                        var manager_review = item_level_2.manager_review;
                                        if (item_level_2.manager_review == 'pending') {
                                            manager_review = '<?php echo $this->lang->line('common_pending') ?>';/*Pending*/
                                        } else if (item_level_2.manager_review == 'rejected') {
                                            manager_review = '<?php echo $this->lang->line('common_rejected') ?>';/*Rejected*/
                                        } else if (item_level_2.manager_review == 'approved') {
                                            manager_review = '<?php echo $this->lang->line('common_approved') ?>';/*Approved*/
                                        }


                                        var is_approved_by_manager = item_level_2.is_approved_by_manager;
                                        var manager_approved_checkbox_status = "";
                                        if (is_approved_by_manager == 1) {
                                            manager_approved_checkbox_status = "checked";
                                        }
                                        var manager_approve_checkbox = '<div style="text-align: center"><input disabled data-task_id="' + item_level_2.id + '" type="checkbox" ' + manager_approved_checkbox_status + '/></div>';


                                        var action = "";
                                        //employee not allowed to edit a task after manager approved.
                                        var action_tooltip = "";
                                        if (is_approved_by_manager == 1) {
                                            action_tooltip = "<?php echo $this->lang->line('appraisal_already_approved_by_manager') ?>";/*Already Approved By Manager*/
                                        }


                                        if (item_level_2.is_closed == 1) {
                                            action_tooltip = "<?php echo $this->lang->line('appraisal_goal_is_closed') ?>";/*Goal is Closed*/
                                        }

                                        if (is_approved_by_manager == 0 && item_level_2.is_closed != 1) {
                                            action = '<i data-department_id="' + department_id + '" data-goal_id="' + goal_id + '" data-appraisal_header_id="' + appraisal_header_id + '" onclick="employee_task_edit_popup.call(this)" data-task_id="' + item_level_2.id + '" class="glyphicon glyphicon-pencil act-btn-margin" style="color: #3c8dbc;" data-goal_from="' + item_level_1.from_date + '" data-goal_to="' + item_level_1.to_date + '"></i>';
                                        } else {
                                            action = '<i title="' + action_tooltip + '" class="fa fa-ban"></i>';
                                        }

                                        if (is_approved_for_emp_performance == 1) {
                                            action = '<i title="Already approved this task for employee performance." class="fa fa-ban"></i>';
                                        }

                                        let sub_department = item_level_2.appraisal_sub_department;
                                        let department = item_level_2.department;

                                        app.goal_close_status[goal_id] = item_level_2.is_closed;

                                        app.employee_task_table_array[table_selector].row.add([sequence, task_description,department,sub_department, weight, Ename1, date_to_complete, manager_approve_checkbox, completion, manager_review, action, discussion]).draw();
                                        sequence++;


                                    });
                                }


                            });


                        }
                    });
                }


                function performance_radio_click() {
                    var grade_id = $(this).data('grade_id');
                    var performance_id = $(this).data('performance_id');
                    var emp_id = $(this).data('emp_id');
                    var goal_id = $(this).data('goal_id');
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/save_emp_softskills_grade_self_eval'); ?>",
                        data: {performance_id: performance_id, emp_id: emp_id, goal_id: goal_id, grade_id: grade_id},
                        success: function (data) {
                            if (data.status == 'success') {
                                myAlert('i', data.message);
                                let id = "#total_" + goal_id;
                                $(id).text(data.total);
                            } else {
                                myAlert('e', data.message);

                            }
                        }
                    });
                }

                function save_employee_skills_comment(input_id) {

                    var id = $(this).data('id');
                    var comment = $(id).val();
                    var goal_id = $(this).data('goal_id');
                    var template_mapping_id = $(this).data('template_mapping_id');
                    
                    if(marking_type == 2){
                        var begin_with_the_end_in_mind = tinyMCE.get('begin_with_the_end_in_mind_'+goal_id+'').getContent();
                        var miscellaneous_worth_mentioning = tinyMCE.get('miscellaneous_worth_mentioning_'+goal_id+'').getContent();
                        var benchmark_objective_assessment = tinyMCE.get('benchmark_objective_assessment_'+goal_id+'').getContent();
                    }else{
                        var begin_with_the_end_in_mind = null;
                        var miscellaneous_worth_mentioning = null;
                        var benchmark_objective_assessment = null;
                    }

                    bootbox.confirm({
                        message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_submit_this') ?>",/*Are you sure you want to submit this?*/
                        buttons: {
                            confirm: {
                                label: '<?php echo $this->lang->line('common_yes'); ?>',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: '<?php echo $this->lang->line('common_no'); ?>',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (user_confirmation) {
                            if (user_confirmation) {
                                startLoad();
                                if (validation_before_confirm(goal_id)) {
                                    $.ajax({
                                        async: false,
                                        dataType: "json",
                                        type: "POST",
                                        url: "<?php echo site_url('Appraisal/save_employee_skills_comment'); ?>",
                                        data:  { 
                                                template_mapping_id: template_mapping_id, 
                                                comment: comment,
                                                begin_with_the_end_in_mind: begin_with_the_end_in_mind,
                                                miscellaneous_worth_mentioning: miscellaneous_worth_mentioning,
                                                benchmark_objective_assessment: benchmark_objective_assessment
                                            },
                                        success: function (data) {
                                            stopLoad();
                                            myAlert(data[0], data[1]);
                                            if (data[0] == 's') {
                                                $('#btn_approve_employee_skills' + goal_id).attr('disabled', 'disabled');
                                                $('#emp_skills_comment_' + goal_id).attr('disabled', 'disabled');
                                                let selector = ".input_" + goal_id;
                                                $(selector).attr('disabled', 'disabled');
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    });


                }

                function validation_before_confirm(goal_id) {
                    var is_valid = true;

                    var rating_input = extract_values_from_rating_input(goal_id);
                    var rating_validation = validate_rating_input(rating_input);
                    if (rating_validation == false) {
                        is_valid = false;
                        show_error('rating_error', '<?php echo $this->lang->line(''); ?>');/*All items are required in the rating satisfaction*/
                        myAlert('e', '<?php echo $this->lang->line('appraisal_all_items_are_required_in_the_rating_satisfaction'); ?>');/*All items are required in the rating satisfaction*/
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

                function extract_values_from_rating_input(goal_id) {

                    let selector = "td input[class*='radio-" + goal_id + "']";
                    var number_of_checkboxes = $(selector).length;
                    var checkbox_group = [];
                    for (var i = 0; i < number_of_checkboxes; i++) {
                        var element = $(selector)[i];
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

                function save_employee_comment(input_id) {
                    var id = $(this).data('id');
                    var goal_id = $(this).data('goal_id');
                    var department_id = $(this).data('department_id');
                    var emp_id = $(this).data('emp_id');

                    var comment = $(id).val();

                    bootbox.confirm({
                        message: "<?php echo $this->lang->line('appraisal_are_you_sure_you_want_to_submit_this'); ?>?",
                        buttons: {
                            confirm: {
                                label: '<?php echo $this->lang->line('common_yes'); ?>',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: '<?php echo $this->lang->line('common_no'); ?>',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (user_confirmation) {
                            if (user_confirmation) {
                                startLoad();
                                $.ajax({
                                    async: false,
                                    dataType: "json",
                                    type: "POST",
                                    url: "<?php echo site_url('Appraisal/save_employee_comment'); ?>",
                                    data: {
                                        goal_id: goal_id,
                                        department_id: department_id,
                                        emp_id: emp_id,
                                        comment: comment
                                    },
                                    success: function (data) {
                                        stopLoad();
                                        if (data) {
                                            myAlert(data[0], data[1]);
                                            if (data[0] == 's') {
                                                $('#btn_approve_employee_performance' + goal_id).attr('disabled', 'disabled');
                                                $('#emp_comment_' + goal_id).attr('disabled', 'disabled');
                                            }
                                        }
                                    }
                                });
                            }
                        }
                    });


                }

                function get_softskills_performance_appraisal(goal_id) {
                    app.res = null;
                    //goal_id = 64;
                    var emp_id = app.current_user_id;
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/fetch_employee_skills_performance_appraisal_self_eval'); ?>",
                        data: {config_goal_id: goal_id, emp_id: emp_id},
                        success: function (data) {
                            app.res = data;
                        }
                    });

                    return app.res;
                }

                function get_softskills_performance_appraisal_manager_marked(goal_id) {
                    app.res = null;
                    //goal_id = 64;
                    var emp_id = app.current_user_id;
                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/fetch_employee_skills_performance_appraisal'); ?>",
                        data: {config_goal_id: goal_id, emp_id: emp_id},
                        success: function (data) {
                            app.res = data;
                        }
                    });

                    return app.res;
                }

                function employee_task_edit_popup() {
                    var task_id = $(this).data('task_id');
                    app.task_id = task_id;
                    app.form_status = 'edit';
                    app.department_id = $(this).data('department_id');
                    app.goal_id = $(this).data('goal_id');
                    app.appraisal_header_id = $(this).data('appraisal_header_id');
                    app.goal_from = $(this).data('goal_from');
                    app.goal_to = $(this).data('goal_to');

                    var local_storage_id = "last_selected_department_id" + app.goal_id;
                    var last_selected_subdepartment = localStorage.getItem(local_storage_id);

                    $.ajax({
                        async: false,
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/get_sub_department_tasks_by_id'); ?>",
                        data: {sub_department_task_id: task_id},
                        success: function (data) {
                            $("#task_description2").val(data[0].task_description);
                            $("#task_weight").val(data[0].weight);
                            app.current_task_weight = data[0].weight;
                            load_department_objectives_dropdown(data[0].department_objective_id);
                            d = new Date(data[0].date_to_complete);
                            var month = format_for_two_digits((d.getMonth() + 1));
                            var date = format_for_two_digits(d.getDate());
                            var to = d.getFullYear() + '-' + month + '-' + date;
                            $('#date_to_complete').val(to);
                            app.current_date_value = to;
                            //load_department_objectives_dropdown();

                            app.department_objectives = new Array();
                            $.ajax({
                                async: false,
                                dataType: "json",
                                type: "POST",
                                url: "<?php echo site_url('Appraisal/get_department_objectives'); ?>",
                                data: {
                                    assigned_department_id: app.department_id,
                                    corporate_goal_id: app.goal_id,
                                    appraisal_header_id: app.appraisal_header_id
                                },
                                success: function (data) {

                                    data.forEach(function (item, index) {
                                        var department_objective = new Array();
                                        department_objective['objective_description'] = item.description;
                                        department_objective['corporate_objective_id'] = item.objective_id;
                                        department_objective['used_percentage'] = item.used_percentage;
                                        app.department_objectives.push(department_objective);
                                    });
                                    load_department_objectives_dropdown();
                                    load_sub_departments_dropdown();

                                    $("#sub_departments_dropdown").val(last_selected_subdepartment);
                                    //load_department_employees_dropdown();
                                    $("#department_appraisal_task_modal").modal('show');
                                }
                            });


                            $("#department_appraisal_task_modal").modal('show');
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
                            }
                        });
                    }
                }

                function show_discussion_dialog() {
                    $('#discussion_message').val("");
                    app.discussion_task_id = $(this).data('task_id');
                    var goal_id = $(this).data('goal_id');
                    var goal_close_status = app.goal_close_status[goal_id];
                    if (goal_close_status == 1) {
                        $("#btn_send_message").attr('disabled', true);
                    } else {
                        $("#btn_send_message").attr('disabled', false);
                    }
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

                function save_progress() {
                    var task_progress = $('#task_progress_input').val();

                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/save_task_progress'); ?>",
                        data: {task_progress: task_progress, task_id: app.task_id_to_update_progress},
                        success: function (data) {
                            load_employee_tasks();
                            addListener();
                            var accordian_id = "#acc" + app.appraisal_header_id;
                            $(accordian_id).trigger("click");
                            $('#task_progress_update_modal').modal('hide');
                        }
                    });
                }

                function update_progress() {
                    app.appraisal_header_id = $(this).data('appraisal_header_id');
                    app.task_id_to_update_progress = $(this).data('task_id');
                    $('#task_description').text($(this).data('task_description'));
                    $('#task_progress_input').val($(this).data('task_progress'));
                    $('#task_progress_in_number').text($(this).data('task_progress') + '%');
                    $('#task_progress_update_modal').modal('show');
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
                addListener();


                function addListener() {
                    var acc = document.getElementsByClassName("accordion");
                    var i;

                    for (i = 0; i < acc.length; i++) {
                        acc[i].addEventListener("click", function () {
                            this.classList.toggle("active");

                            var appraisal_header_id = this.dataset.appraisalId;

                            var panel = this.nextElementSibling;
                            if (panel.style.display === "block") {
                                panel.style.display = "none";
                            } else {
                                panel.style.display = "block";
                            }
                        });
                    }
                }


        function calculate_total_measurePoints(element, nmbr) {
            var total = 0;
            var measuredPoint = 0;
            var employee_points = 0;

            $(element).closest('tr').find('input[name="measuredPoint"]').each(function() {
                measuredPoint = parseFloat($(this).val());
            });

            $(element).closest('table').find('input[name="empmeasuredPoint"]').each(function() {
                employee_points = parseFloat($(this).val());
                if(employee_points > measuredPoint){
                    myAlert('w', 'cannot be greater that measured point');
                    $(this).val(0);
                }else{
                    total += parseFloat($(this).val()) || 0;
                }
                
            });

            $('#emptotal_' + nmbr).val(total);
        }

        // function calculate_grandtotal_employee(element, fieldId) {
        //     var grandtotal_employee = 0;

        //     grandtotal_employee += parseFloat($(this).val()) || 0;

        //     $('#' + fieldId).val(grandtotal_employee);
        // }

        function save_emp_measurepoint(id, value, empid, goalID) {
            var grade_id = value;
            var performance_id = id;
            var emp_id = empid;
            var goal_id = goalID;
            $.ajax({
                async: false,
                dataType: "json",
                type: "POST",
                url: "<?php echo site_url('Appraisal/save_emp_softskills_empPoints'); ?>",
                data: {performance_id: performance_id, emp_id: emp_id, goal_id: goal_id, grade_id: grade_id},
                success: function (data) {
                    if (data.status == 'success') {
                        myAlert('i', data.message);
                        // load_employee_tasks();
                        // var accordian_id = "#acc" + app.appraisal_header_id;
                        // $(accordian_id).trigger("click");
                        //get_softskills_performance_appraisal(app.goal_id);

                    } else {
                        myAlert('e', data.message);

                    }
                }
            });
        }

</script>