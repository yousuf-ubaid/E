<!---- =============================================
-- File Name : erp_accounts_receivable_customer_statement_report.php
-- Project Name : SME ERP
-- Module Name : Report - Accounts Receivable
-- Create date : 10 - November 2016
-- Description : This file contains Customer Statement.

-- REVISION HISTORY
-- =============================================-->
<?php
$isRptCost = false;
$isLocCost = false;
$isTransCost = false;
$isCustCost = false;
if (isset($fieldName)) {
    if ($fieldName == "companyReportingAmount") {
        $isRptCost = true;
    }
    if ($fieldName == "companyLocalAmount") {
        $isLocCost = true;
    }
    if ($fieldName == "transactionAmount") {
        $isTransCost = true;
    }
    if ($fieldName == "customerCurrencyAmount") {
        $isCustCost = true;
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_customer_statement', 'Customer Statement',$excel = TRUE, $pdf = TRUE, $btnSize = 'btn-xs', $functionName = 'generateDrilldownReportPdf()');
        } ?>
    </div>
</div>
<div id="tbl_customer_statement">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> Customer Statement</div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <table class="borderSpace report-table-condensed" id="tbl_report">
                    <thead class="report-header">
                    <tr>
                    <th rowspan="2">Doc Date</th>
                    <th rowspan="2">Doc Type</th>
                    <th rowspan="2">Doc Number</th>
                    <th rowspan="2">Narration</th>
                    <?php
                    if ($isTransCost) {
                        echo '<th colspan="3">Transaction Currency</th>';
                    } else if ($isCustCost) {
                        echo '<th colspan="3">Customer Currency</th>';
                    } else {
                        if ($isRptCost) {
                            echo '<th colspan="2">Reporting Currency(' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                        }
                        if ($isLocCost) {
                            echo '<th colspan="2">Local Currency(' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                        }
                    }
                    ?>
                    </tr>
                    <tr>
                        <?php
                        if ($fieldName == 'transactionAmount' || $fieldName == 'customerCurrencyAmount') {
                            echo '<th>Currency</th>';
                            echo '<th>Debit</th>';
                            echo '<th>Credit</th>';
                        } else {
                            echo '<th>Debit</th>';
                            echo '<th>Credit</th>';
                        }
                        ?>
                    </tr>
                    </thead>
                    <?php
                    $count = 6;
                    $category = array();
                    $customers_arr = array_column($customers, 'customerAutoID');
                    $customersall_arr = array_column($customersall, 'customerAutoID');
                    foreach ($output as $val) {
                        $cus_id = $val["customerID"];
                        $cus_sys_code = $val["customerSystemCode"];
                        $cus_name = $val["customerName"];
                        //echo '<pre>'; print_r($customers);exit;
                        $key = array_search($cus_id, $customers_arr);
                        if($groupbycus == 1){
                            if($key !== false){
                                if(in_array($customers[$key]["masterID"], $customersall_arr)){
                                    $cus_sys_code = $customers[$key]["customerSystemCode"];
                                    $cus_name = $customers[$key]["customerName"];
                                }
                            }
                        }

                        $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$cus_sys_code . " - " . $cus_name][] = $val;
//                        $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["customerSystemCode"] . " - " . $val["customerName"]][] = $val;
                    }
                    $grandtotal = array();
                    if (!empty($category)) {
                        foreach ($category as $key => $glcodes) {
                            echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead2'>" . $key . "</div></td></tr>";
                            foreach ($glcodes as $key2 => $customers) {
                                echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'>" . $key2 . "</div></td></tr>";
                                $subtotal = array();
                                foreach ($customers as $key3 => $val) {
                                    echo "<tr class='hoverTr'>";
                                    echo "<td><div style='margin-left: 30px'>" . $val["bookingDate"] . "</div></td>";
                                    echo "<td>" . $val["documentID"] . "</td>";
                                    if ($type == 'html') {
                                        switch ($template) {
                                            case 'buyback':
                                                echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentID"] . '\',' . $val["invoiceAutoID"] . ',\'buy\')">' . $val["bookingInvCode"] . '</a></td>';
                                                break;
                                            default:
                                                echo '<td><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentID"] . '\',' . $val["invoiceAutoID"] . ')">' . $val["bookingInvCode"] . '</a></td>';
                                        }
                                    }else{
                                        echo '<td> <div style="margin-left: 30px">' . $val["bookingInvCode"] . '</div></td>';
                                    }
                                    echo "<td>" . $val["comments"] . "</td>";
                                    $subtotal[$fieldName][] = $val[$fieldName];
                                    $grandtotal[$fieldName][] = $val[$fieldName];
                                    if ($fieldName == 'transactionAmount' || $fieldName == 'customerCurrencyAmount') {
                                        echo "<td>" . $val[$fieldName . "currency"] . "</td>";
                                        echo print_debit_credit($val[$fieldName], $val[$fieldName . "DecimalPlaces"]);
                                    } else {
                                        echo print_debit_credit($val[$fieldName], $val[$fieldName . "DecimalPlaces"]);
                                    }
                                    echo "</tr>";
                                }
                                echo "<tr>";
                                if ($isLocCost || $isRptCost) {
                                    echo "<td colspan='4'><div style='margin-left: 30px'>Net Balance</div></td>";
                                }
                                $newArray2 = $subtotal[$fieldName];
                                $pos_arr = array();
                                $neg_arr = array();
                                foreach ($newArray2 as $val) {
                                    ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                }
                                $positiveAmount = array_sum($pos_arr);
                                $negativeAmount = array_sum($neg_arr);
                                if ($isLocCost) {
                                    echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                    echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                }
                                if ($isRptCost) {
                                    echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                    echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                }

                                echo "</tr>";
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td colspan='<?php echo $count; ?>'>&nbsp;</td>
                    </tr>

                    <?php
                    echo "<tr>";
                    if ($isLocCost || $isRptCost) {
                        echo "<td colspan='4'><div style='margin-left: 30px'><strong>Grand Total</strong></div></td>";
                    }
                    $newArray2 = $grandtotal[$fieldName];
                    $pos_arr = array();
                    $neg_arr = array();
                    foreach ($newArray2 as $val) {
                        ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                    }
                    $positiveAmount = array_sum($pos_arr);
                    $negativeAmount = array_sum($neg_arr);
                    if ($isLocCost) {
                        echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                        echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_default_decimal']) . '</td>';
                    }
                    if ($isRptCost) {
                        echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                        echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                    }

                    echo "</tr>";
                    ?>
                </table>
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
</script>