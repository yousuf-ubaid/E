<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('sales_marketing_reports', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$barcode = false;
$partNo = false;
$colspan=9;
if (isset($columnSelectionDrop)) {
     if (in_array("barcode", $columnSelectionDrop)) {
        $barcode = true;
        $colspan +=1;
    }
    if (in_array("partNo", $columnSelectionDrop)) {
        $partNo = true;
        $colspan +=1;

    }
}
if ($details) { ?>
    <div class="row" style="margin-top: 5px; margin-right: 1px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('tbl_rpt_free_of_cost', 'FOC Report', True, True);
            } ?>
        </div>
    </div>

    <div class="row" style="margin:1px;margin-top: 5px">
        <div class="col-md-12 " id="tbl_rpt_free_of_cost">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>FOC Report</strong></div>
            <div style="">
                <div style="height: 600px">
                    <table id="tbl_rpt_itemreceivedhistory" class="borderSpace report-table-condensed" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th style="width: 2%;">#</th>
                            <th style="width: 5%;">Item Code</th>
                            <th style="width: 10%;">Secondary Code</th>
                            <?php if($barcode){ ?>
                                <th style="width: 10% ;">Barcode</th>
                            <?php } ?>
                            <?php if($partNo){ ?>
                                <th style="width: 10% ;">Part No</th>
                            <?php } ?>
                            <th style="width: 10% ;">Item Description</th>
                            <th style="width: 10% ;">Document Code</th>
                            <th style="width: 10% ;">Document Date</th>

                            <th style="width: 5% ;">UOM</th>
                            <th style="width: 5% ;">Qty</th>
                            <th style="width: 5% ;">Currency</th>
                            <th style="width: 5% ;">Unit Cost</th>
                            <th style="width: 5% ;">Total Cost</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $a = 1;

                        if($details){
                            $grandTotal = array();
                            if($currency == 'transaction') {
                                foreach ($details as $curr) {
                                    $category[$curr[$currency . 'Currency']][] = $curr;
                                }
                                foreach ($category as $currencyArr) {
                                    foreach ($currencyArr as $val) { ?>
                                        <tr>
                                            <td style=""><?php echo $a  ?></td>
                                            <td><?php echo $val['itemSystemCode'] ?></td>
                                            <?php if($barcode){ ?>
                                                <td><?php echo $val['barcode'] ?></td>
                                            <?php } ?>
                                            <?php if($partNo){ ?>
                                                <td><?php echo $val['partNo'] ?></td>
                                            <?php } ?>
                                            <td><?php echo $val['seconeryItemCode'] ?></td>
                                            <td><?php echo $val['itemDescription'] ?></td>
                                            <td><a  class="drill-down-cursor" onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["documentAutoID"] ?>)"><?php echo $val['documentSystemCode'] ?></a></td>
                                            <td><?php echo $val['documentDate'] ?></td>

                                            <td><?php echo $val['transactionUOM'] ?></td>
                                            <td class="text-right"><?php echo ($val['transactionQTY'] * -1) ?></td>
                                            <td><?php echo $val[$currency . 'Currency'] ?></td>
                                            <?php if($val['transactionQTY'] == 0) { ?>
                                                <td class="text-right"> <?php echo number_format(0 , $val[$currency . 'CurrencyDecimalPlaces']) ?></td>
                                            <?php } else { ?>
                                                <td class="text-right"><?php echo (($val[$currency . 'Amount'] / $val['transactionQTY']))?></td>
                                            <?php } ?>
                                            <td class="text-right"><?php echo number_format($val[$currency . 'Amount'] * -1, $val[$currency . 'CurrencyDecimalPlaces'] )?></td>
                                        </tr>
                                        <?php
                                        $grandTotal['totalCost'][] = abs($val[$currency . 'Amount']);
                                        $a++;
                                    }?>
                                    <tr>
                                        <td></td>
                                        <td colspan="<?php echo $colspan ?>" ><b>Total</b></td>
                                        <td class="reporttotal text-right"><?php echo number_format(array_sum($grandTotal['totalCost']), $this->common_data['company_data']['company_default_decimal']) ?></td>
                                    </tr>
                                <?php }

                            } else {
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
                                        <td><?php echo $val['documentSystemCode'] ?></td>
                                        <td><?php echo $val['documentDate'] ?></td>
                                        <td><?php echo $val['transactionUOM'] ?></td>
                                        <td class="text-right"><?php echo ($val['transactionQTY'] * -1) ?></td>
                                        <td><?php echo $val[$currency . 'Currency'] ?></td>
                                        <?php if($val['transactionQTY'] == 0) { ?>
                                            <td class="text-right"> <?php echo number_format(0 , $val[$currency . 'CurrencyDecimalPlaces']) ?></td>
                                        <?php } else { ?>
                                            <td class="text-right"><?php echo number_format(($val[$currency . 'Amount'] / $val['transactionQTY']), $val[$currency . 'CurrencyDecimalPlaces'] )?></td>
                                        <?php } ?>
                                        <td class="text-right"><?php echo number_format($val[$currency . 'Amount'] * -1, $val[$currency . 'CurrencyDecimalPlaces'] )?></td>
                                    </tr>
                                    <?php
                                    $grandTotal['totalCost'][] = abs($val[$currency . 'Amount']);
                                    $a++;
                                } ?>
                             <tr>
                                <td></td>
                                <td colspan="<?php echo $colspan ?>"><b>Total</b></td>
                                <td class="reporttotal text-right"><?php echo number_format(array_sum($grandTotal['totalCost']), $this->common_data['company_data']['company_default_decimal']) ?></td>
                            </tr>
                            <?php } ?>
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
    $('#tbl_rpt_itemreceivedhistory').tableHeadFixer({
        head: true,
        foot: true,
        left: 0,
        right: 0,
        'z-index': 10
    });

</script>