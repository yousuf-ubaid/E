<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_attendance_lang', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>
<style>
    .download-btn:hover{
        cursor: pointer;
    }
</style>
<div style="">
    <div class="col-md-12">
    <a href="#" onclick="generateReportPdf()"
       style="" class="pull-right">
        <i style="font-size:20px;background-color: #CF000A;color: white"
           class="fa fa-file-pdf-o" aria-hidden="true"></i><?php echo $this->lang->line('hrms_downloadpdf')?> <!--Download PDF-->
    </a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php if ($detail) { ?>
        <div class="bg-border" style="height: 400px">
            <table id="pay-slip-report" class="borderSpace report-table-condensed">
                <thead class="report-header">
                <tr>
                    <th>#</th>
                    <th><?php echo $this->lang->line('hrms_empid')?><!--EmpID--></th>
                    <th><?php echo $this->lang->line('common_employee')?> <!--Employee--></th>
                    <th style=""> <?php echo $this->lang->line('common_currency')?> <!--Currency--></th>
                    <th style=""> <?php echo $this->lang->line('common_addition')?> <!--Addition--></th>
                    <th style=""> <?php echo $this->lang->line('common_deduction')?> <!--Deduction--></th>
                    <th><?php echo $this->lang->line('common_total')?> <!--Total--></th>
                    <th style="z-index: 10;"> <?php echo $this->lang->line('hrms_downloadpayslip')?> <!--Download Payslip--></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $m = 1;
                foreach ($currency as $cur) {
                    $x = 1;
                    foreach ($detail as $val) {
                        if ($val['transactionCurrency'] == $cur['currency']) {
                            $x++;
                            $amount = 0;
                            if ($x == 2) {
                                ?>
                                <tr style="background-color: #d7e4ff;" class="bgc">
                                    <td colspan="8"><strong><?php echo $this->lang->line('common_currency')?> <!--Currency-->
                                            - <?php echo $cur['currency'] ?></strong></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td><?php echo $m; ?></td>
                                <td><?php echo $val['ECode'] ?></td>
                                <td><?php echo $val['Ename2'] ?></td>
                                <td><?php echo $val['transactionCurrency'] ?></td>
                                <td style="text-align: right"><?php echo number_format($val['addition'], 2) ?></td>
                                <td style="text-align: right"><?php echo number_format($val['deduction'], 2) ?></td>


                                <td style="text-align: right">
                                    <strong><?php echo number_format($val['total'], 2) ?></strong>
                                <td  style="text-align: center">
                                    <?php
                                    $isNonPayroll = ($this->input->post('isNonPayroll'))?'Y':'N';
                                    $uri = $val['payrollMasterID'].','.$val['EIdNo'].',\''.$isNonPayroll.'\',\''.$val['ECode'].'\'';
                                    ?>
                                    <a onclick="print_payslip(<?=$uri?>)" style="" class="download-btn">
                                        <i style="font-size:20px;background-color: #CF000A;color: white"
                                       class="fa fa-file-pdf-o" aria-hidden="true"></i> <!--Download PDF--><?php echo $this->lang->line('hrms_downloadpdf')?>
                                    </a>
                                </td>
                            </tr>

                            <?php
                            $m++;
                        }
                    }
                    ?>
                    <tr style="background-color: #cacaca">
                        <td colspan="4"><strong><?php echo $this->lang->line('common_grand_total')?><!--Grand Total--></strong></td>
                        <td style="text-align: right"><strong><?php echo number_format($cur['totaladdition'], 2) ?></strong>
                        </td>
                        <td style="text-align: right">
                            <strong><?php echo number_format($cur['totaldeduction'], 2) ?></strong>
                        </td>
                        <td style="text-align: right"><strong><?php echo number_format($cur['totalamount'], 2) ?></strong>
                        </td>
                        <td></td>
                    </tr><?php
                }
                ?>
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
