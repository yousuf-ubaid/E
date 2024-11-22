<?php if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            $primaryLanguage = getPrimaryLanguage();
            $this->lang->load('hrms_reports', $primaryLanguage);
            $this->lang->load('common', $primaryLanguage);
            if ($type == 'html') {
                echo export_buttons('localizationReport', 'Localization Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="localizationReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong><?php echo $this->lang->line('hrms_localization_report'); ?></strong></div>
            <div style="height: 600px">
                <table id="tbl_rpt_localization" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_department'); ?><!--Department--></th>
                        <th><?php echo $this->lang->line('hrms_local_employees'); ?><!--Local Employees--></th>
                        <th>
                            <?php echo $this->lang->line('hrms_expatriates'); ?><!--Expatriates--></th>
                        <th>
                            <?php echo $this->lang->line('hrms_total_employees'); ?><!--Total Employees--></th>
                        <th>
                            <?php echo $this->lang->line('hrms_localization'); ?>(%)<!--Localization (%)--></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $localEmployee = 0;
                    $expatriateEmployee = 0;
                    $totalEmployee = 0;
                    if ($details) {
                        foreach ($details as $val) {
                            $localEmployee += $val['localEmployee'];
                            $expatriateEmployee += $val['expatriateEmployee'];
                            $totalEmployee += $val['totalEmployee'];
                            ?>
                            <tr class="hoverTr">
                                <td><?php echo $val['description'] ?></td>
                                <td style="text-align: right"><?php echo $val['localEmployee'] ?></td>
                                <td style="text-align: right"><?php echo $val['expatriateEmployee'] ?></td>
                                <td style="text-align: right"><?php echo $val['totalEmployee'] ?></td>
                                <td style="text-align: right"><?php echo $val['localization'] ?></td>
                            </tr>
                            <?php
                        }
                    } ?>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td style="text-align: right" class="reportsubtotal"><?php echo $localEmployee ?></td>
                        <td style="text-align: right" class="reportsubtotal"><?php echo $expatriateEmployee ?></td>
                        <td style="text-align: right" class="reportsubtotal"><?php echo $totalEmployee ?></td>
                        <td style="text-align: right" class="reportsubtotal"><?php echo round(($localEmployee/$totalEmployee)*100) ?></td>
                    </tr>
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
    $('#tbl_rpt_localization').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
    function generateReportPdf() {
        var form = document.getElementById('frm_rpt_localization');
        form.target = '_blank';
        form.action = '<?php echo site_url('Template_paysheet/get_localization_report_pdf'); ?>';
        form.submit();
    }
</script>