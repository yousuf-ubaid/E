<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$priority_arr = all_priority_new_drop();
$current_date = format_date($this->common_data['current_date']);
?>
<div class="nav-tabs-custom">
    <div class="box-tools pull-right">
        <button type="button" onclick="openToDoListModal()" title="Add List" class="btn btn-box-tool"><i
                class="fa fa-plus-square-o"></i>
        </button>
    </div>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#todolistview" onclick="load_to_do_list_view()" data-toggle="tab" aria-expanded="true"><?php echo $this->lang->line('dashboard_to_do_list');?><!--To Do List--> &nbsp;&nbsp;<span style="font-size: 0.7em;">(<?php echo date('d-m-Y'); ?>)</a>
        </li>
        <li class=""><a href="#todolisthistry" onclick="load_to_do_list_History()"
                        data-toggle="tab" aria-expanded="false"><?php echo $this->lang->line('dashboard_history');?><!--History--></a>
        </li>

    </ul>
    <div class="tab-content" style="max-height: calc(45vh - 45px);overflow-y: auto;">
        <div class="tab-pane active" id="todolistview">

        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="todolisthistry">


        </div>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>


<div class="modal fade" id="toDoListModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('dashboard_to_do');?><!--To Do--> <i class="fa fa-list-alt"></i></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <form name="to_do_list_form" id="to_do_list_form" method="post">
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="Link"><?php echo $this->lang->line('common_date');?><!--Date--></label>
                        <input type="text" class="form-control datepicker" value="<?php echo $current_date; ?>"
                               id="startDate" name="startDate"
                               placeholder="Date">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="Link"><?php echo $this->lang->line('common_time');?><!--Time--></label>
                        <input type="text" class="form-control timrpicker" value="10:00 AM" id="startTime"
                               name="startTime"
                               placeholder="Time">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12">
                        <label for="description"><?php echo $this->lang->line('common_description');?><!--Description--></label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="Priority"><?php echo $this->lang->line('dashboard_priority');?><!--Priority--></label>
                        <?php echo form_dropdown('priority', $priority_arr, '', 'class="form-control select2" id="priority"'); ?>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="save_to_do_list()" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editToDoListModal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" style="width: 40%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('dashboard_to_do');?><!--To Do--> <i class="fa fa-list-alt"></i></span></h4>
            </div>
            <div class="modal-body" style="margin-left: 10px">
                <form name="edited_to_do_list_form" id="edited_to_do_list_form" method="post">
                    <input type="hidden" name="autoId" value="" id="autoId">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="Link"><?php echo $this->lang->line('common_date');?><!--Date--></label>
                            <input type="text" class="form-control datepicker" value="<?php  ?>"
                                   id="edit_startDate" name="edit_startDate"
                                   placeholder="Date" >
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="Link"><?php echo $this->lang->line('common_time');?><!--Time--></label>
                            <input type="text" class="form-control timrpicker" value="" id="edit_startTime"
                                   name="edit_startTime"
                                   placeholder="Time" >
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="edit_description"><?php echo $this->lang->line('common_edit_description');?><!--Description--></label>
                            <textarea class="form-control" id="edit_description" name="edit_description" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="Priority"><?php echo $this->lang->line('dashboard_priority');?><!--Priority--></label>
                            <?php echo form_dropdown('edit_priority', $priority_arr, '', 'class="form-control select2" id="edit_priority"'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button"><?php echo $this->lang->line('common_Close');?><!--Close--></button>
                <button type="submit" class="btn btn-primary btn-sm" onclick="update_to_do_list()" id="btnSave"><?php echo $this->lang->line('common_save');?><!--Save-->
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    load_to_do_list_view();

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    }).on('changeDate', function (ev) {
        $('#to_do_list_form').bootstrapValidator('revalidateField', 'startDate');
        $(this).datepicker('hide');
    });


    $('.timrpicker').timepicker({
        minuteStep: 1,
        defaultTime: '10:00 AM',
        template: 'dropdown',
        appendWidgetTo: 'body',
        showSeconds: false
    });

    function openToDoListModal() {
        $('#description').val('');
        $('#priority').val('');
        $('#to_do_list_form').bootstrapValidator('resetForm', true);
        $('#toDoListModal').modal("show");
    }

    function openEditToDoListModal(autoid, userID) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {'autoID':autoid ,'userDashboardID':userID},
            url: "<?php echo site_url('Finance_dashboard/edit_to_do_list'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {

                if (!jQuery.isEmptyObject(data)) {
                    $('#autoId').val(data['autoId']);
                    $('#edit_description').val(data['description']);
                    $('#edit_priority').val(data['priority']).change();
                    $('#edit_startDate').val(data['startDate']);
                    $('#edit_startTime').val(data['startTime']);

                }
                stopLoad();
                refreshNotifications(true);
                    $('#edited_to_do_list_form').bootstrapValidator('resetForm', true);
                    $('#editToDoListModal').modal("show");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                myAlert('e', 'Message: ' + "Select Widget");
                stopLoad();
                refreshNotifications(true);
            }
        });
    }

    function save_to_do_list() {
        const data = $('#to_do_list_form').serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Finance_dashboard/save_to_do_list'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#toDoListModal').modal('hide');
                    load_to_do_list_view();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Select Widget");
            }
        });

    }
    function update_to_do_list() {
        const data = $('#edited_to_do_list_form').serializeArray();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo site_url('Finance_dashboard/update_to_do_list'); ?>",
            data: data,
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                if (data[0] == 's') {
                    $('#editToDoListModal').modal('hide');
                    load_to_do_list_view();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                stopLoad();
                myAlert('e', 'Message: ' + "Select Widget");
            }
        });

    }

    function deletetodoList(id) {
        if (id) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('common_you_want_to_delete');?>",/*You want to delete this Record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_delete');?>",/*Delete*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function () {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo site_url('Finance_dashboard/deletetodoList'); ?>",
                        data: {autoId: id},
                        cache: false,
                        beforeSend: function () {
                            startLoad();
                        },
                        success: function (data) {
                            stopLoad();
                            if (data[0] == 's') {
                                $('#list').hide();
                                myAlert('s', 'Message: ' + data[1]);
                            } else if (data[0] == 'e') {
                                myAlert('e', 'Message: ' + data[1]);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            stopLoad();
                            myAlert('e', 'Message: ' + "Select Widget");
                        }
                    });
                });
        }
    }

    function changeDone(id) {
        let checked = '';
        if ($('#donechk_' + id).is(':checked')) {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "<?php echo $this->lang->line('dashboard_you_want_to_complete_this_record');?>",/*You want to complete this record!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*YES*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        let checked = -1;
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "<?php echo site_url('Finance_dashboard/check_to_do_list'); ?>",
                            data: {autoId: id, checked: checked},
                            cache: false,
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                if (data[0] == 's') {
                                    $('#list').hide();
                                    myAlert('s', 'Message: ' + data[1]);
                                } else if (data[0] == 'e') {
                                    myAlert('e', 'Message: ' + data[1]);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', 'Message: ' + "Error");
                            }
                        });
                    } else {
                        $('#donechk_' + id).prop('checked', false)
                    }
                });
        } else {
            swal({
                    title: "<?php echo $this->lang->line('common_are_you_sure');?>",/*Are you sure?*/
                    text: "You want to Reopen this Task!",/*You want to Reopen this Task!*/
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "<?php echo $this->lang->line('common_yes');?>",/*YES*/
                    cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        let checked = 0;
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "<?php echo site_url('Finance_dashboard/check_to_do_list'); ?>",
                            data: {autoId: id, checked: checked},
                            cache: false,
                            beforeSend: function () {
                                startLoad();
                            },
                            success: function (data) {
                                stopLoad();
                                if (data[0] == 's') {
                                    $('#list').hide();
                                    myAlert('s', 'Message: ' + data[1]);
                                } else if (data[0] == 'e') {
                                    myAlert('e', 'Message: ' + data[1]);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                stopLoad();
                                myAlert('e', 'Message: ' + "Error");
                            }
                        });
                    } else {
                        $('#donechk_' + id).prop('checked', true)
                    }
                });
        }
    }

    function load_to_do_list_History() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/load_to_do_list_History'); ?>",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#todolisthistry").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }

    function load_to_do_list_view() {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/load_to_do_list_view'); ?>",
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#todolistview").html(data);
            },
            error: function () {
                stopLoad();
            }
        });
    }


</script>
