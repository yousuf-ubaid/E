<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('common', $primaryLanguage);
if (!empty($details)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                ?>
                <div class="row" style="margin-top: 5px">
                    <div class="col-md-12">
                        <div class="pull-right"><button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateReportmatchingPdf()">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                            </button> <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Item Matching.xls" onclick="var file = tableToExcel('itemMatchReport', 'Item Matching'); $(this).attr('href', file);">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </a></div>        </div>
                </div>
            <?php
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="itemMatchReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Item Matching Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th><?php echo $this->lang->line('common_document_code'); ?><!--Document Code--></th>
                        <th><?php echo $this->lang->line('common_origin_documnet_code'); ?><!--Origin Document Code--></th>
                        <th><?php echo $this->lang->line('common_document_date'); ?><!--Document Date--></th>
                        <th><?php echo $this->lang->line('common_currency'); ?><!--Currency--></th>
                        <th><?php echo $this->lang->line('common_amount'); ?><!--Amount--></th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if ($details) {
                        $totamount=0;
                        $transactionCurrencyDecimalPlaces=2;
                       foreach($details as $val){
                           ?>
                           <tr>
                               <td><?php echo $val['documentSystemCode'] ?></td>
                               <td class=""><?php echo $val['orginDocumentSystemCode'] ?></td>
                               <td class=""><?php echo $val['documentDate'] ?></td>
                               <td class="text-right"><?php echo $val['transactionCurrency'] ?></td>
                               <td class="text-right"><?php echo number_format($val['totalValue'], $val['transactionCurrencyDecimalPlaces']) ?></td>
                               <td class="text-center"><button style="margin-top: 5px" type="button" onclick="get_item_match_detail_report(<?php echo $val['exceededMatchID'] ?>)" class="btn btn-primary btn-xs">view</button></td>
                           </tr>
                    <?php
                           $totamount+=$val['totalValue'];
                           $transactionCurrencyDecimalPlaces=$val['transactionCurrencyDecimalPlaces'];
                       }
                    } ?>

                    </tbody>
                    <tfoot>
                    <tr>
                        <td class="text-right " colspan="4">Total</td>
                        <td  class="text-right reporttotal"><?php echo number_format($totamount, $transactionCurrencyDecimalPlaces) ?></td>
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