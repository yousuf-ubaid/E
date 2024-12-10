<?php


if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
                    if ($type == 'html') {
                        echo export_buttons('dispatchdetail', 'Dispatch Details', True, false);
                    } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="dispatchdetail">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Dispatch Details</strong></div>
            <div style="">
                <br>
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th style="width: 10%">Farm Name</th>
                        <th style="width: 5%">Batch Code</th>
                        <th style="width: 5%">Document Code</th>
                        <th style="width: 5%" >Segment</th>
                        <th style="width: 5%" >Dispatch Date</th>
                        <th style="width: 5%" >Document Date</th>
                        <th style="width: 5%">Currency</th>
                        <th style="width: 5%">Dispatch Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($details) {
                        $total = 0;
                        foreach ($details as $val) { ?>
                            <tr>
                                <td><?php echo $val["farmName"] ?></td>
                                <td><?php echo $val["batchCode"] ?></td>
                                <td>


                                    <a href="#" class="drill-down-cursor"
                                       onclick="documentPageView_modal('<?php echo $val["documentID"] ?>',<?php echo $val["dispatchAutoID"] ?>,<?php echo $val["batchMasterID"] ?>)"><?php echo $val["documentSystemCode"] ?></a>
                                </td>
                                <td><?php echo $val["segmentCode"] ?></td>
                                <td><?php echo $val["dispatchedDate"] ?></td>
                                <td><?php echo $val["documentDate"] ?></td>
                                <td><?php echo $val["detailCurrency"] ?></td>
                                <td style="text-align: right"><?php echo number_format($val["TransferTotal"] ,2) ?></td>
                            </tr>

                        <?php
                            $total +=$val["TransferTotal"];
                        }?>

                        <tr>
                            <td colspan="7"><b>Total</b></td>
                            <td class="text-right reporttotal"><?php echo number_format($total,2) ?></td>
                        </tr>
<?php
                    }
                 ?>

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
              No Records found
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