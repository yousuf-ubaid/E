<!---- =============================================
-- File Name : erp_accounts_payable_vendor_aging_summary_report.php
-- Project Name : SME ERP
-- Module Name : Report - Accounts Payable
-- Create date : 05 - November 2016
-- Description : This file contains Vendor Aging Summary.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable_lang', $primaryLanguage);
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
$isVenCost = false;
$isTransCost = false;
if (isset($fieldName)) {
    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;
    }
    if (in_array("companyLocalAmount", $fieldName)) {
        $isLocCost = true;
    }
    if (in_array("supplierCurrencyAmount", $fieldName)) {
        $isVenCost = true;
    }
    if (in_array("transactionAmount", $fieldName)) {
        $isTransCost = true;
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_vendor_aging_summary', 'Vendor Aging Summary');
        } ?>
    </div>
</div>
<div id="tbl_vendor_aging_summary">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"><?php echo $this->lang->line('accounts_payable_reports_vs_vendor_aging_summary');?><!--Vendor Aging Summary--></div>
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As of-->: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php
            if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('accounts_payable_reports_vl_vendor');?><!--Vendor--></th>
                            <?php
                            if ($isVenCost || $isTransCost) {
                                echo "<th>$cur<!--Currency--></th>";
                            }
                            ?>
                            <th><?php echo $this->lang->line('accounts_payable_reports_vs_current');?><!--Current--></th>
                            <?php
                            if (!empty($aging)) {
                                foreach ($aging as $val2) {
                                    echo "<th>" . $val2 . "</th>";
                                }
                            }
                            ?>
                            <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = count($aging);
                        $category = array();
                        foreach ($output as $val) {
                            if ($isTransCost) {
                                $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["currency"]][] = $val;
                            } else {
                                $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][] = $val;
                            }
                        }
                        $grandTotal = array();
                        if (!empty($category)) {
                            foreach ($category as $key => $mainCategory) {
                                echo "<tr><td><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                                if($isTransCost) {
                                    foreach ($mainCategory as $key2 => $curr) {
                                        $transTotal = array();
                                        $decimalPlace = 2;
                                        foreach ($curr as $key3 => $supplier) {
                                            $total = 0;
                                            $total += $supplier["current"];
                                            echo "<tr class='hoverTr'>";
                                            echo "<td><div style='margin-left: 15px'>" . $supplier["supplierSystemCode"] . " - " . $supplier["supplierName"] . "</div></td>";
                                            if ($isVenCost || $isTransCost) {
                                                echo "<td>" . $supplier["currency"] . "</td>";
                                            }
                                            echo "<td class='text-right'>" . number_format($supplier["current"], $supplier["DecimalPlaces"]) . "</td>";
                                            $grandTotal["current"][] = $supplier["current"];
                                            $transTotal["current"][] = $supplier["current"];
                                            $i = 1;
                                            $supplierName = htmlspecialchars($supplier["supplierName"], ENT_QUOTES);
                                            foreach ($aging as $value) {
                                                $total += $supplier[$value];
                                                $grandTotal[$value][] = $supplier[$value];
                                                $transTotal[$value][] = $supplier[$value];
                                                if ($i == $count) {
                                                    if ($type == 'html') {
                                                        echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $supplier["supplierID"] . '\',\'' . htmlspecialchars($supplierName) . '\',\'' . $fieldName[0] . '\',\'' . $this->input->post('through') . '\')">' . number_format($supplier[$value], $supplier["DecimalPlaces"]) . '</a></td>';
                                                    } else {
                                                        echo '<td class="text-right">' . number_format($supplier[$value], $supplier["DecimalPlaces"]) . '</td>';
                                                    }
                                                } else {
                                                    if ($type == 'html') {
                                                        echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $supplier["supplierID"] . '\',\'' . htmlspecialchars($supplierName) . '\',\'' . $fieldName[0] . '\',\'' . $value . '\')">' . number_format($supplier[$value], $supplier["DecimalPlaces"]) . '</a></td>';
                                                    } else {
                                                        echo '<td class="text-right">' . number_format($supplier[$value], $supplier["DecimalPlaces"]) . '</td>';
                                                    }
                                                }
                                                $i++;
                                            }
                                            $grandTotal["total"][] = $total;
                                            $transTotal["total"][] = $total;
                                            echo "<td class='text-right'>" . number_format($total, $supplier["DecimalPlaces"]) . "</td>";
                                            echo "</tr>";
                                            $decimalPlace = $supplier["DecimalPlaces"];
                                        }
                                        echo '<tr>';
                                        echo "<td colspan='2'><b>Sub Total </b>&nbsp;</td>";
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($transTotal["current"]), $decimalPlace) . "</td>";

                                        foreach ($aging as $value) {
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($transTotal[$value]), $decimalPlace) . "</td>";
                                        }
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($transTotal["total"]), $decimalPlace) . "</td>";
                                        echo '</tr>';
                                    }
                                } else {
                                    foreach ($mainCategory as $key2 => $supplier) {
                                        $total = 0;
                                        $total += $supplier["current"];
                                        echo "<tr class='hoverTr'>";
                                        echo "<td><div style='margin-left: 15px'>" . $supplier["supplierSystemCode"] . " - " . $supplier["supplierName"] . "</div></td>";
                                        if ($isVenCost || $isTransCost) {
                                            echo "<td>" . $supplier["currency"] . "</td>";
                                        }
                                        echo "<td class='text-right'>" . number_format($supplier["current"], $supplier["DecimalPlaces"]) . "</td>";
                                        $grandTotal["current"][] = $supplier["current"];
                                        $i = 1;
                                        $supplierName = htmlspecialchars($supplier["supplierName"], ENT_QUOTES);
                                        foreach ($aging as $value) {
                                            $total += $supplier[$value];
                                            $grandTotal[$value][] = $supplier[$value];
                                            if ($i == $count) {
                                                if ($type == 'html') {
                                                    echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $supplier["supplierID"] . '\',\'' . htmlspecialchars($supplierName) . '\',\'' . $fieldName[0] . '\',\'' . $this->input->post('through') . '\')">' . number_format($supplier[$value], $supplier["DecimalPlaces"]) . '</a></td>';
                                                } else {
                                                    echo '<td class="text-right">' . number_format($supplier[$value], $supplier["DecimalPlaces"]) . '</td>';
                                                }
                                            } else {
                                                if ($type == 'html') {
                                                    echo '<td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $supplier["supplierID"] . '\',\'' . htmlspecialchars($supplierName) . '\',\'' . $fieldName[0] . '\',\'' . $value . '\')">' . number_format($supplier[$value], $supplier["DecimalPlaces"]) . '</a></td>';
                                                } else {
                                                    echo '<td class="text-right">' . number_format($supplier[$value], $supplier["DecimalPlaces"]) . '</td>';
                                                }
                                            }
                                            $i++;
                                        }
                                        $grandTotal["total"][] = $total;
                                        echo "<td class='text-right'>" . number_format($total, $supplier["DecimalPlaces"]) . "</td>";
                                        echo "</tr>";
                                    }
                                }
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan='<?php echo $count; ?>'>&nbsp;</td>
                        </tr>
                        <?php
                        if ($isRptCost || $isLocCost) {
                            echo "<tr>";
                            echo "<td><strong>$grand<!--Grand Total--></strong></td>";
                            if ($isRptCost) {
                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["current"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                            }
                            if ($isLocCost) {
                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["current"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                            }
                            if (!empty($aging)) {
                                foreach ($aging as $value) {
                                    if ($isRptCost) {
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal[$value]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                    }
                                    if ($isLocCost) {
                                        echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal[$value]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                    }
                                }
                            }
                            if ($isRptCost) {
                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["total"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                            }
                            if ($isLocCost) {
                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal["total"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                        </tfoot>
                    </table>
                </div>
                <?php
            } else {
                $norec=$this->lang->line('common_no_records_found');
                echo warning_message($norec);/*No Records Found!*/
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