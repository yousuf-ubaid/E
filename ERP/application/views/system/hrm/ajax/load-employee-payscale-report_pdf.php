<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>


<div class="table-responsive">
    <table style="width: 100%">
        <tbody>
        <tr>
            <td style="width:50%;">
                <table>
                    <tr>
                        <td>
                            <img alt="Logo" style="height: 130px" src="<?php echo mPDFImage.$this->common_data['company_data']['company_logo']; ?>">
                        </td>
                    </tr>
                </table>
            </td>

            <td style="width:50%;">
                <table>
                    <tr>
                        <td colspan="3">
                            <h3><strong><?php echo $this->common_data['company_data']['company_name'].' ('.$this->common_data['company_data']['company_code'].').'; ?></strong></h3>
                            <h4> <?php echo $this->lang->line('hrms_reports_pay_scale_report');?><!--Pay Scale Report--></h4>
                        </td>


                    </tr>
                    <tr>
                        <td colspan="3"><?php echo $this->lang->line('common_as_of_date');?><!--As of Date-->  &nbsp; <?php echo $asofDate; ?></td>

                    </tr>

                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<hr>

<table id="" class="borderSpace report-table-condensed">
    <thead class="report-header">

            <tr>
            <th><?php echo $this->lang->line('hrms_reports_empid');?><!--EmpID--></th>
            <th>Emp Secondary Code<!--Emp Secondary Code--></th>
            <th><?php echo $this->lang->line('common_employee');?><!--Employee--></th>
            <th style="width: 150px"><?php echo $this->lang->line('common_designation');?><!--Designation--></th>
            <th style="min-width: 75px">Join Date<!--Join Date--></th>
            <th style="width: 80px"><?php echo $this->lang->line('common_segment');?><!--Segement--></th>
            <th style="width: 50px"><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
            <?php if ($category) {
                foreach ($category as $cat) {
                    ?>
                    <th><?php echo $cat['salaryDescription'] ?></th>
                    <?php
                }
            } ?>
            <th><?php echo $this->lang->line('hrms_reports_net_salary');?><!--Net Salary--></th>
</tr>
            </thead>
            <tbody>
            <?php if ($details) {

                foreach ($currency as $cur) {

                    $x = 1;

                    foreach ($details as $val) {
                        if ($val['transactionCurrency'] == $cur['currency']) {
                            $x++;
                            $amount = 0;
                            if ($x == 2) {
                                ?>
                                <tr style="background-color: #d7e4ff;" class="bgc">
                                    <td colspan="<?php echo count($category) + 6 ?>"><strong><?php echo $this->lang->line('common_currency');?><!--Currency-->
                                            - <?php echo $cur['currency'] ?></strong></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td><?php echo $val['ECode'] ?></td>
                                <td><?php echo $val['EmpSecondaryCode'] ?></td>
                                <td><?php echo $val['Ename2'] ?></td>
                                <td><?php echo $val['DesDescription'] ?></td>
                                <td><?php echo $val['EDOJ'] ?></td>
                                <td><?php echo $val['segment'] ?></td>
                                <td><?php echo $val['transactionCurrency'] ?></td>
                                <?php if ($category) {

                                    foreach ($category as $cat) {
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
                    if ($category) {
                        $totalamount = 0;

                        foreach ($category as $cat) {
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
                    </td> </tr><?php
                }
            } ?>
            </tbody>
        </table>

