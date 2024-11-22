<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$barcode = false;
$partNo = false;
if (isset($columnSelectionDrop)) {
    if (in_array("barcode", $columnSelectionDrop)) {
        $barcode = true;
    }
    if (in_array("partNo", $columnSelectionDrop)) {
        $partNo = true;
    }
}
if ($details) { ?>
    <div class="row" style="margin-top: 5px; margin-right: 1px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('tbl_rpt_item_movement', 'Item Movement Report', True, True);
            } ?>
        </div>
    </div>

    <div class="row" style="margin:1px;margin-top: 5px">
        <div class="col-md-12 " id="tbl_rpt_item_movement">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Item Movement Report</strong>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 10px ">
                    <strong><?php echo $this->lang->line('common_filter');?> <!--Filters--> <i class="fa fa-filter"></i></strong><br>
                    <strong><i><?php echo $this->lang->line('common_warehouse');?> <!--Warehouse-->:</i></strong> <?php echo join(",", $warehouse) ?>
                    
                </div>
            </div>
            <div style="">
                <div style="height: 600px">
                    <table id="tbl_rpt_itemmovement" class="borderSpace report-table-condensed" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th rowspan="2" style="width: 2%;">#</th>
                            <th rowspan="2" style="width: 5%;">Item Code</th>
                            <th rowspan="2" style="width: 10%;">Secondary Code</th>
                            <?php if($barcode){ ?>
                                <th rowspan="2" style="width: 10% ;">Barcode</th>
                            <?php } ?>
                            <?php if($partNo){ ?>
                                <th rowspan="2" style="width: 10% ;">Part No</th>
                            <?php } ?>
                            <th rowspan="2" style="width: 10% ;">Item Description</th>
                            <th rowspan="2" style="width: 3% ;">UOM</th>
                            <th rowspan="2" style="width: 5% ;">Opening Balance</th>
                            <th colspan="4" style="width: 2%;">Purchase</th>
                            <th rowspan="2" style="width: 5% ;">Sales</th>
                            <th rowspan="2" style="width: 5% ;">Return</th>
                            <th rowspan="2" style="width: 5% ;">Adjustment</th>
                            <th rowspan="2" style="width: 5% ;">Closing balance</th>
                        </tr>
                        <tr>
                            <th style="width: 6% ;">GRV</th>
                            <th style="width: 6% ;">BSI</th>
                            <th style="width: 6% ;">PV</th>
                            <th style="width: 7% ;">Total Purchase</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $a = 1;
                        if($details){
                            foreach ($details as $val){ ?>
                                <tr>
                                    <td style=""><?php echo $a  ?></td>
                                    <td><?php echo $val['itemSystemCode'] ?></td>
                                    <td><?php echo $val['seconeryItemCode'] ?></td>
                                    <?php if($barcode){ ?>
                                        <td><?php echo $val['barcode'] ?></td>
                                    <?php } ?>
                                    <?php if($partNo){ ?>
                                        <td><?php echo $val['partNo'] ?></td>
                                    <?php } ?>
                                    <td><?php echo $val['itemDescription'] ?></td>
                                    <td><?php echo $val['defaultUnitOfMeasure'] ?></td>
                                    <td class="text-right"><?php echo ($val['openingBalance']) ?></td>
                                    <td class="text-right"><?php echo ($val['GRV']) ?></td>
                                    <td class="text-right"><?php echo ($val['BSI']) ?></td>
                                    <td class="text-right"><?php echo ($val['PV']) ?></td>
                                    <?php $totalPurchase = $val['GRV'] + $val['BSI'] + $val['PV']; ?>
                                    <td class="text-right"><?php echo ($totalPurchase) ?></td>
                                    <td class="text-right"><?php echo ($val['SALES']) ?></td>
                                    <td class="text-right"><?php echo ($val['SLR']) ?></td>
                                    <td class="text-right"><?php echo ($val['adjustmentStock']) ?></td>
                                    <?php $totalclosingBalance = ((float)$val['openingBalance'] + $totalPurchase + (float)$val['SLR'] + (float)$val['adjustmentStock']) + (float)$val['SALES'];?>
                                    <td class="text-right"><?php echo number_format($totalclosingBalance, 2) ?></td>
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
    $('#tbl_rpt_itemmovement').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>