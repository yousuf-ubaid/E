<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('appraisal_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$title = "Appraisal Calendar";
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

    .fc-title {
        font-size: 12px;
    }

    .fc-time {
        font-size: 12px;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url('plugins/bootstrap-slider-master/dist/css/bootstrap-slider.min.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/crm/css/pipeline.css'); ?>">
<link href='<?php echo base_url('plugins/fullcalender/lib/cupertino/jquery-ui.min.css'); ?>' rel='stylesheet'/>
<link href='<?php echo base_url('plugins/fullcalender/fullcalendar.min.css'); ?>' rel='stylesheet'/>
<link href='<?php echo base_url('plugins/fullcalender/fullcalendar.print.min.css'); ?>' rel='stylesheet' media='print'/>
<script type="text/javascript" src="<?php echo base_url('plugins/fullcalender/fullcalendar.min.js'); ?>"></script>

<div class="row">
    <div class="col-md-12" id="sub-container">
        <div class="box">
            <div class="box-header with-border" id="box-header-with-border">
                <h3 class="box-title" id="box-header-title"><?php echo $title; ?></h3>
                <div class="box-tools pull-right">
                    &nbsp;
                </div>
            </div>
            <link rel="stylesheet"
                  href="<?php echo base_url('plugins/Horizontal-Hierarchical/src/jquery.hortree.css'); ?>"/>

            <div class="row">
                <div class="col-md-12">
                    <div id="appraisal_calendar"></div>
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
                                id="CommonEdit_Title"><?php echo $this->lang->line('appraisal_task_details'); ?></h4>
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
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>
    calendar_app = {};
    $(document).ready(function(){
        load_calendar();
    });

    function load_calendar() {
        $('#appraisal_calendar').fullCalendar({
            customButtons: {
                myCustomButton: {
                    text: 'Task List',
                    click: function() {
                        //subtask_task_rpt_dashboard(1);
                    }
                }
            },
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,myCustomButton'
            },
            defaultDate: new Date(),
            navLinks: true, // can click day/week names to navigate views
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: {
                url: '<?php echo site_url('Appraisal/allCalenderEvents'); ?>',
                data: function () {
                    return {};
                },
                type: "POST",
                cache: false
            },
            displayEventTime: false,
            dayClick: function (date) {
                // swal({
                //         title: "Are you sure?",
                //         text: "You want to create a task!",
                //         type: "warning",
                //         showCancelButton: true,
                //         confirmButtonColor: "#00A65A",
                //         confirmButtonText: "Create Task"
                //     },
                //     function () {
                //         fetchPage('system/crm/create_new_task', '', 'Create Task', 2, date.format());
                //     });
            },
            eventRender: function (event, element) {
                /*                element.find(".fc-content").append("<i style='color: white; font-size: 12px' class='fa fa-eye pull-right closeon' aria-hidden='true' title='View'></i>");*/
                element.find(".fc-content").click(function () {
                    viewEvent(event._id);
                });
            }
            //
        });
        // $('.fc-title').attr('style', 'font-size: 12px !important');
    }

    function viewEvent(id){
        $.ajax({
            async: false,
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/get_calendar_task_details'); ?>",
            data: {task_id: id},
            success: function (data) {
                if(data.is_own_task==1){
                    update_progress(data);
                }else{
                    watch_progress(data);
                }
            }
        });
    }

    function watch_progress(data) {
        $("#add_task").hide();
        calendar_app.appraisal_header_id = data.appraisal_id;
        calendar_app.task_id_to_update_progress = data.task_id;
        $('#task_description').text(data.task_description);
        $('#task_progress_input').val(data.completion);
        $('#task_progress_in_number').text(data.completion + '%');
        $('#task_progress_update_modal').modal('show');
    }

    function update_progress(data) {
        $("#add_task").show();
        calendar_app.appraisal_header_id = data.appraisal_id;
        calendar_app.task_id_to_update_progress = data.task_id;
        $('#task_description').text(data.task_description);
        $('#task_progress_input').val(data.completion);
        $('#task_progress_in_number').text(data.completion + '%');
        $('#task_progress_update_modal').modal('show');
    }

    function save_progress() {
        var task_progress = $('#task_progress_input').val();
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "<?php echo site_url('Appraisal/save_task_progress'); ?>",
            data: {task_progress: task_progress, task_id: calendar_app.task_id_to_update_progress},
            success: function (data) {
                $('#task_progress_update_modal').modal('hide');
                myAlert('s', 'Successfully Updated.');
            }
        });
    }

    function task_progress_input_onchange() {
        $('#task_progress_in_number').text($(this).val() + '%');
    }

</script>


