<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('hrms_leave_management', $primaryLanguage);
    $work_drop_down = array(''=>'Select Type of Work','1'=>'Standard','2'=>'Rework','3'=>'Additional Work');
?>

<style>
    .inputbox{
        width: 50px;
    }
</style>

<div>

    <input type="hidden" name="empIDPick" id="empIDPick" value="<?php echo $empDetails['EIdNo'] ?>" />
    <input type="hidden" name="attendanceDatePick" id="attendanceDatePick" value="<?php echo $attendanceDate ?>" />

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
           
                    <p><span class="empName text-bold">Employee Name</span> : <?php echo $empDetails['Ename1'] ?></p>
                    <p><span class="empName text-bold">Employee Code</span> : <?php echo $empDetails['ECode'] ?></p>
                    <p><span class="empName text-bold">Date</span> : <?php echo $attendanceDate ?></p>
            
            </div>


            <div class="col-md-6">
                <div class="form-group">
                    <?php if($view != '1') { ?>
                        <?php echo form_dropdown('manufactureID', $jobList, null, 'class="form-control" multiple id="manufactureID" '); ?>
                        <button class='btn btn-primary' onclick="add_manufacture_jobs()"><i class="fa fa-plus"></i></button>
                    <?php } ?>
                </div>
               <div class="">
                   
               </div>
   
            </div>
        </div>
    </div>

   

    <table class="<?php echo table_class() ?>">
        <thead>
            <tr>
                <td><?php echo '#'?><!--Employee--></td>
                <td><?php echo $this->lang->line('hrms_leave_management_job_code')?><!--Employee--></td>
                <td><?php echo $this->lang->line('hrms_leave_management_job_description')?><!--Employee--></td>
                <td><?php echo $this->lang->line('hrms_leave_management_type_of_work')?><!--Employee--></td>
                <td width="20%"><?php echo $this->lang->line('hrms_leave_management_labour_task')?><!--Employee--></td>
                <td><?php echo $this->lang->line('hrms_leave_management_time')?><!--Employee--></td>
                <td>#<!--Employee--></td>
            </tr>
        </thead>

        <tbody>

            <?php if(count($detail) > 0){ ?>
                <?php foreach($detail as $job_record){ ?>
                    <tr>
                        <td></td>
                        <td><?php echo $job_record['jobCode'] ?> </td>
                        <td><?php echo $job_record['jobDescription'] ?> </td>

                        <td> <?php echo form_dropdown('typeOfWork', $work_drop_down,  $job_record['typeOfWork'], 'class="form-control select2" onchange="change_hours_minutes(this,'.$job_record['id'].',\''.'typeOfWork'.'\')"  id="typeOfWork" '); ?></td>
                        <td>
                            <select name="labourTask" id="labourTask" class="select2" style="width:200px" onchange="update_labour_task(<?php echo $job_record['id'] ?>,this)">
                                <option value="">Select Labour Task</option>
                                <?php foreach($job_record['labour_tasks'] as $val){ ?>
                                    <option value="<?php echo $val['overHeadID'] ?>" <?php echo ($job_record['labourTaskID'] == $val['overHeadID']) ?  'selected' : '' ?>><?php echo $val['overHeadCode'].' - '.$val['description'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><input type="number" class="text-right inputbox" onchange="change_hours_minutes(this,<?php echo $job_record['id'] ?>,'hours')" name="job_time_hour" id="job_time_hour" placeholder="" value="<?php echo $job_record['worked_hour'] ?>" /> <input type="number" class="text-right inputbox" placeholder="" onchange="change_hours_minutes(this,<?php echo $job_record['id'] ?>,'minutes')" name="job_time_mins" id="job_time_mins" value="<?php echo $job_record['worked_minute'] ?>" /></td>
                        <td><a class="btn btn-danger" onclick="delete_added_job(<?php echo $job_record['id'].','.$job_record['id'].',' ?>)"><i class="fa fa-trash"></i></a></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>

                <tr><td></td><td colspan="4">No activity to show </td></tr>

            <?php } ?>
        
        </tbody>
    </table>
</div>


<script>

        $("#manufactureID").multiselect2({
            enableFiltering: true,
            filterPlaceholder: 'Select Job',
            includeSelectAllOption: false
        });

        function add_manufacture_jobs(){
            var manufatureID = $("#manufactureID").val();
            var empID = $("#empIDPick").val();
            var attendanceDate = $("#attendanceDatePick").val();

            $.ajax({
                type: 'post',
                dataType: 'html',
                data: {'empID': empID,'manufatureID':manufatureID,'attendanceDate':attendanceDate},
                url: '<?php echo site_url('Employee/add_mfqjob_employee'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                    change_job_select(attendanceDate,empID);
                 
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });

        }

        function change_hours_minutes(ev,id,type){
            var value = $(ev).val();

            $.ajax({
                type: 'post',
                dataType: 'html',
                data: {'value': value,'id':id,'type':type},
                url: '<?php echo site_url('Employee/change_job_minutes_hours'); ?>',
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    refreshNotifications(true);
                 
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    myAlert('e', '<br>Message: ' + errorThrown);
                }
            });
        }

        function delete_added_job(id){

            swal({
                title: "Are you sure", /*Are you sure?*/
                text:"You want to delete the record", /*You want to confirm this document!*/
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?php echo $this->lang->line('common_confirm');?>", /*Confirm*/
                cancelButtonText: "<?php echo $this->lang->line('common_cancel');?>"/*Confirm*/
            },
            function () {
                $.ajax({
                    async: true,
                    type: 'post',
                    dataType: 'json',
                    data: {"id":id},
                    url: "<?php echo site_url('Employee/remove_mfq_job_added'); ?>",
                    beforeSend: function () {
                        startLoad();
                    },
                    success: function (data) {
                        refreshNotifications(true);
                        

                    }, error: function () {
                        stopLoad();
                        swal("Cancelled", "Your file is safe :)", "error");
                    }
                });
            });

        }

        function update_labour_task(id,ev){

            var labour = $(ev).val();

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {"id":id,"labour":labour},
                url: "<?php echo site_url('Employee/update_labour_taskID'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    refreshNotifications(true);
                    

                }, error: function () {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });


        }

        function update_type_of_work(ev){


        }

        $('.select2').select2();

</script>