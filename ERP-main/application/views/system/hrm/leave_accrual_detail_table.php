
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_leave_management', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
  $confirm=$confirmedYN['confirmedYN'];
  $disabled='';
  if($confirm==1){
      $disabled='disabled';
  }
?>

<table class="<?php echo table_class() ?>">
    <thead>
    <tr>
        <th><?php echo $this->lang->line('hrms_leave_management_employee');?><!--Employee--></th>
        <th><?php echo $this->lang->line('common_description');?><!--Description--></th>
        <?php if($header){
            foreach($header as $value){
                ?>
                <th style=""><?php echo $value['description'] ?></th>
                <?php
            }
        }?>
    </tr>
    </thead>
    <tbody>

    <?php if ($details) {
        foreach ($details as $val) {


            ?>
            <tr>
                <td><?php echo $val['Ename2'] ?></td>
                <td><?php echo $val['description'] ?></td>
                <?php if($header){
                    foreach($header as $value){
                        $string = str_replace(' ','',$value['description']);



                            ?>

                            <td style=""><input <?php echo $disabled ?> step="any" type="number" value="<?php echo $val[$string] ?>" name="daysEntitled" onchange="updateLeaveAdjustmentDetail(1,this,<?php echo $value['leaveType'] ?>,<?php echo $val['empID'] ?>)">  </td>
                            <?php
                        }
                }?>
            </tr>
            <?php

        }
    }
    ?>

    </tbody>
</table>

<hr>
<div class="text-right m-t-xs">
    <a onclick="$('.headerclose').trigger('click');" class="btn btn-sm btn-primary"><?php echo $this->lang->line('common_save');?><!--Save--></a>
<?php if($confirm !=1){?>


    <a onclick="confirmAccrual()"
       class="btn btn-sm btn-success"><?php echo $this->lang->line('common_confirm');?><!--Save--></a>

<?php } ?>
</div>
<script>

</script>