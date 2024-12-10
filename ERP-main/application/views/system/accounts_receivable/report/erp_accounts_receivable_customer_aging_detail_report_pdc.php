<!--
-- =============================================
-- File Name : erp_accounts_receivable_customer_aging_detail_report.php
-- Project Name : SME ERP
-- Module Name : Report - Account receivable
-- Create date : 16 - November 2016
-- Description : This file contains customer aging detail report.

-- REVISION HISTORY
-- =============================================-->
<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('accounts_receivable', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$asof=$this->lang->line('accounts_receivable_common_as_of');
$subtot=$this->lang->line('accounts_receivable_common_sub_tot');
$grandt=$this->lang->line('common_grand_total');



$isRptCost = false;
$isLocCost = false;
$isCustCost = false;
$isTransCost = false;
if (isset($fieldName)) {
    if (in_array("companyReportingAmount", $fieldName)) {
        $isRptCost = true;
    }
    if (in_array("companyLocalAmount", $fieldName)) {
        $isLocCost = true;
    }
    if (in_array("customerCurrencyAmount", $fieldName)) {
        $isCustCost = true;
    }
    if (in_array("transactionAmount", $fieldName)) {
        $isTransCost = true;
    }
}
?>
<div class="row">
    <div class="col-md-12">
        <?php if ($type == 'html') {
            echo export_buttons('tbl_customer_aging_Detail', 'Customer Aging Detail');
        } ?>
    </div>
</div>
<div id="tbl_customer_aging_Detail">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center reportHeaderColor">
                <strong><?php echo $this->common_data['company_data']['company_name'] ?> </strong>
            </div>
            <div class="text-center reportHeader reportHeaderColor"> <?php echo $this->lang->line('accounts_receivable_rs_cad_customer_aging_detail');?><!--Customer Aging Detail--></div>
            <div class="text-center reportHeaderColor"> <?php echo "<strong>$asof<!--As of-->: </strong>" . $from ?></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <?php if (!empty($output)) { ?>
                <div class="fixHeader_Div">
                    <table class="borderSpace report-table-condensed" id="tbl_report">
                        <thead class="report-header">
                        <tr>
                            <th><?php echo $this->lang->line('accounts_receivable_common_document_code');?><!--Document Code--></th>

                            <th><?php echo $this->lang->line('accounts_receivable_common_document_type');?><!--Document Type--></th>
                            <th><?php echo $this->lang->line('common_document_date');?><!--Document Date--></th>
                            <th>Reference No</th>
                            <th>Doc Due Date</th>
                            <th><?php echo $this->lang->line('common_currency');?><!--Currency--></th>
                            <th><?php echo $this->lang->line('accounts_receivable_common_currenct');?><!--Current--></th>
                            <?php
                            if (!empty($aging)) {
                                foreach ($aging as $val2) {
                                    echo "<th>" . $val2 . "</th>";
                                }
                            }
                            ?>
                            <th><?php echo $this->lang->line('common_total');?><!--Total--></th>
                            <th>PDC Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $category = array();
                        foreach ($output as $val) {
                            if ($isTransCost) {
                                $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["customerSystemCode"] . " - " . $val["customerName"]][$val['currency']][] = $val;
                            } else {
                                $category[$val["GLSecondaryCode"] . " - " . $val["GLDescription"]][$val["customerSystemCode"] . " - " . $val["customerName"]][] = $val;
                            }
                        }
                        $grandTotal = array();
                        $grandTotal_pdc = array();
                        if (!empty($category)) {
                            foreach ($category as $key => $mainCategory) {
                                echo "<tr><td><div class='mainCategoryHead'>" . $key . "</div></td></tr>";
                                foreach ($mainCategory as $key2 => $subCategory) {
                                    echo "<tr><td><div style='margin-left: 15px' class='mainCategoryHead2'>" . $key2 . "</div></td></tr>";
                                    $subTotal = array();
                                    $subTotal_pdc = array();
                                    if($isTransCost) {
                                        foreach ($subCategory as $key3 => $currPDC) {
                                            $totalTrans = array();
                                            $totalTrans_pdc = array();
                                            $decimalPlace = 2;
                                            foreach ($currPDC as $key4 => $customer) {
                                                $total = 0;
                                                $total += $customer["current"];
                                                echo "<tr class='hoverTr'>";
                                                if ($type == 'html') {
                                                    switch ($template) {
                                                        case 'buyback':
                                                            echo '<td><div style="margin-left: 30px"><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $customer["documentID"] . '\',' . $customer["invoiceAutoID"] . ',\'buy\')">' . $customer["documentCode"] . '</a></div></td>';
                                                            break;
                                                        default:
                                                            echo '<td><div style="margin-left: 30px"><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $customer["documentID"] . '\',' . $customer["invoiceAutoID"] . ')">' . $customer["documentCode"] . '</a></div></td>';
                                                    }
                                                } else {
                                                    echo '<td><div style="margin-left: 30px">' . $customer["documentCode"] . '</div></td>';
                                                }

                                                echo "<td>" . $customer["documentID"] . "</td>";
                                                echo "<td>" . $customer["documentDate"] . "</td>";
                                                echo "<td>" . $customer["referenceNo"] . "</td>";
                                                echo "<td>" . $customer["invoiceDueDate"] . "</td>";
                                                echo "<td>" . $customer["currency"] . "</td>";
                                                echo "<td class='text-right'>" . number_format($customer["current"], $customer["DecimalPlaces"]) . "</td>";
                                                $grandTotal["current"][] = $customer["current"];
                                                $subTotal["current"][] = $customer["current"];
                                                $totalTrans["current"][] = $customer["current"];
                                                foreach ($aging as $value) {
                                                    $total += $customer[$value];
                                                    $grandTotal[$value][] = $customer[$value];
                                                    $subTotal[$value][] = $customer[$value];
                                                    $totalTrans[$value][] = $customer[$value];
                                                    echo "<td class='text-right'>" . number_format($customer[$value], $customer["DecimalPlaces"]) . "</td>";
                                                }
                                                $grandTotal["total"][] = $total;
                                                $subTotal["total"][] = $total;
                                                $totalTrans["total"][] = $total;

                                                $totalTrans_pdc["pdctotal"][] = $customer['transactionAmountPDC'];
                                                echo "<td class='text-right'>" . number_format($total, $customer["DecimalPlaces"]) . "</td>";
                                                echo "<td class='text-right'>" . number_format($customer['transactionAmountPDC'], $customer["DecimalPlaces"]) . "</td>";

                                                echo "</tr>";
                                                $decimalPlace = $customer['DecimalPlaces'];
                                            }
                                            echo "<tr>";
                                            echo "<td colspan='6'><strong>$subtot<!--Sub Total--></strong></td>";
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($totalTrans["current"]), $decimalPlace) . "</td>";
                                            if (!empty($aging)) {
                                                foreach ($aging as $value) {
                                                    echo "<td class='text-right reporttotal'>" . format_number(array_sum($totalTrans[$value]), $decimalPlace) . "</td>";
                                                }
                                            }
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum($totalTrans["total"]), $decimalPlace) . "</td>";
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum( $totalTrans_pdc["pdctotal"]), $decimalPlace) . "</td>";

                                            echo "</tr>";

                                        }
                                    } else {
                                        foreach ($subCategory as $key3 => $customer) {
                                            $total = 0;
                                            $total += $customer["current"];
                                            echo "<tr class='hoverTr'>";
                                            if ($type == 'html') {
                                                echo '<td><div style="margin-left: 30px"><a href="#" class="drill-down-cursor" onclick="documentPageView_modal(\'' . $customer["documentID"] . '\',' . $customer["invoiceAutoID"] . ')">' . $customer["documentCode"] . '</a></div></td>';
                                            } else {
                                                echo '<td><div style="margin-left: 30px">' . $customer["documentCode"] . '</div></td>';
                                            }

                                            echo "<td>" . $customer["documentID"] . "</td>";
                                            echo "<td>" . $customer["documentDate"] . "</td>";
                                            echo "<td>" . $customer["referenceNo"] . "</td>";
                                            echo "<td>" . $customer["invoiceDueDate"] . "</td>";
                                            echo "<td>" . $customer["currency"] . "</td>";
                                            echo "<td class='text-right'>" . number_format($customer["current"], $customer["DecimalPlaces"]) . "</td>";
                                            $grandTotal["current"][] = $customer["current"];
                                            $subTotal["current"][] = $customer["current"];
                                            foreach ($aging as $value) {
                                                $total += $customer[$value];
                                                $grandTotal[$value][] = $customer[$value];
                                                $subTotal[$value][] = $customer[$value];
                                                echo "<td class='text-right'>" . number_format($customer[$value], $customer["DecimalPlaces"]) . "</td>";
                                            }
                                            $grandTotal["total"][] = $total;
                                            $subTotal["total"][] = $total;
                                            if($isRptCost)
                                            {
                                                $subTotal_pdc["pdctotal"][] = $customer['companyReportingAmountPDC'];
                                                $grandTotal_pdc["pdctotal"][] = $customer['companyReportingAmountPDC'];
                                            }if($isLocCost)
                                            {
                                                $subTotal_pdc["pdctotal"][] = $customer['companyLocalAmountPDC'];
                                                $grandTotal_pdc["pdctotal"][] = $customer['companyLocalAmountPDC'];

                                            }

                                            echo "<td class='text-right'>" . number_format($total, $customer["DecimalPlaces"]) . "</td>";
                                            if($isRptCost)
                                            {
                                                echo "<td class='text-right'>" . number_format($customer['companyReportingAmountPDC'], $customer["DecimalPlaces"]) . "</td>";
                                            }if($isLocCost)
                                            {
                                                echo "<td class='text-right'>" . number_format($customer['companyLocalAmountPDC'], $customer["DecimalPlaces"]) . "</td>";
                                            }if($isTransCost)
                                            {
                                                echo "<td class='text-right'>" . number_format($customer['transactionAmountPDC'], $customer["DecimalPlaces"]) . "</td>";
                                            }

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
                                        if ($isRptCost) {
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum( $subTotal_pdc["pdctotal"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                                        }
                                        if ($isLocCost) {
                                            echo "<td class='text-right reporttotal'>" . format_number(array_sum( $subTotal_pdc["pdctotal"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
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
                            echo "<td colspan='6'><strong>$grandt<!--Grand Total--></strong></td>";
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
                            if ($isRptCost) {
                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal_pdc["pdctotal"]), $this->common_data['company_data']['company_reporting_decimal']) . "</td>";
                            }
                            if ($isLocCost) {
                                echo "<td class='text-right reporttotal'>" . format_number(array_sum($grandTotal_pdc["pdctotal"]), $this->common_data['company_data']['company_default_decimal']) . "</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                        </tfoot>
                    </table>
                </div>
                <?php
            } else {
                $norecfound=$this->lang->line('common_no_records_found');
                echo warning_message($norecfound);/*"No Records Found!"*/
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