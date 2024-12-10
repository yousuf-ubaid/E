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
    .bgcolour {
    background-color: #00c0ef;
        margin-top: 3%;
    }

    .item-label {
        color: #fff;
        height: 21px;
        width: 90px;
        position: absolute;
        font-weight: bold;
        padding-left: 10px;
        padding-top: 0px;
        top: 10px;
        right: -5px;
        margin-right: 0;
        border-radius: 3px 3px 0 3px;
        box-shadow: 0 3px 3px -2px #ccc;
        text-transform: capitalize;
    }
    .item-label:after {
        top: 20px;
        right: 0;
        border-top: 4px solid #1f1d1d;
        border-right: 4px solid rgba(0, 0, 0, 0);
        content: "";
        position: absolute;
    }
     .search-no-results {
         text-align: center;
         background-color: #f6f6f6;
         border: solid 1px #ddd;
         margin-top: 10px;
         padding: 1px;
     }

    .entity-detail .ralign, .property-table .ralign {
        text-align: right;
        color: gray;
        padding: 3px 10px 4px 0;
        width: 150px;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .title {
        color: #aaa;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .tddata {
        color: #333;
        padding: 4px 10px 0 0;
        font-size: 13px;
    }

    .nav-tabs > li > a {
        font-size: 11px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.3);
        color: rgb(130, 130, 130);
    }

    .nav-tabs > li > a:hover {
        background: rgb(230, 231, 234);
        font-size: 12px;
        line-height: 30px;
        height: 30px;
        position: relative;
        padding: 0 25px;
        float: left;
        display: block;
        /*color: rgb(44, 83, 158);*/
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: bold;
        text-align: center;
        border-radius: 3px 3px 0 0;
        border-color: transparent;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: #c0392b;
        cursor: default;
        background-color: #fff;
        font-weight: bold;
        border-bottom: 3px solid #f15727;
    }
    .reportHeaderColorbuyback
    {
        color: black;
    }
    .report-headercolor tr th {
        position: relative;
        top: 0px;
        padding: 0px !important;
    }
    .report-headercolor2 tr th {
        background-color: #ff4c36;

        position: relative;
        top: 0px;
        padding: 0px !important;
    }
    .stars
    {
        display: inline-block;color: #F0F0F0;text-shadow: 0 0 1px #666666;font-size:30px;
    }  .highlights,
       .selectedstars {color:#F4B30A;text-shadow: 0 0 1px #F48F0A;}

</style>

<?php
$isRptCost = false;
$isLocCost = false;
$statusText = "";
$companyID = $this->common_data['company_data']['company_id'];
?>
<br>
<div class="row" style="padding-left: 3%;">
    <div class="col-md-6">
        <div style="font-size: 16px; font-weight: 700;"><?php echo $batchDetail['farmerName'];?>
            <input type="hidden" class="form-control" name="batchMasterIDEdit" id="batchMasterIDEdit" value="<?php echo $batchDetail['batchMasterID'] ?>">
        </div>
    </div>
    <div class="col-md-6" style="padding-right: 4%;">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_purchase_order_list', 'Production Statement');
        } ?>
    </div>
</div>
<br>
<?php if($view == 'Batch Closing'){ ?>
<div class="row" style="padding-right: 4%;">
    <div class="col-md-8">&nbsp;</div>
    <div class="col-md-4">
           <?php echo fetch_account_review(true, false, 0); ?>
    </div>
</div>

<?php  } if($typecostYN !=1)
{?>
    <ul class="nav nav-tabs" id="main-tabs" style="padding-left: 4%;" >
    <li class="active"><a onclick="setProductionType(1)" href="#actual" data-toggle="tab"><i class="fa fa-television"></i>Actual</a></li>
    <li><a onclick="setProductionType(2)" href="#cost" data-toggle="tab"><i class="fa fa-television"></i>Cost</a></li>
    </ul>
<?php }?>
<?php if($batchDetail['isclosed']==1)
{
  echo  '<div class="post-area">
    <article class="post">
        <div class="item-label file bgcolour" style="margin-top: 5%">Closed</div>
    </article>
</div>';
}?>
<br>

