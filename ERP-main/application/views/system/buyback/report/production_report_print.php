<!---- =============================================
-- File Name : production_report.php
-- Project Name : SME ERP
-- Module Name : Report - Production Report
-- Create date : 09 - September 2017
-- Description : This file contains Buyback Production Report.

-- REVISION HISTORY
-- =============================================-->
<?php echo fetch_account_review(true, true, $approval); ?>
<style>

</style>
<?php
$isRptCost = false;
$isLocCost = false;
$statusText = "";
$companyID = $this->common_data['company_data']['company_id'];
?>

<div id="tbl_purchase_order_list">
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
    <?php if (!empty($dispatch)) {
    $grand

    ?>
    <br>
    <hr style="border-top: 1px solid #8e2828;">
    <div class="row">
        <div class="col-sm-6 reportHeaderColor">
            <div class="row">
                <div class="col-sm-2">
                    <strong> Dealer :</strong>
                </div>
                <div class="col-sm-10">
                    <?php if ($dealers['isActive'] == 1) { ?>
                        <?php echo $dealers['customerName']; ?>
                        <?php
                    } else {?>
                        <strong> - </strong>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <strong> Farmer :</strong>
                </div>
                <div class="col-sm-10">
                    <?php echo $batchDetail['farmerName'] . " (" . $batchDetail['farmerCode'] . ") "; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <strong> Address :</strong>
                </div>
                <div class="col-sm-10">
                    <?php echo $batchDetail['farmerAddress']; ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6 reportHeaderColor">
            <div class="row">
                <div class="col-sm-3">
                    <strong> Date :</strong>
                </div>
                <div class="col-sm-9">
                    <?php echo date("d-M-y") . " AGING - (43)" ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <strong> Batch Code :</strong>
                </div>
                <div class="col-sm-9">
                    <?php echo $batchDetail['batchCode']; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <strong> Outstanding :</strong>
                </div>
                <div class="col-sm-9">
                    <?php
                    $voucherDetails = $this->db->query("SELECT SUM(pvd.transactionAmount) AS transactionAmount FROM srp_erp_buyback_paymentvouchermaster pv LEFT JOIN srp_erp_buyback_paymentvoucherdetail pvd ON pvd.pvMasterAutoID = pv.pvMasterAutoID WHERE (pv.BatchID = {$batchDetail['batchMasterID']} AND pv.farmID = {$batchDetail['farmID']} AND pv.approvedYN = 1  AND pv.companyID = $companyID)  OR (pvd.BatchID = {$batchDetail['batchMasterID']} AND pv.farmID = {$batchDetail['farmID']} AND pv.approvedYN = 1  AND pv.companyID = $companyID) AND (PVtype = 1 OR PVtype = 3) AND pvd.type = 'Batch'")->row_array();

                    $wage = wagesPayableAmount($batchDetail['batchMasterID'], TRUE);
                    $outstanding = ($wage['transactionAmount'] - $voucherDetails['transactionAmount']);
                    //  $totalFarmerpay = 0;
                    //   $totalFarmerpay = $batchOutstanding['oustanding'] - $batchTotalPaid['wagesAmount'];
                    echo number_format($outstanding, 2);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <hr style="border-top: 1px solid #8e2828;">
    <br>

    <div class="row" style="margin-top: 10px">
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
                        echo "<td><strong>EXPENSE</strong></td>";
                        echo "</tr>";
                        foreach ($expense as $val) {
                            echo "<tr>";
                            echo "<td>" . $val["documentDate"] . "</td>";
                            echo "<td>" . $val["GLDescription"] . "</td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td style='text-align: right'>" . number_format($val["transactionAmount"], 2) . "</td>";
                            echo "<td></td>";
                            echo "</tr>";

                            $grandTotalrptAmount += $val["transactionAmount"];
                        }
                    }

                    if (!empty($return)) {

                        echo "<tr>";
                        echo "<td><strong>RETURN</strong></td>";
                        echo "</tr>";
                        foreach ($return as $return) {
                            echo "<tr>";
                            echo "<td>". $return["returneddate"] .' - '. $return["documentSystemCode"]."</td>";
                            echo "<td>". $return["descriptiton"] ."</td>";
                            echo "<td style='text-align: right'>". $return["returnedqty"] ."</td>";

                            echo "<td style='text-align: right'>".number_format($return["rate"],2)  ." </td>";
                            echo "<td> </td>";

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
                            echo "<td style='text-align: right'>(" . number_format($buy["transactionQTY"], 2) . ") * " . number_format($buy["unitTransferAmountLocal"], 2) . "</td>";
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
                        <td colspan='10'>&nbsp;</td>
                    </tr>

                    </tfoot>
                </table>
            </div>
            <hr style="border-top: 1px solid #8e2828;">
            <div class="row">
                <div class="col-sm-2 reportHeaderColorbuyback">
                    <div class="row">
                        <div class="col-sm-8">
                            <strong> Mortality :</strong>
                        </div>
                        <div class="col-sm-4">
                            <?php
                            if (!empty($mortality)) {
                                echo $mortality['totalBirds'];
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8">
                            <strong> Mortality %:</strong>
                        </div>
                        <div class="col-sm-4">
                            <?php
                            if (!empty($mortality['totalBirds']) && !empty($chicks['chicksTotal'])) {
                                $mortalityPercentage = ($mortality['totalBirds'] / $chicks['chicksTotal']) * 100;
                                echo number_format($mortalityPercentage, 1);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2 reportHeaderColorbuyback">
                    <div class="row">
                        <div class="col-sm-7">
                            <strong> Feed :</strong>
                        </div>
                        <div class="col-sm-5">
                            <?php
                            if (!empty($feed) && !empty($chicks) && !empty($birdsTotalCount)) {
                                $feedTot = ($chicks['chicksTotal'] + $birdsTotalCount) / 2;
                                $feedPercentage = ($feed['feedTotal'] * 50) / $feedTot;
                                echo number_format($feedPercentage, 2);
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-7">
                            <strong> Cost / Bird :</strong>
                        </div>
                        <div class="col-sm-5">
                            <?php
                            if (!empty($feed) && !empty($chicks) && !empty($birdsTotalCount)) {
                                $feedTot = ($chicks['chicksTotal'] + $birdsTotalCount) / 2;
                                $costBird = ($grandTotalrptAmount / $feedTot);
                                echo number_format($costBird, 2);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2 reportHeaderColorbuyback">
                    <div class="row">
                        <div class="col-sm-8">
                            <strong> Weight :</strong>
                        </div>
                        <div class="col-sm-4">
                            <?php
                            if (!empty($birdsKGWeight) && !empty($birdsTotalCount)) {
                                $weightPercentage = ($birdsKGWeight / $birdsTotalCount);
                                echo round($weightPercentage, 2);
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8">
                            <strong> Profit / Bird :</strong>
                        </div>
                        <div class="col-sm-4">
                            <?php
                            if (!empty($chicks) && !empty($grandTotalBuybackAmount) && !empty($grandTotalrptAmount)) {
                                $wagesPayable = ($grandTotalBuybackAmount - $grandTotalrptAmount);
                                $profitBird = ($wagesPayable / $chicks['chicksTotal']);
                                echo number_format($profitBird, 2);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2 reportHeaderColorbuyback">
                    <div class="row">
                        <div class="col-sm-7">
                            <strong> F.C.R :</strong>
                        </div>
                        <div class="col-sm-5">
                            <?php
                            if (!empty($weightPercentage) && !empty($feedPercentage)) {

                                $fcr = ($feedPercentage / $weightPercentage);
                                echo number_format($fcr, 2);
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-7">
                            <strong> DIFF :</strong>
                        </div>
                        <div class="col-sm-5">
                            <?php echo '0'; ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-1 reportHeaderColorbuyback">
                    &nbsp;
                </div>
                <div class="col-sm-3 reportHeaderColorbuyback">
                    <div class="row">
                        <div class="col-sm-6">
                            <strong><span
                                    style="float: right !important;"><?php echo number_format($grandTotalrptAmount, 2); ?></span></strong>
                        </div>
                        <div class="col-sm-6">
                            <strong><span
                                    style="float: right !important;"><?php echo number_format($grandTotalBuybackAmount, 2); ?></span></strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong><span style="float: right !important;">Wages Payable</span></strong>
                        </div>
                        <div class="col-sm-6">
                            <strong><span style="float: right !important;">
                                    <?php
                                    $wagesPayable = ($grandTotalBuybackAmount - $grandTotalrptAmount);
                                    echo number_format($wagesPayable, 2);
                                    ?>
                                </span></strong>
                        </div>
                    </div>
                </div>
            </div>
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
    $('.review').removeClass('hide');
    a_link = "<?php echo site_url('Buyback/buyback_production_report'); ?>/<?php echo $batchDetail['batchMasterID'] ?>";
    de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_batchClosing'); ?>/" + <?php echo $batchDetail['batchMasterID'] ?> +'/BBBC';
    $("#a_link").attr("href", a_link);
    $("#de_link").attr("href", de_link);
</script>