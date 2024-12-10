



<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px"
                                 src="<?php echo mPDFImage . $this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>

            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3>
                                <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . $this->common_data['company_data']['company_code'] . ').'; ?></strong>
                            </h3>
                            <h4> Employee Leave Balance Report</h4>
                        </td>


                    </tr>

                       <?php echo (isset($balancedata) ?'<tr><td> Leave Type  : '. $leaveType.'</td></tr>' : '')  ?>

                     <tr>
                        <td colspan="3">As of Date&nbsp; &nbsp;: <?php echo $asOfDate; ?></td>

                    </tr>

                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>
</br>
<?php
switch ($groupType) {
    case 1:
        ?>

        <?php
        if ($balancedata) {

            ?>
            <table id="tableID" class="borderSpace report-table-condensed">
                <thead class="report-header">
                <tr>
                    <th>EmpID</th>
                    <th>Employee Name</th>
                    <th>Days Entitle</th>
                    <th>Days Taken</th>
                    <th>Leave Balance</th>
                </tr>
                </thead>
                <tbody class="searchable">
                <?php
                foreach ($balancedata as $value) {
                    ?>
                    <tr class='hoverTr'>
                        <td style=""><?php echo $value['ECode'] ?></td>
                        <td style=""><?php echo $value['Ename2'] ?></td>
                        <td style="text-align: center"><?php echo $value['accrued'] ?></td>
                        <td style="text-align: center"><?php echo $value['leaveTaken'] ?></td>
                        <td style="text-align: center"><?php echo round($value['days'], 1) ?></td>

                    </tr>
                <?php } ?>
                </tbody>
            </table>

        <?php } else {
            ?>
            <div class="row">
                <div class="col-md-12 xxcol-md-offset-2">
                    <div class="alert alert-warning" role="alert">
                        <?php if ($error != '') {
                            echo $error;
                        } else { ?>
                            No Records found.
                        <?php } ?>

                    </div>
                </div>
            </div>
            <?php

        } ?>


        <?php break;
    case 2:
        ?>


      <!--  <h4 style="text-align: center;font-weight: bolder"><?php /*echo(empty($leaveqry) ? '' : ' As of ' . $asOfDate) */?></h4>-->
        <?php
        if ($leaveqry) {

            ?>
            <table id="tableID" class="borderSpace report-table-condensed">
                <thead class="report-header">
                <tr>
                    <th rowspan="2">EmpID</th>
                    <th rowspan="2">Employee Name</th>
                    <?php
                    if ($leaveqry) {
                        foreach ($leaveqry as $val) {
                            echo "<th colspan='3'>" . $val['description'] . "</th>";
                        }
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    if ($leaveqry) {
                        foreach ($leaveqry as $val) {
                            ?>
                            <th>Entitle</th>
                            <th>Taken</th>
                            <th>Balance</th>
                            <?php
                        }
                    }
                    ?>
                </tr>
                </thead>
                <tbody class="searchable">
                <?php
                foreach ($details as $val) {
                    ?>
                    <tr class='hoverTr'>
                        <td style="width:100px ;"><?php echo $val['ECode'] ?></td>
                        <td style=""><?php echo $val['Ename2'] ?></td>
                        <?php
                        if ($leaveqry) {
                            foreach ($leaveqry as $value) {
                                $desc = str_replace(' ', '', $value['description']);
                                $balance = $desc . 'balance';
                                $entitle = $desc . 'entitle';
                                $taken = $desc . 'taken';
                                if($policyType==2){
                                    $val[$entitle]=  timeformat($val[$entitle]);
                                    $val[$taken]=  timeformat($val[$taken]);
                                    $val[$balance]=  timeformat($val[$balance]);

                                }else{
                                  $val[$entitle]=  $val[$entitle];
                                  $val[$taken]=  $val[$taken];
                                  $val[$balance]=  $val[$balance];
                                }
                              echo "<td style='text-align: center'>" . $val[$entitle] . "</td>";
                              echo "<td  style='text-align: center'>" . $val[$taken]. "</td>";
                              echo "<td  style='text-align: center'>" . $val[$balance] . "</td>";
                            }
                        }
                        ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <br>
            <br>
            <br>
        <?php } else {
            ?>
            <div class="row">
                <div class="col-md-12 xxcol-md-offset-2">
                    <div class="alert alert-warning" role="alert">
                        <?php if ($error != '') {
                            echo $error;
                        } else { ?>
                            No Records found.
                        <?php } ?>

                    </div>
                </div>
            </div>
            <?php

        } ?>

        <?php
        break;
}
function timeformat($minutes){

    if ($minutes < 0)
    {

        $num = -1 * $minutes;
        $hours = floor($num / 60);
        $min = $num - ($hours * 60);

        return '-'.$hours."h:".$min."m";
    }
    else{
        $num = $minutes;
        $hours = floor($num / 60);
        $min = $num - ($hours * 60);

        return $hours."h:".$min."m";
    }

}
?>
