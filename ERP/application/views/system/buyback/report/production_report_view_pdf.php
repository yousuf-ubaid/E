<?php
$isRptCost = false;
$isLocCost = false;
$statusText = "";
$companyID = $this->common_data['company_data']['company_id'];
?>
<div id="tbl_purchase_order_list" style=" padding-left: 10px; padding-right: 10px">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['companyPrintAddress'] ?> </strong>
            </div>
            <div class="text-center reportHeaderColor">
                <strong>Tel : <?php echo $this->common_data['company_data']['companyPrintTelephone'] ?> </strong>
            </div>

            <div class="text-center reportHeader reportHeaderColor"> Production Statement</div>
        </div>
    </div>
    <br>
    <hr>

    <?php if (!empty($dispatch)) {
    $grand

    ?>
    <br>
    <div class="table-responsive">
        <table style="width: 100%">
            <tbody>
            <tr>
                <td style="font-size: 12px;font-family: tahoma;" ><strong>Dealer </strong></td>
                <td ><strong>:</strong></td>
                <td style="font-size: 12px;font-family: tahoma;">Direct Farmers (D10004) </td>

                <td style="font-size: 12px;font-family: tahoma;" ><strong>Farmer </strong></td>
                <td ><strong>:</strong></td>
                <td style="font-size: 12px;font-family: tahoma;"><?php echo $batchDetail['farmerName'] . " (" . $batchDetail['farmerCode'] . ") "; ?> </td>
            </tr>
           <tr>
                <td style="font-size: 12px;font-family: tahoma;" ><strong>Address </strong></td>
                <td ><strong>:</strong></td>
                <td style="font-size: 12px;font-family: tahoma;"> <?php echo $batchDetail['farmerAddress']; ?> </td>

                <td style="font-size: 12px;font-family: tahoma;" ><strong>Date </strong></td>
                <td ><strong>:</strong></td>
                <td style="font-size: 12px;font-family: tahoma;">  <?php echo date("d-M-y") . " AGING - (43)" ?> </td>
            </tr>
            <tr>
                <td style="font-size: 12px;font-family: tahoma;" ><strong>Batch Code </strong></td>
                <td ><strong>:</strong></td>
                <td style="font-size: 12px;font-family: tahoma;"> <?php echo $batchDetail['batchCode']; ?></td>

                <td style="font-size: 12px;font-family: tahoma;" ><strong>Outstanding </strong></td>
                <td ><strong>:</strong></td>
                <td style="font-size: 12px;font-family: tahoma;">  <?php
                    $voucherDetails = $this->db->query("SELECT SUM(srp_erp_buyback_paymentvoucherdetail.transactionAmount) AS transactionAmount FROM srp_erp_buyback_paymentvouchermaster INNER JOIN srp_erp_buyback_paymentvoucherdetail ON srp_erp_buyback_paymentvoucherdetail.pvMasterAutoID = srp_erp_buyback_paymentvouchermaster.pvMasterAutoID WHERE srp_erp_buyback_paymentvouchermaster.BatchID = {$batchDetail['batchMasterID']} OR srp_erp_buyback_paymentvoucherdetail.BatchID = {$batchDetail['batchMasterID']} AND srp_erp_buyback_paymentvouchermaster.companyID = {$companyID} AND PVtype = 1 OR PVtype = 3 AND approvedYN = 1 AND srp_erp_buyback_paymentvoucherdetail.type = 'Batch'")->row_array();
                    $wage = wagesPayableAmount($batchDetail['batchMasterID'], TRUE);
                    $outstanding = ($wage['transactionAmount'] - $voucherDetails['transactionAmount']);
                    //  $totalFarmerpay = 0;
                    //   $totalFarmerpay = $batchOutstanding['oustanding'] - $batchTotalPaid['wagesAmount'];
                    echo number_format($outstanding, 2); ?> </td>
            </tr>

            </tbody>
        </table>
    </div>
    <!--<table >
        <tr class="reportHeaderColor">
            <td>
                <strong> Dealer :</strong>
            </td>
            <td>
                Direct Farmers (D10004)
            </td>
        </tr>
    </table>-->
    <!--<div class="row">
        <div class="col-sm-6 reportHeaderColor">
            <div class="row">
                <div class="col-sm-2">

                </div>
                <div class="col-sm-10">

                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <strong> Farmer :</strong>
                </div>
                <div class="col-sm-10">
                    <?php /*echo $batchDetail['farmerName'] . " (" . $batchDetail['farmerCode'] . ") "; */?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <strong> Address :</strong>
                </div>
                <div class="col-sm-10">
                    <?php /*echo $batchDetail['farmerAddress']; */?>
                </div>
            </div>
        </div>
        <div class="col-sm-6 reportHeaderColor">
            <div class="row">
                <div class="col-sm-3">
                    <strong> Date :</strong>
                </div>
                <div class="col-sm-9">
                    <?php /*echo date("d-M-y") . " AGING - (43)" */?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <strong> Batch Code :</strong>
                </div>
                <div class="col-sm-9">
                    <?php /*echo $batchDetail['batchCode']; */?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <strong> Outstanding :</strong>
                </div>
                <div class="col-sm-9">
                    <?php /*echo number_format(26526, 2); */?>
                </div>
            </div>
        </div>
    </div>-->
    <hr style="border-top: 1px solid #8e2828;">
    <br>

    <div class="row" style="margin-top: 10px; padding-left: 10px; padding-right: 10px">
        <div class="col-md-12">
            <div class="fixHeader_Div">
                <table class="borderSpace report-table-condensed" id="tbl_report" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $grandTotalrptAmount = 0;
                    $grandTotalBuybackAmount = 0;
                    $birdsTotalCount = 0;
                    if (!empty($dispatch)) {
                        echo "<tr>";
                        echo "<td><strong>DISPATCH</strong></td>";
                        echo "</tr>";
                        foreach ($dispatch as $row) {
                            echo "<tr>";
                            echo "<td>" . $row["documentDate"] . "</td>";
                            echo "<td>" . $row["itemDescription"] . "</td>";
                            echo "<td style='text-align: right'>" . number_format($row["transactionQTY"], 2) . "</td>";
                            echo "<td style='text-align: right'>" . number_format($row["unitTransferAmountTransaction"], 2) . "</td>";
                            echo "<td style='text-align: right'>" . number_format($row["totalTransferAmountTransaction"], 2) . "</td>";
                            echo "<td></td>";
                            echo "</tr>";

                            $grandTotalrptAmount += $row["totalTransferAmountTransaction"];
                        }
                    }
                    if (!empty($expense)) {
                        echo "<tr>";
                        echo "<td></td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td><strong>EXPENSE</strong></td>";
                        echo "</tr>";
                        foreach ($expense as $val) {
                            echo "<tr>";
                            echo "<td>" . $val["documentDate"] . "</td>";
                            echo "<td>" . $val["expenseDescription"] . "</td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td style='text-align: right'>" . number_format($val["transactionAmount"], 2) . "</td>";
                            echo "<td></td>";
                            echo "</tr>";

                            $grandTotalrptAmount += $val["transactionAmount"];
                        }
                    }

                    if (!empty($returns)) {

                        echo "<tr>";
                        echo "<td></td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td><strong>RETURN</strong></td>";
                        echo "</tr>";
                        foreach ($returns as $return) {
                            echo "<tr>";
                            echo "<td>". $return["returneddate"] ."</td>";
                            echo "<td>". $return["descriptiton"] ."</td>";
                            echo "<td style='text-align: right'>" . number_format($return["returnedqty"], 2) . "</td>";
                            echo "<td style='text-align: right'>" . number_format($return["rate"], 2) . "</td>";
                            echo "<td></td>";
                            echo "<td style='text-align: right'>" . number_format($return["totalTransferCost"], 2) . "</td>";
                            echo "</tr>";
                            $grandTotalBuybackAmount += $return["totalTransferCost"];

                        }
                    }

                    if (!empty($buyback)) {
                        $birdsKGWeight = 0;
                        $birdsTotalCount = 0;
                        echo "<tr>";
                        echo "<td><strong>BUY BACK</strong></td>";
                        echo "</tr>";
                        foreach ($buyback as $buy) {
                            echo "<tr>";
                            echo "<td>" . $buy["documentDate"] . "</td>";
                            echo "<td>Live Birds</td>";
                            echo "<td style='text-align: right'>" . number_format($buy["noOfBirds"], 2) . "</td>";
                            echo "<td style='text-align: right'>(" . number_format($buy["transactionQTY"]) . ") * " . number_format($buy["unitTransferAmountLocal"], 2) . "</td>";
                            echo "<td></td>";
                            echo "<td style='text-align: right'>" . number_format($buy["totalTransferAmountLocal"], 2) . "</td>";
                            echo "</tr>";
                            $birdsKGWeight += $buy["transactionQTY"];
                            $birdsTotalCount += $buy["noOfBirds"];
                            $grandTotalBuybackAmount += $buy["totalTransferAmountLocal"];
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>

                    </tr>

                    </tfoot>
                </table>
            </div>
            <br>
            <hr style="border-top: 1px solid #8e2828;">

            <table>
                <tbody>
                <tr>
                    <td style="width:75%; vertical-align: top; padding-top: -3px;padding-right: 30px;">
                        <table>
                            <tbody>
                            <tr>

                                <td style="width:80px; font-size: 13px;font-family: tahoma;font-weight: bold;"><strong>Mortality : </strong></td>
                                <td style="font-weight: bold; font-size: 13px;font-family: tahoma;">  <?php
                                    if (!empty($mortality)) {
                                        echo $mortality['totalBirds'];
                                    }
                                    ?></td>
                                <td style="width:60px;"><strong> </strong></td>
                                <td style="font-weight: bold;">
                                </td>

                                <td style="width:60px;font-size: 13px;font-family: tahoma;font-weight: bold;"><strong>Feed :</strong></td>
                                <td style="font-size: 13px;font-family: tahoma;font-weight: bold;">     <?php
                                    if (!empty($feed) && !empty($chicks) && !empty($birdsTotalCount)) {
                                        $feedTot = ($chicks['chicksTotal'] + $birdsTotalCount) / 2;
                                        $feedPercentage = ($feed['feedTotal'] * 50) / $feedTot;
                                        echo number_format($feedPercentage, 2);
                                    }
                                    ?></td>
                                <td style="width:60px;"><strong> </strong></td>
                                <td style="font-weight: bold;">
                                </td>

                                <td style="width:70px;font-size: 13px;font-family: tahoma;font-weight: bold;"><strong> Weight : </strong></td>
                                <td style="font-size: 13px;font-family: tahoma;font-weight: bold;">  <?php
                                    if (!empty($birdsKGWeight) && !empty($birdsTotalCount)) {
                                        $weightPercentage = ($birdsKGWeight / $birdsTotalCount);
                                        echo round($weightPercentage, 2);
                                    }
                                    ?></td>
                                <td style="width:60px;"><strong> </strong></td>
                                <td style="font-weight: bold;">
                                </td>

                                <td style="width:70px;font-size: 13px;font-family: tahoma;font-weight: bold;"><strong>  F.C.R  : </strong></td>
                                <td style="font-size: 13px;font-family: tahoma;font-weight: bold;">     <?php
                                    if (!empty($weightPercentage) && !empty($feedPercentage)) {
                                        $fcr = ($feedPercentage / $weightPercentage);
                                        echo number_format($fcr, 2);
                                    }
                                    ?></td>

                                <td style="width:45px;"><strong> </strong></td>
                                <td style="font-size: 13px;font-family: tahoma;font-weight: bold; padding-right: -15px">    <?php echo number_format($grandTotalrptAmount,2); ?>
                                </td>

                                <td style="width:60px;"><strong> </strong></td>
                                <td style="font-size: 13px;font-family: tahoma;font-weight: bold; padding-right: -30px"> <?php echo number_format($grandTotalBuybackAmount, 2); ?>
                                    </td>

                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
                <tbody>
                <tr>
                    <td style="width:75%; vertical-align: top; padding-top: -3px;padding-right: 30px;">
                        <table>
                            <tbody>
                            <tr>

                                <td style="width:110px; font-size: 13px;font-family: tahoma;font-weight: bold;"><strong>Mortality % : </strong></td>
                                <td style="font-weight: bold; font-size: 13px;font-family: tahoma;padding-left: -17px"> <?php
                                    if (!empty($mortality['totalBirds']) && !empty($chicks['chicksTotal'])) {
                                        $mortalityPercentage = ($mortality['totalBirds'] / $chicks['chicksTotal']) * 100;
                                        echo number_format($mortalityPercentage, 1);
                                    }
                                    ?></td>
                                <td style="width:60px;"><strong> </strong></td>
                                <td style="font-weight: bold;">
                                </td>

                                <td style="width:80px;font-size: 13px;font-family: tahoma;font-weight: bold;padding-left: -13px"><strong>Cost / Bird :</strong></td>
                                <td style="font-size: 13px;font-family: tahoma;font-weight: bold;padding-left: -14px">     <?php
                            if (!empty($feed) && !empty($chicks) && !empty($birdsTotalCount)) {
                                $feedTot = ($chicks['chicksTotal'] + $birdsTotalCount) / 2;
                                $costBird = ($grandTotalrptAmount / $feedTot);
                                echo number_format($costBird, 2);
                            }
                            ?></td>
                                <td style="width:60px;"><strong> </strong></td>
                                <td style="font-weight: bold;">
                                </td>

                                <td style="width:100px;font-size: 13px;font-family: tahoma;font-weight: bold;padding-left: -28px;"><strong> Profit / Bird : </strong></td>
                                <td style="font-size: 13px;font-family: tahoma;font-weight: bold;padding-left: -30px;">  <?php
                            if (!empty($chicks) && !empty($grandTotalBuybackAmount) && !empty($grandTotalrptAmount)) {
                                $wagesPayable = ($grandTotalBuybackAmount - $grandTotalrptAmount);
                                $profitBird = ($wagesPayable / $chicks['chicksTotal']);
                                echo number_format($profitBird, 2);
                            }
                            ?></td>
                                <td style="width:60px;"><strong> </strong></td>
                                <td style="font-weight: bold;">
                                </td>

                                <td style="width:70px;font-size: 13px;font-family: tahoma;font-weight: bold;padding-left: -60px;"><strong>  DIFF :  </strong></td>
                                <td style="font-size: 13px;font-family: tahoma;font-weight: bold;padding-left: -80px;">    <?php echo '0'; ?></td>

                                <td style="width:120px;font-size: 13px;font-family: tahoma;font-weight: bold;"><strong>Wages Payable:
                                <td style="font-size: 13px;font-family: tahoma;font-weight: bold;padding-right: -30px;">   <?php
                                    $wagesPayable = ($grandTotalBuybackAmount - $grandTotalrptAmount);
                                    echo number_format($wagesPayable, 2);
                                    ?>   </strong>
                                    </td>

                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <hr style="border-top: 1px solid #8e2828;">
            <?php
            } else {
                echo warning_message("No Records Found!");
            }
            ?>
        </div>
    </div>
</div>
<script>

    /* $(document).ready(function() {
     $('#demo').dragtable();
     });*/

    /*$('#tbl_report').tableHeadFixer({
     head: true,
     foot: false,
     left: 0,
     right: 0,
     'z-index': 0
     });*/
</script>