<!---- =============================================
-- File Name : erp_accounts_payable_vendor_statement_report.php
-- Project Name : SME ERP
-- Module Name : Report - Accounts Payable
-- Create date : 15 - September 2016
-- Description : This file contains Vendor Statement.

-- REVISION HISTORY
-- =============================================-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$asof=$this->lang->line('accounts_payable_reports_vl_as_of'); /*As of Language Transalation*/
$cur=$this->lang->line('common_currency'); /*Currency Language Transalation*/
    $deb=$this->lang->line('accounts_payable_reports_vs_debit'); /*Debit Language Transalation*/
    $cre=$this->lang->line('accounts_payable_reports_vs_credit'); /*Credit Language translation*/
$tot=$this->lang->line('common_total'); /*Total Language Transalation*/
$netbal=$this->lang->line('accounts_payable_reports_vl_net_balance');/*Net Balance*/
$grand=$this->lang->line('common_grand_total');/*Grand Total*/



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
?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_vendor_statement', 'Vendor Statement');
        } ?>
    </div>
</div>
<div id="tbl_vendor_statement">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('accounts_payable_reports_vs_vendor_statement');?><!--Vendor Statement--></div>
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As Of-->: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th rowspan="2"><?php echo $this->lang->line('accounts_payable_reports_vl_doc_type');?><!--Doc Type--></th>
                            <th rowspan="2"><?php echo $this->lang->line('accounts_payable_reports_vl_doc_date');?><!--Doc Date--></th>
                            <th rowspan="2"><?php echo $this->lang->line('accounts_payable_reports_vl_doc_number');?><!--Doc Number--></th>
                            <th rowspan="2"><?php echo $this->lang->line('accounts_payable_reports_vs_doc_due_date');?><!--Doc Due Date--></th>
                            <th rowspan="2"><?php echo $this->lang->line('common_invoice_code');?><!--Invoice Code--></th>
                            <th rowspan="2"><?php echo $this->lang->line('common_narration');?><!--Narration--></th>
                            <th rowspan="2"><?php echo $this->lang->line('common_reference_no');?><!--Reference No--></th>
                            <th rowspan="2"><?php echo $this->lang->line('accounts_payable_common_aging');?><!--Aging--></th>
                            <?php
                            if (!empty($caption)) {
                                foreach ($caption as $val) {
                                    if ($val == "Transaction Currency") {
                                        echo '<th colspan="3">' . $val . '</th>';
                                    } else {
                                        if ($val == "Reporting Currency") {
                                            echo '<th colspan="2">' . $val . '(' . $this->common_data['company_data']['company_reporting_currency'] . ')</th>';
                                        }
                                        if ($val == "Local Currency") {
                                            echo '<th colspan="2">' . $val . '(' . $this->common_data['company_data']['company_default_currency'] . ')</th>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </tr>
                        <tr>
                            <?php
                            if (!empty($fieldNameDetails)) {
                                foreach ($fieldNameDetails as $val) {
                                    if ($val['fieldName'] == 'transactionAmount') {
                                        echo '<th>'.$cur.'<!--Currency--></th>';
                                        echo '<th>'.$deb.'<!--Debit--></th>';
                                        echo '<th>'.$cre.'<!--Credit--></th>';
                                    } else {
                                        echo '<th>'.$deb.'<!--Debit--></th>';
                                        echo '<th>'.$cre.'<!--Credit--></th>';
                                    }

                                }
                            }
                            ?>
                        </tr>
                        </thead>
                        <?php
                        $count = 12;
                        $category = array();
                        if ($isTransCost && !$isRptCost && !$isLocCost) {
                            foreach ($output as $val) {
                                $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["supplierSystemCode"] . " - " . $val["supplierName"]][$val["transactionAmountcurrency"]][$val["type"]][] = $val;
                            }
                        } else {
                            foreach ($output as $val) {
                                $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["supplierSystemCode"] . " - " . $val["supplierName"]][$val["type"]][] = $val;
                            }
                        }
                        $grandtotal = array();
                        if ($isTransCost && !$isRptCost && !$isLocCost) {
                            if (!empty($category)) {
                                foreach ($category as $key => $glcodes) {
                                    echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead2'>" . $key . "</div></td></tr>";
                                    foreach ($glcodes as $key2 => $currency) {
                                        echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'>" . $key2 . "</div></td></tr>";
                                        foreach ($currency as $key3 => $suppliers) {
                                            $subtotal = array();
                                            $currencyDecimalPlace = "";
                                            foreach ($suppliers as $key4 => $suppliers1) {
                                                if($key4 == 2)
                                                {
                                                    echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'><b><u>Post Dated Cheques</u></b></div></td></tr>";
                                                }

                                            foreach ($suppliers1 as $key5 => $val) {
                                                echo "<tr class='hoverTr'>";
                                                echo "<td>" . $val["documentID"] . "</td>";
                                                echo "<td>" . $val["bookingDate"] . "</td>";
                                                if ($type == 'html') {
                                                    echo '<td> <div style="margin-left: 30px"><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentID"] . '\',' . $val["InvoiceAutoID"] . ')">' . $val["bookingInvCode"] . '</a></div></td>';
                                                } else {
                                                    echo '<td> <div style="margin-left: 30px">' . $val["bookingInvCode"] . '</div></td>';
                                                }
                                                
                                                echo "<td>" . $val["invoiceDueDate"] . "</td>";
                                                echo "<td>" . $val["supplierInvoiceNo"] . "</td>";
                                                echo "<td>" . $val["comments"] . "</td>";
                                                echo "<td>" . $val["referenceNo"] . "</td>";
                                                echo "<td>" . $val["age"] . "</td>";
                                                if (!empty($fieldNameDetails)) {
                                                    foreach ($fieldNameDetails as $val2) {
                                                        $subtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                                        $grandtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                                        if ($val2["fieldName"] == 'transactionAmount') {
                                                            $currencyDecimalPlace = $val[$val2["fieldName"] . "DecimalPlaces"];
                                                            echo "<td>" . $val[$val2["fieldName"] . "currency"] . "</td>";
                                                            echo print_debit_credit($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]);
                                                        } else {
                                                            echo print_debit_credit($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]);
                                                        }
                                                    }
                                                }
                                                echo "</tr>";
                                            }
                                            }

                                            echo "<tr>";
                                            echo "<td colspan='9'><div style='margin-left: 30px' class='pull-right'>$tot<!--Total--></div></td>";
                                            if (!empty($fieldNameDetails)) {
                                                foreach ($fieldNameDetails as $key => $val2) {
                                                    $newArray2 = $subtotal[$val2['fieldName']];
                                                    $pos_arr = array();
                                                    $neg_arr = array();
                                                    foreach ($newArray2 as $val) {
                                                        ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                                    }
                                                    $positiveAmount = array_sum($pos_arr);
                                                    $negativeAmount = array_sum($neg_arr);
                                                    if ($val2['fieldName'] == "transactionAmount") {
                                                        echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $currencyDecimalPlace) . '</td>';
                                                        echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $currencyDecimalPlace) . '</td>';
                                                    }
                                                }
                                            }
                                            echo "</tr>";

                                            echo "<tr>";
                                            echo "<td colspan='9'><div style='margin-left: 30px' class='pull-right'>$netbal<!--Net Balance--></div></td>";
                                            if (!empty($fieldNameDetails)) {
                                                foreach ($fieldNameDetails as $key => $val2) {
                                                    $newArray2 = $subtotal[$val2['fieldName']];
                                                    $pos_arr = array();
                                                    $neg_arr = array();
                                                    foreach ($newArray2 as $val) {
                                                        ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                                    }
                                                    $positiveAmount = array_sum($pos_arr);
                                                    $negativeAmount = array_sum($neg_arr);
                                                    $balance = $negativeAmount + $positiveAmount;
                                                    if ($val2['fieldName'] == "transactionAmount") {
                                                        if ($balance < 0) {
                                                            echo "<td class='text-right'></td><td class='text-right reporttotal'>" . number_format(abs($balance), $currencyDecimalPlace) . "</td>";
                                                        } else {
                                                            if ($balance > 0) {
                                                                echo "<td  class='text-right reporttotal'>" . number_format($balance, $currencyDecimalPlace) . "</td><td class='text-right'></td>";
                                                            } else {
                                                                echo "<td  class='text-right reporttotal'></td><td class='text-right reporttotal'></td>";
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            echo "</tr>";
                                        }
                                    }
                                }
                            }
                        } else {
                            if (!empty($category)) {
                                foreach ($category as $key => $glcodes) {
                                    echo "<tr><td colspan='" . $count . "'><div class='mainCategoryHead2'>" . $key . "</div></td></tr>";
                                    foreach ($glcodes as $key2 => $suppliers) {
                                        echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'>" . $key2 . "</div></td></tr>";
                                        $subtotal = array();
                                        foreach ($suppliers as $key3 => $val1) {
                                            if($key3 == 2)
                                            {
                                                echo "<tr><td colspan='" . $count . "'><div style='margin-left: 15px' class='mainCategoryHead2'><b><u>Post Dated Cheques</u></b></div></td></tr>";
                                            }
                                        foreach ($val1 as $key4 => $val) {
                                            echo "<tr class='hoverTr'>";
                                            echo "<td>" . $val["documentID"] . "</td>";
                                            echo "<td>" . $val["bookingDate"] . "</td>";
                                            if ($type == 'html') {
                                                echo '<td> <div style="margin-left: 30px"><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $val["documentID"] . '\',' . $val["InvoiceAutoID"] . ')">' . $val["bookingInvCode"] . '</a></div></td>';
                                            } else {
                                                echo '<td> <div style="margin-left: 30px">' . $val["bookingInvCode"] . '</div></td>';
                                            }
                                            
                                            echo "<td>" . $val["invoiceDueDate"] . "</td>";
                                            echo "<td>" . $val["supplierInvoiceNo"] . "</td>";
                                            echo "<td>" . $val["comments"] . "</td>";
                                            echo "<td>" . $val["referenceNo"] . "</td>";
                                            echo "<td>" . $val["age"] . "</td>";
                                            if (!empty($fieldNameDetails)) {
                                                foreach ($fieldNameDetails as $val2) {
                                                    $subtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                                    $grandtotal[$val2["fieldName"]][] = $val[$val2["fieldName"]];
                                                    if ($val2["fieldName"] == 'transactionAmount') {
                                                        echo "<td>" . $val[$val2["fieldName"] . "currency"] . "</td>";
                                                        echo print_debit_credit($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]);
                                                    } else {
                                                        echo print_debit_credit($val[$val2["fieldName"]], $val[$val2["fieldName"] . "DecimalPlaces"]);
                                                    }
                                                }
                                            }
                                            echo "</tr>";
                                        }
                                        }
                                        echo "<tr>";
                                        if ($isLocCost || $isRptCost) {
                                            if ($isTransCost) {
                                                echo "<td colspan='11'><div style='margin-left: 30px' class='pull-right'>$tot<!--Total--></div></td>";
                                            } else {
                                                echo "<td colspan='8'><div style='margin-left: 30px' class='pull-right'>$tot<!--Total--></div></td>";
                                            }
                                        }
                                        if (!empty($fieldNameDetails)) {
                                            foreach ($fieldNameDetails as $key => $val2) {
                                                $newArray2 = $subtotal[$val2['fieldName']];
                                                $pos_arr = array();
                                                $neg_arr = array();
                                                foreach ($newArray2 as $val) {
                                                    ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                                }
                                                $positiveAmount = array_sum($pos_arr);
                                                $negativeAmount = array_sum($neg_arr);
                                                if ($val2['fieldName'] == "companyLocalAmount") {
                                                    echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                                    echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                                }
                                                if ($val2['fieldName'] == "companyReportingAmount") {
                                                    echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                                    echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                                }

                                            }
                                        }
                                        echo "</tr>";

                                        echo "<tr>";
                                        if ($isLocCost || $isRptCost) {
                                            if ($isTransCost) {
                                                echo "<td colspan='11'><div style='margin-left: 30px' class='pull-right'>$netbal<!--Net Balance--></div></td>";
                                            } else {
                                                echo "<td colspan='8'><div style='margin-left: 30px' class='pull-right'>$netbal<!--Net Balance--></div></td>";
                                            }
                                        }
                                        if (!empty($fieldNameDetails)) {
                                            foreach ($fieldNameDetails as $key => $val2) {
                                                $newArray2 = $subtotal[$val2['fieldName']];
                                                $pos_arr = array();
                                                $neg_arr = array();
                                                foreach ($newArray2 as $val) {
                                                    ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                                }
                                                $positiveAmount = array_sum($pos_arr);
                                                $negativeAmount = array_sum($neg_arr);
                                                $balance = $negativeAmount + $positiveAmount;
                                                if ($val2['fieldName'] == "companyLocalAmount") {
                                                    if ($balance < 0) {
                                                        echo "<td class='text-right'></td><td class='text-right reporttotal'>" . number_format(abs($balance), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                    } else {
                                                        if ($balance > 0) {
                                                            echo "<td  class='text-right reporttotal'>" . number_format($balance, $this->common_data['company_data']['company_default_decimal']) . "</td><td class='text-right'></td>";
                                                        } else {
                                                            echo "<td  class='text-right reporttotal'></td><td class='text-right reporttotal'></td>";
                                                        }
                                                    }
                                                }
                                                if ($val2['fieldName'] == "companyReportingAmount") {
                                                    if ($balance < 0) {
                                                        echo "<td class='text-right'></td><td class='text-right reporttotal'>" . number_format(abs($balance), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                    } else {
                                                        if ($balance > 0) {
                                                            echo "<td  class='text-right reporttotal'>" . number_format($balance, $this->common_data['company_data']['company_reporting_decimal']) . "</td><td class='text-right'></td>";
                                                        } else {
                                                            echo "<td  class='text-right reporttotal'></td><td class='text-right reporttotal'></td>";
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        echo "</tr>";
                                    }
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
                            if ($isTransCost) {
                                echo "<td colspan='11'><div style='margin-left: 30px' class='pull-right'><strong>$grand<!--Grand Total--></strong></div></td>";
                            } else {
                                echo "<td colspan='8'><div style='margin-left: 30px' class='pull-right'><strong>$grand<!--Grand Total--></strong></div></td>";
                            }
                        }
                        if (!empty($fieldNameDetails)) {
                            foreach ($fieldNameDetails as $key => $val2) {
                                $newArray2 = $grandtotal[$val2['fieldName']];
                                $pos_arr = array();
                                $neg_arr = array();
                                foreach ($newArray2 as $val) {
                                    ($val < 0) ? $neg_arr[] = $val : $pos_arr[] = $val;
                                }
                                $positiveAmount = array_sum($pos_arr);
                                $negativeAmount = array_sum($neg_arr);
                                if ($val2['fieldName'] == "companyLocalAmount") {
                                    echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                    echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_default_decimal']) . '</td>';
                                }
                                if ($val2['fieldName'] == "companyReportingAmount") {
                                    echo '<td class="text-right reporttotal">' . number_format($positiveAmount, $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                    echo '<td class="text-right reporttotal">' . number_format(abs($negativeAmount), $this->common_data['company_data']['company_reporting_decimal']) . '</td>';
                                }
                            }
                        }
                        echo "</tr>";
                        ?>

                    </table>
                </div>
                <?php
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*No Records Found!*/
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