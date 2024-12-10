<?php
if (!empty($details)) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                ?>
                <div class="row" style="margin-top: 5px">
                    <div class="col-md-12">
                        <div class="pull-right"><button class="btn btn-pdf btn-xs" id="btn-pdf" type="button" onclick="generateReportmatchingDetailPdf()">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                            </button> <a href="" class="btn btn-excel btn-xs" id="btn-excel" download="Item Matching.xls" onclick="var file = tableToExcel('itemMatchDetailReport', 'Item Match Detail'); $(this).attr('href', file);">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </a></div>        </div>
                </div>
            <?php
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="itemMatchDetailReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Item Match Detail Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>Document Code</th>
                        <th>Exceeded Code</th>
                        <th>Item</th>
                        <th>Warehouse</th>
                        <th>Matched Qty</th>
                        <th>Item Cost</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if ($details) {
                        $totamount=0;
                       foreach($details as $val){
                           ?>
                           <tr>
                               <td><?php echo $val['matchdocCode']  ?></td>
                               <td><?php echo $val['documentSystemCode']  ?></td>
                               <td><?php echo $val['itemSystemCode'] .' | '. $val['itemName'] ?></td>
                               <td class=""><?php echo $val['wareHouseLocation'] ?></td>
                               <td class="text-right"><?php echo $val['matchedQty'] ?></td>
                               <td class="text-right"><?php echo $val['itemCost'] ?></td>
                               <td class="text-right"><?php echo $val['totalValue'] ?></td>
                           </tr>

                    <?php
                           $totamount+=$val['totalValue'];
                       }
                    } ?>

                    </tbody>
                    <tfoot>
                    <tr>
                        <td class="text-right " colspan="6">Total</td>
                        <td  class="text-right reporttotal"><?php echo $totamount ?></td>
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