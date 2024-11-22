<!---- =============================================
-- File Name : production_report.php
-- Project Name : SME ERP
-- Module Name : Report - Production Report
-- Create date : 09 - September 2017
-- Description : This file contains Buyback Production Report.

-- REVISION HISTORY
-- =============================================-->
<?php
$isRptCost = false;
$isLocCost = false;
$statusText = "";
?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_purchase_order_list', 'Purchase Order List');
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

            <div class="text-center reportHeader reportHeaderColor"> Production Statement</div>
        </div>
    </div>
    <?php if (!empty($output)) {
    ?>
    <hr style="border-top: 2px solid #8e2828;">
    <div class="row">
        <div class="col-sm-6 reportHeaderColor">
            <div class="row">
                <div class="col-sm-2">
                    <strong> Dealer :</strong>
                </div>
                <div class="col-sm-10">
                    Direct Farmers (D10004)
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <strong> Farmer :</strong>
                </div>
                <div class="col-sm-10">
                    <?php echo $batchDetail['farmerName']; ?>
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
                    <?php echo number_format(26526, 2); ?>
                </div>
            </div>
        </div>
    </div>
    <hr style="border-top: 2px solid #8e2828;">
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <div class="fixHeader_Div">
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $grandTotalrptAmount = 0;
                    $grandTotalReceiveAmount = 0;
                    $grandTotalBalanceAmount = 0;
                    if (!empty($output)) {
                        foreach ($output as $row) {
                            echo "<td>" . $row["documentDate"] . "</td>";
                            echo "<td>" . $row["itemDescription"] . "</td>";
                            echo "<td>" . $row["transactionQTY"] . "</td>";
                            echo "<td style='text-align: right'>" . number_format($row["unitTransferAmountTransaction"], 2) . "</td>";
                            echo "<td style='text-align: right'>" . number_format($row["totalTransferAmountTransaction"], 2) . "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan='10'>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <?php
                        echo "<tr>";
                        echo "<td colspan='5'> <strong>Grand Total</strong></td>";
                        if ($isRptCost) {
                            echo "<td></td>";
                            echo "<td class='reporttotal text-right'>" . format_number($grandTotalrptAmount, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                            echo "<td class='reporttotal text-right'>" . format_number($grandTotalReceiveAmount, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                            echo "<td class='reporttotal text-right'>" . format_number($grandTotalBalanceAmount, $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                        }
                        ?>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-12">
                    <span class="pull-right">
                    <button id="btn_lockGenerateReport" class="btn btn-primary" type="button"
                            onclick="lock_farmBatch()">Lock
                    </button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                        </span>
                </div>

            </div>
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