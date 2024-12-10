<?php $date_format_policy = date_format_policy(); ?>
<div class="row hide">
    <div class="pull-right" style="margin-right: 2%">
        <label>Pendings :
            <small class="label label-danger" style="border-radius: 50%" onclick="ViewPendingTasks()">&nbsp;<?php echo count($pendingTasks) ?>&nbsp;</small>
        </label>
    </div>
</div>

<div id="to-do_List">
    <ul class="todo-list ui-sortable">
        <?php if (!empty($details))
        {
            $a = 1;
            foreach ($details as $val)
            {
                if($val['linkedDocument'] == '0')
                {
                    $val['linkedDocument'] = '';
                }
                $TodoDate = input_format_date($TodoDate, $date_format_policy);
                if($val['taskDate'] > $TodoDate)
                {
                    $dStart = new DateTime($val['taskDate']);
                    $dEnd = new DateTime($TodoDate);
                    $dDiff = $dStart->diff($dEnd);
                    $newFormattedDate = $dDiff->days;
                    $delayDays = $newFormattedDate;
                } else if($val['taskDate'] < $TodoDate)
                {
                    $dStart = new DateTime($val['taskDate']);
                    $dEnd = new DateTime($TodoDate);
                    $dDiff = $dStart->diff($dEnd);
                    $newFormattedDate = $dDiff->days;
                    $pendingDays = $newFormattedDate;
                }

                ?>
                <li>
                <span class="handle ui-sortable-handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>

                    <input type="checkbox" class="updateTodo<?php echo $a; ?>" onclick="saveTaskDone(<?php echo $a; ?>,<?php echo $val['batchMasterID']; ?>,<?php echo $val['farmID']; ?>,'<?php echo $val['description']; ?>','<?php echo $val['feedscheduledetailID']; ?>','<?php echo $val['tasktypeID']; ?>')" <?php if($val['taskID']) echo "checked disabled";?>>

                    <span class="text"> <?php if($val['taskID']) echo '<del>';?><?php echo $val['farm'] . ' | ' . $val['batch'] . ' | ' . $val['description']?> <?php if($val['taskID']) echo '</del>';?></span>
                    <?php if($val['taskID']){ ?>
                        <span class="text pull-right"><?php echo $val['linkedDocument'] . ' &nbsp;&nbsp; ' .  $val['taskDate'] . ' | ' . $val['taskComment']  ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <?php }  if($val['taskID'] && isset($delayDays)) {?>
                        <small class="label label-danger"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;<?php echo $delayDays . '  Days delay';?></small>
                    <?php } else if($val['taskID'] && isset($pendingDays)) {?>
                        <small class="label label-success"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;<?php echo $pendingDays . '  Day earlier';?></small>
                    <?php } ?>
                </li>


                <?php $a++; } ?>
        <?php } else { ?>
            <li>
                <span class="handle ui-sortable-handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                <span class="text-center">No Tasks Available</span>
            </li>
        <?php } ?>
    </ul>
</div>

