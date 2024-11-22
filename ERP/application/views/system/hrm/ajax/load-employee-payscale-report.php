<?php if ($details) { ?>
<div class="row" style="margin-top: 5px">
    <div class="col-md-12">
    <?php
    $primaryLanguage = getPrimaryLanguage();
    $this->lang->load('hrms_reports', $primaryLanguage);
    $this->lang->load('common', $primaryLanguage);
    echo export_buttons('payscaleReport', 'Employee Pay Scale Report', True, True); ?>
        </div>
</div>
<div class="row" style="margin-top: 5px">
    <div class="col-md-12 " id="payscaleReport" >
        <div class="hide"><?php echo $this->lang->line('common_company');?><!--Company--> - <?php echo current_companyName(); ?></div>
        <div class="hide"><?php echo $this->lang->line('hrms_reports_pay_scale_report_as_of');?><!--Pay Scale Report As of--> <?php echo $asofDate ?></div>

<div style="height: 600px">
        <table id="frm_rpt_payScalex" class="borderSpace report-table-condensed" style="width: 100%">
            <thead class="report-header">
            <tr>
                <th><?php echo $this->lang->line('hrms_reports_empid');?><!--EmpID--></th>
                <th>Emp Secondary Code<!--Emp Secondary Code--></th>
                <th><?php echo $this->lang->line('common_employees');?><!--Employee--></th>
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
                                <tr class=" bgc" style="background-color: #d7e4ff;">
                                    <td colspan="6">
                                        <strong><?php echo $this->lang->line('common_currency'); ?> - <?php echo $cur['currency'] ?></strong>
                                    </td>
                                    <td colspan="<?php echo count($category) ?>">&nbsp;</td>
                                </tr>
                            <?php } ?>
                            <tr class="hoverTr">
                                <td><?php echo $val['ECode'] ?></td>
                                <td><?php echo $val['EmpSecondaryCode'] ?></td>
                                <td><div style="width: 150px"><?php echo $val['Ename2'] ?></div></td>
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
                    <tr class="" style="background-color: #cacaca;">
                    <td colspan="7"><strong><?php echo $this->lang->line('common_total');?><!--Total--></strong></td>
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
</div>

    </div>
</div>

<?php } else{
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found');?><!--No Records found-->.
            </div>
        </div>
    </div>

    <?php
}?>

<script>
    $('#frm_rpt_payScalex').tableHeadFixer({
        head: true,
        foot: true,
        left: 4,
        right: 0,
        'z-index': 10
    });

    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_payScale');
        form.target = '_blank';
        /*form.action = 'php echo site_url('template_paySheet/get_payScale_report_pdf'); ?>';*/
        form.action = '<?php echo site_url('Template_paysheet/get_payScale_report/pdf/Pay_Scale_Report'); ?>';
        form.submit();
    }
</script>