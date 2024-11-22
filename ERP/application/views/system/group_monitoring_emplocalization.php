<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('dashboard_groupmonitoring_lang', $primaryLanguage);

if (!empty($outputdrilldown)) {
    ?>

    <!--<div class="row" style="margin-top: 5px">

        <div class="col-md-6">
            <?php /*if (!empty($customers)) { */ ?>
                <div style="font-size: 12px;"><strong>Customer Name</strong> : <?php /*echo $customers['customerName'] */ ?> </div>
            <?php /*}*/ ?>
        </div>
    </div>-->
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">

            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div><br>
            <!--<div class="reportHeader reportHeaderColor" style="">
                <span style="font-size: 12px; color: black; font-weight: normal;" class="pull-left">Customer Name</strong> : <?php /*echo $customers['customerName'] */ ?></span> <span style="    padding-left: 22%;"><strong>Collection Summary Drill Down</strong></span></div>
            <div style="">-->


            <table class="borderSpace report-table-condensed" id="tbl_report">
               <!-- <h5><strong>Income Statement </strong></h5>-->
                <thead class="report-header">
                <tr>
                    <th><?php echo $this->lang->line('dashboard_companyname') ?></th>
                    <th><?php echo $this->lang->line('dashboard_localemployees') ?></th>
                    <th><?php echo $this->lang->line('dashboard_expatriates') ?></th>
                    <th><?php echo $this->lang->line('dashboard_totalemployees') ?></th>
                    <th><?php echo $this->lang->line('dashboard_localization_c') ?>(%)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $empcounttot = 0;
                $expcounnt = 0;
                $totalemployees = 0;
                $totalemployeesTotal = 0;
                foreach ($outputdrilldown['empcount'] as $val) {
                    $empcounttot += $val['localemp'];
                    $expcounnt += $val['expemp'];
                    $totalemployeesTotal += ($val['localemp'] + $val['expemp']);

                    ?>
                <tr>

                    <td><?php echo $val['compname']?></td>
                    <td style="text-align: right;"><?php echo $val['localemp']?></td>
                    <td style="text-align: right;"><?php echo $val['expemp']?></td>
                    <td style="text-align: right;"><?php echo ($val['localemp'] + $val['expemp'])?></td>
                    <td style="text-align: right;"><?php echo $val['localization']?></td>

                </tr>

                <?php }?>
                </tbody>

                <tfoot>

                <tr>
                    <td class='reporttotalblack'><?php echo $this->lang->line('dashboard_total') ?></td>
                    <td class='reporttotalblack text-right'><?php echo $empcounttot?></td>
                    <td class='reporttotalblack text-right'><?php echo $expcounnt?></td>
                    <td class='reporttotalblack text-right'><?php echo $totalemployeesTotal?></td>
                    <td class='reporttotalblack text-right'><?php echo number_format((($empcounttot)/$totalemployeesTotal)*100,2) ?></td>
                </tr>
                </tfoot>
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
<script>

    $('#tbl_rpt_salesorder').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>