<div class="modal fade bs-example-modal-lg" id="total_pending_tasks" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                    Pending Tasks
                </h4>
            </div>
            <div class="modal-body">
                <ul class="todo-list ui-sortable">
                    <?php if (!empty($pendingTasks))
                    {
                        $a = 1;
                        foreach ($pendingTasks as $val)
                        {

                            ?>
                            <li>
                <span class="handle ui-sortable-handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                                <input type="checkbox" class="updateTodo<?php echo $a; ?>" onclick="saveTaskPending(<?php echo $a; ?>,<?php echo $val['batchMasterID']; ?>,<?php echo $val['farmID']; ?>,'<?php echo $val['description']; ?>','<?php echo $val['feedscheduledetailID']; ?>','<?php echo $val['tasktypeID']; ?>')">

                                <span class="text"><?php echo $val['farm'] . ' | ' . $val['batch'] . ' | ' . $val['description']?> </span>
                                <span class="text pull-right">Task Date : <?php echo date("d-m-Y", $val['task_date']);  ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>

                            </li>


                            <?php $a++; } ?>
                    <?php } else { ?>
                        <li>
                <span class="handle ui-sortable-handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                            <span class="text-center">No Tasks Available</span>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="taskDone_Modal" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title exampleModalLabel" id="exampleModalLabel">
                    Update Task</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="NewTaskDone_form">
                    <input type="hidden" name="checkVal" id="checkVal"/>
                    <input type="hidden" name="farmMasID" id="farmMasID"/>
                    <input type="hidden" name="batchID" id="batchID"/>
                    <input type="hidden" name="taskDescription" id="taskDescription"/>
                    <input type="hidden" name="tasktypeID" id="tasktypeID"/>
                    <input type="hidden" name="feedscheduledetailID" id="feedscheduledetailID"/>

                    <div class="form-group">
                        <label for="Comment" class="col-sm-3 control-label">Comment</label>
                        <div class="col-sm-7">
                            <input type="text" name="taskComment"
                                   placeholder="comment...." maxlength="30"
                                   value="" id="taskComment" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="linkDocument" class="col-sm-3 control-label">Link Document</label>
                        <div class="col-sm-7 linkDocumentDiv">
                            <div class="linkDocumentDrop">
                                <?php $batch_arr = array();
                                echo form_dropdown('DocumentAutoID', $batch_arr, 'Each', 'class="form-control select2" '); ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="UpdateTaskAsDone()">Update</button>
            </div>
        </div>
    </div>
</div>
<script>
    function saveTaskDone(checkVal, batchMasterID, farmID, description,feedscheduledetailID, tasktypeID)
    {
        $('#checkVal').val(checkVal);
        $('#batchID').val(batchMasterID);
        $('#farmMasID').val(farmID);
        $('#taskDescription').val(description);
        $('#feedscheduledetailID').val(feedscheduledetailID);
        $('#tasktypeID').val(tasktypeID);
        $('#taskComment').val('');
        $('#DocumentAutoID').val('');
     //   $('#checkVal').val(checkVal);
        fetch_linkDocument_DropDown(tasktypeID,description, batchMasterID );
        $('#taskDone_Modal').modal('show');
    }

    function saveTaskPending(checkVal, batchMasterID, farmID, description, feedscheduledetailID,tasktypeID)
    {
        $('#checkVal').val(checkVal);
        $('#batchID').val(batchMasterID);
        $('#farmMasID').val(farmID);
        $('#feedscheduledetailID').val(feedscheduledetailID);
        $('#taskDescription').val(description);
        $('#tasktypeID').val(tasktypeID);
        $('#taskComment').val('');
        $('#DocumentAutoID').val('');
     //   $('#checkVal').val(checkVal);
        fetch_linkDocument_DropDown(tasktypeID,description, batchMasterID );
        $('#taskDone_Modal').modal({backdrop: "static"});
    }

    $('.close').click(function() {
        var checkVal=  $('#checkVal').val();
        $('.updateTodo'+ checkVal).attr('checked', false);
    });

    function fetch_linkDocument_DropDown(tasktypeID,type, batch) {
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {tasktypeID:tasktypeID,type : type, batch: batch},
            url: "<?php echo site_url('BuybackDashboard/fetch_linkDocument_DropDown'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                $('.linkDocumentDrop').html(data);
                $('.select2').select2();
                stopLoad();
            }, error: function () {
                stopLoad();
                swal("Cancelled", "Your file is safe :)", "error");
            }
        });
    }

    function UpdateTaskAsDone() {
        var data = $('#NewTaskDone_form').serializeArray();

        $.ajax(
            {
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('BuybackDashboard/Save_TaskDone'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    if (data[0] == 's') {
                        myAlert(data[0], data[1]);
                        load_todayDoList();
                        $('#taskDone_Modal').modal('hide');
                        $('.modal-backdrop').remove();
                    }
                }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
            });
    }

    function ViewPendingTasks() {
        $('#total_pending_tasks').modal({backdrop: "static"});
    }
</script>


<?php
