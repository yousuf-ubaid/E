<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($extra['details']) { ?>
    <div class="row" style="margin-top: 5px">
    </div>
    <div class="row" style="margin-top: 5px;">
        <div class="col-md-12 " id="tbl_rpt_supplierDeliveryAnalysis">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Supplier Deivery Analysis Drilldown - <?php echo $extra['master']['supplierSystemCode'] . ' | ' . $extra['master']['supplierName']; ?></strong></div>

            <div style="">
                <table id="tbl_rpt_toptencustomers" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>Document Code</th>
                        <th>Document Date</th>
                        <th>Expected Delivery Date</th>
                        <th>Max Delivered Date</th>
                        <th>Days</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total = 0;
                    $decimalPlaces = 0;
                    if ($extra['details']) {
                        foreach ($extra['details'] as $val) {
                            ?>
                            <tr>
                                <td width="200px"><a href="#" class="drill-down-cursor"
                                                     onclick="documentPageView_modal('PO',<?php echo $val["purchaseOrderID"] ?>)"><?php echo $val["purchaseOrderCode"] ?></a></td>
                                <td><?php echo $val["documentDate"] ?></td>
                                <td><?php echo $val["expectedDeliveryDate"] ?></td>
                                <td><?php echo $val["receivedDate"] ?></td>
                                <td><?php echo $val["DateDiff"] ?></td>
                            </tr>
                            <?php
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
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
}
}?>
<script>
    $('#tbl_rpt_supplierDeliveryAnalysis').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>