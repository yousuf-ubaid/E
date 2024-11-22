<?php
    
$employee = all_employee_drop(true, 1,null,null);
$current_date = date('Y-m-d');
$date_format_policy = date_format_policy();

?>


<table id="mfq_crew_<?php echo $documentID ?>" class="table table-condensed">
    <thead>
    <tr>
        <th style="min-width: 10%">Stage<!--Stage--></th>
        <th style="min-width: 15%">Progress<!--Stage Progress--></th>
        <th style="min-width: 20%">Assign Person<!--Stage Progress--></th>
        <th style="min-width: 15%">Estimate Date Delivery<!--Stage Progress--></th>
        <th style="min-width: 15%">Actual Date Delivery<!--Stage Progress--></th>
        <th style="min-width: 15%">Checklist<!--Stage Progress--></th>
        <th style="min-width: 15%">Remarks<!--Stage Remarks--></th>
        <th style="min-width: 5%">Approve<!--Stage Remarks--></th>
    </tr>
    </thead>
    <tbody id="stage_body_<?php echo $documentID ?>">

    <?php
            $mfq_stage = get_mfq_stage($workProcessID,$templateDetailID);
            $total_weightage = 1;
            $approved_weightage = 0;

            foreach ($mfq_stage as $mfq_stage_val) { 
                $stage_id = trim($mfq_stage_val['stage_id'] ?? '');
                $stage_progress =trim($mfq_stage_val['stage_progress'] ?? '');
                $total_weightage += $mfq_stage_val['weightage'];

                if($mfq_stage_val['approved'] == 1){
                    $approved_weightage += $mfq_stage_val['weightage'];
                }
                
    
    ?>

    <tr>
        <td><?php echo $mfq_stage_val['stage_name']; ?></td>
        <td>
            <div class="set-div">
                <span id="rangeValue_<?php echo $stage_id; ?>"><?php echo $stage_progress; ?></span>%
                <input class="range" type="range" value="<?php echo $stage_progress; ?>" min="0" max="100" onChange='rangeSlide(this.value, <?php echo $stage_id; ?>)'/>
            </div>
        </td>
        <td>
            <?php $assigned = explode(',',$mfq_stage_val['assigneeID'] ?? '') ?>
            <?php echo form_dropdown('assignee[]',$employee, $assigned, 'class="form-control assignee" multiple id="assignee" required onchange="assign_stage_assignee(this,'.$stage_id.')"'); ?>                                         
        </td>
        <td>
            <div class="input-group ">
                <!-- <div class="input-group-addon"><i class="fa fa-calendar"></i></div> -->
                <input onchange="change_stage_value(<?php echo $workProcessID.','.$stage_id ?>,this,'estimated_date')" type="date" class="" id="estimated_date" name="estimated_date"  value="<?php echo ($mfq_stage_val['estimated_date']) ? $mfq_stage_val['estimated_date'] : $current_date; ?>"
                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required />
            </div>
        </td>
        <td>
            <div class="input-group ">
                <!-- <div class="input-group-addon"><i class="fa fa-calendar"></i></div> -->
                <input onchange="change_stage_value(<?php echo $workProcessID.','.$stage_id ?>,this,'actual_date')" type="date" class="" id="actual_date" name="actual_date"  value="<?php echo ($mfq_stage_val['actual_date']) ? $mfq_stage_val['actual_date'] : $current_date; ?>"
                    data-inputmask="'alias': '<?php echo $date_format_policy ?>'" required />
            </div>
        </td>

        <td>
            <button type="button" class="btn btn-default" onclick="load_checklist(<?php echo $workProcessID.','.$stage_id.','.$templateDetailID  ?>)"><i class="fa fa-pencil"></i></button>

        </td>
        <td>
            <input type="text" name="mfq_stage_remark" id="mfq_stage_remark_<?php echo $mfq_stage_val['stage_id']; ?>" class="form-control" value="<?php echo $mfq_stage_val['stage_remarks']; ?>" onChange="updateMfqRemark(<?php echo $mfq_stage_val['stage_id']; ?>)">
            <input type="hidden" name="mfq_stage_id" id="mfq_stage_id" value="<?php echo $mfq_stage_val['stage_id']; ?>" >                                            
        </td>
        <td>
            <input type="checkbox" value="1" class="approved" id="approved" name="approved" <?php echo ($mfq_stage_val['approved'] == 1) ? 'checked' : '' ?> onchange="change_stage_value(<?php echo $workProcessID.','.$stage_id ?>,this,'approved')" />
        </td>
    </tr>  
    
    <script type="text/javascript">                                        
            function rangeSlide(stage_progress,stage_id) {
                document.getElementById('rangeValue_'+stage_id).innerHTML = stage_progress;
                updateMfqProgress(stage_id,stage_progress);
            }                                       
    </script>
    <?php } ?>      
    <input type="hidden" name="mfq_job_id" id="mfq_job_id" value="<?php echo $workProcessID; ?>" >                     
    </tbody>
</table>



    <script>

        $('.assignee').multiselect2({
            enableCaseInsensitiveFiltering: true,
            includeSelectAllOption: false,
            selectAllValue: 'select-all-value',
            //enableFiltering: true
            buttonWidth: 150,
            maxHeight: 300,
            numberDisplayed: 2
        });
        

        
        function load_checklist(workProcessID,stage_id,templateID){

            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('MFQ_Job/load_stage_checklist'); ?>",
                data: {'workProcessID': workProcessID,'stage_id': stage_id,'templateID': templateID},
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#mfq_stage_div').empty();
                    $('#mfq_stage_div').html(data);
                    $("#mfq_stage_checklist").modal('show');

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                    swal("Cancelled", "Your file is safe :)", "error");
                }
            });



        }
    

    </script>