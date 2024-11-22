<?php

$oldLeaveTypes = array_column($oldGrpDet, 'leaveTypeID');
$newLeaveTypes = array_column($newGrpDet, 'leaveTypeID');
$bothGroup = array_unique( array_merge($oldLeaveTypes, $newLeaveTypes) );

?>
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
            $entitle = '';
            if($oldKey !== false){
                $leaveDes_o = $oldGrpDet[$oldKey]['description'];
                $leaveBalance = $oldGrpDet[$oldKey]['previous_balance'];

            }

            if($newKey !== false){
                $leaveDes_n = $newGrpDet[$newKey]['description'];
                $entitle = $newGrpDet[$newKey]['daysEntitled'];
            }


            echo '<tr>
                <td> '.$n.' </td>
                <td> '.$leaveDes_o.' </td>
                <td align="right"> '.$leaveBalance.' </td>               
                <td style="width: 50px"> &nbsp; </td>              
                <td> '.$leaveDes_n.' </td>
                <td align="right"> '.$entitle.' </td>                     
              <tr/>';

            $n++;
        }

        ?>
        </tbody>
    </table>


<?php
