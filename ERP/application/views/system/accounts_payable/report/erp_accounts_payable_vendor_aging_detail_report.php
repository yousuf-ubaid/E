<!---- =============================================
-- File Name : erp_accounts_payable_vendor_aging_detail_report.php
-- Project Name : SME ERP
-- Module Name : Report - Accounts Payable
-- Create date : 05 - November 2016
-- Description : This file contains Vendor Aging Detail.

-- REVISION HISTORY
-- =============================================-->
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_payable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

$asof=$this->lang->line('accounts_payable_reports_vl_as_of'); /*as of language transalate*/
$subtot=$this->lang->line('accounts_payable_reports_vs_sub_total'); /*sub total language transalate*/
$grand=$this->lang->line('common_grand_total'); /*grand total language translate*/


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
            echo export_buttons('tbl_vendor_aging_Detail', 'Vendor Aging Detail');
        } ?>
    </div>
</div>
<div id="tbl_vendor_aging_Detail">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('accounts_payable_reports_vs_vendor_aging_detail');?><!--Vendor Aging Detail--></div>
            <div
                    class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As of-->: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('accounts_payable_reports_vs_documet_code');?><!--Document Code--></th>
                            <th><?php echo $this->lang->line('accounts_payable_reports_vs_documet_type');?><!--Document Type--></th>
                            <th><?php echo $this->lang->line('accounts_payable_reports_vs_documet_date');?><!--Document Date--></th>
                            <th>Doc Due Date<!--Invoice Code--></th>
                            <th>Supplier Invoice Code<!--Invoice Code--></th>
                            <th><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
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
                        $category = array();
                        foreach ($output as $val) {
                            if($isTransCost) {
                                $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["supplierSystemCode"] . " - " . $val["supplierName"]][$val["currency"]][] = $val;
                            } else {
                                $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["supplierSystemCode"] . " - " . $val["supplierName"]][] = $val;
                            }
                        }
                        $grandTotal = array();
                        if (!empty($category)) {
                            foreach ($category as $key => $mainCategory) {
                                echo "<tr><td><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                                foreach ($mainCategory as $key2 => $subCategory) {
                                    echo "<tr><td><div style='margin-left: 15px' class='mainCategoryHead2'>" . $key2 . "</div></td></tr>";
                                    $subTotal = array();

                                    if($isTransCost) {
                                        foreach ($subCategory as $key3 => $curr) {
                                            $transTotal = array();
                                            foreach ($curr as $key4 => $supplier) {
                                                $total = 0;
                                                $total += $supplier["current"];
                                                echo "<tr class='hoverTr'>";
                                                if ($type == 'html') {
                                                    echo '<td><div style="margin-left: 30px"><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $supplier["documentID"] . '\',' . $supplier["invoiceAutoID"] . ')">' . $supplier["documentCode"] . '</a></div></td>';
                                                } else {
                                                    echo '<td><div style="margin-left: 30px">' . $supplier["documentCode"] . '</div></td>';
                                                }

                                                echo "<td>" . $supplier["documentID"] . "</td>";
                                                echo "<td>" . $supplier["documentDate"] . "</td>";
                                                echo "<td>" . $supplier["invoiceDueDate"] . "</td>";
                                                echo "<td>" . $supplier["supplierInvoiceNo"] . "</td>";
                                                echo "<td>" . $supplier["currency"] . "</td>";
                                                echo "<td class='text-right'>" . number_format($supplier["current"], $supplier["DecimalPlaces"]) . "</td>";
                                                $grandTotal["current"][] = $supplier["current"];
                                                $transTotal["current"][] = $supplier["current"];
                                                $subTotal["current"][] = $supplier["current"];
                                                foreach ($aging as $value) {
                                                    $total += $supplier[$value];
                                                    $grandTotal[$value][] = $supplier[$value];
                                                    $subTotal[$value][] = $supplier[$value];
                                                    $transTotal[$value][] = $supplier[$value];
                                                    echo "<td class='text-right'>" . number_format($supplier[$value], $supplier["DecimalPlaces"]) . "</td>";
                                                }
                                                $grandTotal["total"][] = $total;
                                                $transTotal["total"][] = $total;
                                                $subTotal["total"][] = $total;
                                                echo "<td class='text-right'>" . number_format($total, $supplier["DecimalPlaces"]) . "</td>";
                                                echo "</tr>";
                                                $decimalPlace = $supplier["DecimalPlaces"];
                                            }
                                            echo '<tr>';
                                            echo '<td colspan="6"><b>Sub Total</b> &nbsp;</td>';
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($transTotal["current"]), $decimalPlace) . "</td>";
                                            foreach ($aging as $value) {
                                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($transTotal[$value]), $decimalPlace) . "</td>";
                                            }
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($transTotal["total"]), $decimalPlace) . "</td>";
                                            echo '</tr>';
                                        }
                                    } else {
                                        foreach ($subCategory as $key3 => $supplier) {
                                            $total = 0;
                                            $total += $supplier["current"];
                                            echo "<tr class='hoverTr'>";
                                            if ($type == 'html') {
                                                echo '<td><div style="margin-left: 30px"><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $supplier["documentID"] . '\',' . $supplier["invoiceAutoID"] . ')">' . $supplier["documentCode"] . '</a></div></td>';
                                            } else {
                                                echo '<td><div style="margin-left: 30px">' . $supplier["documentCode"] . '</div></td>';
                                            }

                                            echo "<td>" . $supplier["documentID"] . "</td>";
                                            echo "<td>" . $supplier["documentDate"] . "</td>";
                                            echo "<td>" . $supplier["invoiceDueDate"] . "</td>";
                                            echo "<td>" . $supplier["supplierInvoiceNo"] . "</td>";
                                            echo "<td>" . $supplier["currency"] . "</td>";
                                            echo "<td class='text-right'>" . number_format($supplier["current"], $supplier["DecimalPlaces"]) . "</td>";
                                            $grandTotal["current"][] = $supplier["current"];
                                            $subTotal["current"][] = $supplier["current"];
                                            foreach ($aging as $value) {
                                                $total += $supplier[$value];
                                                $grandTotal[$value][] = $supplier[$value];
                                                $subTotal[$value][] = $supplier[$value];
                                                echo "<td class='text-right'>" . number_format($supplier[$value], $supplier["DecimalPlaces"]) . "</td>";
                                            }
                                            $grandTotal["total"][] = $total;
                                            $subTotal["total"][] = $total;
                                            echo "<td class='text-right'>" . number_format($total, $supplier["DecimalPlaces"]) . "</td>";
                                            echo "</tr>";
                                        }
                                    }

                                    if ($isRptCost || $isLocCost) {
                                        echo "<tr>";
                                        echo "<td colspan='6'><strong>$subtot<!--Sub Total--></strong></td>";
                                        if ($isRptCost) {
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($subTotal["current"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }
                                        if ($isLocCost) {
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($subTotal["current"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
                                        if (!empty($aging)) {
                                            foreach ($aging as $value) {
                                                if ($isRptCost) {
                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($subTotal[$value]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                                }
                                                if ($isLocCost) {
                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($subTotal[$value]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                                }
                                            }
                                        }
                                        if ($isRptCost) {
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($subTotal["total"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }
                                        if ($isLocCost) {
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($subTotal["total"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                                        }
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
                            echo "<td colspan='6'><strong>$grand<!--Grand Total--></strong></td>";
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
                echo warning_message($norec);
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