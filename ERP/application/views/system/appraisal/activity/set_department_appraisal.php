<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = $this->lang->line('appraisal_master_deparment_appraisal_title');


?>
<style>
    .error-message {
        color: red;
    }

    .objectives-table th {
        text-align: left;
    }
    .act-btn-margin{
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
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="department_appraisal_table" class="<?php echo table_class(); ?>">
                                <thead>
                                <tr>
                                    <th style="min-width: 15%">#</th>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('appraisal_master_deparment_appraisal_docref_column'); ?></th>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('appraisal_master_deparment_appraisal_department_column'); ?></th>

                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('appraisal_master_deparment_appraisal_period_column'); ?></th>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('appraisal_master_deparment_appraisal_comment_column'); ?></th>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('appraisal_master_deparment_appraisal_status_column'); ?></th>
                                    <th style="min-width: 15%">
                                        <?php echo $this->lang->line('appraisal_master_deparment_appraisal_completion_column'); ?></th>
                                    <th style="min-width: 15%">
                                        &nbsp;</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo footer_page('Right foot', 'Left foot', false); ?>
            <script type="text/javascript">
                app = {};
                app.company_id = <?php echo current_companyID(); ?>;
                app.department_appraisal_table = $('#department_appraisal_table').DataTable({
                    "language": {
                        "url": "<?php echo base_url("plugins/datatables/i18n/$primaryLanguage.json") ?>"
                    },
                });

                $(document).ready(function () {
                    load_department_appraisal_table();
                });

                function load_department_appraisal_table() {
                    app.department_appraisal_table.clear().draw();
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: "<?php echo site_url('Appraisal/department_appraisal'); ?>",
                        data: {},
                        success: function (data) {
                            var sequence = 1;
                            data.department_appraisals.forEach(function (item, index) {
                                var department = '<div>'+item.DepartmentDes+'</div>';
                                var narration = item.narration;
                                var document_id = item.department_appraisal_doc_id;
                                var period = item.year;

                                var progress_bar_text_color = 'black';
                                if (item.completion_percentage >= 60) {
                                    progress_bar_text_color = 'white';
                                }
                                var task_completion_percentage = '<div class="progress" style="height: 20px;margin-right: 5px;">' +
                                    '  <div class="progress-bar progress-bar-success" role="progressbar" style="width: ' + item.completed_percentage.toFixed(1) + '%;" aria-valuenow="' + item.completed_percentage.toFixed(1) + '" aria-valuemin="0" aria-valuemax="100"></div>' +
                                    '<span style="color: ' + progress_bar_text_color + ';">' + item.completed_percentage.toFixed(1) + '%</span>' +
                                    '</div>';

                                var status = null;
                                if(item.is_closed==0){
                                    status = '<span style="color:forestgreen;"><?php echo $this->lang->line('common_open'); ?></span>';
                                }else{
                                    status = '<span style="color:red;"><?php echo $this->lang->line('common_closed'); ?></span>';
                                }

                                var view_button= ' <div style="text-align: center"><a onclick="attachment_modal('+item.goal_id+',\'<?php echo $this->lang->line('appraisal_master_deparment_appraisal_title')  ?>\',\'APR\',1);"><span title="" rel="tooltip" class="glyphicon glyphicon-paperclip" data-original-title="Attachment"></span></a> <i data-goal_id="'+item.goal_id+'" data-department_id="'+item.DepartmentMasterID+'" onclick="view_button_click.call(this)" class="glyphicon glyphicon-eye-open act-btn-margin" style="color: #3c8dbc;"></i><div>';
                                app.department_appraisal_table.row.add([sequence, document_id, department, period, narration, status,task_completion_percentage, view_button]).draw(false);
                                sequence++;
                            });
                        }
                    });
                }

                function view_button_click(){
                    localStorage.setItem('config_department_id', $(this).data('department_id'));
                    localStorage.setItem('config_goal_id', $(this).data('goal_id'));
                    fetchPage('system/appraisal/activity/set_department_appraisal_config','0','Department Appraisal');
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
