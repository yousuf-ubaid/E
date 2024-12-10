<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_approval_setup_title');

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
</style>
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

            <div class="box-body">
                <style>
                    .empDiv {
                        display: none;
                    }

                    legend {
                        font-size: 16px !important;
                    }
                </style>
                <link rel="stylesheet" type="text/css" href="/plugins/bootstrap/css/tabs.css">
                <div class="row">
                    <div class="col-md-12" style="margin-top: 10px">
                        <div class="form-group col-md-4">
                            <label for=""> <?php echo $this->lang->line('common_department'); ?></label>
                            <select id="departments" class="form-control">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <fieldset class="scheduler-border" style="margin-top: 10px">
                                <legend class="scheduler-border"> <?php echo $this->lang->line('appraisal_leave_approval_levels'); ?><!--Leave Approval Levels--></legend>
                                <div style="margin-top: 10px">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="level-txt" class="col-md-2"> <?php echo $this->lang->line('appraisal_levels'); ?></label>
                                            <input type="number" name="level-txt" id="level-txt" class="col-md-4 number"
                                                   value="">
                                            <button type="button" class="btn btn-primary btn-sm pull-right"
                                                    style="margin-bottom: 10px" onclick="save_department_approval_levels()"> <?php echo $this->lang->line('common_save'); ?>
                                                <!--Save-->
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <fieldset class="scheduler-border" style="margin-top: 10px">
                                <legend class="scheduler-border"> <?php echo $this->lang->line('appraisal_leave_approval_levels'); ?>Leave Approval Levels</legend>
                                <div style="margin-top: 10px">
                                    safs
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo footer_page('Right foot', 'Left foot', false); ?>
    <script type="text/javascript">
        app = {};
        app.company_id =<?php echo current_companyID(); ?>;
        $(document).ready(function () {
            load_departments();
        });

        function save_department_approval_levels(){
            var number_of_levels=$("#level-txt").val();
            var department_id=31;
            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {company_id: app.company_id,department_id:department_id,number_of_levels:number_of_levels},
                url: '<?php echo site_url('appraisal/save_department_approval_levels') ?>',
                success: function (data) {

                },
                error: function () {

                }
            })
        }

        function load_departments() {

            $.ajax({
                async: false,
                type: 'post',
                dataType: 'json',
                data: {company_id: app.company_id},
                url: '<?php echo site_url('appraisal/get_departments') ?>',
                success: function (data) {
                    bind_department_dropdown(data);
                },
                error: function () {

                }
            })
        }

        function bind_department_dropdown(data) {
            var department_list = '';
            data.forEach(function (item, index) {
                department_list += '<option>' + item.department_description + '</option>';
            });
            $('#departments').html(department_list);
        }

        function show_error(errorDivId, errorMessage) {
            var divSelector = "#" + errorDivId;
            $(divSelector).html(errorMessage);
        }

        function hide_error(errorDivId) {
            var divSelector = "#" + errorDivId;
            $(divSelector).html("");
        }

        function format_for_two_digits(num) {
            if (num < 10) {
                return '0' + num;
            } else {
                return num;
            }
        }
    </script>