<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('inventory', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
if (!empty($details)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                ?>
                <div class="row" style="margin-top: 5px">
                    <div class="col-md-12">
                        <div class="pull-right"><button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateReportMatchingSummaryPdf()">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                            </button> <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Item Exceed Summary.xls" onclick="var file = tableToExcel('itemExceedSummeryReport', 'Item Exceed Summary'); $(this).attr('href', file);">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="itemExceedSummeryReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Item Exceed Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th style="width: 20% !important;"><?php echo $this->lang->line('common_item'); ?><!--Item--></th>
                        <th style="width: 20% !important;"><?php echo $this->lang->line('common_item_description')?><!--Item Description--></th>
                        <th style="width: 20% !important;"><?php echo $this->lang->line('common_warehouse'); ?><!--Warehouse--></th>
                        <th style="width: 5% !important;"><?php echo $this->lang->line('common_uom'); ?><!--UOM--></th>
                        <th style="width: 5% !important;"><?php echo $this->lang->line('inventory_exceeded_qty'); ?><!--Exceeded Qty--></th>
                        <th style="width: 5% !important;"><?php echo $this->lang->line('inventory_matched_qty'); ?><!--Matched Qty--></th>
                        <th style="width: 5% !important;"><?php echo $this->lang->line('inventory_balance_qty'); ?><!--Balance Qty--></th>
                        <th style="width: 10% !important;"><?php echo $this->lang->line('inventory_unit_amount'); ?><!--Unit Amount-->(<?php echo $this->common_data['company_data']['company_default_currency']; ?>)</th>
                        <th style="width: 10% !important;"><?php echo $this->lang->line('inventory_exceeded_amount'); ?><!--Exceeded Amount -->(<?php echo $this->common_data['company_data']['company_default_currency']; ?>)</th>
                        <th style="width: 10% !important;"><?php echo $this->lang->line('inventory_matched_amount'); ?><!--Matched Amount--> (<?php echo $this->common_data['company_data']['company_default_currency']; ?>)</th>
                        <th style="width: 10% !important;"><?php echo $this->lang->line('inventory_balance_amount'); ?><!--Balance Amount--> (<?php echo $this->common_data['company_data']['company_default_currency']; ?>)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $totamountexceed=0;
                        $totamountupdate=0;
                        $totamountbal=0;
                        foreach($details as $val){
                            $itemSecondaryCodePolicy =is_show_secondary_code_enabled();
                            if($itemSecondaryCodePolicy){
                                $item_code = $val["seconeryItemCode"];
                            }else{
                                $item_code = $val["itemSystemCode"];
                            }
                            ?>
                            <tr>
                                <td><?php echo $item_code ?></td>
                                <td><?php echo $val['itemName'] ?></td>
                                <td><?php echo $val['wareHouseDescription'] ?></td>
                                <td><?php echo $val['uom'] ?></td>
                                <td class="text-right"><?php echo ($val['exceededQty']) ?></td>
                                <td class="text-right"><?php echo ($val['matchedqty']) ?></td>
                                <td class="text-right"><?php echo ($val['balanceQty']) ?></td>
                                <td class="text-right"><?php echo number_format(($val['transactionAmount']/$val['exceededQty']), $this->common_data['company_data']['company_default_decimal']) ?></td>
                                <td class="text-right"><?php echo number_format($val['transactionAmount'], $this->common_data['company_data']['company_default_decimal']) ?></td>
                                <td class="text-right"><?php echo number_format($val['totval'], $this->common_data['company_data']['company_default_decimal']) ?></td>
                                <td class="text-right"><?php echo number_format(($val['transactionAmount']-$val['totval']), $this->common_data['company_data']['company_default_decimal']) ?></td>
                            </tr>
                            <?php
                            $totamountexceed+=$val['transactionAmount'];
                            $totamountupdate+=$val['totval'];
                            $totamountbal+=($val['transactionAmount']-$val['totval']);
                        }
                    } ?>

                    </tbody>
                    <tfoot>
                    <tr>
                        <td class="text-right " colspan="7">Total</td>
                        <td  class="text-right reporttotal"><?php echo number_format( $totamountexceed, $this->common_data['company_data']['company_default_decimal']) ?></td>
                        <td  class="text-right reporttotal"><?php echo number_format( $totamountupdate, $this->common_data['company_data']['company_default_decimal']) ?></td>
                        <td  class="text-right reporttotal"><?php echo number_format( $totamountbal, $this->common_data['company_data']['company_default_decimal']) ?></td>
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
/*
    $(document).ready(function () {
        $(window).unbind('scroll');
    });*/

</script>