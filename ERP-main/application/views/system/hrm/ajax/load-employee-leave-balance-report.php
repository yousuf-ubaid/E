<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

if ($isPDF != 'pdf') {
    ?>

    <style>
        table {
            margin-left: 0px;
        }
    </style>

<?php
}
else{
?>
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
                                    <strong><?php echo $this->common_data['company_data']['company_name'] . ' (' . current_companyCode() . ').'; ?></strong>
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
<?php
}
switch ($groupType) {
    case 1:
        if ($balancedata) {  ?>
            <?php if ($isPDF != 'pdf') { ?>
            <div class="row" style="margin-top: 5px">
                <div class="col-md-12">
                    <?php echo export_buttons('div_table', 'Employee Leave Balance Report', TRUE, TRUE); ?>
                </div>
            </div>
            <?php } ?>
            <div class="row" id="div_table">
                <?php if ($isPDF != 'pdf') { ?>
                <div class="col-md-12" id="">
                    <div class="text-center reportHeaderColor">
                        <strong><?php echo current_companyName(); ?> </strong>
                    </div>
                    <div
                        class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('hrms_reports_employee_leave_balance_report'); ?>
                        <!--Employee Leave Balance Report--></div>
                    <div class="text-center reportHeaderColor">
                        <strong><?php echo $leaveType . ' As of ' . $asOfDate ?></strong>
                    </div>
                </div>
                <?php } ?>
                <div class="col-md-12 bg-border">
                    <table id="tableID" class="borderSpace report-table-condensed">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('hrms_reports_empid'); ?><!--EmpID--></th>
                        <th><?php echo $this->lang->line('hrms_reports_employee_name'); ?><!--Employee Name--></th>
                        <th><?php echo $this->lang->line('hrms_reports_days_entitle'); ?><!--Days Entitle--></th>
                        <th><?php echo $this->lang->line('hrms_reports_days_taken'); ?><!--Days Taken--></th>
                        <th>Leave <?php echo $this->lang->line('hrms_reports_balance'); ?><!--Balance--></th>
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
                </div>
            </div>
            <br>
            <br>
            <br>
        <?php
        }
        else {  ?>
            <div class="col-md-12 xxcol-md-offset-2">
                <div class="alert alert-warning" role="alert">
                    <?php if ($error != '') {
                        echo $error;
                    } else { ?>
                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->.
                    <?php } ?>
                </div>
            </div>
        <?php
        }
        ?>


    <?php
        break;
    case 2:
        if ($leaveqry) {
            ?>
            <?php if ($isPDF != 'pdf') { ?>
            <div class="row" style="">
                <div class="col-md-12">
                    <?php echo export_buttons('tableID', 'Employee Leave Balance Report', TRUE, TRUE); ?>
                </div>
            </div>
            <?php } ?>
            <div class="row" id="tableID">
                <?php if ($isPDF != 'pdf') { ?>
                <div class="col-md-12" id="">
                    <div class="text-center reportHeaderColor">
                        <strong><?php echo current_companyName(); ?> </strong>
                    </div>
                    <div class="text-center reportHeader reportHeaderColor">
                        <?php echo $this->lang->line('hrms_reports_employee_leave_balance_report'); ?><!--Employee Leave Balance Report--></div>
                    <div class="text-center reportHeaderColor"><strong><?php echo ' As of ' . $asOfDate ?></strong></div>
                </div>
                <?php } ?>

                <div class="col-md-12">
                    <div class="table-responsive" style="overflow: scroll;height: 400px">
                        <table id="table" class="borderSpace report-table-condensed" style="">
                            <thead class="report-header">
                            <tr>
                                <th rowspan="2">
                                    <?php echo $this->lang->line('hrms_reports_employee_id'); ?><!--EmpID--></th>
                                <th rowspan="2">
                                    <?php echo $this->lang->line('hrms_reports_employee_name'); ?><!--Employee Name--></th>
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
                                        <th>
                                            <?php echo $this->lang->line('hrms_reports_employee_entitle'); ?><!--Entitle--></th>
                                        <th><?php echo $this->lang->line('hrms_reports_taken'); ?><!--Taken--></th>
                                        <th><?php echo $this->lang->line('hrms_reports_balance'); ?><!--Balance--></th>
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

                                            $desc = str_replace(' ', '', $value['leaveTypeID']);
                                            $balance = $desc . 'balance';
                                            $entitle = $desc . 'entitle';
                                            $taken = $desc . 'taken';

                                            if ($policyType == 2) {
                                                $val[$entitle] = timeFormat($val[$entitle]);
                                                $val[$taken] = timeFormat($val[$taken]);
                                                $val[$balance] = timeFormat($val[$balance]);

                                            }

                                            $thisVal =  $val[$entitle];
                                            $thisArr =  explode('.', $thisVal);
                                            if (is_array($thisArr) && isset($thisArr[1]) && $thisArr[1] == '00' && isset($thisArr[0])) {
                                                $thisVal = $thisArr[0];
                                            }
                                            echo "<td  style='text-align: center'>" . $thisVal . "</td>";

                                            $thisVal =  $val[$taken];
                                            $thisArr =  explode('.', $thisVal);
                                            if (is_array($thisArr) && isset($thisArr[1]) && $thisArr[1] == '00' && isset($thisArr[0])) {
                                                $thisVal = $thisArr[0];
                                            }
                                            echo "<td  style='text-align: center'>" . $thisVal . "</td>";

                                            $thisVal =  $val[$balance];
                                            $thisArr =  explode('.', $thisVal);
                                            if (is_array($thisArr) && isset($thisArr[1]) && $thisArr[1] == '00' && isset($thisArr[0])) {
                                                $thisVal = $thisArr[0];
                                            }
                                            echo "<td  style='text-align: center'>" . $thisVal . "</td>";

                                        }
                                    }
                                    ?>

                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <br>
        <?php
        }
        else {
        ?>
            <div class="col-md-12 xxcol-md-offset-2">
                <div class="alert alert-warning" role="alert">
                    <?php if ($error != '') {
                        echo $error;
                    } else { ?>
                        <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found.-->
                    <?php } ?>

                </div>

            </div>
        <?php
        }
        ?>
        <?php
    break;
}

function timeFormat($minutes) {

    if ($minutes < 0) {

        $num = -1 * $minutes;
        $hours = floor($num / 60);
        $min = $num - ($hours * 60);

        return '-' . $hours . "h:" . $min . "m";
    } else {
        $num = $minutes;
        $hours = floor($num / 60);
        $min = $num - ($hours * 60);

        return $hours . "h:" . $min . "m";
    }

}

if ($isPDF != 'pdf') {
    ?>
    <script>
        $('#table').tableHeadFixer({
            head: true,
            foot: true,
            left: 2,
            right: 0,
            'z-index': 10
        });

        function generateReportPdf() {

            var form = document.getElementById('formleave');
            form.target = '_blank';
            //form.action = '<?php echo site_url('Employee/employee_leave_balance_report_pdf'); ?>';
            form.action = '<?php echo site_url('Employee/employee_leave_balance_report/pdf/leave-balance-report'); ?>';
            form.submit();
        }
    </script>
    <?php
}
?>
