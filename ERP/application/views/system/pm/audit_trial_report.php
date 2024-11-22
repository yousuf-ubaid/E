<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('employee_master', $primaryLanguage);
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('hrms_attendance', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

echo head_page('Project Management Audit Report', false);
$date_format_policy = date_format_policy();
$emp_id = current_userID();
$current_date = current_format_date();
$monthFirst = convert_date_format(date('Y-m-01'));
$companyname = fetch_company();
$csrf = array(
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
);

$project = load_all_project();
?>

    <style>
        fieldset {
            border: 1px solid silver;
            border-radius: 0px;
            padding: 1%;
            padding-bottom: 15px;
        }

        legend {
            width: auto;
            border-bottom: none;
            margin: 0px 10px;
            font-weight: bold !important;
            font-size: 14px;
            color: #6a6c6f;
        }
    </style>

    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><?php echo $this->lang->line('common_filters'); ?><!--Filter--></legend>
        <?php echo form_open('', ' class="form-horizontal" id="filter_form" role="form"'); ?>
        <div class="row col-md-12">
            <div class="col-sm-2">
                <span style="font-weight: bold;">Project </span>
                <br>
                <?php echo form_dropdown('project_id[]', $project, '', 'multiple class="form-control" id="project_id"'); ?>
            </div>
            <div class="col-sm-2">
                <span style="font-weight: bold;">Start Date </span>
                <br>
                <div class="input-group date_pic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" class="form-control" id="start_date" name="start_date"
                           value="<?php echo $monthFirst; ?>"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required/>
                </div>
            </div>
            <div class="col-sm-2">
                <span style="font-weight: bold;">End Date </span>
                <br>
                <div class="input-group date_pic">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" class="form-control" id="end_date" name="end_date"
                           value="<?php echo $current_date; ?>"
                           data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required/>
                </div>
            </div>
            <div class="col-sm-2">
                <button style="margin-top: 23px" type="button" onclick="get_pm_audit_report()"
                        class="btn btn-primary btn-xs ">Generate
                </button>
            </div>
        </div>
        </div>

        <?php echo form_close(); ?>

    </fieldset>

    <hr style="margin: 0px;">
    <br>

    <div id="div_pmsauditdetail">


    </div>


    <script type="text/javascript">
        var common_an_error = '<?=$this->lang->line('common_an_error_occurred_Please_try_again')?>';
        $(document).ready(function () {
            $('.select2').select2();

            $("#project_id").multiselect2({
                enableCaseInsensitiveFiltering: true,
                numberDisplayed: 1,
                filterPlaceholder: 'Search Cashier',
                includeSelectAllOption: true,
                maxHeight: 400,
                buttonWidth: '200px',
            });
            $("#project_id").multiselect2('selectAll', false);
            $("#project_id").multiselect2('updateButtonText');

            $("#employeenationality_id").multiselect2({
                enableCaseInsensitiveFiltering: true,
                numberDisplayed: 1,
                filterPlaceholder: 'Search Cashier',
                includeSelectAllOption: true,
                maxHeight: 400,
                buttonWidth: '200px',
            });
            $("#employeenationality_id").multiselect2('selectAll', false);
            $("#employeenationality_id").multiselect2('updateButtonText');

            $("#employeenatstatus").multiselect2({
                enableCaseInsensitiveFiltering: true,
                numberDisplayed: 1,
                filterPlaceholder: 'Search Cashier',
                includeSelectAllOption: true,
                maxHeight: 400,
                buttonWidth: '200px',
            });
            $("#employeenatstatus").multiselect2('selectAll', false);
            $("#employeenatstatus").multiselect2('updateButtonText');

            get_pm_audit_report();


        });
        $(document).ready(function () {
            $('.headerclose').click(function () {
                fetchPage('system/pm/audit_trial_report', '', 'PM');
            });
            Inputmask().mask(document.querySelectorAll("input"));
            var date_format_policy = '<?php echo strtoupper($date_format_policy) ?>';
            $('.date_pic').datetimepicker({
                useCurrent: false,
                format: date_format_policy
            }).on('dp.change', function (ev) {

            });
        });

        function get_pm_audit_report() {
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Boq/load_pm_audit_report') ?>",
                data: $("#filter_form").serialize(),
                dataType: "html",
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $("#div_pmsauditdetail").html(data);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<strong> Error </strong><br>Status: ' + textStatus + '<br>Message: ' + errorThrown);
                }
            });
        }
    </script>

<?php
