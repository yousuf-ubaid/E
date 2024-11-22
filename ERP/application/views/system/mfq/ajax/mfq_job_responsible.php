<?php
    $employee = all_employee_drop_mfq_apply(true, 1);
 
?>

<?php if(isset($detail)) { ?>
    <label><u>Assign Responsible Person</u></label>

    <table class="table">
        <thead>
            <th width="25%">Workflow</th>
            <th width="25%">Responsible Person</th>
        </thead>
        <tbody>

            <?php foreach($detail as $data){ ?>
                <tr>
                    <td><?php echo $data['description'] ?></td>
                    <td>
                
                        <?php //$assigned = explode(',',$mfq_stage_val['assigneeID'])  assign_stage_assignee(this,'.$stage_id.')" ?>
                        <?php echo form_dropdown('responsible[]',$employee, isset($workProcess[$data['templateDetailID']]) ? $workProcess[$data['templateDetailID']] : '', 'class="form-control responsible" multiple id="responsible" onchange="assign_stage_responsible(this,'.$workProcessID.','.$data['templateDetailID'].')"'); ?>                                         
                
                    </td>
                </tr>
            <?php } ?>

        </tbody>
    </table>

<?php } ?>

<script>

    $('.responsible').multiselect2({
        enableCaseInsensitiveFiltering: true,
        includeSelectAllOption: false,
        selectAllValue: 'select-all-value',
        //enableFiltering: true
        buttonWidth: 150,
        maxHeight: 300,
        numberDisplayed: 2
    });

    function assign_stage_responsible(ev,workProcessID,phaseID){

        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {responsible: $(ev).val(),workProcessID:workProcessID,phaseID:phaseID},
            url: "<?php echo site_url('MFQ_Template/assign_stage_responsible'); ?>",
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                myAlert(data[0], data[1]);
                
            },
            error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
                refreshNotifications(true);
            }
        });

    }


</script>