<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if ($sales_movement || $purchase_movement || $transfers_movement) { ?>

    <div class="row" style="margin-top: 5px; margin-right: 1px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('tbl_rpt_item_movement', 'Item Movement Analysis', True, True);
            } ?>
        </div>
    </div>


    <div class="row" style="margin:1px;margin-top: 5px">
        <div class="col-md-12 " id="tbl_rpt_item_movement">
            <div style="text-align: center">
                <div class="reportHeader reportHeaderColor" style="">
                    <strong><?php echo current_companyName(); ?></strong></div>
                <div class="reportHeaderColor" style="">
                    Item Movement Analysis
                </div>
                <div class="reportHeaderColor" style="">
                    <?php echo $datefrom; ?> to <?php echo $dateto; ?>
                </div>
            </div>
            <div style="">
                <div style="height: 600px">
                    <table id="tbl_rpt_itemmovement" class="borderSpace report-table-condensed" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th rowspan="2" style="width:50%;">Particulars</th>
                            <th colspan="3"><?php echo $details->itemName; ?> (<?php echo $details->itemSystemCode; ?>) (<?php echo $details->UnitDes; ?>) (<?php echo $currency; ?>) (Wac Amount: <?php echo $item_wac_amount; ?>)</th>
                        </tr>
                        <tr>
                            <th>Quantity</th>
<!--                            <th>Basic Rate</th>-->
                            <th>Effective Rate</th>
                            <th>Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="background-color: #ddd;">
                                <div class="sub-headers">Movement Inward</div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="background-color: #ddd;">Suppliers:</td>
                            <td></td>
                        </tr>
                        <?php if (empty($purchase_movement)) { ?>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                        <?php } ?>
                        <?php
                        $purchase_qty_total = 0;
                        $purchase_val_total = 0;
                        foreach ($purchase_movement as $item) {
                            $purchase_qty_total += $item['transctionQty'];
                            $purchase_val_total += $item['purchaseamount'];
                            ?>
                            <tr>
                                <td><?php echo $item['supplierName']; ?></td>
                                <td style="text-align: right"><?php echo $item['transctionQty']; ?></td>
<!--                                <td style="text-align: right">--><?php //echo number_format($item['basicRate'],2); ?><!--</td>-->
                                <td style="text-align: right"><?php echo number_format($item['avgprice'],$company_default_decimal); ?></td>
                                <td style="text-align: right"><?php echo number_format($item['purchaseamount'],$company_default_decimal); ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td style="font-weight: bolder">Total</td>
                            <td style="text-align: right;font-weight: bold;border-top: thin black solid;border-bottom: thin black solid;"><?php echo number_format($purchase_qty_total,$company_default_decimal); ?></td>
                            <td></td>
                            <td style="text-align: right;font-weight: bold;border-top: thin black solid;border-bottom: thin black solid;"><?php echo number_format($purchase_val_total,$company_default_decimal); ?></td>
                        </tr>
                        <tr>
                            <td style="background-color: #ddd;">
                                <div class="sub-headers">Movement Outward</div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="background-color: #ddd;">Buyers:</td>
                            <td></td>
                        </tr>
                        <?php if (empty($sales_movement)) { ?>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                        <?php } ?>
                        <?php
                        $sales_qty_total = 0;
                        $sales_val_total = 0;
                        foreach ($sales_movement as $item) {
                            $sales_qty_total += $item['transctionQty'];
                            $sales_val_total += $item['salesAmount'];
                            ?>
                            <tr>
                                <td><?php echo $item['customerName']; ?></td>
                                <td style="text-align: right"><?php echo $item['transctionQty']; ?></td>
<!--                                <td style="text-align: right">--><?php //echo number_format($item['basicRate'],2); ?><!--</td>-->
                                <td style="text-align: right"><?php echo number_format($item['avgprice'],$company_default_decimal); ?></td>
                                <td style="text-align: right"><?php echo number_format($item['salesAmount'],$company_default_decimal); ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td style="font-weight: bolder">Total</td>
                            <td style="text-align: right;font-weight: bold;border-top: thin black solid;border-bottom: thin black solid;"><?php echo number_format($sales_qty_total,$company_default_decimal); ?></td>
                            <td></td>
                            <td style="text-align: right;font-weight: bold;border-top: thin black solid;border-bottom: thin black solid;"><?php echo number_format($sales_val_total,$company_default_decimal); ?></td>
                        </tr>
                        <tr>
                            <td style="background-color: #ddd;">
                                <div class="sub-headers">Transfers Outward (Consumption)</div>
                            </td>
                            <td></td>
                        </tr>
                        <?php if (empty($transfers_movement)) { ?>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                        <?php } ?>
                        <?php
                        $transfers_qty_total = 0;
                        $transfers_val_total = 0;
                        foreach ($transfers_movement as $item) {
                            $transfers_qty_total += $item['transctionQty'];
                            $transfers_val_total += $item['purchaseamount'];
                            ?>
                            <tr>
                                <td><?php
                                    if ($item['documentID'] == 'SA') {
                                        echo 'Stock adjustment ';
                                    } else if ($item['documentID'] == 'SCNT') {
                                        echo 'Stock counting ';
                                    } else if ($item['documentID'] == 'MI') {
                                        echo 'Material Issue ';
                                    }
                                    ?></td>
                                <td style="text-align: right"><?php echo $item['transctionQty']; ?></td>
<!--                                <td style="text-align: right">--><?php //echo number_format($item['basicRate'],2); ?><!--</td>-->
                                <td style="text-align: right"><?php echo number_format($item['avgprice'],$company_default_decimal); ?></td>
                                <td style="text-align: right"><?php echo number_format($item['purchaseamount'],$company_default_decimal); ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td style="font-weight: bolder">Total</td>
                            <td style="text-align: right;font-weight: bold;border-top: thin black solid;border-bottom: thin black solid;"><?php echo number_format($transfers_qty_total,$company_default_decimal); ?></td>
                            <td></td>
                            <td style="text-align: right;font-weight: bold;border-top: thin black solid;border-bottom: thin black solid;"><?php echo number_format($transfers_val_total,$company_default_decimal); ?></td>
                        </tr>
                        </tbody>
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