<div class="tab-content">
    <div class="tab-pane active" id="actual" style="box-shadow: none; padding-left: 4%; padding-right: 4%;">
        <br>   <?php if (!empty($dispatch)) {
        $grand
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
                            <?php
                            $newFormattedDate = '0';
                            $chicksAge = $this->db->query("SELECT dpm.dispatchedDate,batch.closedDate FROM srp_erp_buyback_dispatchnote dpm INNER JOIN srp_erp_buyback_dispatchnotedetails dpd ON dpd.dispatchAutoID = dpm.dispatchAutoID AND buybackItemType = 1 LEFT JOIN srp_erp_buyback_batch batch ON batch.batchMasterID = dpm.batchMasterID WHERE dpm.batchMasterID ={$batchDetail['batchMasterID']} ORDER BY dpm.dispatchAutoID ASC")->row_array();
                            if (!empty($chicksAge)) {
                                $dStart = new DateTime($chicksAge['dispatchedDate']);
                                if ($chicksAge['closedDate'] != ' ') {
                                    $dEnd = new DateTime($chicksAge['closedDate']);
                                } else {
                                    $dEnd = new DateTime(current_date());
                                }
                                $dDiff = $dStart->diff($dEnd);
                                $newFormattedDate = $dDiff->days + 1;
                               // echo $newFormattedDate;
                            }


                            echo date("d-M-y") . " AGING - (" . $newFormattedDate . ")" ?>
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

                            $wage = wagesPayableAmount($batchDetail['batchMasterID'],TRUE);
                            $outstanding = ($wage['transactionAmount'] - $voucherDetails['transactionAmount']);
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
                                <th width="5%">Date</th>
                                <th width="5%" >Description</th>
                                <th width="5%">Quantity</th>
                                <th width="5%">Rate</th>
                                <th width="5%">Amount</th>
                                <th width="5%">Amount</th>
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
                                    echo "<td><a onclick='documentPageView_modal(\"BBDPN\"  , " . $row['documentAutoID'] . " , " . $row['batchID'] . ")'>" . $row["documentDate"] .' - '. $row["documentSystemCode"]." </a></td>";
                                    echo "<td>" . $row["itemDescription"] . "</td>";
                                    echo "<td style='text-align: right'>" . number_format($row["qty"]) . "</td>";
                                    echo "<td style='text-align: right'>" . number_format($row["unitActualCost"], 2) . "</td>";
                                    echo "<td style='text-align: right'>" . number_format($row["totalActualCost"], 2) . "</td>";
                                    echo "<td></td>";
                                    echo "</tr>";

                                    $grandTotalrptAmount += $row["totalActualCost"];
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
                                    echo "<td><a onclick='documentPageView_modal(\"BBPV\"  , " . $val['pvMasterAutoID'] . " )'>" . $val["documentDate"] .' - '. $val["documentSystemCode"] . "</a></td>";
                                    echo "<td>" . $val["expenseDescription"] . "</td>";
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
                                    echo "<td><a onclick='documentPageView_modal(\"BBDR\"  , " . $return['returnAutoID'] . " )'>". $return["returneddate"] .' - '. $return["documentSystemCode"]."</a></td>";
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
                                    echo "<td><a onclick='documentPageView_modal(\"BBGRN\"  , " . $buy['documentAutoID'] . " )'>". $buy["documentDate"] .' - '. $buy["documentSystemCode"]."</a></td>";
                                    echo "<td>Live Birds</td>";
                                    echo "<td style='text-align: right'>" .$buy["noOfBirds"] . "</td>";
                                    echo "<td style='text-align: right'>(" .$buy["transactionQTY"]. ") * " . number_format($buy["unitTransferAmountTransaction"], 2) . "</td>";
                                    echo "<td></td>";
                                    echo "<td style='text-align: right'>" . number_format($buy["totalTransferAmountTransaction"], 2) . "</td>";
                                    echo "</tr>";
                                    $birdsKGWeight += $buy["transactionQTY"];
                                    $birdsTotalCount += $buy["noOfBirds"];
                                    $grandTotalBuybackAmount += $buy["totalTransferAmountTransaction"];
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

                </div>
                <?php if($batchDetail['confirmedYN']==1 && $batchDetail['approvedYN']==1){ ?>
                <div class="table-responsive"><br>
                    <table style="width: 50%">
                        <tbody>
                        <tr>
                            <td><b>Closed Comment</b></td>
                            <td><strong>:</strong></td>
                            <td style="padding-left: 8%"><?php echo $batchDetail['closingComment']?></td>
                        </tr>
                        <tr>
                            <td><b>Grading</b></td>
                            <td><strong>:</strong></td>
                            <td>
                                <input type="hidden" name="rating" id="rating" value="<?php echo $batchDetail["grade"]; ?>" />
                                <ul>
                                    <?php
                                    for($i=1;$i<=6;$i++) {
                                        $selected = "";

                                            if(!empty($batchDetail["grade"]) && $i<=$batchDetail["grade"]) {
                                                $selected = "selectedstars";
                                            }

                                        ?>
                                        <li class='stars starsgrading <?php echo $selected; ?>'>★</li>
                                    <?php }  ?>
                                    <ul>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <?php }?>
            </div>
        </div>
    </div>
    <?php if($typecostYN !=1) {?>
    <div class="tab-pane" id="cost" style="box-shadow: none; padding-left: 4%; padding-right: 4%;">
        <br>
        <div id="tbl_purchase_order_list">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center reportHeaderColorbuyback">
                            <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
                        </div>
                        <div class="text-center reportHeaderColorbuyback">
                            <strong><?php echo $this->common_data['company_data']['companyPrintAddress'] ?> </strong>
                        </div>
                        <div class="text-center reportHeaderColorbuyback">
                            <strong>Tel : <?php echo $this->common_data['company_data']['companyPrintTelephone'] ?> </strong>
                        </div>

                        <div class="text-center reportHeader reportHeaderColorbuyback"> Production Statement</div>
                    </div>
                </div>

                <hr style="border-top: 1px solid #8e2828;">
                <div class="row">
                    <div class="col-sm-6 reportHeaderColorbuyback">
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
                    <div class="col-sm-6 reportHeaderColorbuyback">
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
                             //   $totalFarmerpay = 0;
                              //  $totalFarmerpay = $batchOutstanding['oustanding'] - $batchTotalPaid['wagesAmount'];
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
                                <thead class="report-headercolor2">
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Cost</th>
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
                                echo "<td><a onclick='documentPageView_modal(\"BBDPN\"  , " . $row['documentAutoID'] . " , " . $row['batchID'] . ")'>" . $row["documentDate"] .' - '. $row["documentSystemCode"]. "</a></td>";
                                echo "<td>" . $row["itemDescription"] . "</td>";
                                echo "<td style='text-align: right'>" . number_format($row["qty"]) . "</td>";
                                echo "<td style='text-align: right'>" . number_format($row["unitTransferCost"], 2) . "</td>";
                                echo "<td style='text-align: right'>" . number_format($row["totalTransferCost"], 2) . "</td>";
                                echo "<td></td>";
                                echo "</tr>";

                                $grandTotalrptAmount += $row["totalTransferCost"];
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
                                echo "<td><a onclick='documentPageView_modal(\"BBPV\"  , " . $val['pvMasterAutoID'] . " )'>" . $val["documentDate"] . "</a></td>";
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
                                    echo "<td><strong>RETURN</strong></td>";
                                    echo "</tr>";
                                    foreach ($returns as $return) {
                                        echo "<tr>";
                                        echo "<td><a onclick='documentPageView_modal(\"BBDR\"  , " . $return['returnAutoID'] . " )'>". $return["returneddate"] .' - '. $return["documentSystemCode"]."</a></td>";
                                        echo "<td>". $return["descriptiton"] ."</td>";
                                        echo "<td style='text-align: right'>". $return["returnedqty"] ."</td>";

                                        echo "<td style='text-align: right'> ".number_format($return["rate"],2)  ."</td>";
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
                                echo "<td><a onclick='documentPageView_modal(\"BBGRN\"  , " . $buy['documentAutoID'] . " )'>" . $buy["documentDate"] . ' - '. $buy["documentSystemCode"]."</a></td>";
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
                                        <strong> Mortalitys :</strong>
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
                                        <!--<span title="(Total Feed * 50 / ((Total Live Stock + Total Buyback Qty) / 2)" rel="tooltip">--><strong> Feed :</strong><!--</span>-->
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
                                    <div class="col-sm-8">
                                        <!--<span title="(  / ((Total Live Stock + Total Buyback Qty) / 2)" rel="tooltip">--><strong> Cost/Bird :</strong><!--</span>-->
                                    </div>
                                    <div class="col-sm-4">
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
                                       <!--<span title="(Transaction Qty / Total Buyback Qty)" rel="tooltip">--><strong> Weight :</strong><!--</span>-->
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

                    </div>
                    <?php }?>
                 </div>
             </div>
        </div>
    <?php

    if (($batchDetail['confirmedYN']!=1) || $batchDetail['confirmedYN']!=1 || $batchDetail['approvedYN']!=1) { ?>
        <div class="table-responsive"><br>
        <table style="width: 50%">
            <tbody>

            <tr>
                <?php
?>
                <td><b>Grading</b></td>
                <td><strong>:</strong></td>
                <td>
                    <?php if($fcr>=1.65 && $fcr<=1.75) {
                        $rating = 6;
                    }else if($fcr>=1.5 && $fcr<=1.64){
                        $rating = 5;
                    }else if($fcr>=1.76 && $fcr<=1.85){
                        $rating = 4;
                    }else if($fcr>=1.86 && $fcr<=2.00){
                        $rating = 3;
                    }else if($fcr<=1.5)
                    {
                        $rating = 1;
                    }

                    else {
                        $rating = 2;
                    }
                    ?>
                    <input type="hidden" name="rating" id="rating" value="<?php echo $rating ?>" />
                    <ul onMouseOut="resetRating();">
                        <?php
                        for($i=1;$i<=6;$i++) {
                            $selected = "";

                            if(!empty($rating) && $i<=$rating) {
                                $selected = "selectedstars";
                            }

                            ?>
                            <li class='stars starsgrading <?php echo $selected; ?>'>★</li>
                        <?php }  ?>
                        <ul>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
        <?php }?>
<?php
} else {
    echo warning_message("No Records Found!");
}
?>
</div>
<?php echo form_open('login/loginSubmit', ' name="prdctnRptPdfForm" id="prdctnRptPdfForm" class="form-group" role="form"'); ?>
<input class="hidden" id="batchMasterID" name="batchMasterID" value="<?php echo $batchDetail['batchMasterID'] ?>">
<input class="hidden" id="farmID" name="farmID" value="<?php echo $batchDetail['farmID'] ?>">
<input class="hidden" id="acutalorCost" name="acutalorCost" value="1">
</form>
<script>
    var acutalorCost = $('#acutalorCost').val();

    $(document).ready(function () {
        generatePaymentHistory();
        setProductionType(acutalorCost);
    });

    function generatePaymentHistory() {
        var batchMasterID = $('#batchMasterIDEdit').val();
        var farmID =  $('#farmID').val();

        <?php if ($batchDetail['isclosed'] == 0){ ?>
        $("#PaymentHistoryModal").addClass('hide');
        <?php } else { ?>
        $("#PaymentHistoryModal").removeClass('hide');
    <?php } ?>
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'html',
            data: {'batchMasterID' : batchMasterID, 'farmID' : farmID},
            url: '<?php echo site_url('Buyback/production_report_paymentHistory'); ?>',
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#PaymentHistory").html(data);
            }, error: function () {
                alert('An Error Occurred! Please Try Again.');
                stopLoad();
            }
        });
    }

    function generateReportPdf() {
        var fieldNameChk = [];
        var captionChk = [];
        $("input[name=fieldName]:checked").each(function () {
            fieldNameChk.push($(this).val());
            captionChk.push($(this).data('caption'));
        });

        var form = document.getElementById('prdctnRptPdfForm');
        //document.getElementById('fieldNameChkpdf').value = fieldNameChk;
        //document.getElementById('captionChkpdf').value = captionChk;
        form.target = '_blank';
        form.action = '<?php echo site_url('Buyback/buyback_production_report_pdf'); ?>';
        form.submit();
    }

    function generatePaymentHistoryPdf() {
        var form = document.getElementById('prdctnRptPdfForm');
        form.target = '_blank';
        form.action = '<?php echo site_url('Buyback/production_report_paymentHistory_pdf'); ?>';
        form.submit();

    }

    $('.review').removeClass('hide');
    de_link = "<?php echo site_url('Buyback/fetch_double_entry_buyback_batchClosing'); ?>/" + <?php echo $batchDetail['batchMasterID'] ?> +'/BBBC';
    $("#de_link").attr("href", de_link);

    function  setProductionType(acutalorCost){
        $('#acutalorCost').val(acutalorCost);
    }
</script>