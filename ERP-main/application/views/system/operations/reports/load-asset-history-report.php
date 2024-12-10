<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('assetHisReport', 'Asset History Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="assetHisReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Asset History Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>Asset Code</th>
                        <th>Asset Name</th>
                        <th>Call Off</th>
                        <th>Job No</th>
                        <th>Length</th>
                        <th>Start Date</th>
                        <th>End Date</th>

                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    foreach ($details as $val) {
                        if(!empty($details)){
                            ?>
                            <tr>
                                <td><?php echo $val['faCode']; ?></td>
                                <td><?php echo $val['assetDescription']; ?></td>
                                <td><?php echo $val['callof']; ?></td>
                                <td style="text-align: center"><?php echo $val['ticketNo']; ?></td>
                                <td style="text-align: right"><?php echo $val['length']; ?></td>
                                <td><?php echo $val['startdate']; ?></td>
                                <td><?php echo $val['endate']; ?></td>
                            </tr>
                            <?php
                        }else{
                            ?>
                            <tr>
                                <td colspan="7">No Records Found</td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
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
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
