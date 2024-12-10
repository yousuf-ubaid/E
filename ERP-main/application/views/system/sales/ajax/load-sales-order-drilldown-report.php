<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                //echo export_buttons('salesOrderDrilldownReport', 'Sales Order Drilldown', True, True);
            } ?>
        </div>
    </div>
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
                        <th><?php echo $this->lang->line('common_customer_name'); ?><!--Customer Name--></th>
                        <th><?php echo $this->lang->line('common_document_code'); ?><!--Document Code--></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?><!--Document Date--></th>
                        <th><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                        <?php if ($amountType == 1) { ?>
                            <th>
                                <?php echo $this->lang->line('sales_markating_invoice_amount'); ?><!--Invoice Amount--></th>
                        <?php } else { ?>
                            <th>
                                <?php echo $this->lang->line('sales_markating_receipt_amount'); ?><!--Receipt Amount--></th>
                        <?php } ?>
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
                                <td width="200px"><?php echo $val["customerName"] ?></td>
                                <td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["autoID"] ?>)"><?php echo $val["documentCode"] ?></a></td>
                                <td><?php echo $val["documentDate"] ?></td>
                                <td><?php echo $val["transactionCurrency"] ?></td>
                                <td style="text-align: right"><?php echo number_format($val["transactionAmount"], $val["transactionCurrencyDecimalPlaces"]) ?></td>
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
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
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