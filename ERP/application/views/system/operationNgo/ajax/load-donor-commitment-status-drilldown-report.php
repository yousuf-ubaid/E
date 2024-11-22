<?php
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderDrilldownReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong><?php echo $this->lang->line('sales_markating_sales_order_report'); ?></strong></div>
            <div style="">
                <table id="tbl_rpt_salesorderdrilldown" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>Donor</th>
                        <th>Document Code</th>
                        <th>Document Date</th>
                        <th>Currency</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total = 0;
                    $decimalPlaces = 0;
                    if ($details) {
                        foreach ($details as $val) {
                            $total += $val["transactionAmount"];
                            $decimalPlaces = $val["transactionCurrencyDecimalPlaces"];
                            ?>
                            <tr>
                                <td><?php echo $val["donorName"] ?></td>
                                <td><a href="#" onclick="documentPageView_modal('<?php echo $code; ?>',<?php echo $val['autoID'];?>)" ><?php echo $val["documentSystemCode"] ?></a></td>
                                <td><?php echo $val["documentDate"] ?></td>
                                <td><?php echo $val["transactionCurrency"] ?></td>
                                <td style="text-align: right"><?php echo number_format($val["transactionAmount"]) ?></td>
                            </tr>
                            <?php
                        }
                    } ?>
                    <tr>
                        <td colspan="4"><b><?php echo $this->lang->line('common_total'); ?></b></td>
                        <td class="text-right reporttotal"><?php echo number_format($total,$decimalPlaces); ?></td>
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
            <div class="alert alert-warning" role="alert">No Records Found !</div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_salesorderdrilldown').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>