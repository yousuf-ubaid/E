<?php
$decimalPlace = 2;
if ($details) { ?>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12">
            <?php
            if ($type == 'html') {
                echo export_buttons('salesOrderReport', 'Donor Commitment Status Report', True, True);
            } ?>
        </div>
    </div>
    <div class="row" style="margin-top: 5px">
        <div class="col-md-12 " id="salesOrderReport">
            <div class="reportHeaderColor" style="text-align: center">
                <strong><?php echo current_companyName(); ?></strong></div>
            <div class="reportHeader reportHeaderColor" style="text-align: center">
                <strong>Donor Commitment Status Report</strong></div>
            <div style="">
                <table id="tbl_rpt_salesorder" class="borderSpace report-table-condensed" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>Donor Name</th>
                        <th>Currency</th>
                        <th>Total Commitments</th>
                        <th>Total Collections</th>
                        <th>Balance</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($details) {
                        $details = array_group_by($details, 'transactionCurrencyID');
                        foreach ($details as $value) {
                            $commitementTotal = 0;
                            $collectionTotal = 0;
                            $balanceTotal = 0;
                            $balance = 0;
                            $decimalPlace = 2;
                            foreach ($value as $val) {
                                $decimalPlace = $val["transactionCurrencyDecimalPlaces"];
                                ?>
                                <tr>
                                    <td width="200px"><?php echo $val["donorName"] ?></td>
                                    <td><?php echo $val["transactionCurrency"] ?></td>
                                    <?php if ($type == 'html') { ?>
                                    <td style="text-align: right"><a href="#" onclick="drilldownCommitmentReport(<?php echo $val["donorsID"] ?>,'<?php echo $val["transactionCurrencyID"] ?>',1,'Total Commitments')"><?php echo number_format($val["commitmentTotal"], $val["transactionCurrencyDecimalPlaces"]) ?></a>
                                    </td>
                                    <td style="text-align: right"><a href="#" onclick="drilldownCommitmentReport(<?php echo $val["donorsID"] ?>,'<?php echo $val["transactionCurrencyID"] ?>',2,'Total Collections')"> <?php echo number_format($val["collectionTotal"], $val["transactionCurrencyDecimalPlaces"]) ?></a>
                                    </td>
                                    <?php } else { ?>
                                        <td style="text-align: right"> <?php echo number_format($val["commitmentTotal"], $val["transactionCurrencyDecimalPlaces"]) ?></td>
                                        <td style="text-align: right"><?php echo number_format($val["collectionTotal"], $val["transactionCurrencyDecimalPlaces"]) ?></td>
                                        <?php
                                    }
                                    $balance = ($val["commitmentTotal"] - $val["collectionTotal"]);
                                    ?>
                                    <td style="text-align: right"><?php echo number_format($balance, $val["transactionCurrencyDecimalPlaces"]) ?></td>
                                </tr>
                                <?php
                                $commitementTotal += $val["commitmentTotal"];
                                $collectionTotal += $val["collectionTotal"];
                                $balanceTotal += $balance;
                            }

                            ?>
                            <tr>
                                <td colspan="2"><b>Total</b></td>
                                <td class="text-right reporttotal"><?php echo number_format($commitementTotal, $decimalPlace) ?></td>
                                <td class="text-right reporttotal"><?php echo number_format($collectionTotal, $decimalPlace) ?></td>
                                <td class="text-right reporttotal"><?php echo number_format($balanceTotal, $decimalPlace) ?></td>
                            </tr>
                            <?php
                        }
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
            <div class="alert alert-warning" role="alert">No Records found</div>
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