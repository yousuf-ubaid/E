<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

//echo $this->db->last_query().' </br> -----';
//echo '<pre>'; print_r($currency); echo '</pre>';
?>
<div style="">
    <div class="col-md-12">
        <?php echo export_buttons('pay-slip-report', 'Sponsor Wise Salary', True, false); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <?php if ($detail) { ?>
            <div class="bg-border" style="height: 400px">
                <table id="pay-slip-report" class="borderSpace report-table-condensed">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('hrms_empid')?><!--EmpID--></th>
                        <th><?php echo $this->lang->line('common_employee')?> <!--Employee--></th>

                        <th> <?php echo $this->lang->line('common_currency')?> <!--Currency--></th>
                        <th> Location </th>
                        <th> Sponsor </th>
                        <?php if ($categorysalary) {
                            foreach ($categorysalary as $cat) {
                                ?>
                                <th><?php echo $cat['salaryDescription'] ?></th>
                                <?php
                            }
                        } ?>

                        <th><?php echo $this->lang->line('common_total')?> <!--Total--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($detail) {

                        foreach ($currency as $cur) {

                            $x = 1;

                            foreach ($detail as $val) {
                                if ($val['curr'] == $cur['currency']) {
                                    $x++;
                                    $amount = 0;
                                    if ($x == 2) {
                                        ?>
                                        <tr>
                                            <td colspan="10"><span style="font-weight: bold"><?php echo $cur['currency'] ?></span></strong></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td><?php echo $val['ECode'] ?></td>
                                        <td><?php echo $val['Ename2'] ?></td>
                                        <td><?php echo $val['transactionCurrency'] ?></td>
                                        <td><?php echo $val['floorDescription'] ?></td>
                                        <td><?php echo $val['sponsorName'] ?></td>
                                        <?php if ($categorysalary) {

                                            foreach ($categorysalary as $cat) {
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
                            if ($categorysalary) {
                                $totalamount = 0;

                                foreach ($categorysalary as $cat) {
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