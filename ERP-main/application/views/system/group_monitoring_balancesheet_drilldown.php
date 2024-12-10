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
                <strong><?php echo current_companyName(); ?></strong></div>
            <!--<div class="reportHeader reportHeaderColor" style="">
                <span style="font-size: 12px; color: black; font-weight: normal;" class="pull-left">Customer Name</strong> : <?php /*echo $customers['customerName'] */ ?></span> <span style="    padding-left: 22%;"><strong>Collection Summary Drill Down</strong></span></div>
            <div style="">-->


            <table class="borderSpace report-table-condensed" id="tbl_report">
               <!-- <h5><strong>Balance Sheet extract</strong></h5>-->
                <thead class="report-header">
                <tr>
                    <th style="width: 35%;"><?php echo $this->lang->line('dashboard_companyname') ?></th>
                    <?php
                    $date = range(($date - 2), $date);
                    echo '<th>' . $date[2] . '</th> ';
                    echo '<th>' . $date[1] . '</th> ';
                    echo '<th>' . $date[0] . '</th> ';
                    ?>

                </tr>
                </thead>
                <?php

                if ($outputdrilldown) {
                    $totaldate1 = 0;
                    $totaldate2 = 0;
                    $totaldate3 = 0;
                    foreach ($outputdrilldown as $val) {

                        echo '<tr>';
                        echo '<td>' . $val['companyname'] . '</a></td>';
                        echo '<td style="text-align: right;">' . round($val[$date[2]]) . '</td>';
                        echo '<td style="text-align: right;">' . round($val[$date[1]]) . '</td>';
                        echo '<td style="text-align: right;">' . round($val[$date[0]]) . '</td>';


                        ?>
                        <?php
                        echo '</tr>';
                        $totaldate1 += $val[$date[2]];
                        $totaldate2 += $val[$date[1]];
                        $totaldate3 += $val[$date[0]];
                    }
                    echo "<tr><td class='reporttotalblack'>". $this->lang->line('dashboard_total')."</td>";
                    echo "<td class='reporttotalblack text-right'>" . round($totaldate1) . "</td>";
                    echo "<td class='reporttotalblack text-right'>" . round($totaldate2) . "</td>";
                    echo "<td class='reporttotalblack text-right'>" . round($totaldate3) . "</td>";

                } ?>
                <tfoot>
                <tr>
                    <td colspan="3"></td>
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