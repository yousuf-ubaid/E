<?php

    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('hrms_leave_management', $primaryLanguage);

?>

<div>
   
    <div class="col-md-8">
        <div class="row">
            <p><span class="empName text-bold">Employee Name</span> : <?php echo $empDetails['Ename1'] ?></p>
            <p><span class="empName text-bold">Employee Code</span> : <?php echo $empDetails['ECode'] ?></p>
            <p><span class="empName text-bold">Date</span> : <?php echo $attendanceDate ?></p>
        </div>
    </div>
   

    <table class="<?php echo table_class() ?>">
        <thead>
            <tr>
                <td><?php echo '#'?><!--Employee--></td>
                <td><?php echo $this->lang->line('hrms_leave_management_job_code')?><!--Employee--></td>
                <td><?php echo $this->lang->line('hrms_leave_management_job_description')?><!--Employee--></td>
                <td><?php echo $this->lang->line('hrms_leave_management_activity')?><!--Employee--></td>
                <td><?php echo $this->lang->line('hrms_leave_management_starttime')?><!--Employee--></td>
                <td><?php echo $this->lang->line('hrms_leave_management_endtime')?><!--Employee--></td>
                <td><?php echo 'Job '.$this->lang->line('common_confirmed')?><!--Employee--></td>
            </tr>
        </thead>

        <tbody>

            <?php if(count($detail) > 0){ ?>

                <?php foreach($detail as $key => $value){ ?>
                    <tr>
                        <td></td>
                        <td><?php echo $value['job_code'] ?></td>
                        <td><?php echo $value['job_name'] ?></td>
                        <td><?php echo $value['description'] ?></td>
                        <td><?php echo $value['dateFrom'] ?></td>
                        <td><?php echo $value['dateTo'] ?></td>
                        <td>
                            <?php if($value['confirmed'] == 1) { ?>
                                <span class="label label-success">&nbsp;</span>
                            <?php } else { ?>
                                <span class="label label-danger">&nbsp;</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>

            <?php } else { ?>

                <tr><td></td><td colspan="4">No activity to show </td></tr>

            <?php } ?>
        
        </tbody>
    </table>
</div>