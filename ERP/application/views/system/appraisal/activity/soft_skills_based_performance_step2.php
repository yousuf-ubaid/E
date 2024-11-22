<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_activity_softskills_performance_title');


?>
<style>
    .error-message {
        color: red;
    }

    .objectives-table th {
        text-align: left;
    }

    .act-btn-margin {
        margin: 0 2px;
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
</style>
<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?> - <span id="appraisal_name"></span>
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
                                            <span id="closed_label" class="label label-text-size" style="background-color: red; margin-right: 5px;">Closed</span>
                                        </div>
                                    </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div style="margin-top: 15px;"><span class="lbl1" style="font-weight: 600;display:none;">Marked By You</span></div>
                                    <div id="softskills_template">
                                    </div>
                                </div>
                                <div id="rating_error" class="error-message"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-2 total_div" style="margin-top: 7px;">
                                    <span class="label label-text-size" style="background-color: #02cf32;margin-right: 5px;"> <?php echo $this->lang->line('appraisal_total_marks'); ?>: </span> <span
                                        id="total"></span>
                                </div>
                                <div class="col-md-4 last_update_mgr_div" style="margin-top: 7px;">Last updated: <span id="last_update_mgr"></span></div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div style="margin-top: 15px;"><span class="lbl1" style="font-weight: 600;display:none;">Marked By Employee</span></div>
                                    <div id="softskills_template_emp_self">
                                    </div>
                                </div>
                                <div id="rating_error" class="error-message"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-2 total_div" style="margin-top: 7px;">
                                    <span class="label label-text-size" style="background-color: #02cf32;margin-right: 5px;"> <?php echo $this->lang->line('appraisal_total_marks'); ?>: </span> <span
                                        id="total_emp"></span>
                                </div>
                                <div class="col-md-4 last_update_emp_div" style="margin-top: 7px;">Last updated: <span id="last_update_emp"></span></div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="manager_comment_label">
                                            Manager comment
                                        </label>
                                        <textarea id="manager_comment"
                                                  class="manager_comment_text"></textarea>
                                        <div id="manager_comment_error" class="error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="manager_comment_label">
                                            &nbsp;Suggested reward
                                        </label>
                                        <textarea id="suggested_reward_input" class="manager_comment_text"></textarea>
                                        <div id="suggested_reward_error" class="error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="manager_comment_label">
                                            Identified training needs
                                        </label>
                                        <textarea id="identified_training_needs"
                                                  class="manager_comment_text"></textarea>
                                        <div id="identified_training_needs_error" class="error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="manager_comment_label">
                                            &nbsp;Special remarks from HOD
                                        </label>
                                        <textarea id="special_remarks_from_hod" class="manager_comment_text"></textarea>
                                        <div id="special_remarks_from_hod_error" class="error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="manager_comment_label">
                                            &nbsp;Employee comment
                                        </label>
                                        <textarea disabled="true" id="employee_comment"
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
                                            onclick="btn_save_manager_comments_as_draft.call(this)"> Save as Draft
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button id="btn_save_manager_comments" class="btn btn-success form-control pull-right"
                                            onclick="btn_save_manager_comments.call(this)"> Save & Confirm
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
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>

    $(document).ready(function () {
        app.config_department_id = localStorage.getItem('config_department_id');
        app.config_goal_id = localStorage.getItem('config_goal_id');
        $("#appraisal_name").text(localStorage.getItem('appraisal_name'));
        load_department_employees_dropdown(app.config_department_id);
        $(".total_div").hide();
        $(".last_update_mgr_div").hide();
        $(".last_update_emp_div").hide();
        hide_manager_comment_boxes();
    });

    function btn_save_manager_comments_as_draft() {
        var suggested_reward_input = $("#suggested_reward_input").val();
        var identified_training_needs = $("#identified_training_needs").val();
        var special_remarks_from_hod = $("#special_remarks_from_hod").val();
        var manager_comment = $("#manager_comment").val();
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
                    confirmed: 0
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
        var suggested_reward_input = $("#suggested_reward_input").val();
        var identified_training_needs = $("#identified_training_needs").val();
        var special_remarks_from_hod = $("#special_remarks_from_hod").val();
        var manager_comment = $("#manager_comment").val();
        if (validation_before_confirm()) {

            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "You want to confirm this records!",
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
                            confirmed: 1
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
        var suggested_reward_input = $("#suggested_reward_input").val();
        var identified_training_needs = $("#identified_training_needs").val();
        var special_remarks_from_hod = $("#special_remarks_from_hod").val();
        var manager_comment = $("#manager_comment").val();

        if(manager_comment==""){
            is_valid=false;
            show_error('manager_comment_error','Manager comment field is required.');
        }else{
            hide_error('manager_comment_error');
        }

        if(suggested_reward_input==""){
            is_valid=false;
            show_error('suggested_reward_error','Suggested rewards field is required.');
        }else{
            hide_error('suggested_reward_error');
        }

        if(identified_training_needs==""){
            is_valid=false;
            show_error('identified_training_needs_error','Identifed training needs field is required.');
        }else{
            hide_error('identified_training_needs_error');
        }

        if(special_remarks_from_hod==""){
            is_valid=false;
            show_error('special_remarks_from_hod_error','Special remarks from HOD field is required.');
        }else{
            hide_error('special_remarks_from_hod_error');
        }

        var rating_input = extract_values_from_rating_input();
        var rating_validation = validate_rating_input(rating_input);

        if(rating_validation==false){
            is_valid=false;
            show_error('rating_error','All items are required in the rating satisfaction.');
            myAlert('e','All items are required in the rating satisfaction.');
        }else{
            hide_error('rating_error');
        }

        return is_valid;
    }

    function validate_rating_input(rating_input) {
        var is_valid = true;
        rating_input.forEach(function (item, index) {
            var is_valid2 = false;
            item.forEach(function (item2,index2) {
               if(item2.checked==true){
                   is_valid2 = true;
               }
            });
            if(is_valid2==false){
                is_valid=false;
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
        $("#btn_save_manager_comments").show();
        $("#btn_save_manager_comments_as_draft").show();
    }

    function hide_manager_comment_boxes() {
        $(".manager_comment_label").hide();
        $(".manager_comment_text").hide();
        $("#btn_save_manager_comments").hide();
        $("#btn_save_manager_comments_as_draft").hide();
    }

    function clear_comment_boxes(){
        $(".manager_comment_text").val("");
    }


    function employee_dropdown_onchange() {
        $("#softskills_template").html('');
        $("#softskills_template_emp_self").html('');
        $(".total_div").hide();
        $(".last_update_mgr_div").hide();
        $(".last_update_emp_div").hide();
          $(".lbl1").hide();
        clear_comment_boxes();
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
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/fetch_employee_skills_performance_appraisal'); ?>",
            data: {config_goal_id: app.config_goal_id, emp_id: emp_id},
            success: function (data) {
                $(".last_update_mgr_div").show();
                $("#last_update_mgr").text(data.last_update_time);

                var template_body = '<table class="table table-striped table-bordered"><thead><tr>';
                template_body += '<th>Performance Area</th>';
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
                $("#table_body_read_only").html(table_body);
                $("#total").text(total);
                $(".total_div").show();
                $("#suggested_reward_input").val(data.suggested_reward);
                $("#identified_training_needs").val(data.identified_training_needs);
                $("#special_remarks_from_hod").val(data.special_remarks_from_hod);
                $("#manager_comment").val(data.manager_comment);
                $("#employee_comment").val(data.special_remarks_from_emp);
                app.template_mapping_id = data.template_mapping_id;
                show_manager_comments_boxes();
                comment_box_enable_disable(data.is_approved);
                disable_radio_button(data.is_approved);

                let is_goal_closed = data.is_goal_closed;
                if(is_goal_closed=="1"){
                    $("input").prop('disabled',true);
                    $("textarea").prop('disabled',true);
                    $("#btn_save_manager_comments_as_draft").prop('disabled',true);
                    $("#btn_save_manager_comments").prop('disabled',true);
                    $("#closed_label_div").show();
                }else{
                    $("input").prop('disabled',false);
                    $("textarea").prop('disabled',false);
                    $("#btn_save_manager_comments_as_draft").prop('disabled',false);
                    $("#btn_save_manager_comments").prop('disabled',false);
                    $("#closed_label_div").hide();
                }
                $("#employee_comment").prop('disabled',true);//always desabled for this window.
            }
        });

        //Employee Self Evaluation
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/fetch_employee_skills_performance_appraisal_self_eval'); ?>",
            data: {config_goal_id: app.config_goal_id, emp_id: emp_id},
            success: function (data) {
                $(".last_update_emp_div").show();
                $("#last_update_emp").text(data.last_update_time);
                var template_body = '<table class="table table-striped table-bordered"><thead><tr>';
                template_body += '<th>Performance Area</th>';
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
                        table_body += '<td><label class="customcheck" style="cursor: not-allowed;"><input ' + is_checked + ' type="radio" name="' + radio_group_name + '" data-performance_id="' + performance_area_id + '" data-grade_id="' + item.id + '" data-emp_id="' + emp_id + '" /><span class="checkmark"></span></label></td>';
                    });
                    table_body += '</tr>';
                });
                $("#total_emp").text(total);
                $("#template_tablebody_self_eval").html(table_body);
                $("#softskills_template_emp_self input[type=radio]").attr("disabled", true);

            }
        });
    }

    function comment_box_enable_disable(status) {
        if (status == 1) {
            $("#suggested_reward_input").attr("disabled", true);
            $("#identified_training_needs").attr("disabled", true);
            $("#special_remarks_from_hod").attr("disabled", true);
            $("#manager_comment").attr("disabled", true);
            $("#btn_save_manager_comments").attr("disabled", true);
            $("#btn_save_manager_comments_as_draft").attr("disabled", true);
        } else {
            $("#suggested_reward_input").attr("disabled", false);
            $("#identified_training_needs").attr("disabled", false);
            $("#special_remarks_from_hod").attr("disabled", false);
            $("#manager_comment").attr("disabled", false);
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
            data: {performance_id: performance_id, emp_id: emp_id, goal_id: goal_id, grade_id: grade_id},
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

    function load_department_employees_dropdown(department_id) {
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_employees_for_performance_apr'); ?>",
            data: {},
            success: function (data) {
                var options = "";
                data.forEach(function (item, index) {
                    options += '<option value="' + item.EmpID + '">' + item.Ename1 + ' - '+item.ECode+'</option>';
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