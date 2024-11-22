<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_activity_my_emp_apr_title');

$rating_data = getAppraisalRatingData();

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

    table, td, th {
        border: 1px solid darkgrey !important;
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
                                        <select class="form-control" id="corporate_goals_dropdown">
                                        </select>
                                        <div id="corporate_goals_dropdown_error" class="error-message"></div>
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
                                            <span id="closed_label" class="label label-text-size"
                                                  style="background-color: red; margin-right: 5px;">Closed</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-12">

                                        <!-- header details -->
                                        <div class="col-md-12 mpo_based" style="width:100%;padding-left:0px;padding-right:0px;margin-top:50px;">
                                            <div class="col-md-6">
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-4" for="">Employee No/ Name</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-7"><span id="empName"></span></div>
                                                </div>
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-4" for="">Emp-Position</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-7"><span id="empDesignation"></span></div>
                                                </div>
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-4" for="">Company Name</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-7"><span id="companyName"></span></div>
                                                </div>
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-4" for="">Reporting To</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-7"><span id="managerName"></span></div>
                                                </div>
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-4" for="">Manager Position</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-7"><span id="managerdesignation"></span></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-3" for="">Location</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-8"><span id="empLocation"></span></div>
                                                </div>
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-3" for="">DOJ</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-8"><span id="empDOJ"></span></div>
                                                </div>
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-3" for="">Assessment Period</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-8"><span></span></div>
                                                </div>
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-3" for="">JD Number</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-8"><span id=""></span></div>
                                                </div>
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-3" for="">Date</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-8"><span><?php echo current_date(); ?></span></div>
                                                </div>
                                                <div class="row" style="width:100%;border: 1px solid rgba(176,171,171,0.75);">
                                                    <label class="col-sm-3" for="">Assessment Type</label>
                                                    <label class="col-sm-1" for="">:</label>
                                                    <div class="col-sm-8"><span></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 mpo_based" style="width:100%;padding-left:0px;">
                                            <div class="row" style="width:100%;text-align:center;">
                                                <h4><b>JOB Description/ Assessment</b></h4>
                                            </div>
                                        </div>
                                        <br>
                                        <!-- purpose -->
                                        <div class="col-sm-12 mpo_based" style="width:100%;padding-left:0px;margin-top:20px;">
                                            <div class="box box-default box-solid" style="width:100%;padding-left:0px;padding-bottom:0px;padding-top:0px;">
                                                <h4><b><u>Job Purpose/ Main Functions/ Targets</u></b></h4>
                                                <textarea id="job_purpose" class="manager_comment_text" style="width:100%;" onchange="save_jobPurpose(this)"></textarea>
                                            </div>
                                        </div>
                                        <h4 class="col-sm-12 mpo_based" style="width:100%;padding-left:0px;"><u><b>B. LIST OF MEASURED DUTIES TO BE PERFORMED (MAJOR TASK)</b></u></h4>

                                        <!-- marked by you -->
                                        <div style="margin-top: 15px;">
                                            <span class="lbl1" style="font-weight: 600;display:none;">Marked By You</span>
                                        </div>

                                        <div id="softskills_template">

                                        </div>
                                    </div>
                                    <div id="rating_error" class="error-message"></div>
                                </div>
                                <div class="col-md-12 grade_base">
                                    <div class="col-md-2 total_div" style="margin-top: 7px;">
                                        <span class="label label-text-size"
                                              style="background-color: #02cf32;margin-right: 5px;"> <?php echo $this->lang->line('appraisal_total_marks'); ?>: </span>
                                        <span
                                                id="total"></span>
                                    </div>
                                    <div class="col-md-4 last_update_mgr_div" style="margin-top: 7px;">Last updated:
                                        <span id="last_update_mgr"></span></div>
                                </div>
                                <div class="col-md-12 mpo_hide" style="<?php
                                $isHideMarkedByEmpShow = hide_marks_marked_by_employee();
                                if($isHideMarkedByEmpShow){
                                    echo 'display:none';
                                }
                                ?>">
                                    <div class="col-md-12 grade_base">
                                        <div style="margin-top: 15px;"><span class="lbl1"
                                                                             style="font-weight: 600;display:none;">Marked By Employee</span>
                                        </div>

                                        <div id="softskills_template_emp_self">

                                        </div>
                                    </div>
                                    <div id="rating_error" class="error-message"></div>
                                </div>
                                <div class="col-md-12 grade_base" style="<?php
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
                                    <div class="col-md-4 last_update_emp_div" style="margin-top: 7px;">Last updated:
                                        <span id="last_update_emp"></span></div>
                                </div>

                                    <div class="col-md-12 mpo_based">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="manager_comment_label">
                                                    &nbsp;Begin with the end in mind
                                                </label>
                                                <textarea disabled="true" id="begin_with_the_end_in_mind"
                                                        class="manager_comment_text"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mpo_based">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="manager_comment_label">
                                                    &nbsp;Miscellaneous worth mentioning
                                                </label>
                                                <textarea disabled="true" id="miscellaneous_worth_mentioning"
                                                        class="manager_comment_text"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mpo_based">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="manager_comment_label">
                                                    &nbsp;Bench mark objective assessment
                                                </label>
                                                <textarea disabled="true" id="benchmark_objective_assessment"
                                                        class="manager_comment_text"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- this will hide in grade-base and show in mpo-base-->
                                    <div class="col-md-12 mpo_based_emp_comment">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="manager_comment_label">
                                                &nbsp;Employee comment
                                            </label>
                                            <textarea disabled="true" id="employee_comment_mpo"
                                                      class="manager_comment_text"></textarea>
                                        </div>
                                    </div>
                                </div>
                                    
                                    <div class="col-md-12 mpo_based">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title">Career and training action plan</label>
                                                </div>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="career_and_training_action_plan"
                                                                name="technicalDetail"
                                                                rows="2"></textarea>
                                                </div>
                                            </div>
                                            <hr>
                                    </div>
                                    
                                    <div class="col-md-12 mpo_based">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title">Manager assessment undertaking</label>
                                                </div>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="manager_assessment_undertaking"
                                                                name="technicalDetail"
                                                                rows="2"></textarea>
                                                </div>
                                            </div>
                                            <hr>
                                    </div>

                                <div class="col-md-12 common_based" style="margin-top:30px;">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="manager_comment_label">
                                                Rating
                                            </label>
                                            <select id="manager_rating_dropdown" class="form-control" onchange="">
                                                <?php
                                                echo '<option value="0" >Select Rating Value</option>';
                                                foreach ($rating_data as $ratingItem){

                                                    echo '<option value="'.$ratingItem['appraisalRatingID'].'">'.$ratingItem['ratedValue'].' - '.$ratingItem['rating'].' ('.$ratingItem['description'].')</option>';
                                                }
                                                ?>
                                            </select>
                                            <div id="manager_rating_error" class="error-message"></div>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="col-md-12 common_based">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title">Manager comment and assessment</label>
                                                </div>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="manager_comment"
                                                                name="technicalDetail"
                                                                rows="2"></textarea>
                                                </div>
                                            </div>
                                            <hr>
                                    </div>
                               
                                    <div class="col-md-12 common_based">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title">Suggested reward</label>
                                                </div>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="suggested_reward_input"
                                                                name="technicalDetail"
                                                                rows="2"></textarea>
                                                </div>
                                            </div>
                                            <hr>
                                    </div>
                                
                                <div class="col-md-12 common_based">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title">Identified training needs</label>
                                                </div>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="identified_training_needs"
                                                                name="technicalDetail"
                                                                rows="2"></textarea>
                                                </div>
                                            </div>
                                            <hr>
                                    </div>
                                <!-- <div class="col-md-12">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="manager_comment_label">
                                                &nbsp;Special remarks from HOD
                                            </label>
                                            <textarea id="special_remarks_from_hod"
                                                      class="manager_comment_text"></textarea>
                                            <div id="special_remarks_from_hod_error" class="error-message"></div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="col-md-12 common_based">
                                            <div class="row">
                                                <div class="form-group col-sm-2 md-offset-2">
                                                    <label class="title">Special remarks from HOD</label>
                                                </div>
                                                <div class="form-group col-sm-10">
                                                        <textarea class="form-control richtext" id="special_remarks_from_hod"
                                                                name="technicalDetail"
                                                                rows="2"></textarea>
                                                </div>
                                            </div>
                                            <hr>
                                    </div>

                                <!-- this will show in grade-base and hide in mpo-base-->
                                <div class="col-md-12 grade_based_emp_comment">
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
                                        <button id="btn_save_manager_comments"
                                                class="btn btn-success form-control pull-right"
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
    <script src="<?php echo base_url('plugins/tinymce/tinymce.min.js'); ?>"></script>
    <script>
        app = {};
        $(document).ready(function () {

            var marking_type = 0;
            var template_id = 0;
            var manager_id = <?php echo current_userID(); ?>;
            // $("#appraisal_name").text(localStorage.getItem('appraisal_name'));

            load_corporate_goal_dropdown();
            load_department_employees_dropdown();
            $(".total_div").hide();
            $(".last_update_mgr_div").hide();
            $(".last_update_emp_div").hide();
            hide_manager_comment_boxes();

            $(".mpo_based").hide();
            $(".common_based").hide();

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
                    $("#corporate_goals_dropdown").html(options);
                    $("#corporate_goals_dropdown").select2({
                        placeholder: 'Select an option',
                        tags: true
                    });
                }
            });
        }


        function btn_save_manager_comments_as_draft() {
            // var suggested_reward_input = $("#suggested_reward_input").val();
            // var identified_training_needs = $("#identified_training_needs").val();
            // var special_remarks_from_hod = $("#special_remarks_from_hod").val();
            // var manager_comment = $("#manager_comment").val();
            var rating = $("#manager_rating_dropdown").val();
            if(marking_type == 2){
                var career_and_training_action_plan = tinyMCE.get('career_and_training_action_plan').getContent();
                var manager_assessment_undertaking = tinyMCE.get('manager_assessment_undertaking').getContent();
            }else{
                var career_and_training_action_plan = null;
                var manager_assessment_undertaking = null;
            }
            var manager_comment = tinyMCE.get('manager_comment').getContent();
            var suggested_reward_input = tinyMCE.get('suggested_reward_input').getContent();
            var identified_training_needs = tinyMCE.get('identified_training_needs').getContent();
            var special_remarks_from_hod = tinyMCE.get('special_remarks_from_hod').getContent();

            // if (validation_before_confirm()) {  ......................................uncomment after development
                $.ajax({
                    async: false,
                    dataType: "json",
                    type: "POST",
                    url: "<?php echo site_url('Appraisal/update_manager_comments'); ?>",
                    data: {
                        career_and_training_action_plan: career_and_training_action_plan,
                        manager_assessment_undertaking: manager_assessment_undertaking,
                        
                        suggested_reward_input: suggested_reward_input,
                        identified_training_needs: identified_training_needs,
                        special_remarks_from_hod: special_remarks_from_hod,
                        template_mapping_id: app.template_mapping_id,
                        manager_comment: manager_comment,
                        confirmed: 0,
                        rating:rating,
                        //marking_type: marking_type
                    },
                    success: function (data) {
                        myAlert('i', 'Successfully saved.');
                        tinyMCE.get('career_and_training_action_plan').setContent('');
                        tinyMCE.get('manager_assessment_undertaking').setContent('');
                        tinyMCE.get('manager_comment').setContent('');
                        tinyMCE.get('suggested_reward_input').setContent('');
                        tinyMCE.get('identified_training_needs').setContent('');
                        tinyMCE.get('special_remarks_from_hod').setContent('');
                        comment_box_enable_disable(1);
                        disable_radio_button(1);
                    }
                });
            // }
        }


        function btn_save_manager_comments() {
            // var suggested_reward_input = $("#suggested_reward_input").val();
            // var identified_training_needs = $("#identified_training_needs").val();
            // var special_remarks_from_hod = $("#special_remarks_from_hod").val();
            // var manager_comment = $("#manager_comment").val();
            // var rating = $("#manager_rating_dropdown").val();

            var rating = $("#manager_rating_dropdown").val();
            if(marking_type == 2){
                var career_and_training_action_plan = tinyMCE.get('career_and_training_action_plan').getContent();
                var manager_assessment_undertaking = tinyMCE.get('manager_assessment_undertaking').getContent();
            }else{
                var career_and_training_action_plan = null;
                var manager_assessment_undertaking = null;
            }
            var manager_comment = tinyMCE.get('manager_comment').getContent();
            var suggested_reward_input = tinyMCE.get('suggested_reward_input').getContent();
            var identified_training_needs = tinyMCE.get('identified_training_needs').getContent();
            var special_remarks_from_hod = tinyMCE.get('special_remarks_from_hod').getContent();

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
                                career_and_training_action_plan: career_and_training_action_plan,
                                manager_assessment_undertaking: manager_assessment_undertaking,

                                suggested_reward_input: suggested_reward_input,
                                identified_training_needs: identified_training_needs,
                                special_remarks_from_hod: special_remarks_from_hod,
                                template_mapping_id: app.template_mapping_id,
                                manager_comment: manager_comment,
                                confirmed: 1,
                                rating:rating
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
            $("#btn_save_manager_comments").show();
            $("#btn_save_manager_comments_as_draft").show();
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
            var manager_id = manager_id;
            var config_goal_id = $('#corporate_goals_dropdown').val();
            app.config_goal_id = config_goal_id;
            $.ajax({
                async: false,
                dataType: "json",
                type: "POST",
                url: "<?php echo site_url('Appraisal/fetch_employee_skills_performance_appraisal'); ?>",
                data: {config_goal_id: config_goal_id, emp_id: emp_id , manager_id: manager_id},
                success: function (data) {
                    $(".last_update_mgr_div").show();
                    $("#last_update_mgr").text(data.last_update_time);

                    marking_type = data.markingType;
                    template_id = data.template_id; 

                /** ...............................................................MPO.......*/
                if(data.markingType == 2){
  
                    show_manager_comments_boxes();

                    /**set mpo base header header details */
                    $('#empName').text(data.employee_details['empName']);
                    $('#empDesignation').text(data.employee_details['empDesignation']);
                    $('#companyName').text(data.employee_details['companyName']);
                    $('#managerName').text(data.employee_details['managerName']);
                    $('#empLocation').text(data.employee_details['empLocation']);
                    $('#empDOJ').text(data.employee_details['empDOJ']);
                    $('#managerdesignation').text(data.manager['managerdesignation']);

                    $('#job_purpose').text(data.job_purpose);

                    var tbl = "";
                    var grand_total_measure = 0;
                    var grand_total_employee = 0;
                    var grand_total_manager = 0;
                    data.performance_areas.forEach(function (hd_item, index) {

                        var performance_area = hd_item.description;
                        var order = hd_item.order;
                        // Check if hd_item.sub is defined and is an array
                        if (Array.isArray(hd_item.sub)) {
                            tbl += ' <div class="table-responsive">';
                                tbl +=' <table id="soft_skill_MPO_table_mngr" class="<?php echo table_class(); ?>">';
                                        tbl +=' <thead>';
                                            tbl +=' <tr>';
                                                tbl +=' <th style="min-width: 5%"><b>'+order+'</b></th>';
                                                tbl +=' <th class="text-left" style="min-width: 50%"><u><b>'+performance_area+'</b></u></th>';
                                                tbl +=' <th style="min-width: 15%"><b>Measured Points</b></th>';
                                                tbl +=' <th style="min-width: 15%"><b>Employee Points</b></th>';
                                                tbl +=' <th style="min-width: 15%"><b>Manager Pointss</b></th>';
                                            tbl +=' </tr>';
                                        tbl +=' </thead>';
                                        tbl +=' <tbody>';

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

                                            tbl +=' <tr>';
                                                tbl +=' <td class="text-center">&nbsp;</td>';
                                                tbl +=' <td>* '+sub_item.description+'</td>';
                                                tbl +=' <td><input type="text" name="measuredPoint" style="width:100%" id="measuredPoint_'+ sub_item.performance_area_id +'" placeholder="0" value="'+ mpoint +'" class="measuredPoint1 number" onkeyup="calculate_total_measurePoints(this, '+ sub_item.parent_id +')" ';
                                                tbl +=' onchange="save_measurepoint('+ sub_item.performance_area_id +', this.value)" readonly></td> ';

                                                tbl +=' <td><input type="text" name="empmeasuredPoint" style="width:100%" id="empmeasuredPoint_'+ sub_item.performance_area_id +'" placeholder="0" value="'+ emp_point +'" class="empmeasuredPoint1 number" onkeyup="calculate_total_emp_measurePoints(this, '+ sub_item.parent_id +')" ';
                                                tbl +=' onchange="save_emp_point('+ sub_item.performance_area_id +', this.value)" readonly></td> ';
                                                tbl +=' <td><input type="text" name="mngrmeasuredPoint" style="width:100%" id="mngrmeasuredPoint_'+ sub_item.performance_area_id +'" placeholder="0" value="'+ mngr_point +'" class="mngrmeasuredPoint1 number" onkeyup="calculate_total_mngr_measurePoints(this, '+ sub_item.parent_id +')" ';
                                                tbl +=' onchange="save_mngr_point('+ sub_item.performance_area_id +', this.value, '+ emp_id +')" ></td> ';
                                            tbl +=' </tr>';

                                            total_measure += mpoint;
                                            total_employee += emp_point;
                                            total_manager += mngr_point;
                                        });

                                            tbl +=' <tr style="background-color:rgb(221,210,0,0.6);">';
                                                tbl +=' <td class="text-center">-</td>';
                                                tbl +=' <td class="text-center">Total</td>';
                                                tbl +=' <td><input type="text" value="'+ total_measure +'" style="width:100%;background-color:rgb(221,210,0,0.6);" id="total1_'+ hd_item.performance_area_id +'" placeholder="0" class="number" readonly></td>';
                                                tbl +=' <td><input type="text" value="'+ total_employee +'" style="width:100%;background-color:rgb(221,210,0,0.6);" id="emptotal1_'+ hd_item.performance_area_id +'" placeholder="0" class="number" readonly></td>';
                                                tbl +=' <td><input type="text" value="'+ total_manager +'" style="width:100%;background-color:rgb(221,210,0,0.6);" id="mngrtotal1_'+ hd_item.performance_area_id +'" placeholder="0" class="number" readonly></td>';
                                            tbl +=' </tr>';

                                        tbl +=' </tbody>';
                                tbl +=' </table>';
                            tbl +=' </div>';

                            grand_total_measure += total_measure;
                            grand_total_employee += total_employee;
                            grand_total_manager += total_manager;
                        }
                    });
                    //grand total & percentages table
                    var tottbl = "";
                                            var percentage_measure = 100 + '%';
                                            var percentage_employee_a = ( (100/grand_total_measure) * grand_total_employee ).toFixed(2)+ '%';
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

                    tbl += tottbl;
        
                    $("#softskills_template").html('');
                    $("#softskills_template").html(tbl);

                    $(".total_div").hide();

                    data.begin_with_the_end_in_mind ? $('#begin_with_the_end_in_mind').text(data.begin_with_the_end_in_mind) : $('#begin_with_the_end_in_mind').text('');
                    data.miscellaneous_worth_mentioning ? $('#miscellaneous_worth_mentioning').text(data.miscellaneous_worth_mentioning) : $('#miscellaneous_worth_mentioning').text('');
                    data.benchmark_objective_assessment ? $('#benchmark_objective_assessment').text(data.benchmark_objective_assessment) : $('#benchmark_objective_assessment').text('');

                    /**....................................................................set tinyMCE areas */
                       function stripHtmlTags(input) {
                            var tempDiv = document.createElement("div");
                            tempDiv.innerHTML = input;
                            return tempDiv.textContent || tempDiv.innerText || "";
                        }
                        
                        var career_and_training_action_plan = data.career_and_training_action_plan.replace(/<\/?[^>]+(>|$)/g, ""); // This removes all HTML tags
                        career_and_training_action_plan = stripHtmlTags(career_and_training_action_plan).trim(); // This removes leading and trailing whitespace

                        var manager_assessment_undertaking = data.manager_assessment_undertaking.replace(/<\/?[^>]+(>|$)/g, ""); 
                        manager_assessment_undertaking = stripHtmlTags(manager_assessment_undertaking).trim();

                        var mngrComment = data.manager_comment.replace(/<\/?[^>]+(>|$)/g, ""); 
                        mngrComment = stripHtmlTags(mngrComment).trim(); 

                        var suggestedReward = data.suggested_reward.replace(/<\/?[^>]+(>|$)/g, "");
                        suggestedReward = stripHtmlTags(suggestedReward).trim(); 

                        var identifiedTrainingNeeds = data.identified_training_needs.replace(/<\/?[^>]+(>|$)/g, "");
                        identifiedTrainingNeeds = stripHtmlTags(identifiedTrainingNeeds).trim(); 

                        var specialRemarkFromHod = data.special_remarks_from_hod.replace(/<\/?[^>]+(>|$)/g, ""); 
                        specialRemarkFromHod = stripHtmlTags(specialRemarkFromHod).trim(); 

                        setTimeout(function () {
                            tinyMCE.get('career_and_training_action_plan').setContent(career_and_training_action_plan);
                            tinyMCE.get('manager_assessment_undertaking').setContent(manager_assessment_undertaking);
                            tinyMCE.get('manager_comment').setContent(mngrComment);
                            tinyMCE.get('suggested_reward_input').setContent(suggestedReward);
                            tinyMCE.get('identified_training_needs').setContent(identifiedTrainingNeeds);
                            tinyMCE.get('special_remarks_from_hod').setContent(specialRemarkFromHod);
                        }, 1000);
                    /**....................................................................end tinyMCE areas */

                    $("#employee_comment_mpo").text(data.special_remarks_from_emp);
                    $("#manager_rating_dropdown").val(data.ratingID);
                    app.template_mapping_id = data.template_mapping_id;
                    show_manager_comments_boxes();
                    comment_box_enable_disable(data.is_approved);
                    disable_radio_button(data.is_approved);

                    let is_goal_closed = data.is_goal_closed;
                    if (is_goal_closed == "1" || data.is_approved == "1") {
                        $("input").prop('disabled', true);
                        $("textarea").prop('disabled', true);
                        $("#btn_save_manager_comments_as_draft").prop('disabled', true);
                        $("#btn_save_manager_comments").prop('disabled', true);
                        $("#closed_label_div").show();
                        $("#manager_rating_dropdown").prop('disabled', true);
                    } else {
                        $("input").prop('disabled', false);
                        $("textarea").prop('disabled', false);
                        $("#btn_save_manager_comments_as_draft").prop('disabled', false);
                        $("#btn_save_manager_comments").prop('disabled', false);
                        $("#closed_label_div").hide();
                        $("#manager_rating_dropdown").prop('disabled', false);
                    }

                    $(".grade_base").hide();
                    $(".mpo_based").show();
                    $(".common_based").show();
                    $(".mpo_based_emp_comment").show();
                    $(".grade_based_emp_comment").hide();
                    $("#employee_comment_mpo").prop('disabled', true);//always desabled for this window.
                    $("#begin_with_the_end_in_mind").prop('disabled', true);
                    $("#miscellaneous_worth_mentioning").prop('disabled', true);
                    $("#bench_mark_objective_assessment").prop('disabled', true);

                }
                /**.......................................................................................Grade.... */
                else{

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
                            let rowspan = item.sub.length+1;
                            table_body += '<tr>' +
                                '<td rowspan="'+rowspan+'">' + item.description + '</td>';

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

                        }else{
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
                    $("#suggested_reward_input").text(data.suggested_reward);
                    $("#identified_training_needs").text(data.identified_training_needs);
                    $("#special_remarks_from_hod").text(data.special_remarks_from_hod);
                    $("#manager_comment").text(data.manager_comment);
                    $("#employee_comment").text(data.special_remarks_from_emp);
                    $("#manager_rating_dropdown").val(data.ratingID);
                    app.template_mapping_id = data.template_mapping_id;
                    show_manager_comments_boxes();
                    comment_box_enable_disable(data.is_approved);
                    disable_radio_button(data.is_approved);

                    let is_goal_closed = data.is_goal_closed;
                    if (is_goal_closed == "1" || data.is_approved == "1") {
                        $("input").prop('disabled', true);
                        $("textarea").prop('disabled', true);
                        $("#btn_save_manager_comments_as_draft").prop('disabled', true);
                        $("#btn_save_manager_comments").prop('disabled', true);
                        $("#closed_label_div").show();
                        $("#manager_rating_dropdown").prop('disabled', true);
                    } else {
                        $("input").prop('disabled', false);
                        $("textarea").prop('disabled', false);
                        $("#btn_save_manager_comments_as_draft").prop('disabled', false);
                        $("#btn_save_manager_comments").prop('disabled', false);
                        $("#closed_label_div").hide();
                        $("#manager_rating_dropdown").prop('disabled', false);
                    }

                    $("#employee_comment_grade").prop('disabled', true);//always desabled for this window.

                    $(".common_based").show();
                    $(".mpo_based").hide();
                    $(".grade_base").show();
                    $(".mpo_based_emp_comment").hide();
                    $(".grade_based_emp_comment").show();

                }

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
                    if(data.markingType != 2){      //......................................................................grade base

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

                        template_body += '</tr></thead>' +
                            '<tbody id="template_tablebody_self_eval"></tbody></table>';

                        $("#softskills_template_emp_self").html(template_body);

                        var table_body = "";
                        var total = 0;
                        data.performance_areas.forEach(function (item, index) {
                            if (item.sub != null) {
                                let rowspan = item.sub.length+1;
                                table_body += '<tr>' +
                                    '<td rowspan="'+rowspan+'">' + item.description + '</td>';

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
                            }else{
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
                        
                        $("#template_tablebody_self_eval").html(table_body);

                    

                        $("#total_emp").text(total);
                        $("#softskills_template_emp_self input[type=radio]").attr("disabled", true);
                    }
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

        function calculate_total_mngr_measurePoints(element, nmbr) {
            var total = 0;
            var measuredPoint = 0;
            var manager_points = 0;

            $(element).closest('tr').find('input[name="measuredPoint"]').each(function() {
                measuredPoint = parseFloat($(this).val());
            });

            $(element).closest('table').find('input[name="mngrmeasuredPoint"]').each(function() {
                manager_points = parseFloat($(this).val());
                if(manager_points > measuredPoint){
                    myAlert('w', 'cannot be greater that measured point');
                    $(this).val(0);
                }else{
                    total += parseFloat($(this).val()) || 0;
                }
            });

            $('#mngrtotal1_' + nmbr).val(total);
        }


        function save_mngr_point(id, value) {
            //var emp_id = empid;
            var emp_id = $("#employee_list_dropdown").val();
            var goal_id = app.config_goal_id;
            $.ajax({
                type: 'POST',
                dataType: "json",
                url: "<?php echo site_url('Appraisal/save_manager_measurepoint'); ?>",
                data: { 'goal_id': goal_id, 'id': id, 'value': value, 'emp_id' : emp_id},
                success: function(data) {
                    myAlert(data[0], data[1]);
                },
                error: function(error) {
                    console.error('Error saving data', error);
                }
            });
        }

        
        function save_jobPurpose(inputElement){
            var job_purpose = inputElement.value;
            $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {'template_id': template_id, 'job_purpose': job_purpose},
                    url: "<?php echo site_url('Appraisal/update_job_purpose'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        stopLoad();
                        myAlert(data[0],data[1]);
                        //load_soft_skills_template_details(template_id);
                    },
                    error: function () {
                        myAlert('e', 'An Error Occurred! Please Try Again.');
                        stopLoad();
                        refreshNotifications(true);
                    }
            });
        }
    </script>