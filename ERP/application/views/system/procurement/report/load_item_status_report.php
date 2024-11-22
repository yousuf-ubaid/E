<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($details) { ?>
    <div class="row" style="margin-top: 5px; margin-right: 1px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('tbl_rpt_item_status', 'PO Item Status Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>PO Item Status Report</strong>
            </div>
        </div>
    </div>
    <div class="row" style="margin:1px;margin-top: 5px">
        <div class="col-md-12 " id="tbl_rpt_item_status">
            <div style="height: 400px; ">
                <table id="tbl_rpt_item_status" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                            <tr>
                                <th style="width: 2%;">#</th>
                                <th style="width: 5%;">PO Code</th>
                                <th style="width: 8%;">PO Date</th>
                                <th style="width: 5%;">Item Code</th>
                                <th style="width: 10%;">Secondary Code</th>
                                <th style="width: 14% ;">Item Description</th>
                                <th style="width: 3% ;">UOM</th>
                                <th style="width: 5% ;">Currency</th>
                                <th style="width: 5% ;">Transaction Amount</th>
                                <th style="width: 2%;">Requested Qty</th>
                                <th style="width: 5% ;">Received Qty</th>
                                <th style="width: 5% ;">Balance Qty</th>
                                <th style="width: 5% ;">Status</th>
                            </tr>

                    </thead>
                    <tbody>
                        <?php
                        $a = 1;
                        if($details){
                        foreach ($details as $val){ ?>
                            <tr>
                                <td style=""><?php echo $a  ?></td>
                                <td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal('PO',<?php echo $val["purchaseOrderID"] ?>)"><?php echo $val['purchaseOrderCode'] ?></a></td>
                                <td><?php echo $val['documentDate'] ?></td>
                                <td><?php echo $val['itemSystemCode'] ?></td>
                                <td><?php echo $val['seconeryItemCode'] ?></td>
                                <td><?php echo $val['itemDescription'] ?></td>
                                <td><?php echo $val['unitOfMeasure'] ?></td>
                                <td><?php echo $val['transactionCurrency'] ?></td>
                                <td class="text-right"><?php echo number_format($val['totalAmount'], $val['transactionCurrencyDecimalPlaces']) ?></td>
                                <td class="text-right"><?php echo number_format($val['requestedQty']) ?></td>
                                <td class="text-right"><?php echo number_format($val['prQty']) ?></td>
                                <td class="text-right"><?php echo number_format($val['balanceQty']) ?></td>
                                <td class="text-right"><?php
                                    if($val['prQty'] == 0) {
                                        echo '<span class="label label-danger"> Not Received </span>';
                                    } elseif($val['balanceQty'] == 0) {
                                        echo '<span class="label label-success"> Fully Received </span>';
                                    } else {
                                        echo '<span class="label label-warning"> Partially Received </span>';
                                    }
                                    ?></td>
                            </tr>
                            <?php
                            $a++;
                        } ?>
                        </tbody>
                        <?php } ?>
                    </table>
                </div>
        </div>
    </div>
<?php } else {
    ?>
    <br>
    <div class="row" style="margin: 5px">
        <div class="col-md-12 xxcol-md-offset-2">
            <div class="alert alert-warning" role="alert">
                <?php echo $this->lang->line('common_no_records_found'); ?><!--No Records found-->
            </div>
        </div>
    </div>

    <?php
} ?>
<script>
    $('#tbl_rpt_item_status').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });
</script>