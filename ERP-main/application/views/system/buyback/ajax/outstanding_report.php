<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$datefrom = $this->lang->line('accounts_receivable_common_date_from');
$dateto = $this->lang->line('accounts_receivable_common_date_to');
$currency = $this->lang->line('common_currency');
$netbalance = $this->lang->line('accounts_receivable_common_net_balance');

$isRptCost = false;
$isLocCost = false;
$isTransCost = false;
if (isset($fieldName)) {
    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;
    }

    if (in_array("companyLocalAmount", $fieldName)) {
        $isLocCost = true;
    }

    if (in_array("transactionAmount", $fieldName)) {
        $isTransCost = true;
    }
}
$date_format_policy = date_format_policy();
$format_batchClosingDate = input_format_date($from, $date_format_policy);
?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_farm_outstanding', 'Outstanding');
        } ?>
    </div>
</div>
<div id="tbl_customer_ledger">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor">Farm Outstanding</div>
            <div
                class="text-center reportHeaderColor"> <?php echo "<strong>As of : </strong>" . $from?></div>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <strong>Filters <i class="fa fa-filter"></i></strong><br>
            <strong><i>Sub Locations :</i></strong> <?php echo join(",", $output['location']) ?>
        </div>
    </div>

    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output['details'])) { ?>
                <div class="fixHeader_Div">
                    <table class="fixed_header borderSpace report-table-condensed" id="tbl_farm_outstanding">
                        <thead class="report-header">
                        <tr>
                            <th>#</th>
                            <th width="25%">Farm</th>
                            <th>Profit</th>
                            <th>Loss</th>
                            <th>Loan</th>
                            <th>Advance</th>
                            <th>Total</th>
                            <th>Deposit</th>
                            <th>Total with DPS</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $grandtotalProfit = 0;
                        $grandtotalLoss = 0;
                        $grandtotalLoan = 0;
                        $grandtotalAdvance = 0;
                        $grandtotalBalance = 0;
                        $grandtotalWithDPS = 0;
                        $grandtotalDeposit = 0;
                        $a = 1;
                        foreach ($output['details'] as $val)
                        {
                            $profitAmount = 0;
                            $lossAmount = 0;
                            $balanceAmount = 0;
                            $totalWithDPS = 0;
                            $batch = $this->db->query("SELECT batchMasterID FROM srp_erp_buyback_batch WHERE farmID = {$val['farmID']} AND isclosed = 1 AND ((closedDate <= '{$format_batchClosingDate}') OR closedDate IS NULL ) ORDER BY batchMasterID ASC")->result_array();
                            // AND batchClosingDate >= '{$output['format_batchClosingDate']}'
                        //    var_dump($batch);
                            foreach ($batch as $batchID)
                            {
                                if (!empty($caption)) {
                                    $wages = wagesPayableAmount($batchID['batchMasterID'], TRUE);

                                    $batchProfORLoss = wagesPayableAmount($batchID['batchMasterID'], FALSE);
                                    if ($isTransCost) {
                                        $wagesPayable = $wages['transactionAmount'];
                                    } else {
                                        if ($isRptCost) {
                                            $wagesPayable = $wages['companyReportingAmount'];
                                        }
                                        if ($isLocCost) {
                                            $wagesPayable = $wages['companyLocalAmount'];
                                        }
                                    }
                                }
                                if($batchProfORLoss['transactionAmount'] > 0)
                                {
                                    $profitAmount +=  $wagesPayable;
                                } else{
                                    $lossAmount += $wagesPayable;
                                }
                            }
                            $lossAmount = $lossAmount* (-1);
                            $balanceAmount = ($profitAmount) - ($lossAmount + (($val['LoantransactionAmount'] + $val['creditLoanAmount']) - $val['LoanPaidAmount']) + (($val['advancetransactionAmount'] + $val['creditAdvanceAmount']) - $val['advancePaidAmount']));
                            $totalWithDPS = $balanceAmount + ($val['deposittransactionAmount'] + $val['debitDepositAmount']);

                            if($totalWithDPS != 0){
                                echo '<tr class="hoverTr">';
                                echo '<td>' . $a . '<!--farm--></td>';
                                echo '<td width="25%">' . $val['farmSystemCode'] . ' - ' . $val['farmName'] . '<!--farm--></td>';
                                echo '<td style="text-align: right"><a href="#" class="drill-down-cursor" onclick="profitLossBatchDetails_modal(\'Profit Batch \', ' . $profitAmount . ' , ' . $val['farmID'] . ')"> ' . number_format($profitAmount, $val['currencyDecimalPlaces']) . ' </a></td>';
                                echo '<td style="text-align: right"><a href="#" class="drill-down-cursor" onclick="profitLossBatchDetails_modal(\'Loss Batch \', ' . $lossAmount . ', ' . $val['farmID'] . ')"> ' . number_format($lossAmount, $val['currencyDecimalPlaces']) . ' </a></td>';
                                if($val['LoantransactionAmount'] > 0 || $val['creditLoanAmount'] > 0){
                                    echo '<td style="text-align: right"><a href="#" class="drill-down-cursor" onclick="AdvanceDetails_modal(\'Loan Details \', ' . $val['farmID'] . ')"> ' . number_format((($val['LoantransactionAmount'] + $val['creditLoanAmount']) - $val['LoanPaidAmount']), $val['currencyDecimalPlaces']) . ' </a></td>';
                                }else {
                                    echo '<td style="text-align: right"><a href="#" class="drill-down-cursor"> ' . number_format((($val['LoantransactionAmount'] + $val['creditLoanAmount']) - $val['LoanPaidAmount']), $val['currencyDecimalPlaces']) . ' </a></td>';
                                }
                                if($val['advancetransactionAmount'] > 0 || $val['creditAdvanceAmount'] > 0){
                                    echo '<td style="text-align: right"><a href="#" class="drill-down-cursor" onclick="AdvanceDetails_modal(\'Advance Details \', ' . $val['farmID'] . ')"> ' . number_format((($val['advancetransactionAmount'] + $val['creditAdvanceAmount']) - $val['advancePaidAmount']), $val['currencyDecimalPlaces']) . ' </a></td>';
                                }else {
                                    echo '<td style="text-align: right"><a href="#" class="drill-down-cursor"> ' . number_format((($val['advancetransactionAmount'] + $val['creditAdvanceAmount']) - $val['advancePaidAmount']), $val['currencyDecimalPlaces']) . ' </a></td>';
                                }
                                echo '<td style="text-align: right">' . number_format($balanceAmount, $val['currencyDecimalPlaces']) . '<!--bach--></td>';
                                echo '<td style="text-align: right"><a href="#" class="drill-down-cursor" onclick="AdvanceDetails_modal(\'Deposit Details \', ' . $val['farmID'] . ')"> ' . number_format((($val['deposittransactionAmount'] + $val['debitDepositAmount']) - $val['depositPaidAmount']), $val['currencyDecimalPlaces']) . ' </a></td>';
                                echo '<td style="text-align: right">' . number_format($totalWithDPS, $val['currencyDecimalPlaces']) . '<!--bach--></td>';
                                echo "</tr>";
                                $grandtotalProfit += $profitAmount;
                                $grandtotalLoss += $lossAmount;
                                $grandtotalDeposit += ($val['deposittransactionAmount'] + $val['debitDepositAmount']) - $val['depositPaidAmount'];
                                $grandtotalLoan += ($val['LoantransactionAmount'] + $val['creditLoanAmount']) - $val['LoanPaidAmount'];
                                $grandtotalAdvance += ($val['advancetransactionAmount'] + $val['creditAdvanceAmount']) - $val['advancePaidAmount'];
                                $grandtotalBalance += $balanceAmount;
                                $grandtotalWithDPS += $totalWithDPS;
                                $a++;
                            }
                        }
                        echo "</tbody>";
                        echo "<tfoot>";
                        if($grandtotalWithDPS != 0){
                            echo '<tr class="hoverTr">';
                                echo '<td colspan="2">Total Amount</td>';
                                echo '<td class="text-right reporttotal">' . number_format($grandtotalProfit, $val['currencyDecimalPlaces']) . '<!--bach--></td>';
                                echo '<td class="text-right reporttotal">' . number_format($grandtotalLoss, $val['currencyDecimalPlaces']) . '<!--bach--></td>';
                                echo '<td class="text-right reporttotal">' . number_format($grandtotalLoan, $val['currencyDecimalPlaces']) . '<!--bach--></td>';
                                echo '<td class="text-right reporttotal">' . number_format($grandtotalAdvance, $val['currencyDecimalPlaces']) . '<!--bach--></td>';
                                echo '<td class="text-right reporttotal">' . number_format($grandtotalBalance, $val['currencyDecimalPlaces']) . '<!--bach--></td>';
                                echo '<td class="text-right reporttotal">' . number_format($grandtotalDeposit, $val['currencyDecimalPlaces']) . '<!--bach--></td>';
                                echo '<td class="text-right reporttotal">' . number_format($grandtotalWithDPS, $val['currencyDecimalPlaces']) . '<!--bach--></td>';
                            echo "</tr>";
                        } else {
                             echo '<tr class="danger">';
                                echo '<td colspan="9" class="text-center"> <b>' . $this->lang->line('common_no_records_found') . '</b></td>';
                             echo '</tr>';
                        }
                        echo "</tfoot>";
                        ?>
                    </table>
                </div>
                <?php
            } else {
                $norecfound = $this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*No Records Found!*/
            }
            ?>
        </div>
    </div>
</div>


<?php
