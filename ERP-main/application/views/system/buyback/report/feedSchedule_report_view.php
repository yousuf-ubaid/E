<!---- =============================================
-- File Name : production_report.php
-- Project Name : SME ERP
-- Module Name : Report - Production Report
-- Create date : 09 - September 2017
-- Description : This file contains Buyback Production Report.

-- REVISION HISTORY
-- =============================================-->
<style>
    hr {
        margin-top: 0px;
        margin-bottom: 0px;
        border: 0;
        border-top: 1px solid #eee;
    }
</style>
<?php
$companyID = $this->common_data['company_data']['company_id'];
$isRptCost = false;
$isLocCost = false;
$statusText = "";
$date_format_policy = date_format_policy();
$Totalchicks = 0;
$feedPersentageTotal = 0;
$starterBalance = 0;
$todayDate = current_date(false);
$currentAgeCalculation_days = 0;
$boosterBalance = 0;
$totalChicksGiven = 0;
$convertFormat = convert_date_format_sql();
if(!empty($chicks)){
    $totalChicksGiven = $chicks['chicksTotal'];
}
?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_purchase_order_list', 'Production Statement');
        } ?>
    </div>
</div>
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

            <div class="text-center reportHeader reportHeaderColor"> Feed Schedule Report
                - <?php echo $batchDetail['batchCode']; ?></div>
        </div>
    </div>
    <?php if (!empty($feedHeader)) {
        ?>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" style="width: 100%">
                        <thead class="report-header">
                        <tr>
                            <th>Days</th>
                            <th>Feed Type</th>
                            <th>Feed</th>
                            <th>Feed Bag Calculation by Feed Type</th>
                            <th>Dispatch Bags</th>
                            <th>Balance to Send</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $dispatchBags = 0;
                        $dispatchBagsBalance = 0;
                        $feedbagTotalCount = 0;
                        $feedBooster = 0;
                        if (!empty($feedHeader)) {
                            foreach ($feedHeader as $feed) {
                                $booster = ($feed["feedAmount"] * $totalChicksGiven) / 50;
                                echo "<tr class='hoverTr'>";
                                echo "<td>" . $feed["changedDate"] . "</td>";
                                echo "<td>" . $feed["feedName"] . "</td>";
                                echo "<td style='text-align: center'>" . $feed["feedAmount"] . "</td>";
                                echo "<td style='text-align: center'>" . round($booster) . "</td>";
                                $feedBooster = $this->db->query("SELECT sum(qty) AS booster FROM srp_erp_buyback_dispatchnote dpm LEFT JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 2 AND feedType = {$feed['buybackFeedtypeID']} WHERE batchMasterID ={$batchDetail['batchMasterID']} AND dpm.companyID = {$companyID}")->row_array();
                                if (!empty($feedBooster)) {
                                    $feedBooster = $feedBooster['booster'];
                                }
                                $balanceFeedType = (round($booster) - $feedBooster);
                                echo "<td style='text-align: center'>" . round($feedBooster) . "</td>";
                                echo "<td style='text-align: center'>" . round($balanceFeedType) . "</td>";
                                echo "</tr>";
                                $feedPersentageTotal += $feed["feedAmount"];
                                $feedbagTotalCount += round($booster);
                                $dispatchBags += $feedBooster;
                                $dispatchBagsBalance += $balanceFeedType;
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-center reporttotal"><?php echo $feedPersentageTotal; ?></td>
                            <td class="text-center reporttotal"><?php echo $feedbagTotalCount; ?></td>
                            <td class="text-center reporttotal"><?php echo $dispatchBags; ?></td>
                            <td class="text-center reporttotal"><?php echo $dispatchBagsBalance; ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php }
    $batchClosingDate = $batchDetail['batchClosingDate'];
    if (!empty($dispatch)) {
    ?>
    <br>

    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <div class="fixHeader_Div">
                <table class="borderSpace report-table-condensed" id="tbl_report" style="width: 100%">
                    <thead class="report-header">
                    <tr>
                        <th rowspan="2">Age</th>
                        <th rowspan="2">Dispatch Number</th>
                        <th rowspan="2">Description</th>
                        <th rowspan="2">Date</th>
                      <!--  <th rowspan="2">FVR</th> -->
                        <th rowspan="2">Feed Given</th>
                        <th rowspan="2">Cumulative Feed</th>
                        <!--<th colspan="2">Weight</th>
                        <th colspan="2">Feed</th>-->
                        <th rowspan="2">Next Input Date</th>
                        <th rowspan="2">Days Before</th>
                    </tr>
                  <!--  <tr>
                        <th>Less</th>
                        <th>More</th>
                        <th>Less</th>
                        <th>More</th>
                    </tr>-->
                    </thead>
                    <tbody>
                    <?php
                    $zx = 1;
                    $y = 0;
                    $format_firstDate = '';
                    $balanceFeed = 0;
                    $totalChicksGiven;
                    if (!empty($dispatch)) {
                        foreach ($dispatch as $row) {
                            $format_nextInputDay = '-';
                            $format_nextInputDay = '-';
                            if ($balanceFeed == 0) {
                                $balanceFeed = $row["qty"];
                            } else {
                                $balanceFeed += $row["qty"];
                            }
                            if(empty($totalChicksGiven)){
                                $totalChicksGiven = 1;
                            }
                            $cumalativeFeed = ($balanceFeed * 50) / $totalChicksGiven;
                            $cal_nextinputDate = $cumalativeFeed * 1000;

                            $currentAgeCalculation = $this->db->query("SELECT max(age) as currentAge FROM srp_erp_buyback_feedscheduledetail WHERE companyID = {$companyID} AND totalAmount <= {$cal_nextinputDate} ")->row_array();

                            if (!empty($currentAgeCalculation)) {
                                $currentAgeCalculation_days = $currentAgeCalculation['currentAge'];
                                $nextInputDay = strtotime("+ $currentAgeCalculation_days day", strtotime($dispatchFirstDate['documentDate']));
                                $format_nextInputDay = date("d-m-Y", $nextInputDay);

                                $daysBefore_days = ($currentAgeCalculation_days - 4);
                                $daysBefore_date = strtotime("+ $daysBefore_days day", strtotime($dispatchFirstDate['documentDate']));
                                $format_daysBeforeDay = date("d-m-Y", $daysBefore_date);
                            }
                            if (empty($format_firstDate)) {
                                $showFVRDate = $this->db->query("SELECT DATE_FORMAT(documentDate, '%Y-%m-%d') AS documentDate FROM srp_erp_buyback_farmervisitreport WHERE companyID = {$companyID} AND batchMasterID = {$batchDetail['batchMasterID']} AND confirmedYN = 1 AND documentDate = {$row['documentDate']}")->row_array();
                                echo "<tr class='hoverTr'>";
                                echo "<td>" . $zx . "</td>";
                                echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $row["documentID"] . '\',\'' .  $row['dispatchAutoID'] . '\',\' '. $batchDetail["batchMasterID"] .'\')">' . $row["documentSystemCode"] . '</a></td>';
                              //  echo "<td><a href='#' onclick='generateDispatchNoteReport(" . $row['dispatchAutoID'] . ")'>" . $row["documentSystemCode"] . "</a></td>";
                                echo "<td>" . $row["itemDescription"] . "</td>";
                                echo "<td>" . $row["documentDate"] . " </td>";

                            //        echo "<td class='text-center'>-</td>";

                                echo "<td class='text-center'>" . $row["qty"] . "</td>";
                                echo "<td class='text-center'>" . number_format($cumalativeFeed, 2) . "</td>";
                                /*echo "<td class='text-center'>-</td>";
                                echo "<td class='text-center'>-</td>";
                                echo "<td class='text-center'>-</td>";
                                echo "<td class='text-center'>-</td>";*/
                                echo "<td class='text-center'>" . $format_nextInputDay . "</td>";
                                echo "<td class='text-center'>" . $format_daysBeforeDay . "</td>";
                                echo "</tr>";
                                $zx++;
                                $format_firstDate = $row["documentDate"];
                            } else {
                                if ($format_firstDate != $row["documentDate"]) {
                                    $first_date = new DateTime($format_firstDate);
                                    $first_date->modify('+1 day');
                                    $plus_format_firstDate = $first_date->format('d-m-Y');

                                    $second_date = new DateTime($row["documentDate"]);
                                    $second_date->modify('-1 day');
                                    $plus_format_secondDate = $second_date->format('d-m-Y');

                                    $period = new DatePeriod(new DateTime($plus_format_firstDate), new DateInterval('P1D'), new DateTime($plus_format_secondDate . ' +1 day'));
                                    foreach ($period as $date) {
                                        $mvrReport ='';
                                        $showFVRDate = $this->db->query("SELECT farmerVisitID,documentSystemCode,DATE_FORMAT(documentDate, '%Y-%m-%d') AS documentDate FROM srp_erp_buyback_farmervisitreport WHERE companyID = {$companyID} AND batchMasterID = {$batchDetail['batchMasterID']} AND confirmedYN = 1 AND documentDate = '{$date->format("Y-m-d")}'")->row_array();
                                        if($date->format("Y-m-d") == $showFVRDate['documentDate']){
                                            $mvrReport = '&nbsp;&nbsp;<a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . 'BBFVR' . '\','.$showFVRDate["farmerVisitID"].')">' . $showFVRDate["documentSystemCode"] . '</a>';
                                        }
                                    //    var_dump($date);
                                        echo "<tr class='hoverTr'>";
                                        echo "<td >" . $zx . "</td>";
                                        echo "<td class='text-center'>-</td>";
                                        echo "<td class='text-center'>-</td>";
                                        echo "<td>" . $date->format("d-m-Y") . $mvrReport ."</td>";
                                        echo "<td class='text-center'>-</td>";
                                        echo "<td class='text-center'>-</td>";
                                       /* echo "<td class='text-center'>-</td>";
                                        echo "<td class='text-center'>-</td>";
                                        echo "<td class='text-center'>-</td>";
                                        echo "<td class='text-center'>-</td>";*/
                                        echo "<td class='text-center'>-</td>";
                                        echo "<td class='text-center'>-</td>";
                                        echo "</tr>";
                                        $zx++;
                                    }
                                    echo "<tr class='hoverTr'>";
                                    echo "<td>" . $zx . "</td>";
                                  //  var_dump($row["documentDate"]);
                                    echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $row["documentID"] . '\',\'' .  $row['dispatchAutoID'] . '\',\' '. $batchDetail["batchMasterID"] .'\')">' . $row["documentSystemCode"] . '</a></td>';
                                  //  echo "<td><a href='#' onclick='generateDispatchNoteReport(" . $row['dispatchAutoID'] . ")'>" . $row["documentSystemCode"] . "</a></td>";
                                    echo "<td>" . $row["itemDescription"] . "</td>";
                                    echo "<td>" . $row["documentDate"] ."</td>";
                                    echo "<td class='text-center'>" . $row["qty"] . "</td>";
                                    echo "<td class='text-center'>" . number_format($cumalativeFeed, 2) . "</td>";
                                   /* echo "<td class='text-center'>-</td>";
                                    echo "<td class='text-center'>-</td>";
                                    echo "<td class='text-center'>-</td>";
                                    echo "<td class='text-center'>-</td>";*/
                                    echo "<td class='text-center'>" . $format_nextInputDay . "</td>";
                                    echo "<td class='text-center'>" . $format_daysBeforeDay . "</td>";
                                    echo "</tr>";
                                    $format_firstDate = $row["documentDate"];
                                    $zx++;
                                } else {
                                    echo "<tr class='hoverTr'>";
                                    echo "<td>" . $zx . "</td>";
                                    echo "<td><a href='#' onclick='generateDispatchNoteReport(" . $row['dispatchAutoID'] . ")'>" . $row["documentSystemCode"] . "</a></td>";
                                    echo "<td>" . $row["itemDescription"] . "</td>";
                                    echo "<td>" . $row["documentDate"] . "</td>";
                                    echo "<td class='text-center'>" . $row["qty"] . "</td>";
                                    echo "<td class='text-center'>" . number_format($cumalativeFeed, 2) . "</td>";
                                   /* echo "<td class='text-center'>1-</td>";
                                    echo "<td class='text-center'>-</td>";
                                    echo "<td class='text-center'>-</td>";
                                    echo "<td class='text-center'>-</td>";*/
                                    echo "<td class='text-center'>" . $format_nextInputDay . "</td>";
                                    echo "<td class='text-center'>" . $format_daysBeforeDay . "</td>";
                                    echo "</tr>";
                                    $format_firstDate = $row["documentDate"];
                                    $zx++;
                                }
                            }
                            //echo $row["documentDate"]." | ".$format_firstDate."<br>";

                            if (strtotime($row["documentDate"]) != strtotime($format_firstDate)) {
                                $zx++;
                            }
                            if (strtotime($row["documentDate"]) >= strtotime($todayDate)) {
                                break;
                            }
                            $y++;
                        }
                        if ($y == sizeof($dispatch) && sizeof($dispatch) != 0) {
                            $newzx = $zx + 1;
                            $newzx = $zx;
                            $stop_first_date = new DateTime($format_firstDate);
                            $stop_first_date->modify('+1 day');
                            $changedDateStopeed = $stop_first_date->format('d-m-Y');

                            $period = new DatePeriod(new DateTime($changedDateStopeed), new DateInterval('P1D'), new DateTime($batchClosingDate . ' +1 day'));
                            foreach ($period as $date) {
                                $mvrReport ='';
                                $showFVRDate = $this->db->query("SELECT farmerVisitID,documentSystemCode,DATE_FORMAT(documentDate, '%Y-%m-%d') AS documentDate FROM srp_erp_buyback_farmervisitreport WHERE companyID = {$companyID} AND batchMasterID = {$batchDetail['batchMasterID']} AND confirmedYN = 1 AND documentDate = '{$date->format("Y-m-d")}'")->row_array();
                                if($date->format("Y-m-d") == $showFVRDate['documentDate']){
                                    $mvrReport = '&nbsp;&nbsp;<a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . 'BBFVR' . '\','.$showFVRDate["farmerVisitID"].')">' . $showFVRDate["documentSystemCode"] . '</a>';
                                }
                                echo "<tr class='hoverTr'>";
                                echo "<td >" . $newzx . "</td>";
                                echo "<td class='text-center'>-</td>";
                                echo "<td class='text-center'>-</td>";
                                echo "<td>" . $date->format("d-m-Y") .$mvrReport."</td>";
                                echo "<td class='text-center'>-</td>";
                                echo "<td class='text-center'>-</td>";
                              /*  echo "<td class='text-center'>-</td>";
                                echo "<td class='text-center'>-</td>";
                                echo "<td class='text-center'>-</td>";
                                echo "<td class='text-center'>-</td>";*/
                                echo "<td class='text-center'>-</td>";
                                echo "<td class='text-center'>-</td>";
                                echo "</tr>";
                                $newzx++;
                                if (strtotime($date->format("d-m-Y")) >= strtotime($todayDate)) {
                                    break;
                                }
                            }
                        }

                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?php
            } else {
                echo warning_message("No Records Found!");
            }
            ?>
        </div>
    </div>
</div>