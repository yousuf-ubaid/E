<?php

$oldLeaveTypes = array_column($oldGrpDet, 'leaveTypeID');
$newLeaveTypes = array_column($newGrpDet, 'leaveTypeID');
$bothGroup = array_unique( array_merge($oldLeaveTypes, $newLeaveTypes) );

/*
echo '<pre> Balance : '; print_r($leaveBalance_arr); echo '</pre>';
echo '<pre> old : '; print_r($oldLeaveTypes); echo '</pre>';
echo '<pre> new : '; print_r($newLeaveTypes); echo '</pre>';
echo '<pre> bothGroup : '; print_r($bothGroup); echo '</pre>';*/

?>
<style>
    .att-number{
        text-align: right;
    }
</style>
<input type="hidden" name="change-id" value="<?php echo $id; ?>">
<table class="<?php echo table_class() ?>" id="">
    <thead>
        <tr>
            <th colspan="3"> Old Group (<?php echo $oldDes; ?>) </th>
            <th>&nbsp;</th>
            <th colspan="4"> New Group (<?php echo $newDes; ?>) </th>
        </tr>
        <tr>
            <th>#</th>
            <th>Leave Type</th>
            <th>Balance</th>
            <th>&nbsp;</th>
            <th>Leave Type</th>
            <th>Entitle</th>
            <th>Adjustment</th>
        </tr>
    </thead>

    <tbody>
    <?php

    $n = 1;
    foreach ($bothGroup as $leaveType_row){
        $oldKey = array_search($leaveType_row, $oldLeaveTypes);
        $newKey = array_search($leaveType_row, $newLeaveTypes);

        $leaveDes_o = '';
        $leaveDes_n = '';
        $leaveBalance = '-';
        $adjustTextBox = '';
        $entitle = '';
        if($oldKey !== false){
            $leaveDes_o = $oldGrpDet[$oldKey]['description'];
            $leaveBalanceKey = 'lev_'.$leaveType_row;

            if(is_array($leaveBalance_arr)){
                $leaveBalance = (array_key_exists($leaveBalanceKey, $leaveBalance_arr))? $leaveBalance_arr[$leaveBalanceKey]: $leaveBalance;
            }


        }

        if($newKey !== false){
            $entitle = $newGrpDet[$newKey]['noOfDays'];
            $leaveDes_n = $newGrpDet[$newKey]['description'];
            $adjustTextBox = '<input type="text" name="adjustmentVal[]" class="att-number" value="'.$entitle.'" style="width: 50px"/>';
            $adjustTextBox .= '<input type="hidden" name="newLeaveType[]" value="'.$leaveType_row.'"/>';
        }


        echo '<tr>
                <td> '.$n.' </td>
                <td> '.$leaveDes_o.' </td>
                <td align="right"> '.$leaveBalance.' </td>               
                <td style="width: 50px"> &nbsp; </td>              
                <td> '.$leaveDes_n.' </td>
                <td align="right"> '.$entitle.' </td>
                <td align="center"> '.$adjustTextBox.' </td>                     
              <tr/>';

        $n++;
    }

    ?>
    </tbody>
</table>

<script>
    $('.att-number').numeric({decimalPlaces:2});
</script>
<?php
