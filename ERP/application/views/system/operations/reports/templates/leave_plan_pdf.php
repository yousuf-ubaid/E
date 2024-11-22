<?php
$leavePlanData = fetch_leavePlan($empID,$leaveType,$filter);

if( !empty($leavePlanData)) {
    $employees = array_group_by($leavePlanData, 'empID');
}
?>

<h3 style="text-align:center; font-size:16px"><strong>Leave Plan Report</strong></h3>
<table>
    <tr><td height="10px" style="height:10px">&nbsp;</td></tr>
</table>

<table>
    <tr>
        <td style="width:60%">&nbsp;</td>
        <td style="width:40%">
            <table style="width:100%;">
                <tr>
                    <td style="width:25%;"><span style="background-color: #166123; border-radius: 2px; border: 1px solid #ccc;">&nbsp;&nbsp; &nbsp;&nbsp;</span> <strong>Approved</strong></td>
                    <td style="width:25%;"><span style="background-color: #13f358; border-radius: 2px; border: 1px solid #ccc;">&nbsp;&nbsp; &nbsp;&nbsp;</span> <strong>Confirmed</strong></td>
                    <td style="width:25%;"><span style="background-color: #61cde2; border-radius: 2px; border: 1px solid #ccc;">&nbsp;&nbsp; &nbsp;&nbsp;</span> <strong>Draft</strong></td>
                    <td style="width:25%;"><span style="background-color: #fda70a; border-radius: 2px; border: 1px solid #ccc;">&nbsp;&nbsp; &nbsp;&nbsp;</span> <strong>Planned</strong></td>
                </tr>
            </table>
        </td>
    </tr>
</table>


<table border="0" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th colspan="6">Details</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($employees as $empID => $employee): ?>
                <tr>
                    <?php if ($empID ): ?>
                        <td style="width:5%" rowspan="<?php //echo count($employee['details']); ?>"><?php echo htmlspecialchars($employee[0]['text']); ?></td>
                    <?php endif; ?>
                    <?php foreach ($employee as $index => $detail): ?>
                    <td style="background-color:#fff; width:10%">
                       
                        <table style="width:20px; height:10px; border: none;">
                            <tr>
                                <?php
                                    $datetime1 = new DateTime($detail['start_date']);
                                    $datetime2 = new DateTime($detail['endDate2']);
                                    $interval = $datetime1->diff($datetime2)->format('%d');

                                $indx;

                                if($interval == 0){ //one day leave?>
                                    <td><?php echo htmlspecialchars($detail['start_date']); ?></td>
                                <?php }?>

                                <?php if($interval > 0){ //more than one day leave?>
                                        <td><?php echo htmlspecialchars($detail['start_date']); ?></td>
                                    <?php for($indx=0;$indx<($interval-1);$indx++){ 
                                        $datetime1->modify('+1 day');
                                        $date = htmlspecialchars($datetime1->format('d-m-Y')); ?>
                                        <td>
                                            <?php echo $date ?>
                                        </td>
                                    <?php }?>
                                        <td><?php echo htmlspecialchars($detail['endDate2']); ?></td>
                                <?php } ?>
                            </tr>

                            <tr>
                                <?php
                                    $datetime1 = new DateTime($detail['start_date']);
                                    $datetime2 = new DateTime($detail['endDate2']);
                                    $interval = $datetime1->diff($datetime2)->format('%d');
                                ?>
                                    <?php for($i=0; $i<=$interval; $i++){ ?>
                                        <td style="background-color:<?php echo htmlspecialchars($detail['color']); ?>; width:20px; height:10px;">&nbsp;</td>
                                    <?php } ?>
                            </tr>
                        </table>
                    </td>
                    <?php endforeach; ?>
                </tr>
        <?php endforeach; ?>
    </tbody>
</table>