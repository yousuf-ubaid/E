<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_reports_performance_evaluation_summary_title');


?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css"/>
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

    .emp-li {
        cursor: pointer;
    }
    @media print {
        .tool-tip-icon{
            visibility: hidden;
        }
    }
</style>
<style>
    /* Cosmetic only
     Pagination css*/
    #easyPaginate {
        width: 300px;
    }

    #easyPaginate img {
        display: block;
        margin-bottom: 10px;
    }

    .easyPaginateNav a {
        padding: 5px;
    }

    .easyPaginateNav a.current {
        font-weight: bold;
        text-decoration: underline;
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
        top: 0;
        left: 0;
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

    .pt-25 {
        padding-top: 25px;
    }
</style>
<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body">

                <div class="row">
                    <div class="form-group col-md-12">

                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="col-md-12">
                                <div class="form-group col-md-2">
                                    <label for="">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal'); ?>
                                    </label>
                                    <select class="form-control" id="corporate_goals_dropdown"
                                            onchange="corporate_goals_dropdown_onchange()">
                                    </select>
                                    <div id="corporate_goals_dropdown_error" class="error-message"></div>
                                </div>
                                <div class="col-md-2">
                                    <label for="">
                                        <?php echo $this->lang->line('appraisal_activity_department_appraisal_corporate_goal_department'); ?>
                                    </label>
                                    <select class="form-control" id="departments_dropdown"
                                            onchange="department_dropdown_onchange()">
                                    </select>
                                    <div id="departments_dropdown_error" class="error-message"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="">
                                        <?php echo $this->lang->line('appraisal_activity_department_employee'); ?>
                                    </label>
                                    <select class="form-control selectpicker" data-actions-box="true"
                                            id="employees_dropdown" multiple>
                                    </select>
                                    <div id="employees_dropdown_error" class="error-message"></div>
                                </div>

                                <div class="col-md-4 pt-25">

                                    <button class="btn btn-primary"
                                            onclick="fetch_onclick()"><?php echo $this->lang->line('appraisal_fetch'); ?> <!--Fetch-->
                                    </button>

                                    <button id="print_btn" class="btn btn-primary" disabled="disabled"
                                            onclick="print_report()"> <?php echo $this->lang->line('common_print'); ?></button>
                                    <a id="btn_download_as_pdf"
                                       href="<?php echo site_url('Appraisal/generate_evaluation_report_pdf'); ?>">
                                        <button id="download_btn" class="btn btn-primary" disabled="disabled"><?php echo $this->lang->line('appraisal_download_as_pdf'); ?><!--Download as PDF--></button>
                                    </a>
                                </div>

                            </div>
                            <div class="col-md-12">
                                <div class="col-md-10">
                                    <div id="error_message" style="margin-top: 20px;color: red;"><?php echo $this->lang->line('appraisal_no_data_related_to_employee'); ?>: <span id="no_data_employee_name"></span></div>
                                    <div id="report_view" style="margin-top: 10px;">
                                        <div class="table-responsive" id="print_PE_header" style="display: none;">
                                            <table style="width: 100%">
                                                <tbody>
                                                <tr>
                                                <td style="width:50%;">
                                                        <table>
                                                            <tr>
                                                                <td>
                                                                    <img alt="Logo" style="height: 130px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <table>
                                                            <tr>
                                                                <td style="text-align: center;">
                                                                    <h3><strong><?php echo $this->common_data['company_data']['company_name']; ?>.</strong></h3>
                                                                    <p><?php echo $this->common_data['company_data']['company_address1'].' '.$this->common_data['company_data']['company_address2'].' '.$this->common_data['company_data']['company_city'].' '.$this->common_data['company_data']['company_country']; ?></p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="report_title_for_print" style="display: none;margin-bottom: 40px;"><h4
                                                    style="text-align: center"><?php echo $this->lang->line('appraisal_employee_performance_evaluation_summary'); ?></h4>
                                        </div>
                                        <table class="table table-hover">

                                            <tbody>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_epf_number'); ?>
                                                        :</label></td>
                                                <td>
                                                    <span id="epf_number"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_emp_name'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="name_of_the_employee"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_designation'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="designation"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_department'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="department"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_period_of_review'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="period_of_review"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_name_of_hod'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="name_of_department_head"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_master_employee_wise_performance_today'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="date_today"></span>
                                                </td>
                                            </tr>


                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_softskills_evaluation_result'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="soft_skills_evaluation_result"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_soft_skills_evaluation_final_score'); ?>
                                                        : <i class="fa fa-question-circle tool-tip-icon" style="font-size:16px;color: #3c8dbc" title="(<?php echo $this->lang->line('appraisal_softskills_evaluation_result'); /*Soft Skills Evaluation Result*/ ?>) / 100 x 40"></i></label>
                                                </td>
                                                <td>
                                                    <span id="soft_skills_evaluation_final_score"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_kpis_evaluation_result'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="kpi_evaluation_result"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_kpis_evaluation_final_score'); ?>
                                                        : <i class="fa fa-question-circle tool-tip-icon" style="font-size:16px;color: #3c8dbc" title="(<?php echo $this->lang->line('appraisal_kpis_evaluation_result'); /*KPIs Evaluation Result*/ ?>)/100 x 40"></i></label>
                                                </td>
                                                <td>
                                                    <span id="kpi_evaluation_final_score"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_final_score'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="final_score"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_activity_department_manager_comment'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="manager_comment"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_suggested_rewards'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="appraisal_suggested_rewards"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_identified_training_needs'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="appraisal_identified_training_needs"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_special_remarks_from_employee'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="appraisal_special_remarks_from_employee"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_special_remarks_from_hod'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="appraisal_special_remarks_from_hod"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_signature_of_hod'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id=""></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_signature_of_hr'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id=""></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_signature_of_employee'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id=""></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_special_remarks_from_employee_skill'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="appraisal_special_remarks_from_employee_skill"></span>
                                                </td>
                                            </tr>  

                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('manager_comment_skill'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="manager_comment_skill"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_suggested_rewards_skill'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="appraisal_suggested_rewards_skill"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_identified_training_needs_skill'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="appraisal_identified_training_needs_skill"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="">
                                                        <?php echo $this->lang->line('appraisal_special_remarks_from_hod_skill'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id="appraisal_special_remarks_from_hod_skill"></span>
                                                </td>
                                            </tr>
                                                                                      
                                            <!-- <tr>
                                                <td>
                                                    <label for="to_date">
                                                        <?php echo $this->lang->line('appraisal_signature_of_coo'); ?>
                                                        :</label>
                                                </td>
                                                <td>
                                                    <span id=""></span>
                                                </td>
                                            </tr> -->

                                            </tbody>
                                        </table>
                                    </div>
                                    <ul id="pagination" class="pagination">
                                    </ul>
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
<script src="<?php echo base_url('plugins/printJS/jQuery.print.js') ?>"></script>
<script src="<?php echo base_url('plugins/bootstrap-select/bootstrap-select.min.js') ?>"
        type="text/javascript"></script>
<script src="<?php echo base_url('plugins/jspdf/jspdf.min.js') ?>" type="text/javascript"></script>


<script>
    app = {};
    app.company_id = <?php echo current_companyID(); ?>;
    app.current_user_id = <?php echo current_userID(); ?>;

    app.department_id = '';
    $(document).ready(function () {
        $("#error_message").hide();
        load_corporate_goal_dropdown(app.company_id);
        load_departments_dropdown(app.current_user_id);
        var selected_department_id = $("#departments_dropdown").val();
        load_department_employees_dropdown(selected_department_id);

        $('#employees_dropdown').selectpicker();
        var selectedItem = $('#employees_dropdown').val();
        load_department_employees_pagination(selected_department_id, 1, selectedItem);
        $('.emp-li:first').trigger("click");

    });


    $('#employees_dropdown').change(function () {
        var selectedItem = $('#employees_dropdown').val();
    });

    function print_report() {
        $("#report_title_for_print").show();
        $("#print_PE_header").show();
        $.print("#report_view");
        $("#report_title_for_print").hide();
        $("#print_PE_header").hide();
    }

    function department_dropdown_onchange() {
        var selected_department_id = $("#departments_dropdown").val();
        load_department_employees_dropdown(selected_department_id);
    }

    function load_department_employees_dropdown(department_id) {

        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_department_employees'); ?>",
            data: {department_id: department_id},
            success: function (data) {
                let options = '';
                data.forEach(function (item, index) {
                    options += '<option value="' + item.EmpID + '">' + item.Ename1 + '</option>';
                });
                $("#employees_dropdown").html(options);
                $("#employees_dropdown").selectpicker('refresh');
            }
        });
    }

    function corporate_goals_dropdown_onchange() {
        $('.emp-li:first').trigger("click");
    }

    function emp_page_click() {
        //generating pagination separately.
        var current_page = $(this).text();
        var selected_department_id = $("#departments_dropdown").val();
        var selectedItem = $('#employees_dropdown').val();
        load_department_employees_pagination(selected_department_id, current_page, selectedItem);

        //generating report
        var emp_id = $(this).data('emp_id');
        get_summary_report(emp_id);

        //setting parameters in pdf url
        var goal_id = $("#corporate_goals_dropdown").val();
        var url = '/appraisal/generate_evaluation_report_pdf?department_id=' + selected_department_id + '&goal_id=' + goal_id + '&employee_id=' + emp_id
        url = '<?php echo site_url(); ?>' + url;
        $("#btn_download_as_pdf").attr('href', url);
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
                    placeholder: 'Select an option',
                    tags: true
                });
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

    function get_summary_report(employee_id) {
        //var employee_id = $("#employees_dropdown").val();
        var department_id = $("#departments_dropdown").val();
        var goal_id = $("#corporate_goals_dropdown").val();
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/performance_evaluation_summary'); ?>",
            data: {department_id: department_id, goal_id: goal_id, employee_id: employee_id},
            success: function (data) {
                if (data.status == 'success') {

                    $("#error_message").hide();
                    $("#report_view").show();
                    var d = new Date(data.appraisal_start_date);
                    var month = format_for_two_digits((d.getMonth() + 1));
                    var date = format_for_two_digits(d.getDate());
                    var goal_start_date = d.getFullYear() + '-' + month + '-' + date;

                    var d = new Date(data.appraisal_end_date);
                    var month = format_for_two_digits((d.getMonth() + 1));
                    var date = format_for_two_digits(d.getDate());
                    var goal_end_date = d.getFullYear() + '-' + month + '-' + date;

                    var employee_details = get_employee_details(employee_id);
                    if(data.hod_id!=null){
                        var hod_details = get_employee_details(data.hod_id);
                    }else {
                        var hod_details = {};
                        hod_details.Ename1 = '-';
                    }
                    var epf_number = employee_details.ssoNo;
                    var name_of_the_employee = employee_details.Ename1;
                    var designation = employee_details.DesDescription;
                    var department = $("#departments_dropdown option:selected").text();
                    var period_of_review = goal_start_date + " to " + goal_end_date;
                    var name_of_department_head = hod_details.Ename1;
                    var date_today = "<?php echo date('Y-m-d') ?>";
                    $("#name_of_the_employee").text(name_of_the_employee);
                    $("#department").text(department);
                    $("#date_today").text(date_today);
                    $("#period_of_review").text(period_of_review);
                    $("#designation").text(designation);
                    $("#epf_number").text(epf_number);
                    $("#name_of_department_head").text(name_of_department_head);

                    //remarks
                    $("#manager_comment").text(data.manager_comment);
                    $("#appraisal_suggested_rewards").text(data.suggested_reward);
                    $("#appraisal_identified_training_needs").text(data.identified_training_needs);
                    $("#appraisal_special_remarks_from_hod").text(data.special_remarks_from_hod);
                    $("#appraisal_special_remarks_from_employee").text(data.special_remarks_from_emp);

                    $("#manager_comment_skill").text(data.manager_comment_skill);
                    $("#appraisal_suggested_rewards_skill").text(data.suggested_reward_skill);
                    $("#appraisal_identified_training_needs_skill").text(data.identified_training_needs_skill);
                    $("#appraisal_special_remarks_from_hod_skill").text(data.special_remarks_from_hod_skill);
                    $("#appraisal_special_remarks_from_employee_skill").text(data.special_remarks_from_emp_skill);

                    var soft_skills_evaluation_result = 0;
                    var soft_skills_evaluation_final_score = 0;
                    var kpi_evaluation_result = 0;
                    var kpi_evaluation_final_score = 0;
                    var final_score;

                    if (data.softskills_based_percentage_of_employee != null) {
                        soft_skills_evaluation_result = data.softskills_based_percentage_of_employee;
                        if (soft_skills_evaluation_result != 0) {
                            soft_skills_evaluation_final_score = (soft_skills_evaluation_result * 40) / 100;
                        }
                    }

                    if (data.objective_based_percentage_of_employee != null) {
                        kpi_evaluation_result = data.objective_based_percentage_of_employee;
                        if (kpi_evaluation_result != 0) {
                            kpi_evaluation_final_score = (kpi_evaluation_result * 60) / 100;
                        }
                    }

                    final_score = soft_skills_evaluation_final_score + kpi_evaluation_final_score;

                    $("#soft_skills_evaluation_result").text(soft_skills_evaluation_result);
                    $("#soft_skills_evaluation_final_score").text(soft_skills_evaluation_final_score);
                    $("#kpi_evaluation_result").text(kpi_evaluation_result);
                    $("#kpi_evaluation_final_score").text(kpi_evaluation_final_score);
                    $("#final_score").text(final_score);
                }else{
                    var employee_details = get_employee_details(employee_id);
                    $("#error_message").show();
                    $("#report_view").hide();
                    $("#no_data_employee_name").text(employee_details.Ename1);
                }


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
                    placeholder: 'Select an option',
                    tags: true
                });
            }
        });
    }

    function fetch_onclick(department_id, current_page) {

        var selectedItem = $('#employees_dropdown').val();
        if(selectedItem!=null){
            $( "#print_btn" ).prop( "disabled", false );
            $( "#download_btn" ).prop( "disabled", false );
            var selected_department_id = $("#departments_dropdown").val();
            load_department_employees_pagination(selected_department_id, 1, selectedItem);
            $('.emp-li:first').trigger("click");
        }else {
            myAlert('e','Employee field is required.');
        }

    }

    function load_department_employees_pagination(department_id, current_page, selectedItem) {

        if (selectedItem!=null && selectedItem.length == 0) {

        } else {
            $.ajax({
                async: false,
                type: "POST",
                url: "<?php echo site_url('Appraisal/get_department_employees_pagination'); ?>",
                data: {
                    department_id: department_id,
                    current_page: current_page,
                    list_of_employee_ids: selectedItem
                },
                success: function (data) {
                    var options = "";
                    // data.forEach(function (item, index) {
                    //     options += '<li class="emp-li" data-emp_id="' + item.EmpID + '" onclick="emp_page_click.call(this)"><a>' + (index + 1) + '</a></li>';
                    // });
                    $("#pagination").html(data);
                }
            });
        }

    }
</script>
