<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>
<div style="">
    <div class="col-md-12">
        <?php echo export_buttons('salary_process_report', 'Salary Process Report', True, false); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <?php if ($extra['details']) { ?>
            <div class="bg-border" style="height: 400px">
                <table id="salary_process_report" class="borderSpace report-table-condensed">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('hrms_empid')?><!--EmpID--></th>
                        <th><?php echo $this->lang->line('common_employee')?> <!--Employee--></th>

                        <th> <?php echo $this->lang->line('common_currency')?> <!--Currency--></th>
                        <th> Department </th>
                        <th> Segment </th>
                        <?php if ($extra['categorysalary']) {
                            foreach ($extra['categorysalary'] as $cat) {
                                ?>
                                <th><?php echo $cat['salaryDescription'] ?></th>
                                <?php
                            }
                        } ?>

                        <th><?php echo $this->lang->line('common_total')?> <!--Total--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($extra['details']) {

                        foreach ($extra['currency'] as $cur) {
                            $x = 1;

                            foreach ($extra['details'] as $val) {
                                if ($val['currency'] == $cur['currency']) {
                                    $x++;
                                    $amount = 0;
                                    if ($x == 2) {
                                        ?>
                                        <tr>
                                               <td colspan="5"><span style="font-weight: bold"><?php echo $cur['currency'] ?></span></strong></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td><?php echo $val['ECode'] ?></td>
                                        <td><?php echo $val['Ename2'] ?></td>
                                        <td><?php echo $val['transactionCurrency'] ?></td>
                                        <td><?php echo $val['DepartmentDes'] ?></td>
                                        <td><?php echo $val['segmentCode'] ?></td>
                                        <?php if ($extra['categorysalary']) {

                                            foreach ($extra['categorysalary'] as $cat) {
                                                $salaryDescription = str_replace(' ', '', $cat['salaryDescription']);
                                                $salaryDescription = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription);
                                                $amount += $val[$salaryDescription];
                                                ?>
                                                <td style="text-align: right"><?php echo number_format($val[$salaryDescription], 2) ?></td>
                                                <?php

                                            }
                                        } ?>
                                        <td style="text-align: right"><strong><?php echo number_format($amount, 2) ?></strong>
                                        </td>

                                    </tr>


                                    <?php


                                }

                            }
                            ?>
                            <tr style="background-color: #cacaca;">
                            <td colspan="5"><strong><?php echo $this->lang->line('common_total');?><!--Total--></strong></td>
                            <?php
                            if ($extra['categorysalary']) {
                                $totalamount = 0;

                                foreach ($extra['categorysalary'] as $cat) {
                                    $salaryDescription = str_replace(' ', '', $cat['salaryDescription']);
                                    $salaryDescription = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription);
                                    $totalamount += $cur[$salaryDescription];
                                    ?>


                                    <td style="text-align: right">
                                        <strong><?php echo number_format($cur[$salaryDescription], 2) ?></strong></td>

                                    <?php

                                }
                            }
                            ?>
                            <td style="text-align: right"><strong><?php echo number_format($totalamount, 2) ?></strong>
                            </td>
                            <td style="text-align: right"><strong> </strong>
                            </td>
                            </tr><?php
                        }
                    } ?>
                    </tbody>


                </table>
            </div>
        <?php } else {
            ?>
            <div class="row">
                <div class="col-md-12 xxcol-md-offset-2">
                    <div class="alert alert-warning" role="alert">
                        <?php echo $this->lang->line('common_no_records_found')?>.
                        <!--No Records found-->

                    </div>
                </div>
            </div>
            <?php
        } ?>
    </div>
</div>