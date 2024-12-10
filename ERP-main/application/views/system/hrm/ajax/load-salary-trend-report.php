<?php if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            $primaryLanguage = getPrimaryLanguage();
            $this->lang->load('hrms_reports', $primaryLanguage);
            $this->lang->load('common', $primaryLanguage);
            if ($type == 'html') {
                echo export_buttons('salaryTrendReport', 'Salary Trend Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salaryTrendReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong><?php echo $this->lang->line('hrms_salary_trend_report'); ?></strong></div>
            <div style="height: 600px">
                <table id="tbl_rpt_salarytrend" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_description'); ?><!--Description--></th>
                        <?php foreach ($months as $val) {
                            ?>
                            <th><?php echo $val; ?></th>
                            <?php
                        } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $details = array_group_by($details, 'payrollYear');
                    if ($details) {
                        foreach ($details as $key => $value) {
                            $subTotal = array();
                            ?>
                            <tr>
                                <td colspan="13"><strong><?php echo $key ?></strong></td>
                            </tr>
                            <?php foreach ($value as $val) { ?>
                                <tr class="hoverTr">
                                    <td><?php echo $val["description"] ?></td>
                                    <?php foreach ($months as $month) {
                                        $subTotal[$month][] = $val[$month]
                                        ?>
                                        <td style="text-align: right" ><?php echo number_format($val[$month]) ?></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                            <!--<tr>
                                <td><strong>Total</strong></td>
                                <?php /*foreach ($months as $month) { */?>
                                    <td style="text-align: right" class="reportsubtotal"><?php /*echo number_format(array_sum($subTotal[$month])) */?></td>
                                <?php /*} */?>
                            </tr>-->
                            <?php
                        }
                    } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->.
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_salarytrend').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_salary_trend');
        form.target = '_blank';
        form.action = '<?php echo site_url('Template_paysheet/get_salary_trend_report_pdf'); ?>';
        form.submit();
    }
</script>