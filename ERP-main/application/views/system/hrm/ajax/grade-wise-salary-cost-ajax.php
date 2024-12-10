<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('hrms_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

if($is_print == 'Y'){ ?>
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
                                <h4> <?php echo $this->lang->line('hrms_reports_grade_wise_salary_cost_report');?></h4>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <hr>
<?php
}

if ($details) {
    if($is_print == 'N') { ?>
        <div class="row" style="margin-top: 5px">
            <div class="col-md-12">
                <?php echo export_buttons('export-container', 'Employee Grade-wise Salary Cost', True, True); ?>
            </div>
        </div>
    <?php } ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="export-container">
            <div class="hide"><?php echo $this->lang->line('common_company'); ?><!--Company-->
                - <?php echo current_companyName(); ?></div>

            <div style="height: 580px">
                <table id="rpt-table" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('hrms_reports_empid'); ?><!--EmpID--></th>
                        <th style="width: 200px"><?php echo $this->lang->line('common_employees'); ?><!--Employee--></th>
                        <th><?php echo $this->lang->line('common_grade'); ?><!--Grade--></th>
                        <th style="width: 150px">
                            <?php echo $this->lang->line('common_designation'); ?><!--Designation--></th>
                        <th style="width: 80px">
                            <?php echo $this->lang->line('common_segment'); ?><!--Segement--></th>
                        <th style="width: 50px">
                            <?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                        <?php if ($category) {
                            foreach ($category as $cat) {
                                ?>
                                <th><?php echo $cat['salaryDescription'] ?></th>
                                <?php
                            }
                        } ?>
                        <th><?php echo $this->lang->line('hrms_reports_net_salary'); ?><!--Net Salary--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($details) {
                        foreach ($currency as $cur) {

                            $x = 1;

                            foreach ($details as $val) {
                                if ($val['payCurrency'] == $cur['payCurrency']) {
                                    $x++;
                                    $amount = 0;
                                    if ($x == 2) {
                                        ?>
                                        <tr class=" bgc" style="background-color: #d7e4ff;">
                                            <td colspan="7">
                                                <strong><?php echo $this->lang->line('common_currency'); ?> - <?php echo $cur['payCurrency'] ?></strong>
                                            </td>
                                            <td colspan="<?php echo count($category) ?>">&nbsp;</td>
                                        </tr>
                                    <?php } ?>
                                    <tr class="hoverTr">
                                        <td><?php echo $val['ECode'] ?></td>
                                        <td><div style="width: 150px"><?php echo $val['Ename2'] ?></div></td>
                                        <td><?php echo $val['gradeDescription'] ?></td>
                                        <td><?php echo $val['DesDescription'] ?></td>
                                        <td><?php echo $val['segment'] ?></td>
                                        <td><?php echo $val['payCurrency'] ?></td>
                                        <?php if ($category) {

                                            foreach ($category as $key => $cat) {
                                                $salaryDescription = "cat_{$key}";
                                                $amount += $val[$salaryDescription];
                                                ?>
                                                <td style="text-align: right"><?php echo number_format($val[$salaryDescription], 2) ?></td>
                                                <?php

                                            }
                                        } ?>
                                        <td style="text-align: right">
                                            <strong><?php echo number_format($amount, 2) ?></strong>
                                        </td>
                                    </tr>

                                    <?php
                                }

                            }
                            ?>
                            <tr class="" style="background-color: #cacaca;">
                            <td colspan="6"><strong>
                                    <?php echo $this->lang->line('common_total'); ?><!--Total--></strong></td>
                            <?php
                            if ($category) {
                                $totalamount = 0;

                                foreach ($category as $key => $cat) {
                                    $salaryDescription = "cat_{$key}";
                                    $totalamount += $cur[$salaryDescription];
                                    ?>


                                    <td style="text-align: right">
                                        <strong><?php echo number_format($cur[$salaryDescription], 2) ?></strong>
                                    </td>

                                    <?php

                                }
                            }
                            ?>
                            <td style="text-align: right">
                                <strong><?php echo number_format($totalamount, 2) ?></strong>
                            </td> </tr><?php
                        }
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

   <?php }
    else{
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
    $('#rpt-table').tableHeadFixer({
        head: true,
        foot: true,
        left: 4,
        right: 0,
        'z-index': 10
    });

    function generateReportPdf() {
        var form = document.getElementById('frm-rpt');
        form.target = '_blank';
        form.action = '<?php echo site_url('Report/get_grade_wise_salary_cost_report/pdf/Grade-wise-salary-cost'); ?>';
        form.submit();
    }
</script>